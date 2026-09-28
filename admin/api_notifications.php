<?php
/**
 * Mymetaji - Notifications API
 * Returns real-time notification counts for badges
 */
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

header('Content-Type: application/json');

$notifications = [];

if (is_admin()) {
    // Overdue invoices
    $overdue = get_overdue_invoices();
    if (!empty($overdue)) {
        $notifications[] = [
            'type' => 'overdue',
            'icon' => '⚠️',
            'message' => count($overdue) . ' overdue invoice(s)',
            'color' => '#F44336',
            'link' => 'module.php?m=invoices'
        ];
    }

    // Pending tasks
    $tasks = get_pending_tasks();
    if (!empty($tasks)) {
        $notifications[] = [
            'type' => 'tasks',
            'icon' => '📋',
            'message' => count($tasks) . ' pending task(s)',
            'color' => '#2196F3',
            'link' => 'module.php?m=tasks'
        ];
    }

    // Expiring quotations
    $quotations = get_expiring_quotations(7);
    if (!empty($quotations)) {
        $notifications[] = [
            'type' => 'quotations',
            'icon' => '📋',
            'message' => count($quotations) . ' quotation(s) expiring soon',
            'color' => '#FF9800',
            'link' => 'module.php?m=quotations'
        ];
    }

    // Low stock products
    $products = db_get_all('products');
    $lowStock = array_filter($products, fn($p) => (float)($p['stock_qty'] ?? 0) <= 5 && (float)($p['stock_qty'] ?? 0) > 0);
    if (!empty($lowStock)) {
        $notifications[] = [
            'type' => 'lowstock',
            'icon' => '📦',
            'message' => count($lowStock) . ' product(s) low on stock',
            'color' => '#FF9800',
            'link' => 'module.php?m=products'
        ];
    }
}

// Today's summary
$today = date('Y-m-d');
$todayInvoices = 0;
$todayAmount = 0;
$todayCollection = 0;

foreach (db_get_all('invoices') as $inv) {
    if (substr($inv['created_at'] ?? $inv['date'] ?? '', 0, 10) === $today) {
        $todayInvoices++;
        $todayAmount += (float)($inv['amount'] ?? 0);
    }
}
foreach (db_get_all('payments') as $pay) {
    if (substr($pay['created_at'] ?? $pay['date'] ?? '', 0, 10) === $today) {
        $todayCollection += (float)($pay['amount'] ?? 0);
    }
}

echo json_encode([
    'notifications' => $notifications,
    'today' => [
        'invoices' => $todayInvoices,
        'amount' => $todayAmount,
        'collection' => $todayCollection,
    ]
]);
