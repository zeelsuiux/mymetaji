<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/master/db.php';
require_once __DIR__ . '/master/helpers.php';

$token = trim((string)($_GET['token'] ?? $_POST['token'] ?? ''));
$errors = [];
$success = false;
$tenant = panel_tenant_code();
$tokenFile = panel_database_dir() . '/reset_tokens.json';
$tokens = file_exists($tokenFile) ? json_decode(file_get_contents($tokenFile), true) : [];
if (!is_array($tokens)) $tokens = [];
$tokenHash = hash('sha256', $token);
$tokenIndex = -1;
$tokenRow = [];
foreach ($tokens as $index => $row) {
    if (is_array($row) && ($row['token_hash'] ?? '') === $tokenHash && (int)($row['expires_at'] ?? 0) >= time()) {
        $tokenIndex = $index;
        $tokenRow = $row;
        break;
    }
}
if ($token === '' || $tenant === '' || $tokenIndex < 0) $errors[] = 'This reset link is invalid or expired.';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$errors) {
    $password = (string)($_POST['password'] ?? '');
    $confirmation = (string)($_POST['password_confirmation'] ?? '');
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirmation) $errors[] = 'Passwords do not match.';
    if (!$errors) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $members = db_get_all('team');
        $updated = false;
        foreach ($members as &$member) {
            if ((string)($member['id'] ?? '') === (string)($tokenRow['member_id'] ?? '')) {
                $member['password'] = $passwordHash;
                $member['updated_at'] = date('Y-m-d H:i:s');
                $updated = true;
                break;
            }
        }
        unset($member);
        if ($updated) {
            $data = db_load();
            $data['team'] = $members;
            db_save($data);
            $customerId = (int)(get_license()['customer_id'] ?? 0);
            if ($customerId > 0) mdb_update('customers', $customerId, ['password_hash' => $passwordHash]);
        } else {
            $rows = subadmin_db_all();
            foreach ($rows as &$member) {
                if ((string)($member['id'] ?? '') === (string)($tokenRow['member_id'] ?? '')) {
                    $member['password'] = $passwordHash;
                    $member['updated_at'] = date('Y-m-d H:i:s');
                    $updated = true;
                    break;
                }
            }
            unset($member);
            if ($updated) subadmin_db_save($rows);
        }
        if ($updated) {
            unset($tokens[$tokenIndex]);
            file_put_contents($tokenFile, json_encode(array_values($tokens), JSON_PRETTY_PRINT), LOCK_EX);
            $loginPath = $tenant !== '' ? '../' . $tenant . '/login.php' : 'login.php';
            header('Location: ' . $loginPath . '?reset=1');
            exit;
        } else {
            $errors[] = 'Unable to update this account password.';
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Reset Password</title><link rel="stylesheet" href="assets/style.css"></head><body class="login-page"><main class="login-card"><div class="login-brand"><img src="assets/logo.png" alt="Logo"></div><h1>Reset Password</h1><?php if ($success): ?><div class="alert">Password reset successfully.</div><a class="btn btn-primary login-button" href="login.php">Go to Login</a><?php else: ?><?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?><form method="post" class="login-form"><input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>"><label>New Password</label><input type="password" name="password" required minlength="6" autocomplete="new-password"><label>Confirm Password</label><input type="password" name="password_confirmation" required minlength="6" autocomplete="new-password"><button class="btn btn-primary login-button" type="submit">Reset Password</button></form><?php endif; ?></main></body></html>
