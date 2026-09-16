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

// Try to find the matching customer record for extra contact details.
$customer = null;
foreach (db_get_all('customers') as $c) {
    if (trim(mb_strtolower($c['name'])) === trim(mb_strtolower($invoice['customer'] ?? ''))) {
        $customer = $c;
        break;
    }
}

$items = normalize_line_items($invoice['items'] ?? []);
$amount = !empty($items) ? line_items_total($items) : (float)($invoice['amount'] ?? 0);
$paidAmount = 0.0;
foreach (db_get_all('payments') as $payment) {
  if (($payment['invoice_no'] ?? '') === ($invoice['invoice_no'] ?? '')) {
    $paidAmount += (float)($payment['amount'] ?? 0);
  }
}
$remainingAmount = max(0, $amount - $paidAmount);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice <?= htmlspecialchars($invoice['invoice_no']) ?> — <?= COMPANY_NAME ?></title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/print.css">
</head>
<body class="print-page">

<div class="print-toolbar">
  <a href="module.php?m=invoices" class="btn btn-outline btn-sm"><?= icon('back', 15) ?> Back</a>
  <button onclick="window.print()" class="btn btn-primary"><?= icon('download', 16) ?> Download / Print PDF</button>
</div>

<div class="doc-sheet">
  <div class="doc-head">
    <div>
      <img class="document-logo" src="<?= htmlspecialchars(COMPANY_LOGO) ?>" alt="<?= htmlspecialchars(COMPANY_NAME) ?> logo">
      <div class="company-meta">
        <?= htmlspecialchars(COMPANY_TAGLINE) ?><br>
        <?= htmlspecialchars(COMPANY_ADDRESS) ?><br>
        <?= htmlspecialchars(COMPANY_PHONE) ?> · <?= htmlspecialchars(COMPANY_EMAIL) ?>
      </div>
    </div>
    <div class="doc-type">
      <h1>INVOICE</h1>
      <div class="doc-no">#<?= htmlspecialchars($invoice['invoice_no']) ?></div>
      <div class="doc-meta">
        Issued: <?= htmlspecialchars(format_display_date($invoice['created_at'] ?? '')) ?><?php if (!empty($invoice['due_date'])): ?><br>
        Due: <?= htmlspecialchars(format_display_date($invoice['due_date'])) ?><?php endif; ?>
      </div>
    </div>
  </div>


  <div class="doc-parties">
    <div class="block">
      <h4>Billed To</h4>
      <div class="name"><?= htmlspecialchars($invoice['customer']) ?></div>
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
      <tr><th>Description</th><th class="num">Qty</th><th class="num">Selling Rate</th><th class="num">Actual Selling</th><th class="num">Discount</th><th class="num">Amount</th></tr>
    </thead>
    <tbody>
      <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['description'] ?: 'Item') ?></td>
            <td class="num"><?= htmlspecialchars((string)($item['qty'] ?? 0)) ?></td>
            <td class="num">₹<?= number_format((float)($item['price'] ?? 0), 2) ?></td>
            <td class="num">₹<?= number_format((float)($item['actual_selling_price'] ?? ($item['price'] ?? 0)), 2) ?></td>
            <td class="num"><?= (float)($item['discount_rate'] ?? 0) > 0 ? number_format((float)$item['discount_rate'], 2) . '%' : '-' ?></td>
            <td class="num">₹<?= number_format((float)($item['total'] ?? ((float)($item['qty'] ?? 0) * (float)($item['actual_selling_price'] ?? $item['price'] ?? 0))), 2) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td>Professional Services</td>
          <td class="num">1</td>
          <td class="num">₹<?= number_format($amount, 2) ?></td>
          <td class="num">₹<?= number_format($amount, 2) ?></td>
          <td class="num">-</td>
          <td class="num">₹<?= number_format($amount, 2) ?></td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <?php if (!empty($invoice['notes'])): ?>
    <div class="doc-notes">
      <h4>Notes</h4>
      <?= nl2br(htmlspecialchars($invoice['notes'])) ?>
    </div>
  <?php endif; ?>

  <div class="doc-totals">
    <div class="row"><span>Paid Amount</span><span>₹<?= number_format($paidAmount, 2) ?></span></div>
    <div class="row"><span>Remaining Amount</span><span>₹<?= number_format($remainingAmount, 2) ?></span></div>
    <div class="row grand"><span>Grand Total</span><span>₹<?= number_format($amount, 2) ?></span></div>
  </div>

  <div class="doc-footer">This is a system-generated invoice from <?= COMPANY_NAME ?> ERP.</div>
</div>

</body>
</html>
