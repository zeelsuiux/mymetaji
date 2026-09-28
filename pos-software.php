<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';

$city_service = seo_find_service('pos-software');
$page_desc = 'POS Software for Indian businesses. ' . $city_service['short']
         . ' Free plan, works offline, no setup fee.';
$page_schema = [
    seo_organization_node(),
    seo_software_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => 'index.php'],
        ['name' => 'POS Software', 'url' => 'pos-software.php'],
    ]),
    seo_national_service_node($city_service),
];
$service_slug = 'pos-software';
$page_title ='POS Software - ' . BRAND_NAME;
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
    <div class="section-container">
        <h1>Fast & Reliable <span class="highlight">POS Software</span></h1>
        <p>Speed up counter sales with quick checkout. Perfect for retail shops.</p>
        <div class="hero-buttons" style="justify-content:center;margin-top:24px;">
            <a href="#download" class="btn-primary" style="color:#2563EB;">Download Free →</a>
        </div>
    </div>
</section>

<section class="section features">
    <div class="section-container">
        <div class="section-title">
            <h2>POS Features <span class="highlight">That Matter</span></h2>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🖥️</div>
                <h3>Touch-Friendly Interface</h3>
                <p>Easy-to-use touch screen billing. Staff can learn in minutes.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Lightning Fast Checkout</h3>
                <p>Complete billing in seconds. Keep customer queues moving.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💳</div>
                <h3>Multiple Payment Modes</h3>
                <p>Accept cash, UPI, cards, and bank transfers at counter.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🖨️</div>
                <h3>Thermal Printer Support</h3>
                <p>Connect thermal printers for fast receipt printing.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>End-of-Day Reports</h3>
                <p>Auto-generate daily sales summaries and cash reconciliation.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Multi-Staff Access</h3>
                <p>Give each staff member their own login with role-based access.</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
