<?php
require_once __DIR__ . '/auth.php';
require_login();
require_admin();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$wholeseller = db_get_one('team', $id);
if (!$wholeseller || strcasecmp((string)($wholeseller['role'] ?? ''), 'Admin') === 0) {
    header('Location: module.php?m=team&ok=not-found');
    exit;
}

function wholeseller_invoice_paid($invoiceNo) {
    $paid = 0.0;
    foreach (db_get_all('payments') as $payment) {
        if ((string)($payment['invoice_no'] ?? '') === (string)$invoiceNo) {
            $paid += (float)($payment['amount'] ?? 0);
        }
    }
    return $paid;
}

$wholesellerName = trim((string)($wholeseller['name'] ?? ''));
$invoices = array_values(array_filter(db_get_all('invoices'), function ($invoice) use ($wholesellerName) {
    return $wholesellerName !== '' && strcasecmp(trim((string)($invoice['customer'] ?? '')), $wholesellerName) === 0;
}));

foreach ($invoices as &$invoice) {
    $invoice['paid'] = wholeseller_invoice_paid($invoice['invoice_no'] ?? '');
    $invoice['pending'] = max(0, (float)($invoice['amount'] ?? 0) - $invoice['paid']);
    $invoice['status_display'] = $invoice['pending'] <= 0 ? 'Paid' : ($invoice['paid'] > 0 ? 'Partly Paid' : 'Full Pending');
}
unset($invoice);
usort($invoices, fn($a, $b) => ((int)($b['id'] ?? 0)) <=> ((int)($a['id'] ?? 0)));

$totalBilling = array_sum(array_map(fn($i) => (float)($i['amount'] ?? 0), $invoices));
$totalPaid = array_sum(array_map(fn($i) => (float)($i['paid'] ?? 0), $invoices));
$totalPending = max(0, $totalBilling - $totalPaid);

$payments = array_values(array_filter(db_get_all('payments'), function ($payment) use ($wholesellerName) {
    return $wholesellerName !== '' && strcasecmp(trim((string)($payment['customer'] ?? '')), $wholesellerName) === 0;
}));
usort($payments, fn($a, $b) => strcmp((string)($b['date'] ?? ''), (string)($a['date'] ?? '')));

$pageTitle = $wholesellerName . ' - Wholeseller Profile';
require __DIR__ . '/layout_top.php';
?>

<style>
.team-profile-grid { display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap:14px; margin-bottom:18px; }
.team-profile-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:18px; }
.team-profile-label { font-size:12px; color:#64748b; margin-bottom:6px; }
.team-profile-value { font-size:21px; font-weight:700; color:#0f172a; }
.team-profile-head { display:flex; align-items:center; gap:18px; }
.team-avatar { width:72px; height:72px; border-radius:50%; overflow:hidden; background:#eef2f7; display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:700; color:#334155; flex:0 0 auto; }
.team-avatar img { width:100%; height:100%; object-fit:cover; }
.team-profile-name { margin:0; font-size:24px; }
.team-profile-meta { margin:5px 0 0; color:#64748b; }
.team-profile-actions { margin-left:auto; display:flex; gap:8px; }
.billing-table-wrap { overflow-x:auto; }
.billing-table { width:100%; border-collapse:collapse; }
.billing-table th,.billing-table td { padding:11px 12px; border-bottom:1px solid #edf0f3; text-align:left; white-space:nowrap; }
.billing-table th { font-size:12px; color:#64748b; font-weight:700; }
.billing-empty { padding:34px; text-align:center; color:#64748b; }
@media(max-width:900px){ .team-profile-grid{grid-template-columns:repeat(2,minmax(0,1fr));} .team-profile-head{align-items:flex-start; flex-wrap:wrap;} .team-profile-actions{margin-left:0; width:100%;} }
@media(max-width:560px){ .team-profile-grid{grid-template-columns:1fr;} .team-profile-name{font-size:20px;} }
</style>

<div class="panel">
  <div class="panel-header">
    <div class="team-profile-head">
      <div class="team-avatar">
        <?php if (!empty($wholeseller['photo'])): ?>
          <img src="<?= htmlspecialchars($wholeseller['photo']) ?>" alt="<?= htmlspecialchars($wholesellerName) ?>">
        <?php else: ?>
          <?= htmlspecialchars(strtoupper(substr($wholesellerName, 0, 1))) ?>
        <?php endif; ?>
      </div>
      <div>
        <h2 class="team-profile-name"><?= htmlspecialchars($wholesellerName) ?></h2>
        <p class="team-profile-meta">Wholeseller • <?= htmlspecialchars($wholeseller['phone'] ?? '-') ?></p>
      </div>
    </div>
    <div class="team-profile-actions">
      <a href="module.php?m=team&action=edit&id=<?= (int)$wholeseller['id'] ?>" class="btn btn-outline btn-sm"><?= icon('edit',14) ?> Edit</a>
      <a href="module.php?m=team" class="btn btn-outline btn-sm"><?= icon('back',14) ?> Back</a>
    </div>
  </div>
  <div class="panel-body">
    <div class="team-profile-grid">
      <div class="team-profile-card"><div class="team-profile-label">Total Billing</div><div class="team-profile-value">₹<?= number_format($totalBilling,2) ?></div></div>
      <div class="team-profile-card"><div class="team-profile-label">Total Paid</div><div class="team-profile-value">₹<?= number_format($totalPaid,2) ?></div></div>
      <div class="team-profile-card"><div class="team-profile-label">Pending Amount</div><div class="team-profile-value">₹<?= number_format($totalPending,2) ?></div></div>
      <div class="team-profile-card"><div class="team-profile-label">Total Invoices</div><div class="team-profile-value"><?= count($invoices) ?></div></div>
    </div>

    <div class="panel" style="margin:0 0 18px 0;">
      <div class="panel-header"><h3>Wholeseller Profile</h3></div>
      <div class="panel-body">
        <div class="detail-grid">
          <div class="detail-item"><div class="detail-label">Name</div><div class="detail-value"><?= htmlspecialchars($wholesellerName ?: '-') ?></div></div>
          <div class="detail-item"><div class="detail-label">Phone Number</div><div class="detail-value"><?= htmlspecialchars($wholeseller['phone'] ?? '-') ?></div></div>
          <div class="detail-item detail-item-wide"><div class="detail-label">Address</div><div class="detail-value"><?= nl2br(htmlspecialchars($wholeseller['address'] ?? '-')) ?></div></div>
        </div>
      </div>
    </div>

    <div class="panel" style="margin:0 0 18px 0;">
      <div class="panel-header"><h3>Previous Billing</h3><span class="count-pill">(<?= count($invoices) ?>)</span></div>
      <div class="panel-body billing-table-wrap">
        <?php if (empty($invoices)): ?>
          <div class="billing-empty">No previous billing found for this wholeseller.</div>
        <?php else: ?>
          <table class="billing-table">
            <thead><tr><th>Invoice No.</th><th>Date</th><th>Amount</th><th>Paid</th><th>Pending</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
              <?php foreach ($invoices as $invoice): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($invoice['invoice_no'] ?? '-') ?></strong></td>
                  <td><?= htmlspecialchars(format_display_date($invoice['date'] ?? $invoice['due_date'] ?? ($invoice['created_at'] ?? ''))) ?></td>
                  <td>₹<?= number_format((float)($invoice['amount'] ?? 0),2) ?></td>
                  <td>₹<?= number_format((float)($invoice['paid'] ?? 0),2) ?></td>
                  <td>₹<?= number_format((float)($invoice['pending'] ?? 0),2) ?></td>
                  <td><span class="badge <?= badge_class($invoice['status_display']) ?>"><?= htmlspecialchars($invoice['status_display']) ?></span></td>
                  <td>
                    <a class="btn btn-outline btn-sm" href="invoice_print.php?id=<?= (int)$invoice['id'] ?>" target="_blank"><?= icon('view',14) ?> Invoice</a>
                    <a class="btn btn-outline btn-sm" href="module.php?m=invoices&action=edit&id=<?= (int)$invoice['id'] ?>">Open</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

    <div class="panel" style="margin:0;">
      <div class="panel-header"><h3>Payment History</h3><span class="count-pill">(<?= count($payments) ?>)</span></div>
      <div class="panel-body billing-table-wrap">
        <?php if (empty($payments)): ?>
          <div class="billing-empty">No payments recorded for this wholeseller.</div>
        <?php else: ?>
          <table class="billing-table">
            <thead><tr><th>Date</th><th>Invoice No.</th><th>Amount</th><th>Mode</th><th>Reference</th></tr></thead>
            <tbody>
              <?php foreach ($payments as $payment): ?>
                <tr>
                  <td><?= htmlspecialchars(format_display_date($payment['date'] ?? '')) ?></td>
                  <td><?= htmlspecialchars($payment['invoice_no'] ?? '-') ?></td>
                  <td>₹<?= number_format((float)($payment['amount'] ?? 0),2) ?></td>
                  <td><?= htmlspecialchars($payment['mode'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($payment['reference'] ?? '-') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/layout_bottom.php'; ?>
