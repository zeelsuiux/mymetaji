<?php
require_once __DIR__ . '/config.php';
$page_title = 'Free Online Receipt Maker — Payment Receipt Generator | My Metaji';
$page_desc = 'Create professional payment receipts online for free. Add details, calculate totals, and download as PDF.';
include __DIR__ . '/includes/header.php';
?>

<section class="gst-hero"><div class="gst-wrap">
    <h1>Free <span>Receipt Maker</span></h1>
    <p>Create professional payment receipts in seconds. Download as PDF instantly.</p>
</div></section>

<section class="gst-tool" id="receipt-tool"><div class="gst-wrap"><div class="inv-gen-container">
    <div class="inv-gen-form">
        <div class="inv-gen-section">
            <h3 class="inv-gen-section-title">Your Details</h3>
            <div class="inv-gen-row">
                <div class="inv-gen-field"><label>Business Name</label><input type="text" id="sellerName" placeholder="Your Business Name"></div>
                <div class="inv-gen-field"><label>Phone</label><input type="tel" id="sellerPhone" placeholder="98765 43210"></div>
            </div>
            <div class="inv-gen-row"><div class="inv-gen-field inv-gen-full"><label>Address</label><input type="text" id="sellerAddress" placeholder="Business Address"></div></div>
            <div class="inv-gen-row">
                <div class="inv-gen-field"><label>GSTIN (Optional)</label><input type="text" id="sellerGSTIN" placeholder="22AAAAA0000A1Z5" maxlength="15"></div>
                <div class="inv-gen-field"><label>State</label>
                    <select id="sellerState"><option value="">Select State</option><option>Andhra Pradesh</option><option>Arunachal Pradesh</option><option>Assam</option><option>Bihar</option><option>Chhattisgarh</option><option>Goa</option><option>Gujarat</option><option>Haryana</option><option>Himachal Pradesh</option><option>Jharkhand</option><option>Karnataka</option><option>Kerala</option><option>Madhya Pradesh</option><option>Maharashtra</option><option>Manipur</option><option>Meghalaya</option><option>Mizoram</option><option>Nagaland</option><option>Odisha</option><option>Punjab</option><option>Rajasthan</option><option>Sikkim</option><option>Tamil Nadu</option><option>Telangana</option><option>Tripura</option><option>Uttar Pradesh</option><option>Uttarakhand</option><option>West Bengal</option><option>Delhi</option><option>Chandigarh</option><option>Puducherry</option></select>
                </div>
            </div>
        </div>

        <div class="inv-gen-section">
            <h3 class="inv-gen-section-title">Payment Details</h3>
            <div class="inv-gen-row">
                <div class="inv-gen-field"><label>Receipt Number</label><input type="text" id="receiptNo" placeholder="RCP-001"></div>
                <div class="inv-gen-field"><label>Date</label><input type="date" id="receiptDate"></div>
            </div>
            <div class="inv-gen-row">
                <div class="inv-gen-field"><label>Paid By</label><input type="text" id="buyerName" placeholder="Customer Name"></div>
                <div class="inv-gen-field"><label>Phone</label><input type="tel" id="buyerPhone" placeholder="98765 43210"></div>
            </div>
            <div class="inv-gen-row">
                <div class="inv-gen-field"><label>Amount (₹)</label><input type="number" id="payAmount" value="0" min="0" step="0.01" oninput="updatePreview()"></div>
                <div class="inv-gen-field"><label>Payment Mode</label>
                    <select id="payMode" onchange="updatePreview()"><option>Cash</option><option>UPI</option><option>Bank Transfer</option><option>Cheque</option><option>Credit Card</option><option>Debit Card</option><option>NEFT / RTGS</option></select>
                </div>
            </div>
            <div class="inv-gen-row">
                <div class="inv-gen-field"><label>Reference / Transaction ID</label><input type="text" id="payRef" placeholder="Optional" oninput="updatePreview()"></div>
                <div class="inv-gen-field"><label>For (Invoice #)</label><input type="text" id="payInvoice" placeholder="Optional" oninput="updatePreview()"></div>
            </div>
        </div>

        <div class="inv-gen-section">
            <h3 class="inv-gen-section-title">Notes</h3>
            <textarea id="invNotes" rows="2" placeholder="Thank you note, etc." oninput="updatePreview()"></textarea>
        </div>

        <div class="inv-gen-actions">
            <button class="inv-gen-btn inv-gen-btn-primary" onclick="previewDoc()">👁 Preview</button>
            <button class="inv-gen-btn inv-gen-btn-success" onclick="downloadPDF()">📥 Download PDF</button>
        </div>
    </div>

    <div class="inv-gen-preview-wrap">
        <div class="inv-gen-preview-label">Live Preview</div>
        <div class="inv-gen-preview" id="invPreview">
            <div class="inv-preview-sheet">
                <div class="inv-p-head">
                    <div class="inv-p-brand"><h2 id="prevSellerName">Your Business</h2><p id="prevSellerAddr">Address</p><p id="prevSellerPhone">Phone: —</p><p id="prevSellerGSTIN">GSTIN: —</p></div>
                    <div class="inv-p-title"><div class="doc-title">PAYMENT RECEIPT</div><p><strong>Receipt #:</strong> <span id="prevReceiptNo">—</span></p><p><strong>Date:</strong> <span id="prevReceiptDate">—</span></p></div>
                </div>
                <div class="inv-p-parties">
                    <div class="inv-p-party"><h4>Received From</h4><p class="inv-p-party-name" id="prevBuyerName">Customer</p><p id="prevBuyerPhone">Phone: —</p></div>
                    <div class="inv-p-party"><h4>Payment Info</h4><p id="prevPayMode">Mode: Cash</p><p id="prevPayRef" style="display:none">Ref: —</p><p id="prevPayInvoice" style="display:none">Invoice: —</p></div>
                </div>
                <div style="text-align:center;padding:20px;border:2px solid #10B981;border-radius:12px;margin:16px 0;background:#f0fdf4;">
                    <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:1px;">Amount Received</div>
                    <div style="font-size:32px;font-weight:800;color:#10B981;" id="prevAmount">₹0.00</div>
                </div>
                <div class="inv-p-words"><strong>Amount in Words:</strong> <span id="prevWords">Zero Only</span></div>
                <div class="inv-p-notes" id="prevNotesSection" style="display:none"><strong>Notes:</strong> <span id="prevNotes"></span></div>
                <div class="inv-p-footer"><p>Generated by <strong>My Metaji</strong> — Free GST Billing Software</p></div>
            </div>
        </div>
    </div>
</div></div></section>

<section class="gst-faq" style="background:#fff;"><div class="gst-wrap">
    <h2>How to Create a Payment Receipt?</h2>
    <div class="gst-how-grid">
        <div class="gst-how-card"><div class="gst-how-num">1</div><h3>Enter Your Details</h3><p>Fill in your business name, address and GSTIN.</p></div>
        <div class="gst-how-card"><div class="gst-how-num">2</div><h3>Add Payment Info</h3><p>Enter amount, payment mode (Cash/UPI/Bank), and reference number.</p></div>
        <div class="gst-how-card"><div class="gst-how-num">3</div><h3>Add Customer</h3><p>Enter the payer's name and phone number for the receipt.</p></div>
        <div class="gst-how-card"><div class="gst-how-num">4</div><h3>Download PDF</h3><p>Preview your receipt and download as a professional PDF.</p></div>
    </div>
</div></section>

<section class="gst-faq"><div class="gst-wrap">
    <h2>What is a Payment Receipt?</h2>
    <p>A payment receipt is a document issued by a seller to a buyer confirming that payment has been received. It serves as proof of transaction and typically includes the amount paid, payment mode, date, and parties involved. Receipts are essential for bookkeeping and resolving payment disputes.</p>
</div></section>

<section class="gst-cta"><div class="gst-wrap" style="text-align:center;">
    <h2>Need a Complete Billing Solution?</h2><p>Manage receipts, invoices, inventory & GST returns — all in one app.</p>
    <a href="#download" class="gst-cta-btn">Download My Metaji Free →</a>
</div></section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
function nw(n){if(n===0)return'Zero';const o=['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'],t=['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'],c=n=>{if(n<20)return o[n];if(n<100)return t[Math.floor(n/10)]+(n%10?' '+o[n%10]:'');if(n<1000)return o[Math.floor(n/100)]+' Hundred'+(n%100?' and '+c(n%100):'');if(n<100000)return c(Math.floor(n/1000))+' Thousand'+(n%1000?' '+c(n%1000):'');if(n<10000000)return c(Math.floor(n/100000))+' Lakh'+(n%100000?' '+c(n%100000):'');return c(Math.floor(n/10000000))+' Crore'+(n%10000000?' '+c(n%10000000):'');};const i=Math.floor(n),d=Math.round((n-i)*100);let r=c(i);if(d>0)r+=' and '+c(d)+' Paise';return r;}
function updatePreview(){
    document.getElementById('prevSellerName').textContent=document.getElementById('sellerName').value||'Your Business';
    document.getElementById('prevSellerAddr').textContent=document.getElementById('sellerAddress').value||'Address';
    document.getElementById('prevSellerPhone').textContent='Phone: '+(document.getElementById('sellerPhone').value||'—');
    document.getElementById('prevSellerGSTIN').textContent='GSTIN: '+(document.getElementById('sellerGSTIN').value||'—');
    document.getElementById('prevBuyerName').textContent=document.getElementById('buyerName').value||'Customer';
    document.getElementById('prevBuyerPhone').textContent='Phone: '+(document.getElementById('buyerPhone').value||'—');
    document.getElementById('prevReceiptNo').textContent=document.getElementById('receiptNo').value||'—';
    document.getElementById('prevReceiptDate').textContent=document.getElementById('receiptDate').value||'—';
    const amt=parseFloat(document.getElementById('payAmount').value)||0;
    document.getElementById('prevAmount').textContent='₹'+amt.toFixed(2);
    document.getElementById('prevPayMode').textContent='Mode: '+document.getElementById('payMode').value;
    const ref=document.getElementById('payRef').value;document.getElementById('prevPayRef').style.display=ref?'':'none';document.getElementById('prevPayRef').textContent='Ref: '+ref;
    const inv=document.getElementById('payInvoice').value;document.getElementById('prevPayInvoice').style.display=inv?'':'none';document.getElementById('prevPayInvoice').textContent='Invoice: '+inv;
    document.getElementById('prevWords').textContent=nw(amt)+' Only';
    const nt=document.getElementById('invNotes').value;document.getElementById('prevNotesSection').style.display=nt?'':'none';document.getElementById('prevNotes').textContent=nt;
}
function previewDoc(){updatePreview();document.getElementById('invPreview').scrollIntoView({behavior:'smooth'});}
function downloadPDF(){
    updatePreview();const{jsPDF}=window.jspdf;const doc=new jsPDF('p','mm','a4');const w=doc.internal.pageSize.getWidth();const mg=15;let y=mg;
    doc.setFillColor(37,99,235);doc.rect(0,0,w,35,'F');doc.setTextColor(255,255,255);doc.setFontSize(18);doc.setFont('helvetica','bold');
    doc.text(document.getElementById('sellerName').value||'Your Business',mg,16);doc.setFontSize(9);doc.setFont('helvetica','normal');
    doc.text(document.getElementById('sellerAddress').value||'',mg,22);doc.text('Phone: '+(document.getElementById('sellerPhone').value||'—'),mg,27);
    doc.text('GSTIN: '+(document.getElementById('sellerGSTIN').value||'—'),mg,32);
    doc.setFontSize(14);doc.setFont('helvetica','bold');doc.text('PAYMENT RECEIPT',w-mg,16,{align:'right'});
    doc.setFontSize(8);doc.setFont('helvetica','normal');
    doc.text('Receipt #: '+(document.getElementById('receiptNo').value||'—'),w-mg,22,{align:'right'});
    doc.text('Date: '+(document.getElementById('receiptDate').value||'—'),w-mg,27,{align:'right'});
    y=42;doc.setTextColor(0,0,0);doc.setFontSize(9);doc.setFont('helvetica','bold');
    doc.text('Received From:',mg,y);doc.text('Payment Details:',w/2+5,y);y+=5;
    doc.setFont('helvetica','normal');doc.setFontSize(8);
    doc.text(document.getElementById('buyerName').value||'Customer',mg,y);doc.text('Mode: '+document.getElementById('payMode').value,w/2+5,y);y+=4;
    doc.text('Phone: '+(document.getElementById('buyerPhone').value||'—'),mg,y);
    const ref=document.getElementById('payRef').value;if(ref)doc.text('Ref: '+ref,w/2+5,y);y+=10;
    const amt=parseFloat(document.getElementById('payAmount').value)||0;
    doc.setFillColor(240,253,244);doc.roundedRect(mg,y-2,w-2*mg,22,3,3,'F');
    doc.setFontSize(10);doc.setTextColor(16,185,129);doc.setFont('helvetica','bold');doc.text('AMOUNT RECEIVED',w/2,y+4,{align:'center'});
    doc.setFontSize(22);doc.text('₹'+amt.toFixed(2),w/2,y+14,{align:'center'});
    y+=28;doc.setTextColor(0,0,0);doc.setFontSize(8);doc.setFont('helvetica','normal');
    doc.text('Amount in Words: '+nw(amt)+' Only',mg,y);y+=8;
    const nt=document.getElementById('invNotes').value;if(nt){doc.setFont('helvetica','bold');doc.text('Notes:',mg,y);y+=4;doc.setFont('helvetica','normal');doc.text(nt,mg,y);y+=8;}
    doc.setDrawColor(200,200,200);doc.line(mg,y,w-mg,y);y+=5;doc.setFontSize(7);doc.setTextColor(128,128,128);
    doc.text('Generated by My Metaji — Free GST Billing Software',w/2,y,{align:'center'});
    doc.save((document.getElementById('receiptNo').value||'receipt')+'.pdf');
}
document.getElementById('receiptDate').value=new Date().toISOString().split('T')[0];
['sellerName','sellerAddress','sellerPhone','sellerGSTIN','sellerState','buyerName','buyerPhone','receiptNo','receiptDate','payAmount','payMode','payRef','payInvoice','invNotes'].forEach(id=>{const el=document.getElementById(id);if(el){el.addEventListener('input',updatePreview);el.addEventListener('change',updatePreview);}});
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
