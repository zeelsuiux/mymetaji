<?php
$activeModule = 'report_quotation';
$pageTitle = 'Quotation Conversion Report';
require_once __DIR__ . '/layout_top.php';

$quotations = db_get_all('quotations');
$invoices = db_get_all('invoices');

$totalQuotes = count($quotations);
$converted = array_filter($quotations, fn($q) => ($q['status'] ?? '') === 'Converted' || !empty($q['converted_to_invoice_id']));
$convertedCount = count($converted);
$pending = array_filter($quotations, fn($q) => ($q['status'] ?? '') === 'Sent');
$pendingCount = count($pending);
$expired = array_filter($quotations, fn($q) => ($q['status'] ?? '') === 'Expired');
$expiredCount = count($expired);
$rejected = array_filter($quotations, fn($q) => ($q['status'] ?? '') === 'Rejected');
$rejectedCount = count($rejected);
$conversionRate = $totalQuotes > 0 ? round(($convertedCount / $totalQuotes) * 100, 1) : 0;

$totalQuoteValue = array_sum(array_map(fn($q) => (float)($q['amount'] ?? 0), $quotations));
$convertedValue = array_sum(array_map(fn($q) => (float)($q['amount'] ?? 0), $converted));

$byCustomer = [];
foreach ($quotations as $q) {
    $cust = $q['customer'] ?? 'Unknown';
    if (!isset($byCustomer[$cust])) $byCustomer[$cust] = ['total' => 0, 'converted' => 0, 'value' => 0, 'conv_value' => 0];
    $byCustomer[$cust]['total']++;
    $byCustomer[$cust]['value'] += (float)($q['amount'] ?? 0);
    if (($q['status'] ?? '') === 'Converted' || !empty($q['converted_to_invoice_id'])) {
        $byCustomer[$cust]['converted']++;
        $byCustomer[$cust]['conv_value'] += (float)($q['amount'] ?? 0);
    }
}
?>

<style>
.kpi-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin-bottom:24px}
.kpi{background:#fff;border:1.5px solid #E2E6EF;border-radius:12px;padding:16px;text-align:center}
.kpi label{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase}
.kpi .v{font-size:26px;font-weight:800;margin-top:2px}
.kpi .v.g{color:#16a34a}.kpi .v.r{color:#dc2626}.kpi .v.b{color:#2563EB}.kpi .v.o{color:#d97706}
.rate-bar{background:#fff;border:1.5px solid #E2E6EF;border-radius:14px;padding:24px;margin-bottom:24px}
.rate-track{height:32px;background:#F4F6FA;border-radius:16px;overflow:hidden;position:relative;margin-top:12px}
.rate-fill{height:100%;background:linear-gradient(90deg,#2563EB,#3B82F6);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:800;transition:width .5s}
table.rpt-tbl{width:100%;border-collapse:collapse;font-size:13px}
.rpt-tbl th{text-align:left;padding:10px 12px;background:#F4F6FA;font-weight:700;color:#475569;border-bottom:2px solid #E2E6EF;font-size:12px;text-transform:uppercase}
.rpt-tbl td{padding:10px 12px;border-bottom:1px solid #F1F5F9}
.rpt-tbl tr:hover td{background:#F8FAFC}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.badge.green{background:#dcfce7;color:#166534}.badge.orange{background:#fff7ed;color:#9a3412}.badge.red{background:#fef2f2;color:#991b1b}.badge.blue{background:#eff6ff;color:#1e40af}
</style>

<div class="kpi-row">
    <div class="kpi"><label>Total Quotations</label><div class="v"><?= $totalQuotes ?></div></div>
    <div class="kpi"><label>Converted</label><div class="v g"><?= $convertedCount ?></div></div>
    <div class="kpi"><label>Pending</label><div class="v o"><?= $pendingCount ?></div></div>
    <div class="kpi"><label>Expired</label><div class="v r"><?= $expiredCount ?></div></div>
    <div class="kpi"><label>Rejected</label><div class="v r"><?= $rejectedCount ?></div></div>
    <div class="kpi"><label>Conversion Rate</label><div class="v b"><?= $conversionRate ?>%</div></div>
</div>

<div class="rate-bar">
    <div style="display:flex;justify-content:space-between;align-items:center">
        <div><strong style="font-size:16px">Conversion Rate</strong><br><span style="font-size:13px;color:#64748b"><?= $convertedCount ?> of <?= $totalQuotes ?> quotations converted</span></div>
        <div style="font-size:28px;font-weight:900;color:#2563EB"><?= $conversionRate ?>%</div>
    </div>
    <div class="rate-track"><div class="rate-fill" style="width:<?= $conversionRate ?>%"><?= $conversionRate ?>%</div></div>
</div>

<div class="kpi-row" style="grid-template-columns:1fr 1fr">
    <div class="kpi"><label>Total Quote Value</label><div class="v b">₹<?= number_format($totalQuoteValue) ?></div></div>
    <div class="kpi"><label>Converted Value</label><div class="v g">₹<?= number_format($convertedValue) ?></div></div>
</div>

<h3 style="font-size:16px;font-weight:700;margin:0 0 14px">Customer-wise Conversion</h3>
<table class="rpt-tbl">
    <thead><tr><th>Customer</th><th>Total Quotes</th><th>Converted</th><th>Rate</th><th style="text-align:right">Quote Value</th><th style="text-align:right">Converted Value</th></tr></thead>
    <tbody>
    <?php if (empty($byCustomer)): ?>
        <tr><td colspan="6" style="text-align:center;padding:32px;color:#64748b">No quotations found.</td></tr>
    <?php else: foreach ($byCustomer as $cust => $d): $rate = $d['total'] > 0 ? round(($d['converted']/$d['total'])*100) : 0; ?>
        <tr>
            <td><strong><?= htmlspecialchars($cust) ?></strong></td>
            <td><?= $d['total'] ?></td>
            <td><?= $d['converted'] ?></td>
            <td><span class="badge <?= $rate >= 50 ? 'green' : ($rate > 0 ? 'orange' : 'red') ?>"><?= $rate ?>%</span></td>
            <td style="text-align:right">₹<?= number_format($d['value']) ?></td>
            <td style="text-align:right">₹<?= number_format($d['conv_value']) ?></td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
