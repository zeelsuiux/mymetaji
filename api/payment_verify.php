<?php
/**
 * Public endpoint: confirm a payment and hand over the account.
 *
 * Three independent checks before anything is recorded:
 *   1. the HMAC signature the browser returned matches the order and payment ids
 *   2. Razorpay's own API says the payment exists, is captured, and belongs to
 *      the order we created
 *   3. the captured amount and currency are the ones we asked for
 *
 * Only then does payment_capture() create the customer, provision the shop and
 * write the subscription. The new shop's password is returned once, for the
 * success page to show; it is never written to disk and never put in a URL.
 */
require_once __DIR__ . '/../includes/payment.php';

payment_require_post();

if (!razorpay_is_configured()) {
    payment_json(503, ['ok' => false, 'error' => 'Payments are not configured yet.']);
}

if (!payment_csrf_check($_POST['csrf'] ?? '', 'verify')) {
    payment_json(400, ['ok' => false, 'error' => 'Your session expired. Please reload the page and try again.']);
}

$orderId    = trim((string)($_POST['razorpay_order_id'] ?? ''));
$gatewayId  = trim((string)($_POST['razorpay_payment_id'] ?? ''));
$signature  = trim((string)($_POST['razorpay_signature'] ?? ''));
if ($orderId === '' || $gatewayId === '' || $signature === '') {
    payment_json(400, ['ok' => false, 'error' => 'Incomplete payment details.']);
}

$expected = hash_hmac('sha256', $orderId . '|' . $gatewayId, RAZORPAY_KEY_SECRET);
if (!hash_equals($expected, $signature)) {
    payment_json(400, ['ok' => false, 'error' => 'Payment signature verification failed.']);
}

$row = payment_find_by_razorpay_order($orderId);
if (!$row) {
    payment_json(400, ['ok' => false, 'error' => 'We could not find this order. Please contact support.']);
}
if ((string)($row['status'] ?? '') === 'failed') {
    payment_json(400, ['ok' => false, 'error' => 'This payment attempt was already marked failed. Please start again.']);
}

$response = razorpay_api('GET', 'payments/' . rawurlencode($gatewayId));
if (!$response['ok'] || empty($response['data']['id'])) {
    payment_json(502, ['ok' => false, 'error' => 'Could not confirm the payment with the gateway. Please try again.']);
}

$result = payment_capture($response['data'], $row);
if (!$result['ok']) {
    payment_mark_failed($orderId, $result['error']);
    payment_json(400, ['ok' => false, 'error' => $result['error']]);
}

$customer = $result['customer'];
$subscription = $result['subscription'];

// Hand the one-time password to the success page through the session, not the
// query string, so it never lands in browser history or a server access log.
if (!empty($result['password']) && !empty($row['id'])) {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION['payment_credentials'] = [
        'payment_id' => (int)$row['id'],
        'username'   => (string)($customer['username'] ?? ''),
        'password'   => (string)$result['password'],
    ];
}

payment_json(200, [
    'ok'           => true,
    'already'      => (bool)($result['already'] ?? false),
    'created'      => (bool)$result['created'],
    'reference'    => (int)($row['id'] ?? 0),
    'plan'         => (string)($subscription['plan'] ?? ($row['plan'] ?? '')),
    'cycle'        => (string)($subscription['cycle'] ?? ($row['cycle'] ?? 'monthly')),
    'amount'       => (float)($row['amount'] ?? 0),
    'expiry_date'  => (string)($subscription['expiry_date'] ?? ''),
    'username'     => (string)($customer['username'] ?? ''),
    'panel_ready'  => !empty($customer['slug']),
    'redirect'     => 'payment-success.php?ref=' . (int)($row['id'] ?? 0),
]);
