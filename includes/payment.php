<?php
/**
 * Payment layer shared by the public checkout and the master ledger.
 *
 * Two ledgers, deliberately separate:
 *
 *   payments.json      every payment attempt, in any state. The master Payments
 *                      screen reads this, so a buyer who abandoned or failed is
 *                      still visible and followable.
 *   subscriptions.json (master/db.php) the entitlement. Written ONLY once money
 *                      is actually captured, because the customer dashboard
 *                      derives "active / expiring / expired" from it. Putting a
 *                      failed attempt in here would report a customer as paid.
 *
 * Both rows carry razorpay_payment_id, so the two can always be joined.
 *
 * Nothing in here trusts the browser. The amount is always recomputed from
 * $plans in config.php, the capture is confirmed with Razorpay's own API
 * before it is recorded, and provisioning only runs after that confirmation.
 */
require_once __DIR__ . '/plans.php';
require_once __DIR__ . '/../admin/master/db.php';
require_once __DIR__ . '/../admin/master/helpers.php';
require_once __DIR__ . '/../admin/razorpay_config.php';

if (!defined('PAYMENT_TZ')) define('PAYMENT_TZ', 'Asia/Kolkata');
date_default_timezone_set(PAYMENT_TZ);

/** Payment states. 'captured' is the only one that grants access. */
function payment_statuses() {
    return ['pending', 'captured', 'failed', 'refunded'];
}

/** Human label + pill colour for a payment state. */
function payment_status_meta($status) {
    switch ($status) {
        case 'captured': return ['label' => 'Paid', 'class' => 'green'];
        case 'failed':   return ['label' => 'Failed', 'class' => 'red'];
        case 'refunded': return ['label' => 'Refunded', 'class' => 'orange'];
        case 'pending':  return ['label' => 'Awaiting', 'class' => 'gray'];
        default:         return ['label' => 'Unknown', 'class' => 'gray'];
    }
}

/* ------------------------------------------------------------------ store -- */

function payment_all() {
    $rows = mdb_all('payments');
    usort($rows, function ($a, $b) {
        $t = strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? ''));
        return $t !== 0 ? $t : ((int)($b['id'] ?? 0) <=> (int)($a['id'] ?? 0));
    });
    return $rows;
}

function payment_get($id) {
    return mdb_get('payments', (int)$id);
}

function payment_insert(array $record) {
    return mdb_insert('payments', $record);
}

function payment_update($id, array $fields) {
    return mdb_update('payments', $id, $fields);
}

/** The pending order for a Razorpay order id, if we created one. */
function payment_find_by_razorpay_order($orderId) {
    $orderId = trim((string)$orderId);
    if ($orderId === '') return null;
    foreach (payment_all() as $row) {
        if ((string)($row['razorpay_order_id'] ?? '') === $orderId) return $row;
    }
    return null;
}

/** True when this payment id has already been recorded, so we never double-charge the ledger. */
function payment_find_by_razorpay_payment($paymentId) {
    $paymentId = trim((string)$paymentId);
    if ($paymentId === '') return null;
    foreach (payment_all() as $row) {
        if ((string)($row['razorpay_payment_id'] ?? '') === $paymentId) return $row;
    }
    return null;
}

/** Record a failed attempt against the existing pending order, if we can find it. */
function payment_mark_failed($orderId, $reason) {
    $row = payment_find_by_razorpay_order($orderId);
    if ($row) payment_update($row['id'], ['status' => 'failed', 'failure_reason' => $reason]);
}

/**
 * Headline numbers for the master Payments screen.
 * Revenue counts captured money only.
 */
function payment_totals() {
    $captured = $pending = $failed = 0;
    $revenueAll = 0.0;
    $revenueMonth = 0.0;
    $monthStart = (new DateTime('first day of this month'))->format('Y-m-d');
    foreach (payment_all() as $row) {
        $status = (string)($row['status'] ?? '');
        $amount = (float)($row['amount'] ?? 0);
        if ($status === 'captured') {
            $captured++;
            $revenueAll += $amount;
            $when = substr((string)($row['paid_at'] ?? $row['created_at'] ?? ''), 0, 10);
            if ($when !== '' && $when >= $monthStart) $revenueMonth += $amount;
        } elseif ($status === 'pending') {
            $pending++;
        } elseif ($status === 'failed') {
            $failed++;
        }
    }
    return [
        'captured'     => $captured,
        'pending'      => $pending,
        'failed'       => $failed,
        'revenue_all'  => $revenueAll,
        'revenue_month'=> $revenueMonth,
    ];
}

/* ------------------------------------------------------------- razorpay -- */

function razorpay_is_configured() {
    $id = (string)RAZORPAY_KEY_ID;
    $secret = (string)RAZORPAY_KEY_SECRET;
    return $id !== '' && $secret !== ''
        && strpos($id, 'YOUR_') !== 0
        && strpos($secret, 'YOUR_') !== 0;
}

/**
 * Turn a failed gateway call into a message the visitor can act on.
 *
 * A 401 is our mistake, not theirs: the keys in admin/razorpay_config.php are
 * wrong or revoked, and no amount of retrying will change that. Telling such a
 * visitor to "try again in a moment" sends them away for nothing, so the
 * message has to say the payment is unavailable instead.
 */
function razorpay_failure_message(array $response) {
    $status = (int)($response['status'] ?? 0);
    if ($status === 401 || $status === 403) {
        return 'Online payment is not available right now. Please contact us and we will activate your plan manually.';
    }
    if ($status === 0) {
        return 'We could not reach the payment gateway. Please try again in a moment.';
    }
    return 'We could not start the payment. Please try again in a moment.';
}

/**
 * Record why an order could not be created.
 *
 * The visitor only ever sees razorpay_failure_message(); the real reason goes to
 * the error log, because a rejected key pair is invisible otherwise and looks
 * identical to a customer who simply closed the popup.
 */
function razorpay_log_failure($context, array $response) {
    $status = (int)($response['status'] ?? 0);
    $reason = (string)($response['error'] ?: 'unknown error');
    error_log(sprintf(
        'razorpay[%s] status=%d key=%s live=%s reason=%s',
        $context, $status, RAZORPAY_KEY_ID, RAZORPAY_LIVE_MODE ? 'yes' : 'no', $reason
    ));
    if ($status === 401 || $status === 403) {
        error_log('razorpay: keys in admin/razorpay_config.php were rejected by the gateway. Replace them with a valid pair.');
    }
}

/**
 * Check that the configured keys really authenticate.
 *
 * razorpay_is_configured() only proves the strings are filled in, and the pair
 * shipped in admin/razorpay_config.php is filled in but rejected by Razorpay.
 * That left a live "Pay ... Now" button that could never succeed. This does one
 * cheap authenticated read and caches the verdict, so the checkout page can
 * stop promising a payment that will fail without waiting on the network on
 * every page load.
 *
 * Returns ['ok' => bool, 'known' => bool, 'message' => string]. 'known' is
 * false while the answer is still unknown or the probe could not run, which is
 * deliberately not treated as failure - a gateway outage must not switch off
 * payments.
 */
function razorpay_credentials_status($maxAge = 900) {
    if (!razorpay_is_configured()) {
        return ['ok' => false, 'known' => true, 'message' => 'Online payment is not switched on yet.'];
    }
    $fingerprint = substr(hash('sha256', RAZORPAY_KEY_ID . '|' . RAZORPAY_KEY_SECRET . '|' . (RAZORPAY_LIVE_MODE ? '1' : '0')), 0, 16);
    $cacheFile = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'razorpay_cred_' . $fingerprint . '.json';

    if (is_readable($cacheFile)) {
        $cached = json_decode((string)@file_get_contents($cacheFile), true);
        if (is_array($cached) && isset($cached['at']) && (time() - (int)$cached['at']) < $maxAge) {
            return [
                'ok' => (bool)$cached['ok'],
                'known' => true,
                'message' => (string)($cached['message'] ?? ''),
            ];
        }
    }

    $response = razorpay_api('GET', 'orders?count=1');
    $ok = !empty($response['ok']);
    $message = $ok ? '' : razorpay_failure_message($response);
    if (!$ok) razorpay_log_failure('credentials-probe', $response);

    @file_put_contents($cacheFile, json_encode([
        'at' => time(), 'ok' => $ok, 'message' => $message,
    ]), LOCK_EX);

    return ['ok' => $ok, 'known' => true, 'message' => $message];
}

/**
 * Call the Razorpay REST API. Returns ['ok'=>bool, 'status'=>int, 'data'=>array, 'error'=>string].
 * Never throws, so callers can turn a network failure into a clean 502.
 */
function razorpay_api($method, $path, array $payload = null) {
    $url = 'https://api.razorpay.com/v1/' . ltrim($path, '/');
    $curl = curl_init($url);
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD        => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_CONNECTTIMEOUT => 10,
    ];
    if ($payload !== null) {
        $options[CURLOPT_POST]       = true;
        $options[CURLOPT_POSTFIELDS] = json_encode($payload);
        $options[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
    }
    curl_setopt_array($curl, $options);
    $body = curl_exec($curl);
    $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    curl_close($curl);

    $data = json_decode((string)$body, true);
    if ($error !== '') return ['ok' => false, 'status' => 0, 'data' => [], 'error' => $error];
    if ($status < 200 || $status >= 300) {
        $desc = is_array($data) ? (string)($data['error']['description'] ?? '') : '';
        return ['ok' => false, 'status' => $status, 'data' => is_array($data) ? $data : [], 'error' => $desc ?: 'Razorpay error ' . $status];
    }
    return ['ok' => true, 'status' => $status, 'data' => is_array($data) ? $data : [], 'error' => ''];
}

/* ---------------------------------------------------------------- capture -- */

/**
 * Turn a confirmed Razorpay payment into access, exactly once.
 *
 * $payment is Razorpay's payment object. $row is our pending payments.json row.
 * Re-running it for a payment id that is already captured is a no-op that
 * returns the same result, so the browser's verify call and the webhook can
 * both call it safely.
 *
 * Returns ['ok'=>bool, 'error'=>string, 'customer'=>array, 'subscription'=>array, 'created'=>bool].
 */
function payment_capture(array $payment, array $row) {
    $paymentId = trim((string)($payment['id'] ?? ''));
    $orderId   = trim((string)($payment['order_id'] ?? ''));
    if ($paymentId === '' || $orderId === '') {
        return ['ok' => false, 'error' => 'Razorpay did not return a payment reference.'];
    }
    if ((string)($payment['status'] ?? '') !== 'captured') {
        return ['ok' => false, 'error' => 'Payment is not captured yet.'];
    }
    if ((string)($row['razorpay_order_id'] ?? '') !== $orderId) {
        return ['ok' => false, 'error' => 'Payment does not belong to this order.'];
    }

    // The amount Razorpay captured must be the amount we asked for. This is the
    // check that stops a tampered or mismatched payment from granting a plan.
    $capturedPaise = (int)($payment['amount'] ?? 0);
    $expectedPaise = (int)round(((float)($row['amount'] ?? 0)) * 100);
    if ($capturedPaise !== $expectedPaise) {
        return ['ok' => false, 'error' => 'Captured amount does not match the order.'];
    }
    if (strtoupper((string)($payment['currency'] ?? '')) !== strtoupper((string)RAZORPAY_CURRENCY)) {
        return ['ok' => false, 'error' => 'Unexpected payment currency.'];
    }

    // Already done: a second call (verify after webhook, or a double submit).
    $existing = payment_find_by_razorpay_payment($paymentId);
    if ($existing && (string)($existing['status'] ?? '') === 'captured') {
        $customer = $existing['customer_id'] ? mdb_get('customers', $existing['customer_id']) : null;
        $subscription = null;
        foreach (customer_subscriptions($existing['customer_id']) as $sub) {
            if ((string)($sub['transaction_id'] ?? '') === $paymentId) { $subscription = $sub; break; }
        }
        return [
            'ok' => true, 'error' => '', 'created' => false, 'already' => true,
            'customer' => $customer ?: [], 'subscription' => $subscription ?: [],
            'payment' => $existing,
        ];
    }

    $plan  = plan_find_or_die($row['plan'] ?? '');
    $cycle = plan_normalise_cycle($row['cycle'] ?? 'monthly');
    $name    = trim((string)($row['customer_name'] ?? ''));
    $phone   = trim((string)($row['customer_phone'] ?? ''));
    $email   = strtolower(trim((string)($row['customer_email'] ?? '')));
    $address = trim((string)($row['customer_address'] ?? ''));
    if ($name === '' || $phone === '') {
        return ['ok' => false, 'error' => 'Buyer name and phone are required.'];
    }

    // Reuse the customer when the email or phone is already on file, so a buyer
    // who renews does not become a second shop.
    $customer = null;
    foreach (mdb_all('customers') as $c) {
        $sameEmail = $email !== '' && strcasecmp((string)($c['email'] ?? ''), $email) === 0;
        $samePhone = trim((string)($c['phone'] ?? '')) === $phone;
        if ($sameEmail || $samePhone) { $customer = $c; break; }
    }

    $created = false;
    $password = '';
    if (!$customer) {
        $password = generate_random_password(10);
        $customerId = mdb_insert('customers', [
            'name'          => $name,
            'phone'         => $phone,
            'email'         => $email,
            'address'       => $address,
            'username'      => generate_customer_username($name, $email, $phone),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'slug'          => '',
        ]);
        $customer = mdb_get('customers', $customerId);
        $created = true;
    }
    $customerId = (int)$customer['id'];

    $purchaseDate = date('Y-m-d');
    $latest = customer_latest_subscription($customerId);
    $subscription = [
        'amount'         => (float)($row['amount'] ?? 0),
        'payment_type'   => 'Razorpay',
        'purchase_date'  => $purchaseDate,
        'expiry_date'    => plan_expiry_date($purchaseDate, $cycle),
        'plan'           => $plan['name'],
        'cycle'          => $cycle,
        // Explicitly false, not merely absent: a shop that has just paid must
        // stop being described as "on trial" in its own panel and in the
        // master's renewal reminders.
        'is_trial'       => false,
        'razorpay_order_id'   => $orderId,
        'razorpay_payment_id' => $paymentId,
        'transaction_id'      => $paymentId,
    ];

    if ($created) {
        // A brand new buyer: give them their shop panel and login straight away.
        $customer['password_plain'] = $password;
        $slug = provision_shop($customer, $subscription);
        mdb_update('customers', $customerId, ['slug' => $slug]);
        $customer = mdb_get('customers', $customerId);
    } else {
        $subscription['expiry_date'] = calc_renewal_expiry_date(
            $latest['expiry_date'] ?? '',
            $purchaseDate,
            plan_cycle_months($cycle)
        );
        if (!empty($customer['slug'])) {
            write_shop_license($customer['slug'], $customer, $subscription);
        }
    }

    $subscriptionId = mdb_insert('subscriptions', array_merge(['customer_id' => $customerId], $subscription));
    if ($row['id'] ?? 0) {
        payment_update($row['id'], [
            'status'             => 'captured',
            'customer_id'        => $customerId,
            'razorpay_payment_id'=> $paymentId,
            'subscription_id'    => (int)$subscriptionId,
            'expiry_date'        => $subscription['expiry_date'],
            'paid_at'            => date('Y-m-d H:i:s'),
            'payment_method'     => (string)($payment['method'] ?? 'razorpay'),
            'failure_reason'     => '',
        ]);
    }

    return [
        'ok' => true, 'error' => '', 'created' => $created, 'already' => false,
        'customer' => is_array($customer) ? $customer : [],
        'subscription' => $subscription,
        'password' => $password,
    ];
}

/* ------------------------------------------------------------------ trial -- */

/** Days a free trial runs for. Matches calc_trial_expiry_date() in the master. */
function payment_trial_days() {
    return 15;
}

/**
 * Start a 15-day free trial for a brand new buyer.
 *
 * This is the front door: a new customer gets the trial first, and the paid
 * tiers are what they pay when it runs out. Creates the same four records the
 * panel's own signup page creates - customer, Trial subscription, shop panel,
 * signup lead - so master sees a trial exactly like any other subscription.
 *
 * The tier the buyer intends to buy is stored on the trial subscription, which
 * is what the renewal button and the licence read later to know what to charge.
 *
 * Returns ['ok'=>bool, 'error'=>string, 'customer'=>array, 'subscription'=>array].
 */
function payment_start_trial(array $buyer, array $plan, $cycle = 'yearly') {
    $email = strtolower(trim((string)($buyer['email'] ?? '')));
    $phone = trim((string)($buyer['phone'] ?? ''));
    $cycle = plan_normalise_cycle($cycle);

    foreach (mdb_all('customers') as $existing) {
        $sameEmail = $email !== '' && strcasecmp((string)($existing['email'] ?? ''), $email) === 0;
        $samePhone = $phone !== '' && trim((string)($existing['phone'] ?? '')) === $phone;
        if ($sameEmail || $samePhone) {
            return ['ok' => false, 'error' => 'This mobile number or email is already registered. Please sign in and renew from your dashboard, or call us and we will help.'];
        }
    }

    $password = generate_random_password(10);
    $username = generate_customer_username($buyer['name'], $email, $phone);
    $customerId = mdb_insert('customers', [
        'name'          => $buyer['name'],
        'phone'         => $phone,
        'email'         => $email,
        'address'       => (string)($buyer['address'] ?? ''),
        'username'      => $username,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'slug'          => '',
    ]);
    $customer = mdb_get('customers', $customerId);

    $start = date('Y-m-d');
    $subscription = [
        // A trial is not a sale, so no money is recorded against it.
        'amount'        => 0.0,
        'payment_type'  => 'Trial',
        'purchase_date' => $start,
        'expiry_date'   => calc_trial_expiry_date($start),
        'is_trial'      => true,
        'plan'          => $plan['name'],
        'cycle'         => $cycle,
        'trial_days'    => payment_trial_days(),
    ];

    $customer['password_plain'] = $password;
    $slug = provision_shop($customer, $subscription);
    mdb_update('customers', $customerId, ['slug' => $slug]);
    // Re-read: the slug is only known after provisioning, and the success page
    // links straight to the shop panel from this record.
    $customer = mdb_get('customers', $customerId);

    mdb_insert('subscriptions', array_merge(['customer_id' => $customerId], $subscription));
    mdb_insert('leads', [
        'name'          => $buyer['name'],
        'phone'         => $phone,
        'email'         => $email,
        'address'       => (string)($buyer['address'] ?? ''),
        'username'      => $username,
        'customer_id'   => $customerId,
        'password_hash' => $customer['password_hash'],
        'status'        => 'Trial',
        'source'        => 'Website trial',
        'plan'          => $plan['name'],
        'cycle'         => $cycle,
    ]);

    return [
        'ok' => true, 'error' => '',
        'customer' => array_merge($customer, ['password_plain' => $password]),
        'subscription' => $subscription,
    ];
}

/* ------------------------------------------------------------------ misc -- */

/**
 * Make sure the per-purpose token store is an array.
 *
 * Tokens used to be a single string at $_SESSION['payment_csrf']. They are now
 * keyed by purpose, so a session created before that change still holds a string
 * here and indexing it would be a fatal TypeError on checkout.php. Normalise on
 * read: an old token is dropped and a fresh one minted, which costs the visitor
 * one form load and nothing else.
 *
 * Reads and writes $_SESSION directly rather than passing the array around -
 * PHP arrays are copy-on-write, so a returned copy would silently discard every
 * mint and every unset.
 */
function payment_csrf_store_init() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!isset($_SESSION['payment_csrf']) || !is_array($_SESSION['payment_csrf'])) {
        $_SESSION['payment_csrf'] = [];
    }
}

/** Clean a purpose name down to something safe to use as an array key. */
function payment_csrf_purpose($purpose) {
    return preg_replace('/[^a-z0-9_-]/i', '', (string)$purpose) ?: 'form';
}

/**
 * Session CSRF token for the public forms and API calls.
 *
 * Tokens are scoped by purpose and single-use. The purpose matters: a checkout
 * spends one token creating the order and another confirming it, so a spent
 * "checkout" token cannot be replayed and cannot accidentally fail the verify
 * call that follows it.
 */
function payment_csrf_token($purpose = 'form') {
    payment_csrf_store_init();
    $purpose = payment_csrf_purpose($purpose);
    if (empty($_SESSION['payment_csrf'][$purpose]) || !is_string($_SESSION['payment_csrf'][$purpose])) {
        $_SESSION['payment_csrf'][$purpose] = bin2hex(random_bytes(32));
    }
    return $_SESSION['payment_csrf'][$purpose];
}

/** Constant-time CSRF check for the public API. Consumes the token on success. */
function payment_csrf_check($token, $purpose = 'form') {
    payment_csrf_store_init();
    $purpose = payment_csrf_purpose($purpose);
    $expected = (string)($_SESSION['payment_csrf'][$purpose] ?? '');
    $token = trim((string)$token);
    if ($expected === '' || $token === '' || !hash_equals($expected, $token)) return false;
    // One token per request: a replayed form post cannot reuse it.
    unset($_SESSION['payment_csrf'][$purpose]);
    return true;
}

/** JSON reply + exit, so the API endpoints all fail the same way. */
function payment_json($status, array $body) {
    http_response_code((int)$status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($body);
    exit;
}

/** POST-only guard for the public endpoints. */
function payment_require_post() {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        payment_json(405, ['ok' => false, 'error' => 'POST required.']);
    }
}

/** Shared validation for the buyer details on a checkout. Returns [$clean, $errors]. */
function payment_clean_buyer(array $input) {
    $clean = [
        'name'    => trim(preg_replace('/\s+/', ' ', (string)($input['name'] ?? ''))),
        'email'   => strtolower(trim((string)($input['email'] ?? ''))),
        'phone'   => trim((string)($input['phone'] ?? '')),
        'address' => trim((string)($input['address'] ?? '')),
    ];
    $errors = [];
    if ($clean['name'] === '' || mb_strlen($clean['name']) < 2) $errors[] = 'Please enter your name.';
    if ($clean['phone'] === '' || !preg_match('/^[0-9]{10}$/', $clean['phone'])) $errors[] = 'Enter a 10 digit mobile number.';
    if ($clean['email'] === '' || !filter_var($clean['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    return [$clean, $errors];
}
