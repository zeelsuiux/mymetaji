<?php
/**
 * My Metaji - Debit Note Format
 * Lets the user create a GST debit note (document type DN, or D when raised by the
 * recipient) that adds freight, installation, interest or penalty charges to an
 * invoice already issued, preview it live and download a PDF.
 */
require_once __DIR__ . '/config.php';
$page_title = 'Free Debit Note Format — GST Debit Note Generator | ' . BRAND_NAME;
$page_desc  = 'Free GST debit note format generator. Add freight, installation, interest, shortage penalty or buyer-received debit charges to an existing invoice and download a print-ready PDF.';
include __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="gst-hero">
    <div class="gst-wrap">
        <div class="gst-hero-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"/></svg>
            GSTR-1 ready &middot; No sign-up required
        </div>
        <h1>Free <span>Debit Note</span></h1>
        <p>A debit note adds charges the supplier forgot to invoice &mdash; freight, installation, interest on a delayed payment, a shortage penalty &mdash; or records a note raised by the buyer for short or defective receipt. Fill it in and download a print-ready PDF.</p>
        <div class="gst-hero-chips">
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M7 19l5 5 5-5"/><path d="M7 5l5-5 5 5"/></svg>
                5 debit note variants
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                Instant PDF download
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 4h16v16H4z"/><path d="M8 10h8M8 14h5"/></svg>
                Revised invoice value computed
            </span>
        </div>
    </div>
</section>

<!-- ===================== TOOL ===================== -->
<section class="gst-tool" id="debit-note-tool">
    <div class="gst-wrap">
        <div class="qm-layout">

            <!-- ===== LEFT: FORM ===== -->
            <div>

                <!-- 1. VARIANT PICKER -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M8 9h8M8 13h5"/><path d="M12 16v5M9 19h6"/></svg>
                        </div>
                        <div>
                            <h3>What Are You Adding to the Invoice?</h3>
                            <p>The variant decides the ground, the pre-filled charge line and whether the note is raised by the supplier or the buyer.</p>
                        </div>
                        <span class="qm-step">Step 1</span>
                    </div>

                    <div class="fmt-grid" id="fmtGrid" role="group" aria-label="Debit note variant"></div>

                    <div class="note-box" style="margin-top:16px;">
                        <div class="note-ico" id="fmtInfoIco">&#128666;</div>
                        <div>
                            <h4 id="fmtInfoTitle">Freight / Shipping Charge Added</h4>
                            <p id="fmtInfoDesc">The invoice was raised on an ex-works basis, so transport is now billed separately. The freight is added to the invoice value with GST on top.</p>
                            <p style="margin:0;"><strong>Compulsory particulars:</strong> <span id="fmtInfoFields">Debit note number and date, original invoice number and date, description of the charge, HSN/SAC, amount of tax added</span></p>
                        </div>
                    </div>

                    <div class="doc-split" style="margin-top:18px;">
                        <div>
                            <div class="doc-opt">
                                <span>Accent colour</span>
                                <div class="doc-swatches">
                                    <button type="button" class="doc-swatch active" data-dn-accent="blue" style="background:#2563EB" aria-label="Blue"></button>
                                    <button type="button" class="doc-swatch" data-dn-accent="indigo" style="background:#4F46E5" aria-label="Indigo"></button>
                                    <button type="button" class="doc-swatch" data-dn-accent="green" style="background:#059669" aria-label="Green"></button>
                                    <button type="button" class="doc-swatch" data-dn-accent="teal" style="background:#0D9488" aria-label="Teal"></button>
                                    <button type="button" class="doc-swatch" data-dn-accent="amber" style="background:#D97706" aria-label="Amber"></button>
                                    <button type="button" class="doc-swatch" data-dn-accent="violet" style="background:#7C3AED" aria-label="Violet"></button>
                                    <button type="button" class="doc-swatch" data-dn-accent="slate" style="background:#334155" aria-label="Slate"></button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="doc-switches">
                                <label class="doc-chk"><input type="checkbox" id="optWords" checked> Amount in words</label>
                                <label class="doc-chk"><input type="checkbox" id="optSignature" checked> Seller signature</label>
                                <label class="doc-chk"><input type="checkbox" id="optAck" checked> Buyer acknowledgement</label>
                                <label class="doc-chk"><input type="checkbox" id="optTerms" checked> Terms &amp; declaration</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. DEBIT NOTE META + REFERENCE INVOICE -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 3h11l4 4v14H5z"/><path d="M9 12h6M9 16h4"/><path d="M12 9v6"/></svg>
                        </div>
                        <div>
                            <h3>Debit Note Number &amp; Reference Invoice</h3>
                            <p>The debit note must clearly state the invoice number and date it is being raised against.</p>
                        </div>
                        <span class="qm-step">Step 2</span>
                    </div>

                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label>Debit Note Number</label><input type="text" id="dnNo" placeholder="DN-001"></div>
                        <div class="inv-gen-field"><label>Debit Note Date</label><input type="date" id="dnDate"></div>
                    </div>

                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label style="color:var(--error);">Original Invoice Number (mandatory)</label><input type="text" id="invNo" placeholder="INV-001"></div>
                        <div class="inv-gen-field"><label style="color:var(--error);">Original Invoice Date (mandatory)</label><input type="date" id="invDate"></div>
                    </div>

                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label>Original Invoice Value (&#8377;)</label><input type="number" id="origTotal" value="0" min="0" step="0.01" oninput="dnUpdate()"></div>
                        <div class="inv-gen-field"><label>Place of Supply</label><input type="text" id="posNote" placeholder="e.g. Maharashtra (27)"></div>
                    </div>

                    <div class="inv-gen-field"><label>Other Reference (PO / Job Card / Contract)</label><input type="text" id="dnOtherRef" placeholder="Optional"></div>
                </div>

                <!-- 3. PARTIES -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14"/><path d="M10 12h4M10 16h4"/></svg>
                        </div>
                        <div>
                            <h3>Supplier Details</h3>
                            <p>The registered person who issued the original invoice and is adding the supplementary charge.</p>
                        </div>
                        <span class="qm-step">Step 3</span>
                    </div>
                    <div class="doc-split">
                        <div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Supplier / Business Name</label><input type="text" id="sName" placeholder="As registered with GST"></div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Supplier GSTIN</label><input type="text" id="sGst" placeholder="24AAAAA0000A1Z5" maxlength="15"></div>
                        </div>
                        <div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Phone</label><input type="tel" id="sPhone" placeholder="98765 43210"></div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>State</label><select id="sState"></select></div>
                        </div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Registered Address</label><input type="text" id="sAddr" placeholder="Street, city, state, PIN"></div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>State Code</label><input type="text" id="sStateCode" placeholder="24" maxlength="2"></div>
                        <div class="inv-gen-field"><label>Trade Name / Logo Text</label><input type="text" id="sTrade" placeholder="Optional"></div>
                    </div>

                    <div class="inv-gen-section-title" style="margin-top:24px;">Buyer (Recipient) Details</div>
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
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Billing Address</label><input type="text" id="bAddr" placeholder="Street, city, state, PIN"></div>
                </div>

                <!-- 4. GROUNDS + CHARGE LINES -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3 8-8"/><path d="M20 12v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h9"/></svg>
                        </div>
                        <div>
                            <h3>Ground for the Debit Note &amp; Charge Lines</h3>
                            <p>Tick every ground that applies. The ticked lines are compiled into a single narrative on the note.</p>
                        </div>
                        <span class="qm-step">Step 4</span>
                    </div>

                    <div class="chk-list" id="dnReasons" style="--chk-cols: 26px 1fr 138px" role="group" aria-label="Grounds for the debit note"></div>

                    <div class="note-box gold" id="dnIntNote" style="margin-top:14px;display:none;">
                        <div class="note-ico">&#9203;</div>
                        <div>
                            <h4>Interest is computed for you</h4>
                            <p style="margin:0;">Interest is <strong>principal &times; rate &times; days &divide; 365</strong>. Fill the three fields below and the interest line, the tax on it and the revised invoice value all update together. If the invoice has already been paid, mention that the interest is levied under the contract terms in the notes.</p>
                        </div>
                    </div>

                    <div class="doc-split" id="intRow" style="margin-top:16px;display:none;">
                        <div class="inv-gen-field"><label>Interest Rate (% p.a.)</label><input type="number" id="intRate" value="18" min="0" step="0.01" oninput="dnUpdate()"></div>
                        <div class="inv-gen-field"><label>Delay Period (days)</label><input type="number" id="intDays" value="45" min="0" step="1" oninput="dnUpdate()"></div>
                    </div>
                    <div class="doc-split" id="intRow2" style="margin-top:10px;display:none;">
                        <div class="inv-gen-field"><label>Principal (&#8377;)</label><input type="number" id="intPrincipal" value="0" min="0" step="0.01" oninput="dnUpdate()"></div>
                        <div class="inv-gen-field"><label>GST Rate on Interest</label><select id="intGst"></select></div>
                    </div>

                    <div class="doc-items" id="dnItems" style="--doc-cols: 2.4fr .9fr .7fr 1fr .7fr 1fr auto; margin-top:16px;">
                        <div class="doc-item-hd">
                            <span>Charge / Service Added</span><span>HSN / SAC</span><span>Qty</span><span>Rate (&#8377;)</span><span>GST %</span><span style="text-align:right">Amount</span><span></span>
                        </div>
                    </div>
                    <button type="button" class="inv-gen-add-item" onclick="dnAddRow()">+ Add Charge Line</button>
                </div>

                <!-- 5. NOTES -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>
                        </div>
                        <div>
                            <h3>Notes, Contract Terms &amp; Bank</h3>
                            <p>Point the buyer to the clause of the contract or purchase order that permits the charge.</p>
                        </div>
                        <span class="qm-step">Step 5</span>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Notes / Contract Reference</label><textarea id="dnNotes" rows="3" placeholder="Charged as per clause 4.2 of purchase order PO-2026-004 dated 12 Apr 2026. Freight billed on actual weight of 1,240 kg."></textarea></div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>Due Date of Revised Value</label><input type="date" id="dnDueDate"></div>
                        <div class="inv-gen-field"><label>Payment Mode</label>
                            <select id="dnPayMode"><option value="no">Not stated</option><option value="neft">NEFT / RTGS</option><option value="adjust">Adjusted against running balance</option><option value="next">Payable with the next invoice</option></select>
                        </div>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="qm-actions doc-no-print">
                    <button type="button" class="qm-btn qm-btn-primary" onclick="dnPreviewJump()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        Preview
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="dnPrint()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="1"/><path d="M6 17h12v4H6z"/></svg>
                        Print
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="dnReset()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                        Reset
                    </button>
                    <button type="button" class="qm-btn qm-btn-primary" style="flex:2" onclick="dnPDF()">
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
                <div class="inv-gen-preview" id="dnPreview">
                    <div class="doc-sheet doc-sheet-lg" id="dnSheet">
                        <div class="doc-hd">
                            <div class="doc-org">
                                <h2 id="pDnOrgName">Your Business</h2>
                                <p id="pDnOrgTrade" style="display:none"></p>
                                <p id="pDnOrgAddr">Address</p>
                                <p><span id="pDnOrgPhone">Phone: &mdash;</span> &middot; <span id="pDnOrgState">State: &mdash;</span></p>
                                <p><strong>GSTIN:</strong> <span id="pDnOrgGst">&mdash;</span> &middot; <span id="pDnOrgCode"></span></p>
                            </div>
                            <div class="doc-id">
                                <div class="doc-title" id="pDnTitle">DEBIT NOTE</div>
                                <p><strong id="pDnNoLbl">Debit Note No.:</strong> <span id="pDnNo">&mdash;</span></p>
                                <p><strong>Date:</strong> <span id="pDnDate">&mdash;</span></p>
                                <p id="pDnIssuedRow"><strong>Issued By:</strong> <span id="pDnIssued">&mdash;</span></p>
                                <p id="pDnOtherRefRow" style="display:none"><strong>Ref:</strong> <span id="pDnOtherRef">&mdash;</span></p>
                            </div>
                        </div>

                        <div class="note-box gold" id="pDnRefBox" style="padding:9px 12px;margin-bottom:12px;">
                            <div class="note-ico" style="font-size:15px;">&#128206;</div>
                            <div style="min-width:0;">
                                <h4 style="font-size:11px;">Against Original Invoice (mandatory)</h4>
                                <p style="margin:0;font-size:10.5px;">
                                    Invoice No. <strong id="pDnInvNo">&mdash;</strong> dated <strong id="pDnInvDate">&mdash;</strong>
                                    &middot; Invoice value <strong id="pDnOrigVal">&mdash;</strong>
                                    &middot; Place of supply <span id="pDnPos">&mdash;</span>
                                </p>
                            </div>
                        </div>

                        <div class="doc-split" style="margin-bottom:14px;">
                            <div>
                                <p class="doc-sec-title" id="pDnToTitle">Billed To (Buyer)</p>
                                <p class="inv-p-party-name" id="pDnToName" style="margin:3px 0;">Buyer</p>
                                <p id="pDnToAddr" style="font-size:10px;color:#475569;margin:1px 0;">Address</p>
                                <p id="pDnToPhone" style="font-size:10px;color:#475569;margin:1px 0;">Phone: &mdash;</p>
                                <p id="pDnToGstRow" style="font-size:10px;color:#475569;margin:2px 0;"><strong>GSTIN:</strong> <span id="pDnToGst">&mdash;</span></p>
                            </div>
                            <div>
                                <p class="doc-sec-title" id="pDnOtherTitle">Supplier</p>
                                <p id="pDnOtherName" style="margin:3px 0;font-weight:600;">Supplier</p>
                                <p id="pDnOtherGst" style="font-size:10px;color:#475569;margin:2px 0;">GSTIN: &mdash;</p>
                                <p style="font-size:10px;color:#475569;margin:2px 0;"><strong>GSTR-1 doc type:</strong> <span id="pDnDocType">DN &mdash; Debit Note</span></p>
                            </div>
                        </div>

                        <table class="inv-p-table" style="width:100%;border-collapse:collapse;margin-bottom:12px;font-size:10px;">
                            <thead><tr>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">#</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">Charge / Service Added</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">HSN / SAC</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Qty</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Rate</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">GST%</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Tax</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Amount</th>
                            </tr></thead>
                            <tbody id="pDnRows"><tr><td colspan="8" style="padding:14px;text-align:center;color:#94A3B8;">No charge lines yet</td></tr></tbody>
                        </table>

                        <div style="display:flex;justify-content:flex-end;margin-bottom:12px;">
                            <table class="inv-p-totals-table" style="font-size:10px;min-width:235px;">
                                <tr><td style="padding:3px 8px;">Original Invoice Value</td><td id="pDnOrigTotal" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr><td style="padding:3px 8px;">Taxable Value of Charges</td><td id="pDnBase" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pDnIntRow" style="display:none"><td style="padding:3px 8px;">Interest on Delayed Payment</td><td id="pDnInt" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr><td style="padding:3px 8px;">Additional Charges (taxable)</td><td id="pDnCharges" style="text-align:right;font-weight:700;">&#8377;0.00</td></tr>
                                <tr id="pDnCgstRow" style="display:none"><td style="padding:3px 8px;">CGST added</td><td id="pDnCgst" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pDnSgstRow" style="display:none"><td style="padding:3px 8px;">SGST added</td><td id="pDnSgst" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pDnIgstRow" style="display:none"><td style="padding:3px 8px;">IGST added</td><td id="pDnIgst" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr><td style="padding:3px 8px;">Tax on Added Charges</td><td id="pDnAddTax" style="text-align:right;font-weight:700;">&#8377;0.00</td></tr>
                            </table>
                        </div>

                        <div class="amt-hero">
                            <div class="amt-hero-lbl" id="pDnHeroLbl">New Invoice Value (Revised)</div>
                            <div class="amt-hero-val" id="pDnHeroVal">&#8377;0.00</div>
                        </div>

                        <div id="pDnWordsRow" style="font-size:9px;color:#475569;padding:8px 0;border-top:1px solid #E2E8F0;margin-bottom:8px;">
                            <strong>Amount in words:</strong> <span id="pDnWords">Zero Only</span>
                        </div>

                        <div class="note-box" id="pDnReasonBox" style="padding:9px 12px;margin-bottom:8px;background:#F8FAFC;border-color:#E2E8F0;display:none;">
                            <div class="note-ico" style="font-size:15px;">&#128203;</div>
                            <p style="margin:0;font-size:10px;"><strong>Grounds relied upon:</strong> <span id="pDnReason">&mdash;</span></p>
                        </div>

                        <div class="note-box green" id="pDnDeclRow" style="padding:9px 12px;margin-bottom:8px;">
                            <div class="note-ico" style="font-size:15px;">&#10003;</div>
                            <p style="margin:0;font-size:10px;" id="pDnDecl">&mdash;</p>
                        </div>

                        <div id="pDnNotesRow" style="display:none;font-size:9px;color:#475569;padding:6px 0;">
                            <strong>Notes:</strong> <span id="pDnNotes"></span>
                        </div>

                        <div id="pDnPayRow" style="display:none;font-size:9px;color:#475569;padding:6px 0;">
                            <strong>Revised value payable by:</strong> <span id="pDnPayMode">&mdash;</span>
                        </div>

                        <div class="doc-sig" id="pDnSigRow">
                            <div></div>
                            <div><div class="doc-sig-line">For <span id="pDnSigName">&mdash;</span></div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Authorised Signatory</div></div>
                        </div>

                        <div class="doc-sig" id="pDnAckRow" style="grid-template-columns:1fr 1fr;">
                            <div><div class="doc-sig-line">Noted and agreed to pay</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Buyer / Recipient</div></div>
                            <div><div class="doc-sig-line" id="pDnAckName2">&mdash;</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Name of the other party</div></div>
                        </div>

                        <div class="doc-ft">This is a computer-generated debit note &middot; Generated by <strong>My Metaji</strong></div>
                    </div>
                </div>
                <div class="qm-total-strip">
                    <span>New Invoice Value</span>
                    <strong id="pDnStripTotal">&#8377;0.00</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== COMPARISON TABLE ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>Debit Note vs Credit Note &mdash; Know the Difference</h2>
        <p>A debit note pushes the invoice value up; a credit note pulls it down. They are reported to the GST department under different document types, and confusing the two is one of the most frequent reasons an ITC claim gets pulled up in assessment.</p>
        <div class="fmt-table-wrap" style="margin-top:20px;">
            <table class="fmt-table">
                <thead>
                    <tr><th>Point of comparison</th><th>Debit Note</th><th>Credit Note</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Who issues it</strong></td>
                        <td>Supplier adding charges to an invoice already raised (or the buyer, for short / defective receipt)</td>
                        <td>Supplier, against an invoice he has already raised</td>
                    </tr>
                    <tr>
                        <td><strong>Effect on the invoice value</strong></td>
                        <td>Increases it &mdash; the buyer has to pay more</td>
                        <td>Reduces it &mdash; the buyer owes less, or gets a refund</td>
                    </tr>
                    <tr>
                        <td><strong>Effect on GST</strong></td>
                        <td>Adds new tax on the supplementary charge. The original tax is <em>not</em> reduced</td>
                        <td>Reverses the tax already charged (100% on full cancellation, proportionate on a partial return)</td>
                    </tr>
                    <tr>
                        <td><strong>GSTR-1 document type</strong></td>
                        <td><span class="fmt-req yes">DN &mdash; Debit Note</span> by the supplier &middot; <span class="fmt-req cond">D &mdash; by the recipient</span></td>
                        <td><span class="fmt-req yes">C &mdash; Credit Note</span></td>
                    </tr>
                    <tr>
                        <td><strong>Typical reason</strong></td>
                        <td>Freight, installation, interest on delay, job-work charges, shortage penalty</td>
                        <td>Goods returned, damaged, short supplied, or a price correction after invoicing</td>
                    </tr>
                    <tr>
                        <td><strong>Compulsory particulars</strong></td>
                        <td><span class="fmt-req yes">Invoice no. &amp; date</span> <span class="fmt-req yes">Charges added</span> <span class="fmt-req cond">Reason if additional</span></td>
                        <td><span class="fmt-req yes">Invoice no. &amp; date</span> <span class="fmt-req yes">Reason</span> <span class="fmt-req yes">Goods credited</span> <span class="fmt-req yes">Tax reversed</span></td>
                    </tr>
                    <tr>
                        <td><strong>Number series</strong></td>
                        <td>Separate series, typically DN-001 onwards</td>
                        <td>Separate series, typically CN-001 onwards</td>
                    </tr>
                    <tr>
                        <td><strong>Time limit</strong></td>
                        <td>Best raised in the same month the extra charge is incurred</td>
                        <td>Up to 30 November of the following financial year (Section 34)</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="note-box gold" style="margin-top:20px;">
            <div class="note-ico">&#128203;</div>
            <div>
                <h4>A debit note adds to the invoice &mdash; it never reduces it</h4>
                <ul>
                    <li>Report the debit note in <strong>GSTR-1</strong> as document type <strong>DN</strong> in the month it is issued, quoting the original invoice number and date.</li>
                    <li>Where the note is raised by the <strong>recipient</strong> against the supplier, the document type is <strong>D</strong>, not DN. This is the variant that carries a shortage or defect claim.</li>
                    <li>A debit note does <strong>not</strong> reduce the supplier's tax liability on the original supply. It only <strong>adds</strong> to the invoice value, and the tax on the added charge is a fresh liability in the period of the note.</li>
                    <li>Registered persons with aggregate turnover above <strong>&#8377;5 crore</strong> must report debit note particulars under <strong>Rules 34A and 34B</strong>, including the number series and the linked invoice.</li>
                    <li>Freight and installation billed after the invoice should match the original supply's HSN or the correct service SAC. Interest on delayed payment is normally invoiced with the same rate of GST as the principal supply.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===================== HOW TO ===================== -->
<section class="gst-faq" style="background:#fff;">
    <div class="gst-wrap">
        <h2>How to Create a GST Debit Note</h2>
        <div class="gst-how-grid">
            <div class="gst-how-card">
                <div class="gst-how-num">1</div>
                <h3>Choose the Charge Type</h3>
                <p>Freight, installation, interest, shortage penalty or a note raised by the buyer. The grounds checklist and a starter charge line load instantly.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">2</div>
                <h3>Reference the Invoice</h3>
                <p>Enter the number, date and value of the invoice being topped up. The revised value is computed from those three numbers, not guessed.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">3</div>
                <h3>Add Charge Lines &amp; Grounds</h3>
                <p>Each line carries its own HSN or SAC, quantity, rate and GST slab. Tick the grounds that apply and they compile into the note narrative.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">4</div>
                <h3>Download and File It</h3>
                <p>Get the PDF signed by the buyer, then report the same debit note number in GSTR-1 for the month in which it was issued.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SEO + RELATED ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>What is a Debit Note?</h2>
        <p>A <strong>debit note</strong> is a document used to add supplementary amounts to a tax invoice that has already been issued, without having to cancel and re-issue that invoice. The usual triggers are charges that simply could not be computed at the time of billing &mdash; freight and packing on an ex-works sale, installation and commissioning, annual maintenance, job-work or subcontracting charges, and interest on a delayed payment. It may also carry a contractual penalty, for instance a shortage in delivered quantity or damage in transit that the contract makes the buyer's cost. A debit note must state the number and date of the invoice it refers to, describe each charge with its HSN or SAC code, and show the rate and amount of tax added. In GSTR-1 it is reported as document type <strong>DN</strong>, in its own number series (DN-001, DN-002 &hellip;), alongside the original invoice reference.</p>
        <p>The point most people get wrong is the tax effect. A debit note does <strong>not</strong> reduce the supplier's liability on the original supply &mdash; that is what a credit note does. It simply increases the value of the supply, and the GST charged on the added amount becomes a fresh liability for the period in which the note is issued. There is also a distinct case worth knowing: where a buyer finds a short supply or defective goods on inward inspection, the <em>recipient</em> raises a debit note against the supplier, and that document is reported in GSTR-1 as type <strong>D</strong> rather than DN, because it reduces the recipient's input tax credit rather than the supplier's output liability. This tool handles both situations, splits the tax correctly into CGST and SGST or a single IGST line based on the two states, and prints the revised invoice value in figures and in words.</p>

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
        <h2>Billing Software That Raises the Debit Note for You</h2>
        <p><?= BRAND_NAME ?> adds freight, installation and interest to the original invoice automatically, numbers the note in its own series and keeps GSTR-1 ready to file.</p>
        <a href="#download" class="gst-cta-btn">Download <?= BRAND_NAME ?> Free &rarr;</a>
    </div>
</section>

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="assets/js/doc-utils.js"></script>
<script>
/* ---------- Debit note variant definitions ---------- */
const DN_FORMATS = {
    freight: {
        label: 'Freight / Shipping Charge Added', ico: '\u{1F69A}', accent: '#0D9488',
        tag: 'Ex-works sale, transport billed later', tags: ['Freight', 'SAC 996511', 'Transport'],
        title: 'DEBIT NOTE', noLbl: 'Debit Note No.', noHint: 'DN-001', reverse: false, interest: false,
        toTitle: 'Billed To (Buyer)', otherTitle: 'Supplier', docType: 'DN \u2014 Debit Note',
        seed: { name: 'Freight and packing charges', hsn: '996511', qty: 1, rate: 0, gst: '5' },
        grounds: [
            { key: 'freight', text: 'Freight / carriage charged on the consignment, not billed earlier' },
            { key: 'packing', text: 'Packing, loading and handling charges incurred by the transporter' },
            { key: 'insurance', text: 'Transit insurance premium on the shipped consignment' },
            { key: 'cartage', text: 'Cartage, unloading and site delivery charges' }
        ],
        desc: 'The invoice was raised on an ex-works basis, so transport is now billed separately. The freight is added to the invoice value with GST on top.',
        note: 'The original supply was invoiced on an ex-works basis. Transport, packing and allied charges that became payable after dispatch are being debited here and added to the value of that supply.'
    },
    install: {
        label: 'Installation & Commissioning', ico: '\u{1F6E0}', accent: '#2563EB',
        tag: 'Service charges billed after supply', tags: ['Installation', 'SAC 998719', 'Site work'],
        title: 'DEBIT NOTE', noLbl: 'Debit Note No.', noHint: 'DN-001', reverse: false, interest: false,
        toTitle: 'Billed To (Buyer)', otherTitle: 'Supplier', docType: 'DN \u2014 Debit Note',
        seed: { name: 'Installation and commissioning charges', hsn: '998719', qty: 1, rate: 0, gst: '18' },
        grounds: [
            { key: 'install', text: 'Installation, erection and commissioning carried out at site' },
            { key: 'testing', text: 'On-site testing, trial run and performance demonstration' },
            { key: 'training', text: 'Operator training and hand-over of the equipment' },
            { key: 'amc', text: 'Annual maintenance or service contract charges' }
        ],
        desc: 'Installation, commissioning and site service were completed after the invoice was raised, so they are billed now as a separate service line with the correct SAC.',
        note: 'The installation, commissioning and site service obligations under the referenced invoice have since been discharged. The corresponding service charges, together with the GST applicable thereon, are debited to the buyer.'
    },
    interest: {
        label: 'Interest on Delayed Payment', ico: '\u{1F4B8}', accent: '#D97706',
        tag: 'Rate % and period computed', tags: ['Interest', 'Per annum', 'Auto-calculated'],
        title: 'DEBIT NOTE', noLbl: 'Debit Note No.', noHint: 'DN-001', reverse: false, interest: true,
        toTitle: 'Billed To (Buyer)', otherTitle: 'Supplier', docType: 'DN \u2014 Debit Note',
        seed: { name: 'Interest on delayed payment of the invoice value', hsn: '997311', qty: 1, rate: 0, gst: '18' },
        grounds: [
            { key: 'interest', text: 'Interest on the invoice value outstanding beyond the agreed credit period' },
            { key: 'interestPenalty', text: 'Penalty for delay in acceptance beyond the contractual date' },
            { key: 'interestNote', text: 'Interest charged as liquidated damages under the signed contract' }
        ],
        desc: 'Interest is calculated as principal x rate x days / 365. Fill the rate and the delay period and the interest line, its tax and the revised invoice value all update together.',
        note: 'Interest is charged on the outstanding value of the referenced invoice for the period of delay, computed on a simple interest basis as principal multiplied by the rate per annum divided by 365, for the number of days delayed.'
    },
    penalty: {
        label: 'Shortage / Damage Penalty', ico: '\u{1F6A8}', accent: '#DC2626',
        tag: 'Contractual penalty charged to buyer', tags: ['Shortage', 'Damage', 'Liquidated damages'],
        title: 'DEBIT NOTE', noLbl: 'Debit Note No.', noHint: 'DN-001', reverse: false, interest: false,
        toTitle: 'Billed To (Buyer)', otherTitle: 'Supplier', docType: 'DN \u2014 Debit Note',
        seed: { name: 'Penalty for shortage in delivered quantity', hsn: '996511', qty: 1, rate: 0, gst: '18' },
        grounds: [
            { key: 'shortQty', text: 'Shortage in the quantity delivered against the invoice' },
            { key: 'damage', text: 'Damage in transit, made the buyer\u2019s cost by the contract' },
            { key: 'ld', text: 'Liquidated damages for delay, as per the delivery clause' },
            { key: 'rework', text: 'Cost of re-work or re-inspection of the rejected material' }
        ],
        desc: 'A contractual penalty is charged to the buyer for a shortage in delivered quantity, transit damage or delay. The penalty is added to the invoice value with GST on top.',
        note: 'Penalty is charged under the delivery and liquidated damages clauses of the contract for the reason recorded against this note. The penalty amount, together with the GST applicable thereon, is debited to the buyer and added to the value of the supply.'
    },
    buyer: {
        label: 'Debit Note by Buyer (Recipient)', ico: '\u{1F6D2}', accent: '#7C3AED',
        tag: 'Issued by the recipient', tags: ['Document type D', 'Short receipt', 'Defective goods'],
        title: 'DEBIT NOTE (BY RECIPIENT)', noLbl: 'Debit Note No.', noHint: 'DN-001', reverse: true, interest: false,
        toTitle: 'Debit To (Supplier)', otherTitle: 'Recipient / Buyer', docType: 'D \u2014 Debit Note by Recipient',
        seed: { name: 'Value of goods short received / rejected on inward inspection', hsn: '', qty: 1, rate: 0, gst: '18' },
        grounds: [
            { key: 'short', text: 'Goods received short by the quantity stated in this note' },
            { key: 'defect', text: 'Damage or manufacturing defect found on inward inspection' },
            { key: 'rejection', text: 'Balance quantity rejected by the recipient' },
            { key: 'substitute', text: 'Wrong grade, size or specification supplied in place of the order' }
        ],
        desc: 'The buyer found a shortfall or defective goods on inward inspection and raises a debit note against the supplier. Report it in GSTR-1 as document type D.',
        note: 'The goods received against the referenced invoice were found short in quantity and / or defective on inward inspection. The recipient has therefore raised this debit note against the supplier for the value of the shortage or the defect, together with the GST applicable thereon.'
    }
};

let dnFmt = 'freight';
let dnRowId = 0;

const DN_PAY_LABEL = {
    neft: 'NEFT / RTGS to the supplier bank account on record',
    adjust: 'Adjusted against the running balance of the account',
    next: 'Payable along with the next invoice raised by the supplier'
};

/* ---------- Variant picker ---------- */
function dnRenderFormats() {
    const grid = document.getElementById('fmtGrid');
    let h = '';
    Object.keys(DN_FORMATS).forEach(function (k) {
        const f = DN_FORMATS[k];
        h += '<button type="button" class="fmt-card' + (k === dnFmt ? ' active' : '') + '" data-dn-fmt="' + k + '" aria-pressed="' + (k === dnFmt) + '">' +
            '<span class="fmt-mini" style="--fmt-accent:' + f.accent + '">' +
                '<span class="fmt-mini-top"><span class="fmt-mini-brand"></span><span class="fmt-mini-tag"></span></span>' +
                '<span class="fmt-mini-ln w70"></span><span class="fmt-mini-ln w45"></span><span class="fmt-mini-ln w70"></span>' +
                '<span class="fmt-mini-tot"></span>' +
            '</span>' +
            '<span class="fmt-card-body">' +
                '<span class="fmt-card-title">' + f.ico + ' ' + f.label + '</span>' +
                '<span class="fmt-card-sub">' + f.tag + '</span>' +
                '<span class="fmt-tags">' + f.tags.map(function (t) { return '<span class="fmt-tag">' + t + '</span>'; }).join('') + '</span>' +
            '</span>' +
        '</button>';
    });
    grid.innerHTML = h;
    grid.querySelectorAll('[data-dn-fmt]').forEach(function (b) {
        b.addEventListener('click', function () { dnSetFormat(b.dataset.dnFmt); });
    });
    dnSetFormat(dnFmt, true);
}

function dnRenderGrounds() {
    const f = DN_FORMATS[dnFmt];
    const wrap = document.getElementById('dnReasons');
    let h = '<div class="chk-hd"><span></span><span>Ground relied upon</span><span style="text-align:right">Amount (' + DOC.RUPEE + ')</span></div>';
    f.grounds.forEach(function (g, i) {
        h += '<div class="chk-row" data-dn-chk="' + g.key + '">' +
            '<button type="button" class="chk-box" onclick="dnToggleChk(' + i + ')" aria-pressed="false" title="' + g.text + '" aria-label="' + g.text + '">' +
                '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12l6 6L20 6"/></svg>' +
            '</button>' +
            '<span class="chk-txt" style="white-space:normal;">' + g.text + '</span>' +
            '<input type="number" data-dn-camt="' + i + '" value="0" min="0" step="0.01" oninput="dnUpdate()" ' +
                'style="width:100%;padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:12.5px;font-family:inherit;text-align:right;" aria-label="Amount for ' + g.text + '">' +
        '</div>';
    });
    wrap.innerHTML = h;
}

function dnSetFormat(k, quiet) {
    const f = DN_FORMATS[k];
    if (!f) return;
    dnFmt = k;

    document.querySelectorAll('[data-dn-fmt]').forEach(function (b) {
        const on = b.dataset.dnFmt === k;
        b.classList.toggle('active', on);
        b.setAttribute('aria-pressed', on);
    });

    document.getElementById('fmtInfoIco').textContent = f.ico;
    document.getElementById('fmtInfoTitle').textContent = f.label;
    document.getElementById('fmtInfoDesc').textContent = f.desc;

    const cur = DOC.val('dnNo');
    if (!cur || /^(CN|DN)-\d{3}$/.test(cur)) document.getElementById('dnNo').value = f.noHint;

    document.getElementById('intRow').style.display = f.interest ? '' : 'none';
    document.getElementById('intRow2').style.display = f.interest ? '' : 'none';
    document.getElementById('dnIntNote').style.display = f.interest ? '' : 'none';

    document.getElementById('pDnTitle').textContent = f.title;
    document.getElementById('pDnNoLbl').textContent = f.noLbl + ':';
    document.getElementById('pDnToTitle').textContent = f.toTitle;
    document.getElementById('pDnOtherTitle').textContent = f.otherTitle;
    document.getElementById('pDnDocType').textContent = f.docType;
    document.getElementById('pDnHeroLbl').textContent = f.reverse ? 'Net Amount Debited by the Recipient' : 'New Invoice Value (Revised)';
    document.getElementById('pDnIssued').textContent = f.reverse ? 'Recipient / Buyer' : 'Supplier';

    if (!quiet) {
        dnRenderGrounds();
        dnSeedRow(f.seed);
        dnUpdate();
    }
}

function dnIsSeedName(nm) {
    return Object.keys(DN_FORMATS).some(function (k) { return DN_FORMATS[k].seed.name === nm; });
}

function dnSeedRow(seed) {
    const rows = document.getElementById('dnItems').querySelectorAll('.doc-item');
    if (rows.length !== 1) return;
    const g = function (s) { return rows[0].querySelector(s); };
    const nm = g('[data-dn-name]').value.trim();
    const rate = parseFloat(g('[data-dn-rate]').value) || 0;
    if (rate !== 0 || (nm !== '' && !dnIsSeedName(nm))) return;
    g('[data-dn-name]').value = seed.name;
    g('[data-dn-hsn]').value = seed.hsn;
    g('[data-dn-qty]').value = seed.qty;
    g('[data-dn-rate]').value = seed.rate;
    g('[data-dn-gst]').value = seed.gst;
}

function dnToggleChk(i) {
    const rows = document.querySelectorAll('#dnReasons .chk-row');
    const row = rows[i];
    if (!row) return;
    const box = row.querySelector('.chk-box');
    if (!box) return;
    box.classList.toggle('on');
    box.setAttribute('aria-pressed', box.classList.contains('on'));
    dnUpdate();
}

/* ---------- Charge rows ---------- */
function dnAddRow(seed) {
    seed = seed || {};
    const id = dnRowId++;
    document.getElementById('dnItems').insertAdjacentHTML('beforeend',
        '<div class="doc-item" data-row="' + id + '">' +
            '<input type="text" data-dn-name placeholder="Charge or service added" oninput="dnUpdate()">' +
            '<input type="text" data-dn-hsn placeholder="HSN / SAC" maxlength="8" oninput="dnUpdate()">' +
            '<input type="number" data-dn-qty value="' + (seed.qty || 1) + '" min="0" step="0.01" oninput="dnUpdate()">' +
            '<input type="number" data-dn-rate value="0" min="0" step="0.01" oninput="dnUpdate()">' +
            '<select data-dn-gst onchange="dnUpdate()">' + DOC.GST_RATES.map(function (r) {
                return '<option value="' + r + '"' + (r === '18' ? ' selected' : '') + '>' + r + '%</option>';
            }).join('') + '</select>' +
            '<span class="doc-item-amt" data-dn-amt>&#8377;0.00</span>' +
            '<button type="button" class="doc-item-del" onclick="dnDelRow(' + id + ')" title="Remove charge line" aria-label="Remove charge line">&times;</button>' +
        '</div>');
    dnUpdate();
}

function dnDelRow(id) {
    const e = document.querySelector('[data-row="' + id + '"]');
    if (e) e.remove();
    dnUpdate();
}

/* ---------- Totals engine ---------- */
function dnCheckedGrounds() {
    const f = DN_FORMATS[dnFmt];
    const out = [];
    document.querySelectorAll('#dnReasons .chk-row').forEach(function (el) {
        const box = el.querySelector('.chk-box');
        if (!box || !box.classList.contains('on')) return;
        const amtIn = el.querySelector('[data-dn-camt]');
        let g = f.grounds.filter(function (x) { return x.key === el.dataset.dnChk; })[0];
        out.push({ key: el.dataset.dnChk, text: g ? g.text : '', amt: amtIn ? (parseFloat(amtIn.value) || 0) : 0 });
    });
    return out;
}

function dnCollect() {
    const f = DN_FORMATS[dnFmt];
    const inter = DOC.isInterState('sState', 'bState');
    const origTotal = DOC.round2(DOC.num('origTotal'));
    const rows = [];
    let base = 0, tax = 0;

    document.querySelectorAll('#dnItems .doc-item').forEach(function (el, i) {
        const g = function (s) { return el.querySelector(s); };
        const name = (g('[data-dn-name]').value || 'Charge').trim();
        const hsn = (g('[data-dn-hsn]').value || '').trim();
        const q = parseFloat(g('[data-dn-qty]').value) || 0;
        const rate = parseFloat(g('[data-dn-rate]').value) || 0;
        const gr = parseFloat(g('[data-dn-gst]').value) || 0;
        const taxable = DOC.round2(q * rate);
        const taxAmt = DOC.round2(taxable * gr / 100);
        base = base + taxable;
        tax = tax + taxAmt;
        g('[data-dn-amt]').textContent = DOC.money(DOC.round2(taxable + taxAmt));
        rows.push({ i: i + 1, name: name, hsn: hsn, q: q, rate: rate, gr: gr, taxable: taxable, taxAmt: taxAmt, total: DOC.round2(taxable + taxAmt) });
    });

    base = DOC.round2(base);
    tax = DOC.round2(tax);

    let intBase = 0, intTax = 0, intMeta = null;
    if (f.interest) {
        const principal = DOC.round2(DOC.num('intPrincipal') || origTotal);
        const rate = DOC.num('intRate');
        const days = DOC.num('intDays');
        const gr = DOC.num('intGst');
        intBase = DOC.round2(principal * rate / 100 * days / 365);
        intTax = DOC.round2(intBase * gr / 100);
        intMeta = { principal: principal, rate: rate, days: days, gr: gr };
    }

    const charges = DOC.round2(base + intBase);
    const addTax = DOC.round2(tax + intTax);
    const revised = DOC.round2(origTotal + charges + addTax);
    const cgst = DOC.round2(addTax / 2);
    const sgst = DOC.round2(addTax - cgst);
    const grounds = dnCheckedGrounds();

    return {
        f: f, inter: inter, rows: rows, origTotal: origTotal, base: base, tax: tax,
        intBase: intBase, intTax: intTax, intMeta: intMeta, charges: charges, addTax: addTax,
        revised: revised, cgst: cgst, sgst: sgst, grounds: grounds
    };
}

/* ---------- Preview render ---------- */
function dnUpdate() {
    const d = dnCollect();
    const rev = d.f.reverse;

    DOC.text('pDnOrgName', rev ? DOC.val('bName') : DOC.val('sName'));
    DOC.text('pDnOrgAddr', rev ? DOC.val('bAddr') : DOC.val('sAddr'));
    DOC.text('pDnOrgPhone', 'Phone: ' + (rev ? (DOC.val('bPhone') || '\u2014') : (DOC.val('sPhone') || '\u2014')));
    DOC.text('pDnOrgState', 'State: ' + (rev ? (DOC.val('bState') || '\u2014') : (DOC.val('sState') || '\u2014')));
    DOC.text('pDnOrgGst', rev ? DOC.val('bGst') : DOC.val('sGst'));
    DOC.text('pDnOrgCode', (!rev && DOC.val('sStateCode')) ? 'State Code: ' + DOC.val('sStateCode') : '');
    DOC.showIf('pDnOrgCode', !rev && !!DOC.val('sStateCode'));
    const trade = DOC.val('sTrade');
    DOC.showIf('pDnOrgTrade', !!trade);
    if (trade) document.getElementById('pDnOrgTrade').textContent = trade;

    DOC.text('pDnNo', DOC.val('dnNo'));
    DOC.text('pDnDate', DOC.fmtDate(DOC.val('dnDate')));
    document.getElementById('pDnIssued').textContent = rev ? 'Recipient / Buyer' : 'Supplier';

    DOC.text('pDnInvNo', DOC.val('invNo'));
    DOC.text('pDnInvDate', DOC.fmtDate(DOC.val('invDate')));
    DOC.text('pDnOrigVal', DOC.money(d.origTotal));
    DOC.text('pDnPos', DOC.val('posNote') || (DOC.val('bState') || '\u2014'));

    const oref = DOC.val('dnOtherRef');
    DOC.showIf('pDnOtherRefRow', !!oref);
    if (oref) document.getElementById('pDnOtherRef').textContent = oref;

    DOC.text('pDnToName', rev ? (DOC.val('sName') || 'Supplier') : (DOC.val('bName') || 'Buyer'));
    DOC.text('pDnToAddr', rev ? DOC.val('sAddr') : DOC.val('bAddr'));
    DOC.text('pDnToPhone', 'Phone: ' + (rev ? (DOC.val('sPhone') || '\u2014') : (DOC.val('bPhone') || '\u2014')));
    DOC.text('pDnToGst', rev ? DOC.val('sGst') : DOC.val('bGst'));
    DOC.showIf('pDnToGstRow', true);

    DOC.text('pDnOtherName', rev ? (DOC.val('bName') || 'Recipient / Buyer') : (DOC.val('sName') || 'Supplier'));
    DOC.text('pDnOtherGst', 'GSTIN: ' + (rev ? (DOC.val('bGst') || '\u2014') : (DOC.val('sGst') || '\u2014')));

    /* charge table */
    const tb = document.getElementById('pDnRows');
    if (!d.rows.length) {
        tb.innerHTML = '<tr><td colspan="8" style="padding:14px;text-align:center;color:#94A3B8;">No charge lines yet</td></tr>';
    } else {
        tb.innerHTML = d.rows.map(function (r) {
            return '<tr>' +
                '<td>' + r.i + '</td>' +
                '<td>' + r.name + '</td>' +
                '<td>' + (r.hsn || '\u2014') + '</td>' +
                '<td style="text-align:right;">' + DOC.qty(r.q) + '</td>' +
                '<td style="text-align:right;">' + DOC.money(r.rate) + '</td>' +
                '<td style="text-align:right;">' + r.gr + '%</td>' +
                '<td style="text-align:right;">' + DOC.money(r.taxAmt) + '</td>' +
                '<td style="text-align:right;font-weight:700;">' + DOC.money(r.total) + '</td>' +
            '</tr>';
        }).join('');
    }

    /* totals */
    DOC.text('pDnOrigTotal', DOC.money(d.origTotal));
    DOC.text('pDnBase', DOC.money(d.base));
    DOC.showIf('pDnIntRow', !!d.intMeta);
    if (d.intMeta) {
        document.getElementById('pDnInt').textContent = DOC.money(d.intBase) +
            '  (' + DOC.qty(d.intMeta.rate) + '% for ' + DOC.qty(d.intMeta.days) + ' days)';
    }
    DOC.text('pDnCharges', DOC.money(d.charges));
    if (d.inter) {
        DOC.showIf('pDnCgstRow', false); DOC.showIf('pDnSgstRow', false);
        DOC.showIf('pDnIgstRow', true); DOC.text('pDnIgst', DOC.money(d.addTax));
    } else {
        DOC.showIf('pDnIgstRow', false);
        DOC.showIf('pDnCgstRow', true); DOC.text('pDnCgst', DOC.money(d.cgst));
        DOC.showIf('pDnSgstRow', true); DOC.text('pDnSgst', DOC.money(d.sgst));
    }
    DOC.text('pDnAddTax', DOC.money(d.addTax));
    DOC.text('pDnHeroVal', DOC.money(d.revised));
    document.getElementById('pDnStripTotal').textContent = DOC.money(d.revised);

    const words = DOC.val('optWords');
    DOC.showIf('pDnWordsRow', words);
    if (words) document.getElementById('pDnWords').textContent = DOC.inWords(d.revised);

    /* narrative from the ticked grounds */
    DOC.showIf('pDnReasonBox', d.grounds.length > 0);
    if (d.grounds.length) {
        document.getElementById('pDnReason').textContent = d.grounds.map(function (g, i) {
            return (i + 1) + ') ' + g.text + (g.amt > 0 ? ' \u2014 ' + DOC.money(g.amt) : '');
        }).join('  ');
    }

    const refNo = DOC.val('invNo') || '________';
    const refDate = DOC.fmtDate(DOC.val('invDate')) || '________';
    document.getElementById('pDnDecl').textContent = rev
        ? 'We debit ' + (DOC.val('sName') || 'the supplier') + ' for the amount mentioned above against the invoice no. ' + refNo + ' dated ' + refDate + ' received from us. The amount stated includes GST.'
        : 'We debit the amount mentioned above against our invoice no. ' + refNo + ' dated ' + refDate + '. The charges added above, inclusive of GST, take the value of that invoice to ' + DOC.money(d.revised) + '.';

    const notes = DOC.val('dnNotes');
    DOC.showIf('pDnNotesRow', !!notes);
    if (notes) document.getElementById('pDnNotes').textContent = notes;

    const mode = DOC.val('dnPayMode');
    DOC.showIf('pDnPayRow', mode !== 'no');
    if (mode !== 'no') {
        const bits = [DN_PAY_LABEL[mode] || ''];
        if (DOC.val('dnDueDate')) bits.push('Due by ' + DOC.fmtDate(DOC.val('dnDueDate')));
        document.getElementById('pDnPayMode').textContent = bits.filter(Boolean).join(' \u00B7 ');
    }

    const sigOn = DOC.val('optSignature');
    DOC.showIf('pDnSigRow', sigOn);
    if (sigOn) {
        document.getElementById('pDnSigName').textContent = rev ? (DOC.val('bName') || 'the recipient') : (DOC.val('sName') || 'the supplier');
    }
    const ackOn = DOC.val('optAck');
    DOC.showIf('pDnAckRow', ackOn);
    if (ackOn) {
        document.getElementById('pDnAckName2').textContent = rev ? (DOC.val('sName') || 'Supplier') : (DOC.val('bName') || 'Buyer');
    }
    DOC.showIf('pDnDeclRow', DOC.val('optTerms'));
}

/* ---------- Actions ---------- */
function dnPreviewJump() { dnUpdate(); DOC.scrollToPreview('dnPreview'); }
function dnPrint() { dnUpdate(); window.print(); }

function dnReset() {
    ['dnNo', 'dnOtherRef', 'dnNotes', 'posNote', 'invNo', 'sName', 'sGst', 'sPhone',
        'sAddr', 'sStateCode', 'sTrade', 'bName', 'bGst', 'bPhone', 'bAddr'].forEach(function (id) {
        const e = document.getElementById(id); if (e) e.value = '';
    });
    document.getElementById('origTotal').value = 0;
    document.getElementById('intRate').value = 18;
    document.getElementById('intDays').value = 45;
    document.getElementById('intPrincipal').value = 0;
    document.getElementById('dnPayMode').value = 'no';
    document.getElementById('sState').value = '';
    document.getElementById('bState').value = '';
    document.getElementById('dnDate').value = DOC.todayISO();
    document.getElementById('invDate').value = DOC.addDaysISO(-7);
    document.getElementById('dnDueDate').value = DOC.addDaysISO(15);
    document.getElementById('optWords').checked = true;
    document.getElementById('optSignature').checked = true;
    document.getElementById('optAck').checked = true;
    document.getElementById('optTerms').checked = true;
    document.getElementById('dnItems').querySelectorAll('.doc-item').forEach(function (e) { e.remove(); });
    dnAddRow();
    dnSetFormat('freight');
    dnUpdate();
}

/* ---------- PDF ---------- */
function dnPDF() {
    dnUpdate();
    const d = dnCollect();
    const doc = DOC.newDoc();
    if (!doc) return;

    const W = doc.internal.pageSize.getWidth();
    const H = doc.internal.pageSize.getHeight();
    const MG = 14;
    const accent = DOC.currentAccentRGB();
    const rev = d.f.reverse;

    let y = DOC.pdfHeader(doc, {
        org: rev ? (DOC.val('bName') || 'Your Business') : (DOC.val('sName') || 'Your Business'),
        left: [
            { value: (rev ? DOC.val('bAddr') : DOC.val('sAddr')) || 'Address' },
            { value: 'Phone: ' + ((rev ? DOC.val('bPhone') : DOC.val('sPhone')) || '\u2014') + ((rev ? DOC.val('bState') : DOC.val('sState')) ? '  \u00B7  ' + (rev ? DOC.val('bState') : DOC.val('sState')) : '') },
            { value: 'GSTIN: ' + ((rev ? DOC.val('bGst') : DOC.val('sGst')) || '\u2014') + (!rev && DOC.val('sStateCode') ? '  \u00B7  State Code: ' + DOC.val('sStateCode') : '') }
        ],
        title: d.f.title,
        right: [
            { label: d.f.noLbl, value: DOC.val('dnNo') || '\u2014' },
            { label: 'Date', value: DOC.fmtDate(DOC.val('dnDate')) || '\u2014' },
            { label: 'Issued by', value: rev ? 'Recipient / Buyer' : 'Supplier' }
        ],
        accent: accent
    });

    y = DOC.pdfSection(doc, MG, y, 'AGAINST ORIGINAL INVOICE', W - MG * 2);
    doc.setFontSize(8.5);
    doc.setTextColor(30, 41, 59);
    doc.text('Invoice No.: ' + (DOC.val('invNo') || '\u2014') + '     Invoice Date: ' + (DOC.fmtDate(DOC.val('invDate')) || '\u2014'), MG, y); y += 4.4;
    doc.setTextColor(51, 65, 85);
    doc.text('Original Invoice Value: ' + DOC.money(d.origTotal) + '     Place of Supply: ' + (DOC.val('posNote') || DOC.val('bState') || '\u2014'), MG, y); y += 4.4;
    if (DOC.val('dnOtherRef')) { doc.text('Other Reference: ' + DOC.val('dnOtherRef'), MG, y); y += 4.4; }
    doc.setTextColor(120, 113, 108);
    doc.text('GSTR-1 document type: ' + d.f.docType, MG, y); y += 7;

    if (y > H - 40) { doc.addPage(); y = 18; }
    y = DOC.pdfSection(doc, MG, y, d.f.toTitle.toUpperCase(), 90);
    doc.setFont('helvetica', 'bold'); doc.setFontSize(9); doc.setTextColor(15, 23, 42);
    doc.text(rev ? (DOC.val('sName') || 'Supplier') : (DOC.val('bName') || 'Buyer'), MG, y + 4.5); y += 8;
    doc.setFont('helvetica', 'normal'); doc.setFontSize(8);
    (function () {
        const lines = doc.splitTextToSize((rev ? DOC.val('sAddr') : DOC.val('bAddr')) || '', 100);
        doc.text(lines, MG, y); y += lines.length * 3.6;
    })();
    doc.text('Phone: ' + ((rev ? DOC.val('sPhone') : DOC.val('bPhone')) || '\u2014') + '   \u00B7   GSTIN: ' + ((rev ? DOC.val('sGst') : DOC.val('bGst')) || '\u2014'), MG, y); y += 7;

    const head = ['#', 'Charge / Service Added', 'HSN / SAC', 'Qty', 'Rate', 'GST%', 'Tax', 'Amount'];
    const body = d.rows.length ? d.rows.map(function (r) {
        return [r.i, r.name, r.hsn || '\u2014', DOC.qty(r.q), DOC.money(r.rate), r.gr + '%',
            DOC.money(r.taxAmt), DOC.money(r.total)];
    }) : [['', 'No charge lines', '', '', '', '', '', '']];

    y = DOC.pdfTable(doc, head, body, {
        startY: y, accent: accent,
        extra: {
            columnStyles: {
                0: { cellWidth: 8 },
                3: { halign: 'right' }, 4: { halign: 'right' }, 5: { halign: 'right' },
                6: { halign: 'right' }, 7: { halign: 'right', fontStyle: 'bold' }
            }
        }
    }) + 6;

    const rows = [
        { label: 'Original Invoice Value', value: DOC.money(d.origTotal) },
        { label: 'Taxable Value of Charges', value: DOC.money(d.base) }
    ];
    if (d.intMeta) {
        rows.push({
            label: 'Interest @ ' + DOC.qty(d.intMeta.rate) + '% for ' + DOC.qty(d.intMeta.days) + ' days',
            value: DOC.money(d.intBase)
        });
    }
    rows.push({ label: 'Additional Charges (taxable)', value: DOC.money(d.charges), bold: true });
    if (d.inter) {
        rows.push({ label: 'IGST added', value: DOC.money(d.addTax) });
    } else {
        rows.push({ label: 'CGST added', value: DOC.money(d.cgst) });
        rows.push({ label: 'SGST added', value: DOC.money(d.sgst) });
    }
    rows.push({ label: 'Tax on Added Charges', value: DOC.money(d.addTax), bold: true });
    rows.push({ rule: true, gap: 2 });
    rows.push({
        label: rev ? 'Net Amount Debited by Recipient' : 'New Invoice Value (Revised)',
        value: DOC.money(d.revised), bold: true, size: 10.5, color: accent, gap: 7
    });

    y = DOC.pdfTotals(doc, W, y, rows);

    if (DOC.val('optWords')) {
        y += 1;
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Amount in words: ' + DOC.inWords(d.revised), MG, y); y += 6;
        doc.setFont('helvetica', 'normal');
    }

    if (d.grounds.length) {
        if (y > H - 40) { doc.addPage(); y = 18; }
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Grounds relied upon:', MG, y); y += 4;
        doc.setFont('helvetica', 'normal');
        d.grounds.forEach(function (g, i) {
            const line = (i + 1) + ') ' + g.text + (g.amt > 0 ? '  \u2014  ' + DOC.money(g.amt) : '');
            const gl = doc.splitTextToSize(line, W - MG * 2 - 4);
            doc.text(gl, MG + 4, y); y += gl.length * 3.6;
        });
        y += 3;
    }

    if (DOC.val('optTerms')) {
        if (y > H - 40) { doc.addPage(); y = 18; }
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Declaration:', MG, y); y += 4;
        doc.setFont('helvetica', 'normal');
        const dl = doc.splitTextToSize(rev
            ? 'We, the recipient, debit ' + (DOC.val('sName') || 'the supplier') + ' for the amount mentioned above against the invoice no. ' +
                (DOC.val('invNo') || '________') + ' dated ' + (DOC.fmtDate(DOC.val('invDate')) || '________') +
                ' received from us. The amount stated includes GST. This note reduces the input tax credit available to us on that invoice.'
            : 'We debit the amount mentioned above against our invoice no. ' +
                (DOC.val('invNo') || '________') + ' dated ' + (DOC.fmtDate(DOC.val('invDate')) || '________') +
                '. The amount stated includes GST. This debit note increases the value of that invoice and does not reduce the tax already charged on it.',
            W - MG * 2);
        doc.text(dl, MG, y); y += dl.length * 3.6 + 3;
    }

    if (DOC.val('dnNotes')) {
        if (y > H - 40) { doc.addPage(); y = 18; }
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Notes:', MG, y); y += 4;
        doc.setFont('helvetica', 'normal');
        const nl = doc.splitTextToSize(DOC.val('dnNotes'), W - MG * 2);
        doc.text(nl, MG, y); y += nl.length * 3.6 + 3;
    }

    if (DOC.val('optAck') || DOC.val('optSignature')) {
        if (y > H - 34) { doc.addPage(); y = 20; }
        const boxes = [];
        if (DOC.val('optSignature')) boxes.push({ line: 'For ' + (rev ? (DOC.val('bName') || 'the recipient') : (DOC.val('sName') || 'the supplier')), sub: 'Authorised Signatory' });
        if (DOC.val('optAck')) boxes.push({ line: 'Noted and agreed to pay \u2014 ' + (rev ? (DOC.val('sName') || 'Supplier') : (DOC.val('bName') || 'Buyer')), sub: 'Counter-signed by the other party' });
        const colW = (W - MG * 2) / boxes.length;
        const sy = y + 13;
        doc.setDrawColor(148, 163, 184); doc.setLineWidth(0.3);
        boxes.forEach(function (b, i) {
            const cx = MG + colW * i + colW / 2;
            doc.line(cx - colW / 2 + 6, sy, cx + colW / 2 - 6, sy);
            doc.setFontSize(7.5); doc.setTextColor(100, 116, 139);
            doc.text(b.line, cx, sy + 4, { align: 'center' });
            doc.setFontSize(6.8); doc.setTextColor(148, 163, 184);
            doc.text(b.sub, cx, sy + 7.6, { align: 'center' });
        });
        y = sy + 12;
    }

    DOC.pdfFooter(doc, y, (DOC.val('dnNo') || 'debit note') + '  \u00B7  against invoice ' + (DOC.val('invNo') || '\u2014'));
    doc.save(((DOC.val('dnNo') || 'debit-note').replace(/[^\w\-]+/g, '_')) + '.pdf');
}

/* ---------- Boot ---------- */
(function () {
    DOC.fillStates('sState', 'Select state');
    DOC.fillStates('bState', 'Select state');
    DOC.fillRates('intGst');
    document.getElementById('dnDate').value = DOC.todayISO();
    document.getElementById('invDate').value = DOC.addDaysISO(-7);
    document.getElementById('dnDueDate').value = DOC.addDaysISO(15);
    dnRenderFormats();
    dnRenderGrounds();
    dnAddRow();
    dnSeedRow(DN_FORMATS[dnFmt].seed);
    dnUpdate();

    const ids = ['dnNo', 'dnDate', 'dnOtherRef', 'dnNotes', 'dnPayMode', 'dnDueDate',
        'invNo', 'invDate', 'origTotal', 'posNote',
        'sName', 'sGst', 'sPhone', 'sAddr', 'sState', 'sStateCode', 'sTrade',
        'bName', 'bGst', 'bPhone', 'bAddr', 'bState',
        'intRate', 'intDays', 'intPrincipal', 'intGst',
        'optWords', 'optSignature', 'optAck', 'optTerms'];
    DOC.bind(ids, dnUpdate);

    DOC.bindSwatches('data-dn-accent', dnUpdate);
})();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
