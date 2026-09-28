<?php
date_default_timezone_set('Asia/Kolkata');
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/db.php';

function master_admin_file() {
    return MDB_DIR . '/admin.json';
}

function ensure_master_admin() {
    mdb_init();
    if (!file_exists(master_admin_file())) {
        $default = [[
            'id' => 1,
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
        ]];
        file_put_contents(master_admin_file(), json_encode($default, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }
}

function master_admins() {
    ensure_master_admin();
    $rows = json_decode(file_get_contents(master_admin_file()), true);
    return is_array($rows) ? $rows : [];
}

function master_admin_save($rows) {
    file_put_contents(master_admin_file(), json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function current_master_admin() {
    return $_SESSION['master_admin'] ?? null;
}

function master_logged_in() {
    return current_master_admin() !== null;
}

function require_master_login() {
    ensure_master_admin();
    if (!master_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * CSRF token for master forms.
 *
 * Master was built before any form here had a token, so this starts with the
 * screens that move money or hand out access. Each page calls master_csrf_field()
 * inside its <form> and master_csrf_check() at the top of its POST handling.
 * Any other master form that writes data should do the same.
 */
function master_csrf_token() {
    if (empty($_SESSION['master_csrf'])) {
        $_SESSION['master_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['master_csrf'];
}

/** The hidden input to drop inside a form. */
function master_csrf_field() {
    return '<input type="hidden" name="csrf" value="' . htmlspecialchars(master_csrf_token(), ENT_QUOTES) . '">';
}

/**
 * Check a posted token. Returns true, or fills $masterCsrfError and returns
 * false so the caller can show it and stop without acting on the request.
 */
function master_csrf_check($token = null) {
    global $masterCsrfError;
    $masterCsrfError = 'Your session expired. Please reload the page and try again.';
    if ($token === null) $token = $_POST['csrf'] ?? '';
    $expected = (string)($_SESSION['master_csrf'] ?? '');
    $token = trim((string)$token);
    if ($expected === '' || $token === '' || !hash_equals($expected, $token)) return false;
    $masterCsrfError = '';
    return true;
}
