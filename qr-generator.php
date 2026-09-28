<?php
require_once __DIR__ . '/config.php';
$page_title = 'Free QR Code Generator Online — My Metaji';
$page_desc = 'Generate unlimited QR codes for free. Create QR codes for URL, text, WhatsApp, phone & contact. No signup required.';
include __DIR__ . '/includes/header.php';
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<section class="qr-hero">
    <div class="qr-wrap">
        <h1>Free <span>QR Code Generator</span></h1>
        <p>Generate unlimited QR codes for free. No signup required.</p>
        <div class="qr-hero-stats">
            <div class="qr-stat"><strong>100%</strong><span>Free</span></div>
            <div class="qr-stat"><strong>5</strong><span>QR Types</span></div>
            <div class="qr-stat"><strong>0</strong><span>Signup</span></div>
        </div>
    </div>
</section>

<section class="qr-tool">
    <div class="qr-wrap">
        <div class="qr-card">
            <div class="qr-tabs">
                <button class="qr-tab active" onclick="showTab('text')">Text</button>
                <button class="qr-tab" onclick="showTab('url')">URL</button>
                <button class="qr-tab" onclick="showTab('whatsapp')">WhatsApp</button>
                <button class="qr-tab" onclick="showTab('phone')">Phone</button>
                <button class="qr-tab" onclick="showTab('contact')">Contact</button>
            </div>

            <!-- Text -->
            <div class="qr-form" id="tab-text">
                <label>Enter Text</label>
                <textarea id="qr-text" placeholder="Type any text here..." rows="3"></textarea>
                <button class="qr-gen-btn" onclick="generateText()">Generate QR Code</button>
            </div>

            <!-- URL -->
            <div class="qr-form" id="tab-url" style="display:none">
                <label>Enter URL</label>
                <input type="url" id="qr-url" placeholder="https://example.com">
                <button class="qr-gen-btn" onclick="generateURL()">Generate QR Code</button>
            </div>

            <!-- WhatsApp -->
            <div class="qr-form" id="tab-whatsapp" style="display:none">
                <label>WhatsApp Number</label>
                <div class="qr-input-group">
                    <span class="qr-prefix">+91</span>
                    <input type="tel" id="qr-wa-phone" placeholder="10-digit number" maxlength="10">
                </div>
                <label>Message (Optional)</label>
                <input type="text" id="qr-wa-msg" placeholder="Hello!">
                <button class="qr-gen-btn" onclick="generateWhatsApp()">Generate QR Code</button>
            </div>

            <!-- Phone -->
            <div class="qr-form" id="tab-phone" style="display:none">
                <label>Phone Number</label>
                <div class="qr-input-group">
                    <span class="qr-prefix">+91</span>
                    <input type="tel" id="qr-phone" placeholder="10-digit number" maxlength="10">
                </div>
                <button class="qr-gen-btn" onclick="generatePhone()">Generate QR Code</button>
            </div>

            <!-- Contact -->
            <div class="qr-form" id="tab-contact" style="display:none">
                <label>Name</label>
                <input type="text" id="qr-c-name" placeholder="Full name">
                <label>Phone</label>
                <input type="tel" id="qr-c-phone" placeholder="10-digit number" maxlength="10">
                <label>Email</label>
                <input type="email" id="qr-c-email" placeholder="you@example.com">
                <label>Website</label>
                <input type="url" id="qr-c-web" placeholder="https://example.com">
                <button class="qr-gen-btn" onclick="generateContact()">Generate QR Code</button>
            </div>

            <!-- Result -->
            <div class="qr-result" id="qr-result" style="display:none">
                <div class="qr-output">
                    <div id="qr-canvas"></div>
                </div>
                <div class="qr-download-btns">
                    <button class="qr-dl-btn png" onclick="downloadPNG()">Download PNG</button>
                    <button class="qr-dl-btn jpg" onclick="downloadJPG()">Download JPG</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="qr-info" style="background:#fff">
    <div class="qr-wrap">
        <h2>How to Create a QR Code</h2>
        <div class="qr-steps">
            <div class="qr-step"><div class="qr-step-num">1</div><h4>Select Type</h4><p>Choose from Text, URL, WhatsApp, Phone, or Contact.</p></div>
            <div class="qr-step"><div class="qr-step-num">2</div><h4>Enter Details</h4><p>Fill in the information you want to store in the QR code.</p></div>
            <div class="qr-step"><div class="qr-step-num">3</div><h4>Download</h4><p>Generate and download your QR code as PNG or JPG.</p></div>
        </div>
    </div>
</section>

<section class="qr-info" style="background:#f8fafc">
    <div class="qr-wrap">
        <h2>What is a QR Code?</h2>
        <div class="qr-txt">
            <p>A <strong>QR code (Quick Response code)</strong> is a two-dimensional barcode that stores information in a pattern of black and white squares. It can be scanned by smartphones to instantly access the encoded data — whether it's a URL, phone number, text message, or contact details.</p>
            <p>QR codes are widely used across industries for payments, marketing, product packaging, business cards, and more. They eliminate the need for manual typing and provide instant access to digital information.</p>
        </div>
    </div>
</section>

<section class="qr-info" style="background:#fff">
    <div class="qr-wrap">
        <h2>Types of QR Codes</h2>
        <div class="qr-type-grid">
            <div class="qr-type-card"><div class="qr-type-icon" style="background:#eff6ff;color:#2563eb">🔗</div><h4>URL</h4><p>Link to any website, social media profile, or online store.</p></div>
            <div class="qr-type-card"><div class="qr-type-icon" style="background:#f0fdf4;color:#16a34a">💬</div><h4>WhatsApp</h4><p>Start a WhatsApp chat directly with pre-filled message.</p></div>
            <div class="qr-type-card"><div class="qr-type-icon" style="background:#fffbeb;color:#d97706">📞</div><h4>Phone</h4><p>One-tap calling — scan to dial a phone number instantly.</p></div>
            <div class="qr-type-card"><div class="qr-type-icon" style="background:#fef2f2;color:#dc2626">👤</div><h4>Contact</h4><p>Save name, phone, email & website directly to phone contacts.</p></div>
            <div class="qr-type-card"><div class="qr-type-icon" style="background:#f5f3ff;color:#7c3aed">📝</div><h4>Text</h4><p>Store any plain text — notes, codes, or messages.</p></div>
        </div>
    </div>
</section>

<section class="qr-info" style="background:#f8fafc">
    <div class="qr-wrap">
        <h2>Benefits of Using QR Codes</h2>
        <div class="qr-benefits">
            <div class="qr-benefit"><strong>Instant Access</strong><p>Users scan and get information instantly — no typing needed.</p></div>
            <div class="qr-benefit"><strong>Versatile</strong><p>Works for URLs, contacts, payments, menus, tickets, and more.</p></div>
            <div class="qr-benefit"><strong>Cost-Effective</strong><p>Free to generate. Print on any material — paper, stickers, screens.</p></div>
            <div class="qr-benefit"><strong>Trackable</strong><p>Monitor scans and engagement for marketing campaigns.</p></div>
        </div>
    </div>
</section>

<section class="qr-info" style="background:#fff">
    <div class="qr-wrap">
        <h2>FAQs</h2>
        <div class="qr-faq">
            <div class="qr-faq-item"><div class="qr-faq-q" onclick="this.parentElement.classList.toggle('open')">Are QR codes free to create?<span>+</span></div><div class="qr-faq-a">Yes! Our QR code generator is 100% free. Create unlimited QR codes without any signup.</div></div>
            <div class="qr-faq-item"><div class="qr-faq-q" onclick="this.parentElement.classList.toggle('open')">Do QR codes expire?<span>+</span></div><div class="qr-faq-a">No, QR codes never expire. They work forever as long as the encoded data remains valid.</div></div>
            <div class="qr-faq-item"><div class="qr-faq-q" onclick="this.parentElement.classList.toggle('open')">Can I use QR codes for business?<span>+</span></div><div class="qr-faq-a">Absolutely! QR codes are perfect for payments, product packaging, business cards, marketing, and more.</div></div>
            <div class="qr-faq-item"><div class="qr-faq-q" onclick="this.parentElement.classList.toggle('open')">What format can I download?<span>+</span></div><div class="qr-faq-a">You can download QR codes in PNG and JPG formats. Both are high quality and suitable for printing.</div></div>
            <div class="qr-faq-item"><div class="qr-faq-q" onclick="this.parentElement.classList.toggle('open')">How do I scan a QR code?<span>+</span></div><div class="qr-faq-a">Use your phone's camera app or any QR code scanner app. Point it at the QR code and it will automatically decode the information.</div></div>
        </div>
    </div>
</section>

<section class="qr-cta">
    <div class="qr-wrap">
        <h2>Manage Your Business with My Metaji</h2>
        <p>GST billing, inventory, accounting & more — all in one app.</p>
        <a href="#download" class="qr-cta-btn">Try My Metaji Free →</a>
    </div>
</section>

<script>
let currentTab='text';let qr=null;
function showTab(t){currentTab=t;document.querySelectorAll('.qr-tab').forEach(b=>b.classList.remove('active'));document.querySelectorAll('.qr-form').forEach(f=>f.style.display='none');event.target.classList.add('active');document.getElementById('tab-'+t).style.display='block';document.getElementById('qr-result').style.display='none'}
function makeQR(text){document.getElementById('qr-canvas').innerHTML='';qr=new QRCode(document.getElementById('qr-canvas'),{text:text,width:220,height:220,colorDark:'#0f172a',colorLight:'#ffffff',correctLevel:QRCode.CorrectLevel.H});document.getElementById('qr-result').style.display='block'}
function generateText(){const t=document.getElementById('qr-text').value.trim();if(!t){alert('Please enter text');return}makeQR(t)}
function generateURL(){let u=document.getElementById('qr-url').value.trim();if(!u){alert('Please enter a URL');return}if(!u.startsWith('http://')&&!u.startsWith('https://'))u='https://'+u;makeQR(u)}
function generateWhatsApp(){const p=document.getElementById('qr-wa-phone').value.trim();if(p.length!==10||!/^\d+$/.test(p)){alert('Enter valid 10-digit phone number');return}const m=document.getElementById('qr-wa-msg').value||'Hello!';makeQR('https://wa.me/91'+p+'?text='+encodeURIComponent(m))}
function generatePhone(){const p=document.getElementById('qr-phone').value.trim();if(p.length!==10||!/^\d+$/.test(p)){alert('Enter valid 10-digit phone number');return}makeQR('tel:+91'+p)}
function generateContact(){const n=document.getElementById('qr-c-name').value.trim();const p=document.getElementById('qr-c-phone').value.trim();const e=document.getElementById('qr-c-email').value.trim();const w=document.getElementById('qr-c-web').value.trim();if(!n){alert('Enter name');return}let v='BEGIN:VCARD\nVERSION:3.0\nN:'+n+'\nFN:'+n;if(p)v+='\nTEL:+91'+p;if(e)v+='\nEMAIL:'+e;if(w)v+='\nURL:'+w;v+='\nEND:VCARD';makeQR(v)}
function downloadPNG(){const c=document.querySelector('#qr-canvas canvas');if(!c)return;const a=document.createElement('a');a.download='qr-code.png';a.href=c.toDataURL('image/png');a.click()}
function downloadJPG(){const c=document.querySelector('#qr-canvas canvas');if(!c)return;const a=document.createElement('a');a.download='qr-code.jpg';a.href=c.toDataURL('image/jpeg',0.95);a.click()}
</script>

<style>
.qr-hero{background:linear-gradient(135deg,#0f172a 0%,#1e40af 50%,#3b82f6 100%);color:#fff;padding:72px 0 48px;text-align:center}
.qr-wrap{max-width:960px;margin:0 auto;padding:0 24px}
.qr-hero h1{font-size:44px;font-weight:900;margin:0 0 12px;line-height:1.1}
.qr-hero h1 span{color:#93c5fd}
.qr-hero p{font-size:18px;color:#bfdbfe;margin:0 0 24px}
.qr-hero-stats{display:flex;justify-content:center;gap:48px}
.qr-stat strong{display:block;font-size:28px;font-weight:900}
.qr-stat span{font-size:14px;color:#93c5fd;display:block;margin-top:2px}

.qr-tool{background:linear-gradient(135deg,#eff6ff,#dbeafe 50%,#eff6ff);padding:48px 0}
.qr-card{max-width:580px;margin:0 auto;background:#fff;border-radius:20px;box-shadow:0 8px 40px rgba(0,0,0,.1);padding:32px;border:1px solid #e2e8f0}
.qr-tabs{display:flex;gap:4px;background:#f1f5f9;border-radius:12px;padding:3px;margin-bottom:28px;flex-wrap:wrap}
.qr-tab{flex:1;padding:11px 8px;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;background:transparent;color:#64748b;transition:.2s;min-width:60px}
.qr-tab.active{background:#2563eb;color:#fff;box-shadow:0 2px 8px rgba(37,99,235,.3)}
.qr-form label{display:block;font-size:13px;font-weight:700;color:#64748b;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
.qr-form label:not(:first-child){margin-top:16px}
.qr-form input,.qr-form textarea{width:100%;padding:13px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;color:#0f172a;background:#f9fafb;transition:.2s;font-family:inherit;resize:none}
.qr-form input:focus,.qr-form textarea:focus{outline:none;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1);background:#fff}
.qr-input-group{display:flex;align-items:center;background:#f9fafb;border:2px solid #e2e8f0;border-radius:12px;overflow:hidden;transition:.2s}
.qr-input-group:focus-within{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1)}
.qr-prefix{padding:0 14px;font-size:15px;font-weight:800;color:#94a3b8;background:#f1f5f9;border-right:2px solid #e2e8f0;flex-shrink:0}
.qr-input-group input{border:none;background:transparent;padding:13px 16px}
.qr-input-group input:focus{box-shadow:none}
.qr-gen-btn{width:100%;padding:14px;border:none;border-radius:12px;font-size:16px;font-weight:800;background:linear-gradient(135deg,#2563eb,#3b82f6);color:#fff;cursor:pointer;transition:.2s;margin-top:20px;box-shadow:0 4px 12px rgba(37,99,235,.3)}
.qr-gen-btn:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(37,99,235,.4)}

.qr-result{margin-top:28px;text-align:center;padding-top:24px;border-top:2px solid #f1f5f9}
.qr-output{display:inline-block;background:#fff;border:2px solid #e2e8f0;border-radius:16px;padding:20px;margin-bottom:16px}
#qr-canvas img{display:block;margin:0 auto}
.qr-download-btns{display:flex;gap:10px;justify-content:center}
.qr-dl-btn{padding:11px 28px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s;background:#fff;color:#475569}
.qr-dl-btn.png{border-color:#2563eb;color:#2563eb}
.qr-dl-btn.png:hover{background:#2563eb;color:#fff}
.qr-dl-btn.jpg{border-color:#16a34a;color:#16a34a}
.qr-dl-btn.jpg:hover{background:#16a34a;color:#fff}

.qr-info{padding:56px 0}
.qr-info h2{font-size:32px;font-weight:900;color:#0f172a;margin:0 0 28px;text-align:center}
.qr-txt{max-width:720px;margin:0 auto}
.qr-txt p{font-size:16px;color:#475569;line-height:1.8;margin-bottom:12px}

.qr-steps{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.qr-step{background:#fff;border:2px solid #e2e8f0;border-radius:14px;padding:28px 20px;text-align:center}
.qr-step-num{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#3b82f6);color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:900;margin:0 auto 14px;box-shadow:0 4px 12px rgba(37,99,235,.3)}
.qr-step h4{font-size:16px;font-weight:800;color:#0f172a;margin:0 0 6px}
.qr-step p{font-size:14px;color:#64748b;margin:0;line-height:1.5}

.qr-type-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:14px}
.qr-type-card{background:#fff;border:2px solid #e2e8f0;border-radius:14px;padding:24px 14px;text-align:center;transition:.2s}
.qr-type-card:hover{border-color:#2563eb;transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.06)}
.qr-type-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 10px}
.qr-type-card h4{font-size:15px;font-weight:800;color:#0f172a;margin:0 0 4px}
.qr-type-card p{font-size:12px;color:#64748b;margin:0;line-height:1.4}

.qr-benefits{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.qr-benefit{background:#fff;border:2px solid #e2e8f0;border-radius:14px;padding:24px 18px;transition:.2s}
.qr-benefit:hover{border-color:#2563eb}
.qr-benefit strong{font-size:15px;font-weight:800;color:#0f172a;margin:0 0 6px;display:block}
.qr-benefit p{font-size:13px;color:#64748b;margin:0;line-height:1.5}

.qr-faq{max-width:700px;margin:0 auto}
.qr-faq-item{border:2px solid #e2e8f0;border-radius:12px;margin-bottom:8px;background:#fff;overflow:hidden}
.qr-faq-q{padding:16px 20px;font-size:16px;font-weight:700;color:#0f172a;cursor:pointer;display:flex;justify-content:space-between;align-items:center}
.qr-faq-q:hover{background:#f8fafc}
.qr-faq-q span{font-size:20px;color:#94a3b8;font-weight:300;transition:.2s}
.qr-faq-item.open .qr-faq-q span{transform:rotate(45deg)}
.qr-faq-a{padding:0 20px;max-height:0;overflow:hidden;transition:.3s;font-size:15px;color:#475569;line-height:1.7}
.qr-faq-item.open .qr-faq-a{padding:0 20px 16px;max-height:200px}

.qr-cta{background:linear-gradient(135deg,#0f172a,#1e40af);color:#fff;padding:56px 0;text-align:center}
.qr-cta h2{font-size:32px;font-weight:900;margin:0 0 10px}
.qr-cta p{font-size:17px;color:#93c5fd;margin:0 auto 28px}
.qr-cta-btn{display:inline-block;background:#fff;color:#1e40af;padding:14px 36px;border-radius:12px;font-size:17px;font-weight:800;text-decoration:none;box-shadow:0 4px 16px rgba(0,0,0,.2);transition:.3s}
.qr-cta-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.3)}

@media(max-width:768px){
    .qr-hero{padding:56px 0 36px}.qr-hero h1{font-size:30px}
    .qr-type-grid{grid-template-columns:repeat(2,1fr)}
    .qr-benefits{grid-template-columns:1fr 1fr}
    .qr-steps{grid-template-columns:1fr}
    .qr-info h2{font-size:26px}
}
@media(max-width:480px){
    .qr-type-grid,.qr-benefits{grid-template-columns:1fr}
    .qr-card{padding:24px 18px}
    .qr-tabs{flex-wrap:wrap}
    .qr-tab{min-width:auto;padding:10px 12px;font-size:12px}
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
