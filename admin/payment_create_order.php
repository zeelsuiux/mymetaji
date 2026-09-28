<?php
/**
 * In-app renewal: open a Razorpay order from the customer's own dashboard.
 *
 * This is the same ledger the website checkout writes to, so a renewal made from
 * the dashboard shows up on the master Payments screen exactly like one made
 * from the site. The amount is never taken from the browser: it is recomputed
 * from includes/plan-data.php for the tier and cycle the customer picked, and
 * paid_onwards from the licence's own expiry date by includes/payment.php.
 *
 * The licence's plan_amount is deliberately ignored. On a trial it is 0, and on
 * records written before the pricing tiers existed it is 1 - charging either
 * would be wrong.
 */
require_once __DIR__ . '/auth.php';
require_login();
csrf_verify();

require_once __DIR__ . '/license.php';
require_once __DIR__ . '/../includes/plans.php';
require_once __DIR__ . '/../includes/payment.php';

header('Content-Type: application/json; charset=utf-8');

if (!razorpay_is_configured()) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Razorpay payment setup is not configured yet.']);
    exit;
}

$license = get_license();
if (!$license || empty($license['customer_id'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'No active licence on this shop.']);
    exit;
}

$terms = license_renewal_terms($_POST['plan'] ?? null, $_POST['cycle'] ?? null);
if (!$terms['plan'] || !$terms['from_tiers']) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Please choose a valid plan.']);
    exit;
}
if (!plan_is_online($terms['plan'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $terms['plan']['name'] . ' is sold by our sales team. Please contact us to renew it.']);
    exit;
}

$plan  = $terms['plan'];
$cycle = $terms['cycle'];
$amountPaise = plan_amount_paise($plan, $cycle);
if ($amountPaise < 100) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'This plan has no usable price.']);
    exit;
}

$customer = mdb_get('customers', (int)$license['customer_id']);
if (!$customer) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Customer account was not found.']);
    exit;
}

// The attempt is recorded before Razorpay is called, so an abandoned popup is
// still visible in the master ledger as "Awaiting" rather than disappearing.
$paymentId = payment_insert([
    'status'          => 'pending',
    'plan'            => $plan['name'],
    'cycle'           => $cycle,
    'amount'          => $amountPaise / 100,
    'currency'        => RAZORPAY_CURRENCY,
    'payment_method'  => 'razorpay',
    'payment_type'    => 'Online',
    'source'          => 'in-app renewal',
    'customer_id'     => (int)$customer['id'],
    'customer_name'   => $customer['name'] ?? '',
    'customer_email'  => $customer['email'] ?? '',
    'customer_phone'  => $customer['phone'] ?? '',
    'customer_address'=> $customer['address'] ?? '',
    'razorpay_order_id'   => '',
    'razorpay_payment_id' => '',
    'failure_reason'  => '',
]);

$response = razorpay_api('POST', 'orders', [
    'amount'   => $amountPaise,
    'currency' => RAZORPAY_CURRENCY,
    'receipt'  => 'renew_' . customer_tenant_code((int)$customer['id']) . '_' . $paymentId,
    'notes'    => [
        'payment_id'  => $paymentId,
        'customer_id' => (int)$customer['id'],
        'plan'        => $plan['name'],
        'cycle'       => $cycle,
    ],
]);

if (!$response['ok'] || empty($response['data']['id'])) {
    payment_update($paymentId, ['status' => 'failed', 'failure_reason' => $response['error'] ?: 'Order could not be created']);
    razorpay_log_failure('admin-create-order', $response);
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => razorpay_failure_message($response)]);
    exit;
}

$orderId = (string)$response['data']['id'];
payment_update($paymentId, ['razorpay_order_id' => $orderId]);

echo json_encode([
    'ok'        => true,
    'key_id'    => RAZORPAY_KEY_ID,
    'order_id'  => $orderId,
    'amount'    => (int)$response['data']['amount'],
    'currency'  => (string)($response['data']['currency'] ?? RAZORPAY_CURRENCY),
    'reference' => $paymentId,
    'plan'      => $plan['name'],
    'cycle'     => $cycle,
    'contact'   => (string)($customer['phone'] ?? ''),
    'live'      => (bool)RAZORPAY_LIVE_MODE,
]);
