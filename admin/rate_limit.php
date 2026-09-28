<?php
/**
 * MY METAJI - Rate Limiter (file-based storage)
 * Limits login attempts to 5 per 15 minutes per IP address.
 */

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

/**
 * Get the rate limit file path for a given IP address.
 */
function login_rate_limit_file() {
    return sys_get_temp_dir() . '/erp_rate_' . md5($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0') . '.json';
}

/**
 * Read the current rate limit data for the client IP.
 * Returns ['attempts' => int, 'locked_until' => int]
 */
function login_rate_check() {
    $file = login_rate_limit_file();
    if (!file_exists($file)) return ['attempts' => 0, 'locked_until' => 0];
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : ['attempts' => 0, 'locked_until' => 0];
}

/**
 * Record a login attempt. On success, reset counter.
 * On failure, increment counter and lock if threshold reached.
 */
function login_rate_record($success) {
    $file = login_rate_limit_file();
    if ($success) {
        file_put_contents($file, json_encode(['attempts' => 0, 'locked_until' => 0]), LOCK_EX);
        return;
    }
    $data = login_rate_check();
    $data['attempts'] = ($data['attempts'] ?? 0) + 1;
    if ($data['attempts'] >= 5) {
        $data['locked_until'] = time() + 900; // Lock for 15 minutes
    }
    file_put_contents($file, json_encode($data), LOCK_EX);
}

/**
 * Check if login is currently locked. Returns false or a message string.
 */
function login_is_locked() {
    $data = login_rate_check();
    if (($data['locked_until'] ?? 0) > time()) {
        $remaining = $data['locked_until'] - time();
        $minutes = ceil($remaining / 60);
        return 'Too many failed attempts. Try again in ' . $minutes . ' minute(s).';
    }
    return false;
}
