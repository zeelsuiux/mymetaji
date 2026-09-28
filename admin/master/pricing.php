<?php
/**
 * MASTER PANEL - Pricing
 *
 * The one screen that moves money without opening a file: what each tier costs
 * per month and per year. Saving writes data/pricing.json, which
 * includes/plan-data.php overlays on the hardcoded amounts at load time, so the
 * pricing page, the comparison table, the SEO schema, checkout.php, the
 * create-order API and the master ledger all move to the new number together.
 *
 * Only the two amounts are editable. Tier names, feature lists and the
 * online/sales split stay in plan-data.php on purpose, because checkout and the
 * ledger identify a tier by its name - a renamed or reordered tier here would
 * break subscriptions that were already sold against it.
 *
 * The form is validated as a whole and saved all at once: a half-applied price
 * list is worse than a rejected one, because the tiers would disagree with each
 * other and the yearly button could become the cheaper way to buy.
 */
require_once __DIR__ . '/auth.php';
require_master_login();
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/../../includes/plans.php';

$me      = current_master_admin();
$errors  = [];
$notice  = '';
$posted  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!master_csrf_check()) {
        $errors[] = $masterCsrfError;
    } else {
        // Every tier has to come back valid, and the save is all or nothing: a
        // half-applied price list is worse than a rejected one, because the tiers
        // would disagree with each other and the yearly button could become the
        // cheaper way to buy.
        $prices = [];
        foreach (plans_all() as $plan) {
            $name = (string)$plan['name'];
            $key  = 'price_' . preg_replace('/[^A-Za-z0-9_]/', '_', $name);

            $monthly = pricing_parse_amount($_POST[$key . '_monthly'] ?? '');
            $yearly  = pricing_parse_amount($_POST[$key . '_yearly'] ?? '');

            if ($monthly === null) {
                $errors[] = $name . ': enter the monthly amount in whole rupees, like 299.';
            }
            if ($yearly === null) {
                $errors[] = $name . ': enter the yearly amount in whole rupees, like 2999.';
            }
            // A year must never cost less than a month.
            if ($monthly !== null && $yearly !== null && $yearly < $monthly) {
                $errors[] = $name . ': the yearly amount cannot be less than the monthly one.';
            }
            if ($monthly !== null && $yearly !== null) {
                $prices[$name] = ['monthly' => $monthly, 'yearly' => $yearly];
            }
        }

        if ($errors) {
            // Keep what was typed so a rejected form does not have to be retyped.
            $posted = $_POST;
        } else {
            try {
                pricing_store_save($prices, (string)($me['username'] ?? ''));
                $notice = 'Prices saved. The pricing page, checkout and the ledger already use them.';
            } catch (Throwable $e) {
                $errors[] = $e->getMessage();
                $posted   = $_POST;
            }
        }
    }
}

// Live prices. There is nothing to fall back on and nothing to reset to: what a
// tier costs is only ever what is stored in data/pricing.json, so this screen is
// the whole of it.
$live    = plans_all();
$stored  = pricing_store_read();
$meta    = pricing_store_meta();

/** Tiers the price list does not currently cover, which cannot be sold. */
$unpriced = [];
foreach ($live as $plan) {
    if (!isset($plan['monthly'], $plan['yearly'])) $unpriced[] = (string)$plan['name'];
}

/** Show the typed value, else the live one - never a blank box. */
function pricing_field_value($posted, $key, $fallback) {
    if ($posted !== null && isset($posted[$key]) && trim((string)$posted[$key]) !== '') {
        return trim((string)$posted[$key]);
    }
    return $fallback;
}

$pageTitle = 'Pricing';
$activePage = 'pricing';
require __DIR__ . '/layout_top.php';
?>
<?php if ($notice): ?><div class="alert"><?= htmlspecialchars($notice) ?></div><?php endif; ?>
<?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h2><?= icon('payments') ?> Plan Prices</h2>
    </div>
    <div class="panel-body">
        <p class="muted">
            Monthly and yearly amount for each tier, in rupees. Saving changes the
            pricing page, the comparison table, the Google snippet, checkout and the
            master ledger at once - they all read this list.
        </p>

        <?php if (!$stored): ?>
            <div class="alert alert-error">
                <b>No price list.</b> <code>data/pricing.json</code> is missing or
                unreadable, so nothing can be sold. Set every amount below and save to
                start charging again.
            </div>
        <?php elseif ($unpriced): ?>
            <div class="alert alert-error">
                <b>No amount set for: <?= htmlspecialchars(implode(', ', $unpriced)) ?>.</b>
                Those tiers cannot be bought until they have a monthly and yearly amount.
            </div>
        <?php else: ?>
            <div class="alert">
                These are the live prices. Last changed
                <?= htmlspecialchars((string)($meta['updated_at'] ?? 'unknown')) ?>
                <?php if (!empty($meta['updated_by'])): ?>
                    by <?= htmlspecialchars((string)$meta['updated_by']) ?>
                <?php endif; ?>
                - and the checkout will charge exactly these numbers.
            </div>
        <?php endif; ?>

        <form method="post" class="pricing-form">
            <?= master_csrf_field() ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tier</th>
                        <th>Monthly</th>
                        <th>Yearly</th>
                        <th>Yearly saving</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($live as $plan):
                    $name = (string)$plan['name'];
                    $key  = 'price_' . preg_replace('/[^A-Za-z0-9_]/', '_', $name);
                    // An unpriced tier has no amount to read; asking for one would
                    // raise, so the row just shows empty boxes and "not set".
                    $has    = isset($plan['monthly'], $plan['yearly']);
                    $monthly = $has ? plan_amount_rupees($plan, 'monthly') : null;
                    $yearly  = $has ? plan_amount_rupees($plan, 'yearly')  : null;
                    $saving  = $has ? (int)round(($monthly * 12) - $yearly) : 0;
                ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars(trim(($plan['icon'] ?? '') . ' ' . $name)) ?></strong><br>
                            <span class="muted"><?= plan_is_online($plan) ? 'Sold online' : 'Talk to Sales' ?></span>
                            <?php if (!$has): ?><br><span class="badge">not set</span><?php endif; ?>
                        </td>
                        <td>
                            <input type="number" inputmode="numeric" min="1" max="1000000" step="1"
                                   name="<?= htmlspecialchars($key . '_monthly') ?>"
                                   value="<?= htmlspecialchars(pricing_field_value($posted, $key . '_monthly', $monthly === null ? '' : (string)$monthly)) ?>"
                                   required>
                        </td>
                        <td>
                            <input type="number" inputmode="numeric" min="1" max="1000000" step="1"
                                   name="<?= htmlspecialchars($key . '_yearly') ?>"
                                   value="<?= htmlspecialchars(pricing_field_value($posted, $key . '_yearly', $yearly === null ? '' : (string)$yearly)) ?>"
                                   required>
                        </td>
                        <td>
                            <?php if ($saving > 0): ?>
                                <?= htmlspecialchars('₹' . number_format($saving)) ?> (<?= htmlspecialchars((string)round(($saving / ($monthly * 12)) * 100)) ?>%)
                            <?php elseif (!$has): ?>
                                <span class="muted">-</span>
                            <?php else: ?>
                                <span class="muted">none</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Save prices</button>
                <span class="muted">Takes effect immediately. No restart needed.</span>
            </div>
        </form>
    </div>
</div>
