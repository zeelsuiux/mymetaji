<?php
$activeModule = 'report_state_gst';
$pageTitle = 'State-wise GST Summary';
require_once __DIR__ . '/layout_top.php';

$invoices = db_get_all('invoices');
$customers = db_get_all('customers');
$period = $_GET['period'] ?? 'this_month';

$filtered = array_filter($invoices, function($i) use ($period) {
    $d = substr($i['created_at'] ?? '', 0, 10);
    if ($period === 'today') return $d === date('Y-m-d');
    if ($period === 'this_month') return substr($d, 0, 7) === date('Y-m');
    if ($period === 'this_year') return substr($d, 0, 4) === date('Y');
    return true;
});

$customerStates = [];
foreach ($customers as $c) {
    $customerStates[strtolower(trim($c['name'] ?? ''))] = $c['state'] ?? '';
}

$stateData = [];
foreach ($filtered as $inv) {
    $place = $inv['place_of_supply'] ?? '';
    if (!$place) {
        $custKey = strtolower(trim($inv['customer'] ?? ''));
        $place = $customerStates[$custKey] ?? 'Unknown';
    }
    if (!isset($stateData[$place])) $stateData[$place] = ['count' => 0, 'taxable' => 0, 'cgst' => 0, 'sgst' => 0, 'igst' => 0, 'total' => 0];
    $stateData[$place]['count']++;
    $stateData[$place]['taxable'] += (float)($inv['total_taxable'] ?? $inv['amount'] ?? 0);
    $stateData[$place]['cgst'] += (float)($inv['total_cgst'] ?? 0);
    $stateData[$place]['sgst'] += (float)($inv['total_sgst'] ?? 0);
    $stateData[$place]['igst'] += (float)($inv['total_igst'] ?? 0);
    $stateData[$place]['total'] += (float)($inv['grand_total'] ?? $inv['amount'] ?? 0);
}
uasort($stateData, fn($a, $b) => $b['total'] <=> $a['total']);

$totalTaxable = array_sum(array_column($stateData, 'taxable'));
$totalCGST = array_sum(array_column($stateData, 'cgst'));
$totalSGST = array_sum(array_column($stateData, 'sgst'));
$totalIGST = array_sum(array_column($stateData, 'igst'));
$totalGrand = array_sum(array_column($stateData, 'total'));
?>

<style>
.rpt-filters{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px}
.rpt-filters a{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;color:#475569;background:#F4F6FA;border:1.5px solid #E2E6EF;transition:all .2s}
.rpt-filters a.active,.rpt-filters a:hover{background:#2563EB;color:#fff;border-color:#2563EB}
.kpi-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:24px}
.kpi{background:#fff;border:1.5px solid #E2E6EF;border-radius:12px;padding:14px}
.kpi label{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase}
.kpi .v{font-size:20px;font-weight:800;color:#0f172a;margin-top:2px}
table.rpt-tbl{width:100%;border-collapse:collapse;font-size:13px}
.rpt-tbl th{text-align:left;padding:10px 12px;background:#F4F6FA;font-weight:700;color:#475569;border-bottom:2px solid #E2E6EF;font-size:12px;text-transform:uppercase}
.rpt-tbl td{padding:10px 12px;border-bottom:1px solid #F1F5F9}
.rpt-tbl tr:hover td{background:#F8FAFC}
.rpt-tbl tfoot td{font-weight:800;background:#F4F6FA;border-top:2px solid #E2E6EF}
.intra-tag{display:inline-block;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;background:#dcfce7;color:#166534}
.inter-tag{display:inline-block;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;background:#eff6ff;color:#1e40af}
</style>

<div class="rpt-filters">
    <a href="?period=today" class="<?= $period==='today'?'active':'' ?>">Today</a>
    <a href="?period=this_month" class="<?= $period==='this_month'?'active':'' ?>">This Month</a>
    <a href="?period=this_year" class="<?= $period==='this_year'?'active':'' ?>">This Year</a>
    <a href="?period=all" class="<?= $period==='all'?'active':'' ?>">All Time</a>
</div>

<div class="kpi-row">
    <div class="kpi"><label>Total Taxable</label><div class="v">₹<?= number_format($totalTaxable) ?></div></div>
    <div class="kpi"><label>CGST</label><div class="v">₹<?= number_format($totalCGST) ?></div></div>
    <div class="kpi"><label>SGST</label><div class="v">₹<?= number_format($totalSGST) ?></div></div>
    <div class="kpi"><label>IGST</label><div class="v">₹<?= number_format($totalIGST) ?></div></div>
    <div class="kpi"><label>Grand Total</label><div class="v">₹<?= number_format($totalGrand) ?></div></div>
    <div class="kpi"><label>States Covered</label><div class="v"><?= count($stateData) ?></div></div>
</div>

<table class="rpt-tbl">
    <thead><tr><th>State</th><th>Type</th><th>Invoices</th><th style="text-align:right">Taxable</th><th style="text-align:right">CGST</th><th style="text-align:right">SGST</th><th style="text-align:right">IGST</th><th style="text-align:right">Total</th></tr></thead>
    <tbody>
    <?php if (empty($stateData)): ?>
        <tr><td colspan="8" style="text-align:center;padding:32px;color:#64748b">No data found.</td></tr>
    <?php else: foreach ($stateData as $state => $d): ?>
        <tr>
            <td><strong><?= htmlspecialchars($state) ?></strong></td>
            <td><?= $d['igst'] > 0 ? '<span class="inter-tag">Inter-State</span>' : '<span class="intra-tag">Intra-State</span>' ?></td>
            <td><?= $d['count'] ?></td>
            <td style="text-align:right">₹<?= number_format($d['taxable']) ?></td>
            <td style="text-align:right">₹<?= number_format($d['cgst']) ?></td>
            <td style="text-align:right">₹<?= number_format($d['sgst']) ?></td>
            <td style="text-align:right">₹<?= number_format($d['igst']) ?></td>
            <td style="text-align:right"><strong>₹<?= number_format($d['total']) ?></strong></td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><strong>Total</strong></td>
            <td><strong><?= array_sum(array_column($stateData, 'count')) ?></strong></td>
            <td style="text-align:right"><strong>₹<?= number_format($totalTaxable) ?></strong></td>
            <td style="text-align:right"><strong>₹<?= number_format($totalCGST) ?></strong></td>
            <td style="text-align:right"><strong>₹<?= number_format($totalSGST) ?></strong></td>
            <td style="text-align:right"><strong>₹<?= number_format($totalIGST) ?></strong></td>
            <td style="text-align:right"><strong>₹<?= number_format($totalGrand) ?></strong></td>
        </tr>
    </tfoot>
</table>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
