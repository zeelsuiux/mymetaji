<?php
// MY METAJI - Application timezone: India Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

/* Bug #23: Enforce HTTPS in production */
if (!isset($_SERVER['SERVER_NAME']) || (!preg_match('/^(localhost|127\.0\.0\.1|::1)$/i', $_SERVER['SERVER_NAME'] ?? ''))) {
    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
        $redirectUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? '') . $_SERVER['REQUEST_URI'];
        header('Location: ' . $redirectUrl);
        exit;
    }
}

/* Bug #9: Session timeout - auto logout after 30 minutes of inactivity */
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 1800) {
    // Work out which shop this was before the session is wiped, so the owner
    // lands back on that shop's login form instead of the root panel's.
    // panel_tenant_code() is not loaded yet (db.php comes further down), so
    // read the same sources by hand.
    $expiredTenant = (string)($_SESSION['panel_tenant'] ?? '');
    if ($expiredTenant === ''
        && preg_match('#/(cst_[0-9]+)(?:/|$)#i', (string)($_SERVER['REQUEST_URI'] ?? ''), $tenantMatch)) {
        $expiredTenant = $tenantMatch[1];
    }
    if (!preg_match('/^cst_[0-9]+$/', $expiredTenant)) $expiredTenant = '';
    session_unset();
    session_destroy();
    header('Location: login.php'
        . ($expiredTenant !== '' ? '?tenant=' . rawurlencode($expiredTenant) . '&' : '?')
        . 'error=' . rawurlencode('Session expired. Please login again.'));
    exit;
}
$_SESSION['last_activity'] = time();

/* Bug #22: Session fixation - regenerate ID on every page load */
if (isset($_SESSION['team_member']) || isset($_SESSION['subadmin'])) {
    if (empty($_SESSION['_regenerated'])) {
        session_regenerate_id(true);
        $_SESSION['_regenerated'] = true;
    }
}

require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/license.php';
require_once __DIR__ . '/rate_limit.php';

function subadmin_db_file() { return panel_database_dir() . '/subadmins.json'; }
function subadmin_db_all() {
    $file=subadmin_db_file();
    if (!file_exists($file)) { file_put_contents($file, "[]\n", LOCK_EX); }
    $rows=json_decode(file_get_contents($file), true);
    return is_array($rows) ? $rows : [];
}
function subadmin_db_save($rows) {
    $dir=dirname(subadmin_db_file()); if (!is_dir($dir)) mkdir($dir,0755,true);
    file_put_contents(subadmin_db_file(), json_encode(array_values($rows), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), LOCK_EX);
}
function subadmin_next_id($rows) { if (!$rows) return 1; return max(array_map(fn($r)=>(int)($r['id']??0),$rows))+1; }
function db_get_subadmin($id) { foreach(subadmin_db_all() as $r) if((int)($r['id']??0)===(int)$id) return $r; return null; }
function current_subadmin() { return $_SESSION['subadmin'] ?? null; }
function current_team_member() { return $_SESSION['team_member'] ?? current_subadmin(); }
function current_role() { return current_subadmin() ? 'Subadmin' : (string)(current_team_member()['role'] ?? 'Member'); }
function is_admin() { return !current_subadmin() && current_role() === 'Admin'; }
function is_subadmin() { return current_subadmin() !== null; }
function is_staff_user() { return is_admin() || is_subadmin(); }
function current_member_id() { return (string)(current_team_member()['id'] ?? ''); }
function current_member_name() { return trim((string)(current_team_member()['name'] ?? '')); }
function login_identifier_matches($member, $identifier) {
    $identifier = trim((string)$identifier);
    if ($identifier === '') return false;
    foreach (['username', 'email', 'phone'] as $field) {
        $value = trim((string)($member[$field] ?? ''));
        if ($value !== '' && strcasecmp($value, $identifier) === 0) return true;
    }
    return false;
}

function shop_profile_file() { return panel_database_dir() . '/shop_profile.json'; }
function shop_profile_defaults() { return ['name' => '', 'tagline' => '', 'address' => '', 'phone' => '', 'email' => '', 'gstin' => '', 'bank_name' => '', 'bank_acc' => '', 'bank_ifsc' => '', 'upi' => '', 'upi_qr' => '', 'logo' => '', 'cash_opening_balance' => 0.00, 'bank_opening_balance' => 0.00]; }
function shop_profile() {
    $profile = shop_profile_defaults();
    if (file_exists(shop_profile_file())) {
        $saved = json_decode(file_get_contents(shop_profile_file()), true);
        if (is_array($saved)) $profile = array_merge($profile, $saved);
    }
    $member = current_team_member();
    if ($profile['name'] === '') $profile['name'] = trim((string)($member['name'] ?? ''));
    if ($profile['phone'] === '') $profile['phone'] = trim((string)($member['phone'] ?? ''));
    if ($profile['email'] === '') $profile['email'] = trim((string)($member['email'] ?? ''));
    return $profile;
}
function shop_profile_save($profile) { file_put_contents(shop_profile_file(), json_encode(array_merge(shop_profile_defaults(), $profile), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX); }

function activity_db_file() { return panel_database_dir() . '/activity.json'; }
function activity_db_all() {
    $file=activity_db_file();
    if (!file_exists($file)) { file_put_contents($file, "[]\n", LOCK_EX); }
    $rows=json_decode(file_get_contents($file), true);
    return is_array($rows) ? $rows : [];
}
function activity_db_save($rows) {
    $dir=dirname(activity_db_file()); if (!is_dir($dir)) mkdir($dir,0755,true);
    file_put_contents(activity_db_file(), json_encode(array_values($rows), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), LOCK_EX);
}
function log_activity($module, $action, $details='', $recordId=0, $category='') {
    $user = current_team_member();
    if (!$user) return;
    $rows=activity_db_all();
    $rows[]=[
        'id' => empty($rows) ? 1 : (max(array_map(fn($r)=>(int)($r['id']??0), $rows))+1),
        'subadmin_id' => current_member_id(),
        'subadmin_name' => current_member_name(),
        'role' => current_role(),
        'module' => (string)$module,
        'category' => $category !== '' ? $category : (string)$module,
        'action' => (string)$action,
        'details' => (string)$details,
        'record_id' => (int)$recordId,
        'timestamp' => date('Y-m-d h:i:s A'),
    ];
    activity_db_save($rows);
}
function subadmin_activities($subadminId=0, $date='') {
    $rows=activity_db_all();
    if ($subadminId !== 0) $rows=array_values(array_filter($rows, fn($r)=>(string)($r['subadmin_id']??'')===(string)$subadminId));
    if ($date !== '') $rows=array_values(array_filter($rows, fn($r)=>substr((string)($r['timestamp']??''),0,10)===$date));
    usort($rows, fn($a,$b)=>strcmp((string)($b['timestamp']??''),(string)($a['timestamp']??'')));
    return $rows;
}

function can_manage_subadmins() { return is_admin(); }
function can_access_module($module) {
    if (is_admin()) return true;
    if (is_subadmin()) return in_array($module, ['leads','customers','quotations','invoices','tasks','calendar'], true);
    return in_array($module, ['quotations','invoices','calendar'], true);
}
function can_add_customer_as_subadmin($module, $action = '', $id = '') {
    // Subadmin may open the Add Customer form and submit ONLY a new customer.
    // Existing customer edit/update/delete is never allowed.
    return is_subadmin() && $module === 'customers' && ($action === 'new' || ($action === 'save' && trim((string)$id) === ''));
}
function can_manage_module($module) {
    if (is_admin()) return true;
    if (is_subadmin()) return in_array($module, ['leads','quotations','invoices','tasks'], true);
    return false;
}
function record_owner_matches_current_user($record) {
    if (!is_array($record) || !is_subadmin()) return false;
    $id=current_member_id(); $name=current_member_name();
    foreach (['created_by_id','owner_id','assigned_to_id'] as $k) if($id!=='' && (string)($record[$k]??'')===$id) return true;
    foreach (['created_by','owner','assigned_to'] as $k) if($name!=='' && strcasecmp(trim((string)($record[$k]??'')),$name)===0) return true;
    return false;
}
function member_can_access_record($record) {
    if (is_admin()) return true;
    if (!is_array($record)) return false;
    if (is_subadmin()) {
        if (record_owner_matches_current_user($record)) return true;
        return false;
    }
    $customerName=trim((string)($record['customer']??''));
    return $customerName!=='' && strcasecmp($customerName,current_member_name())===0;
}
function member_visible_rows($module,$rows) {
    if (is_admin()) return $rows;
    if (is_subadmin()) {
        if (in_array($module,['customers'],true)) return array_values($rows);
        // Bug #25: Subadmin only sees leads they own, not all leads
        if ($module === 'leads') return array_values(array_filter($rows, function($row) {
            return record_owner_matches_current_user($row);
        }));
        if (in_array($module,['tasks','invoices','quotations','payments'],true)) return array_values(array_filter($rows,'member_can_access_record'));
        return [];
    }
    if (!in_array($module,['quotations','invoices','payments'],true)) return [];
    return array_values(array_filter($rows,'member_can_access_record'));
}
function can_manage_record($module,$record) {
    if (is_admin()) return true;
    if (!is_subadmin()) return false;
    if ($module==='leads') return true;
    if (in_array($module,['quotations','invoices','tasks'],true)) return member_can_access_record($record);
    return false;
}
function require_module_access($module) { require_login(); if(!can_access_module($module)){http_response_code(403);die('You do not have permission to access this module.');} }
function require_module_manage($module) { require_login(); if(!can_manage_module($module)){http_response_code(403);die('You do not have permission to manage this module.');} }
function require_order_access($record) { require_login(); if(!member_can_access_record($record)){http_response_code(403);die('You can only access your own orders.');} }
function ensure_default_admin() {
    $team = db_get_all('team');
    if (empty($team)) {
        // Bug #3: Generate random default password instead of hardcoded 'admin123'
        $defaultPassword = bin2hex(random_bytes(8));
        db_insert('team',[
            'name'=>'Administrator','phone'=>'','address'=>'','photo'=>'',
            'username'=>'admin',
            'password'=>password_hash($defaultPassword, PASSWORD_DEFAULT),
            'role'=>'Admin',
            'force_password_change'=>1,
        ]);
        // Store plain password temporarily so it can be shown to admin on first login
        file_put_contents(panel_database_dir() . '/.default_pw', $defaultPassword);
    }
}

/* Bug #1: CSRF Token Generation and Validation */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function csrf_field() {
    return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}
function csrf_verify() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return true;
    $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if ($token === '' || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        die('Invalid or missing CSRF token. Please go back and try again.');
    }
    return true;
}

/* Aliases for CSRF functions as requested */
function generate_csrf_token() { return csrf_token(); }
function validate_csrf_token() { return csrf_verify(); }

/* Password Strength Validation */
function validate_password_strength($password) {
    $errors = [];
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number.';
    }
    return $errors;
}

/**
 * The Master panel is the owner's own console and is already the most trusted
 * account in the product: it reads the very same tenant JSON stores, takes
 * payments and issues refunds. Opening a shop from Master still used to bounce
 * the owner to that shop's own login form, because a tenant panel only
 * recognises $_SESSION['team_member'] / $_SESSION['subadmin'] while Master signs
 * in under 'master_admin' - so looking at one shop meant signing in twice.
 *
 * A live Master session is therefore promoted into a tenant-scoped Admin
 * identity the first time a shop is opened. Notes on how it is kept honest:
 *  - nothing is granted without a real Master login, it is only read from the
 *    session, never re-derived from user input;
 *  - it is re-derived whenever the tenant changes, so an identity cannot be
 *    carried sideways from one shop into another;
 *  - it dies with the Master session, so signing out of Master does not leave a
 *    working admin behind in some shop;
 *  - a genuine tenant login always wins, because this only runs when no member
 *    is present.
 * It grants no authority the Master did not already have - it removes a
 * redundant password prompt.
 */
function master_tenant_promote() {
    $master = $_SESSION['master_admin'] ?? null;
    if (!is_array($master) || $master === []) return false;
    $tenant = panel_tenant_code();
    if ($tenant === '') return false;
    $username = trim((string)($master['username'] ?? ''));
    if ($username === '') return false;
    unset($_SESSION['subadmin']);
    $_SESSION['team_member'] = [
        'id'         => 'master:' . $tenant,
        'name'       => $username,
        'username'   => $username,
        'role'       => 'Admin',
        'phone'      => '',
        'photo'      => '',
        'via_master' => $tenant,
    ];
    return true;
}

function require_login() {
    ensure_default_admin();
    $member = current_team_member();
    if ($member && !empty($member['via_master'])) {
        // A Master-derived identity is only as valid as the Master login behind
        // it, and is scoped to one shop. If that login is gone, or the owner has
        // moved to a different shop, drop it and start again.
        if (empty($_SESSION['master_admin']) || (string)$member['via_master'] !== panel_tenant_code()) {
            unset($_SESSION['team_member'], $_SESSION['subadmin']);
            $member = null;
        }
    }
    if (!$member && !master_tenant_promote()) {
        // Carry the shop across, so being bounced from cst_3 brings the owner
        // back to cst_3's own login form rather than the root panel's.
        $tenant = panel_tenant_code();
        header('Location: login.php' . ($tenant !== '' ? '?tenant=' . rawurlencode($tenant) : ''));
        exit;
    }
}
function require_admin(){ require_login(); if(!is_admin()){http_response_code(403);die('Admin access required.');} }
