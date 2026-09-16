<?php
require_once __DIR__ . '/auth.php';
require_admin();
require_once __DIR__ . '/icons.php';

$subadminId = (int)($_GET['id'] ?? 0);
$subadmin = db_get_subadmin($subadminId);
if (!$subadmin) {
    header('Location: subadmin.php');
    exit;
}

$date = trim((string)($_GET['date'] ?? date('Y-m-d')));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $date = date('Y-m-d');
$tab = strtolower(trim((string)($_GET['tab'] ?? 'all')));
$allowedTabs = ['all','customer','lead','payment','invoice','quotation','task'];
if (!in_array($tab, $allowedTabs, true)) $tab = 'all';

$activities = subadmin_activities($subadminId, $date);
if ($tab !== 'all') {
    $activities = array_values(array_filter($activities, function($row) use ($tab) {
        return strtolower((string)($row['category'] ?? $row['module'] ?? '')) === $tab;
    }));
}

$allToday = subadmin_activities($subadminId, $date);
$counts = ['customer'=>0,'lead'=>0,'payment'=>0,'invoice'=>0,'quotation'=>0,'task'=>0];
foreach ($allToday as $row) {
    $cat = strtolower((string)($row['category'] ?? $row['module'] ?? ''));
    if (isset($counts[$cat])) $counts[$cat]++;
}

$dayStart = date('Y-m-d', strtotime($date . ' -1 day'));
$dayNext  = date('Y-m-d', strtotime($date . ' +1 day'));
$pageTitle = 'Subadmin Activity';
$activeModule = 'subadmin';
require __DIR__ . '/layout_top.php';
?>
<style>
.activity-head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:18px}
.activity-profile{display:flex;align-items:center;gap:12px}
.activity-avatar{width:44px;height:44px;border-radius:12px;background:#E8F5E9;color:#2F7D32;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800}
.activity-name{font-size:18px;font-weight:700}.activity-meta{font-size:12px;color:var(--text-muted);margin-top:2px}
.date-tools{display:flex;align-items:center;gap:8px;flex-wrap:wrap}.date-tools input{height:38px;padding:0 10px;border:1px solid var(--border);border-radius:8px;background:#fff}
.activity-tabs{display:flex;gap:8px;overflow:auto;padding-bottom:4px;margin-bottom:16px}.activity-tab{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);background:#fff;border-radius:999px;padding:8px 12px;color:var(--text);text-decoration:none;white-space:nowrap;font-size:13px;font-weight:600}.activity-tab.active{background:var(--primary);color:#fff;border-color:var(--primary)}.activity-tab .count{opacity:.8}
.activity-summary{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:10px;margin-bottom:18px}.activity-summary-card{background:#fff;border:1px solid var(--border);border-radius:12px;padding:12px}.activity-summary-card .label{font-size:12px;color:var(--text-muted)}.activity-summary-card .value{font-size:20px;font-weight:800;margin-top:3px}
.activity-table-wrap{overflow:auto}.activity-table{width:100%;border-collapse:collapse}.activity-table th,.activity-table td{padding:12px 13px;border-bottom:1px solid var(--border);text-align:left;vertical-align:top;font-size:13px}.activity-table th{font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.03em}.activity-time{font-weight:700;white-space:nowrap}.activity-detail{font-weight:600}.activity-module{display:inline-flex;padding:5px 8px;border-radius:999px;background:#F3F6F4;font-size:11px;font-weight:700;text-transform:capitalize}.activity-action{font-weight:700}.activity-empty{padding:36px;text-align:center;color:var(--text-muted)}
@media(max-width:900px){.activity-summary{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:560px){.activity-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.activity-table th,.activity-table td{padding:10px}.date-tools{width:100%}.date-tools input{flex:1}}
</style>

<div class="activity-head">
  <div class="activity-profile">
    <div class="activity-avatar"><?= strtoupper(substr((string)($subadmin['name'] ?? 'S'), 0, 1)) ?></div>
    <div>
      <div class="activity-name"><?= htmlspecialchars($subadmin['name'] ?? 'Subadmin') ?></div>
      <div class="activity-meta">@<?= htmlspecialchars($subadmin['username'] ?? '') ?><?= !empty($subadmin['phone']) ? ' • ' . htmlspecialchars($subadmin['phone']) : '' ?></div>
    </div>
  </div>
  <div class="date-tools">
    <a class="btn btn-outline btn-sm" href="subadmin_activity.php?id=<?= $subadminId ?>&date=<?= urlencode($dayStart) ?>&tab=<?= urlencode($tab) ?>">← Previous Day</a>
    <input type="date" value="<?= htmlspecialchars($date) ?>" onchange="window.location='subadmin_activity.php?id=<?= $subadminId ?>&tab=<?= urlencode($tab) ?>&date='+this.value">
    <a class="btn btn-outline btn-sm" href="subadmin_activity.php?id=<?= $subadminId ?>&date=<?= urlencode($dayNext) ?>&tab=<?= urlencode($tab) ?>">Next Day →</a>
  </div>
</div>

<div class="activity-summary">
  <?php foreach ($counts as $key=>$count): ?>
    <div class="activity-summary-card"><div class="label"><?= ucfirst($key) ?> Activity</div><div class="value"><?= (int)$count ?></div></div>
  <?php endforeach; ?>
</div>

<div class="panel">
  <div class="panel-header"><h2><?= icon('calendar') ?> Activity — <?= htmlspecialchars(date('d M Y', strtotime($date))) ?></h2></div>
  <div class="panel-body">
    <div class="activity-tabs">
      <?php
      $tabs = [
        'all' => 'All',
        'customer' => 'Customer',
        'lead' => 'Lead',
        'payment' => 'Payment',
        'invoice' => 'Invoice',
        'quotation' => 'Quotation',
        'task' => 'Task',
      ];
      foreach ($tabs as $key=>$label):
      ?>
        <a class="activity-tab <?= $tab===$key?'active':'' ?>" href="subadmin_activity.php?id=<?= $subadminId ?>&date=<?= urlencode($date) ?>&tab=<?= urlencode($key) ?>">
          <?= htmlspecialchars($label) ?><?php if($key!=='all'): ?><span class="count">(<?= (int)($counts[$key] ?? 0) ?>)</span><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="activity-table-wrap">
      <?php if (empty($activities)): ?>
        <div class="activity-empty">No activity found for this day and tab.</div>
      <?php else: ?>
        <table class="activity-table">
          <thead><tr><th style="width:110px">Time</th><th style="width:110px">Module</th><th style="width:100px">Action</th><th>What they did</th></tr></thead>
          <tbody>
          <?php foreach ($activities as $row): ?>
            <tr>
              <td class="activity-time"><?= htmlspecialchars(date('h:i A', strtotime((string)($row['timestamp'] ?? 'now')))) ?></td>
              <td><span class="activity-module"><?= htmlspecialchars($row['category'] ?? $row['module'] ?? '-') ?></span></td>
              <td class="activity-action"><?= htmlspecialchars(ucfirst((string)($row['action'] ?? '-'))) ?></td>
              <td class="activity-detail"><?= htmlspecialchars($row['details'] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</div>

<div style="margin-top:16px;"><a class="btn btn-outline btn-sm" href="subadmin.php"><?= icon('back', 14) ?> Back to Subadmin List</a></div>
<?php require __DIR__ . '/layout_bottom.php'; ?>
