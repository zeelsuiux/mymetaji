<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/icons.php';

$activeModule = 'calendar';
$pageTitle = 'Calendar';

$view = $_GET['view'] ?? 'month';
$selectedDate = $_GET['date'] ?? date('Y-m-d');
$selectedDateObj = new DateTimeImmutable($selectedDate);
$month = (int)($selectedDateObj->format('n'));
$year = (int)($selectedDateObj->format('Y'));

$meetings = is_admin() ? db_get_all('meetings') : [];
$invoices = member_visible_rows('invoices', db_get_all('invoices'));
$leads = is_admin() || is_subadmin() ? member_visible_rows('leads', db_get_all('leads')) : [];
$payments = member_visible_rows('payments', db_get_all('payments'));
$quotations = member_visible_rows('quotations', db_get_all('quotations'));
$expenses = is_admin() ? db_get_all('expenses') : [];
$tasks = (is_admin() || is_subadmin()) ? array_values(array_filter(db_get_all('tasks'), 'member_can_access_record')) : [];

function d($value)
{
  if ($value === null || $value === '') return '';

  $value = trim((string)$value);

  // Fix legacy dates saved with a 2-digit year, e.g. 0026-09-12 -> 2026-09-12.
  // This keeps old meeting records visible on the correct calendar date.
  if (preg_match('/^(\d{4})-(\d{2})-(\d{2})(?:\s.*)?$/', $value, $m)) {
    $year = (int)$m[1];
    if ($year >= 0 && $year < 100) {
      $year += 2000;
      $value = sprintf('%04d-%02d-%02d', $year, (int)$m[2], (int)$m[3]);
    }
  }

  $stamp = strtotime($value);
  return $stamp ? date('Y-m-d', $stamp) : '';
}

function day_items($dateStr, $items, $field)
{
  return array_values(array_filter($items, function ($item) use ($dateStr, $field) {
    $value = $item[$field] ?? '';
    return d($value) === $dateStr;
  }));
}

function item_total($items)
{
  $total = 0;
  foreach ($items as $item) {
    $total += (float)($item['amount'] ?? 0);
  }
  return $total;
}

function summary_box($label, $value, $color)
{
  return '<div class="kpi-card" style="min-width:180px; background:' . $color . ';"><div class="label">' . htmlspecialchars($label) . '</div><div class="value">' . htmlspecialchars($value) . '</div></div>';
}

$monthStart = new DateTimeImmutable(sprintf('%d-%02d-01', $year, $month));
$prevMonth = $monthStart->modify('-1 month');
$nextMonth = $monthStart->modify('+1 month');

if ($view === 'week') {
  $weekStart = (clone $selectedDateObj)->modify('monday this week');
  $calendarDays = [];
  for ($i = 0; $i < 7; $i++) {
    $calendarDays[] = new DateTimeImmutable($weekStart->format('Y-m-d') . ' + ' . $i . ' days');
  }
} elseif ($view === 'day') {
  $calendarDays = [$selectedDateObj];
} else {
  $startOfWeek = (int)$monthStart->format('N');
  $calendarDays = [];
  for ($i = 1; $i <= 42; $i++) {
    $dayNum = $i - $startOfWeek + 1;
    $calendarDays[] = $monthStart->modify('+' . ($dayNum - 1) . ' day');
  }
}

$rangeStart = $calendarDays[0]->format('Y-m-d');
$rangeEnd = $calendarDays[count($calendarDays) - 1]->format('Y-m-d');

$selectedSummary = [];
foreach ($calendarDays as $date) {
  $dateStr = $date->format('Y-m-d');
  $selectedSummary[$dateStr] = [
    'meetings' => day_items($dateStr, $meetings, 'date'),
    'orders' => array_values(array_filter($invoices, function ($item) use ($dateStr) {
      return d($item['created_at'] ?? '') === $dateStr;
    })),
    'due_invoices' => array_values(array_filter($invoices, function ($item) use ($dateStr) {
      return d($item['due_date'] ?? '') === $dateStr;
    })),
    'lead_updates' => array_values(array_filter($leads, function ($item) use ($dateStr) {
      return d($item['created_at'] ?? '') === $dateStr || d($item['updated_at'] ?? '') === $dateStr;
    })),
    'payments' => array_values(array_filter($payments, function ($item) use ($dateStr) {
      return d($item['date'] ?? '') === $dateStr;
    })),
    'expenses' => array_values(array_filter($expenses, function ($item) use ($dateStr) {
      return d($item['date'] ?? '') === $dateStr || d($item['created_at'] ?? '') === $dateStr;
    })),
    'quotations' => array_values(array_filter($quotations, function ($item) use ($dateStr) {
      return d($item['valid_until'] ?? '') === $dateStr || d($item['updated_at'] ?? '') === $dateStr;
    })),
    'tasks' => array_values(array_filter($tasks, function ($item) use ($dateStr) {
      return d($item['due_date'] ?? '') === $dateStr || d($item['created_at'] ?? '') === $dateStr;
    })),
  ];
}

$selectedDateSummary = $selectedSummary[$selectedDate] ?? [
  'meetings' => [],
  'orders' => [],
  'due_invoices' => [],
  'lead_updates' => [],
  'payments' => [],
  'expenses' => [],
  'quotations' => [],
  'tasks' => [],
];

$productCatalog = db_get_all('products');
$purchasePriceMap = [];
$productNamePurchaseMap = [];

foreach ($productCatalog as $product) {
  $productId = (int)($product['id'] ?? 0);
  $purchasePrice = (float)($product['purchase_price'] ?? 0);
  $productName = trim((string)($product['name'] ?? ''));

  if ($productId > 0) {
    $purchasePriceMap[$productId] = $purchasePrice;
  }

  if ($productName !== '') {
    $productNamePurchaseMap[strtolower($productName)] = $purchasePrice;
  }
}

/*
|--------------------------------------------------------------------------
| PROFIT CALCULATION - SINGLE SOURCE OF TRUTH
|--------------------------------------------------------------------------
| Invoice items already store the FINAL line total after discount in:
|     item['total']
|
| Therefore profit must NEVER calculate discount again when 'total' exists.
|
| Profit = Final Invoice Item Total - Purchase Cost
| Purchase Cost = Qty x Product Purchase Price
|
| Example:
| Selling Price = 1000
| Discount = 10%
| Final Item Total = 900
| Purchase Price = 500
| Profit = 900 - 500 = 400
|--------------------------------------------------------------------------
*/

$calendarProfit = 0.0;

foreach ($invoices as $invoice) {

  $invoiceDate = d($invoice['created_at'] ?? $invoice['date'] ?? '');

  if ($invoiceDate === '' || $invoiceDate < $rangeStart || $invoiceDate > $rangeEnd) {
    continue;
  }

  $invoiceProfit = 0.0;
  $hasUsableLineItems = false;

  $lineItems = normalize_line_items($invoice['items'] ?? []);

  foreach ($lineItems as $item) {

    $qty = (float)($item['qty'] ?? 0);
    if ($qty <= 0) {
      continue;
    }

    $hasUsableLineItems = true;

    /*
     * IMPORTANT:
     * Use the stored final line total first.
     * Your invoice data stores discount_rate, discount_amount and total.
     * Using item['total'] prevents the old discount-rate bug completely.
     */
    if (isset($item['total']) && $item['total'] !== '' && is_numeric($item['total'])) {
      $lineRevenue = (float)$item['total'];
    } else {
      // Backward-compatible fallback for old invoices without item total.
      $sellingPrice = (float)($item['price'] ?? $item['selling_price'] ?? 0);

      if (isset($item['discount_amount']) && $item['discount_amount'] !== '' && is_numeric($item['discount_amount'])) {
        $discountAmount = max(0, min($sellingPrice * $qty, (float)$item['discount_amount']));
        $lineRevenue = ($sellingPrice * $qty) - $discountAmount;
      } else {
        $discountPercent = 0.0;

        foreach (['discount_rate', 'discount_percent', 'discount'] as $discountKey) {
          if (isset($item[$discountKey]) && $item[$discountKey] !== '' && is_numeric($item[$discountKey])) {
            $discountPercent = (float)$item[$discountKey];
            break;
          }
        }

        $discountPercent = max(0, min(100, $discountPercent));
        $lineRevenue = ($sellingPrice * $qty) * (1 - ($discountPercent / 100));
      }
    }

    $lineRevenue = max(0, $lineRevenue);

    /*
     * Purchase price:
     * 1. Use purchase price saved directly in invoice item if available.
     * 2. Otherwise use product ID from product catalogue.
     * 3. Otherwise match product description/name.
     *
     * This prevents a missing product_id from silently turning purchase cost
     * into zero and inflating profit.
     */
    $itemPurchasePrice = null;

    foreach (['purchase_price', 'cost_price', 'purchasePrice', 'costPrice'] as $purchaseKey) {
      if (isset($item[$purchaseKey]) && $item[$purchaseKey] !== '' && is_numeric($item[$purchaseKey])) {
        $itemPurchasePrice = (float)$item[$purchaseKey];
        break;
      }
    }

    if ($itemPurchasePrice === null) {
      $productId = (int)($item['product_id'] ?? 0);
      if ($productId > 0 && array_key_exists($productId, $purchasePriceMap)) {
        $itemPurchasePrice = $purchasePriceMap[$productId];
      }
    }

    if ($itemPurchasePrice === null) {
      $description = trim((string)($item['description'] ?? $item['name'] ?? ''));
      $descriptionKey = strtolower($description);

      if ($descriptionKey !== '' && array_key_exists($descriptionKey, $productNamePurchaseMap)) {
        $itemPurchasePrice = $productNamePurchaseMap[$descriptionKey];
      }
    }

    // If no purchase price can be resolved, do not manufacture profit.
    // Skip that line instead of treating its cost as zero.
    if ($itemPurchasePrice === null) {
      continue;
    }

    $invoiceProfit += $lineRevenue - ($qty * $itemPurchasePrice);
  }

  /*
   * If an invoice has no usable line items, its purchase cost is unknown.
   * Do not use invoice amount as profit; that was the source of the old
   * inflated-profit behaviour.
   */
  if (!$hasUsableLineItems) {
    continue;
  }

  /*
   * Never use max(0, ...).
   * A genuine loss must remain a negative profit.
   */
  $calendarProfit += $invoiceProfit;
}

require __DIR__ . '/layout_top.php';
?>

<div class="panel calendar-page">
  <div class="panel-header calendar-header">
    <h2><?= icon('calendar') ?> Calendar</h2>
    <div class="toolbar calendar-toolbar">
      <div class="calendar-view-switch">
        <a class="btn btn-outline btn-sm <?= $view === 'month' ? 'active' : '' ?>" href="calendar.php?view=month&date=<?= $selectedDate ?>">Month</a>
        <a class="btn btn-outline btn-sm <?= $view === 'week' ? 'active' : '' ?>" href="calendar.php?view=week&date=<?= $selectedDate ?>">Week</a>
        <a class="btn btn-outline btn-sm <?= $view === 'day' ? 'active' : '' ?>" href="calendar.php?view=day&date=<?= $selectedDate ?>">Day</a>
      </div>

      <a class="btn btn-outline btn-sm" href="calendar.php?view=day&date=<?= date('Y-m-d') ?>">Today</a>

      <a class="btn btn-outline btn-sm" href="calendar.php?month=<?= $prevMonth->format('n') ?>&year=<?= $prevMonth->format('Y') ?>&view=month&date=<?= $prevMonth->format('Y-m-d') ?>">Previous</a>

      <strong class="calendar-period"><?= $selectedDateObj->format('F Y') ?></strong>

      <a class="btn btn-outline btn-sm" href="calendar.php?month=<?= $nextMonth->format('n') ?>&year=<?= $nextMonth->format('Y') ?>&view=month&date=<?= $nextMonth->format('Y-m-d') ?>">Next</a>

      <?php if (is_admin()): ?>
        <a class="btn btn-primary btn-sm open-form-popup" href="module.php?m=meetings&action=new"><?= icon('add', 14) ?> Add Meeting</a>
        <a class="btn btn-primary btn-sm open-form-popup" href="module.php?m=tasks&action=new"><?= icon('add', 14) ?> Add Task</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="panel-body">

    <div class="kpi-grid calendar-summary-grid">

      <div class="kpi-card calendar-summary-card">
        <div class="kpi-icon" style="background:#e0f2e1;color:#2e7d32;"><?= icon('leads', 20) ?></div>
        <div class="label">Leads</div>
        <div class="value"><?= count(array_filter($leads, fn($lead) => d($lead['created_at'] ?? '') === $selectedDate || d($lead['updated_at'] ?? '') === $selectedDate)) ?></div>
      </div>

      <div class="kpi-card calendar-summary-card">
        <div class="kpi-icon" style="background:#dcfce7;color:#166534;">₹</div>
        <div class="label">Orders</div>
        <div class="value"><?= count($selectedDateSummary['orders']) ?></div>
      </div>

      <div class="kpi-card calendar-summary-card">
        <div class="kpi-icon" style="background:#fef3c7;color:#b45309;"><?= icon('alert', 20) ?></div>
        <div class="label">Payment Due</div>
        <div class="value"><?= count($selectedDateSummary['due_invoices']) ?></div>
      </div>

      <?php if (is_admin()): ?>
      <div class="kpi-card calendar-summary-card">
        <div class="kpi-icon" style="background:#ede9fe;color:#6d28d9;"><?= icon('quotations', 20) ?></div>
        <div class="label">Finance Activity</div>
        <div class="value"><?= count($selectedDateSummary['payments']) + count($selectedDateSummary['expenses']) ?></div>
      </div>

      <div class="kpi-card calendar-summary-card">
        <div class="kpi-icon" style="background:#d1fae5;color:#047857;">₹</div>
        <div class="label">Profit</div>
        <div class="value"><?= number_format($calendarProfit, 2) ?></div>
      </div>

      <div class="kpi-card calendar-summary-card">
        <div class="kpi-icon" style="background:#fce7f3;color:#be123c;"><?= icon('expenses', 20) ?></div>
        <div class="label">Expenses</div>
        <div class="value">₹<?= number_format(array_sum(array_map(fn($row) => (float)($row['amount'] ?? 0), $selectedDateSummary['expenses'])), 2) ?></div>
      </div>
      <?php endif; ?>

      <div class="kpi-card calendar-summary-card">
        <div class="kpi-icon" style="background:#e0f2e1;color:#166534;"><?= icon('calendar', 20) ?></div>
        <div class="label">Meetings</div>
        <div class="value"><?= count($selectedDateSummary['meetings']) ?></div>
      </div>

      <div class="kpi-card calendar-summary-card">
        <div class="kpi-icon" style="background:#e0e7ff;color:#4338ca;">✓</div>
        <div class="label">Tasks</div>
        <div class="value"><?= count($selectedDateSummary['tasks']) ?></div>
      </div>

    </div>

    <div class="calendar-grid">

      <?php foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName): ?>
        <div class="calendar-weekday">
          <?= htmlspecialchars($dayName) ?>
        </div>
      <?php endforeach; ?>

      <?php foreach ($calendarDays as $date): ?>

        <?php
        $dateStr = $date->format('Y-m-d');
        $isCurrentMonth = $date->format('n') === $month;
        $dayMeetings = $selectedSummary[$dateStr]['meetings'] ?? [];
        $dayOrders = $selectedSummary[$dateStr]['orders'] ?? [];
        $dayDueInvoices = $selectedSummary[$dateStr]['due_invoices'] ?? [];
        $dayPayments = $selectedSummary[$dateStr]['payments'] ?? [];
        $dayExpenses = $selectedSummary[$dateStr]['expenses'] ?? [];
        $dayLeads = $selectedSummary[$dateStr]['lead_updates'] ?? [];
        $dayQuotations = $selectedSummary[$dateStr]['quotations'] ?? [];
        $dayLeadNames = array_map(fn($lead) => $lead['name'] ?? 'Lead', $dayLeads);
        $dayQuotationNames = array_map(fn($quote) => $quote['quotation_no'] ?? 'Quotation', $dayQuotations);
        $isSelected = $dateStr === $selectedDate;
        ?>

        <div class="calendar-day <?= $isSelected ? 'is-selected' : '' ?> <?= $isCurrentMonth ? '' : 'is-outside' ?>" onclick="location.href='calendar.php?view=day&date=<?= $dateStr ?>'">

          <div class="calendar-day-number">
            <?= $date->format('d') ?>
          </div>

          <?php if (!empty($dayMeetings)): ?>
            <div style="font-size:11px;color:#2e7d32;background:#e0f2e1;padding:4px 6px;border-radius:6px;margin-bottom:4px;">
              <?= count($dayMeetings) ?> meeting<?= count($dayMeetings) > 1 ? 's' : '' ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($dayOrders)): ?>
            <div style="font-size:11px;color:#166534;background:#dcfce7;padding:4px 6px;border-radius:6px;margin-bottom:4px;">
              <?= count($dayOrders) ?> order<?= count($dayOrders) > 1 ? 's' : '' ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($dayDueInvoices)): ?>
            <div style="font-size:11px;color:#b45309;background:#fef3c7;padding:4px 6px;border-radius:6px;margin-bottom:4px;">
              <?= count($dayDueInvoices) ?> payment due
            </div>
          <?php endif; ?>

          <?php if (!empty($dayPayments)): ?>
            <div style="font-size:11px;color:#0f766e;background:#ccfbf1;padding:4px 6px;border-radius:6px;margin-bottom:4px;">
              <?= count($dayPayments) ?> finance payment<?= count($dayPayments) > 1 ? 's' : '' ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($dayExpenses)): ?>
            <div style="font-size:11px;color:#be123c;background:#fce7f3;padding:4px 6px;border-radius:6px;margin-bottom:4px;">
              <?= count($dayExpenses) ?> expense<?= count($dayExpenses) > 1 ? 's' : '' ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($selectedSummary[$dateStr]['tasks'] ?? [])): ?>
            <div style="font-size:11px;color:#4338ca;background:#e0e7ff;padding:4px 6px;border-radius:6px;margin-bottom:4px;">
              <?= count($selectedSummary[$dateStr]['tasks']) ?> task<?= count($selectedSummary[$dateStr]['tasks']) > 1 ? 's' : '' ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($dayLeads)): ?>
            <div style="font-size:11px;color:#166534;background:#dcfce7;padding:4px 6px;border-radius:6px;margin-bottom:4px;">
              <?= count($dayLeads) ?> lead update
            </div>

            <?php foreach (array_slice($dayLeadNames, 0, 3) as $leadName): ?>
              <div style="font-size:10px;color:#166534; margin:2px 0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                • <?= htmlspecialchars($leadName) ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php if (!empty($dayQuotations)): ?>
            <div style="font-size:11px;color:#6d28d9;background:#ede9fe;padding:4px 6px;border-radius:6px;margin-top:4px;">
              <?= count($dayQuotations) ?> quotation
            </div>

            <?php foreach (array_slice($dayQuotationNames, 0, 3) as $qName): ?>
              <div style="font-size:10px;color:#6d28d9; margin:2px 0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                • <?= htmlspecialchars($qName) ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div>

      <?php endforeach; ?>

    </div>
  </div>
</div>

<div class="panel" style="margin-top:20px;">

  <div class="panel-header calendar-activity-header">
    <h2><?= icon('calendar') ?> <?= htmlspecialchars(format_display_date($selectedDate)) ?> Activity</h2>
  </div>

  <div class="panel-body">

    <?php

    $activityRows = [];
    $paidByInvoice = [];

    foreach ($payments as $payment) {
      $invoiceNo = (string)($payment['invoice_no'] ?? '');
      $paidByInvoice[$invoiceNo] = ($paidByInvoice[$invoiceNo] ?? 0) + (float)($payment['amount'] ?? 0);
    }

    foreach ($selectedDateSummary['lead_updates'] ?? [] as $lead) {
      $activityRows[] = [
        'type' => 'Lead',
        'name' => $lead['name'] ?? 'Lead',
        'detail' => ($lead['stage'] ?? 'New') . ' / ' . ($lead['owner'] ?? 'Unassigned')
      ];
    }

    foreach ($selectedDateSummary['quotations'] ?? [] as $quote) {
      $activityRows[] = [
        'type' => 'Quotation',
        'name' => $quote['quotation_no'] ?? 'Quotation',
        'detail' => ($quote['customer'] ?? '-') . ' — ₹' . number_format((float)($quote['amount'] ?? 0), 2) . ' — ' . ($quote['status'] ?? '-')
      ];
    }

    foreach ($selectedDateSummary['orders'] ?? [] as $order) {

      $orderAmount = (float)($order['amount'] ?? 0);

      $orderPaid = min(
        $orderAmount,
        (float)($paidByInvoice[(string)($order['invoice_no'] ?? '')] ?? 0)
      );

      $orderPending = max(0, $orderAmount - $orderPaid);

      $activityRows[] = [
        'type' => 'Order',
        'name' => $order['invoice_no'] ?? 'Invoice',
        'detail' => ($order['customer'] ?? '-') . ' — Pending ₹' . number_format($orderPending, 2)
      ];
    }

    foreach ($selectedDateSummary['due_invoices'] ?? [] as $invoice) {
      $activityRows[] = [
        'type' => 'Payment Due',
        'name' => $invoice['invoice_no'] ?? 'Invoice',
        'detail' => ($invoice['customer'] ?? '-') . ' — ₹' . number_format((float)($invoice['amount'] ?? 0), 2)
      ];
    }

    foreach ($selectedDateSummary['payments'] ?? [] as $payment) {
      $activityRows[] = [
        'type' => 'Payment',
        'name' => $payment['customer'] ?? 'Customer',
        'detail' => '₹' . number_format((float)($payment['amount'] ?? 0), 2) . ' / ' . ($payment['mode'] ?? '-')
      ];
    }

    foreach ($selectedDateSummary['expenses'] ?? [] as $expense) {
      $activityRows[] = [
        'type' => 'Expense',
        'name' => $expense['title'] ?? 'Expense',
        'detail' => '₹' . number_format((float)($expense['amount'] ?? 0), 2) . ' / ' . ($expense['category'] ?? '-') . ' / ' . ($expense['mode'] ?? '-')
      ];
    }

    foreach ($selectedDateSummary['meetings'] ?? [] as $meeting) {
      $activityRows[] = [
        'type' => 'Meeting',
        'name' => $meeting['title'] ?? 'Meeting',
        'detail' => ($meeting['time'] ?? '-') . ' — ' . ($meeting['with_person'] ?? '-')
      ];
    }

    foreach ($selectedDateSummary['tasks'] ?? [] as $task) {
      $activityRows[] = [
        'type' => 'Task',
        'name' => $task['title'] ?? 'Task',
        'detail' => ($task['assigned_to'] ?? 'Unassigned') . ' — ' . ($task['status'] ?? 'To Do') . ' — ' . ($task['due_date'] ? format_display_date($task['due_date']) : '-')
      ];
    }

    ?>

    <?php if (empty($activityRows)): ?>

      <div class="empty-state">
        <div class="empty-icon"><?= icon('calendar', 30) ?></div>
        No activity on this date.
      </div>

    <?php else: ?>

      <?php $activityTypes = array_values(array_unique(array_column($activityRows, 'type'))); ?>

      <div class="calendar-activity-toolbar">

        <div class="calendar-activity-tabs" role="tablist" aria-label="Activity type filters">

          <button type="button" class="calendar-activity-tab is-active" data-activity-type="All">
            All (<?= count($activityRows) ?>)
          </button>

          <?php foreach ($activityTypes as $activityType): ?>

            <button type="button" class="calendar-activity-tab" data-activity-type="<?= htmlspecialchars($activityType) ?>">
              <?= htmlspecialchars($activityType) ?>
              (<?= count(array_filter($activityRows, fn($row) => $row['type'] === $activityType)) ?>)
            </button>

          <?php endforeach; ?>

        </div>

        <label class="search-box calendar-activity-search" aria-label="Search activity">
          <span class="search-icon"><?= icon('search', 15) ?></span>
          <input type="search" id="calendar-activity-search" placeholder="Search activity" autocomplete="off">
        </label>

      </div>

      <table class="data-table">

        <thead>
          <tr>
            <th>Type</th>
            <th>Name</th>
            <th>Details</th>
          </tr>
        </thead>

        <tbody>

          <?php foreach ($activityRows as $row): ?>

            <tr data-activity-row-type="<?= htmlspecialchars($row['type']) ?>">

              <td>
                <span class="badge <?= badge_class($row['type']) ?>">
                  <?= htmlspecialchars($row['type']) ?>
                </span>
              </td>

              <td><?= htmlspecialchars($row['name']) ?></td>

              <td><?= htmlspecialchars($row['detail']) ?></td>

            </tr>

          <?php endforeach; ?>

        </tbody>

      </table>

    <?php endif; ?>

  </div>
</div>

<div class="form-modal" id="calendar-form-modal" aria-hidden="true">

  <div class="form-modal-dialog meeting-modal-dialog" role="dialog" aria-modal="true" aria-label="Meeting form">

    <button type="button" class="form-modal-close" aria-label="Close form">&times;</button>

    <iframe title="Add or edit meeting" id="calendar-form-modal-frame"></iframe>

  </div>

</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.calendar-activity-tab').forEach(function(tab) {

      tab.addEventListener('click', function() {

        const type = tab.dataset.activityType;

        document.querySelectorAll('.calendar-activity-tab').forEach((item) =>
          item.classList.toggle('is-active', item === tab)
        );

        document.querySelectorAll('[data-activity-row-type]').forEach((row) => {
          row.hidden = type !== 'All' && row.dataset.activityRowType !== type;
        });

      });

    });

    const activitySearch = document.getElementById('calendar-activity-search');

    let selectedActivityType = 'All';

    document.querySelectorAll('.calendar-activity-tab').forEach((tab) => {

      tab.addEventListener('click', function() {
        selectedActivityType = tab.dataset.activityType;
        filterActivityRows();
      });

    });

    const filterActivityRows = function() {

      const query = (activitySearch?.value || '').trim().toLowerCase();

      document.querySelectorAll('[data-activity-row-type]').forEach((row) => {

        const typeMatches =
          selectedActivityType === 'All' ||
          row.dataset.activityRowType === selectedActivityType;

        row.hidden = !typeMatches ||
          (query !== '' && !row.textContent.toLowerCase().includes(query));

      });

    };

    if (activitySearch) {
      activitySearch.addEventListener('input', filterActivityRows);
    }

    const modal = document.getElementById('calendar-form-modal');
    const frame = document.getElementById('calendar-form-modal-frame');

    if (!modal || !frame) return;

    document.querySelectorAll('a.open-form-popup').forEach(function(link) {

      link.addEventListener('click', function(event) {

        event.preventDefault();

        const popupUrl = new URL(link.href, window.location.href);

        popupUrl.searchParams.set('popup', '1');

        frame.src = popupUrl.toString();

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');

      });

    });

    const close = function() {

      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      frame.src = 'about:blank';

    };

    modal.querySelector('.form-modal-close').addEventListener('click', close);

    modal.addEventListener('click', function(event) {

      if (event.target === modal) close();

    });

  });
</script>

<?php require __DIR__ . '/layout_bottom.php'; ?>