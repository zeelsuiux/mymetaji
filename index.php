<?php
/**
 * My Metaji - Home Page
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/seo.php';

$page_title = BRAND_NAME . ' - ' . BRAND_DESCRIPTION;
$page_desc  = BRAND_DESCRIPTION . ' for Indian MSMEs. GST invoicing, inventory, POS, accounting reports and e-invoicing. Plans from ₹299 a month, works offline, 9+ languages.';

$page_schema = [
    seo_organization_node(),
    seo_software_node(),
    seo_web_site_node(),
    seo_faq_node($faqs),
];
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<!-- HERO -->
<section class="hero">
    <div class="hero-container">
        <div class="hero-text">
            <div class="hero-badge">🎯 <?= BRAND_TAGLINE ?></div>
            <h1><?= BRAND_DESCRIPTION ?></h1>
            <p>Easy GST billing, manage tax compliance, track payments, control inventory, and simplify accounting with <?= BRAND_NAME ?>. Start your free trial today.</p>
            <div class="hero-buttons">
                <a href="trial-signup.php" class="btn-primary">Start 15-Day Free Trial →</a>
                <a href="features.php" class="btn-outline">Explore Features</a>
            </div>
            <div class="hero-stats">
                <?php foreach ($stats as $stat): ?>
                    <div class="hero-stat">
                        <h3><?= $stat['value'] ?></h3>
                        <p><?= $stat['label'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="hero-image">
            <div class="hero-image-placeholder">
                <div class="mock-bill">
                    <h4>🏪 <?= BRAND_NAME ?> Invoice</h4>
                    <div class="line"></div>
                    <div class="line med"></div>
                    <div class="line short"></div>
                    <div class="line"></div>
                    <div class="line med"></div>
                    <div class="line short"></div>
                    <div class="line"></div>
                    <div class="total">Total: ₹ 2,499.00</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TRUSTED BY -->
<section class="trusted">
    <p>Trusted by <?= $stats[0]['value'] ?> Indian Businesses Across India</p>
    <div class="trusted-logos">
        <div class="tlogo">🏪 Retail</div>
        <div class="tlogo">🛒 Grocery</div>
        <div class="tlogo">💊 Pharmacy</div>
        <div class="tlogo">🍽️ Restaurant</div>
        <div class="tlogo">💍 Jewellery</div>
        <div class="tlogo">👗 Fashion</div>
        <div class="tlogo">⚡ Electronics</div>
    </div>
</section>

<!-- SOLUTIONS LINKS -->
<section class="section">
    <div class="section-container">
        <div class="section-title">
            <h2>Our <span class="highlight">Solutions</span></h2>
            <p>Explore our complete range of business management tools.</p>
        </div>
        <div class="solutions-link-grid">
            <a href="billing-software.php" class="solution-link-card">
                <span class="solution-link-icon">📄</span>
                <h3>Billing Software</h3>
                <p>GST billing, invoice generation, and e-invoicing</p>
            </a>
            <a href="accounting-software.php" class="solution-link-card">
                <span class="solution-link-icon">📊</span>
                <h3>Accounting Software</h3>
                <p>Profit/loss, balance sheets, and cash flow</p>
            </a>
            <a href="inventory-software.php" class="solution-link-card">
                <span class="solution-link-icon">📦</span>
                <h3>Inventory Software</h3>
                <p>Stock tracking, alerts, and warehouse management</p>
            </a>
            <a href="invoicing-software.php" class="solution-link-card">
                <span class="solution-link-icon">🧾</span>
                <h3>Invoicing Software</h3>
                <p>Professional invoices, estimates, and quotations</p>
            </a>
            <a href="pos-software.php" class="solution-link-card">
                <span class="solution-link-icon">🖥️</span>
                <h3>POS Software</h3>
                <p>Fast counter billing and receipt printing</p>
            </a>
            <a href="e-invoice.php" class="solution-link-card">
                <span class="solution-link-icon">🔐</span>
                <h3>E-Invoice</h3>
                <p>GST e-invoice and e-way bill generation</p>
            </a>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="section features" id="features">
    <div class="section-container">
        <div class="section-title">
            <h2>Why Businesses Choose <span class="highlight"><?= BRAND_NAME ?></span></h2>
            <p>Everything you need to manage your business — all in one place.</p>
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
        <div class="cta-center" style="margin-top:40px;">
            <a href="features.php" class="btn-cta">View All Features →</a>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="section how-it-works">
    <div class="section-container">
        <div class="section-title">
            <h2>How to Get <span class="highlight">Started</span></h2>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Download & Install</h3>
                <p>Download <?= BRAND_NAME ?> for free and install on your device.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Set Up Business</h3>
                <p>Enter business details and add products with prices.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Create Bills</h3>
                <p>Start generating professional GST invoices.</p>
            </div>
            <div class="step-card">
                <div class="step-number">4</div>
                <h3>Track & Grow</h3>
                <p>View reports and grow your business.</p>
            </div>
        </div>
    </div>
</section>

<!-- INDUSTRIES -->
<section class="section industry" id="industries">
    <div class="section-container">
        <div class="section-title">
            <h2>One Software Across <span class="highlight">Every Industry</span></h2>
        </div>
        <div class="industry-grid">
            <?php foreach ($industries as $industry): ?>
                <div class="industry-card">
                    <div class="industry-icon"><?= $industry['icon'] ?></div>
                    <h3><?= $industry['title'] ?></h3>
                    <p><?= $industry['desc'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="cta-center" style="margin-top:40px;">
            <a href="industries.php" class="btn-cta">View All Industries →</a>
        </div>
    </div>
</section>

<!-- STATS BANNER -->
<section class="stats-banner">
    <div class="stats-container">
        <?php foreach ($stats as $stat): ?>
            <div class="stat-item">
                <h2><?= $stat['value'] ?></h2>
                <p><?= $stat['label'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- WHY CHOOSE -->
<section class="section why-choose">
    <div class="section-container">
        <div class="section-title">
            <h2>Why <span class="highlight"><?= BRAND_NAME ?></span>?</h2>
        </div>
        <div class="why-grid">
            <div class="why-card"><h3>⚡ 10-Second Invoicing</h3><p>Create professional GST invoices in seconds. No expertise required.</p></div>
            <div class="why-card"><h3>🔒 Secure Data</h3><p>Encrypted with SSL security and backed up to cloud. Always protected.</p></div>
            <div class="why-card"><h3>💰 Pay Monthly or Yearly</h3><p>Start at <?= htmlspecialchars(plan_price_string($plans[0], 'monthly'), ENT_QUOTES) ?> a month, or pay once a year and save two months.</p></div>
            <div class="why-card"><h3>🌐 9+ Languages</h3><p>Hindi, English, Tamil, Telugu, Marathi, Gujarati & more.</p></div>
            <div class="why-card"><h3>🖨️ Hardware Support</h3><p>Thermal printers, barcode scanners, weighing scales.</p></div>
            <div class="why-card"><h3>📈 Profit Tracking</h3><p>Get profit insights for each bill. Smarter pricing decisions.</p></div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="section testimonials">
    <div class="section-container">
        <div class="section-title">
            <h2>What Our <span class="highlight">Customers</span> Say</h2>
        </div>
        <div class="testimonial-grid">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-card">
                    <div class="stars"><?= str_repeat('★', $testimonial['stars']) ?></div>
                    <p>"<?= $testimonial['text'] ?>"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar"><?= $testimonial['initial'] ?></div>
                        <div class="author-info">
                            <h4><?= $testimonial['name'] ?></h4>
                            <p><?= $testimonial['role'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- PRICING -->
<section class="section pricing" id="pricing">
    <div class="section-container">
        <div class="section-title">
            <h2>Simple, <span class="highlight">Transparent</span> Pricing</h2>
        </div>
        <?php include __DIR__ . '/includes/plan-cards.php'; ?>
        <div class="cta-center" style="margin-top:40px;">
            <a href="pricing.php" class="btn-cta">View Full Pricing →</a>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="section faq" id="faq">
    <div class="section-container">
        <div class="section-title">
            <h2>Frequently Asked <span class="highlight">Questions</span></h2>
        </div>
        <div class="faq-list">
            <?php foreach ($faqs as $i => $faq): ?>
                <div class="faq-item <?= $i === 0 ? 'active' : '' ?>">
                    <div class="faq-question" onclick="toggleFaq(this)"><?= $faq['question'] ?><span class="arrow">▼</span></div>
                    <div class="faq-answer"><p><?= $faq['answer'] ?></p></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- DOWNLOAD -->
<section class="download-cta" id="download">
    <h2>Download <?= BRAND_NAME ?> Now</h2>
    <p>Join thousands of Indian businesses growing with <?= BRAND_NAME ?>.</p>
    <div class="download-buttons">
        <a href="#" class="dl-btn" onclick="handleDownload('android'); return false;">
            <span class="icon">▶</span>
            <div><span>GET IT ON</span>Google Play</div>
        </a>
        <a href="#" class="dl-btn" onclick="handleDownload('ios'); return false;">
            <span class="icon"></span>
            <div><span>Download on the</span>App Store</div>
        </a>
        <a href="#" class="dl-btn" onclick="handleDownload('windows'); return false;">
            <span class="icon">🪟</span>
            <div><span>Download for</span>Windows</div>
        </a>
        <a href="#" class="dl-btn" onclick="handleDownload('mac'); return false;">
            <span class="icon"></span>
            <div><span>Download for</span>Mac</div>
        </a>
    </div>
</section>

<!-- CONTACT -->
<section class="section contact-section" id="contact">
    <div class="section-container">
        <div class="section-title">
            <h2>Get in <span class="highlight">Touch</span></h2>
        </div>
        <div style="max-width:600px;margin:0 auto;">
            <div class="contact-grid">
                <div class="contact-card"><div class="icon">📞</div><h4>Call Us</h4><p><?= $contact['phone1'] ?></p></div>
                <div class="contact-card"><div class="icon">📧</div><h4>Email Us</h4><p><?= $contact['email'] ?></p></div>
            </div>
            <p class="contact-hours">📞 <?= $contact['hours'] ?></p>
            <div class="cta-center" style="margin-top:20px;">
                <a href="contact.php" class="btn-cta">Contact Us →</a>
            </div>
        </div>
    </div>
</section>

<!-- ABOUT -->
<section class="section about-section" id="about">
    <div class="section-container">
        <div class="section-title"><h2>About <span class="highlight"><?= BRAND_NAME ?></span></h2></div>
        <div class="about-content">
            <div class="about-text">
                <p><?= BRAND_NAME ?> was founded with a simple goal — to make billing easy for every Indian business. From small kirana shops to growing retail chains, we believe every business deserves professional-grade billing tools.</p>
                <div class="about-values">
                    <div class="value-item"><span class="value-icon">🎯</span><h4>Simplicity First</h4><p>Billing should be easy, not complicated.</p></div>
                    <div class="value-item"><span class="value-icon">🤝</span><h4>Trust & Security</h4><p>Your data is encrypted and backed up.</p></div>
                    <div class="value-item"><span class="value-icon">🚀</span><h4>Always Improving</h4><p>We update based on real user feedback.</p></div>
                </div>
                <div class="cta-center" style="margin-top:30px;">
                    <a href="about.php" class="btn-cta">Learn More About Us →</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
