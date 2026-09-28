<?php
/**
 * My Metaji - XML sitemap
 *
 * Built from config.php so it can never drift from the pages that exist. Point
 * Google Search Console at https://mymetaji.com/sitemap.php
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';

header('Content-Type: application/xml; charset=utf-8');

$today = date('Y-m-d');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php
foreach ($seo_static_pages as $page) {
    if (!file_exists(__DIR__ . '/' . $page['file'])) {
        continue;
    }
    printf(
        "    <url>\n        <loc>%s</loc>\n        <lastmod>%s</lastmod>\n        <changefreq>%s</changefreq>\n        <priority>%s</priority>\n    </url>\n",
        htmlspecialchars(seo_url($page['file']), ENT_QUOTES | ENT_XML1, 'UTF-8'),
        $today,
        $page['freq'],
        $page['priority']
    );
}

// Industry pages: retail-software.php and friends. One real file per entry in
// $industry_pages (config.php), so an industry added there shows up here and in
// the nav without any extra step. The industries hub itself is listed from
// $seo_static_pages when it is registered there.
foreach ((array)$industry_pages as $industry) {
    if (!file_exists(__DIR__ . '/' . $industry['file'])) {
        continue;
    }
    printf(
        "    <url>\n        <loc>%s</loc>\n        <lastmod>%s</lastmod>\n        <changefreq>monthly</changefreq>\n        <priority>%s</priority>\n    </url>\n",
        htmlspecialchars(seo_industry_url($industry), ENT_QUOTES | ENT_XML1, 'UTF-8'),
        $today,
        '0.8'
    );
}

// City pages: real .php files such as inventory-software-vadodara.php, written
// by includes/city-pages.php from the same $cities config, so a city added
// there appears here on the next request with no extra step. The head office
// city leads because it is where the company is actually based.
$rank = ['surat' => 0, 'ahmedabad' => 1];
$ordered = $cities;
usort($ordered, function ($a, $b) use ($rank) {
    return ($rank[$a['slug']] ?? 2) <=> ($rank[$b['slug']] ?? 2);
});

foreach ($services as $service) {
    foreach ($ordered as $city) {
        $priority = $city['slug'] === 'surat' ? '0.8' : ($city['slug'] === 'ahmedabad' ? '0.7' : '0.6');
        printf(
            "    <url>\n        <loc>%s</loc>\n        <lastmod>%s</lastmod>\n        <changefreq>monthly</changefreq>\n        <priority>%s</priority>\n    </url>\n",
            htmlspecialchars(seo_city_url($service, $city), ENT_QUOTES | ENT_XML1, 'UTF-8'),
            $today,
            $priority
        );
    }
}
?>
</urlset>
