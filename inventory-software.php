<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';

$city_service = seo_find_service('inventory-software');
$page_desc = 'Inventory Management Software for Indian businesses. ' . $city_service['short']
         . ' Free plan, works offline, no setup fee.';
$page_schema = [
    seo_organization_node(),
    seo_software_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => 'index.php'],
        ['name' => 'Inventory Management Software', 'url' => 'inventory-software.php'],
    ]),
    seo_national_service_node($city_service),
];
$service_slug = 'inventory-software';
$page_title ='Inventory Software - ' . BRAND_NAME;
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
    <div class="section-container">
        <h1>Smart <span class="highlight">Inventory Management</span> Software</h1>
        <p>Track stock levels in real-time. Get low stock alerts and manage products efficiently.</p>
        <div class="hero-buttons" style="justify-content:center;margin-top:24px;">
            <a href="#download" class="btn-primary" style="color:#2563EB;">Download Free →</a>
        </div>
    </div>
</section>

<section class="section features">
    <div class="section-container">
        <div class="section-title">
            <h2>Inventory Features <span class="highlight">You'll Love</span></h2>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📦</div>
                <h3>Real-Time Stock Tracking</h3>
                <p>Know exactly what's in stock at any moment. No more guessing.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚠️</div>
                <h3>Low Stock Alerts</h3>
                <p>Get notified when stock runs low. Never run out of popular items.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏷️</div>
                <h3>Barcode Support</h3>
                <p>Generate and scan barcodes for faster product management.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏪</div>
                <h3>Multi-Warehouse</h3>
                <p>Manage stock across multiple locations from one dashboard.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔄</div>
                <h3>Stock Transfers</h3>
                <p>Move stock between warehouses with proper documentation.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📈</div>
                <h3>Stock Reports</h3>
                <p>View stock valuation, movement reports, and slow-moving items.</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
