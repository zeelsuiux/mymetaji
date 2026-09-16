<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/master/db.php';
require_once __DIR__ . '/master/helpers.php';

$errors = [];
$sent = false;
$email = trim((string)($_POST['email'] ?? ''));
$tenant = panel_tenant_code();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    } elseif ($tenant === '') {
        $errors[] = 'Customer panel context is missing. Open Forgot Password from the customer login page.';
    } else {
        $member = null;
        foreach (db_get_all('team') as $row) {
            if (strcasecmp((string)($row['email'] ?? ''), $email) === 0) { $member = $row; break; }
        }
        if (!$member) {
            foreach (subadmin_db_all() as $row) {
                if (strcasecmp((string)($row['email'] ?? ''), $email) === 0) { $member = $row; break; }
            }
        }

        if ($member) {
            $token = bin2hex(random_bytes(32));
            $tokenFile = panel_database_dir() . '/reset_tokens.json';
            $tokens = file_exists($tokenFile) ? json_decode(file_get_contents($tokenFile), true) : [];
            if (!is_array($tokens)) $tokens = [];
            $tokens[] = ['token_hash' => hash('sha256', $token), 'member_id' => (string)($member['id'] ?? ''), 'expires_at' => time() + 3600];
            file_put_contents($tokenFile, json_encode($tokens, JSON_PRETTY_PRINT), LOCK_EX);

            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $link = $scheme . '://' . $host . '/' . rawurlencode($tenant) . '/reset_password.php?token=' . rawurlencode($token);
            $subject = 'Reset your Prisha ERP password';
            $message = "Hello " . ($member['name'] ?? 'Customer') . ",\n\nUse this link to reset your password:\n" . $link . "\n\nThis link expires in 1 hour. If you did not request this, ignore this email.\n";
            $headers = "From: no-reply@" . preg_replace('/^www\./', '', $host) . "\r\nContent-Type: text/plain; charset=UTF-8\r\n";
            $sent = mail($email, $subject, $message, $headers);
        }
        if ($sent) {
            header('Location: forgot_password.php?sent=1');
            exit;
        }
        $errors[] = 'If this email is registered, a reset link has been sent. Check your inbox or spam folder.';
    }
}
$sent = ($_GET['sent'] ?? '') === '1';
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Forgot Password</title><link rel="stylesheet" href="assets/style.css"></head><body class="login-page"><main class="login-card"><div class="login-brand"><img src="assets/logo.png" alt="Logo"></div><h1>Forgot Password</h1><p class="login-subtitle">Enter your registered email to receive a reset link.</p><?php if ($sent): ?><div class="alert">Reset link sent. Check your email.</div><a class="btn btn-outline login-button" href="login.php">Back to Login</a><?php else: ?><?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?><form method="post" class="login-form"><label>Email</label><input type="email" name="email" required autofocus autocomplete="email" value="<?= htmlspecialchars($email) ?>"><button class="btn btn-primary login-button" type="submit">Send Reset Link</button></form><a class="btn btn-outline login-button" href="login.php">Back to Login</a><?php endif; ?></main></body></html>
