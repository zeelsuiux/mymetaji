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
    $profile = array_merge($profile, [
        'name' => trim((string)($_POST['name'] ?? '')),
        'tagline' => trim((string)($_POST['tagline'] ?? '')),
        'address' => trim((string)($_POST['address'] ?? '')),
        'phone' => trim((string)($_POST['phone'] ?? '')),
        'email' => trim((string)($_POST['email'] ?? '')),
        'gstin' => trim((string)($_POST['gstin'] ?? '')),
        'bank_name' => trim((string)($_POST['bank_name'] ?? '')),
        'bank_acc' => trim((string)($_POST['bank_acc'] ?? '')),
        'bank_ifsc' => trim((string)($_POST['bank_ifsc'] ?? '')),
        'upi' => trim((string)($_POST['upi'] ?? '')),
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

    if (!$errors) {
        shop_profile_save($profile);
        $success = 'Shop settings saved. New invoices and quotations will use these details.';
    }
}
require __DIR__ . '/layout_top.php';
?>
<main class="page">
  <div class="page-header"><div><h2>Shop Settings</h2><p class="text-muted">These details appear on your invoices and quotations.</p></div></div>
  <?php if ($success): ?><div class="alert"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if ($errors): ?><div class="alert alert-error"><?= htmlspecialchars(implode(' ', $errors)) ?></div><?php endif; ?>
  <div class="panel"><div class="panel-header"><h2><?= icon('settings') ?> Business Profile</h2></div><div class="panel-body"><form method="post" enctype="multipart/form-data">
    <div class="form-grid">
      <div class="form-group"><label>Shop / Business Name *</label><input name="name" required value="<?= htmlspecialchars($profile['name']) ?>"></div>
      <div class="form-group"><label>Tagline</label><input name="tagline" value="<?= htmlspecialchars($profile['tagline']) ?>"></div>
      <div class="form-group"><label>Mobile Number</label><input name="phone" value="<?= htmlspecialchars($profile['phone']) ?>"></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>"></div>
      <div class="form-group" style="grid-column:1/-1"><label>Address</label><textarea name="address"><?= htmlspecialchars($profile['address']) ?></textarea></div>
      <div class="form-group"><label>GSTIN</label><input name="gstin" value="<?= htmlspecialchars($profile['gstin']) ?>"></div>
      <div class="form-group"><label>UPI ID</label><input name="upi" value="<?= htmlspecialchars($profile['upi']) ?>"></div>
      <div class="form-group"><label>Bank Name</label><input name="bank_name" value="<?= htmlspecialchars($profile['bank_name']) ?>"></div>
      <div class="form-group"><label>Bank Account Number</label><input name="bank_acc" value="<?= htmlspecialchars($profile['bank_acc']) ?>"></div>
      <div class="form-group"><label>Bank IFSC</label><input name="bank_ifsc" value="<?= htmlspecialchars($profile['bank_ifsc']) ?>"></div>
      <div class="form-group"><label>Logo</label><input type="file" name="logo" accept="image/png,image/jpeg,image/webp"><small>PNG, JPG or WEBP, maximum 2 MB.</small></div>
    </div>
    <div class="form-actions"><button class="btn btn-primary" type="submit"><?= icon('save', 16) ?> Save Settings</button></div>
  </form></div></div>
</main>
<?php require __DIR__ . '/layout_bottom.php'; ?>
