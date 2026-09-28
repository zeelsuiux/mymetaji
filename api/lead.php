<?php
/**
 * Public endpoint: Enterprise / sales enquiry.
 *
 * Enterprise is sold by the sales team rather than through a self-serve
 * checkout, so the card button posts here instead. The enquiry lands in the
 * master's leads store and shows up on the Signup Leads screen, tagged with the
 * tier it was raised against, so nothing is lost in an inbox.
 */
require_once __DIR__ . '/../includes/payment.php';

payment_require_post();

if (!payment_csrf_check($_POST['csrf'] ?? '', 'lead')) {
    payment_json(400, ['ok' => false, 'error' => 'Your session expired. Please reload the page and try again.']);
}

$plan = plan_find($_POST['plan'] ?? '');
if (!$plan) payment_json(400, ['ok' => false, 'error' => 'Please tell us which plan you are interested in.']);
// This endpoint is the sales path, not a second way into the checkout. A plan
// that can be paid online has to go through payment_create_order.php, so a
// self-serve tier cannot be smuggled in here and sit un-paid in the leads list.
if (plan_is_online($plan)) {
    payment_json(400, ['ok' => false, 'error' => $plan['name'] . ' can be paid online. Please use the checkout for that plan.']);
}

[$buyer, $errors] = payment_clean_buyer($_POST);
$message = trim((string)($_POST['message'] ?? ''));
if (mb_strlen($message) > 2000) $errors[] = 'Please keep the message under 2000 characters.';
if ($errors) payment_json(400, ['ok' => false, 'error' => implode(' ', $errors)]);

$leadId = mdb_insert('leads', [
    'name'     => $buyer['name'],
    'phone'    => $buyer['phone'],
    'email'    => $buyer['email'],
    'address'  => $buyer['address'],
    'company'  => trim((string)($_POST['company'] ?? '')),
    'status'   => 'New',
    'source'   => 'Enterprise enquiry',
    'plan'     => $plan['name'],
    'message'  => $message,
    'username' => '',
]);

payment_json(200, [
    'ok' => true,
    'lead_id' => (int)$leadId,
    'message' => 'Thank you. Our sales team will call you within one working day.',
]);
