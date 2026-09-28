<?php
/**
 * Renders pages the way the web server would and asserts the SEO output.
 *
 *   php tools/check-seo.php                          # a few representative pages
 *   php tools/check-seo.php --all                    # every page in the site
 *   php tools/check-seo.php accounting-software.php
 *   php tools/check-seo.php inventory-software-vadodara.php   # a city page
 *   php tools/check-seo.php --cities                 # every service x city page
 *
 * City pages are ordinary .php stubs, so they are included like any other page;
 * requiring config.php above also runs the city-page sync, so any city added
 * to config.php is on disk by the time this script looks for it.
 *
 * Pages are included at script scope (not inside a function) on purpose, so that
 * config.php's $services and $cities land in the global scope exactly as they do
 * under Apache. Including them from a function scope is a false negative.
 */

$root = dirname(__DIR__);
require_once $root . '/config.php';
require_once $root . '/includes/seo.php';

$argvAll = array_slice($argv, 1);

// Matches a generated city stub, so those pages get the extra city assertions.
$cityPagePattern = '~^(' . implode('|', array_map('preg_quote', array_column($services, 'slug')))
    . ')-(' . implode('|', array_map('preg_quote', array_column($cities, 'slug'))) . ')\.php$~';

if (in_array('--all', $argvAll, true)) {
    $pages = [];
    foreach ($seo_static_pages as $p) {
        $pages[] = $p['file'];
    }
    foreach (seo_all_city_pages() as $pair) {
        $pages[] = seo_city_path($pair[0], $pair[1]);
    }
} elseif (in_array('--cities', $argvAll, true)) {
    $pages = [];
    foreach (seo_all_city_pages() as $pair) {
        $pages[] = seo_city_path($pair[0], $pair[1]);
    }
} else {
    $pages = array_values(array_filter($argvAll, function ($a) {
        return strpos($a, '--') !== 0;
    }));
    if (!$pages) {
        $pages = [
            'index.php',
            'pricing.php',
            'accounting-software.php',
            'pos-software-chennai.php',
            'accounting-software-surat.php',
            'gst-software-coimbatore.php',
            'invoice-format.php',
            'partner.php',
        ];
    }
}

// Only page-scoped variables get cleared between pages. Never unset config.php
// data ($faqs, $plans, $contact, $services, $cities, ...) or the pages that
// follow will render with missing data, because config.php is require_once'd.
$pageVars = ['service_slug', 'city_slug', 'service', 'city', 'cityService', 'slugService', 'slugCity'];

$failures = [];
$warns    = [];

function check(string $label, bool $ok, string $detail = ''): void
{
    global $failures, $warns;
    if (!$ok) {
        $failures[] = $label . ($detail !== '' ? " -- $detail" : '');
    }
    printf("  [%s] %s%s\n", $ok ? 'ok' : 'FAIL', $label, ($ok || $detail === '') ? '' : " -- $detail");
}

// --- config.php sanity, before any page is rendered ---
// A city slug repeated in $cities is easy to do by copy-paste, and it fails
// quietly: the same page is generated and listed twice, the sitemap ships
// duplicate URLs, and internal links double up. Nothing downstream reports it,
// so it is asserted here instead.
echo "\n=== config.php ===\n";

$dupeOf = function (array $rows, string $key): array {
    $seen = [];
    foreach ($rows as $row) {
        $slug = $row[$key] ?? '';
        if ($slug === '') {
            continue;
        }
        $seen[$slug] = ($seen[$slug] ?? 0) + 1;
    }
    $out = [];
    foreach ($seen as $slug => $n) {
        if ($n > 1) {
            $out[] = "$slug (x$n)";
        }
    }
    return $out;
};

check('city slugs are unique', !$dupes = $dupeOf($cities, 'slug'), implode(', ', $dupes));
check('service slugs are unique', !$dupes = $dupeOf($services, 'slug'), implode(', ', $dupes));

$empty = array_filter($cities, function ($c) {
    return ($c['slug'] ?? '') === '' || ($c['name'] ?? '') === '' || ($c['state'] ?? '') === '';
});
check('every city has slug + name + state', !$empty, count($empty) . ' incomplete');

// A slug becomes a filename and a URL, so restrict it to what is safe in both.
check('city slugs are URL and filename safe',
    !array_filter($cities, function ($c) {
        return !preg_match('~^[a-z0-9]+(?:-[a-z0-9]+)*$~', (string)($c['slug'] ?? ''));
    }));

// Every generated stub must exist, or a link somewhere is pointing at a 404.
$missingFiles = [];
foreach (seo_all_city_pages() as $pair) {
    $f = seo_city_path($pair[0], $pair[1]);
    if (!file_exists($root . '/' . $f)) {
        $missingFiles[] = $f;
    }
}
check('every city page file exists on disk', !$missingFiles, implode(', ', array_slice($missingFiles, 0, 10)));

foreach ($pages as $page) {
    echo "\n=== $page ===\n";

    foreach ($pageVars as $v) {
        unset($GLOBALS[$v], $v);
    }
    // A page that forgot to set $page_* would silently inherit the previous
    // page's value, so clear them between pages.
    foreach (array_keys($GLOBALS) as $g) {
        if (strpos((string)$g, 'page_') === 0) {
            unset($GLOBALS[$g], $g);
        }
    }

    $_SERVER['PHP_SELF']       = '/' . $page;
    $_SERVER['SCRIPT_NAME']    = '/' . $page;
    $_SERVER['REQUEST_URI']    = '/' . $page;
    $_SERVER['REQUEST_METHOD'] = 'GET';

    ob_start();
    $fatal = null;
    try {
        include $root . '/' . $page;
    } catch (Throwable $e) {
        $fatal = get_class($e) . ': ' . $e->getMessage();
    }
    $html = (string)ob_get_clean();

    if ($fatal !== null) {
        check('page renders', false, $fatal);
        continue;
    }

    check('page renders', strlen($html) > 3000, 'len=' . strlen($html));

    // --- Title ---
    // Length is measured on the decoded string: "&amp;" occupies 5 characters
    // in the attribute but is 1 character as Google sees it.
    preg_match('~<title>(.*?)</title>~s', $html, $m);
    $title = trim(html_entity_decode($m[1] ?? '', ENT_QUOTES, 'UTF-8'));
    check('title present', $title !== '');
    check('title length 15-62', mb_strlen($title) >= 15 && mb_strlen($title) <= 62, 'len=' . mb_strlen($title) . " :: $title");

    // --- Meta description ---
    preg_match('~<meta name="description" content="([^"]*)"~', $html, $m);
    $desc = html_entity_decode($m[1] ?? '', ENT_QUOTES, 'UTF-8');
    check('meta description present', $desc !== '');
    check('meta description length 70-158', mb_strlen($desc) >= 70 && mb_strlen($desc) <= 158, 'len=' . mb_strlen($desc));

    // --- Canonical ---
    preg_match('~<link rel="canonical" href="([^"]*)"~', $html, $m);
    $canonical = $m[1] ?? '';
    check('canonical present', $canonical !== '');
    check('canonical is absolute https', strpos($canonical, 'https://mymetaji.com/') === 0, $canonical);
    check('canonical matches page', $canonical === 'https://mymetaji.com/' . $page, $canonical);

    // --- Open Graph / Twitter ---
    foreach (['og:title', 'og:description', 'og:url', 'og:image', 'og:type', 'og:site_name'] as $og) {
        check("meta $og present", strpos($html, 'property="' . $og . '"') !== false);
    }
    check('twitter:card present', strpos($html, 'name="twitter:card"') !== false);
    check('robots indexable', strpos($html, 'name="robots" content="index, follow') !== false);

    // --- JSON-LD ---
    preg_match_all('~<script type="application/ld\+json">(.*?)</script>~s', $html, $m);
    $nodes = 0;
    $valid = true;
    foreach ($m[1] as $json) {
        $decoded = json_decode($json, true);
        if ($decoded === null) {
            $valid = false;
            $warns[] = "$page: invalid JSON-LD";
            break;
        }
        $nodes += isset($decoded['@graph']) ? count($decoded['@graph']) : 1;
    }
    check('json-ld parses', $valid);
    check('json-ld has >=3 nodes', $nodes >= 3, "nodes=$nodes");
    check('has Organization', strpos($html, '"Organization"') !== false);

    // --- Headings ---
    preg_match_all('~<h1[ >]~i', $html, $m);
    check('exactly one h1', count($m[0]) === 1, 'count=' . count($m[0]));

    // --- Internal links ---
    preg_match_all('~href="([a-z0-9\-]+\.php)"~', $html, $m);
    $links = array_values(array_unique($m[1]));
    foreach ($links as $l) {
        check("link target exists: $l", file_exists($root . '/' . $l));
    }
    check('links back to home', in_array('index.php', $links, true));
    check('links to pricing', in_array('pricing.php', $links, true));

    // How many of those links are other city pages, for the cluster assertion.
    $cityLinks = array_values(array_filter($links, function ($l) use ($cityPagePattern) {
        return (bool) preg_match($cityPagePattern, $l);
    }));

    // City links are real files, so the file_exists loop above already covers
    // them. This extra pass is what catches a stale link: a page still on disk
    // whose city has since been removed from config.php.
    $staleCityLinks = [];
    foreach ($links as $l) {
        if (preg_match($cityPagePattern, $l, $cm) && !file_exists($root . '/' . $l)) {
            $staleCityLinks[] = $l;
        }
    }
    check('no links to retired city pages', !$staleCityLinks, implode(', ', $staleCityLinks));

    // --- City page specific ---
    if (preg_match($cityPagePattern, $page, $cm)) {
        $service = seo_find_service($cm[1]);
        $city    = seo_find_city($cm[2]);

        $h1 = '';
        if (preg_match('~<h1[^>]*>(.*?)</h1>~s', $html, $m)) {
            $h1 = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES, 'UTF-8'));
        }
        check('h1 is "<service> in <city>"',
            $h1 === $service['name'] . ' in ' . $city['name'], "got: $h1");

        check('title names the city', strpos($title, 'in ' . $city['name']) !== false, $title);
        check('description contains ' . $service['kw'] . ' and ' . $city['name'],
            stripos($desc, $service['kw']) !== false && stripos($desc, $city['name']) !== false, $desc);

        check('has Service node with areaServed', strpos($html, '"areaServed"') !== false);
        check('areaServed names ' . $city['name'], strpos($html, '"name":"' . $city['name'] . '"') !== false);
        check('has BreadcrumbList', strpos($html, '"BreadcrumbList"') !== false);
        check('has FAQPage', strpos($html, '"FAQPage"') !== false);
        check('has SoftwareApplication', strpos($html, '"SoftwareApplication"') !== false);
        check('links to its own main service page', in_array($service['page'], $links, true), $service['page']);
        check('links back to home', in_array('index.php', $links, true));
        check('links to >=10 other city pages', count($cityLinks) >= 10, 'found=' . count($cityLinks));
    }

    // --- Hygiene ---
    check('no PHP error in output', !preg_match('~(Warning|Notice|Fatal error|Deprecated|Parse error):~', $html),
        trim((string)preg_match('~(Warning|Notice|Fatal error|Deprecated|Parse error):.*~s', $html, $e) ? $e[0] : ''));
    check('no undefined variable noise', stripos($html, 'undefined variable') === false);
    check('html not empty of body', stripos($html, '</html>') !== false);
}

echo "\n" . str_repeat('-', 60) . "\n";
foreach (array_slice($warns, 0, 20) as $w) {
    echo "WARN: $w\n";
}
if ($failures) {
    echo count($failures) . " FAILURE(S):\n";
    foreach ($failures as $f) {
        echo "  - $f\n";
    }
    exit(1);
}
echo "ALL CHECKS PASSED across " . count($pages) . " page(s)\n";
exit(0);
