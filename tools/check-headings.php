<?php
/**
 * Reports every <h1> on every marketing page. Google uses the first h1 as the
 * page's primary heading; a second one is a structural defect.
 *
 *   php tools/check-headings.php
 */

$root = dirname(__DIR__);
require_once $root . '/config.php';
require_once $root . '/includes/seo.php';

$pages = [];
foreach ($seo_static_pages as $p) {
    $pages[] = $p['file'];
}
foreach (seo_all_city_pages() as $pair) {
    $pages[] = seo_city_path($pair[0], $pair[1]);
}
$pages = array_values(array_unique($pages));

$bad = 0;
foreach ($pages as $page) {
    $path = $root . '/' . $page;
    if (!file_exists($path)) {
        continue;
    }
    $src = (string)file_get_contents($path);

    // City pages are stubs that require city-page.php for their markup, so pull
    // that template in before counting. Test for the require rather than for
    // $service_slug, because the nationwide service pages set that too.
    if (strpos($src, "require __DIR__ . '/city-page.php'") !== false) {
        $src .= (string)file_get_contents($root . '/city-page.php');
    }

    // Strip PHP blocks and the shared partials so we only count the page's own
    // markup. Header/footer h1s would otherwise be counted once per page.
    $src = preg_replace('~<\?php.*?\?>~s', '', $src);
    $src = preg_replace('~<\?php.*?\?>|<\?=.*?\?>~s', '', $src);

    preg_match_all('~<h1\b[^>]*>(.*?)</h1>~si', $src, $m);
    $count = count($m[0]);
    if ($count !== 1) {
        $bad++;
        echo "$page: $count h1\n";
        foreach ($m[1] as $h) {
            echo "    " . trim(preg_replace('~\s+~', ' ', strip_tags($h))) . "\n";
        }
    }
}

echo "\n", $bad === 0 ? "All pages have exactly one h1" : "$bad page(s) with a wrong h1 count", "\n";
exit($bad === 0 ? 0 : 1);
