<?php
require_once __DIR__ . '/auth.php';
require_admin();
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/db.php';

$pageTitle = 'Database Backup';
$activeModule = 'backup';
$success = '';
$error = '';

$backupDir = __DIR__ . '/backups';
if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);

/* --- Handle Actions --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $do = $_POST['do'] ?? '';

    if ($do === 'create') {
        $dbDir = panel_database_dir();
        $files = glob($dbDir . '/*.json');
        if (empty($files)) {
            $error = 'No database files found to back up.';
        } else {
            $zipName = 'backup-' . date('Y-m-d-His') . '.zip';
            $zipPath = $backupDir . '/' . $zipName;

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                foreach ($files as $file) {
                    $zip->addFile($file, basename($file));
                }
                // Also include shop_profile.json and subadmins.json if they exist
                $extras = [
                    panel_database_dir() . '/shop_profile.json',
                    panel_database_dir() . '/subadmins.json',
                    panel_database_dir() . '/activity.json',
                ];
                foreach ($extras as $extra) {
                    if (file_exists($extra) && !in_array($extra, $files)) {
                        $zip->addFile($extra, basename($extra));
                    }
                }
                $zip->close();
                log_activity('backup', 'created', 'Backup created: ' . $zipName);
                $success = 'Backup created successfully: ' . $zipName;
            } else {
                $error = 'Failed to create ZIP archive.';
            }
        }
    } elseif ($do === 'delete') {
        $file = trim((string)($_POST['file'] ?? ''));
        $filePath = $backupDir . '/' . basename($file);
        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
            log_activity('backup', 'deleted', 'Backup deleted: ' . basename($file));
            $success = 'Backup deleted.';
        } else {
            $error = 'Backup file not found.';
        }
    } elseif ($do === 'restore') {
        $file = trim((string)($_POST['file'] ?? ''));
        $filePath = $backupDir . '/' . basename($file);
        if (!file_exists($filePath)) {
            $error = 'Backup file not found.';
        } else {
            $zip = new ZipArchive();
            if ($zip->open($filePath) === true) {
                $dbDir = panel_database_dir();
                if (!is_dir($dbDir)) mkdir($dbDir, 0755, true);
                $zip->extractTo($dbDir);
                $zip->close();
                log_activity('backup', 'restored', 'Database restored from: ' . basename($file));
                $success = 'Database restored from ' . basename($file) . '. Reload the page to see updated data.';
            } else {
                $error = 'Failed to read the ZIP archive.';
            }
        }
    }
}

/* --- List existing backups --- */
$backups = [];
foreach (glob($backupDir . '/backup-*.zip') as $path) {
    $backups[] = [
        'name' => basename($path),
        'size' => filesize($path),
        'time' => filemtime($path),
    ];
}
usort($backups, fn($a, $b) => $b['time'] <=> $a['time']);

require __DIR__ . '/layout_top.php';
?>

<main class="page">
  <a href="index.php" class="page-back-link">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Back to Dashboard
  </a>
  <div class="db-greeting" style="margin-bottom:20px;">
    <div>
      <h2>💾 Database Backup</h2>
      <p>Create, download, restore or delete database backups.</p>
    </div>
  </div>

  <?php if ($success): ?><div class="alert">✓ <?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-error">✕ <?= htmlspecialchars($error) ?></div><?php endif; ?>

  <div class="panel">
    <div class="panel-header" style="background:linear-gradient(135deg, #f8fafc, #f0f4ff);">
      <h2><?= icon('save') ?> Create Backup</h2>
    </div>
    <div class="panel-body">
      <p>Creates a ZIP archive of all JSON database files (invoices, payments, customers, products, leads, expenses, team, shop profile, etc.).</p>
      <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="do" value="create">
        <button class="btn btn-primary" type="submit"><?= icon('save', 16) ?> Create Backup Now</button>
      </form>
    </div>
  </div>

  <div class="panel">
    <div class="panel-header">
      <h2><?= icon('chart') ?> Existing Backups (<?= count($backups) ?>)</h2>
    </div>
    <div class="panel-body">
      <?php if (empty($backups)): ?>
        <div class="empty-state">No backups yet. Create your first backup above.</div>
      <?php else: ?>
        <table class="data-table">
          <thead>
            <tr>
              <th>Filename</th>
              <th>Size</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($backups as $backup): ?>
            <tr>
              <td><?= htmlspecialchars($backup['name']) ?></td>
              <td><?= number_format($backup['size'] / 1024, 1) ?> KB</td>
              <td><?= date('d-M-Y h:i A', $backup['time']) ?></td>
              <td>
                <a href="backups/<?= htmlspecialchars($backup['name']) ?>" class="btn btn-outline btn-sm" download><?= icon('view', 14) ?> Download</a>
                <form method="post" style="display:inline" onsubmit="return confirm('Restore database from this backup? This will overwrite current data.');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="do" value="restore">
                  <input type="hidden" name="file" value="<?= htmlspecialchars($backup['name']) ?>">
                  <button class="btn btn-outline btn-sm" type="submit"><?= icon('edit', 14) ?> Restore</button>
                </form>
                <form method="post" style="display:inline" onsubmit="return confirm('Delete this backup permanently?');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="do" value="delete">
                  <input type="hidden" name="file" value="<?= htmlspecialchars($backup['name']) ?>">
                  <button class="btn btn-outline btn-sm" type="submit" style="color:#dc2626;"><?= icon('delete', 14) ?> Delete</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php require __DIR__ . '/layout_bottom.php'; ?>
