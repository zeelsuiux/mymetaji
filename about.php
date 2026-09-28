<?php
require_once __DIR__ . '/config.php';
$page_title = 'About Us - ' . BRAND_NAME . ' | GST Billing Software for Indian Businesses';

$glance = [
    'Founded'     => '2024',
    'Head office' => 'Surat, Gujarat',
    'Built for'   => 'Indian MSMEs & retailers',
    'Languages'   => '9+ Indian languages',
    'Support'     => '24/7, all week',
    'Platforms'   => 'Android · iOS · Windows · Mac',
];

$principles = [
    [
        'icon'  => '🎯',
        'tag'   => 'Our Mission',
        'title' => 'Make billing effortless',
        'desc'  => 'Professional-grade billing and accounting should be affordable and easy for every Indian business — not just the big ones. No bloated complexity, no contracts, no call-centre maze.',
    ],
    [
        'icon'  => '🔭',
        'tag'   => 'Our Vision',
        'title' => 'The default billing platform for Indian commerce',
        'desc'  => 'From a single shop counter in a small town to a multi-outlet chain running across states — every business should run on software that understands GST, local languages and the way Indian trade actually works.',
    ],
];

$values = [
    ['icon' => '🎯', 'title' => 'Simplicity First',        'desc' => 'If a shop owner needs training to raise a bill, the feature is wrong. We design for clarity, not for feature checklists.'],
    ['icon' => '🇮🇳', 'title' => 'Built for India',         'desc' => 'GST rules, HSN/SAC codes, e-invoicing and regional business practices are native here — not bolted on afterwards.'],
    ['icon' => '🔒', 'title' => 'Trust & Security',        'desc' => 'Bank-level encryption, automatic backups and strict data isolation. Your books are your business.'],
    ['icon' => '📶', 'title' => 'Offline First',           'desc' => 'Internet drops in a basement market or a village shop all the time. Keep billing, and we sync the moment you are back.'],
    ['icon' => '🚀', 'title' => 'Always Improving',        'desc' => 'Shipped from real feedback, not focus groups. Every release is measured by whether it saves a business time.'],
    ['icon' => '🗣️', 'title' => 'Support in Your Language','desc' => 'Help in 9+ Indian languages, seven days a week — because explaining GST in English is not support.'],
];

$journey = [
    ['icon' => '🌱', 'title' => 'Where we began',   'desc' => '2024, Surat. A small team and one shared frustration: billing software that a small shop could neither afford nor understand.'],
    ['icon' => '🌳', 'title' => 'Where we are',     'desc' => '50,000+ businesses billing daily in 9+ languages, with GST returns that pass the first time they are filed.'],
    ['icon' => '🚀', 'title' => 'Where we are going','desc' => 'Deeper inventory, faster reconciliation, and software that works just as well offline as it does online.'],
];

$reasons = [
    ['icon' => '🇮🇳', 'title' => 'Built for India',     'desc' => 'Designed specifically for Indian businesses with GST compliance, local languages, and regional support.'],
    ['icon' => '💰', 'title' => 'Affordable Pricing',  'desc' => 'Start for free and upgrade only when you need more. No hidden charges, no surprises.'],
    ['icon' => '📱', 'title' => 'Multi-Device',       'desc' => 'Works on Android, iOS, Windows, and Mac. Manage your business from anywhere.'],
    ['icon' => '🔒', 'title' => 'Data Security',       'desc' => 'Bank-level encryption and automatic backups keep your business data safe always.'],
    ['icon' => '📶', 'title' => 'Offline First',       'desc' => 'Bills even without internet. Data syncs automatically when you\'re back online.'],
    ['icon' => '🤝', 'title' => 'Dedicated Support',   'desc' => 'Our support team is available in 9+ Indian languages to help you anytime.'],
];
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<!-- PAGE HERO -->
<section class="page-hero ab-hero">
    <div class="section-container">
        <span class="ab-eyebrow">About <?= BRAND_NAME ?></span>
        <h1>Billing that <span class="highlight">just works</span> for every Indian business</h1>
        <p class="page-lead"><?= BRAND_NAME ?> is a GST-ready billing and accounting platform built in India, by people who have spent time sitting with the shop owners who use it every day.</p>
        <div class="ab-hero-cta">
            <a href="features.php" class="ab-btn ab-btn-primary">Explore features <span aria-hidden="true">→</span></a>
            <a href="contact.php" class="ab-btn ab-btn-ghost">Talk to us</a>
        </div>
        <div class="ab-statstrip">
            <?php foreach ($stats as $s): ?>
                <div class="ab-stat">
                    <b><?= htmlspecialchars($s['value']) ?></b>
                    <span><?= htmlspecialchars($s['label']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- MISSION & STORY -->
<section class="section ab-alt" id="about">
    <div class="section-container">
        <div class="ab-story">
            <div class="ab-prose">
                <h2>Our Mission</h2>
                <p><strong><?= BRAND_NAME ?> was founded with a single goal — make billing effortless for every Indian business.</strong> From a single-counter kirana store to a multi-outlet retail chain, every business deserves access to professional-grade billing and accounting without the complexity or the high cost.</p>
                <p>Today <strong><?= htmlspecialchars($stats[0]['value']) ?> businesses</strong> across India trust <?= BRAND_NAME ?> for their daily billing, inventory management and GST compliance. Our software is built in India, for Indian businesses — supporting local tax rules, multiple languages and the regional practices trade actually runs on.</p>

                <h3>Our Story</h3>
                <p>We started in 2024 out of frustration with the billing software available to small businesses: either far too complicated to learn, or far too expensive to justify. Having worked closely with MSMEs across India, we knew exactly what a shop owner needs from this software — and, just as importantly, what they can live without.</p>
                <p>So we built <?= BRAND_NAME ?> around three things: <strong>simplicity, reliability and honest pricing.</strong> No feature ships until it makes a real shopkeeper's day easier. That is still the standard we work to.</p>
            </div>

            <aside class="ab-glance">
                <div class="ab-glance-hd">
                    <h3><?= BRAND_NAME ?> at a glance</h3>
                    <p>The short version of who we are</p>
                </div>
                <dl class="ab-glance-body">
                    <?php foreach ($glance as $k => $v): ?>
                        <div class="ab-glance-row">
                            <dt><?= htmlspecialchars($k) ?></dt>
                            <dd><?= htmlspecialchars($v) ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
                <div class="ab-glance-ft">
                    <a href="mailto:<?= htmlspecialchars($contact['email']) ?>"><?= htmlspecialchars($contact['email']) ?></a>
                    <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $contact['phone1'])) ?>"><?= htmlspecialchars($contact['phone1']) ?></a>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- MISSION & VISION -->
<section class="section">
    <div class="section-container">
        <div class="section-title">
            <h2>What we are <span class="highlight">building towards</span></h2>
            <p>Two ideas decide every product decision we make.</p>
        </div>
        <div class="ab-mv">
            <?php foreach ($principles as $p): ?>
                <div class="ab-card">
                    <div class="ab-icon" aria-hidden="true"><?= $p['icon'] ?></div>
                    <span class="ab-tag"><?= htmlspecialchars($p['tag']) ?></span>
                    <h3><?= htmlspecialchars($p['title']) ?></h3>
                    <p><?= htmlspecialchars($p['desc']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- VALUES -->
<section class="section ab-alt">
    <div class="section-container">
        <div class="section-title">
            <h2>Our <span class="highlight">Values</span></h2>
            <p>Six things we will not trade away, whatever the pressure.</p>
        </div>
        <div class="ab-values">
            <?php foreach ($values as $v): ?>
                <div class="ab-value">
                    <div class="ab-icon" aria-hidden="true"><?= $v['icon'] ?></div>
                    <h4><?= htmlspecialchars($v['title']) ?></h4>
                    <p><?= htmlspecialchars($v['desc']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- JOURNEY -->
<section class="section">
    <div class="section-container">
        <div class="section-title">
            <h2>The <span class="highlight">Journey</span></h2>
            <p>From one frustrated founder to a platform businesses open every single day.</p>
        </div>
        <div class="ab-journey">
            <?php foreach ($journey as $j): ?>
                <div class="ab-step">
                    <div class="ab-step-dot" aria-hidden="true"><?= $j['icon'] ?></div>
                    <h4><?= htmlspecialchars($j['title']) ?></h4>
                    <p><?= htmlspecialchars($j['desc']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- WHY CHOOSE -->
<section class="section ab-alt why-choose">
    <div class="section-container">
        <div class="section-title">
            <h2>Why Choose <span class="highlight"><?= BRAND_NAME ?></span>?</h2>
        </div>
        <div class="why-grid">
            <?php foreach ($reasons as $r): ?>
                <div class="why-card">
                    <h3><?= $r['icon'] ?> <?= htmlspecialchars($r['title']) ?></h3>
                    <p><?= htmlspecialchars($r['desc']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- REACH US -->
<section class="section ab-alt">
    <div class="section-container">
        <div class="section-title">
            <h2>Come <span class="highlight">say hello</span></h2>
            <p>Questions, feedback or a feature request — we read everything.</p>
        </div>
        <div class="ab-contact">
            <div class="ab-contact-card">
                <div class="ab-icon" aria-hidden="true">📞</div>
                <h4>Call us</h4>
                <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $contact['phone1'])) ?>"><?= htmlspecialchars($contact['phone1']) ?></a>
                <?php if (!empty($contact['phone2'])): ?>
                <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $contact['phone2'])) ?>"><?= htmlspecialchars($contact['phone2']) ?></a>
                <?php endif; ?>
            </div>
            <div class="ab-contact-card">
                <div class="ab-icon" aria-hidden="true">✉️</div>
                <h4>Email us</h4>
                <a href="mailto:<?= htmlspecialchars($contact['email']) ?>"><?= htmlspecialchars($contact['email']) ?></a>
                <p><?= htmlspecialchars($contact['hours']) ?></p>
            </div>
            <div class="ab-contact-card">
                <div class="ab-icon" aria-hidden="true">📍</div>
                <h4>Visit us</h4>
                <p><?= htmlspecialchars($contact['address']) ?></p>
            </div>
        </div>
    </div>
</section>

<!-- WHAT CUSTOMERS SAY -->
<section class="section">
    <div class="section-container">
        <div class="section-title">
            <h2>In their <span class="highlight">own words</span></h2>
            <p>The reason we keep building.</p>
        </div>
        <div class="testimonial-grid">
            <?php foreach (array_slice($testimonials, 0, 3) as $t): ?>
                <div class="testimonial-card">
                    <div class="stars" aria-label="<?= (int) $t['stars'] ?> out of 5">
                        <?= str_repeat('★', (int) $t['stars']) ?>
                    </div>
                    <p>&ldquo;<?= htmlspecialchars($t['text']) ?>&rdquo;</p>
                    <div class="testimonial-author">
                        <div class="author-avatar"><?= htmlspecialchars($t['initial']) ?></div>
                        <div class="author-info">
                            <h4><?= htmlspecialchars($t['name']) ?></h4>
                            <p><?= htmlspecialchars($t['role']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="download-cta" id="download">
    <h2>Join <?= BRAND_NAME ?> Today</h2>
    <p>Start your journey with us. Free to start, easy to grow.</p>
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

<script>
(function () {
    // NB: .testimonial-card / .why-card are already revealed by the script in
    // includes/footer.php - do not double-register them here.
    var targets = document.querySelectorAll(
        '.ab-stat, .ab-value, .ab-card, .ab-step, .ab-contact-card, .ab-glance'
    );
    if (!targets.length) return;
    if (!('IntersectionObserver' in window) ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        targets.forEach(function (el) { el.classList.add('ab-in'); });
        return;
    }
    targets.forEach(function (el) { el.classList.add('ab-reveal'); });
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e, i) {
            if (!e.isIntersecting) return;
            var el = e.target;
            setTimeout(function () { el.classList.add('ab-in'); }, i * 70);
            io.unobserve(el);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    targets.forEach(function (el) { io.observe(el); });
})();
</script>
