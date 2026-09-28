<?php
require_once __DIR__ . '/config.php';
$page_title = 'Contact Us - ' . BRAND_NAME;
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<!-- PAGE HERO -->
<section class="page-hero">
    <div class="section-container">
        <h1>Get in <span class="highlight">Touch</span></h1>
        <p>Have questions? Our support team is here to help you.</p>
    </div>
</section>

<!-- CONTACT SECTION -->
<section class="section contact-section" id="contact">
    <div class="section-container">
        <div class="contact-grid-full">
            <!-- Contact Info -->
            <div class="contact-info">
                <h3>Contact Information</h3>
                <p>Reach out to us through any of these channels. We're here to help!</p>

                <div class="contact-item">
                    <div class="contact-icon">📞</div>
                    <div>
                        <h4>Call Us</h4>
                        <p><?= $contact['phone1'] ?></p>
                        <?php if (!empty($contact['phone2'])): ?>
                        <p><?= $contact['phone2'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">💬</div>
                    <div>
                        <h4>WhatsApp</h4>
                        <p><?= $contact['whatsapp'] ?></p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">📧</div>
                    <div>
                        <h4>Email Us</h4>
                        <p><?= $contact['email'] ?></p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">📍</div>
                    <div>
                        <h4>Address</h4>
                        <p><?= $contact['address'] ?></p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">🕐</div>
                    <div>
                        <h4>Support Hours</h4>
                        <p><?= $contact['hours'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-wrapper">
                <h3>Send us a Message</h3>
                <form class="contact-form" onsubmit="handleContact(event)">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter your name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject" required>
                            <option value="">Select a subject</option>
                            <option value="general">General Inquiry</option>
                            <option value="support">Technical Support</option>
                            <option value="sales">Sales Question</option>
                            <option value="partnership">Partnership</option>
                            <option value="feedback">Feedback</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Write your message here..." required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Send Message →</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- MAP PLACEHOLDER -->
<section class="section map-section">
    <div class="section-container">
        <div class="map-placeholder">
            <div class="map-content">
                <span class="map-icon">📍</span>
                <h3>Our Location</h3>
                <p><?= $contact['address'] ?></p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="download-cta" id="download">
    <h2>Download <?= BRAND_NAME ?> Now</h2>
    <p>Start billing in minutes. It's free!</p>
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

<script>
function handleContact(e) {
    e.preventDefault();
    alert('Thank you for your message! We will get back to you shortly.');
    e.target.reset();
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
