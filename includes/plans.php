<?php
/**
 * Plan helpers.
 *
 * $plans in config.php is the single source of truth for what a tier costs. It
 * stores the prices as display strings ("₹2,999") because that is what the
 * pricing page and the schema quotes; the gateway needs paise. Rather than
 * keeping a second copy of every price in config.php - where the two would
 * quietly drift - the integer amount is derived from the same string here.
 *
 * Nothing in this file trusts the browser: checkout.php, the create-order API
 * and the master ledger all re-resolve the plan from $plans and recompute the
 * amount, so a tampered form field cannot change what anyone is charged.
 */

/** Cycles a tier can be bought on. */
function plan_cycles() {
    return ['monthly', 'yearly'];
}

/**
 * The tier list, loading it on first use.
 *
 * config.php already exposes $plans for the public pages. The admin panel does
 * not include config.php (that would re-run the city page generator on every
 * renewal click), so it reaches the same data through includes/plan-data.php.
 * Either way there is one copy of the prices.
 */
function plans_all() {
    if (empty($GLOBALS['plans'])) {
        $GLOBALS['plans'] = (require __DIR__ . '/plan-data.php')['plans'];
    }
    return (array)$GLOBALS['plans'];
}

/** Comparison-table headings, keyed by tier level. */
function plan_groups_all() {
    if (empty($GLOBALS['plan_groups'])) {
        $GLOBALS['plan_groups'] = (require __DIR__ . '/plan-data.php')['groups'];
    }
    return (array)$GLOBALS['plan_groups'];
}

/**
 * The price as a display string, or a plain notice when the tier has none.
 *
 * The pricing cards, the SEO schema and the enquiry form all print amounts
 * straight onto a page, so they cannot be allowed to throw when the price list
 * is missing a tier. The sale itself is still refused - plan_amount_rupees()
 * raises - so what happens here is only ever the wording on a page, never the
 * figure the customer is charged.
 */
function plan_price_string($plan, $cycle) {
    $cycle = plan_normalise_cycle($cycle);
    return isset($plan[$cycle]) ? (string)$plan[$cycle] : 'price unavailable';
}

/** Human label for a cycle, e.g. 'Monthly billing'. */
function plan_cycle_label($cycle) {
    return ($cycle === 'yearly') ? 'Yearly billing' : 'Monthly billing';
}

/** How many months of access a cycle buys. */
function plan_cycle_months($cycle) {
    return ($cycle === 'yearly') ? 12 : 1;
}

/** Find a tier by name, case-insensitively. Returns null when there is no match. */
function plan_find($name) {
    $needle = strtolower(trim((string)$name));
    if ($needle === '') return null;
    foreach (plans_all() as $plan) {
        if (strtolower((string)($plan['name'] ?? '')) === $needle) return $plan;
    }
    return null;
}

/** Find a tier by name and fail loudly rather than charge the wrong amount. */
function plan_find_or_die($name) {
    $plan = plan_find($name);
    if (!$plan) {
        throw new InvalidArgumentException('Unknown plan: ' . $name);
    }
    return $plan;
}

/** Normalise a cycle coming from a query string or form. */
function plan_normalise_cycle($cycle) {
    return ($cycle === 'yearly') ? 'yearly' : 'monthly';
}

/** True when the tier is sold through the online checkout. */
function plan_is_online($plan) {
    return !empty($plan['online']);
}

/**
 * Price in rupees for a cycle, e.g. 2999.0 for the yearly Starter plan.
 * Throws if the stored string is not a rupee amount, because silently charging
 * 0 is far worse than refusing the sale.
 */
function plan_amount_rupees($plan, $cycle) {
    $cycle = plan_normalise_cycle($cycle);
    if (!isset($plan[$cycle])) {
        throw new InvalidArgumentException(
            'Plan has no ' . $cycle . ' price. Set it on the master console under Pricing, which writes data/pricing.json.'
        );
    }
    $raw = str_replace(['₹', ',', ' '], '', trim((string)$plan[$cycle]));
    if ($raw === '' || !is_numeric($raw)) {
        throw new InvalidArgumentException('Plan ' . $plan['name'] . ' has an unreadable ' . $cycle . ' price.');
    }
    return (float)$raw;
}

/** Price in paise, the unit Razorpay expects. */
function plan_amount_paise($plan, $cycle) {
    return (int)round(plan_amount_rupees($plan, $cycle) * 100);
}

/** '₹2,999' for summaries and receipts. */
function plan_amount_display($plan, $cycle) {
    return '₹' . number_format(plan_amount_rupees($plan, $cycle), 0);
}

/** Tier name prefixed with its icon, for the ledger and the checkout summary. */
function plan_label($plan) {
    return trim(($plan['icon'] ?? '') . ' ' . ($plan['name'] ?? ''));
}

/** URL that starts the purchase, or '' when the tier is not sold online. */
function plan_checkout_url($plan, $cycle) {
    if (!plan_is_online($plan)) return '';
    return 'checkout.php?plan=' . rawurlencode($plan['name']) . '&cycle=' . rawurlencode(plan_normalise_cycle($cycle));
}

/** Where a tier that is not sold online should send the buyer instead. */
function plan_enquiry_url($plan) {
    $name = rawurlencode((string)($plan['name'] ?? ''));
    return 'enterprise.php?plan=' . $name;
}

/**
 * Last day the buyer is entitled to, from a purchase date.
 * Monthly access ends the day before the same date next month, so a purchase on
 * the 15th never hands out a day it was not paid for.
 */
function plan_expiry_date($purchaseDate, $cycle) {
    $date = DateTime::createFromFormat('Y-m-d', substr((string)$purchaseDate, 0, 10));
    if (!$date) $date = new DateTime('today');
    if (plan_cycle_months($cycle) === 12) {
        $date->modify('+1 year');
    } else {
        $date->modify('+1 month');
    }
    return $date->modify('-1 day')->format('Y-m-d');
}

/** The tier a customer is on, from their most recent paid subscription. */
function plan_from_subscription(array $subscription) {
    $name = trim((string)($subscription['plan'] ?? ''));
    return $name !== '' ? plan_find($name) : null;
}
