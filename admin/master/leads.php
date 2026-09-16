<?php
require_once __DIR__ . '/auth.php';
require_master_login();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/icons.php';

$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);
if ($action === 'delete' && $id) { mdb_delete('leads', $id); header('Location: leads.php?ok=deleted'); exit; }
$leads = mdb_all('leads');
usort($leads, fn($a, $b) => strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? '')));
$pageTitle = 'Signup Leads'; $activePage = 'leads';
require __DIR__ . '/layout_top.php';
?>
<div class="panel"><div class="panel-header"><h2><?= icon('leads') ?> Signup Leads <span class="count-pill">(<?= count($leads) ?>)</span></h2></div><div class="panel-body">
<?php if (isset($_GET['ok'])): ?><div class="alert">Lead deleted.</div><?php endif; ?>
<?php if (!$leads): ?><div class="empty-state"><p>No signup leads found.</p><p>Public registration URL: <code>/signup.php</code></p></div><?php else: ?><table class="data-table"><thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Username</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($leads as $lead): ?><tr><td><?= htmlspecialchars($lead['name'] ?? '-') ?></td><td><?= htmlspecialchars($lead['phone'] ?? '-') ?></td><td><?= htmlspecialchars($lead['email'] ?? '-') ?></td><td><?= htmlspecialchars($lead['username'] ?? '-') ?></td><td><?= format_display_date_m($lead['created_at'] ?? '') ?></td><td><span class="status-pill <?= ($lead['status'] ?? 'New') === 'Converted' ? 'green' : 'orange' ?>"><?= htmlspecialchars($lead['status'] ?? 'New') ?></span></td><td class="row-actions"><?php if (($lead['status'] ?? 'New') === 'New'): ?><a class="btn btn-primary btn-sm" href="customer_form.php?action=new&lead_id=<?= (int)$lead['id'] ?>">Convert</a><?php endif; ?><a class="btn btn-outline btn-sm" href="leads.php?action=delete&id=<?= (int)$lead['id'] ?>" onclick="return confirm('Delete this lead?')">Delete</a></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></div></div>
<?php require __DIR__ . '/layout_bottom.php'; ?>