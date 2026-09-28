<?php
/**
 * My Metaji - Bill Book Format
 * A running sequential register of all bills (GST / Cash Book / Retail POS / Daily Summary)
 * built from the bills you add, with a live printable register sheet and PDF export.
 */
require_once __DIR__ . '/config.php';
$page_title = 'Free Bill Book Format — Bill Register & Cash Book | ' . BRAND_NAME;
$page_desc  = 'Create a free bill book format online. Add every bill to a running register and print a GST bill book, cash book, retail POS bill book or daily summary sheet as PDF.';
include __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="gst-hero">
    <div class="gst-wrap">
        <div class="gst-hero-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"/></svg>
            Sequential register &middot; No sign-up required
        </div>
        <h1>Free <span>Bill Book Format</span></h1>
        <p>A bill book is not a single bill &mdash; it is the running register of every bill you raised. Add each bill, watch the serial, the totals and the outstanding update live, then print the whole register as one sheet.</p>
        <div class="gst-hero-chips">
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 4h16v16H4z"/><path d="M4 9h16M9 4v16"/></svg>
                4 register formats
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
                Live running totals
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 3v12"/><path d="M7 11l5 5 5-5"/><path d="M4 21h16"/></svg>
                A4 register PDF
            </span>
        </div>
    </div>
</section>

<!-- ===================== TOOL ===================== -->
<section class="gst-tool" id="bill-book-format-tool">
    <div class="gst-wrap">
        <div class="qm-layout">

            <!-- ===== LEFT: FORM ===== -->
            <div>

                <!-- 1. FORMAT PICKER -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M4 9h16M9 4v16"/></svg>
                        </div>
                        <div>
                            <h3>Choose Your Bill Book Format</h3>
                            <p>The register you keep decides which columns the sheet carries.</p>
                        </div>
                        <span class="qm-step">Step 1</span>
                    </div>

                    <div class="fmt-grid" id="fmtGrid" role="group" aria-label="Bill book format"></div>

                    <div class="note-box" style="margin-top:16px;">
                        <div class="note-ico" id="fmtInfoIco">&#128214;</div>
                        <div>
                            <h4 id="fmtInfoTitle">GST Bill Book</h4>
                            <p id="fmtInfoDesc">The statutory register of sales invoices. Shows the party GSTIN and the tax split so the sheet ties up with your GST return.</p>
                            <p style="margin:0;"><strong>Columns:</strong> <span id="fmtInfoFields">Serial, Bill No., Date, Party Name, GSTIN, Taxable Value, Tax, Amount</span></p>
                        </div>
                    </div>

                    <div class="doc-split" style="margin-top:18px;">
                        <div>
                            <div class="doc-opt">
                                <span>Accent colour</span>
                                <div class="doc-swatches">
                                    <button type="button" class="doc-swatch active" data-bill-accent="blue" style="background:#2563EB" aria-label="Blue"></button>
                                    <button type="button" class="doc-swatch" data-bill-accent="indigo" style="background:#4F46E5" aria-label="Indigo"></button>
                                    <button type="button" class="doc-swatch" data-bill-accent="green" style="background:#059669" aria-label="Green"></button>
                                    <button type="button" class="doc-swatch" data-bill-accent="teal" style="background:#0D9488" aria-label="Teal"></button>
                                    <button type="button" class="doc-swatch" data-bill-accent="amber" style="background:#D97706" aria-label="Amber"></button>
                                    <button type="button" class="doc-swatch" data-bill-accent="red" style="background:#DC2626" aria-label="Red"></button>
                                    <button type="button" class="doc-swatch" data-bill-accent="slate" style="background:#334155" aria-label="Slate"></button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="doc-switches">
                                <label class="doc-chk"><input type="checkbox" id="optBlank" checked> Blank carry-forward rows</label>
                                <label class="doc-chk"><input type="checkbox" id="optTotals" checked> Subtotal &amp; grand total</label>
                                <label class="doc-chk"><input type="checkbox" id="optSign"> Signature block</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. SHOP + PERIOD -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14"/><path d="M9 11h6M9 15h6"/></svg>
                        </div>
                        <div>
                            <h3>Shop &amp; Register Period</h3>
                            <p>The page header of every printed bill book page.</p>
                        </div>
                        <span class="qm-step">Step 2</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Shop / Business Name</label><input type="text" id="shName" placeholder="As printed on your bills"></div>
                        <div class="inv-gen-field"><label>Phone</label><input type="tel" id="shPhone" placeholder="98765 43210"></div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Address</label><input type="text" id="shAddr" placeholder="Street, city, state, PIN"></div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>GSTIN (optional)</label><input type="text" id="shGst" placeholder="24AAAAA0000A1Z5" maxlength="15"></div>
                        <div class="inv-gen-field"><label>Opening Balance (₹)<span id="openBalLbl" style="display:none;"> &mdash; cash book</span></label><input type="number" id="openBal" value="0" step="0.01" oninput="billUpdate()"></div>
                    </div>
                    <div class="doc-split" style="margin-top:10px;">
                        <div class="inv-gen-field"><label>Period From</label><input type="date" id="perFrom"></div>
                        <div class="inv-gen-field"><label>Period To</label><input type="date" id="perTo"></div>
                    </div>
                </div>

                <!-- 3. ADD BILL -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                        </div>
                        <div>
                            <h3>Add a Bill</h3>
                            <p>One line per bill raised. The register numbers itself as you go.</p>
                        </div>
                        <span class="qm-step">Step 3</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Bill Number</label><input type="text" id="bNo" placeholder="INV-1043"></div>
                        <div class="inv-gen-field"><label>Bill Date</label><input type="date" id="bDate"></div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Customer / Party Name</label><input type="text" id="bParty" placeholder="Party name as printed on the bill"></div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Bill Amount (₹)</label><input type="number" id="bAmt" value="0" min="0" step="0.01" oninput="billUpdate()"></div>
                        <div class="inv-gen-field"><label>Payment Mode</label>
                            <select id="bMode">
                                <option>Cash</option>
                                <option>UPI</option>
                                <option>Card</option>
                                <option>Bank Transfer</option>
                                <option>Cheque</option>
                                <option>Credit</option>
                            </select>
                        </div>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Payment Status</label>
                            <select id="bStatus">
                                <option>Paid</option>
                                <option>Part Paid</option>
                                <option>Unpaid</option>
                                <option>Cancelled</option>
                            </select>
                        </div>
                        <div class="inv-gen-field" id="bGstRow"><label>Party GSTIN</label><input type="text" id="bGstin" placeholder="27BBBBB0000B1Z5" maxlength="15"></div>
                    </div>
                    <div class="doc-split" id="bTaxRow" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Taxable Value (₹)</label><input type="number" id="bTaxable" value="0" min="0" step="0.01" oninput="billUpdate()"></div>
                        <div class="inv-gen-field"><label>GST / Tax Amount (₹)</label><input type="number" id="bTax" value="0" min="0" step="0.01" oninput="billUpdate()"></div>
                    </div>
                    <div class="doc-split" id="bRetailRow" style="margin-bottom:10px; display:none;">
                        <div class="inv-gen-field"><label>Time</label><input type="time" id="bTime"></div>
                        <div class="inv-gen-field"><label>Items / Description</label><input type="text" id="bItems" placeholder="e.g. 4 items &mdash; Rice 5kg"></div>
                    </div>
                    <div class="doc-split" id="bOutRow" style="display:none;">
                        <div class="inv-gen-field"><label>Amount Out / Payment (₹)</label><input type="number" id="bOut" value="0" min="0" step="0.01" oninput="billUpdate()"></div>
                        <div class="inv-gen-field"><label>Against Bill No. (Cash Book)</label><input type="text" id="bRcpt" placeholder="Payment receipt / voucher no."></div>
                    </div>
                    <button type="button" class="inv-gen-add-item" onclick="billAddRow()">+ Add This Bill to the Bill Book</button>
                </div>

                <!-- 4. REGISTER LIST -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                        </div>
                        <div>
                            <h3>Registered Bills</h3>
                            <p>Edit any cell and the register sheet, totals and preview re-render instantly.</p>
                        </div>
                        <span class="qm-step">Step 4</span>
                    </div>

                    <div class="doc-stats" style="margin-bottom:16px;">
                        <div class="doc-stat"><span>Bill Count</span><strong id="stCount">0</strong></div>
                        <div class="doc-stat"><span>Total Billed</span><strong id="stBilled">₹0.00</strong></div>
                        <div class="doc-stat"><span>Total Collected</span><strong id="stCollected">₹0.00</strong></div>
                        <div class="doc-stat amber"><span>Outstanding</span><strong id="stDue">₹0.00</strong></div>
                    </div>

                    <div class="doc-items" id="billItems" style="--doc-cols: 26px 1fr 1fr 1.6fr 1fr 0.9fr 0.95fr auto">
                        <div class="doc-item-hd">
                            <span>#</span><span>Bill No.</span><span>Date</span><span>Party Name</span><span style="text-align:right">Amount</span><span>Mode</span><span>Status</span><span></span>
                        </div>
                    </div>
                    <button type="button" class="inv-gen-add-item" onclick="billClear()">Clear All Bills</button>
                </div>

                <!-- ACTIONS -->
                <div class="qm-actions doc-no-print">
                    <button type="button" class="qm-btn qm-btn-primary" onclick="billPreviewJump()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        Preview
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="billPrint()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="1"/><path d="M6 17h12v4H6z"/></svg>
                        Print
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="billReset()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                        Reset
                    </button>
                    <button type="button" class="qm-btn qm-btn-primary" style="flex:2" onclick="billPDF()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="M7 11l5 5 5-5"/><path d="M4 21h16"/></svg>
                        Download PDF
                    </button>
                </div>
            </div>

            <!-- ===== RIGHT: LIVE PREVIEW ===== -->
            <div class="qm-preview-card">
                <div class="qm-preview-head">
                    <h3><span class="qm-live-dot"></span> Live Preview</h3>
                    <span class="qm-preview-hint">A4 &middot; <?= BRAND_NAME ?></span>
                </div>
                <div class="inv-gen-preview" id="billPreview">
                    <div class="doc-sheet doc-sheet-lg" id="billSheet">

                        <div class="doc-hd">
                            <div class="doc-org">
                                <h2 id="pShop">Your Shop</h2>
                                <p id="pShopAddr">Address</p>
                                <p><span id="pShopPhone">Phone: &mdash;</span></p>
                                <p id="pGstRow" style="display:none"><strong>GSTIN:</strong> <span id="pGst">&mdash;</span></p>
                            </div>
                            <div class="doc-id">
                                <div class="doc-title" id="pBookTitle">BILL BOOK</div>
                                <p id="pPeriod" style="font-size:9.5px;">Period: &mdash;</p>
                                <p id="pOpenRow" style="display:none"><strong>Opening:</strong> <span id="pOpen">&mdash;</span></p>
                            </div>
                        </div>

                        <div style="overflow-x:auto;">
                            <table class="bs-table" id="pTable" style="margin-top:4px; font-size:10px; min-width:300px;">
                                <thead><tr id="pHead"></tr></thead>
                                <tbody id="pRows"></tbody>
                            </table>
                        </div>

                        <div class="doc-sig" id="pSig" style="display:none;">
                            <div><div class="doc-sig-line">Prepared by</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Signature</div></div>
                            <div><div class="doc-sig-line">Checked by</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Signature</div></div>
                            <div><div class="doc-sig-line" id="pSigShop">For &mdash;</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Proprietor</div></div>
                        </div>
                        <div class="doc-ft">This is a computer-generated register &middot; Generated by <strong>My Metaji</strong></div>
                    </div>
                </div>
                <div class="qm-total-strip">
                    <span id="pStripLbl">Total Billed</span>
                    <strong id="pStripTotal">₹0.00</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== FORMAT COMPARISON ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>Which Bill Book Format Do You Need?</h2>
        <p>The bill book is the register, not the bill. Rule 36 of the GST Rules wants invoices preserved in order, and every other register &mdash; cash book, POS summary, daily sales &mdash; is a different cut of the same data.</p>
        <div class="fmt-table-wrap" style="margin-top:20px;">
            <table class="fmt-table">
                <thead>
                    <tr><th>Format</th><th>Who keeps it</th><th>Key columns</th><th>What it proves</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>GST Bill Book</strong></td>
                        <td>Any GST-registered dealer, trader or distributor</td>
                        <td>Serial, Bill No., Date, Party, GSTIN, Taxable, Tax, Amount</td>
                        <td><span class="fmt-req yes">B2B / B2C series</span> <span class="fmt-req yes">GSTIN of buyer</span> <span class="fmt-req cond">Tax breakup</span></td>
                    </tr>
                    <tr>
                        <td><strong>Simple / Cash Book</strong></td>
                        <td>Retailers, kirana stores, service firms, clubs</td>
                        <td>Date, Receipt No., Party, Amount In, Amount Out, Balance</td>
                        <td><span class="fmt-req yes">Running balance</span> <span class="fmt-req yes">Opening balance</span></td>
                    </tr>
                    <tr>
                        <td><strong>Retail / POS Bill Book</strong></td>
                        <td>Shops billing on a counter all day</td>
                        <td>Serial, Bill No., Time, Items, Amount, Payment Mode</td>
                        <td><span class="fmt-req yes">Unbroken daily series</span> <span class="fmt-req cond">Time stamp</span></td>
                    </tr>
                    <tr>
                        <td><strong>Daily Summary Bill Book</strong></td>
                        <td>Multi-counter stores, franchises, daily reconciliations</td>
                        <td>Date, No. of Bills, Total Billed, Cash, UPI, Card, Other</td>
                        <td><span class="fmt-req yes">Day-wise totals</span> <span class="fmt-req cond">Mode split</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="note-box gold" style="margin-top:20px;">
            <div class="note-ico">&#128214;</div>
            <div>
                <h4>Bill book mistakes that invite trouble</h4>
                <ul>
                    <li>Starting a fresh series mid-year &mdash; invoice numbers must run continuously; a gap is the first thing an officer asks about.</li>
                    <li>Reusing a cancelled serial &mdash; mark it cancelled and strike it through, never delete and re-issue.</li>
                    <li>No party GSTIN on B2B entries &mdash; the buyer loses input tax credit and you lose the sale.</li>
                    <li>Editing an old page instead of raising a credit note &mdash; the original entry must stay exactly as it was issued.</li>
                    <li>Keeping the register only on a computer with no print &mdash; invoices must be preserved for at least 72 months from the due date of the annual return.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===================== HOW TO ===================== -->
<section class="gst-faq" style="background:#fff;">
    <div class="gst-wrap">
        <h2>How to Make a Bill Book</h2>
        <div class="gst-how-grid">
            <div class="gst-how-card">
                <div class="gst-how-num">1</div>
                <h3>Pick the Register</h3>
                <p>Choose GST bill book, cash book, retail POS bill book or daily summary. The columns of the register sheet change instantly.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">2</div>
                <h3>Set Shop &amp; Period</h3>
                <p>Enter your shop name, GSTIN and the from-to dates the register covers. An opening balance is available for the cash book.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">3</div>
                <h3>Add Every Bill</h3>
                <p>Type each bill with its number, date, party, amount, mode and status. The serial, subtotals and outstanding update as you type.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">4</div>
                <h3>Print the Register</h3>
                <p>Review the A4 register sheet with its blank carry-forward rows, then download a print-ready PDF for your files.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SEO + RELATED ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>What is a Bill Book Format?</h2>
        <p>A <strong>bill book format</strong> is the ruled register in which a business records every bill it raises, in one continuous running series. It is not a bill &mdash; a bill is a single transaction document. The bill book is the book of records that sits behind all of them, and it exists for three reasons: to prove the invoice series has no gaps, to let any single entry be traced back to its invoice, and to let the day's sales be totalled without re-reading every bill. A standard bill book carries a running serial number, the invoice or bill number, the date, the name of the buying party, the amount, and the payment status. A <strong>GST bill book</strong> adds the party GSTIN and the taxable value and tax split, so the register can be compared line by line against the GSTR-1 and GSTR-3B filed with the department. That reconciliation is the single strongest defence in a GST audit.</p>
        <p>Beyond GST, the same idea takes other shapes. A <strong>cash book</strong> is a register of money coming in and money going out with a running balance, and it is what a small retailer actually reconciles at the end of the day. A <strong>retail or POS bill book</strong> adds a timestamp and a payment mode to every line so that the counter sales of the day can be split between cash, UPI and card. A <strong>daily summary bill book</strong> collapses the day into one line per date with the number of bills and the total value &mdash; useful for a multi-counter store or a franchise that needs one number per day. Rule 36 of the CGST Rules requires every invoice to be preserved in order for at least <strong>72 months</strong> from the due date of the annual return in which it is reported, which is why a properly numbered, tamper-evident register is not just good practice but a legal requirement.</p>

        <h2 style="margin-top:32px;">Explore Other Document Formats</h2>
        <p>Every business document has its own legal shape. Build the rest of your paperwork with the same free, instant-PDF approach.</p>
        <div class="rel-grid" style="margin-top:20px;">
            <a class="rel-card" href="quotation-format.php"><span class="rel-ico">&#128203;</span><div><strong>Quotation Format</strong><span>Pre-deal estimate with validity</span></div></a>
            <a class="rel-card" href="receipt-format.php"><span class="rel-ico">&#129534;</span><div><strong>Receipt Format</strong><span>Proof of payment received</span></div></a>
            <a class="rel-card" href="bill-book-format.php"><span class="rel-ico">&#128214;</span><div><strong>Bill Book Format</strong><span>Sequential register of all bills</span></div></a>
            <a class="rel-card" href="estimate-format.php"><span class="rel-ico">&#128202;</span><div><strong>Estimate Format</strong><span>Ballpark cost before confirmation</span></div></a>
            <a class="rel-card" href="credit-note.php"><span class="rel-ico">&#128221;</span><div><strong>Credit Note</strong><span>Reduce a previously issued invoice</span></div></a>
            <a class="rel-card" href="debit-note.php"><span class="rel-ico">&#128223;</span><div><strong>Debit Note</strong><span>Add charges to a customer</span></div></a>
            <a class="rel-card" href="delivery-challan.php"><span class="rel-ico">&#128666;</span><div><strong>Delivery Challan</strong><span>Goods dispatch note &amp; e-way bill base</span></div></a>
            <a class="rel-card" href="balance-sheet.php"><span class="rel-ico">&#128202;</span><div><strong>Balance Sheet</strong><span>Assets, liabilities &amp; equity snapshot</span></div></a>
            <a class="rel-card" href="work-order.php"><span class="rel-ico">&#128221;</span><div><strong>Work Order</strong><span>Job card for manufacturing &amp; service</span></div></a>
        </div>
    </div>
</section>

<!-- ===================== CTA ===================== -->
<section class="gst-cta">
    <div class="gst-wrap">
        <h2>Your Bill Book, Maintained Automatically</h2>
        <p><?= BRAND_NAME ?> numbers every invoice continuously, builds the register for you, and files your bills and GST returns without a single re-typed figure.</p>
        <a href="#download" class="gst-cta-btn">Download <?= BRAND_NAME ?> Free &rarr;</a>
    </div>
</section>

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="assets/js/doc-utils.js"></script>
<script>
/* ---------- Format definitions ---------- */
const BILL_FORMATS = {
    gst: {
        label: 'GST Bill Book', tag: 'Sales invoice register', tags: ['GSTIN', 'Tax breakup', 'GSTR-1 ready'],
        ico: '\u{1F4C4}', accent: '#2563EB',
        desc: 'The statutory register of sales invoices. Shows the party GSTIN and the tax split so the sheet ties up with your GST return.',
        fields: 'Serial, Bill No., Date, Party Name, GSTIN, Taxable Value, Tax, Amount',
        title: 'BILL BOOK \u2014 GST', strip: 'Total Billed', grandLbl: 'Grand Total',
        head: ['#', 'Bill No.', 'Date', 'Party Name', 'GSTIN', 'Taxable', 'Tax', 'Amount'],
        numCols: [5, 6, 7], gstin: true
    },
    simple: {
        label: 'Simple / Cash Book', tag: 'Money in &amp; out', tags: ['Running balance', 'Opening balance', 'Daily close'],
        ico: '\u{1F4B0}', accent: '#059669',
        desc: 'A money register. Every receipt in, every payment out, with a running balance you can carry forward to the next page.',
        fields: 'Serial, Date, Receipt No., Party, Amount In, Amount Out, Balance',
        title: 'CASH BOOK', strip: 'Total In', grandLbl: 'Closing Balance',
        head: ['#', 'Date', 'Receipt No.', 'Party', 'Amount In', 'Amount Out', 'Balance'],
        numCols: [4, 5, 6], gstin: false
    },
    retail: {
        label: 'Retail / POS Bill Book', tag: 'Counter sales', tags: ['Time stamp', 'Payment mode', 'Daily series'],
        ico: '\u{1F6D2}', accent: '#D97706',
        desc: 'Built for a shop that bills all day. Every line carries the time, the items sold and the mode the customer paid in.',
        fields: 'Serial, Bill No., Time, Items, Amount, Payment Mode',
        title: 'BILL BOOK \u2014 RETAIL / POS', strip: 'Total Sales', grandLbl: 'Grand Total',
        head: ['#', 'Bill No.', 'Time', 'Items / Description', 'Amount', 'Payment Mode'],
        numCols: [4], gstin: false
    },
    daily: {
        label: 'Daily Summary Bill Book', tag: 'Day-wise totals', tags: ['Bill count', 'Cash / UPI / Card', 'One line per day'],
        ico: '\u{1F4C5}', accent: '#4F46E5',
        desc: 'One line per day with the number of bills and the value split across cash, UPI, card and other modes. Ideal for multi-counter shops.',
        fields: 'Date, No. of Bills, Total Billed, Cash, UPI, Card, Other',
        title: 'BILL BOOK \u2014 DAILY SUMMARY', strip: 'Total Billed', grandLbl: 'Grand Total',
        head: ['#', 'Date', 'Bills', 'Total Billed', 'Cash', 'UPI', 'Card', 'Other'],
        numCols: [2, 3, 4, 5, 6, 7], gstin: false
    }
};

const BILL_MODES = ['Cash', 'UPI', 'Card', 'Bank Transfer', 'Cheque', 'Credit'];
const BILL_STATUS = ['Paid', 'Part Paid', 'Unpaid', 'Cancelled'];

let billFmt = 'gst';
let billRowId = 0;
let billSeq = 0;

/* ---------- Format picker ---------- */
function billRenderFormats() {
    const grid = document.getElementById('fmtGrid');
    let h = '';
    Object.keys(BILL_FORMATS).forEach(k => {
        const f = BILL_FORMATS[k];
        h += `<button type="button" class="fmt-card${k === billFmt ? ' active' : ''}" data-bill-fmt="${k}" aria-pressed="${k === billFmt}">
            <span class="fmt-mini" style="--fmt-accent:${f.accent}">
                <span class="fmt-mini-top"><span class="fmt-mini-brand"></span><span class="fmt-mini-tag"></span></span>
                <span class="fmt-mini-ln w70"></span><span class="fmt-mini-ln w45"></span><span class="fmt-mini-ln w70"></span>
                <span class="fmt-mini-tot"></span>
            </span>
            <span class="fmt-card-body">
                <span class="fmt-card-title">${f.ico} ${f.label}</span>
                <span class="fmt-card-sub">${f.tag}</span>
                <span class="fmt-tags">${f.tags.map(t => `<span class="fmt-tag${k === 'simple' ? ' ok' : k === 'retail' ? ' warn' : k === 'daily' ? ' neutral' : ''}">${t}</span>`).join('')}</span>
            </span>
        </button>`;
    });
    grid.innerHTML = h;
    grid.querySelectorAll('[data-bill-fmt]').forEach(b => b.addEventListener('click', () => billSetFormat(b.dataset.billFmt)));
    billSetFormat(billFmt, true);
}

function billSetFormat(k, quiet) {
    const f = BILL_FORMATS[k];
    if (!f) return;
    billFmt = k;

    document.querySelectorAll('[data-bill-fmt]').forEach(b => {
        const on = b.dataset.billFmt === k;
        b.classList.toggle('active', on);
        b.setAttribute('aria-pressed', on);
    });

    document.getElementById('fmtInfoIco').textContent = f.ico;
    document.getElementById('fmtInfoTitle').textContent = f.label;
    document.getElementById('fmtInfoDesc').textContent = f.desc;
    document.getElementById('fmtInfoFields').textContent = f.fields;
    document.getElementById('openBalLbl').textContent = k === 'simple' ? ' \u2014 cash book' : '';

    const show = (id, on) => { const e = document.getElementById(id); if (e) e.style.display = on ? '' : 'none'; };
    show('bGstRow', f.gstin);
    show('bTaxRow', f.gstin);
    show('bRetailRow', k === 'retail');
    show('bOutRow', k === 'simple');

    if (!quiet) billUpdate();
}

/* ---------- Register rows ---------- */
function billAddRow() {
    const id = billRowId++;
    const mode = DOC.val('bMode') || 'Cash';
    const status = DOC.val('bStatus') || 'Paid';
    const attrs = [
        'data-time="' + (DOC.val('bTime') || '') + '"',
        'data-items="' + (DOC.val('bItems') || '').replace(/"/g, '&quot;') + '"',
        'data-gstin="' + (DOC.val('bGstin') || '').replace(/"/g, '&quot;') + '"',
        'data-rcpt="' + (DOC.val('bRcpt') || '').replace(/"/g, '&quot;') + '"',
        'data-taxable="' + DOC.num('bTaxable') + '"',
        'data-tax="' + DOC.num('bTax') + '"',
        'data-out="' + DOC.num('bOut') + '"'
    ].join(' ');

    document.getElementById('billItems').insertAdjacentHTML('beforeend',
        `<div class="doc-item" data-row="${id}" ${attrs}>
            <span class="doc-item-amt" data-bb-serial style="text-align:left;">${++billSeq}</span>
            <input type="text" data-bb-no placeholder="INV-0001" oninput="billUpdate()">
            <input type="date" data-bb-date value="${DOC.val('bDate') || DOC.todayISO()}" onchange="billUpdate()">
            <input type="text" data-bb-party placeholder="Party name" oninput="billUpdate()">
            <input type="number" data-bb-amt value="${DOC.num('bAmt')}" min="0" step="0.01" placeholder="0" oninput="billUpdate()">
            <select data-bb-mode onchange="billUpdate()">${BILL_MODES.map(m => `<option${m === mode ? ' selected' : ''}>${m}</option>`).join('')}</select>
            <select data-bb-status onchange="billUpdate()">${BILL_STATUS.map(s => `<option${s === status ? ' selected' : ''}>${s}</option>`).join('')}</select>
            <button type="button" class="doc-item-del" onclick="billDelRow(${id})" title="Remove bill" aria-label="Remove bill">&times;</button>
        </div>`);

    ['bNo', 'bParty'].forEach(function (k) { const e = document.getElementById(k); if (e) e.value = ''; });
    document.getElementById('bAmt').value = 0;
    document.getElementById('bTaxable').value = 0;
    document.getElementById('bTax').value = 0;
    document.getElementById('bOut').value = 0;
    document.getElementById('bGstin').value = '';
    document.getElementById('bItems').value = '';
    document.getElementById('bRcpt').value = '';
    document.getElementById('bTime').value = '';
    billUpdate();
}

function billDelRow(id) {
    const e = document.querySelector(`[data-row="${id}"]`);
    if (e) e.remove();
    billRenumber();
    billUpdate();
}

function billClear() {
    document.getElementById('billItems').querySelectorAll('.doc-item').forEach(e => e.remove());
    billSeq = 0;
    billUpdate();
}

function billRenumber() {
    let n = 0;
    document.querySelectorAll('#billItems .doc-item').forEach(function (el) {
        const s = el.querySelector('[data-bb-serial]');
        if (s) s.textContent = ++n;
    });
    billSeq = n;
}

/* ---------- Collect + compute ---------- */
function billReadRows() {
    const f = BILL_FORMATS[billFmt];
    const from = DOC.val('perFrom');
    const to = DOC.val('perTo');
    const rows = [];
    let s = 0;

    document.querySelectorAll('#billItems .doc-item').forEach(function (el) {
        const g = (k) => el.querySelector('[data-bb-' + k + ']');
        const no = (g('no').value || '').trim();
        const date = g('date').value || '';
        const party = (g('party').value || '').trim();
        if (!no && !party) return;
        s += 1;
        const amt = DOC.round2(parseFloat(g('amt').value) || 0);
        rows.push({
            i: s,
            no: no, date: date, party: party,
            amt: amt,
            mode: g('mode').value,
            status: g('status').value,
            time: el.dataset.time || '',
            items: el.dataset.items || '',
            gstin: el.dataset.gstin || '',
            taxable: DOC.round2(parseFloat(el.dataset.taxable) || 0),
            tax: DOC.round2(parseFloat(el.dataset.tax) || 0),
            out: DOC.round2(parseFloat(el.dataset.out) || 0),
            rcpt: el.dataset.rcpt || ''
        });
    });

    const inRange = rows.filter(function (r) {
        if (from && r.date && r.date < from) return false;
        if (to && r.date && r.date > to) return false;
        return true;
    });
    return { f, rows: inRange, all: rows };
}

function billCollect() {
    const f = BILL_FORMATS[billFmt];
    const d = billReadRows();
    const rows = d.rows;
    const openBal = DOC.round2(DOC.num('openBal'));

    let count = 0, billed = 0, collected = 0, due = 0, inSum = 0, outSum = 0, bal = openBal, taxSum = 0, taxableSum = 0;
    const modeTot = { Cash: 0, UPI: 0, Card: 0, Other: 0 };
    const days = [];
    const dayMap = {};

    rows.forEach(function (r) {
        if (r.status === 'Cancelled') return;
        count += 1;
        billed += r.amt;
        if (r.status === 'Paid') collected += r.amt;
        else if (r.status === 'Part Paid') collected += r.amt;
        else due += r.amt;
        inSum += r.amt;
        outSum += r.out;
        bal = DOC.round2(bal + r.amt - r.out);
        taxableSum += r.taxable;
        taxSum += r.tax;
        if (modeTot[r.mode] !== undefined) modeTot[r.mode] += r.amt;
        else modeTot.Other += r.amt;

        const k = r.date || '\u2014';
        if (!dayMap[k]) { dayMap[k] = { date: k, count: 0, total: 0, cash: 0, upi: 0, card: 0, other: 0 }; days.push(dayMap[k]); }
        dayMap[k].count += 1;
        dayMap[k].total = DOC.round2(dayMap[k].total + r.amt);
        if (r.mode === 'Cash') dayMap[k].cash = DOC.round2(dayMap[k].cash + r.amt);
        else if (r.mode === 'UPI') dayMap[k].upi = DOC.round2(dayMap[k].upi + r.amt);
        else if (r.mode === 'Card') dayMap[k].card = DOC.round2(dayMap[k].card + r.amt);
        else dayMap[k].other = DOC.round2(dayMap[k].other + r.amt);
    });

    days.sort(function (a, b) { return (a.date || '').localeCompare(b.date || ''); });

    let subTotal = 0, grandTotal = 0;
    if (billFmt === 'simple') { subTotal = inSum; grandTotal = bal; }
    else if (billFmt === 'daily') { subTotal = billed; grandTotal = DOC.round2(modeTot.Cash + modeTot.UPI + modeTot.Card + modeTot.Other); }
    else { subTotal = billFmt === 'gst' ? taxableSum : billed; grandTotal = billed; }

    return {
        f, rows, days, count, billed: DOC.round2(billed), collected: DOC.round2(collected), due: DOC.round2(due),
        inSum: DOC.round2(inSum), outSum: DOC.round2(outSum), bal: DOC.round2(bal), openBal,
        taxableSum: DOC.round2(taxableSum), taxSum: DOC.round2(taxSum), modeTot,
        subTotal: DOC.round2(subTotal), grandTotal: DOC.round2(grandTotal)
    };
}

/* ---------- Preview render ---------- */
function billUpdate() {
    const d = billCollect();
    const f = d.f;

    DOC.text('pShop', DOC.val('shName'));
    DOC.text('pShopAddr', DOC.val('shAddr'));
    DOC.text('pShopPhone', 'Phone: ' + (DOC.val('shPhone') || '\u2014'));
    DOC.showIf('pGstRow', !!DOC.val('shGst'));
    if (DOC.val('shGst')) document.getElementById('pGst').textContent = DOC.val('shGst');

    document.getElementById('pBookTitle').textContent = f.title;
    const fr = DOC.fmtDate(DOC.val('perFrom')) || '\u2014';
    const to = DOC.fmtDate(DOC.val('perTo')) || '\u2014';
    document.getElementById('pPeriod').textContent = 'Period: ' + fr + ' \u2013 ' + to;
    DOC.showIf('pOpenRow', billFmt === 'simple');
    if (billFmt === 'simple') document.getElementById('pOpen').textContent = DOC.money(d.openBal);

    /* header row */
    const head = document.getElementById('pHead');
    const cols = f.head.length;
    if (head.children.length !== cols) {
        let h = '';
        f.head.forEach(function (t, i) {
            h += '<th' + (f.numCols.indexOf(i) >= 0 ? ' class="num"' : '') + '>' + t + '</th>';
        });
        head.innerHTML = h;
    }

    /* body */
    const blankRows = document.getElementById('optBlank').checked;
    let h = '';

    if (!d.rows.length) {
        h = '<tr><td colspan="' + cols + '" style="padding:14px;text-align:center;color:#94A3B8;">No bills registered yet \u2014 add your first bill on the left</td></tr>';
    } else if (billFmt === 'simple') {
        let bal = d.openBal;
        h = d.rows.map(function (r) {
            if (r.status !== 'Cancelled') bal = DOC.round2(bal + r.amt - r.out);
            return '<tr><td>' + r.i + '</td><td>' + (DOC.fmtDate(r.date) || '\u2014') + '</td><td>' + (r.rcpt || r.no || '\u2014') +
                '</td><td>' + (r.party || '\u2014') + '</td><td class="num">' + DOC.money(r.amt) +
                '</td><td class="num">' + (r.out ? DOC.money(r.out) : '\u2014') + '</td><td class="num">' + DOC.money(bal) + '</td></tr>';
        }).join('');
    } else if (billFmt === 'gst') {
        h = d.rows.map(function (r) {
            return '<tr><td>' + r.i + '</td><td>' + (r.no || '\u2014') + '</td><td>' + (DOC.fmtDate(r.date) || '\u2014') +
                '</td><td>' + (r.party || '\u2014') + '</td><td>' + (r.gstin || '\u2014') +
                '</td><td class="num">' + DOC.money(r.taxable) + '</td><td class="num">' + DOC.money(r.tax) +
                '</td><td class="num"><strong>' + DOC.money(r.amt) + '</strong></td></tr>';
        }).join('');
    } else if (billFmt === 'retail') {
        h = d.rows.map(function (r) {
            return '<tr><td>' + r.i + '</td><td>' + (r.no || '\u2014') + '</td><td>' + (r.time || '\u2014') +
                '</td><td>' + (r.items || '\u2014') + '</td><td class="num"><strong>' + DOC.money(r.amt) +
                '</strong></td><td>' + r.mode + '</td></tr>';
        }).join('');
    } else {
        h = d.days.map(function (day, i) {
            return '<tr><td>' + (i + 1) + '</td><td>' + (DOC.fmtDate(day.date) || '\u2014') + '</td><td class="num">' + day.count +
                '</td><td class="num"><strong>' + DOC.money(day.total) + '</strong></td><td class="num">' + DOC.money(day.cash) +
                '</td><td class="num">' + DOC.money(day.upi) + '</td><td class="num">' + DOC.money(day.card) +
                '</td><td class="num">' + DOC.money(day.other) + '</td></tr>';
        }).join('');
    }

    if (document.getElementById('optTotals').checked && d.rows.length) {
        h += '<tr class="bs-sub"><td colspan="' + cols + '" style="text-align:right;">Sub Total \u2014 ' + d.count + ' bills: ' + DOC.money(d.subTotal) + '</td></tr>';
        h += '<tr class="bs-total"><td colspan="' + cols + '" style="text-align:right;">' + f.grandLbl + ': ' + DOC.money(d.grandTotal) + '</td></tr>';
    }

    if (blankRows) {
        for (let i = 0; i < 6; i++) {
            h += '<tr><td>&nbsp;</td>' + Array(cols - 1).join('<td>&nbsp;</td>');
            h += '<td>&nbsp;</td></tr>';
        }
        h += '<tr><td colspan="' + cols + '" style="font-size:8.5px;color:#94A3B8;padding-top:6px;">Carry forward \u2014 use the blank rows above to continue the register on the next page.</td></tr>';
    }

    document.getElementById('pRows').innerHTML = h;

    /* stats */
    document.getElementById('stCount').textContent = String(d.count);
    document.getElementById('stBilled').textContent = DOC.money(d.billed);
    document.getElementById('stCollected').textContent = DOC.money(d.collected);
    document.getElementById('stDue').textContent = DOC.money(d.due);

    document.getElementById('pStripLbl').textContent = f.strip;
    document.getElementById('pStripTotal').textContent = DOC.money(billFmt === 'simple' ? d.bal : d.billed);

    DOC.showIf('pSig', document.getElementById('optSign').checked);
    if (document.getElementById('optSign').checked) {
        document.getElementById('pSigShop').textContent = 'For ' + (DOC.val('shName') || '\u2014');
    }
}

function billPreviewJump() { billUpdate(); DOC.scrollToPreview('billPreview'); }
function billPrint() { billUpdate(); window.print(); }

function billReset() {
    ['shName', 'shAddr', 'shPhone', 'shGst', 'bNo', 'bParty'].forEach(id => {
        const e = document.getElementById(id); if (e) e.value = '';
    });
    ['bAmt', 'bTaxable', 'bTax', 'bOut', 'openBal'].forEach(id => {
        const e = document.getElementById(id); if (e) e.value = 0;
    });
    document.getElementById('bGstin').value = '';
    document.getElementById('bItems').value = '';
    document.getElementById('bRcpt').value = '';
    document.getElementById('bTime').value = '';
    document.getElementById('bDate').value = DOC.todayISO();
    document.getElementById('perFrom').value = DOC.todayISO().slice(0, 8) + '01';
    document.getElementById('perTo').value = DOC.todayISO();
    document.getElementById('bMode').selectedIndex = 0;
    document.getElementById('bStatus').selectedIndex = 0;
    document.getElementById('billItems').querySelectorAll('.doc-item').forEach(e => e.remove());
    billSeq = 0;
    DOC.setToggle('optBlank', true);
    DOC.setToggle('optTotals', true);
    DOC.setToggle('optSign', false);
    billSetFormat('gst');
    billUpdate();
}

function billPDF() {
    billUpdate();
    const d = billCollect();
    const f = d.f;
    const doc = DOC.newDoc();
    if (!doc) return;

    const W = doc.internal.pageSize.getWidth();
    const H = doc.internal.pageSize.getHeight();
    const MG = 14;
    const accent = DOC.currentAccentRGB();
    const cols = f.head.length;

    const right = [
        { label: 'Period From', value: DOC.fmtDate(DOC.val('perFrom')) || '\u2014' },
        { label: 'Period To', value: DOC.fmtDate(DOC.val('perTo')) || '\u2014' }
    ];
    if (billFmt === 'simple') right.push({ label: 'Opening balance', value: DOC.money(d.openBal) });

    let y = DOC.pdfHeader(doc, {
        org: DOC.val('shName') || 'Your Shop',
        left: [
            { value: DOC.val('shAddr') || 'Address' },
            { value: 'Phone: ' + (DOC.val('shPhone') || '\u2014') },
            { value: DOC.val('shGst') ? 'GSTIN: ' + DOC.val('shGst') : '' }
        ],
        title: 'BILL BOOK',
        right: right,
        accent: accent
    });

    y = DOC.pdfSection(doc, MG, y, f.title + '  \u00B7  ' + d.count + ' BILLS', W - MG * 2) + 1;

    let body = [];
    if (billFmt === 'simple') {
        let bal = d.openBal;
        body = d.rows.map(function (r) {
            if (r.status !== 'Cancelled') bal = DOC.round2(bal + r.amt - r.out);
            return [r.i, DOC.fmtDate(r.date) || '\u2014', r.rcpt || r.no || '\u2014', r.party || '\u2014',
                DOC.money(r.amt), r.out ? DOC.money(r.out) : '\u2014', DOC.money(bal)];
        });
    } else if (billFmt === 'gst') {
        body = d.rows.map(function (r) {
            return [r.i, r.no || '\u2014', DOC.fmtDate(r.date) || '\u2014', r.party || '\u2014', r.gstin || '\u2014',
                DOC.money(r.taxable), DOC.money(r.tax), DOC.money(r.amt)];
        });
    } else if (billFmt === 'retail') {
        body = d.rows.map(function (r) {
            return [r.i, r.no || '\u2014', r.time || '\u2014', r.items || '\u2014', DOC.money(r.amt), r.mode];
        });
    } else {
        body = d.days.map(function (day, i) {
            return [i + 1, DOC.fmtDate(day.date) || '\u2014', String(day.count), DOC.money(day.total),
                DOC.money(day.cash), DOC.money(day.upi), DOC.money(day.card), DOC.money(day.other)];
        });
    }
    if (!body.length) body = [new Array(cols).fill('\u2014')];

    const cs = { 0: { cellWidth: 8, halign: 'left' } };
    f.numCols.forEach(function (i) {
        cs[i] = { halign: 'right' };
        if (billFmt === 'gst' && i === 7) cs[i].fontStyle = 'bold';
        if (billFmt === 'retail' && i === 4) cs[i].fontStyle = 'bold';
        if (billFmt === 'daily' && i === 3) cs[i].fontStyle = 'bold';
    });
    if (billFmt === 'gst') cs[4] = { halign: 'left', fontSize: 7 };

    y = DOC.pdfTable(doc, f.head, body, { startY: y, accent: accent, extra: { columnStyles: cs } }) + 4;

    if (document.getElementById('optTotals').checked) {
        const rows = [
            { label: 'Total bills in period', value: String(d.count) },
            { label: 'Total billed', value: DOC.money(d.billed) },
            { label: 'Total collected', value: DOC.money(d.collected) }
        ];
        if (billFmt === 'gst') {
            rows.push({ label: 'Taxable value', value: DOC.money(d.taxableSum) });
            rows.push({ label: 'Tax collected', value: DOC.money(d.taxSum) });
        }
        rows.push({ label: 'Outstanding', value: DOC.money(d.due) });
        if (billFmt === 'simple') {
            rows.push({ label: 'Amount in', value: DOC.money(d.inSum) });
            rows.push({ label: 'Amount out', value: DOC.money(d.outSum) });
        }
        if (billFmt === 'daily') {
            rows.push({ label: 'Cash', value: DOC.money(d.modeTot.Cash) });
            rows.push({ label: 'UPI', value: DOC.money(d.modeTot.UPI) });
            rows.push({ label: 'Card', value: DOC.money(d.modeTot.Card) });
            rows.push({ label: 'Other modes', value: DOC.money(d.modeTot.Other) });
        }
        rows.push({ rule: true, gap: 2 });
        rows.push({
            label: f.grandLbl, value: DOC.money(d.grandTotal),
            bold: true, size: 10.5, color: accent, gap: 6
        });
        y = DOC.pdfTotals(doc, W, y, rows);
    }

    if (document.getElementById('optBlank').checked) {
        if (y > H - 70) { doc.addPage(); y = 18; }
        y = DOC.pdfSection(doc, MG, y + 2, 'CARRY FORWARD \u2014 CONTINUE ON NEXT PAGE', W - MG * 2) + 1;
        const blanks = [];
        for (let i = 0; i < 8; i++) blanks.push(new Array(cols).fill(''));
        y = DOC.pdfTable(doc, f.head, blanks, { startY: y, accent: accent, extra: { columnStyles: cs } }) + 4;
    }

    if (document.getElementById('optSign').checked) {
        if (y > H - 34) { doc.addPage(); y = 20; }
        const colW = (W - MG * 2) / 3;
        const sy = y + 13;
        doc.setDrawColor(148, 163, 184); doc.setLineWidth(0.3);
        [0, 1, 2].forEach(function (i) {
            const x = MG + colW * i + colW / 2;
            doc.line(x - 30, sy, x + 30, sy);
        });
        doc.setFontSize(7.5); doc.setTextColor(100, 116, 139);
        doc.text('Prepared by', MG + colW / 2, sy + 4, { align: 'center' });
        doc.text('Checked by', MG + colW * 1.5, sy + 4, { align: 'center' });
        doc.setFont('helvetica', 'bold');
        doc.text('For ' + (DOC.val('shName') || 'the proprietor'), MG + colW * 2.5, sy + 4, { align: 'center' });
        doc.setFont('helvetica', 'normal');
        y = sy + 9;
    }

    DOC.pdfFooter(doc, y, f.title + ' \u00B7 ' + d.count + ' bills \u00B7 ' + (DOC.fmtDate(DOC.val('perFrom')) || '') + ' to ' + (DOC.fmtDate(DOC.val('perTo')) || ''));
    doc.save('bill-book_' + (DOC.fmtDate(DOC.val('perFrom')) || 'period').replace(/[^\w\-]+/g, '_') + '.pdf');
}

/* ---------- Boot ---------- */
(function () {
    const t = DOC.todayISO();
    document.getElementById('bDate').value = t;
    document.getElementById('perFrom').value = t.slice(0, 8) + '01';
    document.getElementById('perTo').value = t;
    billRenderFormats();

    const ids = ['shName', 'shAddr', 'shPhone', 'shGst', 'openBal', 'perFrom', 'perTo',
        'bNo', 'bDate', 'bParty', 'bAmt', 'bMode', 'bStatus', 'bGstin', 'bTaxable', 'bTax',
        'bTime', 'bItems', 'bOut', 'bRcpt', 'optBlank', 'optTotals', 'optSign'];
    DOC.bind(ids, billUpdate);

    DOC.bindSwatches('data-bill-accent', billUpdate);

    billUpdate();
})();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
