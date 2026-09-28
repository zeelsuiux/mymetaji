<?php
$activeModule = 'report_revenue';
$pageTitle = 'Revenue Trend & Forecast';
require_once __DIR__ . '/layout_top.php';

$invoices = db_get_all('invoices');
$payments = db_get_all('payments');
$expenses = db_get_all('expenses');

$months = [];
for ($i = 5; $i >= 0; $i--) {
    $m = date('Y-m', strtotime("-{$i} months"));
    $label = date('M Y', strtotime("{$m}-01"));
    $months[$m] = ['label' => $label, 'invoiced' => 0, 'collected' => 0, 'expenses' => 0];
}

foreach ($invoices as $inv) {
    $m = substr($inv['created_at'] ?? '', 0, 7);
    if (isset($months[$m])) $months[$m]['invoiced'] += (float)($inv['amount'] ?? $inv['grand_total'] ?? 0);
}
foreach ($payments as $p) {
    $m = substr($p['date'] ?? $p['created_at'] ?? '', 0, 7);
    if (isset($months[$m])) $months[$m]['collected'] += (float)($p['amount'] ?? 0);
}
foreach ($expenses as $e) {
    $m = substr($e['date'] ?? $e['created_at'] ?? '', 0, 7);
    if (isset($months[$m])) $months[$m]['expenses'] += (float)($e['amount'] ?? 0);
}

$maxVal = max(array_map(fn($m) => max($m['invoiced'], $m['collected'], $m['expenses']), $months));
if ($maxVal == 0) $maxVal = 1;

$profitData = array_map(fn($m) => $m['collected'] - $m['expenses'], $months);
$avgMonthly = count($months) > 0 ? array_sum(array_column($months, 'collected')) / count($months) : 0;
$forecast = $avgMonthly * 1.1;
$growth = [];
$vals = array_values(array_column($months, 'collected'));
for ($i = 1; $i < count($vals); $i++) {
    $growth[] = $vals[$i-1] > 0 ? round((($vals[$i] - $vals[$i-1]) / $vals[$i-1]) * 100, 1) : 0;
}
$avgGrowth = !empty($growth) ? array_sum($growth) / count($growth) : 0;
?>

<style>
.kpi-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-bottom:24px}
.kpi{background:#fff;border:1.5px solid #E2E6EF;border-radius:12px;padding:16px}
.kpi label{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase}
.kpi .v{font-size:22px;font-weight:800;margin-top:2px}
.kpi .v.g{color:#16a34a}.kpi .v.b{color:#2563EB}.kpi .v.o{color:#d97706}
.chart-container{background:#fff;border:1.5px solid #E2E6EF;border-radius:14px;padding:24px;margin-bottom:24px}
.chart-container h3{font-size:16px;font-weight:700;margin:0 0 20px}
.chart-bars{display:flex;align-items:flex-end;gap:12px;height:220px;padding:0 0 30px;position:relative}
.chart-bars::before{content:'';position:absolute;bottom:30px;left:0;right:0;height:1px;background:#E2E6EF}
.chart-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;position:relative}
.chart-col .bars{display:flex;gap:3px;align-items:flex-end;width:100%}
.chart-col .bar{flex:1;border-radius:4px 4px 0 0;transition:height .5s;min-width:0}
.chart-col .bar.inv{background:linear-gradient(180deg,#2563EB,#3B82F6)}
.chart-col .bar.col{background:linear-gradient(180deg,#16a34a,#22c55e)}
.chart-col .bar.exp{background:linear-gradient(180deg,#dc2626,#ef4444)}
.chart-col .bar.profit{background:linear-gradient(180deg,#8b5cf6,#a78bfa)}
.chart-label{font-size:11px;color:#64748b;font-weight:600;margin-top:8px;text-align:center}
.chart-val{font-size:9px;color:#475569;text-align:center;margin-bottom:2px}
.legend{display:flex;gap:16px;justify-content:center;margin-top:16px}
.legend span{display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#475569}
.legend span i{width:12px;height:12px;border-radius:3px;display:inline-block}
.forecast-card{background:linear-gradient(135deg,#eff6ff,#e0ecff);border:2px solid #BFDBFE;border-radius:14px;padding:24px;margin-bottom:24px}
.forecast-card h3{font-size:16px;font-weight:700;margin:0 0 8px;color:#1e40af}
.forecast-card .big{font-size:32px;font-weight:900;color:#2563EB}
.forecast-card p{font-size:14px;color:#475569;margin:4px 0 0}
table.rpt-tbl{width:100%;border-collapse:collapse;font-size:13px}
.rpt-tbl th{text-align:left;padding:10px 12px;background:#F4F6FA;font-weight:700;color:#475569;border-bottom:2px solid #E2E6EF;font-size:12px;text-transform:uppercase}
.rpt-tbl td{padding:10px 12px;border-bottom:1px solid #F1F5F9}
.rpt-tbl tr:hover td{background:#F8FAFC}
</style>

<div class="kpi-row">
    <div class="kpi"><label>Avg Monthly Revenue</label><div class="v b">₹<?= number_format($avgMonthly) ?></div></div>
    <div class="kpi"><label>Forecast (Next Month)</label><div class="v g">₹<?= number_format($forecast) ?></div></div>
    <div class="kpi"><label>Avg Growth Rate</label><div class="v o"><?= $avgGrowth ?>%</div></div>
    <?php $totalInvoiced = array_sum(array_column($months, 'invoiced')); $totalCollected = array_sum(array_column($months, 'collected')); $totalExpenses = array_sum(array_column($months, 'expenses')); ?>
    <div class="kpi"><label>6M Revenue</label><div class="v b">₹<?= number_format($totalInvoiced) ?></div></div>
    <div class="kpi"><label>6M Collected</label><div class="v g">₹<?= number_format($totalCollected) ?></div></div>
    <div class="kpi"><label>6M Expenses</label><div class="v" style="color:#dc2626">₹<?= number_format($totalExpenses) ?></div></div>
</div>

<div class="forecast-card">
    <h3>Forecast for Next Month</h3>
    <div class="big">₹<?= number_format($forecast) ?></div>
    <p>Based on average monthly collection of ₹<?= number_format($avgMonthly) ?> with <?= $avgGrowth ?>% growth trend</p>
</div>

<div class="chart-container">
    <h3>Revenue Trend (Last 6 Months)</h3>
    <div class="chart-bars" style="display:flex;align-items:flex-end;gap:8px;height:200px;padding:0 8px">
        <?php foreach ($months as $m => $d): $h = $maxVal > 0 ? ($d['invoiced'] / $maxVal) * 100 : 0; ?>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%">
                <div style="font-size:10px;color:#475569;margin-bottom:4px">₹<?= number_format($d['invoiced']/1000) ?>k</div>
                <div style="width:100%;max-width:48px;height:<?= $h ?>%;background:linear-gradient(180deg,#2563EB,#3B82F6);border-radius:4px 4px 0 0;transition:height .5s"></div>
                <div style="font-size:11px;color:#64748b;font-weight:600;margin-top:6px"><?= date('M', strtotime($m.'-01')) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="legend">
        <span><i style="background:#2563EB"></i>Invoiced</span>
        <span><i style="background:#16a34a"></i>Collected</span>
        <span><i style="background:#dc2626"></i>Expenses</span>
    </div>
</div>

<h3 style="font-size:16px;font-weight:700;margin:0 0 14px">Monthly Breakdown</h3>
<table class="rpt-tbl">
    <thead><tr><th>Month</th><th style="text-align:right">Invoiced</th><th style="text-align:right">Collected</th><th style="text-align:right">Expenses</th><th style="text-align:right">Profit/Loss</th><th>Growth</th></tr></thead>
    <tbody>
    <?php $prevCol = 0; foreach ($months as $m => $d): $profit = $d['collected'] - $d['expenses']; $g = $prevCol > 0 ? round((($d['collected'] - $prevCol) / $prevCol) * 100, 1) : 0; ?>
        <tr>
            <td><strong><?= $d['label'] ?></strong></td>
            <td style="text-align:right">₹<?= number_format($d['invoiced']) ?></td>
            <td style="text-align:right;color:#16a34a">₹<?= number_format($d['collected']) ?></td>
            <td style="text-align:right;color:#dc2626">₹<?= number_format($d['expenses']) ?></td>
            <td style="text-align:right;font-weight:700;color:<?= $profit >= 0 ? '#16a34a' : '#dc2626' ?>">₹<?= number_format($profit) ?></td>
            <td><?= $prevCol > 0 ? ($g >= 0 ? '<span style="color:#16a34a">+'.$g.'%</span>' : '<span style="color:#dc2626">'.$g.'%</span>') : '-' ?></td>
        </tr>
    <?php $prevCol = $d['collected']; endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
