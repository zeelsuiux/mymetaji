<?php
/**
 * My Metaji - industries hub
 *
 * A short index only. Each industry has its own page now (retail-software.php
 * and friends, rendered by includes/industry-page.php), so this page just lists
 * them; the copy for each one lives in $industry_pages in config.php.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';

$page_title     = 'Industries - ' . BRAND_NAME . ' for Every Business Type';
$page_desc      = 'Billing software built for retail, grocery, pharmacy, restaurant, jewellery and clothing businesses. Pick your industry to see the tools made for the way you trade.';
$page_canonical = seo_url('industries.php');
$page_schema    = [
    seo_organization_node(),
    seo_software_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => 'index.php'],
        ['name' => 'Industries', 'url' => 'industries.php'],
    ]),
];

include __DIR__ . '/includes/header.php';
?>

<!-- PAGE HERO -->
<section class="page-hero">
    <div class="section-container">
        <h1>Billing Software for <span class="highlight">Every Industry</span></h1>
        <p>Built for everyday Indian businesses that need simple billing and better control. Pick your industry to see what is ready-made for you.</p>
    </div>
</section>

<!-- ONE PAGE PER INDUSTRY -->
<section class="section ind-section">
    <div class="section-container">
        <div class="section-title">
            <h2>One Software Across <span class="highlight">Every Industry</span></h2>
            <p>From neighbourhood shop owners to growing retailers, <?= BRAND_NAME ?> is built for every Indian business.</p>
        </div>

        <div class="ind-grid">
            <?php foreach ($industry_pages as $ind): ?>
                <a class="ind-card" href="<?= seo_esc(seo_industry_path($ind)) ?>">
                    <div class="ind-card-head">
                        <span class="ind-icon"><?= $ind['icon'] ?></span>
                        <span class="ind-tag"><?= seo_esc($ind['tag']) ?></span>
                    </div>
                    <h3><?= seo_esc($ind['title']) ?></h3>
                    <p><?= seo_esc($ind['short']) ?>.</p>
                    <ul class="ind-list">
                        <?php foreach (array_slice($ind['features'], 0, 3) as $f): ?>
                            <li><?= seo_esc($f['title']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <span class="ind-cta">See <?= seo_esc($ind['tag']) ?> software
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="download-cta" id="download">
    <h2>Start Using <?= BRAND_NAME ?> Today</h2>
    <p>Join thousands of businesses growing with our industry-specific solutions.</p>
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

<?php include __DIR__ . '/includes/footer.php'; ?>
