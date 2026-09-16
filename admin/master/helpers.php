<?php
/**
 * MASTER PANEL - Helpers: expiry/status calculation + shop provisioning.
 *
 * Shop provisioning: when a new customer (shopkeeper) is created, we copy
 * the shared panel's tenant data folders into ../shops/cst_<id> and:
 *   - wipe its sample database/*.json files so the new shop starts empty
 *   - write database/team.json with ONE Admin login using the username/password
 *     the master admin chose for this customer
 *   - write database/license.json with the subscription info so the panel
 *     itself can show the "expiring soon" banner and lock write access after
 *     expiry, without needing to call back to the master panel over the network.
 */

define('SHOPS_DIR', __DIR__ . '/../shops');

function format_display_date_m($value, $fallback = '-') {
    $value = trim((string)$value);
    if ($value === '') return $fallback;
    $timestamp = strtotime($value);
    return $timestamp === false ? $value : date('d-m-Y', $timestamp);
}

/** purchase date (Y-m-d) -> expiry date (Y-m-d), exactly 1 year later. */
function calc_expiry_date($purchaseDate) {
    $d = DateTime::createFromFormat('Y-m-d', substr($purchaseDate, 0, 10));
    if (!$d) return '';
    $d->modify('+1 year');
    return $d->format('Y-m-d');
}

function calc_renewal_expiry_date($currentExpiry, $purchaseDate) {
    $purchase = DateTime::createFromFormat('Y-m-d', substr((string)$purchaseDate, 0, 10));
    if (!$purchase) $purchase = new DateTime('today');
    $base = DateTime::createFromFormat('Y-m-d', substr((string)$currentExpiry, 0, 10));
    if (!$base || $base < $purchase) $base = clone $purchase;
    $base->modify('+1 year');
    return $base->format('Y-m-d');
}

function calc_trial_expiry_date($startDate = null) {
    $date = DateTime::createFromFormat('Y-m-d', substr((string)($startDate ?: date('Y-m-d')), 0, 10));
    if (!$date) $date = new DateTime('today');
    $date->modify('+15 days');
    return $date->format('Y-m-d');
}

function generate_customer_username($name, $email, $phone) {
    $base = trim((string)$email) !== '' ? strstr((string)$email, '@', true) : preg_replace('/\D+/', '', (string)$phone);
    if ($base === '' || $base === false) $base = preg_replace('/[^a-z0-9]+/i', '', (string)$name);
    $base = strtolower(trim((string)$base, '-_')) ?: 'customer';
    $username = $base;
    $suffix = 1;
    $used = array_map(fn($row) => strtolower((string)($row['username'] ?? '')), mdb_all('customers'));
    while (in_array(strtolower($username), $used, true)) $username = $base . $suffix++;
    return $username;
}

/** Signed days until expiry date. Negative = already expired. Null = no date. */
function days_left($expiryDate) {
    $expiryDate = trim((string)$expiryDate);
    if ($expiryDate === '') return null;
    $expiry = DateTime::createFromFormat('Y-m-d', substr($expiryDate, 0, 10));
    if (!$expiry) return null;
    $today = new DateTime('today');
    $diff = $today->diff($expiry);
    $days = (int)$diff->format('%a');
    return $diff->invert ? -$days : $days;
}

/** ['label'=>..., 'class'=>green|orange|red|gray, 'days'=>int|null] */
function subscription_status($expiryDate) {
    $days = days_left($expiryDate);
    if ($days === null) return ['label' => 'No Subscription', 'class' => 'gray', 'days' => null];
    if ($days < 0) return ['label' => 'Expired', 'class' => 'red', 'days' => $days];
    if ($days <= 15) return ['label' => 'Expiring Soon', 'class' => 'orange', 'days' => $days];
    return ['label' => 'Active', 'class' => 'green', 'days' => $days];
}

function customer_subscriptions($customerId) {
    $rows = array_values(array_filter(mdb_all('subscriptions'), fn($r) => (int)($r['customer_id'] ?? 0) === (int)$customerId));
    usort($rows, fn($a, $b) => strcmp((string)($b['purchase_date'] ?? ''), (string)($a['purchase_date'] ?? '')));
    return $rows;
}

function customer_latest_subscription($customerId) {
    $rows = customer_subscriptions($customerId);
    return $rows[0] ?? null;
}

function customer_tenant_code($customerId) { return 'cst_' . (int)$customerId; }
function customer_panel_path($slug) {
    $slug = (string)$slug;
    return '../' . $slug . '/';
}

function recursive_copy($src, $dst) {
    if (!is_dir($dst)) mkdir($dst, 0755, true);
    $items = scandir($src);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || $item === '.git') continue;
        $srcPath = $src . '/' . $item;
        $dstPath = $dst . '/' . $item;
        if (is_dir($srcPath)) {
            recursive_copy($srcPath, $dstPath);
        } else {
            copy($srcPath, $dstPath);
        }
    }
}

function recursive_delete($dir) {
    if (!is_dir($dir)) return;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            recursive_delete($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dir);
}

function shop_path($slug) {
    return SHOPS_DIR . '/' . preg_replace('/[^A-Za-z0-9_-]/', '', $slug);
}

function initialize_tenant_database($dir) {
    $modules = ['activity', 'customers', 'expenses', 'invoices', 'leads', 'meetings', 'payments', 'products', 'quotations', 'shop_profile', 'subadmins', 'tasks', 'team'];
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    foreach ($modules as $module) file_put_contents($dir . '/' . $module . '.json', "[]\n", LOCK_EX);
    file_put_contents($dir . '/.htaccess', "Options -Indexes\n", LOCK_EX);
}

/** Write/refresh the license.json inside a shop's own database folder. */
function write_shop_license($slug, $customer, $subscription) {
    $license = [
        'customer_id'   => (int)$customer['id'],
        'customer_name' => $customer['name'],
        'plan_amount'   => $subscription['amount'],
        'payment_type'  => $subscription['payment_type'],
        'purchase_date' => $subscription['purchase_date'],
        'expiry_date'   => $subscription['expiry_date'],
    ];
    foreach (['is_trial', 'razorpay_order_id', 'razorpay_payment_id', 'transaction_id'] as $key) {
        if (array_key_exists($key, $subscription)) $license[$key] = $subscription[$key];
    }
    $dir = shop_path($slug) . '/database';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    file_put_contents($dir . '/license.json', json_encode($license, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

/** Write/refresh the shop's Admin login (team.json row #1) from customer data. */
function write_shop_login($slug, $customer, $plainPassword = null) {
    $dir = shop_path($slug) . '/database';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $file = $dir . '/team.json';
    $rows = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    if (!is_array($rows)) $rows = [];

    $adminIndex = null;
    foreach ($rows as $i => $row) {
        if (strcasecmp((string)($row['role'] ?? ''), 'Admin') === 0) { $adminIndex = $i; break; }
    }
    $passwordHash = (string)($customer['password_hash'] ?? '') !== ''
        ? $customer['password_hash']
        : ($plainPassword !== null && $plainPassword !== ''
            ? password_hash($plainPassword, PASSWORD_DEFAULT)
            : ($adminIndex !== null ? ($rows[$adminIndex]['password'] ?? '') : password_hash('changeme123', PASSWORD_DEFAULT)));

    $record = [
        'id'         => $adminIndex !== null ? $rows[$adminIndex]['id'] : 1,
        'name'       => $customer['name'],
        'phone'      => $customer['phone'],
        'email'      => $customer['email'] ?? '',
        'address'    => $customer['address'],
        'photo'      => $adminIndex !== null ? ($rows[$adminIndex]['photo'] ?? '') : '',
        'username'   => $customer['username'],
        'password'   => $passwordHash,
        'role'       => 'Admin',
        'created_at' => $adminIndex !== null ? ($rows[$adminIndex]['created_at'] ?? date('Y-m-d H:i:s')) : date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ];

    if ($adminIndex !== null) {
        $rows[$adminIndex] = $record;
    } else {
        array_unshift($rows, $record);
    }
    file_put_contents($file, json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

/** Create only the tenant data/assets folders. Shared PHP files live at the project root. */
function provision_shop($customer, $subscription) {
    $slug = customer_tenant_code($customer['id']);
    $dest = shop_path($slug);
    initialize_tenant_database($dest . '/database');
    recursive_copy(__DIR__ . '/../assets', $dest . '/assets');

    write_shop_login($slug, $customer, $customer['password_plain'] ?? null);
    write_shop_license($slug, $customer, $subscription);
    return $slug;
}

function generate_random_password($length = 8) {
    $chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    $out = '';
    for ($i = 0; $i < $length; $i++) $out .= $chars[random_int(0, strlen($chars) - 1)];
    return $out;
}
