<?php
$activeModule = 'report_outstanding';
$pageTitle = 'Outstanding Report';
require_once __DIR__ . '/layout_top.php';

$invoices = db_get_all('invoices');
$payments = db_get_all('payments');
$customers = db_get_all('customers');

$outstanding = [];
foreach ($invoices as $inv) {
    if (($inv['status'] ?? '') === 'Paid') continue;
    $invNo = $inv['invoice_no'] ?? '';
    $amount = (float)($inv['amount'] ?? $inv['grand_total'] ?? 0);
    $paid = array_sum(array_map(fn($p) => (float)($p['amount'] ?? 0), array_filter($payments, fn($p) => ($p['invoice_no'] ?? '') === $invNo)));
    $pending = $amount - $paid;
    if ($pending <= 0) continue;
    $cust = $inv['customer'] ?? 'Unknown';
    $dueDate = $inv['due_date'] ?? '';
    $daysOverdue = ($dueDate && $dueDate < date('Y-m-d')) ? (int)((strtotime(date('Y-m-d')) - strtotime($dueDate)) / 86400) : 0;
    $outstanding[] = ['customer' => $cust, 'invoice_no' => $invNo, 'date' => substr($inv['created_at'] ?? '', 0, 10), 'amount' => $amount, 'paid' => $paid, 'pending' => $pending, 'due_date' => $dueDate, 'days_overdue' => $daysOverdue, 'status' => $inv['status'] ?? 'Pending'];
}
usort($outstanding, fn($a, $b) => $b['pending'] <=> $a['pending']);

$byCustomer = [];
foreach ($outstanding as $o) {
    $c = $o['customer'];
    if (!isset($byCustomer[$c])) $byCustomer[$c] = ['pending' => 0, 'count' => 0, 'max_overdue' => 0];
    $byCustomer[$c]['pending'] += $o['pending'];
    $byCustomer[$c]['count']++;
    $byCustomer[$c]['max_overdue'] = max($byCustomer[$c]['max_overdue'], $o['days_overdue']);
}
arsort(array_column($byCustomer, 'pending'));

$totalOutstanding = array_sum(array_map(fn($o) => $o['pending'], $outstanding));
?>

<style>
.kpi-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin-bottom:24px}
.kpi{background:#fff;border:1.5px solid #E2E6EF;border-radius:12px;padding:16px}
.kpi label{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase}
.kpi .v{font-size:22px;font-weight:800;margin-top:2px}
.kpi .v.r{color:#dc2626}.kpi .v.o{color:#d97706}.kpi .v.b{color:#2563EB}
table.rpt-tbl{width:100%;border-collapse:collapse;font-size:13px}
.rpt-tbl th{text-align:left;padding:10px 12px;background:#F4F6FA;font-weight:700;color:#475569;border-bottom:2px solid #E2E6EF;font-size:12px;text-transform:uppercase}
.rpt-tbl td{padding:10px 12px;border-bottom:1px solid #F1F5F9}
.rpt-tbl tr:hover td{background:#F8FAFC}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.badge.red{background:#fef2f2;color:#991b1b}.badge.orange{background:#fff7ed;color:#9a3412}.badge.green{background:#dcfce7;color:#166534}
</style>

<div class="kpi-row">
    <div class="kpi"><label>Total Outstanding</label><div class="v r">₹<?= number_format($totalOutstanding) ?></div></div>
    <div class="kpi"><label>Pending Invoices</label><div class="v o"><?= count($outstanding) ?></div></div>
    <div class="kpi"><label>Customers with Dues</label><div class="v b"><?= count($byCustomer) ?></div></div>
    <?php $overdueCount = count(array_filter($outstanding, fn($o) => $o['days_overdue'] > 0)); ?>
    <div class="kpi"><label>Overdue</label><div class="v r"><?= $overdueCount ?></div></div>
</div>

<h3 style="font-size:16px;font-weight:700;margin:0 0 14px">Customer-wise Outstanding</h3>
<table class="rpt-tbl">
    <thead><tr><th>Customer</th><th>Invoices</th><th>Oldest Overdue</th><th style="text-align:right">Pending Amount</th></tr></thead>
    <tbody>
    <?php if (empty($byCustomer)): ?>
        <tr><td colspan="4" style="text-align:center;padding:32px;color:#64748b">No outstanding dues!</td></tr>
    <?php else: foreach ($byCustomer as $cust => $d): ?>
        <tr>
            <td><strong><?= htmlspecialchars($cust) ?></strong></td>
            <td><?= $d['count'] ?></td>
            <td><?= $d['max_overdue'] > 0 ? '<span class="badge red">'.$d['max_overdue'].' days</span>' : '<span class="badge green">On Time</span>' ?></td>
            <td style="text-align:right;font-weight:700;color:#dc2626">₹<?= number_format($d['pending']) ?></td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<h3 style="font-size:16px;font-weight:700;margin:24px 0 14px">Invoice-wise Detail</h3>
<table class="rpt-tbl">
    <thead><tr><th>Invoice No</th><th>Date</th><th>Customer</th><th>Due Date</th><th>Days Overdue</th><th style="text-align:right">Amount</th><th style="text-align:right">Paid</th><th style="text-align:right">Pending</th></tr></thead>
    <tbody>
    <?php foreach ($outstanding as $o): ?>
        <tr>
            <td><strong><?= htmlspecialchars($o['invoice_no']) ?></strong></td>
            <td><?= date('d M Y', strtotime($o['date'])) ?></td>
            <td><?= htmlspecialchars($o['customer']) ?></td>
            <td><?= $o['due_date'] ? date('d M Y', strtotime($o['due_date'])) : '-' ?></td>
            <td><?= $o['days_overdue'] > 0 ? '<span class="badge red">'.$o['days_overdue'].'</span>' : '-' ?></td>
            <td style="text-align:right">₹<?= number_format($o['amount']) ?></td>
            <td style="text-align:right">₹<?= number_format($o['paid']) ?></td>
            <td style="text-align:right;font-weight:700;color:#dc2626">₹<?= number_format($o['pending']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
