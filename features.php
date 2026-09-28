<?php
require_once __DIR__ . '/config.php';
$page_title = 'Features - ' . BRAND_NAME . ' Billing Software';

$deepFeatures = [
    [
        'id' => 'billing',
        'icon' => '📄',
        'badge' => 'Billing',
        'title' => 'GST-Ready Billing',
        'subtitle' => 'Invoices, e-invoice & e-way bills',
        'desc' => 'Create GST-compliant invoices with automatic tax calculation. Generate e-invoices and e-way bills instantly. Support for CGST, SGST, IGST with proper HSN/SAC codes.',
        'features' => ['Auto GST calculation (CGST, SGST, IGST)', 'E-Invoice & E-Way Bill generation', 'HSN/SAC code support', 'Multi-state GST handling', 'GSTR-1 & GSTR-3B reports'],
        'mock' => 'invoice',
    ],
    [
        'id' => 'inventory',
        'icon' => '📦',
        'badge' => 'Inventory',
        'title' => 'Inventory Management',
        'subtitle' => 'Live stock, low-stock alerts & transfers',
        'desc' => 'Track stock levels in real-time. Get low stock alerts, manage warehouses, and handle stock transfers efficiently.',
        'features' => ['Real-time stock tracking', 'Low stock alerts', 'Barcode support', 'Multi-warehouse management', 'Stock transfer between locations'],
        'mock' => 'inventory',
    ],
    [
        'id' => 'reports',
        'icon' => '📊',
        'badge' => 'Accounting',
        'title' => 'Accounting & Reports',
        'subtitle' => 'P&L, balance sheet & cash flow',
        'desc' => 'Get complete business insights with powerful reports. Track profit/loss, balance sheets, and cash flow.',
        'features' => ['Profit & Loss statements', 'Balance Sheet reports', 'Cash Flow tracking', 'Tax summaries', 'Custom report generation'],
        'mock' => 'report',
    ],
];
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<!-- PAGE HERO -->
<section class="page-hero">
    <div class="section-container">
        <h1>Powerful <span class="highlight">Features</span> for Your Business</h1>
        <p>Everything you need to manage billing, inventory, accounting, and more — all in one place.</p>
    </div>
</section>

<!-- MAIN FEATURES -->
<section class="section features" id="features">
    <div class="section-container">
        <div class="section-title">
            <h2>All Features <span class="highlight">At a Glance</span></h2>
            <p>Explore everything <?= BRAND_NAME ?> offers to simplify your business operations.</p>
        </div>
        <div class="features-grid">
            <?php foreach ($features as $feature): ?>
                <div class="feature-card">
                    <div class="feature-icon"><?= $feature['icon'] ?></div>
                    <h3><?= $feature['title'] ?></h3>
                    <p><?= $feature['desc'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- DETAILED FEATURES -->
<section class="section fd-section" id="deep-dive">
    <div class="section-container">
        <div class="section-title">
            <h2>Deep Dive Into <span class="highlight">Key Features</span></h2>
            <p>Tap a feature to see exactly how <?= BRAND_NAME ?> handles it.</p>
        </div>

        <div class="fd-layout">
            <div class="fd-tabs" role="tablist">
                <?php foreach ($deepFeatures as $i => $df): ?>
                    <button type="button" class="fd-tab<?= $i === 0 ? ' active' : '' ?>" data-fd-tab="<?= $df['id'] ?>" role="tab" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>">
                        <span class="fd-tab-icon"><?= $df['icon'] ?></span>
                        <span class="fd-tab-text">
                            <strong><?= $df['title'] ?></strong>
                            <small><?= $df['subtitle'] ?></small>
                        </span>
                        <svg class="fd-tab-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="fd-panels">
                <?php foreach ($deepFeatures as $i => $df): ?>
                    <div class="fd-panel<?= $i === 0 ? ' active' : '' ?>" data-fd-panel="<?= $df['id'] ?>">
                        <div class="fd-panel-body">
                            <span class="fd-badge"><?= $df['badge'] ?></span>
                            <h3><?= $df['icon'] ?> <?= $df['title'] ?></h3>
                            <p><?= $df['desc'] ?></p>
                            <ul class="fd-list">
                                <?php foreach ($df['features'] as $f): ?>
                                    <li><?= $f ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="fd-visual">
                            <div class="fd-window">
                                <div class="fd-window-bar"><span></span><span></span><span></span></div>
                                <div class="fd-window-body">
                                    <?php if ($df['mock'] === 'invoice'): ?>
                                        <div class="mock-invoice">
                                            <h4>GST Invoice</h4>
                                            <div class="line"></div>
                                            <div class="line med"></div>
                                            <div class="line short"></div>
                                            <div class="tax-row">CGST 9%: ₹180</div>
                                            <div class="tax-row">SGST 9%: ₹180</div>
                                            <div class="total">Total: ₹2,180</div>
                                        </div>
                                    <?php elseif ($df['mock'] === 'inventory'): ?>
                                        <div class="mock-inventory">
                                            <h4>Inventory Dashboard</h4>
                                            <div class="stock-item">Widget A - <span class="in-stock">In Stock: 150</span></div>
                                            <div class="stock-item">Widget B - <span class="low-stock">Low Stock: 12</span></div>
                                            <div class="stock-item">Widget C - <span class="out-stock">Out of Stock</span></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="mock-report">
                                            <h4>Business Reports</h4>
                                            <div class="report-row">Sales: ₹5,00,000</div>
                                            <div class="report-row">Expenses: ₹2,00,000</div>
                                            <div class="report-row profit">Profit: ₹3,00,000</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.querySelectorAll('[data-fd-tab]').forEach(function (tab) {
    tab.addEventListener('click', function () {
        var id = this.getAttribute('data-fd-tab');
        document.querySelectorAll('[data-fd-tab]').forEach(function (t) {
            t.classList.toggle('active', t === tab);
            t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
        });
        document.querySelectorAll('[data-fd-panel]').forEach(function (p) {
            p.classList.toggle('active', p.getAttribute('data-fd-panel') === id);
        });
    });
});
</script>

<!-- CTA -->
<section class="download-cta" id="download">
    <h2>Start Using <?= BRAND_NAME ?> Today</h2>
    <p>Join thousands of businesses growing with our powerful features.</p>
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
