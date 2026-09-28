<?php
$activeModule = 'report_sales';
$pageTitle = 'Sales Report';
require_once __DIR__ . '/layout_top.php';

$period = $_GET['period'] ?? 'monthly';
$startDate = $_GET['start'] ?? date('Y-m-01');
$endDate = $_GET['end'] ?? date('Y-m-t');
$invoices = db_get_all('invoices');
$payments = db_get_all('payments');

function filterByPeriod($invoices, $period, $start, $end) {
    return array_filter($invoices, function($i) use ($period, $start, $end) {
        $d = substr($i['created_at'] ?? '', 0, 10);
        if ($period === 'today') return $d === date('Y-m-d');
        if ($period === 'yesterday') return $d === date('Y-m-d', strtotime('-1 day'));
        if ($period === 'this_week') return $d >= date('Y-m-d', strtotime('monday this week')) && $d <= date('Y-m-d');
        if ($period === 'this_month') return substr($d, 0, 7) === date('Y-m');
        if ($period === 'last_month') return substr($d, 0, 7) === date('Y-m', strtotime('-1 month'));
        if ($period === 'this_year') return substr($d, 0, 4) === date('Y');
        if ($period === 'custom') return $d >= $start && $d <= $end;
        return true;
    });
}

$filtered = filterByPeriod($invoices, $period, $startDate, $endDate);
$totalAmount = array_sum(array_map(fn($i) => (float)($i['amount'] ?? 0), $filtered));
$totalGST = array_sum(array_map(fn($i) => (float)($i['total_gst'] ?? 0), $filtered));
$totalInvoices = count($filtered);
$paidCount = count(array_filter($filtered, fn($i) => ($i['status'] ?? '') === 'Paid'));
$pendingCount = $totalInvoices - $paidCount;

$paidInvoices = array_filter($filtered, fn($i) => ($i['status'] ?? '') === 'Paid');
$collectedFromFiltered = 0;
foreach ($filtered as $inv) {
    $invNo = $inv['invoice_no'] ?? '';
    $collectedFromFiltered += array_sum(array_map(fn($p) => (float)($p['amount'] ?? 0), array_filter($payments, fn($p) => ($p['invoice_no'] ?? '') === $invNo)));
}
?>

<style>
.rpt-filters{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;align-items:center}
.rpt-filters a{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;color:#475569;background:#F4F6FA;border:1.5px solid #E2E6EF;transition:all .2s}
.rpt-filters a.active,.rpt-filters a:hover{background:#2563EB;color:#fff;border-color:#2563EB}
.rpt-filters input{padding:8px 12px;border:1.5px solid #E2E6EF;border-radius:8px;font-size:13px}
.kpi-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:24px}
.kpi{background:#fff;border:1.5px solid #E2E6EF;border-radius:12px;padding:14px}
.kpi label{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase}
.kpi .v{font-size:22px;font-weight:800;color:#0f172a;margin-top:2px}
.kpi .v.g{color:#16a34a}.kpi .v.r{color:#dc2626}.kpi .v.b{color:#2563EB}
table.rpt-tbl{width:100%;border-collapse:collapse;font-size:13px}
.rpt-tbl th{text-align:left;padding:10px 12px;background:#F4F6FA;font-weight:700;color:#475569;border-bottom:2px solid #E2E6EF;font-size:12px;text-transform:uppercase}
.rpt-tbl td{padding:10px 12px;border-bottom:1px solid #F1F5F9}
.rpt-tbl tr:hover td{background:#F8FAFC}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.badge.green{background:#dcfce7;color:#166534}.badge.red{background:#fef2f2;color:#991b1b}.badge.orange{background:#fff7ed;color:#9a3412}.badge.blue{background:#eff6ff;color:#1e40af}
</style>

<div class="rpt-filters">
    <a href="?period=today" class="<?= $period==='today'?'active':'' ?>">Today</a>
    <a href="?period=yesterday" class="<?= $period==='yesterday'?'active':'' ?>">Yesterday</a>
    <a href="?period=this_week" class="<?= $period==='this_week'?'active':'' ?>">This Week</a>
    <a href="?period=this_month" class="<?= $period==='this_month'?'active':'' ?>">This Month</a>
    <a href="?period=last_month" class="<?= $period==='last_month'?'active':'' ?>">Last Month</a>
    <a href="?period=this_year" class="<?= $period==='this_year'?'active':'' ?>">This Year</a>
    <a href="?period=custom" class="<?= $period==='custom'?'active':'' ?>">Custom</a>
    <?php if ($period === 'custom'): ?>
        <input type="date" value="<?= $startDate ?>" onchange="window.location='?period=custom&start='+this.value+'&end='+document.getElementById('endD').value">
        <span style="color:#64748b">to</span>
        <input type="date" id="endD" value="<?= $endDate ?>" onchange="window.location='?period=custom&start='+document.getElementById('startD').value+'&end='+this.value">
    <?php endif; ?>
</div>

<div class="kpi-row">
    <div class="kpi"><label>Total Invoices</label><div class="v"><?= $totalInvoices ?></div></div>
    <div class="kpi"><label>Sales Amount</label><div class="v b">₹<?= number_format($totalAmount) ?></div></div>
    <div class="kpi"><label>Collected</label><div class="v g">₹<?= number_format($collectedFromFiltered) ?></div></div>
    <div class="kpi"><label>Pending</label><div class="v r">₹<?= number_format($totalAmount - $collectedFromFiltered) ?></div></div>
    <div class="kpi"><label>Paid</label><div class="v g"><?= $paidCount ?></div></div>
    <div class="kpi"><label>Unpaid</label><div class="v r"><?= $pendingCount ?></div></div>
</div>

<table class="rpt-tbl">
    <thead><tr><th>Invoice No</th><th>Date</th><th>Customer</th><th>Amount</th><th>GST</th><th>Total</th><th>Status</th></tr></thead>
    <tbody>
    <?php if (empty($filtered)): ?>
        <tr><td colspan="7" style="text-align:center;padding:32px;color:#64748b">No invoices found for this period.</td></tr>
    <?php else: foreach ($filtered as $inv): ?>
        <tr>
            <td><strong><?= htmlspecialchars($inv['invoice_no'] ?? '') ?></strong></td>
            <td><?= date('d M Y', strtotime($inv['created_at'] ?? '')) ?></td>
            <td><?= htmlspecialchars($inv['customer'] ?? '') ?></td>
            <td>₹<?= number_format((float)($inv['amount'] ?? 0)) ?></td>
            <td>₹<?= number_format((float)($inv['total_gst'] ?? 0)) ?></td>
            <td><strong>₹<?= number_format((float)($inv['grand_total'] ?? $inv['amount'] ?? 0)) ?></strong></td>
            <td><span class="badge <?= strtolower($inv['status'] ?? '') === 'paid' ? 'green' : (strtolower($inv['status'] ?? '') === 'overdue' ? 'red' : 'orange') ?>"><?= htmlspecialchars($inv['status'] ?? 'Pending') ?></span></td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
