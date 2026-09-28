<?php
/**
 * Razorpay webhook: the safety net for buyers who close the tab after paying.
 *
 * The browser normally reports the payment itself, but that call can be lost -
 * closed tab, dropped connection, phone locking mid-redirect. Razorpay then
 * retries this endpoint until it gets a 200, so the purchase still reaches the
 * master ledger.
 *
 * Point a webhook at this URL for the `payment.captured` event. The
 * X-Razorpay-Signature header is checked against the webhook secret from the
 * dashboard; with no secret configured the endpoint refuses every request
 * rather than trusting an unverified callback.
 */
require_once __DIR__ . '/../includes/payment.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    payment_json(405, ['ok' => false, 'error' => 'POST required.']);
}

$secret = trim((string)RAZORPAY_WEBHOOK_SECRET);
if ($secret === '') {
    payment_json(501, ['ok' => false, 'error' => 'No webhook secret configured. Set RAZORPAY_WEBHOOK_SECRET.']);
}

$body = (string)file_get_contents('php://input');
$signature = trim((string)($_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? ''));
if ($signature === '' || !hash_equals(hash_hmac('sha256', $body, $secret), $signature)) {
    payment_json(400, ['ok' => false, 'error' => 'Invalid webhook signature.']);
}

$event = json_decode($body, true);
if (!is_array($event)) payment_json(400, ['ok' => false, 'error' => 'Malformed webhook body.']);

$name = (string)($event['event'] ?? '');
if ($name !== 'payment.captured') {
    // Everything else is acknowledged without action, otherwise Razorpay keeps
    // retrying an endpoint that will never act on it.
    payment_json(200, ['ok' => true, 'ignored' => $name]);
}

$gatewayPayment = $event['payload']['payment']['entity'] ?? [];
if (!is_array($gatewayPayment) || empty($gatewayPayment['id'])) {
    payment_json(400, ['ok' => false, 'error' => 'Webhook carried no payment.']);
}

$row = payment_find_by_razorpay_order((string)($gatewayPayment['order_id'] ?? ''));
if (!$row) {
    // Not one of ours. Nothing to do, and nothing to tell an outsider.
    payment_json(200, ['ok' => true, 'ignored' => 'unknown_order']);
}

$result = payment_capture($gatewayPayment, $row);
if (!$result['ok']) {
    payment_json(400, ['ok' => false, 'error' => $result['error']]);
}

payment_json(200, ['ok' => true, 'already' => (bool)($result['already'] ?? false)]);
