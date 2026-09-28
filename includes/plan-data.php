<?php
/**
 * Pricing data, kept on its own so both the public site and the admin panel can
 * read the same prices.
 *
 * config.php does `$pricing = require .../includes/plan-data.php;` and exposes
 * $plans / $plan_groups. The admin panel loads this file directly, because
 * pulling in config.php would also re-run the city page generator on every
 * renewal click.
 *
 * Four cumulative tiers. Every feature carries the 'level' it was introduced at,
 * and a plan includes a feature when the plan's level is at or above it. The
 * cards, the comparison table and the schema all read from this one list, so a
 * feature can never be missing from one and present in another.
 *
 * To add a tier: give it the next 'level', then tag its features with it.
 *
 * 'online' decides how the tier is sold: true sends the buyer to checkout.php
 * and takes the money through Razorpay, false routes them to the sales form
 * (a negotiated rollout is not worth a self-serve checkout).
 *
 * The rupee amounts are the one editable part. The master console writes them to
 * data/pricing.json, which is overlaid on the numbers below when this file is
 * loaded; the numbers below stay as the fallback and as the "reset to default"
 * target. Overlaying at load time is why a price change is live immediately, and
 * why every consumer - the cards, the comparison table, checkout.php, the
 * create-order API and the master ledger - moves together instead of drifting.
 *
 * Returning an array (rather than assigning globals) keeps this file free of
 * side effects, so it is safe to require from anywhere.
 */
require_once __DIR__ . '/pricing-store.php';

$planData = [
// ============ PRICING DATA ============
// Four cumulative tiers. Every feature carries the 'level' it was introduced at,
// and a plan includes a feature when the plan's level is at or above it. The
// cards, the comparison table and the schema all read from this one list, so a
// feature can never be missing from one and present in another.
//
// To add a tier: give it the next 'level', then tag its features with it.
//
// 'online' decides how the tier is sold: true sends the buyer to checkout.php
// and takes the money through Razorpay, false routes them to the sales form
// (a negotiated rollout is not worth a self-serve checkout).
//
// There are deliberately no amounts in this file. What a tier costs is master
// data: it lives in data/pricing.json, is edited on the master console's Pricing
// screen, and is overlaid onto the tiers below at load time. Keeping the numbers
// out of the code means there is exactly one place they can be wrong, and the
// person who can change them is the person who owns the console.
//
// A tier the price list does not cover is left with no amount at all, on purpose:
// plan_amount_rupees() then refuses to sell it, which is the correct outcome for a
// missing price. Filling it in with a zero or a guess would be worse - it would
// either give the software away or quietly charge the wrong figure.
'plans' => [
    [
        'level'     => 1,
        'name'      => 'Starter',
        'icon'      => '🟢',
        'desc'      => 'Basic business management to get a new shop billing on day one.',
        'includes'  => '',
        'highlight' => false,
        'btn_text'  => 'Start with Starter',
        'online'    => true,
        'features'  => [
            ['label' => 'Dashboard', 'level' => 1],
            ['label' => 'Customers', 'level' => 1],
            ['label' => 'Customer Credit Limit & Hold', 'level' => 1],
            ['label' => 'Products & Inventory', 'level' => 1],
            ['label' => 'Barcode & Expiry Tracking', 'level' => 1],
            ['label' => 'Leads & Pipeline', 'level' => 1],
            ['label' => 'Invoices & GST Billing', 'level' => 1],
            ['label' => 'Quotations', 'level' => 1],
            ['label' => 'Tasks & Kanban Board', 'level' => 1],
            ['label' => 'Calendar', 'level' => 1],
            ['label' => 'Meetings', 'level' => 1],
        ],
    ],
    [
        'level'     => 2,
        'name'      => 'Business',
        'icon'      => '🔵',
        'desc'      => 'The finance and GST desk, for a business that files its own returns.',
        'includes'  => 'Everything in Starter, plus:',
        'highlight' => true,
        'btn_text'  => 'Choose Business',
        'online'    => true,
        'features'  => [
            ['label' => 'Finance Dashboard', 'level' => 2],
            ['label' => 'Payments & Expenses', 'level' => 2],
            ['label' => 'Debit Notes', 'level' => 2],
            ['label' => 'Credit Notes', 'level' => 2],
            ['label' => 'GSTR-1 & GSTR-3B', 'level' => 2],
            ['label' => 'Sales Report', 'level' => 2],
            ['label' => 'Expense Report', 'level' => 2],
            ['label' => 'Payment Report', 'level' => 2],
            ['label' => 'Outstanding Payments', 'level' => 2],
            ['label' => 'Notifications & Reminders', 'level' => 2],
        ],
    ],
    [
        'level'     => 3,
        'name'      => 'Professional',
        'icon'      => '🟣',
        'desc'      => 'Deeper reporting and WhatsApp billing, for a team that has outgrown manual tracking.',
        'includes'  => 'Everything in Business, plus:',
        'highlight' => false,
        'btn_text'  => 'Choose Professional',
        'online'    => true,
        'features'  => [
            ['label' => 'All Reports Hub', 'level' => 3],
            ['label' => 'Profit & Loss', 'level' => 3],
            ['label' => 'Product Sales', 'level' => 3],
            ['label' => 'HSN Summary', 'level' => 3],
            ['label' => 'State GST', 'level' => 3],
            ['label' => 'Quote Conversion', 'level' => 3],
            ['label' => 'Aging Report', 'level' => 3],
            ['label' => 'Revenue Trend', 'level' => 3],
            ['label' => 'Invoice Profit View', 'level' => 3],
            ['label' => 'Share Invoice Link', 'level' => 3],
            ['label' => 'Wholesaler', 'level' => 3],
            ['label' => 'WhatsApp Billing', 'level' => 3],
        ],
    ],
    [
        'level'     => 4,
        'name'      => 'Enterprise',
        'icon'      => '🟠',
        'desc'      => 'Staff roles, shop configuration and installable apps for larger teams.',
        'includes'  => 'Everything in Professional, plus:',
        'highlight' => false,
        'btn_text'  => 'Talk to Sales',
        'online'    => false,
        // Every label here was checked against a file that actually exists.
        // The previous list (Custom Reports, Custom Workflow, API / Integrations,
        // Custom Configuration, Advanced Analytics, Priority Support) named six
        // things the product has no code for at all, and the panel has no plan
        // gating, so anything listed here is reachable on every tier.
        'features'  => [
            ['label' => 'Subadmin & Staff Roles', 'level' => 4],
            ['label' => 'Staff Activity Log', 'level' => 4],
            ['label' => 'Shop & GST Profile', 'level' => 4],
            ['label' => 'Data Export', 'level' => 4],
            ['label' => 'Offline Installable App', 'level' => 4],
            ['label' => 'Desktop & Android App', 'level' => 4],
        ],
    ],
],

    // Headings for the comparison table, one per tier, in the order they stack.
    'groups' => [
        1 => 'Basic business management',
        2 => 'Finance, GST & receivables',
        3 => 'Reporting, sales & WhatsApp',
        4 => 'Staff, shop setup & apps',
    ],
];

// Hand the tiers over with the master's amounts filled in. A tier the price list
// does not cover comes back without an amount, which makes the sale refuse
// itself rather than guess a figure.
$planData['plans'] = pricing_apply_master_amounts($planData['plans']);

return $planData;
