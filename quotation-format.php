<?php
/**
 * My Metaji - Quotation Format
 * Lets the user pick a quotation format (Detailed / Lumpsum / Site Survey / AMC / Tender),
 * fill it in, preview it live and download it as a PDF.
 */
require_once __DIR__ . '/config.php';
$page_title = 'Free Quotation Format — Quotation Templates Online | ' . BRAND_NAME;
$page_desc  = 'Create professional quotations free. Choose detailed, lumpsum, site survey, AMC or tender format, fill in line items with GST and download a print-ready PDF.';
include __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="gst-hero">
    <div class="gst-wrap">
        <div class="gst-hero-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
            Free forever &middot; No sign-up, no watermark
        </div>
        <h1>Free <span>Quotation Format</span></h1>
        <p>A quotation is the priced offer you send <em>before</em> the deal closes &mdash; it must carry a validity date, a clear tax breakup and a signature block. Pick the right format, fill the line items and download a print-ready PDF.</p>
        <div class="gst-hero-chips">
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                5 quotation formats
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 3v18M7 7h7a3 3 0 0 1 0 6H7"/></svg>
                Auto CGST / SGST / IGST
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 3v12"/><path d="M7 11l5 5 5-5"/><path d="M4 21h16"/></svg>
                Instant PDF download
            </span>
        </div>
    </div>
</section>

<!-- ===================== TOOL ===================== -->
<section class="gst-tool" id="quotation-format-tool">
    <div class="gst-wrap">
        <div class="qm-layout">

            <!-- ===== LEFT: FORM ===== -->
            <div>

                <!-- 1. FORMAT PICKER -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                        </div>
                        <div>
                            <h3>Choose Your Quotation Format</h3>
                            <p>The format decides the columns, the extra blocks and how the total is shown.</p>
                        </div>
                        <span class="qm-step">Step 1</span>
                    </div>

                    <div class="fmt-grid" id="fmtGrid" role="group" aria-label="Quotation format"></div>

                    <div class="note-box" style="margin-top:16px;">
                        <div class="note-ico" id="fmtInfoIco">&#128203;</div>
                        <div>
                            <h4 id="fmtInfoTitle">Detailed Quotation</h4>
                            <p id="fmtInfoDesc">The full working quotation with every line item itemised, HSN/SAC code, unit of measure and discount.</p>
                            <p style="margin:0;"><strong>Must contain:</strong> <span id="fmtInfoFields">Quotation number, quotation date, valid-until date, seller GSTIN, buyer details, HSN/SAC, quantity, rate, GST and total</span></p>
                        </div>
                    </div>

                    <div class="doc-split" style="margin-top:18px;">
                        <div>
                            <div class="doc-opt">
                                <span>Accent colour</span>
                                <div class="doc-swatches">
                                    <button type="button" class="doc-swatch active" data-quote-accent="blue" style="background:#2563EB" aria-label="Blue"></button>
                                    <button type="button" class="doc-swatch" data-quote-accent="indigo" style="background:#4F46E5" aria-label="Indigo"></button>
                                    <button type="button" class="doc-swatch" data-quote-accent="teal" style="background:#0D9488" aria-label="Teal"></button>
                                    <button type="button" class="doc-swatch" data-quote-accent="amber" style="background:#D97706" aria-label="Amber"></button>
                                    <button type="button" class="doc-swatch" data-quote-accent="violet" style="background:#7C3AED" aria-label="Violet"></button>
                                    <button type="button" class="doc-swatch" data-quote-accent="slate" style="background:#334155" aria-label="Slate"></button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="doc-switches">
                                <label class="doc-chk"><input type="checkbox" id="optSignature" checked> Signature block</label>
                                <label class="doc-chk"><input type="checkbox" id="optWords" checked> Amount in words</label>
                                <label class="doc-chk"><input type="checkbox" id="optTerms" checked> Standard terms</label>
                                <label class="doc-chk"><input type="checkbox" id="optBank"> Bank details</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. SELLER -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14"/><path d="M10 12h4M10 16h4"/></svg>
                        </div>
                        <div>
                            <h3>Your / Seller Details</h3>
                            <p>Quotations are normally issued on your letterhead, so these details print at the top.</p>
                        </div>
                        <span class="qm-step">Step 2</span>
                    </div>
                    <div class="doc-split">
                        <div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Business / Legal Name</label><input type="text" id="sName" placeholder="As registered with GST"></div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>GSTIN</label><input type="text" id="sGst" placeholder="24AAAAA0000A1Z5" maxlength="15"></div>
                        </div>
                        <div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Phone</label><input type="tel" id="sPhone" placeholder="98765 43210"></div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>State</label><select id="sState"></select></div>
                        </div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Registered Address</label><input type="text" id="sAddr" placeholder="Street, city, state, PIN"></div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>State Code</label><input type="text" id="sStateCode" placeholder="24" maxlength="2"></div>
                        <div class="inv-gen-field"><label>Trade Name / Letterhead</label><input type="text" id="sTrade" placeholder="Optional"></div>
                    </div>
                </div>

                <!-- 3. BUYER -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
                        </div>
                        <div>
                            <h3>Quotation For &mdash; Buyer Details</h3>
                            <p>The party the offer is made to. The state decides CGST + SGST or IGST.</p>
                        </div>
                        <span class="qm-step">Step 3</span>
                    </div>
                    <div class="doc-split">
                        <div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Buyer Name</label><input type="text" id="bName" placeholder="Customer or company name"></div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Buyer GSTIN</label><input type="text" id="bGst" placeholder="27BBBBB0000B1Z5" maxlength="15"></div>
                        </div>
                        <div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Phone</label><input type="tel" id="bPhone" placeholder="98765 43210"></div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>State</label><select id="bState"></select></div>
                        </div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Billing / Site Address</label><input type="text" id="bAddr" placeholder="Street, city, state, PIN"></div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>Place of Supply</label><input type="text" id="bPos" placeholder="e.g. Gujarat (24)"></div>
                        <div class="inv-gen-field"><label>PO / Enquiry Reference</label><input type="text" id="bPo" placeholder="ENQ-2026-118 &middot; optional"></div>
                    </div>
                </div>

                <!-- 4. QUOTATION META + ITEMS -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M8 9h8M8 13h8M8 17h4"/></svg>
                        </div>
                        <div>
                            <h3>Quotation Number, Date &amp; Items</h3>
                            <p>Always set a valid-until date. An open-ended quotation is commercially worthless.</p>
                        </div>
                        <span class="qm-step">Step 4</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label id="quoteNoLbl">Quotation Number</label><input type="text" id="quoteNo" placeholder="QTN-2026-001"></div>
                        <div class="inv-gen-field"><label>Quotation Date</label><input type="date" id="quoteDate"></div>
                    </div>
                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label>Valid Until</label><input type="date" id="quoteValid"></div>
                        <div class="inv-gen-field"><label>Enquiry / Lead Source</label>
                            <select id="quoteSource">
                                <option value="">Not specified</option>
                                <option>Direct enquiry</option>
                                <option>Website / enquiry form</option>
                                <option>Referral</option>
                                <option>Existing customer</option>
                                <option>Agent / channel partner</option>
                            </select>
                        </div>
                    </div>

                    <div id="itemsCard">
                        <div class="doc-items" id="quoteItems" style="--doc-cols: 2.3fr .95fr .6fr .75fr 1fr .7fr .7fr 1fr auto">
                            <div class="doc-item-hd">
                                <span>Item / Service</span><span id="qHsnHd">HSN / SAC</span><span>Qty</span><span id="qUnitHd">Unit</span><span>Rate (₹)</span><span id="qDiscHd">Disc %</span><span>GST %</span><span style="text-align:right">Amount</span><span></span>
                            </div>
                        </div>
                        <button type="button" class="inv-gen-add-item" onclick="quoteAddRow()">+ Add Item / Service</button>

                        <div class="doc-split" style="margin-top:16px;">
                            <div class="inv-gen-field"><label>Extra Discount (₹)</label><input type="number" id="quoteDisc" value="0" min="0" step="0.01" oninput="quoteUpdate()"></div>
                            <div class="inv-gen-field"><label>Round Off (₹)</label><input type="number" id="quoteRound" value="0" step="0.01" oninput="quoteUpdate()"></div>
                        </div>
                    </div>

                    <div id="lumpRow" style="display:none;">
                        <div class="doc-split" style="margin-bottom:12px;">
                            <div class="inv-gen-field"><label>Lump Sum Amount (₹)</label><input type="number" id="quoteLump" value="0" min="0" step="0.01" oninput="quoteUpdate()"></div>
                            <div class="inv-gen-field" id="lumpGstWrap"><label>GST Rate</label><select id="quoteLumpGst" onchange="quoteUpdate()"></select></div>
                        </div>
                        <div class="inv-gen-field"><label>Scope Covered in Lump Sum</label><textarea id="quoteScope" rows="3" placeholder="Complete interior package for 2 BHK &mdash; false ceiling, kitchen modules, wardrobes, painting and electrical work."></textarea></div>
                    </div>
                </div>

                <!-- 5. VARIANT-SPECIFIC BLOCKS -->
                <div class="qm-card" id="svRow" style="display:none;">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        </div>
                        <div>
                            <h3>Site Visit / Survey Report</h3>
                            <p>What you saw on site, so the buyer knows the price came from a real inspection.</p>
                        </div>
                        <span class="qm-step">Step 5</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Survey Date</label><input type="date" id="svDate"></div>
                        <div class="inv-gen-field"><label>Site Area / Built-up</label><input type="text" id="svArea" placeholder="e.g. 1,250 sq ft"></div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Scope of Work</label><textarea id="svScope" rows="3" placeholder="Civil repair, waterproofing of terrace, electrical rewiring, painting with apex Ultima."></textarea></div>
                    <div class="inv-gen-field"><label>Validity Note</label><textarea id="svNote" rows="2" placeholder="Price is based on the present site condition. Hidden defects discovered during execution will be quoted separately."></textarea></div>
                </div>

                <div class="qm-card" id="amcRow" style="display:none;">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-9-9"/><path d="M21 3v6h-6"/><path d="M12 7v5l3 2"/></svg>
                        </div>
                        <div>
                            <h3>AMC / Service Contract Terms</h3>
                            <p>Annual maintenance is sold on visits and response time, not on parts.</p>
                        </div>
                        <span class="qm-step">Step 5</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Contract Period</label>
                            <select id="amcPeriod">
                                <option>12 Months</option>
                                <option>24 Months</option>
                                <option>36 Months</option>
                                <option>Quarterly (3 Months)</option>
                                <option>Monthly (1 Month)</option>
                            </select>
                        </div>
                        <div class="inv-gen-field"><label>Number of Visits</label><input type="number" id="amcVisits" value="4" min="0" step="1" oninput="quoteUpdate()"></div>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Service Frequency</label>
                            <select id="amcFreq">
                                <option>Once in 3 months</option>
                                <option>Once in 6 months</option>
                                <option>Once in 12 months</option>
                                <option>Monthly</option>
                                <option>Quarterly</option>
                                <option>On-call (unscheduled)</option>
                            </select>
                        </div>
                        <div class="inv-gen-field"><label>Response Time</label>
                            <select id="amcResp">
                                <option>Within 24 hours</option>
                                <option>Within 48 hours</option>
                                <option>Within 72 hours</option>
                                <option>Next working day</option>
                            </select>
                        </div>
                    </div>
                    <div class="inv-gen-field"><label>Renewal Terms</label><textarea id="amcRenewal" rows="2" placeholder="Contract renews automatically for a further 12 months unless either party gives 30 days written notice. Rates escalate by 8% p.a."></textarea></div>
                </div>

                <div class="qm-card" id="tdRow" style="display:none;">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M8 12h8M8 8h5"/></svg>
                        </div>
                        <div>
                            <h3>Tender / EOI Particulars</h3>
                            <p>Tenders are judged on paperwork, not just price. Keep every reference exact.</p>
                        </div>
                        <span class="qm-step">Step 5</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Client / Issuing Authority</label><input type="text" id="tdClient" placeholder="Name of the tendering body"></div>
                        <div class="inv-gen-field"><label>Tender Reference No.</label><input type="text" id="tdRef" placeholder="TDR/2026/GJ/041"></div>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Submission Date &amp; Time</label><input type="date" id="tdSubmit"></div>
                        <div class="inv-gen-field"><label>Bid Validity</label>
                            <select id="tdBid">
                                <option>90 days from submission</option>
                                <option>120 days from submission</option>
                                <option>180 days from submission</option>
                                <option>30 days from submission</option>
                            </select>
                        </div>
                    </div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>EMD Amount (₹)</label><input type="number" id="tdEmd" value="0" min="0" step="0.01" oninput="quoteUpdate()"></div>
                        <div class="inv-gen-field"><label>EMD Validity / Refund</label>
                            <select id="tdEmdNote">
                                <option>Refundable within 30 days of award</option>
                                <option>Forfeited on failure to execute</option>
                                <option>Bank guarantee submitted</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 6. NOTES + BANK -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>
                        </div>
                        <div>
                            <h3>Notes, Terms &amp; Bank Details</h3>
                            <p>Payment milestones, warranty and the terms you are willing to be held to.</p>
                        </div>
                        <span class="qm-step">Step 6</span>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Notes / Terms</label><textarea id="quoteNotes" rows="3" placeholder="50% advance with order, balance before dispatch. Prices are for GST registered buyers. Work starts 10 days after confirmed order."></textarea></div>
                    <div class="doc-split" id="bankRow" style="display:none;">
                        <div class="inv-gen-field"><label>Bank Name</label><input type="text" id="bBank" placeholder="HDFC Bank"></div>
                        <div class="inv-gen-field"><label>Account Name &amp; Number</label><input type="text" id="bAcct" placeholder="ABC Traders &mdash; 50100299887766"></div>
                    </div>
                    <div class="doc-split" id="bankRow2" style="display:none; margin-top:10px;">
                        <div class="inv-gen-field"><label>IFSC Code</label><input type="text" id="bIfsc" placeholder="HDFC0001234"></div>
                        <div class="inv-gen-field"><label>UPI ID</label><input type="text" id="bUpi" placeholder="name@bank"></div>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="qm-actions doc-no-print">
                    <button type="button" class="qm-btn qm-btn-primary" onclick="quotePreviewJump()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        Preview
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="quotePrint()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="1"/><path d="M6 17h12v4H6z"/></svg>
                        Print
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="quoteReset()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                        Reset
                    </button>
                    <button type="button" class="qm-btn qm-btn-primary" style="flex:2" onclick="quotePDF()">
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
                <div class="inv-gen-preview" id="quotePreview">
                    <div class="doc-sheet doc-sheet-lg" id="quoteSheet">
                        <div class="doc-hd">
                            <div class="doc-org">
                                <h2 id="qPSName">Your Business</h2>
                                <p id="qPSTrade" style="display:none"></p>
                                <p id="qPSAddr">Address</p>
                                <p><span id="qPSPhone">Phone: &mdash;</span> &middot; <span id="qPSState">State: &mdash;</span></p>
                                <p><strong>GSTIN:</strong> <span id="qPSGst">&mdash;</span> &middot; <span id="qPSCode"></span></p>
                            </div>
                            <div class="doc-id">
                                <div class="doc-title" id="qPTitle">QUOTATION</div>
                                <p><strong id="qPNoLbl">Quotation No.:</strong> <span id="qPNo">&mdash;</span></p>
                                <p><strong>Date:</strong> <span id="qPDate">&mdash;</span></p>
                                <p style="color:var(--fmt-accent,#2563EB);"><strong>Valid Until:</strong> <span id="qPValid">&mdash;</span></p>
                            </div>
                        </div>

                        <div class="doc-stats" style="margin-bottom:12px;">
                            <div class="doc-stat amber"><span>Valid Until</span><strong id="qPStatValid" style="font-size:15px;">&mdash;</strong></div>
                            <div class="doc-stat"><span>Quoted Value</span><strong id="qPStatTotal" style="font-size:15px;">₹0.00</strong></div>
                            <div class="doc-stat grey"><span>Line Items</span><strong id="qPStatItems" style="font-size:15px;">0</strong></div>
                        </div>

                        <div class="doc-split" style="margin-bottom:12px;">
                            <div>
                                <p class="doc-sec-title">Quotation For</p>
                                <p class="inv-p-party-name" id="qPBName" style="margin:3px 0;">Customer</p>
                                <p id="qPBAddr" style="font-size:10px;color:#475569;margin:1px 0;">Address</p>
                                <p id="qPBPhone" style="font-size:10px;color:#475569;margin:1px 0;">Phone: &mdash;</p>
                                <p id="qPBGstRow" style="font-size:10px;color:#475569;margin:2px 0;"><strong>GSTIN:</strong> <span id="qPBGst">&mdash;</span></p>
                            </div>
                            <div>
                                <p class="doc-sec-title" id="qPRightTitle">Quotation Details</p>
                                <p style="font-size:10px;color:#475569;margin:2px 0;"><strong>Place of Supply:</strong> <span id="qPPos">&mdash;</span></p>
                                <p id="qPRefRow" style="font-size:10px;color:#475569;margin:2px 0;display:none;"><strong>Enquiry Ref:</strong> <span id="qPRef">&mdash;</span></p>
                                <p id="qPSrcRow" style="font-size:10px;color:#475569;margin:2px 0;display:none;"><strong>Source:</strong> <span id="qPSrc">&mdash;</span></p>
                            </div>
                        </div>

                        <div id="qPSiteBox" style="display:none;margin-bottom:12px;">
                            <p class="doc-sec-title">Site Visit Report</p>
                            <div class="kv-grid">
                                <div class="kv-item"><div class="kv-k">Survey Date</div><div class="kv-v" id="qPSvDate">&mdash;</div></div>
                                <div class="kv-item"><div class="kv-k">Site Area</div><div class="kv-v" id="qPSvArea">&mdash;</div></div>
                                <div class="kv-item wide"><div class="kv-k">Scope of Work</div><div class="kv-v" id="qPSvScope">&mdash;</div></div>
                                <div class="kv-item wide"><div class="kv-k">Validity Note</div><div class="kv-v" id="qPSvNote">&mdash;</div></div>
                            </div>
                        </div>

                        <div id="qPAmcBox" style="display:none;margin-bottom:12px;">
                            <p class="doc-sec-title">AMC / Service Contract</p>
                            <div class="kv-grid">
                                <div class="kv-item"><div class="kv-k">Contract Period</div><div class="kv-v" id="qPAmcPeriod">&mdash;</div></div>
                                <div class="kv-item"><div class="kv-k">Number of Visits</div><div class="kv-v" id="qPAmcVisits">&mdash;</div></div>
                                <div class="kv-item"><div class="kv-k">Frequency</div><div class="kv-v" id="qPAmcFreq">&mdash;</div></div>
                                <div class="kv-item"><div class="kv-k">Response Time</div><div class="kv-v" id="qPAmcResp">&mdash;</div></div>
                                <div class="kv-item wide"><div class="kv-k">Renewal Terms</div><div class="kv-v" id="qPAmcRenewal">&mdash;</div></div>
                            </div>
                        </div>

                        <div id="qPTenderBox" style="display:none;margin-bottom:12px;">
                            <p class="doc-sec-title">Tender / EOI Particulars</p>
                            <div class="kv-grid">
                                <div class="kv-item"><div class="kv-k">Client</div><div class="kv-v" id="qPTdClient">&mdash;</div></div>
                                <div class="kv-item"><div class="kv-k">Tender Ref. No.</div><div class="kv-v" id="qPTdRef">&mdash;</div></div>
                                <div class="kv-item"><div class="kv-k">Submission Date</div><div class="kv-v" id="qPTdSubmit">&mdash;</div></div>
                                <div class="kv-item"><div class="kv-k">Bid Validity</div><div class="kv-v" id="qPTdBid">&mdash;</div></div>
                                <div class="kv-item"><div class="kv-k">EMD Amount</div><div class="kv-v" id="qPTdEmd">&mdash;</div></div>
                                <div class="kv-item"><div class="kv-k">EMD Terms</div><div class="kv-v" id="qPTdEmdNote">&mdash;</div></div>
                            </div>
                        </div>

                        <div id="qPTableWrap" style="margin-bottom:12px;">
                            <table class="inv-p-table">
                                <thead><tr id="qPHead"></tr></thead>
                                <tbody id="qPRows"><tr><td style="padding:14px;text-align:center;color:#94A3B8;">No items yet</td></tr></tbody>
                            </table>
                        </div>

                        <div id="qPLumpBox" style="display:none;margin-bottom:12px;">
                            <div class="kv-grid" style="margin-bottom:10px;">
                                <div class="kv-item wide"><div class="kv-k">Scope Covered</div><div class="kv-v" id="qPScope">&mdash;</div></div>
                            </div>
                            <table class="inv-p-table">
                                <thead><tr>
                                    <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">Particulars</th>
                                    <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Amount</th>
                                </tr></thead>
                                <tbody id="qPLumpRows"></tbody>
                            </table>
                        </div>

                        <div style="display:flex;justify-content:flex-end;margin-bottom:12px;">
                            <table class="inv-p-totals-table" style="min-width:230px;">
                                <tr><td style="padding:3px 8px;">Taxable Value</td><td id="qPSub" style="text-align:right;font-weight:600;">₹0.00</td></tr>
                                <tr id="qPDiscRow" style="display:none"><td style="padding:3px 8px;">Discount</td><td id="qPDisc" style="text-align:right;font-weight:600;color:#059669;">&minus;₹0.00</td></tr>
                                <tr id="qPCgstRow" style="display:none"><td style="padding:3px 8px;">CGST</td><td id="qPCgst" style="text-align:right;font-weight:600;">₹0.00</td></tr>
                                <tr id="qPSgstRow" style="display:none"><td style="padding:3px 8px;">SGST</td><td id="qPSgst" style="text-align:right;font-weight:600;">₹0.00</td></tr>
                                <tr id="qPIgstRow" style="display:none"><td style="padding:3px 8px;">IGST</td><td id="qPIgst" style="text-align:right;font-weight:600;">₹0.00</td></tr>
                                <tr id="qPRoundRow" style="display:none"><td style="padding:3px 8px;">Round Off</td><td id="qPRound" style="text-align:right;font-weight:600;">₹0.00</td></tr>
                                <tr style="border-top:2px solid #1E293B;"><td style="padding:6px 8px;font-weight:800;font-size:12px;color:var(--fmt-accent,#2563EB);" id="qPGrandLbl">Quoted Total</td><td style="padding:6px 8px;text-align:right;font-weight:800;font-size:12px;color:var(--fmt-accent,#2563EB);" id="qPGrand">₹0.00</td></tr>
                            </table>
                        </div>

                        <div class="amt-hero">
                            <div class="amt-hero-lbl">Total Quoted Value &middot; Valid until <span id="qPValidHero">&mdash;</span></div>
                            <div class="amt-hero-val" id="qPGrandHero">₹0.00</div>
                        </div>

                        <div class="inv-p-words" id="qPWordsRow" style="font-size:9px;color:#475569;padding:8px 0;border-top:1px solid #E2E8F0;margin-bottom:8px;">
                            <strong>Amount in words:</strong> <span id="qPWords">Zero Only</span>
                        </div>
                        <div class="inv-p-notes" id="qPNotesRow" style="display:none;font-size:9px;color:#475569;padding:6px 0;">
                            <strong>Notes:</strong> <span id="qPNotes">&mdash;</span>
                        </div>
                        <div id="qPTermsRow" style="font-size:9px;color:#475569;padding:6px 0;">
                            <strong>Terms:</strong>
                            <span id="qPTerms">This quotation is valid until the date stated above. Rates are firm and include the GST shown. Goods once ordered will not be taken back. Disputes are subject to local jurisdiction only.</span>
                        </div>
                        <div id="qPBankRow" style="display:none;font-size:9px;color:#475569;padding:8px 0;border-top:1px dashed #E2E8F0;margin-bottom:6px;">
                            <strong>Remittance details:</strong> <span id="qPBankTxt">Add your bank details above</span>
                        </div>
                        <div class="doc-sig" id="qPSigRow">
                            <div><div class="doc-sig-line">Customer Acceptance</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Name, date &amp; signature</div></div>
                            <div><div class="doc-sig-line">For <span id="qPSigName">&mdash;</span></div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Authorised Signatory</div></div>
                            <div><div class="doc-sig-line">Quotation Prepared By</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Contact &amp; phone</div></div>
                        </div>
                        <div class="doc-ft">This is a computer-generated document &middot; Generated by <strong>My Metaji</strong></div>
                    </div>
                </div>
                <div class="qm-total-strip">
                    <span>Quoted Total</span>
                    <strong id="qPStripTotal">₹0.00</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== FORMAT COMPARISON ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>Quotation or Something Else?</h2>
        <p>Most small businesses lose deals by sending the wrong document at the wrong stage. A quotation is a priced, dated, expiring offer &mdash; it is not a bill, and it is not a purchase order. Use the table below to match the stage of your deal to the right document.</p>
        <div class="fmt-table-wrap" style="margin-top:20px;">
            <table class="fmt-table">
                <thead>
                    <tr><th>Document</th><th>Send it when</th><th>Binding?</th><th>Tax breakup</th><th>Validity</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Quotation</strong></td>
                        <td>You have priced a job and the buyer is still comparing options</td>
                        <td><span class="fmt-req no">Not binding until accepted</span></td>
                        <td>CGST + SGST or IGST shown, as on a tax invoice</td>
                        <td><span class="fmt-req yes">Must carry valid-until date</span></td>
                    </tr>
                    <tr>
                        <td><strong>Cost Estimate</strong></td>
                        <td>The scope is still fuzzy and the buyer only wants a ballpark figure</td>
                        <td><span class="fmt-req no">Indicative only</span></td>
                        <td>Optional &mdash; usually a single approximate figure</td>
                        <td>Short, often 7 to 15 days</td>
                    </tr>
                    <tr>
                        <td><strong>Proforma Invoice</strong></td>
                        <td>The buyer has accepted the quote and an advance is being raised</td>
                        <td><span class="fmt-req cond">Binding once advance is paid</span></td>
                        <td>Full breakup, adjusted against the final invoice</td>
                        <td>Valid until the delivery date</td>
                    </tr>
                    <tr>
                        <td><strong>Purchase Order</strong></td>
                        <td>The buyer raises a formal PO against your quotation</td>
                        <td><span class="fmt-req yes">Binding on both sides</span></td>
                        <td>Usually mentioned but not itemised</td>
                        <td>As per the PO</td>
                    </tr>
                    <tr>
                        <td><strong>Work Order</strong></td>
                        <td>Job has started and you need an internal job card</td>
                        <td><span class="fmt-req cond">Internal / contractual</span></td>
                        <td>Not applicable</td>
                        <td>Till completion of the job</td>
                    </tr>
                    <tr>
                        <td><strong>Tax Invoice</strong></td>
                        <td>Goods have been supplied or services rendered and payment is due</td>
                        <td><span class="fmt-req yes">Statutory &mdash; required by law</span></td>
                        <td>Compulsory, with buyer GSTIN for B2B</td>
                        <td>Due date drives payment, not a validity date</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="note-box gold" style="margin-top:20px;">
            <div class="note-ico">&#128221;</div>
            <div>
                <h4>Five things every quotation must get right</h4>
                <ul>
                    <li><strong>Valid-until date.</strong> Without it the buyer can hold your price open indefinitely &mdash; and your input costs will not wait for it.</li>
                    <li><strong>Consistent numbering.</strong> QTN-2026-001, 002, 003. A gap in the series makes it look like pages were removed before converting it into an invoice.</li>
                    <li><strong>Tax split by place of supply.</strong> Same state means CGST + SGST, different state means IGST. Choosing the wrong one exposes you to a credit note.</li>
                    <li><strong>Scope in writing.</strong> Write what is <em>excluded</em> as carefully as what is included &mdash; that single line prevents most rework disputes.</li>
                    <li><strong>GSTIN of both parties.</strong> A buyer registered for GST cannot claim input tax credit on your quotation unless the GSTIN is printed on it.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===================== HOW TO ===================== -->
<section class="gst-faq" style="background:#fff;">
    <div class="gst-wrap">
        <h2>How to Create a Professional Quotation</h2>
        <div class="gst-how-grid">
            <div class="gst-how-card">
                <div class="gst-how-num">1</div>
                <h3>Pick the Format</h3>
                <p>Detailed for goods and itemised work, lumpsum for fixed-price interiors, site survey after a visit, AMC for annual contracts, tender for EOI submissions.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">2</div>
                <h3>Set Number &amp; Validity</h3>
                <p>Give the quotation a running number and a valid-until date. Most vendors accept a quote for 7 to 15 days; construction quotes usually need 30.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">3</div>
                <h3>Price the Line Items</h3>
                <p>Add each item with HSN/SAC, unit, quantity, rate, discount and GST rate. Taxable value, tax and the quoted total update live as you type.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">4</div>
                <h3>Download &amp; Follow Up</h3>
                <p>Check the preview, download the PDF and email it the same day. Then convert the accepted quote to an invoice from the same number series.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SEO + RELATED ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>What is a Quotation Format?</h2>
        <p>A <strong>quotation format</strong> is the standard layout used to present a priced offer to a prospective buyer before any supply is made. Unlike a tax invoice, a quotation is not a statutory document under the CGST Act &mdash; there is no prescribed format for it &mdash; but it must still contain the particulars a buyer needs to accept and later convert into a purchase order: the supplier's legal name and GSTIN, the buyer's name, address, GSTIN and place of supply, a unique quotation number, the date of issue, a clearly stated valid-until date, an itemised description with HSN/SAC codes where applicable, unit of measure, quantity, rate, discount, the rate and amount of tax split into CGST and SGST for an intra-state supply or IGST for an inter-state supply, the total value in figures and in words, and an authorised signature block.</p>
        <p>The commercial value of a quotation lies in its <strong>validity clause</strong>. A price quoted without an expiry date is an open-ended promise, and for a business buying raw material, labour or imported components, that is a real financial risk because the seller &mdash; not the buyer &mdash; carries it. The same discipline applies to scope: a quotation that lists only "interior work" invites a dispute the moment the buyer imagines wardrobes and kitchen modules are included. Experienced vendors itemise aggressively, state exclusions explicitly, and attach payment milestones (advance on order, balance before dispatch) so the buyer can budget without a follow-up phone call. Once the buyer accepts, the same quotation becomes the base document for the proforma invoice, the purchase order and finally the tax invoice, which is why it is worth spending ten minutes on the format at the start.</p>

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
        <h2>Send Quotations That Close Deals</h2>
        <p><?= BRAND_NAME ?> keeps your rate cards, GST rates and customer details ready, so the next quotation takes a minute &mdash; not an afternoon.</p>
        <a href="#download" class="gst-cta-btn">Download <?= BRAND_NAME ?> Free &rarr;</a>
    </div>
</section>

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="assets/js/doc-utils.js"></script>
<script>
/* ---------- Format definitions ---------- */
const QUOTE_FORMATS = {
    detailed: {
        label: 'Detailed Quotation', tag: 'Itemised, HSN/SAC & discounts', tags: ['Itemised', 'HSN/SAC', 'Discount %'],
        ico: '\u{1F4CB}', accent: '#2563EB',
        desc: 'The full working quotation. Every line carries its HSN/SAC code, unit of measure, quantity, rate, line discount and GST rate, so the buyer can verify every number.',
        fields: 'Quotation number & date, valid-until date, seller GSTIN, buyer GSTIN, HSN/SAC, unit, quantity, rate, discount %, GST %, amount in words',
        title: 'QUOTATION', noLbl: 'Quotation No.', rightTitle: 'Quotation Details',
        items: true, hsn: true, unit: true, disc: true, grandLbl: 'Quoted Total'
    },
    lumpsum: {
        label: 'Lumpsum Quotation', tag: 'One fixed total, no breakdown', tags: ['Fixed price', 'Interiors', 'AMC projects'],
        ico: '\u{1F3E1}', accent: '#0D9488',
        desc: 'A single all-inclusive price for a defined scope. Standard for turnkey interiors, civil jobs, AMC packages and any project where itemising invites unprofitable line-level negotiation.',
        fields: 'Quotation number & date, valid-until date, lump sum amount, GST rate, scope covered, exclusions, signature block',
        title: 'LUMSUM QUOTATION', noLbl: 'Quotation No.', rightTitle: 'Offer Summary',
        items: false, hsn: false, unit: false, disc: false, lump: true, grandLbl: 'Total Lumpsum Offer'
    },
    sitevisit: {
        label: 'On-Site / Survey Quotation', tag: 'Built after a site visit', tags: ['Site survey', 'Scope of work', 'Validity note'],
        ico: '\u{1F5FA}', accent: '#D97706',
        desc: 'Issued after inspecting the premises. Adds a Site Visit Report so the buyer can see the price came from a real survey and is not a blind guess.',
        fields: 'Survey date, site area, scope of work, validity note, quoted items, valid-until date, signature block',
        title: 'SITE SURVEY QUOTATION', noLbl: 'Survey Quotation No.', rightTitle: 'Survey Details',
        items: true, hsn: false, unit: false, disc: false, site: true, grandLbl: 'Quoted Total'
    },
    amc: {
        label: 'AMC / Service Quotation', tag: 'Annual maintenance contract', tags: ['AMC', 'Visits per year', 'Renewal'],
        ico: '\u{1F504}', accent: '#059669',
        desc: 'An annual service contract priced on visits, frequency and response time rather than on parts. Always states the renewal and escalation terms.',
        fields: 'Contract period, number of visits, service frequency, response time, renewal terms, valid-until date, signature block',
        title: 'AMC / SERVICE QUOTATION', noLbl: 'AMC Reference No.', rightTitle: 'Contract Summary',
        items: true, hsn: false, unit: true, disc: false, amc: true, grandLbl: 'Contract Value'
    },
    tenders: {
        label: 'Tender / EOI Quotation', tag: 'Bid submission document', tags: ['Tender ref', 'Bid validity', 'EMD'],
        ico: '\u{1F4C4}', accent: '#7C3AED',
        desc: 'A bid document for a tender or expression of interest. The tender reference, submission date, bid validity and EMD amount are the fields that get a bid rejected if they are wrong.',
        fields: 'Client name, tender reference no., submission date, bid validity, EMD amount & terms, valid-until date, signature block',
        title: 'TENDER / EOI QUOTATION', noLbl: 'Tender Ref. No.', rightTitle: 'Bid Particulars',
        items: true, hsn: false, unit: true, disc: false, tender: true, grandLbl: 'Quoted Bid Value'
    }
};

let quoteFmt = 'detailed';
let quoteRowId = 0;

const QUOTE_TERMS = 'This quotation is valid until the date stated above. Rates are firm and include the GST shown. Goods once ordered will not be taken back. Payment is due as per the agreed milestones. Disputes are subject to local jurisdiction only.';

/* ---------- Format picker ---------- */
function quoteRenderFormats() {
    const grid = document.getElementById('fmtGrid');
    let h = '';
    Object.keys(QUOTE_FORMATS).forEach(k => {
        const f = QUOTE_FORMATS[k];
        h += '<button type="button" class="fmt-card' + (k === quoteFmt ? ' active' : '') + '" data-quote-fmt="' + k + '" aria-pressed="' + (k === quoteFmt) + '">' +
            '<span class="fmt-mini" style="--fmt-accent:' + f.accent + '">' +
                '<span class="fmt-mini-top"><span class="fmt-mini-brand"></span><span class="fmt-mini-tag"></span></span>' +
                '<span class="fmt-mini-ln w70"></span><span class="fmt-mini-ln w45"></span><span class="fmt-mini-ln w70"></span>' +
                '<span class="fmt-mini-tot"></span>' +
            '</span>' +
            '<span class="fmt-card-body">' +
                '<span class="fmt-card-title">' + f.ico + ' ' + f.label + '</span>' +
                '<span class="fmt-card-sub">' + f.tag + '</span>' +
                '<span class="fmt-tags">' + f.tags.map(t => '<span class="fmt-tag">' + t + '</span>').join('') + '</span>' +
            '</span>' +
        '</button>';
    });
    grid.innerHTML = h;
    grid.querySelectorAll('[data-quote-fmt]').forEach(b => b.addEventListener('click', () => quoteSetFormat(b.dataset.quoteFmt)));
    quoteSetFormat(quoteFmt, true);
}

function quoteCols(f) {
    const c = [{ k: 'i', l: '#' }, { k: 'name', l: 'Item / Service' }];
    if (f.hsn) c.push({ k: 'hsn', l: 'HSN/SAC' });
    c.push({ k: 'q', l: 'Qty', r: true });
    if (f.unit) c.push({ k: 'unit', l: 'Unit' });
    c.push({ k: 'rate', l: 'Rate', r: true });
    if (f.disc) c.push({ k: 'dr', l: 'Disc %', r: true });
    c.push({ k: 'gr', l: 'GST %', r: true });
    c.push({ k: 'total', l: 'Amount', r: true, b: true });
    return c;
}

function quoteColTemplate() {
    const f = QUOTE_FORMATS[quoteFmt];
    const p = ['2.3fr'];
    if (f.hsn) p.push('.95fr');
    p.push('.6fr');
    if (f.unit) p.push('.75fr');
    p.push('1fr');
    if (f.disc) p.push('.7fr');
    p.push('.7fr');
    p.push('1fr');
    p.push('auto');
    return p.join(' ');
}

function quoteSetFormat(k, quiet) {
    const f = QUOTE_FORMATS[k];
    if (!f) return;
    quoteFmt = k;

    document.querySelectorAll('[data-quote-fmt]').forEach(b => {
        const on = b.dataset.quoteFmt === k;
        b.classList.toggle('active', on);
        b.setAttribute('aria-pressed', on);
    });

    document.getElementById('fmtInfoIco').textContent = f.ico;
    document.getElementById('fmtInfoTitle').textContent = f.label;
    document.getElementById('fmtInfoDesc').textContent = f.desc;
    document.getElementById('fmtInfoFields').textContent = f.fields;
    document.getElementById('quoteNoLbl').textContent = f.noLbl;
    document.getElementById('qPNoLbl').textContent = f.noLbl + ':';
    document.getElementById('qPTitle').textContent = f.title;
    document.getElementById('qPRightTitle').textContent = f.rightTitle;

    const show = (id, on) => { const e = document.getElementById(id); if (e) e.style.display = on ? '' : 'none'; };
    show('itemsCard', f.items);
    show('lumpRow', !!f.lump);
    show('svRow', !!f.site);
    show('amcRow', !!f.amc);
    show('tdRow', !!f.tender);

    const items = document.getElementById('quoteItems');
    if (items) items.style.setProperty('--doc-cols', quoteColTemplate());

    document.querySelectorAll('[data-quote-hsn]').forEach(e => { e.style.display = f.hsn ? '' : 'none'; });
    document.querySelectorAll('[data-quote-unit]').forEach(e => { e.style.display = f.unit ? '' : 'none'; });
    document.querySelectorAll('[data-quote-disc]').forEach(e => { e.style.display = f.disc ? '' : 'none'; });
    document.getElementById('qHsnHd').style.display = f.hsn ? '' : 'none';
    document.getElementById('qUnitHd').style.display = f.unit ? '' : 'none';
    document.getElementById('qDiscHd').style.display = f.disc ? '' : 'none';

    if (!quiet) quoteUpdate();
}

/* ---------- Item rows ---------- */
function quoteAddRow(seed) {
    seed = seed || {};
    const f = QUOTE_FORMATS[quoteFmt];
    const id = quoteRowId++;
    const vis = on => (on ? '' : 'display:none;');

    document.getElementById('quoteItems').insertAdjacentHTML('beforeend',
        '<div class="doc-item" data-row="' + id + '">' +
            '<input type="text" data-quote-name placeholder="Item or service description" oninput="quoteUpdate()">' +
            '<input type="text" data-quote-hsn style="' + vis(f.hsn) + '" placeholder="HSN" maxlength="8" oninput="quoteUpdate()">' +
            '<input type="number" data-quote-qty value="' + (seed.qty || 1) + '" min="0" step="0.01" oninput="quoteUpdate()">' +
            '<select data-quote-unit style="' + vis(f.unit) + '" onchange="quoteUpdate()"><option>NOS</option><option selected>PCS</option><option>KGS</option><option>LTR</option><option>BOX</option><option>SET</option><option>SQF</option><option>MTR</option><option>DOZ</option></select>' +
            '<input type="number" data-quote-rate value="0" min="0" step="0.01" oninput="quoteUpdate()">' +
            '<input type="number" data-quote-disc style="' + vis(f.disc) + '" value="0" min="0" max="100" step="0.01" oninput="quoteUpdate()">' +
            '<select data-quote-gst onchange="quoteUpdate()">' + DOC.GST_RATES.map(r => '<option value="' + r + '"' + (r === '18' ? ' selected' : '') + '>' + r + '%</option>').join('') + '</select>' +
            '<span class="doc-item-amt" data-quote-amt>₹0.00</span>' +
            '<button type="button" class="doc-item-del" onclick="quoteDelRow(' + id + ')" title="Remove item" aria-label="Remove item">&times;</button>' +
        '</div>');
    quoteUpdate();
}

function quoteDelRow(id) {
    const e = document.querySelector('[data-row="' + id + '"]');
    if (e) e.remove();
    quoteUpdate();
}

/* ---------- Totals engine ---------- */
function quoteCollect() {
    const f = QUOTE_FORMATS[quoteFmt];
    const inter = DOC.isInterState('sState', 'bState');
    const rows = [];
    let sub = 0, tax = 0;

    if (f.lump) {
        const base = DOC.num('quoteLump');
        const rate = DOC.num('quoteLumpGst');
        const taxAmt = DOC.round2(base * rate / 100);
        sub = DOC.round2(base);
        tax = taxAmt;
    } else {
        document.querySelectorAll('#quoteItems .doc-item').forEach((el, i) => {
            const g = s => el.querySelector(s);
            const name = (g('[data-quote-name]').value || 'Item').trim();
            const hsn = (g('[data-quote-hsn]').value || '').trim();
            const q = parseFloat(g('[data-quote-qty]').value) || 0;
            const rate = parseFloat(g('[data-quote-rate]').value) || 0;
            const gr = parseFloat(g('[data-quote-gst]').value) || 0;
            const dr = f.disc ? (parseFloat(g('[data-quote-disc]').value) || 0) : 0;
            const unit = f.unit ? (g('[data-quote-unit]').value || '') : '';
            const gross = q * rate;
            const discAmt = DOC.round2(gross * dr / 100);
            const taxable = DOC.round2(gross - discAmt);
            const taxAmt = DOC.round2(taxable * gr / 100);
            sub += taxable; tax += taxAmt;
            g('[data-quote-amt]').textContent = DOC.money(taxable + taxAmt);
            rows.push({ i: i + 1, name: name, hsn: hsn, q: q, rate: rate, gr: gr, dr: dr, unit: unit, discAmt: discAmt, taxable: taxable, taxAmt: taxAmt, total: DOC.round2(taxable + taxAmt) });
        });
        sub = DOC.round2(sub);
        tax = DOC.round2(tax);
    }

    const manualDisc = DOC.num('quoteDisc');
    const afterDisc = DOC.round2(Math.max(0, sub - manualDisc));
    const roundOff = DOC.num('quoteRound');
    const grand = DOC.round2(afterDisc + tax + roundOff);
    const lumpRate = f.lump ? DOC.num('quoteLumpGst') : 0;

    return { f: f, inter: inter, rows: rows, sub: sub, tax: tax, manualDisc: manualDisc, afterDisc: afterDisc, roundOff: roundOff, grand: grand, lumpRate: lumpRate };
}

/* ---------- Preview render ---------- */
function quoteUpdate() {
    const d = quoteCollect();

    DOC.text('qPSName', DOC.val('sName'));
    DOC.text('qPSAddr', DOC.val('sAddr'));
    document.getElementById('qPSPhone').textContent = 'Phone: ' + (DOC.val('sPhone') || '\u2014');
    document.getElementById('qPSState').textContent = 'State: ' + (DOC.val('sState') || '\u2014');
    DOC.text('qPSGst', DOC.val('sGst'));
    DOC.showIf('qPSCode', !!DOC.val('sStateCode'));
    if (DOC.val('sStateCode')) document.getElementById('qPSCode').textContent = 'State Code: ' + DOC.val('sStateCode');
    const trade = DOC.val('sTrade');
    DOC.showIf('qPSTrade', !!trade);
    if (trade) document.getElementById('qPSTrade').textContent = trade;

    DOC.text('qPNo', DOC.val('quoteNo'));
    DOC.text('qPDate', DOC.fmtDate(DOC.val('quoteDate')));
    const validTxt = DOC.fmtDate(DOC.val('quoteValid')) || '\u2014';
    DOC.text('qPValid', validTxt);
    DOC.text('qPValidHero', validTxt);
    document.getElementById('qPStatValid').textContent = validTxt;

    DOC.text('qPBName', DOC.val('bName'));
    DOC.text('qPBAddr', DOC.val('bAddr'));
    document.getElementById('qPBPhone').textContent = 'Phone: ' + (DOC.val('bPhone') || '\u2014');
    DOC.text('qPBGst', DOC.val('bGst'));
    DOC.text('qPPos', DOC.val('bPos') || (DOC.val('bState') || '\u2014'));
    DOC.showIf('qPRefRow', !!DOC.val('bPo'));
    if (DOC.val('bPo')) document.getElementById('qPRef').textContent = DOC.val('bPo');
    DOC.showIf('qPSrcRow', !!DOC.val('quoteSource'));
    if (DOC.val('quoteSource')) document.getElementById('qPSrc').textContent = DOC.val('quoteSource');

    /* variant blocks */
    DOC.showIf('qPSiteBox', !!d.f.site);
    if (d.f.site) {
        DOC.text('qPSvDate', DOC.fmtDate(DOC.val('svDate')) || 'Not recorded');
        DOC.text('qPSvArea', DOC.val('svArea'));
        DOC.text('qPSvScope', DOC.val('svScope'));
        DOC.text('qPSvNote', DOC.val('svNote'));
    }
    DOC.showIf('qPAmcBox', !!d.f.amc);
    if (d.f.amc) {
        DOC.text('qPAmcPeriod', DOC.val('amcPeriod'));
        DOC.text('qPAmcVisits', DOC.num('amcVisits') ? DOC.qty(DOC.num('amcVisits')) + ' per year' : '');
        DOC.text('qPAmcFreq', DOC.val('amcFreq'));
        DOC.text('qPAmcResp', DOC.val('amcResp'));
        DOC.text('qPAmcRenewal', DOC.val('amcRenewal'));
    }
    DOC.showIf('qPTenderBox', !!d.f.tender);
    if (d.f.tender) {
        DOC.text('qPTdClient', DOC.val('tdClient'));
        DOC.text('qPTdRef', DOC.val('tdRef'));
        DOC.text('qPTdSubmit', DOC.fmtDate(DOC.val('tdSubmit')) || 'As per tender notice');
        DOC.text('qPTdBid', DOC.val('tdBid'));
        DOC.text('qPTdEmd', DOC.num('tdEmd') ? DOC.money(DOC.num('tdEmd')) : 'Nil');
        DOC.text('qPTdEmdNote', DOC.val('tdEmdNote'));
    }

    /* items table */
    DOC.showIf('qPTableWrap', !d.f.lump);
    DOC.showIf('qPLumpBox', !!d.f.lump);
    const cols = quoteCols(d.f);
    document.getElementById('qPHead').innerHTML = cols.map(c =>
        '<th style="background:#334155;color:#fff;padding:5px 6px;text-align:' + (c.r ? 'right' : 'left') + ';font-size:9px;">' + c.l + '</th>').join('');

    const tb = document.getElementById('qPRows');
    if (d.f.lump) {
        DOC.text('qPScope', DOC.val('quoteScope'));
        document.getElementById('qPLumpRows').innerHTML =
            '<tr><td style="padding:5px 6px;">Lump sum for the complete scope</td><td style="padding:5px 6px;text-align:right;">' + DOC.money(d.afterDisc) + '</td></tr>' +
            '<tr><td style="padding:5px 6px;">GST @ ' + DOC.qty(d.lumpRate) + '%</td><td style="padding:5px 6px;text-align:right;">' + DOC.money(d.tax) + '</td></tr>' +
            (d.roundOff !== 0 ? '<tr><td style="padding:5px 6px;">Round off</td><td style="padding:5px 6px;text-align:right;">' + (d.roundOff < 0 ? '\u2212' : '') + DOC.money(Math.abs(d.roundOff)) + '</td></tr>' : '');
    } else if (!d.rows.length) {
        tb.innerHTML = '<tr><td colspan="' + cols.length + '" style="padding:14px;text-align:center;color:#94A3B8;">No items yet</td></tr>';
    } else {
        tb.innerHTML = d.rows.map(r => {
            const cells = [];
            cols.forEach(c => {
                let v = '', al = c.r ? 'right' : 'left';
                if (c.k === 'i') v = r.i;
                else if (c.k === 'name') v = r.name;
                else if (c.k === 'hsn') v = r.hsn || '\u2014';
                else if (c.k === 'q') v = DOC.qty(r.q);
                else if (c.k === 'unit') v = r.unit || '\u2014';
                else if (c.k === 'rate') v = DOC.money(r.rate);
                else if (c.k === 'dr') v = DOC.qty(r.dr) + '%';
                else if (c.k === 'gr') v = DOC.qty(r.gr) + '%';
                else v = DOC.money(r.total);
                const w = c.b ? 'font-weight:700;' : '';
                cells.push('<td style="padding:5px 6px;text-align:' + al + ';' + w + '">' + v + '</td>');
            });
            return '<tr>' + cells.join('') + '</tr>';
        }).join('');
    }

    /* totals */
    DOC.text('qPSub', DOC.money(d.afterDisc));
    DOC.showIf('qPDiscRow', d.manualDisc > 0);
    if (d.manualDisc > 0) document.getElementById('qPDisc').textContent = '\u2212' + DOC.money(d.manualDisc);
    DOC.showIf('qPRoundRow', d.roundOff !== 0);
    if (d.roundOff !== 0) document.getElementById('qPRound').textContent = (d.roundOff < 0 ? '\u2212' : '') + DOC.money(Math.abs(d.roundOff));

    if (d.inter) {
        DOC.showIf('qPCgstRow', false); DOC.showIf('qPSgstRow', false);
        DOC.showIf('qPIgstRow', true); DOC.text('qPIgst', DOC.money(d.tax));
    } else {
        DOC.showIf('qPIgstRow', false);
        DOC.showIf('qPCgstRow', true); DOC.text('qPCgst', DOC.money(d.tax / 2));
        DOC.showIf('qPSgstRow', true); DOC.text('qPSgst', DOC.money(d.tax - d.tax / 2));
    }

    document.getElementById('qPGrandLbl').textContent = d.f.grandLbl;
    DOC.text('qPGrand', DOC.money(d.grand));
    DOC.text('qPGrandHero', DOC.money(d.grand));
    document.getElementById('qPStripTotal').textContent = DOC.money(d.grand);
    document.getElementById('qPStatTotal').textContent = DOC.money(d.grand);
    document.getElementById('qPStatItems').textContent = String(d.rows.length);

    /* toggles */
    DOC.showIf('qPWordsRow', document.getElementById('optWords').checked);
    if (document.getElementById('optWords').checked) document.getElementById('qPWords').textContent = DOC.inWords(d.grand);
    DOC.showIf('qPTermsRow', document.getElementById('optTerms').checked);

    const notes = DOC.val('quoteNotes');
    DOC.showIf('qPNotesRow', !!notes);
    if (notes) document.getElementById('qPNotes').textContent = notes;

    const bank = document.getElementById('optBank').checked;
    DOC.showIf('bankRow', bank);
    DOC.showIf('bankRow2', bank);
    DOC.showIf('qPBankRow', bank);
    if (bank) {
        const bits = [];
        if (DOC.val('bBank')) bits.push(DOC.val('bBank'));
        if (DOC.val('bAcct')) bits.push('A/c ' + DOC.val('bAcct'));
        if (DOC.val('bIfsc')) bits.push('IFSC ' + DOC.val('bIfsc'));
        if (DOC.val('bUpi')) bits.push('UPI ' + DOC.val('bUpi'));
        document.getElementById('qPBankTxt').textContent = bits.length ? bits.join(' \u00B7 ') : 'Add your bank details above';
    }

    DOC.showIf('qPSigRow', document.getElementById('optSignature').checked);
    if (document.getElementById('optSignature').checked) document.getElementById('qPSigName').textContent = DOC.val('sName') || 'the vendor';
}

/* ---------- Actions ---------- */
function quotePreviewJump() { quoteUpdate(); DOC.scrollToPreview('quotePreview'); }
function quotePrint() { quoteUpdate(); window.print(); }

function quoteReset() {
    ['sName', 'sGst', 'sPhone', 'sAddr', 'sStateCode', 'sTrade', 'bName', 'bGst', 'bPhone', 'bAddr',
        'bPos', 'bPo', 'quoteNo', 'quoteNotes', 'quoteScope', 'bBank', 'bAcct', 'bIfsc', 'bUpi',
        'svDate', 'svArea', 'svScope', 'svNote', 'tdClient', 'tdRef', 'tdSubmit'].forEach(id => {
            const e = document.getElementById(id); if (e) e.value = '';
        });
    ['quoteDisc', 'quoteRound', 'quoteLump', 'amcVisits', 'tdEmd'].forEach(id => {
        const e = document.getElementById(id); if (e) e.value = 0;
    });
    document.getElementById('sState').value = '';
    document.getElementById('bState').value = '';
    document.getElementById('quoteSource').value = '';
    document.getElementById('quoteDate').value = DOC.todayISO();
    document.getElementById('quoteValid').value = DOC.addDaysISO(15);
    document.getElementById('quoteItems').querySelectorAll('.doc-item').forEach(e => e.remove());
    quoteAddRow();
    quoteSetFormat('detailed');
    quoteUpdate();
}

function quotePDF() {
    quoteUpdate();
    const d = quoteCollect();
    const doc = DOC.newDoc();
    if (!doc) return;

    const W = doc.internal.pageSize.getWidth();
    const MG = 14;
    const accent = DOC.currentAccentRGB();

    let y = DOC.pdfHeader(doc, {
        org: DOC.val('sName') || 'Your Business',
        left: [
            { value: DOC.val('sAddr') || 'Address' },
            { value: 'Phone: ' + (DOC.val('sPhone') || '\u2014') + (DOC.val('sState') ? '  \u00B7  ' + DOC.val('sState') : '') },
            { value: 'GSTIN: ' + (DOC.val('sGst') || '\u2014') + (DOC.val('sStateCode') ? '  \u00B7  State Code: ' + DOC.val('sStateCode') : '') }
        ],
        title: d.f.title,
        right: [
            { label: d.f.noLbl, value: DOC.val('quoteNo') || '\u2014' },
            { label: 'Date', value: DOC.fmtDate(DOC.val('quoteDate')) || '\u2014' },
            { label: 'Valid until', value: DOC.fmtDate(DOC.val('quoteValid')) || '\u2014' }
        ],
        accent: accent
    });

    /* buyer block */
    y = DOC.pdfSection(doc, MG, y, 'QUOTATION FOR', 60);
    doc.setFont('helvetica', 'bold'); doc.setFontSize(9); doc.setTextColor(15, 23, 42);
    doc.text(DOC.val('bName') || 'Customer', MG, y + 4.5); y += 8;
    doc.setFont('helvetica', 'normal'); doc.setFontSize(8); doc.setTextColor(51, 65, 85);
    const bl = doc.splitTextToSize(DOC.val('bAddr') || '', 95);
    doc.text(bl, MG, y); y += bl.length * 3.6;
    doc.text('Phone: ' + (DOC.val('bPhone') || '\u2014') + '  \u00B7  GSTIN: ' + (DOC.val('bGst') || '\u2014'), MG, y); y += 4;
    doc.text('Place of supply: ' + (DOC.val('bPos') || DOC.val('bState') || '\u2014') + (DOC.val('bPo') ? '  \u00B7  Enquiry ref: ' + DOC.val('bPo') : ''), MG, y); y += 6;

    /* variant blocks */
    if (d.f.site) {
        y = DOC.pdfSection(doc, MG, y, 'SITE VISIT REPORT', 70);
        doc.setFontSize(8);
        doc.text('Survey date: ' + (DOC.fmtDate(DOC.val('svDate')) || '\u2014') + '  \u00B7  Site area: ' + (DOC.val('svArea') || '\u2014'), MG, y); y += 4;
        doc.text('Scope of work: ' + (DOC.val('svScope') || '\u2014'), MG, y, { maxWidth: W - MG * 2 }); y += 4;
        const svl = doc.splitTextToSize(DOC.val('svNote') || '', W - MG * 2);
        doc.setTextColor(146, 64, 14);
        doc.text(svl, MG, y); y += svl.length * 3.6 + 5;
        doc.setTextColor(51, 65, 85);
    }
    if (d.f.amc) {
        y = DOC.pdfSection(doc, MG, y, 'AMC / SERVICE CONTRACT', 70);
        doc.setFontSize(8);
        doc.text('Period: ' + DOC.val('amcPeriod') + '  \u00B7  Visits: ' + (DOC.qty(DOC.num('amcVisits')) || '0') + '  \u00B7  Frequency: ' + DOC.val('amcFreq'), MG, y); y += 4;
        doc.text('Response time: ' + DOC.val('amcResp'), MG, y); y += 4;
        const arl = doc.splitTextToSize('Renewal: ' + (DOC.val('amcRenewal') || '\u2014'), W - MG * 2);
        doc.text(arl, MG, y); y += arl.length * 3.6 + 5;
    }
    if (d.f.tender) {
        y = DOC.pdfSection(doc, MG, y, 'TENDER / EOI PARTICULARS', 80);
        doc.setFontSize(8);
        doc.text('Client: ' + (DOC.val('tdClient') || '\u2014') + '  \u00B7  Tender ref: ' + (DOC.val('tdRef') || '\u2014'), MG, y); y += 4;
        doc.text('Submission: ' + (DOC.fmtDate(DOC.val('tdSubmit')) || 'As per notice') + '  \u00B7  Bid validity: ' + DOC.val('tdBid'), MG, y); y += 4;
        doc.text('EMD: ' + (DOC.num('tdEmd') ? DOC.money(DOC.num('tdEmd')) : 'Nil') + '  \u00B7  ' + DOC.val('tdEmdNote'), MG, y); y += 6;
    }

    /* price block */
    if (d.f.lump) {
        const lhead = ['Particulars', 'Amount'];
        const lbody = [['Lump sum for the complete scope', DOC.money(d.afterDisc)]];
        if (d.manualDisc > 0) lbody.push(['Less: discount', '\u2212' + DOC.money(d.manualDisc)]);
        lbody.push(['GST @ ' + DOC.qty(d.lumpRate) + '%', DOC.money(d.tax)]);
        if (d.roundOff !== 0) lbody.push(['Round off', (d.roundOff < 0 ? '\u2212' : '') + DOC.money(Math.abs(d.roundOff))]);
        y = DOC.pdfTable(doc, lhead, lbody, {
            startY: y, accent: accent,
            extra: { columnStyles: { 1: { halign: 'right', fontStyle: 'bold' } } }
        }) + 5;
    } else {
        const head = quoteCols(d.f).map(c => c.l);
        const body = d.rows.length ? d.rows.map(r => [
            r.i,
            r.name + (r.unit ? ' (' + r.unit + ')' : ''),
            r.hsn || '\u2014',
            DOC.qty(r.q),
            DOC.money(r.rate),
            DOC.qty(r.dr) + '%',
            DOC.qty(r.gr) + '%',
            DOC.money(r.total)
        ]) : [['', 'No items', '', '', '', '', '', '']];

        const styles = { 0: { cellWidth: 8 } };
        quoteCols(d.f).forEach((c, idx) => { if (c.r) styles[idx] = c.b ? { halign: 'right', fontStyle: 'bold' } : { halign: 'right' }; });

        y = DOC.pdfTable(doc, head, body, { startY: y, accent: accent, extra: { columnStyles: styles } }) + 5;
    }

    /* totals */
    const rows = [{ label: 'Taxable Value', value: DOC.money(d.afterDisc) }];
    if (d.manualDisc > 0) rows.push({ label: 'Discount', value: '\u2212' + DOC.money(d.manualDisc) });
    if (d.inter) {
        rows.push({ label: 'IGST', value: DOC.money(d.tax) });
    } else {
        rows.push({ label: 'CGST', value: DOC.money(d.tax / 2) });
        rows.push({ label: 'SGST', value: DOC.money(d.tax - d.tax / 2) });
    }
    if (d.roundOff !== 0) rows.push({ label: 'Round off', value: (d.roundOff < 0 ? '\u2212' : '') + DOC.money(Math.abs(d.roundOff)) });
    rows.push({ rule: true, gap: 2 });
    rows.push({ label: d.f.grandLbl, value: DOC.money(d.grand), bold: true, size: 10.5, color: accent, gap: 6 });

    y = DOC.pdfTotals(doc, W, y, rows);

    if (document.getElementById('optWords').checked) {
        y += 2;
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Amount in words: ' + DOC.inWords(d.grand), MG, y); y += 6;
        doc.setFont('helvetica', 'normal');
    }
    if (DOC.val('quoteNotes')) {
        if (y > doc.internal.pageSize.getHeight() - 30) { doc.addPage(); y = 20; }
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Notes:', MG, y); y += 4;
        doc.setFont('helvetica', 'normal');
        const nl = doc.splitTextToSize(DOC.val('quoteNotes'), W - MG * 2);
        doc.text(nl, MG, y); y += nl.length * 3.6 + 3;
    }
    if (document.getElementById('optTerms').checked) {
        if (y > doc.internal.pageSize.getHeight() - 30) { doc.addPage(); y = 20; }
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Terms:', MG, y); y += 4;
        doc.setFont('helvetica', 'normal');
        const tl = doc.splitTextToSize(QUOTE_TERMS, W - MG * 2);
        doc.text(tl, MG, y); y += tl.length * 3.6 + 3;
    }
    if (document.getElementById('optBank').checked) {
        const bits = [];
        if (DOC.val('bBank')) bits.push(DOC.val('bBank'));
        if (DOC.val('bAcct')) bits.push('A/c ' + DOC.val('bAcct'));
        if (DOC.val('bIfsc')) bits.push('IFSC ' + DOC.val('bIfsc'));
        if (DOC.val('bUpi')) bits.push('UPI ' + DOC.val('bUpi'));
        if (bits.length) {
            if (y > doc.internal.pageSize.getHeight() - 26) { doc.addPage(); y = 20; }
            doc.setDrawColor(226, 232, 240); doc.setLineWidth(0.2);
            doc.line(MG, y, W - MG, y); y += 4;
            doc.setFontSize(7.6); doc.setTextColor(100, 116, 139);
            doc.text('Remittance details: ' + bits.join('  \u00B7  '), MG, y); y += 5;
        }
    }

    if (document.getElementById('optSignature').checked) {
        if (y > doc.internal.pageSize.getHeight() - 30) { doc.addPage(); y = 20; }
        const colW = (W - MG * 2) / 3;
        const sy = y + 12;
        doc.setDrawColor(148, 163, 184); doc.setLineWidth(0.3);
        [0, 1, 2].forEach(function (i) {
            const x = MG + colW * i + colW / 2;
            doc.line(x - 30, sy, x + 30, sy);
        });
        doc.setFontSize(7.5); doc.setTextColor(100, 116, 139);
        doc.text('Customer Acceptance', MG + colW / 2, sy + 4, { align: 'center' });
        doc.setFont('helvetica', 'bold');
        doc.text('For ' + (DOC.val('sName') || 'the vendor'), MG + colW * 1.5, sy + 4, { align: 'center' });
        doc.setFont('helvetica', 'normal');
        doc.text('Quotation Prepared By', MG + colW * 2.5, sy + 4, { align: 'center' });
        y = sy + 10;
    }

    DOC.pdfFooter(doc, y, (DOC.val('quoteNo') || 'quotation') + '  \u00B7  Valid until ' + (DOC.fmtDate(DOC.val('quoteValid')) || '\u2014'));
    doc.save(((DOC.val('quoteNo') || 'quotation').replace(/[^\w\-]+/g, '_')) + '.pdf');
}

/* ---------- Boot ---------- */
(function () {
    DOC.fillStates('sState', 'Select state');
    DOC.fillStates('bState', 'Select state');
    DOC.fillRates('quoteLumpGst');
    document.getElementById('quoteDate').value = DOC.todayISO();
    document.getElementById('quoteValid').value = DOC.addDaysISO(15);
    document.getElementById('svDate').value = DOC.todayISO();
    document.getElementById('tdSubmit').value = DOC.todayISO();

    quoteRenderFormats();
    quoteAddRow();

    const ids = ['sName', 'sGst', 'sPhone', 'sAddr', 'sState', 'sStateCode', 'sTrade',
        'bName', 'bGst', 'bPhone', 'bAddr', 'bState', 'bPos', 'bPo',
        'quoteNo', 'quoteDate', 'quoteValid', 'quoteSource', 'quoteDisc', 'quoteRound',
        'quoteLump', 'quoteLumpGst', 'quoteScope', 'quoteNotes',
        'svDate', 'svArea', 'svScope', 'svNote',
        'amcPeriod', 'amcVisits', 'amcFreq', 'amcResp', 'amcRenewal',
        'tdClient', 'tdRef', 'tdSubmit', 'tdBid', 'tdEmd', 'tdEmdNote',
        'bBank', 'bAcct', 'bIfsc', 'bUpi',
        'optSignature', 'optWords', 'optTerms', 'optBank'];
    DOC.bind(ids, quoteUpdate);

    DOC.bindSwatches('data-quote-accent', quoteUpdate);

    quoteUpdate();
})();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
