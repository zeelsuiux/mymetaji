<?php
/**
 * TEST: Due Soon Invoice Detection
 * Run this to see which invoices are due within 2 days
 */
date_default_timezone_set('Asia/Kolkata');
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

echo "=== Due Soon Invoices (within 2 days) ===\n\n";

$dueSoon = get_due_soon_invoices(2);

if (empty($dueSoon)) {
    echo "No invoices due within 2 days.\n";
} else {
    foreach ($dueSoon as $inv) {
        echo "Invoice: " . ($inv['invoice_no'] ?? '') . "\n";
        echo "Customer: " . ($inv['customer'] ?? '') . "\n";
        echo "Amount: ₹" . number_format((float)($inv['amount'] ?? 0), 2) . "\n";
        echo "Pending: ₹" . number_format((float)($inv['_pending'] ?? 0), 2) . "\n";
        echo "Due Date: " . ($inv['due_date'] ?? '') . "\n";
        echo "Days Until Due: " . ($inv['_days_until_due'] ?? 0) . "\n";
        echo "---\n";
    }
}

echo "\n=== Overdue Invoices ===\n\n";

$overdue = get_overdue_invoices();

if (empty($overdue)) {
    echo "No overdue invoices.\n";
} else {
    foreach ($overdue as $inv) {
        echo "Invoice: " . ($inv['invoice_no'] ?? '') . "\n";
        echo "Customer: " . ($inv['customer'] ?? '') . "\n";
        echo "Amount: ₹" . number_format((float)($inv['amount'] ?? 0), 2) . "\n";
        echo "Pending: ₹" . number_format((float)($inv['_pending'] ?? 0), 2) . "\n";
        echo "Days Overdue: " . ($inv['_days_overdue'] ?? 0) . "\n";
        echo "---\n";
    }
}
