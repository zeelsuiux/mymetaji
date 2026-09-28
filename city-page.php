<?php
/**
 * My Metaji - city landing page (renders every *-software-*.php city page)
 *
 * The per-city files in the root folder (inventory-software-vadodara.php and
 * friends) are 4-line generated stubs. Each one sets $service_slug and
 * $city_slug and then requires this file, which owns all of the markup and SEO
 * output. There is no page content to keep in sync, only the file list, and
 * includes/city-pages.php keeps that in step with config.php.
 *
 * An unknown service or city returns a real 404 rather than rendering an
 * empty page, so a stale URL cannot masquerade as content.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';

// The generated stub sets these. The $_GET fallback only applies if this file
// is requested directly, which keeps the 404 path working.
$slugService = (string)($service_slug ?? $_GET['service'] ?? '');
$slugCity    = (string)($city_slug ?? $_GET['city'] ?? '');

$service = seo_find_service($slugService);
$city    = seo_find_city($slugCity);

if (!$service || !$city) {
    http_response_code(404);
    $page_noindex   = true;
    $page_title     = 'Page not found - ' . BRAND_NAME;
    $page_desc      = 'The page you were looking for does not exist.';
    $page_canonical = seo_url('index.php');
    include __DIR__ . '/includes/header.php';
    echo '<section class="page-hero"><div class="section-container" style="text-align:center">'
       . '<h1>Page not found</h1>'
       . '<p>That page does not exist. Try the <a href="index.php">homepage</a> or '
       . '<a href="pos-software.php">POS software</a>.</p>'
       . '</div></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$cn       = $city['name'];
$keyword  = $service['kw'] . ' in ' . $cn;
$features = $service['features'];

// ---------- SEO ----------
$page_title     = $service['name'] . ' in ' . $cn . ' - ' . BRAND_NAME;
$page_desc      = seo_city_meta_desc($service, $city);
$page_canonical = seo_city_url($service, $city);
$page_schema    = [
    seo_organization_node(),
    seo_software_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => 'index.php'],
        ['name' => $service['name'], 'url' => $service['page']],
        ['name' => $service['name'] . ' in ' . $cn, 'url' => seo_city_path($service, $city)],
    ]),
    seo_city_service_node($service, $city),
    seo_faq_node(seo_city_faqs($service, $city)),
];

// ---------- Internal links ----------
// The other services in this city, which ties the pages into a cluster instead
// of leaving every city page an orphan.
$otherServices = array_values(array_filter($services, function ($s) use ($service) {
    return $s['slug'] !== $service['slug'];
}));

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="section-container">
        <h1><?= seo_esc($service['name']) ?> <span class="highlight">in <?= seo_esc($cn) ?></span></h1>
        <p><?= seo_esc($service['lead']) ?></p>
        <div class="hero-buttons" style="justify-content:center;margin-top:24px;">
            <a href="#download" class="btn-primary" style="color:#2563EB;">Download Free &rarr;</a>
            <a href="<?= seo_esc($service['page']) ?>" class="btn-outline">All features</a>
        </div>
    </div>
</section>

<section class="section features">
    <div class="section-container">
        <div class="section-title">
            <h2><?= seo_esc($service['name']) ?> <span class="highlight">for <?= seo_esc($cn) ?></span></h2>
            <p><?= seo_esc($service['short']) ?>. Built for businesses across <?= seo_esc($city['state']) ?>.</p>
        </div>
        <div class="features-grid">
            <?php foreach ($features as $f): ?>
                <div class="feature-card">
                    <div class="feature-icon"><?= $f['icon'] ?></div>
                    <h3><?= seo_esc($f['title']) ?></h3>
                    <p><?= seo_esc($f['desc']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section faq">
    <div class="section-container">
        <div class="section-title">
            <h2><?= seo_esc($service['name']) ?> in <?= seo_esc($cn) ?> &mdash; <span class="highlight">FAQ</span></h2>
        </div>
        <div class="faq-list">
            <?php foreach (seo_city_faqs($service, $city) as $i => $faq): ?>
                <div class="faq-item<?= $i === 0 ? ' active' : '' ?>">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <?= seo_esc($faq['question']) ?><span class="arrow"></span>
                    </div>
                    <div class="faq-answer"><p><?= seo_esc($faq['answer']) ?></p></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="section-container">
        <div class="section-title">
            <h2>Also available <span class="highlight">in <?= seo_esc($cn) ?></span></h2>
        </div>
        <div class="rel-grid">
            <?php foreach ($otherServices as $s): ?>
                <a class="rel-card" href="<?= seo_esc(seo_city_path($s, $city)) ?>">
                    <span class="rel-ico">&#128188;</span>
                    <div>
                        <strong><?= seo_esc($s['name']) ?> in <?= seo_esc($cn) ?></strong>
                        <span><?= seo_esc($s['short']) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
