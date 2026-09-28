<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/company.php';

$module = ($_GET['m'] ?? '') === 'credit_notes' ? 'credit_notes' : 'debit_notes';
$note = db_get_one($module, (int)($_GET['id'] ?? 0));
if (!$note) die('Note not found.');
if (!is_admin() && !member_can_access_record($note)) die('You do not have permission to view this note.');

$customer = null;
foreach (db_get_all('customers') as $row) {
    if (strcasecmp(trim((string)($row['name'] ?? '')), trim((string)($note['customer'] ?? ''))) === 0) {
        $customer = $row;
        break;
    }
}
$noteTitle = $module === 'credit_notes' ? 'CREDIT NOTE' : 'DEBIT NOTE';
$accent = $module === 'credit_notes' ? '#7ab52d' : '#d46a3a';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($noteTitle . ' ' . ($note['note_no'] ?? '')) ?> - <?= htmlspecialchars(COMPANY_NAME) ?></title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/print.css">
<style>
.note-print-accent { border-color: <?= htmlspecialchars($accent) ?> !important; }
.note-print-number { color: <?= htmlspecialchars($accent) ?> !important; }
.note-summary { display:flex; justify-content:flex-end; margin-top:24px; }
.note-summary-box { min-width:260px; padding:18px 20px; border-radius:10px; background:#f7faf5; border:1px solid #dfe7dc; }
.note-summary-box .row { display:flex; justify-content:space-between; gap:30px; color:#788278; font-size:13px; }
.note-summary-box .grand { margin-top:10px; padding-top:10px; border-top:1px solid #dfe7dc; color:#172017; font-weight:800; font-size:18px; }
</style>
</head>
<body class="print-page">
<div class="print-toolbar">
  <a href="module.php?m=<?= htmlspecialchars($module) ?>" class="btn btn-outline btn-sm"><?= icon('back', 15) ?> Back</a>
  <button onclick="window.print()" class="btn btn-primary"><?= icon('download', 16) ?> Download / Print PDF</button>
</div>
<div class="doc-sheet">
  <div class="doc-head note-print-accent">
    <div>
      <img class="document-logo" src="<?= htmlspecialchars(COMPANY_LOGO) ?>" alt="<?= htmlspecialchars(COMPANY_NAME) ?> logo">
      <div class="company-meta"><?= htmlspecialchars(COMPANY_TAGLINE) ?><br><?= htmlspecialchars(COMPANY_ADDRESS) ?><br><?= htmlspecialchars(COMPANY_PHONE) ?> · <?= htmlspecialchars(COMPANY_EMAIL) ?></div>
    </div>
    <div class="doc-type"><h1><?= $noteTitle ?></h1><div class="doc-no note-print-number">#<?= htmlspecialchars($note['note_no'] ?? '') ?></div><div class="doc-meta">Date: <?= htmlspecialchars(format_display_date($note['date'] ?? $note['created_at'] ?? '')) ?><?php if (!empty($note['invoice_no'])): ?><br>Invoice: <?= htmlspecialchars($note['invoice_no']) ?><?php endif; ?></div></div>
  </div>
  <div class="doc-parties"><div class="block"><h4>Customer</h4><div class="name"><?= htmlspecialchars($note['customer'] ?? '-') ?></div><div class="meta"><?php if ($customer): ?><?= htmlspecialchars($customer['company'] ?? '') ?><br><?= htmlspecialchars($customer['address'] ?? '') ?><br><?php if (!empty($customer['phone'])): ?>Mobile: <?= htmlspecialchars($customer['phone']) ?><br><?php endif; ?><?= !empty($customer['email']) ? 'Email: ' . htmlspecialchars($customer['email']) : '' ?><?php endif; ?></div></div></div>
  <table class="doc-table"><thead><tr><th>Description</th><th class="num">Amount</th></tr></thead><tbody><tr><td><?= htmlspecialchars($note['reason'] ?? $noteTitle) ?></td><td class="num">₹<?= number_format((float)($note['amount'] ?? 0), 2) ?></td></tr></tbody></table>
  <?php if (!empty($note['notes'])): ?><div class="doc-notes"><h4>Notes</h4><?= nl2br(htmlspecialchars($note['notes'])) ?></div><?php endif; ?>
  <div class="note-summary"><div class="note-summary-box"><div class="row grand"><span>Total Amount</span><span>₹<?= number_format((float)($note['amount'] ?? 0), 2) ?></span></div></div></div>
  <div class="doc-footer">This is a system-generated <?= strtolower($noteTitle) ?> from <?= htmlspecialchars(COMPANY_NAME) ?> ERP.</div>
</div>
</body>
</html>
