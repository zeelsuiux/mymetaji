<?php
require_once __DIR__ . '/auth.php';
require_master_login();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/../../includes/payment.php';

$pageTitle = 'Dashboard';
$customers = mdb_all('customers');
$paymentTotals = payment_totals();
$recentPayments = array_slice(payment_all(), 0, 5);

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
    <div class="kpi-card"><div class="label"><?= icon('rupee', 14) ?> Collected This Month</div><div class="value green">₹<?= number_format($paymentTotals['revenue_month'], 2) ?></div></div>
</div>

<?php
$subscriptionTotal = max(1, count($customers));
$subscriptionActive = $counts['active'];
$subscriptionExpired = $counts['expired'];
$subscriptionTrial = $counts['expiring'];
$subscriptionPending = $counts['none'];
$subscriptionActiveEnd = round(($subscriptionActive / $subscriptionTotal) * 100, 2);
$subscriptionExpiredEnd = $subscriptionActiveEnd + round(($subscriptionExpired / $subscriptionTotal) * 100, 2);
$subscriptionTrialEnd = $subscriptionExpiredEnd + round(($subscriptionTrial / $subscriptionTotal) * 100, 2);
?>
<div class="master-dashboard-grid">
    <div class="panel subscription-chart-card">
        <div class="panel-header"><h2>Subscription Status</h2><span class="count-pill"><?= count($customers) ?> total</span></div>
        <div class="subscription-chart-body">
            <div class="subscription-donut" style="background: conic-gradient(#36b878 0 <?= $subscriptionActiveEnd ?>%, #ee555b <?= $subscriptionActiveEnd ?>% <?= $subscriptionExpiredEnd ?>%, #f3ad27 <?= $subscriptionExpiredEnd ?>% <?= $subscriptionTrialEnd ?>%, #4b83df <?= $subscriptionTrialEnd ?>% 100%);"><div><strong><?= count($customers) ?></strong><span>Total</span></div></div>
            <div class="subscription-legend">
                <div><i class="subscription-dot dot-active"></i><span>Active</span><b><?= $subscriptionActive ?></b></div>
                <div><i class="subscription-dot dot-expired"></i><span>Expired</span><b><?= $subscriptionExpired ?></b></div>
                <div><i class="subscription-dot dot-trial"></i><span>Trial / Expiring</span><b><?= $subscriptionTrial ?></b></div>
                <div><i class="subscription-dot dot-pending"></i><span>Pending</span><b><?= $subscriptionPending ?></b></div>
            </div>
        </div>
    </div>
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
                        <a class="btn btn-outline btn-sm" href="<?= htmlspecialchars(customer_panel_path($c['slug'])) ?>" target="_blank"><?= icon('building', 14) ?> Open Panel</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h2><?= icon('payments') ?> Recent Payments</h2>
        <div class="toolbar">
            <a class="btn btn-outline" href="payments.php">View Payment Ledger</a>
        </div>
    </div>
    <div class="panel-body">
        <?php if (empty($recentPayments)): ?>
            <div class="empty-state">
                <div class="empty-icon"><?= icon('payments', 40) ?></div>
                <p>No payments yet. Trials are free, so they do not appear here — the first row appears when a trial converts or an offline payment is recorded.</p>
            </div>
        <?php else: ?>
        <table class="data-table">
            <thead><tr><th>Date</th><th>Customer</th><th>Plan</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($recentPayments as $p): $meta = payment_status_meta((string)($p['status'] ?? '')); ?>
                <tr>
                    <td><?= format_display_date_m($p['paid_at'] ?? $p['created_at'] ?? '') ?></td>
                    <td>
                        <?php if (!empty($p['customer_id'])): ?>
                            <a class="cell-link" href="customer_detail.php?id=<?= (int)$p['customer_id'] ?>"><?= htmlspecialchars((string)($p['customer_name'] ?? '')) ?></a>
                        <?php else: ?>
                            <?= htmlspecialchars((string)($p['customer_name'] ?: '—')) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars((string)($p['plan'] ?? '—')) ?><div style="font-size:12px;color:#6B7280;"><?= htmlspecialchars(plan_cycle_label($p['cycle'] ?? 'monthly')) ?></div></td>
                    <td><b>₹<?= number_format((float)($p['amount'] ?? 0), 2) ?></b></td>
                    <td><span class="status-pill <?= $meta['class'] ?>"><?= htmlspecialchars($meta['label']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>
