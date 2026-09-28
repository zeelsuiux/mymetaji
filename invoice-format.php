<?php
/**
 * My Metaji - Invoice Format
 * Lets the user pick a GST invoice format (B2B / B2C / Export / RCM / Proforma / E-Invoice)
 * fill it in, preview it live and download it as a PDF.
 */
require_once __DIR__ . '/config.php';
$page_title = 'Free Invoice Format — GST Invoice Templates Online | ' . BRAND_NAME;
$page_desc  = 'Create GST-compliant invoices in the correct format. Choose B2B tax invoice, B2C retail bill, export invoice, RCM, proforma or e-invoice and download a free PDF.';
include __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="gst-hero">
    <div class="gst-wrap">
        <div class="gst-hero-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"/></svg>
            100% GST compliant &middot; No sign-up required
        </div>
        <h1>Free <span>Invoice Format</span></h1>
        <p>Pick the invoice format the law actually asks for &mdash; B2B tax invoice, B2C retail bill, export, reverse charge or e-invoice &mdash; fill the details and download a print-ready PDF.</p>
        <div class="gst-hero-chips">
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
                6 ready formats
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                Instant PDF download
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 3v18M7 7h7a3 3 0 0 1 0 6H7"/></svg>
                Auto CGST / SGST / IGST
            </span>
        </div>
    </div>
</section>

<!-- ===================== TOOL ===================== -->
<section class="gst-tool" id="invoice-format-tool">
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
                            <h3>Choose Your Invoice Format</h3>
                            <p>The format decides which fields are mandatory and how tax is split.</p>
                        </div>
                        <span class="qm-step">Step 1</span>
                    </div>

                    <div class="fmt-grid" id="fmtGrid" role="group" aria-label="Invoice format"></div>

                    <div class="note-box" style="margin-top:16px;">
                        <div class="note-ico" id="fmtInfoIco">&#128196;</div>
                        <div>
                            <h4 id="fmtInfoTitle">B2B Tax Invoice</h4>
                            <p id="fmtInfoDesc">Issued to a registered business. GSTIN of the buyer, HSN/SAC code, unit of measure and place of supply are compulsory.</p>
                            <p style="margin:0;"><strong>Mandatory fields:</strong> <span id="fmtInfoFields">Seller GSTIN, buyer GSTIN, invoice number, invoice date, HSN/SAC, place of supply</span></p>
                        </div>
                    </div>

                    <div class="doc-split" style="margin-top:18px;">
                        <div>
                            <div class="doc-opt">
                                <span>Accent colour</span>
                                <div class="doc-swatches">
                                    <button type="button" class="doc-swatch active" data-inv-accent="blue" style="background:#2563EB" aria-label="Blue"></button>
                                    <button type="button" class="doc-swatch" data-inv-accent="indigo" style="background:#4F46E5" aria-label="Indigo"></button>
                                    <button type="button" class="doc-swatch" data-inv-accent="green" style="background:#059669" aria-label="Green"></button>
                                    <button type="button" class="doc-swatch" data-inv-accent="amber" style="background:#D97706" aria-label="Amber"></button>
                                    <button type="button" class="doc-swatch" data-inv-accent="red" style="background:#DC2626" aria-label="Red"></button>
                                    <button type="button" class="doc-swatch" data-inv-accent="slate" style="background:#334155" aria-label="Slate"></button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="doc-switches">
                                <label class="doc-chk"><input type="checkbox" id="optSignature" checked> Signature block</label>
                                <label class="doc-chk"><input type="checkbox" id="optBank" checked> Bank details</label>
                                <label class="doc-chk"><input type="checkbox" id="optWords" checked> Amount in words</label>
                                <label class="doc-chk"><input type="checkbox" id="optReturn"> Return / refund policy</label>
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
                            <h3>Seller Details</h3>
                            <p>Who is issuing this invoice &mdash; your registered business details.</p>
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
                        <div class="inv-gen-field"><label>Trade Name / Logo Text</label><input type="text" id="sTrade" placeholder="Optional"></div>
                    </div>
                </div>

                <!-- 3. BUYER -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
                        </div>
                        <div>
                            <h3>Buyer Details</h3>
                            <p>Bill-to party. GSTIN is mandatory only for B2B supply.</p>
                        </div>
                        <span class="qm-step">Step 3</span>
                    </div>
                    <div class="doc-split">
                        <div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Buyer Name</label><input type="text" id="bName" placeholder="Customer or company name"></div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label id="bGstLbl">Buyer GSTIN</label><input type="text" id="bGst" placeholder="27BBBBB0000B1Z5" maxlength="15"></div>
                        </div>
                        <div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Phone</label><input type="tel" id="bPhone" placeholder="98765 43210"></div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>State</label><select id="bState"></select></div>
                        </div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Billing Address</label><input type="text" id="bAddr" placeholder="Street, city, state, PIN"></div>
                    <div class="doc-split" id="posRow">
                        <div class="inv-gen-field"><label>Place of Supply</label><input type="text" id="bPos" placeholder="e.g. Gujarat (24)"></div>
                        <div class="inv-gen-field"><label>Reverse Charge (RCM)</label>
                            <select id="bRcm"><option value="no">No &mdash; I am paying the tax</option><option value="yes">Yes &mdash; recipient pays the tax</option></select>
                        </div>
                    </div>
                    <div class="doc-split" id="expRow" style="display:none;">
                        <div class="inv-gen-field"><label>Country</label><input type="text" id="bCountry" placeholder="e.g. United Arab Emirates"></div>
                        <div class="inv-gen-field"><label>Port Code / Shipping Bill</label><input type="text" id="bPort" placeholder="INMUN1 / 1234567"></div>
                    </div>
                    <div class="doc-split" id="advRow" style="display:none;">
                        <div class="inv-gen-field"><label>PO / Reference No.</label><input type="text" id="bPo" placeholder="PO-2026-004"></div>
                        <div class="inv-gen-field"><label>Advance Received (&#8377;)</label><input type="number" id="bAdvance" value="0" min="0" step="0.01" oninput="invUpdate()"></div>
                    </div>
                </div>

                <!-- 4. INVOICE META + ITEMS -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M8 9h8M8 13h8M8 17h4"/></svg>
                        </div>
                        <div>
                            <h3>Invoice Details &amp; Items</h3>
                            <p>Serial numbering is mandatory &mdash; invoices must run in a continuous series.</p>
                        </div>
                        <span class="qm-step">Step 4</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label id="invNoLbl">Invoice Number</label><input type="text" id="invNo" placeholder="INV-001"></div>
                        <div class="inv-gen-field"><label>Invoice Date</label><input type="date" id="invDate"></div>
                    </div>
                    <div class="doc-split" id="irnRow" style="display:none; margin-bottom:14px;">
                        <div class="inv-gen-field"><label>IRN (32 chars)</label><input type="text" id="invIrn" placeholder="a1b2c3..."></div>
                        <div class="inv-gen-field"><label>Ack. Number / UIN</label><input type="text" id="invAck" placeholder="112230000123456"></div>
                    </div>

                    <div class="doc-items" id="invItems" style="--doc-cols: 2.3fr .95fr .6fr .75fr 1fr .8fr 1fr auto">
                        <div class="doc-item-hd">
                            <span>Item / Service</span><span id="hsnHd">HSN / SAC</span><span>Qty</span><span id="unitHd">Unit</span><span>Rate (&#8377;)</span><span>GST %</span><span style="text-align:right">Amount</span><span></span>
                        </div>
                    </div>
                    <button type="button" class="inv-gen-add-item" onclick="invAddRow()">+ Add Item / Service</button>

                    <div class="doc-split" style="margin-top:16px;">
                        <div class="inv-gen-field"><label>Discount (&#8377;)</label><input type="number" id="disc" value="0" min="0" step="0.01" oninput="invUpdate()"></div>
                        <div class="inv-gen-field"><label>Round Off (&#8377;)</label><input type="number" id="round" value="0" step="0.01" oninput="invUpdate()"></div>
                    </div>
                </div>

                <!-- 5. NOTES + BANK -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>
                        </div>
                        <div>
                            <h3>Notes, Terms &amp; Bank Details</h3>
                            <p>Payment terms, bank details and any declaration the buyer needs.</p>
                        </div>
                        <span class="qm-step">Step 5</span>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Notes / Terms</label><textarea id="invNotes" rows="3" placeholder="Payment due within 15 days. Goods once sold will not be taken back."></textarea></div>
                    <div class="doc-split" id="bankRow">
                        <div class="inv-gen-field"><label>Bank Name</label><input type="text" id="bBank" placeholder="HDFC Bank"></div>
                        <div class="inv-gen-field"><label>Account Name &amp; Number</label><input type="text" id="bAcct" placeholder="ABC Traders &mdash; 50100299887766"></div>
                    </div>
                    <div class="doc-split" style="margin-top:10px;">
                        <div class="inv-gen-field"><label>IFSC Code</label><input type="text" id="bIfsc" placeholder="HDFC0001234"></div>
                        <div class="inv-gen-field"><label>UPI ID</label><input type="text" id="bUpi" placeholder="name@bank"></div>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="qm-actions doc-no-print">
                    <button type="button" class="qm-btn qm-btn-primary" onclick="invPreviewJump()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        Preview
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="invPrint()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="1"/><path d="M6 17h12v4H6z"/></svg>
                        Print
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="invReset()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                        Reset
                    </button>
                    <button type="button" class="qm-btn qm-btn-primary" style="flex:2" onclick="invPDF()">
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
                <div class="inv-gen-preview" id="invPreview">
                    <div class="doc-sheet doc-sheet-lg" id="invSheet">
                        <div class="doc-hd">
                            <div class="doc-org">
                                <h2 id="pSName">Your Business</h2>
                                <p id="pSTrade" style="display:none"></p>
                                <p id="pSAddr">Address</p>
                                <p><span id="pSPhone">Phone: &mdash;</span> &middot; <span id="pSState">State: &mdash;</span></p>
                                <p><strong>GSTIN:</strong> <span id="pSGst">&mdash;</span> &middot; <span id="pSCode"></span></p>
                            </div>
                            <div class="doc-id">
                                <div class="doc-title" id="pTitle">TAX INVOICE</div>
                                <p><strong id="pNoLbl">Invoice No.:</strong> <span id="pInvNo">&mdash;</span></p>
                                <p><strong>Date:</strong> <span id="pInvDate">&mdash;</span></p>
                                <p id="pRefRow" style="display:none"><strong>Ref:</strong> <span id="pRef"></span></p>
                                <p id="pIrnRow" style="display:none"><strong>IRN:</strong> <span id="pIrn"></span></p>
                                <p id="pAckRow" style="display:none"><strong>Ack:</strong> <span id="pAck"></span></p>
                            </div>
                        </div>

                        <div class="doc-split" style="margin-bottom:14px;">
                            <div>
                                <p class="doc-sec-title">Bill To</p>
                                <p class="inv-p-party-name" id="pBName" style="margin:3px 0;">Customer</p>
                                <p id="pBAddr" style="font-size:10px;color:#475569;margin:1px 0;">Address</p>
                                <p id="pBPhone" style="font-size:10px;color:#475569;margin:1px 0;">Phone: &mdash;</p>
                                <p id="pBGstRow" style="font-size:10px;color:#475569;margin:2px 0;"><strong>GSTIN:</strong> <span id="pBGst">&mdash;</span></p>
                            </div>
                            <div id="pRightBox">
                                <p class="doc-sec-title" id="pRightTitle">Supply Details</p>
                                <p style="font-size:10px;color:#475569;margin:2px 0;" id="pPosRow"><strong>Place of Supply:</strong> <span id="pPos">&mdash;</span></p>
                                <p style="font-size:10px;color:#475569;margin:2px 0;display:none;" id="pExpRow"><strong>Country:</strong> <span id="pCountry">&mdash;</span></p>
                                <p style="font-size:10px;color:#475569;margin:2px 0;display:none;" id="pRcmRow"><strong>Reverse Charge:</strong> Yes</p>
                            </div>
                        </div>

                        <table class="inv-p-table" style="width:100%;border-collapse:collapse;margin-bottom:12px;font-size:10px;">
                            <thead><tr>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">#</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">Item / Service</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Qty</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Rate</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">GST %</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Tax</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Total</th>
                            </tr></thead>
                            <tbody id="pRows"><tr><td colspan="7" style="padding:14px;text-align:center;color:#94A3B8;">No items yet</td></tr></tbody>
                        </table>

                        <div style="display:flex;justify-content:flex-end;margin-bottom:12px;">
                            <table class="inv-p-totals-table" style="font-size:10px;min-width:220px;">
                                <tr><td style="padding:3px 8px;">Taxable Value</td><td id="pSub" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pDiscRow" style="display:none"><td style="padding:3px 8px;">Discount</td><td id="pDisc" style="text-align:right;font-weight:600;color:#059669;">&minus;&#8377;0.00</td></tr>
                                <tr id="pCgstRow" style="display:none"><td style="padding:3px 8px;">CGST</td><td id="pCgst" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pSgstRow" style="display:none"><td style="padding:3px 8px;">SGST</td><td id="pSgst" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pIgstRow" style="display:none"><td style="padding:3px 8px;">IGST</td><td id="pIgst" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pRcmRow2" style="display:none"><td style="padding:3px 8px;">Tax on RCM (payable by buyer)</td><td id="pRcm" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pAdvRow2" style="display:none"><td style="padding:3px 8px;">Advance Adjusted</td><td id="pAdv" style="text-align:right;font-weight:600;color:#059669;">&minus;&#8377;0.00</td></tr>
                                <tr id="pRoundRow" style="display:none"><td style="padding:3px 8px;">Round Off</td><td id="pRound" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr style="border-top:2px solid #1E293B;"><td style="padding:6px 8px;font-weight:800;font-size:12px;color:var(--fmt-accent,#2563EB);" id="pGrandLbl">Grand Total</td><td style="padding:6px 8px;text-align:right;font-weight:800;font-size:12px;color:var(--fmt-accent,#2563EB);" id="pGrand">&#8377;0.00</td></tr>
                            </table>
                        </div>

                        <div class="inv-p-words" id="pWordsRow" style="font-size:9px;color:#475569;padding:8px 0;border-top:1px solid #E2E8F0;margin-bottom:8px;">
                            <strong>Amount in words:</strong> <span id="pWords">Zero Only</span>
                        </div>
                        <div id="pRcmNote" class="note-box red" style="display:none;padding:9px 12px;margin-bottom:8px;">
                            <div class="note-ico" style="font-size:15px;">&#9888;</div>
                            <p style="margin:0;font-size:10px;">Reverse charge applies. Tax is payable by the recipient.</p>
                        </div>
                        <div class="inv-p-notes" id="pNotesRow" style="display:none;font-size:9px;color:#475569;padding:6px 0;">
                            <strong>Notes:</strong> <span id="pNotes"></span>
                        </div>
                        <div id="pBankRow" style="font-size:9px;color:#475569;padding:8px 0;border-top:1px dashed #E2E8F0;margin-bottom:6px;">
                            <strong>Bank details:</strong> <span id="pBankTxt">Add your bank details above</span>
                        </div>
                        <div id="pRetRow" style="display:none;font-size:9px;color:#475569;padding:6px 0;">
                            <strong>Returns:</strong> Goods once sold will not be taken back. Interest @18% p.a. is charged on overdue payments.
                        </div>
                        <div class="doc-sig" id="pSigRow">
                            <div></div>
                            <div></div>
                            <div><div class="doc-sig-line">For <span id="pSigName">&mdash;</span></div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Authorised Signatory</div></div>
                        </div>
                        <div class="doc-ft">This is a computer-generated document &middot; Generated by <strong>My Metaji</strong></div>
                    </div>
                </div>
                <div class="qm-total-strip">
                    <span>Invoice Total</span>
                    <strong id="pStripTotal">&#8377;0.00</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== FORMAT COMPARISON ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>Which Invoice Format Do You Actually Need?</h2>
        <p>Rule 46 and Rule 48 of the CGST Rules decide the layout. Picking the wrong one is the most common reason a buyer rejects an invoice, so match your supply type first.</p>
        <div class="fmt-table-wrap" style="margin-top:20px;">
            <table class="fmt-table">
                <thead>
                    <tr><th>Format</th><th>Who it is for</th><th>Tax split</th><th>Compulsory fields</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>B2B Tax Invoice</strong></td>
                        <td>Registered business buying for resale or input credit</td>
                        <td>CGST + SGST (intra-state) or IGST (inter-state)</td>
                        <td><span class="fmt-req yes">Buyer GSTIN</span> <span class="fmt-req yes">HSN</span> <span class="fmt-req yes">Place of supply</span></td>
                    </tr>
                    <tr>
                        <td><strong>B2C Retail Bill</strong></td>
                        <td>Walk-in consumer, unregistered buyer</td>
                        <td>CGST + SGST or IGST &mdash; shown separately on the bill</td>
                        <td><span class="fmt-req yes">Seller GSTIN</span> <span class="fmt-req cond">Buyer GSTIN if &gt; &#8377;2.5L in a day</span></td>
                    </tr>
                    <tr>
                        <td><strong>Export Invoice</strong></td>
                        <td>Goods shipped outside India</td>
                        <td>Zero-rated &mdash; show as <em>Nil</em> under LUT</td>
                        <td><span class="fmt-req yes">Country</span> <span class="fmt-req yes">Port code</span> <span class="fmt-req cond">Shipping bill no.</span></td>
                    </tr>
                    <tr>
                        <td><strong>Reverse Charge Invoice</strong></td>
                        <td>Goods notified under Section 9(3) / 9(4), or import of services</td>
                        <td>Amount is tax-inclusive; tax shown separately as <em>payable by recipient</em></td>
                        <td><span class="fmt-req yes">RCM declaration</span> <span class="fmt-req yes">GSTIN of both parties</span></td>
                    </tr>
                    <tr>
                        <td><strong>Proforma / Advance Invoice</strong></td>
                        <td>Advance received before delivery</td>
                        <td>Same as final invoice; adjusted against the final bill</td>
                        <td><span class="fmt-req yes">PO reference</span> <span class="fmt-req yes">Advance amount</span></td>
                    </tr>
                    <tr>
                        <td><strong>E-Invoice (IRN)</strong></td>
                        <td>Turnover above the notified threshold</td>
                        <td>Same as B2B / B2C; IRN and QR must be printed</td>
                        <td><span class="fmt-req yes">IRN (32 char)</span> <span class="fmt-req yes">Ack. no.</span> <span class="fmt-req yes">QR code</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="note-box gold" style="margin-top:20px;">
            <div class="note-ico">&#128203;</div>
            <div>
                <h4>Common mistakes to avoid</h4>
                <ul>
                    <li>Consecutive serial numbering &mdash; a gap in the series invites scrutiny during assessment.</li>
                    <li>Missing buyer GSTIN on a B2B bill makes the buyer lose input tax credit.</li>
                    <li>HSN is mandatory for goods above &#8377;5,000 per item; 4 digits minimum.</li>
                    <li>Rate of tax and amount of tax are compulsory columns &mdash; never merge them into a single "GST" line.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===================== HOW TO ===================== -->
<section class="gst-faq" style="background:#fff;">
    <div class="gst-wrap">
        <h2>How to Create an Invoice in the Right Format</h2>
        <div class="gst-how-grid">
            <div class="gst-how-card">
                <div class="gst-how-num">1</div>
                <h3>Pick the Format</h3>
                <p>Choose B2B, B2C, export, RCM, proforma or e-invoice based on the supply. The form reshapes itself instantly.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">2</div>
                <h3>Fill Both Parties</h3>
                <p>Enter your GSTIN and state, then the buyer's name, GSTIN and state. Tax splits into CGST+SGST or IGST automatically.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">3</div>
                <h3>Add Line Items</h3>
                <p>Add each item with HSN/SAC, quantity, unit, rate and GST rate. Taxable value, tax and grand total compute live.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">4</div>
                <h3>Download or Print</h3>
                <p>Check the live preview, then download a print-ready PDF or print straight from the browser for your records.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SEO + RELATED ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>What is an Invoice Format?</h2>
        <p>An <strong>invoice format</strong> is the fixed layout a tax invoice must follow so that it is valid under the Central Goods and Services Tax (CGST) Act. It is not a design choice &mdash; the law specifies which particulars must appear, in what order, and how tax must be shown. A compliant format always carries the supplier's legal name and GSTIN, a unique and continuous serial number, the date of supply, the recipient's name and address, a description of goods or services with HSN/SAC codes, quantity, rate, taxable value, the rate and amount of tax split into CGST/SGST or IGST, the total invoice value in figures <em>and</em> in words, and the authorised signatory.</p>
        <p>Choosing the wrong format is the most common reason a buyer's finance team sends an invoice back. A retail bill issued to another GST-registered dealer lacks the buyer's GSTIN, which means the buyer cannot claim input tax credit. An export invoice that shows GST as charged instead of nil under LUT will be rejected by the port authority. A reverse-charge supply that shows tax as already paid will result in double taxation. This tool lets you pick the correct format first, then fills in only the fields that format actually needs.</p>

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
        <h2>Stop Typing Invoices by Hand</h2>
        <p><?= BRAND_NAME ?> generates GST invoices in the correct format automatically, remembers your rates, and keeps your stock and GST returns in sync.</p>
        <a href="#download" class="gst-cta-btn">Download <?= BRAND_NAME ?> Free &rarr;</a>
    </div>
</section>

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="assets/js/doc-utils.js"></script>
<script>
/* ---------- Format definitions ---------- */
const INV_FORMATS = {
    b2b: {
        label: 'B2B Tax Invoice', tag: 'GSTIN required', tags: ['B2B', 'HSN', 'Input credit'],
        ico: '\u{1F4E6}',
        desc: 'Issued to a registered business. GSTIN of the buyer, HSN/SAC code, unit of measure and place of supply are compulsory.',
        fields: 'Seller GSTIN, buyer GSTIN, invoice number, invoice date, HSN/SAC, place of supply',
        title: 'TAX INVOICE', noLbl: 'Invoice No.', buyerGst: true, hsn: 'HSN', unit: true,
        pos: true, rcm: true, export: false, advance: false, irn: false, rightTitle: 'Supply Details'
    },
    b2c: {
        label: 'B2C Retail Bill', tag: 'Walk-in customers', tags: ['B2C', 'Simplified', 'Retail'],
        ico: '\u{1F6D2}',
        desc: 'Simplified bill for a walk-in consumer. Buyer GSTIN is not needed unless a single customer crosses \u20B92.5 lakh in a day.',
        fields: 'Seller GSTIN, invoice number, invoice date, HSN, tax breakup in words',
        title: 'RETAIL INVOICE', noLbl: 'Bill No.', buyerGst: false, hsn: 'HSN', unit: false,
        pos: false, rcm: false, export: false, advance: false, irn: false, rightTitle: 'Bill Details'
    },
    export: {
        label: 'Export Invoice', tag: 'Zero rated', tags: ['Export', 'LUT', 'IGST 0%'],
        ico: '\u{1F69A}',
        desc: 'For goods shipped outside India. Tax is zero-rated, so show it as Nil when you are claiming under the Letter of Undertaking.',
        fields: 'Country, port code, shipping bill number, buyer address abroad, LUT declaration',
        title: 'EXPORT INVOICE', noLbl: 'Invoice No.', buyerGst: false, hsn: 'HSN', unit: true,
        pos: false, rcm: false, export: true, advance: false, irn: false, rightTitle: 'Export Details'
    },
    rcm: {
        label: 'Reverse Charge Invoice', tag: 'Section 9(3) / 9(4)', tags: ['RCM', 'Tax payable by buyer', 'Unregistered'],
        ico: '\u{1F504}',
        desc: 'For supplies where the recipient pays the GST. The bill must clearly state that tax is payable on reverse charge by the recipient.',
        fields: 'RCM declaration, both GSTINs, tax-inclusive amount, tax shown separately',
        title: 'TAX INVOICE (RCM)', noLbl: 'Invoice No.', buyerGst: true, hsn: 'HSN', unit: false,
        pos: true, rcm: true, export: false, advance: false, irn: false, rightTitle: 'RCM Details'
    },
    proforma: {
        label: 'Proforma / Advance', tag: 'Advance received', tags: ['Proforma', 'PO linked', 'Advance adjust'],
        ico: '\u{1F4CB}',
        desc: 'Sent after the buyer confirms the deal but before delivery. Shows the advance already received and the balance still payable.',
        fields: 'PO reference, advance amount, balance due, validity of the offer',
        title: 'PROFORMA INVOICE', noLbl: 'Proforma No.', buyerGst: true, hsn: 'HSN', unit: true,
        pos: true, rcm: false, export: false, advance: true, irn: false, rightTitle: 'Order Details'
    },
    einvoice: {
        label: 'E-Invoice (IRN)', tag: 'IRN + QR required', tags: ['E-Invoice', 'IRN', 'QR code'],
        ico: '\u{1F4F7}',
        desc: 'For taxpayers above the e-invoicing turnover threshold. The IRN, acknowledgement number and the signed QR must be printed on the invoice.',
        fields: 'IRN (32 characters), acknowledgement number, UIN, signed QR code',
        title: 'TAX INVOICE', noLbl: 'Invoice No.', buyerGst: true, hsn: 'HSN', unit: true,
        pos: true, rcm: true, export: false, advance: false, irn: true, rightTitle: 'E-Invoice Details'
    }
};

let invFmt = 'b2b';
let invRowId = 0;

/* ---------- Format picker ---------- */
function invRenderFormats() {
    const grid = document.getElementById('fmtGrid');
    let h = '';
    Object.keys(INV_FORMATS).forEach(k => {
        const f = INV_FORMATS[k];
        h += `<button type="button" class="fmt-card${k === invFmt ? ' active' : ''}" data-inv-fmt="${k}" aria-pressed="${k === invFmt}">
            <span class="fmt-mini" style="--fmt-accent:${f.export ? '#0D9488' : f.rcm ? '#DC2626' : f.einvoice ? '#7C3AED' : f.proforma ? '#D97706' : f.b2c ? '#4F46E5' : '#2563EB'}">
                <span class="fmt-mini-top"><span class="fmt-mini-brand"></span><span class="fmt-mini-tag"></span></span>
                <span class="fmt-mini-ln w70"></span><span class="fmt-mini-ln w45"></span><span class="fmt-mini-ln w70"></span>
                <span class="fmt-mini-tot"></span>
            </span>
            <span class="fmt-card-body">
                <span class="fmt-card-title">${f.ico} ${f.label}</span>
                <span class="fmt-card-sub">${f.tag}</span>
                <span class="fmt-tags">${f.tags.map(t => `<span class="fmt-tag">${t}</span>`).join('')}</span>
            </span>
        </button>`;
    });
    grid.innerHTML = h;
    grid.querySelectorAll('[data-inv-fmt]').forEach(b => b.addEventListener('click', () => invSetFormat(b.dataset.invFmt)));
    invSetFormat(invFmt, true);
}

function invSetFormat(k, quiet) {
    const f = INV_FORMATS[k];
    if (!f) return;
    invFmt = k;

    document.querySelectorAll('[data-inv-fmt]').forEach(b => {
        const on = b.dataset.invFmt === k;
        b.classList.toggle('active', on);
        b.setAttribute('aria-pressed', on);
    });

    document.getElementById('fmtInfoIco').textContent = f.ico;
    document.getElementById('fmtInfoTitle').textContent = f.label;
    document.getElementById('fmtInfoDesc').textContent = f.desc;
    document.getElementById('fmtInfoFields').textContent = f.fields;

    document.getElementById('bGstLbl').textContent = f.buyerGst ? 'Buyer GSTIN (required)' : 'Buyer GSTIN (optional)';
    document.getElementById('hsnHd').textContent = f.hsn;
    document.getElementById('unitHd').textContent = f.unit ? 'Unit' : 'Disc %';
    document.getElementById('invNoLbl').textContent = f.noLbl;
    document.getElementById('pNoLbl').textContent = f.noLbl + ':';
    document.getElementById('pRightTitle').textContent = f.rightTitle;
    document.getElementById('pGrandLbl').textContent = f.advance ? 'Balance Payable' : 'Grand Total';

    const show = (id, on) => { const e = document.getElementById(id); if (e) e.style.display = on ? '' : 'none'; };
    show('posRow', f.pos);
    show('expRow', f.export);
    show('advRow', f.advance);
    show('irnRow', f.einvoice);

    invSwapCells();
    if (!quiet) invUpdate();
}

/* ---------- Item rows ---------- */
function invUnitDiscCell() {
    const f = INV_FORMATS[invFmt];
    if (f.unit) {
        return `<select data-inv-unit onchange="invUpdate()"><option>NOS</option><option selected>PCS</option><option>KGS</option><option>LTR</option><option>BOX</option><option>SET</option><option>MTR</option><option>DOZ</option><option>TON</option></select>`;
    }
    return `<input type="number" data-inv-disc placeholder="0" min="0" max="100" step="0.01" value="0" oninput="invUpdate()">`;
}

function invAddRow(seed) {
    seed = seed || {};
    const f = INV_FORMATS[invFmt];
    const id = invRowId++;

    document.getElementById('invItems').insertAdjacentHTML('beforeend',
        `<div class="doc-item" data-row="${id}">
            <input type="text" data-inv-name placeholder="Item or service description" oninput="invUpdate()">
            <input type="text" data-inv-hsn placeholder="${f.hsn}" maxlength="8" oninput="invUpdate()">
            <input type="number" data-inv-qty value="${seed.qty || 1}" min="0" step="0.01" oninput="invUpdate()">
            <div data-slot="unitdisc">${invUnitDiscCell()}</div>
            <input type="number" data-inv-rate value="0" min="0" step="0.01" oninput="invUpdate()">
            <select data-inv-gst onchange="invUpdate()">${DOC.GST_RATES.map(r => `<option value="${r}"${r === '18' ? ' selected' : ''}>${r}%</option>`).join('')}</select>
            <span class="doc-item-amt" data-inv-amt>&#8377;0.00</span>
            <button type="button" class="doc-item-del" onclick="invDelRow(${id})" title="Remove item" aria-label="Remove item">&times;</button>
        </div>`);
    invUpdate();
}

/** Swaps the Unit / Discount % cell of every existing row when the format changes. */
function invSwapCells() {
    const f = INV_FORMATS[invFmt];
    document.querySelectorAll('#invItems [data-slot="unitdisc"]').forEach(slot => {
        slot.innerHTML = invUnitDiscCell();
    });
    document.querySelectorAll('#invItems [data-inv-hsn]').forEach(i => {
        i.placeholder = f.hsn;
    });
}

function invDelRow(id) {
    const e = document.querySelector(`[data-row="${id}"]`);
    if (e) e.remove();
    invUpdate();
}

/* ---------- Totals engine ---------- */
function invCollect() {
    const f = INV_FORMATS[invFmt];
    const isExp = !!f.export;
    const inter = isExp || DOC.isInterState('sState', 'bState');
    const rcm = f.rcm && DOC.val('bRcm') === 'yes';

    let sub = 0, tax = 0, discTotal = 0;
    const rows = [];

    document.querySelectorAll('#invItems .doc-item').forEach((el, i) => {
        const g = (s) => el.querySelector(s);
        const gv = (s) => { const n = g(s); return n ? (n.value === undefined ? '' : n.value) : ''; };
        const name = (gv('[data-inv-name]') || 'Item').trim();
        const hsn = gv('[data-inv-hsn]').trim();
        const q = parseFloat(gv('[data-inv-qty]')) || 0;
        const rate = parseFloat(gv('[data-inv-rate]')) || 0;
        const gr = isExp ? 0 : (parseFloat(gv('[data-inv-gst]')) || 0);
        const disc = f.unit ? 0 : (parseFloat(gv('[data-inv-disc]')) || 0);
        const unit = f.unit ? (gv('[data-inv-unit]') || '') : '';
        const gross = q * rate;
        const discAmt = DOC.round2(gross * disc / 100);
        const taxable = DOC.round2(gross - discAmt);
        const taxAmt = DOC.round2(taxable * gr / 100);
        const amtCell = g('[data-inv-amt]');
        if (amtCell) amtCell.textContent = DOC.money(taxable + taxAmt);
        sub += taxable; tax += taxAmt; discTotal += discAmt;
        rows.push({ i: i + 1, name, hsn, q, rate, gr, unit, disc, discAmt, taxable, taxAmt, total: DOC.round2(taxable + taxAmt) });
    });

    sub = DOC.round2(sub); tax = DOC.round2(tax);
    const manualDisc = DOC.num('disc');
    const afterDisc = DOC.round2(Math.max(0, sub - manualDisc));
    const roundOff = DOC.num('round');
    const advance = f.advance ? DOC.num('bAdvance') : 0;
    const grand = DOC.round2(afterDisc + tax + roundOff - advance);

    return { f, isExp, inter, rcm, rows, sub, tax, discTotal, manualDisc, afterDisc, roundOff, advance, grand };
}

/* ---------- Preview render ---------- */
function invUpdate() {
    const d = invCollect();

    DOC.text('pSName', DOC.val('sName'));
    DOC.text('pSAddr', DOC.val('sAddr'));
    DOC.text('pSPhone', 'Phone: ' + (DOC.val('sPhone') || '\u2014'));
    DOC.text('pSState', 'State: ' + (DOC.val('sState') || '\u2014'));
    DOC.text('pSGst', DOC.val('sGst'));
    DOC.text('pSCode', DOC.val('sStateCode') ? 'State Code: ' + DOC.val('sStateCode') : '');
    DOC.showIf('pSCode', !!DOC.val('sStateCode'));
    const trade = DOC.val('sTrade');
    DOC.showIf('pSTrade', !!trade);
    if (trade) document.getElementById('pSTrade').textContent = trade;

    DOC.text('pTitle', d.f.title);
    DOC.text('pInvNo', DOC.val('invNo'));
    DOC.text('pInvDate', DOC.fmtDate(DOC.val('invDate')));
    DOC.text('pBName', DOC.val('bName'));
    DOC.text('pBAddr', DOC.val('bAddr'));
    DOC.text('pBPhone', 'Phone: ' + (DOC.val('bPhone') || '\u2014'));
    DOC.text('pBGst', DOC.val('bGst'));
    DOC.showIf('pBGstRow', d.f.buyerGst);

    const pos = DOC.val('bPos') || (DOC.val('bState') || '\u2014');
    DOC.text('pPos', pos);
    DOC.showIf('pPosRow', d.f.pos);
    DOC.text('pCountry', DOC.val('bCountry') || (DOC.val('bState') || '\u2014'));
    DOC.showIf('pExpRow', d.f.export);
    DOC.showIf('pRcmRow', d.rcm);
    DOC.showIf('pRcmNote', d.rcm);
    DOC.showIf('pRcmRow2', d.rcm);

    const ref = DOC.val('bPo');
    DOC.showIf('pRefRow', d.f.advance && !!ref);
    if (ref) document.getElementById('pRef').textContent = ref;
    DOC.text('pIrn', DOC.val('invIrn'));
    DOC.showIf('pIrnRow', d.f.einvoice && !!DOC.val('invIrn'));
    DOC.text('pAck', DOC.val('invAck'));
    DOC.showIf('pAckRow', d.f.einvoice && !!DOC.val('invAck'));

    /* item table */
    const tb = document.getElementById('pRows');
    if (!d.rows.length) {
        tb.innerHTML = '<tr><td colspan="7" style="padding:14px;text-align:center;color:#94A3B8;">No items yet</td></tr>';
    } else {
        tb.innerHTML = d.rows.map(r => `<tr>
            <td>${r.i}</td>
            <td>${r.name}${r.hsn ? ` <span style="color:#94A3B8;">[${r.hsn}]</span>` : ''}</td>
            <td style="text-align:right;">${DOC.qty(r.q)}${r.unit ? ' ' + r.unit : ''}</td>
            <td style="text-align:right;">${DOC.money(r.rate)}</td>
            <td style="text-align:right;">${r.gr}%</td>
            <td style="text-align:right;">${DOC.money(r.taxAmt)}</td>
            <td style="text-align:right;">${DOC.money(r.total)}</td>
        </tr>`).join('');
    }

    /* totals */
    DOC.text('pSub', DOC.money(d.afterDisc));
    DOC.showIf('pDiscRow', d.manualDisc > 0);
    if (d.manualDisc > 0) document.getElementById('pDisc').textContent = '\u2212' + DOC.money(d.manualDisc);
    DOC.showIf('pRoundRow', d.roundOff !== 0);
    if (d.roundOff !== 0) document.getElementById('pRound').textContent = (d.roundOff < 0 ? '\u2212' : '') + DOC.money(Math.abs(d.roundOff));

    if (d.rcm) {
        DOC.showIf('pCgstRow', false); DOC.showIf('pSgstRow', false); DOC.showIf('pIgstRow', false);
        DOC.text('pRcm', DOC.money(d.tax));
    } else if (d.isExp) {
        DOC.showIf('pCgstRow', false); DOC.showIf('pSgstRow', false);
        DOC.showIf('pIgstRow', true); DOC.text('pIgst', 'Nil (0.00)');
    } else if (d.inter) {
        DOC.showIf('pCgstRow', false); DOC.showIf('pSgstRow', false);
        DOC.showIf('pIgstRow', true); DOC.text('pIgst', DOC.money(d.tax));
    } else {
        DOC.showIf('pIgstRow', false);
        DOC.showIf('pCgstRow', true); DOC.text('pCgst', DOC.money(d.tax / 2));
        DOC.showIf('pSgstRow', true); DOC.text('pSgst', DOC.money(d.tax - d.tax / 2));
    }
    DOC.showIf('pAdvRow2', d.f.advance && d.advance > 0);
    if (d.advance > 0) document.getElementById('pAdv').textContent = '\u2212' + DOC.money(d.advance);

    DOC.text('pGrand', DOC.money(d.grand));
    document.getElementById('pGrandLbl').textContent = d.f.advance ? 'Balance Payable' : 'Grand Total';
    document.getElementById('pStripTotal').textContent = DOC.money(d.grand);

    /* toggles */
    const words = document.getElementById('optWords').checked;
    DOC.showIf('pWordsRow', words);
    if (words) document.getElementById('pWords').textContent = DOC.inWords(d.grand);
    DOC.showIf('pSigRow', document.getElementById('optSignature').checked);
    if (document.getElementById('optSignature').checked) document.getElementById('pSigName').textContent = DOC.val('sName') || 'the seller';

    const notes = DOC.val('invNotes');
    DOC.showIf('pNotesRow', !!notes);
    if (notes) document.getElementById('pNotes').textContent = notes;

    const bank = document.getElementById('optBank').checked;
    DOC.showIf('pBankRow', bank);
    if (bank) {
        const bits = [];
        if (DOC.val('bBank')) bits.push(DOC.val('bBank'));
        if (DOC.val('bAcct')) bits.push('A/c ' + DOC.val('bAcct'));
        if (DOC.val('bIfsc')) bits.push('IFSC ' + DOC.val('bIfsc'));
        if (DOC.val('bUpi')) bits.push('UPI ' + DOC.val('bUpi'));
        document.getElementById('pBankTxt').textContent = bits.length ? bits.join(' \u00B7 ') : 'Add your bank details above';
    }
    DOC.showIf('pRetRow', document.getElementById('optReturn').checked);
}

/* ---------- Actions ---------- */
function invPreviewJump() { invUpdate(); DOC.scrollToPreview('invPreview'); }
function invPrint() { invUpdate(); window.print(); }

function invReset() {
    ['sName', 'sGst', 'sPhone', 'sAddr', 'sStateCode', 'sTrade', 'bName', 'bGst', 'bPhone', 'bAddr',
        'bPos', 'bCountry', 'bPort', 'bPo', 'invNo', 'invNotes', 'bBank', 'bAcct', 'bIfsc', 'bUpi'].forEach(id => {
            const e = document.getElementById(id); if (e) e.value = '';
        });
    ['disc', 'round', 'bAdvance'].forEach(id => { const e = document.getElementById(id); if (e) e.value = 0; });
    document.getElementById('bRcm').value = 'no';
    document.getElementById('sState').value = ''; document.getElementById('bState').value = '';
    document.getElementById('invDate').value = DOC.todayISO();
    document.getElementById('invItems').querySelectorAll('.doc-item').forEach(e => e.remove());
    invAddRow();
    invSetFormat('b2b');
    invUpdate();
}

function invPDF() {
    invUpdate();
    const d = invCollect();
    const doc = DOC.newDoc();
    if (!doc) return;

    const W = doc.internal.pageSize.getWidth();
    const MG = 14;
    const accent = DOC.currentAccentRGB();

    let y = DOC.pdfHeader(doc, {
        org: DOC.val('sName') || 'Your Business',
        left: [
            { value: DOC.val('sAddr') || 'Address' },
            { value: 'Phone: ' + (DOC.val('sPhone') || '\u2014') + (DOC.val('sState') ? ' \u00B7 ' + DOC.val('sState') : '') },
            { value: 'GSTIN: ' + (DOC.val('sGst') || '\u2014') + (DOC.val('sStateCode') ? ' \u00B7 State Code: ' + DOC.val('sStateCode') : '') }
        ],
        title: d.f.title,
        right: [
            { label: d.f.noLbl, value: DOC.val('invNo') || '\u2014' },
            { label: 'Date', value: DOC.fmtDate(DOC.val('invDate')) || '\u2014' },
            { label: 'Place of supply', value: d.f.pos ? (DOC.val('bPos') || '\u2014') : 'N/A' }
        ],
        accent: accent
    });

    /* buyer block */
    y = DOC.pdfSection(doc, MG, y, 'BILL TO', 60);
    doc.setFont('helvetica', 'bold'); doc.setFontSize(9); doc.setTextColor(15, 23, 42);
    doc.text(DOC.val('bName') || 'Customer', MG, y + 4.5); y += 8;
    doc.setFont('helvetica', 'normal'); doc.setFontSize(8);
    (function () {
        const lines = doc.splitTextToSize(DOC.val('bAddr') || '', 90);
        doc.text(lines, MG, y); y += lines.length * 3.6;
    })();
    doc.text('Phone: ' + (DOC.val('bPhone') || '\u2014') + (d.f.buyerGst ? '  \u00B7  GSTIN: ' + (DOC.val('bGst') || '\u2014') : ''), MG, y); y += 4;
    if (d.rcm) {
        doc.setFont('helvetica', 'bold'); doc.setTextColor(185, 28, 28);
        doc.text('Reverse Charge: Tax payable by the recipient.', MG, y); y += 4;
        doc.setFont('helvetica', 'normal'); doc.setTextColor(51, 65, 85);
    }
    if (d.f.export) {
        doc.setTextColor(13, 148, 136);
        doc.text('Country: ' + (DOC.val('bCountry') || '\u2014') + '  \u00B7  Port Code: ' + (DOC.val('bPort') || '\u2014'), MG, y); y += 4;
        doc.setTextColor(51, 65, 85);
    }
    if (d.f.advance && DOC.val('bPo')) { doc.text('Reference: PO ' + DOC.val('bPo'), MG, y); y += 4; }
    if (d.f.einvoice) {
        doc.setTextColor(124, 58, 237);
        doc.text('IRN: ' + (DOC.val('invIrn') || '\u2014'), MG, y); y += 3.6;
        doc.text('Ack. No.: ' + (DOC.val('invAck') || '\u2014') + '  \u00B7  UIN: as registered on the portal', MG, y); y += 4;
        doc.setTextColor(51, 65, 85);
    }
    y += 4;

    /* items table */
    const head = ['#', 'Description', d.f.hsn, 'Qty', 'Rate', 'GST%', 'Taxable', 'Tax', 'Total'];
    const body = d.rows.length ? d.rows.map(r => [
        r.i, r.name + (r.unit ? ' (' + r.unit + ')' : ''), r.hsn || '\u2014',
        DOC.qty(r.q), DOC.money(r.rate), r.gr + '%',
        DOC.money(r.taxable), DOC.money(r.taxAmt), DOC.money(r.total)
    ]) : [['', 'No items', '', '', '', '', '', '', '']];

    y = DOC.pdfTable(doc, head, body, {
        startY: y, accent: accent,
        extra: { columnStyles: { 0: { cellWidth: 8 }, 3: { halign: 'right' }, 4: { halign: 'right' }, 5: { halign: 'right' }, 6: { halign: 'right' }, 7: { halign: 'right' }, 8: { halign: 'right', fontStyle: 'bold' } } }
    }) + 5;

    /* totals */
    const rows = [{ label: 'Taxable Value', value: DOC.money(d.afterDisc) }];
    if (d.manualDisc > 0) rows.push({ label: 'Discount', value: '\u2212' + DOC.money(d.manualDisc) });
    if (d.rcm) {
        rows.push({ label: 'Tax on RCM (payable by buyer)', value: DOC.money(d.tax) });
    } else if (d.isExp) {
        rows.push({ label: 'IGST (Export under LUT)', value: 'Nil (0.00)' });
    } else if (d.inter) {
        rows.push({ label: 'IGST', value: DOC.money(d.tax) });
    } else {
        rows.push({ label: 'CGST', value: DOC.money(d.tax / 2) });
        rows.push({ label: 'SGST', value: DOC.money(d.tax - d.tax / 2) });
    }
    if (d.f.advance && d.advance > 0) rows.push({ label: 'Advance adjusted', value: '\u2212' + DOC.money(d.advance) });
    if (d.roundOff !== 0) rows.push({ label: 'Round off', value: (d.roundOff < 0 ? '\u2212' : '') + DOC.money(Math.abs(d.roundOff)) });
    rows.push({ rule: true, gap: 2 });
    rows.push({
        label: d.f.advance ? 'Balance Payable' : 'Grand Total', value: DOC.money(d.grand),
        bold: true, size: 10.5, color: accent, gap: 6
    });

    y = DOC.pdfTotals(doc, W, y, rows);

    if (document.getElementById('optWords').checked) {
        y += 2;
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Amount in words: ' + DOC.inWords(d.grand), MG, y); y += 6;
        doc.setFont('helvetica', 'normal');
    }
    if (DOC.val('invNotes')) {
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Notes:', MG, y); y += 4;
        doc.setFont('helvetica', 'normal');
        const nl = doc.splitTextToSize(DOC.val('invNotes'), W - MG * 2);
        doc.text(nl, MG, y); y += nl.length * 3.6 + 3;
    }
    if (document.getElementById('optBank').checked) {
        const bits = [];
        if (DOC.val('bBank')) bits.push(DOC.val('bBank'));
        if (DOC.val('bAcct')) bits.push('A/c ' + DOC.val('bAcct'));
        if (DOC.val('bIfsc')) bits.push('IFSC ' + DOC.val('bIfsc'));
        if (DOC.val('bUpi')) bits.push('UPI ' + DOC.val('bUpi'));
        if (bits.length) {
            doc.setDrawColor(226, 232, 240); doc.setLineWidth(0.2);
            doc.line(MG, y, W - MG, y); y += 4;
            doc.setFontSize(7.6); doc.setTextColor(100, 116, 139);
            doc.text('Bank details: ' + bits.join('  \u00B7  '), MG, y); y += 5;
        }
    }
    if (document.getElementById('optReturn').checked) {
        doc.setFontSize(7); doc.setTextColor(130, 138, 145);
        doc.text('Returns: goods once sold will not be taken back. Interest @18% p.a. charged on overdue payments.', MG, y); y += 5;
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
        doc.text('Customer Signature', MG + colW / 2, sy + 4, { align: 'center' });
        doc.text('Receiver', MG + colW * 1.5, sy + 4, { align: 'center' });
        doc.setFont('helvetica', 'bold');
        doc.text('For ' + (DOC.val('sName') || 'the seller'), MG + colW * 2.5, sy + 4, { align: 'center' });
        doc.setFont('helvetica', 'normal');
        y = sy + 10;
    }

    DOC.pdfFooter(doc, y, (DOC.val('invNo') || 'invoice') + (d.f.einvoice && DOC.val('invIrn') ? ' \u00B7 IRN ' + DOC.val('invIrn') : ''));
    doc.save(((DOC.val('invNo') || 'invoice').replace(/[^\w\-]+/g, '_')) + '.pdf');
}

/* ---------- Boot ---------- */
(function () {
    DOC.fillStates('sState', 'Select state');
    DOC.fillStates('bState', 'Select state');
    document.getElementById('invDate').value = DOC.todayISO();
    invRenderFormats();
    invAddRow();

    const ids = ['sName', 'sGst', 'sPhone', 'sAddr', 'sState', 'sStateCode', 'sTrade',
        'bName', 'bGst', 'bPhone', 'bAddr', 'bState', 'bPos', 'bRcm', 'bCountry', 'bPort', 'bPo',
        'invNo', 'invDate', 'invIrn', 'invAck', 'disc', 'round', 'invNotes',
        'bBank', 'bAcct', 'bIfsc', 'bUpi', 'optSignature', 'optBank', 'optWords', 'optReturn'];
    DOC.bind(ids, invUpdate);

    DOC.bindSwatches('data-inv-accent', invUpdate);

    invUpdate();
})();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
