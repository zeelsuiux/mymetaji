<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';

$city_service = seo_find_service('accounting-software');
$page_desc = 'Accounting Software for Indian businesses. ' . $city_service['short']
         . ' Free plan, works offline, no setup fee.';
$page_schema = [
    seo_organization_node(),
    seo_software_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => 'index.php'],
        ['name' => 'Accounting Software', 'url' => 'accounting-software.php'],
    ]),
    seo_national_service_node($city_service),
];
$service_slug = 'accounting-software';
$page_title = 'Accounting Software - ' . BRAND_NAME;
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
    <div class="section-container">
        <h1>Simple <span class="highlight">Accounting Software</span> for Small Businesses</h1>
        <p>Track profit/loss, balance sheets, and cash flow — no accounting degree needed.</p>
        <div class="hero-buttons" style="justify-content:center;margin-top:24px;">
            <a href="#download" class="btn-primary" style="color:#2563EB;">Download Free →</a>
        </div>
    </div>
</section>

<section class="section features">
    <div class="section-container">
        <div class="section-title">
            <h2>Accounting <span class="highlight">Made Simple</span></h2>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3>Profit & Loss Reports</h3>
                <p>See your business profitability at a glance with automated P&L statements.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Balance Sheet</h3>
                <p>Track assets, liabilities, and equity with auto-generated balance sheets.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💵</div>
                <h3>Cash Flow Tracking</h3>
                <p>Monitor money coming in and going out. Never lose track of cash.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📑</div>
                <h3>Expense Management</h3>
                <p>Record and categorize all business expenses. Track where money goes.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔄</div>
                <h3>Payment Tracking</h3>
                <p>Track who paid, who owes, and send automatic payment reminders.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Tax Reports</h3>
                <p>Generate GST summaries and tax reports for easy filing.</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
