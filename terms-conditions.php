<?php
require_once __DIR__ . '/config.php';
$page_title = 'Terms & Conditions - ' . BRAND_NAME;
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
    <div class="section-container">
        <h1>Terms & <span class="highlight">Conditions</span></h1>
        <p>Last updated: <?= date('d M Y') ?></p>
    </div>
</section>

<section class="section legal-page">
    <div class="section-container">
        <div class="legal-content">

            <div class="legal-block">
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing and using <?= BRAND_NAME ?> ("the Software"), you agree to be bound by these Terms and Conditions. If you do not agree with any part of these terms, you may not use our services.</p>
            </div>

            <div class="legal-block">
                <h2>2. Description of Service</h2>
                <p><?= BRAND_NAME ?> is a GST billing, accounting, and inventory management software designed for small businesses in India. Our services include:</p>
                <ul>
                    <li>GST-compliant invoicing and billing</li>
                    <li>Inventory management and stock tracking</li>
                    <li>Accounting and financial reports</li>
                    <li>E-invoice and e-way bill generation</li>
                    <li>Multi-device synchronization</li>
                    <li>WhatsApp integration for bill sharing</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>3. User Accounts</h2>
                <p>To use <?= BRAND_NAME ?>, you must create an account. You are responsible for:</p>
                <ul>
                    <li>Maintaining the confidentiality of your login credentials</li>
                    <li>All activities that occur under your account</li>
                    <li>Notifying us immediately of any unauthorized use</li>
                    <li>Ensuring your account information is accurate and up-to-date</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>4. Subscription Plans</h2>
                <p><?= BRAND_NAME ?> offers Free, Pro, and Enterprise plans. By subscribing to a paid plan:</p>
                <ul>
                    <li>You agree to pay all applicable fees</li>
                    <li>Subscriptions renew automatically unless cancelled</li>
                    <li>Refunds are subject to our Refund Policy</li>
                    <li>We reserve the right to change pricing with 30 days notice</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>5. Data and Privacy</h2>
                <p>Your business data belongs to you. We:</p>
                <ul>
                    <li>Store your data securely with encryption</li>
                    <li>Never sell or share your data with third parties</li>
                    <li>Allow you to export your data at any time</li>
                    <li>Delete your data upon account closure request</li>
                </ul>
                <p>Please review our <a href="privacy-policy.php">Privacy Policy</a> for detailed information.</p>
            </div>

            <div class="legal-block">
                <h2>6. Acceptable Use</h2>
                <p>You agree not to:</p>
                <ul>
                    <li>Use the software for any illegal purpose</li>
                    <li>Attempt to gain unauthorized access to our systems</li>
                    <li>Reverse engineer or decompile the software</li>
                    <li>Share your account credentials with others</li>
                    <li>Use the software to harm or defraud others</li>
                    <li>Overload or disrupt our servers</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>7. Intellectual Property</h2>
                <p>All content, features, and functionality of <?= BRAND_NAME ?> are owned by us and are protected by international copyright, trademark, and other intellectual property laws.</p>
            </div>

            <div class="legal-block">
                <h2>8. Limitation of Liability</h2>
                <p><?= BRAND_NAME ?> shall not be liable for:</p>
                <ul>
                    <li>Any indirect, incidental, or consequential damages</li>
                    <li>Loss of data due to circumstances beyond our control</li>
                    <li>Errors in tax calculations — users must verify GST compliance</li>
                    <li>Service interruptions due to maintenance or force majeure</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>9. Termination</h2>
                <p>We may terminate or suspend your account if you violate these terms. Upon termination:</p>
                <ul>
                    <li>Your right to use the software ceases immediately</li>
                    <li>We will retain your data for 30 days for backup purposes</li>
                    <li>You may request data export before termination</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>10. Changes to Terms</h2>
                <p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting. Your continued use of the software constitutes acceptance of the modified terms.</p>
            </div>

            <div class="legal-block">
                <h2>11. Governing Law</h2>
                <p>These terms shall be governed by and construed in accordance with the laws of India. Any disputes shall be subject to the exclusive jurisdiction of courts in Bengaluru, Karnataka.</p>
            </div>

            <div class="legal-block">
                <h2>12. Contact Us</h2>
                <p>For any questions about these Terms & Conditions, please contact us:</p>
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
