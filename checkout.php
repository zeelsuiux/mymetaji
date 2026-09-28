<?php
/**
 * Public checkout for the online plan tiers.
 *
 * The page only collects who is buying; the price it shows is read from
 * config.php and the price that is actually charged is recomputed on the
 * server by api/payment_create_order.php. The buyer can switch tier and cycle
 * here, but they cannot set an amount.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';
require_once __DIR__ . '/includes/plans.php';
require_once __DIR__ . '/includes/payment.php';

$onlinePlans = array_values(array_filter($plans, 'plan_is_online'));

$plan = plan_find($_GET['plan'] ?? '');
if (!$plan || !plan_is_online($plan)) $plan = $onlinePlans[0] ?? null;
$cycle = plan_normalise_cycle($_GET['cycle'] ?? 'monthly');

$page_title = 'Checkout - ' . BRAND_NAME;
$page_desc  = 'Pay for your ' . BRAND_NAME . ' plan securely by UPI, card or net banking.';
// A checkout holds a one-time price, a form and a token in the HTML. Nothing here
// is worth an index entry, and the plan buttons already link to the real pages.
$page_noindex = true;
$page_schema = [];

$csrf = payment_csrf_token('checkout');

// Filled-in keys are not the same as working keys. A pair the gateway rejects
// would otherwise leave a live "Pay ... Now" button that can never succeed, so
// probe once (cached) and only advertise payment when it actually works.
$gateway = razorpay_credentials_status();
$paymentsReady = razorpay_is_configured() && $gateway['ok'];
$paymentsBlockedReason = $gateway['message'];
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
    <div class="section-container">
        <h1>Secure <span class="highlight">Checkout</span></h1>
        <p>Pay by UPI, card or net banking to keep your plan running. New here? <a href="trial-signup.php">Start <?= payment_trial_days() ?> days free first</a> — nothing to pay today.</p>
    </div>
</section>

<section class="section">
    <div class="section-container">
        <?php if (!$plan): ?>
            <div class="alert alert-error">No plan is available for online purchase. Please <a href="contact.php">contact us</a>.</div>
        <?php else: ?>
        <div class="checkout-layout">

            <form id="checkout-form" class="checkout-card" method="post" novalidate
                  data-create-order="api/payment_create_order.php"
                  data-verify="api/payment_verify.php"
                  data-plan="<?= htmlspecialchars($plan['name'], ENT_QUOTES) ?>"
                  data-cycle="<?= htmlspecialchars($cycle, ENT_QUOTES) ?>">
                <input type="hidden" name="csrf" id="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">

                <div class="checkout-step">
                    <div class="checkout-step-head">
                        <span class="checkout-step-num">1</span>
                        <h3>Choose your plan</h3>
                    </div>

                    <fieldset class="checkout-choices">
                        <legend class="checkout-legend">Plan</legend>
                        <div class="checkout-plan-grid">
                            <?php foreach ($onlinePlans as $option): ?>
                                <?php $isChosen = $option['name'] === $plan['name']; ?>
                                <label class="checkout-plan<?= $isChosen ? ' is-selected' : '' ?>" data-plan-card="<?= htmlspecialchars($option['name'], ENT_QUOTES) ?>">
                                    <input type="radio" name="plan" class="checkout-radio" value="<?= htmlspecialchars($option['name'], ENT_QUOTES) ?>"<?= $isChosen ? ' checked' : '' ?>>
                                    <span class="checkout-plan-body">
                                        <span class="checkout-plan-top">
                                            <span class="checkout-plan-name"><?= htmlspecialchars(plan_label($option), ENT_QUOTES) ?></span>
                                            <?php if (!empty($option['highlight'])): ?>
                                                <span class="checkout-plan-badge">Most popular</span>
                                            <?php endif; ?>
                                        </span>
                                        <span class="checkout-plan-foot">
                                            <span class="checkout-plan-price" data-price-for="<?= htmlspecialchars($option['name'], ENT_QUOTES) ?>">
                                                <?= htmlspecialchars(plan_amount_display($option, $cycle), ENT_QUOTES) ?>
                                                <em>/<?= $cycle === 'yearly' ? 'year' : 'month' ?></em>
                                            </span>
                                            <span class="checkout-plan-meta">or <?= htmlspecialchars(plan_amount_display($option, $cycle === 'yearly' ? 'monthly' : 'yearly'), ENT_QUOTES) ?> <?= $cycle === 'yearly' ? 'a month' : 'a year' ?></span>
                                        </span>
                                    </span>
                                    <span class="checkout-plan-tick" aria-hidden="true"></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>

                    <fieldset class="checkout-choices checkout-choices-billing">
                        <legend class="checkout-legend">Billing</legend>
                        <div class="checkout-cycle-toggle">
                            <?php
                            $cycleLabels = [
                                'monthly' => ['title' => 'Monthly', 'note' => 'Billed every month, cancel any time'],
                                'yearly'  => ['title' => 'Yearly', 'note' => 'Twelve months in one payment'],
                            ];
                            $starterOnline = $onlinePlans[0] ?? $plan;
                            $starterMonthly = plan_amount_rupees($starterOnline, 'monthly');
                            $starterYearly = plan_amount_rupees($starterOnline, 'yearly');
                            $yearlySavePct = $starterMonthly > 0
                                ? (int)round((1 - ($starterYearly / ($starterMonthly * 12))) * 100)
                                : 0;
                            ?>
                            <?php foreach (plan_cycles() as $option): ?>
                                <label class="checkout-cycle<?= $option === $cycle ? ' is-selected' : '' ?>" data-cycle-card="<?= htmlspecialchars($option, ENT_QUOTES) ?>">
                                    <input type="radio" name="cycle" class="checkout-radio" value="<?= htmlspecialchars($option, ENT_QUOTES) ?>"<?= $option === $cycle ? ' checked' : '' ?>>
                                    <span class="checkout-cycle-title">
                                        <?= htmlspecialchars($cycleLabels[$option]['title'] ?? plan_cycle_label($option), ENT_QUOTES) ?>
                                        <?php if ($option === 'yearly' && $yearlySavePct > 0): ?>
                                            <span class="checkout-cycle-save">Save <?= $yearlySavePct ?>%</span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="checkout-cycle-note"><?= htmlspecialchars($cycleLabels[$option]['note'] ?? '', ENT_QUOTES) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>
                </div>

                <div class="checkout-step">
                    <div class="checkout-step-head">
                        <span class="checkout-step-num">2</span>
                        <h3>Your details</h3>
                    </div>
                    <div class="checkout-fields">
                        <div class="form-group">
                            <label for="name">Your Name *</label>
                            <input type="text" id="name" name="name" autocomplete="name" placeholder="e.g. Rajesh Patel" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Mobile Number *</label>
                            <input type="tel" id="phone" name="phone" inputmode="numeric" maxlength="10" autocomplete="tel-national" placeholder="10 digit number" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" autocomplete="email" placeholder="Used for your account and receipts" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Shop Address</label>
                            <textarea id="address" name="address" rows="2" placeholder="Optional — helps us set up your GST details"></textarea>
                        </div>
                    </div>
                </div>

                <div id="checkout-error" class="alert alert-error" style="display:none;"></div>
                <div id="checkout-trial" class="alert alert-info" style="display:none;">
                    <span id="checkout-trial-text"></span>
                    <a id="checkout-trial-link" href="trial-signup.php" class="checkout-alert-link">Start my free trial →</a>
                </div>

                <button type="submit" id="pay-button" class="btn-submit checkout-pay"<?= $paymentsReady ? '' : ' disabled' ?>>
                    <?= $paymentsReady ? 'Pay ' . htmlspecialchars(plan_amount_display($plan, $cycle), ENT_QUOTES) . ' Now →' : 'Online payment unavailable' ?>
                </button>
                <?php if (!$paymentsReady): ?>
                    <p class="checkout-foot checkout-foot-off">
                        <?= htmlspecialchars($paymentsBlockedReason ?: 'Online payment is not switched on yet.', ENT_QUOTES) ?>
                        <a href="contact.php">Contact us</a>
                    </p>
                <?php else: ?>
                    <p class="checkout-foot">Secured by Razorpay. We never see or store your card or UPI details.</p>
                <?php endif; ?>
            </form>

            <aside class="checkout-summary">
                <div class="checkout-card">
                    <h3>Order summary</h3>
                    <div class="checkout-summary-plan">
                        <span class="checkout-summary-name" id="summary-plan"><?= htmlspecialchars(plan_label($plan), ENT_QUOTES) ?></span>
                        <span class="checkout-summary-cycle" id="summary-cycle"><?= htmlspecialchars(plan_cycle_label($cycle), ENT_QUOTES) ?></span>
                    </div>
                    <dl class="checkout-summary-rows">
                        <div>
                            <dt>Plan price</dt>
                            <dd id="summary-amount"><?= htmlspecialchars(plan_amount_display($plan, $cycle), ENT_QUOTES) ?></dd>
                        </div>
                        <div>
                            <dt>Setup fee</dt>
                            <dd class="checkout-summary-muted">₹0</dd>
                        </div>
                        <div>
                            <dt>Access for</dt>
                            <dd id="summary-period"><?= plan_cycle_months($cycle) === 12 ? '12 months' : '1 month' ?></dd>
                        </div>
                    </dl>
                    <div class="checkout-summary-total">
                        <span>Total payable</span>
                        <b id="summary-total"><?= htmlspecialchars(plan_amount_display($plan, $cycle), ENT_QUOTES) ?></b>
                    </div>
                    <p class="checkout-note">
                        One payment covers the whole period. Your existing expiry date is carried forward,
                        so nothing is lost by renewing early.
                    </p>
                </div>

                <div class="checkout-card checkout-reassure">
                    <h4>What you get</h4>
                    <ul>
                        <li>Your shop, billing and inventory ready to use</li>
                        <li>Unlimited customers, products and documents</li>
                        <li>Free software updates</li>
                        <li>Cancel any time — no lock-in</li>
                    </ul>
                </div>
            </aside>

        </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($plan && $paymentsReady): ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<?php endif; ?>
<?php if ($plan): ?>
<script>
(function () {
    var form = document.getElementById('checkout-form');
    if (!form) return;

    // The price table is the same one the server charges from, so the summary
    // and the receipt can never disagree.
    var PRICES = <?= json_encode(array_reduce($onlinePlans, function ($carry, $p) {
        $carry[strtolower($p['name'])] = [
            'label'   => plan_label($p),
            'monthly' => plan_amount_display($p, 'monthly'),
            'yearly'  => plan_amount_display($p, 'yearly'),
            'months'  => ['monthly' => 1, 'yearly' => 12],
        ];
        return $carry;
    }, []), JSON_UNESCAPED_UNICODE) ?>;
    var CYCLE_LABELS = <?= json_encode(array_reduce(plan_cycles(), function ($carry, $c) {
        $carry[$c] = plan_cycle_label($c);
        return $carry;
    }, []), JSON_UNESCAPED_UNICODE) ?>;
    var CYCLE_SUFFIX = { monthly: '/month', yearly: '/year' };

    // Plan and Billing are radio groups now, not <select>s, so the chosen value
    // is read off whichever input is checked. A radio with no checked sibling
    // would mean the markup and this script disagree, so fall back rather than
    // posting an empty plan.
    function checkedValue(name) {
        var picked = form.querySelector('input[name="' + name + '"]:checked');
        return picked ? picked.value : '';
    }

    var payButton = document.getElementById('pay-button');
    var errorBox = document.getElementById('checkout-error');
    var trialBox = document.getElementById('checkout-trial');
    var trialText = document.getElementById('checkout-trial-text');
    var trialLink = document.getElementById('checkout-trial-link');
    var summaryPlan = document.getElementById('summary-plan');
    var summaryCycle = document.getElementById('summary-cycle');
    var summaryAmount = document.getElementById('summary-amount');
    var summaryTotal = document.getElementById('summary-total');
    var summaryPeriod = document.getElementById('summary-period');

    function currentPlan() {
        return PRICES[checkedValue('plan').toLowerCase()] || null;
    }

    function refreshSummary() {
        var price = currentPlan();
        if (!price) return;
        var cycle = checkedValue('cycle');
        var amount = price[cycle] || price.monthly;

        if (summaryPlan) summaryPlan.textContent = price.label;
        if (summaryCycle) summaryCycle.textContent = CYCLE_LABELS[cycle] || '';
        if (summaryAmount) summaryAmount.textContent = amount;
        if (summaryTotal) summaryTotal.textContent = amount;
        if (summaryPeriod) {
            summaryPeriod.textContent =
                (price.months[cycle] === 12 ? '12 months' : '1 month');
        }
        payButton.textContent = 'Pay ' + amount + ' Now →';

        // Re-price every plan card for the chosen billing period, so the
        // cheaper yearly rate is visible before picking a different plan.
        Array.prototype.forEach.call(document.querySelectorAll('[data-price-for]'), function (node) {
            var row = PRICES[node.getAttribute('data-price-for').toLowerCase()];
            if (!row) return;
            node.innerHTML = '';
            node.appendChild(document.createTextNode(row[cycle] || row.monthly));
            var suffix = document.createElement('em');
            suffix.textContent = CYCLE_SUFFIX[cycle] || CYCLE_SUFFIX.monthly;
            node.appendChild(suffix);
        });
    }

    // The selected card is styled with .is-selected; the radio itself stays
    // hidden but keeps native keyboard and form behaviour.
    function paintChoices() {
        Array.prototype.forEach.call(form.querySelectorAll('[data-plan-card]'), function (card) {
            var input = card.querySelector('input[name="plan"]');
            card.classList.toggle('is-selected', !!input && input.checked);
        });
        Array.prototype.forEach.call(form.querySelectorAll('[data-cycle-card]'), function (card) {
            var input = card.querySelector('input[name="cycle"]');
            card.classList.toggle('is-selected', !!input && input.checked);
        });
    }

    form.addEventListener('change', function (event) {
        if (event.target.name !== 'plan' && event.target.name !== 'cycle') return;
        paintChoices();
        refreshSummary();
    });

    function showError(message) {
        trialBox.style.display = 'none';
        errorBox.textContent = message;
        errorBox.style.display = 'block';
    }
    function clearError() {
        trialBox.style.display = 'none';
        errorBox.style.display = 'none';
        errorBox.textContent = '';
    }

    // A buyer who has never registered has not used a trial yet, so the server
    // sends them to the trial form instead of taking money. Show that as a
    // prompt, not an error.
    function showTrialPrompt(message, url) {
        errorBox.style.display = 'none';
        trialText.textContent = message + ' ';
        if (url) trialLink.setAttribute('href', url);
        trialBox.style.display = 'block';
        payButton.disabled = false;
        refreshSummary();
    }

    // Two different single-use tokens: 'checkout' opens the order, 'verify'
    // confirms it. The verify token is minted by the server with the order and
    // handed back here, so a spent order token can never be replayed.
    function post(url, fields, token) {
        var body = new URLSearchParams();
        body.append('csrf', token || document.getElementById('csrf').value);
        Object.keys(fields).forEach(function (key) { body.append(key, fields[key]); });
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString()
        }).then(function (response) { return response.json(); });
    }

    function busy(on, label) {
        payButton.disabled = on;
        if (on) payButton.textContent = label || 'Please wait…';
        else refreshSummary();
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        // Enter can submit a form even with a disabled button, and the button is
        // disabled precisely when the gateway is not usable.
        if (payButton.disabled) return;
        clearError();
        if (!form.reportValidity()) return;

        var plan = checkedValue('plan');
        var cycle = checkedValue('cycle');
        if (!plan || !cycle) {
            showError('Please choose a plan and a billing period.');
            return;
        }

        busy(true, 'Opening secure payment…');
        post(form.dataset.createOrder, {
            plan: plan,
            cycle: cycle,
            name: document.getElementById('name').value,
            phone: document.getElementById('phone').value,
            email: document.getElementById('email').value,
            address: document.getElementById('address').value
        }).then(function (created) {
            if (created.trial_url) {
                showTrialPrompt(created.error || 'Start your free trial first.', created.trial_url);
                return;
            }
            if (!created.ok) throw new Error(created.error || 'Could not start the payment.');
            // The order token is now spent; take the replacement the server sent
            // so a cancelled popup can simply be tried again.
            if (created.checkout_token) document.getElementById('csrf').value = created.checkout_token;
            openCheckout(created);
        }).catch(function (error) {
            busy(false);
            showError(error.message);
        });
    });

    function openCheckout(order) {
        // The Razorpay library is only loaded when the gateway answered, so a
        // stale page or a failed probe cannot reach an undefined constructor.
        if (typeof Razorpay !== 'function') {
            busy(false);
            showError('Online payment is not available right now. Please contact us and we will activate your plan manually.');
            return;
        }
        var razorpay = new Razorpay({
            key: order.key_id,
            amount: order.amount,
            currency: order.currency,
            name: <?= json_encode(BRAND_NAME) ?>,
            description: order.plan + ' plan — ' + (order.cycle === 'yearly' ? 'yearly' : 'monthly'),
            order_id: order.order_id,
            prefill: order.prefill,
            notes: { plan: order.plan, cycle: order.cycle },
            theme: { color: '#2563EB' },
            modal: {
                ondismiss: function () {
                    busy(false);
                    showError('Payment was cancelled. Nothing was charged.');
                }
            },
            handler: function (response) {
                busy(true, 'Confirming your payment…');
                post(form.dataset.verify, {
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_signature: response.razorpay_signature
                }, order.verify_token).then(function (verified) {
                    if (!verified.ok) throw new Error(verified.error || 'Payment could not be confirmed.');
                    window.location.href = verified.redirect;
                }).catch(function (error) {
                    busy(false);
                    showError(error.message + ' If money was deducted it is refunded automatically — please contact us with your UPI reference.');
                });
            }
        });
        razorpay.open();
    }
})();
</script>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
