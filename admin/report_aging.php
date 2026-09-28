<?php
$activeModule = 'report_aging';
$pageTitle = 'Customer Aging Report';
require_once __DIR__ . '/layout_top.php';

$invoices = db_get_all('invoices');
$payments = db_get_all('payments');
$customers = db_get_all('customers');

$aging = ['current' => ['count' => 0, 'amount' => 0], '1_30' => ['count' => 0, 'amount' => 0], '31_60' => ['count' => 0, 'amount' => 0], '61_90' => ['count' => 0, 'amount' => 0], '90_plus' => ['count' => 0, 'amount' => 0]];
$customerAging = [];

foreach ($invoices as $inv) {
    if (($inv['status'] ?? '') === 'Paid') continue;
    $invNo = $inv['invoice_no'] ?? '';
    $amount = (float)($inv['amount'] ?? $inv['grand_total'] ?? 0);
    $paid = array_sum(array_map(fn($p) => (float)($p['amount'] ?? 0), array_filter($payments, fn($p) => ($p['invoice_no'] ?? '') === $invNo)));
    $pending = $amount - $paid;
    if ($pending <= 0) continue;

    $dueDate = $inv['due_date'] ?? '';
    $invDate = substr($inv['created_at'] ?? '', 0, 10);
    $refDate = $dueDate ?: $invDate;
    $days = ($refDate && $refDate < date('Y-m-d')) ? (int)((strtotime(date('Y-m-d')) - strtotime($refDate)) / 86400) : 0;

    $cust = $inv['customer'] ?? 'Unknown';
    if (!isset($customerAging[$cust])) $customerAging[$cust] = ['total' => 0, 'max_days' => 0, 'invoices' => 0, 'current' => 0, '1_30' => 0, '31_60' => 0, '61_90' => 0, '90_plus' => 0];
    $customerAging[$cust]['total'] += $pending;
    $customerAging[$cust]['invoices']++;
    $customerAging[$cust]['max_days'] = max($customerAging[$cust]['max_days'], $days);

    if ($days === 0) { $aging['current']['count']++; $aging['current']['amount'] += $pending; $customerAging[$cust]['current'] += $pending; }
    elseif ($days <= 30) { $aging['1_30']['count']++; $aging['1_30']['amount'] += $pending; $customerAging[$cust]['1_30'] += $pending; }
    elseif ($days <= 60) { $aging['31_60']['count']++; $aging['31_60']['amount'] += $pending; $customerAging[$cust]['31_60'] += $pending; }
    elseif ($days <= 90) { $aging['61_90']['count']++; $aging['61_90']['amount'] += $pending; $customerAging[$cust]['61_90'] += $pending; }
    else { $aging['90_plus']['count']++; $aging['90_plus']['amount'] += $pending; $customerAging[$cust]['90_plus'] += $pending; }
}
uasort($customerAging, fn($a, $b) => $b['total'] <=> $a['total']);
$totalPending = array_sum(array_column($aging, 'amount'));
?>

<style>
.kpi-row{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:24px}
.kpi{background:#fff;border:1.5px solid #E2E6EF;border-radius:12px;padding:16px;text-align:center}
.kpi label{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase}
.kpi .v{font-size:22px;font-weight:800;margin-top:2px}
.kpi .v.g{color:#16a34a}.kpi .v.o{color:#d97706}.kpi .v.r{color:#dc2626}
.aging-bar{display:flex;gap:4px;height:40px;border-radius:10px;overflow:hidden;margin-bottom:24px}
.aging-bar div{display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;transition:width .5s}
.aging-legend{display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap}
.aging-legend span{display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#475569}
.aging-legend span i{width:12px;height:12px;border-radius:3px;display:inline-block}
table.rpt-tbl{width:100%;border-collapse:collapse;font-size:13px}
.rpt-tbl th{text-align:left;padding:10px 12px;background:#F4F6FA;font-weight:700;color:#475569;border-bottom:2px solid #E2E6EF;font-size:12px;text-transform:uppercase}
.rpt-tbl td{padding:10px 12px;border-bottom:1px solid #F1F5F9}
.rpt-tbl tr:hover td{background:#F8FAFC}
.rpt-tbl tfoot td{font-weight:800;background:#F4F6FA;border-top:2px solid #E2E6EF}
</style>

<div class="kpi-row">
    <div class="kpi"><label>Current</label><div class="v g">₹<?= number_format($aging['current']['amount']) ?></div></div>
    <div class="kpi"><label>1-30 Days</label><div class="v" style="color:#d97706">₹<?= number_format($aging['1_30']['amount']) ?></div></div>
    <div class="kpi"><label>31-60 Days</label><div class="v o">₹<?= number_format($aging['31_60']['amount']) ?></div></div>
    <div class="kpi"><label>61-90 Days</label><div class="v r">₹<?= number_format($aging['61_90']['amount']) ?></div></div>
    <div class="kpi"><label>90+ Days</div><div class="v r">₹<?= number_format($aging['90_plus']['amount']) ?></div></div>
</div>

<div class="aging-bar">
    <?php $colors = ['#16a34a','#d97706','#ea580c','#dc2626','#991b1b']; $labels = ['Current','1-30','31-60','61-90','90+']; $keys = ['current','1_30','31_60','61_90','90_plus']; foreach ($keys as $i => $k): $pct = $totalPending > 0 ? round(($aging[$k]['amount']/$totalPending)*100) : 0; ?>
        <div style="width:<?= $pct ?>%;background:<?= $colors[$i] ?>;min-width:<?= $pct > 0 ? '40' : '0' ?>px"><?= $pct > 5 ? $labels[$i].'<br>'.$pct.'%' : '' ?></div>
    <?php endforeach; ?>
</div>

<div class="aging-legend">
    <?php foreach ($keys as $i => $k): ?>
        <span><i style="background:<?= $colors[$i] ?>"></i><?= $labels[$i] ?>: ₹<?= number_format($aging[$k]['amount']) ?></span>
    <?php endforeach; ?>
</div>

<h3 style="font-size:16px;font-weight:700;margin:0 0 14px">Customer-wise Aging</h3>
<table class="rpt-tbl">
    <thead><tr><th>Customer</th><th>Invoices</th><th>Oldest (Days)</th><th style="text-align:right">Current</th><th style="text-align:right">1-30</th><th style="text-align:right">31-60</th><th style="text-align:right">61-90</th><th style="text-align:right">90+</th><th style="text-align:right">Total</th></tr></thead>
    <tbody>
    <?php if (empty($customerAging)): ?>
        <tr><td colspan="9" style="text-align:center;padding:32px;color:#64748b">No outstanding dues!</td></tr>
    <?php else: foreach ($customerAging as $cust => $d): ?>
        <tr>
            <td><strong><?= htmlspecialchars($cust) ?></strong></td>
            <td><?= $d['invoices'] ?></td>
            <td><?= $d['max_days'] > 0 ? $d['max_days'].'d' : '-' ?></td>
            <td style="text-align:right;color:#16a34a"><?= $d['current'] > 0 ? '₹'.number_format($d['current']) : '-' ?></td>
            <td style="text-align:right;color:#d97706"><?= $d['1_30'] > 0 ? '₹'.number_format($d['1_30']) : '-' ?></td>
            <td style="text-align:right;color:#ea580c"><?= $d['31_60'] > 0 ? '₹'.number_format($d['31_60']) : '-' ?></td>
            <td style="text-align:right;color:#dc2626"><?= $d['61_90'] > 0 ? '₹'.number_format($d['61_90']) : '-' ?></td>
            <td style="text-align:right;color:#991b1b"><?= $d['90_plus'] > 0 ? '₹'.number_format($d['90_plus']) : '-' ?></td>
            <td style="text-align:right"><strong>₹<?= number_format($d['total']) ?></strong></td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
