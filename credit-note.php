<?php
/**
 * My Metaji - Credit Note Format
 * Lets the user create a GST credit note (document type C) or a recipient-issued
 * debit note against a previously raised invoice, preview it live and download a PDF.
 */
require_once __DIR__ . '/config.php';
$page_title = 'Free Credit Note Format — GST Credit Note / CN Generator | ' . BRAND_NAME;
$page_desc  = 'Free GST credit note format generator. Issue a Section 34 credit note for goods return, damage, short supply or price correction, report it in GSTR-1 as type C and download a PDF.';
include __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="gst-hero">
    <div class="gst-wrap">
        <div class="gst-hero-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"/></svg>
            Section 34 compliant &middot; No sign-up required
        </div>
        <h1>Free <span>Credit Note</span></h1>
        <p>A credit note is how a supplier withdraws tax he already charged. Pick the reason, enter the original invoice number and date, and the tax reversal is worked out for you &mdash; then report it in GSTR-1 as document type C.</p>
        <div class="gst-hero-chips">
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 3v18M7 7h7a3 3 0 0 1 0 6H7"/></svg>
                5 credit note reasons
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                Instant PDF download
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                Auto CGST / SGST / IGST reversal
            </span>
        </div>
    </div>
</section>

<!-- ===================== TOOL ===================== -->
<section class="gst-tool" id="credit-note-tool">
    <div class="gst-wrap">
        <div class="qm-layout">

            <!-- ===== LEFT: FORM ===== -->
            <div>

                <!-- 1. REASON PICKER -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M8 9h8M8 13h5"/><path d="M3 21 21 3"/></svg>
                        </div>
                        <div>
                            <h3>Why Is the Credit Note Being Raised?</h3>
                            <p>The reason drives the narration, the default credit note number and the basis of reversal.</p>
                        </div>
                        <span class="qm-step">Step 1</span>
                    </div>

                    <div class="fmt-grid" id="fmtGrid" role="group" aria-label="Credit note reason"></div>

                    <div class="note-box" style="margin-top:16px;">
                        <div class="note-ico" id="fmtInfoIco">&#128221;</div>
                        <div>
                            <h4 id="fmtInfoTitle">Goods Return / Full Cancellation</h4>
                            <p id="fmtInfoDesc">The buyer returned the entire consignment. The full invoice value and the whole of the GST charged on it are reversed.</p>
                            <p style="margin:0;"><strong>Compulsory particulars:</strong> <span id="fmtInfoFields">Original invoice number and date, reason for issue, description of goods credited, rate and amount of tax reversed</span></p>
                        </div>
                    </div>

                    <div class="doc-split" style="margin-top:18px;">
                        <div>
                            <div class="doc-opt">
                                <span>Accent colour</span>
                                <div class="doc-swatches">
                                    <button type="button" class="doc-swatch active" data-cn-accent="blue" style="background:#2563EB" aria-label="Blue"></button>
                                    <button type="button" class="doc-swatch" data-cn-accent="indigo" style="background:#4F46E5" aria-label="Indigo"></button>
                                    <button type="button" class="doc-swatch" data-cn-accent="green" style="background:#059669" aria-label="Green"></button>
                                    <button type="button" class="doc-swatch" data-cn-accent="red" style="background:#DC2626" aria-label="Red"></button>
                                    <button type="button" class="doc-swatch" data-cn-accent="amber" style="background:#D97706" aria-label="Amber"></button>
                                    <button type="button" class="doc-swatch" data-cn-accent="teal" style="background:#0D9488" aria-label="Teal"></button>
                                    <button type="button" class="doc-swatch" data-cn-accent="slate" style="background:#334155" aria-label="Slate"></button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="doc-switches">
                                <label class="doc-chk"><input type="checkbox" id="optOrigAuto" checked> Auto-calculate invoice value</label>
                                <label class="doc-chk"><input type="checkbox" id="optWords" checked> Amount in words</label>
                                <label class="doc-chk"><input type="checkbox" id="optSignature" checked> Seller signature</label>
                                <label class="doc-chk"><input type="checkbox" id="optAck" checked> Buyer acknowledgement</label>
                                <label class="doc-chk"><input type="checkbox" id="optTerms" checked> Terms &amp; declaration</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. CREDIT NOTE META + REFERENCE INVOICE -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 3h11l4 4v14H5z"/><path d="M9 12h6M9 16h4"/></svg>
                        </div>
                        <div>
                            <h3>Credit Note Number &amp; Reference Invoice</h3>
                            <p>The original invoice number and date are compulsory under Section 34 of the CGST Act.</p>
                        </div>
                        <span class="qm-step">Step 2</span>
                    </div>

                    <div class="note-box red" style="margin-bottom:16px;">
                        <div class="note-ico">&#9888;&#65039;</div>
                        <div>
                            <h4>Run a separate number series</h4>
                            <p style="margin:0;">A credit note needs <strong>its own series</strong> (CN-001, CN-002 &hellip;) that never overlaps your invoice series. The invoice number you enter below is only the <strong>reference</strong> being corrected &mdash; it is not the number of this document.</p>
                        </div>
                    </div>

                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label>Credit Note Number</label><input type="text" id="cnNo" placeholder="CN-001"></div>
                        <div class="inv-gen-field"><label>Credit Note Date</label><input type="date" id="cnDate"></div>
                    </div>

                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label style="color:var(--error);">Original Invoice Number (mandatory)</label><input type="text" id="invNo" placeholder="INV-001"></div>
                        <div class="inv-gen-field"><label style="color:var(--error);">Original Invoice Date (mandatory)</label><input type="date" id="invDate"></div>
                    </div>

                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label>Original Invoice Value (&#8377;)</label><input type="number" id="origTotal" value="0" min="0" step="0.01" oninput="cnUpdate()"></div>
                        <div class="inv-gen-field"><label>Place of Supply</label><input type="text" id="posNote" placeholder="e.g. Gujarat (24)"></div>
                    </div>
                </div>

                <!-- 3. PARTIES -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14"/><path d="M10 12h4M10 16h4"/></svg>
                        </div>
                        <div>
                            <h3>Supplier (Issuer) Details</h3>
                            <p>The registered person who issued the original invoice and is now reversing the tax.</p>
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

                <!-- 4. REASON + ITEMS -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h10M4 18h7"/><path d="M18 15l3 3-3 3"/></svg>
                        </div>
                        <div>
                            <h3>Reason &amp; Credited Items</h3>
                            <p>Enter the invoiced quantity and the quantity actually credited. Tax is reversed only on the credited quantity.</p>
                        </div>
                        <span class="qm-step">Step 4</span>
                    </div>

                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label>Reason for Issue</label><select id="cnReason"></select></div>
                        <div class="inv-gen-field"><label>Other Reference (PO / GRN / Debit Note)</label><input type="text" id="cnOtherRef" placeholder="Optional"></div>
                    </div>

                    <div class="inv-gen-field" style="margin-bottom:16px;"><label>Reason Narration (auto-filled, edit freely)</label><textarea id="cnReasonText" rows="3" placeholder="Goods returned in full against the referenced invoice."></textarea></div>

                    <div class="doc-items" id="cnItems" style="--doc-cols: 2.2fr .8fr .75fr .75fr .9fr .7fr 1fr auto">
                        <div class="doc-item-hd">
                            <span>Goods / Service Credited</span><span>HSN / SAC</span><span>Invoice Qty</span><span>Credited Qty</span><span>Rate (&#8377;)</span><span>GST %</span><span style="text-align:right">Tax Reversed</span><span></span>
                        </div>
                    </div>
                    <button type="button" class="inv-gen-add-item" onclick="cnAddRow()">+ Add Credited Item</button>
                </div>

                <!-- 5. NOTES -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>
                        </div>
                        <div>
                            <h3>Additional Notes &amp; Terms</h3>
                            <p>Return logistics, bank refund details, or the acceptance terms printed on the credit note.</p>
                        </div>
                        <span class="qm-step">Step 5</span>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Notes / Terms</label><textarea id="cnNotes" rows="3" placeholder="Refunded to the buyer's account by NEFT within 7 working days. Subject to reconciliation in GSTR-1 and GSTR-3B."></textarea></div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>Refund / Adjustment Mode</label>
                            <select id="cnRefund"><option value="no">No mode stated</option><option value="neft">NEFT / RTGS to bank account</option><option value="adjust">Adjusted against next invoice</option><option value="cash">Paid in cash (TDS as applicable)</option><option value="next">Carry forward to next bill</option></select>
                        </div>
                        <div class="inv-gen-field"><label>Expected Credit Date</label><input type="date" id="cnCreditDate"></div>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="qm-actions doc-no-print">
                    <button type="button" class="qm-btn qm-btn-primary" onclick="cnPreviewJump()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        Preview
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="cnPrint()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="1"/><path d="M6 17h12v4H6z"/></svg>
                        Print
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="cnReset()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                        Reset
                    </button>
                    <button type="button" class="qm-btn qm-btn-primary" style="flex:2" onclick="cnPDF()">
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
                <div class="inv-gen-preview" id="cnPreview">
                    <div class="doc-sheet doc-sheet-lg" id="cnSheet">
                        <div class="doc-hd">
                            <div class="doc-org">
                                <h2 id="pCnOrgName">Your Business</h2>
                                <p id="pCnOrgTrade" style="display:none"></p>
                                <p id="pCnOrgAddr">Address</p>
                                <p><span id="pCnOrgPhone">Phone: &mdash;</span> &middot; <span id="pCnOrgState">State: &mdash;</span></p>
                                <p><strong>GSTIN:</strong> <span id="pCnOrgGst">&mdash;</span> &middot; <span id="pCnOrgCode"></span></p>
                            </div>
                            <div class="doc-id">
                                <div class="doc-title" id="pCnTitle">CREDIT NOTE</div>
                                <p><strong id="pCnNoLbl">Credit Note No.:</strong> <span id="pCnNo">&mdash;</span></p>
                                <p><strong>Date:</strong> <span id="pCnDate">&mdash;</span></p>
                                <p id="pCnIssuedRow"><strong>Issued By:</strong> <span id="pCnIssued">&mdash;</span></p>
                                <p id="pCnOtherRefRow" style="display:none"><strong>Ref:</strong> <span id="pCnOtherRef">&mdash;</span></p>
                            </div>
                        </div>

                        <div class="note-box red" id="pCnRefBox" style="padding:9px 12px;margin-bottom:12px;">
                            <div class="note-ico" style="font-size:15px;">&#128206;</div>
                            <div style="min-width:0;">
                                <h4 style="font-size:11px;">Against Original Invoice (mandatory)</h4>
                                <p style="margin:0;font-size:10.5px;">
                                    Invoice No. <strong id="pCnInvNo">&mdash;</strong> dated <strong id="pCnInvDate">&mdash;</strong>
                                    &middot; Invoice value <strong id="pCnInvValue">&mdash;</strong>
                                    &middot; Place of supply <span id="pCnPos">&mdash;</span>
                                </p>
                            </div>
                        </div>

                        <div class="doc-split" style="margin-bottom:14px;">
                            <div>
                                <p class="doc-sec-title" id="pCnToTitle">Credit To (Buyer)</p>
                                <p class="inv-p-party-name" id="pCnToName" style="margin:3px 0;">Buyer</p>
                                <p id="pCnToAddr" style="font-size:10px;color:#475569;margin:1px 0;">Address</p>
                                <p id="pCnToPhone" style="font-size:10px;color:#475569;margin:1px 0;">Phone: &mdash;</p>
                                <p id="pCnToGstRow" style="font-size:10px;color:#475569;margin:2px 0;"><strong>GSTIN:</strong> <span id="pCnToGst">&mdash;</span></p>
                            </div>
                            <div>
                                <p class="doc-sec-title" id="pCnOtherTitle">Supplier</p>
                                <p id="pCnOtherName" style="margin:3px 0;font-weight:600;">Supplier</p>
                                <p id="pCnOtherGst" style="font-size:10px;color:#475569;margin:2px 0;">GSTIN: &mdash;</p>
                                <p style="font-size:10px;color:#475569;margin:2px 0;"><strong>GSTR-1 doc type:</strong> <span id="pCnDocType">C &mdash; Credit Note</span></p>
                            </div>
                        </div>

                        <table class="inv-p-table" style="width:100%;border-collapse:collapse;margin-bottom:12px;font-size:10px;">
                            <thead><tr>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">#</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">Goods / Service Credited</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">HSN</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Inv Qty</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Credit Qty</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Rate</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">GST%</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Tax Rev.</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Credit</th>
                            </tr></thead>
                            <tbody id="pCnRows"><tr><td colspan="9" style="padding:14px;text-align:center;color:#94A3B8;">No items yet</td></tr></tbody>
                        </table>

                        <div style="display:flex;justify-content:flex-end;margin-bottom:12px;">
                            <table class="inv-p-totals-table" style="font-size:10px;min-width:230px;">
                                <tr><td style="padding:3px 8px;">Original Invoice Value</td><td id="pCnOrigVal" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr><td style="padding:3px 8px;">Taxable Value Credited</td><td id="pCnSub" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pCnCgstRow" style="display:none"><td style="padding:3px 8px;">CGST reversed</td><td id="pCnCgst" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pCnSgstRow" style="display:none"><td style="padding:3px 8px;">SGST reversed</td><td id="pCnSgst" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pCnIgstRow" style="display:none"><td style="padding:3px 8px;">IGST reversed</td><td id="pCnIgst" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr style="border-top:2px solid #1E293B;"><td style="padding:6px 8px;font-weight:800;font-size:11px;color:#B91C1C;">Total Tax Reversed</td><td style="padding:6px 8px;text-align:right;font-weight:800;font-size:11px;color:#B91C1C;" id="pCnTaxRev">&minus;&#8377;0.00</td></tr>
                            </table>
                        </div>

                        <div class="amt-hero credit">
                            <div class="amt-hero-lbl" id="pCnHeroLbl">Net Credit Amount</div>
                            <div class="amt-hero-val" id="pCnHeroVal">&#8377;0.00</div>
                        </div>

                        <div id="pCnWordsRow" style="font-size:9px;color:#475569;padding:8px 0;border-top:1px solid #E2E8F0;margin-bottom:8px;">
                            <strong>Amount in words:</strong> <span id="pCnWords">Zero Only</span>
                        </div>

                        <div class="note-box" id="pCnReasonBox" style="padding:9px 12px;margin-bottom:8px;background:#F8FAFC;border-color:#E2E8F0;">
                            <div class="note-ico" style="font-size:15px;">&#128221;</div>
                            <p style="margin:0;font-size:10px;"><strong>Reason:</strong> <span id="pCnReasonText">&mdash;</span></p>
                        </div>

                        <div class="note-box green" id="pCnDeclRow" style="padding:9px 12px;margin-bottom:8px;">
                            <div class="note-ico" style="font-size:15px;">&#10003;</div>
                            <p style="margin:0;font-size:10px;" id="pCnDecl">&mdash;</p>
                        </div>

                        <div id="pCnNotesRow" style="display:none;font-size:9px;color:#475569;padding:6px 0;">
                            <strong>Notes:</strong> <span id="pCnNotes"></span>
                        </div>

                        <div id="pCnRefundRow" style="display:none;font-size:9px;color:#475569;padding:6px 0;">
                            <strong>Credit note to be settled by:</strong> <span id="pCnRefund">&mdash;</span>
                        </div>

                        <div class="doc-sig" id="pCnSigRow">
                            <div></div>
                            <div><div class="doc-sig-line">For <span id="pCnSigName">&mdash;</span></div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Authorised Signatory</div></div>
                        </div>

                        <div class="doc-sig" id="pCnAckRow" style="grid-template-columns:1fr 1fr;">
                            <div><div class="doc-sig-line">Received and agreed</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Buyer / Recipient</div></div>
                            <div><div class="doc-sig-line" id="pCnAckName2">&mdash;</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Date</div></div>
                        </div>

                        <div class="doc-ft">This is a computer-generated credit note &middot; Generated by <strong>My Metaji</strong></div>
                    </div>
                </div>
                <div class="qm-total-strip">
                    <span>Net Credit Amount</span>
                    <strong id="pCnStripTotal">&#8377;0.00</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== COMPARISON TABLE ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>Credit Note vs Debit Note &mdash; Know the Difference</h2>
        <p>Both documents post against a previous invoice, but they move the number in opposite directions and they are reported to the GST department in different ways. Picking the wrong one means the tax is either reversed when it should have been added, or added when it should have been reversed.</p>
        <div class="fmt-table-wrap" style="margin-top:20px;">
            <table class="fmt-table">
                <thead>
                    <tr><th>Point of comparison</th><th>Credit Note</th><th>Debit Note</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Who issues it</strong></td>
                        <td>Supplier, against an invoice he has already raised</td>
                        <td>Supplier, to add charges to an invoice already raised (or the buyer, for short / defective receipt)</td>
                    </tr>
                    <tr>
                        <td><strong>Effect on the invoice value</strong></td>
                        <td>Reduces it &mdash; the buyer owes less, or gets a refund</td>
                        <td>Increases it &mdash; the buyer has to pay more</td>
                    </tr>
                    <tr>
                        <td><strong>Effect on GST</strong></td>
                        <td>Reverses the tax already charged (100% on full cancellation, proportionate on a partial return)</td>
                        <td>Adds new tax on the supplementary charge. The original tax is <em>not</em> reduced</td>
                    </tr>
                    <tr>
                        <td><strong>GSTR-1 document type</strong></td>
                        <td><span class="fmt-req yes">C &mdash; Credit Note</span></td>
                        <td><span class="fmt-req yes">DN &mdash; Debit Note</span> by the supplier &middot; <span class="fmt-req cond">D &mdash; by the recipient</span></td>
                    </tr>
                    <tr>
                        <td><strong>Typical reason</strong></td>
                        <td>Goods returned, damaged, short supplied, or a price correction after invoicing</td>
                        <td>Freight, installation, interest on delay, job-work charges, shortage penalty</td>
                    </tr>
                    <tr>
                        <td><strong>Compulsory particulars</strong></td>
                        <td><span class="fmt-req yes">Invoice no. &amp; date</span> <span class="fmt-req yes">Reason</span> <span class="fmt-req yes">Goods credited</span> <span class="fmt-req yes">Tax reversed</span></td>
                        <td><span class="fmt-req yes">Invoice no. &amp; date</span> <span class="fmt-req yes">Charges added</span> <span class="fmt-req cond">Reason if additional</span></td>
                    </tr>
                    <tr>
                        <td><strong>Number series</strong></td>
                        <td>Separate series, typically CN-001 onwards</td>
                        <td>Separate series, typically DN-001 onwards</td>
                    </tr>
                    <tr>
                        <td><strong>Time limit</strong></td>
                        <td>Up to 30 November of the following financial year (Section 34)</td>
                        <td>Best raised in the same month the extra charge is incurred</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="note-box gold" style="margin-top:20px;">
            <div class="note-ico">&#128203;</div>
            <div>
                <h4>Report it in the same return, or expect an ITC reversal order</h4>
                <ul>
                    <li>Report the credit note in <strong>GSTR-1</strong> as document type <strong>C</strong> in the month it is issued, quoting the original invoice number and date.</li>
                    <li>The tax reversal is carried through <strong>GSTR-3B table 4B</strong> (credit / debit notes), so the GST you collected on the original invoice comes back in the same period.</li>
                    <li>Registered persons with aggregate turnover above <strong>&#8377;5 crore</strong> must also furnish full particulars of the credit note &mdash; its number, the series it belongs to and the linked invoice &mdash; under <strong>Rules 34A and 34B</strong>.</li>
                    <li>Section 34(2) allows a credit note to be issued no later than <strong>30 November of the following financial year</strong>. After that date the only correct route is a refund claim or a write-off in the books.</li>
                    <li>Every credit note must be traceable in the annual <strong>GSTR-9</strong> reconciliation. A mismatch between the credit note, GSTR-1 and GSTR-3B is the most common trigger for an input tax credit reversal under Section 74.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===================== HOW TO ===================== -->
<section class="gst-faq" style="background:#fff;">
    <div class="gst-wrap">
        <h2>How to Create a GST Credit Note</h2>
        <div class="gst-how-grid">
            <div class="gst-how-card">
                <div class="gst-how-num">1</div>
                <h3>Pick the Reason</h3>
                <p>Goods return, damage, short supply, price correction or a note raised by the buyer. The narration and the reversal basis are filled in for you.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">2</div>
                <h3>Enter the Reference Invoice</h3>
                <p>The number and date of the invoice being corrected. Without these the credit note is not valid under Section 34 and the buyer cannot reverse input credit.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">3</div>
                <h3>Credit the Exact Quantity</h3>
                <p>Enter the invoiced quantity and the quantity actually credited. Tax is reversed only on the credited quantity, split into CGST and SGST or IGST.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">4</div>
                <h3>Get It Signed and Filed</h3>
                <p>Download the PDF, get the buyer's acknowledgement, and report the same number in GSTR-1 and GSTR-3B in the month of issue.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SEO + RELATED ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>What is a Credit Note?</h2>
        <p>A <strong>credit note</strong> is a document issued by a supplier to reduce or completely withdraw a tax invoice he has already issued. Once an invoice has been reported in GSTR-1 and the GST on it deposited through GSTR-3B, the supplier cannot simply edit the invoice. The lawful way to correct it is to raise a credit note in a series of its own &mdash; CN-001, CN-002 and so on &mdash; that clearly states the number and date of the invoice being corrected, the reason for the credit, the description and quantity of the goods or services being credited, the rate of tax and the amount of tax being reversed. Section 34 of the CGST Act makes those particulars compulsory; a credit note missing the reference invoice number cannot be defended in an audit.</p>
        <p>Tax reversal is proportional to what is credited. A complete cancellation of a supply reverses 100% of the GST that was charged. A partial return of two of ten units reverses tax on two units. A price correction where the invoice rate was too high reverses only the difference between the invoiced value and the corrected value &mdash; the quantity and the original rate stay on the books. Once the credit note is issued, the supplier reports it in GSTR-1 as document type <strong>C</strong> and the reversal flows into GSTR-3B table 4B, while the buyer reduces his input tax credit correspondingly. This tool builds the note with the correct own-number series, computes the CGST/SGST or IGST reversal line by line, and prints the amount in words with signature blocks for both the supplier and the buyer's acknowledgement &mdash; the combination that makes a credit note defensible if the department ever questions it.</p>

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
        <h2>Never Write a Credit Note by Hand Again</h2>
        <p><?= BRAND_NAME ?> raises a credit note the moment a return is entered, numbers it in its own series, reverses the tax and keeps GSTR-1 and GSTR-3B ready to file.</p>
        <a href="#download" class="gst-cta-btn">Download <?= BRAND_NAME ?> Free &rarr;</a>
    </div>
</section>

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="assets/js/doc-utils.js"></script>
<script>
/* ---------- Credit note reason definitions ---------- */
const CN_FORMATS = {
    return: {
        label: 'Goods Return / Full Cancellation', ico: '\u{1F4E6}', accent: '#DC2626',
        tag: 'Reverses 100% of the tax', tags: ['Full reversal', 'Section 34', 'GSTR-1 type C'],
        title: 'CREDIT NOTE', noLbl: 'Credit Note No.', noHint: 'CN-001', reverse: false,
        toTitle: 'Credit To (Buyer)', otherTitle: 'Supplier', docType: 'C \u2014 Credit Note',
        basis: 'Reversal basis: 100% of the tax on the original invoice is reversed because the supply is cancelled in full.',
        desc: 'The buyer returned the entire consignment. The full invoice value and the whole of the GST charged on it are reversed in the books and in the return.',
        reason: 'The goods supplied under the referenced invoice have been returned by the buyer in full and accepted by us. The complete invoice value together with the GST charged thereon is hereby reversed and credited to the buyer.'
    },
    damage: {
        label: 'Damaged / Defective Goods', ico: '\u{1F6A8}', accent: '#D97706',
        tag: 'Breakage or manufacturing defect', tags: ['Partial credit', 'Inspection', 'Goods in transit'],
        title: 'CREDIT NOTE', noLbl: 'Credit Note No.', noHint: 'CN-001', reverse: false,
        toTitle: 'Credit To (Buyer)', otherTitle: 'Supplier', docType: 'C \u2014 Credit Note',
        basis: 'Reversal basis: tax is reversed only on the quantity found damaged or defective, not on the whole invoice.',
        desc: 'Goods were damaged in transit or carry a manufacturing defect. Credit is granted on the rejected quantity only, so the reversal is proportionate.',
        reason: 'On inward inspection the goods noted below were found damaged in transit / defective in manufacture. The affected quantity is therefore rejected and the value of the same together with the GST thereon is credited to the buyer.'
    },
    short: {
        label: 'Short Supply / Shortage', ico: '\u{1F4E5}', accent: '#4F46E5',
        tag: 'Quantity not delivered', tags: ['Short qty', 'Proportional', 'GRN linked'],
        title: 'CREDIT NOTE', noLbl: 'Credit Note No.', noHint: 'CN-001', reverse: false,
        toTitle: 'Credit To (Buyer)', otherTitle: 'Supplier', docType: 'C \u2014 Credit Note',
        basis: 'Reversal basis: the credit equals the quantity actually short against the quantity invoiced, valued at the invoiced rate.',
        desc: 'The despatch was short of the invoiced quantity. Credit is calculated on the shortfall, so tax is reversed on exactly the units that were never supplied.',
        reason: 'The consignment against the referenced invoice was found short of the quantity invoiced. The value of the shortage computed at the original invoice rate, together with the GST applicable thereon, is credited to the buyer.'
    },
    price: {
        label: 'Price Correction / Discount', ico: '\u{1F4B0}', accent: '#059669',
        tag: 'Rate reduced after invoicing', tags: ['Value credit', 'Rate change', 'Discount'],
        title: 'CREDIT NOTE', noLbl: 'Credit Note No.', noHint: 'CN-001', reverse: false,
        toTitle: 'Credit To (Buyer)', otherTitle: 'Supplier', docType: 'C \u2014 Credit Note',
        basis: 'Reversal basis: only the reduction in value is credited, so the tax reversal is limited to the difference between the invoice value and the corrected value.',
        desc: 'The rate was reduced after the invoice was raised, usually on a negotiated discount. The credit is on the value difference, not on the quantity.',
        reason: 'The price of the goods supplied under the referenced invoice has been reduced by mutual agreement. The difference between the original invoice value and the corrected value is credited to the buyer, together with the GST applicable on that difference.'
    },
    buyer: {
        label: 'Debit Note by Buyer (Recipient)', ico: '\u{1F6D2}', accent: '#7C3AED',
        tag: 'Issued by the recipient', tags: ['Document type D', 'Short receipt', 'Defective goods'],
        title: 'DEBIT NOTE (BY RECIPIENT)', noLbl: 'Debit Note No.', noHint: 'DN-001', reverse: true,
        toTitle: 'Debit To (Supplier)', otherTitle: 'Recipient / Buyer', docType: 'D \u2014 Debit Note by Recipient',
        basis: 'Note: this document is raised by the RECIPIENT against the supplier. Report it in GSTR-1 as document type D \u2014 it reduces the recipient input tax credit.',
        desc: 'The buyer found a shortfall in quantity or defective goods on inward inspection and raises a debit note against the supplier. This is document type D, not C, in GSTR-1.',
        reason: 'The goods received against the referenced invoice were found short in quantity and / or defective on inward inspection. The recipient has therefore raised this debit note against the supplier for the value of the shortage or the defect, together with the GST applicable thereon.'
    }
};

let cnFmt = 'return';
let cnRowId = 0;

const CN_REFUND_LABEL = {
    neft: 'NEFT / RTGS to the bank account mentioned on the original invoice',
    adjust: 'Adjusted against the next invoice raised on the buyer',
    cash: 'Paid in cash \u2014 TDS as applicable under Section 194Q',
    next: 'Carried forward and set off against the next purchase order'
};

/* ---------- Reason picker ---------- */
function cnRenderFormats() {
    const grid = document.getElementById('fmtGrid');
    let h = '';
    Object.keys(CN_FORMATS).forEach(function (k) {
        const f = CN_FORMATS[k];
        h += '<button type="button" class="fmt-card' + (k === cnFmt ? ' active' : '') + '" data-cn-fmt="' + k + '" aria-pressed="' + (k === cnFmt) + '">' +
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
    grid.querySelectorAll('[data-cn-fmt]').forEach(function (b) {
        b.addEventListener('click', function () { cnSetFormat(b.dataset.cnFmt); });
    });
    cnSetFormat(cnFmt, true);
}

function cnFillReasonSelect() {
    const sel = document.getElementById('cnReason');
    let h = '';
    Object.keys(CN_FORMATS).forEach(function (k) { h += '<option value="' + k + '">' + CN_FORMATS[k].label + '</option>'; });
    sel.innerHTML = h;
    sel.value = cnFmt;
}

function cnSetFormat(k, quiet) {
    const f = CN_FORMATS[k];
    if (!f) return;
    cnFmt = k;

    document.querySelectorAll('[data-cn-fmt]').forEach(function (b) {
        const on = b.dataset.cnFmt === k;
        b.classList.toggle('active', on);
        b.setAttribute('aria-pressed', on);
    });

    document.getElementById('fmtInfoIco').textContent = f.ico;
    document.getElementById('fmtInfoTitle').textContent = f.label;
    document.getElementById('fmtInfoDesc').textContent = f.desc;

    document.getElementById('cnReason').value = k;
    document.getElementById('cnReasonText').value = f.reason;

    const cur = DOC.val('cnNo');
    if (!cur || /^(CN|DN)-\d{3}$/.test(cur)) document.getElementById('cnNo').value = f.noHint;

    document.getElementById('pCnTitle').textContent = f.title;
    document.getElementById('pCnNoLbl').textContent = f.noLbl + ':';
    document.getElementById('pCnToTitle').textContent = f.toTitle;
    document.getElementById('pCnOtherTitle').textContent = f.otherTitle;
    document.getElementById('pCnDocType').textContent = f.docType;
    document.getElementById('pCnHeroLbl').textContent = f.reverse ? 'Net Debit Amount' : 'Net Credit Amount';
    document.getElementById('pCnIssued').textContent = f.reverse ? 'Recipient / Buyer' : 'Supplier';

    if (!quiet) cnUpdate();
}

function cnOnReasonChange() {
    const k = DOC.val('cnReason');
    if (CN_FORMATS[k]) cnSetFormat(k);
}

/* ---------- Item rows ---------- */
function cnAddRow(seed) {
    seed = seed || {};
    const id = cnRowId++;
    document.getElementById('cnItems').insertAdjacentHTML('beforeend',
        '<div class="doc-item" data-row="' + id + '">' +
            '<input type="text" data-cn-name placeholder="Goods or service credited" oninput="cnUpdate()">' +
            '<input type="text" data-cn-hsn placeholder="HSN" maxlength="8" oninput="cnUpdate()">' +
            '<input type="number" data-cn-iqty value="' + (seed.iqty || 1) + '" min="0" step="0.01" oninput="cnUpdate()">' +
            '<input type="number" data-cn-rqty value="' + (seed.rqty || 1) + '" min="0" step="0.01" oninput="cnUpdate()">' +
            '<input type="number" data-cn-rate value="0" min="0" step="0.01" oninput="cnUpdate()">' +
            '<select data-cn-gst onchange="cnUpdate()">' + DOC.GST_RATES.map(function (r) {
                return '<option value="' + r + '"' + (r === '18' ? ' selected' : '') + '>' + r + '%</option>';
            }).join('') + '</select>' +
            '<span class="doc-item-amt" data-cn-amt>&#8377;0.00</span>' +
            '<button type="button" class="doc-item-del" onclick="cnDelRow(' + id + ')" title="Remove item" aria-label="Remove item">&times;</button>' +
        '</div>');
    cnUpdate();
}

function cnDelRow(id) {
    const e = document.querySelector('[data-row="' + id + '"]');
    if (e) e.remove();
    cnUpdate();
}

/* ---------- Totals engine ---------- */
function cnCollect() {
    const f = CN_FORMATS[cnFmt];
    const inter = DOC.isInterState('sState', 'bState');
    const rows = [];
    let invTaxable = 0, invTax = 0, credTaxable = 0, taxRev = 0;

    document.querySelectorAll('#cnItems .doc-item').forEach(function (el, i) {
        const g = function (s) { return el.querySelector(s); };
        const name = (g('[data-cn-name]').value || 'Item').trim();
        const hsn = (g('[data-cn-hsn]').value || '').trim();
        const iq = parseFloat(g('[data-cn-iqty]').value) || 0;
        const rq = parseFloat(g('[data-cn-rqty]').value) || 0;
        const rate = parseFloat(g('[data-cn-rate]').value) || 0;
        const gr = parseFloat(g('[data-cn-gst]').value) || 0;

        const invT = DOC.round2(iq * rate);
        const invTaxAmt = DOC.round2(invT * gr / 100);
        const cT = DOC.round2(rq * rate);
        const taxAmt = DOC.round2(cT * gr / 100);

        invTaxable = invTaxable + invT;
        invTax = invTax + invTaxAmt;
        credTaxable = credTaxable + cT;
        taxRev = taxRev + taxAmt;

        g('[data-cn-amt]').textContent = DOC.money(taxAmt);
        rows.push({
            i: i + 1, name: name, hsn: hsn, iq: iq, rq: rq, rate: rate, gr: gr,
            invT: invT, invTaxAmt: invTaxAmt, cred: cT, taxAmt: taxAmt,
            total: DOC.round2(cT + taxAmt)
        });
    });

    invTaxable = DOC.round2(invTaxable);
    invTax = DOC.round2(invTax);
    credTaxable = DOC.round2(credTaxable);
    taxRev = DOC.round2(taxRev);

    const invTotalAuto = DOC.round2(invTaxable + invTax);
    const origTotal = DOC.val('optOrigAuto') ? invTotalAuto : DOC.round2(DOC.num('origTotal'));
    const net = DOC.round2(credTaxable + taxRev);
    const cgst = DOC.round2(taxRev / 2);
    const sgst = DOC.round2(taxRev - cgst);

    return {
        f: f, inter: inter, rows: rows, invTaxable: invTaxable, invTax: invTax,
        credTaxable: credTaxable, taxRev: taxRev, origTotal: origTotal,
        net: net, cgst: cgst, sgst: sgst
    };
}

/* ---------- Preview render ---------- */
function cnUpdate() {
    const d = cnCollect();
    const rev = d.f.reverse;

    document.getElementById('origTotal').disabled = DOC.val('optOrigAuto');

    DOC.text('pCnOrgName', rev ? DOC.val('bName') : DOC.val('sName'));
    DOC.text('pCnOrgAddr', rev ? DOC.val('bAddr') : DOC.val('sAddr'));
    DOC.text('pCnOrgPhone', 'Phone: ' + (rev ? (DOC.val('bPhone') || '\u2014') : (DOC.val('sPhone') || '\u2014')));
    DOC.text('pCnOrgState', 'State: ' + (rev ? (DOC.val('bState') || '\u2014') : (DOC.val('sState') || '\u2014')));
    DOC.text('pCnOrgGst', rev ? DOC.val('bGst') : DOC.val('sGst'));
    DOC.text('pCnOrgCode', (!rev && DOC.val('sStateCode')) ? 'State Code: ' + DOC.val('sStateCode') : '');
    DOC.showIf('pCnOrgCode', !rev && !!DOC.val('sStateCode'));
    const trade = DOC.val('sTrade');
    DOC.showIf('pCnOrgTrade', !!trade);
    if (trade) document.getElementById('pCnOrgTrade').textContent = trade;

    DOC.text('pCnNo', DOC.val('cnNo'));
    DOC.text('pCnDate', DOC.fmtDate(DOC.val('cnDate')));
    document.getElementById('pCnIssued').textContent = rev ? 'Recipient / Buyer (the party in receipt of goods or services)' : 'Supplier';

    DOC.text('pCnInvNo', DOC.val('invNo'));
    DOC.text('pCnInvDate', DOC.fmtDate(DOC.val('invDate')));
    DOC.text('pCnInvValue', DOC.money(d.origTotal));
    DOC.text('pCnPos', DOC.val('posNote') || (DOC.val('bState') || '\u2014'));

    const oref = DOC.val('cnOtherRef');
    DOC.showIf('pCnOtherRefRow', !!oref);
    if (oref) document.getElementById('pCnOtherRef').textContent = oref;

    DOC.text('pCnToName', rev ? (DOC.val('sName') || 'Supplier') : (DOC.val('bName') || 'Buyer'));
    DOC.text('pCnToAddr', rev ? DOC.val('sAddr') : DOC.val('bAddr'));
    DOC.text('pCnToPhone', 'Phone: ' + (rev ? (DOC.val('sPhone') || '\u2014') : (DOC.val('bPhone') || '\u2014')));
    DOC.text('pCnToGst', rev ? DOC.val('sGst') : DOC.val('bGst'));
    DOC.showIf('pCnToGstRow', true);

    DOC.text('pCnOtherName', rev ? (DOC.val('bName') || 'Recipient / Buyer') : (DOC.val('sName') || 'Supplier'));
    DOC.text('pCnOtherGst', 'GSTIN: ' + (rev ? (DOC.val('bGst') || '\u2014') : (DOC.val('sGst') || '\u2014')));

    /* item table */
    const tb = document.getElementById('pCnRows');
    if (!d.rows.length) {
        tb.innerHTML = '<tr><td colspan="9" style="padding:14px;text-align:center;color:#94A3B8;">No items yet</td></tr>';
    } else {
        tb.innerHTML = d.rows.map(function (r) {
            return '<tr>' +
                '<td>' + r.i + '</td>' +
                '<td>' + r.name + '</td>' +
                '<td>' + (r.hsn || '\u2014') + '</td>' +
                '<td style="text-align:right;">' + DOC.qty(r.iq) + '</td>' +
                '<td style="text-align:right;">' + DOC.qty(r.rq) + '</td>' +
                '<td style="text-align:right;">' + DOC.money(r.rate) + '</td>' +
                '<td style="text-align:right;">' + r.gr + '%</td>' +
                '<td style="text-align:right;">' + DOC.money(r.taxAmt) + '</td>' +
                '<td style="text-align:right;font-weight:700;">' + DOC.money(r.total) + '</td>' +
            '</tr>';
        }).join('');
    }

    /* totals */
    DOC.text('pCnOrigVal', DOC.money(d.origTotal));
    DOC.text('pCnSub', DOC.money(d.credTaxable));
    if (d.inter) {
        DOC.showIf('pCnCgstRow', false); DOC.showIf('pCnSgstRow', false);
        DOC.showIf('pCnIgstRow', true);
        DOC.text('pCnIgst', '\u2212' + DOC.money(d.taxRev));
    } else {
        DOC.showIf('pCnIgstRow', false);
        DOC.showIf('pCnCgstRow', true); DOC.text('pCnCgst', '\u2212' + DOC.money(d.cgst));
        DOC.showIf('pCnSgstRow', true); DOC.text('pCnSgst', '\u2212' + DOC.money(d.sgst));
    }
    DOC.text('pCnTaxRev', '\u2212' + DOC.money(d.taxRev));
    DOC.text('pCnHeroVal', DOC.money(d.net));
    document.getElementById('pCnStripTotal').textContent = DOC.money(d.net);

    const words = DOC.val('optWords');
    DOC.showIf('pCnWordsRow', words);
    if (words) document.getElementById('pCnWords').textContent = DOC.inWords(d.net);

    const rt = DOC.val('cnReasonText');
    DOC.showIf('pCnReasonBox', !!rt);
    if (rt) document.getElementById('pCnReasonText').textContent = rt;

    const refNo = DOC.val('invNo') || '________';
    const refDate = DOC.fmtDate(DOC.val('invDate')) || '________';
    const verb = rev ? 'debit' : 'credit';
    const decl = 'We ' + verb + ' the amount mentioned above against our invoice no. ' + refNo + ' dated ' + refDate +
        '. The amount stated includes GST.';
    document.getElementById('pCnDecl').textContent = decl;

    const notes = DOC.val('cnNotes');
    DOC.showIf('pCnNotesRow', !!notes);
    if (notes) document.getElementById('pCnNotes').textContent = notes;

    const mode = DOC.val('cnRefund');
    DOC.showIf('pCnRefundRow', mode !== 'no');
    if (mode !== 'no') {
        const bits = [CN_REFUND_LABEL[mode] || ''];
        if (DOC.val('cnCreditDate')) bits.push('Expected by ' + DOC.fmtDate(DOC.val('cnCreditDate')));
        document.getElementById('pCnRefund').textContent = bits.filter(Boolean).join(' \u00B7 ');
    }

    const sigOn = DOC.val('optSignature');
    DOC.showIf('pCnSigRow', sigOn);
    if (sigOn) {
        document.getElementById('pCnSigName').textContent = rev ? (DOC.val('bName') || 'the recipient') : (DOC.val('sName') || 'the supplier');
    }
    const ackOn = DOC.val('optAck');
    DOC.showIf('pCnAckRow', ackOn);
    if (ackOn) {
        document.getElementById('pCnAckName2').textContent = rev ? (DOC.val('sName') || 'Supplier') : (DOC.val('bName') || 'Buyer');
    }
    DOC.showIf('pCnDeclRow', DOC.val('optTerms'));
}

/* ---------- Actions ---------- */
function cnPreviewJump() { cnUpdate(); DOC.scrollToPreview('cnPreview'); }
function cnPrint() { cnUpdate(); window.print(); }

function cnReset() {
    ['cnNo', 'cnOtherRef', 'cnReasonText', 'cnNotes', 'posNote', 'invNo', 'sName', 'sGst', 'sPhone',
        'sAddr', 'sStateCode', 'sTrade', 'bName', 'bGst', 'bPhone', 'bAddr'].forEach(function (id) {
        const e = document.getElementById(id); if (e) e.value = '';
    });
    document.getElementById('origTotal').value = 0;
    document.getElementById('cnRefund').value = 'no';
    document.getElementById('sState').value = '';
    document.getElementById('bState').value = '';
    document.getElementById('cnDate').value = DOC.todayISO();
    document.getElementById('invDate').value = DOC.addDaysISO(-7);
    document.getElementById('cnCreditDate').value = '';
    document.getElementById('optOrigAuto').checked = true;
    document.getElementById('optWords').checked = true;
    document.getElementById('optSignature').checked = true;
    document.getElementById('optAck').checked = true;
    document.getElementById('optTerms').checked = true;
    document.getElementById('cnItems').querySelectorAll('.doc-item').forEach(function (e) { e.remove(); });
    cnAddRow();
    cnSetFormat('return');
    cnUpdate();
}

/* ---------- PDF ---------- */
function cnPDF() {
    cnUpdate();
    const d = cnCollect();
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
            { label: d.f.noLbl, value: DOC.val('cnNo') || '\u2014' },
            { label: 'Date', value: DOC.fmtDate(DOC.val('cnDate')) || '\u2014' },
            { label: 'Issued by', value: rev ? 'Recipient / Buyer' : 'Supplier' }
        ],
        accent: accent
    });

    /* reference invoice band */
    y = DOC.pdfSection(doc, MG, y, 'AGAINST ORIGINAL INVOICE', W - MG * 2);
    doc.setFontSize(8.5);
    doc.setTextColor(30, 41, 59);
    doc.text('Invoice No.: ' + (DOC.val('invNo') || '\u2014') + '     Invoice Date: ' + (DOC.fmtDate(DOC.val('invDate')) || '\u2014'), MG, y); y += 4.4;
    doc.setTextColor(51, 65, 85);
    doc.text('Original Invoice Value: ' + DOC.money(d.origTotal) + '     Place of Supply: ' + (DOC.val('posNote') || DOC.val('bState') || '\u2014'), MG, y); y += 4.4;
    if (DOC.val('cnOtherRef')) { doc.text('Other Reference: ' + DOC.val('cnOtherRef'), MG, y); y += 4.4; }
    doc.setTextColor(120, 113, 108);
    doc.text('GSTR-1 document type: ' + d.f.docType, MG, y); y += 7;

    /* party blocks */
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

    /* items table */
    const head = ['#', 'Goods / Service Credited', 'HSN', 'Inv Qty', 'Credit Qty', 'Rate', 'GST%', 'Tax Reversed', 'Credit Amount'];
    const body = d.rows.length ? d.rows.map(function (r) {
        return [r.i, r.name, r.hsn || '\u2014', DOC.qty(r.iq), DOC.qty(r.rq),
            DOC.money(r.rate), r.gr + '%', DOC.money(r.taxAmt), DOC.money(r.total)];
    }) : [['', 'No items', '', '', '', '', '', '', '']];

    y = DOC.pdfTable(doc, head, body, {
        startY: y, accent: accent,
        extra: {
            columnStyles: {
                0: { cellWidth: 8 },
                3: { halign: 'right' }, 4: { halign: 'right' }, 5: { halign: 'right' },
                6: { halign: 'right' }, 7: { halign: 'right' }, 8: { halign: 'right', fontStyle: 'bold' }
            }
        }
    }) + 6;

    /* totals */
    const rows = [
        { label: 'Original Invoice Value', value: DOC.money(d.origTotal) },
        { label: 'Taxable Value Credited', value: DOC.money(d.credTaxable) }
    ];
    if (d.inter) {
        rows.push({ label: 'IGST reversed', value: '\u2212' + DOC.money(d.taxRev) });
    } else {
        rows.push({ label: 'CGST reversed', value: '\u2212' + DOC.money(d.cgst) });
        rows.push({ label: 'SGST reversed', value: '\u2212' + DOC.money(d.sgst) });
    }
    rows.push({ rule: true, gap: 2 });
    rows.push({
        label: rev ? 'Net Debit Amount' : 'Net Credit Amount', value: DOC.money(d.net),
        bold: true, size: 10.5, color: accent, gap: 4
    });
    rows.push({ label: 'Total Tax Reversed', value: '\u2212' + DOC.money(d.taxRev), size: 8, color: [185, 28, 28], gap: 7 });

    y = DOC.pdfTotals(doc, W, y, rows);

    if (DOC.val('optWords')) {
        y += 1;
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Amount in words: ' + DOC.inWords(d.net), MG, y); y += 6;
        doc.setFont('helvetica', 'normal');
    }

    if (DOC.val('cnReasonText')) {
        if (y > H - 40) { doc.addPage(); y = 18; }
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Reason for issue:', MG, y); y += 4;
        doc.setFont('helvetica', 'normal');
        const rl = doc.splitTextToSize(DOC.val('cnReasonText'), W - MG * 2);
        doc.text(rl, MG, y); y += rl.length * 3.6 + 3;
    }

    if (DOC.val('optTerms')) {
        if (y > H - 40) { doc.addPage(); y = 18; }
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Declaration:', MG, y); y += 4;
        doc.setFont('helvetica', 'normal');
        const dl = doc.splitTextToSize('We ' + (rev ? 'debit' : 'credit') + ' the amount mentioned above against invoice no. ' +
            (DOC.val('invNo') || '________') + ' dated ' + (DOC.fmtDate(DOC.val('invDate')) || '________') +
            '. The amount stated includes GST. This credit note is issued in our own number series and is reportable in GSTR-1.',
            W - MG * 2);
        doc.text(dl, MG, y); y += dl.length * 3.6 + 3;
    }

    if (DOC.val('cnNotes')) {
        if (y > H - 40) { doc.addPage(); y = 18; }
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Notes:', MG, y); y += 4;
        doc.setFont('helvetica', 'normal');
        const nl = doc.splitTextToSize(DOC.val('cnNotes'), W - MG * 2);
        doc.text(nl, MG, y); y += nl.length * 3.6 + 3;
    }

    if (DOC.val('optAck') || DOC.val('optSignature')) {
        if (y > H - 34) { doc.addPage(); y = 20; }
        const boxes = [];
        if (DOC.val('optSignature')) boxes.push({ line: 'For ' + (rev ? (DOC.val('bName') || 'the recipient') : (DOC.val('sName') || 'the supplier')), sub: 'Authorised Signatory' });
        if (DOC.val('optAck')) boxes.push({ line: 'Received and agreed \u2014 ' + (rev ? (DOC.val('sName') || 'Supplier') : (DOC.val('bName') || 'Buyer')), sub: 'Counter-signed by the other party' });
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

    DOC.pdfFooter(doc, y, (DOC.val('cnNo') || 'credit note') + '  \u00B7  against invoice ' + (DOC.val('invNo') || '\u2014'));
    doc.save(((DOC.val('cnNo') || 'credit-note').replace(/[^\w\-]+/g, '_')) + '.pdf');
}

/* ---------- Boot ---------- */
(function () {
    DOC.fillStates('sState', 'Select state');
    DOC.fillStates('bState', 'Select state');
    document.getElementById('cnDate').value = DOC.todayISO();
    document.getElementById('invDate').value = DOC.addDaysISO(-7);
    cnFillReasonSelect();
    cnRenderFormats();
    cnAddRow();

    const ids = ['cnNo', 'cnDate', 'cnOtherRef', 'cnReasonText', 'cnNotes', 'cnRefund', 'cnCreditDate',
        'invNo', 'invDate', 'origTotal', 'optOrigAuto', 'posNote',
        'sName', 'sGst', 'sPhone', 'sAddr', 'sState', 'sStateCode', 'sTrade',
        'bName', 'bGst', 'bPhone', 'bAddr', 'bState',
        'optWords', 'optSignature', 'optAck', 'optTerms'];
    DOC.bind(ids, cnUpdate);
    document.getElementById('cnReason').addEventListener('change', cnOnReasonChange);

    DOC.bindSwatches('data-cn-accent', cnUpdate);

    cnUpdate();
})();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
