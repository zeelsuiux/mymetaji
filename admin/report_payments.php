<?php
$activeModule = 'report_payments';
$pageTitle = 'Payment Report';
require_once __DIR__ . '/layout_top.php';

$payments = db_get_all('payments');
$invoices = db_get_all('invoices');
$period = $_GET['period'] ?? 'this_month';

$filtered = array_filter($payments, function($p) use ($period) {
    $d = $p['date'] ?? substr($p['created_at'] ?? '', 0, 10);
    if ($period === 'today') return $d === date('Y-m-d');
    if ($period === 'this_week') return $d >= date('Y-m-d', strtotime('monday this week')) && $d <= date('Y-m-d');
    if ($period === 'this_month') return substr($d, 0, 7) === date('Y-m');
    if ($period === 'last_month') return substr($d, 0, 7) === date('Y-m', strtotime('-1 month'));
    if ($period === 'this_year') return substr($d, 0, 4) === date('Y');
    return true;
});
$filtered = array_values($filtered);

$totalCollected = array_sum(array_map(fn($p) => (float)($p['amount'] ?? 0), $filtered));
$byMode = [];
$byCustomer = [];
foreach ($filtered as $p) {
    $mode = $p['mode'] ?? 'Unknown';
    $byMode[$mode] = ($byMode[$mode] ?? 0) + (float)($p['amount'] ?? 0);
    $cust = $p['customer'] ?? 'Unknown';
    $byCustomer[$cust] = ($byCustomer[$cust] ?? 0) + (float)($p['amount'] ?? 0);
}
arsort($byCustomer);

$totalInvoiced = array_sum(array_map(fn($i) => (float)($i['amount'] ?? 0), $invoices));
$totalAllPayments = array_sum(array_map(fn($p) => (float)($p['amount'] ?? 0), $payments));
$totalPending = $totalInvoiced - $totalAllPayments;
?>

<style>
.rpt-filters{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px}
.rpt-filters a{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;color:#475569;background:#F4F6FA;border:1.5px solid #E2E6EF;transition:all .2s}
.rpt-filters a.active,.rpt-filters a:hover{background:#2563EB;color:#fff;border-color:#2563EB}
.kpi-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin-bottom:24px}
.kpi{background:#fff;border:1.5px solid #E2E6EF;border-radius:12px;padding:16px}
.kpi label{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase}
.kpi .v{font-size:22px;font-weight:800;margin-top:2px}
.kpi .v.g{color:#16a34a}.kpi .v.r{color:#dc2626}.kpi .v.b{color:#2563EB}
.mode-cards{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px}
.mode-card{background:#fff;border:1.5px solid #E2E6EF;border-radius:14px;padding:24px;text-align:center}
.mode-card h4{font-size:13px;color:#64748b;margin:0 0 8px;text-transform:uppercase}
.mode-card .val{font-size:28px;font-weight:800}
.mode-card .val.cash{color:#16a34a}.mode-card .val.online{color:#2563EB}
.mode-card .pct{font-size:14px;color:#64748b;margin-top:4px}
table.rpt-tbl{width:100%;border-collapse:collapse;font-size:13px}
.rpt-tbl th{text-align:left;padding:10px 12px;background:#F4F6FA;font-weight:700;color:#475569;border-bottom:2px solid #E2E6EF;font-size:12px;text-transform:uppercase}
.rpt-tbl td{padding:10px 12px;border-bottom:1px solid #F1F5F9}
.rpt-tbl tr:hover td{background:#F8FAFC}
.mode-pill{display:inline-block;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700}
.mode-pill.cash{background:#dcfce7;color:#166534}.mode-pill.online{background:#eff6ff;color:#1e40af}
</style>

<div class="rpt-filters">
    <a href="?period=today" class="<?= $period==='today'?'active':'' ?>">Today</a>
    <a href="?period=this_week" class="<?= $period==='this_week'?'active':'' ?>">This Week</a>
    <a href="?period=this_month" class="<?= $period==='this_month'?'active':'' ?>">This Month</a>
    <a href="?period=last_month" class="<?= $period==='last_month'?'active':'' ?>">Last Month</a>
    <a href="?period=this_year" class="<?= $period==='this_year'?'active':'' ?>">This Year</a>
    <a href="?period=all" class="<?= $period==='all'?'active':'' ?>">All Time</a>
</div>

<div class="kpi-row">
    <div class="kpi"><label>Total Collected</label><div class="v g">₹<?= number_format($totalCollected) ?></div></div>
    <div class="kpi"><label>Transactions</label><div class="v"><?= count($filtered) ?></div></div>
    <div class="kpi"><label>Total Outstanding</label><div class="v r">₹<?= number_format($totalPending) ?></div></div>
</div>

<div class="mode-cards">
<?php foreach ($byMode as $mode => $amt): $pct = $totalCollected > 0 ? round(($amt/$totalCollected)*100) : 0; ?>
    <div class="mode-card">
        <h4><?= $mode ?> Payments</h4>
        <div class="val <?= strtolower($mode) === 'cash' ? 'cash' : 'online' ?>">₹<?= number_format($amt) ?></div>
        <div class="pct"><?= $pct ?>% of total</div>
    </div>
<?php endforeach; ?>
</div>

<h3 style="font-size:16px;font-weight:700;margin:0 0 14px">Customer-wise Collection</h3>
<table class="rpt-tbl">
    <thead><tr><th>Customer</th><th style="text-align:right">Amount Received</th></tr></thead>
    <tbody>
    <?php foreach ($byCustomer as $cust => $amt): ?>
        <tr>
            <td><strong><?= htmlspecialchars($cust) ?></strong></td>
            <td style="text-align:right;font-weight:700;color:#16a34a">₹<?= number_format($amt) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h3 style="font-size:16px;font-weight:700;margin:24px 0 14px">All Payments</h3>
<table class="rpt-tbl">
    <thead><tr><th>Date</th><th>Invoice</th><th>Customer</th><th>Mode</th><th>Reference</th><th style="text-align:right">Amount</th></tr></thead>
    <tbody>
    <?php if (empty($filtered)): ?>
        <tr><td colspan="6" style="text-align:center;padding:32px;color:#64748b">No payments found.</td></tr>
    <?php else: foreach ($filtered as $p): ?>
        <tr>
            <td><?= date('d M Y', strtotime($p['date'] ?? $p['created_at'] ?? '')) ?></td>
            <td><?= htmlspecialchars($p['invoice_no'] ?? '-') ?></td>
            <td><?= htmlspecialchars($p['customer'] ?? '') ?></td>
            <td><span class="mode-pill <?= strtolower($p['mode'] ?? '') === 'cash' ? 'cash' : 'online' ?>"><?= htmlspecialchars($p['mode'] ?? '-') ?></span></td>
            <td><?= htmlspecialchars($p['reference'] ?? '-') ?></td>
            <td style="text-align:right;font-weight:700;color:#16a34a">₹<?= number_format((float)($p['amount'] ?? 0)) ?></td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
