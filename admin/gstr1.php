<?php
require_once __DIR__ . '/auth.php';
require_admin();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/company.php';

$pageTitle = 'GSTR-1 Report';
$activeModule = 'gstr1';

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
$hsnSummary = [];
$stateWiseSummary = [];
$b2bInvoices = [];
$b2cInvoices = [];

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
    $pos = trim((string)($inv['place_of_supply'] ?? ''));
    $isIntra = !empty($inv['is_intra_state']);

    $totalTaxable += $taxableAmount;
    $totalCgst += $cgst;
    $totalSgst += $sgst;
    $totalIgst += $igst;
    $totalInvoiceValue += $grandTotal;

    $hasGstin = false;
    $customerName = trim((string)($inv['customer'] ?? ''));
    foreach (db_get_all('customers') as $c) {
        if (strcasecmp(trim((string)($c['name'] ?? '')), $customerName) === 0) {
            if (!empty($c['gstin'])) $hasGstin = true;
            break;
        }
    }

    $invoiceData = [
        'invoice_no' => $inv['invoice_no'] ?? '',
        'date' => $invDate,
        'customer' => $customerName,
        'gstin' => '',
        'place_of_supply' => $pos,
        'taxable_amount' => $taxableAmount,
        'cgst' => $cgst,
        'sgst' => $sgst,
        'igst' => $igst,
        'total' => $grandTotal,
        'is_intra' => $isIntra,
    ];
    foreach (db_get_all('customers') as $c) {
        if (strcasecmp(trim((string)($c['name'] ?? '')), $customerName) === 0) {
            $invoiceData['gstin'] = trim((string)($c['gstin'] ?? ''));
            $invoiceData['state'] = trim((string)($c['state'] ?? ''));
            break;
        }
    }

    if ($hasGstin) {
        $b2bInvoices[] = $invoiceData;
    } else {
        $b2cInvoices[] = $invoiceData;
    }

    foreach ($items as $item) {
        $hsn = trim((string)($item['hsn_code'] ?? '')) ?: 'NA';
        $rate = (float)($item['gst_rate'] ?? 0);
        $key = $hsn . '_' . $rate;
        if (!isset($hsnSummary[$key])) {
            $hsnSummary[$key] = ['hsn' => $hsn, 'rate' => $rate, 'qty' => 0, 'taxable' => 0, 'cgst' => 0, 'sgst' => 0, 'igst' => 0];
        }
        $hsnSummary[$key]['qty'] += (float)($item['qty'] ?? 0);
        $hsnSummary[$key]['taxable'] += (float)($item['total'] ?? 0);
        $itemGst = (float)($item['total'] ?? 0) * $rate / 100;
        if ($isIntra) {
            $hsnSummary[$key]['cgst'] += $itemGst / 2;
            $hsnSummary[$key]['sgst'] += $itemGst / 2;
        } else {
            $hsnSummary[$key]['igst'] += $itemGst;
        }
    }

    if ($pos !== '') {
        if (!isset($stateWiseSummary[$pos])) {
            $stateWiseSummary[$pos] = ['taxable' => 0, 'cgst' => 0, 'sgst' => 0, 'igst' => 0, 'count' => 0];
        }
        $stateWiseSummary[$pos]['taxable'] += $taxableAmount;
        $stateWiseSummary[$pos]['cgst'] += $cgst;
        $stateWiseSummary[$pos]['sgst'] += $sgst;
        $stateWiseSummary[$pos]['igst'] += $igst;
        $stateWiseSummary[$pos]['count']++;
    }
}

require __DIR__ . '/layout_top.php';
?>

<a href="index.php" class="page-back-link">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
  Back to Dashboard
</a>

<div class="db-greeting" style="margin-bottom:20px;">
    <div>
        <h2><?= icon('chart') ?> GSTR-1 Report</h2>
        <p>Outward supply summary for GST filing — <?= htmlspecialchars($monthName) ?></p>
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
        <div style="font-size:12px;color:#64748b;">Total Taxable Value</div>
        <div style="font-size:20px;font-weight:700;color:#111827;">₹<?= number_format($totalTaxable, 2) ?></div>
    </div>
    <div class="panel" style="padding:16px;">
        <div style="font-size:12px;color:#64748b;">Total CGST</div>
        <div style="font-size:20px;font-weight:700;color:#16a34a;">₹<?= number_format($totalCgst, 2) ?></div>
    </div>
    <div class="panel" style="padding:16px;">
        <div style="font-size:12px;color:#64748b;">Total SGST</div>
        <div style="font-size:20px;font-weight:700;color:#16a34a;">₹<?= number_format($totalSgst, 2) ?></div>
    </div>
    <div class="panel" style="padding:16px;">
        <div style="font-size:12px;color:#64748b;">Total IGST</div>
        <div style="font-size:20px;font-weight:700;color:#ea580c;">₹<?= number_format($totalIgst, 2) ?></div>
    </div>
    <div class="panel" style="padding:16px;">
        <div style="font-size:12px;color:#64748b;">Total Invoice Value</div>
        <div style="font-size:20px;font-weight:700;color:#02A9F7;">₹<?= number_format($totalInvoiceValue, 2) ?></div>
    </div>
    <div class="panel" style="padding:16px;">
        <div style="font-size:12px;color:#64748b;">Total Invoices</div>
        <div style="font-size:20px;font-weight:700;color:#111827;"><?= count($filteredInvoices) ?></div>
    </div>
</div>

<div class="pdf-tables" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    <div class="panel">
        <div class="panel-header"><h3>B2B Invoices (With GSTIN) — <?= count($b2bInvoices) ?></h3></div>
        <div class="panel-body">
            <?php if (empty($b2bInvoices)): ?>
                <div class="empty-state" style="padding:20px;">No B2B invoices for this period.</div>
            <?php else: ?>
                <div class="table-scroll-wrap">
                <table class="data-table">
                    <thead><tr><th>Invoice No</th><th>Date</th><th>Customer</th><th>GSTIN</th><th>Place</th><th>Taxable</th><th>CGST</th><th>SGST</th><th>IGST</th><th>Total</th></tr></thead>
                    <tbody>
                        <?php foreach ($b2bInvoices as $b): ?>
                        <tr>
                            <td><?= htmlspecialchars($b['invoice_no']) ?></td>
                            <td><?= htmlspecialchars(format_display_date($b['date'])) ?></td>
                            <td><?= htmlspecialchars($b['customer']) ?></td>
                            <td style="font-size:11px;"><?= htmlspecialchars(format_gst_number($b['gstin'])) ?></td>
                            <td><?= htmlspecialchars($b['place_of_supply']) ?></td>
                            <td>₹<?= number_format($b['taxable_amount'], 2) ?></td>
                            <td>₹<?= number_format($b['cgst'], 2) ?></td>
                            <td>₹<?= number_format($b['sgst'], 2) ?></td>
                            <td>₹<?= number_format($b['igst'], 2) ?></td>
                            <td><strong>₹<?= number_format($b['total'], 2) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header"><h3>B2C Invoices (Without GSTIN) — <?= count($b2cInvoices) ?></h3></div>
        <div class="panel-body">
            <?php if (empty($b2cInvoices)): ?>
                <div class="empty-state" style="padding:20px;">No B2C invoices for this period.</div>
            <?php else: ?>
                <div class="table-scroll-wrap">
                <table class="data-table">
                    <thead><tr><th>Invoice No</th><th>Date</th><th>Customer</th><th>Place</th><th>Taxable</th><th>GST</th><th>Total</th></tr></thead>
                    <tbody>
                        <?php foreach ($b2cInvoices as $b): ?>
                        <tr>
                            <td><?= htmlspecialchars($b['invoice_no']) ?></td>
                            <td><?= htmlspecialchars(format_display_date($b['date'])) ?></td>
                            <td><?= htmlspecialchars($b['customer']) ?></td>
                            <td><?= htmlspecialchars($b['place_of_supply']) ?></td>
                            <td>₹<?= number_format($b['taxable_amount'], 2) ?></td>
                            <td>₹<?= number_format($b['cgst'] + $b['sgst'] + $b['igst'], 2) ?></td>
                            <td><strong>₹<?= number_format($b['total'], 2) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="panel" style="margin-bottom:24px;">
    <div class="panel-header"><h3>HSN-wise Summary</h3></div>
    <div class="panel-body">
        <?php if (empty($hsnSummary)): ?>
            <div class="empty-state" style="padding:20px;">No HSN data for this period.</div>
        <?php else: ?>
            <div class="table-scroll-wrap">
            <table class="data-table">
                <thead><tr><th>HSN/SAC</th><th>GST Rate</th><th>Total Qty</th><th>Taxable Value</th><th>CGST</th><th>SGST</th><th>IGST</th><th>Total</th></tr></thead>
                <tbody>
                    <?php foreach ($hsnSummary as $hsn): ?>
                    <tr>
                        <td><?= htmlspecialchars($hsn['hsn']) ?></td>
                        <td><?= number_format($hsn['rate'], 2) ?>%</td>
                        <td><?= number_format($hsn['qty'], 2) ?></td>
                        <td>₹<?= number_format($hsn['taxable'], 2) ?></td>
                        <td>₹<?= number_format($hsn['cgst'], 2) ?></td>
                        <td>₹<?= number_format($hsn['sgst'], 2) ?></td>
                        <td>₹<?= number_format($hsn['igst'], 2) ?></td>
                        <td><strong>₹<?= number_format($hsn['taxable'] + $hsn['cgst'] + $hsn['sgst'] + $hsn['igst'], 2) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($stateWiseSummary)): ?>
<div class="panel" style="margin-bottom:24px;">
    <div class="panel-header"><h3>State-wise Summary</h3></div>
    <div class="panel-body">
        <div class="table-scroll-wrap">
        <table class="data-table">
            <thead><tr><th>Place of Supply</th><th>Invoices</th><th>Taxable Value</th><th>CGST</th><th>SGST</th><th>IGST</th><th>Total</th></tr></thead>
            <tbody>
                <?php foreach ($stateWiseSummary as $state => $s): ?>
                <tr>
                    <td><?= htmlspecialchars($state) ?></td>
                    <td><?= $s['count'] ?></td>
                    <td>₹<?= number_format($s['taxable'], 2) ?></td>
                    <td>₹<?= number_format($s['cgst'], 2) ?></td>
                    <td>₹<?= number_format($s['sgst'], 2) ?></td>
                    <td>₹<?= number_format($s['igst'], 2) ?></td>
                    <td><strong>₹<?= number_format($s['taxable'] + $s['cgst'] + $s['sgst'] + $s['igst'], 2) ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="pdf-print-footer" style="display:none;text-align:center;padding:20px;color:#94a3b8;font-size:12px;">
    Generated on <?= date('d-m-Y H:i:s') ?> · <?= htmlspecialchars(COMPANY_NAME) ?>
</div>

<style>
@media print {
  body { background: #fff !important; margin: 0 !important; padding: 0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  .topbar, .sidebar, .page-back-link, .db-greeting, .no-print,
  .pdf-kpis, form, .btn { display: none !important; }
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
  .empty-state { display: none !important; }
}
</style>

<div class="pdf-print-header" style="display:none;">
  <h1>GSTR-1 Report</h1>
  <p>Outward Supply Summary — <?= htmlspecialchars($monthName) ?> · <?= htmlspecialchars(COMPANY_NAME) ?></p>
</div>

<?php require __DIR__ . '/layout_bottom.php'; ?>
