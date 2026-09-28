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
$pageTitle = $customer['name'];
$stats = customer_stats($customer['name']);
$customerPayments = array_reduce($stats['payments'], fn($carry, $p) => $carry + (float)($p['amount'] ?? 0), 0.0);
$totalPending = max(0, (float)$stats['total_invoiced'] - $customerPayments);
$backUrl = (string)($_GET['return_to'] ?? 'module.php?m=customers');
if (!preg_match('/^module\.php\?/', $backUrl)) $backUrl = 'module.php?m=customers';

// Risk Management
$riskData = calculate_customer_risk_score($customer['name']);
$creditSummary = customer_credit_summary($customer['name']);
$paymentDelay = get_payment_delay_days($customer['name']);

// Quotation stats
$quotations = array_values(array_filter(db_get_all('quotations'), fn($q) => strcasecmp(trim((string)($q['customer'] ?? '')), trim($customer['name'])) === 0));
usort($quotations, fn($a, $b) => strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? '')));
$quotationCount = count($quotations);

// Payment history
$payments = array_values(array_filter(db_get_all('payments'), fn($p) => strcasecmp(trim((string)($p['customer'] ?? '')), trim($customer['name'])) === 0));
usort($payments, fn($a, $b) => strcmp((string)($b['date'] ?? ''), (string)($a['date'] ?? '')));

// Overdue invoices — properly count unique invoice numbers
$overdueInvoiceNos = [];
foreach ($stats['invoices'] as $inv) {
    if (($inv['status'] ?? '') !== 'Paid' && !empty($inv['due_date']) && $inv['due_date'] < date('Y-m-d')) {
        $overdueInvoiceNos[$inv['invoice_no'] ?? ''] = true;
    }
}
$overdueCount = count($overdueInvoiceNos);

// Last purchase date
$lastPurchaseDate = '';
if (!empty($stats['invoices'])) {
    $dates = array_map(fn($inv) => $inv['created_at'] ?? $inv['date'] ?? '', $stats['invoices']);
    usort($dates, fn($a, $b) => strcmp($b, $a));
    $lastPurchaseDate = $dates[0] ?? '';
}

// Average order value
$avgOrderValue = $stats['orders'] > 0 ? (float)$stats['total_invoiced'] / $stats['orders'] : 0;

// Payment completion percentage
$paymentPercent = $stats['total_invoiced'] > 0 ? min(100, ($customerPayments / $stats['total_invoiced']) * 100) : 0;

// Active tab
$tab = $_GET['tab'] ?? 'invoices';
if (!in_array($tab, ['invoices', 'quotations', 'payments'])) $tab = 'invoices';

require __DIR__ . '/layout_top.php';
?>

<style>
/* Customer Detail — Clean Modern UI */
.cd-page { max-width: 1100px; margin: 0 auto; }

/* Breadcrumb + Actions */
.cd-topbar {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 16px; flex-wrap: wrap; gap: 10px;
}
.cd-breadcrumb {
  font-size: 13px; color: #94a3b8; text-decoration: none;
  display: inline-flex; align-items: center; gap: 4px;
}
.cd-breadcrumb:hover { color: #334155; }
.cd-topbar-actions { display: flex; gap: 8px; }

/* Profile Card */
.cd-profile {
  background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 60%, #f0f9ff 100%);
  border: 1px solid #e2e8f0; border-radius: 16px;
  padding: 24px 28px; margin-bottom: 18px;
  display: flex; align-items: center; gap: 20px; flex-wrap: wrap;
  position: relative; overflow: hidden;
}
.cd-profile::after {
  content: ''; position: absolute; top: -30px; right: -30px;
  width: 100px; height: 100px; border-radius: 50%;
  background: rgba(47,125,50,0.05);
}
.cd-avatar {
  width: 60px; height: 60px; border-radius: 14px;
  background: #2f7d32; color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: 24px; font-weight: 800; flex-shrink: 0;
  box-shadow: 0 4px 14px rgba(47,125,50,0.22);
}
.cd-profile-info { flex: 1; min-width: 0; }
.cd-name { font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 3px; }
.cd-company { font-size: 13px; color: #64748b; margin-bottom: 6px; display: flex; align-items: center; gap: 5px; }
.cd-meta { display: flex; flex-wrap: wrap; gap: 14px; font-size: 12.5px; color: #64748b; }
.cd-meta span { display: inline-flex; align-items: center; gap: 4px; }
.cd-profile-actions { display: flex; gap: 8px; flex-shrink: 0; }

/* KPI Strip */
.cd-kpis {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;
  margin-bottom: 18px;
}
.cd-kpi {
  background: #fff; border: 1px solid #e9ecef; border-radius: 12px;
  padding: 16px; text-align: center;
  transition: box-shadow 0.15s;
}
.cd-kpi:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.cd-kpi-icon {
  width: 36px; height: 36px; border-radius: 10px; margin: 0 auto 8px;
  display: flex; align-items: center; justify-content: center;
}
.cd-kpi-value { font-size: 22px; font-weight: 800; color: #1e293b; line-height: 1.2; }
.cd-kpi-label { font-size: 11.5px; color: #94a3b8; margin-top: 2px; font-weight: 600; letter-spacing: 0.3px; text-transform: uppercase; }

/* Payment Progress */
.cd-payment-bar {
  background: #fff; border: 1px solid #e9ecef; border-radius: 12px;
  padding: 16px 20px; margin-bottom: 18px;
  display: flex; align-items: center; gap: 18px; flex-wrap: wrap;
}
.cd-payment-bar-left { flex-shrink: 0; }
.cd-payment-bar-left strong { font-size: 13px; color: #1e293b; display: block; margin-bottom: 1px; }
.cd-payment-bar-left small { font-size: 12px; color: #94a3b8; }
.cd-progress-wrap { flex: 1; min-width: 180px; }
.cd-progress-info { display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px; }
.cd-progress-track { height: 8px; background: #f1f5f9; border-radius: 8px; overflow: hidden; }
.cd-progress-fill { height: 100%; border-radius: 8px; background: #22c55e; transition: width 0.6s ease; }
.cd-progress-fill.mid { background: #f59e0b; }
.cd-progress-fill.low { background: #ef4444; }
.cd-overdue-badge {
  flex-shrink: 0; background: #fef2f2; border: 1px solid #fecaca;
  border-radius: 10px; padding: 8px 14px; text-align: center; min-width: 60px;
}
.cd-overdue-badge .num { font-size: 20px; font-weight: 800; color: #dc2626; line-height: 1; }
.cd-overdue-badge .lbl { font-size: 10px; color: #dc2626; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

/* Tabs */
.cd-tabs-bar {
  display: flex; gap: 0; border-bottom: 2px solid #e9ecef; margin-bottom: 0;
}
.cd-tab-link {
  padding: 11px 20px; font-size: 13.5px; font-weight: 600;
  color: #94a3b8; text-decoration: none;
  border-bottom: 2px solid transparent; margin-bottom: -2px;
  transition: all 0.15s; display: flex; align-items: center; gap: 6px;
}
.cd-tab-link:hover { color: #334155; background: #f8fafc; }
.cd-tab-link.active { color: #2f7d32; border-bottom-color: #2f7d32; }
.cd-tab-badge {
  background: #f1f5f9; color: #64748b; font-size: 11px; font-weight: 700;
  padding: 1px 7px; border-radius: 10px; line-height: 1.4;
}
.cd-tab-link.active .cd-tab-badge { background: #dcfce7; color: #15803d; }

/* Tab Panel */
.cd-panel {
  background: #fff; border: 1px solid #e9ecef; border-top: none;
  border-radius: 0 0 12px 12px;
}

/* Record Cards */
.cd-records { padding: 14px; }
.cd-record {
  border: 1px solid #f1f5f9; border-radius: 10px;
  padding: 14px 16px; margin-bottom: 8px;
  display: flex; align-items: center; gap: 14px;
  transition: border-color 0.12s, background 0.12s;
}
.cd-record:hover { border-color: #e2e8f0; background: #f8fafc; }
.cd-record:last-child { margin-bottom: 0; }
.cd-record-icon {
  width: 38px; height: 38px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.cd-record-body { flex: 1; min-width: 0; }
.cd-record-title { font-size: 13.5px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 7px; flex-wrap: wrap; }
.cd-record-sub { font-size: 12px; color: #94a3b8; margin-top: 3px; display: flex; gap: 12px; flex-wrap: wrap; }
.cd-record-right { text-align: right; flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end; gap: 5px; }
.cd-record-amount { font-size: 15px; font-weight: 800; }
.cd-record-actions { display: flex; gap: 5px; }

/* Overdue card highlight */
.cd-record.overdue { border-color: #fca5a5; background: #fff5f5; }
.cd-record.overdue:hover { background: #fef2f2; }

/* Empty state */
.cd-empty { padding: 44px 20px; text-align: center; }
.cd-empty-ic { font-size: 36px; opacity: 0.3; margin-bottom: 10px; }
.cd-empty-msg { font-size: 13px; color: #94a3b8; }

/* Responsive */
@media (max-width: 768px) {
  .cd-kpis { grid-template-columns: 1fr 1fr; }
  .cd-profile { padding: 18px; }
  .cd-record { flex-direction: column; align-items: flex-start; }
  .cd-record-right { align-items: flex-start; text-align: left; flex-direction: row; gap: 10px; width: 100%; justify-content: space-between; }
}
@media (max-width: 480px) {
  .cd-kpis { grid-template-columns: 1fr 1fr; gap: 8px; }
  .cd-kpi { padding: 12px 8px; }
  .cd-kpi-value { font-size: 18px; }
  .cd-profile { flex-direction: column; text-align: center; }
  .cd-meta { justify-content: center; }
  .cd-profile-actions { justify-content: center; width: 100%; }
}

/* Risk Summary */
.cd-risk {
  background: #fff; border: 1px solid #e9ecef; border-radius: 12px;
  padding: 16px 20px; margin-bottom: 18px;
}
.cd-risk-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 12px;
}
.cd-risk-title { font-size: 13px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 6px; }
.cd-risk-badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
}
.cd-risk-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;
}
.cd-risk-item {
  text-align: center; padding: 10px 8px; border-radius: 10px; background: #f8fafc;
}
.cd-risk-item-value { font-size: 18px; font-weight: 800; color: #1e293b; line-height: 1.2; }
.cd-risk-item-label { font-size: 10.5px; color: #94a3b8; margin-top: 2px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }
.cd-risk-credit-bar {
  margin-top: 12px; padding-top: 12px; border-top: 1px solid #f1f5f9;
}
.cd-risk-credit-info {
  display: flex; justify-content: space-between; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 5px;
}
.cd-risk-credit-track { height: 6px; background: #f1f5f9; border-radius: 6px; overflow: hidden; }
.cd-risk-credit-fill { height: 100%; border-radius: 6px; transition: width 0.5s ease; }
.cd-risk-hold-banner {
  margin-top: 10px; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;
  display: flex; align-items: center; gap: 6px;
}
@media (max-width: 480px) {
  .cd-risk-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
}
</style>

<div class="cd-page">

<!-- Top Bar -->
<div class="cd-topbar">
  <a href="<?= htmlspecialchars($backUrl) ?>" class="cd-breadcrumb"><?= icon('back', 15) ?> Customers</a>
  <div class="cd-topbar-actions">
    <a href="module.php?m=customers&action=edit&id=<?= $customer['id'] ?>" class="btn btn-outline btn-sm"><?= icon('edit', 13) ?> Edit</a>
    <a href="module.php?m=invoices&action=new&customer=<?= urlencode($customer['name']) ?>" class="btn btn-primary btn-sm"><?= icon('add', 14) ?> New Invoice</a>
    <?php if ($totalPending > 0): ?>
      <a href="module.php?m=payments&action=new&customer=<?= urlencode($customer['name']) ?>" class="btn btn-outline btn-sm" style="border-color:#2f7d32;color:#2f7d32;"><?= icon('rupee', 13) ?> Record Payment</a>
    <?php endif; ?>
  </div>
</div>

<!-- Profile -->
<div class="cd-profile">
  <div class="cd-avatar"><?= strtoupper(mb_substr($customer['name'], 0, 1)) ?></div>
  <div class="cd-profile-info">
    <div class="cd-name"><?= htmlspecialchars($customer['name']) ?></div>
    <?php if (!empty($customer['company'])): ?>
      <div class="cd-company"><?= icon('building', 13) ?> <?= htmlspecialchars($customer['company']) ?></div>
    <?php endif; ?>
    <div class="cd-meta">
      <?php if (!empty($customer['phone'])): ?><span><?= icon('phone', 13) ?> <?= htmlspecialchars($customer['phone']) ?></span><?php endif; ?>
      <?php if (!empty($customer['email'])): ?><span><?= icon('mail', 13) ?> <?= htmlspecialchars($customer['email']) ?></span><?php endif; ?>
      <?php if (!empty($customer['address'])): ?><span><?= icon('building', 13) ?> <?= htmlspecialchars(mb_strimwidth($customer['address'], 0, 50, '...')) ?></span><?php endif; ?>
      <?php if ($lastPurchaseDate !== ''): ?><span><?= icon('calendar', 13) ?> Last: <?= htmlspecialchars(format_display_date($lastPurchaseDate)) ?></span><?php endif; ?>
    </div>
  </div>
</div>

<!-- Risk Summary -->
<?php $riskColor = risk_color_class($riskData['rating']); $riskBg = risk_bg_class($riskData['rating']); ?>
<div class="cd-risk">
  <div class="cd-risk-header">
    <div class="cd-risk-title">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="<?= $riskColor ?>" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      Risk Summary
    </div>
    <div class="cd-risk-badge" style="background:<?= $riskBg ?>;color:<?= $riskColor ?>;">
      <?= $riskData['rating'] ?> Risk · <?= $riskData['score'] ?>/100
    </div>
  </div>
  <div class="cd-risk-grid">
    <div class="cd-risk-item">
      <div class="cd-risk-item-value" style="color:<?= $riskColor ?>;"><?= $riskData['score'] ?></div>
      <div class="cd-risk-item-label">Risk Score</div>
    </div>
    <div class="cd-risk-item">
      <div class="cd-risk-item-value"><?= $paymentDelay ?>d</div>
      <div class="cd-risk-item-label">Avg Pay Delay</div>
    </div>
    <div class="cd-risk-item">
      <div class="cd-risk-item-value" style="color:<?= ($stats['overdue_count'] ?? 0) > 0 ? '#dc2626' : '#16a34a' ?>;"><?= $stats['overdue_count'] ?? 0 ?></div>
      <div class="cd-risk-item-label">Overdue Inv.</div>
    </div>
  </div>
  <?php if ($creditSummary['credit_limit'] > 0): ?>
  <div class="cd-risk-credit-bar">
    <div class="cd-risk-credit-info">
      <span>Credit: ₹<?= number_format($creditSummary['used_credit'], 0) ?> / ₹<?= number_format($creditSummary['credit_limit'], 0) ?></span>
      <span><?= number_format($creditSummary['usage_percent'], 1) ?>% used</span>
    </div>
    <div class="cd-risk-credit-track">
      <div class="cd-risk-credit-fill" style="width:<?= $creditSummary['usage_percent'] ?>%;background:<?= $creditSummary['is_over_limit'] ? '#dc2626' : ($creditSummary['usage_percent'] > 80 ? '#f59e0b' : '#16a34a') ?>;"></div>
    </div>
  </div>
  <?php endif; ?>
  <?php if ($creditSummary['credit_hold'] === 'Yes'): ?>
  <div class="cd-risk-hold-banner" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    Credit Hold Active — New invoices are blocked for this customer.
  </div>
  <?php endif; ?>
</div>

<!-- KPI Cards -->
<div class="cd-kpis">
  <div class="cd-kpi">
    <div class="cd-kpi-icon" style="background:#f0fdf4;color:#16a34a;"><?= icon('invoices', 18) ?></div>
    <div class="cd-kpi-value"><?= $stats['orders'] ?></div>
    <div class="cd-kpi-label">Orders</div>
  </div>
  <div class="cd-kpi">
    <div class="cd-kpi-icon" style="background:#eff6ff;color:#2563eb;"><?= icon('chart', 18) ?></div>
    <div class="cd-kpi-value">₹<?= number_format($stats['total_invoiced'], 0) ?></div>
    <div class="cd-kpi-label">Invoiced</div>
  </div>
  <div class="cd-kpi">
    <div class="cd-kpi-icon" style="background:#f0fdf4;color:#16a34a;"><?= icon('rupee', 18) ?></div>
    <div class="cd-kpi-value" style="color:#16a34a;">₹<?= number_format($customerPayments, 0) ?></div>
    <div class="cd-kpi-label">Paid</div>
  </div>
  <div class="cd-kpi">
    <div class="cd-kpi-icon" style="background:<?= $totalPending > 0 ? '#fef3c7' : '#f0fdf4' ?>;color:<?= $totalPending > 0 ? '#d97706' : '#16a34a' ?>;"><?= icon('alert', 18) ?></div>
    <div class="cd-kpi-value" style="color:<?= $totalPending > 0 ? '#d97706' : '#16a34a' ?>;">₹<?= number_format($totalPending, 0) ?></div>
    <div class="cd-kpi-label">Pending</div>
  </div>
</div>

<!-- Payment Progress -->
<div class="cd-payment-bar">
  <div class="cd-payment-bar-left">
    <strong>Payment Status</strong>
    <small><?= $stats['orders'] ?> order<?= $stats['orders'] === 1 ? '' : 's' ?> · ₹<?= number_format($totalPending, 2) ?> pending</small>
  </div>
  <div class="cd-progress-wrap">
    <div class="cd-progress-info">
      <span><?= number_format($paymentPercent, 1) ?>% collected</span>
      <span>₹<?= number_format($customerPayments, 2) ?> / ₹<?= number_format($stats['total_invoiced'], 2) ?></span>
    </div>
    <div class="cd-progress-track">
      <div class="cd-progress-fill <?= $paymentPercent <= 0 ? 'low' : ($paymentPercent < 50 ? 'mid' : '') ?>" style="width:<?= $paymentPercent ?>%"></div>
    </div>
  </div>
  <?php if ($overdueCount > 0): ?>
    <div class="cd-overdue-badge">
      <div class="num"><?= $overdueCount ?></div>
      <div class="lbl">Overdue</div>
    </div>
  <?php endif; ?>
</div>

<!-- Tabs -->
<div class="cd-tabs-bar">
  <a href="?id=<?= $id ?>&tab=invoices" class="cd-tab-link <?= $tab === 'invoices' ? 'active' : '' ?>">
    <?= icon('invoices', 14) ?> Invoices <span class="cd-tab-badge"><?= count($stats['invoices']) ?></span>
  </a>
  <?php if ($quotationCount > 0): ?>
  <a href="?id=<?= $id ?>&tab=quotations" class="cd-tab-link <?= $tab === 'quotations' ? 'active' : '' ?>">
    <?= icon('quotations', 14) ?> Quotations <span class="cd-tab-badge"><?= $quotationCount ?></span>
  </a>
  <?php endif; ?>
  <?php if (count($payments) > 0): ?>
  <a href="?id=<?= $id ?>&tab=payments" class="cd-tab-link <?= $tab === 'payments' ? 'active' : '' ?>">
    <?= icon('payments', 14) ?> Payments <span class="cd-tab-badge"><?= count($payments) ?></span>
  </a>
  <?php endif; ?>
</div>

<!-- Panel -->
<div class="cd-panel">

<?php if ($tab === 'invoices'): ?>
  <div class="cd-records">
    <?php if (empty($stats['invoices'])): ?>
      <div class="cd-empty">
        <div class="cd-empty-ic"><?= icon('invoices', 36) ?></div>
        <div class="cd-empty-msg">No invoices yet for this customer.</div>
      </div>
    <?php else: ?>
      <?php foreach ($stats['invoices'] as $inv):
        $invPaid = 0.0;
        foreach ($payments as $p) if (($p['invoice_no'] ?? '') === ($inv['invoice_no'] ?? '')) $invPaid += (float)($p['amount'] ?? 0);
        $invPending = max(0, (float)$inv['amount'] - $invPaid);
        $isOverdue = ($inv['status'] ?? '') !== 'Paid' && !empty($inv['due_date']) && $inv['due_date'] < date('Y-m-d');
        $invPercent = (float)$inv['amount'] > 0 ? min(100, ($invPaid / (float)$inv['amount']) * 100) : 0;
        $statusClass = badge_class($inv['status'] ?? '');
      ?>
        <div class="cd-record <?= $isOverdue ? 'overdue' : '' ?>">
          <div class="cd-record-icon" style="background:<?= $isOverdue ? '#fef2f2' : '#f0fdf4' ?>;color:<?= $isOverdue ? '#dc2626' : '#16a34a' ?>;">
            <?= icon('invoices', 18) ?>
          </div>
          <div class="cd-record-body">
            <div class="cd-record-title">
              <?= htmlspecialchars($inv['invoice_no']) ?>
              <span class="badge <?= $statusClass ?>" style="font-size:10.5px;padding:2px 8px;"><?= htmlspecialchars($inv['status']) ?></span>
              <?php if ($isOverdue): ?><span style="font-size:10.5px;color:#dc2626;font-weight:700;">⚠ Overdue</span><?php endif; ?>
            </div>
            <div class="cd-record-sub">
              <?php if (!empty($inv['due_date'])): ?><span>Due: <?= htmlspecialchars(format_display_date($inv['due_date'])) ?></span><?php endif; ?>
              <?php if ($invPaid > 0 && $invPending > 0): ?><span style="color:#16a34a;">Paid ₹<?= number_format($invPaid, 0) ?></span><?php endif; ?>
            </div>
          </div>
          <div class="cd-record-right">
            <div class="cd-record-amount" style="color:<?= $invPending > 0 ? '#d97706' : '#16a34a' ?>;">₹<?= number_format((float)$inv['amount'], 2) ?></div>
            <div class="cd-record-actions">
              <a class="btn btn-outline btn-sm" href="invoice_print.php?id=<?= $inv['id'] ?>" target="_blank" style="padding:4px 10px;font-size:12px;"><?= icon('download', 12) ?> PDF</a>
              <?php if ($invPending > 0 && is_admin()): ?>
                <a class="btn btn-outline btn-sm" href="module.php?m=payments&action=new&invoice_no=<?= urlencode($inv['invoice_no'] ?? '') ?>&customer=<?= urlencode($customer['name']) ?>" style="padding:4px 10px;font-size:12px;color:#2f7d32;border-color:#2f7d32;"><?= icon('rupee', 12) ?> Pay</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

<?php elseif ($tab === 'quotations'): ?>
  <div class="cd-records">
    <?php foreach ($quotations as $quo):
      $status = quotation_effective_status($quo);
    ?>
      <div class="cd-record">
        <div class="cd-record-icon" style="background:#eff6ff;color:#2563eb;"><?= icon('quotations', 18) ?></div>
        <div class="cd-record-body">
          <div class="cd-record-title">
            <?= htmlspecialchars($quo['quotation_no'] ?? '-') ?>
            <span class="badge <?= badge_class($status) ?>" style="font-size:10.5px;padding:2px 8px;"><?= htmlspecialchars($status) ?></span>
          </div>
          <div class="cd-record-sub">
            <?php if (!empty($quo['valid_until'])): ?><span>Valid: <?= htmlspecialchars(format_display_date($quo['valid_until'])) ?></span><?php endif; ?>
          </div>
        </div>
        <div class="cd-record-right">
          <div class="cd-record-amount">₹<?= number_format((float)($quo['amount'] ?? 0), 2) ?></div>
          <div class="cd-record-actions">
            <a class="btn btn-outline btn-sm" href="quotation_print.php?id=<?= $quo['id'] ?>" target="_blank" style="padding:4px 10px;font-size:12px;"><?= icon('download', 12) ?> PDF</a>
            <?php if ($status !== 'Converted' && is_admin()): ?>
              <a class="btn btn-outline btn-sm" href="module.php?m=invoices&action=new&copy_from=quotations&id=<?= $quo['id'] ?>" style="padding:4px 10px;font-size:12px;color:#2563eb;border-color:#2563eb;"><?= icon('add', 12) ?> Convert</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

<?php elseif ($tab === 'payments'): ?>
  <div class="cd-records">
    <?php foreach ($payments as $payment): ?>
      <div class="cd-record">
        <div class="cd-record-icon" style="background:#f0fdf4;color:#16a34a;"><?= icon('payments', 18) ?></div>
        <div class="cd-record-body">
          <div class="cd-record-title" style="color:#16a34a;">
            + ₹<?= number_format((float)($payment['amount'] ?? 0), 2) ?>
            <span class="badge <?= ($payment['mode'] ?? '') === 'Cash' ? 'green' : 'blue' ?>" style="font-size:10.5px;padding:2px 8px;"><?= htmlspecialchars($payment['mode'] ?? '-') ?></span>
          </div>
          <div class="cd-record-sub">
            <?php if (!empty($payment['date'])): ?><span><?= icon('calendar', 12) ?> <?= htmlspecialchars(format_display_date($payment['date'])) ?></span><?php endif; ?>
            <?php if (!empty($payment['invoice_no'])): ?><span><?= icon('invoices', 12) ?> <?= htmlspecialchars($payment['invoice_no']) ?></span><?php endif; ?>
            <?php if (!empty($payment['reference'])): ?><span><?= htmlspecialchars($payment['reference']) ?></span><?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

</div><!-- /cd-panel -->
</div><!-- /cd-page -->

<?php require __DIR__ . '/layout_bottom.php'; ?>
