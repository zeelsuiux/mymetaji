<?php
/**
 * In-app renewal: confirm the payment and grant the extra time.
 *
 * Does exactly what the public checkout does, in the same order:
 *   1. the HMAC signature the browser returned matches the order and payment ids
 *   2. Razorpay's own API says the payment exists, is captured, and belongs to
 *      the order we created for this customer
 *   3. the captured amount and currency are the ones we asked for
 *
 * Only then does payment_capture() write the subscription, extend the expiry from
 * the licence's current date, and mark the pending ledger row as paid. Running it
 * twice for the same payment id returns the same result and writes nothing new,
 * so a double click or a retried request cannot grant two periods.
 */
require_once __DIR__ . '/auth.php';
require_login();
csrf_verify();

require_once __DIR__ . '/license.php';
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

$orderId   = trim((string)($_POST['razorpay_order_id'] ?? ''));
$gatewayId = trim((string)($_POST['razorpay_payment_id'] ?? ''));
$signature = trim((string)($_POST['razorpay_signature'] ?? ''));
if ($orderId === '' || $gatewayId === '' || $signature === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Incomplete payment details.']);
    exit;
}

$expected = hash_hmac('sha256', $orderId . '|' . $gatewayId, RAZORPAY_KEY_SECRET);
if (!hash_equals($expected, $signature)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Payment signature verification failed.']);
    exit;
}

$row = payment_find_by_razorpay_order($orderId);
if (!$row) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'We could not find this order. Please contact support.']);
    exit;
}

// The order has to belong to the shop that is paying, or any signed-in customer
// could confirm somebody else's order id.
if ((int)($row['customer_id'] ?? 0) !== (int)$license['customer_id']) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'This order does not belong to your account.']);
    exit;
}
if ((string)($row['status'] ?? '') === 'failed') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'This payment attempt was already marked failed. Please try again.']);
    exit;
}

$response = razorpay_api('GET', 'payments/' . rawurlencode($gatewayId));
if (!$response['ok'] || empty($response['data']['id'])) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => 'Could not confirm the payment with the gateway. Please try again.']);
    exit;
}

$result = payment_capture($response['data'], $row);
if (!$result['ok']) {
    payment_mark_failed($orderId, $result['error']);
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $result['error']]);
    exit;
}

echo json_encode([
    'ok'         => true,
    'already'    => (bool)($result['already'] ?? false),
    'reference'  => (int)($row['id'] ?? 0),
    'plan'       => (string)($result['subscription']['plan'] ?? ($row['plan'] ?? '')),
    'cycle'      => (string)($result['subscription']['cycle'] ?? ($row['cycle'] ?? 'yearly')),
    'amount'     => (float)($row['amount'] ?? 0),
    'expiry_date'=> (string)($result['subscription']['expiry_date'] ?? ''),
    'message'    => 'Payment successful. Your subscription has been extended.',
]);
