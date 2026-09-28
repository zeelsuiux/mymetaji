<?php
require_once __DIR__ . '/auth.php';
require_admin();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/company.php';

$pageTitle = 'GSTR-3B Report';
$activeModule = 'gstr3b';

$month = (int)($_GET['month'] ?? date('m'));
$year = (int)($_GET['year'] ?? date('Y'));
if ($month < 1 || $month > 12) $month = (int)date('m');
if ($year < 2000 || $year > 2100) $year = (int)date('Y');

$monthStart = sprintf('%04d-%02d-01', $year, $month);
$monthEnd = date('Y-m-t', strtotime($monthStart));
$prevMonth = $month === 1 ? 12 : $month - 1;
$prevYear = $month === 1 ? $year - 1 : $year;
$nextMonth = $month === 12 ? 1 : $month + 1;
$nextYear = $month === 12 ? $year + 1 : $year;
$monthName = date('F Y', strtotime($monthStart));

$invoices = db_get_all('invoices');
$filteredInvoices = [];
foreach ($invoices as $inv) {
    $createdAt = trim((string)($inv['created_at'] ?? ''));
    if ($createdAt === '') continue;
    $invDate = substr($createdAt, 0, 10);
    if ($invDate >= $monthStart && $invDate <= $monthEnd) {
        $filteredInvoices[] = $inv;
    }
}

$totalTaxable = 0;
$totalCgst = 0;
$totalSgst = 0;
$totalIgst = 0;
$totalInvoiceValue = 0;
$intraStateTaxable = 0;
$intraStateCgst = 0;
$intraStateSgst = 0;
$interStateTaxable = 0;
$interStateIgst = 0;
$rateWiseSummary = [];

foreach ($filteredInvoices as $inv) {
    $items = normalize_line_items($inv['items'] ?? []);
    $taxableAmount = 0;
    foreach ($items as $item) {
        $taxableAmount += (float)($item['total'] ?? 0);
    }
    $cgst = (float)($inv['total_cgst'] ?? 0);
    $sgst = (float)($inv['total_sgst'] ?? 0);
    $igst = (float)($inv['total_igst'] ?? 0);
    $grandTotal = (float)($inv['grand_total'] ?? ($taxableAmount + $cgst + $sgst + $igst));
    $isIntra = !empty($inv['is_intra_state']);

    $totalTaxable += $taxableAmount;
    $totalCgst += $cgst;
    $totalSgst += $sgst;
    $totalIgst += $igst;
    $totalInvoiceValue += $grandTotal;

    if ($isIntra) {
        $intraStateTaxable += $taxableAmount;
        $intraStateCgst += $cgst;
        $intraStateSgst += $sgst;
    } else {
        $interStateTaxable += $taxableAmount;
        $interStateIgst += $igst;
    }

    foreach ($items as $item) {
        $rate = (float)($item['gst_rate'] ?? 0);
        if (!isset($rateWiseSummary[$rate])) {
            $rateWiseSummary[$rate] = ['taxable' => 0, 'cgst' => 0, 'sgst' => 0, 'igst' => 0];
        }
        $rateWiseSummary[$rate]['taxable'] += (float)($item['total'] ?? 0);
        $itemGst = (float)($item['total'] ?? 0) * $rate / 100;
        if ($isIntra) {
            $rateWiseSummary[$rate]['cgst'] += $itemGst / 2;
            $rateWiseSummary[$rate]['sgst'] += $itemGst / 2;
        } else {
            $rateWiseSummary[$rate]['igst'] += $itemGst;
        }
    }
}
ksort($rateWiseSummary);

require __DIR__ . '/layout_top.php';
?>

<a href="index.php" class="page-back-link">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
  Back to Dashboard
</a>

<div class="db-greeting" style="margin-bottom:20px;">
    <div>
        <h2><?= icon('chart') ?> GSTR-3B Report</h2>
        <p>Monthly GST return summary — <?= htmlspecialchars($monthName) ?></p>
    </div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        <form method="get" style="display:flex;gap:6px;align-items:center;">
            <select name="month" onchange="this.form.submit()" style="padding:6px 10px;border:1px solid #d9dee7;border-radius:6px;font-size:13px;background:#fff;color:#334155;">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>" <?= $i === $month ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
                <?php endfor; ?>
            </select>
            <select name="year" onchange="this.form.submit()" style="padding:6px 10px;border:1px solid #d9dee7;border-radius:6px;font-size:13px;background:#fff;color:#334155;">
                <?php for ($y = (int)date('Y'); $y >= 2020; $y--): ?>
                    <option value="<?= $y ?>" <?= $y === $year ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </form>
        <button onclick="window.print()" class="btn btn-primary btn-sm"><?= icon('download', 14) ?> Download PDF</button>
    </div>
</div>

<div class="pdf-kpis" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
    <div class="panel" style="padding:16px;">
        <div style="font-size:12px;color:#64748b;">Total Outward Taxable Supplies</div>
        <div style="font-size:20px;font-weight:700;color:#111827;">₹<?= number_format($totalTaxable, 2) ?></div>
    </div>
    <div class="panel" style="padding:16px;">
        <div style="font-size:12px;color:#64748b;">Total IGST Payable</div>
        <div style="font-size:20px;font-weight:700;color:#ea580c;">₹<?= number_format($totalIgst, 2) ?></div>
    </div>
    <div class="panel" style="padding:16px;">
        <div style="font-size:12px;color:#64748b;">Total CGST Payable</div>
        <div style="font-size:20px;font-weight:700;color:#16a34a;">₹<?= number_format($totalCgst, 2) ?></div>
    </div>
    <div class="panel" style="padding:16px;">
        <div style="font-size:12px;color:#64748b;">Total SGST Payable</div>
        <div style="font-size:20px;font-weight:700;color:#16a34a;">₹<?= number_format($totalSgst, 2) ?></div>
    </div>
    <div class="panel" style="padding:16px;">
        <div style="font-size:12px;color:#64748b;">Total Tax Liability</div>
        <div style="font-size:20px;font-weight:700;color:#dc2626;">₹<?= number_format($totalCgst + $totalSgst + $totalIgst, 2) ?></div>
    </div>
</div>

<div class="panel" style="margin-bottom:24px;">
    <div class="panel-header"><h3>Table 3.1 — Outward Supplies (Taxable)</h3></div>
    <div class="panel-body">
        <div class="table-scroll-wrap">
        <table class="data-table">
            <thead><tr><th>Description</th><th>Total Taxable Value</th><th>Integrated Tax (IGST)</th><th>Central Tax (CGST)</th><th>State Tax (SGST)</th><th>Total Tax</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>Outward taxable supplies (other than zero rated, nil rated and exempted)</strong></td>
                    <td>₹<?= number_format($totalTaxable, 2) ?></td>
                    <td>₹<?= number_format($totalIgst, 2) ?></td>
                    <td>₹<?= number_format($totalCgst, 2) ?></td>
                    <td>₹<?= number_format($totalSgst, 2) ?></td>
                    <td><strong>₹<?= number_format($totalCgst + $totalSgst + $totalIgst, 2) ?></strong></td>
                </tr>
                <tr>
                    <td>Outward taxable supplies (zero rated)</td>
                    <td>₹0.00</td>
                    <td>₹0.00</td>
                    <td>₹0.00</td>
                    <td>₹0.00</td>
                    <td>₹0.00</td>
                </tr>
                <tr>
                    <td>Other outward supplies (nil rated, exempted)</td>
                    <td>₹0.00</td>
                    <td>₹0.00</td>
                    <td>₹0.00</td>
                    <td>₹0.00</td>
                    <td>₹0.00</td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>
</div>

<div class="pdf-tables" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    <div class="panel">
        <div class="panel-header"><h3>Intra-State Supplies</h3></div>
        <div class="panel-body">
            <div style="display:grid;gap:12px;">
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;">
                    <span>Taxable Value</span><strong>₹<?= number_format($intraStateTaxable, 2) ?></strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;">
                    <span>CGST</span><strong style="color:#16a34a;">₹<?= number_format($intraStateCgst, 2) ?></strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;">
                    <span>SGST</span><strong style="color:#16a34a;">₹<?= number_format($intraStateSgst, 2) ?></strong>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header"><h3>Inter-State Supplies</h3></div>
        <div class="panel-body">
            <div style="display:grid;gap:12px;">
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;">
                    <span>Taxable Value</span><strong>₹<?= number_format($interStateTaxable, 2) ?></strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;">
                    <span>IGST</span><strong style="color:#ea580c;">₹<?= number_format($interStateIgst, 2) ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($rateWiseSummary)): ?>
<div class="panel" style="margin-bottom:24px;">
    <div class="panel-header"><h3>Rate-wise Summary</h3></div>
    <div class="panel-body">
        <div class="table-scroll-wrap">
        <table class="data-table">
            <thead><tr><th>GST Rate</th><th>Taxable Value</th><th>CGST</th><th>SGST</th><th>IGST</th><th>Total Tax</th></tr></thead>
            <tbody>
                <?php foreach ($rateWiseSummary as $rate => $r): ?>
                <tr>
                    <td><?= number_format($rate, 2) ?>%</td>
                    <td>₹<?= number_format($r['taxable'], 2) ?></td>
                    <td>₹<?= number_format($r['cgst'], 2) ?></td>
                    <td>₹<?= number_format($r['sgst'], 2) ?></td>
                    <td>₹<?= number_format($r['igst'], 2) ?></td>
                    <td><strong>₹<?= number_format($r['cgst'] + $r['sgst'] + $r['igst'], 2) ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="pdf-due-notice" style="margin-bottom:24px;background:#fef3c7;border:1px solid #fde68a;">
    <div class="panel-body" style="padding:16px;">
        <strong>GST Payment Due:</strong> ₹<?= number_format($totalCgst + $totalSgst + $totalIgst, 2) ?>
        <span style="margin-left:12px;color:#64748b;font-size:13px;">(CGST: ₹<?= number_format($totalCgst, 2) ?> + SGST: ₹<?= number_format($totalSgst, 2) ?> + IGST: ₹<?= number_format($totalIgst, 2) ?>)</span>
        <br><small style="color:#92400e;">Due date: <?= date('d-m-Y', strtotime($monthEnd . ' + 20 days')) ?> (20th of following month)</small>
    </div>
</div>

<div class="pdf-print-footer" style="display:none;text-align:center;padding:20px;color:#94a3b8;font-size:12px;">
    Generated on <?= date('d-m-Y H:i:s') ?> · <?= htmlspecialchars(COMPANY_NAME) ?>
</div>

<style>
@media print {
  body { background: #fff !important; margin: 0 !important; padding: 0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  .topbar, .sidebar, .page-back-link, .db-greeting, .no-print,
  .pdf-kpis, .pdf-due-notice, form, .btn { display: none !important; }
  .content { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
  .pdf-tables { grid-template-columns: 1fr !important; gap: 20px !important; }
  .panel { box-shadow: none !important; border: 1px solid #d1d5db !important; margin-bottom: 16px !important; break-inside: avoid; }
  .panel-header { border-bottom: 1px solid #d1d5db !important; padding: 8px 12px !important; background: #f9fafb !important; }
  .panel-header h3 { font-size: 13px !important; margin: 0 !important; color: #111827 !important; }
  .panel-body { padding: 8px !important; }
  .table-scroll-wrap { overflow: visible !important; }
  .data-table { width: 100% !important; font-size: 11px !important; border-collapse: collapse !important; }
  .data-table th { background: #e5e7eb !important; font-size: 10px !important; padding: 6px 8px !important; border: 1px solid #d1d5db !important; text-transform: uppercase !important; letter-spacing: 0.3px !important; }
  .data-table td { padding: 5px 8px !important; border: 1px solid #e5e7eb !important; font-size: 11px !important; }
  .pdf-print-header { display: block !important; text-align: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #111827; }
  .pdf-print-header h1 { font-size: 18px; margin: 0 0 4px; color: #111827; }
  .pdf-print-header p { font-size: 12px; margin: 0; color: #64748b; }
  .pdf-print-footer { display: block !important; text-align: center; padding-top: 12px; border-top: 1px solid #d1d5db; margin-top: 16px; font-size: 11px; color: #64748b; }
}
</style>

<div class="pdf-print-header" style="display:none;">
  <h1>GSTR-3B Report</h1>
  <p>Monthly GST Return Summary — <?= htmlspecialchars($monthName) ?> · <?= htmlspecialchars(COMPANY_NAME) ?></p>
</div>

<?php require __DIR__ . '/layout_bottom.php'; ?>
