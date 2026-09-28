<?php
/**
 * My Metaji - Footer Section (Vyapar-style)
 */
?>
<footer>
    <div class="footer-container">
        <div class="row g-4 g-lg-5 footer-top-grid">

            <!-- Brand Column -->
            <div class="col-12 col-lg-4 col-xl-3 footer-brand">
                <img src="assets/images/white-logo.png" alt="<?= BRAND_NAME ?>" class="footer-logo">
                <p>India's best GST billing, accounting & inventory software for small business owners. Simple, powerful, and built for Indian MSMEs.</p>
                <div class="social-links d-flex flex-wrap gap-2">
                    <a href="<?= $social['facebook'] ?>" target="_blank" title="Facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="<?= $social['twitter'] ?>" target="_blank" title="Twitter/X">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="<?= $social['linkedin'] ?>" target="_blank" title="LinkedIn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <a href="<?= $social['youtube'] ?>" target="_blank" title="YouTube">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    <a href="<?= $social['instagram'] ?>" target="_blank" title="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                    </a>
                </div>
                <div class="footer-app-badges d-flex flex-wrap gap-2">
                    <a href="#" class="footer-badge-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M3 20.5v-17c0-.83.52-1.28 1-1.5l10 10-10 10c-.48-.22-1-.67-1-1.5zm15-7.5l-3.5 3.5-2-2L3 22l12.5-5.5-2-2zm0-4l-3.5-3.5 2-2L18 1.5 5.5 7l2 2L18 9zm-7.5-6.5l2 2-2 2-2-2 2-2z"/></svg>
                        Google Play
                    </a>
                    <a href="#" class="footer-badge-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                        App Store
                    </a>
                </div>
            </div>

            <!-- Link Columns -->
            <div class="col-12 col-lg-8 col-xl-9">
                <div class="row g-4 g-lg-5">

            <!-- Software -->
            <div class="col-6 col-md-4 col-lg-4 col-xl-2 footer-col">
                <h4>Software</h4>
                <ul>
                    <li><a href="billing-software.php">Billing Software</a></li>
                    <li><a href="accounting-software.php">Accounting Software</a></li>
                    <li><a href="inventory-software.php">Inventory Software</a></li>
                    <li><a href="invoicing-software.php">Invoicing Software</a></li>
                    <li><a href="pos-software.php">POS Software</a></li>
                    <li><a href="e-invoice.php">E-Invoice</a></li>
                </ul>
            </div>

            <!-- Industry Solutions -->
            <div class="col-6 col-md-4 col-lg-4 col-xl-2 footer-col">
                <h4>Industry Solutions</h4>
                <ul>
                    <?php foreach ((array)$industry_pages as $ind): ?>
                        <li><a href="<?= seo_esc(seo_industry_path($ind)) ?>"><?= seo_esc($ind['nav']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Free Tools -->
            <div class="col-6 col-md-4 col-lg-4 col-xl-2 footer-col">
                <h4>Free Tools</h4>
                <ul>
                    <li><a href="gst-calculator.php">GST Calculator</a></li>
                    <li><a href="invoice-generator.php">Invoice Generator</a></li>
                    <li><a href="qr-generator.php">QR Code Generator</a></li>
                    <li><a href="barcode-generator.php">Barcode Generator</a></li>
                    <li><a href="quotation-maker.php">Quotation Maker</a></li>
                    <li><a href="receipt-maker.php">Receipt Maker</a></li>
                    <li><a href="purchase-order.php">Purchase Order</a></li>
                    <li><a href="proforma-invoice.php">Proforma Invoice</a></li>
                    <li><a href="work-order.php">Work Order</a></li>
                    <li><a href="delivery-challan.php">Delivery Challan</a></li>
                    <li><a href="credit-note.php">Credit Note</a></li>
                    <li><a href="debit-note.php">Debit Note</a></li>
                    <li><a href="balance-sheet.php">Balance Sheet</a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div class="col-6 col-md-4 col-lg-4 col-xl-2 footer-col">
                <h4>Resources</h4>
                <ul>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Videos</a></li>
                    <li><a href="#">Guides</a></li>
                    <li><a href="#">Success Stories</a></li>
                    <li><a href="#">Use Cases</a></li>
                    <li><a href="#">GST Filing Guide</a></li>
                </ul>
            </div>

            <!-- Invoice Formats -->
            <!-- Only the template/guide pages live here. The credit note, debit note,
                 delivery challan, balance sheet and work order tools are listed once,
                 in Free Tools above, so the same link is not offered twice. -->
            <div class="col-6 col-md-4 col-lg-4 col-xl-2 footer-col">
                <h4>Invoice Formats</h4>
                <ul>
                    <li><a href="invoice-format.php">Invoice Format</a></li>
                    <li><a href="quotation-format.php">Quotation Format</a></li>
                    <li><a href="receipt-format.php">Receipt Format</a></li>
                    <li><a href="bill-book-format.php">Bill Book Format</a></li>
                    <li><a href="estimate-format.php">Estimate Format</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div class="col-6 col-md-4 col-lg-4 col-xl-2 footer-col">
                <h4>Company</h4>
                <ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="features.php">Features</a></li>
                    <li><a href="pricing.php">Pricing</a></li>
                    <li><a href="partner.php">Partner with Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="terms-conditions.php">Terms &amp; Conditions</a></li>
                    <li><a href="privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="refund-policy.php">Refund Policy</a></li>
                </ul>
            </div>

                </div>
            </div>
        </div>

        <div class="footer-divider"></div>

        <?php if (!empty($cities)): ?>
        <!-- SEO: the city cluster is linked from every page so no city page is
             orphaned. Driven by $cities in config.php, so it can never list a
             city that does not have pages. -->
        <div class="footer-city-hub">
            <h4>Popular in your city</h4>
            <div class="footer-city-cols">
                <?php
                $hubService = seo_find_service('pos-software') ?: seo_find_service($services[0]['slug']);
                foreach ($cities as $c):
                    ?>
                    <a href="<?= seo_esc(seo_city_path($hubService, $c)) ?>"><?= seo_esc($c['name']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="footer-divider"></div>
        <?php endif; ?>

        <div class="row g-3 justify-content-center text-center text-lg-start footer-contact-bar">
            <div class="col-12 col-md-6 col-lg-auto footer-contact-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <?= $contact['phone1'] ?>
            </div>
            <div class="col-12 col-md-6 col-lg-auto footer-contact-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <?= $contact['email'] ?>
            </div>
            <div class="col-12 col-md-12 col-lg-auto footer-contact-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <a href="https://maps.app.goo.gl/13ocCrHeQJNNF5Hg9218" target="_blank" class="footer-contact-link"><?= $contact['address'] ?></a>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 text-center text-sm-start">
            <p class="mb-0">&copy; <?= date('Y') ?> <?= BRAND_NAME ?>. All Rights Reserved.</p>
            <div class="footer-bottom-links d-flex flex-wrap gap-4 justify-content-center">
                <a href="terms-conditions.php">Terms</a>
                <a href="privacy-policy.php">Privacy</a>
                <a href="refund-policy.php">Refund</a>
            </div>
        </div>
    </div>
</footer>

<!-- JavaScript -->
<script>
    // FAQ Toggle
    function toggleFaq(el) {
        const item = el.parentElement;
        document.querySelectorAll('.faq-item').forEach(i => {
            if (i !== item) i.classList.remove('active');
        });
        item.classList.toggle('active');
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
                document.getElementById('navLinks')?.classList.remove('show');
                document.getElementById('hamburger')?.classList.remove('active');
            }
        });
    });

    // Download button handler
    function handleDownload(platform) {
        alert('Download for ' + platform.charAt(0).toUpperCase() + platform.slice(1) + ' coming soon!');
    }

    // Header scroll effect
    let lastScroll = 0;
    window.addEventListener('scroll', () => {
        const header = document.querySelector('header');
        const currentScroll = window.scrollY;
        if (currentScroll > 50) {
            header.style.boxShadow = '0 4px 25px rgba(23,105,224,0.08)';
            header.style.padding = '8px 0';
        } else {
            header.style.boxShadow = 'none';
            header.style.padding = '12px 0';
        }
        lastScroll = currentScroll;
    });

    // Intersection Observer for scroll animations
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.feature-card, .industry-card, .why-card, .testimonial-card, .pricing-card, .step-card, .solution-link-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });

    // Counter animation
    function animateCounters() {
        document.querySelectorAll('.stat-item h2, .hero-stat h3').forEach(counter => {
            counter.style.opacity = '1';
        });
    }

    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.stats-banner, .hero-stats').forEach(el => {
        statsObserver.observe(el);
    });

    // Close mobile dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nav-dropdown')) {
            document.querySelectorAll('.nav-dropdown').forEach(d => d.classList.remove('open'));
        }
    });
</script>

</body>
</html>
