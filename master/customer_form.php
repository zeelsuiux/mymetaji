<?php
require_once __DIR__ . '/auth.php';
require_master_login();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/icons.php';

$action = $_GET['action'] ?? 'new';
$id = (int)($_GET['id'] ?? 0);
$errors = [];
$form = [
    'name' => '', 'phone' => '', 'email' => '', 'address' => '',
    'username' => '', 'password' => '',
    'plan_amount' => '', 'payment_type' => 'Cash', 'purchase_date' => date('Y-m-d'),
];
$existing = null;

if ($action === 'edit' && $id) {
    $existing = mdb_get('customers', $id);
    if (!$existing) { header('Location: customers.php'); exit; }
    $form = array_merge($form, $existing);
    $form['password'] = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $form['name'] = trim((string)($_POST['name'] ?? ''));
    $form['phone'] = trim((string)($_POST['phone'] ?? ''));
    $form['email'] = trim((string)($_POST['email'] ?? ''));
    $form['address'] = trim((string)($_POST['address'] ?? ''));
    $form['username'] = trim((string)($_POST['username'] ?? ''));
    $form['password'] = (string)($_POST['password'] ?? '');
    $form['plan_amount'] = trim((string)($_POST['plan_amount'] ?? ''));
    $form['payment_type'] = (string)($_POST['payment_type'] ?? 'Cash');
    $form['purchase_date'] = trim((string)($_POST['purchase_date'] ?? date('Y-m-d')));

    if ($form['name'] === '') $errors[] = 'Name is required.';
    if ($form['phone'] === '') $errors[] = 'Phone number is required.';
    if ($form['username'] === '') $errors[] = 'Username is required.';

    $allCustomers = mdb_all('customers');
    foreach ($allCustomers as $c) {
        if ((int)$c['id'] !== $id && strcasecmp((string)($c['username'] ?? ''), $form['username']) === 0) {
            $errors[] = 'This username is already used by another customer.';
        }
    }

    if ($id === 0) {
        if ($form['password'] === '') $errors[] = 'Password is required for a new customer.';
        if ($form['plan_amount'] === '' || !is_numeric($form['plan_amount'])) $errors[] = 'Subscription plan amount is required.';
        if ($form['purchase_date'] === '') $errors[] = 'Purchase date is required.';
    }

    if (!$errors && $id === 0) {
        // --- Create new customer + first subscription + provision their shop panel ---
        $customerId = mdb_insert('customers', [
            'name' => $form['name'], 'phone' => $form['phone'], 'email' => $form['email'],
            'address' => $form['address'], 'username' => $form['username'],
            'password_hash' => password_hash($form['password'], PASSWORD_DEFAULT),
            'slug' => '',
        ]);
        $customer = mdb_get('customers', $customerId);
        $customer['password_plain'] = $form['password'];

        $expiryDate = calc_expiry_date($form['purchase_date']);
        $subscription = [
            'amount' => (float)$form['plan_amount'],
            'payment_type' => $form['payment_type'],
            'purchase_date' => $form['purchase_date'],
            'expiry_date' => $expiryDate,
        ];

        $slug = provision_shop($customer, $subscription);
        mdb_update('customers', $customerId, ['slug' => $slug]);

        mdb_insert('subscriptions', array_merge(['customer_id' => $customerId], $subscription));

        header('Location: customer_detail.php?id=' . $customerId . '&ok=created');
        exit;
    } elseif (!$errors && $id > 0) {
        // --- Update contact info (and optionally username/password) for an existing customer ---
        $fields = [
            'name' => $form['name'], 'phone' => $form['phone'],
            'email' => $form['email'], 'address' => $form['address'],
            'username' => $form['username'],
        ];
        if ($form['password'] !== '') {
            $fields['password_hash'] = password_hash($form['password'], PASSWORD_DEFAULT);
        }
        mdb_update('customers', $id, $fields);
        $customer = mdb_get('customers', $id);
        if (!empty($customer['slug'])) {
            write_shop_login($customer['slug'], $customer, $form['password'] !== '' ? $form['password'] : null);
        }
        header('Location: customer_detail.php?id=' . $id . '&ok=updated');
        exit;
    }
}

$pageTitle = $id ? 'Edit Customer' : 'Add Customer';
$activePage = 'customers';
require __DIR__ . '/layout_top.php';
?>
<div class="panel">
    <div class="panel-header"><h2><?= icon('customers') ?> <?= $id ? 'Edit Customer' : 'Add New Customer' ?></h2></div>
    <div class="panel-body">
        <?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>
        <form method="post">
            <input type="hidden" name="id" value="<?= $id ?>">
            <h3 style="margin-top:0;">Customer Details</h3>
            <div class="form-grid">
                <div class="form-group"><label>Name *</label><input name="name" required value="<?= htmlspecialchars($form['name']) ?>"></div>
                <div class="form-group"><label>Number *</label><input name="phone" required value="<?= htmlspecialchars($form['phone']) ?>"></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($form['email']) ?>"></div>
                <div class="form-group"><label>Address</label><textarea name="address"><?= htmlspecialchars($form['address']) ?></textarea></div>
                <div class="form-group"><label>Username *</label><input name="username" required value="<?= htmlspecialchars($form['username']) ?>"></div>
                <div class="form-group"><label>Password <?= $id ? '(leave blank to keep current)' : '*' ?></label><input type="password" name="password" <?= $id ? '' : 'required' ?>></div>
            </div>

            <?php if (!$id): ?>
            <h3>First Subscription Purchase</h3>
            <div class="form-grid">
                <div class="form-group"><label>Subscription Plan Amount (₹) *</label><input type="number" step="0.01" name="plan_amount" required value="<?= htmlspecialchars($form['plan_amount']) ?>"></div>
                <div class="form-group"><label>Payment Type *</label>
                    <select name="payment_type">
                        <option value="Cash" <?= $form['payment_type'] === 'Cash' ? 'selected' : '' ?>>Cash</option>
                        <option value="Online" <?= $form['payment_type'] === 'Online' ? 'selected' : '' ?>>Online</option>
                    </select>
                </div>
                <div class="form-group"><label>Purchase Date *</label><input type="date" name="purchase_date" required value="<?= htmlspecialchars($form['purchase_date']) ?>"></div>
            </div>
            <p style="color:#6B7280;font-size:13px;">Expiry date will be set automatically to 1 year after the purchase date. A shopkeeper panel will be created automatically for this customer.</p>
            <?php endif; ?>

            <div class="form-actions">
                <a class="btn btn-outline" href="customers.php">Back</a>
                <button class="btn btn-primary" type="submit">Save Customer</button>
            </div>
        </form>
    </div>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>
