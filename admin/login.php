<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/icons.php';
ensure_default_admin();
if (current_team_member()) {
  header('Location: index.php');
  exit;
}
$members = db_get_all('team');
$subadmins = subadmin_db_all();
$error = trim((string) ($_GET['error'] ?? ''));
$notice = '';
if (($_GET['registered'] ?? '') === '1')
  $notice = 'Registration successful. Your 15-day trial is ready.';
if (($_GET['reset'] ?? '') === '1')
  $notice = 'Password reset successfully. You can login now.';

$rateLimitMsg = login_is_locked();
if ($rateLimitMsg && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $error = $rateLimitMsg;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($rateLimitMsg)) {
  csrf_verify();
  $identifier = trim((string) ($_POST['username'] ?? ''));
  $password = (string) ($_POST['password'] ?? '');
  $loginSuccess = false;
  foreach ($members as $member) {
    if (login_identifier_matches($member, $identifier) && password_verify($password, (string) ($member['password'] ?? ''))) {
      $_SESSION['subadmin'] = null;
      unset($_SESSION['subadmin']);
      $_SESSION['team_member'] = ['id' => $member['id'], 'name' => $member['name'], 'username' => $member['username'], 'role' => $member['role'] ?? 'Member', 'photo' => $member['photo'] ?? ''];
      session_regenerate_id(true);
      $loginSuccess = true;
      login_rate_record(true);
      header('Location:index.php');
      exit;
    }
  }
  foreach ($subadmins as $sa) {
    if ((string) ($sa['status'] ?? 'Active') === 'Active' && login_identifier_matches($sa, $identifier) && password_verify($password, (string) ($sa['password'] ?? ''))) {
      unset($_SESSION['team_member']);
      $_SESSION['subadmin'] = ['id' => $sa['id'], 'name' => $sa['name'], 'username' => $sa['username'], 'role' => 'Subadmin', 'phone' => $sa['phone'] ?? '', 'photo' => ''];
      session_regenerate_id(true);
      $loginSuccess = true;
      login_rate_record(true);
      header('Location:index.php');
      exit;
    }
  }
  if (!$loginSuccess) {
    login_rate_record(false);
    // Check if identifier matches any cst_ tenant user and redirect to their login page
    $tenantCode = panel_tenant_code();
    if ($tenantCode === '') {
      $identifier = trim((string) ($_POST['username'] ?? ''));
      // Scan all cst_ directories for matching user
      $shopsDir = __DIR__ . '/shops';
      if (is_dir($shopsDir)) {
        foreach (glob($shopsDir . '/cst_[0-9]*') as $tenantDir) {
          $tc = basename($tenantDir);
          $teamFile = $tenantDir . '/database/team.json';
          if (file_exists($teamFile)) {
            $team = json_decode(file_get_contents($teamFile), true);
            if (is_array($team)) {
                foreach ($team as $member) {
                if (login_identifier_matches($member, $identifier)) {
                  // One shared login page serves every shop. The tenant travels
                  // as ?tenant= so panel_tenant_code() scopes the form to that
                  // shop's team.json. A per-tenant "cst_N/login.php" URL looks
                  // harmless but resolves outside /admin/, where the rewrite
                  // rules do not apply - the browser lands on a 404.
                  header('Location: login.php?tenant=' . rawurlencode($tc));
                  exit;
                }
              }
            }
          }
        }
      }
    }
  }
  $tenantCode = panel_tenant_code();
  // Stay on this one page. The old '../cst_N/login.php' climbed out of /admin/,
  // where nothing rewrites it, so a wrong password on any shop ended on a 404
  // instead of back at the login form with the error message.
  $loginPath = 'login.php';
  if ($tenantCode !== '' && preg_match('/^cst_[0-9]+$/', $tenantCode)) {
    $loginPath .= '?tenant=' . rawurlencode($tenantCode)
               . '&error=' . rawurlencode('Invalid username or password.');
  } else {
    $loginPath .= '?error=' . rawurlencode('Invalid username or password.');
  }
  header('Location: ' . $loginPath);
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — My Metaji</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 40%, #1a1a2e 100%);
      padding: 20px;
      position: relative;
      overflow: hidden;
    }

    body::before {
      content: '';
      position: absolute;
      top: -30%;
      right: -15%;
      width: 500px;
      height: 500px;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 50%;
      pointer-events: none;
    }

    body::after {
      content: '';
      position: absolute;
      bottom: -20%;
      left: -10%;
      width: 400px;
      height: 400px;
      background: rgba(255, 255, 255, 0.03);
      border-radius: 50%;
      pointer-events: none;
    }

    .login-card {
      background: #fff;
      border-radius: 20px;
      padding: 40px 36px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
      position: relative;
      z-index: 1;
    }

    .login-brand {
      text-align: center;
      margin-bottom: 24px;
    }

    .login-brand img {
      height: 48px;
    }

    .login-card h1 {
      text-align: center;
      font-size: 24px;
      font-weight: 700;
      color: #1a1a2e;
      margin-bottom: 6px;
    }

    .login-subtitle {
      text-align: center;
      font-size: 14px;
      color: #888;
      margin-bottom: 28px;
    }

    .alert {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 12px 16px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 500;
      margin-bottom: 16px;
      background: #e8f5e9;
      color: #2e7d32;
      border: 1px solid #c8e6c9;
    }

    .alert-error {
      background: #fce4ec;
      color: #c62828;
      border-color: #f8bbd0;
    }

    .login-form {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .login-form label {
      font-size: 13px;
      font-weight: 600;
      color: #444;
    }

    .login-form input[type="text"],
    .login-form input[type="password"] {
      width: 100%;
      padding: 12px 16px;
      border: 1px solid #e0e5ec;
      border-radius: 10px;
      font-size: 14px;
      font-family: inherit;
      color: #333;
      background: #f9fafc;
      transition: all 0.2s;
    }

    .login-form input:focus {
      outline: none;
      border-color: #2563EB;
      box-shadow: 0 0 0 3px rgba(21, 101, 192, 0.1);
      background: #fff;
    }

    .password-field {
      position: relative;
    }

    .password-field input {
      width: 100%;
      padding-right: 44px;
    }

    .password-toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: #888;
      padding: 4px;
      border-radius: 6px;
    }

    .password-toggle:hover {
      color: #2563EB;
      background: #f0f2f5;
    }

    .password-icon-hidden {
      display: none;
    }

    .login-button {
      width: 100%;
      padding: 13px;
      border-radius: 10px;
      font-size: 15px;
      font-weight: 600;
      font-family: inherit;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
      text-align: center;
      display: block;
      border: none;
    }

    .login-button[type="submit"] {
      background: linear-gradient(135deg, #2563EB, #1D4ED8);
      color: #fff;
      box-shadow: 0 4px 14px rgba(13, 71, 161, 0.25);
    }

    .login-button[type="submit"]:hover {
      background: linear-gradient(135deg, #1D4ED8, #1E3A5F);
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(13, 71, 161, 0.35);
    }

    .login-button[type="submit"]:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }

    .btn-outline {
      background: #2563EB;
      color: #fff;
      border: 1.5px solid #2563EB;
    }

    .btn-outline:hover {
      background: #1D4ED8;
      border-color: #1D4ED8;
      color: #fff;
    }

    .login-links {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-top: 16px;
    }

    @media (max-width: 480px) {
      .login-card {
        padding: 28px 24px;
      }

      .login-card h1 {
        font-size: 20px;
      }
    }
  </style>
</head>

<body>
  <main class="login-card">
    <div class="login-brand">
      <img src="assets/logo.png" alt="My Metaji logo">
    </div>
    <h1>Welcome Back</h1>
    <p class="login-subtitle">Sign in to your My Metaji account</p>

    <?php if ($notice): ?>
      <div class="alert">✓ <?= htmlspecialchars($notice) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-error">✕ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="login-form">
      <?= csrf_field() ?>
      <div>
        <label>Username, Email or Phone</label>
        <input type="text" name="username" required autofocus autocomplete="username" placeholder="Enter your username">
      </div>
      <div>
        <label>Password</label>
        <div class="password-field">
          <input id="login-password" type="password" name="password" required autocomplete="current-password"
            placeholder="Enter your password">
          <button type="button" class="password-toggle" data-target="login-password">
            <span class="password-icon-show"><?= icon('view', 18) ?></span>
            <span class="password-icon-hide password-icon-hidden"><?= icon('eye-off', 18) ?></span>
          </button>
        </div>
      </div>
      <button class="login-button" type="submit" <?= !empty($rateLimitMsg) ? 'disabled' : '' ?>>
        Sign In →
      </button>
    </form>

    <div class="login-links">
      <a class="login-button btn-outline" href="forgot_password.php">Forgot Password?</a>
      <a class="login-button btn-outline" href="signup.php">New Customer? Register Here</a>
    </div>
  </main>

  <script>
    document.querySelectorAll('.password-toggle').forEach(function (b) {
      b.addEventListener('click', function () {
        var i = document.getElementById(b.dataset.target);
        var show = i.type === 'password';
        i.type = show ? 'text' : 'password';
        b.querySelector('.password-icon-show').classList.toggle('password-icon-hidden', show);
        b.querySelector('.password-icon-hide').classList.toggle('password-icon-hidden', !show);
      });
    });
  </script>
</body>

</html>