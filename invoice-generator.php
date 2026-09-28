<?php
require_once __DIR__ . '/config.php';
$page_title = 'Free Online Invoice Generator — Create GST Invoice | My Metaji';
$page_desc = 'Create professional GST invoices online for free. Add items, calculate GST automatically, and download as PDF. Perfect for small businesses in India.';
include __DIR__ . '/includes/header.php';
?>

<section class="gst-hero">
    <div class="gst-wrap">
        <h1>Free <span>Invoice Generator</span></h1>
        <p>Create professional GST invoices in seconds. Add items, auto-calculate tax, download PDF.</p>
    </div>
</section>

<section class="gst-tool" id="invoice-tool">
    <div class="gst-wrap">
        <div class="inv-gen-container">

            <!-- LEFT: Form -->
            <div class="inv-gen-form">

                <!-- Seller Info -->
                <div class="inv-gen-section">
                    <h3 class="inv-gen-section-title">Your Details (Seller)</h3>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field">
                            <label>Business Name</label>
                            <input type="text" id="sellerName" placeholder="Your Business Name">
                        </div>
                        <div class="inv-gen-field">
                            <label>Phone</label>
                            <input type="tel" id="sellerPhone" placeholder="98765 43210">
                        </div>
                    </div>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field inv-gen-full">
                            <label>Address</label>
                            <input type="text" id="sellerAddress" placeholder="Business Address, City, State">
                        </div>
                    </div>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field">
                            <label>GSTIN (Optional)</label>
                            <input type="text" id="sellerGSTIN" placeholder="22AAAAA0000A1Z5" maxlength="15">
                        </div>
                        <div class="inv-gen-field">
                            <label>State</label>
                            <select id="sellerState">
                                <option value="">Select State</option>
                                <option value="Andhra Pradesh">Andhra Pradesh</option>
                                <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                <option value="Assam">Assam</option>
                                <option value="Bihar">Bihar</option>
                                <option value="Chhattisgarh">Chhattisgarh</option>
                                <option value="Goa">Goa</option>
                                <option value="Gujarat">Gujarat</option>
                                <option value="Haryana">Haryana</option>
                                <option value="Himachal Pradesh">Himachal Pradesh</option>
                                <option value="Jharkhand">Jharkhand</option>
                                <option value="Karnataka">Karnataka</option>
                                <option value="Kerala">Kerala</option>
                                <option value="Madhya Pradesh">Madhya Pradesh</option>
                                <option value="Maharashtra">Maharashtra</option>
                                <option value="Manipur">Manipur</option>
                                <option value="Meghalaya">Meghalaya</option>
                                <option value="Mizoram">Mizoram</option>
                                <option value="Nagaland">Nagaland</option>
                                <option value="Odisha">Odisha</option>
                                <option value="Punjab">Punjab</option>
                                <option value="Rajasthan">Rajasthan</option>
                                <option value="Sikkim">Sikkim</option>
                                <option value="Tamil Nadu">Tamil Nadu</option>
                                <option value="Telangana">Telangana</option>
                                <option value="Tripura">Tripura</option>
                                <option value="Uttar Pradesh">Uttar Pradesh</option>
                                <option value="Uttarakhand">Uttarakhand</option>
                                <option value="West Bengal">West Bengal</option>
                                <option value="Andaman and Nicobar Islands">Andaman & Nicobar</option>
                                <option value="Chandigarh">Chandigarh</option>
                                <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra & Nagar Haveli / Daman & Diu</option>
                                <option value="Delhi">Delhi</option>
                                <option value="Jammu and Kashmir">Jammu & Kashmir</option>
                                <option value="Ladakh">Ladakh</option>
                                <option value="Lakshadweep">Lakshadweep</option>
                                <option value="Puducherry">Puducherry</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Buyer Info -->
                <div class="inv-gen-section">
                    <h3 class="inv-gen-section-title">Customer Details (Buyer)</h3>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field">
                            <label>Customer Name</label>
                            <input type="text" id="buyerName" placeholder="Customer / Business Name">
                        </div>
                        <div class="inv-gen-field">
                            <label>Phone</label>
                            <input type="tel" id="buyerPhone" placeholder="98765 43210">
                        </div>
                    </div>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field inv-gen-full">
                            <label>Address</label>
                            <input type="text" id="buyerAddress" placeholder="Customer Address, City, State">
                        </div>
                    </div>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field">
                            <label>GSTIN (Optional)</label>
                            <input type="text" id="buyerGSTIN" placeholder="22AAAAA0000A1Z5" maxlength="15">
                        </div>
                        <div class="inv-gen-field">
                            <label>State</label>
                            <select id="buyerState">
                                <option value="">Select State</option>
                                <option value="Andhra Pradesh">Andhra Pradesh</option>
                                <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                <option value="Assam">Assam</option>
                                <option value="Bihar">Bihar</option>
                                <option value="Chhattisgarh">Chhattisgarh</option>
                                <option value="Goa">Goa</option>
                                <option value="Gujarat">Gujarat</option>
                                <option value="Haryana">Haryana</option>
                                <option value="Himachal Pradesh">Himachal Pradesh</option>
                                <option value="Jharkhand">Jharkhand</option>
                                <option value="Karnataka">Karnataka</option>
                                <option value="Kerala">Kerala</option>
                                <option value="Madhya Pradesh">Madhya Pradesh</option>
                                <option value="Maharashtra">Maharashtra</option>
                                <option value="Manipur">Manipur</option>
                                <option value="Meghalaya">Meghalaya</option>
                                <option value="Mizoram">Mizoram</option>
                                <option value="Nagaland">Nagaland</option>
                                <option value="Odisha">Odisha</option>
                                <option value="Punjab">Punjab</option>
                                <option value="Rajasthan">Rajasthan</option>
                                <option value="Sikkim">Sikkim</option>
                                <option value="Tamil Nadu">Tamil Nadu</option>
                                <option value="Telangana">Telangana</option>
                                <option value="Tripura">Tripura</option>
                                <option value="Uttar Pradesh">Uttar Pradesh</option>
                                <option value="Uttarakhand">Uttarakhand</option>
                                <option value="West Bengal">West Bengal</option>
                                <option value="Andaman and Nicobar Islands">Andaman & Nicobar</option>
                                <option value="Chandigarh">Chandigarh</option>
                                <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra & Nagar Haveli / Daman & Diu</option>
                                <option value="Delhi">Delhi</option>
                                <option value="Jammu and Kashmir">Jammu & Kashmir</option>
                                <option value="Ladakh">Ladakh</option>
                                <option value="Lakshadweep">Lakshadweep</option>
                                <option value="Puducherry">Puducherry</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Invoice Meta -->
                <div class="inv-gen-section">
                    <h3 class="inv-gen-section-title">Invoice Details</h3>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field">
                            <label>Invoice Number</label>
                            <input type="text" id="invoiceNo" placeholder="INV-001">
                        </div>
                        <div class="inv-gen-field">
                            <label>Invoice Date</label>
                            <input type="date" id="invoiceDate">
                        </div>
                    </div>
                    <div class="inv-gen-row">
                        <div class="inv-gen-field">
                            <label>Due Date</label>
                            <input type="date" id="dueDate">
                        </div>
                        <div class="inv-gen-field">
                            <label>Place of Supply</label>
                            <select id="placeOfSupply">
                                <option value="">Select State</option>
                                <option value="Andhra Pradesh">Andhra Pradesh</option>
                                <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                <option value="Assam">Assam</option>
                                <option value="Bihar">Bihar</option>
                                <option value="Chhattisgarh">Chhattisgarh</option>
                                <option value="Goa">Goa</option>
                                <option value="Gujarat">Gujarat</option>
                                <option value="Haryana">Haryana</option>
                                <option value="Himachal Pradesh">Himachal Pradesh</option>
                                <option value="Jharkhand">Jharkhand</option>
                                <option value="Karnataka">Karnataka</option>
                                <option value="Kerala">Kerala</option>
                                <option value="Madhya Pradesh">Madhya Pradesh</option>
                                <option value="Maharashtra">Maharashtra</option>
                                <option value="Manipur">Manipur</option>
                                <option value="Meghalaya">Meghalaya</option>
                                <option value="Mizoram">Mizoram</option>
                                <option value="Nagaland">Nagaland</option>
                                <option value="Odisha">Odisha</option>
                                <option value="Punjab">Punjab</option>
                                <option value="Rajasthan">Rajasthan</option>
                                <option value="Sikkim">Sikkim</option>
                                <option value="Tamil Nadu">Tamil Nadu</option>
                                <option value="Telangana">Telangana</option>
                                <option value="Tripura">Tripura</option>
                                <option value="Uttar Pradesh">Uttar Pradesh</option>
                                <option value="Uttarakhand">Uttarakhand</option>
                                <option value="West Bengal">West Bengal</option>
                                <option value="Andaman and Nicobar Islands">Andaman & Nicobar</option>
                                <option value="Chandigarh">Chandigarh</option>
                                <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra & Nagar Haveli / Daman & Diu</option>
                                <option value="Delhi">Delhi</option>
                                <option value="Jammu and Kashmir">Jammu & Kashmir</option>
                                <option value="Ladakh">Ladakh</option>
                                <option value="Lakshadweep">Lakshadweep</option>
                                <option value="Puducherry">Puducherry</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Line Items -->
                <div class="inv-gen-section">
                    <h3 class="inv-gen-section-title">Items</h3>
                    <div class="inv-gen-items-header">
                        <span class="inv-ih-name">Item Name</span>
                        <span class="inv-ih-hsn">HSN</span>
                        <span class="inv-ih-qty">Qty</span>
                        <span class="inv-ih-rate">Rate (₹)</span>
                        <span class="inv-ih-gst">GST %</span>
                        <span class="inv-ih-total">Amount</span>
                    </div>
                    <div id="invItems"></div>
                    <button class="inv-gen-add-item" onclick="addInvItem()">+ Add Item</button>
                </div>

                <!-- Notes -->
                <div class="inv-gen-section">
                    <h3 class="inv-gen-section-title">Notes / Terms</h3>
                    <textarea id="invNotes" rows="2" placeholder="Payment terms, thank you note, etc."></textarea>
                </div>

                <!-- Actions -->
                <div class="inv-gen-actions">
                    <button class="inv-gen-btn inv-gen-btn-primary" onclick="previewInvoice()">👁 Preview Invoice</button>
                    <button class="inv-gen-btn inv-gen-btn-success" onclick="downloadPDF()">📥 Download PDF</button>
                </div>
            </div>

            <!-- RIGHT: Live Preview -->
            <div class="inv-gen-preview-wrap">
                <div class="inv-gen-preview-label">Live Preview</div>
                <div class="inv-gen-preview" id="invPreview">
                    <div class="inv-preview-sheet" id="invSheet">
                        <div class="inv-p-head">
                            <div class="inv-p-brand">
                                <h2 id="prevSellerName">Your Business Name</h2>
                                <p id="prevSellerAddr">Address, City, State</p>
                                <p id="prevSellerPhone">Phone: —</p>
                                <p id="prevSellerGSTIN">GSTIN: —</p>
                            </div>
                            <div class="inv-p-title">
                                <div class="doc-title">TAX INVOICE</div>
                                <p><strong>Invoice #:</strong> <span id="prevInvNo">—</span></p>
                                <p><strong>Date:</strong> <span id="prevInvDate">—</span></p>
                                <p><strong>Due:</strong> <span id="prevDueDate">—</span></p>
                            </div>
                        </div>
                        <div class="inv-p-parties">
                            <div class="inv-p-party">
                                <h4>Bill To</h4>
                                <p id="prevBuyerName" class="inv-p-party-name">Customer Name</p>
                                <p id="prevBuyerAddr">Address</p>
                                <p id="prevBuyerPhone">Phone: —</p>
                                <p id="prevBuyerGSTIN">GSTIN: —</p>
                            </div>
                            <div class="inv-p-party">
                                <h4>Ship To</h4>
                                <p id="prevShipAddr">Same as above</p>
                            </div>
                        </div>
                        <table class="inv-p-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>HSN</th>
                                    <th>Qty</th>
                                    <th>Rate</th>
                                    <th>GST</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="prevItems"></tbody>
                        </table>
                        <div class="inv-p-totals">
                            <table class="inv-p-totals-table">
                                <tr><td>Subtotal</td><td id="prevSubtotal">₹0.00</td></tr>
                                <tr id="prevCGSTRow" style="display:none"><td>CGST</td><td id="prevCGST">₹0.00</td></tr>
                                <tr id="prevSGSTRow" style="display:none"><td>SGST</td><td id="prevSGST">₹0.00</td></tr>
                                <tr id="prevIGSTRow" style="display:none"><td>IGST</td><td id="prevIGST">₹0.00</td></tr>
                                <tr class="inv-p-grand"><td>Grand Total</td><td id="prevGrand">₹0.00</td></tr>
                            </table>
                        </div>
                        <div class="inv-p-words">
                            <strong>Amount in Words:</strong> <span id="prevWords">Zero Only</span>
                        </div>
                        <div class="inv-p-notes" id="prevNotesSection" style="display:none">
                            <strong>Notes:</strong> <span id="prevNotes"></span>
                        </div>
                        <div class="inv-p-footer">
                            <p>Generated by <strong>My Metaji</strong> — Free GST Billing Software</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Content Sections -->
<section class="gst-faq" style="background:#fff;">
    <div class="gst-wrap">
        <h2>How to Create a GST Invoice Online?</h2>
        <div class="gst-how-grid">
            <div class="gst-how-card">
                <div class="gst-how-num">1</div>
                <h3>Enter Your Details</h3>
                <p>Fill in your business name, address, phone and GSTIN. This appears as the seller on your invoice.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">2</div>
                <h3>Add Customer Info</h3>
                <p>Enter the buyer's name, address and state. The place of supply determines if CGST+SGST or IGST applies.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">3</div>
                <h3>Add Items</h3>
                <p>Add products/services with HSN code, quantity, rate and GST%. The invoice calculates totals automatically.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">4</div>
                <h3>Download PDF</h3>
                <p>Preview your invoice live, then download as a professional PDF. Share it with your customer instantly.</p>
            </div>
        </div>
    </div>
</section>

<section class="gst-faq">
    <div class="gst-wrap">
        <h2>What is a GST Invoice?</h2>
        <p>A GST invoice is a document issued by a registered seller to a buyer when goods or services are supplied. It contains mandatory fields like supplier and buyer details, invoice number, date, HSN/SAC codes, taxable value, GST rate and amount, and place of supply. Under Indian GST law, businesses with annual turnover above ₹40 lakhs (₹20 lakhs for special category states) must register for GST and issue GST-compliant invoices.</p>

        <h2 style="margin-top:32px;">What is the difference between CGST, SGST and IGST?</h2>
        <p><strong>CGST (Central GST)</strong> and <strong>SGST (State GST)</strong> are charged when the seller and buyer are in the same state (intra-state transaction). <strong>IGST (Integrated GST)</strong> is charged when the seller and buyer are in different states (inter-state transaction). For example, if you sell goods within Gujarat, you charge CGST + SGST. If you sell to a buyer in Maharashtra, you charge IGST.</p>

        <h2 style="margin-top:32px;">Is this invoice generator free?</h2>
        <p>Yes, the My Metaji Invoice Generator is 100% free. You can create unlimited invoices, add multiple items, auto-calculate GST, and download PDFs without any charges or sign-up required.</p>

        <h2 style="margin-top:32px;">Can I use this for e-invoicing?</h2>
        <p>This tool creates standard GST invoices for download and sharing. For Government e-Invoicing (IRN generation), you need a registered billing software like My Metaji that integrates with the GST portal.</p>
    </div>
</section>

<section class="gst-cta">
    <div class="gst-wrap" style="text-align:center;">
        <h2>Need a Complete Billing Solution?</h2>
        <p>Manage invoices, inventory, accounting & GST returns — all in one app.</p>
        <a href="#download" class="gst-cta-btn">Download My Metaji Free →</a>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
let invItemIndex = 0;

function addInvItem(name, hsn, qty, rate, gst) {
    const id = invItemIndex++;
    const html = `<div class="inv-gen-item" data-id="${id}">
        <input type="text" class="inv-item-name" placeholder="Item name" value="${name||''}" oninput="updatePreview()">
        <input type="text" class="inv-item-hsn" placeholder="HSN" value="${hsn||''}" oninput="updatePreview()">
        <input type="number" class="inv-item-qty" placeholder="1" value="${qty||1}" min="1" oninput="updatePreview()">
        <input type="number" class="inv-item-rate" placeholder="0" value="${rate||0}" min="0" step="0.01" oninput="updatePreview()">
        <select class="inv-item-gst" onchange="updatePreview()">
            <option value="0"${gst==0?' selected':''}>0%</option>
            <option value="5"${gst==5?' selected':''}>5%</option>
            <option value="12"${gst==12?' selected':''}>12%</option>
            <option value="18"${gst==18?' selected':''}>18%</option>
            <option value="28"${gst==28?' selected':''}>28%</option>
        </select>
        <div class="inv-item-amount">₹0.00</div>
        <button class="inv-item-remove" onclick="removeInvItem(${id})" title="Remove">&times;</button>
    </div>`;
    document.getElementById('invItems').insertAdjacentHTML('beforeend', html);
    updatePreview();
}

function removeInvItem(id) {
    const el = document.querySelector(`.inv-gen-item[data-id="${id}"]`);
    if (el) el.remove();
    updatePreview();
}

function getState() {
    return document.getElementById('sellerState').value;
}

function isInterState() {
    return document.getElementById('sellerState').value !== document.getElementById('buyerState').value;
}

function updatePreview() {
    const sellerName = document.getElementById('sellerName').value || 'Your Business Name';
    const sellerAddr = document.getElementById('sellerAddress').value || 'Address, City, State';
    const sellerPhone = document.getElementById('sellerPhone').value || '—';
    const sellerGSTIN = document.getElementById('sellerGSTIN').value || '—';
    const buyerName = document.getElementById('buyerName').value || 'Customer Name';
    const buyerAddr = document.getElementById('buyerAddress').value || 'Address';
    const buyerPhone = document.getElementById('buyerPhone').value || '—';
    const buyerGSTIN = document.getElementById('buyerGSTIN').value || '—';
    const invNo = document.getElementById('invoiceNo').value || '—';
    const invDate = document.getElementById('invoiceDate').value || '—';
    const dueDate = document.getElementById('dueDate').value || '—';

    document.getElementById('prevSellerName').textContent = sellerName;
    document.getElementById('prevSellerAddr').textContent = sellerAddr;
    document.getElementById('prevSellerPhone').textContent = 'Phone: ' + sellerPhone;
    document.getElementById('prevSellerGSTIN').textContent = 'GSTIN: ' + sellerGSTIN;
    document.getElementById('prevBuyerName').textContent = buyerName;
    document.getElementById('prevBuyerAddr').textContent = buyerAddr;
    document.getElementById('prevBuyerPhone').textContent = 'Phone: ' + buyerPhone;
    document.getElementById('prevBuyerGSTIN').textContent = 'GSTIN: ' + buyerGSTIN;
    document.getElementById('prevInvNo').textContent = invNo;
    document.getElementById('prevInvDate').textContent = invDate;
    document.getElementById('prevDueDate').textContent = dueDate;
    document.getElementById('prevShipAddr').textContent = buyerAddr;

    const interState = isInterState();
    let subtotal = 0, totalCGST = 0, totalSGST = 0, totalIGST = 0;
    const rows = [];
    let idx = 1;

    document.querySelectorAll('.inv-gen-item').forEach(item => {
        const name = item.querySelector('.inv-item-name').value || 'Item';
        const hsn = item.querySelector('.inv-item-hsn').value || '—';
        const qty = parseFloat(item.querySelector('.inv-item-qty').value) || 0;
        const rate = parseFloat(item.querySelector('.inv-item-rate').value) || 0;
        const gstRate = parseFloat(item.querySelector('.inv-item-gst').value) || 0;
        const taxable = qty * rate;
        const gstAmt = taxable * gstRate / 100;
        const total = taxable + gstAmt;

        item.querySelector('.inv-item-amount').textContent = '₹' + total.toFixed(2);

        subtotal += taxable;
        if (interState) {
            totalIGST += gstAmt;
        } else {
            totalCGST += gstAmt / 2;
            totalSGST += gstAmt / 2;
        }

        rows.push(`<tr>
            <td>${idx++}</td>
            <td>${name}</td>
            <td>${hsn}</td>
            <td>${qty}</td>
            <td>₹${rate.toFixed(2)}</td>
            <td>${gstRate}%</td>
            <td>₹${total.toFixed(2)}</td>
        </tr>`);
    });

    document.getElementById('prevItems').innerHTML = rows.join('');
    document.getElementById('prevSubtotal').textContent = '₹' + subtotal.toFixed(2);

    if (interState) {
        document.getElementById('prevCGSTRow').style.display = 'none';
        document.getElementById('prevSGSTRow').style.display = 'none';
        document.getElementById('prevIGSTRow').style.display = '';
        document.getElementById('prevIGST').textContent = '₹' + totalIGST.toFixed(2);
    } else {
        document.getElementById('prevCGSTRow').style.display = '';
        document.getElementById('prevSGSTRow').style.display = '';
        document.getElementById('prevIGSTRow').style.display = 'none';
        document.getElementById('prevCGST').textContent = '₹' + totalCGST.toFixed(2);
        document.getElementById('prevSGST').textContent = '₹' + totalSGST.toFixed(2);
    }

    const grand = subtotal + totalCGST + totalSGST + totalIGST;
    document.getElementById('prevGrand').textContent = '₹' + grand.toFixed(2);
    document.getElementById('prevWords').textContent = numberToWords(grand) + ' Only';

    const notes = document.getElementById('invNotes').value;
    document.getElementById('prevNotesSection').style.display = notes ? '' : 'none';
    document.getElementById('prevNotes').textContent = notes;
}

function numberToWords(num) {
    if (num === 0) return 'Zero';
    const ones = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
    const tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
    const convert = (n) => {
        if (n < 20) return ones[n];
        if (n < 100) return tens[Math.floor(n/10)] + (n%10 ? ' ' + ones[n%10] : '');
        if (n < 1000) return ones[Math.floor(n/100)] + ' Hundred' + (n%100 ? ' and ' + convert(n%100) : '');
        if (n < 100000) return convert(Math.floor(n/1000)) + ' Thousand' + (n%1000 ? ' ' + convert(n%1000) : '');
        if (n < 10000000) return convert(Math.floor(n/100000)) + ' Lakh' + (n%100000 ? ' ' + convert(n%100000) : '');
        return convert(Math.floor(n/10000000)) + ' Crore' + (n%10000000 ? ' ' + convert(n%10000000) : '');
    };
    const intPart = Math.floor(num);
    const decPart = Math.round((num - intPart) * 100);
    let result = convert(intPart);
    if (decPart > 0) result += ' and ' + convert(decPart) + ' Paise';
    return result;
}

function previewInvoice() {
    updatePreview();
    document.getElementById('invPreview').scrollIntoView({ behavior: 'smooth' });
}

function downloadPDF() {
    updatePreview();
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    const w = doc.internal.pageSize.getWidth();
    const margin = 15;
    let y = margin;

    // Header
    doc.setFillColor(37, 99, 235);
    doc.rect(0, 0, w, 35, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(18);
    doc.setFont('helvetica', 'bold');
    doc.text(document.getElementById('sellerName').value || 'Your Business', margin, 16);
    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    doc.text(document.getElementById('sellerAddress').value || '', margin, 22);
    doc.text('Phone: ' + (document.getElementById('sellerPhone').value || '—'), margin, 27);
    doc.text('GSTIN: ' + (document.getElementById('sellerGSTIN').value || '—'), margin, 32);

    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('TAX INVOICE', w - margin, 16, { align: 'right' });
    doc.setFontSize(8);
    doc.setFont('helvetica', 'normal');
    doc.text('Invoice #: ' + (document.getElementById('invoiceNo').value || '—'), w - margin, 22, { align: 'right' });
    doc.text('Date: ' + (document.getElementById('invoiceDate').value || '—'), w - margin, 27, { align: 'right' });
    doc.text('Due: ' + (document.getElementById('dueDate').value || '—'), w - margin, 32, { align: 'right' });

    y = 42;

    // Parties
    doc.setTextColor(0, 0, 0);
    doc.setFontSize(9);
    doc.setFont('helvetica', 'bold');
    doc.text('Bill To:', margin, y);
    doc.text('Ship To:', w / 2 + 5, y);
    y += 5;
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(8);
    doc.text(document.getElementById('buyerName').value || 'Customer', margin, y);
    doc.text(document.getElementById('buyerAddress').value || '', w / 2 + 5, y);
    y += 4;
    doc.text('Phone: ' + (document.getElementById('buyerPhone').value || '—'), margin, y);
    y += 4;
    doc.text('GSTIN: ' + (document.getElementById('buyerGSTIN').value || '—'), margin, y);
    doc.text('Same as above', w / 2 + 5, y);

    y += 8;

    // Items table
    const items = [];
    let subtotal = 0, totalCGST = 0, totalSGST = 0, totalIGST = 0;
    const interState = isInterState();
    let idx = 1;

    document.querySelectorAll('.inv-gen-item').forEach(item => {
        const name = item.querySelector('.inv-item-name').value || 'Item';
        const hsn = item.querySelector('.inv-item-hsn').value || '—';
        const qty = parseFloat(item.querySelector('.inv-item-qty').value) || 0;
        const rate = parseFloat(item.querySelector('.inv-item-rate').value) || 0;
        const gstRate = parseFloat(item.querySelector('.inv-item-gst').value) || 0;
        const taxable = qty * rate;
        const gstAmt = taxable * gstRate / 100;
        const total = taxable + gstAmt;

        subtotal += taxable;
        if (interState) { totalIGST += gstAmt; } else { totalCGST += gstAmt / 2; totalSGST += gstAmt / 2; }

        items.push([idx++, name, hsn, qty, '₹' + rate.toFixed(2), gstRate + '%', '₹' + total.toFixed(2)]);
    });

    doc.autoTable({
        startY: y,
        head: [['#', 'Item', 'HSN', 'Qty', 'Rate', 'GST', 'Amount']],
        body: items,
        theme: 'grid',
        styles: { fontSize: 8, cellPadding: 2 },
        headStyles: { fillColor: [37, 99, 235], textColor: 255, fontStyle: 'bold' },
        columnStyles: { 0: { cellWidth: 8 }, 3: { cellWidth: 12 }, 4: { cellWidth: 22 }, 5: { cellWidth: 15 }, 6: { cellWidth: 25 } },
        margin: { left: margin, right: margin }
    });

    y = doc.lastAutoTable.finalY + 6;

    // Totals
    const totalsX = w - margin - 55;
    doc.setFontSize(8);
    doc.setFont('helvetica', 'normal');
    doc.text('Subtotal:', totalsX, y); doc.text('₹' + subtotal.toFixed(2), w - margin, y, { align: 'right' }); y += 5;

    if (interState) {
        doc.text('IGST:', totalsX, y); doc.text('₹' + totalIGST.toFixed(2), w - margin, y, { align: 'right' }); y += 5;
    } else {
        doc.text('CGST:', totalsX, y); doc.text('₹' + totalCGST.toFixed(2), w - margin, y, { align: 'right' }); y += 5;
        doc.text('SGST:', totalsX, y); doc.text('₹' + totalSGST.toFixed(2), w - margin, y, { align: 'right' }); y += 5;
    }

    const grand = subtotal + totalCGST + totalSGST + totalIGST;
    doc.setDrawColor(37, 99, 235);
    doc.line(totalsX, y, w - margin, y); y += 5;
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10);
    doc.text('Grand Total:', totalsX, y); doc.text('₹' + grand.toFixed(2), w - margin, y, { align: 'right' }); y += 7;

    // Amount in words
    doc.setFontSize(8);
    doc.setFont('helvetica', 'normal');
    doc.text('Amount in Words: ' + numberToWords(grand) + ' Only', margin, y); y += 8;

    // Notes
    const notes = document.getElementById('invNotes').value;
    if (notes) {
        doc.setFont('helvetica', 'bold');
        doc.text('Notes:', margin, y); y += 4;
        doc.setFont('helvetica', 'normal');
        const noteLines = doc.splitTextToSize(notes, w - 2 * margin);
        doc.text(noteLines, margin, y); y += noteLines.length * 4 + 4;
    }

    // Footer
    doc.setDrawColor(200, 200, 200);
    doc.line(margin, y, w - margin, y); y += 5;
    doc.setFontSize(7);
    doc.setTextColor(128, 128, 128);
    doc.text('Generated by My Metaji — Free GST Billing Software', w / 2, y, { align: 'center' });

    doc.save((document.getElementById('invoiceNo').value || 'invoice') + '.pdf');
}

// Init
document.getElementById('invoiceDate').value = new Date().toISOString().split('T')[0];
addInvItem('', '', 1, 0, 18);

// Live update on input changes
document.querySelectorAll('#sellerName, #sellerAddress, #sellerPhone, #sellerGSTIN, #sellerState, #buyerName, #buyerAddress, #buyerPhone, #buyerGSTIN, #buyerState, #invoiceNo, #invoiceDate, #dueDate, #placeOfSupply, #invNotes').forEach(el => {
    el.addEventListener('input', updatePreview);
    el.addEventListener('change', updatePreview);
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
