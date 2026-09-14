<?php
/**
 * PRISHA AYURVEDIC ERP - JSON file storage helper
 * Each module is stored in database/<module>.json.
 * {
 *   "customers": [ {"id":1,"name":"...", ...}, {"id":2,...} ],
 *   "leads": [ ... ]
 * }
 */

define('DB_DIR', __DIR__ . '/database');

function db_modules() {
    global $MODULES;
    return array_keys($MODULES);
}

function db_file($module) {
    return DB_DIR . '/' . preg_replace('/[^a-zA-Z0-9_-]/', '', $module) . '.json';
}

function db_init() {
    if (!is_dir(DB_DIR)) mkdir(DB_DIR, 0755, true);
    $modules = db_modules();
    $hasModuleFiles = false;
    foreach ($modules as $module) {
        if (file_exists(db_file($module))) {
            $hasModuleFiles = true;
            break;
        }
    }
    if ($hasModuleFiles) return;

    foreach ($modules as $module) {
        file_put_contents(db_file($module), "[]\n", LOCK_EX);
    }
}

function db_load() {
    db_init();
    $data = [];
    foreach (db_modules() as $module) {
        $raw = file_exists(db_file($module)) ? file_get_contents(db_file($module)) : '[]';
        $rows = json_decode($raw, true);
        $data[$module] = is_array($rows) ? $rows : [];
    }
    return $data;
}

function db_save($data) {
    db_init();
    foreach (db_modules() as $module) {
        $rows = isset($data[$module]) && is_array($data[$module]) ? $data[$module] : [];
        file_put_contents(db_file($module), json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }
}

function db_next_id($rows) {
    if (empty($rows)) return 1;
    $ids = array_column($rows, 'id');
    return max($ids) + 1;
}

function db_get_all($module) {
    $data = db_load();
    return $data[$module] ?? [];
}

/**
 * Compute order/revenue stats for one customer by matching the customer name
 * against invoices.customer and payments.customer (case-insensitive, trimmed).
 */
function customer_stats($customerName) {
    $data = db_load();
    $name = trim(mb_strtolower($customerName));

    $invoices = array_values(array_filter($data['invoices'], function ($inv) use ($name) {
        return trim(mb_strtolower($inv['customer'] ?? '')) === $name;
    }));

    $totalInvoiced = 0;
    $pendingCount = 0;
    $pendingAmount = 0;
    $totalPaid = 0;
    $payments = $data['payments'];
    foreach ($invoices as $inv) {
        $amt = (float)($inv['amount'] ?? 0);
        $totalInvoiced += $amt;
        $invoicePaid = 0;
        foreach ($payments as $payment) {
            if (($payment['invoice_no'] ?? '') === ($inv['invoice_no'] ?? '')) {
                $invoicePaid += (float)($payment['amount'] ?? 0);
            }
        }
        $totalPaid += $invoicePaid;
        if (($inv['status'] ?? '') !== 'Cancelled' && $invoicePaid < $amt) {
            $pendingCount++;
            $pendingAmount += max(0, $amt - $invoicePaid);
        }
    }

    $customerPayments = array_values(array_filter($payments, function ($p) use ($name) {
        return trim(mb_strtolower($p['customer'] ?? '')) === $name;
    }));

    return [
        'orders'          => count($invoices),
        'pending_count'   => $pendingCount,
        'pending_amount'  => $pendingAmount,
        'total_invoiced'  => $totalInvoiced,
        'total_revenue'   => $totalPaid,
        'invoices'        => $invoices,
        'payments'        => $customerPayments,
    ];
}

function db_get_one($module, $id) {
    $rows = db_get_all($module);
    foreach ($rows as $row) {
        if ((int)$row['id'] === (int)$id) return $row;
    }
    return null;
}

function db_insert($module, $record) {
    $data = db_load();
    $record['id'] = db_next_id($data[$module]);
    $record['created_at'] = date('Y-m-d H:i:s');
    $record['updated_at'] = date('Y-m-d H:i:s');
    $data[$module][] = $record;
    db_save($data);
    return $record['id'];
}

function db_update($module, $id, $record) {
    $data = db_load();
    foreach ($data[$module] as &$row) {
        if ((int)$row['id'] === (int)$id) {
            $record['id'] = $row['id'];
            $record['created_at'] = $row['created_at'] ?? date('Y-m-d H:i:s');
            $record['updated_at'] = date('Y-m-d H:i:s');
            $row = $record;
            break;
        }
    }
    unset($row);
    db_save($data);
}

function db_patch($module, $id, $fields) {
    $data = db_load();
    $found = false;
    foreach ($data[$module] as &$row) {
        if ((int)$row['id'] === (int)$id) {
            foreach ($fields as $k => $v) {
                $row[$k] = $v;
            }
            $row['updated_at'] = date('Y-m-d H:i:s');
            $found = true;
            break;
        }
    }
    unset($row);
    if ($found) db_save($data);
    return $found;
}

function db_delete($module, $id) {
    $data = db_load();
    $data[$module] = array_values(array_filter($data[$module], function ($row) use ($id) {
        return (int)$row['id'] !== (int)$id;
    }));
    db_save($data);
}
