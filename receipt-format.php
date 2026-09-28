<?php
/**
 * My Metaji - Receipt Format
 * Lets the user pick a receipt format (Payment / GRN / Delivery / Money / Advance),
 * fill it in, preview it live and download it as a PDF.
 */
require_once __DIR__ . '/config.php';
$page_title = 'Free Receipt Format — Payment Receipt & GRN Templates | ' . BRAND_NAME;
$page_desc  = 'Create free receipts in the right format: payment receipt, goods receipt note (GRN), delivery receipt, money receipt and advance receipt. Fill in and download a print-ready PDF.';
include __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="gst-hero">
    <div class="gst-wrap">
        <div class="gst-hero-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"/></svg>
            100% audit ready &middot; No sign-up required
        </div>
        <h1>Free <span>Receipt Format</span></h1>
        <p>Proof of payment, in the format the law and your accountant expect. Pick a payment receipt, goods receipt note, delivery receipt, money receipt or advance receipt &mdash; fill the details and download a print-ready PDF.</p>
        <div class="gst-hero-chips">
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
                5 receipt formats
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M7 11V6a2 2 0 0 1 4 0v6"/><path d="M13 9a2 2 0 0 1 4 0v6a6 6 0 0 1-6 6H9l-4-3 3-8"/></svg>
                Amount in words auto
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 3v12"/><path d="M7 11l5 5 5-5"/><path d="M4 21h16"/></svg>
                Instant PDF download
            </span>
        </div>
    </div>
</section>

<!-- ===================== TOOL ===================== -->
<section class="gst-tool" id="receipt-format-tool">
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
                            <h3>Choose Your Receipt Format</h3>
                            <p>A receipt proves something actually happened. Pick the one that matches.</p>
                        </div>
                        <span class="qm-step">Step 1</span>
                    </div>

                    <div class="fmt-grid" id="fmtGrid" role="group" aria-label="Receipt format"></div>

                    <div class="note-box" style="margin-top:16px;">
                        <div class="note-ico" id="fmtInfoIco">&#128176;</div>
                        <div>
                            <h4 id="fmtInfoTitle">Payment Receipt</h4>
                            <p id="fmtInfoDesc">Issued after a customer pays. Proves the money has reached you and tells the accountant which invoice it settles.</p>
                            <p style="margin:0;"><strong>Mandatory fields:</strong> <span id="fmtInfoFields">Receipt number, date, payer name, amount in words, payment mode, reference number, invoice number</span></p>
                        </div>
                    </div>

                    <div class="doc-split" style="margin-top:18px;">
                        <div>
                            <div class="doc-opt">
                                <span>Accent colour</span>
                                <div class="doc-swatches">
                                    <button type="button" class="doc-swatch active" data-rcpt-accent="blue" style="background:#2563EB" aria-label="Blue"></button>
                                    <button type="button" class="doc-swatch" data-rcpt-accent="indigo" style="background:#4F46E5" aria-label="Indigo"></button>
                                    <button type="button" class="doc-swatch" data-rcpt-accent="green" style="background:#059669" aria-label="Green"></button>
                                    <button type="button" class="doc-swatch" data-rcpt-accent="teal" style="background:#0D9488" aria-label="Teal"></button>
                                    <button type="button" class="doc-swatch" data-rcpt-accent="amber" style="background:#D97706" aria-label="Amber"></button>
                                    <button type="button" class="doc-swatch" data-rcpt-accent="red" style="background:#DC2626" aria-label="Red"></button>
                                    <button type="button" class="doc-swatch" data-rcpt-accent="slate" style="background:#334155" aria-label="Slate"></button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="doc-switches">
                                <label class="doc-chk"><input type="checkbox" id="optWords" checked> Amount in words</label>
                                <label class="doc-chk"><input type="checkbox" id="optSig" checked> Signature block</label>
                                <label class="doc-chk"><input type="checkbox" id="optBounce" checked> Bounce charge note</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. ISSUER -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14"/><path d="M10 12h4M10 16h4"/></svg>
                        </div>
                        <div>
                            <h3>Issuing Business</h3>
                            <p>Who is giving this receipt &mdash; the details that make it traceable.</p>
                        </div>
                        <span class="qm-step">Step 2</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Business / Firm Name</label><input type="text" id="rName" placeholder="As printed on your invoice"></div>
                        <div class="inv-gen-field"><label>Phone</label><input type="tel" id="rPhone" placeholder="98765 43210"></div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Address</label><input type="text" id="rAddr" placeholder="Street, city, state, PIN"></div>
                    <div class="inv-gen-field"><label>GSTIN (optional)</label><input type="text" id="rGst" placeholder="24AAAAA0000A1Z5" maxlength="15"></div>
                </div>

                <!-- 3. RECEIPT DETAILS -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M8 9h8M8 13h8M8 17h4"/></svg>
                        </div>
                        <div>
                            <h3>Receipt Details</h3>
                            <p>Serial number, date and how the money or goods moved.</p>
                        </div>
                        <span class="qm-step">Step 3</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label id="rNoLbl">Receipt Number</label><input type="text" id="rNo" placeholder="RCP-001"></div>
                        <div class="inv-gen-field"><label id="rDateLbl">Receipt Date</label><input type="date" id="rDate"></div>
                    </div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>Payment / Receipt Mode</label>
                            <select id="rMode">
                                <option>Cash</option>
                                <option>UPI</option>
                                <option>Card</option>
                                <option>Bank Transfer</option>
                                <option>Cheque</option>
                                <option>NEFT / RTGS</option>
                                <option>Cheque / Demand Draft</option>
                            </select>
                        </div>
                        <div class="inv-gen-field"><label id="rInvLbl">Against Invoice No.</label><input type="text" id="rInv" placeholder="INV-1042"></div>
                    </div>
                    <div class="doc-split" style="margin-top:10px;">
                        <div class="inv-gen-field"><label>Reference / Transaction ID</label><input type="text" id="rRef" placeholder="Txn / Ref number"></div>
                        <div class="inv-gen-field"><label>UTR / UPI Reference</label><input type="text" id="rUtr" placeholder="UTR 412298000123"></div>
                    </div>

                    <div class="doc-split" id="chqRow" style="margin-top:10px; display:none;">
                        <div class="inv-gen-field"><label>Bank Name</label><input type="text" id="rBank" placeholder="HDFC Bank"></div>
                        <div class="inv-gen-field"><label>Cheque / DD Number</label><input type="text" id="rChq" placeholder="004521"></div>
                    </div>

                    <div class="doc-split" id="grnRow" style="margin-top:10px; display:none;">
                        <div class="inv-gen-field"><label>Item / Material Received</label><input type="text" id="gItem" placeholder="e.g. MS Angle 50x50x6mm"></div>
                        <div class="inv-gen-field"><label>Quantity Received</label><input type="number" id="gQty" value="0" min="0" step="0.01" oninput="rcptUpdate()"></div>
                    </div>
                    <div class="doc-split" id="grnRow2" style="margin-top:10px; display:none;">
                        <div class="inv-gen-field"><label>Unit</label>
                            <select id="gUnit"><option>NOS</option><option selected>PCS</option><option>KGS</option><option>MTR</option><option>BOX</option><option>SET</option><option>TON</option><option>LTR</option></select>
                        </div>
                        <div class="inv-gen-field"><label>Condition of Goods</label>
                            <select id="gCond"><option>Good &mdash; accepted in full</option><option>Accepted with minor damage</option><option>Partly accepted &mdash; short supply</option><option>Rejected at gate</option><option>Awaiting inspection</option></select>
                        </div>
                    </div>
                    <div class="doc-split" id="grnRow3" style="margin-top:10px; display:none;">
                        <div class="inv-gen-field"><label>Gate Entry No.</label><input type="text" id="gGate" placeholder="GE-2026-0412"></div>
                        <div class="inv-gen-field"><label>Purchase Order / Bill Ref.</label><input type="text" id="gPo" placeholder="PO-118 / Supplier bill no."></div>
                    </div>

                    <div class="doc-split" id="valRow" style="margin-top:10px; display:none;">
                        <div class="inv-gen-field"><label>Total Invoice / Order Value (₹)</label><input type="number" id="rInvVal" value="0" min="0" step="0.01" oninput="rcptUpdate()"></div>
                        <div class="inv-gen-field"><label>Part of Full Payment</label>
                            <select id="rPart"><option value="no">No &mdash; full amount received</option><option value="yes">Yes &mdash; part / advance payment</option></select>
                        </div>
                    </div>
                </div>

                <!-- 4. PARTY -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="4"/></svg>
                        </div>
                        <div>
                            <h3 id="pBlockTitle">Received From</h3>
                            <p id="pBlockSub">Full name, contact and address of the payer or supplier.</p>
                        </div>
                        <span class="qm-step">Step 4</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label id="pNameLbl">Payer / Customer Name</label><input type="text" id="pName" placeholder="Name as per the invoice"></div>
                        <div class="inv-gen-field"><label>Phone</label><input type="tel" id="pPhone" placeholder="98765 43210"></div>
                    </div>
                    <div class="inv-gen-field"><label id="pAddrLbl">Address</label><input type="text" id="pAddr" placeholder="Street, city, state, PIN"></div>
                </div>

                <!-- 5. VALUE / AMOUNT -->
                <div class="qm-card" id="amtCard">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="M7 7h7a3 3 0 0 1 0 6H7"/></svg>
                        </div>
                        <div>
                            <h3 id="amtTitle">Amount Received</h3>
                            <p>The single figure this receipt is about &mdash; no item table needed.</p>
                        </div>
                        <span class="qm-step">Step 5</span>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label id="rAmtLbl">Amount Received (₹)</label><input type="number" id="rAmt" value="0" min="0" step="0.01" oninput="rcptUpdate()"></div>

                    <div id="delWrap" style="display:none;">
                        <p class="inv-gen-section-title" style="margin:6px 0 8px;">Items Delivered</p>
                        <div class="doc-items" id="delItems" style="--doc-cols: 3fr 1fr 1fr auto">
                            <div class="doc-item-hd">
                                <span>Item / Description</span><span>Qty</span><span>Unit</span><span></span>
                            </div>
                        </div>
                        <button type="button" class="inv-gen-add-item" onclick="rcptAddRow()">+ Add Delivered Item</button>
                    </div>
                </div>

                <!-- 6. NOTES -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>
                        </div>
                        <div>
                            <h3>Notes</h3>
                            <p>Anything the reader needs to know &mdash; subject, period, terms.</p>
                        </div>
                        <span class="qm-step">Step 6</span>
                    </div>
                    <div class="inv-gen-field"><label>Notes / Remarks</label><textarea id="rNotes" rows="3" placeholder="Received against invoice INV-1042 dated 04 Apr 2026. Cheque subject to realisation."></textarea></div>
                </div>

                <!-- ACTIONS -->
                <div class="qm-actions doc-no-print">
                    <button type="button" class="qm-btn qm-btn-primary" onclick="rcptPreviewJump()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        Preview
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="rcptPrint()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="1"/><path d="M6 17h12v4H6z"/></svg>
                        Print
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="rcptReset()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                        Reset
                    </button>
                    <button type="button" class="qm-btn qm-btn-primary" style="flex:2" onclick="rcptPDF()">
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
                <div class="inv-gen-preview" id="rcptPreview">
                    <div class="doc-sheet doc-sheet-lg" id="rcptSheet">

                        <div class="doc-hd">
                            <div class="doc-org">
                                <h2 id="pOrg">Your Business</h2>
                                <p id="pOrgAddr">Address</p>
                                <p><span id="pOrgPhone">Phone: &mdash;</span></p>
                                <p id="pGstRow" style="display:none"><strong>GSTIN:</strong> <span id="pGst">&mdash;</span></p>
                            </div>
                            <div class="doc-id">
                                <div class="doc-title" id="pTitle">PAYMENT RECEIPT</div>
                                <p><strong id="pNoLbl">Receipt No.:</strong> <span id="pNo">&mdash;</span></p>
                                <p><strong id="pDateLbl">Date:</strong> <span id="pDate">&mdash;</span></p>
                            </div>
                        </div>

                        <div class="doc-split" style="margin-bottom:6px;">
                            <div>
                                <p class="doc-sec-title" id="pPartyTitle">Received From</p>
                                <p style="font-size:11px;font-weight:700;margin:3px 0;" id="pPartyName">Payer</p>
                                <p id="pPartyPhone" style="font-size:10px;color:#475569;margin:1px 0;">Phone: &mdash;</p>
                                <p id="pPartyAddr" style="font-size:10px;color:#475569;margin:1px 0;">Address</p>
                            </div>
                            <div>
                                <p class="doc-sec-title" id="pDetailTitle">Payment Details</p>
                                <p id="pModeRow" style="font-size:10px;color:#475569;margin:2px 0;"><strong id="pModeLbl">Mode:</strong> <span id="pMode">Cash</span></p>
                                <p id="pInvRow" style="font-size:10px;color:#475569;margin:2px 0;"><strong>Against Inv.:</strong> <span id="pInv">&mdash;</span></p>
                                <p id="pRefRow" style="font-size:10px;color:#475569;margin:2px 0;"><strong>Ref / Txn:</strong> <span id="pRef">&mdash;</span></p>
                                <p id="pUtrRow" style="font-size:10px;color:#475569;margin:2px 0;"><strong>UTR:</strong> <span id="pUtr">&mdash;</span></p>
                                <p id="pChqRow" style="font-size:10px;color:#475569;margin:2px 0;"><strong>Instrument:</strong> <span id="pChq">&mdash;</span></p>
                                <p id="pGateRow" style="font-size:10px;color:#475569;margin:2px 0;"><strong>Gate Entry:</strong> <span id="pGate">&mdash;</span></p>
                                <p id="pPoRow" style="font-size:10px;color:#475569;margin:2px 0;"><strong>PO / Ref:</strong> <span id="pPo">&mdash;</span></p>
                                <p id="pCondRow" style="font-size:10px;color:#475569;margin:2px 0;"><strong>Condition:</strong> <span id="pCond">&mdash;</span></p>
                            </div>
                        </div>

                        <div class="amt-hero" id="pHero">
                            <div class="amt-hero-lbl" id="pHeroLbl">Amount Received</div>
                            <div class="amt-hero-val" id="pHeroVal">₹0.00</div>
                        </div>

                        <div id="pBalRow" style="display:none; font-size:10px;color:#475569; padding:7px 0; border-top:1px solid #E2E8F0; border-bottom:1px solid #E2E8F0; margin-bottom:6px;">
                            <strong id="pBalLbl">Balance Remaining</strong>
                            <span id="pBal" style="float:right;font-weight:800;">₹0.00</span>
                        </div>

                        <div id="pWordsRow" style="font-size:9.5px;color:#475569;padding:8px 0;">
                            <strong>Amount in words:</strong> <span id="pWords">Zero Only</span>
                        </div>

                        <div id="pDelWrap" style="display:none; margin:8px 0;">
                            <p class="doc-sec-title">Items Delivered</p>
                            <div style="overflow-x:auto;">
                                <table class="bs-table" id="pDelTable" style="font-size:10px; min-width:260px;">
                                    <thead><tr id="pDelHead"></tr></thead>
                                    <tbody id="pDelRows"></tbody>
                                </table>
                            </div>
                        </div>

                        <div id="pBounce" class="note-box red" style="display:none; padding:8px 10px; margin:8px 0;">
                            <div class="note-ico" style="font-size:15px;">&#9888;</div>
                            <p style="margin:0;font-size:9.5px;">Cheques that are dishonoured will be charged a <strong>₹500/- per instance</strong> plus the bank charges, and the drawer is liable to prosecution under Section 138 of the Negotiable Instruments Act, 1881. This receipt is valid only on realisation.</p>
                        </div>

                        <div id="pNotesRow" style="display:none;font-size:9.5px;color:#475569;padding:6px 0;">
                            <strong>Notes:</strong> <span id="pNotes"></span>
                        </div>

                        <div class="doc-sig" id="pSig">
                            <div><div class="doc-sig-line" id="pSig1">Received by</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Signature of Payer</div></div>
                            <div><div class="doc-sig-line" id="pSig2">Witness</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Witness</div></div>
                            <div><div class="doc-sig-line" id="pSig3">For &mdash;</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Authorised Signatory</div></div>
                        </div>
                        <div class="doc-ft">This is a computer-generated receipt &middot; Generated by <strong>My Metaji</strong></div>
                    </div>
                </div>
                <div class="qm-total-strip">
                    <span id="pStripLbl">Amount Received</span>
                    <strong id="pStripAmt">₹0.00</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== FORMAT COMPARISON ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>Which Receipt Format Do You Actually Need?</h2>
        <p>An invoice asks for money. A receipt proves it arrived. Using the wrong one leaves a gap in your books that no accountant can close, so match the document to the event first.</p>
        <div class="fmt-table-wrap" style="margin-top:20px;">
            <table class="fmt-table">
                <thead>
                    <tr><th>Format</th><th>What it proves</th><th>Value shown</th><th>Compulsory fields</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Payment Receipt</strong></td>
                        <td>Money has been received against an invoice</td>
                        <td>Amount received, in words</td>
                        <td><span class="fmt-req yes">Receipt no.</span> <span class="fmt-req yes">Amount in words</span> <span class="fmt-req cond">UTR for UPI / NEFT</span></td>
                    </tr>
                    <tr>
                        <td><strong>Goods Receipt Note (GRN)</strong></td>
                        <td>Material physically received and its condition</td>
                        <td>Quantity only &mdash; no money</td>
                        <td><span class="fmt-req yes">GRN no.</span> <span class="fmt-req yes">Supplier</span> <span class="fmt-req yes">Qty received</span> <span class="fmt-req cond">Gate entry no.</span></td>
                    </tr>
                    <tr>
                        <td><strong>Delivery Receipt</strong></td>
                        <td>Goods reached the buyer's gate and who accepted them</td>
                        <td>Declared value of the consignment</td>
                        <td><span class="fmt-req yes">Challan no.</span> <span class="fmt-req yes">Item list</span> <span class="fmt-req yes">Receiver signature</span></td>
                    </tr>
                    <tr>
                        <td><strong>Money / Custom Receipt</strong></td>
                        <td>A deposit or part payment against an old balance</td>
                        <td>Amount received + balance remaining</td>
                        <td><span class="fmt-req yes">Balance shown</span> <span class="fmt-req yes">Invoice referred</span> <span class="fmt-req cond">Payment mode</span></td>
                    </tr>
                    <tr>
                        <td><strong>Advance Receipt</strong></td>
                        <td>Money taken before the invoice is raised</td>
                        <td>Advance received, adjustable later</td>
                        <td><span class="fmt-req yes">Order / PO ref.</span> <span class="fmt-req yes">Advance amount</span> <span class="fmt-req cond">Adjustment note</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="note-box gold" style="margin-top:20px;">
            <div class="note-ico">&#128203;</div>
            <div>
                <h4>Receipt mistakes that cause problems</h4>
                <ul>
                    <li>No amount in words &mdash; on a cheque or a money receipt this is treated as incomplete, because the words rule out any difference with the figures.</li>
                    <li>Cheque receipts without the instrument number and bank &mdash; if the cheque bounces, you have no way to identify the drawer.</li>
                    <li>Writing "cheque receipt" before the cheque actually clears &mdash; book the money only when the instrument is realised.</li>
                    <li>A GRN with a value on it &mdash; the value belongs to the supplier's bill, not the goods note.</li>
                    <li>Serial numbers that repeat or skip &mdash; keep the receipt series continuous, just like invoices.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===================== HOW TO ===================== -->
<section class="gst-faq" style="background:#fff;">
    <div class="gst-wrap">
        <h2>How to Create a Receipt in the Right Format</h2>
        <div class="gst-how-grid">
            <div class="gst-how-card">
                <div class="gst-how-num">1</div>
                <h3>Pick the Format</h3>
                <p>Choose payment, GRN, delivery, money or advance based on what actually happened. The form reshapes itself instantly.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">2</div>
                <h3>Fill the Core Details</h3>
                <p>Enter the serial number, date, your business details and the payer's full name, phone and address.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">3</div>
                <h3>Add Amount &amp; Reference</h3>
                <p>Enter the amount, the payment mode, the transaction or UTR reference and the invoice number it settles.</p>
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
        <h2>What is a Receipt Format?</h2>
        <p>A <strong>receipt format</strong> is the fixed layout of a document that proves money or goods have actually changed hands. Unlike an invoice, which is a <em>request</em> for payment, a receipt is issued only after the payment is realised &mdash; and it is the first document your accountant, your bank or your auditor will ask for when the books have to be reconciled. A proper payment receipt carries the issuer's name and address, a unique serial number in a continuous series, the date of receipt, the payer's full name, the exact amount in figures <em>and</em> in words, the mode of payment, the transaction or UTR reference that traces the money, and the invoice number the payment is being adjusted against. Missing any one of these makes the entry hard to match, and a bank reconciliation that cannot be matched is an audit finding waiting to happen.</p>
        <p>Different situations call for different receipt formats, and the difference is about what the document has to prove. A <strong>goods receipt note (GRN)</strong> records the quantity and condition of material received and carries no value, because the supplier's bill will do that. A <strong>delivery receipt</strong> proves goods physically reached the buyer's premises and needs the signature of the person who accepted them. A <strong>money receipt</strong> is used for deposits, part payments and old outstanding balances, so it must show the balance still pending. An <strong>advance receipt</strong> records money taken before the invoice exists and is adjusted against the final bill later. The format also changes with the payment mode: a cheque or demand draft must show the instrument number, the bank and the date, and it must carry a printed note about dishonour &mdash; because until the cheque clears, <strong>Section 138 of the Negotiable Instruments Act, 1881</strong> can be invoked against you.</p>

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
        <h2>Stop Writing Receipts by Hand</h2>
        <p><?= BRAND_NAME ?> auto-generates receipts against every invoice, numbers them continuously, and keeps your cash, UPI and bank books reconciled for you.</p>
        <a href="#download" class="gst-cta-btn">Download <?= BRAND_NAME ?> Free &rarr;</a>
    </div>
</section>

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="assets/js/doc-utils.js"></script>
<script>
/* ---------- Format definitions ---------- */
const RCPT_FORMATS = {
    payment: {
        label: 'Payment Receipt', tag: 'Money received', tags: ['Standard', 'In words', 'Audit proof'],
        ico: '\u{1F4B8}', accent: '#2563EB',
        desc: 'Issued after a customer pays. Proves the money has reached you and tells the accountant exactly which invoice it settles.',
        fields: 'Receipt number, date, payer name, amount in words, payment mode, reference number, invoice number',
        title: 'PAYMENT RECEIPT', noLbl: 'Receipt No.', dateLbl: 'Receipt Date',
        partyTitle: 'Received From', partyName: 'Payer / Customer Name', partyCap: 'Signature of Payer',
        detailTitle: 'Payment Details', modeLbl: 'Mode', heroLbl: 'Amount Received',
        money: true, words: 'amount', cheque: true, grn: false, items: false, value: false,
        balanceLbl: '', amtTitle: 'Amount Received', amtLbl: 'Amount Received',
        sig1: 'Received by', sig2: 'Witness', showSig3: true, stripLbl: 'Amount Received'
    },
    goods: {
        label: 'Goods Receipt Note (GRN)', tag: 'Quantity, no money', tags: ['GRN', 'Store record', 'Condition'],
        ico: '\u{1F4E6}', accent: '#D97706',
        desc: 'Records what physically arrived from the supplier and in what condition. Carries quantity only \u2014 the value belongs to the supplier\u2019s bill.',
        fields: 'GRN number, date, supplier name, item description, quantity received, condition, gate entry number',
        title: 'GOODS RECEIPT NOTE (GRN)', noLbl: 'GRN No.', dateLbl: 'GRN Date',
        partyTitle: 'Received From (Supplier)', partyName: 'Supplier / Vendor Name', partyCap: 'Supplier Representative',
        detailTitle: 'Goods Details', modeLbl: 'Unit', heroLbl: 'Quantity Received',
        money: false, words: 'qty', cheque: false, grn: true, items: false, value: false,
        balanceLbl: '', amtTitle: 'Goods Received', amtLbl: 'Total Invoice Value (Optional)',
        sig1: 'Supplier Representative', sig2: 'Store Keeper', showSig3: true, stripLbl: 'Quantity Received'
    },
    delivery: {
        label: 'Delivery Receipt', tag: 'Goods handed over', tags: ['Challan link', 'Receiver sign', 'Item list'],
        ico: '\u{1F69A}', accent: '#0D9488',
        desc: 'Proves the goods reached the buyer\u2019s premises. Lists every item delivered and captures the signature of the person who received it.',
        fields: 'Delivery challan number, date, delivered-to name, item list with quantity, receiver signature',
        title: 'DELIVERY RECEIPT', noLbl: 'Delivery Ch. No.', dateLbl: 'Delivery Date',
        partyTitle: 'Delivered To', partyName: 'Received By (Name)', partyCap: 'Receiver Signature',
        detailTitle: 'Dispatch Details', modeLbl: 'Mode', heroLbl: 'Declared Value',
        money: true, words: 'amount', cheque: false, grn: false, items: true, value: false,
        balanceLbl: '', amtTitle: 'Declared Value', amtLbl: 'Declared Value of Goods',
        sig1: 'Transporter / Driver', sig2: 'Received By', showSig3: true, stripLbl: 'Declared Value'
    },
    custom: {
        label: 'Custom / Money Receipt', tag: 'Part payment', tags: ['Deposit', 'Balance shown', 'Old dues'],
        ico: '\u{1F4CB}', accent: '#4F46E5',
        desc: 'For a deposit, part payment or an old outstanding balance. Must show the total value, what was received now, and the balance still pending.',
        fields: 'Money receipt number, date, payer, invoice value, amount received, balance remaining',
        title: 'MONEY RECEIPT', noLbl: 'Money Receipt No.', dateLbl: 'Receipt Date',
        partyTitle: 'Received From', partyName: 'Payer Name', partyCap: 'Signature of Payer',
        detailTitle: 'Adjustment Details', modeLbl: 'Mode', heroLbl: 'Amount Received',
        money: true, words: 'amount', cheque: true, grn: false, items: false, value: true,
        balanceLbl: 'Balance Remaining on Invoice', amtTitle: 'Amount Received', amtLbl: 'Amount Received',
        sig1: 'Received by', sig2: 'Witness', showSig3: true, stripLbl: 'Amount Received'
    },
    advance: {
        label: 'Advance Receipt', tag: 'Paid before billing', tags: ['Advance', 'PO linked', 'Adjusted later'],
        ico: '\u{1F4C4}', accent: '#7C3AED',
        desc: 'Records money taken before the invoice is raised, usually against a confirmed order. Later adjusted against the final bill.',
        fields: 'Advance receipt number, date, payer, order or PO reference, advance amount, adjustment note',
        title: 'ADVANCE RECEIPT', noLbl: 'Advance Receipt No.', dateLbl: 'Receipt Date',
        partyTitle: 'Received From', partyName: 'Payer / Customer Name', partyCap: 'Signature of Payer',
        detailTitle: 'Advance Details', modeLbl: 'Mode', heroLbl: 'Advance Received',
        money: true, words: 'amount', cheque: true, grn: false, items: false, value: true,
        balanceLbl: 'Balance Payable After Adjustment', amtTitle: 'Advance Received', amtLbl: 'Advance Received',
        sig1: 'Received by', sig2: 'Witness', showSig3: true, stripLbl: 'Advance Received'
    }
};

let rcptFmt = 'payment';
let rcptRowId = 0;

/* ---------- Format picker ---------- */
function rcptRenderFormats() {
    const grid = document.getElementById('fmtGrid');
    let h = '';
    Object.keys(RCPT_FORMATS).forEach(k => {
        const f = RCPT_FORMATS[k];
        h += `<button type="button" class="fmt-card${k === rcptFmt ? ' active' : ''}" data-rcpt-fmt="${k}" aria-pressed="${k === rcptFmt}">
            <span class="fmt-mini" style="--fmt-accent:${f.accent}">
                <span class="fmt-mini-top"><span class="fmt-mini-brand"></span><span class="fmt-mini-tag"></span></span>
                <span class="fmt-mini-ln w70"></span><span class="fmt-mini-ln w45"></span><span class="fmt-mini-ln w70"></span>
                <span class="fmt-mini-tot"></span>
            </span>
            <span class="fmt-card-body">
                <span class="fmt-card-title">${f.ico} ${f.label}</span>
                <span class="fmt-card-sub">${f.tag}</span>
                <span class="fmt-tags">${f.tags.map(t => `<span class="fmt-tag${k === 'goods' ? ' warn' : k === 'advance' ? '' : k === 'custom' ? ' neutral' : ''}">${t}</span>`).join('')}</span>
            </span>
        </button>`;
    });
    grid.innerHTML = h;
    grid.querySelectorAll('[data-rcpt-fmt]').forEach(b => b.addEventListener('click', () => rcptSetFormat(b.dataset.rcptFmt)));
    rcptSetFormat(rcptFmt, true);
}

function rcptSetFormat(k, quiet) {
    const f = RCPT_FORMATS[k];
    if (!f) return;
    rcptFmt = k;

    document.querySelectorAll('[data-rcpt-fmt]').forEach(b => {
        const on = b.dataset.rcptFmt === k;
        b.classList.toggle('active', on);
        b.setAttribute('aria-pressed', on);
    });

    document.getElementById('fmtInfoIco').textContent = f.ico;
    document.getElementById('fmtInfoTitle').textContent = f.label;
    document.getElementById('fmtInfoDesc').textContent = f.desc;
    document.getElementById('fmtInfoFields').textContent = f.fields;

    document.getElementById('rNoLbl').textContent = f.noLbl;
    document.getElementById('rDateLbl').textContent = f.dateLbl;
    document.getElementById('pBlockTitle').textContent = f.partyTitle;
    document.getElementById('pBlockSub').textContent = 'Full name, contact and address of the ' + (f.grn ? 'supplier' : f.items ? 'receiver' : 'payer') + '.';
    document.getElementById('pNameLbl').textContent = f.partyName;
    document.getElementById('amtTitle').textContent = f.amtTitle;
    document.getElementById('rAmtLbl').textContent = f.amtLbl + ' (\u20B9)';
    document.getElementById('rInvLbl').textContent = f.grn ? 'Supplier Bill / Invoice No.' : f.advance ? 'Order / PO Reference' : 'Against Invoice No.';

    const show = (id, on) => { const e = document.getElementById(id); if (e) e.style.display = on ? '' : 'none'; };
    show('grnRow', f.grn);
    show('grnRow2', f.grn);
    show('grnRow3', f.grn);
    show('valRow', f.value);

    if (!quiet) rcptUpdate();
}

/* ---------- Delivered-item rows ---------- */
function rcptAddRow(seed) {
    seed = seed || {};
    const id = rcptRowId++;
    document.getElementById('delItems').insertAdjacentHTML('beforeend',
        `<div class="doc-item" data-row="${id}">
            <input type="text" data-dl-desc placeholder="Item description" oninput="rcptUpdate()">
            <input type="number" data-dl-qty value="${seed.qty || 1}" min="0" step="0.01" oninput="rcptUpdate()">
            <select data-dl-unit onchange="rcptUpdate()"><option>NOS</option><option selected>PCS</option><option>KGS</option><option>BOX</option><option>SET</option><option>MTR</option><option>TON</option></select>
            <button type="button" class="doc-item-del" onclick="rcptDelRow(${id})" title="Remove item" aria-label="Remove item">&times;</button>
        </div>`);
    rcptUpdate();
}

function rcptDelRow(id) {
    const e = document.querySelector(`[data-row="${id}"]`);
    if (e) e.remove();
    rcptUpdate();
}

function rcptCollect() {
    const f = RCPT_FORMATS[rcptFmt];
    const mode = DOC.val('rMode') || 'Cash';
    const isChq = mode === 'Cheque' || mode === 'Cheque / Demand Draft';
    const amount = DOC.round2(DOC.num('rAmt'));
    const gQty = DOC.round2(DOC.num('gQty'));
    const unit = DOC.val('gUnit') || 'PCS';
    const invVal = DOC.round2(DOC.num('rInvVal'));

    const items = [];
    document.querySelectorAll('#delItems .doc-item').forEach((el, i) => {
        const g = (s) => el.querySelector(s);
        const desc = (g('[data-dl-desc]').value || 'Item').trim();
        const q = parseFloat(g('[data-dl-qty]').value) || 0;
        const u = g('[data-dl-unit]').value || 'PCS';
        items.push({ i: i + 1, desc, q, u, total: DOC.round2(q) });
    });
    const itemCount = items.length;
    const itemQty = DOC.round2(items.reduce((s, r) => s + r.q, 0));

    const heroVal = f.money ? DOC.money(amount) : DOC.qty(gQty) + ' ' + unit;
    const balance = f.value ? DOC.round2(Math.max(0, invVal - amount)) : 0;
    const words = f.words === 'qty' ? DOC.numToWords(gQty) + ' ' + unit + ' only' : DOC.inWords(amount);

    return { f, mode, isChq, amount, gQty, unit, invVal, items, itemCount, itemQty, heroVal, balance, words };
}

/* ---------- Preview render ---------- */
function rcptUpdate() {
    const d = rcptCollect();
    const f = d.f;

    DOC.text('pOrg', DOC.val('rName'));
    DOC.text('pOrgAddr', DOC.val('rAddr'));
    DOC.text('pOrgPhone', 'Phone: ' + (DOC.val('rPhone') || '\u2014'));
    DOC.showIf('pGstRow', !!DOC.val('rGst'));
    if (DOC.val('rGst')) document.getElementById('pGst').textContent = DOC.val('rGst');

    document.getElementById('pTitle').textContent = f.title;
    document.getElementById('pNoLbl').textContent = f.noLbl + ':';
    document.getElementById('pDateLbl').textContent = f.dateLbl + ':';
    DOC.text('pNo', DOC.val('rNo'));
    DOC.text('pDate', DOC.fmtDate(DOC.val('rDate')));

    document.getElementById('pPartyTitle').textContent = f.partyTitle;
    DOC.text('pPartyName', DOC.val('pName'));
    DOC.text('pPartyPhone', 'Phone: ' + (DOC.val('pPhone') || '\u2014'));
    DOC.text('pPartyAddr', DOC.val('pAddr'));
    document.getElementById('pDetailTitle').textContent = f.detailTitle;
    document.getElementById('pModeLbl').textContent = (f.grn ? 'Material' : f.modeLbl) + ':';
    if (f.grn) {
        document.getElementById('pMode').textContent = DOC.val('gItem') || '\u2014';
    } else {
        document.getElementById('pMode').textContent = d.mode;
    }
    DOC.showIf('pInvRow', !f.grn);
    if (!f.grn) document.getElementById('pInv').textContent = DOC.val('rInv') || '\u2014';
    DOC.showIf('pRefRow', !!DOC.val('rRef'));
    if (DOC.val('rRef')) document.getElementById('pRef').textContent = DOC.val('rRef');
    DOC.showIf('pUtrRow', !f.grn && !!DOC.val('rUtr'));
    if (!f.grn && DOC.val('rUtr')) document.getElementById('pUtr').textContent = DOC.val('rUtr');
    DOC.showIf('pChqRow', f.cheque && d.isChq);
    if (f.cheque && d.isChq) {
        const bits = [];
        if (DOC.val('rChq')) bits.push('Cheque/DD No. ' + DOC.val('rChq'));
        if (DOC.val('rBank')) bits.push(DOC.val('rBank'));
        document.getElementById('pChq').textContent = bits.length ? bits.join(' \u00B7 ') : '\u2014';
    }
    DOC.showIf('pGateRow', f.grn && !!DOC.val('gGate'));
    if (f.grn && DOC.val('gGate')) document.getElementById('pGate').textContent = DOC.val('gGate');
    DOC.showIf('pPoRow', f.grn && !!DOC.val('gPo'));
    if (f.grn && DOC.val('gPo')) document.getElementById('pPo').textContent = DOC.val('gPo');
    DOC.showIf('pCondRow', f.grn && !!DOC.val('gCond'));
    if (f.grn) document.getElementById('pCond').textContent = DOC.val('gCond');

    document.getElementById('pHeroLbl').textContent = f.heroLbl;
    document.getElementById('pHeroVal').textContent = d.heroVal;
    document.getElementById('pHero').classList.toggle('amber', f.grn);

    DOC.showIf('pBalRow', !!f.balanceLbl && f.value);
    if (f.balanceLbl && f.value) {
        document.getElementById('pBalLbl').textContent = f.balanceLbl + (DOC.val('rInv') ? ' (' + DOC.val('rInv') + ')' : '');
        document.getElementById('pBal').textContent = DOC.money(d.balance);
    }

    const words = document.getElementById('optWords').checked;
    DOC.showIf('pWordsRow', words);
    if (words) {
        document.getElementById('pWords').textContent = d.words;
    }

    DOC.showIf('pDelWrap', f.items);
    if (f.items) {
        const head = document.getElementById('pDelHead');
        if (head.children.length !== 4) {
            head.innerHTML = '<th style="width:26px;">#</th><th>Item / Description</th><th class="num">Qty</th><th class="num">Unit</th>';
        }
        document.getElementById('pDelRows').innerHTML = d.items.length ? d.items.map(r =>
            `<tr><td>${r.i}</td><td>${r.desc}</td><td class="num">${DOC.qty(r.q)}</td><td class="num">${r.u}</td></tr>`
        ).join('') : '<tr><td colspan="4" style="padding:10px;text-align:center;color:#94A3B8;">No items added</td></tr>';
    }

    DOC.showIf('pBounce', document.getElementById('optBounce').checked && f.cheque && d.isChq);

    const notes = DOC.val('rNotes');
    DOC.showIf('pNotesRow', !!notes);
    if (notes) document.getElementById('pNotes').textContent = notes;

    DOC.showIf('pSig', document.getElementById('optSig').checked);
    document.getElementById('pSig1').textContent = f.sig1;
    document.getElementById('pSig2').textContent = f.sig2;
    document.getElementById('pSig3').textContent = 'For ' + (DOC.val('rName') || '\u2014');
    const caps = document.querySelectorAll('#pSig > div > div:last-child');
    if (caps[0]) caps[0].textContent = f.partyCap;

    document.getElementById('pStripLbl').textContent = f.stripLbl;
    document.getElementById('pStripAmt').textContent = d.heroVal;
}

/* ---------- Actions ---------- */
function rcptPreviewJump() { rcptUpdate(); DOC.scrollToPreview('rcptPreview'); }
function rcptPrint() { rcptUpdate(); window.print(); }

function rcptReset() {
    ['rName', 'rAddr', 'rPhone', 'rGst', 'rNo', 'rInv', 'rRef', 'rUtr', 'rBank', 'rChq',
        'pName', 'pPhone', 'pAddr', 'rNotes', 'gItem', 'gGate', 'gPo'].forEach(id => {
            const e = document.getElementById(id); if (e) e.value = '';
        });
    document.getElementById('rAmt').value = 0;
    document.getElementById('rInvVal').value = 0;
    document.getElementById('gQty').value = 0;
    document.getElementById('rDate').value = DOC.todayISO();
    document.getElementById('rMode').selectedIndex = 0;
    document.getElementById('rPart').value = 'no';
    document.getElementById('gCond').selectedIndex = 0;
    document.getElementById('gUnit').selectedIndex = 1;
    document.getElementById('delItems').querySelectorAll('.doc-item').forEach(e => e.remove());
    rcptAddRow();
    DOC.setToggle('optWords', true);
    DOC.setToggle('optSig', true);
    DOC.setToggle('optBounce', true);
    rcptSetFormat('payment');
    rcptUpdate();
}

function rcptPDF() {
    rcptUpdate();
    const d = rcptCollect();
    const f = d.f;
    const doc = DOC.newDoc();
    if (!doc) return;

    const W = doc.internal.pageSize.getWidth();
    const H = doc.internal.pageSize.getHeight();
    const MG = 14;
    const accent = DOC.currentAccentRGB();

    let y = DOC.pdfHeader(doc, {
        org: DOC.val('rName') || 'Your Business',
        left: [
            { value: DOC.val('rAddr') || 'Address' },
            { value: 'Phone: ' + (DOC.val('rPhone') || '\u2014') },
            { value: DOC.val('rGst') ? 'GSTIN: ' + DOC.val('rGst') : '' }
        ],
        title: f.title,
        right: [
            { label: f.noLbl, value: DOC.val('rNo') || '\u2014' },
            { label: f.dateLbl, value: DOC.fmtDate(DOC.val('rDate')) || '\u2014' }
        ],
        accent: accent
    });

    /* party + details */
    y = DOC.pdfSection(doc, MG, y, f.partyTitle.toUpperCase(), 82);
    doc.setFont('helvetica', 'bold'); doc.setFontSize(9); doc.setTextColor(15, 23, 42);
    doc.text(DOC.val('pName') || 'Party', MG, y + 4.5); y += 8;
    doc.setFont('helvetica', 'normal'); doc.setFontSize(8);
    (function () {
        const lines = doc.splitTextToSize(DOC.val('pAddr') || '', 82);
        doc.text(lines, MG, y); y += lines.length * 3.6;
    })();
    doc.text('Phone: ' + (DOC.val('pPhone') || '\u2014'), MG, y); y += 6;

    y = DOC.pdfSection(doc, MG + 92, y - 5, f.detailTitle.toUpperCase(), W - MG * 2 - 92);
    doc.setFontSize(8);
    const facts = [];
    facts.push(f.grn ? 'Item / Material: ' + (DOC.val('gItem') || '\u2014') : f.modeLbl + ': ' + d.mode);
    if (!f.grn) facts.push('Against Inv.: ' + (DOC.val('rInv') || '\u2014'));
    if (DOC.val('rRef')) facts.push('Ref / Txn: ' + DOC.val('rRef'));
    if (!f.grn && DOC.val('rUtr')) facts.push('UTR: ' + DOC.val('rUtr'));
    if (f.cheque && d.isChq) {
        const cb = [];
        if (DOC.val('rChq')) cb.push('Cheque/DD No. ' + DOC.val('rChq'));
        if (DOC.val('rBank')) cb.push(DOC.val('rBank'));
        facts.push('Instrument: ' + (cb.length ? cb.join('  \u00B7  ') : '\u2014'));
    }
    if (f.grn && DOC.val('gGate')) facts.push('Gate Entry: ' + DOC.val('gGate'));
    if (f.grn && DOC.val('gPo')) facts.push('PO / Ref: ' + DOC.val('gPo'));
    if (f.grn && DOC.val('gCond')) facts.push('Condition: ' + DOC.val('gCond'));
    facts.forEach(function (t) { doc.text(t, MG + 92, y); y += 4.2; });
    y += 4;

    /* amount panel */
    const grnCol = f.grn ? [180, 83, 9] : [5, 150, 105];
    doc.setFillColor(grnCol[0], grnCol[1], grnCol[2]);
    doc.setDrawColor(grnCol[0], grnCol[1], grnCol[2]);
    doc.setLineWidth(0.6);
    doc.roundedRect(MG, y, W - MG * 2, 26, 3, 3, 'FD');
    doc.setTextColor(255, 255, 255);
    doc.setFont('helvetica', 'bold'); doc.setFontSize(9);
    doc.text(f.heroLbl.toUpperCase(), W / 2, y + 9, { align: 'center' });
    doc.setFontSize(19);
    doc.text(d.heroVal, W / 2, y + 20, { align: 'center' });
    y += 33;

    if (f.value) {
        doc.setFontSize(8.5); doc.setTextColor(51, 65, 85);
        doc.text('Invoice / Order Value: ' + DOC.money(d.invVal), MG, y);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(grnCol[0], grnCol[1], grnCol[2]);
        doc.text(f.balanceLbl + ': ' + DOC.money(d.balance), W - MG, y, { align: 'right' });
        doc.setFont('helvetica', 'normal'); doc.setTextColor(51, 65, 85);
        y += 7;
    }

    if (document.getElementById('optWords').checked) {
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text((f.words === 'qty' ? 'Quantity in words: ' : 'Amount in words: ') + d.words, MG, y);
        doc.setFont('helvetica', 'normal');
        y += 7;
    }

    if (f.items) {
        if (y > H - 60) { doc.addPage(); y = 18; }
        y = DOC.pdfSection(doc, MG, y, 'ITEMS DELIVERED', 70) + 1;
        y = DOC.pdfTable(doc, ['#', 'Item / Description', 'Qty', 'Unit'],
            d.items.length ? d.items.map(r => [r.i, r.desc, DOC.qty(r.q), r.u]) : [['', 'No items', '', '']],
            { startY: y, accent: accent, extra: { columnStyles: { 0: { cellWidth: 8 }, 2: { halign: 'right' }, 3: { halign: 'right' } } } }) + 5;
    }

    if (document.getElementById('optBounce').checked && f.cheque && d.isChq) {
        if (y > H - 40) { doc.addPage(); y = 18; }
        doc.setFillColor(254, 242, 242);
        doc.setDrawColor(254, 202, 202);
        doc.setLineWidth(0.3);
        doc.roundedRect(MG, y, W - MG * 2, 17, 2, 2, 'FD');
        doc.setFontSize(7.2); doc.setTextColor(153, 27, 27);
        const bl = doc.splitTextToSize('Cheques dishonoured will be charged \u20B9500/- per instance plus bank charges, and the drawer is liable to prosecution under Section 138 of the Negotiable Instruments Act, 1881. This receipt is valid only on realisation.', W - MG * 2 - 8);
        doc.text(bl, MG + 4, y + 5.5);
        y += 21;
    }

    if (DOC.val('rNotes')) {
        if (y > H - 40) { doc.addPage(); y = 18; }
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Notes:', MG, y); y += 4;
        doc.setFont('helvetica', 'normal');
        const nl = doc.splitTextToSize(DOC.val('rNotes'), W - MG * 2);
        doc.text(nl, MG, y); y += nl.length * 3.6 + 3;
    }

    if (document.getElementById('optSig').checked) {
        if (y > H - 34) { doc.addPage(); y = 20; }
        const colW = (W - MG * 2) / 3;
        const sy = y + 13;
        doc.setDrawColor(148, 163, 184); doc.setLineWidth(0.3);
        [0, 1, 2].forEach(function (i) {
            const x = MG + colW * i + colW / 2;
            doc.line(x - 30, sy, x + 30, sy);
        });
        doc.setFontSize(7.5); doc.setTextColor(100, 116, 139);
        doc.text(f.sig1, MG + colW / 2, sy + 4, { align: 'center' });
        doc.text(f.sig2, MG + colW * 1.5, sy + 4, { align: 'center' });
        doc.setFont('helvetica', 'bold');
        doc.text('For ' + (DOC.val('rName') || 'the issuer'), MG + colW * 2.5, sy + 4, { align: 'center' });
        doc.setFont('helvetica', 'normal');
        y = sy + 9;
    }

    DOC.pdfFooter(doc, y, (DOC.val('rNo') || 'receipt') + ' \u00B7 ' + f.title);
    doc.save(((DOC.val('rNo') || 'receipt').replace(/[^\w\-]+/g, '_')) + '.pdf');
}

/* ---------- Boot ---------- */
(function () {
    document.getElementById('rDate').value = DOC.todayISO();
    rcptRenderFormats();
    rcptAddRow();

    const ids = ['rName', 'rAddr', 'rPhone', 'rGst', 'rNo', 'rDate', 'rMode', 'rInv', 'rRef', 'rUtr',
        'rBank', 'rChq', 'pName', 'pPhone', 'pAddr', 'gItem', 'gQty', 'gUnit', 'gCond', 'gGate', 'gPo',
        'rInvVal', 'rPart', 'rAmt', 'rNotes', 'optWords', 'optSig', 'optBounce'];
    DOC.bind(ids, rcptUpdate);

    DOC.bindSwatches('data-rcpt-accent', rcptUpdate);

    rcptUpdate();
})();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
