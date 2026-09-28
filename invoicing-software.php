<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';

$city_service = seo_find_service('invoicing-software');
$page_desc = 'Invoicing Software for Indian businesses. ' . $city_service['short']
         . ' Free plan, works offline, no setup fee.';
$page_schema = [
    seo_organization_node(),
    seo_software_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => 'index.php'],
        ['name' => 'Invoicing Software', 'url' => 'invoicing-software.php'],
    ]),
    seo_national_service_node($city_service),
];
$service_slug = 'invoicing-software';
$page_title ='Invoicing Software - ' . BRAND_NAME;
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
    <div class="section-container">
        <h1>Professional <span class="highlight">Invoicing Software</span></h1>
        <p>Create GST and Non-GST invoices in seconds. Share via WhatsApp or PDF.</p>
        <div class="hero-buttons" style="justify-content:center;margin-top:24px;">
            <a href="#download" class="btn-primary" style="color:#2563EB;">Download Free →</a>
        </div>
    </div>
</section>

<section class="section features">
    <div class="section-container">
        <div class="section-title">
            <h2>Invoicing <span class="highlight">Made Easy</span></h2>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🧾</div>
                <h3>Multiple Formats</h3>
                <p>Choose from various invoice templates. Customize with your branding.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Quick Creation</h3>
                <p>Create invoices in under 10 seconds. Fast, simple, efficient.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📤</div>
                <h3>Share Anywhere</h3>
                <p>Share via WhatsApp, email, SMS, or download as PDF.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Estimates & Quotations</h3>
                <p>Create estimates and convert them to invoices with one click.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔄</div>
                <h3>Recurring Invoices</h3>
                <p>Set up automatic recurring bills for regular customers.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Invoice Reports</h3>
                <p>Track paid, pending, and overdue invoices with detailed reports.</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
