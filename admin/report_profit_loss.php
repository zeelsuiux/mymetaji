<?php
$activeModule = 'report_profit_loss';
$pageTitle = 'Profit & Loss Report';
require_once __DIR__ . '/layout_top.php';

$invoices = db_get_all('invoices');
$payments = db_get_all('payments');
$expenses = db_get_all('expenses');
$products = db_get_all('products');

$period = $_GET['period'] ?? 'this_month';

$filtered = array_filter($invoices, function($i) use ($period) {
    $d = substr($i['created_at'] ?? '', 0, 10);
    if ($period === 'today') return $d === date('Y-m-d');
    if ($period === 'this_week') return $d >= date('Y-m-d', strtotime('monday this week')) && $d <= date('Y-m-d');
    if ($period === 'this_month') return substr($d, 0, 7) === date('Y-m');
    if ($period === 'last_month') return substr($d, 0, 7) === date('Y-m', strtotime('-1 month'));
    if ($period === 'this_year') return substr($d, 0, 4) === date('Y');
    return true;
});
$filtered = array_values($filtered);

$totalRevenue = 0;
$totalCOGS = 0;
$totalGST = 0;
$profitByCustomer = [];

foreach ($filtered as $inv) {
    $totalGST += (float)($inv['total_gst'] ?? 0);
    $items = is_array($inv['items'] ?? null) ? $inv['items'] : [];
    foreach ($items as $item) {
        $selling = (float)($item['actual_selling_price'] ?? $item['price'] ?? 0);
        $purchase = (float)($item['purchase_price'] ?? 0);
        $qty = (float)($item['qty'] ?? 1);
        $disc = (float)($item['discount_rate'] ?? 0) / 100;
        $netSelling = $selling * $qty * (1 - $disc);
        $cost = $purchase * $qty;
        $totalRevenue += $netSelling;
        $totalCOGS += $cost;
    }
    $cust = $inv['customer'] ?? 'Unknown';
    if (!isset($profitByCustomer[$cust])) $profitByCustomer[$cust] = ['revenue' => 0, 'cost' => 0];
    foreach ($items as $item) {
        $selling = (float)($item['actual_selling_price'] ?? $item['price'] ?? 0);
        $purchase = (float)($item['purchase_price'] ?? 0);
        $qty = (float)($item['qty'] ?? 1);
        $disc = (float)($item['discount_rate'] ?? 0) / 100;
        $profitByCustomer[$cust]['revenue'] += $selling * $qty * (1 - $disc);
        $profitByCustomer[$cust]['cost'] += $purchase * $qty;
    }
}
arsort(array_column($profitByCustomer, 'revenue'));

$filteredExpenses = array_filter($expenses, function($e) use ($period) {
    $d = $e['date'] ?? substr($e['created_at'] ?? '', 0, 10);
    if ($period === 'today') return $d === date('Y-m-d');
    if ($period === 'this_week') return $d >= date('Y-m-d', strtotime('monday this week')) && $d <= date('Y-m-d');
    if ($period === 'this_month') return substr($d, 0, 7) === date('Y-m');
    if ($period === 'last_month') return substr($d, 0, 7) === date('Y-m', strtotime('-1 month'));
    if ($period === 'this_year') return substr($d, 0, 4) === date('Y');
    return true;
});
$totalExpenses = array_sum(array_map(fn($e) => (float)($e['amount'] ?? 0), $filteredExpenses));

$grossProfit = $totalRevenue - $totalCOGS;
$netProfit = $grossProfit - $totalExpenses;
$margin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0;
?>

<style>
.rpt-filters{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px}
.rpt-filters a{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;color:#475569;background:#F4F6FA;border:1.5px solid #E2E6EF;transition:all .2s}
.rpt-filters a.active,.rpt-filters a:hover{background:#2563EB;color:#fff;border-color:#2563EB}
.pl-summary{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px}
.pl-card{background:#fff;border:1.5px solid #E2E6EF;border-radius:14px;padding:24px}
.pl-card h3{font-size:14px;font-weight:700;color:#475569;margin:0 0 16px;text-transform:uppercase;letter-spacing:.5px}
.pl-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #F1F5F9;font-size:14px}
.pl-row:last-child{border-bottom:none}
.pl-row.total{font-weight:800;font-size:16px;border-top:2px solid #E2E6EF;margin-top:8px;padding-top:12px}
.pl-row .green{color:#16a34a}.pl-row .red{color:#dc2626}.pl-row .blue{color:#2563EB}
.kpi-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
.kpi{background:#fff;border:1.5px solid #E2E6EF;border-radius:12px;padding:16px;text-align:center}
.kpi label{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase}
.kpi .v{font-size:26px;font-weight:800;margin-top:2px}
.kpi .v.green{color:#16a34a}.kpi .v.red{color:#dc2626}.kpi .v.blue{color:#2563EB}.kpi .v.gray{color:#64748b}
table.rpt-tbl{width:100%;border-collapse:collapse;font-size:13px}
.rpt-tbl th{text-align:left;padding:10px 12px;background:#F4F6FA;font-weight:700;color:#475569;border-bottom:2px solid #E2E6EF;font-size:12px;text-transform:uppercase}
.rpt-tbl td{padding:10px 12px;border-bottom:1px solid #F1F5F9}
.rpt-tbl tr:hover td{background:#F8FAFC}
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
    <div class="kpi"><label>Revenue</label><div class="v blue">₹<?= number_format($totalRevenue) ?></div></div>
    <div class="kpi"><label>COGS</label><div class="v gray">₹<?= number_format($totalCOGS) ?></div></div>
    <div class="kpi"><label>Gross Profit</label><div class="v <?= $grossProfit >= 0 ? 'green' : 'red' ?>">₹<?= number_format($grossProfit) ?></div></div>
    <div class="kpi"><label>Net Profit</label><div class="v <?= $netProfit >= 0 ? 'green' : 'red' ?>">₹<?= number_format($netProfit) ?></div></div>
</div>

<div class="pl-summary">
    <div class="pl-card">
        <h3>Revenue</h3>
        <div class="pl-row"><span>Sales Revenue</span><span class="blue">₹<?= number_format($totalRevenue) ?></span></div>
        <div class="pl-row total"><span>Gross Revenue</span><span class="blue">₹<?= number_format($totalRevenue) ?></span></div>
    </div>
    <div class="pl-card">
        <h3>Costs</h3>
        <div class="pl-row"><span>Cost of Goods Sold</span><span>₹<?= number_format($totalCOGS) ?></span></div>
        <div class="pl-row"><span>Operating Expenses</span><span>₹<?= number_format($totalExpenses) ?></span></div>
        <div class="pl-row total"><span>Total Costs</span><span class="red">₹<?= number_format($totalCOGS + $totalExpenses) ?></span></div>
    </div>
</div>

<div style="background:#fff;border:1.5px solid #E2E6EF;border-radius:14px;padding:24px;margin-bottom:24px">
    <div style="display:flex;justify-content:space-between;align-items:center">
        <div><span style="font-size:14px;color:#64748b">Net Profit Margin</span><br><strong style="font-size:28px;font-weight:800;color:<?= $margin >= 0 ? '#16a34a' : '#dc2626' ?>"><?= $margin ?>%</strong></div>
        <div><span style="font-size:14px;color:#64748b">GST Collected</span><br><strong style="font-size:28px;font-weight:800;color:#2563EB">₹<?= number_format($totalGST) ?></strong></div>
    </div>
</div>

<h3 style="font-size:16px;font-weight:700;margin:0 0 14px">Customer-wise Profit</h3>
<table class="rpt-tbl">
    <thead><tr><th>Customer</th><th style="text-align:right">Revenue</th><th style="text-align:right">Cost</th><th style="text-align:right">Profit</th></tr></thead>
    <tbody>
    <?php foreach ($profitByCustomer as $cust => $data): $p = $data['revenue'] - $data['cost']; ?>
        <tr>
            <td><strong><?= htmlspecialchars($cust) ?></strong></td>
            <td style="text-align:right">₹<?= number_format($data['revenue']) ?></td>
            <td style="text-align:right">₹<?= number_format($data['cost']) ?></td>
            <td style="text-align:right;font-weight:700;color:<?= $p >= 0 ? '#16a34a' : '#dc2626' ?>">₹<?= number_format($p) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
