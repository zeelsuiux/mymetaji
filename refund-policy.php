<?php
require_once __DIR__ . '/config.php';
$page_title = 'Refund Policy - ' . BRAND_NAME;
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
    <div class="section-container">
        <h1>Refund <span class="highlight">Policy</span></h1>
        <p>Last updated: <?= date('d M Y') ?></p>
    </div>
</section>

<section class="section legal-page">
    <div class="section-container">
        <div class="legal-content">

            <div class="legal-block">
                <h2>1. Overview</h2>
                <p>At <?= BRAND_NAME ?>, we want you to be completely satisfied with our service. If you're not happy with your purchase, we offer refunds under the conditions outlined below.</p>
            </div>

            <div class="legal-block">
                <h2>2. Free Trial</h2>
                <p>The 15-day free trial requires no payment up front, so there is nothing to refund. Ending a trial before it finishes carries no charge.</p>
            </div>

            <div class="legal-block">
                <h2>3. Paid Plans (Starter, Business, Professional &amp; Enterprise)</h2>
                <h3>3.1 Eligibility for Refund</h3>
                <p>You may request a refund if:</p>
                <ul>
                    <li>You request a refund within <strong>7 days</strong> of your initial purchase</li>
                    <li>You have not used more than <strong>20%</strong> of the features included in your plan</li>
                    <li>The software has a critical bug that prevents you from using core features, and our support team cannot resolve it within 48 hours</li>
                </ul>

                <h3>3.2 Non-Refundable Situations</h3>
                <p>Refunds will NOT be issued for:</p>
                <ul>
                    <li>Requests made after 7 days of purchase</li>
                    <li>Annual subscription plans after 14 days</li>
                    <li>Accounts that have been used to generate more than 20 invoices during the billing period</li>
                    <li>Add-on services or custom integrations</li>
                    <li>Downgrade from a higher plan to a lower plan</li>
                    <li>Accounts terminated due to violation of Terms & Conditions</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>4. Subscription Cancellation</h2>
                <ul>
                    <li>You can cancel your subscription at any time from your account settings</li>
                    <li>Cancellation takes effect at the end of the current billing cycle</li>
                    <li>You will continue to have access until the billing period ends</li>
                    <li>No partial refunds for unused time within a billing cycle</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>5. Auto-Renewal</h2>
                <p>Subscriptions auto-renew at the end of each billing cycle. To avoid charges:</p>
                <ul>
                    <li>Cancel at least 24 hours before the renewal date</li>
                    <li>We will send a reminder email 7 days before renewal</li>
                    <li>If you forget to cancel, contact us within 48 hours of the charge for consideration</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>6. How to Request a Refund</h2>
                <p>To request a refund:</p>
                <ul>
                    <li>📧 Email us at: <?= $contact['email'] ?></li>
                    <li>📞 Call us at: <?= $contact['phone1'] ?></li>
                    <li>Include your account email, transaction ID, and reason for refund</li>
                    <li>Refund requests are processed within 5-7 business days</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>7. Refund Method</h2>
                <ul>
                    <li>Refunds are processed via the original payment method</li>
                    <li>Bank transfers may take 7-10 business days to reflect</li>
                    <li>UPI refunds typically process within 24-48 hours</li>
                    <li>Credit/debit card refunds may take one billing cycle</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>8. Trial Period</h2>
                <ul>
                    <li>Free trials are provided for 15 days</li>
                    <li>No charge is made during the trial period</li>
                    <li>If you don't cancel before the trial ends, you will be charged for the selected plan</li>
                    <li>You may cancel during the trial at no cost</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>9. Exceptions</h2>
                <p>In rare cases, we may offer exceptions to this policy at our discretion, such as:</p>
                <ul>
                    <li>Extended service outages exceeding 24 hours</li>
                    <li>Billing errors on our part</li>
                    <li>Duplicate charges</li>
                </ul>
            </div>

            <div class="legal-block">
                <h2>10. Contact Us</h2>
                <p>For refund requests or questions about this policy:</p>
                <ul>
                    <li>📧 Email: <?= $contact['email'] ?></li>
                    <li>📞 Phone: <?= $contact['phone1'] ?></li>
                    <li>🕐 Support Hours: <?= $contact['hours'] ?></li>
                    <li>📍 Address: <?= $contact['address'] ?></li>
                </ul>
            </div>

        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
