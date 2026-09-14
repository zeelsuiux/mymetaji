<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

if (function_exists('license_is_expired') && license_is_expired()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Subscription expired. Renew to make changes.']);
    exit;
}

$module = $_POST['module'] ?? 'tasks';
$id = (int)($_POST['id'] ?? 0);
$status = trim($_POST['status'] ?? '');

$validModules = ['tasks', 'quotations', 'leads'];
if (!can_access_module($module)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No access']);
    exit;
}
if (!in_array($module, $validModules, true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid module']);
    exit;
}
$statusField = $module === 'leads' ? 'stage' : 'status';
$validStatuses = $MODULES[$module]['fields'][$statusField]['options'] ?? [];
if (!$id || !in_array($status, $validStatuses, true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid id or status']);
    exit;
}

$record = db_get_one($module, $id);
if (!$record || !can_manage_record($module, $record)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Record access denied']);
    exit;
}
$ok = db_patch($module, $id, [$statusField => $status]);
echo json_encode(['ok' => $ok]);
