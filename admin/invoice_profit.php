<?php
require_once __DIR__ . '/auth.php';
require_admin();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/company.php';

$id = (int)($_GET['id'] ?? 0);
$invoice = db_get_one('invoices', $id);
if (!$invoice) { die('Invoice not found.'); }
require_order_access($invoice);

$items = normalize_line_items($invoice['items'] ?? []);
$productsById = [];
foreach (db_get_all('products') as $product) {
    $productsById[(int)($product['id'] ?? 0)] = $product;
}

$totalSelling = 0.0;
$totalDiscount = 0.0;
$totalPurchase = 0.0;
$totalProfitPlaceholder = 0.0;

$profitRows = [];
foreach ($items as $item) {
    $qty = (float)($item['qty'] ?? 0);
    $sellingPrice = (float)($item['price'] ?? 0);
    $discountRate = (float)($item['discount_rate'] ?? $item['discount_percent'] ?? $item['discount'] ?? 0);
    $discountRate = min(100, max(0, $discountRate));

    $grossSelling = $qty * $sellingPrice;
    $discountAmount = ($grossSelling * $discountRate) / 100;
    $netSelling = $grossSelling - $discountAmount;

    $productId = (int)($item['product_id'] ?? 0);
    $product = $productsById[$productId] ?? null;
    $purchasePrice = $product ? (float)($product['purchase_price'] ?? 0) : 0.0;
    $purchaseCost = $qty * $purchasePrice;
    $profit = $netSelling - $purchaseCost;

    $totalSelling += $grossSelling;
    $totalDiscount += $discountAmount;
    $totalPurchase += $purchaseCost;
    $totalProfitPlaceholder += $profit;

    $profitRows[] = [
        'description' => $item['description'] ?? ($product['name'] ?? 'Item'),
        'qty' => $qty,
        'selling_price' => $sellingPrice,
        'discount_rate' => $discountRate,
        'discount_amount' => $discountAmount,
        'net_selling' => $netSelling,
        'purchase_price' => $purchasePrice,
        'purchase_cost' => $purchaseCost,
        'profit' => $profit,
    ];
}
$totalProfit = $totalProfitPlaceholder;
$invoiceTotal = !empty($items) ? line_items_total($items) : (float)($invoice['amount'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profit Report <?= htmlspecialchars($invoice['invoice_no'] ?? '') ?> — <?= COMPANY_NAME ?></title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/print.css">
<style>
  .profit-report-note { margin: 16px 0 22px; font-size: 13px; color: #64748b; }
  .profit-positive { font-weight: 700; }
  .profit-negative { font-weight: 700; }
  .profit-summary { margin-top: 22px; margin-left: auto; width: min(430px, 100%); }
  .profit-summary .row { display:flex; justify-content:space-between; gap:20px; padding:8px 0; }
  .profit-summary .grand { border-top:2px solid #0f172a; margin-top:6px; padding-top:12px; font-size:18px; font-weight:700; }
  @media print {
    .print-toolbar { display:none !important; }
    .doc-sheet { box-shadow:none !important; margin:0 !important; }
  }
</style>
</head>
<body class="print-page">

<div class="print-toolbar">
  <a href="module.php?m=invoices" class="btn btn-outline btn-sm"><?= icon('back', 15) ?> Back</a>
  <button onclick="window.print()" class="btn btn-primary"><?= icon('download', 16) ?> Download / Print PDF</button>
</div>

<div class="doc-sheet">
  <div class="doc-head">
    <div>
      <img class="document-logo" src="assets/logo.png" alt="<?= htmlspecialchars(COMPANY_NAME) ?> logo">
      <div class="company-meta">
        <?= htmlspecialchars(COMPANY_TAGLINE) ?><br>
        <?= htmlspecialchars(COMPANY_ADDRESS) ?><br>
        <?= htmlspecialchars(COMPANY_PHONE) ?> · <?= htmlspecialchars(COMPANY_EMAIL) ?>
      </div>
    </div>
    <div class="doc-type">
      <h1>PROFIT REPORT</h1>
      <div class="doc-no">#<?= htmlspecialchars($invoice['invoice_no'] ?? '') ?></div>
      <div class="doc-meta">
        Customer: <?= htmlspecialchars($invoice['customer'] ?? '-') ?><br>
        Issued: <?= htmlspecialchars(format_display_date($invoice['created_at'] ?? '')) ?>
      </div>
    </div>
  </div>

  <div class="profit-report-note">
    Internal profit report. Profit is calculated product-wise as: Net Selling Amount after Discount − Purchase Cost.
  </div>

  <table class="doc-table">
    <thead>
      <tr>
        <th>Product</th>
        <th class="num">Qty</th>
        <th class="num">Selling Rate</th>
        <th class="num">Discount</th>
        <th class="num">Net Selling</th>
        <th class="num">Purchase Rate</th>
        <th class="num">Purchase Cost</th>
        <th class="num">Profit</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($profitRows)): ?>
        <?php foreach ($profitRows as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td class="num"><?= number_format($row['qty'], 2) ?></td>
            <td class="num">₹<?= number_format($row['selling_price'], 2) ?></td>
            <td class="num"><?= $row['discount_rate'] > 0 ? number_format($row['discount_rate'], 2) . '%<br>₹' . number_format($row['discount_amount'], 2) : '-' ?></td>
            <td class="num">₹<?= number_format($row['net_selling'], 2) ?></td>
            <td class="num">₹<?= number_format($row['purchase_price'], 2) ?></td>
            <td class="num">₹<?= number_format($row['purchase_cost'], 2) ?></td>
            <td class="num <?= $row['profit'] >= 0 ? 'profit-positive' : 'profit-negative' ?>">₹<?= number_format($row['profit'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="8">No product items found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="profit-summary">
    <div class="row"><span>Gross Selling</span><span>₹<?= number_format($totalSelling, 2) ?></span></div>
    <div class="row"><span>Total Discount</span><span>₹<?= number_format($totalDiscount, 2) ?></span></div>
    <div class="row"><span>Net Selling / Bill Total</span><span>₹<?= number_format($invoiceTotal, 2) ?></span></div>
    <div class="row"><span>Total Purchase Cost</span><span>₹<?= number_format($totalPurchase, 2) ?></span></div>
    <div class="row grand"><span>Total Bill Profit</span><span>₹<?= number_format($totalProfit, 2) ?></span></div>
  </div>

  <div class="doc-footer">Internal profit report generated by <?= COMPANY_NAME ?> ERP.</div>
</div>

</body>
</html>
