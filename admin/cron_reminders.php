<?php
/**
 * MY METAJI - Cron Reminder Endpoint
 * 
 * Automatically sends WhatsApp reminders for:
 * 1. Due-soon invoices (X days before due date)
 * 2. Overdue invoices (X days after due date)
 * 
 * CALL THIS ENDPOINT DAILY via Windows Task Scheduler:
 * "C:\xampp\php\php.exe" "D:\xampp\htdocs\projects\Mymetaji-Admin\cron_reminders.php"
 * 
 * Or via web browser (one-time test):
 * http://localhost/projects/Mymetaji-Admin/cron_reminders.php?key=metaji_cron_2024
 */

// ============ SECURITY ============
$CRON_SECRET_KEY = 'metaji_cron_2024';
$isCLI = (php_sapi_name() === 'cli');

if (!$isCLI) {
    $key = $_GET['key'] ?? '';
    if ($key !== $CRON_SECRET_KEY) {
        http_response_code(403);
        echo 'Invalid or missing secret key. Add ?key=YOUR_SECRET_KEY';
        exit;
    }
}

// ============ INIT ============
// Set timezone manually (skip auth.php which has HTTPS/session checks)
date_default_timezone_set('Asia/Kolkata');

require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/whatsapp.php';

$logFile = panel_database_dir() . '/cron_log.json';

// ============ 1. DUE SOON REMINDERS ============
$dueSoonResults = whatsapp_auto_due_soon_reminders();
$dueSoonSent = 0;
$dueSoonSkipped = 0;
$dueSoonErrors = [];

foreach ($dueSoonResults as $r) {
    if ($r['success'] ?? false) {
        $dueSoonSent++;
    } else {
        $dueSoonSkipped++;
        if (!empty($r['error'])) {
            $dueSoonErrors[] = ($r['customer'] ?? '') . ': ' . ($r['error'] ?? '');
        }
    }
}

// ============ 2. OVERDUE REMINDERS ============
$overdueResults = whatsapp_auto_overdue_reminders();
$overdueSent = 0;
$overdueSkipped = 0;

foreach ($overdueResults as $r) {
    if ($r['success'] ?? false) {
        $overdueSent++;
    } else {
        $overdueSkipped++;
    }
}

// ============ SAVE LOG ============
$logs = [];
if (file_exists($logFile)) {
    $logs = json_decode(file_get_contents($logFile), true);
    if (!is_array($logs)) $logs = [];
}
$logs[] = [
    'id'             => (empty($logs) ? 1 : max(array_map(fn($r) => (int)($r['id'] ?? 0), $logs)) + 1),
    'date'           => date('Y-m-d H:i:s'),
    'due_soon_sent'  => $dueSoonSent,
    'due_soon_skip'  => $dueSoonSkipped,
    'overdue_sent'   => $overdueSent,
    'overdue_skip'   => $overdueSkipped,
];
if (count($logs) > 90) $logs = array_slice($logs, -90);
file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

// ============ OUTPUT ============
$totalSent = $dueSoonSent + $overdueSent;

if ($isCLI) {
    echo "========================================\n";
    echo "MY METAJI - Cron Reminder Report\n";
    echo "Date: " . date('Y-m-d H:i:s') . "\n";
    echo "========================================\n";
    echo "Due Soon (advance): {$dueSoonSent} sent, {$dueSoonSkipped} skipped\n";
    echo "Overdue:            {$overdueSent} sent, {$overdueSkipped} skipped\n";
    echo "----------------------------------------\n";
    echo "Total: {$totalSent} WhatsApp messages sent\n";
    echo "========================================\n";
    if (!empty($dueSoonErrors)) {
        echo "Errors:\n";
        foreach ($dueSoonErrors as $e) echo "  - {$e}\n";
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'due_soon'  => ['sent' => $dueSoonSent, 'skipped' => $dueSoonSkipped],
        'overdue'   => ['sent' => $overdueSent, 'skipped' => $overdueSkipped],
        'total_sent' => $totalSent,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
