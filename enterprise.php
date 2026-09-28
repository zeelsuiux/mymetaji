<?php
/**
 * "Talk to Sales" page for the tiers that are not sold through the self-serve
 * checkout (Enterprise).
 *
 * The enquiry is written to the master's leads store rather than emailed, so it
 * appears on the Signup Leads screen tagged with the tier and the sales team
 * cannot lose it in a shared inbox.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';
require_once __DIR__ . '/includes/plans.php';
require_once __DIR__ . '/includes/payment.php';

$enquiryPlans = array_values(array_filter(plans_all(), fn($p) => !plan_is_online($p)));
$plan = plan_find($_GET['plan'] ?? '');
if (!$plan || plan_is_online($plan)) $plan = $enquiryPlans[0] ?? null;

$page_title = 'Talk to Sales - ' . BRAND_NAME;
$page_desc  = 'Need staff roles, a shop-wide setup, or a rollout across many outlets? Tell us what you run and our team will call you within one working day.';
$page_schema = [
    seo_organization_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => 'index.php'],
        ['name' => 'Pricing', 'url' => 'pricing.php'],
        ['name' => 'Talk to Sales', 'url' => 'enterprise.php'],
    ]),
];

$csrf = payment_csrf_token('lead');
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
    <div class="section-container">
        <h1>Talk to <span class="highlight">Sales</span></h1>
        <p>Staff roles across many outlets, a shop-wide setup, or a plan shaped around how your business actually bills.</p>
    </div>
</section>

<section class="section">
    <div class="section-container">
        <div class="contact-form-wrapper" style="max-width:620px;margin:0 auto;">
            <h3>Tell us what you run</h3>
            <?php if ($plan): ?>
                <p style="font-size:14px;color:var(--gray-dark);margin-bottom:20px;">
                    You are enquiring about the <b><?= htmlspecialchars(plan_label($plan), ENT_QUOTES) ?></b> plan<?= isset($plan['yearly']) ? ' — listed at ' . htmlspecialchars(plan_price_string($plan, 'yearly'), ENT_QUOTES) . ' a year' : '' ?>. Final pricing is set with you after we understand the rollout.
                </p>
            <?php endif; ?>
            <form id="lead-form" class="contact-form" method="post" novalidate
                  data-endpoint="api/lead.php" data-plan="<?= htmlspecialchars($plan['name'] ?? '', ENT_QUOTES) ?>">
                <input type="hidden" name="csrf" id="lead-csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
                <?php if (count($enquiryPlans) > 1): ?>
                    <div class="form-group">
                        <label for="plan">Plan</label>
                        <select id="plan" name="plan">
                            <?php foreach ($enquiryPlans as $option): ?>
                                <option value="<?= htmlspecialchars($option['name'], ENT_QUOTES) ?>" <?= ($plan['name'] ?? '') === $option['name'] ? 'selected' : '' ?>><?= htmlspecialchars(plan_label($option), ENT_QUOTES) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="plan" value="<?= htmlspecialchars($plan['name'] ?? '', ENT_QUOTES) ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="company">Company / Shop Name</label>
                    <input type="text" id="company" name="company" placeholder="e.g. Patel Kirana Store">
                </div>
                <div class="form-group">
                    <label for="name">Your Name *</label>
                    <input type="text" id="name" name="name" autocomplete="name" required>
                </div>
                <div class="form-group">
                    <label for="phone">Mobile Number *</label>
                    <input type="tel" id="phone" name="phone" inputmode="numeric" maxlength="10" autocomplete="tel-national" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" autocomplete="email" required>
                </div>
                <div class="form-group">
                    <label for="address">City / Address</label>
                    <textarea id="address" name="address" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label for="message">What do you need?</label>
                    <textarea id="message" name="message" rows="4" placeholder="Number of outlets, billing volume, anything you need built."></textarea>
                </div>
                <div id="lead-error" class="alert alert-error" style="display:none;margin-bottom:16px;"></div>
                <div id="lead-done" class="alert" style="display:none;margin-bottom:16px;"></div>
                <button type="submit" id="lead-button" class="btn-submit" style="width:100%;">Request a Callback →</button>
            </form>
        </div>
    </div>
</section>

<script>
(function () {
    var form = document.getElementById('lead-form');
    if (!form) return;
    var errorBox = document.getElementById('lead-error');
    var doneBox = document.getElementById('lead-done');
    var button = document.getElementById('lead-button');

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        errorBox.style.display = 'none';
        doneBox.style.display = 'none';
        if (!form.reportValidity()) return;

        button.disabled = true;
        button.textContent = 'Sending…';

        var body = new URLSearchParams();
        body.append('csrf', document.getElementById('lead-csrf').value);
        ['plan', 'company', 'name', 'phone', 'email', 'address', 'message'].forEach(function (key) {
            var field = form.querySelector('[name="' + key + '"]');
            if (field) body.append(key, field.value);
        });

        fetch(form.dataset.endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString()
        }).then(function (response) { return response.json(); }).then(function (result) {
            if (!result.ok) throw new Error(result.error || 'Could not send your enquiry.');
            doneBox.textContent = result.message;
            doneBox.style.display = 'block';
            form.reset();
            button.textContent = 'Request a Callback →';
        }).catch(function (error) {
            errorBox.textContent = error.message;
            errorBox.style.display = 'block';
            button.disabled = false;
            button.textContent = 'Request a Callback →';
        });
    });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
