<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';
require_once __DIR__ . '/includes/payment.php';

$starter = $plans[0];
$page_title = 'Pricing - ' . BRAND_NAME . ' Plans & Pricing';
$page_desc  = BRAND_NAME . ' pricing: Starter at ' . plan_price_string($starter, 'monthly') . ' a month or ' . plan_price_string($starter, 'yearly')
            . ' a year, up to Enterprise at ' . plan_price_string($plans[3], 'monthly') . ' a month or ' . plan_price_string($plans[3], 'yearly')
            . '. Pay monthly or yearly, no setup fee.';

$page_schema = [
    seo_organization_node(),
    seo_software_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => 'index.php'],
        ['name' => 'Pricing', 'url' => 'pricing.php'],
    ]),
];

// How much the yearly price saves against twelve monthly payments, so the copy
// below can never drift from the prices in data/pricing.json. A tier with no
// amount set contributes no saving figure rather than warning on the page.
$monthlyNum = isset($plans[0]['monthly']) ? (float)str_replace(['₹', ','], '', $plans[0]['monthly']) : 0.0;
$yearlyNum  = isset($plans[0]['yearly'])  ? (float)str_replace(['₹', ','], '', $plans[0]['yearly'])  : 0.0;
$yearlySave = $monthlyNum > 0 ? (int)round((1 - ($yearlyNum / ($monthlyNum * 12))) * 100) : 0;

// Every feature across every tier, grouped by the tier that introduced it. Because
// the plans are cumulative, every feature inside a group answers the same question
// - "is this plan at or above the tier that introduced it?" - so each group collapses
// into a single row with its features listed together. That is lossless and takes the
// table from 41 rows down to 8, which is the difference between a table people skim
// and one they scroll past.
$planMatrix = [];
foreach ($plans as $plan) {
    foreach ($plan['features'] as $feature) {
        $planMatrix[(int)$feature['level']][] = $feature['label'];
    }
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<!-- PAGE HERO -->
<section class="page-hero">
    <div class="section-container">
        <h1>Simple, <span class="highlight">Transparent</span> Pricing</h1>
        <p>Four plans that stack on each other. Start with the counter, add the finance desk when you file your own GST, and keep going as the business grows.</p>
    </div>
</section>

<!-- PRICING -->
<section class="section pricing" id="pricing">
    <div class="section-container">
        <div class="trial-banner">
            <div>
                <h2>New here? Start with <?= payment_trial_days() ?> days free</h2>
                <p>No card, no setup fee. Use <?= BRAND_NAME ?> on your real billing first, then pay only if you want to keep it — from <?= htmlspecialchars($plans[0]['yearly'], ENT_QUOTES) ?> a year.</p>
            </div>
            <a href="trial-signup.php" class="btn-cta">Start Free Trial →</a>
        </div>
        <?php include __DIR__ . '/includes/plan-cards.php'; ?>
    </div>
</section>

<!-- COMPARISON TABLE -->
<section class="section">
    <div class="section-container">
        <div class="section-title">
            <h2>Feature <span class="highlight">Comparison</span></h2>
            <p>The plans are cumulative &mdash; everything in an earlier plan is included in every plan above it.</p>
        </div>
        <div class="comparison-table">
            <table>
                <thead>
                    <tr>
                        <th>Feature</th>
                        <?php foreach ($plans as $plan): ?>
                            <th><?= htmlspecialchars($plan['icon'] . ' ' . $plan['name'], ENT_QUOTES) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($planMatrix as $level => $labels): ?>
                        <tr class="comparison-group">
                            <th colspan="<?= count($plans) + 1 ?>" scope="rowgroup">
                                <?= htmlspecialchars($plan_groups[$level] ?? '', ENT_QUOTES) ?>
                                <span class="comparison-count"><?= count($labels) ?></span>
                            </th>
                        </tr>
                        <tr>
                            <td class="comparison-features">
                                <ul>
                                    <?php foreach ($labels as $label): ?>
                                        <li><?= htmlspecialchars($label, ENT_QUOTES) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <?php foreach ($plans as $plan): ?>
                                <td><?= (int)$plan['level'] >= $level ? '&#10003;' : '&mdash;' ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="comparison-group">
                        <th colspan="<?= count($plans) + 1 ?>" scope="rowgroup">Billing</th>
                    </tr>
                    <tr>
                        <td>Monthly price</td>
                        <?php foreach ($plans as $plan): ?>
                            <td><?= htmlspecialchars(plan_price_string($plan, 'monthly'), ENT_QUOTES) ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>Yearly price</td>
                        <?php foreach ($plans as $plan): ?>
                            <td><?= htmlspecialchars(plan_price_string($plan, 'yearly'), ENT_QUOTES) ?></td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="section faq" id="faq">
    <div class="section-container">
        <div class="section-title">
            <h2>Pricing <span class="highlight">FAQ</span></h2>
        </div>
        <div class="faq-list">
            <div class="faq-item active">
                <div class="faq-question" onclick="toggleFaq(this)">Is there a free trial?<span class="arrow">&#9660;</span></div>
                <div class="faq-answer"><p>Yes! We offer a 15-day free trial, no credit card required, so you can run a full billing cycle before you pick a plan. Plans start at <?= htmlspecialchars($plans[0]['monthly'], ENT_QUOTES) ?> a month on <?= htmlspecialchars($plans[0]['name'], ENT_QUOTES) ?>.</p></div>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">What is the difference between monthly and yearly billing?<span class="arrow">&#9660;</span></div>
                <div class="faq-answer"><p>Nothing except the price. Yearly billing is charged once for twelve months and saves <?= $yearlySave ?>% over paying monthly &mdash; <?= htmlspecialchars($plans[0]['yearly'], ENT_QUOTES) ?> instead of &#8377;<?= number_format($monthlyNum * 12) ?> on <?= htmlspecialchars($plans[0]['name'], ENT_QUOTES) ?>. You can move between the two at any time.</p></div>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">What do the plans stack on top of each other?<span class="arrow">&#9660;</span></div>
                <div class="faq-answer"><p><?= htmlspecialchars($plans[0]['name'], ENT_QUOTES) ?> is the base: dashboard, customers, products, leads, invoices, quotations, tasks, calendar and meetings. <?= htmlspecialchars($plans[1]['name'], ENT_QUOTES) ?> adds the finance and GST desk, <?= htmlspecialchars($plans[2]['name'], ENT_QUOTES) ?> adds the deeper reports and WhatsApp billing, and <?= htmlspecialchars($plans[3]['name'], ENT_QUOTES) ?> adds staff roles, shop setup and the installable apps. Each plan includes everything in the ones before it.</p></div>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">Can I switch plans later?<span class="arrow">&#9660;</span></div>
                <div class="faq-answer"><p>Absolutely! You can upgrade or downgrade at any time. Changes take effect immediately with prorated billing, and a yearly plan is adjusted against the time already covered.</p></div>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">What payment methods do you accept?<span class="arrow">&#9660;</span></div>
                <div class="faq-answer"><p>We accept UPI, credit/debit cards, net banking, and bank transfers. All payments are secured with SSL encryption.</p></div>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">Is my data safe if I downgrade?<span class="arrow">&#9660;</span></div>
                <div class="faq-answer"><p>Yes! Your data is always safe. If you downgrade, you'll still have access to view all your data. You can upgrade again anytime.</p></div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="download-cta" id="download">
    <h2>Start Billing Today</h2>
    <p>Install <?= BRAND_NAME ?>, pick a plan, and raise your first GST bill in minutes.</p>
    <div class="download-buttons">
        <a href="#" class="dl-btn" onclick="handleDownload('android'); return false;">
            <span class="icon">&#9654;</span>
            <div><span>GET IT ON</span>Google Play</div>
        </a>
        <a href="#" class="dl-btn" onclick="handleDownload('windows'); return false;">
            <span class="icon">&#128719;</span>
            <div><span>Download for</span>Windows</div>
        </a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
