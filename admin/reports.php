<?php
$activeModule = 'reports';
$pageTitle = 'All Reports';
require_once __DIR__ . '/layout_top.php';
$invoices = db_get_all('invoices');
$payments = db_get_all('payments');
$expenses = db_get_all('expenses');
$products = db_get_all('products');
$customers = db_get_all('customers');
$totalInvoiced = array_sum(array_map(fn($i) => (float)($i['amount'] ?? $i['grand_total'] ?? 0), $invoices));
$totalCollected = array_sum(array_map(fn($p) => (float)($p['amount'] ?? 0), $payments));
$totalExpenses = array_sum(array_map(fn($e) => (float)($e['amount'] ?? 0), $expenses));
$totalPending = $totalInvoiced - $totalCollected;
$paidCount = count(array_filter($invoices, fn($i) => ($i['status'] ?? '') === 'Paid'));
$overdueCount = count(array_filter($invoices, fn($i) => ($i['status'] ?? '') === 'Overdue'));
?>
<style>
.rpt-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;margin-bottom:24px}
.rpt-card{background:#fff;border:1.5px solid #E2E6EF;border-radius:14px;padding:22px 20px;text-decoration:none;color:inherit;transition:all .2s;display:flex;align-items:center;gap:16px}
.rpt-card:hover{border-color:#2563EB;box-shadow:0 4px 16px rgba(37,99,235,0.1);transform:translateY(-2px)}
.rpt-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
.rpt-card h3{font-size:15px;font-weight:700;margin:0 0 3px}
.rpt-card p{font-size:12px;color:#64748b;margin:0}
.kpi-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin-bottom:24px}
.kpi-card{background:#fff;border:1.5px solid #E2E6EF;border-radius:12px;padding:16px}
.kpi-card label{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.5px}
.kpi-card .val{font-size:24px;font-weight:800;color:#0f172a;margin-top:4px}
.kpi-card .val.green{color:#16a34a}.kpi-card .val.red{color:#dc2626}.kpi-card .val.blue{color:#2563EB}.kpi-card .val.orange{color:#d97706}
</style>

<div class="kpi-row">
    <div class="kpi-card"><label>Total Invoiced</label><div class="val">₹<?= number_format($totalInvoiced) ?></div></div>
    <div class="kpi-card"><label>Collected</label><div class="val green">₹<?= number_format($totalCollected) ?></div></div>
    <div class="kpi-card"><label>Pending</label><div class="val orange">₹<?= number_format($totalPending) ?></div></div>
    <div class="kpi-card"><label>Expenses</label><div class="val red">₹<?= number_format($totalExpenses) ?></div></div>
    <div class="kpi-card"><label>Paid Bills</label><div class="val green"><?= $paidCount ?></div></div>
    <div class="kpi-card"><label>Overdue</label><div class="val red"><?= $overdueCount ?></div></div>
</div>

<div class="rpt-grid">
    <a href="report_sales.php" class="rpt-card"><div class="rpt-icon" style="background:#eff6ff;color:#2563EB">📊</div><div><h3>Sales Report</h3><p>Daily, weekly, monthly, yearly sales</p></div></a>
    <a href="report_expense.php" class="rpt-card"><div class="rpt-icon" style="background:#fef2f2;color:#dc2626">💸</div><div><h3>Expense Report</h3><p>Expenses by category</p></div></a>
    <a href="report_profit_loss.php" class="rpt-card"><div class="rpt-icon" style="background:#f0fdf4;color:#16a34a">💰</div><div><h3>Profit & Loss</h3><p>Revenue vs expenses overview</p></div></a>
    <a href="report_outstanding.php" class="rpt-card"><div class="rpt-icon" style="background:#fffbeb;color:#d97706">📋</div><div><h3>Outstanding</h3><p>Accounts receivable & pending</p></div></a>
    <a href="report_product_sales.php" class="rpt-card"><div class="rpt-icon" style="background:#eff6ff;color:#2563EB">📦</div><div><h3>Product Sales</h3><p>Top selling products</p></div></a>
    <a href="report_payments.php" class="rpt-card"><div class="rpt-icon" style="background:#f0fdf4;color:#16a34a">💳</div><div><h3>Payment Report</h3><p>Received vs pending by mode</p></div></a>
    <a href="report_hsn.php" class="rpt-card"><div class="rpt-icon" style="background:#eff6ff;color:#2563EB">🏷️</div><div><h3>HSN Summary</h3><p>HSN-wise GST breakdown</p></div></a>
    <a href="report_state_gst.php" class="rpt-card"><div class="rpt-icon" style="background:#fffbeb;color:#d97706">🗺️</div><div><h3>State-wise GST</h3><p>State-wise tax summary</p></div></a>
    <a href="report_quotation.php" class="rpt-card"><div class="rpt-icon" style="background:#f0fdf4;color:#16a34a">📝</div><div><h3>Quote Conversion</h3><p>Quotation to invoice ratio</p></div></a>
    <a href="report_aging.php" class="rpt-card"><div class="rpt-icon" style="background:#fef2f2;color:#dc2626">⏰</div><div><h3>Aging Report</h3><p>30/60/90 day receivables</p></div></a>
    <a href="report_revenue.php" class="rpt-card"><div class="rpt-icon" style="background:#eff6ff;color:#2563EB">📈</div><div><h3>Revenue Trend</h3><p>Monthly trend & forecast</p></div></a>
</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
