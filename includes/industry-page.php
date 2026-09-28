<?php
/**
 * My Metaji - industry landing page (renders retail-software.php and friends)
 *
 * Each industry page in the root folder (jewellery-software.php and the rest) is
 * a short stub that sets $industry_slug and then requires this file, which owns
 * all of the markup and SEO output - the same split the city pages use. The copy
 * lives in $industry_pages in config.php, so the nav, the footer and the sitemap
 * are generated from the same list and can never drift.
 *
 * An unknown industry returns a real 404 rather than an empty page, so a stale
 * URL cannot masquerade as content.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/seo.php';

// The generated stub sets this. The $_GET fallback only applies when this file is
// requested directly, which keeps the 404 path working.
$slugIndustry = (string)($industry_slug ?? $_GET['industry'] ?? '');
$industry     = seo_find_industry($slugIndustry);

if (!$industry) {
    http_response_code(404);
    $page_noindex   = true;
    $page_title     = 'Page not found - ' . BRAND_NAME;
    $page_desc      = 'The page you were looking for does not exist.';
    $page_canonical = seo_url('index.php');
    include __DIR__ . '/header.php';
    echo '<section class="page-hero"><div class="section-container" style="text-align:center">'
       . '<h1>Page not found</h1>'
       . '<p>That page does not exist. Try the <a href="index.php">homepage</a> or '
       . '<a href="industries.php">the industries we serve</a>.</p>'
       . '</div></section>';
    include __DIR__ . '/footer.php';
    exit;
}

$features = $industry['features'];
$others   = seo_other_industries($industry);

// ---------- SEO ----------
$page_title     = $industry['title'] . ' - ' . BRAND_NAME;
$page_desc      = seo_industry_meta_desc($industry);
$page_canonical = seo_industry_url($industry);
$page_schema    = [
    seo_organization_node(),
    seo_software_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => 'index.php'],
        ['name' => 'Industries', 'url' => 'industries.php'],
        ['name' => $industry['tag'], 'url' => seo_industry_path($industry)],
    ]),
    seo_industry_service_node($industry),
    seo_faq_node(seo_industry_faqs($industry)),
];

include __DIR__ . '/header.php';
?>

<section class="page-hero">
    <div class="section-container">
        <h1><?= seo_esc($industry['icon']) ?> <?= seo_esc($industry['title']) ?></h1>
        <p><?= seo_esc($industry['lead']) ?></p>
        <div class="hero-buttons" style="justify-content:center;margin-top:24px;">
            <a href="#download" class="btn-primary" style="color:#2563EB;">Download Free &rarr;</a>
            <a href="billing-software.php" class="btn-outline">All features</a>
        </div>
    </div>
</section>

<section class="section features">
    <div class="section-container">
        <div class="section-title">
            <h2><?= seo_esc($industry['tag']) ?> Tools <span class="highlight">Built In</span></h2>
            <p><?= seo_esc($industry['short']) ?>. Switch on only what your business needs.</p>
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
            <h2><?= seo_esc($industry['tag']) ?> Billing Software &mdash; <span class="highlight">FAQ</span></h2>
        </div>
        <div class="faq-list">
            <?php foreach (seo_industry_faqs($industry) as $i => $faq): ?>
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
            <h2>Also built <span class="highlight">for other industries</span></h2>
        </div>
        <div class="rel-grid">
            <?php foreach ($others as $other): ?>
                <a class="rel-card" href="<?= seo_esc(seo_industry_path($other)) ?>">
                    <span class="rel-ico"><?= $other['icon'] ?></span>
                    <div>
                        <strong><?= seo_esc($other['tag']) ?></strong>
                        <span><?= seo_esc($other['short']) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="download-cta" id="download">
    <h2>Start Using <?= BRAND_NAME ?> Today</h2>
    <p>Free to start, and built for <?= seo_esc(strtolower($industry['tag'])) ?> businesses from day one.</p>
    <div class="download-buttons">
        <a href="#" class="dl-btn" onclick="handleDownload('android'); return false;">
            <span class="icon">▶</span>
            <div><span>GET IT ON</span>Google Play</div>
        </a>
        <a href="#" class="dl-btn" onclick="handleDownload('windows'); return false;">
            <span class="icon">🪟</span>
            <div><span>Download for</span>Windows</div>
        </a>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
