<?php
// Application timezone: India Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/license.php';

function subadmin_db_file() { return __DIR__ . '/database/subadmins.json'; }
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
function shop_profile_file() { return __DIR__ . '/database/shop_profile.json'; }
function shop_profile_defaults() { return ['name' => '', 'tagline' => '', 'address' => '', 'phone' => '', 'email' => '', 'gstin' => '', 'bank_name' => '', 'bank_acc' => '', 'bank_ifsc' => '', 'upi' => '', 'logo' => '']; }
function shop_profile() {
    $profile = shop_profile_defaults();
    if (file_exists(shop_profile_file())) { $saved = json_decode(file_get_contents(shop_profile_file()), true); if (is_array($saved)) $profile = array_merge($profile, $saved); }
    $member = current_team_member();
    if ($profile['name'] === '') $profile['name'] = trim((string)($member['name'] ?? ''));
    if ($profile['phone'] === '') $profile['phone'] = trim((string)($member['phone'] ?? ''));
    if ($profile['email'] === '') $profile['email'] = trim((string)($member['email'] ?? ''));
    return $profile;
}
function shop_profile_save($profile) { file_put_contents(shop_profile_file(), json_encode(array_merge(shop_profile_defaults(), $profile), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX); }

function activity_db_file() { return __DIR__ . '/database/activity.json'; }
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
    if (!is_subadmin()) return;
    $rows=activity_db_all();
    $rows[]=[
        'id' => empty($rows) ? 1 : (max(array_map(fn($r)=>(int)($r['id']??0), $rows))+1),
        'subadmin_id' => current_member_id(),
        'subadmin_name' => current_member_name(),
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
        if (in_array($module,['leads','customers'],true)) return array_values($rows);
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
    if (empty(db_get_all('team'))) db_insert('team',['name'=>'Administrator','phone'=>'','address'=>'','photo'=>'','username'=>'admin','password'=>password_hash('admin123',PASSWORD_DEFAULT),'role'=>'Admin']);
}
function require_login() { ensure_default_admin(); if(!current_team_member()){header('Location: login.php');exit;} }
function require_admin(){ require_login(); if(!is_admin()){http_response_code(403);die('Admin access required.');} }
