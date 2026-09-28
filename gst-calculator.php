<?php
require_once __DIR__ . '/config.php';
$page_title = 'Free GST Calculator Online 2026 — My Metaji';
$page_desc = 'Calculate GST instantly with My Metaji free online calculator. Updated for 2026 tax slabs. Get CGST, SGST & IGST breakdown.';
include __DIR__ . '/includes/header.php';
?>

<section class="gst-hero">
    <div class="gst-wrap">
        <h1>Free <span>GST Calculator</span></h1>
        <p>Calculate GST instantly for any rate. Get CGST, SGST & IGST breakdown.</p>
    </div>
</section>

<section class="gst-tool" id="calculator">
    <div class="gst-wrap">
        <div class="gst-calc">
            <div class="gst-calc-left">
                <div class="gst-tabs">
                    <button class="gst-tab active" onclick="setMode('add')">Add GST</button>
                    <button class="gst-tab" onclick="setMode('remove')">Remove GST</button>
                </div>
                <div class="gst-field">
                    <label>Amount</label>
                    <div class="gst-input-wrap">
                        <span class="gst-currency">₹</span>
                        <input type="number" id="gcAmount" placeholder="0.00" oninput="calc()">
                    </div>
                </div>
                <div class="gst-field">
                    <label>GST Rate</label>
                    <div class="gst-rate-btns">
                        <button onclick="setRate(0)" data-r="0">0%</button>
                        <button onclick="setRate(5)" data-r="5">5%</button>
                        <button class="on" onclick="setRate(18)" data-r="18">18%</button>
                        <button onclick="setRate(28)" data-r="28">28%</button>
                        <button onclick="setCustom()" data-r="c">Custom</button>
                    </div>
                    <input type="number" id="gcCustom" style="display:none" placeholder="Enter %" oninput="calc()">
                </div>
                <div class="gst-field">
                    <label>Transaction</label>
                    <div class="gst-txn-btns">
                        <button class="on" onclick="setTxn('intra')">Same State (CGST + SGST)</button>
                        <button onclick="setTxn('inter')">Different State (IGST)</button>
                    </div>
                </div>
                <button class="gst-reset" onclick="resetCalc()">Reset</button>
            </div>
            <div class="gst-calc-right">
                <div class="gst-res-label">Result</div>
                <div class="gst-res-row"><span>Original Amount</span><strong id="gcOriginal">₹0.00</strong></div>
                <div class="gst-res-row"><span>GST Amount</span><strong id="gcGST" style="color:#2563eb">₹0.00</strong></div>
                <div class="gst-res-row gst-total"><span>Total Amount</span><strong id="gcTotal">₹0.00</strong></div>
                <div class="gst-res-split">
                    <div class="gst-split-box" id="gcCGSTBox"><small>CGST (<span id="gcCgstR">9</span>%)</small><strong id="gcCgstA">₹0.00</strong></div>
                    <div class="gst-split-box" id="gcSGSTBox"><small>SGST (<span id="gcSgstR">9</span>%)</small><strong id="gcSgstA">₹0.00</strong></div>
                    <div class="gst-split-box" id="gcIGSTBox" style="display:none"><small>IGST (<span id="gcIgstR">18</span>%)</small><strong id="gcIgstA">₹0.00</strong></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="gst-info" style="background:#fff">
    <div class="gst-wrap">
        <h2>What is GST?</h2>
        <div class="gst-txt"><p><strong>GST (Goods and Services Tax)</strong> is a single, nationwide tax that replaced multiple indirect taxes like VAT, excise, and service tax. Before GST, each state had its own tax system, creating confusion and double taxation.</p></div>
    </div>
</section>

<section class="gst-info" style="background:#f8fafc">
    <div class="gst-wrap">
        <h2>GST Tax Slabs in India 2026</h2>
        <div class="gst-slab-grid">
            <div class="gst-slab"><div class="gst-slab-num g-green">0%</div><h4>Nil</h4><p>Fruits, vegetables, grains, milk, medicines</p></div>
            <div class="gst-slab"><div class="gst-slab-num g-blue">5%</div><h4>Everyday</h4><p>Edible oils, packaged food, restaurants</p></div>
            <div class="gst-slab"><div class="gst-slab-num g-orange">18%</div><h4>Standard</h4><p>Electronics, appliances, furniture, services</p></div>
            <div class="gst-slab"><div class="gst-slab-num g-red">28%</div><h4>Luxury</h4><p>Cigarettes, tobacco, luxury cars</p></div>
        </div>
    </div>
</section>

<section class="gst-info" style="background:#fff">
    <div class="gst-wrap">
        <h2>Types of GST</h2>
        <div class="gst-type-grid">
            <div class="gst-type"><div class="gst-type-i" style="background:#eff6ff;color:#2563eb">C</div><h4>CGST</h4><p>Central GST — half the rate, intra-state. For 18% GST → 9% CGST.</p></div>
            <div class="gst-type"><div class="gst-type-i" style="background:#f0fdf4;color:#16a34a">S</div><h4>SGST</h4><p>State GST — half the rate, intra-state. For 18% GST → 9% SGST.</p></div>
            <div class="gst-type"><div class="gst-type-i" style="background:#fef2f2;color:#dc2626">I</div><h4>IGST</h4><p>Integrated GST — full rate, inter-state. For 18% GST → 18% IGST.</p></div>
        </div>
    </div>
</section>

<section class="gst-info" style="background:#f8fafc">
    <div class="gst-wrap">
        <h2>How to Calculate GST</h2>
        <div class="gst-formula-grid">
            <div class="gst-formula"><h4>Add GST (Exclusive)</h4><div class="gst-fbox"><p><strong>GST</strong> = (Price × Rate) ÷ 100</p><p><strong>Total</strong> = Price + GST</p></div><div class="gst-fex">₹1,000 at 18% → GST = ₹180, Total = ₹1,180</div></div>
            <div class="gst-formula"><h4>Remove GST (Inclusive)</h4><div class="gst-fbox"><p><strong>Original</strong> = Total × (100 ÷ (100 + Rate))</p><p><strong>GST</strong> = Total − Original</p></div><div class="gst-fex">₹1,180 at 18% → Original = ₹1,000, GST = ₹180</div></div>
        </div>
    </div>
</section>

<section class="gst-info" style="background:#fff">
    <div class="gst-wrap">
        <h2>Old vs New GST Rates</h2>
        <table class="gst-tbl">
            <thead><tr><th>Item</th><th>Old</th><th>New</th><th>Impact</th></tr></thead>
            <tbody>
                <tr><td>Processed food</td><td>12%</td><td>5%</td><td><span class="gst-tag g-green">Cheaper</span></td></tr>
                <tr><td>Medicines</td><td>5%</td><td>0%</td><td><span class="gst-tag g-green">Cheaper</span></td></tr>
                <tr><td>Electronics</td><td>28%</td><td>18%</td><td><span class="gst-tag g-green">Affordable</span></td></tr>
                <tr><td>Furniture</td><td>28%</td><td>18%</td><td><span class="gst-tag g-green">Affordable</span></td></tr>
                <tr><td>Mobile phones</td><td>18%</td><td>18%</td><td><span class="gst-tag g-blue">Same</span></td></tr>
                <tr><td>Restaurants</td><td>5%</td><td>5%</td><td><span class="gst-tag g-blue">Same</span></td></tr>
                <tr><td>Luxury watches</td><td>28%</td><td>28%</td><td><span class="gst-tag g-red">Costlier</span></td></tr>
                <tr><td>Tobacco</td><td>28%+cess</td><td>28%</td><td><span class="gst-tag g-red">Costlier</span></td></tr>
            </tbody>
        </table>
    </div>
</section>

<section class="gst-info" style="background:#f8fafc">
    <div class="gst-wrap">
        <h2>FAQs</h2>
        <div class="gst-faq">
            <div class="gst-faq-item"><div class="gst-faq-q" onclick="this.parentElement.classList.toggle('open')">What is GST?<span>+</span></div><div class="gst-faq-a">GST (Goods and Services Tax) is a single nationwide indirect tax that replaced VAT, excise, and service tax in India.</div></div>
            <div class="gst-faq-item"><div class="gst-faq-q" onclick="this.parentElement.classList.toggle('open')">How many GST slabs?<span>+</span></div><div class="gst-faq-a">4 slabs: 0% for essentials, 5% for everyday items, 18% for standard goods, 28% for luxury goods.</div></div>
            <div class="gst-faq-item"><div class="gst-faq-q" onclick="this.parentElement.classList.toggle('open')">CGST + SGST vs IGST?<span>+</span></div><div class="gst-faq-a">CGST + SGST for same-state. IGST for different-state transactions.</div></div>
            <div class="gst-faq-item"><div class="gst-faq-q" onclick="this.parentElement.classList.toggle('open')">How to add GST?<span>+</span></div><div class="gst-faq-a">GST = (Price × Rate) ÷ 100. Total = Price + GST.</div></div>
            <div class="gst-faq-item"><div class="gst-faq-q" onclick="this.parentElement.classList.toggle('open')">How to remove GST?<span>+</span></div><div class="gst-faq-a">Original = Total × (100 ÷ (100 + Rate)). GST = Total − Original.</div></div>
            <div class="gst-faq-item"><div class="gst-faq-q" onclick="this.parentElement.classList.toggle('open')">Is this calculator free?<span>+</span></div><div class="gst-faq-a">Yes, 100% free. No signup. Unlimited calculations.</div></div>
        </div>
    </div>
</section>

<section class="gst-cta">
    <div class="gst-wrap">
        <h2>Need More Than a Calculator?</h2>
        <p>Billing, inventory & tax reports — all in one app.</p>
        <a href="#download" class="gst-cta-btn">Try My Metaji Free →</a>
    </div>
</section>

<script>
let mode='add',rate=18,txn='intra';
function setMode(m){mode=m;document.querySelectorAll('.gst-tab').forEach((b,i)=>{b.classList.toggle('active',i===(m==='add'?0:1))});calc()}
function setRate(r){rate=r;document.querySelectorAll('.gst-rate-btns button').forEach(b=>{b.classList.toggle('on',b.dataset.r==r)});document.getElementById('gcCustom').style.display='none';calc()}
function setCustom(){document.querySelectorAll('.gst-rate-btns button').forEach(b=>b.classList.toggle('on',b.dataset.r==='c'));const i=document.getElementById('gcCustom');i.style.display='block';i.focus();rate=parseFloat(i.value)||0;calc()}
function setTxn(t){txn=t;document.querySelectorAll('.gst-txn-btns button').forEach((b,i)=>{b.classList.toggle('on',i===(t==='intra'?0:1))});document.getElementById('gcCGSTBox').style.display=t==='intra'?'block':'none';document.getElementById('gcSGSTBox').style.display=t==='intra'?'block':'none';document.getElementById('gcIGSTBox').style.display=t==='inter'?'block':'none';calc()}
function fmt(n){return'₹'+n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,',')}
function calc(){const a=parseFloat(document.getElementById('gcAmount').value)||0;if(document.querySelector('.gst-rate-btns button[data-r="c"]')?.classList.contains('on'))rate=parseFloat(document.getElementById('gcCustom').value)||0;let o,g,t;if(mode==='add'){o=a;g=a*rate/100;t=a+g}else{t=a;o=a*(100/(100+rate));g=t-o}const h=rate/2;document.getElementById('gcOriginal').textContent=fmt(o);document.getElementById('gcGST').textContent=fmt(g);document.getElementById('gcTotal').textContent=fmt(t);if(txn==='intra'){document.getElementById('gcCgstR').textContent=h;document.getElementById('gcSgstR').textContent=h;document.getElementById('gcCgstA').textContent=fmt(g/2);document.getElementById('gcSgstA').textContent=fmt(g/2)}else{document.getElementById('gcIgstR').textContent=rate;document.getElementById('gcIgstA').textContent=fmt(g)}}
function resetCalc(){document.getElementById('gcAmount').value='';document.getElementById('gcCustom').value='';document.getElementById('gcCustom').style.display='none';rate=18;mode='add';txn='intra';document.querySelectorAll('.gst-tab').forEach((b,i)=>b.classList.toggle('active',i===0));document.querySelectorAll('.gst-rate-btns button').forEach(b=>b.classList.toggle('on',b.dataset.r==='18'));document.querySelectorAll('.gst-txn-btns button').forEach((b,i)=>b.classList.toggle('on',i===0));document.getElementById('gcCGSTBox').style.display='block';document.getElementById('gcSGSTBox').style.display='block';document.getElementById('gcIGSTBox').style.display='none';calc()}
</script>

<style>
.gst-hero{background:linear-gradient(135deg,#0f172a 0%,#1e40af 50%,#3b82f6 100%);color:#fff;padding:72px 0 48px;text-align:center}
.gst-wrap{max-width:960px;margin:0 auto;padding:0 24px}
.gst-hero h1{font-size:44px;font-weight:900;margin:0 0 12px;line-height:1.1}
.gst-hero h1 span{color:#93c5fd}
.gst-hero p{font-size:18px;color:#bfdbfe;margin:0}

.gst-tool{background:linear-gradient(135deg,#eff6ff,#dbeafe 50%,#eff6ff);padding:48px 0}
.gst-calc{display:grid;grid-template-columns:1fr 1fr;gap:0;background:#fff;border-radius:20px;box-shadow:0 8px 40px rgba(0,0,0,0.1);overflow:hidden;border:1px solid #e2e8f0}
.gst-calc-left{padding:32px;border-right:1px solid #f1f5f9}
.gst-calc-right{padding:32px;background:#f8fafc;display:flex;flex-direction:column}

.gst-tabs{display:flex;background:#f1f5f9;border-radius:10px;padding:3px;margin-bottom:24px}
.gst-tab{flex:1;padding:11px;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;background:transparent;color:#64748b;transition:.2s}
.gst-tab.active{background:#2563eb;color:#fff;box-shadow:0 2px 8px rgba(37,99,235,.3)}

.gst-field{margin-bottom:20px}
.gst-field label{display:block;font-size:13px;font-weight:700;color:#64748b;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px}
.gst-input-wrap{display:flex;align-items:center;background:#f9fafb;border:2px solid #e2e8f0;border-radius:12px;overflow:hidden;transition:.2s}
.gst-input-wrap:focus-within{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1)}
.gst-currency{padding:0 14px;font-size:18px;font-weight:800;color:#94a3b8;background:#f1f5f9;border-right:2px solid #e2e8f0}
.gst-input-wrap input{flex:1;border:none;background:transparent;padding:14px 16px;font-size:20px;font-weight:700;color:#0f172a;outline:none;-moz-appearance:textfield}
.gst-input-wrap input::-webkit-outer-spin-button,.gst-input-wrap input::-webkit-inner-spin-button{-webkit-appearance:none}
.gst-rate-btns{display:flex;gap:6px;flex-wrap:wrap}
.gst-rate-btns button{padding:9px 16px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;background:#fff;color:#475569;transition:.2s}
.gst-rate-btns button.on{background:#2563eb;color:#fff;border-color:#2563eb}
.gst-rate-btns button:hover:not(.on){border-color:#2563eb;color:#2563eb}
#gcCustom{margin-top:8px;width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:15px;font-weight:600;outline:none}
#gcCustom:focus{border-color:#2563eb}
.gst-txn-btns{display:flex;gap:0;background:#f1f5f9;border-radius:10px;padding:3px}
.gst-txn-btns button{flex:1;padding:10px;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;background:transparent;color:#64748b;transition:.2s}
.gst-txn-btns button.on{background:#2563eb;color:#fff}
.gst-reset{width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-size:14px;font-weight:700;cursor:pointer;transition:.2s;margin-top:4px}
.gst-reset:hover{border-color:#dc2626;color:#dc2626}

.gst-res-label{font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:20px}
.gst-res-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #e2e8f0}
.gst-res-row span{font-size:14px;color:#64748b}
.gst-res-row strong{font-size:16px;font-weight:800;color:#0f172a}
.gst-total{border-bottom:none;border-top:2px solid #e2e8f0;margin-top:8px;padding-top:14px}
.gst-total strong{font-size:28px;font-weight:900;color:#0f172a}
.gst-res-split{display:flex;gap:10px;margin-top:auto;padding-top:20px}
.gst-split-box{flex:1;background:#fff;border:2px solid #e2e8f0;border-radius:12px;padding:14px;text-align:center}
.gst-split-box small{display:block;font-size:11px;color:#64748b;font-weight:600;margin-bottom:4px}
.gst-split-box strong{font-size:18px;font-weight:900;color:#0f172a}

.gst-info{padding:56px 0}
.gst-info h2{font-size:32px;font-weight:900;color:#0f172a;margin:0 0 28px;text-align:center}
.gst-txt{max-width:720px;margin:0 auto}
.gst-txt p{font-size:16px;color:#475569;line-height:1.8;margin-bottom:12px}

.gst-slab-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
.gst-slab{background:#fff;border:2px solid #e2e8f0;border-radius:14px;padding:24px 16px;text-align:center;transition:.2s}
.gst-slab:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.06);border-color:#2563eb}
.gst-slab-num{width:56px;height:56px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;font-size:20px;font-weight:900;margin-bottom:12px}
.g-green{background:#f0fdf4;color:#16a34a}.g-blue{background:#eff6ff;color:#2563eb}.g-orange{background:#fffbeb;color:#d97706}.g-red{background:#fef2f2;color:#dc2626}
.gst-slab h4{font-size:16px;font-weight:800;color:#0f172a;margin:0 0 6px}
.gst-slab p{font-size:13px;color:#64748b;margin:0;line-height:1.5}

.gst-type-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.gst-type{background:#fff;border:2px solid #e2e8f0;border-radius:14px;padding:24px;text-align:center;transition:.2s}
.gst-type:hover{border-color:#2563eb}
.gst-type-i{width:48px;height:48px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;font-size:20px;font-weight:900;margin-bottom:12px}
.gst-type h4{font-size:18px;font-weight:800;color:#0f172a;margin:0 0 6px}
.gst-type p{font-size:14px;color:#64748b;margin:0;line-height:1.6}

.gst-formula-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.gst-formula{background:#fff;border:2px solid #e2e8f0;border-radius:14px;padding:24px}
.gst-formula h4{font-size:16px;font-weight:800;color:#0f172a;margin:0 0 12px}
.gst-fbox{background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;padding:14px;margin-bottom:12px}
.gst-fbox p{font-size:14px;color:#334155;margin:0 0 4px;line-height:1.6}
.gst-fex{background:#eff6ff;border-radius:8px;padding:10px;font-size:13px;color:#1e40af;line-height:1.6}

.gst-tbl{width:100%;border-collapse:collapse;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.04)}
.gst-tbl th{background:#f1f5f9;padding:12px 16px;font-size:13px;font-weight:700;color:#475569;text-align:left;border-bottom:2px solid #e2e8f0}
.gst-tbl td{padding:12px 16px;font-size:14px;color:#334155;border-bottom:1px solid #f1f5f9}
.gst-tbl tr:hover td{background:#f8fafc}
.gst-tag{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}

.gst-faq{max-width:700px;margin:0 auto}
.gst-faq-item{border:2px solid #e2e8f0;border-radius:12px;margin-bottom:8px;background:#fff;overflow:hidden}
.gst-faq-q{padding:16px 20px;font-size:16px;font-weight:700;color:#0f172a;cursor:pointer;display:flex;justify-content:space-between;align-items:center}
.gst-faq-q:hover{background:#f8fafc}
.gst-faq-q span{font-size:20px;color:#94a3b8;font-weight:300;transition:.2s}
.gst-faq-item.open .gst-faq-q span{transform:rotate(45deg)}
.gst-faq-a{padding:0 20px;max-height:0;overflow:hidden;transition:.3s;font-size:15px;color:#475569;line-height:1.7}
.gst-faq-item.open .gst-faq-a{padding:0 20px 16px;max-height:200px}

.gst-cta{background:linear-gradient(135deg,#0f172a,#1e40af);color:#fff;padding:56px 0;text-align:center}
.gst-cta h2{font-size:32px;font-weight:900;margin:0 0 10px}
.gst-cta p{font-size:17px;color:#93c5fd;margin:0 auto 28px}
.gst-cta-btn{display:inline-block;background:#fff;color:#1e40af;padding:14px 36px;border-radius:12px;font-size:17px;font-weight:800;text-decoration:none;box-shadow:0 4px 16px rgba(0,0,0,.2);transition:.3s}
.gst-cta-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.3)}

@media(max-width:768px){
    .gst-hero{padding:56px 0 36px}.gst-hero h1{font-size:30px}
    .gst-calc{grid-template-columns:1fr}.gst-calc-left{border-right:none;border-bottom:1px solid #f1f5f9}
    .gst-slab-grid{grid-template-columns:1fr 1fr}
    .gst-type-grid,.gst-formula-grid{grid-template-columns:1fr}
    .gst-info h2{font-size:26px}
}
@media(max-width:480px){
    .gst-slab-grid{grid-template-columns:1fr}
    .gst-calc-left,.gst-calc-right{padding:24px 18px}
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
