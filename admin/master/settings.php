<?php
require_once __DIR__ . '/auth.php';
require_master_login();
require_once __DIR__ . '/icons.php';

$errors = [];
$notice = '';
$admins = master_admins();
$me = current_master_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = (string)($_POST['current_password'] ?? '');
    $newUsername = trim((string)($_POST['username'] ?? ''));
    $newPassword = (string)($_POST['new_password'] ?? '');

    $matched = false;
    foreach ($admins as &$a) {
        if ((int)$a['id'] === (int)$me['id']) {
            if (!password_verify($currentPassword, $a['password'])) {
                $errors[] = 'Current password is incorrect.';
                break;
            }
            $matched = true;
            if ($newUsername === '') $errors[] = 'Username cannot be empty.';
            if (!$errors) {
                $a['username'] = $newUsername;
                if ($newPassword !== '') $a['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                $_SESSION['master_admin']['username'] = $newUsername;
            }
            break;
        }
    }
    unset($a);
    if ($matched && !$errors) {
        master_admin_save($admins);
        $notice = 'Settings updated successfully.';
        $admins = master_admins();
    }
}

$current = null;
foreach ($admins as $a) if ((int)$a['id'] === (int)$me['id']) $current = $a;

$pageTitle = 'Settings';
$activePage = 'settings';
require __DIR__ . '/layout_top.php';
?>
<div class="panel">
    <div class="panel-header"><h2><?= icon('edit') ?> Master Login Settings</h2></div>
    <div class="panel-body">
        <?php if ($notice): ?><div class="alert"><?= htmlspecialchars($notice) ?></div><?php endif; ?>
        <?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>
        <form method="post">
            <div class="form-grid">
                <div class="form-group"><label>Current Password *</label><input type="password" name="current_password" required></div>
                <div class="form-group"><label>Username *</label><input name="username" required value="<?= htmlspecialchars($current['username'] ?? '') ?>"></div>
                <div class="form-group"><label>New Password (leave blank to keep current)</label><input type="password" name="new_password"></div>
            </div>
            <div class="form-actions"><button class="btn btn-primary" type="submit">Save</button></div>
        </form>
        <p style="color:#6B7280;font-size:13px;margin-top:16px;">Default login was <b>admin / admin123</b> &mdash; please change this now if you haven't already.</p>
    </div>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>
