<?php
/**
 * My Metaji - Estimate Format
 * Lets the user pick a cost-estimate format (Itemised / Summary / Ballpark / Comparative /
 * Replacement), fill it in, preview it live and download it as a PDF.
 */
require_once __DIR__ . '/config.php';
$page_title = 'Free Estimate Format — Cost Estimate Templates Online | ' . BRAND_NAME;
$page_desc  = 'Create a free cost estimate before you commit. Itemised, summary, ballpark, comparative or repair estimate with assumptions, ±10% tolerance and instant PDF download.';
include __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="gst-hero">
    <div class="gst-wrap">
        <div class="gst-hero-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 2"/><circle cx="12" cy="12" r="9"/></svg>
            Approximate, not binding &middot; Free to download
        </div>
        <h1>Free <span>Estimate Format</span></h1>
        <p>An estimate is a rough costing you show <em>before</em> committing &mdash; it is deliberately not a bill. Build one with assumptions, exclusions and a clear tolerance band so nobody is surprised later.</p>
        <div class="gst-hero-chips">
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                5 estimate formats
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 12h16M4 8h16M4 16h10"/></svg>
                Low&ndash;High range option
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 3v18M7 7h7a3 3 0 0 1 0 6H7"/></svg>
                Assumptions &amp; exclusions
            </span>
        </div>
    </div>
</section>

<!-- ===================== TOOL ===================== -->
<section class="gst-tool" id="estimate-format-tool">
    <div class="gst-wrap">
        <div class="qm-layout">

            <!-- ===== LEFT: FORM ===== -->
            <div>

                <!-- 1. FORMAT PICKER -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5M4 19h16"/><path d="M8 15l4-6 3 3 5-7"/></svg>
                        </div>
                        <div>
                            <h3>Choose Your Estimate Format</h3>
                            <p>Each format has different columns &mdash; pick the one that matches the question the client is asking.</p>
                        </div>
                        <span class="qm-step">Step 1</span>
                    </div>

                    <div class="fmt-grid" id="fmtGrid" role="group" aria-label="Estimate format"></div>

                    <div class="note-box gold" style="margin-top:16px;">
                        <div class="note-ico" id="fmtInfoIco">&#128202;</div>
                        <div>
                            <h4 id="fmtInfoTitle">Itemised Cost Estimate</h4>
                            <p id="fmtInfoDesc">A rough costing broken down line by line with a contingency percentage and a GST breakup.</p>
                            <p style="margin:0;"><strong>Must contain:</strong> <span id="fmtInfoFields">Estimate number, name of work, client details, quantity, rate, assumptions and exclusions</span></p>
                        </div>
                    </div>

                    <div class="doc-split" style="margin-top:18px;">
                        <div>
                            <div class="doc-opt">
                                <span>Accent colour</span>
                                <div class="doc-swatches">
                                    <button type="button" class="doc-swatch active" data-est-accent="blue" style="background:#2563EB" aria-label="Blue"></button>
                                    <button type="button" class="doc-swatch" data-est-accent="teal" style="background:#0D9488" aria-label="Teal"></button>
                                    <button type="button" class="doc-swatch" data-est-accent="violet" style="background:#7C3AED" aria-label="Violet"></button>
                                    <button type="button" class="doc-swatch" data-est-accent="amber" style="background:#D97706" aria-label="Amber"></button>
                                    <button type="button" class="doc-swatch" data-est-accent="red" style="background:#DC2626" aria-label="Red"></button>
                                    <button type="button" class="doc-swatch" data-est-accent="slate" style="background:#334155" aria-label="Slate"></button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="doc-switches">
                                <label class="doc-chk"><input type="checkbox" id="optDisclaim" checked> "Not a bill" notice</label>
                                <label class="doc-chk"><input type="checkbox" id="optTol" checked> ±10% tolerance</label>
                                <label class="doc-chk"><input type="checkbox" id="optWords" checked> Amount in words</label>
                                <label class="doc-chk"><input type="checkbox" id="optSignature" checked> Signature block</label>
                                <label class="doc-chk"><input type="checkbox" id="optBank"> Bank details</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. VENDOR -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14"/><path d="M10 12h4M10 16h4"/></svg>
                        </div>
                        <div>
                            <h3>Your Details (Estimator)</h3>
                            <p>An estimate without a named estimator carries no weight with a client.</p>
                        </div>
                        <span class="qm-step">Step 2</span>
                    </div>
                    <div class="doc-split">
                        <div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Business / Estimator Name</label><input type="text" id="sName" placeholder="As registered with GST"></div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>GSTIN</label><input type="text" id="sGst" placeholder="24AAAAA0000A1Z5" maxlength="15"></div>
                        </div>
                        <div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Phone</label><input type="tel" id="sPhone" placeholder="98765 43210"></div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>State</label><select id="sState"></select></div>
                        </div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Address</label><input type="text" id="sAddr" placeholder="Street, city, state, PIN"></div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>State Code</label><input type="text" id="sStateCode" placeholder="24" maxlength="2"></div>
                        <div class="inv-gen-field"><label>Contact Person</label><input type="text" id="sContact" placeholder="Who signs the estimate"></div>
                    </div>
                </div>

                <!-- 3. CLIENT -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
                        </div>
                        <div>
                            <h3>Client &amp; Site</h3>
                            <p>Who the estimate is for and where the work will be done.</p>
                        </div>
                        <span class="qm-step">Step 3</span>
                    </div>
                    <div class="doc-split">
                        <div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Client Name</label><input type="text" id="cName" placeholder="Prospect or customer name"></div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Client GSTIN</label><input type="text" id="cGst" placeholder="27BBBBB0000B1Z5" maxlength="15"></div>
                        </div>
                        <div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>Phone</label><input type="tel" id="cPhone" placeholder="98765 43210"></div>
                            <div class="inv-gen-field" style="margin-bottom:10px;"><label>State</label><select id="cState"></select></div>
                        </div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Client / Site Address</label><input type="text" id="cAddr" placeholder="Street, city, state, PIN"></div>
                    <div class="inv-gen-field"><label>Place of Supply</label><input type="text" id="cPos" placeholder="e.g. Gujarat (24)"></div>
                </div>

                <!-- 4. ESTIMATE META + ITEMS -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>
                        </div>
                        <div>
                            <h3>Estimate Number, Work &amp; Items</h3>
                            <p>The name of the work is the single most important line on the whole document.</p>
                        </div>
                        <span class="qm-step">Step 4</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label>Estimate Number</label><input type="text" id="estNo" placeholder="EST-2026-001"></div>
                        <div class="inv-gen-field"><label>Estimate Date</label><input type="date" id="estDate"></div>
                    </div>
                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label>Estimate For (Name of Work)</label><input type="text" id="estFor" placeholder="e.g. 2 BHK interior fit-out, Phase 1"></div>
                        <div class="inv-gen-field"><label>Valid Until</label><input type="date" id="estValid"></div>
                    </div>
                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label>Enquiry / Reference</label><input type="text" id="estRef" placeholder="ENQ-2026-118 &middot; optional"></div>
                        <div class="inv-gen-field"><label>Site Measured On</label><input type="date" id="estSurvey"></div>
                    </div>

                    <div class="doc-items" id="estItems" style="--doc-cols: 2.4fr .6fr .8fr 1fr 1fr auto">
                        <div class="doc-item-hd" id="eItemHd"></div>
                    </div>
                    <button type="button" class="inv-gen-add-item" onclick="estAddRow()">+ Add Line</button>

                    <div class="doc-split" style="margin-top:16px;">
                        <div class="inv-gen-field"><label>Contingency (%)</label><input type="number" id="estCont" value="5" min="0" max="100" step="0.5" oninput="estUpdate()"></div>
                        <div class="inv-gen-field"><label>GST Rate</label><select id="estGst" onchange="estUpdate()"></select></div>
                    </div>
                </div>

                <!-- 5. VARIANT-SPECIFIC -->
                <div class="qm-card" id="apRow" style="display:none;">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 18V9M10 18V5M16 18v-7M22 18H2"/></svg>
                        </div>
                        <div>
                            <h3>Basis of the Ballpark Figure</h3>
                            <p>Say out loud how you arrived at the number. A range without a basis is a guess.</p>
                        </div>
                        <span class="qm-step">Step 5</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Rate Basis</label>
                            <select id="apBasis">
                                <option>Per square foot</option>
                                <option>Per running foot</option>
                                <option>Per running metre</option>
                                <option>Per unit / per system</option>
                                <option>Per man-day</option>
                                <option>Percentage of project cost</option>
                            </select>
                        </div>
                        <div class="inv-gen-field"><label>Market / Cluster Used</label><input type="text" id="apMarket" placeholder="e.g. Ahmedabad, mid-segment"></div>
                    </div>
                    <div class="inv-gen-field"><label>Range Note</label><textarea id="apNote" rows="2" placeholder="Low end assumes standard brands and no structural changes. High end covers premium finishes and rework of existing services."></textarea></div>
                </div>

                <div class="qm-card" id="cpRow" style="display:none;">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="M7 7h7a3 3 0 0 1 0 6H7"/><path d="M5 13H3l4 8 4-8H9"/></svg>
                        </div>
                        <div>
                            <h3>Second Quote / Previous Estimate</h3>
                            <p>Put both numbers on one page. It is the fastest way to justify your own price.</p>
                        </div>
                        <span class="qm-step">Step 5</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Compared With (Vendor / Earlier Quote)</label><input type="text" id="cpVendor" placeholder="e.g. ABC Interiors, or our 2025 quote"></div>
                        <div class="inv-gen-field"><label>Their Reference No.</label><input type="text" id="cpRef" placeholder="QTN/2025/118"></div>
                    </div>
                    <div class="inv-gen-field"><label>Why You Are Competitive</label><textarea id="cpNote" rows="2" placeholder="Includes 2-year workmanship warranty, branded material with 1-year manufacturer cover, and free site supervision &mdash; the other quote excludes these."></textarea></div>
                </div>

                <div class="qm-card" id="rpRow" style="display:none;">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 1-5.4 5.4L4 17v3h3l5.3-5.3a4 4 0 0 1 5.4-5.4z"/></svg>
                        </div>
                        <div>
                            <h3>Asset &amp; Warranty Details</h3>
                            <p>Repair estimates live or die on what is and is not under warranty.</p>
                        </div>
                        <span class="qm-step">Step 5</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Asset / Appliance</label><input type="text" id="rpAsset" placeholder="e.g. 1.5T Voltas AC, 3 yrs old"></div>
                        <div class="inv-gen-field"><label>Warranty Status</label>
                            <select id="rpWarranty">
                                <option>Out of warranty</option>
                                <option>Under manufacturer warranty</option>
                                <option>Under extended warranty</option>
                                <option>Partially under warranty</option>
                            </select>
                        </div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Diagnosis / Current Fault</label><textarea id="rpFault" rows="2" placeholder="AC not cooling since 4 days. Compressor drawing 14A, suspected capacitor failure. No refrigerant leak observed."></textarea></div>
                    <div class="inv-gen-field"><label>Old Part Handling</label><textarea id="rpOld" rows="2" placeholder="Old part returned to you on completion. Parts warranty 6 months / 30 days whichever is earlier. Visit charges are not refundable if the fault is not rectified."></textarea></div>
                </div>

                <!-- 6. ASSUMPTIONS + NOTES -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>
                        </div>
                        <div>
                            <h3>Assumptions, Exclusions &amp; Notes</h3>
                            <p>The most important section on the whole page. Assumptions protect you; exclusions prevent disputes.</p>
                        </div>
                        <span class="qm-step">Step 6</span>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Assumptions &amp; Exclusions</label><textarea id="estAssump" rows="5" placeholder="Assumed: existing walls are plumb and load-bearing; electrical point count as per plan; water and power available at site.&#10;Excluded: civil repair, waterproofing, electrical rewiring, society charges, statutory approvals, furniture of any kind."></textarea></div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Notes / Validity</label><textarea id="estNotes" rows="3" placeholder="Estimate valid for 15 days from the date above. Final price is confirmed after detailed measurement. Payment 50% advance, 50% on completion."></textarea></div>
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
                    <button type="button" class="qm-btn qm-btn-primary" onclick="estPreviewJump()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        Preview
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="estPrint()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="1"/><path d="M6 17h12v4H6z"/></svg>
                        Print
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="estReset()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                        Reset
                    </button>
                    <button type="button" class="qm-btn qm-btn-primary" style="flex:2" onclick="estPDF()">
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
                <div class="inv-gen-preview" id="estPreview">
                    <div class="doc-sheet doc-sheet-lg" id="estSheet">
                        <div class="doc-hd">
                            <div class="doc-org">
                                <h2 id="ePSName">Your Business</h2>
                                <p id="ePSAddr">Address</p>
                                <p><span id="ePSPhone">Phone: &mdash;</span> &middot; <span id="ePSState">State: &mdash;</span></p>
                                <p><strong>GSTIN:</strong> <span id="ePSGst">&mdash;</span> &middot; <span id="ePSCode"></span></p>
                            </div>
                            <div class="doc-id">
                                <div class="doc-title" id="ePTitle">COST ESTIMATE</div>
                                <p><strong id="ePNoLbl">Estimate No.:</strong> <span id="ePNo">&mdash;</span></p>
                                <p><strong>Date:</strong> <span id="ePDate">&mdash;</span></p>
                                <p style="color:var(--fmt-accent,#2563EB);"><strong>Valid Until:</strong> <span id="ePValid">&mdash;</span></p>
                            </div>
                        </div>

                        <div class="note-box gold" id="ePDisclaimer" style="padding:11px 14px;margin-bottom:12px;">
                            <div class="note-ico" style="font-size:16px;">&#9888;</div>
                            <div>
                                <h4 style="font-size:12.5px;">This is an estimate, not a bill.</h4>
                                <p style="font-size:10.5px;margin:0;" id="ePDiscTxt">Figures are approximate and indicative, based on the assumptions listed below. No payment is due against this document.</p>
                            </div>
                        </div>

                        <div class="doc-stats" style="margin-bottom:12px;">
                            <div class="doc-stat amber"><span>Valid Until</span><strong id="ePStatValid" style="font-size:15px;">&mdash;</strong></div>
                            <div class="doc-stat"><span>Line Items</span><strong id="ePStatItems" style="font-size:15px;">0</strong></div>
                            <div class="doc-stat grey" id="ePTolStat"><span>Tolerance</span><strong style="font-size:15px;">&plusmn;10%</strong></div>
                        </div>

                        <div class="doc-split" style="margin-bottom:12px;">
                            <div>
                                <p class="doc-sec-title">Estimate For</p>
                                <p class="inv-p-party-name" id="ePCName" style="margin:3px 0;">Client</p>
                                <p id="ePCAddr" style="font-size:10px;color:#475569;margin:1px 0;">Address</p>
                                <p id="ePCPhone" style="font-size:10px;color:#475569;margin:1px 0;">Phone: &mdash;</p>
                                <p id="ePCGstRow" style="font-size:10px;color:#475569;margin:2px 0;"><strong>GSTIN:</strong> <span id="ePCGst">&mdash;</span></p>
                            </div>
                            <div>
                                <p class="doc-sec-title">Estimate Details</p>
                                <p style="font-size:10px;color:#475569;margin:2px 0;"><strong>Name of work:</strong> <span id="ePFor">&mdash;</span></p>
                                <p style="font-size:10px;color:#475569;margin:2px 0;"><strong>Place of supply:</strong> <span id="ePPos">&mdash;</span></p>
                                <p id="ePRefRow" style="font-size:10px;color:#475569;margin:2px 0;display:none;"><strong>Ref:</strong> <span id="ePRef">&mdash;</span></p>
                                <p id="ePSurveyRow" style="font-size:10px;color:#475569;margin:2px 0;display:none;"><strong>Measured on:</strong> <span id="ePSurvey">&mdash;</span></p>
                            </div>
                        </div>

                        <div id="ePApBox" style="display:none;margin-bottom:12px;">
                            <p class="doc-sec-title">Basis of Estimate</p>
                            <div class="kv-grid">
                                <div class="kv-item"><div class="kv-k">Rate Basis</div><div class="kv-v" id="ePApBasis">&mdash;</div></div>
                                <div class="kv-item"><div class="kv-k">Market / Cluster</div><div class="kv-v" id="ePApMarket">&mdash;</div></div>
                                <div class="kv-item wide"><div class="kv-k">Range Note</div><div class="kv-v" id="ePApNote">&mdash;</div></div>
                            </div>
                        </div>

                        <div id="ePCpBox" style="display:none;margin-bottom:12px;">
                            <p class="doc-sec-title">Compared With</p>
                            <div class="kv-grid">
                                <div class="kv-item"><div class="kv-k">Vendor / Earlier Quote</div><div class="kv-v" id="ePCpVendor">&mdash;</div></div>
                                <div class="kv-item"><div class="kv-k">Their Reference</div><div class="kv-v" id="ePCpRef">&mdash;</div></div>
                                <div class="kv-item wide"><div class="kv-k">Why we are competitive</div><div class="kv-v" id="ePCpNote">&mdash;</div></div>
                            </div>
                        </div>

                        <div id="ePRpBox" style="display:none;margin-bottom:12px;">
                            <p class="doc-sec-title">Asset &amp; Diagnosis</p>
                            <div class="kv-grid">
                                <div class="kv-item"><div class="kv-k">Asset / Appliance</div><div class="kv-v" id="ePRpAsset">&mdash;</div></div>
                                <div class="kv-item"><div class="kv-k">Warranty</div><div class="kv-v" id="ePRpWarranty">&mdash;</div></div>
                                <div class="kv-item wide"><div class="kv-k">Current Fault</div><div class="kv-v" id="ePRpFault">&mdash;</div></div>
                                <div class="kv-item wide"><div class="kv-k">Old Part / Warranty Terms</div><div class="kv-v" id="ePRpOld">&mdash;</div></div>
                            </div>
                        </div>

                        <div style="margin-bottom:12px;">
                            <table class="inv-p-table">
                                <thead><tr id="ePHead"></tr></thead>
                                <tbody id="ePRows"><tr><td style="padding:14px;text-align:center;color:#94A3B8;">No lines yet</td></tr></tbody>
                            </table>
                        </div>

                        <div id="ePCmpWrap" style="display:none;margin-bottom:12px;">
                            <p class="doc-sec-title">Cost Comparison</p>
                            <table class="inv-p-table">
                                <thead><tr>
                                    <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">Particulars</th>
                                    <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Our Estimate</th>
                                    <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Other Quote</th>
                                </tr></thead>
                                <tbody id="ePCmpRows"></tbody>
                            </table>
                        </div>

                        <div style="display:flex;justify-content:flex-end;margin-bottom:12px;">
                            <table class="inv-p-totals-table" style="min-width:230px;">
                                <tr><td style="padding:3px 8px;">Estimated Subtotal</td><td id="ePSub" style="text-align:right;font-weight:600;">₹0.00</td></tr>
                                <tr id="ePContRow" style="display:none"><td style="padding:3px 8px;">Contingency</td><td id="ePCont" style="text-align:right;font-weight:600;">₹0.00</td></tr>
                                <tr id="ePCgstRow" style="display:none"><td style="padding:3px 8px;">CGST</td><td id="ePCgst" style="text-align:right;font-weight:600;">₹0.00</td></tr>
                                <tr id="ePSgstRow" style="display:none"><td style="padding:3px 8px;">SGST</td><td id="ePSgst" style="text-align:right;font-weight:600;">₹0.00</td></tr>
                                <tr id="ePIgstRow" style="display:none"><td style="padding:3px 8px;">IGST</td><td id="ePIgst" style="text-align:right;font-weight:600;">₹0.00</td></tr>
                                <tr style="border-top:2px solid #1E293B;"><td style="padding:6px 8px;font-weight:800;font-size:12px;color:var(--fmt-accent,#2563EB);" id="ePGrandLbl">Estimated Cost</td><td style="padding:6px 8px;text-align:right;font-weight:800;font-size:12px;color:var(--fmt-accent,#2563EB);" id="ePGrand">₹0.00</td></tr>
                            </table>
                        </div>

                        <div class="amt-hero amber">
                            <div class="amt-hero-lbl" id="ePHeroLbl">Estimated Cost (Approximate)</div>
                            <div class="amt-hero-val" id="ePHeroVal">₹0.00</div>
                        </div>

                        <div id="ePTolNote" class="note-box" style="padding:9px 13px;margin-bottom:10px;">
                            <div class="note-ico" style="font-size:15px;">&#9878;</div>
                            <p style="margin:0;font-size:9.5px;">Tolerance: the final cost may vary by <strong>±10%</strong> of this estimate depending on final measurements, material availability and site conditions. An estimate is not a bill and no payment is due.</p>
                        </div>

                        <div class="inv-p-words" id="ePWordsRow" style="font-size:9px;color:#475569;padding:8px 0;border-top:1px solid #E2E8F0;margin-bottom:8px;">
                            <strong>Amount in words:</strong> <span id="ePWords">Zero Only</span>
                        </div>
                        <div id="ePAssumpWrap" style="font-size:9px;color:#475569;padding:6px 0;">
                            <strong>Assumptions &amp; Exclusions:</strong> <span id="ePAssump">Add your assumptions and exclusions above &mdash; this is what protects both sides.</span>
                        </div>
                        <div class="inv-p-notes" id="ePNotesRow" style="display:none;font-size:9px;color:#475569;padding:6px 0;">
                            <strong>Notes:</strong> <span id="ePNotes">&mdash;</span>
                        </div>
                        <div id="ePBankRow" style="display:none;font-size:9px;color:#475569;padding:8px 0;border-top:1px dashed #E2E8F0;margin-bottom:6px;">
                            <strong>Bank details:</strong> <span id="ePBankTxt">Add your bank details above</span>
                        </div>
                        <div class="doc-sig" id="ePSigRow">
                            <div><div class="doc-sig-line">Client Acknowledgement</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Name, date &amp; signature</div></div>
                            <div><div class="doc-sig-line">For <span id="ePSigName">&mdash;</span></div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Estimator</div></div>
                            <div><div class="doc-sig-line">Contact</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;">Phone &amp; email</div></div>
                        </div>
                        <div class="doc-ft">This is a computer-generated document &middot; Generated by <strong>My Metaji</strong></div>
                    </div>
                </div>
                <div class="qm-total-strip">
                    <span>Estimated Cost</span>
                    <strong id="ePStripTotal">₹0.00</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== FORMAT COMPARISON ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>Estimate, Quotation or Invoice?</h2>
        <p>The three documents look similar and are routinely confused. The difference is <em>certainty</em>. An estimate is your best guess with a tolerance band, a quotation is a firm offer with an expiry date, and an invoice is a statutory demand for payment. Send the wrong one and you either lose money or lose the customer.</p>
        <div class="fmt-table-wrap" style="margin-top:20px;">
            <table class="fmt-table">
                <thead>
                    <tr><th>Point</th><th>Cost Estimate</th><th>Quotation</th><th>Tax Invoice</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Stage of the deal</strong></td>
                        <td>Scope still being discussed, or a rough idea is being floated</td>
                        <td>Scope is known and you are quoting a firm price</td>
                        <td>Supply or service is complete and payment is due</td>
                    </tr>
                    <tr>
                        <td><strong>Binding on the seller?</strong></td>
                        <td><span class="fmt-req no">No &mdash; indicative only</span></td>
                        <td><span class="fmt-req cond">Yes, until the valid-until date</span></td>
                        <td><span class="fmt-req yes">Yes, amount is payable</span></td>
                    </tr>
                    <tr>
                        <td><strong>Single figure or a range?</strong></td>
                        <td><span class="fmt-req yes">Range is normal (Low &ndash; High)</span></td>
                        <td>One firm total, optionally with a Low&ndash;High band</td>
                        <td>One exact total, never a range</td>
                    </tr>
                    <tr>
                        <td><strong>GST treatment</strong></td>
                        <td>Shown as an estimate of tax; can change before the invoice</td>
                        <td>Full breakup, CGST + SGST or IGST</td>
                        <td>Compulsory breakup with buyer GSTIN for B2B</td>
                    </tr>
                    <tr>
                        <td><strong>Must carry</strong></td>
                        <td>Tolerance note, assumptions and exclusions</td>
                        <td>Valid-until date, number, tax breakup, signature</td>
                        <td>Serial number, HSN, tax breakup, total in words</td>
                    </tr>
                    <tr>
                        <td><strong>Used for</strong></td>
                        <td>Internal approval, first discussion, budget planning</td>
                        <td>Sent to the client to win the order</td>
                        <td>Raised to collect payment and claim ITC</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="note-box" style="margin-top:20px;">
            <div class="note-ico">&#128202;</div>
            <div>
                <h4>How to use the tolerance band honestly</h4>
                <ul>
                    <li><strong>±10% is the Indian market norm</strong> for civil, interior and installation work. Narrower bands (say ±3%) only work for standardised, catalogue-priced items.</li>
                    <li><strong>State the tolerance in writing</strong>, on the face of the document, not in a WhatsApp message. It is the only thing that protects you when a hidden wall or a re-route adds cost.</li>
                    <li><strong>Write the assumptions down.</strong> "Assumed: existing walls are plumb and load-bearing" is worth more than any tolerance clause, because it defines what the estimate was based on.</li>
                    <li><strong>Exclusions are not rude.</strong> Listing what the price does <em>not</em> cover prevents the most common dispute, and experienced clients actively expect the line.</li>
                    <li><strong>Never issue an estimate with a GSTIN of the wrong state</strong> &mdash; the CGST/SGST or IGST split in your estimate is what the client will compare against your invoice, and a mismatch is an audit query waiting to happen.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===================== HOW TO ===================== -->
<section class="gst-faq" style="background:#fff;">
    <div class="gst-wrap">
        <h2>How to Create a Cost Estimate</h2>
        <div class="gst-how-grid">
            <div class="gst-how-card">
                <div class="gst-how-num">1</div>
                <h3>Pick the Format</h3>
                <p>Itemised for detailed costing, summary for head-wise budgets, ballpark for a range, comparative when you are quoting against someone else, repair for parts and labour.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">2</div>
                <h3>Price &amp; Add Contingency</h3>
                <p>Enter quantities and market rates, then add a 5&ndash;10% contingency. The subtotal, tax and estimated total update live as you type.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">3</div>
                <h3>Write Assumptions</h3>
                <p>List what the estimate assumes and what it excludes &mdash; site condition, material grade, approvals, society charges. This section settles most disputes.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">4</div>
                <h3>Download &amp; Convert</h3>
                <p>Download the PDF, get the estimate approved, then raise a firm quotation or invoice from the same figures. Nothing is retyped, so nothing drifts.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SEO + RELATED ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>What is an Estimate Format?</h2>
        <p>An <strong>estimate format</strong> is the layout used to share a preliminary, non-binding costing of a job before a firm price is agreed. The single most important thing it must communicate is its own status: this is not a bill, no payment is due, and the numbers are approximate. A good estimate therefore leads with the name of the work, the estimator's identity, the date and a validity date, then presents a breakdown of heads or line items with quantities and assumed market rates, a contingency allowance, the estimated tax and a final approximate total &mdash; frequently as a Low&ndash;High band rather than one precise-looking figure, because a single number implies a certainty you do not have.</p>
        <p>The second most important element is the <strong>assumptions and exclusions</strong> section. A ₹4 lakh interior estimate is meaningless without knowing whether it assumes the existing walls are plumb, whether electrical rewiring is included, whether society deposits and statutory approvals are payable, and what grade of material the rate was built on. Experienced estimators write exclusions first &mdash; listing civil repair, waterproofing, structural changes and government fees as excluded &mdash; because that single paragraph converts an open-ended promise into a bounded one. Adding an explicit tolerance band (typically <strong>±10%</strong> for civil, interior and installation work, tighter for catalogue-priced items) completes the document: it tells the client that a ±10% movement is normal and expected, and it protects the estimator from being held to a number produced without a final measurement.</p>

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
        <h2>From Estimate to Invoice in One Click</h2>
        <p><?= BRAND_NAME ?> keeps your rate card, GST rates and stock prices current, so an approved estimate converts into a real invoice without a single correction.</p>
        <a href="#download" class="gst-cta-btn">Download <?= BRAND_NAME ?> Free &rarr;</a>
    </div>
</section>

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="assets/js/doc-utils.js"></script>
<script>
/* ---------- Format definitions ---------- */
const EST_FORMATS = {
    itemised: {
        label: 'Itemised Estimate', tag: 'Line-by-line rough costing', tags: ['Itemised', 'Contingency %', 'GST breakup'],
        ico: '\u{1F4D8}', accent: '#2563EB', mode: 'itemised',
        desc: 'The general-purpose rough costing. Every head or item gets its own line with quantity and an assumed market rate, plus a contingency percentage and a full GST breakup.',
        fields: 'Estimate number, name of work, client & site, quantity, rate, unit, contingency %, GST %, assumptions & exclusions',
        title: 'COST ESTIMATE', noLbl: 'Estimate No.', grandLbl: 'Estimated Cost'
    },
    summary: {
        label: 'Summary / Head-wise', tag: 'Budget by head of work', tags: ['Head-wise', 'Budget planning', 'Fewer lines'],
        ico: '\u{1F4CA}', accent: '#4F46E5', mode: 'summary',
        desc: 'Costing rolled up into a handful of heads &mdash; civil, electrical, plumbing, finishes. Use it for internal approvals and for clients who want a budget, not a bill of quantities.',
        fields: 'Estimate number, name of work, head of work, quantity, rate, amount, contingency %, assumptions & exclusions',
        title: 'SUMMARY COST ESTIMATE', noLbl: 'Estimate No.', grandLbl: 'Estimated Cost'
    },
    approx: {
        label: 'Ballpark / Range', tag: 'Low &ndash; high columns', tags: ['Low &ndash; High', 'Tolerance band', 'Early enquiry'],
        ico: '\u{1F4C8}', accent: '#0D9488', mode: 'approx',
        desc: 'A single figure you are not confident about, shown honestly as a Low&ndash;High range. Ideal for the first conversation, or when the site has not been measured yet.',
        fields: 'Estimate number, name of work, rate basis, low rate, high rate, Low&ndash;High range, tolerance band, assumptions',
        title: 'BALLPARK COST ESTIMATE', noLbl: 'Estimate No.', grandLbl: 'Estimated Range'
    },
    cmp: {
        label: 'Comparative Estimate', tag: 'Your price vs another quote', tags: ['Side by side', 'Savings shown', 'Justifies price'],
        ico: '\u{1F534}', accent: '#7C3AED', mode: 'cmp',
        desc: 'Your estimate and a second vendor&rsquo;s or earlier quote on the same line items, with the difference and the percentage saving worked out for you.',
        fields: 'Estimate number, name of work, compared-with vendor, their reference, our rate vs their rate, difference, saving %',
        title: 'COMPARATIVE COST ESTIMATE', noLbl: 'Estimate No.', grandLbl: 'Your Estimated Total'
    },
    replacement: {
        label: 'Replacement / Repair', tag: 'Labour &amp; part columns', tags: ['Labour + parts', 'Warranty status', 'Diagnosis'],
        ico: '\u{1F527}', accent: '#D97706', mode: 'replacement',
        desc: 'A repair estimate that splits every line into part cost and labour cost, and records the warranty status of the asset so nobody pays for a part that is still covered.',
        fields: 'Asset, warranty status, diagnosis, description, qty, part rate, labour rate, total, old part handling',
        title: 'REPLACEMENT &amp; REPAIR ESTIMATE', noLbl: 'Estimate No.', grandLbl: 'Estimated Repair Cost'
    }
};

let estFmt = 'itemised';
let estRowId = 0;

/* ---------- Column definitions ---------- */
function estCols(f) {
    if (f.mode === 'approx') {
        return [{ k: 'i', l: '#' }, { k: 'name', l: 'Work / Item' }, { k: 'q', l: 'Qty', r: 1 },
            { k: 'low', l: 'Low Rate', r: 1 }, { k: 'high', l: 'High Rate', r: 1 }, { k: 'rng', l: 'Low \u2013 High', r: 1, b: 1 }];
    }
    if (f.mode === 'cmp') {
        return [{ k: 'i', l: '#' }, { k: 'name', l: 'Work / Item' }, { k: 'q', l: 'Qty', r: 1 },
            { k: 'orate', l: 'Our Rate', r: 1 }, { k: 'otot', l: 'Our Total', r: 1, b: 1 },
            { k: 'rrate', l: 'Other Rate', r: 1 }, { k: 'rtot', l: 'Other Total', r: 1 },
            { k: 'diff', l: 'Difference', r: 1 }];
    }
    if (f.mode === 'replacement') {
        return [{ k: 'i', l: '#' }, { k: 'name', l: 'Description' }, { k: 'q', l: 'Qty', r: 1 },
            { k: 'part', l: 'Part Rate', r: 1 }, { k: 'lab', l: 'Labour Rate', r: 1 }, { k: 'tot', l: 'Total', r: 1, b: 1 }];
    }
    if (f.mode === 'summary') {
        return [{ k: 'i', l: '#' }, { k: 'name', l: 'Head of Work' }, { k: 'q', l: 'Qty', r: 1 },
            { k: 'rate', l: 'Rate', r: 1 }, { k: 'tot', l: 'Amount', r: 1, b: 1 }];
    }
    return [{ k: 'i', l: '#' }, { k: 'name', l: 'Description' }, { k: 'q', l: 'Qty', r: 1 },
        { k: 'unit', l: 'Unit' }, { k: 'rate', l: 'Rate', r: 1 }, { k: 'tot', l: 'Amount', r: 1, b: 1 }];
}

function estColTemplate() {
    const m = EST_FORMATS[estFmt].mode;
    if (m === 'approx') return '2.4fr .6fr 1fr 1fr 1.1fr auto';
    if (m === 'cmp') return '2fr .5fr .9fr 1fr .9fr 1fr .9fr auto';
    if (m === 'replacement') return '2.3fr .6fr 1fr 1fr 1fr auto';
    if (m === 'summary') return '2.6fr .6fr 1fr 1fr auto';
    return '2.4fr .6fr .8fr 1fr 1fr auto';
}

/* ---------- Format picker ---------- */
function estRenderFormats() {
    const grid = document.getElementById('fmtGrid');
    let h = '';
    Object.keys(EST_FORMATS).forEach(k => {
        const f = EST_FORMATS[k];
        h += '<button type="button" class="fmt-card' + (k === estFmt ? ' active' : '') + '" data-est-fmt="' + k + '" aria-pressed="' + (k === estFmt) + '">' +
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
    grid.querySelectorAll('[data-est-fmt]').forEach(b => b.addEventListener('click', () => estSetFormat(b.dataset.estFmt)));
    estSetFormat(estFmt, true);
}

function estSetFormat(k, quiet) {
    const f = EST_FORMATS[k];
    if (!f) return;
    estFmt = k;

    document.querySelectorAll('[data-est-fmt]').forEach(b => {
        const on = b.dataset.estFmt === k;
        b.classList.toggle('active', on);
        b.setAttribute('aria-pressed', on);
    });

    document.getElementById('fmtInfoIco').textContent = f.ico;
    document.getElementById('fmtInfoTitle').textContent = f.label;
    document.getElementById('fmtInfoDesc').textContent = f.desc;
    document.getElementById('fmtInfoFields').textContent = f.fields;
    document.getElementById('ePTitle').textContent = f.title;
    document.getElementById('ePNoLbl').textContent = f.noLbl + ':';

    const show = (id, on) => { const e = document.getElementById(id); if (e) e.style.display = on ? '' : 'none'; };
    show('apRow', f.mode === 'approx');
    show('cpRow', f.mode === 'cmp');
    show('rpRow', f.mode === 'replacement');

    const items = document.getElementById('estItems');
    if (items) items.style.setProperty('--doc-cols', estColTemplate());

    const cols = estCols(f);
    document.getElementById('eItemHd').innerHTML = cols.map(c =>
        '<span' + (c.r ? ' style="text-align:right"' : '') + '>' + c.l + '</span>').join('') + '<span></span>';
    document.getElementById('ePHead').innerHTML = cols.map(c =>
        '<th style="background:#334155;color:#fff;padding:5px 6px;text-align:' + (c.r ? 'right' : 'left') + ';font-size:9px;">' + c.l + '</th>').join('');

    const snap = estSnapshot();
    document.getElementById('estItems').querySelectorAll('.doc-item').forEach(e => e.remove());
    (snap.length ? snap : [{}]).forEach(s => estAddRowRaw(s));

    if (!quiet) estUpdate();
}

/* ---------- Line rows ---------- */
function estClean(v) {
    return String(v === undefined || v === null ? '' : v).replace(/[&<>"']/g, '');
}

function estSnapshot() {
    const out = [];
    document.querySelectorAll('#estItems .doc-item').forEach(el => {
        const g = s => { const e = el.querySelector(s); return e ? (e.value || '') : ''; };
        out.push({
            name: g('[data-est-name]'),
            qty: g('[data-est-qty]'),
            a: g('[data-est-rate]') || g('[data-est-low]') || g('[data-est-orate]') || g('[data-est-part]'),
            b: g('[data-est-lab]') || g('[data-est-high]') || g('[data-est-rrate]')
        });
    });
    return out;
}

function estAddRowRaw(seed) {
    seed = seed || {};
    const f = EST_FORMATS[estFmt];
    const id = estRowId++;
    const q = seed.qty === undefined || seed.qty === '' ? 1 : estClean(seed.qty);
    const a = estClean(seed.a || 0);
    const b = estClean(seed.b || 0);
    const n = s => '<input type="number" ' + s + ' min="0" step="0.01" oninput="estUpdate()">';

    let cells;
    if (f.mode === 'approx') {
        cells = n('data-est-low value="' + a + '"') + n('data-est-high value="' + b + '"') +
            '<span class="doc-item-amt" data-est-rng>&#8377;0.00</span>';
    } else if (f.mode === 'cmp') {
        cells = n('data-est-orate value="' + a + '"') + '<span class="doc-item-amt" data-est-otot>&#8377;0.00</span>' +
            n('data-est-rrate value="' + b + '"') + '<span class="doc-item-amt" data-est-rtot>&#8377;0.00</span>' +
            '<span class="doc-item-amt" data-est-diff>&#8377;0.00</span>';
    } else if (f.mode === 'replacement') {
        cells = n('data-est-part value="' + a + '"') + n('data-est-lab value="' + b + '"') +
            '<span class="doc-item-amt" data-est-tot>&#8377;0.00</span>';
    } else if (f.mode === 'summary') {
        cells = n('data-est-rate value="' + a + '"') + '<span class="doc-item-amt" data-est-tot>&#8377;0.00</span>';
    } else {
        cells = '<select data-est-unit onchange="estUpdate()"><option>NOS</option><option selected>PCS</option><option>KGS</option><option>SQF</option><option>MTR</option><option>RFT</option><option>SET</option><option>BOX</option><option>LTR</option><option>DAY</option></select>' +
            n('data-est-rate value="' + a + '"') + '<span class="doc-item-amt" data-est-tot>&#8377;0.00</span>';
    }

    document.getElementById('estItems').insertAdjacentHTML('beforeend',
        '<div class="doc-item" data-row="' + id + '">' +
            '<input type="text" data-est-name value="' + estClean(seed.name || '') + '" placeholder="Work, item or head" oninput="estUpdate()">' +
            '<input type="number" data-est-qty value="' + q + '" min="0" step="0.01" oninput="estUpdate()">' +
            cells +
            '<button type="button" class="doc-item-del" onclick="estDelRow(' + id + ')" title="Remove line" aria-label="Remove line">&times;</button>' +
        '</div>');
}

function estAddRow(seed) {
    estAddRowRaw(seed);
    estUpdate();
}

function estDelRow(id) {
    const e = document.querySelector('[data-row="' + id + '"]');
    if (e) e.remove();
    estUpdate();
}

/* ---------- Totals engine ---------- */
function estCollect() {
    const f = EST_FORMATS[estFmt];
    const inter = DOC.isInterState('sState', 'cState');
    const contPct = DOC.num('estCont');
    const gstPct = DOC.num('estGst');
    const rows = [];
    let base = 0, oth = 0, low = 0, high = 0;

    const nv = (el, sel) => { const e = el.querySelector(sel); return e ? (parseFloat(e.value) || 0) : 0; };
    const sv = (el, sel) => { const e = el.querySelector(sel); return e ? (e.value || '').trim() : ''; };
    const set = (el, sel, v) => { const e = el.querySelector(sel); if (e) e.textContent = v; };

    document.querySelectorAll('#estItems .doc-item').forEach((el, i) => {
        const name = sv(el, '[data-est-name]') || 'Item';
        const q = nv(el, '[data-est-qty]');
        const row = { i: i + 1, name: name, q: q, unit: f.mode === 'itemised' ? sv(el, '[data-est-unit]') : '' };

        if (f.mode === 'approx') {
            const lr = nv(el, '[data-est-low]'), hr = nv(el, '[data-est-high]');
            row.low = DOC.round2(q * lr);
            row.high = DOC.round2(q * hr);
            row.mid = DOC.round2((row.low + row.high) / 2);
            low += row.low; high += row.high; base += row.mid;
            set(el, '[data-est-rng]', DOC.money(row.low) + ' \u2013 ' + DOC.money(row.high));
        } else if (f.mode === 'cmp') {
            const orate = nv(el, '[data-est-orate]'), rrate = nv(el, '[data-est-rrate]');
            row.orate = orate; row.rrate = rrate;
            row.otot = DOC.round2(q * orate);
            row.rtot = DOC.round2(q * rrate);
            row.diff = DOC.round2(row.rtot - row.otot);
            base += row.otot; oth += row.rtot;
            set(el, '[data-est-otot]', DOC.money(row.otot));
            set(el, '[data-est-rtot]', DOC.money(row.rtot));
            set(el, '[data-est-diff]', (row.diff > 0 ? '+' : row.diff < 0 ? '\u2212' : '') + DOC.money(Math.abs(row.diff)));
        } else if (f.mode === 'replacement') {
            const part = nv(el, '[data-est-part]'), lab = nv(el, '[data-est-lab]');
            row.part = part; row.lab = lab;
            row.tot = DOC.round2(q * (part + lab));
            base += row.tot;
            set(el, '[data-est-tot]', DOC.money(row.tot));
        } else {
            const rate = nv(el, '[data-est-rate]');
            row.rate = rate;
            row.tot = DOC.round2(q * rate);
            base += row.tot;
            set(el, '[data-est-tot]', DOC.money(row.tot));
        }
        rows.push(row);
    });

    base = DOC.round2(base);
    oth = DOC.round2(oth);
    low = DOC.round2(low);
    high = DOC.round2(high);

    const cont = DOC.round2(base * contPct / 100);
    const taxable = DOC.round2(base + cont);
    const tax = DOC.round2(taxable * gstPct / 100);
    const grand = DOC.round2(taxable + tax);

    const othCont = DOC.round2(oth * contPct / 100);
    const othTaxable = DOC.round2(oth + othCont);
    const othTax = DOC.round2(othTaxable * gstPct / 100);
    const othGrand = DOC.round2(othTaxable + othTax);

    const lowCont = DOC.round2(low * contPct / 100);
    const highCont = DOC.round2(high * contPct / 100);
    const lowTax = DOC.round2((low + lowCont) * gstPct / 100);
    const highTax = DOC.round2((high + highCont) * gstPct / 100);
    const lowTotal = DOC.round2(low + lowCont + lowTax);
    const highTotal = DOC.round2(high + highCont + highTax);

    const diff = DOC.round2(grand - othGrand);
    const diffPct = othGrand > 0 ? DOC.round2(Math.abs(diff) / othGrand * 100) : 0;

    return {
        f: f, inter: inter, contPct: contPct, gstPct: gstPct, rows: rows,
        base: base, oth: oth, low: low, high: high,
        cont: cont, taxable: taxable, tax: tax, grand: grand,
        lowTotal: lowTotal, highTotal: highTotal,
        othGrand: othGrand, diff: diff, diffPct: diffPct,
        wordBase: f.mode === 'approx' ? highTotal : grand
    };
}

/* ---------- Preview render ---------- */
function estUpdate() {
    const d = estCollect();

    DOC.text('ePSName', DOC.val('sName'));
    DOC.text('ePSAddr', DOC.val('sAddr'));
    document.getElementById('ePSPhone').textContent = 'Phone: ' + (DOC.val('sPhone') || '\u2014');
    document.getElementById('ePSState').textContent = 'State: ' + (DOC.val('sState') || '\u2014');
    DOC.text('ePSGst', DOC.val('sGst'));
    DOC.showIf('ePSCode', !!DOC.val('sStateCode'));
    if (DOC.val('sStateCode')) document.getElementById('ePSCode').textContent = 'State Code: ' + DOC.val('sStateCode');

    DOC.text('ePNo', DOC.val('estNo'));
    DOC.text('ePDate', DOC.fmtDate(DOC.val('estDate')));
    const validTxt = DOC.fmtDate(DOC.val('estValid')) || '\u2014';
    DOC.text('ePValid', validTxt);
    document.getElementById('ePStatValid').textContent = validTxt;
    document.getElementById('ePStatItems').textContent = String(d.rows.length);

    DOC.showIf('ePDisclaimer', document.getElementById('optDisclaim').checked);
    DOC.showIf('ePTolNote', document.getElementById('optTol').checked);
    DOC.showIf('ePTolStat', document.getElementById('optTol').checked);

    DOC.text('ePCName', DOC.val('cName'));
    DOC.text('ePCAddr', DOC.val('cAddr'));
    document.getElementById('ePCPhone').textContent = 'Phone: ' + (DOC.val('cPhone') || '\u2014');
    DOC.text('ePCGst', DOC.val('cGst'));
    DOC.text('ePFor', DOC.val('estFor'));
    DOC.text('ePPos', DOC.val('cPos') || (DOC.val('cState') || '\u2014'));
    DOC.showIf('ePRefRow', !!DOC.val('estRef'));
    if (DOC.val('estRef')) document.getElementById('ePRef').textContent = DOC.val('estRef');
    DOC.showIf('ePSurveyRow', !!DOC.val('estSurvey'));
    if (DOC.val('estSurvey')) document.getElementById('ePSurvey').textContent = DOC.fmtDate(DOC.val('estSurvey'));

    DOC.showIf('ePApBox', d.f.mode === 'approx');
    if (d.f.mode === 'approx') {
        DOC.text('ePApBasis', DOC.val('apBasis'));
        DOC.text('ePApMarket', DOC.val('apMarket'));
        DOC.text('ePApNote', DOC.val('apNote'));
    }
    DOC.showIf('ePCpBox', d.f.mode === 'cmp');
    if (d.f.mode === 'cmp') {
        DOC.text('ePCpVendor', DOC.val('cpVendor'));
        DOC.text('ePCpRef', DOC.val('cpRef'));
        DOC.text('ePCpNote', DOC.val('cpNote'));
    }
    DOC.showIf('ePRpBox', d.f.mode === 'replacement');
    if (d.f.mode === 'replacement') {
        DOC.text('ePRpAsset', DOC.val('rpAsset'));
        DOC.text('ePRpWarranty', DOC.val('rpWarranty'));
        DOC.text('ePRpFault', DOC.val('rpFault'));
        DOC.text('ePRpOld', DOC.val('rpOld'));
    }

    /* item table */
    const cols = estCols(d.f);
    const tb = document.getElementById('ePRows');
    if (!d.rows.length) {
        tb.innerHTML = '<tr><td colspan="' + cols.length + '" style="padding:14px;text-align:center;color:#94A3B8;">No lines yet</td></tr>';
    } else {
        tb.innerHTML = d.rows.map(r => {
            const cells = [];
            cols.forEach(c => {
                let v = '';
                if (c.k === 'i') v = r.i;
                else if (c.k === 'name') v = r.name;
                else if (c.k === 'q') v = DOC.qty(r.q);
                else if (c.k === 'unit') v = r.unit || '\u2014';
                else if (c.k === 'low') v = DOC.money(r.low);
                else if (c.k === 'high') v = DOC.money(r.high);
                else if (c.k === 'rng') v = DOC.money(r.low) + ' \u2013 ' + DOC.money(r.high);
                else if (c.k === 'orate') v = DOC.money(r.orate);
                else if (c.k === 'otot') v = DOC.money(r.otot);
                else if (c.k === 'rrate') v = DOC.money(r.rrate);
                else if (c.k === 'rtot') v = DOC.money(r.rtot);
                else if (c.k === 'diff') v = (r.diff > 0 ? '+' : r.diff < 0 ? '\u2212' : '') + DOC.money(Math.abs(r.diff));
                else if (c.k === 'part') v = DOC.money(r.part);
                else if (c.k === 'lab') v = DOC.money(r.lab);
                else v = DOC.money(r.tot);
                cells.push('<td style="padding:5px 6px;text-align:' + (c.r ? 'right' : 'left') + ';' + (c.b ? 'font-weight:700;' : '') + '">' + v + '</td>');
            });
            return '<tr>' + cells.join('') + '</tr>';
        }).join('');
    }

    /* comparative summary */
    DOC.showIf('ePCmpWrap', d.f.mode === 'cmp');
    if (d.f.mode === 'cmp') {
        document.getElementById('ePCmpRows').innerHTML =
            '<tr><td style="padding:5px 6px;">Base cost</td><td style="padding:5px 6px;text-align:right;">' + DOC.money(d.base) + '</td><td style="padding:5px 6px;text-align:right;">' + DOC.money(d.oth) + '</td></tr>' +
            '<tr><td style="padding:5px 6px;">Contingency @ ' + DOC.qty(d.contPct) + '%</td><td style="padding:5px 6px;text-align:right;">' + DOC.money(d.cont) + '</td><td style="padding:5px 6px;text-align:right;">' + DOC.money(DOC.round2(d.oth * d.contPct / 100)) + '</td></tr>' +
            '<tr><td style="padding:5px 6px;">GST @ ' + DOC.qty(d.gstPct) + '%</td><td style="padding:5px 6px;text-align:right;">' + DOC.money(d.tax) + '</td><td style="padding:5px 6px;text-align:right;">' + DOC.money(DOC.round2((d.oth + DOC.round2(d.oth * d.contPct / 100)) * d.gstPct / 100)) + '</td></tr>' +
            '<tr style="border-top:2px solid #1E293B;"><td style="padding:6px;font-weight:800;">Total</td><td style="padding:6px;text-align:right;font-weight:800;color:#059669;">' + DOC.money(d.grand) + '</td><td style="padding:6px;text-align:right;font-weight:800;color:#B91C1C;">' + DOC.money(d.othGrand) + '</td></tr>' +
            '<tr><td style="padding:5px 6px;font-weight:700;">' + (d.diff <= 0 ? 'Our saving' : 'Our cost higher by') + '</td><td colspan="2" style="padding:5px 6px;text-align:right;font-weight:800;">' + (d.diff <= 0 ? '' : '+') + DOC.money(Math.abs(d.diff)) + ' (' + DOC.qty(d.diffPct) + '%)</td></tr>';
    }

    /* totals */
    DOC.text('ePSub', DOC.money(d.base));
    DOC.showIf('ePContRow', d.cont > 0);
    if (d.cont > 0) document.getElementById('ePCont').textContent = DOC.money(d.cont);
    if (d.inter) {
        DOC.showIf('ePCgstRow', false); DOC.showIf('ePSgstRow', false);
        DOC.showIf('ePIgstRow', true); DOC.text('ePIgst', DOC.money(d.tax));
    } else {
        DOC.showIf('ePIgstRow', false);
        DOC.showIf('ePCgstRow', true); DOC.text('ePCgst', DOC.money(d.tax / 2));
        DOC.showIf('ePSgstRow', true); DOC.text('ePSgst', DOC.money(d.tax - d.tax / 2));
    }

    document.getElementById('ePGrandLbl').textContent = d.f.grandLbl;
    DOC.text('ePGrand', DOC.money(d.grand));

    const heroLbl = document.getElementById('ePHeroLbl');
    const heroVal = document.getElementById('ePHeroVal');
    if (d.f.mode === 'approx') {
        heroLbl.textContent = 'Estimated Range (Low \u2013 High) \u00B7 Valid until ' + validTxt;
        heroVal.textContent = DOC.money(d.lowTotal) + ' \u2013 ' + DOC.money(d.highTotal);
        heroVal.style.fontSize = '19px';
    } else if (d.f.mode === 'cmp') {
        heroLbl.textContent = 'Your Estimated Total \u00B7 Valid until ' + validTxt;
        heroVal.textContent = DOC.money(d.grand);
        heroVal.style.fontSize = '28px';
    } else {
        heroLbl.textContent = d.f.grandLbl + ' (Approximate) \u00B7 Valid until ' + validTxt;
        heroVal.textContent = DOC.money(d.grand);
        heroVal.style.fontSize = '28px';
    }

    const stripLbl = document.querySelector('.qm-total-strip span');
    if (stripLbl) stripLbl.textContent = d.f.mode === 'approx' ? 'Estimated Range' : (d.f.mode === 'cmp' ? 'Your Estimate' : 'Estimated Cost');
    document.getElementById('ePStripTotal').textContent = d.f.mode === 'approx'
        ? (DOC.money(d.lowTotal) + ' \u2013 ' + DOC.money(d.highTotal))
        : DOC.money(d.grand);

    /* toggles + free text */
    DOC.showIf('ePWordsRow', document.getElementById('optWords').checked);
    if (document.getElementById('optWords').checked) document.getElementById('ePWords').textContent = DOC.inWords(d.wordBase);

    const assump = DOC.val('estAssump');
    if (assump) document.getElementById('ePAssump').textContent = assump;
    const notes = DOC.val('estNotes');
    DOC.showIf('ePNotesRow', !!notes);
    if (notes) document.getElementById('ePNotes').textContent = notes;

    const bank = document.getElementById('optBank').checked;
    DOC.showIf('bankRow', bank);
    DOC.showIf('bankRow2', bank);
    DOC.showIf('ePBankRow', bank);
    if (bank) {
        const bits = [];
        if (DOC.val('bBank')) bits.push(DOC.val('bBank'));
        if (DOC.val('bAcct')) bits.push('A/c ' + DOC.val('bAcct'));
        if (DOC.val('bIfsc')) bits.push('IFSC ' + DOC.val('bIfsc'));
        if (DOC.val('bUpi')) bits.push('UPI ' + DOC.val('bUpi'));
        document.getElementById('ePBankTxt').textContent = bits.length ? bits.join(' \u00B7 ') : 'Add your bank details above';
    }

    DOC.showIf('ePSigRow', document.getElementById('optSignature').checked);
    if (document.getElementById('optSignature').checked) document.getElementById('ePSigName').textContent = DOC.val('sName') || 'the estimator';
}

/* ---------- Actions ---------- */
function estPreviewJump() { estUpdate(); DOC.scrollToPreview('estPreview'); }
function estPrint() { estUpdate(); window.print(); }

function estReset() {
    ['sName', 'sGst', 'sPhone', 'sAddr', 'sStateCode', 'sContact',
        'cName', 'cGst', 'cPhone', 'cAddr', 'cPos',
        'estNo', 'estFor', 'estRef', 'estSurvey', 'estAssump', 'estNotes',
        'bBank', 'bAcct', 'bIfsc', 'bUpi',
        'apMarket', 'apNote', 'cpVendor', 'cpRef', 'cpNote', 'rpAsset', 'rpFault', 'rpOld'].forEach(id => {
            const e = document.getElementById(id); if (e) e.value = '';
        });
    document.getElementById('estCont').value = 5;
    document.getElementById('sState').value = '';
    document.getElementById('cState').value = '';
    document.getElementById('estDate').value = DOC.todayISO();
    document.getElementById('estValid').value = DOC.addDaysISO(15);
    document.getElementById('estSurvey').value = DOC.todayISO();
    document.getElementById('estItems').querySelectorAll('.doc-item').forEach(e => e.remove());
    estSetFormat('itemised');
    estUpdate();
}

function estPDF() {
    estUpdate();
    const d = estCollect();
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
            { label: d.f.noLbl, value: DOC.val('estNo') || '\u2014' },
            { label: 'Date', value: DOC.fmtDate(DOC.val('estDate')) || '\u2014' },
            { label: 'Valid until', value: DOC.fmtDate(DOC.val('estValid')) || '\u2014' }
        ],
        accent: accent
    });

    /* not-a-bill banner */
    if (document.getElementById('optDisclaim').checked) {
        doc.setFillColor(255, 251, 235); doc.setDrawColor(253, 230, 138); doc.setLineWidth(0.3);
        doc.roundedRect(MG, y, W - MG * 2, 12, 1.5, 1.5, 'FD');
        doc.setTextColor(146, 64, 14); doc.setFont('helvetica', 'bold'); doc.setFontSize(9);
        doc.text('This is an estimate, not a bill.', MG + 3, y + 4.6);
        doc.setFont('helvetica', 'normal'); doc.setFontSize(7.2); doc.setTextColor(120, 113, 108);
        doc.text('Figures are approximate and indicative. No payment is due against this document.', MG + 3, y + 8.6);
        y += 16;
    }

    /* client + work */
    y = DOC.pdfSection(doc, MG, y, 'ESTIMATE FOR', 60);
    doc.setFont('helvetica', 'bold'); doc.setFontSize(9); doc.setTextColor(15, 23, 42);
    doc.text(DOC.val('cName') || 'Client', MG, y + 4.5);
    doc.setFont('helvetica', 'normal'); doc.setFontSize(8); doc.setTextColor(51, 65, 85);
    doc.text('Name of work: ' + (DOC.val('estFor') || '\u2014'), W - MG, y + 4.5, { align: 'right' });
    y += 8;
    const cl = doc.splitTextToSize(DOC.val('cAddr') || '', 95);
    doc.text(cl, MG, y); y += cl.length * 3.6;
    doc.text('Phone: ' + (DOC.val('cPhone') || '\u2014') + '  \u00B7  GSTIN: ' + (DOC.val('cGst') || '\u2014') + '  \u00B7  Place of supply: ' + (DOC.val('cPos') || DOC.val('cState') || '\u2014'), MG, y); y += 4;
    if (DOC.val('estRef')) { doc.text('Reference: ' + DOC.val('estRef') + (DOC.val('estSurvey') ? '  \u00B7  Measured on: ' + DOC.fmtDate(DOC.val('estSurvey')) : ''), MG, y); y += 4; }
    y += 4;

    /* variant blocks */
    if (d.f.mode === 'approx') {
        y = DOC.pdfSection(doc, MG, y, 'BASIS OF ESTIMATE', 70);
        doc.setFontSize(8);
        doc.text('Rate basis: ' + DOC.val('apBasis') + '  \u00B7  Market / cluster: ' + (DOC.val('apMarket') || '\u2014'), MG, y); y += 4;
        if (DOC.val('apNote')) {
            const al = doc.splitTextToSize(DOC.val('apNote'), W - MG * 2);
            doc.text(al, MG, y); y += al.length * 3.6;
        }
        y += 2;
    }
    if (d.f.mode === 'cmp') {
        y = DOC.pdfSection(doc, MG, y, 'COMPARED WITH', 70);
        doc.setFontSize(8);
        doc.text(DOC.val('cpVendor') || '\u2014' + (DOC.val('cpRef') ? '  \u00B7  Ref: ' + DOC.val('cpRef') : ''), MG, y); y += 4;
        if (DOC.val('cpNote')) {
            const cpl = doc.splitTextToSize(DOC.val('cpNote'), W - MG * 2);
            doc.text(cpl, MG, y); y += cpl.length * 3.6;
        }
        y += 2;
    }
    if (d.f.mode === 'replacement') {
        y = DOC.pdfSection(doc, MG, y, 'ASSET & DIAGNOSIS', 70);
        doc.setFontSize(8);
        doc.text('Asset: ' + (DOC.val('rpAsset') || '\u2014') + '  \u00B7  Warranty: ' + DOC.val('rpWarranty'), MG, y); y += 4;
        if (DOC.val('rpFault')) {
            const rl = doc.splitTextToSize('Fault: ' + DOC.val('rpFault'), W - MG * 2);
            doc.text(rl, MG, y); y += rl.length * 3.6;
        }
        y += 2;
    }

    /* items table */
    const cols = estCols(d.f);
    const cell = function (r, k) {
        if (k === 'i') return r.i;
        if (k === 'name') return r.name + (r.unit ? ' (' + r.unit + ')' : '');
        if (k === 'q') return DOC.qty(r.q);
        if (k === 'unit') return r.unit || '\u2014';
        if (k === 'low') return DOC.money(r.low);
        if (k === 'high') return DOC.money(r.high);
        if (k === 'rng') return DOC.money(r.low) + ' \u2013 ' + DOC.money(r.high);
        if (k === 'orate') return DOC.money(r.orate);
        if (k === 'otot') return DOC.money(r.otot);
        if (k === 'rrate') return DOC.money(r.rrate);
        if (k === 'rtot') return DOC.money(r.rtot);
        if (k === 'diff') return (r.diff > 0 ? '+' : r.diff < 0 ? '\u2212' : '') + DOC.money(Math.abs(r.diff));
        if (k === 'part') return DOC.money(r.part);
        if (k === 'lab') return DOC.money(r.lab);
        return DOC.money(r.tot);
    };
    const body = d.rows.length ? d.rows.map(r => cols.map(c => cell(r, c.k)))
        : [cols.map((c, i) => (i === 1 ? 'No lines added' : ''))];
    const styles = {};
    cols.forEach((c, idx) => { if (c.r) styles[idx] = c.b ? { halign: 'right', fontStyle: 'bold' } : { halign: 'right' }; });

    y = DOC.pdfTable(doc, cols.map(c => c.l), body, { startY: y, accent: accent, extra: { columnStyles: styles } }) + 5;

    /* comparative summary */
    if (d.f.mode === 'cmp') {
        y = DOC.pdfTable(doc, ['Particulars', 'Our estimate', 'Other quote'], [
            ['Base cost', DOC.money(d.base), DOC.money(d.oth)],
            ['Contingency @ ' + DOC.qty(d.contPct) + '%', DOC.money(d.cont), DOC.money(DOC.round2(d.oth * d.contPct / 100))],
            ['GST @ ' + DOC.qty(d.gstPct) + '%', DOC.money(d.tax), DOC.money(DOC.round2((d.oth + DOC.round2(d.oth * d.contPct / 100)) * d.gstPct / 100))],
            [{ content: 'Total', styles: { fontStyle: 'bold' } }, { content: DOC.money(d.grand), styles: { halign: 'right', fontStyle: 'bold' } }, { content: DOC.money(d.othGrand), styles: { halign: 'right', fontStyle: 'bold' } }],
            [{ content: d.diff <= 0 ? 'Our saving' : 'Our cost higher by', styles: { fontStyle: 'bold' } }, { content: (d.diff <= 0 ? '' : '+') + DOC.money(Math.abs(d.diff)) + '  (' + DOC.qty(d.diffPct) + '%)', styles: { halign: 'right', fontStyle: 'bold' } }, '']
        ], { startY: y, accent: accent, extra: { columnStyles: { 1: { halign: 'right' }, 2: { halign: 'right' } } } }) + 5;
    }

    /* totals */
    const rows = [{ label: 'Estimated subtotal', value: DOC.money(d.base) }];
    if (d.cont > 0) rows.push({ label: 'Contingency @ ' + DOC.qty(d.contPct) + '%', value: DOC.money(d.cont) });
    if (d.inter) {
        rows.push({ label: 'IGST @ ' + DOC.qty(d.gstPct) + '%', value: DOC.money(d.tax) });
    } else {
        rows.push({ label: 'CGST @ ' + DOC.qty(d.gstPct / 2) + '%', value: DOC.money(d.tax / 2) });
        rows.push({ label: 'SGST @ ' + DOC.qty(d.gstPct / 2) + '%', value: DOC.money(d.tax - d.tax / 2) });
    }
    rows.push({ rule: true, gap: 2 });
    if (d.f.mode === 'approx') {
        rows.push({ label: 'Estimated range (low)', value: DOC.money(d.lowTotal), size: 8 });
        rows.push({ label: 'Estimated range (high)', value: DOC.money(d.highTotal), bold: true, size: 10.5, color: accent, gap: 6 });
    } else {
        rows.push({ label: d.f.grandLbl, value: DOC.money(d.grand), bold: true, size: 10.5, color: accent, gap: 6 });
    }
    y = DOC.pdfTotals(doc, W, y, rows);

    if (document.getElementById('optTol').checked) {
        y += 2;
        doc.setTextColor(180, 83, 9); doc.setFontSize(7.6);
        doc.text('Tolerance: the final cost may vary by \u00B110% of this estimate. This document is an estimate, not a bill.', MG, y);
        y += 6;
        doc.setTextColor(51, 65, 85);
    }
    if (document.getElementById('optWords').checked) {
        y += 2;
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Amount in words: ' + DOC.inWords(d.wordBase), MG, y); y += 6;
        doc.setFont('helvetica', 'normal');
    }
    if (DOC.val('estAssump')) {
        if (y > doc.internal.pageSize.getHeight() - 30) { doc.addPage(); y = 20; }
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Assumptions & Exclusions:', MG, y); y += 4;
        doc.setFont('helvetica', 'normal');
        const al = doc.splitTextToSize(DOC.val('estAssump'), W - MG * 2);
        doc.text(al, MG, y); y += al.length * 3.6 + 3;
    }
    if (DOC.val('estNotes')) {
        if (y > doc.internal.pageSize.getHeight() - 30) { doc.addPage(); y = 20; }
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Notes:', MG, y); y += 4;
        doc.setFont('helvetica', 'normal');
        const nl = doc.splitTextToSize(DOC.val('estNotes'), W - MG * 2);
        doc.text(nl, MG, y); y += nl.length * 3.6 + 3;
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
            doc.text('Bank details: ' + bits.join('  \u00B7  '), MG, y); y += 5;
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
        doc.text('Client Acknowledgement', MG + colW / 2, sy + 4, { align: 'center' });
        doc.setFont('helvetica', 'bold');
        doc.text('For ' + (DOC.val('sName') || 'the estimator'), MG + colW * 1.5, sy + 4, { align: 'center' });
        doc.setFont('helvetica', 'normal');
        doc.text('Contact', MG + colW * 2.5, sy + 4, { align: 'center' });
        y = sy + 10;
    }

    DOC.pdfFooter(doc, y, (DOC.val('estNo') || 'estimate') + '  \u00B7  Estimate, not a bill');
    doc.save(((DOC.val('estNo') || 'estimate').replace(/[^\w\-]+/g, '_')) + '.pdf');
}

/* ---------- Boot ---------- */
(function () {
    DOC.fillStates('sState', 'Select state');
    DOC.fillStates('cState', 'Select state');
    DOC.fillRates('estGst');
    document.getElementById('estDate').value = DOC.todayISO();
    document.getElementById('estValid').value = DOC.addDaysISO(15);
    document.getElementById('estSurvey').value = DOC.todayISO();

    estRenderFormats();

    const ids = ['sName', 'sGst', 'sPhone', 'sAddr', 'sState', 'sStateCode', 'sContact',
        'cName', 'cGst', 'cPhone', 'cAddr', 'cState', 'cPos',
        'estNo', 'estDate', 'estFor', 'estValid', 'estRef', 'estSurvey', 'estCont', 'estGst',
        'apBasis', 'apMarket', 'apNote', 'cpVendor', 'cpRef', 'cpNote',
        'rpAsset', 'rpWarranty', 'rpFault', 'rpOld',
        'estAssump', 'estNotes', 'bBank', 'bAcct', 'bIfsc', 'bUpi',
        'optDisclaim', 'optTol', 'optWords', 'optSignature', 'optBank'];
    DOC.bind(ids, estUpdate);

    DOC.bindSwatches('data-est-accent', estUpdate);

    estUpdate();
})();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
