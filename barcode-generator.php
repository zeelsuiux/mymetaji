<?php
require_once __DIR__ . '/config.php';
$page_title = 'Free Online Barcode Generator — My Metaji';
$page_desc = 'Generate unlimited barcodes for free. Create barcodes for inventory, products & shipping. No signup required.';
include __DIR__ . '/includes/header.php';
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.6/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<section class="bc-hero">
    <div class="bc-wrap">
        <h1>Free <span>Barcode Generator</span></h1>
        <p>Create barcodes for every item in your inventory. No signup required.</p>
        <div class="bc-hero-stats">
            <div class="bc-stat"><strong>100%</strong><span>Free</span></div>
            <div class="bc-stat"><strong>5</strong><span>Formats</span></div>
            <div class="bc-stat"><strong>0</strong><span>Signup</span></div>
        </div>
    </div>
</section>

<section class="bc-tool">
    <div class="bc-wrap">
        <div class="bc-card">
            <div class="bc-form">
                <div class="bc-field">
                    <label>Item Name</label>
                    <input type="text" id="bc-name" placeholder="e.g. Widget Pro">
                </div>
                <div class="bc-field">
                    <label>Item Code / Number</label>
                    <input type="text" id="bc-code" placeholder="e.g. 123456789">
                </div>
                <div class="bc-field">
                    <label>Barcode Format</label>
                    <div class="bc-format-btns">
                        <button class="on" onclick="setFormat('CODE128','code128')" data-f="code128">CODE128</button>
                        <button onclick="setFormat('CODE39','code39')" data-f="code39">CODE39</button>
                        <button onclick="setFormat('EAN13','ean13')" data-f="ean13">EAN-13</button>
                        <button onclick="setFormat('UPC','upc')" data-f="upc">UPC-A</button>
                        <button onclick="setFormat('ITF14','itf14')" data-f="itf14">ITF-14</button>
                    </div>
                    <div class="bc-format-help" id="bc-format-help">Alphanumeric, any length</div>
                </div>
                <div class="bc-field">
                    <label>Barcode Size</label>
                    <div class="bc-size-btns">
                        <button class="on" onclick="setSize('small',2,60)" data-s="small">Small</button>
                        <button onclick="setSize('medium',2.5,80)" data-s="medium">Medium</button>
                        <button onclick="setSize('large',3,100)" data-s="large">Large</button>
                    </div>
                </div>
                <div class="bc-field">
                    <label>Show Text</label>
                    <div class="bc-toggle-row">
                        <label class="bc-toggle"><input type="checkbox" id="bc-showtext" checked onchange="generateBC()"><span class="bc-slider"></span></label>
                        <span class="bc-toggle-label">Display text below barcode</span>
                    </div>
                </div>
                <button class="bc-gen-btn" onclick="generateBC()">Generate Barcode</button>
            </div>

            <div class="bc-result" id="bc-result" style="display:none">
                <div class="bc-preview">
                    <div class="bc-item-name" id="bc-item-name"></div>
                    <svg id="bc-svg"></svg>
                </div>
                <div class="bc-download-btns">
                    <button class="bc-dl-btn png" onclick="downloadPNG()">Download PNG</button>
                    <button class="bc-dl-btn jpg" onclick="downloadJPG()">Download JPG</button>
                    <button class="bc-dl-btn pdf" onclick="downloadPDF()">Download PDF</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bc-info" style="background:#fff">
    <div class="bc-wrap">
        <h2>How to Generate a Barcode</h2>
        <div class="bc-steps">
            <div class="bc-step"><div class="bc-step-num">1</div><h4>Enter Details</h4><p>Type the item name and code/number you want to encode.</p></div>
            <div class="bc-step"><div class="bc-step-num">2</div><h4>Choose Format</h4><p>Select from CODE128, CODE39, EAN-13, UPC-A, or ITF-14.</p></div>
            <div class="bc-step"><div class="bc-step-num">3</div><h4>Download</h4><p>Generate and download your barcode as PNG, JPG, or PDF.</p></div>
        </div>
    </div>
</section>

<section class="bc-info" style="background:#f8fafc">
    <div class="bc-wrap">
        <h2>What is a Barcode?</h2>
        <div class="bc-txt">
            <p>A <strong>barcode</strong> is a machine-readable representation of data in a visual format. It consists of parallel black and white bars of varying widths that encode information. Barcodes are widely used in retail, warehousing, logistics, and manufacturing to quickly identify products and track inventory.</p>
            <p>By scanning a barcode with a barcode reader or smartphone, you can instantly access product details, pricing, stock levels, and more — eliminating manual data entry errors and speeding up operations.</p>
        </div>
    </div>
</section>

<section class="bc-info" style="background:#fff">
    <div class="bc-wrap">
        <h2>Barcode Formats Explained</h2>
        <div class="bc-format-grid">
            <div class="bc-format-card"><h4>CODE128</h4><p>Most versatile. Supports all 128 ASCII characters. Used in logistics, shipping, and inventory.</p><div class="bc-format-tag">Alphanumeric, any length</div></div>
            <div class="bc-format-card"><h4>CODE39</h4><p>Alphanumeric (A-Z, 0-9, symbols). Common in manufacturing and military. Self-checking format.</p><div class="bc-format-tag">Letters, numbers, symbols</div></div>
            <div class="bc-format-card"><h4>EAN-13</h4><p>13-digit European Article Number. Standard for retail products worldwide. Includes check digit.</p><div class="bc-format-tag">12 or 13 digits only</div></div>
            <div class="bc-format-card"><h4>UPC-A</h4><p>12-digit Universal Product Code. Standard in North America for retail point-of-sale scanning.</p><div class="bc-format-tag">11 or 12 digits only</div></div>
            <div class="bc-format-card"><h4>ITF-14</h4><p>14-digit Interleaved 2 of 5. Used for shipping cartons and outer packaging labels.</p><div class="bc-format-tag">13 or 14 digits only</div></div>
        </div>
    </div>
</section>

<section class="bc-info" style="background:#f8fafc">
    <div class="bc-wrap">
        <h2>Why Use Barcodes?</h2>
        <div class="bc-benefits">
            <div class="bc-benefit"><div class="bc-benefit-icon">⏱️</div><strong>Save Time</strong><p>Scan items instantly instead of manual data entry.</p></div>
            <div class="bc-benefit"><div class="bc-benefit-icon">📦</div><strong>Track Inventory</strong><p>Monitor stock levels, batches, and expiry dates.</p></div>
            <div class="bc-benefit"><div class="bc-benefit-icon">✅</div><strong>Reduce Errors</strong><p>Eliminate manual entry mistakes in billing and inventory.</p></div>
            <div class="bc-benefit"><div class="bc-benefit-icon">🚚</div><strong>Shipping & Delivery</strong><p>Label packages for easy tracking and logistics.</p></div>
        </div>
    </div>
</section>

<section class="bc-info" style="background:#fff">
    <div class="bc-wrap">
        <h2>FAQs</h2>
        <div class="bc-faq">
            <div class="bc-faq-item"><div class="bc-faq-q" onclick="this.parentElement.classList.toggle('open')">Is the barcode generator free?<span>+</span></div><div class="bc-faq-a">Yes! Our barcode generator is 100% free. Create unlimited barcodes without any signup.</div></div>
            <div class="bc-faq-item"><div class="bc-faq-q" onclick="this.parentElement.classList.toggle('open')">Do barcodes expire?<span>+</span></div><div class="bc-faq-a">No, barcodes never expire. They work forever as long as the encoded data remains valid.</div></div>
            <div class="bc-faq-item"><div class="bc-faq-q" onclick="this.parentElement.classList.toggle('open')">Which barcode format should I use?<span>+</span></div><div class="bc-faq-a">Use CODE128 for general purpose. EAN-13/UPC-A for retail products. ITF-14 for shipping cartons. CODE39 for manufacturing.</div></div>
            <div class="bc-faq-item"><div class="bc-faq-q" onclick="this.parentElement.classList.toggle('open')">Can I use barcodes for my business?<span>+</span></div><div class="bc-faq-a">Absolutely! Barcodes are perfect for product labeling, inventory management, billing, shipping, and more.</div></div>
            <div class="bc-faq-item"><div class="bc-faq-q" onclick="this.parentElement.classList.toggle('open')">What format can I download?<span>+</span></div><div class="bc-faq-a">You can download barcodes in PNG, JPG, and PDF formats. All are high quality for printing.</div></div>
        </div>
    </div>
</section>

<section class="bc-cta">
    <div class="bc-wrap">
        <h2>Manage Your Business with My Metaji</h2>
        <p>GST billing, inventory, accounting & barcode support — all in one app.</p>
        <a href="#download" class="bc-cta-btn">Try My Metaji Free →</a>
    </div>
</section>

<script>
let bcFormat='CODE128',bcWidth=2.5,bcHeight=80;
const formatHelp={code128:'Alphanumeric, any length',code39:'Letters, numbers, symbols (A-Z, 0-9, -.$/+%*)',ean13:'12 or 13 digits (e.g. 5901234123457)',upc:'11 or 12 digits (e.g. 012345678905)',itf14:'13 or 14 digits (e.g. 1234567890123)'};
function setFormat(f,label){bcFormat=f;document.querySelectorAll('.bc-format-btns button').forEach(b=>b.classList.toggle('on',b.dataset.f===label));document.getElementById('bc-format-help').textContent=formatHelp[label]}
function setSize(s,w,h){bcWidth=w;bcHeight=h;document.querySelectorAll('.bc-size-btns button').forEach(b=>b.classList.toggle('on',b.dataset.s===s))}
function generateBC(){const code=document.getElementById('bc-code').value.trim();const name=document.getElementById('bc-name').value.trim();if(!code){alert('Please enter item code');return}try{JsBarcode('#bc-svg',code,{format:bcFormat,width:bcWidth,height:bcHeight,displayValue:document.getElementById('bc-showtext').checked,fontSize:14,font:'monospace',margin:10});document.getElementById('bc-item-name').textContent=name||code;document.getElementById('bc-result').style.display='block'}catch(e){alert('Invalid code for '+bcFormat+' format. '+e.message)}}
function downloadPNG(){const svg=document.getElementById('bc-svg');const canvas=document.createElement('canvas');const ctx=canvas.getContext('2d');const data=new XMLSerializer().serializeToString(svg);const img=new Image();img.onload=function(){canvas.width=img.width*2;canvas.height=img.height*2;ctx.fillStyle='#fff';ctx.fillRect(0,0,canvas.width,canvas.height);ctx.drawImage(img,0,0,canvas.width,canvas.height);const a=document.createElement('a');a.download='barcode.png';a.href=canvas.toDataURL('image/png');a.click()};img.src='data:image/svg+xml;base64,'+btoa(unescape(encodeURIComponent(data)))}
function downloadJPG(){const svg=document.getElementById('bc-svg');const canvas=document.createElement('canvas');const ctx=canvas.getContext('2d');const data=new XMLSerializer().serializeToString(svg);const img=new Image();img.onload=function(){canvas.width=img.width*2;canvas.height=img.height*2;ctx.fillStyle='#fff';ctx.fillRect(0,0,canvas.width,canvas.height);ctx.drawImage(img,0,0,canvas.width,canvas.height);const a=document.createElement('a');a.download='barcode.jpg';a.href=canvas.toDataURL('image/jpeg',0.95);a.click()};img.src='data:image/svg+xml;base64,'+btoa(unescape(encodeURIComponent(data)))}
function downloadPDF(){const svg=document.getElementById('bc-svg');const data=new XMLSerializer().serializeToString(svg);const img=new Image();img.onload=function(){const{jsPDF}=window.jspdf;const pdf=new jsPDF('l','mm','a4');const w=pdf.internal.pageSize.getWidth();const h=pdf.internal.pageSize.getHeight();pdf.setFillColor(255,255,255);pdf.rect(0,0,w,h,'F');const svgW=svg.getBoundingClientRect().width;const svgH=svg.getBoundingClientRect().height;const scale=Math.min((w-40)/svgW,(h-40)/svgH);const x=(w-svgW*scale)/2;const y=(h-svgH*scale)/2;pdf.addImage(img.src,'SVG',x,y,svgW*scale,svgH*scale);pdf.save('barcode.pdf')};img.src='data:image/svg+xml;base64,'+btoa(unescape(encodeURIComponent(data)))}
</script>

<style>
.bc-hero{background:linear-gradient(135deg,#0f172a 0%,#1e40af 50%,#3b82f6 100%);color:#fff;padding:72px 0 48px;text-align:center}
.bc-wrap{max-width:960px;margin:0 auto;padding:0 24px}
.bc-hero h1{font-size:44px;font-weight:900;margin:0 0 12px;line-height:1.1}
.bc-hero h1 span{color:#93c5fd}
.bc-hero p{font-size:18px;color:#bfdbfe;margin:0 0 24px}
.bc-hero-stats{display:flex;justify-content:center;gap:48px}
.bc-stat strong{display:block;font-size:28px;font-weight:900}
.bc-stat span{font-size:14px;color:#93c5fd;display:block;margin-top:2px}

.bc-tool{background:linear-gradient(135deg,#eff6ff,#dbeafe 50%,#eff6ff);padding:48px 0}
.bc-card{max-width:640px;margin:0 auto;background:#fff;border-radius:20px;box-shadow:0 8px 40px rgba(0,0,0,.1);padding:32px;border:1px solid #e2e8f0}
.bc-form{margin-bottom:0}
.bc-field{margin-bottom:20px}
.bc-field label{display:block;font-size:13px;font-weight:700;color:#64748b;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px}
.bc-form input[type="text"]{width:100%;padding:13px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;color:#0f172a;background:#f9fafb;transition:.2s;font-family:inherit}
.bc-form input:focus{outline:none;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1);background:#fff}
.bc-format-btns{display:flex;gap:6px;flex-wrap:wrap}
.bc-format-btns button{padding:9px 16px;border:2px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;background:#fff;color:#475569;transition:.2s}
.bc-format-btns button.on{background:#2563eb;color:#fff;border-color:#2563eb}
.bc-format-btns button:hover:not(.on){border-color:#2563eb;color:#2563eb}
.bc-format-help{font-size:12px;color:#94a3b8;margin-top:6px;font-style:italic}
.bc-size-btns{display:flex;gap:8px}
.bc-size-btns button{flex:1;padding:10px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;background:#fff;color:#475569;transition:.2s}
.bc-size-btns button.on{background:#2563eb;color:#fff;border-color:#2563eb}
.bc-toggle-row{display:flex;align-items:center;gap:12px}
.bc-toggle{position:relative;display:inline-block;width:44px;height:24px;cursor:pointer}
.bc-toggle input{opacity:0;width:0;height:0}
.bc-slider{position:absolute;inset:0;background:#e2e8f0;border-radius:24px;transition:.3s}
.bc-slider::before{content:'';position:absolute;height:18px;width:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s}
.bc-toggle input:checked+.bc-slider{background:#2563eb}
.bc-toggle input:checked+.bc-slider::before{transform:translateX(20px)}
.bc-toggle-label{font-size:14px;color:#475569}
.bc-gen-btn{width:100%;padding:14px;border:none;border-radius:12px;font-size:16px;font-weight:800;background:linear-gradient(135deg,#2563eb,#3b82f6);color:#fff;cursor:pointer;transition:.2s;box-shadow:0 4px 12px rgba(37,99,235,.3);margin-top:4px}
.bc-gen-btn:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(37,99,235,.4)}

.bc-result{margin-top:28px;text-align:center;padding-top:24px;border-top:2px solid #f1f5f9}
.bc-preview{display:inline-block;background:#fff;border:2px solid #e2e8f0;border-radius:16px;padding:24px;margin-bottom:16px}
.bc-item-name{font-size:14px;font-weight:700;color:#0f172a;margin-bottom:10px;text-align:center}
.bc-preview svg{display:block;margin:0 auto}
.bc-download-btns{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.bc-dl-btn{padding:11px 24px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s;background:#fff;color:#475569}
.bc-dl-btn.png{border-color:#2563eb;color:#2563eb}.bc-dl-btn.png:hover{background:#2563eb;color:#fff}
.bc-dl-btn.jpg{border-color:#16a34a;color:#16a34a}.bc-dl-btn.jpg:hover{background:#16a34a;color:#fff}
.bc-dl-btn.pdf{border-color:#dc2626;color:#dc2626}.bc-dl-btn.pdf:hover{background:#dc2626;color:#fff}

.bc-info{padding:56px 0}
.bc-info h2{font-size:32px;font-weight:900;color:#0f172a;margin:0 0 28px;text-align:center}
.bc-txt{max-width:720px;margin:0 auto}
.bc-txt p{font-size:16px;color:#475569;line-height:1.8;margin-bottom:12px}

.bc-steps{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.bc-step{background:#fff;border:2px solid #e2e8f0;border-radius:14px;padding:28px 20px;text-align:center}
.bc-step-num{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#3b82f6);color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:900;margin:0 auto 14px;box-shadow:0 4px 12px rgba(37,99,235,.3)}
.bc-step h4{font-size:16px;font-weight:800;color:#0f172a;margin:0 0 6px}
.bc-step p{font-size:14px;color:#64748b;margin:0;line-height:1.5}

.bc-format-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.bc-format-card{background:#fff;border:2px solid #e2e8f0;border-radius:14px;padding:24px 18px;transition:.2s}
.bc-format-card:hover{border-color:#2563eb;transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.06)}
.bc-format-card h4{font-size:17px;font-weight:800;color:#0f172a;margin:0 0 6px}
.bc-format-card p{font-size:13px;color:#64748b;margin:0 0 10px;line-height:1.5}
.bc-format-tag{display:inline-block;font-size:11px;font-weight:700;color:#2563eb;background:#eff6ff;padding:3px 10px;border-radius:6px}

.bc-benefits{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.bc-benefit{background:#fff;border:2px solid #e2e8f0;border-radius:14px;padding:24px 18px;text-align:center;transition:.2s}
.bc-benefit:hover{border-color:#2563eb}
.bc-benefit-icon{font-size:32px;margin-bottom:10px}
.bc-benefit strong{display:block;font-size:15px;font-weight:800;color:#0f172a;margin-bottom:4px}
.bc-benefit p{font-size:13px;color:#64748b;margin:0;line-height:1.5}

.bc-faq{max-width:700px;margin:0 auto}
.bc-faq-item{border:2px solid #e2e8f0;border-radius:12px;margin-bottom:8px;background:#fff;overflow:hidden}
.bc-faq-q{padding:16px 20px;font-size:16px;font-weight:700;color:#0f172a;cursor:pointer;display:flex;justify-content:space-between;align-items:center}
.bc-faq-q:hover{background:#f8fafc}
.bc-faq-q span{font-size:20px;color:#94a3b8;font-weight:300;transition:.2s}
.bc-faq-item.open .bc-faq-q span{transform:rotate(45deg)}
.bc-faq-a{padding:0 20px;max-height:0;overflow:hidden;transition:.3s;font-size:15px;color:#475569;line-height:1.7}
.bc-faq-item.open .bc-faq-a{padding:0 20px 16px;max-height:200px}

.bc-cta{background:linear-gradient(135deg,#0f172a,#1e40af);color:#fff;padding:56px 0;text-align:center}
.bc-cta h2{font-size:32px;font-weight:900;margin:0 0 10px}
.bc-cta p{font-size:17px;color:#93c5fd;margin:0 auto 28px}
.bc-cta-btn{display:inline-block;background:#fff;color:#1e40af;padding:14px 36px;border-radius:12px;font-size:17px;font-weight:800;text-decoration:none;box-shadow:0 4px 16px rgba(0,0,0,.2);transition:.3s}
.bc-cta-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.3)}

@media(max-width:768px){
    .bc-hero{padding:56px 0 36px}.bc-hero h1{font-size:30px}
    .bc-format-grid{grid-template-columns:1fr}
    .bc-benefits{grid-template-columns:1fr 1fr}
    .bc-steps{grid-template-columns:1fr}
    .bc-info h2{font-size:26px}
}
@media(max-width:480px){
    .bc-benefits{grid-template-columns:1fr}
    .bc-card{padding:24px 18px}
    .bc-format-btns{gap:4px}
    .bc-format-btns button{padding:8px 10px;font-size:12px}
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
