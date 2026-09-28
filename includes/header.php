<?php
/**
 * My Metaji - Header Section
 */
require_once __DIR__ . '/seo.php';

$currentPage = basename($_SERVER['PHP_SELF'], '.php');

/**
 * The Sign In button points at the panel login, so it should not invite a second
 * sign-in while the visitor is already signed in. The session cookie is issued
 * with path=/, which means a shop or Master login is technically visible from the
 * public pages - but only if the session is actually loaded, and the public pages
 * never loaded one. Opening a session for every anonymous visitor would write a
 * session file per page view for no reason, so a session is only started when the
 * browser already carries one, which is precisely the signed-in case.
 *
 * Starting the session and detecting the login are deliberately two separate
 * steps. On trial-signup.php and checkout.php the session is already active by the
 * time this header runs, because includes/payment.php opened it to mint a payment
 * CSRF token. Folding the two into one "start it if it is not running" test left
 * the detection skipped on exactly those pages, so a signed-in Master still saw a
 * live Sign In link there.
 */
$viewerSignedIn = false;
$viewerPanelUrl = '';
if (session_status() !== PHP_SESSION_ACTIVE && !empty($_COOKIE[session_name()])) {
    @session_start();
}
if (session_status() === PHP_SESSION_ACTIVE) {
    if (!empty($_SESSION['master_admin'])) {
        $viewerSignedIn = true;
        $viewerPanelUrl = 'admin/master/payments.php';
    } elseif (!empty($_SESSION['team_member']) || !empty($_SESSION['subadmin'])) {
        $viewerSignedIn = true;
        $viewerPanelUrl = 'admin/index.php';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php seo_head(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/m-logo.png">
</head>
<body>

<!-- HEADER -->
<header>
    <div class="header-container">
        <!-- Logo -->
        <a href="index.php" class="logo-link">
            <img src="assets/images/mymetaji-logo.png" alt="<?= BRAND_NAME ?>" class="header-logo">
        </a>

        <!-- Desktop Nav -->
        <nav class="nav-links" id="navLinks">
            <div class="nav-dropdown">
                <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event, this)">Solutions ▾</a>
                <div class="dropdown-menu">
                    <a href="billing-software.php">Billing Software</a>
                    <a href="accounting-software.php">Accounting Software</a>
                    <a href="inventory-software.php">Inventory Software</a>
                    <a href="invoicing-software.php">Invoicing Software</a>
                    <a href="pos-software.php">POS Software</a>
                    <a href="e-invoice.php">E-Invoice</a>
                </div>
            </div>
            <div class="nav-dropdown">
                <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event, this)">Industries ▾</a>
                <div class="dropdown-menu">
                    <?php foreach ((array)$industry_pages as $ind): ?>
                        <a href="<?= seo_esc(seo_industry_path($ind)) ?>"><?= seo_esc($ind['nav']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if (!empty($cities)): ?>
            <div class="nav-dropdown nav-cities">
                <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event, this)">Cities ▾</a>
                <div class="dropdown-menu">
                    <div class="nav-cities-grid">
                        <?php
                        $navService = seo_find_service('pos-software') ?: seo_find_service($services[0]['slug']);
                        foreach ($cities as $c):
                        ?>
                            <a href="<?= seo_esc(seo_city_path($navService, $c)) ?>">
                                <span><?= seo_esc($c['name']) ?></span>
                                <em><?= seo_esc($c['state']) ?></em>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="pos-software.php" class="nav-cities-all">See how the software works</a>
                </div>
            </div>
            <?php endif; ?>
            <a href="features.php" class="<?= $currentPage === 'features' ? 'active' : '' ?>">Features</a>
            <a href="pricing.php" class="<?= $currentPage === 'pricing' ? 'active' : '' ?>">Pricing</a>
            <a href="about.php" class="<?= $currentPage === 'about' ? 'active' : '' ?>">About</a>
            <a href="contact.php" class="<?= $currentPage === 'contact' ? 'active' : '' ?>">Contact</a>
                <?php if ($viewerSignedIn): ?>
                    <span class="header-signin is-disabled" aria-disabled="true" title="You are already signed in">Sign In</span>
                <?php else: ?>
                    <a href="admin/login.php" class="header-signin">Sign In</a>
                <?php endif; ?>
            <a href="trial-signup.php" class="header-btn">Start Free Trial</a>
        </nav>

        <!-- Mobile: CTA + Hamburger -->
        <div class="mobile-actions">
                <?php if ($viewerSignedIn): ?>
                    <span class="mobile-signin-btn is-disabled" aria-disabled="true" title="You are already signed in">Sign In</span>
                <?php else: ?>
                    <a href="admin/login.php" class="mobile-signin-btn">Sign In</a>
                <?php endif; ?>
            <a href="trial-signup.php" class="mobile-cta-btn">Start Free Trial</a>
            <div class="hamburger" id="hamburger" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileOverlay" onclick="closeMenu()"></div>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-header">
        <a href="index.php" class="mobile-menu-logo">
            <img src="assets/images/m-logo.png" alt="<?= BRAND_NAME ?>">
        </a>
        <div class="mobile-menu-actions">
            <a href="trial-signup.php" class="mobile-cta-btn" onclick="closeMenu()">Sign Up Free</a>
            <button class="mobile-close-btn" onclick="closeMenu()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
    </div>
    <nav class="mobile-menu-nav">
        <div class="mobile-nav-item">
            <a href="#" class="mobile-nav-link" onclick="toggleMobileDropdown(event, this)">Accounting & Billing <span class="mobile-arrow">▾</span></a>
            <div class="mobile-submenu">
                <a href="billing-software.php">Billing Software</a>
                <a href="accounting-software.php">Accounting Software</a>
                <a href="inventory-software.php">Inventory Software</a>
                <a href="invoicing-software.php">Invoicing Software</a>
                <a href="pos-software.php">POS Software</a>
                <a href="e-invoice.php">E-Invoice</a>
            </div>
        </div>
        <div class="mobile-nav-item">
            <a href="#" class="mobile-nav-link" onclick="toggleMobileDropdown(event, this)">Industry Solutions <span class="mobile-arrow">▾</span></a>
            <div class="mobile-submenu">
                <?php foreach ((array)$industry_pages as $ind): ?>
                    <a href="<?= seo_esc(seo_industry_path($ind)) ?>" onclick="closeMenu()"><?= seo_esc($ind['nav']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if (!empty($cities)): ?>
        <div class="mobile-nav-item">
            <a href="#" class="mobile-nav-link" onclick="toggleMobileDropdown(event, this)">Cities <span class="mobile-arrow">▾</span></a>
            <div class="mobile-submenu">
                <?php
                $mNavService = seo_find_service('pos-software') ?: seo_find_service($services[0]['slug']);
                foreach ($cities as $c):
                    ?>
                    <a href="<?= seo_esc(seo_city_path($mNavService, $c)) ?>" onclick="closeMenu()"><?= seo_esc($c['name']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <div class="mobile-nav-item">
            <a href="features.php" class="mobile-nav-link" onclick="closeMenu()">Features</a>
        </div>
        <div class="mobile-nav-item">
            <a href="pricing.php" class="mobile-nav-link" onclick="closeMenu()">Pricing</a>
        </div>
        <div class="mobile-nav-item">
            <a href="about.php" class="mobile-nav-link" onclick="closeMenu()">About</a>
        </div>
        <div class="mobile-nav-item">
            <a href="contact.php" class="mobile-nav-link" onclick="closeMenu()">Contact</a>
        </div>
        <div class="mobile-nav-item">
                <?php if ($viewerSignedIn): ?>
                    <span class="mobile-nav-link mobile-signin-link is-disabled" aria-disabled="true" title="You are already signed in">Sign In</span>
                <?php else: ?>
                    <a href="admin/login.php" class="mobile-nav-link mobile-signin-link" onclick="closeMenu()">Sign In</a>
                <?php endif; ?>
        </div>
        <div class="mobile-nav-item">
            <a href="gst-calculator.php" class="mobile-nav-link" onclick="closeMenu()">GST Calculator</a>
        </div>
        <div class="mobile-nav-item">
            <a href="qr-generator.php" class="mobile-nav-link" onclick="closeMenu()">QR Generator</a>
        </div>
        <div class="mobile-nav-item">
            <a href="barcode-generator.php" class="mobile-nav-link" onclick="closeMenu()">Barcode Generator</a>
        </div>
        <div class="mobile-nav-item">
            <a href="invoice-generator.php" class="mobile-nav-link" onclick="closeMenu()">Invoice Generator</a>
        </div>
        <div class="mobile-nav-item">
            <a href="quotation-maker.php" class="mobile-nav-link" onclick="closeMenu()">Quotation Maker</a>
        </div>
        <div class="mobile-nav-item">
            <a href="purchase-order.php" class="mobile-nav-link" onclick="closeMenu()">Purchase Order</a>
        </div>
        <div class="mobile-nav-item">
            <a href="proforma-invoice.php" class="mobile-nav-link" onclick="closeMenu()">Proforma Invoice</a>
        </div>
        <div class="mobile-nav-item">
            <a href="receipt-maker.php" class="mobile-nav-link" onclick="closeMenu()">Receipt Maker</a>
        </div>
        <div class="mobile-nav-item">
            <a href="#" class="mobile-nav-link" onclick="toggleMobileDropdown(event, this)">Document Formats <span class="mobile-arrow">▾</span></a>
            <div class="mobile-submenu">
                <a href="invoice-format.php">Invoice Format</a>
                <a href="quotation-format.php">Quotation Format</a>
                <a href="estimate-format.php">Estimate Format</a>
                <a href="receipt-format.php">Receipt Format</a>
                <a href="bill-book-format.php">Bill Book Format</a>
                <a href="credit-note.php">Credit Note</a>
                <a href="debit-note.php">Debit Note</a>
                <a href="delivery-challan.php">Delivery Challan</a>
                <a href="balance-sheet.php">Balance Sheet</a>
                <a href="work-order.php">Work Order</a>
            </div>
        </div>
    </nav>
</div>

<script>
// Mobile Menu Toggle
function toggleMenu() {
    const menu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('mobileOverlay');
    const hamburger = document.getElementById('hamburger');
    menu.classList.toggle('open');
    overlay.classList.toggle('open');
    hamburger.classList.toggle('active');
    document.body.style.overflow = menu.classList.contains('open') ? 'hidden' : '';
}

function closeMenu() {
    const menu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('mobileOverlay');
    const hamburger = document.getElementById('hamburger');
    menu.classList.remove('open');
    overlay.classList.remove('open');
    hamburger.classList.remove('active');
    document.body.style.overflow = '';
}

// Mobile Dropdown Toggle
function toggleMobileDropdown(e, el) {
    e.preventDefault();
    const item = el.closest('.mobile-nav-item');
    item.classList.toggle('open');
}

// Desktop Dropdown Toggle
function toggleDropdown(e, el) {
    e.preventDefault();
    e.stopPropagation();
    const dropdown = el.closest('.nav-dropdown');
    document.querySelectorAll('.nav-dropdown').forEach(d => {
        if (d !== dropdown) d.classList.remove('open');
    });
    dropdown.classList.toggle('open');
}

// Desktop hover
function checkDesktop() {
    if (window.innerWidth > 768) {
        document.querySelectorAll('.nav-dropdown').forEach(dropdown => {
            dropdown.onmouseenter = () => dropdown.classList.add('open');
            dropdown.onmouseleave = () => dropdown.classList.remove('open');
        });
    } else {
        document.querySelectorAll('.nav-dropdown').forEach(dropdown => {
            dropdown.onmouseenter = null;
            dropdown.onmouseleave = null;
        });
    }
}
checkDesktop();
window.addEventListener('resize', checkDesktop);

// Close dropdowns outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('.nav-dropdown')) {
        document.querySelectorAll('.nav-dropdown').forEach(d => d.classList.remove('open'));
    }
});
</script>
