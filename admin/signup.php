<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/master/db.php';
require_once __DIR__ . '/master/helpers.php';
require_once __DIR__ . '/razorpay_config.php';
require_once __DIR__ . '/icons.php';

$errors = [];
$form = ['name' => '', 'phone' => '', 'email' => '', 'address' => ''];
$tenantCode = '';
$sourceTenant = panel_tenant_code();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form as $field => $value) $form[$field] = trim((string)($_POST[$field] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if ($form['name'] === '') $errors[] = 'Name is required.';
    if ($form['phone'] === '') $errors[] = 'Phone number is required.';
    if ($password === '') $errors[] = 'Password is required.';
    if ($form['email'] !== '' && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';

    foreach (mdb_all('customers') as $customer) {
        if ($form['email'] !== '' && strcasecmp((string)($customer['email'] ?? ''), $form['email']) === 0) $errors[] = 'This email is already registered.';
        if (strcasecmp((string)($customer['phone'] ?? ''), $form['phone']) === 0) $errors[] = 'This phone number is already registered.';
    }

    if (!$errors) {
        $username = generate_customer_username($form['name'], $form['email'], $form['phone']);
        $customerId = mdb_insert('customers', [
            'name' => $form['name'], 'phone' => $form['phone'], 'email' => $form['email'],
            'address' => $form['address'], 'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'slug' => '',
        ]);
        $customer = mdb_get('customers', $customerId);
        $customer['password_plain'] = $password;
        $subscription = [
            'amount' => (float)RAZORPAY_PLAN_AMOUNT,
            'payment_type' => 'Trial',
            'purchase_date' => date('Y-m-d'),
            'expiry_date' => calc_trial_expiry_date(),
            'is_trial' => true,
        ];
        $tenantCode = provision_shop($customer, $subscription);
        mdb_update('customers', $customerId, ['slug' => $tenantCode]);
        mdb_insert('subscriptions', array_merge(['customer_id' => $customerId], $subscription));
        mdb_insert('leads', [
            'name' => $form['name'], 'phone' => $form['phone'], 'email' => $form['email'],
            'address' => $form['address'], 'username' => $username, 'customer_id' => $customerId,
            'password_hash' => $customer['password_hash'], 'status' => 'Trial',
        ]);
        $loginPath = $sourceTenant !== '' ? '../' . $tenantCode . '/login.php' : $tenantCode . '/login.php';
        header('Location: ' . $loginPath . '?registered=1');
        exit;
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Register</title><link rel="stylesheet" href="assets/style.css"></head><body class="login-page">
<main class="login-card"><div class="login-brand"><img src="assets/logo.png" alt="Logo"></div><h1>Register Your Business</h1>
<?php if (!empty($success)): ?><div class="alert">Your 15-day free trial is active. You can login now.</div><a class="btn btn-primary login-button" href="<?= htmlspecialchars($tenantCode) ?>/login.php">Go to Customer Login</a><?php else: ?>
<?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>
<form method="post" class="login-form"><label>Name</label><input name="name" required value="<?= htmlspecialchars($form['name']) ?>"><label>Phone</label><input name="phone" required value="<?= htmlspecialchars($form['phone']) ?>"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($form['email']) ?>"><label>Address</label><input name="address" value="<?= htmlspecialchars($form['address']) ?>"><label>Password</label><div class="password-field"><input id="signup-password" type="password" name="password" required autocomplete="new-password"><button type="button" class="password-toggle" aria-label="Show password" title="Show password" data-target="signup-password"><span class="password-icon-show"><?= icon('view', 16) ?></span><span class="password-icon-hide password-icon-hidden"><?= icon('eye-off', 16) ?></span></button></div><button class="btn btn-primary login-button" type="submit">Submit Registration</button></form>
<?php endif; ?></main></body></html>
<script>document.querySelectorAll('.password-toggle').forEach(function (button) { button.addEventListener('click', function () { var input = document.getElementById(button.dataset.target); var showIcon = button.querySelector('.password-icon-show'); var hideIcon = button.querySelector('.password-icon-hide'); var showing = input.type === 'password'; input.type = showing ? 'text' : 'password'; showIcon.classList.toggle('password-icon-hidden', showing); hideIcon.classList.toggle('password-icon-hidden', !showing); button.setAttribute('aria-label', showing ? 'Hide password' : 'Show password'); button.setAttribute('title', showing ? 'Hide password' : 'Show password'); }); });</script>