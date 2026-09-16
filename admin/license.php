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
    return panel_database_dir() . '/license.json';
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
    return $days !== null && $days <= 0;
}

function license_is_expiring_soon($thresholdDays = 15) {
    $days = license_days_left();
    return $days !== null && $days > 0 && $days <= $thresholdDays;
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

    if ($days <= 0) {
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

function razorpay_renewal_markup($buttonId = 'renew-button') {
    require_once __DIR__ . '/razorpay_config.php';
    $paymentReady = RAZORPAY_KEY_ID !== 'YOUR_RAZORPAY_KEY_ID' && RAZORPAY_KEY_SECRET !== 'YOUR_RAZORPAY_KEY_SECRET';
    if (!$paymentReady) return '<p style="color:#9A6B00;">Online renewal is not configured yet. Please contact the software provider.</p>';
    $license = get_license();
    $amount = (float)($license['plan_amount'] ?? RAZORPAY_PLAN_AMOUNT);
    if ($amount <= 0) $amount = (float)RAZORPAY_PLAN_AMOUNT;
    $buttonId = preg_replace('/[^A-Za-z0-9_-]/', '', $buttonId);
    $messageId = $buttonId . '-message';
    return '<button id="' . $buttonId . '" style="background:#02A9F7;color:#fff;border:0;border-radius:6px;padding:12px 22px;font-size:15px;cursor:pointer;">Pay ₹' . htmlspecialchars(number_format($amount, 2)) . ' & Renew</button><p id="' . $messageId . '" style="color:#C0392B;"></p><script src="https://checkout.razorpay.com/v1/checkout.js"></script><script>(function(){var button=document.getElementById(' . json_encode($buttonId) . ');var message=document.getElementById(' . json_encode($messageId) . ');button.addEventListener("click",async function(){button.disabled=true;message.textContent="";try{var response=await fetch("payment_create_order.php",{method:"POST"});var order=await response.json();if(!order.ok)throw new Error(order.error||"Unable to start payment.");var checkout=new Razorpay({key:order.key_id,amount:order.amount,currency:order.currency,name:"Prisha Ayurvedic ERP",description:"Subscription renewal",order_id:order.order_id,handler:async function(payment){var verify=await fetch("payment_verify.php",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:new URLSearchParams(payment)});var result=await verify.json();if(!result.ok)throw new Error(result.error||"Payment verification failed.");window.location.reload();}});checkout.on("payment.failed",function(event){message.textContent=event.error.description||"Payment failed.";button.disabled=false;});checkout.open();}catch(error){message.textContent=error.message;button.disabled=false;}});})();</script>';
}

function license_renewal_popup() {
    $license = get_license();
    if (!$license) return '';
    $days = license_days_left();
    if ($days === null) return '';
    $heading = $days <= 0 ? 'Subscription Expired' : 'Renew Subscription';
    $description = $days <= 0
        ? 'You can view your existing data, but changes and exports are locked.'
        : 'Renew now to extend your plan from the current expiry date.';
    $display = format_display_date($license['expiry_date'] ?? '');
    return '<div id="renewal-popup" style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;display:none;align-items:center;justify-content:center;padding:20px;"><div style="position:relative;width:min(480px,100%);background:#fff;border-radius:10px;padding:30px;text-align:center;box-shadow:0 12px 40px rgba(0,0,0,.25);"><button type="button" onclick="document.getElementById(\'renewal-popup\').style.display=\'none\'" style="position:absolute;right:14px;top:10px;border:0;background:transparent;font-size:24px;cursor:pointer;">&times;</button><h2 style="color:' . ($days <= 0 ? '#C0392B' : '#176B3A') . ';margin-top:0;">' . htmlspecialchars($heading) . '</h2><p>' . htmlspecialchars($description) . '</p><p>Current expiry: <b>' . htmlspecialchars($display) . '</b> (' . htmlspecialchars((string)$days) . ' day(s) left)</p>' . razorpay_renewal_markup('dashboard-renew-button') . '</div></div>';
}

function license_renewal_summary() {
    $license = get_license();
    if (!$license) return '';
    $days = license_days_left();
    if ($days === null) return '';
    $label = $days <= 0 ? 'Plan expired' : ($days . ' day(s) left');
    $color = $days <= 0 ? '#C0392B' : ($days <= 15 ? '#9A6B00' : '#176B3A');
    return '<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;justify-content:flex-end;"><span style="font-size:12px;font-weight:600;color:' . $color . ';">' . htmlspecialchars($label) . '</span><button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById(\'renewal-popup\').style.display=\'flex\';">Renew</button></div>';
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
    require_once __DIR__ . '/razorpay_config.php';
    $paymentButton = razorpay_renewal_markup();
    die('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Subscription Expired</title>'
        . '<link rel="stylesheet" href="assets/style.css"></head><body>'
        . '<div style="max-width:520px;margin:80px auto;text-align:center;padding:30px;'
        . 'border:1px solid #f3c6c6;background:#fff5f5;border-radius:10px;font-family:sans-serif;">'
        . '<h2 style="color:#C0392B;margin-top:0;">Subscription Expired</h2>'
        . '<p>Your subscription expired on <b>' . htmlspecialchars($expiryDisplay) . '</b>.</p>'
        . '<p>You can view your existing data, but adding, editing, deleting and exporting are locked until payment is successful.</p>'
        . $paymentButton
        . '<p><a href="index.php">&larr; Back to Dashboard</a></p></div></body></html>');
}
