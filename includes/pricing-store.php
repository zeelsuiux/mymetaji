<?php
/**
 * MASTER-EDITABLE PLAN PRICES
 *
 * What each tier costs per month and per year. This file is the only place those
 * numbers exist: there is no second copy in code to drift out of step, and no
 * fallback hiding a mistake. The master console writes to data/pricing.json,
 * includes/plan-data.php overlays it onto the tiers at load time, and everything
 * downstream - the pricing page, the cards, the comparison table, the SEO schema,
 * checkout.php, the create-order API and the master ledger - reads the result.
 *
 * The file sits in data/, next to cities.json, which data/.htaccess already keeps
 * off the web. PHP reads it, nobody downloads it.
 *
 * Nothing here trusts the browser. A posted amount is parsed and range-checked
 * here, and checkout.php, the create-order API and the master ledger all
 * re-resolve the tier through plans_all() before charging, so the amount a
 * customer pays is always the amount the master stored - not the amount that was
 * typed into a form. Because the read happens at request time, a price change is
 * live the moment it is saved; there is no cache to clear and no restart.
 *
 * A missing or damaged file is not papered over. A tier with no usable amount
 * comes back with no amount at all, so plan_amount_rupees() refuses to sell it.
 * A guessed zero would either give the software away or charge the wrong figure,
 * and a stale hardcoded price would quietly override whatever the master set.
 */

/** Where the master console stores the amounts. */
function pricing_store_file() {
    return __DIR__ . '/../data/pricing.json';
}

/**
 * Parse a typed amount into whole rupees, or null when the text is not an
 * amount we are willing to store.
 *
 * Whole rupees only, on purpose: the pricing page and the receipts format with
 * number_format(..., 0), so allowing 299.50 would show the customer a rounded
 * figure the ledger never charged. The upper bound is a guard against a stray
 * keying turning a typo into a five-figure bill.
 */
function pricing_parse_amount($raw) {
    $clean = str_replace(['₹', ',', ' '], '', trim((string)$raw));
    if ($clean === '' || !is_numeric($clean)) return null;
    $amount = (float)$clean;
    if ($amount <= 0 || $amount > 1000000) return null;
    if (abs($amount - round($amount)) > 0.0001) return null;
    return (int)round($amount);
}

/** '₹2,999' - the display format the pricing page and schema already expect. */
function pricing_amount_to_display($amount) {
    return '₹' . number_format((float)$amount, 0);
}

/**
 * The stored amounts, keyed by tier name.
 *
 * A missing or damaged file returns an empty set rather than throwing, because
 * the hardcoded amounts in plan-data.php are still a correct answer and a
 * pricing page must not go down over a bad edit. One unusable tier is skipped
 * instead of discarding the whole file, and the same parser the master form uses
 * is the one that judges the stored value, so anything that reaches a customer
 * has already been through it.
 */
function pricing_store_read() {
    static $cache = null;
    if ($cache !== null) return $cache;

    $cache = [];
    $file = pricing_store_file();
    if (!is_file($file)) return $cache;
    $raw = @file_get_contents($file);
    if ($raw === false || trim($raw) === '') return $cache;
    $data = json_decode($raw, true);
    if (!is_array($data) || empty($data['prices']) || !is_array($data['prices'])) return $cache;

    foreach ($data['prices'] as $name => $pair) {
        $name = trim((string)$name);
        if ($name === '' || !is_array($pair)) continue;
        $monthly = pricing_parse_amount($pair['monthly'] ?? '');
        $yearly  = pricing_parse_amount($pair['yearly'] ?? '');
        if ($monthly === null || $yearly === null) continue;
        // A year must never cost less than a month, or the yearly button would
        // quietly become the cheaper way to buy the same access.
        if ($yearly < $monthly) continue;
        $cache[$name] = ['monthly' => $monthly, 'yearly' => $yearly];
    }
    return $cache;
}

/** Who changed the prices last, and when. For the master screen. */
function pricing_store_meta() {
    $file = pricing_store_file();
    if (!is_file($file)) return null;
    $data = json_decode((string)@file_get_contents($file), true);
    return is_array($data) ? $data : null;
}

/**
 * Persist the amounts. Only the master console calls this.
 *
 * Written to a temp file and swapped in, so a visitor arriving mid-save reads
 * either the old list or the new one and never a half-written one.
 */
function pricing_store_save(array $prices, $by = '') {
    $file = pricing_store_file();
    $dir = dirname($file);
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    if (!is_writable($dir)) {
        throw new RuntimeException('The data folder is not writable, so the price list cannot be saved.');
    }

    $json = json_encode([
        'updated_at' => date('Y-m-d H:i:s'),
        'updated_by' => (string)$by,
        'prices'     => $prices,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) throw new RuntimeException('The price list could not be encoded.');

    $tmp = $file . '.tmp';
    if (@file_put_contents($tmp, $json . "\n", LOCK_EX) === false) {
        throw new RuntimeException('The price list could not be written.');
    }
    if (!@rename($tmp, $file)) {
        @unlink($tmp);
        throw new RuntimeException('The price list could not be saved.');
    }
    return true;
}

/**
 * Fill each tier's two amounts from the master list.
 *
 * A tier the list does not cover is left with no amount at all, which makes the
 * sale refuse itself through plan_amount_rupees() rather than fall back to a
 * guess. That is deliberate: a missing price should stop a sale, not complete one
 * at the wrong figure.
 */
function pricing_apply_master_amounts(array $plans) {
    $stored = pricing_store_read();
    if (!$stored) return $plans;

    foreach ($plans as $i => $plan) {
        $name = (string)($plan['name'] ?? '');
        if (!isset($stored[$name])) continue;
        $plan['monthly'] = pricing_amount_to_display($stored[$name]['monthly']);
        $plan['yearly']  = pricing_amount_to_display($stored[$name]['yearly']);
        $plans[$i] = $plan;
    }
    return $plans;
}
