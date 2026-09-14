<?php
require_once __DIR__ . '/auth.php';
require_admin();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';

$data = db_load();
$data['invoices'] = member_visible_rows('invoices', $data['invoices']);
$data['payments'] = member_visible_rows('payments', $data['payments']);
if (!is_admin()) {
  $data['expenses'] = [];
}

/*
 * STATIC OPENING BALANCE
 * This amount is NOT stored as a transaction.
 * It is only used as the starting balance for Finance calculations.
 */
$openingBalance = 10000.00;

$invoiceAmounts = [];
foreach ($data['invoices'] as $invoice) {
    $invoiceAmounts[$invoice['invoice_no'] ?? ''] = (float)($invoice['amount'] ?? 0);
}

$transactions = [];

function finance_transaction_time($row) {
  $timestamp = strtotime((string)($row['created_at'] ?? ''));
  if ($timestamp === false) $timestamp = strtotime((string)($row['date'] ?? '')) ?: 0;
  return $timestamp;
}

function finance_transaction_label($value) {
  $timestamp = strtotime((string)$value);
  return $timestamp === false ? (string)$value : date('d-m-Y h:i A', $timestamp);
}

$cashCollected = 0.0;
$onlineCollected = 0.0;
$remainingByInvoice = $invoiceAmounts;

foreach ($data['payments'] as $payment) {
    $invoiceNo = (string)($payment['invoice_no'] ?? '');

    if (!array_key_exists($invoiceNo, $remainingByInvoice) || $remainingByInvoice[$invoiceNo] <= 0) {
        continue;
    }

    $amount = min(
        $remainingByInvoice[$invoiceNo],
        (float)($payment['amount'] ?? 0)
    );

    if ($amount <= 0) continue;

    $remainingByInvoice[$invoiceNo] -= $amount;

    $mode = strcasecmp((string)($payment['mode'] ?? ''), 'cash') === 0
        ? 'Cash'
        : 'Online';

    if ($mode === 'Cash') {
        $cashCollected += $amount;
    } else {
        $onlineCollected += $amount;
    }

    $transactions[] = [
        'date' => $payment['date'] ?? ($payment['created_at'] ?? ''),
        'created_at' => $payment['created_at'] ?? ($payment['date'] ?? ''),
        'type' => 'Income',
        'mode' => $mode,
        'description' => 'Payment - ' . ($invoiceNo ?: 'Invoice'),
        'reference' => $payment['reference'] ?? '-',
        'amount' => $amount,
    ];
}

$cashSpent = 0.0;
$onlineSpent = 0.0;

foreach ($data['expenses'] as $expense) {
    $amount = (float)($expense['amount'] ?? 0);

    $mode = strcasecmp((string)($expense['mode'] ?? ''), 'online') === 0
        ? 'Online'
        : 'Cash';

    if ($mode === 'Cash') {
        $cashSpent += $amount;
    } else {
        $onlineSpent += $amount;
    }

    $transactions[] = [
        'date' => $expense['date'] ?? ($expense['created_at'] ?? ''),
        'created_at' => $expense['created_at'] ?? ($expense['date'] ?? ''),
        'type' => 'Expense',
        'mode' => $mode,
        'description' => $expense['title'] ?? 'Expense',
        'reference' => $expense['category'] ?? '-',
        'amount' => $amount,
    ];
}

usort($transactions, function ($a, $b) {
    $timeCompare = finance_transaction_time($b) <=> finance_transaction_time($a);

    if ($timeCompare !== 0) {
        return $timeCompare;
    }

    return strcmp(
        (string)($b['description'] ?? ''),
        (string)($a['description'] ?? '')
    );
});

$totalCollected = $cashCollected + $onlineCollected;
$totalSpent = $cashSpent + $onlineSpent;
$totalInvoiced = array_sum($invoiceAmounts);
$totalPending = max(0, $totalInvoiced - $totalCollected);

/*
 * Opening balance is added ONLY to the available balance.
 * It is not included in Total Collected or any transaction.
 *
 * To keep the original Cash/Online separation intact, the static
 * opening balance is assigned to Cash Balance.
 */
$cashBalance = $cashCollected - $cashSpent;
$onlineBalance = $openingBalance + $onlineCollected - $onlineSpent;

$totalAvailableBalance = $openingBalance + $totalCollected - $totalSpent;

$activeModule = 'finance';
$pageTitle = 'Finance';

require __DIR__ . '/layout_top.php';
?>

<div class="finance-page">
  <div class="finance-heading">
    <div>
      <h2><?= icon('chart') ?> Finance Overview</h2>
      <p>Track collections, expenses and available balances in one place.</p>
    </div>

    <div class="finance-actions">
      <a href="module.php?m=payments&action=new&return_to=finance.php"
         class="btn btn-outline btn-sm finance-form-trigger">
        <?= icon('add', 14) ?> Add Payment
      </a>

      <a href="module.php?m=expenses&action=new&return_to=finance.php"
         class="btn btn-primary btn-sm finance-form-trigger">
        <?= icon('add', 14) ?> Add Expense
      </a>
    </div>
  </div>

  <div class="kpi-grid finance-kpis">

    <!-- Static Opening Balance -->
    <div class="kpi-card finance-card">
      <div class="label">Opening Balance</div>
      <div class="value">₹<?= number_format($openingBalance, 2) ?></div>
      <small>Static opening balance</small>
    </div>

    <div class="kpi-card finance-card finance-cash">
      <div class="label">Cash Balance</div>
      <div class="value">₹<?= number_format($cashBalance, 2) ?></div>
      <small>
        Opening Balance ₹<?= number_format($openingBalance, 2) ?>
        · In ₹<?= number_format($cashCollected, 2) ?>
        · Out ₹<?= number_format($cashSpent, 2) ?>
      </small>
    </div>

    <div class="kpi-card finance-card finance-online">
      <div class="label">Online Balance</div>
      <div class="value">₹<?= number_format($onlineBalance, 2) ?></div>
      <small>
        In ₹<?= number_format($onlineCollected, 2) ?>
        · Out ₹<?= number_format($onlineSpent, 2) ?>
      </small>
    </div>

    <div class="kpi-card finance-card">
      <div class="label">Total Available Balance</div>
      <div class="value green">₹<?= number_format($totalAvailableBalance, 2) ?></div>
      <small>
        Opening + Collected − Spent
      </small>
    </div>

    <div class="kpi-card finance-card">
      <div class="label">Total Collected</div>
      <div class="value green">₹<?= number_format($totalCollected, 2) ?></div>
      <small>All valid invoice payments</small>
    </div>

    <div class="kpi-card finance-card">
      <div class="label">Total Spent</div>
      <div class="value orange">₹<?= number_format($totalSpent, 2) ?></div>
      <small>Cash + Online expenses</small>
    </div>

    <div class="kpi-card finance-card">
      <div class="label">Pending Receivable</div>
      <div class="value orange">₹<?= number_format($totalPending, 2) ?></div>
      <small>Total invoices minus collected</small>
    </div>

  </div>

  <div class="panel finance-transactions">
    <div class="panel-header">
      <h2>
        <?= icon('payments') ?>
        All Transactions
        <span class="count-pill">(<?= count($transactions) ?>)</span>
      </h2>
    </div>

    <div class="panel-body">
      <?php if (empty($transactions)): ?>

        <div class="empty-state">
          No financial transactions yet.
        </div>

      <?php else: ?>

        <table class="data-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Type</th>
              <th>Mode</th>
              <th>Description</th>
              <th>Reference</th>
              <th>Amount</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($transactions as $transaction): ?>

              <tr>
                <td>
                  <?= htmlspecialchars(
                    finance_transaction_label(
                      $transaction['created_at'] ?? $transaction['date'] ?? ''
                    )
                  ) ?>
                </td>

                <td>
                  <span class="badge <?= $transaction['type'] === 'Income' ? 'green' : 'orange' ?>">
                    <?= htmlspecialchars($transaction['type']) ?>
                  </span>
                </td>

                <td>
                  <?= htmlspecialchars($transaction['mode']) ?>
                </td>

                <td>
                  <?= htmlspecialchars($transaction['description']) ?>
                </td>

                <td>
                  <?= htmlspecialchars($transaction['reference'] ?: '-') ?>
                </td>

                <td class="finance-amount <?= $transaction['type'] === 'Income' ? 'finance-income' : 'finance-expense' ?>">
                  <?= $transaction['type'] === 'Income' ? '+' : '-' ?>
                  ₹<?= number_format($transaction['amount'], 2) ?>
                </td>
              </tr>

            <?php endforeach; ?>
          </tbody>
        </table>

      <?php endif; ?>
    </div>
  </div>
</div>

<div class="form-modal" id="finance-form-modal" aria-hidden="true">
  <div class="form-modal-dialog"
       role="dialog"
       aria-modal="true"
       aria-label="Finance form">

    <button type="button"
            class="form-modal-close"
            aria-label="Close form">
      &times;
    </button>

    <iframe title="Finance form" id="finance-form-frame"></iframe>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('finance-form-modal');
  const frame = document.getElementById('finance-form-frame');

  if (!modal || !frame) return;

  document.querySelectorAll('.finance-form-trigger').forEach(function (link) {
    link.addEventListener('click', function (event) {
      event.preventDefault();

      const popupUrl = new URL(link.href, window.location.href);
      popupUrl.searchParams.set('popup', '1');

      frame.src = popupUrl.toString();

      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
    });
  });

  const close = function () {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    frame.src = 'about:blank';
  };

  const closeButton = modal.querySelector('.form-modal-close');

  if (closeButton) {
    closeButton.addEventListener('click', close);
  }

  modal.addEventListener('click', function (event) {
    if (event.target === modal) {
      close();
    }
  });
});
</script>

<?php require __DIR__ . '/layout_bottom.php'; ?>
