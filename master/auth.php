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
