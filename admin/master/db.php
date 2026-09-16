<?php
/**
 * MASTER PANEL - JSON file storage helper.
 * Stores its own customer (shopkeeper), signup lead and subscription history.
 */

define('MDB_DIR', __DIR__ . '/database');

function mdb_file($name) {
    return MDB_DIR . '/' . preg_replace('/[^a-zA-Z0-9_-]/', '', $name) . '.json';
}

function mdb_init() {
    if (!is_dir(MDB_DIR)) mkdir(MDB_DIR, 0755, true);
    foreach (['customers', 'subscriptions', 'leads'] as $name) {
        if (!file_exists(mdb_file($name))) {
            file_put_contents(mdb_file($name), "[]\n", LOCK_EX);
        }
    }
}

function mdb_all($name) {
    mdb_init();
    $raw = file_exists(mdb_file($name)) ? file_get_contents(mdb_file($name)) : '[]';
    $rows = json_decode($raw, true);
    return is_array($rows) ? $rows : [];
}

function mdb_save($name, $rows) {
    mdb_init();
    file_put_contents(mdb_file($name), json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function mdb_next_id($rows) {
    if (empty($rows)) return 1;
    return max(array_map(fn($r) => (int)($r['id'] ?? 0), $rows)) + 1;
}

function mdb_get($name, $id) {
    foreach (mdb_all($name) as $row) {
        if ((int)($row['id'] ?? 0) === (int)$id) return $row;
    }
    return null;
}

function mdb_insert($name, $record) {
    $rows = mdb_all($name);
    $record['id'] = mdb_next_id($rows);
    $record['created_at'] = date('Y-m-d H:i:s');
    $record['updated_at'] = date('Y-m-d H:i:s');
    $rows[] = $record;
    mdb_save($name, $rows);
    return $record['id'];
}

function mdb_update($name, $id, $fields) {
    $rows = mdb_all($name);
    $found = false;
    foreach ($rows as &$row) {
        if ((int)($row['id'] ?? 0) === (int)$id) {
            foreach ($fields as $k => $v) $row[$k] = $v;
            $row['updated_at'] = date('Y-m-d H:i:s');
            $found = true;
            break;
        }
    }
    unset($row);
    if ($found) mdb_save($name, $rows);
    return $found;
}

function mdb_delete($name, $id) {
    $rows = mdb_all($name);
    $rows = array_values(array_filter($rows, fn($r) => (int)($r['id'] ?? 0) !== (int)$id));
    mdb_save($name, $rows);
}
