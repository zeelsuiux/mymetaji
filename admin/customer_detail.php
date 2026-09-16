<?php
require_once __DIR__ . '/auth.php';
require_login();
if (is_subadmin()) {
    header('Location: module.php?m=customers');
    exit;
}
require_admin();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$customer = db_get_one('customers', $id);
if (!$customer) {
    header('Location: module.php?m=customers');
    exit;
}

$activeModule = 'customers';
$pageTitle = 'Customer — ' . $customer['name'];
$stats = customer_stats($customer['name']);
$customerPayments = array_reduce($stats['payments'], fn($carry, $p) => $carry + (float)($p['amount'] ?? 0), 0.0);
$totalPending = max(0, (float)$stats['total_invoiced'] - $customerPayments);

require __DIR__ . '/layout_top.php';
?>

<div style="margin-bottom:16px;">
  <a href="module.php?m=customers" class="btn btn-outline btn-sm"><?= icon('back', 15) ?> Back to Customers</a>
</div>

<!-- Profile card -->
<div class="panel" style="margin-bottom:20px;">
  <div class="panel-body" style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:16px;">
    <div style="display:flex;gap:16px;align-items:flex-start;">
      <div class="avatar-circle"><?= strtoupper(substr($customer['name'], 0, 1)) ?></div>
      <div>
        <div style="font-size:19px;font-weight:700;display:flex;align-items:center;gap:10px;">
          <?= htmlspecialchars($customer['name']) ?>
        </div>
        <?php if (!empty($customer['company'])): ?>
          <div style="color:var(--text-muted);font-size:13px;margin-top:2px;"><?= icon('building', 13) ?> <?= htmlspecialchars($customer['company']) ?></div>
        <?php endif; ?>
        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:8px;font-size:13px;color:var(--text-muted);">
          <?php if (!empty($customer['phone'])): ?><span><?= icon('phone', 13) ?> <?= htmlspecialchars($customer['phone']) ?></span><?php endif; ?>
          <?php if (!empty($customer['email'])): ?><span><?= icon('mail', 13) ?> <?= htmlspecialchars($customer['email']) ?></span><?php endif; ?>
        </div>
      </div>
    </div>
    <a href="module.php?m=customers&action=edit&id=<?= $customer['id'] ?>" class="btn btn-outline btn-sm"><?= icon('edit', 14) ?> Edit Customer</a>
  </div>
</div>

<!-- KPI cards: orders, pending, revenue -->
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-icon" style="background:#E8F5E9;color:var(--primary);"><?= icon('invoices', 20) ?></div>
    <div class="label">Total Orders (Invoices)</div>
    <div class="value"><?= $stats['orders'] ?></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon" style="background:#E8F5E9;color:#2F7D32;"><?= icon('alert', 20) ?></div>
    <div class="label">Pending Invoices</div>
    <div class="value"><?= $stats['pending_count'] ?> <span style="font-size:13px;color:var(--text-muted);font-weight:500;">(₹<?= number_format($stats['pending_amount'], 2) ?>)</span></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon" style="background:#E8F5E9;color:var(--primary);"><?= icon('chart', 20) ?></div>
    <div class="label">Total Invoiced</div>
    <div class="value green">₹<?= number_format($stats['total_invoiced'], 2) ?></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon" style="background:#E6F7EC;color:#1B8A45;"><?= icon('rupee', 20) ?></div>
    <div class="label">Total Revenue Given</div>
    <div class="value green">₹<?= number_format($stats['total_revenue'], 2) ?></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon" style="background:#E8F5E9;color:#2F7D32;"><?= icon('alert', 20) ?></div>
    <div class="label">Pending Amount</div>
    <div class="value orange">₹<?= number_format($totalPending, 2) ?></div>
  </div>
</div>

<!-- Invoices -->
<div class="panel" style="margin-bottom:20px;">
  <div class="panel-header">
    <h2><?= icon('invoices') ?> Invoices <span class="count-pill">(<?= count($stats['invoices']) ?>)</span></h2>
  </div>
  <div class="panel-body">
    <?php if (empty($stats['invoices'])): ?>
      <div class="empty-state"><div class="empty-icon"><?= icon('invoices', 30) ?></div>No invoices for this customer yet.</div>
    <?php else: ?>
      <table class="data-table">
        <thead><tr><th>Invoice No.</th><th>Amount</th><th>Status</th><th>Due Date</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($stats['invoices'] as $inv): ?>
            <tr>
              <td><?= htmlspecialchars($inv['invoice_no']) ?></td>
              <td><?= icon('rupee', 12) ?> <?= number_format((float)$inv['amount'], 2) ?></td>
              <td><span class="badge <?= badge_class($inv['status'] ?? '') ?>"><?= htmlspecialchars($inv['status']) ?></span></td>
              <td><?= htmlspecialchars(format_display_date($inv['due_date'] ?? '')) ?></td>
              <td class="row-actions">
                <a class="btn btn-outline btn-sm" href="invoice_print.php?id=<?= $inv['id'] ?>" target="_blank"><?= icon('download', 14) ?> PDF</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/layout_bottom.php'; ?>
