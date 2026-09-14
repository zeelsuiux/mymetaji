<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';

$m = $_GET['m'] ?? '';
$action = $_GET['action'] ?? 'list';
if (!isset($MODULES[$m])) {
  http_response_code(404);
  die('Module not found.');
}
if (!can_access_module($m)) {
  http_response_code(403);
  die('You do not have permission to access this module.');
}
if ($m === 'team') require_admin();
if (in_array($action, ['new', 'edit', 'save', 'delete'], true) && !can_manage_module($m)) {
  $customerAddAllowed = $m === 'customers' && can_add_customer_as_subadmin($m, $action, $_POST['id'] ?? $_GET['id'] ?? '');
  if (!($m === 'customers' && $action === 'edit' && is_admin()) && !$customerAddAllowed) {
    http_response_code(403);
    die('You do not have permission to manage this module.');
  }
}
if (is_subadmin() && $m === 'customers' && in_array($action, ['edit', 'delete'], true)) {
  http_response_code(403);
  die('Subadmin can only add customers.');
}
if (in_array($action, ['new', 'edit', 'save', 'delete'], true)) {
  require_active_license();
}

$config = $MODULES[$m];
$activeModule = $m;
$isPopup = ($_GET['popup'] ?? '') === '1';
$errors = [];


if (in_array($m, ['leads', 'tasks'], true) && $action === 'list' && ($_GET['view'] ?? '') !== 'list') {
  header('Location: kanban.php?m=' . rawurlencode($m));
  exit;
}

function module_record_amount($row, $module)
{
  if (in_array($module, ['quotations', 'invoices'], true) && isset($row['items'])) {
    $items = normalize_line_items($row['items']);
    if (!empty($items)) {
      return (float)line_items_total($items);
    }
  }
  return (float)($row['amount'] ?? 0);
}

function module_product_selling_price($product)
{
  if (!is_array($product)) return 0.0;
  foreach (['selling_price', 'sale_price', 'sellingPrice', 'price'] as $key) {
    if (isset($product[$key]) && $product[$key] !== '' && is_numeric($product[$key])) {
      return max(0, (float)$product[$key]);
    }
  }
  return 0.0;
}

function module_customer_id($customerName)
{
  $name = trim((string)$customerName);
  if ($name === '') return 0;
  foreach (db_get_all('customers') as $customer) {
    if (strcasecmp(trim((string)($customer['name'] ?? '')), $name) === 0) {
      return (int)($customer['id'] ?? 0);
    }
  }
  return 0;
}

function module_wholeseller_id($wholesellerName)
{
  $name = trim((string)$wholesellerName);
  if ($name === '') return 0;
  foreach (db_get_all('team') as $member) {
    if (strcasecmp((string)($member['role'] ?? ''), 'Admin') === 0) continue;
    if (strcasecmp(trim((string)($member['name'] ?? '')), $name) === 0) {
      return (int)($member['id'] ?? 0);
    }
  }
  return 0;
}

function module_invoice_party_link($invoice)
{
  $name = trim((string)($invoice['customer'] ?? ''));
  if ($name === '') return null;

  $type = strtolower(trim((string)($invoice['customer_type'] ?? '')));
  if ($type === 'wholeseller' || $type === 'team' || $type === 'member') {
    $id = module_wholeseller_id($name);
    if ($id > 0) return 'team_detail.php?id=' . $id;
  }
  if ($type === 'customer') {
    $id = module_customer_id($name);
    if ($id > 0) return 'customer_detail.php?id=' . $id;
  }

  // Backward-compatible fallback for older invoices without customer_type.
  $wholesellerId = module_wholeseller_id($name);
  if ($wholesellerId > 0) return 'team_detail.php?id=' . $wholesellerId;
  $customerId = module_customer_id($name);
  if ($customerId > 0) return 'customer_detail.php?id=' . $customerId;
  return null;
}

function module_invoice_customer_allowed($customerName)
{
  $name = trim((string)$customerName);
  if ($name === '') return false;

  foreach (db_get_all('customers') as $customer) {
    if (strcasecmp(trim((string)($customer['name'] ?? '')), $name) === 0) {
      return true;
    }
  }

  foreach (db_get_all('team') as $member) {
    // Only non-Admin team members are invoice customers (Wholesellers).
    if (strcasecmp(trim((string)($member['role'] ?? '')), 'Admin') === 0) continue;
    if (strcasecmp(trim((string)($member['name'] ?? '')), $name) === 0) {
      return true;
    }
  }

  return false;
}

function module_invoice_paid($invoiceNo)
{
  $paid = 0.0;
  foreach (db_get_all('payments') as $payment) {
    if (($payment['invoice_no'] ?? '') === $invoiceNo) {
      $paid += (float)($payment['amount'] ?? 0);
    }
  }
  return $paid;
}

function module_invoice_status($invoice)
{
  $amount = (float)($invoice['amount'] ?? 0);
  $paid = module_invoice_paid((string)($invoice['invoice_no'] ?? ''));
  $pending = max(0, $amount - $paid);
  if ($pending <= 0) {
    return 'Paid';
  }
  if (!empty($invoice['due_date']) && $invoice['due_date'] < date('Y-m-d')) {
    return 'Over due';
  }
  return $paid > 0 ? 'Partly Paid' : 'Full Pending';
}

function module_invoice_profit($invoice)
{
  $items = normalize_line_items($invoice['items'] ?? []);
  if (empty($items)) return 0.0;

  $productsById = [];
  foreach (db_get_all('products') as $product) {
    $productsById[(int)($product['id'] ?? 0)] = $product;
  }

  $profit = 0.0;
  foreach ($items as $item) {
    $qty = (float)($item['qty'] ?? 0);
    $price = (float)($item['price'] ?? 0);
    $discountRate = (float)($item['discount_rate'] ?? $item['discount_percent'] ?? $item['discount'] ?? 0);
    $discountRate = min(100, max(0, $discountRate));
    $gross = $qty * $price;
    $discountAmount = ($gross * $discountRate) / 100;
    $netSelling = $gross - $discountAmount;

    $productId = (int)($item['product_id'] ?? 0);
    $purchasePrice = isset($productsById[$productId])
      ? (float)($productsById[$productId]['purchase_price'] ?? 0)
      : 0.0;
    $purchaseCost = $qty * $purchasePrice;

    $profit += $netSelling - $purchaseCost;
  }

  return $profit;
}

function generate_invoice_number()
{
  $invoices = db_get_all('invoices');
  $payments = db_get_all('payments');
  $maxNumber = 0;
  foreach (array_merge($invoices, $payments) as $record) {
    $invoiceNo = trim((string)($record['invoice_no'] ?? ''));
    if (preg_match('/INV-(\d{4})-(\d+)/i', $invoiceNo, $matches)) {
      $num = (int)$matches[2];
      if ($num > $maxNumber) {
        $maxNumber = $num;
      }
    }
  }
  $year = date('Y');
  return 'INV-' . $year . '-' . sprintf('%03d', $maxNumber + 1);
}

function generate_quotation_number()
{
  $quotations = db_get_all('quotations');
  $maxNumber = 0;
  foreach ($quotations as $quotation) {
    $quotationNo = trim((string)($quotation['quotation_no'] ?? ''));
    if (preg_match('/QUO-(\d{4})-(\d+)/i', $quotationNo, $matches)) {
      $maxNumber = max($maxNumber, (int)$matches[2]);
    }
  }
  return 'QUO-' . date('Y') . '-' . sprintf('%03d', $maxNumber + 1);
}

/* -------- Handle Save (Create / Update) -------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
  $paymentAmount = 0.0;
  $paymentMode = 'Cash';
  $paymentReference = '';
  $paymentDate = date('Y-m-d');
  if ($m === 'invoices') {
    $invoiceNo = trim((string)($_POST['invoice_no'] ?? ''));
    if ($invoiceNo === '') {
      $_POST['invoice_no'] = generate_invoice_number();
    }
  }
  if ($m === 'quotations' && trim((string)($_POST['quotation_no'] ?? '')) === '') {
    $_POST['quotation_no'] = generate_quotation_number();
  }

  $record = [];
  $existingTeamRecord = null;
  $teamId = normalize_field_value($_POST['id'] ?? '');
  if ($m === 'team' && $teamId !== '') {
    $existingTeamRecord = db_get_one('team', (int)$teamId);
  }
  foreach ($config['fields'] as $key => $field) {
    if ($m === 'team' && $key === 'photo') {
      $record[$key] = $existingTeamRecord['photo'] ?? '';
      continue;
    }
    if (in_array($m, ['quotations', 'invoices'], true) && $key === 'amount') {
      continue;
    }
    $val = normalize_field_value($_POST[$key] ?? '');
    if ($m === 'team' && $key === 'role' && $val === '') {
      $val = 'Member';
    }
    if ($field['type'] === 'date') $val = normalize_date_input($val);

    // Wholeseller: only Admin role gets login credentials.
    // Member role must not show/store username or password.
    $selectedTeamRole = $m === 'team'
      ? trim((string)($_POST['role'] ?? ($existingTeamRecord['role'] ?? 'Member')))
      : '';
    if ($m === 'team' && in_array($key, ['username', 'password'], true) && strcasecmp($selectedTeamRole, 'Member') === 0) {
      $val = '';
    } elseif ($m === 'team' && $key === 'password' && $val === '' && $existingTeamRecord) {
      $val = $existingTeamRecord['password'] ?? '';
    }

    $isCredentialRequired = ($m === 'team' && in_array($key, ['username'], true) && strcasecmp($selectedTeamRole, 'Admin') === 0);
    if (($field['required'] ?? false) && $val === '' && !($m === 'team' && in_array($key, ['username', 'password'], true))) {
      $errors[] = $field['label'] . ' is required.';
    }
    if ($isCredentialRequired && $val === '') {
      $errors[] = $field['label'] . ' is required for Admin role.';
    }
    if ($m === 'team' && $key === 'password' && strcasecmp($selectedTeamRole, 'Admin') === 0 && $teamId === '' && $val === '') {
      $errors[] = 'Password is required for a new Admin wholeseller.';
    }
    if ($m === 'team' && $key === 'password' && strcasecmp($selectedTeamRole, 'Admin') === 0 && $val !== '' && (!$existingTeamRecord || $val !== ($existingTeamRecord['password'] ?? ''))) {
      $val = password_hash($val, PASSWORD_DEFAULT);
    }
    $record[$key] = $val;
  }
  if (in_array($m, ['quotations', 'invoices'], true) && $teamId !== '') {
    $existingOrderRecord = db_get_one($m, (int)$teamId);
    foreach (['created_by_id', 'created_by'] as $ownershipField) {
      if (isset($existingOrderRecord[$ownershipField])) $record[$ownershipField] = $existingOrderRecord[$ownershipField];
    }
  }
  if (is_subadmin() && $m === 'customers') {
    // Subadmin may create a customer with ONLY name and mobile number.
    if ($teamId !== '') {
      http_response_code(403);
      die('Subadmin can only add new customers.');
    }
    $record = [
      'name' => trim((string)($record['name'] ?? '')),
      'phone' => trim((string)($record['phone'] ?? '')),
    ];
    if ($record['name'] === '') $errors[] = 'Customer Name is required.';
    if ($record['phone'] === '') $errors[] = 'Mobile Number is required.';
  }

  /* Dedicated Wholeseller save path.
       Wholeseller records live in database/team.json and are completely
       separate from the Subadmin records in database/subadmins.json. */
  if ($m === 'team') {
    $record['role'] = trim((string)($record['role'] ?? '')) ?: 'Member';

    // Prevent accidental duplicate usernames.
    foreach (db_get_all('team') as $existingMember) {
      if ((int)($existingMember['id'] ?? 0) === (int)($teamId)) continue;
      if (strcasecmp(trim((string)($existingMember['username'] ?? '')), trim((string)($record['username'] ?? ''))) === 0 && trim((string)($record['username'] ?? '')) !== '') {
        $errors[] = 'Username already exists for another wholeseller.';
        break;
      }
    }
  }

  if ($m === 'team' && isset($_FILES['photo']) && ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    $photo = $_FILES['photo'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(pathinfo($photo['name'] ?? '', PATHINFO_EXTENSION));
    if (($photo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || !in_array($extension, $allowedExtensions, true)) {
      $errors[] = 'Please upload a valid JPG, PNG, or WEBP photo.';
    } else {
      $uploadDirectory = __DIR__ . '/uploads/team';
      if (!is_dir($uploadDirectory)) mkdir($uploadDirectory, 0755, true);
      $fileName = 'team_' . uniqid('', true) . '.' . $extension;
      if (move_uploaded_file($photo['tmp_name'], $uploadDirectory . '/' . $fileName)) {
        $record['photo'] = 'uploads/team/' . $fileName;
      } else {
        $errors[] = 'Photo upload failed. Please try again.';
      }
    }
  }
  if ($m === 'team' && empty($errors)) {
    // Save Wholeseller explicitly to team.json. This avoids the team/
    // subadmin separation from ever sharing the same storage.
    $teamRows = db_get_all('team');
    $saveId = (int)$teamId;

    if ($saveId > 0) {
      $found = false;
      foreach ($teamRows as &$teamRow) {
        if ((int)($teamRow['id'] ?? 0) === $saveId) {
          $record['id'] = $teamRow['id'];
          $record['created_at'] = $teamRow['created_at'] ?? date('Y-m-d H:i:s');
          $record['updated_at'] = date('Y-m-d H:i:s');
          $teamRow = $record;
          $found = true;
          break;
        }
      }
      unset($teamRow);
      if (!$found) {
        $errors[] = 'Wholeseller record not found.';
      }
    } else {
      $record['id'] = db_next_id($teamRows);
      $record['created_at'] = date('Y-m-d H:i:s');
      $record['updated_at'] = date('Y-m-d H:i:s');
      $teamRows[] = $record;
      $saveId = (int)$record['id'];
    }

    if (empty($errors)) {
      $teamFile = db_file('team');
      $teamDirectory = dirname($teamFile);
      if (!is_dir($teamDirectory)) mkdir($teamDirectory, 0755, true);
      $encodedTeamRows = json_encode(array_values($teamRows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
      $written = file_put_contents($teamFile, $encodedTeamRows . "\n", LOCK_EX);
      if ($written === false) {
        $errors[] = 'Wholeseller could not be saved. Please check database/team.json write permission.';
      }
    }

    if (empty($errors)) {
      // Verify that the record actually reached team.json before redirecting.
      $verifyRows = db_get_all('team');
      $verified = false;
      foreach ($verifyRows as $verifyRow) {
        if ((int)($verifyRow['id'] ?? 0) === $saveId) {
          $verified = true;
          break;
        }
      }
      if (!$verified) {
        $errors[] = 'Wholeseller save verification failed. Please try again.';
      } else {
        if ($isPopup) {
          $returnTo = trim((string)($_GET['return_to'] ?? ''));
          $redirectUrl = $returnTo !== '' ? $returnTo . (str_contains($returnTo, '?') ? '&' : '?') . 'ok=' . rawurlencode($teamId !== '' ? 'updated' : 'created') : 'module.php?m=team&ok=' . rawurlencode($teamId !== '' ? 'updated' : 'created');
          echo '<script>window.parent.location.href = ' . json_encode($redirectUrl) . ';</script>';
        } else {
          header('Location: module.php?m=team&ok=' . rawurlencode($teamId !== '' ? 'updated' : 'created'));
        }
        exit;
      }
    }
  }

  if ($m === 'invoices') {
    $invoiceCustomerName = trim((string)($record['customer'] ?? ''));
    if (!module_invoice_customer_allowed($invoiceCustomerName)) {
      $errors[] = 'Please select a Customer or Wholeseller from the list. Invoice cannot be created for an unlisted customer.';
    }
  }

  if (in_array($m, ['quotations', 'invoices'], true)) {
    $rawItems = $_POST['items'] ?? [];
    if ($m === 'invoices') {
      $customerNameForType = trim((string)($record['customer'] ?? ''));
      $record['customer_type'] = '';
      if (module_wholeseller_id($customerNameForType) > 0) {
        $record['customer_type'] = 'wholeseller';
      } elseif (module_customer_id($customerNameForType) > 0) {
        $record['customer_type'] = 'customer';
      }
    }
    if (in_array($m, ['quotations', 'invoices'], true)) {
      $productsById = [];
      foreach (db_get_all('products') as $product) {
        $productsById[(int)($product['id'] ?? 0)] = $product;
      }
      $teamCustomerNames = array_map(function ($member) {
        return trim(mb_strtolower((string)($member['name'] ?? '')));
      }, array_filter(db_get_all('team'), function ($member) {
        return strcasecmp((string)($member['role'] ?? ''), 'Admin') !== 0;
      }));
      $isTeamCustomer = in_array(trim(mb_strtolower((string)($record['customer'] ?? ''))), $teamCustomerNames, true);
      foreach ($rawItems as &$rawItem) {
        if (!is_array($rawItem)) continue;
        $productId = (int)($rawItem['product_id'] ?? 0);
        if ($productId > 0 && isset($productsById[$productId])) {
          $rawItem['description'] = $productsById[$productId]['name'] ?? ($rawItem['description'] ?? '');
          // Keep the product's normal selling price as the reference price.
          $rawItem['price'] = module_product_selling_price($productsById[$productId]);
        }

        $referencePrice = max(0, (float)($rawItem['price'] ?? 0));
        if ($m === 'invoices') {
          // Invoice: user enters Actual Selling Price and discount % is auto-calculated.
          $actualInput = $rawItem['actual_selling_price'] ?? $referencePrice;
          $actualSellingPrice = round((float)$actualInput);
          if ($actualSellingPrice < 0) {
            $actualSellingPrice = 0;
          }
          if ($actualSellingPrice > $referencePrice) {
            $errors[] = 'Actual Selling Price cannot be greater than Selling Price for ' . ($rawItem['description'] ?? 'this product') . '.';
            $actualSellingPrice = $referencePrice;
          }
          $rawItem['actual_selling_price'] = (int)$actualSellingPrice;
          $rawItem['discount_rate'] = $referencePrice > 0
            ? round((($referencePrice - $actualSellingPrice) / $referencePrice) * 100, 2)
            : 0;
          $rawItem['discount_amount'] = round((float)($rawItem['qty'] ?? 0) * ($referencePrice - $actualSellingPrice), 2);
          $rawItem['total'] = round((float)($rawItem['qty'] ?? 0) * $actualSellingPrice, 2);
        } elseif (!$isTeamCustomer) {
          $rawItem['discount_rate'] = 0;
        }
      }
      unset($rawItem);
    }
    $items = normalize_line_items($rawItems);
    $record['items'] = $items;
    $total = line_items_total($items);
    $record['amount'] = $total;
    if ($record['amount'] <= 0) {
      $errors[] = 'At least one valid item is required.';
    }
    if (!is_admin()) {
      $member = current_team_member();
      $record['created_by_id'] = $member['id'] ?? '';
      $record['created_by'] = $member['name'] ?? '';
    }
  }
  if ($m === 'leads' && empty($record['stage'])) {
    $record['stage'] = 'New';
  }
  if ($m === 'quotations') {
    $quotationStatuses = ['Sent', 'Converted', 'Expired', 'Rejected'];
    if (!in_array($record['status'] ?? '', $quotationStatuses, true)) {
      $record['status'] = 'Sent';
    }
    $validUntil = trim((string)($record['valid_until'] ?? ''));
    if ($validUntil !== '' && strtotime($validUntil) !== false && strtotime($validUntil) < strtotime('today') && !in_array($record['status'], ['Converted', 'Rejected'], true)) {
      $record['status'] = 'Expired';
    }
  }
  if ($m === 'invoices') {
    if (empty(trim((string)($record['invoice_no'] ?? '')))) {
      $record['invoice_no'] = generate_invoice_number();
    }
    $paymentAmount = isset($_POST['payment_amount']) ? (float)$_POST['payment_amount'] : 0;
    $paymentMode = normalize_field_value($_POST['payment_mode'] ?? 'Cash');
    $paymentReference = normalize_field_value($_POST['payment_reference'] ?? '');
    $paymentDate = normalize_date_input($_POST['payment_date'] ?? date('Y-m-d'));
    $invoiceId = normalize_field_value($_POST['id'] ?? '');
    $existingPaid = $invoiceId !== '' ? module_invoice_paid((string)($record['invoice_no'] ?? '')) : 0.0;
    $pendingBeforePayment = max(0, (float)$record['amount'] - $existingPaid);
    if ($paymentAmount > 0) {
      if ($paymentAmount > $pendingBeforePayment) {
        $errors[] = 'Payment cannot be greater than the pending amount.';
      }
    }
    $totalPaid = $existingPaid + $paymentAmount;
    $record['status'] = module_invoice_status([
      'invoice_no' => $record['invoice_no'] ?? '',
      'amount' => $record['amount'],
      'due_date' => $record['due_date'] ?? '',
    ]);
    if ($paymentAmount > 0 && $totalPaid >= (float)$record['amount']) {
      $record['status'] = 'Paid';
    } elseif ($paymentAmount > 0 && $totalPaid > 0) {
      $record['status'] = 'Partly Paid';
    }
  }
  if (empty($errors)) {
    $id = normalize_field_value($_POST['id'] ?? '');
    if ($id !== '' && $m === 'customers' && is_subadmin()) {
      http_response_code(403);
      die('Subadmin can only add new customers.');
    }
    if ($id !== '' && !is_admin() && !member_can_access_record(db_get_one($m, (int)$id))) {
      http_response_code(403);
      die('You can only edit your own orders.');
    }
    $newInvoiceId = null;
    if ($id !== '') {
      db_update($m, (int)$id, $record);
      $msg = 'updated';
    } else {
      $newInvoiceId = db_insert($m, $record);
      $msg = 'created';
    }

    if (is_subadmin()) {
      $activityId = $id !== '' ? (int)$id : (int)($newInvoiceId ?? 0);
      $label = $m === 'invoices' ? ($record['invoice_no'] ?? '') : ($m === 'quotations' ? ($record['quotation_no'] ?? '') : ($record['name'] ?? $record['title'] ?? ''));
      $actionWord = $id !== '' ? 'Updated' : 'Created';
      $category = $m === 'customers' ? 'customer' : $m;
      $detailsParts = [$actionWord . ' ' . ucfirst(rtrim($m, 's'))];
      if ($label !== '') $detailsParts[] = $label;
      if (!empty($record['customer'])) $detailsParts[] = 'Customer: ' . $record['customer'];
      if ($m === 'leads' && !empty($record['stage'])) $detailsParts[] = 'Stage: ' . $record['stage'];
      if (in_array($m, ['invoices', 'quotations'], true)) $detailsParts[] = 'Amount: ₹' . number_format((float)($record['amount'] ?? 0), 2);
      log_activity($m, strtolower($actionWord), implode(' • ', $detailsParts), $activityId, $category);
      if (!empty($record['customer']) && in_array($m, ['invoices', 'quotations'], true)) {
        log_activity('customers', 'used', 'Used customer in ' . ucfirst(rtrim($m, 's')) . ': ' . ($label !== '' ? $label : 'New record'), module_customer_id($record['customer']), 'customer');
      }
    }

    if ($m === 'invoices' && $paymentAmount > 0) {
      $newPaymentId = db_insert('payments', [
        'invoice_no' => $record['invoice_no'] ?? '',
        'customer' => $record['customer'] ?? '',
        'amount' => $paymentAmount,
        'mode' => $paymentMode !== '' ? $paymentMode : 'Cash',
        'reference' => $paymentReference,
        'date' => $paymentDate,
        'created_by_id' => is_subadmin() ? current_member_id() : '',
        'created_by' => is_subadmin() ? current_member_name() : '',
      ]);
      if (is_subadmin()) {
        log_activity('payments', 'recorded', 'Payment: ₹' . number_format($paymentAmount, 2) . ' • Invoice: ' . ($record['invoice_no'] ?? '') . ' • Customer: ' . ($record['customer'] ?? '') . ' • Mode: ' . ($paymentMode !== '' ? $paymentMode : 'Cash'), $newPaymentId, 'payment');
      }
    }
    if ($m === 'invoices' && !empty($_GET['copy_from']) && $_GET['copy_from'] === 'quotations' && !empty($_GET['id'])) {
      $sourceQuotation = db_get_one('quotations', (int)$_GET['id']);
      if ($sourceQuotation) {
        db_update('quotations', (int)$sourceQuotation['id'], array_merge($sourceQuotation, [
          'status' => 'Converted',
          'converted_to_invoice_id' => $newInvoiceId ?? (int)$id,
        ]));
      }
    }
    if ($isPopup) {
      $returnTo = trim((string)($_GET['return_to'] ?? ''));
      $redirectUrl = $returnTo !== '' ? $returnTo . (str_contains($returnTo, '?') ? '&' : '?') . 'ok=' . rawurlencode($msg) : 'module.php?m=' . rawurlencode($m) . '&ok=' . rawurlencode($msg);
      echo '<script>window.parent.location.href = ' . json_encode($redirectUrl) . ';</script>';
      exit;
    }
    header("Location: module.php?m=$m&ok=$msg");
    exit;
  }
}

/* -------- PRG: Redirect validation failures so browser never keeps a POST page in history -------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save' && !empty($errors)) {
  $_SESSION['module_form_flash'] = [
    'module' => $m,
    'edit_id' => (string)($_POST['id'] ?? ''),
    'record' => $record,
    'errors' => $errors,
  ];

  $redirectParams = [
    'm' => $m,
    'action' => !empty($_POST['id']) ? 'edit' : 'new',
  ];
  if (!empty($_POST['id'])) $redirectParams['id'] = (int)$_POST['id'];
  if ($isPopup) $redirectParams['popup'] = '1';
  if (!empty($_GET['return_to'])) $redirectParams['return_to'] = (string)$_GET['return_to'];

  header('Location: module.php?' . http_build_query($redirectParams));
  exit;
}

/* -------- Handle Delete -------- */
if ($action === 'delete' && isset($_GET['id'])) {
  if (!is_admin() && !member_can_access_record(db_get_one($m, (int)$_GET['id']))) {
    http_response_code(403);
    die('You can only delete your own orders.');
  }
  $deleteRecord = db_get_one($m, (int)$_GET['id']);
  db_delete($m, (int)$_GET['id']);
  if (is_subadmin()) {
    $label = $m === 'invoices' ? ($deleteRecord['invoice_no'] ?? '') : ($m === 'quotations' ? ($deleteRecord['quotation_no'] ?? '') : ($deleteRecord['name'] ?? $deleteRecord['title'] ?? ''));
    $details = 'Deleted ' . ucfirst(rtrim($m, 's')) . ($label !== '' ? ': ' . $label : '');
    if (!empty($deleteRecord['customer'])) $details .= ' • Customer: ' . $deleteRecord['customer'];
    log_activity($m, 'deleted', $details, (int)$_GET['id'], $m);
  }
  header("Location: module.php?m=$m&ok=deleted");
  exit;
}

$pageTitle = $config['label'];

/* -------- READ-ONLY DETAIL VIEW -------- */
if ($action === 'view' && in_array($m, ['leads', 'quotations', 'invoices', 'tasks'], true)) {
  $record = db_get_one($m, (int)($_GET['id'] ?? 0));
  if (!$record) {
    header("Location: module.php?m=$m");
    exit;
  }
  $pageTitle = 'View ' . $config['label'];
  require __DIR__ . '/layout_top.php';
?>
  <div class="panel detail-panel">
    <div class="panel-header">
      <?php $detailTitle = $m === 'invoices' ? ($record['invoice_no'] ?? '') : ($m === 'quotations' ? ($record['quotation_no'] ?? '') : ($record['name'] ?? $record['title'] ?? $config['label'])); ?>
      <h2><?= icon($config['icon']) ?> <?= htmlspecialchars((string)$detailTitle) ?></h2>
      <div class="toolbar">
        <?php if (is_admin()): ?><a href="module.php?m=<?= $m ?>&action=edit&id=<?= (int)$record['id'] ?>" class="btn btn-outline btn-sm"><?= icon('edit', 14) ?> Edit</a><?php endif; ?>
        <a href="module.php?m=<?= $m ?>" class="btn btn-outline btn-sm"><?= icon('back', 14) ?> Back</a>
      </div>
    </div>
    <div class="panel-body">
      <div class="detail-grid">
        <?php foreach ($config['fields'] as $key => $field):
          $value = trim((string)($record[$key] ?? ''));
          $displayValue = $m === 'quotations' && $key === 'status' ? quotation_effective_status($record) : $value;
        ?>
          <div class="detail-item <?= $field['type'] === 'textarea' ? 'detail-item-wide' : '' ?>">
            <div class="detail-label"><?= htmlspecialchars($field['label']) ?></div>
            <div class="detail-value">
              <?php if ($field['type'] === 'select' && $displayValue !== ''): ?>
                <span class="badge <?= badge_class($displayValue) ?>"><?= htmlspecialchars($displayValue) ?></span>
              <?php elseif ($displayValue !== ''): ?>
                <?= $field['type'] === 'textarea' ? nl2br(htmlspecialchars($displayValue)) : htmlspecialchars($displayValue) ?>
              <?php else: ?>
                <span class="detail-empty">-</span>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php
  require __DIR__ . '/layout_bottom.php';
  exit;
}

/* -------- EDIT / NEW FORM VIEW -------- */
if ($action === 'new' || $action === 'edit') {
  $formFlash = $_SESSION['module_form_flash'] ?? null;
  if (
    is_array($formFlash)
    && ($formFlash['module'] ?? '') === $m
    && (string)($formFlash['edit_id'] ?? '') === (string)($_GET['id'] ?? '')
  ) {
    unset($_SESSION['module_form_flash']);
  } else {
    $formFlash = null;
  }

  $record = [];
  if ($action === 'edit') {
    $record = db_get_one($m, (int)($_GET['id'] ?? 0));
    if (!$record) {
      header("Location: module.php?m=$m");
      exit;
    }
    if (in_array($m, ['quotations', 'invoices'], true) && !member_can_access_record($record)) {
      http_response_code(403);
      die('You can only access your own orders.');
    }
    if ($m === 'invoices') {
      $record['status'] = module_invoice_status($record);
    }
  } else {
    $copyFrom = $_GET['copy_from'] ?? '';
    $copyId = (int)($_GET['id'] ?? 0);
    if ($copyFrom && $copyId && isset($MODULES[$copyFrom])) {
      $sourceRecord = db_get_one($copyFrom, $copyId);
      if ($sourceRecord) {
        if ($m === 'invoices' && $copyFrom === 'quotations') {
          if (quotation_effective_status($sourceRecord) === 'Converted' || !empty($sourceRecord['converted_to_invoice_id'])) {
            header('Location: module.php?m=quotations&ok=already-converted');
            exit;
          }
          $record['customer'] = $sourceRecord['customer'] ?? '';
          $record['amount'] = (float)($sourceRecord['amount'] ?? 0);
          $record['status'] = 'Draft';
          $record['items'] = normalize_line_items($sourceRecord['items'] ?? []);
          $record['source_quotation_id'] = (int)$copyId;
        }
      }
    }
    if ($m === 'invoices' && empty(trim((string)($record['invoice_no'] ?? '')))) {
      $record['invoice_no'] = generate_invoice_number();
    }
    if ($m === 'quotations' && empty(trim((string)($record['quotation_no'] ?? '')))) {
      $record['quotation_no'] = generate_quotation_number();
    }
    if ($m === 'leads') {
      $record['stage'] = 'New';
    }
    if ($m === 'expenses' && empty($record['date'])) {
      $record['date'] = date('Y-m-d');
    }
  }

  if (is_array($formFlash)) {
    if (isset($formFlash['record']) && is_array($formFlash['record'])) {
      $record = $formFlash['record'];
    }
    if (isset($formFlash['errors']) && is_array($formFlash['errors'])) {
      $errors = $formFlash['errors'];
    }
  }

  $customerList = (is_admin() || is_subadmin()) && in_array('customer_select', array_column($config['fields'], 'type')) ? db_get_all('customers') : [];
  $teamList = (in_array($m, ['quotations', 'invoices'], true) || in_array('team_select', array_column($config['fields'], 'type'))) ? array_values(array_filter(db_get_all('team'), function ($member) {
    return strcasecmp((string)($member['role'] ?? ''), 'Admin') !== 0;
  })) : [];
  $productList = in_array($m, ['quotations', 'invoices'], true) ? db_get_all('products') : [];
  $teamCustomerNames = array_map(function ($member) {
    return trim(mb_strtolower((string)($member['name'] ?? '')));
  }, $teamList);
  $isTeamCustomer = in_array($m, ['quotations', 'invoices'], true) && in_array(trim(mb_strtolower((string)($record['customer'] ?? ''))), $teamCustomerNames, true);
  $invoicePayments = [];
  $invoiceTotal = (float)($record['amount'] ?? 0);
  $invoiceTotalPaid = 0.0;
  $invoicePending = 0.0;
  if ($m === 'invoices' && $action === 'edit') {
    $invoicePayments = array_values(array_filter(db_get_all('payments'), function ($payment) use ($record) {
      return ($payment['invoice_no'] ?? '') === ($record['invoice_no'] ?? '') || (($record['invoice_no'] ?? '') === '' && ($payment['customer'] ?? '') === ($record['customer'] ?? ''));
    }));
    $invoiceTotalPaid = array_reduce($invoicePayments, fn($carry, $payment) => $carry + (float)($payment['amount'] ?? 0), 0.0);
    $invoicePending = max(0, $invoiceTotal - $invoiceTotalPaid);
  }

  if ($isPopup) {
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>' . htmlspecialchars($pageTitle) . '</title><link rel="stylesheet" href="assets/style.css">
<style>
 .product-picker.item-product-picker { position:relative; width:100%; min-width:0; }
.product-picker.item-product-picker .item-product-search { box-sizing:border-box; width:100%; min-height:40px; padding:9px 34px 9px 11px; border:1px solid #d9dee7; border-radius:8px; background:#fff; color:#334155; font:inherit; }
.product-picker.item-product-picker .item-product-search:focus { outline:none; border-color:var(--primary,#2f7d32); box-shadow:0 0 0 2px rgba(47,125,50,.12); }
.product-picker.item-product-picker::after { content:"▾"; position:absolute; top:9px; right:12px; color:#64748b; pointer-events:none; }
.product-picker.item-product-picker .item-product-options { display:none; position:absolute; left:0; right:0; top:calc(100% + 4px); z-index:9999; max-height:300px; overflow-y:auto; background:#fff; border:1px solid #d9dee7; border-radius:10px; box-shadow:0 10px 24px rgba(0,0,0,.14); padding:6px; }
.product-picker.item-product-picker:focus-within .item-product-options,
.product-picker.item-product-picker.is-open .item-product-options { display:block; }
.product-picker.item-product-picker .item-product-option { width:100%; display:flex; align-items:center; justify-content:space-between; gap:12px; border:0; background:#fff; padding:9px 10px; border-radius:7px; text-align:left; cursor:pointer; color:#334155; font:inherit; }
.product-picker.item-product-picker .item-product-option:hover,
.product-picker.item-product-picker .item-product-option.selected { background:#eef6ff; }
.product-picker.item-product-picker .item-product-option span { min-width:0; flex:1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.product-picker.item-product-picker .item-product-option small { flex:0 0 auto; font-weight:600; color:#334155; }
.product-picker.item-product-picker .item-product-empty { padding:10px; color:#64748b; font-size:13px; }
.product-picker.item-product-picker .item-product-select { display:none !important; }
.product-picker.item-product-picker .item-product-custom-option { border-top:1px solid #e5e7eb; margin-top:4px; color:var(--primary,#2f7d32); font-weight:600; }
.product-picker.item-product-picker .item-product-custom-option small { color:#64748b; font-weight:500; }
.line-item-row.custom-line-item .item-product-search { border-color:var(--primary,#2f7d32); background:#f8fffb; }
.item-price.custom-rate-input { background:#fff; cursor:text; }

.wholeseller-link,.customer-link,.invoice-party-link{cursor:pointer;text-decoration:none;}
.wholeseller-link:hover,.customer-link:hover,.invoice-party-link:hover{text-decoration:underline;}
</style>
</head><body class="popup-form-page">';
  } else {
    require __DIR__ . '/layout_top.php';
  }
?>
  <div class="panel">
    <div class="panel-header">
      <h2><?= icon($config['icon']) ?> <?= $action === 'edit' ? 'Edit' : 'Add New' ?> <?= htmlspecialchars($config['label']) ?></h2>
      <?php if (!$isPopup): ?>
        <a href="module.php?m=<?= $m ?>" class="btn btn-outline btn-sm"><?= icon('back', 16) ?> Back to list</a>
      <?php endif; ?>
    </div>
    <div class="panel-body">
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
        </div>
      <?php endif; ?>
      <form method="post" action="module.php?m=<?= $m ?>&action=save<?= $isPopup ? '&popup=1' : '' ?><?= $isPopup && !empty($_GET['return_to']) ? '&return_to=' . urlencode($_GET['return_to']) : '' ?>" <?= $m === 'team' ? 'enctype="multipart/form-data"' : '' ?>>
        <?php if ($action === 'edit'): ?>
          <input type="hidden" name="id" value="<?= (int)$record['id'] ?>">
        <?php endif; ?>
        <div class="form-grid">
          <?php foreach ($config['fields'] as $key => $field):
            if (is_subadmin() && $m === 'customers' && !in_array($key, ['name', 'phone'], true)) {
              continue;
            }
            if (is_subadmin() && $m === 'customers' && $key === 'phone') {
              $field['label'] = 'Mobile Number';
              $field['type'] = 'tel';
              $field['required'] = true;
            }
            if ($m === 'leads' && $action === 'new' && $key === 'stage') {
              continue;
            }
            if ($m === 'invoices' && $key === 'status') {
              continue;
            }
            if (in_array($m, ['quotations', 'invoices'], true) && $key === 'amount') {
              continue;
            }
            $val = $field['type'] === 'password' ? '' : htmlspecialchars($record[$key] ?? '');
            if ($field['type'] === 'date') $val = htmlspecialchars(format_input_date($record[$key] ?? '', ''));
          ?>
            <div class="form-group <?= in_array($field['type'], ['textarea']) ? 'full' : '' ?>" <?= $m === 'team' && in_array($key, ['username', 'password'], true) ? ' data-team-credential="1"' : '' ?>>
              <label><?= htmlspecialchars($field['label']) ?><?= ($field['required'] ?? false) ? ' *' : '' ?></label>
              <?php if ($m === 'invoices' && $key === 'invoice_no'): ?>
                <input type="text" name="<?= $key ?>" value="<?= $val ?>" readonly class="readonly-field">
              <?php elseif ($field['type'] === 'date'): ?>
                <div class="date-picker-field">
                  <input type="text" name="<?= $key ?>" value="<?= $val ?>" placeholder="dd/mm/yyyy" inputmode="numeric" autocomplete="off" class="date-display-input" data-picker-id="date-picker-<?= htmlspecialchars($key) ?>">
                  <button type="button" class="date-picker-button" data-picker-target="date-picker-<?= htmlspecialchars($key) ?>" aria-label="Choose date" title="Choose date"><?= icon('calendar', 16) ?></button>
                  <input type="date" id="date-picker-<?= htmlspecialchars($key) ?>" class="date-picker-native" value="<?= htmlspecialchars($record[$key] ?? '') ?>" tabindex="-1" aria-hidden="true">
                </div>
              <?php elseif ($field['type'] === 'textarea'): ?>
                <textarea name="<?= $key ?>" rows="3"><?= $val ?></textarea>
              <?php elseif ($field['type'] === 'select'): ?>
                <select name="<?= $key ?>">
                  <option value="">Select <?= htmlspecialchars($field['label']) ?></option>
                  <?php foreach ($field['options'] as $opt): ?>
                    <option value="<?= htmlspecialchars($opt) ?>" <?= ($record[$key] ?? '') === $opt ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
                  <?php endforeach; ?>
                </select>
              <?php elseif ($field['type'] === 'radio'): ?>
                <div class="radio-group">
                  <?php foreach ($field['options'] as $opt): ?>
                    <?php $radioValue = $record[$key] ?? ($field['default'] ?? ''); ?>
                    <label class="radio-option">
                      <input type="radio" name="<?= $key ?>" value="<?= htmlspecialchars($opt) ?>" <?= $radioValue === $opt ? 'checked' : '' ?> required>
                      <span><?= htmlspecialchars($opt) ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              <?php elseif ($field['type'] === 'customer_select'): ?>
                <div class="customer-picker">
                  <input type="search" name="<?= $key ?>" class="customer-search" value="<?= $val ?>" placeholder="Search or select <?= htmlspecialchars($field['label']) ?>" autocomplete="off">
                  <div class="customer-options">
                    <?php foreach ($customerList as $c): ?>
                      <button type="button" class="customer-option" data-value="<?= htmlspecialchars($c['name']) ?>"><?= htmlspecialchars($c['name']) ?><?= !empty($c['phone']) ? ' — ' . htmlspecialchars($c['phone']) : '' ?><?= is_admin() && !empty($c['company']) ? ' (' . htmlspecialchars($c['company']) . ')' : '' ?></button>
                    <?php endforeach; ?>
                    <?php if ($m === 'invoices'): ?>
                      <?php foreach ($teamList as $member): ?>
                        <button type="button" class="customer-option" data-value="<?= htmlspecialchars($member['name']) ?>"><?= htmlspecialchars($member['name']) ?> (Wholeseller)</button>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>
                </div>
                <?php if (empty($customerList) && is_admin()): ?>
                  <small style="color:#2F7D32;">No customers yet — <a href="module.php?m=customers&action=new" style="color:var(--primary);">add one first</a>.</small>
                <?php endif; ?>
                <?php if ($m === 'invoices' && is_admin()): ?>
                  <a href="module.php?m=customers&action=new" class="field-action-link"><?= icon('add', 12) ?> Add Customer</a>
                <?php endif; ?>
              <?php elseif ($field['type'] === 'team_select'): ?>
                <select name="<?= $key ?>">
                  <option value="">Select wholeseller</option>
                  <?php foreach ($teamList as $member): ?>
                    <option value="<?= htmlspecialchars($member['name']) ?>" <?= ($record[$key] ?? '') === ($member['name'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($member['name']) ?></option>
                  <?php endforeach; ?>
                </select>
                <?php if (empty($teamList)): ?>
                  <small style="color:#2F7D32;">No wholesellers yet — add one from the Wholeseller menu.</small>
                <?php endif; ?>
              <?php elseif ($field['type'] === 'password'): ?>
                <div class="password-field">
                  <input id="team-password" type="password" name="<?= $key ?>" value="" autocomplete="new-password">
                  <button type="button" class="password-toggle" aria-label="Show password" title="Show password" data-target="team-password">
                    <span class="password-icon-show"><?= icon('view', 16) ?></span>
                    <span class="password-icon-hide password-icon-hidden"><?= icon('eye-off', 16) ?></span>
                  </button>
                </div>
              <?php else: ?>
                <input type="<?= $field['type'] ?>" name="<?= $key ?>" value="<?= $val ?>">
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if (in_array($m, ['quotations', 'invoices'], true)): ?>
          <?php $items = normalize_line_items($record['items'] ?? []);
          if (empty($items)) {
            $items = [['description' => '', 'qty' => 1, 'price' => (float)($record['amount'] ?? 0), 'total' => (float)($record['amount'] ?? 0)]];
          } ?>
          <div class="items-panel">
            <div class="items-header">
              <h3>Line Items</h3>
              <button type="button" class="btn btn-outline btn-sm" id="add-item-row">Add Item</button>
            </div>
            <table class="line-items-table">
              <thead>
                <tr>
                  <?php if (in_array($m, ['quotations', 'invoices'], true)): ?><th>Product</th><?php else: ?><th>Description</th><?php endif; ?>
                  <th style="width:120px;">Qty</th>
                  <?php if ($m === 'invoices'): ?>
                    <th style="width:145px;">Selling Price</th>
                    <th style="width:165px;">Actual Selling Price</th>
                    <th style="width:120px;">Discount (%)</th>
                  <?php else: ?>
                    <th style="width:150px;">Price</th>
                    <?php if ($m === 'quotations'): ?><th style="width:130px;" class="team-discount-column<?= $isTeamCustomer ? '' : ' is-hidden' ?>">Discount (%)</th><?php endif; ?>
                  <?php endif; ?>
                  <th style="width:150px;">Total</th>
                  <th style="width:80px;">Action</th>
                </tr>
              </thead>
              <tbody id="line-items-body">
                <?php foreach ($items as $index => $item): ?>
                  <?php
                  $selectedProductId = (int)($item['product_id'] ?? 0);
                  $selectedProduct = null;
                  if ($selectedProductId > 0) {
                    foreach ($productList as $productRow) {
                      if ((int)($productRow['id'] ?? 0) === $selectedProductId) {
                        $selectedProduct = $productRow;
                        break;
                      }
                    }
                  }
                  $itemPrice = $selectedProduct ? module_product_selling_price($selectedProduct) : (float)($item['price'] ?? 0);
                  $itemActualPrice = array_key_exists('actual_selling_price', $item)
                    ? round((float)$item['actual_selling_price'])
                    : max(0, $itemPrice - (($itemPrice * (float)($item['discount_rate'] ?? 0)) / 100));
                  $itemActualPrice = (int)max(0, min($itemPrice, round($itemActualPrice)));
                  $initialTotal = (float)($item['total'] ?? ((float)($item['qty'] ?? 0) * $itemActualPrice));
                  ?>
                  <tr class="line-item-row">
                    <?php if (in_array($m, ['quotations', 'invoices'], true)): ?>
                      <td>
                        <div class="product-picker item-product-picker">
                          <input type="search" class="item-product-search" value="<?= htmlspecialchars($item['description'] ?? '') ?>" placeholder="Search / select product" autocomplete="off">
                          <div class="product-options item-product-options">
                            <?php if (empty($productList)): ?>
                              <div class="item-product-empty">No products found. Please add products first.</div>
                            <?php else: ?>
                              <?php foreach ($productList as $product): ?>
                                <button type="button" class="product-option item-product-option <?= (int)($item['product_id'] ?? 0) === (int)$product['id'] ? 'selected' : '' ?>" data-product-id="<?= (int)$product['id'] ?>" data-price="<?= htmlspecialchars((string)module_product_selling_price($product)) ?>">
                                  <span><?= htmlspecialchars($product['name'] ?? '') ?></span>
                                  <small>₹<?= number_format(module_product_selling_price($product), 2) ?></small>
                                </button>
                              <?php endforeach; ?>
                              <button type="button" class="product-option item-product-custom-option" data-custom-product="1">
                                <span>+ Add Custom Item</span>
                                <small>Enter your own rate</small>
                              </button>
                            <?php endif; ?>
                          </div>
                          <input type="hidden" name="items[<?= $index ?>][product_id]" class="item-product-select" value="<?= (int)($item['product_id'] ?? 0) ?>">
                        </div>
                        <input type="hidden" name="items[<?= $index ?>][description]" class="item-description" value="<?= htmlspecialchars($item['description'] ?? '') ?>">
                      </td>
                    <?php else: ?>
                      <td><input type="text" name="items[<?= $index ?>][description]" value="<?= htmlspecialchars($item['description'] ?? '') ?>" placeholder="Item description"></td>
                    <?php endif; ?>
                    <td><input type="number" step="1" min="0" name="items[<?= $index ?>][qty]" class="item-qty" value="<?= htmlspecialchars((string)($item['qty'] ?? 1)) ?>" oninput="window.recalcLineItems && window.recalcLineItems()"></td>
                    <?php if ($m === 'invoices'): ?>
                      <td><input type="number" step="0.01" min="0" name="items[<?= $index ?>][price]" class="item-price" value="<?= htmlspecialchars((string)$itemPrice) ?>" readonly></td>
                      <td><input type="number" step="1" min="0" max="<?= htmlspecialchars((string)$itemPrice) ?>" name="items[<?= $index ?>][actual_selling_price]" class="item-actual-price" value="<?= htmlspecialchars((string)$itemActualPrice) ?>" oninput="window.recalcLineItems && window.recalcLineItems()"></td>
                      <td><input type="number" step="0.01" min="0" max="100" name="items[<?= $index ?>][discount_rate]" class="item-discount" value="<?= htmlspecialchars((string)($item['discount_rate'] ?? 0)) ?>" readonly></td>
                    <?php elseif ($m === 'quotations'): ?>
                      <td><input type="number" step="1" min="0" name="items[<?= $index ?>][price]" class="item-price" value="<?= htmlspecialchars((string)($item['price'] ?? 0)) ?>" oninput="window.recalcLineItems && window.recalcLineItems()"></td>
                      <td class="team-discount-column<?= $isTeamCustomer ? '' : ' is-hidden' ?>"><input type="number" step="0.01" min="0" max="100" name="items[<?= $index ?>][discount_rate]" class="item-discount" value="<?= htmlspecialchars((string)($item['discount_rate'] ?? 0)) ?>" oninput="window.recalcLineItems && window.recalcLineItems()"></td>
                    <?php endif; ?>
                    <td><input type="number" step="0.01" min="0" name="items[<?= $index ?>][total]" class="item-total" value="<?= htmlspecialchars((string)$initialTotal) ?>" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <div class="items-total-box">
              <span>Total</span>
              <strong id="items-total-display">₹<?= number_format((float)($record['amount'] ?? line_items_total($items)), 2) ?></strong>
              <input type="hidden" name="amount" id="items-total-input" value="<?= htmlspecialchars((string)((float)($record['amount'] ?? line_items_total($items)))) ?>">
            </div>
          </div>
        <?php endif; ?>

        <?php if ($m === 'invoices'): ?>
          <div class="payment-summary">
            <div class="payment-summary-header">
              <h3>Payment Summary</h3>
            </div>
            <div class="payment-summary-grid">
              <div class="payment-stat payment-stat-neutral">
                <div class="payment-label">Total Amount</div>
                <div class="payment-value" id="payment-total-display">₹<?= number_format($invoiceTotal, 2) ?></div>
              </div>
              <div class="payment-stat payment-stat-green">
                <div class="payment-label">Total Paid</div>
                <div class="payment-value" id="payment-paid-display">₹<?= number_format($invoiceTotalPaid, 2) ?></div>
              </div>
              <div class="payment-stat payment-stat-orange">
                <div class="payment-label">Pending</div>
                <div class="payment-value" id="payment-pending-display">₹<?= number_format($invoicePending, 2) ?></div>
              </div>
              <div class="payment-stat payment-stat-form">
                <div class="payment-label">Apply Payment</div>
                <div class="payment-form">
                  <input class="payment-input" id="payment-amount-input" type="number" step="0.01" min="0" max="<?= max(0, $invoicePending) ?>" name="payment_amount" placeholder="Amount" value="0">
                  <select class="payment-select" name="payment_mode">
                    <?php foreach (['Cash', 'Online'] as $mode): ?>
                      <option value="<?= htmlspecialchars($mode) ?>"><?= htmlspecialchars($mode) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="date-picker-field payment-date-field">
                    <input class="payment-input date-display-input" type="text" name="payment_date" value="<?= htmlspecialchars(format_input_date(date('Y-m-d'))) ?>" placeholder="dd/mm/yyyy" inputmode="numeric" autocomplete="off" data-picker-id="date-picker-payment">
                    <button type="button" class="date-picker-button" data-picker-target="date-picker-payment" aria-label="Choose payment date" title="Choose payment date"><?= icon('calendar', 16) ?></button>
                    <input type="date" id="date-picker-payment" class="date-picker-native" value="<?= date('Y-m-d') ?>" tabindex="-1" aria-hidden="true">
                  </div>
                  <input class="payment-input" type="text" name="payment_reference" placeholder="Ref No.">
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <div class="form-actions">
          <div class="form-actions-left">
            <?php if (!$isPopup): ?>
              <a href="module.php?m=<?= $m ?>" class="btn btn-outline"><?= icon('back', 16) ?> Back</a>
            <?php endif; ?>
          </div>
          <div class="form-actions-right">
            <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Save <?= htmlspecialchars(rtrim($config['label'], 's')) ?></button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const teamRoleFields = document.querySelectorAll('input[name="role"]');
      const teamCredentialGroups = document.querySelectorAll('[data-team-credential="1"]');
      const updateTeamCredentialVisibility = () => {
        if (!teamRoleFields.length || !teamCredentialGroups.length) return;
        const selected = document.querySelector('input[name="role"]:checked');
        const isAdminRole = selected && String(selected.value).toLowerCase() === 'admin';
        teamCredentialGroups.forEach(group => {
          group.style.display = isAdminRole ? '' : 'none';
          group.querySelectorAll('input').forEach(input => {
            input.disabled = !isAdminRole;
            if (!isAdminRole) input.value = '';
          });
        });
      };
      teamRoleFields.forEach(radio => radio.addEventListener('change', updateTeamCredentialVisibility));
      updateTeamCredentialVisibility();
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const formatPickerDate = (value) => {
        const parts = value.split('-');
        return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : value;
      };
      document.querySelectorAll('.date-picker-button').forEach((button) => {
        const picker = document.getElementById(button.dataset.pickerTarget);
        const display = document.querySelector('[data-picker-id="' + button.dataset.pickerTarget + '"]');
        if (!picker || !display) return;
        button.addEventListener('click', () => {
          if (typeof picker.showPicker === 'function') picker.showPicker();
          else picker.click();
        });
        picker.addEventListener('change', () => {
          display.value = formatPickerDate(picker.value);
        });
      });

      const tbody = document.getElementById('line-items-body');
      if (!tbody) return;

      const totalInput = document.getElementById('items-total-input');
      const totalDisplay = document.getElementById('items-total-display');
      const paymentTotalDisplay = document.getElementById('payment-total-display');
      const paymentPaidDisplay = document.getElementById('payment-paid-display');
      const paymentPendingDisplay = document.getElementById('payment-pending-display');
      const paymentAmountInput = document.getElementById('payment-amount-input');
      const totalPaid = <?= json_encode((float)$invoiceTotalPaid) ?>;
      const money = (value) => Number(value || 0).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });

      const teamCustomerNames = <?= json_encode($teamCustomerNames, JSON_UNESCAPED_UNICODE) ?>;
      const productCatalog = <?= json_encode(array_map(function ($product) {
                                return ['id' => (int)($product['id'] ?? 0), 'name' => (string)($product['name'] ?? ''), 'price' => module_product_selling_price($product)];
                              }, $productList), JSON_UNESCAPED_UNICODE) ?>;
      const hasLineItemProducts = <?= in_array($m, ['quotations', 'invoices'], true) ? 'true' : 'false' ?>;
      const isInvoice = <?= $m === 'invoices' ? 'true' : 'false' ?>;
      const customerInput = document.querySelector('[name="customer"]');
      const customerPicker = document.querySelector('.customer-picker');
      const filterCustomers = () => {
        if (!customerPicker || !customerInput) return;
        const query = (customerInput.value || '').trim().toLowerCase();
        customerPicker.classList.add('is-open');
        customerPicker.querySelectorAll('.customer-option').forEach((option) => {
          option.hidden = query !== '' && !option.textContent.toLowerCase().includes(query);
        });
      };
      if (customerPicker && customerInput) {
        customerInput.addEventListener('focus', filterCustomers);
        customerInput.addEventListener('input', filterCustomers);
        customerPicker.addEventListener('click', (event) => {
          const option = event.target.closest('.customer-option');
          if (!option) return;
          customerInput.value = option.dataset.value || option.textContent.trim();
          customerPicker.classList.remove('is-open');
          customerInput.dispatchEvent(new Event('change', {
            bubbles: true
          }));
        });
        document.addEventListener('click', (event) => {
          if (!event.target.closest('.customer-picker')) customerPicker.classList.remove('is-open');
        });

        <?php if ($m === 'invoices'): ?>
          const invoiceForm = customerInput.closest('form');
          if (invoiceForm) {
            invoiceForm.addEventListener('submit', function(event) {
              const selectedName = (customerInput.value || '').trim().toLowerCase();
              const allowedNames = Array.from(customerPicker.querySelectorAll('.customer-option'))
                .map(option => String(option.dataset.value || '').trim().toLowerCase())
                .filter(Boolean);
              if (!selectedName || !allowedNames.includes(selectedName)) {
                event.preventDefault();
                customerInput.focus();
                customerPicker.classList.add('is-open');
                alert('Please select a Customer or Wholeseller from the list.');
              }
            });
          }
        <?php endif; ?>
      }
      const updateDiscountVisibility = () => {
        // Invoice discounts are available for every customer.
        if (isInvoice) return;
        if (!customerInput) return;
        const visible = teamCustomerNames.includes((customerInput.value || '').trim().toLowerCase());
        document.querySelectorAll('.team-discount-column').forEach((column) => column.classList.toggle('is-hidden', !visible));
        if (!visible) tbody.querySelectorAll('.item-discount').forEach((input) => {
          input.value = '0';
        });
      };
      const recalc = () => {
        let total = 0;
        tbody.querySelectorAll('.line-item-row').forEach((row) => {
          const qty = parseFloat(row.querySelector('.item-qty')?.value || 0) || 0;
          const priceInput = row.querySelector('.item-price');
          const actualInput = row.querySelector('.item-actual-price');
          const discountInput = row.querySelector('.item-discount');
          const price = parseFloat(priceInput?.value || 0) || 0;

          let rowTotal = 0;
          if (isInvoice && actualInput) {
            let actual = parseFloat(actualInput.value || 0);
            if (!Number.isFinite(actual)) actual = price;
            actual = Math.max(0, Math.min(price, actual));
            actualInput.max = price.toFixed(2);
            actualInput.value = String(Math.round(actual));

            const discount = price > 0 ? ((price - actual) / price) * 100 : 0;
            if (discountInput) discountInput.value = Math.max(0, Math.min(100, discount)).toFixed(2);
            rowTotal = qty * actual;
          } else {
            const discount = parseFloat(discountInput?.value || 0) || 0;
            const subtotal = qty * price;
            rowTotal = Math.max(0, subtotal - (subtotal * Math.min(100, Math.max(0, discount)) / 100));
          }

          const totalField = row.querySelector('.item-total');
          if (totalField) totalField.value = rowTotal.toFixed(2);
          total += rowTotal;
        });

        if (totalInput) totalInput.value = total.toFixed(2);
        if (totalDisplay) totalDisplay.textContent = '₹' + money(total);
        const enteredPayment = Math.max(0, parseFloat(paymentAmountInput?.value || 0) || 0);
        const livePaid = totalPaid + Math.min(enteredPayment, Math.max(0, total - totalPaid));
        const pending = Math.max(0, total - livePaid);
        if (paymentTotalDisplay) paymentTotalDisplay.textContent = '₹' + money(total);
        if (paymentPaidDisplay) paymentPaidDisplay.textContent = '₹' + money(livePaid);
        if (paymentPendingDisplay) paymentPendingDisplay.textContent = '₹' + money(pending);
        if (paymentAmountInput) {
          const originalPending = Math.max(0, total - totalPaid);
          paymentAmountInput.max = originalPending.toFixed(2);
          if (enteredPayment > originalPending) {
            paymentAmountInput.value = originalPending.toFixed(2);
          }
        }
        return total;
      };

      const setCustomItem = (row, itemName = '') => {
        const picker = row.querySelector('.item-product-picker');
        const select = row.querySelector('.item-product-select');
        const description = row.querySelector('.item-description');
        const search = picker?.querySelector('.item-product-search');
        const price = row.querySelector('.item-price');
        const actualPrice = row.querySelector('.item-actual-price');
        if (select) select.value = '';
        if (description) description.value = itemName || (search?.value || '').trim();
        if (search) search.value = itemName || (search.value || '').trim();
        row.classList.add('custom-line-item');
        if (price) {
          price.readOnly = false;
          price.classList.add('custom-rate-input');
          if (!price.value || Number(price.value) === 0) price.value = '0';
        }
        if (actualPrice) {
          actualPrice.max = Number(price?.value || 0).toFixed(2);
          if (!actualPrice.value || Number(actualPrice.value) === 0) actualPrice.value = String(Math.round(Number(price?.value || 0)));
        }
        picker?.classList.remove('is-open');
        recalc();
      };

      const syncProduct = (row, productId) => {
        const product = productCatalog.find((entry) => String(entry.id) === String(productId));
        const description = row.querySelector('.item-description');
        const price = row.querySelector('.item-price');
        const actualPrice = row.querySelector('.item-actual-price');
        row.classList.remove('custom-line-item');
        if (price) {
          price.readOnly = !!product;
          price.classList.toggle('custom-rate-input', !product);
        }
        if (product) {
          if (description) description.value = product.name;
          if (price) price.value = product.price;
          if (actualPrice) {
            actualPrice.max = Number(product.price || 0).toFixed(2);
            actualPrice.value = String(Math.round(Number(product.price || 0)));
          }
        } else {
          if (description) description.value = '';
          if (price) price.value = '0';
          if (actualPrice) {
            actualPrice.max = '0';
            actualPrice.value = '0';
          }
        }
        recalc();
      };

      const filterProductPicker = (picker) => {
        if (!picker) return;
        const search = picker.querySelector('.item-product-search');
        const query = (search?.value || '').trim().toLowerCase();
        picker.classList.add('is-open');
        let visibleCount = 0;
        picker.querySelectorAll('.item-product-option').forEach((option) => {
          const match = query === '' || option.textContent.toLowerCase().includes(query);
          option.hidden = !match;
          if (match) visibleCount++;
        });
        const empty = picker.querySelector('.item-product-empty');
        if (empty) empty.style.display = visibleCount === 0 ? 'block' : 'none';
      };

      const syncPickerSelection = (row, productId) => {
        const picker = row.querySelector('.item-product-picker');
        if (!picker) return;
        const product = productCatalog.find((entry) => String(entry.id) === String(productId));
        const search = picker.querySelector('.item-product-search');
        picker.querySelectorAll('.item-product-option').forEach((option) => {
          option.classList.toggle('selected', String(option.dataset.productId) === String(productId));
        });
        if (search) search.value = product ? product.name : '';
        picker.classList.remove('is-open');
      };

      const attachRowEvents = (row) => {
        row.querySelectorAll('input').forEach((input) => {
          input.addEventListener('input', recalc);
          input.addEventListener('change', recalc);
        });
        const productSearch = row.querySelector('.item-product-search');
        if (productSearch) {
          productSearch.addEventListener('input', function() {
            if (row.classList.contains('custom-line-item')) {
              const description = row.querySelector('.item-description');
              if (description) description.value = this.value;
            }
          });
        }

        const removeButton = row.querySelector('.remove-item');
        if (removeButton) {
          removeButton.addEventListener('click', function() {
            row.remove();
            recalc();
          });
        }
      };

      // Product dropdown/search: delegated so it works for existing and newly added rows.
      tbody.addEventListener('focusin', (event) => {
        const search = event.target.closest('.item-product-search');
        if (search) filterProductPicker(search.closest('.item-product-picker'));
      });
      tbody.addEventListener('input', (event) => {
        const search = event.target.closest('.item-product-search');
        if (search) filterProductPicker(search.closest('.item-product-picker'));
      });
      tbody.addEventListener('click', (event) => {
        const customOption = event.target.closest('.item-product-custom-option');
        if (customOption) {
          const row = customOption.closest('.line-item-row');
          if (row) setCustomItem(row);
          return;
        }
        const option = event.target.closest('.item-product-option');
        if (!option) return;
        const row = option.closest('.line-item-row');
        const picker = option.closest('.item-product-picker');
        const select = row?.querySelector('.item-product-select');
        if (!row || !picker || !select) return;
        select.value = option.dataset.productId || '';
        syncProduct(row, select.value);
        syncPickerSelection(row, select.value);
      });

      const addRow = () => {
        const rows = tbody.querySelectorAll('.line-item-row');
        const index = rows.length;
        const newRow = document.createElement('tr');
        newRow.className = 'line-item-row';
        const productSelect = hasLineItemProducts ? '<div class="product-picker item-product-picker"><input type="search" class="item-product-search" placeholder="Search / select product" autocomplete="off"><div class="product-options item-product-options">' + (productCatalog.length ? productCatalog.map((product) => '<button type="button" class="product-option item-product-option" data-product-id="' + product.id + '" data-price="' + Number(product.price || 0).toFixed(2) + '"><span>' + product.name.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') + '</span><small>₹' + Number(product.price || 0).toLocaleString('en-IN', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        }) + '</small></button>').join('') + '<button type="button" class="product-option item-product-custom-option" data-custom-product="1"><span>+ Add Custom Item</span><small>Enter your own rate</small></button>' : '<button type="button" class="product-option item-product-custom-option" data-custom-product="1"><span>+ Add Custom Item</span><small>Enter your own rate</small></button>') + '</div><input type="hidden" name="items[' + index + '][product_id]" class="item-product-select" value=""></div><input type="hidden" name="items[' + index + '][description]" class="item-description">' : '<input type="text" name="items[' + index + '][description]" placeholder="Item description">';
        newRow.innerHTML = '<td>' + productSelect + '</td>' +
          '<td><input type="number" step="1" min="0" name="items[' + index + '][qty]" class="item-qty" value="1"></td>' +
          (isInvoice ?
            '<td><input type="number" step="0.01" min="0" name="items[' + index + '][price]" class="item-price" value="0" readonly></td>' +
            '<td><input type="number" step="1" min="0" name="items[' + index + '][actual_selling_price]" class="item-actual-price" value="0"></td>' +
            '<td><input type="number" step="0.01" min="0" max="100" name="items[' + index + '][discount_rate]" class="item-discount" value="0" readonly></td>' :
            '<td><input type="number" step="1" min="0" name="items[' + index + '][price]" class="item-price" value="0"></td>' +
            (hasLineItemProducts ? '<td class="team-discount-column' + (teamCustomerNames.includes((customerInput?.value || '').trim().toLowerCase()) ? '' : ' is-hidden') + '"><input type="number" step="0.01" min="0" max="100" name="items[' + index + '][discount_rate]" class="item-discount" value="0"></td>' : '')
          ) +
          '<td><input type="number" step="0.01" min="0" name="items[' + index + '][total]" class="item-total" value="0" readonly></td>' +
          '<td><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></td>';

        tbody.appendChild(newRow);
        attachRowEvents(newRow);
        const removeButton = newRow.querySelector('.remove-item');
        if (removeButton) {
          removeButton.addEventListener('click', function() {
            newRow.remove();
            recalc();
          });
        }
        recalc();
      };

      const addButton = document.getElementById('add-item-row');
      if (addButton) addButton.addEventListener('click', addRow);

      const form = document.querySelector('form[method="post"]');
      if (form) form.addEventListener('submit', recalc);

      tbody.querySelectorAll('.line-item-row').forEach((row) => {
        attachRowEvents(row);
        const select = row.querySelector('.item-product-select');
        if (select && select.value) {
          syncProduct(row, select.value);
          syncPickerSelection(row, select.value);
        } else if (row.querySelector('.item-description')?.value || Number(row.querySelector('.item-price')?.value || 0) > 0) {
          setCustomItem(row, row.querySelector('.item-description')?.value || '');
        }
      });
      document.addEventListener('click', (event) => {
        document.querySelectorAll('.item-product-picker.is-open').forEach((picker) => {
          if (!picker.contains(event.target)) picker.classList.remove('is-open');
        });
      });
      if (customerInput) {
        customerInput.addEventListener('input', updateDiscountVisibility);
        customerInput.addEventListener('change', updateDiscountVisibility);
      }
      if (paymentAmountInput) paymentAmountInput.addEventListener('input', recalc);
      updateDiscountVisibility();
      recalc();
    });
  </script>
<?php if ($isPopup) {
    echo '</body></html>';
  } else {
    require __DIR__ . '/layout_bottom.php';
  }
  exit;
}

/* -------- LIST VIEW -------- */
$rows = member_visible_rows($m, db_get_all($m));
if ($m === 'team') {
  $rows = array_values(array_filter($rows, function ($row) {
    return strcasecmp((string)($row['role'] ?? ''), 'Admin') !== 0;
  }));
}

if ($m === 'leads') {
  $leadInvoices = db_get_all('invoices');
  $leadProducts = db_get_all('products');
  foreach ($rows as &$leadRow) {
    $leadRow['stage'] = lead_automatic_stage($leadRow, $leadInvoices, $leadProducts);
  }
  unset($leadRow);
}

if ($m === 'invoices') {
  foreach ($rows as &$row) {
    $row['status'] = module_invoice_status($row);
    $row['pending'] = max(0, (float)($row['amount'] ?? 0) - module_invoice_paid((string)($row['invoice_no'] ?? '')));
    if (is_admin()) $row['profit'] = module_invoice_profit($row);
  }
  unset($row);
}

/* Calculate total pending amount for every wholeseller. */
if ($m === 'team') {
  $allInvoicesForTeam = db_get_all('invoices');
  foreach ($rows as &$row) {
    $wholesellerName = trim((string)($row['name'] ?? ''));
    $totalPending = 0.0;

    foreach ($allInvoicesForTeam as $invoice) {
      $invoiceCustomer = trim((string)($invoice['customer'] ?? ''));
      if ($wholesellerName === '' || strcasecmp($invoiceCustomer, $wholesellerName) !== 0) {
        continue;
      }

      $invoiceAmount = (float)($invoice['amount'] ?? 0);
      $invoicePaid = module_invoice_paid((string)($invoice['invoice_no'] ?? ''));
      $totalPending += max(0, $invoiceAmount - $invoicePaid);
    }

    $row['pending'] = $totalPending;
  }
  unset($row);
}

$range = $_GET['range'] ?? 'all';
$customFrom = normalize_date_input($_GET['from'] ?? '');
$customTo = normalize_date_input($_GET['to'] ?? '');

if ($m === 'customers') {
  foreach ($rows as &$row) {
    $row['pending'] = customer_stats($row['name'] ?? '')['pending_amount'] ?? 0;
  }
  unset($row);
}

function get_record_date_value($row, $module, $fallback = '')
{
  $keys = ['date', 'due_date', 'valid_until', 'created_at', 'updated_at'];
  foreach ($keys as $key) {
    if (!empty($row[$key] ?? '')) return $row[$key];
  }
  if (isset($row['created_at'])) return $row['created_at'];
  return $fallback;
}

function match_date_range($value, $range, $from = '', $to = '')
{
  if ($range === 'all') return true;
  $stamp = strtotime((string)$value);
  if ($stamp === false) return true;
  $now = new DateTime('today');
  $today = $now->format('Y-m-d');
  $todayTs = strtotime($today);
  switch ($range) {
    case 'today':
      return date('Y-m-d', $stamp) === $today;
    case 'yesterday':
      return date('Y-m-d', $stamp) === date('Y-m-d', strtotime('-1 day'));
    case '7days':
      return $stamp >= strtotime('-6 days') && $stamp <= time();
    case '30days':
      return $stamp >= strtotime('-29 days') && $stamp <= time();
    case 'month':
      return date('Y-m', $stamp) === date('Y-m');
    case 'year':
      return date('Y', $stamp) === date('Y');
    case 'lastyear':
      return date('Y', $stamp) === (string)(date('Y') - 1);
    case 'custom':
      if ($from !== '' && $stamp < strtotime($from)) return false;
      if ($to !== '' && $stamp > strtotime($to . ' 23:59:59')) return false;
      return true;
    default:
      return true;
  }
}

if ($range !== 'all' || $customFrom !== '' || $customTo !== '') {
  $rows = array_values(array_filter($rows, function ($row) use ($m, $range, $customFrom, $customTo) {
    $value = get_record_date_value($row, $m);
    return match_date_range($value, $range, $customFrom, $customTo);
  }));
}

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
  $searchColumns = $m === 'customers' ? ['name', 'company', 'phone', 'email'] : $config['columns'];
  $rows = array_filter($rows, function ($row) use ($q, $searchColumns) {
    foreach ($searchColumns as $col) {
      if (stripos((string)($row[$col] ?? ''), $q) !== false) return true;
    }
    return false;
  });
}

if ($m === 'leads') {
  $stageFilter = $_GET['stage'] ?? 'All';
  $leadStages = array_merge(['All'], $MODULES['leads']['fields']['stage']['options']);
  $statusCounts = ['All' => count($rows)];
  foreach ($leadStages as $stage) {
    if ($stage !== 'All') $statusCounts[$stage] = count(array_filter($rows, fn($row) => ($row['stage'] ?? 'New') === $stage));
  }
  if ($stageFilter !== 'All') {
    $rows = array_values(array_filter($rows, function ($row) use ($stageFilter) {
      return (($row['stage'] ?? 'New') === $stageFilter);
    }));
  }
  $invoiceStatusFilter = 'All';
  $invoiceStatuses = [];
} elseif ($m === 'invoices') {
  $stageFilter = 'All';
  $leadStages = [];
  $invoiceStatuses = ['All', 'Paid', 'Full Pending', 'Partly Paid', 'Over due'];
  $statusCounts = ['All' => count($rows)];
  foreach ($invoiceStatuses as $invoiceStatus) {
    if ($invoiceStatus !== 'All') $statusCounts[$invoiceStatus] = count(array_filter($rows, fn($row) => ($row['status'] ?? '') === $invoiceStatus));
  }
  $invoiceStatusFilter = $_GET['status'] ?? 'All';
  if (!in_array($invoiceStatusFilter, $invoiceStatuses, true)) {
    $invoiceStatusFilter = 'All';
  }
  if ($invoiceStatusFilter !== 'All') {
    $rows = array_values(array_filter($rows, function ($row) use ($invoiceStatusFilter) {
      return ($row['status'] ?? '') === $invoiceStatusFilter;
    }));
  }
} elseif ($m === 'quotations') {
  $stageFilter = 'All';
  $leadStages = [];
  $invoiceStatusFilter = 'All';
  $invoiceStatuses = [];
  $recordStatuses = array_merge(['All'], $MODULES[$m]['fields']['status']['options'] ?? []);
  $statusCounts = ['All' => count($rows)];
  foreach ($recordStatuses as $recordStatus) {
    if ($recordStatus === 'All') continue;
    $statusCounts[$recordStatus] = count(array_filter($rows, fn($row) => quotation_effective_status($row) === $recordStatus));
  }
  $recordStatusFilter = $_GET['status'] ?? 'All';
  if (!in_array($recordStatusFilter, $recordStatuses, true)) {
    $recordStatusFilter = 'All';
  }
  if ($recordStatusFilter !== 'All') {
    $rows = array_values(array_filter($rows, function ($row) use ($recordStatusFilter) {
      return quotation_effective_status($row) === $recordStatusFilter;
    }));
  }
} elseif ($m === 'tasks') {
  $stageFilter = 'All';
  $leadStages = [];
  $invoiceStatusFilter = 'All';
  $invoiceStatuses = [];
  $recordStatuses = array_merge(['All'], $MODULES[$m]['fields']['status']['options'] ?? []);
  $statusCounts = ['All' => count($rows)];
  foreach ($recordStatuses as $recordStatus) {
    if ($recordStatus !== 'All') $statusCounts[$recordStatus] = count(array_filter($rows, fn($row) => ($row['status'] ?? '') === $recordStatus));
  }
  $recordStatusFilter = $_GET['status'] ?? 'All';
  if (!in_array($recordStatusFilter, $recordStatuses, true)) {
    $recordStatusFilter = 'All';
  }
  if ($recordStatusFilter !== 'All') {
    $rows = array_values(array_filter($rows, function ($row) use ($recordStatusFilter) {
      return ($row['status'] ?? '') === $recordStatusFilter;
    }));
  }
} else {
  $stageFilter = 'All';
  $leadStages = [];
  $invoiceStatusFilter = 'All';
  $invoiceStatuses = [];
  $recordStatusFilter = 'All';
  $recordStatuses = [];
  $statusCounts = [];
}
// newest first
usort($rows, fn($a, $b) => ($b['id'] ?? 0) <=> ($a['id'] ?? 0));

$hasPdf = in_array($m, ['invoices', 'quotations']);
$pdfBase = $m === 'invoices' ? 'invoice_print.php' : 'quotation_print.php';
$displayColumns = $m === 'customers'
  ? (is_subadmin() ? ['name', 'phone'] : ['name', 'company', 'phone', 'email', 'pending'])
  : ($m === 'invoices'
    ? (is_admin() ? ['invoice_no', 'customer', 'amount', 'status', 'pending', 'profit', 'due_date'] : ['invoice_no', 'customer', 'amount', 'status', 'pending', 'due_date'])
    : ($m === 'team'
      ? ['name', 'phone', 'role', 'username', 'pending']
      : $config['columns']));

require __DIR__ . '/layout_top.php';
?>

<?php if (isset($_GET['ok'])): ?>
  <div class="alert"><?= icon('check-circle', 16) ?> Record <?= htmlspecialchars($_GET['ok']) ?> successfully.</div>
<?php endif; ?>

<div class="panel">
  <div class="panel-header">
    <h2><?= icon($config['icon']) ?> <?= htmlspecialchars($config['label']) ?> <span class="count-pill">(<?= count($rows) ?>)</span></h2>
    <div class="toolbar">
      <?php if ($m === 'tasks'): ?>
        <div class="view-switch">
          <a href="module.php?m=tasks&view=list" class="active"><?= icon('list', 15) ?> List</a>
          <a href="kanban.php?m=tasks"><?= icon('board', 15) ?> Board</a>
        </div>
      <?php elseif ($m === 'quotations'): ?>
        <div class="view-switch">
          <a href="module.php?m=quotations&view=list" class="active"><?= icon('list', 15) ?> List</a>
          <a href="kanban.php?m=quotations"><?= icon('board', 15) ?> Board</a>
        </div>
      <?php elseif ($m === 'leads'): ?>
        <div class="view-switch">
          <a href="module.php?m=leads&view=list" class="active"><?= icon('list', 15) ?> List</a>
          <a href="kanban.php?m=leads"><?= icon('board', 15) ?> Board</a>
        </div>
      <?php endif; ?>
      <form method="get" class="search-box">
        <input type="hidden" name="m" value="<?= $m ?>">
        <?php if ($m === 'leads' && $stageFilter !== 'All'): ?>
          <input type="hidden" name="stage" value="<?= htmlspecialchars($stageFilter) ?>">
        <?php endif; ?>
        <?php if ($m === 'invoices' && $invoiceStatusFilter !== 'All'): ?>
          <input type="hidden" name="status" value="<?= htmlspecialchars($invoiceStatusFilter) ?>">
        <?php endif; ?>
        <?php if (in_array($m, ['quotations', 'tasks'], true) && $recordStatusFilter !== 'All'): ?>
          <input type="hidden" name="status" value="<?= htmlspecialchars($recordStatusFilter) ?>">
        <?php endif; ?>
        <span class="search-icon"><?= icon('search', 15) ?></span>
        <input type="text" name="q" placeholder="Search..." value="<?= htmlspecialchars($q) ?>">
      </form>
      <?php if (is_admin()): ?><a href="export.php?module=<?= $m ?>&range=<?= urlencode($range) ?>&from=<?= urlencode($customFrom) ?>&to=<?= urlencode($customTo) ?>" class="btn btn-outline btn-sm export-popup-trigger">Export Excel</a><?php endif; ?>
      <?php if (is_admin() || (is_subadmin() && in_array($m, ['quotations', 'invoices', 'leads', 'tasks'], true)) || (is_subadmin() && $m === 'customers')): ?><a href="module.php?m=<?= $m ?>&action=new" class="btn btn-primary"><?= icon('add', 16) ?> Add New</a><?php endif; ?>
    </div>
  </div>
  <div class="panel-body">
    <?php $dateRanges = [
      'all' => 'All',
      'today' => 'Today',
      'yesterday' => 'Yesterday',
      '7days' => 'Last 7 Days',
      '30days' => 'Last 30 Days',
      'month' => 'This Month',
      'year' => 'This Year',
      'lastyear' => 'Last Year',
      'custom' => 'Custom',
    ]; ?>
    <div class="filter-row">
      <?php if ($m === 'leads'): ?>
        <div class="status-tabs">
          <?php foreach ($leadStages as $stage): ?>
            <a class="status-tab <?= ($stage === $stageFilter ? 'active' : '') ?>" href="module.php?m=leads<?= $stage === 'All' ? '' : '&stage=' . urlencode($stage) ?>"><?= htmlspecialchars($stage) ?> (<?= (int)($statusCounts[$stage] ?? 0) ?>)</a>
          <?php endforeach; ?>
        </div>
      <?php elseif ($m === 'invoices'): ?>
        <div class="status-tabs">
          <?php foreach ($invoiceStatuses as $invoiceStatus): ?>
            <a class="status-tab <?= ($invoiceStatus === $invoiceStatusFilter ? 'active' : '') ?>" href="module.php?m=invoices<?= $invoiceStatus === 'All' ? '' : '&status=' . urlencode($invoiceStatus) ?><?= $range !== 'all' ? '&range=' . urlencode($range) : '' ?><?= $customFrom !== '' ? '&from=' . urlencode($customFrom) : '' ?><?= $customTo !== '' ? '&to=' . urlencode($customTo) : '' ?>"><?= htmlspecialchars($invoiceStatus) ?> (<?= (int)($statusCounts[$invoiceStatus] ?? 0) ?>)</a>
          <?php endforeach; ?>
        </div>
      <?php elseif (in_array($m, ['quotations', 'tasks'], true)): ?>
        <div class="status-tabs">
          <?php foreach ($recordStatuses as $recordStatus): ?>
            <a class="status-tab <?= ($recordStatus === $recordStatusFilter ? 'active' : '') ?>" href="module.php?m=<?= $m ?><?= $recordStatus === 'All' ? '' : '&status=' . urlencode($recordStatus) ?><?= $range !== 'all' ? '&range=' . urlencode($range) : '' ?><?= $customFrom !== '' ? '&from=' . urlencode($customFrom) : '' ?><?= $customTo !== '' ? '&to=' . urlencode($customTo) : '' ?>"><?= htmlspecialchars($recordStatus) ?> (<?= (int)($statusCounts[$recordStatus] ?? 0) ?>)</a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <div class="date-filter-bar">
        <form method="get" class="date-custom-form time-filter">
          <input type="hidden" name="m" value="<?= $m ?>">
          <?php if ($m === 'leads' && $stageFilter !== 'All'): ?>
            <input type="hidden" name="stage" value="<?= htmlspecialchars($stageFilter) ?>">
          <?php endif; ?>
          <label for="time-range" class="time-filter-label">Time</label>
          <select id="time-range" name="range" class="time-range-select" onchange="this.form.submit()">
            <?php foreach ($dateRanges as $key => $label): ?>
              <option value="<?= htmlspecialchars($key) ?>" <?= $range === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
          </select>
          <?php if ($range === 'custom' || $customFrom !== '' || $customTo !== ''): ?>
            <input type="text" name="from" value="<?= htmlspecialchars(format_input_date($customFrom, '')) ?>" placeholder="dd/mm/yyyy" inputmode="numeric" aria-label="From date">
            <input type="text" name="to" value="<?= htmlspecialchars(format_input_date($customTo, '')) ?>" placeholder="dd/mm/yyyy" inputmode="numeric" aria-label="To date">
            <button type="submit" class="btn btn-outline btn-sm">Apply</button>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <?php if (empty($rows)): ?>
      <div class="empty-state">
        <div class="empty-icon"><?= icon($config['icon'], 34) ?></div>
        No records yet. Click "Add New" to create the first <?= strtolower(rtrim($config['label'], 's')) ?>.
      </div>
    <?php else: ?>

      <!-- Desktop table (>=900px) -->
      <table class="data-table">
        <thead>
          <tr>
            <?php foreach ($displayColumns as $col): ?>
              <th><?= htmlspecialchars(
                    $col === 'profit'
                      ? 'Profit (₹)'
                      : ((in_array($m, ['customers', 'invoices', 'team'], true) && $col === 'pending')
                        ? ($m === 'team' ? 'Total Pending Amount' : 'Pending Amount')
                        : ($config['fields'][$col]['label'] ?? ucfirst(str_replace('_', ' ', $col))))
                  ) ?></th>
            <?php endforeach; ?>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <tr>
              <?php foreach ($displayColumns as $i => $col):
                $fieldType = $config['fields'][$col]['type'] ?? 'text';
                if ($m === 'invoices' && $col === 'status') $fieldType = 'select';
                if ($m === 'invoices' && $col === 'profit') {
                  $fieldType = 'number';
                  $val = (float)($row['profit'] ?? 0);
                } elseif (in_array($m, ['customers', 'invoices', 'team'], true) && $col === 'pending') {
                  $fieldType = 'number';
                  $val = (float)($row['pending'] ?? 0);
                } else {
                  $val = normalize_field_value($row[$col] ?? '');
                  if ($fieldType === 'date') $val = format_display_date($row[$col] ?? '');
                }
                $isFirstCol = ($i === 0);
              ?>
                <td>
                  <?php if ($m === 'customers' && $col === 'name' && !is_subadmin()): ?>
                    <a href="customer_detail.php?id=<?= $row['id'] ?>" class="cell-link"><?= htmlspecialchars($val ?: '-') ?></a>
                  <?php elseif ($m === 'customers' && $col === 'name'): ?>
                    <?= htmlspecialchars($val ?: '-') ?>
                  <?php elseif ($m === 'team' && $col === 'name'): ?>
                    <a href="team_detail.php?id=<?= $row['id'] ?>" class="cell-link"><?= htmlspecialchars($val ?: '-') ?></a>
                  <?php elseif ($m === 'invoices' && $col === 'customer' && ($partyHref = module_invoice_party_link($row))): ?>
                    <a href="<?= htmlspecialchars($partyHref) ?>" target="_self" class="cell-link invoice-party-link" style="position:relative;z-index:5;cursor:pointer;pointer-events:auto;" title="Open profile" onclick="event.stopPropagation();"><?= htmlspecialchars($val) ?></a>
                  <?php elseif ($col === 'customer' && ($customerId = module_customer_id($val))): ?>
                    <a href="customer_detail.php?id=<?= $customerId ?>" class="cell-link" title="Open customer profile"><?= htmlspecialchars($val) ?></a>
                  <?php elseif ($col === 'customer' && ($wholesellerId = module_wholeseller_id($val))): ?>
                    <a href="team_detail.php?id=<?= $wholesellerId ?>" class="cell-link" title="Open wholeseller profile"><?= htmlspecialchars($val) ?></a>
                  <?php elseif ($fieldType === 'select'): ?>
                    <?php $displayBadge = $m === 'quotations' ? quotation_effective_status($row) : $val; ?>
                    <span class="badge <?= badge_class($displayBadge) ?>"><?= htmlspecialchars($displayBadge ?: '-') ?></span>
                  <?php elseif ($fieldType === 'number'): ?>
                    <?= icon('rupee', 12) ?> <?= number_format((float)$val, 2) ?>
                  <?php else: ?>
                    <?= htmlspecialchars($val ?: '-') ?>
                  <?php endif; ?>
                </td>
              <?php endforeach; ?>
              <td class="row-actions">
                <?php if (in_array($m, ['leads', 'quotations', 'tasks'], true)): ?>
                  <a class="btn btn-outline btn-sm" href="module.php?m=<?= $m ?>&amp;action=view&amp;id=<?= $row['id'] ?>" title="View record"><?= icon('view', 14) ?> View</a>
                <?php elseif ($m === 'invoices' && is_admin()): ?>
                  <a class="btn btn-outline btn-sm" href="invoice_profit.php?id=<?= $row['id'] ?>" target="_blank" title="Download Profit PDF"><?= icon('download', 14) ?> Profit PDF</a>
                <?php endif; ?>
                <?php if ($hasPdf): ?>
                  <a class="btn btn-outline btn-sm" href="<?= $pdfBase ?>?id=<?= $row['id'] ?>" target="_blank" title="Download PDF"><?= icon('download', 14) ?> PDF</a>
                <?php endif; ?>
                <?php if ($m === 'quotations' && is_admin() && quotation_effective_status($row) !== 'Converted'): ?>
                  <a class="btn btn-outline btn-sm" href="module.php?m=invoices&amp;action=new&amp;copy_from=quotations&amp;id=<?= $row['id'] ?>" title="Convert to Invoice"><?= icon('invoices', 14) ?> Invoice</a>
                <?php endif; ?>
                <?php if (is_admin() || (is_subadmin() && in_array($m, ['leads', 'quotations', 'invoices', 'tasks'], true) && member_can_access_record($row))): ?>
                  <a class="btn btn-outline btn-sm" href="module.php?m=<?= $m ?>&action=edit&id=<?= $row['id'] ?>" title="Edit"><?= icon('edit', 14) ?></a>
                <?php endif; ?>
                <?php if (is_admin()): ?>
                  <a class="btn btn-danger btn-sm" href="module.php?m=<?= $m ?>&action=delete&id=<?= $row['id'] ?>"
                    onclick="return confirm('Delete this record?')" title="Delete"><?= icon('delete', 14) ?></a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Mobile / Tablet cards (<900px) -->
      <div class="card-list">
        <?php foreach ($rows as $row):
          $primaryCol = $displayColumns[0];
          $statusCol = null;
          if ($m === 'invoices') $statusCol = 'status';
          foreach ($displayColumns as $c) {
            if (($config['fields'][$c]['type'] ?? '') === 'select') {
              $statusCol = $c;
              break;
            }
          }
        ?>
          <div class="record-card">
            <div class="card-top">
              <div class="card-title">
                <?php if ($m === 'customers' && !is_subadmin()): ?>
                  <a href="customer_detail.php?id=<?= $row['id'] ?>" class="cell-link"><?= htmlspecialchars($row[$primaryCol] ?? '-') ?></a>
                <?php elseif ($m === 'customers'): ?>
                  <?= htmlspecialchars($row[$primaryCol] ?? '-') ?>
                <?php elseif ($m === 'team'): ?>
                  <a href="team_detail.php?id=<?= $row['id'] ?>" class="cell-link"><?= htmlspecialchars($row[$primaryCol] ?? '-') ?></a>
                <?php else: ?>
                  <?= htmlspecialchars($row[$primaryCol] ?? '-') ?>
                <?php endif; ?>
              </div>
              <?php if ($statusCol): ?>
                <span class="badge <?= badge_class($row[$statusCol] ?? '') ?>"><?= htmlspecialchars($row[$statusCol] ?: '-') ?></span>
              <?php endif; ?>
            </div>
            <div class="meta-grid">
              <?php foreach ($displayColumns as $col):
                if ($col === $primaryCol || $col === $statusCol) continue;
                $fieldType = $config['fields'][$col]['type'] ?? 'text';
                if ($m === 'invoices' && $col === 'profit') {
                  $fieldType = 'number';
                  $val = (float)($row['profit'] ?? 0);
                } elseif (in_array($m, ['customers', 'invoices', 'team'], true) && $col === 'pending') {
                  $fieldType = 'number';
                  $val = (float)($row['pending'] ?? 0);
                } else {
                  $val = normalize_field_value($row[$col] ?? '');
                  if ($fieldType === 'date') $val = format_display_date($row[$col] ?? '');
                }
              ?>
                <div>
                  <?php
                  if ($col === 'profit') {
                    $columnLabel = 'Profit (₹)';
                  } elseif (in_array($m, ['customers', 'invoices', 'team'], true) && $col === 'pending') {
                    $columnLabel = $m === 'team' ? 'Total Pending Amount' : 'Pending Amount';
                  } else {
                    $columnLabel = $config['fields'][$col]['label'] ?? ucfirst(str_replace('_', ' ', $col));
                  }
                  ?>
                  <?= htmlspecialchars($columnLabel) ?>:
                  <b><?php if ($col === 'customer' && $m === 'invoices' && ($wholesellerId = module_wholeseller_id($val))): ?><a href="team_detail.php?id=<?= $wholesellerId ?>" class="cell-link invoice-party-link wholeseller-link" title="Open wholeseller profile"><?= htmlspecialchars($val) ?></a><?php elseif ($col === 'customer' && $m === 'invoices' && ($customerId = module_customer_id($val))): ?><a href="customer_detail.php?id=<?= $customerId ?>" class="cell-link invoice-party-link customer-link" title="Open customer profile"><?= htmlspecialchars($val) ?></a><?php elseif ($col === 'customer' && ($partyHref = module_invoice_party_link($row))): ?><a href="<?= htmlspecialchars($partyHref) ?>" target="_self" class="cell-link invoice-party-link" style="position:relative;z-index:5;cursor:pointer;pointer-events:auto;" title="Open profile" onclick="event.stopPropagation();"><?= htmlspecialchars($val) ?></a><?php elseif ($col === 'customer' && ($customerId = module_customer_id($val))): ?><a href="customer_detail.php?id=<?= $customerId ?>" class="cell-link" title="Open customer profile"><?= htmlspecialchars($val) ?></a><?php elseif ($col === 'customer' && ($wholesellerId = module_wholeseller_id($val))): ?><a href="team_detail.php?id=<?= $wholesellerId ?>" class="cell-link" title="Open wholeseller profile"><?= htmlspecialchars($val) ?></a><?php elseif ($fieldType === 'number'): ?>₹<?= number_format((float)$val, 2) ?><?php else: ?><?= htmlspecialchars($val ?: '-') ?><?php endif; ?></b>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="card-actions">
              <?php if (in_array($m, ['leads', 'quotations', 'tasks'], true)): ?>
                <a class="btn btn-outline btn-sm" href="module.php?m=<?= $m ?>&amp;action=view&amp;id=<?= $row['id'] ?>"><?= icon('view', 14) ?> View</a>
              <?php elseif ($m === 'invoices' && is_admin()): ?>
                <a class="btn btn-outline btn-sm" href="invoice_profit.php?id=<?= $row['id'] ?>" target="_blank"><?= icon('download', 14) ?> Profit PDF</a>
              <?php endif; ?>
              <?php if ($hasPdf): ?>
                <a class="btn btn-outline btn-sm" href="<?= $pdfBase ?>?id=<?= $row['id'] ?>" target="_blank"><?= icon('download', 14) ?> PDF</a>
              <?php endif; ?>
              <?php if ($m === 'quotations' && is_admin()): ?>
                <a class="btn btn-outline btn-sm" href="module.php?m=invoices&amp;action=new&amp;copy_from=quotations&amp;id=<?= $row['id'] ?>"><?= icon('invoices', 14) ?> Invoice</a>
              <?php endif; ?>
              <?php if (is_admin() || (is_subadmin() && in_array($m, ['leads', 'quotations', 'invoices', 'tasks'], true) && member_can_access_record($row))): ?>
                <a class="btn btn-outline btn-sm" href="module.php?m=<?= $m ?>&action=edit&id=<?= $row['id'] ?>"><?= icon('edit', 14) ?> Edit</a>
              <?php endif; ?>
              <?php if (is_admin()): ?>
                <a class="btn btn-danger btn-sm" href="module.php?m=<?= $m ?>&action=delete&id=<?= $row['id'] ?>"
                  onclick="return confirm('Delete this record?')"><?= icon('delete', 14) ?> Delete</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    <?php endif; ?>
  </div>
</div>

<div class="form-modal" id="form-modal" aria-hidden="true">
  <div class="form-modal-dialog" role="dialog" aria-modal="true" aria-label="Form">
    <button type="button" class="form-modal-close" aria-label="Close form">&times;</button>
    <iframe title="Add or edit form" id="form-modal-frame"></iframe>
  </div>
</div>

<div class="form-modal" id="export-modal" aria-hidden="true">
  <div class="form-modal-dialog" role="dialog" aria-modal="true" aria-label="Export data form">
    <button type="button" class="form-modal-close" aria-label="Close export form">&times;</button>
    <iframe title="Export data form" id="export-modal-frame"></iframe>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const formModal = document.getElementById('form-modal');
    const formModalFrame = document.getElementById('form-modal-frame');
    const closeFormModal = function() {
      if (!formModal) return;
      formModal.classList.remove('is-open');
      formModal.setAttribute('aria-hidden', 'true');
      if (formModalFrame) formModalFrame.src = 'about:blank';
    };
    if (formModal && formModalFrame) {
      document.querySelectorAll('a.open-form-popup').forEach(function(link) {
        link.addEventListener('click', function(event) {
          event.preventDefault();
          const popupUrl = new URL(link.href, window.location.href);
          popupUrl.searchParams.set('popup', '1');
          formModalFrame.src = popupUrl.toString();
          formModal.classList.add('is-open');
          formModal.setAttribute('aria-hidden', 'false');
        });
      });
      formModal.querySelector('.form-modal-close').addEventListener('click', closeFormModal);
      formModal.addEventListener('click', function(event) {
        if (event.target === formModal) closeFormModal();
      });
    }

    const exportModal = document.getElementById('export-modal');
    const exportFrame = document.getElementById('export-modal-frame');
    if (exportModal && exportFrame) {
      const closeExportModal = function() {
        exportModal.classList.remove('is-open');
        exportModal.setAttribute('aria-hidden', 'true');
        exportFrame.src = 'about:blank';
      };
      document.querySelectorAll('.export-popup-trigger').forEach(function(link) {
        link.addEventListener('click', function(event) {
          event.preventDefault();
          exportFrame.src = link.href;
          exportModal.classList.add('is-open');
          exportModal.setAttribute('aria-hidden', 'false');
        });
      });
      exportModal.querySelector('.form-modal-close').addEventListener('click', closeExportModal);
      exportModal.addEventListener('click', function(event) {
        if (event.target === exportModal) closeExportModal();
      });
    }

    document.querySelectorAll('.password-toggle').forEach(function(button) {
      button.addEventListener('click', function() {
        const input = document.getElementById(button.dataset.target);
        const showIcon = button.querySelector('.password-icon-show');
        const hideIcon = button.querySelector('.password-icon-hide');
        if (!input) return;
        const showing = input.type === 'password';
        input.type = showing ? 'text' : 'password';
        showIcon.classList.toggle('password-icon-hidden', showing);
        hideIcon.classList.toggle('password-icon-hidden', !showing);
        button.setAttribute('aria-label', showing ? 'Hide password' : 'Show password');
        button.setAttribute('title', showing ? 'Hide password' : 'Show password');
      });
    });

    const searchInput = document.querySelector('.search-box input[name="q"]');
    if (searchInput) {
      const form = searchInput.closest('form');
      if (form) {
        let searchTimer = null;
        searchInput.addEventListener('input', function() {
          clearTimeout(searchTimer);
          searchTimer = setTimeout(function() {
            form.submit();
          }, 350);
        });
      }
    }

    const tbody = document.getElementById('line-items-body');
    if (tbody) {
      const totalInput = document.getElementById('items-total-input');
      const totalDisplay = document.getElementById('items-total-display');
      const money = (value) => Number(value || 0).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });

      const recalc = () => {
        let total = 0;
        tbody.querySelectorAll('.line-item-row').forEach((row) => {
          const qty = parseFloat(row.querySelector('.item-qty')?.value || 0);
          const price = parseFloat(row.querySelector('.item-price')?.value || 0);
          const rowTotal = Number.isFinite(qty) && Number.isFinite(price) ? qty * price : 0;
          const totalField = row.querySelector('.item-total');
          if (totalField) {
            totalField.value = rowTotal.toFixed(2);
          }
          total += rowTotal;
        });

        if (totalInput) totalInput.value = total.toFixed(2);
        if (totalDisplay) totalDisplay.textContent = '₹' + money(total);
        return total;
      };

      const addRow = () => {
        const rows = tbody.querySelectorAll('.line-item-row');
        const index = rows.length;
        const newRow = document.createElement('tr');
        newRow.className = 'line-item-row';
        newRow.innerHTML = '<td><input type="text" name="items[' + index + '][description]" placeholder="Item description"></td>' +
          '<td><input type="number" step="1" min="0" name="items[' + index + '][qty]" class="item-qty" value="1"></td>' +
          '<td><input type="number" step="1" min="0" name="items[' + index + '][price]" class="item-price" value="0"></td>' +
          '<td><input type="number" step="0.01" min="0" name="items[' + index + '][total]" class="item-total" value="0" readonly></td>' +
          '<td><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></td>';

        tbody.appendChild(newRow);
        newRow.querySelector('.remove-item').addEventListener('click', function() {
          newRow.remove();
          recalc();
        });

        newRow.querySelectorAll('input').forEach((input) => {
          input.addEventListener('input', recalc);
          input.addEventListener('change', recalc);
        });
        recalc();
      };

      const attachRowEvents = (row) => {
        row.querySelectorAll('input').forEach((input) => {
          input.addEventListener('input', recalc);
          input.addEventListener('change', recalc);
        });
        const productSearch = row.querySelector('.item-product-search');
        if (productSearch) {
          productSearch.addEventListener('input', function() {
            if (row.classList.contains('custom-line-item')) {
              const description = row.querySelector('.item-description');
              if (description) description.value = this.value;
            }
          });
        }

        const removeButton = row.querySelector('.remove-item');
        if (removeButton) {
          removeButton.addEventListener('click', function() {
            row.remove();
            recalc();
          });
        }
      };

      window.recalcLineItems = recalc;

      const invoiceForm = document.querySelector('form[method="post"]');
      if (invoiceForm) {
        invoiceForm.addEventListener('submit', function() {
          recalc();
        });
      }

      const addButton = document.getElementById('add-item-row');
      if (addButton) {
        addButton.addEventListener('click', addRow);
      }

      tbody.querySelectorAll('.line-item-row').forEach((row) => {
        attachRowEvents(row);
      });

      recalc();
    }

  });
</script>
<?php require __DIR__ . '/layout_bottom.php'; ?>