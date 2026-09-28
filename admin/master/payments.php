<?php
/**
 * MASTER PANEL - Payments.
 *
 * The payment ledger: every plan purchase that came through the website
 * checkout, in whatever state it reached, plus anything recorded by hand
 * (cash, UPI, bank transfer) for sales that never touched the site.
 *
 * A row is only promoted to a subscription once money is actually in - see
 * includes/payment.php. That is why "Awaiting" and "Failed" rows sit in this
 * table without making any customer look paid.
 */
require_once __DIR__ . '/auth.php';
require_master_login();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/../../includes/plans.php';
require_once __DIR__ . '/../../includes/payment.php';

$errors = [];
$notice = '';

/* ------------------------------------------------- money actions need a token */
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$action = (string)($_POST['form_action'] ?? '');
if ($isPost && !master_csrf_check()) {
    $errors[] = $masterCsrfError;
    $isPost = false;
    $action = '';
}

/* ------------------------------------------- record a manual (offline) payment */
if ($isPost && $action === 'manual') {
    $customerId = (int)($_POST['customer_id'] ?? 0);
    $planName   = trim((string)($_POST['plan'] ?? ''));
    $cycle      = plan_normalise_cycle($_POST['cycle'] ?? 'monthly');
    $method     = trim((string)($_POST['payment_method'] ?? 'Cash'));
    $paidOn     = trim((string)($_POST['paid_on'] ?? date('Y-m-d')));
    $amount     = trim((string)($_POST['amount'] ?? ''));
    $reference  = trim((string)($_POST['reference'] ?? ''));

    $customer = $customerId ? mdb_get('customers', $customerId) : null;
    if (!$customer) $errors[] = 'Choose the customer this payment belongs to.';
    $plan = plan_find($planName);
    if (!$plan) $errors[] = 'Choose a plan.';
    if ($method === '') $errors[] = 'Enter how the payment was received.';
    if ($paidOn === '' || !strtotime($paidOn)) $errors[] = 'Enter a valid payment date.';
    if ($amount === '' || !is_numeric($amount) || (float)$amount <= 0) $errors[] = 'Enter the amount received.';

    if (!$errors) {
        $latest = customer_latest_subscription($customerId);
        $subscription = [
            'amount'        => (float)$amount,
            'payment_type'  => $method === 'Razorpay' ? 'Online' : $method,
            'purchase_date' => substr($paidOn, 0, 10),
            'expiry_date'   => calc_renewal_expiry_date(
                $latest['expiry_date'] ?? '',
                $paidOn,
                plan_cycle_months($cycle)
            ),
            'plan'          => $plan['name'],
            'cycle'         => $cycle,
            'transaction_id'=> $reference,
        ];
        $subscriptionId = mdb_insert('subscriptions', array_merge(['customer_id' => $customerId], $subscription));
        if (!empty($customer['slug'])) {
            write_shop_license($customer['slug'], $customer, $subscription);
        }
        payment_insert([
            'status'          => 'captured',
            'plan'            => $plan['name'],
            'cycle'           => $cycle,
            'amount'          => (float)$amount,
            'currency'        => RAZORPAY_CURRENCY,
            'payment_method'  => strtolower($method),
            'payment_type'    => $subscription['payment_type'],
            'source'          => 'manual',
            'customer_id'     => $customerId,
            'customer_name'   => $customer['name'] ?? '',
            'customer_email'  => $customer['email'] ?? '',
            'customer_phone'  => $customer['phone'] ?? '',
            'subscription_id' => (int)$subscriptionId,
            'expiry_date'     => $subscription['expiry_date'],
            'transaction_id'  => $reference,
            'paid_at'         => date('Y-m-d H:i:s', (int)strtotime($paidOn)),
        ]);
        $notice = 'Payment of ₹' . number_format((float)$amount, 2) . ' recorded for ' . ($customer['name'] ?? '') . '.';
    }
}

/* -------------------------------------------------------------- mark refunded */
if ($isPost && $action === 'refund') {
    $id = (int)($_POST['id'] ?? 0);
    $row = payment_get($id);
    if ($row && (string)($row['status'] ?? '') === 'captured') {
        payment_update($id, ['status' => 'refunded', 'failure_reason' => trim((string)($_POST['note'] ?? 'Refunded by master admin'))]);
        $notice = 'Payment #' . $id . ' marked as refunded. The customer subscription was left untouched - shorten it by hand if access must end.';
    } else {
        $errors[] = 'Only a paid payment can be marked refunded.';
    }
}

/* -------------------------------------------------------------------- listing */
$status = (string)($_GET['status'] ?? 'all');
$search = trim((string)($_GET['q'] ?? ''));

// Read the ledger once. payment_all() re-reads and re-decodes payments.json on
// every call, and the status tabs below used to call it inside their own loop -
// so one page load parsed the whole file six or seven times over. It only gets
// slower as payments pile up.
$allPayments = payment_all();

$rows = array_values(array_filter($allPayments, function ($row) use ($status, $search) {
    if ($status !== 'all' && (string)($row['status'] ?? '') !== $status) return false;
    if ($search === '') return true;
    $haystack = strtolower(implode(' ', [
        (string)($row['customer_name'] ?? ''),
        (string)($row['customer_email'] ?? ''),
        (string)($row['customer_phone'] ?? ''),
        (string)($row['plan'] ?? ''),
        (string)($row['razorpay_payment_id'] ?? ''),
        (string)($row['transaction_id'] ?? ''),
    ]));
    return strpos($haystack, strtolower($search)) !== false;
}));

$statusCounts = ['all' => count($allPayments)];
foreach (payment_statuses() as $statusKey) {
    $statusCounts[$statusKey] = 0;
}
foreach ($allPayments as $paymentRow) {
    $rowStatus = (string)($paymentRow['status'] ?? '');
    if (isset($statusCounts[$rowStatus])) $statusCounts[$rowStatus]++;
}

$totals = payment_totals();
$customers = mdb_all('customers');
usort($customers, fn($a, $b) => strcasecmp((string)($a['name'] ?? ''), (string)($b['name'] ?? '')));

// Current expiry per customer, so recording a payment can show the date the
// customer will actually end up with instead of leaving the master to work it
// out from the subscriptions screen.
$customerExpiry = [];
foreach ($customers as $customerRow) {
    $latestSub = customer_latest_subscription((int)$customerRow['id']);
    $customerExpiry[(int)$customerRow['id']] = (string)($latestSub['expiry_date'] ?? '');
}

$pageTitle = 'Payments';
$activePage = 'payments';
require __DIR__ . '/layout_top.php';
?>

<?php if ($notice): ?><div class="alert"><?= htmlspecialchars($notice) ?></div><?php endif; ?>
<?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>

<div class="kpi-grid master-kpis pg-kpis">
    <div class="kpi-card">
        <div class="label"><?= icon('rupee', 14) ?> Collected This Month</div>
        <div class="value green">₹<?= number_format($totals['revenue_month'], 2) ?></div>
    </div>
    <div class="kpi-card">
        <div class="label">Total Collected</div>
        <div class="value">₹<?= number_format($totals['revenue_all'], 2) ?></div>
    </div>
    <div class="kpi-card">
        <div class="label">Paid</div>
        <div class="value"><?= $totals['captured'] ?></div>
    </div>
    <div class="kpi-card">
        <div class="label">Awaiting</div>
        <div class="value orange"><?= $totals['pending'] ?></div>
    </div>
    <div class="kpi-card">
        <div class="label">Failed</div>
        <div class="value red"><?= $totals['failed'] ?></div>
    </div>
</div>

<div class="panel">
    <div class="panel-header pg-ledger-head">
        <h2><?= icon('payments') ?> Payment Ledger</h2>
        <form method="get" class="pg-search" action="payments.php">
            <?php if ($status !== 'all'): ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($status, ENT_QUOTES) ?>">
            <?php endif; ?>
            <div class="pg-search-field">
                <?= icon('search', 16) ?>
                <input type="search" name="q" value="<?= htmlspecialchars($search, ENT_QUOTES) ?>"
                       placeholder="Name, phone, plan or reference">
            </div>
            <button class="btn btn-primary btn-sm" type="submit">Search</button>
            <?php if ($search !== ''): ?>
                <a class="btn btn-outline btn-sm" href="payments.php<?= $status !== 'all' ? '?status=' . urlencode($status) : '' ?>">Clear</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="panel-body">
        <div class="status-tabs pg-tabs">
            <?php
            $tabs = ['all' => 'All'];
            foreach (payment_statuses() as $statusKey) {
                $meta = payment_status_meta($statusKey);
                $tabs[$statusKey] = $meta['label'];
            }
            foreach ($tabs as $key => $label):
            ?>
                <a class="status-tab<?= $status === $key ? ' active' : '' ?>"
                   href="payments.php?status=<?= urlencode($key) ?><?= $search !== '' ? '&amp;q=' . urlencode($search) : '' ?>">
                    <?= htmlspecialchars($label) ?>
                    <span class="pg-tab-count"><?= (int)($statusCounts[$key] ?? 0) ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($rows)): ?>
            <div class="empty-state">
                <div class="empty-icon"><?= icon('payments', 40) ?></div>
                <p>No payments<?= $status !== 'all' ? ' with that status' : '' ?><?= $search !== '' ? ' matching "' . htmlspecialchars($search) . '"' : '' ?> yet.</p>
                <?php if ($status !== 'all' || $search !== ''): ?>
                    <a class="btn btn-outline btn-sm" href="payments.php">Show all payments</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
        <div class="pg-table-wrap">
            <table class="data-table pg-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Plan</th>
                        <th class="pg-amount">Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Reference</th>
                        <th class="pg-actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row):
                    $meta = payment_status_meta((string)($row['status'] ?? ''));
                    $plan = plan_find($row['plan'] ?? '');
                    $customer = !empty($row['customer_id']) ? mdb_get('customers', $row['customer_id']) : null;
                    $reference = trim((string)($row['razorpay_payment_id'] ?? $row['transaction_id'] ?? ''));
                    $rowStatus = (string)($row['status'] ?? '');
                ?>
                    <tr>
                        <td class="pg-date"><?= format_display_date_m($row['paid_at'] ?? $row['created_at'] ?? '') ?></td>
                        <td>
                            <?php if ($customer): ?>
                                <a class="cell-link" href="customer_detail.php?id=<?= (int)$customer['id'] ?>"><?= htmlspecialchars($customer['name']) ?></a>
                            <?php else: ?>
                                <span class="cell-link"><?= htmlspecialchars($row['customer_name'] ?: '—') ?></span>
                            <?php endif; ?>
                            <div class="pg-sub">
                                <?= htmlspecialchars($row['customer_phone'] ?: ($customer['phone'] ?? '')) ?>
                                <?php if (empty($row['customer_phone']) && !empty($row['customer_email'])): ?>
                                    <?= htmlspecialchars($row['customer_email']) ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?= $plan ? htmlspecialchars(plan_label($plan)) : htmlspecialchars((string)($row['plan'] ?? '—')) ?>
                            <div class="pg-sub"><?= htmlspecialchars(plan_cycle_label($row['cycle'] ?? 'monthly')) ?></div>
                        </td>
                        <td class="pg-amount"><b>₹<?= number_format((float)($row['amount'] ?? 0), 2) ?></b></td>
                        <td>
                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', (string)($row['payment_method'] ?? 'razorpay')))) ?>
                            <?php if (($row['source'] ?? '') === 'manual'): ?><div class="pg-sub">by master</div><?php endif; ?>
                        </td>
                        <td>
                            <span class="status-pill <?= $meta['class'] ?>"><?= htmlspecialchars($meta['label']) ?></span>
                            <?php if (!empty($row['failure_reason'])): ?>
                                <div class="pg-reason"><?= htmlspecialchars($row['failure_reason']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="pg-ref">
                            <?php if ($reference !== ''): ?><code><?= htmlspecialchars($reference) ?></code><?php else: ?><span class="pg-sub">—</span><?php endif; ?>
                        </td>
                        <td class="row-actions pg-actions">
                            <?php if ($customer): ?>
                                <a class="btn btn-outline btn-sm" href="customer_detail.php?id=<?= (int)$customer['id'] ?>"><?= icon('view', 14) ?> View</a>
                            <?php endif; ?>
                            <?php if (!empty($customer['slug'])): ?>
                                <a class="btn btn-outline btn-sm" href="<?= htmlspecialchars(customer_panel_path($customer['slug'])) ?>" target="_blank" rel="noopener"><?= icon('building', 14) ?> Panel</a>
                            <?php endif; ?>
                            <?php if ($rowStatus === 'captured'): ?>
                                <form method="post" class="pg-inline-form" onsubmit="return confirm('Mark this payment as refunded?')">
                                    <?= master_csrf_field() ?>
                                    <input type="hidden" name="form_action" value="refund">
                                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                    <button class="btn btn-outline btn-sm pg-refund" type="submit">Refund</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="pg-footnote">
            Showing <?= count($rows) ?> of <?= count($allPayments) ?> payment<?= count($allPayments) === 1 ? '' : 's' ?>.
            Click any column heading to sort.
        </p>
        <?php endif; ?>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h2><?= icon('add') ?> Record an Offline Payment</h2></div>
    <div class="panel-body">
        <p class="pg-intro">
            For money received by cash, UPI or bank transfer outside the website. This grants the customer the same
            access a paid checkout would, and shows up in the ledger above.
        </p>

        <div class="pg-record">
            <form method="post" class="pg-record-form">
                <?= master_csrf_field() ?>
                <input type="hidden" name="form_action" value="manual">

                <div class="form-group">
                    <label for="manual-customer">Customer *</label>
                    <select name="customer_id" id="manual-customer" required>
                        <option value="">Choose a customer…</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= (int)$c['id'] ?>"
                                data-name="<?= htmlspecialchars((string)($c['name'] ?? ''), ENT_QUOTES) ?>"
                                data-expiry="<?= htmlspecialchars($customerExpiry[(int)$c['id']] ?? '', ENT_QUOTES) ?>">
                                <?= htmlspecialchars(($c['name'] ?? '') . ' — ' . ($c['phone'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="pg-field-pair">
                    <div class="form-group">
                        <label for="manual-plan">Plan *</label>
                        <select name="plan" id="manual-plan" required>
                            <?php foreach (plans_all() as $p): ?>
                                <option value="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                                    data-monthly="<?= plan_amount_rupees($p, 'monthly') ?>"
                                    data-yearly="<?= plan_amount_rupees($p, 'yearly') ?>"><?= htmlspecialchars(plan_label($p), ENT_QUOTES) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="manual-cycle">Billing</label>
                        <select name="cycle" id="manual-cycle">
                            <option value="monthly">Monthly (1 month)</option>
                            <option value="yearly" selected>Yearly (12 months)</option>
                        </select>
                    </div>
                </div>

                <div class="pg-field-pair">
                    <div class="form-group">
                        <label for="manual-amount">Amount Received (₹) *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" id="manual-amount" required>
                        <small class="pg-hint" id="manual-price-hint"></small>
                    </div>
                    <div class="form-group">
                        <label for="manual-method">Payment Method *</label>
                        <select name="payment_method" id="manual-method" required>
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                </div>

                <div class="pg-field-pair">
                    <div class="form-group">
                        <label for="manual-date">Payment Date *</label>
                        <input type="date" name="paid_on" id="manual-date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="manual-ref">Reference</label>
                        <input type="text" name="reference" id="manual-ref" placeholder="UPI ref or cheque no.">
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary" type="submit"><?= icon('save', 14) ?> Record Payment</button>
                </div>
            </form>

            <aside class="pg-preview" id="pg-preview">
                <h3>What this grants</h3>
                <dl class="pg-preview-list">
                    <div>
                        <dt>Customer</dt>
                        <dd id="pg-prev-customer">—</dd>
                    </div>
                    <div>
                        <dt>Plan</dt>
                        <dd id="pg-prev-plan">—</dd>
                    </div>
                    <div>
                        <dt>List price</dt>
                        <dd id="pg-prev-list">—</dd>
                    </div>
                    <div>
                        <dt>Amount recorded</dt>
                        <dd id="pg-prev-amount">—</dd>
                    </div>
                    <div>
                        <dt>Current expiry</dt>
                        <dd id="pg-prev-current">—</dd>
                    </div>
                    <div class="pg-preview-total">
                        <dt>New expiry</dt>
                        <dd id="pg-prev-new">—</dd>
                    </div>
                </dl>
                <p class="pg-preview-note">
                    The new expiry is carried forward from the customer's current one, so paying early never
                    shortens their access.
                </p>
            </aside>
        </div>
    </div>
</div>

<script>
// Show the list price next to the amount box, and offer to fill it in. The
// master can still type a different figure - negotiated Enterprise deals are
// the normal reason - but the standard price is one click away and is never
// guessed from memory.
(function () {
    var plan = document.getElementById('manual-plan');
    var cycle = document.getElementById('manual-cycle');
    var amount = document.getElementById('manual-amount');
    var hint = document.getElementById('manual-price-hint');
    var customer = document.getElementById('manual-customer');
    var paidOn = document.getElementById('manual-date');
    if (!plan || !cycle || !amount || !hint) return;

    var prev = {
        customer: document.getElementById('pg-prev-customer'),
        plan: document.getElementById('pg-prev-plan'),
        list: document.getElementById('pg-prev-list'),
        amount: document.getElementById('pg-prev-amount'),
        current: document.getElementById('pg-prev-current'),
        next: document.getElementById('pg-prev-new')
    };
    var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    function money(value) {
        return '₹' + Number(value).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function toDate(iso) {
        var p = String(iso || '').slice(0, 10).split('-');
        if (p.length !== 3) return null;
        var d = new Date(Number(p[0]), Number(p[1]) - 1, Number(p[2]));
        return isNaN(d.getTime()) ? null : d;
    }

    function prettyDate(iso) {
        var d = toDate(iso);
        return d ? d.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear() : '—';
    }

    function price() {
        var option = plan.options[plan.selectedIndex];
        return option ? parseFloat(option.getAttribute('data-' + cycle.value) || '0') : 0;
    }

    // Mirrors calc_renewal_expiry_date() in admin/master/helpers.php: carry the
    // current expiry forward while it is still in the future, otherwise count
    // from the payment date, so paying early never shortens anybody's access.
    // The server recomputes and stores the real value - this is only a preview.
    function nextExpiry() {
        var months = cycle.value === 'yearly' ? 12 : 1;
        var base = toDate(paidOn && paidOn.value ? paidOn.value : '');
        var current = customer && customer.value && customer.options[customer.selectedIndex]
            ? toDate(customer.options[customer.selectedIndex].getAttribute('data-expiry'))
            : null;
        if (current && base && current >= base) base = current;
        if (!base) return '';
        if (months === 12) base.setFullYear(base.getFullYear() + 1);
        else base.setMonth(base.getMonth() + months);
        return base.getFullYear() + '-' + String(base.getMonth() + 1).padStart(2, '0') + '-' + String(base.getDate()).padStart(2, '0');
    }

    function set(field, value) {
        if (field) field.textContent = value;
    }

    function refresh() {
        var value = price();
        hint.textContent = value ? 'List price for this plan and billing: ₹' + value.toLocaleString('en-IN') : '';

        var planOption = plan.options[plan.selectedIndex];
        var customerOption = customer && customer.value ? customer.options[customer.selectedIndex] : null;
        var currentExpiry = customerOption ? (customerOption.getAttribute('data-expiry') || '') : '';

        set(prev.plan, planOption ? planOption.textContent.trim() : '—');
        set(prev.list, value ? money(value) : '—');
        set(prev.amount, amount.value ? money(amount.value) : '—');
        set(prev.customer, customerOption
            ? (customerOption.getAttribute('data-name') || customerOption.textContent.trim())
            : '—');
        set(prev.current, customerOption ? (currentExpiry ? prettyDate(currentExpiry) : 'No active plan') : '—');
        set(prev.next, customerOption ? prettyDate(nextExpiry()) : '—');
    }

    amount.addEventListener('input', function () {
        if (price() && Math.abs(parseFloat(amount.value || '0') - price()) < 0.005) amount.value = '';
        set(prev.amount, amount.value ? money(amount.value) : '—');
    });
    plan.addEventListener('change', refresh);
    cycle.addEventListener('change', refresh);
    if (customer) customer.addEventListener('change', refresh);
    if (paidOn) paidOn.addEventListener('change', refresh);
    refresh();
})();
</script>

<?php require __DIR__ . '/layout_bottom.php'; ?>
