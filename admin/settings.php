<?php
require_once __DIR__ . '/auth.php';
require_admin();
require_once __DIR__ . '/icons.php';

$pageTitle = 'Shop Settings';
$activeModule = 'settings';
$profile = shop_profile();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Bug #1: Verify CSRF token
    csrf_verify();
    $profile = array_merge($profile, [
        'name' => trim((string)($_POST['name'] ?? '')),
        'tagline' => trim((string)($_POST['tagline'] ?? '')),
        'address' => trim((string)($_POST['address'] ?? '')),
        'phone' => trim((string)($_POST['phone'] ?? '')),
        'email' => trim((string)($_POST['email'] ?? '')),
        'gstin' => strtoupper(trim((string)($_POST['gstin'] ?? ''))),
        'seller_state' => trim((string)($_POST['seller_state'] ?? '')),
        'seller_state_code' => trim((string)($_POST['seller_state_code'] ?? '')),
        'bank_name' => trim((string)($_POST['bank_name'] ?? '')),
        'bank_acc' => trim((string)($_POST['bank_acc'] ?? '')),
        'bank_ifsc' => trim((string)($_POST['bank_ifsc'] ?? '')),
        'upi' => trim((string)($_POST['upi'] ?? '')),
        'upi_qr' => $profile['upi_qr'] ?? '',
        'cash_opening_balance' => max(0, (float)($_POST['cash_opening_balance'] ?? 0)),
        'bank_opening_balance' => max(0, (float)($_POST['bank_opening_balance'] ?? 0)),
    ]);
    if ($profile['name'] === '') $errors[] = 'Shop name is required.';
    if ($profile['email'] !== '' && !filter_var($profile['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';

    if (!empty($_FILES['logo']['name']) && ($_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp'];
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['logo']['tmp_name']);
        if (!isset($allowed[$mime])) $errors[] = 'Logo must be a PNG, JPG or WEBP image.';
        elseif ((int)$_FILES['logo']['size'] > 2 * 1024 * 1024) $errors[] = 'Logo must be smaller than 2 MB.';
        else {
            $filename = 'shop-logo.' . $allowed[$mime];
            $assetsDirectory = __DIR__ . '/shops/' . panel_tenant_code() . '/assets';
            if (!is_dir($assetsDirectory)) mkdir($assetsDirectory, 0755, true);
            $destination = $assetsDirectory . '/' . $filename;
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $destination)) $profile['logo'] = 'assets/' . $filename;
            else $errors[] = 'Unable to save the logo.';
        }
    }

    if (!empty($_FILES['upi_qr']['name']) && ($_FILES['upi_qr']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
      $allowedQr = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp'];
      $mimeQr = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['upi_qr']['tmp_name']);
      if (!isset($allowedQr[$mimeQr])) $errors[] = 'UPI QR must be a PNG, JPG or WEBP image.';
      elseif ((int)$_FILES['upi_qr']['size'] > 2 * 1024 * 1024) $errors[] = 'UPI QR must be smaller than 2 MB.';
      else {
        $assetsDirectory = __DIR__ . '/shops/' . panel_tenant_code() . '/assets';
        if (!is_dir($assetsDirectory)) mkdir($assetsDirectory, 0755, true);
        $destination = $assetsDirectory . '/upi-qr.' . $allowedQr[$mimeQr];
        if (move_uploaded_file($_FILES['upi_qr']['tmp_name'], $destination)) $profile['upi_qr'] = 'assets/upi-qr.' . $allowedQr[$mimeQr];
        else $errors[] = 'Unable to save the UPI QR image.';
      }
    }

    if (!$errors) {
        shop_profile_save($profile);
        $success = 'Shop settings saved. New invoices and quotations will use these details.';
    }
}
require_once __DIR__ . '/helpers.php';
require __DIR__ . '/layout_top.php';
?>
<main class="page">
  <a href="index.php" class="page-back-link">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Back to Dashboard
  </a>
  <div class="db-greeting" style="margin-bottom:20px;">
    <div>
      <h2>⚙️ Shop Settings</h2>
      <p>These details appear on your invoices and quotations.</p>
    </div>
  </div>
  <?php if ($success): ?><div class="alert">✓ <?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if ($errors): ?><div class="alert alert-error">✕ <?= htmlspecialchars(implode(' ', $errors)) ?></div><?php endif; ?>
  <div class="panel"><div class="panel-header" style="background:linear-gradient(135deg, #f8fafc, #f0f4ff);"><h2><?= icon('settings') ?> Business Profile</h2></div><div class="panel-body"><form method="post" enctype="multipart/form-data"><?= csrf_field() ?>
    <div class="form-grid">
      <div class="form-group"><label>Shop / Business Name *</label><input name="name" required value="<?= htmlspecialchars($profile['name']) ?>"></div>
      <div class="form-group"><label>Tagline</label><input name="tagline" value="<?= htmlspecialchars($profile['tagline']) ?>"></div>
      <div class="form-group"><label>Mobile Number</label><input name="phone" value="<?= htmlspecialchars($profile['phone']) ?>"></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>"></div>
      <div class="form-group" style="grid-column:1/-1"><label>Address</label><textarea name="address"><?= htmlspecialchars($profile['address']) ?></textarea></div>
      <div class="form-group"><label>GSTIN</label><input name="gstin" value="<?= htmlspecialchars($profile['gstin']) ?>" placeholder="22AAAAA0000A1Z5"></div>
      <div class="form-group"><label>Seller State (for GST)</label><select name="seller_state" id="seller-state-select"><option value="">Select State</option><?php foreach (indian_states() as $code => $name): ?><option value="<?= htmlspecialchars($name) ?>" data-code="<?= htmlspecialchars($code) ?>" <?= trim((string)($profile['seller_state'] ?? '')) === $name ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option><?php endforeach; ?></select><input type="hidden" name="seller_state_code" id="seller-state-code" value="<?= htmlspecialchars((string)($profile['seller_state_code'] ?? '')) ?>"><small>Your business state — used for CGST/SGST vs IGST calculation.</small></div>
      <div class="form-group"><label>GPay UPI ID (for scan-to-pay)</label><input name="upi" placeholder="yourname@oksbi / yourname@okaxis" value="<?= htmlspecialchars($profile['upi']) ?>"><small>Required for a QR that opens direct payment in Google Pay. Bank account and IFSC alone cannot create a direct GPay payment QR.</small></div>
      <div class="form-group"><label>UPI QR Code</label><input type="file" name="upi_qr" accept="image/png,image/jpeg,image/webp"><small>Upload the QR image to print on invoices. PNG, JPG or WEBP, maximum 2 MB.</small><?php if (!empty($profile['upi_qr'])): ?><small class="settings-file-note">Current QR image is saved.</small><?php endif; ?></div>
      <div class="form-group"><label>Bank Name</label><input name="bank_name" value="<?= htmlspecialchars($profile['bank_name']) ?>"></div>
      <div class="form-group"><label>Bank Account Number</label><input name="bank_acc" value="<?= htmlspecialchars($profile['bank_acc']) ?>"></div>
      <div class="form-group"><label>Bank IFSC</label><input name="bank_ifsc" value="<?= htmlspecialchars($profile['bank_ifsc']) ?>"></div>
      <div class="form-group"><label>Logo</label><input type="file" name="logo" accept="image/png,image/jpeg,image/webp"><small>PNG, JPG or WEBP, maximum 2 MB.</small></div>
      <div class="form-group"><label>Cash Opening Balance (₹)</label><input type="number" name="cash_opening_balance" step="0.01" min="0" value="<?= htmlspecialchars((string)($profile['cash_opening_balance'] ?? 0)) ?>"><small>Starting cash-in-hand balance. Set this once at the beginning.</small></div>
      <div class="form-group"><label>Bank / Online Opening Balance (₹)</label><input type="number" name="bank_opening_balance" step="0.01" min="0" value="<?= htmlspecialchars((string)($profile['bank_opening_balance'] ?? 0)) ?>"><small>Starting bank/UPI balance. Set this once at the beginning.</small></div>
    </div>
    <div class="form-actions"><button class="btn btn-primary" type="submit"><?= icon('save', 16) ?> Save Settings</button></div>
  </form></div></div>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    var stateSelect = document.getElementById('seller-state-select');
    var stateCodeInput = document.getElementById('seller-state-code');
    if (stateSelect && stateCodeInput) {
      stateSelect.addEventListener('change', function() {
        var opt = stateSelect.options[stateSelect.selectedIndex];
        stateCodeInput.value = opt ? (opt.dataset.code || '') : '';
      });
    }
  });
  </script>
</main>
<?php require __DIR__ . '/layout_bottom.php'; ?>
