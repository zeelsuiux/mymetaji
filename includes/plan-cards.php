<?php
/**
 * My Metaji - plan cards
 *
 * The pricing grid, rendered from $plans in config.php. Included by both
 * pricing.php and index.php so the two can never show different prices.
 *
 * A card lists only the features its own tier introduced; the tiers below it are
 * called out by the "Everything in X, plus:" line, because the plans are
 * cumulative - everything in Starter is also in Business, Professional and
 * Enterprise.
 *
 * Sets $plan_cards (the card markup) for the caller if it wants to place the
 * monthly/yearly toggle itself; otherwise the whole picker is emitted.
 */
if (!isset($plans) || !$plans) {
    return;
}
require_once __DIR__ . '/plans.php';

$planEscape = function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};

/** The features a tier introduced, i.e. the ones no cheaper tier has. */
$planOwnFeatures = function (array $plan) {
    $own = [];
    foreach ($plan['features'] as $feature) {
        if ((int)$feature['level'] === (int)$plan['level']) {
            $own[] = $feature['label'];
        }
    }
    return $own;
};

/** How much the yearly price saves against twelve monthly payments. */
$planSaving = function (array $plan) {
    // A tier with no amount set has no saving to advertise; plan_amount_rupees()
    // would raise, and a card must still render.
    if (!isset($plan['monthly'], $plan['yearly'])) return 0;
    $monthly = (float)str_replace(['₹', ','], '', $plan['monthly']);
    $yearly  = (float)str_replace(['₹', ','], '', $plan['yearly']);
    if ($monthly <= 0) return 0;
    return (int)round((1 - ($yearly / ($monthly * 12))) * 100);
};
?>
<div class="plan-picker" data-plan-picker>
    <div class="plan-toggle" role="group" aria-label="Billing cycle">
        <button type="button" class="plan-toggle-btn is-active" data-cycle="monthly" aria-pressed="true">Monthly</button>
        <button type="button" class="plan-toggle-btn" data-cycle="yearly" aria-pressed="false">Yearly <span>2 months free</span></button>
    </div>

    <div class="pricing-grid">
        <?php foreach ($plans as $plan):
            $saving = $planSaving($plan);
            $own    = $planOwnFeatures($plan);
            ?>
            <div class="pricing-card<?= $plan['highlight'] ? ' highlighted' : '' ?>">
                <?php if ($plan['highlight']): ?>
                    <div class="pricing-badge">Most Popular</div>
                <?php endif; ?>
                <h3><span class="plan-icon" aria-hidden="true"><?= $planEscape($plan['icon']) ?></span><?= $planEscape($plan['name']) ?></h3>
                <div class="pricing-price">
                    <span class="amount" data-monthly="<?= $planEscape(plan_price_string($plan, 'monthly')) ?>" data-yearly="<?= $planEscape(plan_price_string($plan, 'yearly')) ?>"><?= $planEscape(plan_price_string($plan, 'monthly')) ?></span>
                    <span class="period" data-monthly="/month" data-yearly="/year">/month</span>
                </div>
                <p class="plan-cycle-note"
                   data-monthly="Billed monthly, cancel any time"
                   data-yearly="Billed yearly &middot; saves <?= $saving ?>%">Billed monthly, cancel any time</p>
                <p class="pricing-desc"><?= $planEscape($plan['desc']) ?></p>
                <?php if (!empty($plan['includes'])): ?>
                    <p class="plan-includes"><?= $planEscape($plan['includes']) ?></p>
                <?php endif; ?>
                <ul class="pricing-features">
                    <?php foreach ($own as $feature): ?>
                        <li><?= $planEscape($feature) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php if (!empty($plan['online'])): ?>
                    <a class="pricing-btn <?= $plan['highlight'] ? 'btn-primary-dark' : 'btn-outline-dark' ?>"
                       href="<?= $planEscape(plan_checkout_url($plan, 'monthly')) ?>"
                       data-plan-href="<?= $planEscape('checkout.php?plan=' . rawurlencode($plan['name']) . '&cycle={cycle}') ?>"><?= $planEscape($plan['btn_text']) ?></a>
                <?php else: ?>
                    <a class="pricing-btn btn-outline-dark" href="<?= $planEscape(plan_enquiry_url($plan)) ?>"><?= $planEscape($plan['btn_text']) ?></a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
(function () {
    document.querySelectorAll('[data-plan-picker]').forEach(function (picker) {
        var buttons = picker.querySelectorAll('.plan-toggle-btn');
        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var cycle = button.dataset.cycle;
                buttons.forEach(function (b) {
                    var on = b === button;
                    b.classList.toggle('is-active', on);
                    b.setAttribute('aria-pressed', on ? 'true' : 'false');
                });
                picker.querySelectorAll('[data-monthly]').forEach(function (node) {
                    node.textContent = node.dataset[cycle];
                });
                // Keep the buy button pointing at the cycle the buyer is looking at.
                picker.querySelectorAll('[data-plan-href]').forEach(function (link) {
                    link.setAttribute('href', link.dataset.planHref.replace('{cycle}', cycle));
                });
                picker.classList.toggle('is-yearly', cycle === 'yearly');
            });
        });
    });
})();
</script>
