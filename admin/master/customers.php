<?php
require_once __DIR__ . '/auth.php';
require_master_login();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/icons.php';

$pageTitle = 'Customers';
$activePage = 'customers';
$q = trim((string)($_GET['q'] ?? ''));

$customers = mdb_all('customers');
if ($q !== '') {
    $needle = mb_strtolower($q);
    $customers = array_values(array_filter($customers, function ($c) use ($needle) {
        return strpos(mb_strtolower($c['name'] ?? ''), $needle) !== false
            || strpos(mb_strtolower($c['phone'] ?? ''), $needle) !== false
            || strpos(mb_strtolower($c['username'] ?? ''), $needle) !== false
            || strpos(mb_strtolower($c['email'] ?? ''), $needle) !== false;
    }));
}

$rows = [];
foreach ($customers as $c) {
    $latest = customer_latest_subscription($c['id']);
    $status = subscription_status($latest ? ($latest['expiry_date'] ?? '') : '');
    $rows[] = ['customer' => $c, 'latest' => $latest, 'status' => $status];
}
usort($rows, fn($a, $b) => strcasecmp($a['customer']['name'] ?? '', $b['customer']['name'] ?? ''));

require __DIR__ . '/layout_top.php';
?>
<div class="panel">
    <div class="panel-header">
        <h2><?= icon('customers') ?> Customers <span class="count-pill">(<?= count($rows) ?>)</span></h2>
        <div class="toolbar"><a class="btn btn-primary" href="customer_form.php?action=new"><?= icon('add', 16) ?> Add Customer</a></div>
    </div>
    <div class="panel-body">
        <form method="get" style="margin-bottom:14px;max-width:320px;">
            <input type="text" name="q" placeholder="Search name, phone, username..." value="<?= htmlspecialchars($q) ?>">
        </form>
        <?php if (isset($_GET['ok'])): ?><div class="alert">Saved successfully.</div><?php endif; ?>
        <?php if (empty($rows)): ?>
            <div class="empty-state">
                <div class="empty-icon"><?= icon('customers', 40) ?></div>
                <p>No customers found.</p>
            </div>
        <?php else: ?>
        <table class="data-table">
            <thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Username</th><th>Expiry Date</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $r): $c = $r['customer']; $latest = $r['latest']; $status = $r['status']; ?>
                <tr>
                    <td><a class="cell-link" href="customer_detail.php?id=<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></a></td>
                    <td><?= htmlspecialchars($c['phone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['username'] ?? '-') ?></td>
                    <td><?= $latest ? format_display_date_m($latest['expiry_date']) : '-' ?></td>
                    <td>
                        <span class="status-pill <?= $status['class'] ?>">
                            <?= htmlspecialchars($status['label']) ?><?php if ($status['days'] !== null && $status['class'] !== 'gray'): ?> &middot; <?= $status['days'] < 0 ? abs($status['days']) . 'd ago' : $status['days'] . 'd left' ?><?php endif; ?>
                        </span>
                    </td>
                    <td class="row-actions">
                        <a class="btn btn-outline btn-sm" href="customer_detail.php?id=<?= (int)$c['id'] ?>"><?= icon('view', 14) ?> View</a>
                        <a class="btn btn-outline btn-sm" href="customer_form.php?action=edit&id=<?= (int)$c['id'] ?>"><?= icon('edit', 14) ?> Edit</a>
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
<?php require __DIR__ . '/layout_bottom.php'; ?>
