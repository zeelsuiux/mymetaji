<?php
/**
 * MY METAJI - License / Subscription Guard
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

/**
 * The tier this shop renews onto, and the price for it.
 *
 * The amount is always recomputed from the pricing tiers - never from
 * plan_amount in the licence, which is 0 for a shop still on its free trial and
 * 1 for records written before the tiers existed, either of which would charge
 * the wrong money. Falls back to plan_amount only when no tier price can be
 * read at all.
 *
 * Returns ['plan'=>array|null, 'cycle'=>string, 'amount'=>float, 'from_tiers'=>bool].
 */
function license_renewal_terms($planName = null, $cycleName = null) {
    require_once __DIR__ . '/../includes/plans.php';
    $license = get_license();

    $plan = plan_find($planName ?? ($license['plan'] ?? ''));
    if (!$plan) {
        foreach (plans_all() as $candidate) {
            if (!empty($candidate['online'])) { $plan = $candidate; break; }
        }
    }
    $cycle = plan_normalise_cycle($cycleName ?? ($license['cycle'] ?? 'yearly'));

    $amount = 0.0;
    $fromTiers = false;
    if ($plan) {
        try {
            $amount = plan_amount_rupees($plan, $cycle);
            $fromTiers = true;
        } catch (InvalidArgumentException $e) {
            $amount = 0.0;
        }
    }
    if ($amount <= 0) {
        $amount = (float)($license['plan_amount'] ?? 0);
        $cycle = 'yearly';
    }
    return ['plan' => $plan, 'cycle' => $cycle, 'amount' => $amount, 'from_tiers' => $fromTiers];
}

/**
 * The renewal form shown in the expiring/expired popup.
 *
 * The shop picks its tier and cycle here, and the button text is updated from
 * the same price table the server will charge from, so the figure on screen can
 * never drift from the figure on the invoice.
 */
function razorpay_renewal_markup($buttonId = 'renew-button') {
    require_once __DIR__ . '/razorpay_config.php';
    require_once __DIR__ . '/../includes/plans.php';
    $paymentReady = RAZORPAY_KEY_ID !== 'YOUR_RAZORPAY_KEY_ID' && RAZORPAY_KEY_SECRET !== 'YOUR_RAZORPAY_KEY_SECRET';
    if (!$paymentReady) return '<p style="color:#9A6B00;">Online renewal is not configured yet. Please contact the software provider.</p>';

    $terms = license_renewal_terms();
    $buttonId = preg_replace('/[^A-Za-z0-9_-]/', '', $buttonId);
    $messageId = $buttonId . '-message';
    $planId = $buttonId . '-plan';
    $cycleId = $buttonId . '-cycle';
    // The renewal endpoints reject a request without this, so it is baked into
    // the script rather than left to a form the popup does not have.
    $csrfToken = function_exists('csrf_token') ? csrf_token() : '';

    $planOptions = '';
    $prices = [];
    foreach (plans_all() as $option) {
        $selected = ($terms['plan'] && $option['name'] === $terms['plan']['name']) ? ' selected' : '';
        $planOptions .= '<option value="' . htmlspecialchars($option['name'], ENT_QUOTES) . '"' . $selected . '>'
            . htmlspecialchars(trim($option['icon'] . ' ' . $option['name']), ENT_QUOTES) . '</option>';
        $prices[$option['name']] = [
            'monthly' => plan_amount_rupees($option, 'monthly'),
            'yearly'  => plan_amount_rupees($option, 'yearly'),
        ];
    }
    $pricesJson = json_encode($prices, JSON_UNESCAPED_UNICODE);

    return '<div style="text-align:left;">'
        . '<label for="' . $planId . '" style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">Plan</label>'
        . '<select id="' . $planId . '" style="width:100%;padding:10px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;margin-bottom:12px;">' . $planOptions . '</select>'
        . '<label for="' . $cycleId . '" style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">Billing</label>'
        . '<select id="' . $cycleId . '" style="width:100%;padding:10px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;margin-bottom:16px;">'
        . '<option value="yearly"' . ($terms['cycle'] === 'yearly' ? ' selected' : '') . '>Yearly (12 months)</option>'
        . '<option value="monthly"' . ($terms['cycle'] === 'monthly' ? ' selected' : '') . '>Monthly (1 month)</option>'
        . '</select>'
        . '<button id="' . $buttonId . '" style="width:100%;background:#02A9F7;color:#fff;border:0;border-radius:6px;padding:12px 22px;font-size:15px;font-weight:600;cursor:pointer;">Pay &amp; Renew</button>'
        . '<p id="' . $messageId . '" style="color:#C0392B;"></p>'
        . '</div>'
        . '<script src="https://checkout.razorpay.com/v1/checkout.js"></script>'
        . '<script>(function(){'
        . 'var PRICES=' . $pricesJson . ';'
        . 'var CSRF=' . json_encode($csrfToken) . ';'
        . 'var button=document.getElementById(' . json_encode($buttonId) . ');'
        . 'var message=document.getElementById(' . json_encode($messageId) . ');'
        . 'var planSelect=document.getElementById(' . json_encode($planId) . ');'
        . 'var cycleSelect=document.getElementById(' . json_encode($cycleId) . ');'
        . 'function rupees(n){return "₹" + Number(n).toLocaleString("en-IN");}'
        . 'function buttonText(){var price=PRICES[planSelect.value];if(!price)return;button.textContent="Pay "+rupees(price[cycleSelect.value])+" & Renew";}'
        . 'planSelect.addEventListener("change",buttonText);cycleSelect.addEventListener("change",buttonText);buttonText();'
        . 'button.addEventListener("click",async function(){'
        . 'button.disabled=true;message.textContent="";'
        . 'try{'
        . 'var response=await fetch("payment_create_order.php",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:new URLSearchParams({_csrf_token:CSRF,plan:planSelect.value,cycle:cycleSelect.value})});'
        . 'var order=await response.json();if(!order.ok)throw new Error(order.error||"Unable to start payment.");'
        . 'var checkout=new Razorpay({key:order.key_id,amount:order.amount,currency:order.currency,name:"My Metaji",description:order.plan+" plan — "+order.cycle,order_id:order.order_id,'
        . 'prefill:{contact:order.contact||undefined},'
        . 'modal:{ondismiss:function(){button.disabled=false;buttonText();message.textContent="Payment was cancelled. Nothing was charged.";}},'
        . 'handler:async function(payment){'
        . 'message.style.color="#176B3A";message.textContent="Confirming your payment…";'
        . 'var body=new URLSearchParams(payment);body.append("_csrf_token",CSRF);'
        . 'var verify=await fetch("payment_verify.php",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:body.toString()});'
        . 'var result=await verify.json();if(!result.ok)throw new Error(result.error||"Payment verification failed.");'
        . 'window.location.reload();}});'
        . 'checkout.on("payment.failed",function(event){message.textContent=(event.error&&event.error.description)||"Payment failed.";button.disabled=false;buttonText();});'
        . 'checkout.open();'
        . '}catch(error){message.textContent=error.message;button.disabled=false;buttonText();}});'
        . '})();</script>';
}

function license_renewal_popup() {
    $license = get_license();
    if (!$license) return '';
    $days = license_days_left();
    if ($days === null) return '';
    // A shop still inside its free trial is not "expiring" in the paid sense -
    // say so, otherwise the first thing a trial user sees is a payment demand.
    $onTrial = !empty($license['is_trial']);
    $heading = $onTrial ? 'Your free trial is ending' : ($days <= 0 ? 'Subscription Expired' : 'Renew Subscription');
    $description = $onTrial
        ? 'You have been trying the software free. Keep it going and pay only for the time you want.'
        : ($days <= 0
            ? 'You can view your existing data, but changes and exports are locked.'
            : 'Renew now to extend your plan from the current expiry date.');
    $display = format_display_date($license['expiry_date'] ?? '');
    $trialNote = $onTrial
        ? '<p style="color:#176B3A;font-size:14px;">Nothing has been charged for this trial.</p>'
        : '';
    return '<div id="renewal-popup" style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;display:none;align-items:center;justify-content:center;padding:20px;"><div style="position:relative;width:min(480px,100%);background:#fff;border-radius:10px;padding:30px;text-align:center;box-shadow:0 12px 40px rgba(0,0,0,.25);"><button type="button" onclick="document.getElementById(\'renewal-popup\').style.display=\'none\'" style="position:absolute;right:14px;top:10px;border:0;background:transparent;font-size:24px;cursor:pointer;">&times;</button><h2 style="color:' . ($days <= 0 ? '#C0392B' : '#176B3A') . ';margin-top:0;">' . htmlspecialchars($heading) . '</h2><p>' . htmlspecialchars($description) . '</p><p>Current expiry: <b>' . htmlspecialchars($display) . '</b> (' . htmlspecialchars((string)$days) . ' day(s) left)</p>' . $trialNote . razorpay_renewal_markup('dashboard-renew-button') . '</div></div>';
}

function license_renewal_summary() {
    $license = get_license();
    if (!$license) return '';
    $days = license_days_left();
    if ($days === null) return '';
    $label = $days <= 0 ? 'Plan expired' : ($days . ' day(s) left');
    $color = $days <= 0 ? '#C0392B' : ($days <= 15 ? '#9A6B00' : '#176B3A');
    return '<div class="license-summary" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;justify-content:flex-end;"><span style="font-size:12px;font-weight:600;color:' . $color . ';">' . htmlspecialchars($label) . '</span><button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById(\'renewal-popup\').style.display=\'flex\';">Renew</button></div>';
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
