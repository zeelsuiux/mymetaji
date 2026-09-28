<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/company.php';

$id = (int)($_GET['id'] ?? 0);
$quotation = db_get_one('quotations', $id);
if (!$quotation) {
  die('Quotation not found.');
}
require_order_access($quotation);

$customer = null;
foreach (db_get_all('customers') as $c) {
  if (trim(mb_strtolower($c['name'])) === trim(mb_strtolower($quotation['customer'] ?? ''))) {
    $customer = $c;
    break;
  }
}

$items = normalize_line_items($quotation['items'] ?? []);
$amount = !empty($items) ? line_items_total($items) : (float)($quotation['amount'] ?? 0);
$upiQrSource = '';
$paymentQrLabel = 'Pay via UPI';
$paymentQrNote = 'Scan the QR code to make payment.';
if (COMPANY_UPI !== '') {
  $upiPayload = 'upi://pay?pa=' . rawurlencode(COMPANY_UPI) . '&pn=' . rawurlencode(COMPANY_NAME) . '&am=' . number_format($amount, 2, '.', '') . '&cu=INR&tn=' . rawurlencode('Quotation ' . ($quotation['quotation_no'] ?? ''));
  $upiQrSource = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&format=png&data=' . rawurlencode($upiPayload);
} elseif (COMPANY_UPI_QR !== '' && is_file(__DIR__ . '/' . COMPANY_UPI_QR)) {
  $upiQrSource = COMPANY_UPI_QR;
}

$defaultTerms = "1. Prices are valid until the date mentioned above.\n2. 50% advance payment required to start work; balance on delivery.\n3. Scope changes beyond agreed requirement may attract additional charges.\n4. Hosting, domain and third-party licence costs are billed separately unless stated.";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quotation <?= htmlspecialchars($quotation['quotation_no']) ?> — <?= COMPANY_NAME ?></title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/print.css">
</head>

<body class="print-page">

  <link rel="stylesheet" href="assets/enhancements.css">
  <div class="print-toolbar">
    <a href="module.php?m=quotations" class="btn btn-outline btn-sm"><?= icon('back', 15) ?> Back</a>
    <button onclick="window.print()" class="btn btn-primary"><?= icon('download', 16) ?> Download / Print PDF</button>
    <?php if (!empty($customer['phone'])): ?>
    <button class="mm-whatsapp-btn" onclick="sendWhatsApp('<?= htmlspecialchars($customer['phone']) ?>', 'Quotation #<?= htmlspecialchars($quotation['quotation_no']) ?> — Amount: ₹<?= number_format($amount, 2) ?> — Please review and confirm.')">💬 Share on WhatsApp</button>
    <?php endif; ?>
  </div>
  <script>
  function sendWhatsApp(phone, message) {
    phone = phone.replace(/[^0-9]/g, '');
    if (phone.length < 10) { alert('Invalid phone number'); return; }
    var url = 'https://wa.me/91' + phone + '?text=' + encodeURIComponent(message);
    window.open(url, '_blank');
  }
  </script>

  <div class="doc-sheet">
    <div class="doc-head">
      <div>
        <img class="document-logo" src="<?= htmlspecialchars(COMPANY_LOGO) ?>" alt="<?= htmlspecialchars(COMPANY_NAME) ?> logo">
        <div class="company-meta">
          <?= htmlspecialchars(COMPANY_TAGLINE) ?><br>
          <?= htmlspecialchars(COMPANY_ADDRESS) ?><br><br>
          <?= htmlspecialchars(COMPANY_PHONE) ?> <br> <?= htmlspecialchars(COMPANY_EMAIL) ?>
        </div>
      </div>
      <div class="doc-type">
        <h1>QUOTATION</h1>
        <div class="doc-no">#<?= htmlspecialchars($quotation['quotation_no']) ?></div>
        <div class="doc-meta">
          Issued: <?= htmlspecialchars(format_display_date($quotation['created_at'] ?? '')) ?><br>
          Valid Until: <?= htmlspecialchars(format_display_date($quotation['valid_until'] ?? '')) ?>
        </div>
      </div>
    </div>

    <div class="doc-parties">
      <div class="block">
        <h4>Prepared For</h4>
        <div class="name"><?= htmlspecialchars($quotation['customer']) ?></div>
        <div class="meta">
          <?php if ($customer): ?>
            <?= htmlspecialchars($customer['company'] ?? '') ?><br>
            <?= htmlspecialchars($customer['address'] ?? '') ?><br>
            <?php if (!empty($customer['phone'])): ?>Mobile: <?= htmlspecialchars($customer['phone']) ?><br><?php endif; ?>
          <?= !empty($customer['email']) ? 'Email: ' . htmlspecialchars($customer['email']) : '' ?>
        <?php endif; ?>
        </div>
      </div>
    </div>

    <table class="doc-table">
      <thead>
        <tr>
          <th>Description</th>
          <th class="num">Qty</th>
          <th class="num">Rate</th>
          <th class="num">Discount</th>
          <th class="num">Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($items)): ?>
          <?php foreach ($items as $item): ?>
            <tr>
              <td><?= htmlspecialchars($item['description'] ?: 'Item') ?></td>
              <td class="num"><?= htmlspecialchars((string)($item['qty'] ?? 0)) ?></td>
              <td class="num">₹<?= number_format((float)($item['price'] ?? 0), 2) ?></td>
              <td class="num"><?= (float)($item['discount_rate'] ?? 0) > 0 ? number_format((float)$item['discount_rate'], 2) . '%' : '-' ?></td>
              <td class="num">₹<?= number_format((float)($item['total'] ?? ((float)($item['qty'] ?? 0) * (float)($item['price'] ?? 0))), 2) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td>Proposed Services</td>
            <td class="num">1</td>
            <td class="num">₹<?= number_format($amount, 2) ?></td>
            <td class="num">-</td>
            <td class="num">₹<?= number_format($amount, 2) ?></td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>



    <div class="doc-totals">
      <div class="row grand"><span>Grand Total</span><span>₹<?= number_format($amount, 2) ?></span></div>
    </div>

    <?php if (COMPANY_BANK_NAME !== '' || COMPANY_BANK_ACC !== '' || COMPANY_BANK_IFSC !== ''): ?>
      <div class="doc-bank">
        <div><h4>Bank Details</h4><div><?= htmlspecialchars(COMPANY_BANK_NAME ?: '-') ?></div></div>
        <div><h4>Account Number</h4><div><?= htmlspecialchars(COMPANY_BANK_ACC ?: '-') ?></div></div>
        <div><h4>IFSC Code</h4><div><?= htmlspecialchars(COMPANY_BANK_IFSC ?: '-') ?></div></div>
      </div>
    <?php endif; ?>

    <?php if ($upiQrSource !== ''): ?>
      <div class="upi-payment-box">
        <img src="<?= htmlspecialchars($upiQrSource) ?>" alt="UPI QR Code">
        <div><strong><?= htmlspecialchars($paymentQrLabel) ?></strong><?php if (COMPANY_UPI !== ''): ?><span>UPI ID: <?= htmlspecialchars(COMPANY_UPI) ?></span><?php elseif (COMPANY_BANK_NAME !== ''): ?><span><?= htmlspecialchars(COMPANY_BANK_NAME) ?></span><?php endif; ?><small><?= htmlspecialchars($paymentQrNote) ?></small></div>
      </div>
    <?php endif; ?>
    <div class="doc-notes">
      <h4>Terms & Conditions</h4>
      <?= nl2br(htmlspecialchars(!empty($quotation['terms']) ? $quotation['terms'] : $defaultTerms)) ?>
    </div>
    <div class="doc-footer">This is a system-generated quotation from <?= COMPANY_NAME ?> ERP. Prices valid until the date mentioned above.</div>
  </div>

</body>

</html>