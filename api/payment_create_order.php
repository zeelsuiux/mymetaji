<?php
/**
 * Public endpoint: open a Razorpay order for a plan purchase.
 *
 * This is the RENEWAL path. A new customer is expected to have started a
 * 15-day trial first, so this endpoint will not create a first subscription for
 * an unknown buyer - it sends them to the trial form instead. That is what keeps
 * "trial first, pay at renewal" true no matter which page someone pays from.
 *
 * The plan name and billing cycle come from the form, but the amount does not:
 * it is recomputed from includes/plan-data.php, so editing the form (or posting
 * your own amount) cannot change what anyone is charged.
 *
 * The attempt is written to payments.json as 'pending' before Razorpay is
 * called, which is what lets the master ledger show abandoned checkouts and
 * gives the verify step a server-side record of the amount to check against.
 */
require_once __DIR__ . '/../includes/payment.php';

payment_require_post();

if (!razorpay_is_configured()) {
    payment_json(503, ['ok' => false, 'error' => 'Payments are not configured yet. Please contact us on WhatsApp.']);
}

$input = $_POST;
if (!payment_csrf_check($_POST['csrf'] ?? '', 'checkout')) {
    payment_json(400, ['ok' => false, 'error' => 'Your session expired. Please reload the page and try again.']);
}

$plan = plan_find($input['plan'] ?? '');
if (!$plan) payment_json(400, ['ok' => false, 'error' => 'Please choose a plan.']);
if (!plan_is_online($plan)) {
    payment_json(400, ['ok' => false, 'error' => $plan['name'] . ' is sold by our sales team. Please use the enquiry form.']);
}
$cycle = plan_normalise_cycle($input['cycle'] ?? 'yearly');
if (!in_array($input['cycle'] ?? 'yearly', plan_cycles(), true)) {
    payment_json(400, ['ok' => false, 'error' => 'Unknown billing cycle.']);
}

[$buyer, $errors] = payment_clean_buyer($input);
if ($errors) payment_json(400, ['ok' => false, 'error' => implode(' ', $errors)]);

// Trial first. If we have never seen this buyer, they have not started a trial
// yet, so send them there rather than selling to them straight away.
$existing = null;
foreach (mdb_all('customers') as $c) {
    $sameEmail = $buyer['email'] !== '' && strcasecmp((string)($c['email'] ?? ''), $buyer['email']) === 0;
    $samePhone = $buyer['phone'] !== '' && trim((string)($c['phone'] ?? '')) === $buyer['phone'];
    if ($sameEmail || $samePhone) { $existing = $c; break; }
}
if (!$existing) {
    payment_json(409, [
        'ok' => false,
        'error' => 'Start your ' . payment_trial_days() . '-day free trial first, then pay for the plan you pick.',
        'trial_url' => 'trial-signup.php?plan=' . rawurlencode($plan['name']),
    ]);
}

$amountPaise = plan_amount_paise($plan, $cycle);
if ($amountPaise < 100) payment_json(500, ['ok' => false, 'error' => 'This plan has no usable price.']);

$paymentId = payment_insert([
    'status'          => 'pending',
    'plan'            => $plan['name'],
    'cycle'           => $cycle,
    'amount'          => $amountPaise / 100,
    'currency'        => RAZORPAY_CURRENCY,
    'payment_method'  => 'razorpay',
    'payment_type'    => 'Online',
    'source'          => 'renewal',
    'customer_id'     => (int)$existing['id'],
    'customer_name'   => $buyer['name'],
    'customer_email'  => $buyer['email'],
    'customer_phone'  => $buyer['phone'],
    'customer_address'=> $buyer['address'],
    'razorpay_order_id'   => '',
    'razorpay_payment_id' => '',
    'failure_reason'  => '',
]);

$receipt = 'plan_' . strtolower($plan['name']) . '_' . $cycle . '_' . $paymentId;
$response = razorpay_api('POST', 'orders', [
    'amount'      => $amountPaise,
    'currency'    => RAZORPAY_CURRENCY,
    'receipt'     => $receipt,
    'notes'       => [
        'payment_id' => $paymentId,
        'plan'       => $plan['name'],
        'cycle'      => $cycle,
        'name'       => $buyer['name'],
        'email'      => $buyer['email'],
        'phone'      => $buyer['phone'],
    ],
]);

if (!$response['ok'] || empty($response['data']['id'])) {
    payment_update($paymentId, ['status' => 'failed', 'failure_reason' => $response['error'] ?: 'Order could not be created']);
    razorpay_log_failure('public-create-order', $response);
    payment_json(502, ['ok' => false, 'error' => razorpay_failure_message($response)]);
}

$orderId = (string)$response['data']['id'];
payment_update($paymentId, ['razorpay_order_id' => $orderId]);

payment_json(200, [
    'ok'        => true,
    'key_id'    => RAZORPAY_KEY_ID,
    'order_id'  => $orderId,
    'amount'    => (int)$response['data']['amount'],
    'currency'  => (string)($response['data']['currency'] ?? RAZORPAY_CURRENCY),
    'reference' => $paymentId,
    'plan'      => $plan['name'],
    'cycle'     => $cycle,
    // Fresh single-use tokens for the next step. The order token is spent above;
    // handing back a new one means a buyer who dismisses the Razorpay popup can
    // press Pay again without being told their session expired.
    'verify_token'  => payment_csrf_token('verify'),
    'checkout_token'=> payment_csrf_token('checkout'),
    'prefill'   => [
        'name'    => $buyer['name'],
        'email'   => $buyer['email'],
        'contact' => $buyer['phone'],
    ],
    'live'      => (bool)RAZORPAY_LIVE_MODE,
]);
