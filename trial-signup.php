<?php
/**
 * Public 15-day free trial signup.
 *
 * This is the front door for a new customer: they try the software first, and
 * the paid tiers on pricing.php are what they pay once the trial runs out. No
 * card is asked for and no money moves here - the trial is recorded as a Trial
 * subscription, which is how the master panel already reports trials.
 *
 * The tier the buyer is heading for is stored on the trial, so the renewal
 * button in their dashboard already knows which plan to charge.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';
require_once __DIR__ . '/includes/plans.php';
require_once __DIR__ . '/includes/payment.php';

$plan = plan_find($_GET['plan'] ?? $_POST['plan'] ?? '');
if (!$plan || !plan_is_online($plan)) $plan = plans_all()[1] ?? plans_all()[0] ?? null;

$page_title = 'Start Free Trial - ' . BRAND_NAME;
$page_desc  = 'Try ' . BRAND_NAME . ' free for ' . payment_trial_days() . ' days. No card needed. Your shop, your billing, and you only pay when you are ready.';
$page_schema = [
    seo_organization_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => 'index.php'],
        ['name' => 'Pricing', 'url' => 'pricing.php'],
        ['name' => 'Free Trial', 'url' => 'trial-signup.php'],
    ]),
];

$errors = [];
$success = null;
$csrf = payment_csrf_token('trial');
$cycle = plan_normalise_cycle($_GET['cycle'] ?? $_POST['cycle'] ?? 'yearly');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!payment_csrf_check($_POST['csrf'] ?? '', 'trial')) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $postPlan = plan_find($_POST['plan'] ?? '');
        if ($postPlan && plan_is_online($postPlan)) $plan = $postPlan;
        $cycle = plan_normalise_cycle($_POST['cycle'] ?? 'yearly');
        [$buyer, $buyerErrors] = payment_clean_buyer($_POST);
        $errors = array_merge($errors, $buyerErrors);
        $password = (string)($_POST['password'] ?? '');
        if (mb_strlen($password) < 8) $errors[] = 'Choose a password of at least 8 characters.';

        if (!$errors) {
            $result = payment_start_trial($buyer, $plan, $cycle);
            if (!$result['ok']) {
                $errors[] = $result['error'];
            } else {
                $success = $result;
                // The one-time password goes to the session, never the URL.
                if (session_status() !== PHP_SESSION_ACTIVE) session_start();
                $_SESSION['trial_credentials'] = [
                    'customer_id' => (int)$result['customer']['id'],
                    'username'    => (string)$result['customer']['username'],
                    'password'    => (string)$result['customer']['password_plain'],
                    'slug'        => (string)$result['customer']['slug'],
                ];
            }
        }
    }
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
    <div class="section-container">
        <h1><?= $success ? 'Your trial is <span class="highlight">active</span>' : 'Start your <span class="highlight">' . payment_trial_days() . '-day free trial</span>' ?></h1>
        <p><?= $success
            ? 'Nothing was charged. Your shop is ready to use right now.'
            : 'No card, no setup fee. Raise real bills for ' . payment_trial_days() . ' days and pay only when you are ready to keep it.' ?></p>
    </div>
</section>

<section class="section">
    <div class="section-container">
        <?php if ($success):
            $creds = $_SESSION['trial_credentials'];
            unset($_SESSION['trial_credentials']);
            $slug = (string)$creds['slug'];
        ?>
        <div class="contact-form-wrapper" style="max-width:560px;margin:0 auto;">
            <h3>You are in. Here are your login details</h3>
            <table class="comparison-table" style="box-shadow:none;">
                <tbody>
                    <tr><td>Trial ends on</td><td><b><?= htmlspecialchars(format_display_date_m($success['subscription']['expiry_date']), ENT_QUOTES) ?></b></td></tr>
                    <tr><td>Plan you chose</td><td><?= htmlspecialchars($plan['name'] . ' — ' . plan_amount_display($plan, $cycle) . ' a ' . ($cycle === 'yearly' ? 'year' : 'month'), ENT_QUOTES) ?></td></tr>
                    <tr><td>Login ID</td><td><code><?= htmlspecialchars($creds['username'], ENT_QUOTES) ?></code></td></tr>
                    <tr><td>Password</td><td><code><?= htmlspecialchars($creds['password'], ENT_QUOTES) ?></code></td></tr>
                </tbody>
            </table>
            <div class="alert" style="margin-top:20px;">
                <b>Save this password now.</b> This page will not show it again, and you can change it after logging in.
            </div>
            <div class="download-buttons" style="margin-top:20px;justify-content:center;">
                <a class="dl-btn" href="<?= htmlspecialchars($slug !== '' ? '../' . $slug . '/' : 'login.php', ENT_QUOTES) ?>" style="text-decoration:none;">
                    <span class="icon">&#128719;</span>
                    <div><span>Open</span>Your Shop Panel</div>
                </a>
                <a class="dl-btn" href="index.php#download" style="text-decoration:none;">
                    <span class="icon">&#9654;</span>
                    <div><span>GET IT ON</span>Google Play</div>
                </a>
            </div>
            <p style="font-size:13px;color:var(--gray);margin-top:20px;text-align:center;">
                When the trial ends, keep it for <b><?= htmlspecialchars(plan_amount_display($plan, $cycle), ENT_QUOTES) ?></b>
                <?= $cycle === 'yearly' ? 'a year' : 'a month' ?> on the
                <b><?= htmlspecialchars($plan['name'], ENT_QUOTES) ?></b> plan — or cancel any time and pay nothing.
            </p>
        </div>
        <?php else: ?>
        <form class="contact-form-wrapper" method="post" style="max-width:560px;margin:0 auto;" novalidate>
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
            <h3>Create your shop</h3>
            <?php if ($errors): ?><div class="alert alert-error" style="margin-bottom:16px;"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>
            <div class="form-group">
                <label for="plan">Plan you want after the trial</label>
                <select id="plan" name="plan">
                    <?php foreach (array_filter(plans_all(), 'plan_is_online') as $option): ?>
                        <option value="<?= htmlspecialchars($option['name'], ENT_QUOTES) ?>" <?= $option['name'] === ($plan['name'] ?? '') ? 'selected' : '' ?>>
                            <?= htmlspecialchars(plan_label($option), ENT_QUOTES) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="cycle">Pay monthly or yearly</label>
                <select id="cycle" name="cycle">
                    <?php foreach (plan_cycles() as $option): ?>
                        <option value="<?= $option ?>" <?= $option === $cycle ? 'selected' : '' ?>>
                            <?= htmlspecialchars(plan_cycle_label($option), ENT_QUOTES) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p style="font-size:12px;color:var(--gray);margin:6px 0 0;">Nothing is charged now. We only remember this so the renewal button already shows the plan you want.</p>
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
                <label for="address">Shop Address</label>
                <textarea id="address" name="address" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label for="password">Choose a Password *</label>
                <input type="password" id="password" name="password" minlength="8" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn-submit" style="width:100%;">Start My <?= payment_trial_days() ?>-Day Free Trial →</button>
            <p style="font-size:12px;color:var(--gray);margin-top:12px;text-align:center;">
                Prefer to pay straight away? <a href="pricing.php">See the plans</a>.
            </p>
        </form>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
