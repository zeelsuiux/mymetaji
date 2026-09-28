<?php
/**
 * Mymetaji - Dashboard Summary API
 * Returns JSON data for live dashboard widgets
 */
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

header('Content-Type: application/json');

$data = db_load();
$today = date('Y-m-d');

// Today's stats
$todayInvoices = 0;
$todayAmount = 0;
$todayCollection = 0;
$overdueCount = 0;
$lowStockCount = 0;

foreach ($data['invoices'] as $invoice) {
    $created = substr($invoice['created_at'] ?? $invoice['date'] ?? '', 0, 10);
    if ($created === $today) {
        $todayInvoices++;
        $todayAmount += (float)($invoice['amount'] ?? 0);
    }
    // Check overdue
    if (!empty($invoice['due_date']) && $invoice['due_date'] < $today && ($invoice['status'] ?? '') !== 'Paid') {
        $overdueCount++;
    }
}

foreach ($data['payments'] as $payment) {
    $created = substr($payment['created_at'] ?? $payment['date'] ?? '', 0, 10);
    if ($created === $today) {
        $todayCollection += (float)($payment['amount'] ?? 0);
    }
}

// Low stock products
foreach ($data['products'] as $product) {
    if ((float)($product['stock_qty'] ?? 0) <= 5 && (float)($product['stock_qty'] ?? 0) > 0) {
        $lowStockCount++;
    }
}

// Total pending
$totalInvoiced = array_sum(array_map(fn($i) => (float)($i['amount'] ?? 0), $data['invoices']));
$totalPaid = array_sum(array_map(fn($p) => (float)($p['amount'] ?? 0), $data['payments']));
$totalPending = max(0, $totalInvoiced - $totalPaid);

// Last 7 days sales data for chart
$chartLabels = [];
$chartData = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $chartLabels[] = date('d M', strtotime($date));
    $dayTotal = 0;
    foreach ($data['invoices'] as $inv) {
        if (substr($inv['created_at'] ?? $inv['date'] ?? '', 0, 10) === $date) {
            $dayTotal += (float)($inv['amount'] ?? 0);
        }
    }
    $chartData[] = $dayTotal;
}

echo json_encode([
    'todayInvoices' => $todayInvoices,
    'todayAmount' => $todayAmount,
    'todayCollection' => $todayCollection,
    'totalPending' => $totalPending,
    'overdueCount' => $overdueCount,
    'lowStockCount' => $lowStockCount,
    'chartLabels' => $chartLabels,
    'chartData' => $chartData,
]);
