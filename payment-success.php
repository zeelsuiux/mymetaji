<?php
/**
 * Post-purchase page.
 *
 * Reads the payment out of the master ledger by reference. Nothing about the
 * buyer is taken from the query string, so a guessed ?ref= only ever reveals
 * that some payment exists, never whose it is.
 *
 * The one-time shop password is read from the session (the verify call put it
 * there) and is removed on the way out, so a shared or reloaded link cannot
 * show it twice. A buyer who misses it can reset it from the login page.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';
require_once __DIR__ . '/includes/plans.php';
require_once __DIR__ . '/includes/payment.php';

$ref = (int)($_GET['ref'] ?? 0);
$payment = $ref ? payment_get($ref) : null;

$page_title = 'Payment Successful - ' . BRAND_NAME;
$page_desc  = 'Your ' . BRAND_NAME . ' plan is active.';
$page_schema = [seo_organization_node()];
// A success page should never be indexed.
$page_noindex = true;

$credentials = [];
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$session = $_SESSION['payment_credentials'] ?? [];
if ($payment && (int)($session['payment_id'] ?? 0) === (int)$payment['id'] && !empty($session['username'])) {
    $credentials = $session;
    unset($_SESSION['payment_credentials']);
}

$customer = ($payment && !empty($payment['customer_id'])) ? mdb_get('customers', $payment['customer_id']) : null;
$plan = $payment ? plan_find($payment['plan'] ?? '') : null;
$failed = !$payment || (string)($payment['status'] ?? '') !== 'captured';
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
    <div class="section-container">
        <?php if ($failed): ?>
            <h1>Payment <span class="highlight">not found</span></h1>
            <p>We could not find a completed payment for this link.</p>
        <?php else: ?>
            <h1>Payment <span class="highlight">Successful</span></h1>
            <p><?= !empty($credentials['password'])
                ? 'Your plan is active. Here are your login details.'
                : 'Your plan has been renewed. Your existing login still works.' ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="section-container">
        <?php if ($failed): ?>
            <div class="contact-form-wrapper">
                <h3>Nothing to worry about</h3>
                <p>If you were charged, the payment is recorded and our team will confirm it. Send us the UPI or card reference and we will sort it out.</p>
                <a class="btn-submit" href="contact.php" style="display:inline-block;text-decoration:none;">Contact Support →</a>
            </div>
        <?php else: ?>
        <div class="contact-form-wrapper">
            <h3><?= htmlspecialchars(($plan ? plan_label($plan) : $payment['plan']) . ' plan', ENT_QUOTES) ?> is active</h3>
            <table class="comparison-table" style="box-shadow:none;">
                <tbody>
                    <tr><td>Amount paid</td><td><?= htmlspecialchars('₹' . number_format((float)($payment['amount'] ?? 0), 2), ENT_QUOTES) ?></td></tr>
                    <tr><td>Billing</td><td><?= htmlspecialchars(plan_cycle_label($payment['cycle'] ?? 'monthly'), ENT_QUOTES) ?></td></tr>
                    <tr><td>Paid on</td><td><?= htmlspecialchars(format_display_date_m($payment['paid_at'] ?? $payment['created_at'] ?? ''), ENT_QUOTES) ?></td></tr>
                    <tr><td>Access until</td><td><?= htmlspecialchars(format_display_date_m($payment['expiry_date'] ?? ''), ENT_QUOTES) ?></td></tr>
                    <tr><td>Payment reference</td><td><code><?= htmlspecialchars((string)($payment['razorpay_payment_id'] ?? $payment['transaction_id'] ?? ''), ENT_QUOTES) ?></code></td></tr>
                    <?php if ($customer && !empty($customer['username'])): ?>
                        <tr><td>Login ID</td><td><code><?= htmlspecialchars($customer['username'], ENT_QUOTES) ?></code></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if (!empty($credentials['password'])): ?>
                <div class="alert" style="margin-top:20px;">
                    <b>Your password: <code style="background:#fff;padding:2px 8px;border-radius:4px;"><?= htmlspecialchars($credentials['password'], ENT_QUOTES) ?></code></b><br>
                    Change it after your first login. This page will not show it again.
                </div>
            <?php elseif ($customer && !empty($customer['username'])): ?>
                <p style="margin-top:20px;font-size:14px;color:var(--gray-dark);">
                    Signing in again? Use your usual password — <a href="<?= htmlspecialchars(customer_panel_path($customer['slug'] ?? ''), ENT_QUOTES) ?>login.php">forgot it?</a>
                </p>
            <?php endif; ?>

            <div class="download-buttons" style="margin-top:24px;justify-content:center;">
                <?php if ($customer && !empty($customer['slug'])): ?>
                    <a href="<?= htmlspecialchars(customer_panel_path($customer['slug']), ENT_QUOTES) ?>" class="dl-btn">
                        <span class="icon">&#128719;</span>
                        <div><span>Go to</span>Your Shop Panel</div>
                    </a>
                <?php endif; ?>
                <a href="index.php#download" class="dl-btn">
                    <span class="icon">&#9654;</span>
                    <div><span>GET IT ON</span>Google Play</div>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
