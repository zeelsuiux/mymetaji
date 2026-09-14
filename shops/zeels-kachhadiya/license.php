<?php
/**
 * PRISHA AYURVEDIC ERP - License / Subscription Guard
 *
 * database/license.json is written by the MASTER PANEL (not editable from
 * here). It contains the shop's current subscription info:
 *   {
 *     "customer_id": 1,
 *     "customer_name": "...",
 *     "plan_amount": 5000,
 *     "payment_type": "Online",
 *     "purchase_date": "2026-09-14",
 *     "expiry_date": "2027-09-14"
 *   }
 *
 * Rules:
 *  - No license.json at all  -> treated as unrestricted (useful for local/dev
 *    copies of this template that were never provisioned by the master panel).
 *  - Expiry more than 15 days away -> normal, no message.
 *  - Expiry within 15 days (but not passed) -> warning banner, everything still works.
 *  - Expiry passed -> banner + the shop can still log in and VIEW everything,
 *    but cannot add / edit / delete / export any data.
 */

require_once __DIR__ . '/helpers.php';

function license_file() {
    return __DIR__ . '/database/license.json';
}

function get_license() {
    $file = license_file();
    if (!file_exists($file)) return null;
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : null;
}

/** Signed days until expiry. Negative = already expired. Null = no license file. */
function license_days_left() {
    $license = get_license();
    $expiry = trim((string)($license['expiry_date'] ?? ''));
    if ($expiry === '') return null;
    $expiryDate = DateTime::createFromFormat('Y-m-d', substr($expiry, 0, 10));
    if (!$expiryDate) return null;
    $today = new DateTime('today');
    $diff = $today->diff($expiryDate);
    $days = (int)$diff->format('%a');
    return $diff->invert ? -$days : $days;
}

function license_is_expired() {
    $days = license_days_left();
    return $days !== null && $days < 0;
}

function license_is_expiring_soon($thresholdDays = 15) {
    $days = license_days_left();
    return $days !== null && $days >= 0 && $days <= $thresholdDays;
}

/**
 * Returns a banner descriptor to show on every page, or null if nothing to show.
 * ['type' => 'expired'|'expiring', 'text' => '...']
 */
function license_banner() {
    $license = get_license();
    if (!$license) return null;
    $days = license_days_left();
    if ($days === null) return null;
    $expiryDisplay = format_display_date($license['expiry_date'] ?? '');

    if ($days < 0) {
        return [
            'type' => 'expired',
            'text' => 'Your subscription expired on ' . $expiryDisplay . ' (' . abs($days) . ' day(s) ago). '
                . 'You can still view your data, but adding, editing, deleting or exporting is locked until you renew.',
        ];
    }
    if ($days <= 15) {
        return [
            'type' => 'expiring',
            'text' => 'Your subscription will expire on ' . $expiryDisplay . ' (' . $days . ' day(s) left). '
                . 'Please renew soon to avoid interruption.',
        ];
    }
    return null;
}

/**
 * Call this at the top of any action that adds / edits / deletes / exports data.
 * Stops execution with a friendly message if the subscription has expired.
 */
function require_active_license() {
    if (!license_is_expired()) return;
    http_response_code(403);
    $isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Subscription expired. Renew to make changes.']);
        exit;
    }
    $license = get_license();
    $expiryDisplay = format_display_date($license['expiry_date'] ?? '');
    die('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Subscription Expired</title>'
        . '<link rel="stylesheet" href="assets/style.css"></head><body>'
        . '<div style="max-width:520px;margin:80px auto;text-align:center;padding:30px;'
        . 'border:1px solid #f3c6c6;background:#fff5f5;border-radius:10px;font-family:sans-serif;">'
        . '<h2 style="color:#C0392B;margin-top:0;">Subscription Expired</h2>'
        . '<p>Your subscription expired on <b>' . htmlspecialchars($expiryDisplay) . '</b>.</p>'
        . '<p>You can still view your existing data, but adding, editing, deleting or exporting is disabled until the subscription is renewed. Please contact the software provider to renew.</p>'
        . '<p><a href="index.php">&larr; Back to Dashboard</a></p></div></body></html>');
}
