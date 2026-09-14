<?php
require_once __DIR__ . '/auth.php';
require_master_login();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/icons.php';

$pageTitle = 'Dashboard';
$customers = mdb_all('customers');

$counts = ['active' => 0, 'expiring' => 0, 'expired' => 0, 'none' => 0];
$rows = [];
foreach ($customers as $c) {
    $latest = customer_latest_subscription($c['id']);
    $status = subscription_status($latest ? ($latest['expiry_date'] ?? '') : '');
    if ($status['class'] === 'green') $counts['active']++;
    elseif ($status['class'] === 'orange') $counts['expiring']++;
    elseif ($status['class'] === 'red') $counts['expired']++;
    else $counts['none']++;
    $rows[] = ['customer' => $c, 'latest' => $latest, 'status' => $status];
}

// Most urgent first: expired, then expiring soon, then active, then none.
$rank = ['red' => 0, 'orange' => 1, 'green' => 2, 'gray' => 3];
usort($rows, function ($a, $b) use ($rank) {
    $ra = $rank[$a['status']['class']] ?? 9;
    $rb = $rank[$b['status']['class']] ?? 9;
    if ($ra !== $rb) return $ra <=> $rb;
    return ($a['status']['days'] ?? 9999) <=> ($b['status']['days'] ?? 9999);
});

require __DIR__ . '/layout_top.php';
?>
<div class="kpi-grid master-kpis">
    <div class="kpi-card"><div class="label">Total Customers</div><div class="value"><?= count($customers) ?></div></div>
    <div class="kpi-card"><div class="label">Active</div><div class="value green"><?= $counts['active'] ?></div></div>
    <div class="kpi-card"><div class="label">Expiring Soon (15 days)</div><div class="value orange"><?= $counts['expiring'] ?></div></div>
    <div class="kpi-card"><div class="label">Expired</div><div class="value" style="color:#C0392B;"><?= $counts['expired'] ?></div></div>
</div>

<div class="panel">
    <div class="panel-header">
        <h2><?= icon('customers') ?> Customers Needing Attention</h2>
        <div class="toolbar">
            <a class="btn btn-outline" href="customers.php">View All Customers</a>
            <a class="btn btn-primary" href="customer_form.php?action=new"><?= icon('add', 16) ?> Add Customer</a>
        </div>
    </div>
    <div class="panel-body">
        <?php $rows = array_slice($rows, 0, 8); ?>
        <?php if (empty($rows)): ?>
            <div class="empty-state">
                <div class="empty-icon"><?= icon('customers', 40) ?></div>
                <p>No customers yet. Click "Add Customer" to create your first shopkeeper account.</p>
            </div>
        <?php else: ?>
        <table class="data-table">
            <thead><tr><th>Name</th><th>Phone</th><th>Username</th><th>Expiry Date</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $r): $c = $r['customer']; $latest = $r['latest']; $status = $r['status']; ?>
                <tr>
                    <td><a class="cell-link" href="customer_detail.php?id=<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></a></td>
                    <td><?= htmlspecialchars($c['phone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['username'] ?? '-') ?></td>
                    <td><?= $latest ? format_display_date_m($latest['expiry_date']) : '-' ?></td>
                    <td>
                        <span class="status-pill <?= $status['class'] ?>">
                            <?= htmlspecialchars($status['label']) ?><?php if ($status['days'] !== null && $status['class'] !== 'gray'): ?> &middot; <?= $status['days'] < 0 ? abs($status['days']) . 'd ago' : $status['days'] . 'd left' ?><?php endif; ?>
                        </span>
                    </td>
                    <td class="row-actions">
                        <a class="btn btn-outline btn-sm" href="customer_detail.php?id=<?= (int)$c['id'] ?>"><?= icon('view', 14) ?> View</a>
                        <?php if (!empty($c['slug'])): ?>
                        <a class="btn btn-outline btn-sm" href="../s/<?= htmlspecialchars($c['slug']) ?>/" target="_blank"><?= icon('building', 14) ?> Open Panel</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>
