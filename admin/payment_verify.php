<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/razorpay_config.php';
require_once __DIR__ . '/master/db.php';
require_once __DIR__ . '/master/helpers.php';

header('Content-Type: application/json');
$orderId = trim((string)($_POST['razorpay_order_id'] ?? ''));
$paymentId = trim((string)($_POST['razorpay_payment_id'] ?? ''));
$signature = trim((string)($_POST['razorpay_signature'] ?? ''));
$license = get_license();
if ($orderId === '' || $paymentId === '' || $signature === '' || !$license) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Incomplete payment details.']);
    exit;
}
$expected = hash_hmac('sha256', $orderId . '|' . $paymentId, RAZORPAY_KEY_SECRET);
if (!hash_equals($expected, $signature)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Payment signature verification failed.']);
    exit;
}

$curl = curl_init('https://api.razorpay.com/v1/payments/' . rawurlencode($paymentId));
curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_USERPWD => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET, CURLOPT_TIMEOUT => 20]);
$response = curl_exec($curl);
$status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
$error = curl_error($curl);
curl_close($curl);
$payment = json_decode((string)$response, true);
if ($error || $status < 200 || $status >= 300 || !is_array($payment) || ($payment['status'] ?? '') !== 'captured' || ($payment['order_id'] ?? '') !== $orderId) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Payment is not captured yet.']);
    exit;
}

$customerId = (int)($license['customer_id'] ?? 0);
$customer = mdb_get('customers', $customerId);
if (!$customer || empty($customer['slug'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Customer account was not found.']);
    exit;
}
foreach (mdb_all('subscriptions') as $subscription) {
    if (($subscription['transaction_id'] ?? '') === $paymentId) {
        echo json_encode(['ok' => true, 'message' => 'Payment already recorded.']);
        exit;
    }
}
$purchaseDate = date('Y-m-d');
$subscription = [
    'amount' => ((int)($payment['amount'] ?? 0)) / 100,
    'payment_type' => 'Razorpay',
    'purchase_date' => $purchaseDate,
    'expiry_date' => calc_renewal_expiry_date($license['expiry_date'] ?? '', $purchaseDate),
    'razorpay_order_id' => $orderId,
    'razorpay_payment_id' => $paymentId,
    'transaction_id' => $paymentId,
];
mdb_insert('subscriptions', array_merge(['customer_id' => $customerId], $subscription));
write_shop_license($customer['slug'], $customer, $subscription);
echo json_encode(['ok' => true, 'message' => 'Payment successful. Your subscription is active again.']);
