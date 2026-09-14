<?php
require_once __DIR__ . '/auth.php';
require_master_login();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/icons.php';

$id = (int)($_GET['id'] ?? 0);
$customer = mdb_get('customers', $id);
if (!$customer) { header('Location: customers.php'); exit; }

$errors = [];
$notice = '';
$newPasswordToShow = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = $_POST['form_action'] ?? '';

    if ($formAction === 'renew') {
        $amount = trim((string)($_POST['plan_amount'] ?? ''));
        $paymentType = (string)($_POST['payment_type'] ?? 'Cash');
        $purchaseDate = trim((string)($_POST['purchase_date'] ?? date('Y-m-d')));

        if ($amount === '' || !is_numeric($amount)) $errors[] = 'Subscription plan amount is required.';
        if ($purchaseDate === '') $errors[] = 'Purchase date is required.';

        if (!$errors) {
            $expiryDate = calc_expiry_date($purchaseDate);
            $subscription = [
                'amount' => (float)$amount,
                'payment_type' => $paymentType,
                'purchase_date' => $purchaseDate,
                'expiry_date' => $expiryDate,
            ];
            mdb_insert('subscriptions', array_merge(['customer_id' => $id], $subscription));
            if (!empty($customer['slug'])) {
                write_shop_license($customer['slug'], $customer, $subscription);
            }
            header('Location: customer_detail.php?id=' . $id . '&ok=renewed');
            exit;
        }
    } elseif ($formAction === 'reset_password') {
        $newPassword = trim((string)($_POST['new_password'] ?? ''));
        if ($newPassword === '') $newPassword = generate_random_password();
        mdb_update('customers', $id, ['password_hash' => password_hash($newPassword, PASSWORD_DEFAULT)]);
        $customer = mdb_get('customers', $id);
        if (!empty($customer['slug'])) {
            write_shop_login($customer['slug'], $customer, $newPassword);
        }
        $newPasswordToShow = $newPassword;
        $notice = 'Password reset successfully. Share this new password with the shopkeeper securely: ';
    } elseif ($formAction === 'delete') {
        if (!empty($customer['slug'])) {
            recursive_delete(shop_path($customer['slug']));
        }
        foreach (mdb_all('subscriptions') as $s) {
            if ((int)($s['customer_id'] ?? 0) === $id) mdb_delete('subscriptions', $s['id']);
        }
        mdb_delete('customers', $id);
        header('Location: customers.php?ok=deleted');
        exit;
    }
}

$subscriptions = customer_subscriptions($id);
$latest = $subscriptions[0] ?? null;
$status = subscription_status($latest ? ($latest['expiry_date'] ?? '') : '');

$pageTitle = 'Customer: ' . $customer['name'];
$activePage = 'customers';
require __DIR__ . '/layout_top.php';
?>
<?php if (isset($_GET['ok'])): ?><div class="alert">
    <?= $_GET['ok'] === 'created' ? 'Customer created and shop panel provisioned successfully.' : (
        $_GET['ok'] === 'renewed' ? 'Subscription renewed successfully.' : 'Updated successfully.') ?>
</div><?php endif; ?>
<?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>
<?php if ($notice): ?><div class="alert" style="font-weight:600;"><?= htmlspecialchars($notice) ?><code style="background:#fff;padding:2px 8px;border-radius:4px;"><?= htmlspecialchars($newPasswordToShow) ?></code></div><?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h2><?= icon('customers') ?> <?= htmlspecialchars($customer['name']) ?>
            <span class="status-pill <?= $status['class'] ?>"><?= htmlspecialchars($status['label']) ?><?php if ($status['days'] !== null && $status['class'] !== 'gray'): ?> &middot; <?= $status['days'] < 0 ? abs($status['days']) . 'd ago' : $status['days'] . 'd left' ?><?php endif; ?></span>
        </h2>
        <div class="toolbar">
            <a class="btn btn-outline" href="customer_form.php?action=edit&id=<?= $id ?>"><?= icon('edit', 14) ?> Edit Details</a>
            <?php if (!empty($customer['slug'])): ?>
            <a class="btn btn-outline" href="../s/<?= htmlspecialchars($customer['slug']) ?>/" target="_blank"><?= icon('building', 14) ?> Open Shop Panel</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="panel-body">
        <div class="form-grid">
            <div><b>Phone:</b> <?= htmlspecialchars($customer['phone'] ?? '-') ?></div>
            <div><b>Email:</b> <?= htmlspecialchars($customer['email'] ?? '-') ?></div>
            <div><b>Username:</b> <?= htmlspecialchars($customer['username'] ?? '-') ?></div>
            <div><b>Address:</b> <?= htmlspecialchars($customer['address'] ?? '-') ?></div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h2><?= icon('calendar') ?> Subscription History</h2></div>
    <div class="panel-body">
        <?php if (empty($subscriptions)): ?>
            <div class="empty-state"><p>No subscription purchases recorded.</p></div>
        <?php else: ?>
        <table class="data-table">
            <thead><tr><th>Purchase Date</th><th>Amount</th><th>Payment Type</th><th>Expiry Date</th></tr></thead>
            <tbody>
            <?php foreach ($subscriptions as $s): ?>
                <tr>
                    <td><?= format_display_date_m($s['purchase_date'] ?? '') ?></td>
                    <td>₹<?= number_format((float)($s['amount'] ?? 0), 2) ?></td>
                    <td><?= htmlspecialchars($s['payment_type'] ?? '-') ?></td>
                    <td><?= format_display_date_m($s['expiry_date'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h2><?= icon('add') ?> Add Renewal / New Purchase</h2></div>
    <div class="panel-body">
        <form method="post">
            <input type="hidden" name="form_action" value="renew">
            <div class="form-grid">
                <div class="form-group"><label>Subscription Plan Amount (₹) *</label><input type="number" step="0.01" name="plan_amount" required></div>
                <div class="form-group"><label>Payment Type *</label>
                    <select name="payment_type">
                        <option value="Cash">Cash</option>
                        <option value="Online">Online</option>
                    </select>
                </div>
                <div class="form-group"><label>Purchase Date *</label><input type="date" name="purchase_date" required value="<?= date('Y-m-d') ?>"></div>
            </div>
            <p style="color:#6B7280;font-size:13px;">Expiry will automatically be set to 1 year after the purchase date, and the shop panel's lock will be lifted immediately.</p>
            <div class="form-actions"><button class="btn btn-primary" type="submit">Save Purchase</button></div>
        </form>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h2><?= icon('edit') ?> Reset Shopkeeper Login Password</h2></div>
    <div class="panel-body">
        <form method="post" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            <input type="hidden" name="form_action" value="reset_password">
            <div class="form-group"><label>New Password (leave blank to auto-generate)</label><input type="text" name="new_password" placeholder="Leave blank for random password"></div>
            <button class="btn btn-outline" type="submit">Reset Password</button>
        </form>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h2 style="color:#C0392B;"><?= icon('delete') ?> Danger Zone</h2></div>
    <div class="panel-body">
        <form method="post" onsubmit="return confirm('This will permanently delete this customer AND their entire shop panel data. Continue?')">
            <input type="hidden" name="form_action" value="delete">
            <button class="btn btn-danger" type="submit"><?= icon('delete', 14) ?> Delete Customer &amp; Shop Data</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>
