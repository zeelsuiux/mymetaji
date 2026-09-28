<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';

$city_service = seo_find_service('e-invoice-software');
$page_desc = 'E-Invoice Software for Indian businesses. ' . $city_service['short']
         . ' Free plan, works offline, no setup fee.';
$page_schema = [
    seo_organization_node(),
    seo_software_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => 'index.php'],
        ['name' => 'E-Invoice Software', 'url' => 'e-invoice.php'],
    ]),
    seo_national_service_node($city_service),
];
$service_slug = 'e-invoice-software';
$page_title ='E-Invoice Software - ' . BRAND_NAME;
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
    <div class="section-container">
        <h1>GST <span class="highlight">E-Invoice</span> Software</h1>
        <p>Generate GST-compliant e-invoices and e-way bills instantly. IRN integration included.</p>
        <div class="hero-buttons" style="justify-content:center;margin-top:24px;">
            <a href="#download" class="btn-primary" style="color:#2563EB;">Download Free →</a>
        </div>
    </div>
</section>

<section class="section features">
    <div class="section-container">
        <div class="section-title">
            <h2>E-Invoice <span class="highlight">Features</span></h2>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🔐</div>
                <h3>IRN Generation</h3>
                <p>Auto-generate Invoice Reference Number (IRN) for every bill as per GST rules.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚛</div>
                <h3>E-Way Bill</h3>
                <p>Create e-way bills for goods movement. Auto-populate invoice details.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">✅</div>
                <h3>GST Compliance</h3>
                <p>100% GST compliant. Auto-validate before submission to GST portal.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Bulk Generation</h3>
                <p>Generate multiple e-invoices at once. Save time during peak hours.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📤</div>
                <h3>Auto Sharing</h3>
                <p>Automatically share e-invoices with customers via email or WhatsApp.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Report Export</h3>
                <p>Export e-invoice reports for GST filing and audit purposes.</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
