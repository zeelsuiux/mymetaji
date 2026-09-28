<?php
/**
 * My Metaji - city master (the one place the city list lives)
 *
 * Every city page, the nav dropdown, the footer city hub and the sitemap read
 * $cities from config.php, and config.php builds it from this file. Cities are
 * added, renamed, reordered and removed from the Master Panel
 * (admin/master/cities.php), which saves them to data/cities.json. Nothing else
 * has to be touched: includes/city-pages.php writes the service page files for
 * the new list on the very next page load.
 *
 * Until the panel saves for the first time data/cities.json does not exist and
 * the seed list below is used instead - that seed is also what the panel's
 * "Restore default list" puts back, so treat it as the original 20 cities.
 *
 * data/.htaccess blocks the JSON from being fetched over HTTP; it is read by
 * the site itself, never by a browser.
 */

define('CITY_MASTER_DIR', __DIR__ . '/../data');
define('CITY_MASTER_FILE', CITY_MASTER_DIR . '/cities.json');

function city_master_file() {
    return CITY_MASTER_FILE;
}

/** The shipped city list: the fallback, and what "Restore default list" writes. */
function city_master_seed() {
    return [
        ['slug' => 'surat',       'name' => 'Surat',       'state' => 'Gujarat'],
        ['slug' => 'ahmedabad',   'name' => 'Ahmedabad',   'state' => 'Gujarat'],
        ['slug' => 'vadodara',    'name' => 'Vadodara',    'state' => 'Gujarat'],
        ['slug' => 'rajkot',      'name' => 'Rajkot',      'state' => 'Gujarat'],
        ['slug' => 'gandhinagar', 'name' => 'Gandhinagar', 'state' => 'Gujarat'],
        ['slug' => 'bharuch',     'name' => 'Bharuch',     'state' => 'Gujarat'],
        ['slug' => 'bhavnagar',   'name' => 'Bhavnagar',   'state' => 'Gujarat'],
        ['slug' => 'jamnagar',    'name' => 'Jamnagar',    'state' => 'Gujarat'],
        ['slug' => 'mumbai',      'name' => 'Mumbai',      'state' => 'Maharashtra'],
        ['slug' => 'pune',        'name' => 'Pune',        'state' => 'Maharashtra'],
        ['slug' => 'delhi',       'name' => 'Delhi',       'state' => 'Delhi'],
        ['slug' => 'bengaluru',   'name' => 'Bengaluru',   'state' => 'Karnataka'],
        ['slug' => 'kolkata',     'name' => 'Kolkata',     'state' => 'West Bengal'],
        ['slug' => 'hyderabad',   'name' => 'Hyderabad',   'state' => 'Telangana'],
        ['slug' => 'chennai',     'name' => 'Chennai',     'state' => 'Tamil Nadu'],
        ['slug' => 'coimbatore',  'name' => 'Coimbatore',  'state' => 'Tamil Nadu'],
        ['slug' => 'jaipur',      'name' => 'Jaipur',      'state' => 'Rajasthan'],
        ['slug' => 'lucknow',     'name' => 'Lucknow',     'state' => 'Uttar Pradesh'],
        ['slug' => 'indore',      'name' => 'Indore',      'state' => 'Madhya Pradesh'],
        ['slug' => 'latur',       'name' => 'Latur',       'state' => 'Madhya Pradesh'],
    ];
}

/** States and union territories, for the panel's dropdown. */
function city_master_states() {
    return [
        'Andaman and Nicobar Islands', 'Andhra Pradesh', 'Arunachal Pradesh', 'Assam',
        'Bihar', 'Chandigarh', 'Chhattisgarh', 'Dadra and Nagar Haveli and Daman and Diu',
        'Delhi', 'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jammu and Kashmir',
        'Jharkhand', 'Karnataka', 'Kerala', 'Ladakh', 'Lakshadweep', 'Madhya Pradesh',
        'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha',
        'Puducherry', 'Punjab', 'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana',
        'Tripura', 'Uttar Pradesh', 'Uttarakhand', 'West Bengal',
    ];
}

/** "New Delhi" -> "new-delhi". The URL word for a city. */
function city_master_slug($name) {
    $slug = strtolower(trim((string)$name));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim((string)$slug, '-');
}

/**
 * Keep only what the site can actually render - a slug, a name and a state -
 * and drop duplicate slugs, because two cities on one slug would fight over the
 * same page files. Anything unreadable is simply left out.
 */
function city_master_clean($rows) {
    $clean = [];
    $seen  = [];
    foreach ((array)$rows as $row) {
        if (!is_array($row)) continue;
        $slug = city_master_slug($row['slug'] ?? $row['name'] ?? '');
        $name = trim((string)($row['name'] ?? ''));
        if ($slug === '' || $name === '' || isset($seen[$slug])) continue;
        $seen[$slug] = true;
        $clean[] = [
            'slug'  => $slug,
            'name'  => $name,
            'state' => trim((string)($row['state'] ?? '')) ?: 'Gujarat',
        ];
    }
    return $clean;
}

/** True once the Master Panel has saved a list of its own. */
function city_master_is_saved() {
    return is_readable(city_master_file());
}

/**
 * The live list, in the order it should appear in the nav, footer and sitemap.
 * A garbled file falls back to the seed rather than taking every city page
 * offline; an empty list is honoured, because that is what a master admin
 * deleting every city means.
 */
function city_master_all() {
    if (city_master_is_saved()) {
        $rows = json_decode((string) @file_get_contents(city_master_file()), true);
        if (is_array($rows)) {
            $clean = city_master_clean($rows);
            if (!$rows || $clean) return $clean;
        }
    }
    return city_master_seed();
}

function city_master_save(array $rows) {
    if (!is_dir(CITY_MASTER_DIR) && !@mkdir(CITY_MASTER_DIR, 0755, true) && !is_dir(CITY_MASTER_DIR)) {
        return false;
    }
    $json = json_encode(array_values(city_master_clean($rows)), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return @file_put_contents(city_master_file(), $json . "\n", LOCK_EX) !== false;
}

function city_master_find($slug) {
    foreach (city_master_all() as $row) {
        if ($row['slug'] === $slug) return $row;
    }
    return null;
}
