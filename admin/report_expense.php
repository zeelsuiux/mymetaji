<?php
$activeModule = 'report_expense';
$pageTitle = 'Expense Report';
require_once __DIR__ . '/layout_top.php';

$expenses = db_get_all('expenses');
$period = $_GET['period'] ?? 'this_month';

$filtered = array_filter($expenses, function($e) use ($period) {
    $d = $e['date'] ?? substr($e['created_at'] ?? '', 0, 10);
    if ($period === 'today') return $d === date('Y-m-d');
    if ($period === 'this_week') return $d >= date('Y-m-d', strtotime('monday this week')) && $d <= date('Y-m-d');
    if ($period === 'this_month') return substr($d, 0, 7) === date('Y-m');
    if ($period === 'last_month') return substr($d, 0, 7) === date('Y-m', strtotime('-1 month'));
    if ($period === 'this_year') return substr($d, 0, 4) === date('Y');
    return true;
});
$filtered = array_values($filtered);

$totalExpenses = array_sum(array_map(fn($e) => (float)($e['amount'] ?? 0), $filtered));

$byCategory = [];
foreach ($filtered as $e) {
    $cat = $e['category'] ?? 'Other';
    $byCategory[$cat] = ($byCategory[$cat] ?? 0) + (float)($e['amount'] ?? 0);
}
arsort($byCategory);

$byMode = [];
foreach ($filtered as $e) {
    $mode = $e['mode'] ?? 'Unknown';
    $byMode[$mode] = ($byMode[$mode] ?? 0) + (float)($e['amount'] ?? 0);
}
?>

<style>
.rpt-filters{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px}
.rpt-filters a{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;color:#475569;background:#F4F6FA;border:1.5px solid #E2E6EF;transition:all .2s}
.rpt-filters a.active,.rpt-filters a:hover{background:#2563EB;color:#fff;border-color:#2563EB}
.kpi-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin-bottom:24px}
.kpi{background:#fff;border:1.5px solid #E2E6EF;border-radius:12px;padding:16px}
.kpi label{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase}
.kpi .v{font-size:24px;font-weight:800;color:#0f172a;margin-top:2px}
.kpi .v.r{color:#dc2626}.kpi .v.b{color:#2563EB}
.cat-bar{display:flex;align-items:center;gap:12px;margin-bottom:10px}
.cat-bar .label{width:120px;font-size:13px;font-weight:600;color:#334155;text-align:right;flex-shrink:0}
.cat-bar .bar{flex:1;height:28px;background:#F4F6FA;border-radius:6px;overflow:hidden;position:relative}
.cat-bar .fill{height:100%;background:linear-gradient(90deg,#2563EB,#3B82F6);border-radius:6px;display:flex;align-items:center;padding-left:10px;font-size:12px;color:#fff;font-weight:700;min-width:40px;transition:width .5s}
.cat-bar .amt{width:100px;font-size:13px;font-weight:700;color:#0f172a;text-align:right;flex-shrink:0}
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
    <div class="kpi"><label>Total Expenses</label><div class="v r">₹<?= number_format($totalExpenses) ?></div></div>
    <div class="kpi"><label>Transactions</label><div class="v"><?= count($filtered) ?></div></div>
    <?php foreach ($byMode as $mode => $amt): ?>
    <div class="kpi"><label><?= $mode ?></label><div class="v b">₹<?= number_format($amt) ?></div></div>
    <?php endforeach; ?>
</div>

<h3 style="font-size:16px;font-weight:700;margin:0 0 16px">Expenses by Category</h3>
<div style="background:#fff;border:1.5px solid #E2E6EF;border-radius:14px;padding:24px;margin-bottom:24px">
<?php if (empty($byCategory)): ?>
    <p style="text-align:center;color:#64748b;padding:20px">No expenses found.</p>
<?php else: $maxCat = max($byCategory); foreach ($byCategory as $cat => $amt): ?>
    <div class="cat-bar">
        <div class="label"><?= htmlspecialchars($cat) ?></div>
        <div class="bar"><div class="fill" style="width:<?= $maxCat > 0 ? round(($amt/$maxCat)*100) : 0 ?>%"><?= round(($amt/$totalExpenses)*100) ?>%</div></div>
        <div class="amt">₹<?= number_format($amt) ?></div>
    </div>
<?php endforeach; endif; ?>
</div>

<table class="rpt-tbl">
    <thead><tr><th>Date</th><th>Title</th><th>Category</th><th>Mode</th><th style="text-align:right">Amount</th></tr></thead>
    <tbody>
    <?php if (empty($filtered)): ?>
        <tr><td colspan="5" style="text-align:center;padding:32px;color:#64748b">No expenses found.</td></tr>
    <?php else: foreach ($filtered as $e): ?>
        <tr>
            <td><?= date('d M Y', strtotime($e['date'] ?? $e['created_at'] ?? '')) ?></td>
            <td><strong><?= htmlspecialchars($e['title'] ?? '') ?></strong></td>
            <td><?= htmlspecialchars($e['category'] ?? '-') ?></td>
            <td><span class="mode-pill <?= strtolower($e['mode'] ?? '') === 'cash' ? 'cash' : 'online' ?>"><?= htmlspecialchars($e['mode'] ?? '-') ?></span></td>
            <td style="text-align:right;font-weight:700;color:#dc2626">₹<?= number_format((float)($e['amount'] ?? 0)) ?></td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
