<?php
$activeModule = 'report_product_sales';
$pageTitle = 'Product-wise Sales Report';
require_once __DIR__ . '/layout_top.php';

$invoices = db_get_all('invoices');
$products = db_get_all('products');
$period = $_GET['period'] ?? 'this_month';

$filtered = array_filter($invoices, function($i) use ($period) {
    $d = substr($i['created_at'] ?? '', 0, 10);
    if ($period === 'today') return $d === date('Y-m-d');
    if ($period === 'this_month') return substr($d, 0, 7) === date('Y-m');
    if ($period === 'this_year') return substr($d, 0, 4) === date('Y');
    return true;
});

$productSales = [];
foreach ($filtered as $inv) {
    $items = is_array($inv['items'] ?? null) ? $inv['items'] : [];
    foreach ($items as $item) {
        $pid = $item['product_id'] ?? $item['description'] ?? 'Unknown';
        $name = $item['description'] ?? 'Product #' . $pid;
        $qty = (float)($item['qty'] ?? 1);
        $price = (float)($item['actual_selling_price'] ?? $item['price'] ?? 0);
        $disc = (float)($item['discount_rate'] ?? 0) / 100;
        $total = $price * $qty * (1 - $disc);
        if (!isset($productSales[$name])) $productSales[$name] = ['qty' => 0, 'revenue' => 0, 'count' => 0];
        $productSales[$name]['qty'] += $qty;
        $productSales[$name]['revenue'] += $total;
        $productSales[$name]['count']++;
    }
}
uasort($productSales, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

$totalRevenue = array_sum(array_column($productSales, 'revenue'));
$totalQty = array_sum(array_column($productSales, 'qty'));
$maxRevenue = !empty($productSales) ? max(array_column($productSales, 'revenue')) : 0;
?>

<style>
.rpt-filters{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px}
.rpt-filters a{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;color:#475569;background:#F4F6FA;border:1.5px solid #E2E6EF;transition:all .2s}
.rpt-filters a.active,.rpt-filters a:hover{background:#2563EB;color:#fff;border-color:#2563EB}
.kpi-row{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px}
.kpi{background:#fff;border:1.5px solid #E2E6EF;border-radius:12px;padding:16px;text-align:center}
.kpi label{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase}
.kpi .v{font-size:24px;font-weight:800;color:#0f172a;margin-top:2px}
.prod-bar{display:flex;align-items:center;gap:12px;margin-bottom:8px}
.prod-bar .name{width:180px;font-size:13px;font-weight:600;color:#334155;text-align:right;flex-shrink:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.prod-bar .bar{flex:1;height:30px;background:#F4F6FA;border-radius:6px;overflow:hidden}
.prod-bar .fill{height:100%;background:linear-gradient(90deg,#2563EB,#3B82F6);border-radius:6px;display:flex;align-items:center;padding-left:10px;font-size:11px;color:#fff;font-weight:700;min-width:30px}
.prod-bar .info{width:140px;font-size:12px;color:#475569;flex-shrink:0}
.prod-bar .info strong{color:#0f172a;display:block;font-size:13px}
</style>

<div class="rpt-filters">
    <a href="?period=today" class="<?= $period==='today'?'active':'' ?>">Today</a>
    <a href="?period=this_month" class="<?= $period==='this_month'?'active':'' ?>">This Month</a>
    <a href="?period=this_year" class="<?= $period==='this_year'?'active':'' ?>">This Year</a>
    <a href="?period=all" class="<?= $period==='all'?'active':'' ?>">All Time</a>
</div>

<div class="kpi-row">
    <div class="kpi"><label>Total Products Sold</label><div class="v"><?= number_format($totalQty) ?></div></div>
    <div class="kpi"><label>Unique Products</label><div class="v"><?= count($productSales) ?></div></div>
    <div class="kpi"><label>Total Revenue</label><div class="v" style="color:#2563EB">₹<?= number_format($totalRevenue) ?></div></div>
</div>

<h3 style="font-size:16px;font-weight:700;margin:0 0 16px">Top Selling Products</h3>
<div style="background:#fff;border:1.5px solid #E2E6EF;border-radius:14px;padding:24px;margin-bottom:24px">
<?php if (empty($productSales)): ?>
    <p style="text-align:center;color:#64748b;padding:20px">No product sales data found.</p>
<?php else: $rank = 0; foreach (array_slice($productSales, 0, 20, true) as $name => $d): $rank++; ?>
    <div class="prod-bar">
        <div class="name" title="<?= htmlspecialchars($name) ?>"><?= $rank ?>. <?= htmlspecialchars($name) ?></div>
        <div class="bar"><div class="fill" style="width:<?= $maxRevenue > 0 ? round(($d['revenue']/$maxRevenue)*100) : 0 ?>%"><?= $d['qty'] ?> units</div></div>
        <div class="info"><strong>₹<?= number_format($d['revenue']) ?></strong><?= $d['count'] ?> orders</div>
    </div>
<?php endforeach; endif; ?>
</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
