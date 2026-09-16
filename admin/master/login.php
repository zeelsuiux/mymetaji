<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/icons.php';
ensure_master_admin();
if (master_logged_in()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    foreach (master_admins() as $admin) {
        if (strcasecmp((string)($admin['username'] ?? ''), $username) === 0 && password_verify($password, (string)($admin['password'] ?? ''))) {
            $_SESSION['master_admin'] = ['id' => $admin['id'], 'username' => $admin['username']];
            header('Location: index.php');
            exit;
        }
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Master Panel - Login</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-page">
<main class="login-card">
<div class="login-brand"><img src="assets/logo.png" alt="Master Panel"></div>
<h1>Master Panel Login</h1>
<p class="login-subtitle">Sign in to manage customers &amp; subscriptions.</p>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post" class="login-form">
<label>Username</label>
<input type="text" name="username" required autofocus autocomplete="username">
<label>Password</label>
<div class="password-field">
<input id="login-password" type="password" name="password" required autocomplete="current-password">
<button type="button" class="password-toggle" data-target="login-password">
<span class="password-icon-show"><?= icon('view', 16) ?></span>
<span class="password-icon-hide password-icon-hidden"><?= icon('eye-off', 16) ?></span>
</button>
</div>
<button class="btn btn-primary login-button" type="submit">Sign In</button>
</form>
</main>
<script>document.querySelectorAll('.password-toggle').forEach(b=>b.addEventListener('click',()=>{const i=document.getElementById(b.dataset.target);const show=i.type==='password';i.type=show?'text':'password';}));</script>
</body>
</html>
