<?php
require_once __DIR__ . '/config.php';
$page_title = 'Privacy Policy - ' . BRAND_NAME;
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
    <div class="section-container">
        <h1>Privacy <span class="highlight">Policy</span></h1>
        <p>Last updated: <?= date('d M Y') ?></p>
    </div>
</section>

<section class="section legal-page">
    <div class="section-container">
        <div class="legal-content">

            <div class="legal-block">
                <h2>1. Introduction</h2>
                <p>Welcome to <?= BRAND_NAME ?>. We are committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our software and services.</p>
            </div>

            <div class="legal-block">
                <h2>2. Information We Collect</h2>
                <h3>2.1 Personal Information</h3>
                <ul>
                    <li>Name, email address, phone number</li>
                    <li>Business name and GSTIN</li>
                    <li>Payment information (processed securely via Razorpay)</li>
                    <li>Account login credentials</li>
                </ul>

                <h3>2.2 Business Data</h3>
                <ul>
                    <li>Invoices, bills, and transaction records</li>
                    <li>Customer and supplier information</li>
                    <li>Product inventory and stock data</li>
                    <li>Financial reports and accounting data</li>
                </ul>

                <h3>2.3 Technical Information</h3>
                <ul>
                    <li>Device type and operating system</li>
                    <li>IP address and browser information</li>
                    <li>Usage logs and app analytics</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>3. How We Use Your Information</h2>
                <ul>
                    <li>To provide and maintain our services</li>
                    <li>To process transactions and send related information</li>
                    <li>To send administrative notifications (updates, security alerts)</li>
                    <li>To provide customer support</li>
                    <li>To improve our software and develop new features</li>
                    <li>To comply with legal obligations (GST, tax regulations)</li>
                    <li>To detect and prevent fraud or abuse</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>4. Data Storage & Security</h2>
                <p>Your data is stored securely on our servers with:</p>
                <ul>
                    <li>256-bit SSL encryption for data in transit</li>
                    <li>AES-256 encryption for data at rest</li>
                    <li>Regular automated backups</li>
                    <li>Multi-factor authentication options</li>
                    <li>SOC 2 compliant infrastructure</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>5. Data Sharing</h2>
                <p>We do NOT sell your data. We only share information with:</p>
                <ul>
                    <li><strong>Service Providers:</strong> Hosting, payment processing (Razorpay), analytics</li>
                    <li><strong>Legal Requirements:</strong> When required by law or government authorities</li>
                    <li><strong>Business Transfers:</strong> In case of merger or acquisition (with notice)</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>6. Your Rights</h2>
                <p>You have the right to:</p>
                <ul>
                    <li>Access your personal data</li>
                    <li>Correct inaccurate data</li>
                    <li>Request deletion of your data</li>
                    <li>Export your data in standard formats</li>
                    <li>Opt-out of marketing communications</li>
                    <li>Withdraw consent for data processing</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>7. Data Retention</h2>
                <p>We retain your data for as long as your account is active. After account deletion:</p>
                <ul>
                    <li>Active data is deleted within 30 days</li>
                    <li>Backup copies are purged within 90 days</li>
                    <li>Some data may be retained for legal compliance</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>8. Cookies & Tracking</h2>
                <p>We use cookies to:</p>
                <ul>
                    <li>Remember your login session</li>
                    <li>Improve user experience</li>
                    <li>Analyze app usage patterns</li>
                    <li>You can disable cookies in your browser settings</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>9. Children's Privacy</h2>
                <p><?= BRAND_NAME ?> is not intended for users under 18 years of age. We do not knowingly collect information from children.</p>
            </div>

            <div class="legal-block">
                <h2>10. Changes to This Policy</h2>
                <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "Last updated" date.</p>
            </div>

            <div class="legal-block">
                <h2>11. Contact Us</h2>
                <p>If you have any questions about this Privacy Policy, please contact us:</p>
                <ul>
                    <li>📧 Email: <?= $contact['email'] ?></li>
                    <li>📞 Phone: <?= $contact['phone1'] ?></li>
                    <li>📍 Address: <?= $contact['address'] ?></li>
                </ul>
            </div>

        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
