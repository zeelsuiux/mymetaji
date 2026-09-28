<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';

$city_service = seo_find_service('billing-software');
$page_desc = 'GST Billing Software for Indian businesses. ' . $city_service['short']
         . ' Free plan, works offline, no setup fee.';
$page_schema = [
    seo_organization_node(),
    seo_software_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => 'index.php'],
        ['name' => 'GST Billing Software', 'url' => 'billing-software.php'],
    ]),
    seo_national_service_node($city_service),
];
$service_slug = 'billing-software';
$page_title ='Billing Software - ' . BRAND_NAME;
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
    <div class="section-container">
        <h1>Best <span class="highlight">GST Billing Software</span> for Small Businesses</h1>
        <p>Create GST-compliant invoices in seconds. Auto tax calculation, e-invoicing, and more.</p>
        <div class="hero-buttons" style="justify-content:center;margin-top:24px;">
            <a href="#download" class="btn-primary" style="color:#2563EB;">Download Free →</a>
        </div>
    </div>
</section>

<section class="section features">
    <div class="section-container">
        <div class="section-title">
            <h2>Why Our <span class="highlight">Billing Software</span>?</h2>
            <p>Everything you need for fast, accurate, GST-compliant billing.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📄</div>
                <h3>GST-Ready Invoices</h3>
                <p>Auto-calculate CGST, SGST, IGST on every bill. Generate e-invoices and e-way bills instantly.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>10-Second Invoicing</h3>
                <p>Create professional bills in seconds. Select items, add customer, and print — done!</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🧾</div>
                <h3>Custom Bill Formats</h3>
                <p>Choose from multiple invoice formats. Add your logo, shop name, and branding.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔗</div>
                <h3>WhatsApp Sharing</h3>
                <p>Send bills directly to customers via WhatsApp. Collect payments faster.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏷️</div>
                <h3>Barcode Billing</h3>
                <p>Scan barcodes for instant product lookup. Generate barcodes for your products.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Sales Reports</h3>
                <p>Track daily, weekly, monthly sales. Get insights into your business performance.</p>
            </div>
        </div>
    </div>
</section>

<section class="section how-it-works">
    <div class="section-container">
        <div class="section-title">
            <h2>How to Create a <span class="highlight">Bill</span></h2>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Select Customer</h3>
                <p>Choose existing customer or enter new customer details.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Add Items</h3>
                <p>Add products by scanning barcode or searching by name.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Review & Save</h3>
                <p>Check totals, apply discounts, and save the invoice.</p>
            </div>
            <div class="step-card">
                <div class="step-number">4</div>
                <h3>Share & Print</h3>
                <p>Print the bill or share via WhatsApp/PDF instantly.</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
