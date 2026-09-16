<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/razorpay_config.php';

header('Content-Type: application/json');
if (RAZORPAY_KEY_ID === 'YOUR_RAZORPAY_KEY_ID' || RAZORPAY_KEY_SECRET === 'YOUR_RAZORPAY_KEY_SECRET') {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Razorpay payment setup is not configured yet.']);
    exit;
}

$license = get_license();
$amount = (float)($license['plan_amount'] ?? RAZORPAY_PLAN_AMOUNT);
if ($amount <= 0) $amount = (float)RAZORPAY_PLAN_AMOUNT;
$payload = json_encode([
    'amount' => (int)round($amount * 100),
    'currency' => RAZORPAY_CURRENCY,
    'receipt' => 'cst_' . (string)($license['customer_id'] ?? 'customer') . '_' . time(),
    'notes' => ['customer_id' => (string)($license['customer_id'] ?? '')],
]);

$curl = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_USERPWD => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT => 20,
]);
$response = curl_exec($curl);
$status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
$error = curl_error($curl);
curl_close($curl);
$data = json_decode((string)$response, true);
if ($error || $status < 200 || $status >= 300 || !is_array($data) || empty($data['id'])) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => 'Unable to create Razorpay order.', 'detail' => $error ?: ($data['error']['description'] ?? 'Razorpay error')]);
    exit;
}
echo json_encode(['ok' => true, 'key_id' => RAZORPAY_KEY_ID, 'order_id' => $data['id'], 'amount' => $data['amount'], 'currency' => $data['currency']]);
