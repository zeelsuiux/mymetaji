<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/company.php';

$id = (int)($_GET['id'] ?? 0);
$invoice = db_get_one('invoices', $id);
if (!$invoice) { die('Invoice not found.'); }
require_order_access($invoice);

$customer = null;
foreach (db_get_all('customers') as $c) {
    if (trim(mb_strtolower($c['name'])) === trim(mb_strtolower($invoice['customer'] ?? ''))) {
        $customer = $c;
        break;
    }
}

$items = normalize_line_items($invoice['items'] ?? []);
$amount = !empty($items) ? line_items_total($items) : (float)($invoice['amount'] ?? 0);
$grandTotal = !empty($invoice['grand_total']) ? (float)$invoice['grand_total'] : $amount;
$totalTaxable = !empty($invoice['total_taxable']) ? (float)$invoice['total_taxable'] : $amount;
$totalCgst = (float)($invoice['total_cgst'] ?? 0);
$totalSgst = (float)($invoice['total_sgst'] ?? 0);
$totalIgst = (float)($invoice['total_igst'] ?? 0);
$totalGst = (float)($invoice['total_gst'] ?? 0);
$isIntra = !empty($invoice['is_intra_state']);
$placeOfSupply = trim((string)($invoice['place_of_supply'] ?? ''));
$sellerState = COMPANY_SELLER_STATE;
$sellerStateCode = get_seller_state_code();
$buyerStateCode = '';
if ($placeOfSupply !== '') {
    $buyerStateCode = get_state_code_by_name($placeOfSupply);
}
$buyerState = $placeOfSupply;
$posCode = $buyerStateCode !== '' ? $buyerStateCode : '';

$paidAmount = 0.0;
foreach (db_get_all('payments') as $payment) {
    if (($payment['invoice_no'] ?? '') === ($invoice['invoice_no'] ?? '')) {
        $paidAmount += (float)($payment['amount'] ?? 0);
    }
}
$remainingAmount = max(0, $grandTotal - $paidAmount);

// Build HSN summary
$hsnSummary = [];
foreach ($items as $item) {
    $hsn = trim((string)($item['hsn_code'] ?? '')) ?: 'NA';
    $rate = (float)($item['gst_rate'] ?? 0);
    $taxableAmt = (float)($item['total'] ?? 0);
    $qty = (float)($item['qty'] ?? 0);
    $key = $hsn . '_' . $rate;
    if (!isset($hsnSummary[$key])) {
        $hsnSummary[$key] = ['hsn' => $hsn, 'uqc' => 'NOS', 'qty' => 0, 'rate' => $rate, 'taxable' => 0, 'cgst_rate' => 0, 'cgst_amt' => 0, 'sgst_rate' => 0, 'sgst_amt' => 0, 'igst_rate' => 0, 'igst_amt' => 0];
    }
    $hsnSummary[$key]['qty'] += $qty;
    $hsnSummary[$key]['taxable'] += $taxableAmt;
    $itemGst = $taxableAmt * $rate / 100;
    if ($isIntra && $rate > 0) {
        $hsnSummary[$key]['cgst_rate'] = $rate / 2;
        $hsnSummary[$key]['sgst_rate'] = $rate / 2;
        $hsnSummary[$key]['cgst_amt'] += $itemGst / 2;
        $hsnSummary[$key]['sgst_amt'] += $itemGst / 2;
    } elseif ($rate > 0) {
        $hsnSummary[$key]['igst_rate'] = $rate;
        $hsnSummary[$key]['igst_amt'] += $itemGst;
    }
}

$upiQrSource = '';
if (COMPANY_UPI !== '') {
    $upiPayload = 'upi://pay?pa=' . rawurlencode(COMPANY_UPI) . '&pn=' . rawurlencode(COMPANY_NAME) . '&am=' . number_format($grandTotal, 2, '.', '') . '&cu=INR&tn=' . rawurlencode('Invoice ' . ($invoice['invoice_no'] ?? ''));
    $upiQrSource = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&format=png&data=' . rawurlencode($upiPayload);
} elseif (COMPANY_UPI_QR !== '' && is_file(__DIR__ . '/' . COMPANY_UPI_QR)) {
    $upiQrSource = COMPANY_UPI_QR;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TAX INVOICE <?= htmlspecialchars($invoice['invoice_no']) ?> — <?= COMPANY_NAME ?></title>
<link rel="stylesheet" href="assets/style.css">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Poppins', Arial, sans-serif; font-size: 12px; color: #1a1a1a; background: #f1f5f9; }
  .print-toolbar { max-width: 210mm; margin: 0 auto 12px; padding: 10px 0; display: flex; justify-content: space-between; align-items: center; }
  .print-toolbar .btn { cursor: pointer; }
  .mm-whatsapp-btn { background: #25D366; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px; }

  .tax-invoice { width: 210mm; min-height: 297mm; margin: 0 auto 40px; background: #fff; padding: 12mm; font-size: 11px; color: #1a1a1a; line-height: 1.5; }

  /* === HEADER === */
  .inv-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px double #1a1a1a; padding-bottom: 12px; margin-bottom: 14px; }
  .inv-header .supplier-info { flex: 1; }
  .inv-header .supplier-info .company-name { font-size: 18px; font-weight: 700; color: #1a1a1a; margin-bottom: 2px; }
  .inv-header .supplier-info .company-detail { font-size: 10px; color: #444; line-height: 1.5; }
  .inv-header .supplier-info .gstin-label { font-size: 10px; color: #666; }
  .inv-header .supplier-info .gstin-value { font-size: 13px; font-weight: 700; color: #1a1a1a; letter-spacing: 1px; }
  .inv-header .invoice-title { text-align: right; }
  .inv-header .invoice-title h1 { font-size: 22px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; color: #1a1a1a; margin: 0; }
  .inv-header .invoice-title .inv-no { font-size: 13px; font-weight: 700; margin-top: 4px; }

  /* === INVOICE META === */
  .inv-meta-row { display: flex; justify-content: space-between; gap: 20px; margin-bottom: 14px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #e2e8f0; }
  .inv-meta-item { font-size: 10px; }
  .inv-meta-item .meta-label { color: #666; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }
  .inv-meta-item .meta-value { font-weight: 700; color: #1a1a1a; font-size: 11px; margin-top: 1px; }

  /* === PARTIES === */
  .inv-parties { display: flex; justify-content: space-between; gap: 20px; margin-bottom: 16px; }
  .inv-party { flex: 1; border: 1px solid #e2e8f0; padding: 10px 12px; }
  .inv-party .party-title { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #666; margin-bottom: 6px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
  .inv-party .party-name { font-weight: 700; font-size: 13px; }
  .inv-party .party-detail { font-size: 10px; color: #444; line-height: 1.6; margin-top: 3px; }
  .inv-party .party-gstin { font-size: 11px; font-weight: 700; color: #1a1a1a; margin-top: 4px; letter-spacing: 0.5px; }

  /* === ITEMS TABLE === */
  .inv-items { width: 100%; border-collapse: collapse; margin-bottom: 6px; font-size: 10px; }
  .inv-items th { background: #1a1a1a; color: #fff; font-weight: 600; text-align: center; padding: 6px 4px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid #333; }
  .inv-items td { padding: 5px 4px; border: 1px solid #ddd; text-align: center; font-size: 10px; }
  .inv-items td.num { text-align: right; }
  .inv-items td.text-left { text-align: left; }
  .inv-items tr:nth-child(even) td { background: #fafafa; }
  .inv-items .tax-header { background: #f0f0f0; font-weight: 700; font-size: 9px; }
  .inv-items .tax-header td { border: 1px solid #ccc; padding: 4px; }

  /* === HSN SUMMARY TABLE === */
  .hsn-summary { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 10px; font-size: 10px; }
  .hsn-summary th { background: #1a1a1a; color: #fff; font-weight: 600; text-align: center; padding: 5px 3px; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid #333; }
  .hsn-summary td { padding: 4px 3px; border: 1px solid #ddd; text-align: center; font-size: 9px; }
  .hsn-summary td.num { text-align: right; }
  .hsn-summary tr:nth-child(even) td { background: #fafafa; }
  .hsn-summary .hsn-total-row td { background: #f0f0f0; font-weight: 700; }

  /* === TOTALS === */
  .inv-totals-section { display: flex; justify-content: space-between; gap: 20px; margin: 12px 0; }
  .inv-amount-words { flex: 1; border: 1px solid #e2e8f0; padding: 8px 12px; font-size: 10px; }
  .inv-amount-words .words-label { font-weight: 700; text-transform: uppercase; font-size: 9px; color: #666; letter-spacing: 0.5px; }
  .inv-amount-words .words-value { font-weight: 700; font-size: 11px; margin-top: 3px; color: #1a1a1a; text-transform: capitalize; }

  .inv-grand-total { width: 280px; border: 2px solid #1a1a1a; }
  .inv-grand-total .gt-row { display: flex; justify-content: space-between; padding: 4px 10px; font-size: 10px; }
  .inv-grand-total .gt-row .gt-label { color: #444; }
  .inv-grand-total .gt-row .gt-value { font-weight: 600; }
  .inv-grand-total .gt-row.grand { background: #1a1a1a; color: #fff; font-weight: 700; font-size: 13px; padding: 6px 10px; }
  .inv-grand-total .gt-row.grand .gt-label { color: #fff; }
  .inv-grand-total .gt-row.grand .gt-value { color: #fff; }

  /* === REVERSE CHARGE === */
  .reverse-charge { margin: 10px 0; padding: 6px 12px; border: 1px solid #e2e8f0; background: #f8f9fa; font-size: 10px; }
  .reverse-charge strong { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }

  /* === DECLARATION & SIGNATORY === */
  .inv-footer-section { display: flex; justify-content: space-between; gap: 20px; margin-top: 16px; }
  .inv-declaration { flex: 1; border: 1px solid #e2e8f0; padding: 10px 12px; font-size: 9px; color: #444; line-height: 1.6; }
  .inv-declaration .decl-title { font-weight: 700; text-transform: uppercase; font-size: 9px; letter-spacing: 0.5px; margin-bottom: 4px; }
  .inv-signatory { width: 220px; text-align: center; border: 1px solid #e2e8f0; padding: 10px 12px; }
  .inv-signatory .sign-title { font-weight: 700; text-transform: uppercase; font-size: 9px; letter-spacing: 0.5px; color: #666; margin-top: 30px; border-top: 1px solid #1a1a1a; padding-top: 6px; }

  /* === PAYMENT BOX === */
  .payment-section { display: flex; gap: 16px; margin-top: 14px; flex-wrap: wrap; }
  .payment-box { display: flex; align-items: center; gap: 10px; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 10px; }
  .payment-box img { width: 50px; height: 50px; object-fit: contain; }
  .payment-box .pb-label { font-weight: 700; font-size: 11px; }
  .payment-box .pb-value { color: #25D366; font-weight: 600; font-size: 10px; }
  .payment-box .pb-note { color: #666; font-size: 9px; }

  .doc-footer { text-align: center; font-size: 9px; color: #999; margin-top: 14px; padding-top: 10px; border-top: 1px solid #e2e8f0; }

  @media print {
    body { background: #fff; }
    .print-toolbar { display: none !important; }
    .tax-invoice { box-shadow: none; width: 210mm; min-height: 297mm; margin: 0; padding: 12mm; }
    @page { size: A4; margin: 0; }
  }
</style>
</head>
<body class="print-page">

<div class="print-toolbar">
  <a href="module.php?m=invoices" class="btn btn-outline btn-sm"><?= icon('back', 15) ?> Back</a>
  <button onclick="window.print()" class="btn btn-primary"><?= icon('download', 16) ?> Download / Print PDF</button>
  <?php if (!empty($customer['phone'])): ?>
  <button class="mm-whatsapp-btn" onclick="sendWhatsApp('<?= htmlspecialchars($customer['phone']) ?>', 'Tax Invoice #<?= htmlspecialchars($invoice['invoice_no']) ?> — Amount: ₹<?= number_format($grandTotal, 2) ?> — Thank you for your purchase!')">💬 Share on WhatsApp</button>
  <?php endif; ?>
</div>
<script>
function sendWhatsApp(phone, message) {
  phone = phone.replace(/[^0-9]/g, '');
  if (phone.length < 10) { alert('Invalid phone number'); return; }
  window.open('https://wa.me/91' + phone + '?text=' + encodeURIComponent(message), '_blank');
}
</script>

<div class="tax-invoice">

  <!-- ========== HEADER ========== -->
  <div class="inv-header">
    <div class="supplier-info">
      <?php if (!empty(COMPANY_LOGO)): ?>
        <img src="<?= htmlspecialchars(COMPANY_LOGO) ?>" alt="Logo" style="max-height:50px;margin-bottom:6px;">
      <?php endif; ?>
      <div class="company-name"><?= htmlspecialchars(COMPANY_NAME) ?></div>
      <div class="company-detail">
        <?= htmlspecialchars(COMPANY_ADDRESS) ?><br>
        <?php if (COMPANY_PHONE !== '' || COMPANY_EMAIL !== ''): ?>
          <?= htmlspecialchars(COMPANY_PHONE) ?><?= COMPANY_PHONE !== '' && COMPANY_EMAIL !== '' ? ' | ' : '' ?><?= htmlspecialchars(COMPANY_EMAIL) ?>
        <?php endif; ?>
        <?php if (COMPANY_SELLER_STATE !== ''): ?><br>State: <?= htmlspecialchars(COMPANY_SELLER_STATE) ?> (<?= htmlspecialchars($sellerStateCode) ?>)<?php endif; ?>
      </div>
      <?php if (COMPANY_GSTIN !== ''): ?>
        <div style="margin-top:6px;">
          <div class="gstin-label">GSTIN</div>
          <div class="gstin-value"><?= htmlspecialchars(format_gst_number(COMPANY_GSTIN)) ?></div>
        </div>
      <?php endif; ?>
    </div>
    <div class="invoice-title">
      <h1>TAX INVOICE</h1>
      <div class="inv-no"><?= htmlspecialchars($invoice['invoice_no'] ?? '') ?></div>
    </div>
  </div>

  <!-- ========== INVOICE META ROW ========== -->
  <div class="inv-meta-row">
    <div class="inv-meta-item">
      <div class="meta-label">Invoice No.</div>
      <div class="meta-value"><?= htmlspecialchars($invoice['invoice_no'] ?? '') ?></div>
    </div>
    <div class="inv-meta-item">
      <div class="meta-label">Invoice Date</div>
      <div class="meta-value"><?= htmlspecialchars(format_display_date($invoice['created_at'] ?? '')) ?></div>
    </div>
    <?php if (!empty($invoice['due_date'])): ?>
    <div class="inv-meta-item">
      <div class="meta-label">Due Date</div>
      <div class="meta-value"><?= htmlspecialchars(format_display_date($invoice['due_date'])) ?></div>
    </div>
    <?php endif; ?>
    <div class="inv-meta-item">
      <div class="meta-label">Place of Supply</div>
      <div class="meta-value"><?= htmlspecialchars($placeOfSupply !== '' ? $placeOfSupply . ' (' . $posCode . ')' : '-') ?></div>
    </div>
    <div class="inv-meta-item">
      <div class="meta-label">Reverse Charge</div>
      <div class="meta-value">No</div>
    </div>
  </div>

  <!-- ========== PARTIES ========== -->
  <div class="inv-parties">
    <div class="inv-party" style="flex:1.2;">
      <div class="party-title">Details of Receiver (Billed To)</div>
      <div class="party-name"><?= htmlspecialchars($invoice['customer'] ?? '') ?></div>
      <div class="party-detail">
        <?php if ($customer): ?>
          <?php if (!empty($customer['company'])): ?><?= htmlspecialchars($customer['company']) ?><br><?php endif; ?>
          <?php if (!empty($customer['address'])): ?><?= htmlspecialchars($customer['address']) ?><br><?php endif; ?>
          <?php if (!empty($customer['phone'])): ?>Ph: <?= htmlspecialchars($customer['phone']) ?><?php endif; ?>
          <?php if (!empty($customer['email'])): ?><br>Email: <?= htmlspecialchars($customer['email']) ?><?php endif; ?>
          <?php if (!empty($customer['state'])): ?><br>State: <?= htmlspecialchars($customer['state']) ?><?php endif; ?>
        <?php endif; ?>
      </div>
      <?php if (!empty($customer['gstin'])): ?>
        <div class="party-gstin">GSTIN: <?= htmlspecialchars(format_gst_number($customer['gstin'])) ?></div>
      <?php else: ?>
        <div class="party-gstin" style="color:#999;">Unregistered / Composition</div>
      <?php endif; ?>
    </div>
    <div class="inv-party" style="flex:0.8;">
      <div class="party-title">Details of Supplier (Billed By)</div>
      <div class="party-name"><?= htmlspecialchars(COMPANY_NAME) ?></div>
      <div class="party-detail">
        <?= htmlspecialchars(COMPANY_ADDRESS) ?><br>
        <?php if (COMPANY_SELLER_STATE !== ''): ?>State: <?= htmlspecialchars(COMPANY_SELLER_STATE) ?> (<?= htmlspecialchars($sellerStateCode) ?>)<?php endif; ?>
      </div>
      <?php if (COMPANY_GSTIN !== ''): ?>
        <div class="party-gstin">GSTIN: <?= htmlspecialchars(format_gst_number(COMPANY_GSTIN)) ?></div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ========== ITEMS TABLE ========== -->
  <table class="inv-items">
    <thead>
      <tr>
        <th style="width:28px;">Sr.</th>
        <th>Description of Goods / Services</th>
        <?php if (count($hsnSummary) > 0): ?><th style="width:60px;">HSN/SAC</th><?php endif; ?>
        <th style="width:35px;">Qty</th>
        <th style="width:30px;">Unit</th>
        <th style="width:60px;">Rate</th>
        <th style="width:65px;">Amount</th>
        <?php if ($totalGst > 0): ?>
          <?php if ($isIntra): ?>
            <th style="width:45px;">CGST Rate</th>
            <th style="width:60px;">CGST Amt</th>
            <th style="width:45px;">SGST Rate</th>
            <th style="width:60px;">SGST Amt</th>
          <?php else: ?>
            <th style="width:50px;">IGST Rate</th>
            <th style="width:70px;">IGST Amt</th>
          <?php endif; ?>
        <?php endif; ?>
        <th style="width:70px;">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php $sr = 1; ?>
      <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
          <?php
            $itemRate = (float)($item['actual_selling_price'] ?? ($item['price'] ?? 0));
            $itemTotal = (float)($item['total'] ?? 0);
            $itemGstRate = (float)($item['gst_rate'] ?? 0);
            $itemGstAmt = round($itemTotal * $itemGstRate / 100, 2);
          ?>
          <tr>
            <td><?= $sr++ ?></td>
            <td class="text-left"><?= htmlspecialchars($item['description'] ?: 'Item') ?></td>
            <?php if (count($hsnSummary) > 0): ?><td><?= htmlspecialchars($item['hsn_code'] ?? '') ?></td><?php endif; ?>
            <td class="num"><?= (float)($item['qty'] ?? 0) ?></td>
            <td>NOS</td>
            <td class="num">₹<?= number_format($itemRate, 2) ?></td>
            <td class="num">₹<?= number_format($itemTotal, 2) ?></td>
            <?php if ($totalGst > 0): ?>
              <?php if ($isIntra): ?>
                <td class="num"><?= $itemGstRate > 0 ? number_format($itemGstRate / 2, 1) . '%' : '-' ?></td>
                <td class="num"><?= $itemGstRate > 0 ? '₹' . number_format($itemGstAmt / 2, 2) : '-' ?></td>
                <td class="num"><?= $itemGstRate > 0 ? number_format($itemGstRate / 2, 1) . '%' : '-' ?></td>
                <td class="num"><?= $itemGstRate > 0 ? '₹' . number_format($itemGstAmt / 2, 2) : '-' ?></td>
              <?php else: ?>
                <td class="num"><?= $itemGstRate > 0 ? number_format($itemGstRate, 1) . '%' : '-' ?></td>
                <td class="num"><?= $itemGstRate > 0 ? '₹' . number_format($itemGstAmt, 2) : '-' ?></td>
              <?php endif; ?>
            <?php endif; ?>
            <td class="num" style="font-weight:700;">₹<?= number_format($itemTotal, 2) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td>1</td>
          <td class="text-left">Professional Services</td>
          <?php if (count($hsnSummary) > 0): ?><td></td><?php endif; ?>
          <td class="num">1</td>
          <td>NOS</td>
          <td class="num">₹<?= number_format($amount, 2) ?></td>
          <td class="num">₹<?= number_format($amount, 2) ?></td>
          <td class="num" style="font-weight:700;">₹<?= number_format($amount, 2) ?></td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- ========== HSN SUMMARY TABLE ========== -->
  <?php if (!empty($hsnSummary)): ?>
  <table class="hsn-summary">
    <thead>
      <tr>
        <th style="width:40px;">HSN/SAC</th>
        <th>UQC</th>
        <th style="width:40px;">Total Qty</th>
        <th style="width:60px;">Taxable Value</th>
        <?php if ($isIntra): ?>
          <th style="width:45px;">CGST Rate</th>
          <th style="width:60px;">CGST Amt</th>
          <th style="width:45px;">SGST Rate</th>
          <th style="width:60px;">SGST Amt</th>
        <?php else: ?>
          <th style="width:50px;">IGST Rate</th>
          <th style="width:70px;">IGST Amt</th>
        <?php endif; ?>
        <th style="width:70px;">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($hsnSummary as $hsn): ?>
        <tr>
          <td><?= htmlspecialchars($hsn['hsn']) ?></td>
          <td><?= htmlspecialchars($hsn['uqc']) ?></td>
          <td class="num"><?= number_format($hsn['qty'], 2) ?></td>
          <td class="num">₹<?= number_format($hsn['taxable'], 2) ?></td>
          <?php if ($isIntra): ?>
            <td class="num"><?= $hsn['cgst_rate'] > 0 ? number_format($hsn['cgst_rate'], 1) . '%' : '0%' ?></td>
            <td class="num">₹<?= number_format($hsn['cgst_amt'], 2) ?></td>
            <td class="num"><?= $hsn['sgst_rate'] > 0 ? number_format($hsn['sgst_rate'], 1) . '%' : '0%' ?></td>
            <td class="num">₹<?= number_format($hsn['sgst_amt'], 2) ?></td>
          <?php else: ?>
            <td class="num"><?= $hsn['igst_rate'] > 0 ? number_format($hsn['igst_rate'], 1) . '%' : '0%' ?></td>
            <td class="num">₹<?= number_format($hsn['igst_amt'], 2) ?></td>
          <?php endif; ?>
          <td class="num" style="font-weight:700;">₹<?= number_format($hsn['taxable'] + $hsn['cgst_amt'] + $hsn['sgst_amt'] + $hsn['igst_amt'], 2) ?></td>
        </tr>
      <?php endforeach; ?>
      <tr class="hsn-total-row">
        <td colspan="2" style="text-align:left;font-weight:700;">TOTAL</td>
        <td class="num" style="font-weight:700;"><?= number_format(array_sum(array_column($hsnSummary, 'qty')), 2) ?></td>
        <td class="num" style="font-weight:700;">₹<?= number_format($totalTaxable, 2) ?></td>
        <?php if ($isIntra): ?>
          <td></td>
          <td class="num" style="font-weight:700;">₹<?= number_format($totalCgst, 2) ?></td>
          <td></td>
          <td class="num" style="font-weight:700;">₹<?= number_format($totalSgst, 2) ?></td>
        <?php else: ?>
          <td></td>
          <td class="num" style="font-weight:700;">₹<?= number_format($totalIgst, 2) ?></td>
        <?php endif; ?>
        <td class="num" style="font-weight:700;">₹<?= number_format($grandTotal, 2) ?></td>
      </tr>
    </tbody>
  </table>
  <?php endif; ?>

  <!-- ========== REVERSE CHARGE ========== -->
  <div class="reverse-charge">
    <strong>Whether tax is payable under reverse charge:</strong> No
  </div>

  <!-- ========== TOTALS + AMOUNT IN WORDS ========== -->
  <div class="inv-totals-section">
    <div class="inv-amount-words">
      <div class="words-label">Amount Chargeable (in words)</div>
      <div class="words-value"><?= htmlspecialchars(amount_in_words($grandTotal)) ?></div>
    </div>
    <div class="inv-grand-total">
      <div class="gt-row"><span class="gt-label">Taxable Value</span><span class="gt-value">₹<?= number_format($totalTaxable, 2) ?></span></div>
      <?php if ($isIntra): ?>
        <?php if ($totalCgst > 0): ?>
          <div class="gt-row"><span class="gt-label">CGST</span><span class="gt-value">₹<?= number_format($totalCgst, 2) ?></span></div>
        <?php endif; ?>
        <?php if ($totalSgst > 0): ?>
          <div class="gt-row"><span class="gt-label">SGST / UTGST</span><span class="gt-value">₹<?= number_format($totalSgst, 2) ?></span></div>
        <?php endif; ?>
      <?php else: ?>
        <?php if ($totalIgst > 0): ?>
          <div class="gt-row"><span class="gt-label">IGST</span><span class="gt-value">₹<?= number_format($totalIgst, 2) ?></span></div>
        <?php endif; ?>
      <?php endif; ?>
      <div class="gt-row grand"><span class="gt-label">GRAND TOTAL</span><span class="gt-value">₹<?= number_format($grandTotal, 2) ?></span></div>
    </div>
  </div>

  <!-- ========== PAYMENT DETAILS ========== -->
  <?php if ($paidAmount > 0 || $remainingAmount > 0): ?>
  <div style="margin:8px 0;padding:6px 12px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:4px;font-size:10px;display:flex;gap:24px;">
    <div><strong>Total Paid:</strong> ₹<?= number_format($paidAmount, 2) ?></div>
    <div><strong>Balance Due:</strong> ₹<?= number_format($remainingAmount, 2) ?></div>
  </div>
  <?php endif; ?>

  <!-- ========== NOTES ========== -->
  <?php if (!empty($invoice['notes'])): ?>
  <div style="margin:10px 0;padding:6px 12px;border:1px solid #e2e8f0;font-size:10px;">
    <strong>Notes:</strong> <?= htmlspecialchars($invoice['notes']) ?>
  </div>
  <?php endif; ?>

  <!-- ========== DECLARATION + SIGNATORY ========== -->
  <div class="inv-footer-section">
    <div class="inv-declaration">
      <div class="decl-title">Declaration</div>
      We hereby certify that this invoice is true and correct and the goods/services mentioned above were supplied / will be supplied as stated. The tax charged on this invoice is correct and has been charged in accordance with the provisions of the GST Act and Rules.
    </div>
    <div class="inv-signatory">
      <div style="font-size:9px;color:#666;">for <strong><?= htmlspecialchars(COMPANY_NAME) ?></strong></div>
      <div style="margin-top:30px;border-top:1px solid #1a1a1a;padding-top:6px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Authorized Signatory</div>
    </div>
  </div>

  <!-- ========== BANK / UPI ========== -->
  <?php if (COMPANY_BANK_NAME !== '' || COMPANY_UPI !== ''): ?>
  <div class="payment-section">
    <?php if (COMPANY_BANK_NAME !== '' || COMPANY_BANK_ACC !== '' || COMPANY_BANK_IFSC !== ''): ?>
    <div class="payment-box" style="flex:1;">
      <div>
        <div class="pb-label">Bank Details</div>
        <div style="font-size:10px;color:#444;margin-top:2px;">
          Bank: <?= htmlspecialchars(COMPANY_BANK_NAME ?: '-') ?><br>
          A/c No: <?= htmlspecialchars(COMPANY_BANK_ACC ?: '-') ?><br>
          IFSC: <?= htmlspecialchars(COMPANY_BANK_IFSC ?: '-') ?>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php if ($upiQrSource !== ''): ?>
    <div class="payment-box">
      <img src="<?= htmlspecialchars($upiQrSource) ?>" alt="UPI QR">
      <div>
        <div class="pb-label">Pay via UPI</div>
        <?php if (COMPANY_UPI !== ''): ?><div class="pb-value">UPI ID: <?= htmlspecialchars(COMPANY_UPI) ?></div><?php endif; ?>
        <div class="pb-note">Scan to pay</div>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <div class="doc-footer">
    This is a computer-generated Tax Invoice · <?= htmlspecialchars(COMPANY_NAME) ?> · GSTIN: <?= htmlspecialchars(format_gst_number(COMPANY_GSTIN)) ?>
  </div>

</div>

</body>
</html>
