<?php
require_once __DIR__ . '/config.php';
$page_title = 'Become a Partner — My Metaji';

$success = false;
$errors = [];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name === '') $errors[] = 'Name is required.';
    if ($phone === '' || strlen($phone) < 10) $errors[] = 'Valid phone number is required.';
    if ($city === '') $errors[] = 'City is required.';
    if ($state === '') $errors[] = 'Please select a state.';
    if ($age === '' || !ctype_digit($age) || (int)$age < 18 || (int)$age > 100) $errors[] = 'Enter a valid age (18-100).';
    if ($address === '') $errors[] = 'Full address is required.';

    if (!$errors) {
        $dbDir = __DIR__ . '/admin/master/database';
        if (!is_dir($dbDir)) mkdir($dbDir, 0755, true);
        $file = $dbDir . '/partners.json';
        if (!file_exists($file)) file_put_contents($file, "[]\n", LOCK_EX);
        $partners = json_decode(file_get_contents($file), true) ?: [];
        $newId = empty($partners) ? 1 : max(array_column($partners, 'id')) + 1;
        $partners[] = [
            'id' => $newId, 'name' => $name, 'phone' => $phone, 'city' => $city,
            'state' => $state, 'age' => (int)$age, 'address' => $address,
            'status' => 'New', 'created_at' => date('Y-m-d H:i:s'),
        ];
        file_put_contents($file, json_encode($partners, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
        $success = true;
    }
}

$indianStates = [
    'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh',
    'Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand',
    'Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur',
    'Meghalaya','Mizoram','Nagaland','Odisha','Punjab',
    'Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura',
    'Uttar Pradesh','Uttarakhand','West Bengal',
    'Andaman & Nicobar','Chandigarh','Dadra & Nagar Haveli','Delhi',
    'Jammu & Kashmir','Ladakh','Lakshadweep','Puducherry',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become a Partner — My Metaji</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/m-logo.png">
    <style>
        .p-section{padding:80px 0}
        .p-wrap{max-width:1100px;margin:0 auto;padding:0 24px}
        .p-title{text-align:center;margin-bottom:48px}
        .p-title h2{font-size:42px;font-weight:900;color:#0f172a;margin:0 0 12px}
        .p-title p{font-size:18px;color:#64748b;margin:0 auto;max-width:600px;line-height:1.7}

        .p-hero{background:linear-gradient(135deg,#0f172a,#1e40af 50%,#3b82f6);color:#fff;padding:100px 0 80px;text-align:center}
        .p-hero h1{font-size:58px;font-weight:900;margin:0 0 20px;line-height:1.08}
        .p-hero h1 em{font-style:normal;color:#93c5fd}
        .p-hero>p{font-size:21px;color:#bfdbfe;margin:0 auto 40px;max-width:620px;line-height:1.7}
        .p-hero-stats{display:flex;justify-content:center;gap:60px;margin-top:48px}
        .p-hero-stat strong{display:block;font-size:40px;font-weight:900}
        .p-hero-stat span{font-size:15px;color:#93c5fd;display:block;margin-top:4px}
        .p-btn{background:#fff;color:#1e40af;padding:18px 48px;border-radius:14px;font-size:19px;font-weight:800;text-decoration:none;display:inline-block;border:none;cursor:pointer;box-shadow:0 6px 24px rgba(0,0,0,0.2);transition:all .3s}
        .p-btn:hover{transform:translateY(-3px);box-shadow:0 12px 36px rgba(0,0,0,0.3)}

        .p-grid4{display:grid;grid-template-columns:repeat(4,1fr);gap:24px}
        .p-card{background:#fff;border:2px solid #e2e8f0;border-radius:20px;padding:32px 24px;text-align:center;transition:all .3s}
        .p-card:hover{transform:translateY(-6px);box-shadow:0 20px 40px rgba(0,0,0,0.08);border-color:#2563eb}
        .p-card-icon{width:72px;height:72px;border-radius:18px;background:#eff6ff;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;font-size:32px}
        .p-card h3{font-size:20px;font-weight:800;color:#0f172a;margin:0 0 10px}
        .p-card p{font-size:16px;color:#64748b;margin:0;line-height:1.6}

        .p-steps{display:grid;grid-template-columns:repeat(3,1fr);gap:40px}
        .p-step{text-align:center}
        .p-step-num{width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#3b82f6);color:#fff;display:flex;align-items:center;justify-content:center;font-size:30px;font-weight:900;margin:0 auto 20px;box-shadow:0 8px 24px rgba(37,99,235,0.35)}
        .p-step h3{font-size:22px;font-weight:800;color:#0f172a;margin:0 0 10px}
        .p-step p{font-size:17px;color:#64748b;margin:0 auto;line-height:1.7;max-width:300px}

        .p-grid3{display:grid;grid-template-columns:repeat(3,1fr);gap:28px}
        .p-testi{background:#fff;border:2px solid #e2e8f0;border-radius:20px;padding:32px;transition:all .3s}
        .p-testi:hover{box-shadow:0 12px 32px rgba(0,0,0,0.06);border-color:#2563eb}
        .p-testi-stars{color:#f59e0b;font-size:22px;margin-bottom:16px;letter-spacing:3px}
        .p-testi blockquote{font-size:17px;color:#334155;line-height:1.7;margin:0 0 20px;font-style:italic}
        .p-testi-author{display:flex;align-items:center;gap:14px}
        .p-testi-avatar{width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#3b82f6);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:20px;flex-shrink:0}
        .p-testi-name{font-size:17px;font-weight:700;color:#0f172a}
        .p-testi-role{font-size:14px;color:#64748b;margin-top:2px}

        .p-faq{max-width:800px;margin:0 auto}
        .p-faq-item{border:2px solid #e2e8f0;border-radius:16px;margin-bottom:14px;background:#fff;overflow:hidden}
        .p-faq-q{padding:22px 28px;font-size:18px;font-weight:700;color:#0f172a;cursor:pointer;display:flex;justify-content:space-between;align-items:center;transition:background .2s}
        .p-faq-q:hover{background:#f8fafc}
        .p-faq-q::after{content:'+';font-size:28px;color:#94a3b8;font-weight:300}
        .p-faq-item.open .p-faq-q::after{content:'−'}
        .p-faq-a{padding:0 28px;max-height:0;overflow:hidden;transition:all .3s;font-size:17px;color:#475569;line-height:1.7}
        .p-faq-item.open .p-faq-a{padding:0 28px 22px;max-height:400px}

        .p-form-section{background:linear-gradient(135deg,#eff6ff,#e0ecff 50%,#f0f7ff);padding:80px 0}
        .p-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:start}
        .p-form-left h2{font-size:40px;font-weight:900;color:#0f172a;margin:0 0 16px;line-height:1.15}
        .p-form-left h2 em{font-style:normal;color:#2563eb}
        .p-form-left>p{font-size:18px;color:#64748b;margin:0 0 32px;line-height:1.7}
        .p-form-feats{display:flex;flex-direction:column;gap:20px}
        .p-form-feat{display:flex;align-items:flex-start;gap:14px}
        .p-form-feat-icon{width:44px;height:44px;border-radius:12px;background:#fff;border:2px solid #e2e8f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:22px;font-weight:900;color:#2563eb}
        .p-form-feat h4{margin:0;font-size:17px;font-weight:700;color:#0f172a}
        .p-form-feat p{margin:4px 0 0;font-size:15px;color:#64748b;line-height:1.5}

        .p-form-card{background:#fff;border-radius:24px;box-shadow:0 8px 40px rgba(0,0,0,0.08);padding:40px 36px;border:2px solid #e2e8f0}
        .p-form-card h3{font-size:26px;font-weight:800;color:#0f172a;margin:0 0 28px;text-align:center}
        .p-fg{margin-bottom:20px}
        .p-fg-row{display:grid;grid-template-columns:1fr 1fr;gap:0 16px}
        .p-fg label{display:block;font-size:16px;font-weight:700;color:#334155;margin-bottom:8px}
        .p-fg input,.p-fg select,.p-fg textarea{width:100%;padding:14px 16px;border:2px solid #d1d5db;border-radius:12px;font-size:16px;color:#0f172a;background:#f9fafb;transition:all .2s;font-family:inherit}
        .p-fg input:focus,.p-fg select:focus,.p-fg textarea:focus{outline:none;border-color:#2563eb;box-shadow:0 0 0 4px rgba(37,99,235,0.1);background:#fff}
        .p-fg textarea{resize:vertical;min-height:90px}
        .p-fg select{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14'%3E%3Cpath fill='%236b7280' d='M7 9L1 3h12z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:40px}
        .p-phone{display:flex}
        .p-phone-prefix{display:flex;align-items:center;padding:0 16px;background:#f1f5f9;border:2px solid #d1d5db;border-right:none;border-radius:12px 0 0 12px;font-size:16px;color:#475569;font-weight:700}
        .p-phone input{border-radius:0 12px 12px 0}
        .p-submit{width:100%;padding:16px;font-size:18px;font-weight:800;border-radius:12px;background:linear-gradient(135deg,#2563eb,#3b82f6);color:#fff;border:none;cursor:pointer;box-shadow:0 6px 20px rgba(37,99,235,0.35);transition:all .3s}
        .p-submit:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(37,99,235,0.45)}

        .p-alert{padding:16px 20px;border-radius:12px;font-size:16px;margin-bottom:20px;line-height:1.6}
        .p-alert.error{background:#fef2f2;color:#991b1b;border:2px solid #fecaca}
        .p-alert.success{background:#f0fdf4;color:#166534;border:2px solid #bbf7d0}

        .p-cta{background:linear-gradient(135deg,#0f172a,#1e40af);color:#fff;padding:80px 0;text-align:center}
        .p-cta h2{font-size:42px;font-weight:900;margin:0 0 14px}
        .p-cta p{font-size:19px;color:#93c5fd;margin:0 auto 36px;max-width:540px;line-height:1.7}

        @media(max-width:960px){
            .p-hero h1{font-size:38px}
            .p-hero-stats{gap:32px;flex-wrap:wrap}
            .p-hero-stat strong{font-size:30px}
            .p-grid4{grid-template-columns:1fr 1fr}
            .p-steps{grid-template-columns:1fr}
            .p-grid3{grid-template-columns:1fr}
            .p-form-grid{grid-template-columns:1fr}
            .p-form-left{display:none}
            .p-title h2{font-size:32px}
        }
        @media(max-width:540px){
            .p-hero{padding:70px 0 50px}
            .p-hero h1{font-size:30px}
            .p-grid4{grid-template-columns:1fr}
            .p-section{padding:56px 0}
            .p-title h2{font-size:26px}
            .p-form-card{padding:28px 20px}
            .p-fg-row{grid-template-columns:1fr}
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<section class="p-hero">
    <div class="p-wrap">
        <h1>Turn Your Network Into<br>a <em>Monthly Income</em></h1>
        <p>Recommend My Metaji to businesses and earn recurring commissions. Zero investment. Full support. Start earning in 24 hours.</p>
        <a href="#partner-form" class="p-btn">Become a Partner →</a>
        <div class="p-hero-stats">
            <div class="p-hero-stat"><strong>500+</strong><span>Active Partners</span></div>
            <div class="p-hero-stat"><strong>₹50K+</strong><span>Monthly Earning</span></div>
            <div class="p-hero-stat"><strong>50,000+</strong><span>Businesses Served</span></div>
            <div class="p-hero-stat"><strong>4.8★</strong><span>Play Store Rating</span></div>
        </div>
    </div>
</section>

<section class="p-section" style="background:#fff">
    <div class="p-wrap">
        <div class="p-title"><h2>Which Kind of Partner Are You?</h2><p>We have tailored programs for every professional background.</p></div>
        <div class="p-grid4">
            <div class="p-card"><div class="p-card-icon">📊</div><h3>CA / Tax Consultant</h3><p>Recommend to your existing clients. Earn on every renewal. Trusted GST software adds professional credibility.</p></div>
            <div class="p-card"><div class="p-card-icon">💼</div><h3>Software Reseller</h3><p>Sell My Metaji subscriptions to local businesses. Build a recurring book of business with regular settlements.</p></div>
            <div class="p-card"><div class="p-card-icon">🏪</div><h3>Retail Store Owner</h3><p>Help nearby businesses manage billing, stock, and GST with ease. Earn extra income by recommending.</p></div>
            <div class="p-card"><div class="p-card-icon">🔧</div><h3>Hardware Store Owner</h3><p>Already connected with local businesses? Advise them to sign up and earn as they run their shop.</p></div>
        </div>
    </div>
</section>

<section class="p-section" style="background:#f8fafc">
    <div class="p-wrap">
        <div class="p-title"><h2>Start Earning in 3 Simple Steps</h2><p>No tech skills, no inventory, no upfront cost.</p></div>
        <div class="p-steps">
            <div class="p-step"><div class="p-step-num">01</div><h3>Register for Free</h3><p>Fill the form below. Get approved within 24 hours and receive your partner link and dashboard access.</p></div>
            <div class="p-step"><div class="p-step-num">02</div><h3>Refer Businesses</h3><p>Share your link with business owners, clients, or your local network. They get a great tool; you get credited.</p></div>
            <div class="p-step"><div class="p-step-num">03</div><h3>Earn Every Month</h3><p>Money settles directly to your bank account. Track everything in your partner dashboard.</p></div>
        </div>
    </div>
</section>

<section class="p-section" style="background:#fff">
    <div class="p-wrap">
        <div class="p-title"><h2>Why Become a My Metaji Partner?</h2><p>We give you everything you need to succeed.</p></div>
        <div class="p-grid4">
            <div class="p-card"><div class="p-card-icon">💰</div><h3>Recurring Income</h3><p>Earn commission on every sale and renewal. Your income grows every month.</p></div>
            <div class="p-card"><div class="p-card-icon">⚡</div><h3>Fast Payout</h3><p>Daily settlements directly to your bank account. No waiting for monthly cycles.</p></div>
            <div class="p-card"><div class="p-card-icon">🎯</div><h3>Full Support</h3><p>Get marketing materials, training, and a dedicated support team to help you succeed.</p></div>
            <div class="p-card"><div class="p-card-icon">🚀</div><h3>Zero Investment</h3><p>Start with zero cost. No need to purchase inventory or pay registration fees.</p></div>
        </div>
    </div>
</section>

<section class="p-section" style="background:#f8fafc">
    <div class="p-wrap">
        <div class="p-title"><h2>Why Your Clients Will Love My Metaji</h2><p>India's most downloaded billing app — used by 50,000+ businesses.</p></div>
        <div class="p-grid4">
            <div class="p-card"><div class="p-card-icon">🧾</div><h3>GST-Ready Invoicing</h3><p>GSTIN & HSN auto-validated. Compliant by default on every bill.</p></div>
            <div class="p-card"><div class="p-card-icon">📊</div><h3>Auto GSTR Reports</h3><p>GSTR-1 & GSTR-3B ready before you ask. Filing made easy.</p></div>
            <div class="p-card"><div class="p-card-icon">🔗</div><h3>Instant Data Sharing</h3><p>Clients share P&L & ledgers directly from the app.</p></div>
            <div class="p-card"><div class="p-card-icon">✅</div><h3>Errors Caught at Entry</h3><p>Wrong HSNs flagged before filing — not after.</p></div>
        </div>
    </div>
</section>

<section class="p-section" style="background:#fff">
    <div class="p-wrap">
        <div class="p-title"><h2>Success Stories from Partners</h2><p>Hear from partners who are building monthly income with My Metaji.</p></div>
        <div class="p-grid3">
            <div class="p-testi">
                <div class="p-testi-stars">★★★★★</div>
                <blockquote>"My Metaji helped me convert my local business network into income. With every sale, my earnings steadily grew beyond expectations."</blockquote>
                <div class="p-testi-author"><div class="p-testi-avatar">R</div><div><div class="p-testi-name">Ravi Sharma</div><div class="p-testi-role">Software Reseller, Delhi</div></div></div>
            </div>
            <div class="p-testi">
                <div class="p-testi-stars">★★★★★</div>
                <blockquote>"I recommend My Metaji to my clients and earn commissions. It's a win-win — they love the software, I earn regularly."</blockquote>
                <div class="p-testi-author"><div class="p-testi-avatar">M</div><div><div class="p-testi-name">Mehul Sinha</div><div class="p-testi-role">GST Consultant, Mumbai</div></div></div>
            </div>
            <div class="p-testi">
                <div class="p-testi-stars">★★★★★</div>
                <blockquote>"Earning ₹50,000+ each month with My Metaji feels amazing. Their training is top-notch and the product sells itself."</blockquote>
                <div class="p-testi-author"><div class="p-testi-avatar">P</div><div><div class="p-testi-name">Priya Patel</div><div class="p-testi-role">Digital Marketer, Ahmedabad</div></div></div>
            </div>
        </div>
    </div>
</section>

<section class="p-section" style="background:#f8fafc">
    <div class="p-wrap">
        <div class="p-title"><h2>Frequently Asked Questions</h2></div>
        <div class="p-faq">
            <div class="p-faq-item"><div class="p-faq-q" onclick="this.parentElement.classList.toggle('open')">What is the My Metaji Partner Program?</div><div class="p-faq-a">The My Metaji Partner Program allows you to earn by promoting and selling My Metaji software to businesses. You'll earn for each successful referral, creating a sustainable income stream.</div></div>
            <div class="p-faq-item"><div class="p-faq-q" onclick="this.parentElement.classList.toggle('open')">How much can I earn?</div><div class="p-faq-a">Partners typically earn between ₹20,000 to ₹50,000 per month, with top performers making over ₹1 lakh monthly.</div></div>
            <div class="p-faq-item"><div class="p-faq-q" onclick="this.parentElement.classList.toggle('open')">Do I need to invest to join?</div><div class="p-faq-a">No. The My Metaji Partner Program is free to join. You don't need to invest any money to start earning.</div></div>
            <div class="p-faq-item"><div class="p-faq-q" onclick="this.parentElement.classList.toggle('open')">Do I need technical knowledge?</div><div class="p-faq-a">No technical knowledge is required. We provide comprehensive training and support materials to help you succeed.</div></div>
            <div class="p-faq-item"><div class="p-faq-q" onclick="this.parentElement.classList.toggle('open')">How do I receive payments?</div><div class="p-faq-a">Payments are transferred directly to your bank account. Track everything in your partner dashboard in real-time.</div></div>
            <div class="p-faq-item"><div class="p-faq-q" onclick="this.parentElement.classList.toggle('open')">Can I recommend without becoming a full-time partner?</div><div class="p-faq-a">Absolutely. There's no sales target, no minimum clients, and no obligation to actively sell.</div></div>
        </div>
    </div>
</section>

<section class="p-form-section" id="partner-form">
    <div class="p-wrap">
        <div class="p-form-grid">
            <div class="p-form-left">
                <h2>Become a <em>Partner</em></h2>
                <p>Fill in your details and our team will contact you within 24 hours to set up your partner dashboard.</p>
                <div class="p-form-feats">
                    <div class="p-form-feat"><div class="p-form-feat-icon">✓</div><div><h4>Dedicated partner dashboard</h4><p>Track all clients, sales, and earnings from one place.</p></div></div>
                    <div class="p-form-feat"><div class="p-form-feat-icon">✓</div><div><h4>Marketing materials & training</h4><p>Get everything you need to start selling immediately.</p></div></div>
                    <div class="p-form-feat"><div class="p-form-feat-icon">✓</div><div><h4>Personal relationship manager</h4><p>Dedicated support to help you succeed.</p></div></div>
                    <div class="p-form-feat"><div class="p-form-feat-icon">✓</div><div><h4>Start earning from day one</h4><p>No waiting period. Start referring immediately.</p></div></div>
                </div>
            </div>
            <div class="p-form-card">
                <h3>Register Now</h3>
                <?php if ($success): ?>
                    <div class="p-alert success" style="text-align:center">
                        <strong style="font-size:20px;display:block;margin-bottom:8px">✓ Thank You!</strong>
                        Your partner registration has been submitted. Our team will contact you within 24 hours.
                    </div>
                <?php else: ?>
                    <?php if ($errors): ?><div class="p-alert error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>
                    <form method="post" action="#partner-form">
                        <div class="p-fg"><label>Full Name *</label><input type="text" name="name" placeholder="Enter your full name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"></div>
                        <div class="p-fg"><label>Mobile Number *</label><div class="p-phone"><span class="p-phone-prefix">+91</span><input type="tel" name="phone" placeholder="10-digit mobile number" required maxlength="10" pattern="[0-9]{10}" inputmode="numeric" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"></div></div>
                        <div class="p-fg-row">
                            <div class="p-fg"><label>City *</label><input type="text" name="city" placeholder="Enter your city" required value="<?= htmlspecialchars($_POST['city'] ?? '') ?>"></div>
                            <div class="p-fg"><label>Age *</label><input type="number" name="age" placeholder="e.g. 28" required min="18" max="100" inputmode="numeric" value="<?= htmlspecialchars($_POST['age'] ?? '') ?>"></div>
                        </div>
                        <div class="p-fg"><label>State *</label><select name="state" required><option value="">Select state</option><?php foreach ($indianStates as $s): ?><option value="<?= $s ?>" <?= ($_POST['state'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></div>
                        <div class="p-fg"><label>Full Address *</label><textarea name="address" placeholder="House / Shop No., Street, Area, Landmark, PIN Code" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea></div>
                        <button type="submit" class="p-submit">Submit Registration →</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="p-cta">
    <div class="p-wrap">
        <h2>Ready to Start Earning?</h2>
        <p>Join 500+ partners across India earning monthly income by promoting India's most loved billing software.</p>
        <a href="#partner-form" class="p-btn">Register Now →</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
