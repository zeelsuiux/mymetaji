<?php
require_once __DIR__ . '/config.php';
$page_title = 'Free Online Quotation Maker — Create GST Quotation | My Metaji';
$page_desc = 'Create professional quotations online for free. Add items, calculate GST, and download as PDF. Perfect for small businesses in India.';
include __DIR__ . '/includes/header.php';
?>

<section class="gst-hero">
    <div class="gst-wrap">
        <h1>Free <span>Quotation Maker</span></h1>
        <p>Create professional quotations in seconds. Add items, auto-calculate GST, download PDF.</p>
    </div>
</section>

<section class="gst-tool" id="quote-tool">
    <div class="gst-wrap">
        <div class="inv-gen-container">
            <div class="inv-gen-form">
                <div class="inv-gen-section">
                    <h3 class="inv-gen-section-title">Your Details</h3>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field"><label>Business Name</label><input type="text" id="sellerName" placeholder="Your Business Name"></div>
                        <div class="inv-gen-field"><label>Phone</label><input type="tel" id="sellerPhone" placeholder="98765 43210"></div>
                    </div>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field inv-gen-full"><label>Address</label><input type="text" id="sellerAddress" placeholder="Business Address, City, State"></div>
                    </div>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field"><label>GSTIN (Optional)</label><input type="text" id="sellerGSTIN" placeholder="22AAAAA0000A1Z5" maxlength="15"></div>
                        <div class="inv-gen-field"><label>State</label>
                            <select id="sellerState"><option value="">Select State</option>
                                <option>Andhra Pradesh</option><option>Arunachal Pradesh</option><option>Assam</option><option>Bihar</option><option>Chhattisgarh</option><option>Goa</option><option>Gujarat</option><option>Haryana</option><option>Himachal Pradesh</option><option>Jharkhand</option><option>Karnataka</option><option>Kerala</option><option>Madhya Pradesh</option><option>Maharashtra</option><option>Manipur</option><option>Meghalaya</option><option>Mizoram</option><option>Nagaland</option><option>Odisha</option><option>Punjab</option><option>Rajasthan</option><option>Sikkim</option><option>Tamil Nadu</option><option>Telangana</option><option>Tripura</option><option>Uttar Pradesh</option><option>Uttarakhand</option><option>West Bengal</option><option>Delhi</option><option>Chandigarh</option><option>Puducherry</option><option>Jammu &amp; Kashmir</option><option>Ladakh</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="inv-gen-section">
                    <h3 class="inv-gen-section-title">Customer Details</h3>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field"><label>Customer Name</label><input type="text" id="buyerName" placeholder="Customer / Business Name"></div>
                        <div class="inv-gen-field"><label>Phone</label><input type="tel" id="buyerPhone" placeholder="98765 43210"></div>
                    </div>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field inv-gen-full"><label>Address</label><input type="text" id="buyerAddress" placeholder="Customer Address, City, State"></div>
                    </div>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field"><label>GSTIN (Optional)</label><input type="text" id="buyerGSTIN" placeholder="22AAAAA0000A1Z5" maxlength="15"></div>
                        <div class="inv-gen-field"><label>State</label>
                            <select id="buyerState"><option value="">Select State</option>
                                <option>Andhra Pradesh</option><option>Arunachal Pradesh</option><option>Assam</option><option>Bihar</option><option>Chhattisgarh</option><option>Goa</option><option>Gujarat</option><option>Haryana</option><option>Himachal Pradesh</option><option>Jharkhand</option><option>Karnataka</option><option>Kerala</option><option>Madhya Pradesh</option><option>Maharashtra</option><option>Manipur</option><option>Meghalaya</option><option>Mizoram</option><option>Nagaland</option><option>Odisha</option><option>Punjab</option><option>Rajasthan</option><option>Sikkim</option><option>Tamil Nadu</option><option>Telangana</option><option>Tripura</option><option>Uttar Pradesh</option><option>Uttarakhand</option><option>West Bengal</option><option>Delhi</option><option>Chandigarh</option><option>Puducherry</option><option>Jammu &amp; Kashmir</option><option>Ladakh</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="inv-gen-section">
                    <h3 class="inv-gen-section-title">Quotation Details</h3>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field"><label>Quotation Number</label><input type="text" id="quoteNo" placeholder="QUO-001"></div>
                        <div class="inv-gen-field"><label>Date</label><input type="date" id="quoteDate"></div>
                    </div>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field"><label>Valid Until</label><input type="date" id="validUntil"></div>
                        <div class="inv-gen-field"><label>Place of Supply</label>
                            <select id="placeOfSupply"><option value="">Select State</option>
                                <option>Andhra Pradesh</option><option>Arunachal Pradesh</option><option>Assam</option><option>Bihar</option><option>Chhattisgarh</option><option>Goa</option><option>Gujarat</option><option>Haryana</option><option>Himachal Pradesh</option><option>Jharkhand</option><option>Karnataka</option><option>Kerala</option><option>Madhya Pradesh</option><option>Maharashtra</option><option>Manipur</option><option>Meghalaya</option><option>Mizoram</option><option>Nagaland</option><option>Odisha</option><option>Punjab</option><option>Rajasthan</option><option>Sikkim</option><option>Tamil Nadu</option><option>Telangana</option><option>Tripura</option><option>Uttar Pradesh</option><option>Uttarakhand</option><option>West Bengal</option><option>Delhi</option><option>Chandigarh</option><option>Puducherry</option><option>Jammu &amp; Kashmir</option><option>Ladakh</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="inv-gen-section">
                    <h3 class="inv-gen-section-title">Items</h3>
                    <div class="inv-gen-items-header">
                        <span class="inv-ih-name">Item Name</span><span class="inv-ih-hsn">HSN</span><span class="inv-ih-qty">Qty</span><span class="inv-ih-rate">Rate (₹)</span><span class="inv-ih-gst">GST %</span><span class="inv-ih-total">Amount</span>
                    </div>
                    <div id="invItems"></div>
                    <button class="inv-gen-add-item" onclick="addInvItem()">+ Add Item</button>
                </div>

                <div class="inv-gen-section">
                    <h3 class="inv-gen-section-title">Notes / Terms</h3>
                    <textarea id="invNotes" rows="2" placeholder="Validity period, payment terms, etc."></textarea>
                </div>

                <div class="inv-gen-actions">
                    <button class="inv-gen-btn inv-gen-btn-primary" onclick="previewDoc()">👁 Preview</button>
                    <button class="inv-gen-btn inv-gen-btn-success" onclick="downloadPDF()">📥 Download PDF</button>
                </div>
            </div>

            <div class="inv-gen-preview-wrap">
                <div class="inv-gen-preview-label">Live Preview</div>
                <div class="inv-gen-preview" id="invPreview">
                    <div class="inv-preview-sheet" id="invSheet">
                        <div class="inv-p-head">
                            <div class="inv-p-brand">
                                <h2 id="prevSellerName">Your Business</h2>
                                <p id="prevSellerAddr">Address</p>
                                <p id="prevSellerPhone">Phone: —</p>
                                <p id="prevSellerGSTIN">GSTIN: —</p>
                            </div>
                            <div class="inv-p-title">
                                <div class="doc-title">QUOTATION</div>
                                <p><strong>Quote #:</strong> <span id="prevQuoteNo">—</span></p>
                                <p><strong>Date:</strong> <span id="prevQuoteDate">—</span></p>
                                <p><strong>Valid Until:</strong> <span id="prevValidUntil">—</span></p>
                            </div>
                        </div>
                        <div class="inv-p-parties">
                            <div class="inv-p-party">
                                <h4>To</h4>
                                <p id="prevBuyerName" class="inv-p-party-name">Customer</p>
                                <p id="prevBuyerAddr">Address</p>
                                <p id="prevBuyerPhone">Phone: —</p>
                            </div>
                        </div>
                        <table class="inv-p-table"><thead><tr><th>#</th><th>Item</th><th>HSN</th><th>Qty</th><th>Rate</th><th>GST</th><th>Amount</th></tr></thead><tbody id="prevItems"></tbody></table>
                        <div class="inv-p-totals"><table class="inv-p-totals-table">
                            <tr><td>Subtotal</td><td id="prevSubtotal">₹0.00</td></tr>
                            <tr id="prevCGSTRow" style="display:none"><td>CGST</td><td id="prevCGST">₹0.00</td></tr>
                            <tr id="prevSGSTRow" style="display:none"><td>SGST</td><td id="prevSGST">₹0.00</td></tr>
                            <tr id="prevIGSTRow" style="display:none"><td>IGST</td><td id="prevIGST">₹0.00</td></tr>
                            <tr class="inv-p-grand"><td>Grand Total</td><td id="prevGrand">₹0.00</td></tr>
                        </table></div>
                        <div class="inv-p-words"><strong>Amount in Words:</strong> <span id="prevWords">Zero Only</span></div>
                        <div class="inv-p-notes" id="prevNotesSection" style="display:none"><strong>Notes:</strong> <span id="prevNotes"></span></div>
                        <div class="inv-p-footer"><p>Generated by <strong>My Metaji</strong> — Free GST Billing Software</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="gst-faq" style="background:#fff;"><div class="gst-wrap">
    <h2>How to Create a Quotation Online?</h2>
    <div class="gst-how-grid">
        <div class="gst-how-card"><div class="gst-how-num">1</div><h3>Enter Your Details</h3><p>Fill in your business name, address and GSTIN to appear as the quotation sender.</p></div>
        <div class="gst-how-card"><div class="gst-how-num">2</div><h3>Add Customer Info</h3><p>Enter the buyer's name, address and state for the quotation recipient.</p></div>
        <div class="gst-how-card"><div class="gst-how-num">3</div><h3>Add Items</h3><p>Add products/services with HSN, qty, rate and GST%. Set a validity date for the quote.</p></div>
        <div class="gst-how-card"><div class="gst-how-num">4</div><h3>Download PDF</h3><p>Preview your quotation live, then download as a professional PDF to share with customers.</p></div>
    </div>
</div></section>

<section class="gst-faq"><div class="gst-wrap">
    <h2>What is a Quotation?</h2>
    <p>A quotation is a formal document sent by a seller to a potential buyer, detailing the products or services offered along with their prices, taxes, and terms. Unlike an invoice, a quotation is not a demand for payment — it is an offer that the buyer can accept or negotiate. Quotations typically have a validity period after which the prices may change.</p>
    <h2 style="margin-top:32px;">What is the difference between a Quotation and a Proforma Invoice?</h2>
    <p>A <strong>quotation</strong> is a price estimate sent before any agreement. A <strong>proforma invoice</strong> is sent after the buyer agrees to the terms, acting as a preliminary bill before the actual sale. Both contain similar details, but a proforma invoice implies a confirmed deal.</p>
</div></section>

<section class="gst-cta"><div class="gst-wrap" style="text-align:center;">
    <h2>Need a Complete Billing Solution?</h2>
    <p>Manage quotations, invoices, inventory & GST returns — all in one app.</p>
    <a href="#download" class="gst-cta-btn">Download My Metaji Free →</a>
</div></section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
let invItemIndex = 0;
function addInvItem(n,h,q,r,g){const id=invItemIndex++;document.getElementById('invItems').insertAdjacentHTML('beforeend',`<div class="inv-gen-item" data-id="${id}"><input type="text" class="inv-item-name" placeholder="Item" value="${n||''}" oninput="updatePreview()"><input type="text" class="inv-item-hsn" placeholder="HSN" value="${h||''}" oninput="updatePreview()"><input type="number" class="inv-item-qty" value="${q||1}" min="1" oninput="updatePreview()"><input type="number" class="inv-item-rate" value="${r||0}" min="0" step="0.01" oninput="updatePreview()"><select class="inv-item-gst" onchange="updatePreview()"><option value="0"${g==0?' selected':''}>0%</option><option value="5"${g==5?' selected':''}>5%</option><option value="12"${g==12?' selected':''}>12%</option><option value="18"${g==18?' selected':''}>18%</option><option value="28"${g==28?' selected':''}>28%</option></select><div class="inv-item-amount">₹0.00</div><button class="inv-item-remove" onclick="removeInvItem(${id})">&times;</button></div>`);updatePreview();}
function removeInvItem(id){const el=document.querySelector(`.inv-gen-item[data-id="${id}"]`);if(el)el.remove();updatePreview();}
function isInterState(){return document.getElementById('sellerState').value!==document.getElementById('buyerState').value;}
function nw(n){if(n===0)return'Zero';const o=['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'],t=['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'],c=n=>{if(n<20)return o[n];if(n<100)return t[Math.floor(n/10)]+(n%10?' '+o[n%10]:'');if(n<1000)return o[Math.floor(n/100)]+' Hundred'+(n%100?' and '+c(n%100):'');if(n<100000)return c(Math.floor(n/1000))+' Thousand'+(n%1000?' '+c(n%1000):'');if(n<10000000)return c(Math.floor(n/100000))+' Lakh'+(n%100000?' '+c(n%100000):'');return c(Math.floor(n/10000000))+' Crore'+(n%10000000?' '+c(n%10000000):'');};const i=Math.floor(n),d=Math.round((n-i)*100);let r=c(i);if(d>0)r+=' and '+c(d)+' Paise';return r;}
function updatePreview(){
    document.getElementById('prevSellerName').textContent=document.getElementById('sellerName').value||'Your Business';
    document.getElementById('prevSellerAddr').textContent=document.getElementById('sellerAddress').value||'Address';
    document.getElementById('prevSellerPhone').textContent='Phone: '+(document.getElementById('sellerPhone').value||'—');
    document.getElementById('prevSellerGSTIN').textContent='GSTIN: '+(document.getElementById('sellerGSTIN').value||'—');
    document.getElementById('prevBuyerName').textContent=document.getElementById('buyerName').value||'Customer';
    document.getElementById('prevBuyerAddr').textContent=document.getElementById('buyerAddress').value||'Address';
    document.getElementById('prevBuyerPhone').textContent='Phone: '+(document.getElementById('buyerPhone').value||'—');
    document.getElementById('prevQuoteNo').textContent=document.getElementById('quoteNo').value||'—';
    document.getElementById('prevQuoteDate').textContent=document.getElementById('quoteDate').value||'—';
    document.getElementById('prevValidUntil').textContent=document.getElementById('validUntil').value||'—';
    const inter=isInterState();let sub=0,tcg=0,tsg=0,tig=0;const rows=[];let idx=1;
    document.querySelectorAll('.inv-gen-item').forEach(item=>{
        const nm=item.querySelector('.inv-item-name').value||'Item',hs=item.querySelector('.inv-item-hsn').value||'—',q=parseFloat(item.querySelector('.inv-item-qty').value)||0,r=parseFloat(item.querySelector('.inv-item-rate').value)||0,g=parseFloat(item.querySelector('.inv-item-gst').value)||0,tx=q*r,ga=tx*g/100,tt=tx+ga;
        item.querySelector('.inv-item-amount').textContent='₹'+tt.toFixed(2);sub+=tx;inter?tig+=ga:(tcg+=ga/2,tsg+=ga/2);rows.push(`<tr><td>${idx++}</td><td>${nm}</td><td>${hs}</td><td>${q}</td><td>₹${r.toFixed(2)}</td><td>${g}%</td><td>₹${tt.toFixed(2)}</td></tr>`);
    });
    document.getElementById('prevItems').innerHTML=rows.join('');document.getElementById('prevSubtotal').textContent='₹'+sub.toFixed(2);
    if(inter){document.getElementById('prevCGSTRow').style.display='none';document.getElementById('prevSGSTRow').style.display='none';document.getElementById('prevIGSTRow').style.display='';document.getElementById('prevIGST').textContent='₹'+tig.toFixed(2);}
    else{document.getElementById('prevCGSTRow').style.display='';document.getElementById('prevSGSTRow').style.display='';document.getElementById('prevIGSTRow').style.display='none';document.getElementById('prevCGST').textContent='₹'+tcg.toFixed(2);document.getElementById('prevSGST').textContent='₹'+tsg.toFixed(2);}
    const g=sub+tcg+tsg+tig;document.getElementById('prevGrand').textContent='₹'+g.toFixed(2);document.getElementById('prevWords').textContent=nw(g)+' Only';
    const nt=document.getElementById('invNotes').value;document.getElementById('prevNotesSection').style.display=nt?'':'none';document.getElementById('prevNotes').textContent=nt;
}
function previewDoc(){updatePreview();document.getElementById('invPreview').scrollIntoView({behavior:'smooth'});}
function downloadPDF(){
    updatePreview();const{jsPDF}=window.jspdf;const doc=new jsPDF('p','mm','a4');const w=doc.internal.pageSize.getWidth();const mg=15;let y=mg;
    doc.setFillColor(37,99,235);doc.rect(0,0,w,35,'F');doc.setTextColor(255,255,255);doc.setFontSize(18);doc.setFont('helvetica','bold');
    doc.text(document.getElementById('sellerName').value||'Your Business',mg,16);doc.setFontSize(9);doc.setFont('helvetica','normal');
    doc.text(document.getElementById('sellerAddress').value||'',mg,22);doc.text('Phone: '+(document.getElementById('sellerPhone').value||'—'),mg,27);
    doc.text('GSTIN: '+(document.getElementById('sellerGSTIN').value||'—'),mg,32);
    doc.setFontSize(14);doc.setFont('helvetica','bold');doc.text('QUOTATION',w-mg,16,{align:'right'});
    doc.setFontSize(8);doc.setFont('helvetica','normal');
    doc.text('Quote #: '+(document.getElementById('quoteNo').value||'—'),w-mg,22,{align:'right'});
    doc.text('Date: '+(document.getElementById('quoteDate').value||'—'),w-mg,27,{align:'right'});
    doc.text('Valid Until: '+(document.getElementById('validUntil').value||'—'),w-mg,32,{align:'right'});
    y=42;doc.setTextColor(0,0,0);doc.setFontSize(9);doc.setFont('helvetica','bold');doc.text('To:',mg,y);y+=5;
    doc.setFont('helvetica','normal');doc.setFontSize(8);
    doc.text(document.getElementById('buyerName').value||'Customer',mg,y);y+=4;
    doc.text(document.getElementById('buyerAddress').value||'',mg,y);y+=8;
    const items=[];let sub=0,tcg=0,tsg=0,tig=0;const inter=isInterState();let idx=1;
    document.querySelectorAll('.inv-gen-item').forEach(item=>{
        const nm=item.querySelector('.inv-item-name').value||'Item',hs=item.querySelector('.inv-item-hsn').value||'—',q=parseFloat(item.querySelector('.inv-item-qty').value)||0,r=parseFloat(item.querySelector('.inv-item-rate').value)||0,g=parseFloat(item.querySelector('.inv-item-gst').value)||0,tx=q*r,ga=tx*g/100,tt=tx+ga;
        sub+=tx;inter?tig+=ga:(tcg+=ga/2,tsg+=ga/2);items.push([idx++,nm,hs,q,'₹'+r.toFixed(2),g+'%','₹'+tt.toFixed(2)]);
    });
    doc.autoTable({startY:y,head:[['#','Item','HSN','Qty','Rate','GST','Amount']],body:items,theme:'grid',styles:{fontSize:8,cellPadding:2},headStyles:{fillColor:[37,99,235],textColor:255,fontStyle:'bold'},margin:{left:mg,right:mg}});
    y=doc.lastAutoTable.finalY+6;const tx=w-mg-55;doc.setFontSize(8);doc.setFont('helvetica','normal');
    doc.text('Subtotal:',tx,y);doc.text('₹'+sub.toFixed(2),w-mg,y,{align:'right'});y+=5;
    if(inter){doc.text('IGST:',tx,y);doc.text('₹'+tig.toFixed(2),w-mg,y,{align:'right'});y+=5;}
    else{doc.text('CGST:',tx,y);doc.text('₹'+tcg.toFixed(2),w-mg,y,{align:'right'});y+=5;doc.text('SGST:',tx,y);doc.text('₹'+tsg.toFixed(2),w-mg,y,{align:'right'});y+=5;}
    const g=sub+tcg+tsg+tig;doc.setDrawColor(37,99,235);doc.line(tx,y,w-mg,y);y+=5;
    doc.setFont('helvetica','bold');doc.setFontSize(10);doc.text('Grand Total:',tx,y);doc.text('₹'+g.toFixed(2),w-mg,y,{align:'right'});y+=7;
    doc.setFontSize(8);doc.setFont('helvetica','normal');doc.text('Amount in Words: '+nw(g)+' Only',mg,y);y+=8;
    const nt=document.getElementById('invNotes').value;if(nt){doc.setFont('helvetica','bold');doc.text('Notes:',mg,y);y+=4;doc.setFont('helvetica','normal');const nl=doc.splitTextToSize(nt,w-2*mg);doc.text(nl,mg,y);y+=nl.length*4+4;}
    doc.setDrawColor(200,200,200);doc.line(mg,y,w-mg,y);y+=5;doc.setFontSize(7);doc.setTextColor(128,128,128);
    doc.text('Generated by My Metaji — Free GST Billing Software',w/2,y,{align:'center'});
    doc.save((document.getElementById('quoteNo').value||'quotation')+'.pdf');
}
document.getElementById('quoteDate').value=new Date().toISOString().split('T')[0];
addInvItem('','',1,0,18);
['sellerName','sellerAddress','sellerPhone','sellerGSTIN','sellerState','buyerName','buyerAddress','buyerPhone','buyerGSTIN','buyerState','quoteNo','quoteDate','validUntil','placeOfSupply','invNotes'].forEach(id=>{const el=document.getElementById(id);if(el){el.addEventListener('input',updatePreview);el.addEventListener('change',updatePreview);}});
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
