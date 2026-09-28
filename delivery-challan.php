<?php
/**
 * My Metaji - Delivery Challan Format
 * Create a goods dispatch note in the right challan format (unpriced, priced, e-way bill, job work or split dispatch) and download it as a PDF.
 */
require_once __DIR__ . '/config.php';
$page_title = 'Free Delivery Challan Format — E-Way Bill Dispatch Note | ' . BRAND_NAME;
$page_desc  = 'Create a free delivery challan format for goods dispatch. Unpriced, priced, e-way bill, job work or split dispatch challan with live preview and instant PDF download.';
include __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="gst-hero">
    <div class="gst-wrap">
        <div class="gst-hero-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M1 3h13v13H1z"/><path d="M14 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="17.5" cy="18.5" r="2"/></svg>
            Goods dispatch note &middot; E-way bill ready &middot; No sign-up
        </div>
        <h1>Free <span>Delivery Challan</span></h1>
        <p>A delivery challan records the goods physically leaving your premises and is the <strong>primary document behind the e-way bill</strong>. Pick unpriced, priced, e-way bill, job work or split dispatch, then download a print-ready PDF.</p>
        <div class="gst-hero-chips">
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
                5 challan formats
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                Instant PDF download
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6L9 17l-5-5"/></svg>
                Tri-signature gate copy
            </span>
        </div>
    </div>
</section>

<!-- ===================== TOOL ===================== -->
<section class="gst-tool" id="delivery-challan-tool">
    <div class="gst-wrap">
        <div class="qm-layout">

            <!-- ===== LEFT: FORM ===== -->
            <div>

                <!-- 1. FORMAT PICKER -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 3h13v13H1z"/><path d="M14 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="17.5" cy="18.5" r="2"/></svg>
                        </div>
                        <div>
                            <h3>Choose Your Challan Format</h3>
                            <p>A challan carries goods, not value &mdash; unless you price it. The format decides which blocks print.</p>
                        </div>
                        <span class="qm-step">Step 1</span>
                    </div>

                    <div class="fmt-grid" id="dcFmtGrid" role="group" aria-label="Delivery challan format"></div>

                    <div class="note-box gold" style="margin-top:16px;">
                        <div class="note-ico" id="dcInfoIco">&#128230;</div>
                        <div>
                            <h4 id="dcInfoTitle">Delivery Challan (Unpriced)</h4>
                            <p id="dcInfoDesc">The most common challan. It records exactly what left the premises and who received it, without carrying any price, so no GST is charged on it.</p>
                            <p style="margin:0;"><strong>Compulsory particulars:</strong> <span id="dcInfoFields">Challan number and date, dispatch date and time, consignee name and address, item description with quantity and UOM, mode of transport and vehicle number</span></p>
                        </div>
                    </div>

                    <div class="doc-split" style="margin-top:18px;">
                        <div>
                            <div class="doc-opt">
                                <span>Accent colour</span>
                                <div class="doc-swatches">
                                    <button type="button" class="doc-swatch active" data-dc-accent="blue" style="background:#2563EB" aria-label="Blue"></button>
                                    <button type="button" class="doc-swatch" data-dc-accent="indigo" style="background:#4F46E5" aria-label="Indigo"></button>
                                    <button type="button" class="doc-swatch" data-dc-accent="green" style="background:#059669" aria-label="Green"></button>
                                    <button type="button" class="doc-swatch" data-dc-accent="teal" style="background:#0D9488" aria-label="Teal"></button>
                                    <button type="button" class="doc-swatch" data-dc-accent="amber" style="background:#D97706" aria-label="Amber"></button>
                                    <button type="button" class="doc-swatch" data-dc-accent="red" style="background:#DC2626" aria-label="Red"></button>
                                    <button type="button" class="doc-swatch" data-dc-accent="slate" style="background:#334155" aria-label="Slate"></button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="doc-switches">
                                <label class="doc-chk"><input type="checkbox" id="dcOptSig" checked> Tri-signature block</label>
                                <label class="doc-chk"><input type="checkbox" id="dcOptTime" checked> Time in signatures</label>
                                <label class="doc-chk"><input type="checkbox" id="dcOptValue"> Declared / insured value</label>
                                <label class="doc-chk"><input type="checkbox" id="dcOptWords" checked> Amount in words</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. PARTIES -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14"/><path d="M9 12h6M9 16h6"/></svg>
                        </div>
                        <div>
                            <h3>From &amp; To Parties</h3>
                            <p>Who is dispatching the goods and who is receiving them at the other end.</p>
                        </div>
                        <span class="qm-step">Step 2</span>
                    </div>
                    <p class="inv-gen-section-title" style="margin-bottom:10px;">Dispatched From (Seller)</p>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Seller / Company Name</label><input type="text" id="dFromName" placeholder="As registered with GST"></div>
                        <div class="inv-gen-field"><label>Seller GSTIN</label><input type="text" id="dFromGst" placeholder="24AAAAA0000A1Z5" maxlength="15"></div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Seller Address</label><input type="text" id="dFromAddr" placeholder="Warehouse / factory address, city, PIN"></div>
                    <div class="inv-gen-field" style="margin-bottom:16px;"><label>Seller State</label><select id="dFromState"></select></div>

                    <p class="inv-gen-section-title" style="margin-bottom:10px;">Delivered To (Consignee)</p>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Consignee Name</label><input type="text" id="dToName" placeholder="Consignee company or person"></div>
                        <div class="inv-gen-field"><label>Consignee GSTIN</label><input type="text" id="dToGst" placeholder="27BBBBB0000B1Z5" maxlength="15"></div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Consignee Address</label><input type="text" id="dToAddr" placeholder="Delivery address, city, state, PIN"></div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>Consignee State</label><select id="dToState"></select></div>
                        <div class="inv-gen-field"><label>Consignee Phone</label><input type="tel" id="dToPhone" placeholder="98765 43210"></div>
                    </div>
                </div>

                <!-- 3. DISPATCH PARTICULARS -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        </div>
                        <div>
                            <h3>Challan &amp; Dispatch Particulars</h3>
                            <p>Number, date, dispatch time and the vehicle actually carrying the goods.</p>
                        </div>
                        <span class="qm-step">Step 3</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label id="dNoLbl">Challan Number</label><input type="text" id="dNo" placeholder="DC-001"></div>
                        <div class="inv-gen-field"><label>Challan Date</label><input type="date" id="dDate"></div>
                    </div>
                    <div class="doc-split" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label>Dispatch Date</label><input type="date" id="dDispDate"></div>
                        <div class="inv-gen-field"><label>Dispatch Time</label><input type="time" id="dDispTime"></div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:14px;">
                        <label>Mode of Transport</label>
                        <select id="dMode">
                            <option value="own">Own Vehicle</option>
                            <option value="transporter">Contract Transporter</option>
                            <option value="courier">Courier / Logistics</option>
                            <option value="hand">Hand Delivery</option>
                        </select>
                    </div>

                    <div class="doc-split" id="dVehRow" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label>Vehicle Number</label><input type="text" id="dVehNo" placeholder="GJ 01 AB 1234"></div>
                        <div class="inv-gen-field"><label>Vehicle Type / Capacity</label><input type="text" id="dVehType" placeholder="10 ft Open Body"></div>
                    </div>
                    <div class="doc-split" id="dDriverRow" style="margin-bottom:14px;">
                        <div class="inv-gen-field"><label>Driver Name</label><input type="text" id="dDriver" placeholder="Driver / handler name"></div>
                        <div class="inv-gen-field"><label>Driver Mobile</label><input type="tel" id="dDriverPhone" placeholder="98765 43210"></div>
                    </div>
                    <div class="doc-split" id="dTransRow" style="margin-bottom:14px; display:none;">
                        <div class="inv-gen-field"><label>Transporter Name</label><input type="text" id="dTransName" placeholder="Transport company"></div>
                        <div class="inv-gen-field"><label>Transporter GSTIN</label><input type="text" id="dTransGst" placeholder="24AAAAA0000A1Z5" maxlength="15"></div>
                    </div>
                    <div class="doc-split" id="dDocRow" style="margin-bottom:14px; display:none;">
                        <div class="inv-gen-field"><label>LR / Consignment Note No.</label><input type="text" id="dLrNo" placeholder="LR-8891"></div>
                        <div class="inv-gen-field">
                            <label>Freight Paid By</label>
                            <select id="dFreight">
                                <option value="Seller">Seller</option>
                                <option value="Consignee">Consignee</option>
                                <option value="Transporter">Transporter (to be recovered)</option>
                            </select>
                        </div>
                    </div>
                    <div class="doc-split" id="dCourierRow" style="margin-bottom:14px; display:none;">
                        <div class="inv-gen-field"><label>Courier / Logistics Name</label><input type="text" id="dCourier" placeholder="Courier name"></div>
                        <div class="inv-gen-field"><label>Docket / AWB Number</label><input type="text" id="dDocket" placeholder="Docket or airway bill no."></div>
                    </div>
                    <div class="doc-split" id="dHandRow" style="margin-bottom:0; display:none;">
                        <div class="inv-gen-field"><label>Delivered By (Name)</label><input type="text" id="dHandBy" placeholder="Name of the person handing over"></div>
                        <div class="inv-gen-field"><label>Gate Entry No.</label><input type="text" id="dGateNo" placeholder="Gate entry number"></div>
                    </div>
                </div>

                <!-- 4. E-WAY BILL / JOB WORK / LEGS -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v6H4z"/><path d="M6 10v10M18 10v10M10 14h4"/></svg>
                        </div>
                        <div>
                            <h3>Format-Specific Blocks</h3>
                            <p>E-way bill details, job work direction and split-dispatch legs appear only for the chosen format.</p>
                        </div>
                        <span class="qm-step">Step 4</span>
                    </div>

                    <div class="inv-gen-section-title" id="dEwayTitle" style="display:none; margin-bottom:10px;">E-Way Bill Particulars</div>
                    <div class="doc-split" id="dEwayBox" style="display:none; margin-bottom:14px;">
                        <div class="inv-gen-field"><label>E-Way Bill Number</label><input type="text" id="dEwayNo" placeholder="121000012345" maxlength="12"></div>
                        <div class="inv-gen-field"><label>E-Way Bill Date &amp; Time</label><input type="date" id="dEwayDate"></div>
                    </div>
                    <div class="doc-split" id="dEwayBox2" style="display:none; margin-bottom:14px;">
                        <div class="inv-gen-field"><label>Attached Tax Invoice No.</label><input type="text" id="dEwayInv" placeholder="INV-001"></div>
                        <div class="inv-gen-field"><label>Distance (km)</label><input type="number" id="dEwayDist" value="0" min="0" step="1"></div>
                    </div>

                    <div class="inv-gen-section-title" id="dJwTitle" style="display:none; margin-bottom:10px;">Job Work Details</div>
                    <div class="doc-split" id="dJwBox" style="display:none; margin-bottom:14px;">
                        <div class="inv-gen-field">
                            <label>Direction</label>
                            <select id="dJwDir">
                                <option value="out">Sent for job work</option>
                                <option value="in">Returned from job work</option>
                            </select>
                        </div>
                        <div class="inv-gen-field"><label>Job Work Challan No.</label><input type="text" id="dJwNo" placeholder="JW-001"></div>
                    </div>
                    <div class="doc-split" id="dJwBox2" style="display:none; margin-bottom:14px;">
                        <div class="inv-gen-field"><label>Job Worker / Subcontractor</label><input type="text" id="dJwParty" placeholder="Name of the job worker"></div>
                        <div class="inv-gen-field"><label>Process Charge (&#8377;)</label><input type="number" id="dJwCharge" value="0" min="0" step="0.01"></div>
                    </div>

                    <div class="inv-gen-section-title" id="dLegTitle" style="display:none; margin-bottom:10px;">Vehicle / Dispatch Legs</div>
                    <div id="dLegBox" style="display:none;">
                        <div class="chk-list" id="dcLegs"></div>
                        <button type="button" class="inv-gen-add-item" onclick="dcAddLeg()">+ Add Vehicle / Leg</button>
                    </div>
                </div>

                <!-- 5. GOODS -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3 8-8"/><path d="M20 12v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h9"/></svg>
                        </div>
                        <div>
                            <h3>Goods Dispatched</h3>
                            <p>Tick a row the moment the consignment is handed over &mdash; unticked rows print as not dispatched.</p>
                        </div>
                        <span class="qm-step">Step 5</span>
                    </div>

                    <div class="chk-list" id="dcItems" style="--doc-cols: 1.7fr .55fr .7fr .75fr .5fr .85fr .8fr auto;"></div>
                    <button type="button" class="inv-gen-add-item" onclick="dcAddRow()">+ Add Item</button>
                </div>

                <!-- 6. VALUE + NOTES -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>
                        </div>
                        <div>
                            <h3>Declared Value &amp; Notes</h3>
                            <p>Optional insurance value plus any instruction the gate or the driver must read.</p>
                        </div>
                        <span class="qm-step">Step 6</span>
                    </div>
                    <div class="doc-split" id="dValueRow" style="display:none; margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Declared Value (&#8377;)</label><input type="number" id="dDeclValue" value="0" min="0" step="0.01"></div>
                        <div class="inv-gen-field"><label>Insured Value (&#8377;)</label><input type="number" id="dInsValue" value="0" min="0" step="0.01"></div>
                    </div>
                    <div class="inv-gen-field"><label>Remarks / Instructions</label><textarea id="dNotes" rows="3" placeholder="Goods received in good condition. Shortage to be reported within 24 hours."></textarea></div>
                </div>

                <!-- ACTIONS -->
                <div class="qm-actions doc-no-print">
                    <button type="button" class="qm-btn qm-btn-primary" onclick="dcPreviewJump()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        Preview
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="dcPrint()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="1"/><path d="M6 17h12v4H6z"/></svg>
                        Print
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="dcReset()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                        Reset
                    </button>
                    <button type="button" class="qm-btn qm-btn-primary" style="flex:2" onclick="dcPDF()">
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
                <div class="inv-gen-preview" id="dcPreview">
                    <div class="doc-sheet doc-sheet-lg" id="dcSheet">

                        <div class="doc-hd">
                            <div class="doc-org">
                                <h2 id="pFrom">Your Business</h2>
                                <p id="pFromAddr">Address</p>
                                <p><strong>GSTIN:</strong> <span id="pFromGst">&mdash;</span></p>
                                <p id="pFromState">State: &mdash;</p>
                            </div>
                            <div class="doc-id">
                                <div class="doc-title" id="pTitle">DELIVERY CHALLAN</div>
                                <p><strong id="pNoLbl">Challan No.:</strong> <span id="pDcNo">&mdash;</span></p>
                                <p><strong>Date:</strong> <span id="pDcDate">&mdash;</span></p>
                                <p id="pJwDirRow" style="display:none"><strong>Direction:</strong> <span id="pJwDir">&mdash;</span></p>
                                <p id="pJwNoRow" style="display:none"><strong>Job Work Challan:</strong> <span id="pJwNo">&mdash;</span></p>
                            </div>
                        </div>

                        <div class="doc-split" style="margin-bottom:12px;">
                            <div>
                                <p class="doc-sec-title">Dispatched From</p>
                                <p style="font-weight:700;margin:3px 0;font-size:11px;" id="pFromName2">&mdash;</p>
                                <p id="pFromAddr2" style="font-size:9.5px;color:#475569;margin:1px 0;">&mdash;</p>
                            </div>
                            <div>
                                <p class="doc-sec-title">Delivered To (Consignee)</p>
                                <p style="font-weight:700;margin:3px 0;font-size:11px;" id="pToName">&mdash;</p>
                                <p id="pToAddr" style="font-size:9.5px;color:#475569;margin:1px 0;">&mdash;</p>
                                <p id="pToGstRow" style="font-size:9.5px;color:#475569;margin:1px 0;"><strong>GSTIN:</strong> <span id="pToGst">&mdash;</span> &middot; <span id="pToPhone">Ph: &mdash;</span></p>
                            </div>
                        </div>

                        <div class="doc-split" style="margin-bottom:12px;gap:10px;">
                            <div style="border:2px solid var(--fmt-accent,#2563EB);border-radius:12px;padding:11px 14px;">
                                <div style="font-size:8.5px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:var(--gray);">Dispatched at</div>
                                <div style="font-size:20px;font-weight:800;color:var(--fmt-accent,#2563EB);line-height:1.25;"><span id="pTimeVal">__:__</span> <span style="font-size:11px;font-weight:700;">hrs</span></div>
                                <div style="font-size:9px;color:var(--gray);">on <span id="pTimeDate">&mdash;</span> &middot; gate out</div>
                            </div>
                            <div style="border:1.5px dashed #94A3B8;border-radius:12px;padding:11px 14px;">
                                <div style="font-size:8.5px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:var(--gray);">Vehicle / Transport No.</div>
                                <div style="font-size:20px;font-weight:800;color:var(--text);line-height:1.25;word-break:break-word;" id="pVehNo">&mdash;</div>
                                <div style="font-size:9px;color:var(--gray);" id="pVehMeta">&mdash;</div>
                            </div>
                        </div>

                        <div class="kv-grid" style="margin-bottom:12px;">
                            <div class="kv-item"><div class="kv-k">Mode of Transport</div><div class="kv-v" id="pMode">&mdash;</div></div>
                            <div class="kv-item"><div class="kv-k">Transporter</div><div class="kv-v" id="pTrans">&mdash;</div></div>
                            <div class="kv-item" id="pEwayKv"><div class="kv-k">E-Way Bill</div><div class="kv-v" id="pEway">&mdash;</div></div>
                        </div>

                        <div id="pLegBox" style="display:none; margin-bottom:12px;">
                            <p class="doc-sec-title">Vehicle / Dispatch Legs</p>
                            <div class="chk-list" id="pLegs"></div>
                        </div>

                        <table style="width:100%;border-collapse:collapse;margin-bottom:12px;font-size:10px;">
                            <thead id="pHead"><tr>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">#</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">Item / Description</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">HSN</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Ord</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Disp</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">UOM</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">Batch / Serial</th>
                            </tr></thead>
                            <tbody id="pRows"><tr><td colspan="7" style="padding:14px;text-align:center;color:#94A3B8;">No items yet</td></tr></tbody>
                        </table>

                        <div id="pTotalsBox" style="display:none; margin-bottom:12px;">
                            <table style="font-size:10px;margin-left:auto;min-width:220px;">
                                <tr><td style="padding:3px 8px;">Taxable Value</td><td id="pTaxable" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pCgstRow" style="display:none"><td style="padding:3px 8px;">CGST</td><td id="pCgst" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pSgstRow" style="display:none"><td style="padding:3px 8px;">SGST</td><td id="pSgst" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pIgstRow" style="display:none"><td style="padding:3px 8px;">IGST</td><td id="pIgst" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr id="pJwRow" style="display:none"><td style="padding:3px 8px;">Process Charge</td><td id="pJwCharge" style="text-align:right;font-weight:600;">&#8377;0.00</td></tr>
                                <tr><td style="padding:6px 8px;font-weight:800;font-size:12px;border-top:2px solid #1E293B;color:var(--fmt-accent,#2563EB);">Challan Value</td><td id="pGrand" style="padding:6px 8px;text-align:right;font-weight:800;font-size:12px;border-top:2px solid #1E293B;color:var(--fmt-accent,#2563EB);">&#8377;0.00</td></tr>
                            </table>
                            <div id="pWordsRow" style="font-size:9px;color:#475569;padding:6px 0;"><strong>Amount in words:</strong> <span id="pWords">Zero Only</span></div>
                        </div>

                        <div class="doc-stats" style="margin-bottom:12px;">
                            <div class="doc-stat"><span>Total Items</span><strong id="pStItems">0</strong></div>
                            <div class="doc-stat"><span>Total Dispatch Qty</span><strong id="pStQty">0</strong></div>
                            <div class="doc-stat amber"><span>Not Dispatched</span><strong id="pStLeft">0</strong></div>
                            <div class="doc-stat grey" id="pStExtraBox" style="display:none;"><span id="pStExtraLbl">Legs</span><strong id="pStExtra">0</strong></div>
                        </div>

                        <div class="note-box gold" id="pEwayNoteBox" style="display:none;padding:9px 12px;margin-bottom:12px;">
                            <div class="note-ico" style="font-size:15px;">&#9888;</div>
                            <p style="margin:0;font-size:9.5px;line-height:1.6;"><strong>E-way bill attached.</strong> This challan is a primary document under the e-way bill rules. Keep it with the invoice and hand a signed copy to the transporter at the gate.</p>
                        </div>

                        <div id="pNotesRow" style="display:none;font-size:9px;color:#475569;padding:6px 0;border-top:1px dashed #E2E8F0;margin-bottom:6px;">
                            <strong>Remarks:</strong> <span id="pNotes"></span>
                        </div>

                        <div class="doc-sig" id="pSigRow">
                            <div>
                                <div class="doc-sig-line">Received in good condition</div>
                                <div style="font-size:8.5px;color:#94A3B8;margin-top:2px;" id="pSigTime1">Consignee &middot; Time: ____</div>
                            </div>
                            <div>
                                <div class="doc-sig-line">Loaded by</div>
                                <div style="font-size:8.5px;color:#94A3B8;margin-top:2px;" id="pSigTime2">Dispatcher &middot; Time: ____</div>
                            </div>
                            <div>
                                <div class="doc-sig-line">Checked by</div>
                                <div style="font-size:8.5px;color:#94A3B8;margin-top:2px;" id="pSigTime3">Gate / Security &middot; Time: ____</div>
                            </div>
                        </div>
                        <div class="doc-ft">This is a computer-generated delivery challan &middot; Generated by <strong>My Metaji</strong></div>
                    </div>
                </div>
                <div class="qm-total-strip">
                    <span id="dcStripLbl">Total Dispatch Qty</span>
                    <strong id="dcStripVal">0</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== FORMAT COMPARISON ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>Which Delivery Challan Format Do You Need?</h2>
        <p>A delivery challan is not a tax invoice. It is a receipt for goods in transit, and the right variant depends entirely on whether you are pricing the goods, moving them against an e-way bill, sending them out for job work or splitting them across vehicles.</p>
        <div class="fmt-table-wrap" style="margin-top:20px;">
            <table class="fmt-table">
                <thead>
                    <tr><th>Format</th><th>Use it when</th><th>Carries value?</th><th>Extra blocks it adds</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Delivery Challan (Unpriced)</strong></td>
                        <td>Goods are moving on sale or transfer and the invoice is raised separately</td>
                        <td><span class="fmt-req no">No value</span></td>
                        <td>Vehicle block, dispatch time, tri-signature</td>
                    </tr>
                    <tr>
                        <td><strong>Priced Delivery Challan</strong></td>
                        <td>You want one document that doubles as the tax invoice</td>
                        <td><span class="fmt-req yes">Rate, GST, total</span></td>
                        <td>CGST/SGST or IGST breakup, amount in words</td>
                    </tr>
                    <tr>
                        <td><strong>E-Way Bill Challan</strong></td>
                        <td>Consignment value crosses the e-way bill threshold</td>
                        <td><span class="fmt-req no">No value</span></td>
                        <td><span class="fmt-req yes">E-way bill no. &amp; date</span> vehicle no., transporter GSTIN, distance</td>
                    </tr>
                    <tr>
                        <td><strong>Job Work / Subcontract Challan</strong></td>
                        <td>Material is sent to a job worker or comes back finished</td>
                        <td><span class="fmt-req cond">Process charge only</span></td>
                        <td>Sent / returned direction, job work challan no., process charge</td>
                    </tr>
                    <tr>
                        <td><strong>Multiple Vehicle / Split Dispatch</strong></td>
                        <td>One order is loaded on two or more vehicles or legs</td>
                        <td><span class="fmt-req no">No value</span></td>
                        <td>Leg-wise vehicle, transporter, LR number table</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="note-box gold" style="margin-top:20px;">
            <div class="note-ico">&#128228;</div>
            <div>
                <h4>Challan, e-way bill and Rule 138 in one minute</h4>
                <ul>
                    <li>A <strong>delivery challan is a primary document</strong> for the e-way bill. The consignment details printed on it must match the e-way bill exactly, or the transporter will be stopped at the check post.</li>
                    <li>An <strong>e-way bill is required</strong> whenever the <strong>consolidated value of the invoice exceeds &#8377;50,000</strong>, and for exempted goods, and for goods sent without an invoice where the value exceeds &#8377;50,000.</li>
                    <li>For goods between <strong>&#8377;50,000 and &#8377;2,00,000</strong>, a consignment note or invoice plus a <strong>permit under Rule 138</strong> of the Goods Taxation (Prevention of Misuse) Rules is sufficient &mdash; no e-way bill portal entry is needed.</li>
                    <li>Above <strong>&#8377;2,00,000</strong> the consignment must be covered by a <strong>transport identity document</strong>: e-way bill, tax invoice or delivery challan, whichever applies, and the transporter must carry a copy.</li>
                    <li>Goods exempt from GST, non-motorised conveyances, and personal luggage weighing up to 20 kg do not need any of these documents.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===================== HOW TO ===================== -->
<section class="gst-faq" style="background:#fff;">
    <div class="gst-wrap">
        <h2>How to Make a Delivery Challan</h2>
        <div class="gst-how-grid">
            <div class="gst-how-card">
                <div class="gst-how-num">1</div>
                <h3>Pick the Variant</h3>
                <p>Unpriced, priced, e-way bill, job work or split dispatch. The form adds the right fields and the preview drops the rest.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">2</div>
                <h3>List the Goods</h3>
                <p>Add every item with HSN, ordered qty, dispatched qty, UOM and batch or serial number. Tick the rows that actually left the premises.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">3</div>
                <h3>Record the Vehicle</h3>
                <p>Own vehicle, transporter, courier or hand delivery. The vehicle number block and the dispatch time print large so the gate copy is unambiguous.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">4</div>
                <h3>Get It Signed</h3>
                <p>Download the PDF or print it. The consignee, the loader and the gate checker each sign with a time &mdash; that is what makes the challan evidence.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SEO + RELATED ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>What is a Delivery Challan?</h2>
        <p>A <strong>delivery challan</strong> is a document that records goods physically leaving a seller&rsquo;s premises and being handed over to a transporter, a courier or the buyer&rsquo;s own vehicle. It is generated at the moment of dispatch &mdash; before the invoice is necessarily settled &mdash; and it acts as a receipt for goods in transit. Unlike a tax invoice it does not have to carry a price. An unpriced challan simply states what left, how many units, in what quantity, under which batch or serial number, on which vehicle, and who received it. That is exactly why most businesses print two copies: one signed by the transporter at the gate and one signed by the consignee on arrival.</p>
        <p>The challan is also the backbone of indirect tax logistics. The <strong>e-way bill rules</strong> treat a delivery challan, a tax invoice and a consignment note as primary documents, and the transporter is expected to carry them for inspection. When a consignment moves between states or crosses a value threshold, the details on the challan are cross-checked against the e-way bill at the check post &mdash; a mismatch in the number of pieces, the vehicle or the consignee is the most common reason a lorry is detained. For goods sent to a job worker, the same challan is reused with the direction reversed so the outward dispatch and the return of finished goods are both traceable, which also feeds directly into your job work costing and the GST treatment of the process charge.</p>

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
        <h2>Stop Writing Challans on Scrap Paper</h2>
        <p><?= BRAND_NAME ?> raises the challan from the same sales order, prints the e-way bill reference, tracks the vehicle in transit and keeps your batch and serial numbers in stock &mdash; automatically.</p>
        <a href="#download" class="gst-cta-btn">Download <?= BRAND_NAME ?> Free &rarr;</a>
    </div>
</section>

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="assets/js/doc-utils.js"></script>
<script>
/* ---------- Format definitions ---------- */
const DC_FORMATS = {
    plain: {
        label: 'Delivery Challan (Unpriced)', tag: 'Goods only, no value', tags: ['Unpriced', 'Most common', 'Dispatch note'],
        ico: '\u{1F4E6}', color: '#2563EB',
        desc: 'The most common challan. It records exactly what left the premises and who received it, without carrying any price, so no GST is charged on it.',
        fields: 'Challan number and date, dispatch date and time, consignee name and address, item description with quantity and UOM, mode of transport and vehicle number',
        title: 'DELIVERY CHALLAN', noLbl: 'Challan No.',
        priced: false, eway: false, jobwork: false, multi: false
    },
    priced: {
        label: 'Priced Delivery Challan', tag: 'Doubles as tax invoice', tags: ['Priced', 'GST breakup', 'Tax invoice'],
        ico: '\u{1F4B8}', color: '#059669',
        desc: 'Adds rate, quantity and GST to the challan so a single printed document can act as the tax invoice for the consignment.',
        fields: 'Every unpriced particular plus rate, dispatch qty, GST rate, CGST/SGST or IGST, total value and amount in words',
        title: 'PRICED DELIVERY CHALLAN', noLbl: 'Challan No.',
        priced: true, eway: false, jobwork: false, multi: false
    },
    eway: {
        label: 'E-Way Bill Challan', tag: 'For e-way bill consignment', tags: ['E-way bill', 'Vehicle no.', 'Transporter GSTIN'],
        ico: '\u{1F69A}', color: '#0D9488',
        desc: 'Carries the e-way bill number, its date, the vehicle, the transporter with GSTIN and the distance covered, and states that the tax invoice is attached.',
        fields: 'Every unpriced particular plus e-way bill number and date, vehicle number, transporter name and GSTIN, distance and attached invoice number',
        title: 'E-WAY BILL CHALLAN', noLbl: 'Challan No.',
        priced: false, eway: true, jobwork: false, multi: false
    },
    jobwork: {
        label: 'Job Work / Subcontract Challan', tag: 'Sent or returned', tags: ['Job work', 'Two-way', 'Process charge'],
        ico: '\u{1F6E0}', color: '#D97706',
        desc: 'For material sent out to a job worker and the same material coming back finished. A direction toggle records whether this is the outward or the return leg.',
        fields: 'Every unpriced particular plus direction (sent for job work / returned from job work), job work challan number, job worker name and process charge',
        title: 'JOB WORK DISPATCH CHALLAN', noLbl: 'Challan No.',
        priced: false, eway: false, jobwork: true, multi: false
    },
    multiple: {
        label: 'Multiple Vehicle / Split Dispatch', tag: 'One challan, many legs', tags: ['Split dispatch', 'Leg table', 'Multi vehicle'],
        ico: '\u{1F69B}', color: '#7C3AED',
        desc: 'When one order is loaded on more than one vehicle or travels in stages. A leg table lists each vehicle, transporter, LR number and driver under a single challan.',
        fields: 'Every unpriced particular plus a leg-wise table of vehicle number, transporter, LR or consignment number, driver and mobile',
        title: 'SPLIT DISPATCH DELIVERY CHALLAN', noLbl: 'Challan No.',
        priced: false, eway: false, jobwork: false, multi: true
    }
};

const DC_COLS_PLAIN = '1.7fr .55fr .7fr .75fr .5fr .85fr .8fr auto';
const DC_COLS_PRICED = '1.25fr .5fr .6fr .65fr .42fr .72fr .7fr .62fr .52fr .8fr auto';

const DC_MODE_LABEL = { own: 'Own Vehicle', transporter: 'Contract Transporter', courier: 'Courier / Logistics', hand: 'Hand Delivery' };

let dcFmt = 'plain';
let dcRowId = 0;
let dcLegId = 0;

/* ---------- Format picker ---------- */
function dcRenderFormats() {
    const grid = document.getElementById('dcFmtGrid');
    let h = '';
    Object.keys(DC_FORMATS).forEach(k => {
        const f = DC_FORMATS[k];
        h += '<button type="button" class="fmt-card' + (k === dcFmt ? ' active' : '') + '" data-dc-fmt="' + k + '" aria-pressed="' + (k === dcFmt) + '">'
            + '<span class="fmt-mini" style="--fmt-accent:' + f.color + '">'
            + '<span class="fmt-mini-top"><span class="fmt-mini-brand"></span><span class="fmt-mini-tag"></span></span>'
            + '<span class="fmt-mini-ln w70"></span><span class="fmt-mini-ln w45"></span><span class="fmt-mini-ln w70"></span>'
            + '<span class="fmt-mini-tot"></span>'
            + '</span>'
            + '<span class="fmt-card-body">'
            + '<span class="fmt-card-title">' + f.ico + ' ' + f.label + '</span>'
            + '<span class="fmt-card-sub">' + f.tag + '</span>'
            + '<span class="fmt-tags">' + f.tags.map(t => '<span class="fmt-tag">' + t + '</span>').join('') + '</span>'
            + '</span></button>';
    });
    grid.innerHTML = h;
    grid.querySelectorAll('[data-dc-fmt]').forEach(b => b.addEventListener('click', () => dcSetFormat(b.dataset.dcFmt)));
    dcSetFormat(dcFmt, true);
}

function dcSetFormat(k, quiet) {
    const f = DC_FORMATS[k];
    if (!f) return;
    dcFmt = k;

    document.querySelectorAll('[data-dc-fmt]').forEach(b => {
        const on = b.dataset.dcFmt === k;
        b.classList.toggle('active', on);
        b.setAttribute('aria-pressed', on);
    });

    document.getElementById('dcInfoIco').textContent = f.ico;
    document.getElementById('dcInfoTitle').textContent = f.label;
    document.getElementById('dcInfoDesc').textContent = f.desc;
    document.getElementById('dcInfoFields').textContent = f.fields;
    document.getElementById('dNoLbl').textContent = f.noLbl;
    document.getElementById('pNoLbl').textContent = f.noLbl + ':';

    const show = (id, on) => { const e = document.getElementById(id); if (e) e.style.display = on ? '' : 'none'; };
    show('dEwayTitle', f.eway); show('dEwayBox', f.eway); show('dEwayBox2', f.eway);
    show('dJwTitle', f.jobwork); show('dJwBox', f.jobwork); show('dJwBox2', f.jobwork);
    show('dLegTitle', f.multi); show('dLegBox', f.multi);

    document.querySelectorAll('[data-dc-price]').forEach(e => { e.style.display = f.priced ? '' : 'none'; });
    document.getElementById('dcItems').style.setProperty('--doc-cols', f.priced ? DC_COLS_PRICED : DC_COLS_PLAIN);

    if (!quiet) dcUpdate();
}

/* ---------- Line items ---------- */
function dcAddRow(seed) {
    seed = seed || {};
    const priced = !!DC_FORMATS[dcFmt].priced;
    const id = dcRowId++;
    const oq = seed.oq !== undefined ? seed.oq : 1;
    const dq = seed.dq !== undefined ? seed.dq : oq;
    const done = seed.done !== undefined ? seed.done : true;
    const esc = v => String(v === undefined || v === null ? '' : v).replace(/"/g, '&quot;');

    document.getElementById('dcItems').insertAdjacentHTML('beforeend',
        '<div class="chk-row' + (done ? ' is-done' : '') + '" data-row="' + id + '" style="--chk-cols:26px 1fr;">'
        + '<button type="button" class="chk-box' + (done ? ' on' : '') + '" onclick="dcToggleRow(' + id + ')" aria-pressed="' + done + '" aria-label="Mark item as dispatched" title="Mark as dispatched">'
        + '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>'
        + '</button>'
        + '<div class="doc-item">'
        + '<input type="text" data-dc-name aria-label="Item description" placeholder="Item description" value="' + esc(seed.name) + '" oninput="dcUpdate()">'
        + '<input type="text" data-dc-hsn aria-label="HSN code" placeholder="HSN" maxlength="8" value="' + esc(seed.hsn) + '" oninput="dcUpdate()">'
        + '<input type="number" data-dc-oq aria-label="Ordered quantity" value="' + oq + '" min="0" step="0.01" oninput="dcUpdate()">'
        + '<input type="number" data-dc-dq aria-label="Dispatched quantity" value="' + dq + '" min="0" step="0.01" oninput="dcUpdate()">'
        + '<input type="text" data-dc-uom aria-label="Unit of measure" placeholder="PCS" maxlength="8" value="' + esc(seed.uom) + '" oninput="dcUpdate()">'
        + '<input type="text" data-dc-batch aria-label="Batch or lot number" placeholder="Batch / Lot" value="' + esc(seed.batch) + '" oninput="dcUpdate()">'
        + '<input type="text" data-dc-serial aria-label="Serial number" placeholder="Serial No." value="' + esc(seed.serial) + '" oninput="dcUpdate()">'
        + '<input type="number" data-dc-rate data-dc-price aria-label="Rate per unit" placeholder="Rate" value="' + (seed.rate || 0) + '" min="0" step="0.01" oninput="dcUpdate()">'
        + '<select data-dc-gst data-dc-price aria-label="GST rate" onchange="dcUpdate()">' + DOC.GST_RATES.map(r => '<option value="' + r + '"' + (String(r) === '18' ? ' selected' : '') + '>' + r + '%</option>').join('') + '</select>'
        + '<span class="doc-item-amt" data-dc-amt aria-label="Line amount">&#8377;0.00</span>'
        + '<button type="button" class="doc-item-del" onclick="dcDelRow(' + id + ')" title="Remove item" aria-label="Remove item">&times;</button>'
        + '</div></div>');
    dcUpdate();
}

function dcDelRow(id) {
    const e = document.querySelector('#dcItems [data-row="' + id + '"]');
    if (e) e.remove();
    dcUpdate();
}

function dcToggleRow(id) {
    const el = document.querySelector('#dcItems [data-row="' + id + '"]');
    if (!el) return;
    const on = !el.classList.contains('is-done');
    el.classList.toggle('is-done', on);
    const b = el.querySelector('.chk-box');
    if (b) { b.classList.toggle('on', on); b.setAttribute('aria-pressed', on ? 'true' : 'false'); }
    dcUpdate();
}

/* ---------- Legs (split dispatch) ---------- */
function dcAddLeg(seed) {
    seed = seed || {};
    const id = dcLegId++;
    const esc = v => String(v === undefined || v === null ? '' : v).replace(/"/g, '&quot;');
    document.getElementById('dcLegs').insertAdjacentHTML('beforeend',
        '<div class="chk-row" data-leg="' + id + '" style="--chk-cols:26px 1fr;">'
        + '<span class="chk-box on" aria-hidden="true"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>'
        + '<div class="doc-item" style="--doc-cols: 1fr 1.25fr .95fr 1fr .85fr auto;">'
        + '<input type="text" data-dg-veh aria-label="Leg vehicle number" placeholder="GJ 01 AB 1234" value="' + esc(seed.veh) + '" oninput="dcUpdate()">'
        + '<input type="text" data-dg-trans aria-label="Leg transporter" placeholder="Transporter" value="' + esc(seed.trans) + '" oninput="dcUpdate()">'
        + '<input type="text" data-dg-lr aria-label="LR or consignment number" placeholder="LR / Doc No." value="' + esc(seed.lr) + '" oninput="dcUpdate()">'
        + '<input type="text" data-dg-drv aria-label="Leg driver name" placeholder="Driver" value="' + esc(seed.drv) + '" oninput="dcUpdate()">'
        + '<input type="text" data-dg-mob aria-label="Leg driver mobile" placeholder="Mobile" value="' + esc(seed.mob) + '" oninput="dcUpdate()">'
        + '<button type="button" class="doc-item-del" onclick="dcDelLeg(' + id + ')" title="Remove leg" aria-label="Remove leg">&times;</button>'
        + '</div></div>');
    dcUpdate();
}

function dcDelLeg(id) {
    const e = document.querySelector('#dcLegs [data-leg="' + id + '"]');
    if (e) e.remove();
    dcUpdate();
}

/* ---------- Helpers ---------- */
function dcTimeLabel(t) {
    if (!t) return '\u2014';
    const p = String(t).split(':');
    let h = parseInt(p[0], 10) || 0;
    const m = p[1] || '00';
    const ap = h >= 12 ? 'PM' : 'AM';
    h = h % 12;
    if (h === 0) h = 12;
    return (h < 10 ? '0' : '') + h + ':' + m + ' ' + ap;
}

function dcModeBlock() {
    const m = DOC.val('dMode') || 'own';
    const show = (id, on) => { const e = document.getElementById(id); if (e) e.style.display = on ? '' : 'none'; };
    show('dVehRow', m === 'own' || m === 'transporter');
    show('dDriverRow', m === 'own' || m === 'transporter');
    show('dTransRow', m === 'transporter');
    show('dDocRow', m === 'transporter');
    show('dCourierRow', m === 'courier');
    show('dHandRow', m === 'hand');
    return m;
}

/* ---------- Collect ---------- */
function dcCollect() {
    const f = DC_FORMATS[dcFmt];
    const priced = !!f.priced;
    const mode = dcModeBlock();
    const inter = DOC.isInterState('dFromState', 'dToState');

    let dispQty = 0, ordQty = 0, notDisp = 0, taxable = 0, tax = 0;
    const rows = [];

    document.querySelectorAll('#dcItems .chk-row[data-row]').forEach((el, i) => {
        const g = s => el.querySelector(s);
        const v = s => { const e = g(s); return e ? String(e.value || '').trim() : ''; };
        const done = el.classList.contains('is-done');
        const oq = parseFloat(v('[data-dc-oq]')) || 0;
        const dq = parseFloat(v('[data-dc-dq]')) || 0;
        const rate = priced ? (parseFloat(v('[data-dc-rate]')) || 0) : 0;
        const gr = priced ? (parseFloat(v('[data-dc-gst]')) || 0) : 0;
        const amt = DOC.round2(dq * rate);
        const tAmt = DOC.round2(amt * gr / 100);
        const r = {
            i: i + 1, done: done, name: v('[data-dc-name]') || 'Item', hsn: v('[data-dc-hsn]'),
            oq: oq, dq: dq, uom: v('[data-dc-uom]'), batch: v('[data-dc-batch]'), serial: v('[data-dc-serial]'),
            rate: rate, gr: gr, amt: amt, tAmt: tAmt, total: DOC.round2(amt + tAmt)
        };
        if (done) { dispQty += dq; ordQty += oq; taxable += r.amt; tax += tAmt; }
        if (!done || dq < oq) notDisp++;
        const cell = g('[data-dc-amt]');
        if (cell) cell.textContent = priced ? DOC.money(r.total) : DOC.qty(dq);
        rows.push(r);
    });

    const legs = [];
    document.querySelectorAll('#dcLegs .chk-row[data-leg]').forEach((el, i) => {
        const g = s => { const e = el.querySelector(s); return e ? String(e.value || '').trim() : ''; };
        const l = { i: i + 1, veh: g('[data-dg-veh]'), trans: g('[data-dg-trans]'), lr: g('[data-dg-lr]'), drv: g('[data-dg-drv]'), mob: g('[data-dg-mob]') };
        if (l.veh || l.trans || l.lr || l.drv) legs.push(l);
    });

    const jwCharge = f.jobwork ? DOC.num('dJwCharge') : 0;
    const taxableR = DOC.round2(taxable);
    const taxR = DOC.round2(tax);
    const grand = DOC.round2(taxableR + taxR + jwCharge);

    const title = f.jobwork
        ? (DOC.val('dJwDir') === 'in' ? 'JOB WORK RETURN CHALLAN' : 'JOB WORK DISPATCH CHALLAN')
        : f.title;

    return {
        f: f, priced: priced, mode: mode, inter: inter, rows: rows, legs: legs,
        dispQty: DOC.round2(dispQty), ordQty: DOC.round2(ordQty), notDisp: notDisp,
        taxable: taxableR, tax: taxR, jwCharge: jwCharge, grand: grand, title: title,
        sig: document.getElementById('dcOptSig').checked,
        sigTime: document.getElementById('dcOptTime').checked,
        words: document.getElementById('dcOptWords').checked
    };
}

/* ---------- Preview render ---------- */
function dcUpdate() {
    const d = dcCollect();

    DOC.text('pFrom', DOC.val('dFromName'));
    DOC.text('pFromAddr', DOC.val('dFromAddr'));
    DOC.text('pFromGst', DOC.val('dFromGst'));
    DOC.text('pFromState', 'State: ' + (DOC.val('dFromState') || '\u2014'));
    DOC.text('pFromName2', DOC.val('dFromName') || '\u2014');
    DOC.text('pFromAddr2', DOC.val('dFromAddr') || '\u2014');

    DOC.text('pTitle', d.title);
    DOC.text('pDcNo', DOC.val('dNo'));
    DOC.text('pDcDate', DOC.fmtDate(DOC.val('dDate')));

    DOC.text('pToName', DOC.val('dToName') || '\u2014');
    DOC.text('pToAddr', DOC.val('dToAddr') || '\u2014');
    DOC.text('pToGst', DOC.val('dToGst') || '\u2014');
    DOC.text('pToPhone', 'Ph: ' + (DOC.val('dToPhone') || '\u2014'));
    DOC.showIf('pToGstRow', !!DOC.val('dToGst') || !!DOC.val('dToPhone'));

    DOC.showIf('pJwDirRow', d.f.jobwork);
    DOC.text('pJwDir', DOC.val('dJwDir') === 'in' ? 'Returned from job work' : 'Sent for job work');
    DOC.showIf('pJwNoRow', d.f.jobwork && !!DOC.val('dJwNo'));
    DOC.text('pJwNo', DOC.val('dJwNo') || '\u2014');

    DOC.text('pTimeVal', dcTimeLabel(DOC.val('dDispTime')));
    DOC.text('pTimeDate', DOC.fmtDate(DOC.val('dDispDate')) || '\u2014');
    DOC.text('pMode', DC_MODE_LABEL[d.mode] || '\u2014');

    const veh = DOC.val('dVehNo');
    DOC.text('pVehNo', veh || (d.mode === 'courier' ? (DOC.val('dDocket') || '\u2014') : (DOC.val('dGateNo') || '\u2014')));
    const metaBits = [];
    if (DOC.val('dVehType')) metaBits.push(DOC.val('dVehType'));
    if (d.mode === 'transporter' && DOC.val('dLrNo')) metaBits.push('LR ' + DOC.val('dLrNo'));
    if (d.mode === 'courier' && DOC.val('dCourier')) metaBits.push(DOC.val('dCourier'));
    if (d.mode === 'hand' && DOC.val('dHandBy')) metaBits.push('By ' + DOC.val('dHandBy'));
    if (DOC.val('dDriver')) metaBits.push(DOC.val('dDriver'));
    DOC.text('pVehMeta', metaBits.length ? metaBits.join(' \u00B7 ') : '\u2014');

    let trans = '\u2014';
    if (d.mode === 'transporter') trans = (DOC.val('dTransName') || '\u2014') + (DOC.val('dTransGst') ? ' (' + DOC.val('dTransGst') + ')' : '');
    else if (d.mode === 'courier') trans = DOC.val('dCourier') || '\u2014';
    else if (d.mode === 'own') trans = 'Own fleet';
    else trans = 'Hand delivery';
    DOC.text('pTrans', trans);

    DOC.showIf('pEwayKv', d.f.eway);
    DOC.text('pEway', d.f.eway ? ((DOC.val('dEwayNo') || '\u2014') + (DOC.val('dEwayDist') ? ' \u00B7 ' + DOC.qty(DOC.num('dEwayDist')) + ' km' : '')) : '\u2014');
    DOC.showIf('pEwayNoteBox', d.f.eway);

    DOC.showIf('pLegBox', d.f.multi);
    if (d.f.multi) {
        const box = document.getElementById('pLegs');
        box.innerHTML = d.legs.length ? d.legs.map(l =>
            '<div class="chk-row" style="--chk-cols:20px 1.2fr 1fr .8fr;">'
            + '<span style="color:#059669;font-weight:800;">\u2713</span>'
            + '<span class="chk-txt" style="white-space:normal;"><strong>' + (l.veh || '\u2014') + '</strong> \u00B7 ' + (l.trans || '\u2014') + '</span>'
            + '<span class="chk-txt" style="white-space:normal;">' + (l.lr || '\u2014') + '</span>'
            + '<span class="chk-txt" style="white-space:normal;text-align:right;">' + (l.drv || '\u2014') + '</span>'
            + '</div>').join('')
            : '<div class="chk-txt" style="color:#94A3B8;">No legs added yet</div>';
    }

    const head = document.getElementById('pHead');
    const th = (t, r) => '<th style="background:#334155;color:#fff;padding:5px 6px;font-size:9px;text-align:' + (r ? 'right' : 'left') + ';">' + t + '</th>';
    head.innerHTML = '<tr>' + th('#') + th('Item / Description') + th('HSN')
        + (d.priced ? th('Qty', true) + th('UOM') + th('Rate', true) + th('GST%', true) + th('Taxable', true) + th('Tax', true) + th('Total', true)
            : th('Ord', true) + th('Disp', true) + th('UOM') + th('Batch / Serial'))
        + '</tr>';

    const tb = document.getElementById('pRows');
    const cols = d.priced ? 10 : 7;
    if (!d.rows.length) {
        tb.innerHTML = '<tr><td colspan="' + cols + '" style="padding:14px;text-align:center;color:#94A3B8;">No items yet</td></tr>';
    } else {
        tb.innerHTML = d.rows.map(r => {
            const st = r.done ? '' : 'text-decoration:line-through;color:#94A3B8;';
            let tds = '<td style="' + st + '">' + r.i + '</td>';
            tds += '<td style="' + st + '">' + r.name + '</td>';
            tds += '<td style="' + st + '">' + (r.hsn || '\u2014') + '</td>';
            if (d.priced) {
                tds += '<td style="text-align:right;' + st + '">' + DOC.qty(r.dq) + '</td>';
                tds += '<td style="' + st + '">' + (r.uom || '\u2014') + '</td>';
                tds += '<td style="text-align:right;' + st + '">' + DOC.money(r.rate) + '</td>';
                tds += '<td style="text-align:right;' + st + '">' + r.gr + '%</td>';
                tds += '<td style="text-align:right;' + st + '">' + DOC.money(r.amt) + '</td>';
                tds += '<td style="text-align:right;' + st + '">' + DOC.money(r.tAmt) + '</td>';
                tds += '<td style="text-align:right;font-weight:700;' + st + '">' + DOC.money(r.total) + '</td>';
            } else {
                tds += '<td style="text-align:right;' + st + '">' + DOC.qty(r.oq) + '</td>';
                tds += '<td style="text-align:right;font-weight:700;' + st + '">' + DOC.qty(r.dq) + '</td>';
                tds += '<td style="' + st + '">' + (r.uom || '\u2014') + '</td>';
                tds += '<td style="' + st + '">' + ((r.batch || '') + (r.serial ? ' / ' + r.serial : '') || '\u2014') + '</td>';
            }
            return '<tr>' + tds + '</tr>';
        }).join('');
    }

    DOC.showIf('pTotalsBox', d.priced || d.f.jobwork);
    if (d.priced) {
        DOC.text('pTaxable', DOC.money(d.taxable));
        if (d.inter) {
            DOC.showIf('pCgstRow', false); DOC.showIf('pSgstRow', false);
            DOC.showIf('pIgstRow', true); DOC.text('pIgst', DOC.money(d.tax));
        } else {
            DOC.showIf('pIgstRow', false);
            DOC.showIf('pCgstRow', true); DOC.text('pCgst', DOC.money(DOC.round2(d.tax / 2)));
            DOC.showIf('pSgstRow', true); DOC.text('pSgst', DOC.money(DOC.round2(d.tax - DOC.round2(d.tax / 2))));
        }
    } else {
        DOC.showIf('pCgstRow', false); DOC.showIf('pSgstRow', false); DOC.showIf('pIgstRow', false);
        DOC.text('pTaxable', DOC.money(d.taxable));
    }
    DOC.showIf('pJwRow', d.f.jobwork);
    DOC.text('pJwCharge', DOC.money(d.jwCharge));
    DOC.text('pGrand', DOC.money(d.grand));
    DOC.showIf('pWordsRow', d.words && d.grand > 0);
    if (d.words) document.getElementById('pWords').textContent = DOC.inWords(d.grand);

    document.getElementById('pStItems').textContent = d.rows.length;
    document.getElementById('pStQty').textContent = DOC.qty(d.dispQty);
    document.getElementById('pStLeft').textContent = d.notDisp;
    const extraOn = d.f.multi || d.priced;
    DOC.showIf('pStExtraBox', extraOn);
    if (extraOn) {
        document.getElementById('pStExtraLbl').textContent = d.f.multi ? 'Vehicle / Legs' : 'Challan Value';
        document.getElementById('pStExtra').textContent = d.f.multi ? d.legs.length : DOC.money(d.grand);
    }

    DOC.showIf('dValueRow', document.getElementById('dcOptValue').checked);

    const notes = DOC.val('dNotes');
    DOC.showIf('pNotesRow', !!notes);
    if (notes) document.getElementById('pNotes').textContent = notes;

    DOC.showIf('pSigRow', d.sig);
    if (d.sig) {
        const tm = d.sigTime ? (dcTimeLabel(DOC.val('dDispTime'))) : '____';
        document.getElementById('pSigTime1').textContent = 'Consignee \u00B7 Time: ' + tm;
        document.getElementById('pSigTime2').textContent = 'Dispatcher \u00B7 Time: ' + tm;
        document.getElementById('pSigTime3').textContent = 'Gate / Security \u00B7 Time: ' + tm;
    }

    document.getElementById('dcStripLbl').textContent = d.priced ? 'Challan Value' : 'Total Dispatch Qty';
    document.getElementById('dcStripVal').textContent = d.priced ? DOC.money(d.grand) : DOC.qty(d.dispQty);
}

/* ---------- Actions ---------- */
function dcPreviewJump() { dcUpdate(); DOC.scrollToPreview('dcPreview'); }
function dcPrint() { dcUpdate(); window.print(); }

function dcReset() {
    ['dNo', 'dFromName', 'dFromGst', 'dFromAddr', 'dToName', 'dToGst', 'dToAddr', 'dToPhone',
        'dVehNo', 'dVehType', 'dDriver', 'dDriverPhone', 'dTransName', 'dTransGst', 'dLrNo',
        'dCourier', 'dDocket', 'dHandBy', 'dGateNo', 'dEwayNo', 'dEwayInv', 'dJwNo', 'dJwParty',
        'dDeclValue', 'dInsValue', 'dNotes'].forEach(id => { const e = document.getElementById(id); if (e) e.value = ''; });
    ['dEwayDist', 'dJwCharge', 'dDeclValue', 'dInsValue'].forEach(id => { const e = document.getElementById(id); if (e) e.value = 0; });
    document.getElementById('dMode').value = 'own';
    document.getElementById('dJwDir').value = 'out';
    document.getElementById('dFreight').value = 'Seller';
    document.getElementById('dFromState').value = '';
    document.getElementById('dToState').value = '';
    document.getElementById('dDate').value = DOC.todayISO();
    document.getElementById('dDispDate').value = DOC.todayISO();
    document.getElementById('dEwayDate').value = DOC.todayISO();
    document.getElementById('dDispTime').value = '';
    DOC.setToggle('dcOptSig', true);
    DOC.setToggle('dcOptTime', true);
    DOC.setToggle('dcOptValue', false);
    DOC.setToggle('dcOptWords', true);
    document.getElementById('dcItems').querySelectorAll('.chk-row').forEach(e => e.remove());
    document.getElementById('dcLegs').querySelectorAll('.chk-row').forEach(e => e.remove());
    dcAddRow();
    dcAddLeg();
    dcSetFormat('plain');
    dcUpdate();
}

/* ---------- PDF ---------- */
function dcPDF() {
    dcUpdate();
    const d = dcCollect();
    const doc = DOC.newDoc();
    if (!doc) return;

    const W = doc.internal.pageSize.getWidth();
    const MG = 14;
    const accent = DOC.currentAccentRGB();

    const y0 = DOC.pdfHeader(doc, {
        org: DOC.val('dFromName') || 'Your Business',
        left: [
            { value: DOC.val('dFromAddr') || 'Address' },
            { value: 'GSTIN: ' + (DOC.val('dFromGst') || '\u2014') + (DOC.val('dFromState') ? '  \u00B7  ' + DOC.val('dFromState') : '') }
        ],
        title: d.title,
        right: [
            { label: d.f.noLbl, value: DOC.val('dNo') || '\u2014' },
            { label: 'Date', value: DOC.fmtDate(DOC.val('dDate')) || '\u2014' },
            { label: 'Dispatch', value: (DOC.fmtDate(DOC.val('dDispDate')) || '\u2014') + ' ' + dcTimeLabel(DOC.val('dDispTime')) }
        ],
        accent: accent
    });

    let y = y0;
    y = DOC.pdfSection(doc, MG, y, 'DISPATCHED FROM / DELIVERED TO', W - MG * 2);
    doc.setFontSize(8.5);
    doc.setFont('helvetica', 'bold'); doc.setTextColor(15, 23, 42);
    doc.text(DOC.val('dFromName') || 'Seller', MG, y); y += 4.2;
    doc.setFont('helvetica', 'normal'); doc.setTextColor(51, 65, 85);
    let lines = doc.splitTextToSize(DOC.val('dFromAddr') || '', 88);
    doc.text(lines, MG, y); y += lines.length * 3.4;

    const cx = MG + 96;
    let cy = y0 - 1.2;
    doc.setFont('helvetica', 'bold'); doc.setFontSize(8.5); doc.setTextColor(15, 23, 42);
    doc.text(DOC.val('dToName') || 'Consignee', cx, cy); cy += 4.2;
    doc.setFont('helvetica', 'normal'); doc.setTextColor(51, 65, 85);
    lines = doc.splitTextToSize(DOC.val('dToAddr') || '', 82);
    doc.text(lines, cx, cy); cy += lines.length * 3.4;
    doc.text('GSTIN: ' + (DOC.val('dToGst') || '\u2014') + '  \u00B7  Ph: ' + (DOC.val('dToPhone') || '\u2014'), cx, cy); cy += 4;
    y = Math.max(y, cy) + 2;

    y = DOC.pdfSection(doc, MG, y, 'DISPATCH & TRANSPORT DETAILS', W - MG * 2);
    const meta = [
        'Mode of transport: ' + (DC_MODE_LABEL[d.mode] || '\u2014'),
        'Vehicle No.: ' + (DOC.val('dVehNo') || '\u2014') + (DOC.val('dVehType') ? '  \u00B7  ' + DOC.val('dVehType') : ''),
        'Driver: ' + (DOC.val('dDriver') || '\u2014') + (DOC.val('dDriverPhone') ? '  \u00B7  ' + DOC.val('dDriverPhone') : ''),
        'Transporter: ' + (d.mode === 'transporter' ? ((DOC.val('dTransName') || '\u2014') + (DOC.val('dTransGst') ? '  \u00B7  GSTIN ' + DOC.val('dTransGst') : '')) : (d.mode === 'courier' ? (DOC.val('dCourier') || '\u2014') : (d.mode === 'hand' ? (DOC.val('dHandBy') || '\u2014') : 'Own fleet'))),
        'LR / Docket: ' + (DOC.val('dLrNo') || DOC.val('dDocket') || DOC.val('dGateNo') || '\u2014') + (DOC.val('dFreight') ? '  \u00B7  Freight paid by ' + DOC.val('dFreight') : '')
    ];
    doc.setFontSize(8); doc.setTextColor(51, 65, 85);
    meta.forEach(t => { doc.text(t, MG, y); y += 4; });

    doc.setFont('helvetica', 'bold');
    doc.text('Dispatched at ' + dcTimeLabel(DOC.val('dDispTime')) + ' hrs on ' + (DOC.fmtDate(DOC.val('dDispDate')) || '\u2014'), MG, y + 1.5);
    doc.setFont('helvetica', 'normal');
    y += 7;

    if (d.f.eway) {
        y = DOC.pdfSection(doc, MG, y, 'E-WAY BILL PARTICULARS', W - MG * 2);
        doc.setFontSize(8);
        doc.text('E-Way Bill No.: ' + (DOC.val('dEwayNo') || '\u2014') + '  \u00B7  Date: ' + (DOC.fmtDate(DOC.val('dEwayDate')) || '\u2014'), MG, y); y += 4;
        doc.text('Vehicle No.: ' + (DOC.val('dVehNo') || '\u2014') + '  \u00B7  Distance: ' + DOC.qty(DOC.num('dEwayDist')) + ' km', MG, y); y += 4;
        doc.setFont('helvetica', 'bold'); doc.setTextColor(180, 83, 9);
        doc.text('Tax invoice attached: Yes  \u00B7  Invoice No.: ' + (DOC.val('dEwayInv') || '\u2014') + '. This challan is a primary document for the e-way bill.', MG, y); y += 4.5;
        doc.setFont('helvetica', 'normal'); doc.setTextColor(51, 65, 85);
    }

    if (d.f.jobwork) {
        y = DOC.pdfSection(doc, MG, y, 'JOB WORK DETAILS', W - MG * 2);
        doc.setFontSize(8);
        doc.text('Direction: ' + (DOC.val('dJwDir') === 'in' ? 'Returned from job work' : 'Sent for job work') + '  \u00B7  Job Work Challan No.: ' + (DOC.val('dJwNo') || '\u2014'), MG, y); y += 4;
        doc.text('Job Worker / Subcontractor: ' + (DOC.val('dJwParty') || '\u2014') + '  \u00B7  Process Charge: ' + DOC.money(d.jwCharge), MG, y); y += 5;
    }

    if (d.f.multi) {
        y = DOC.pdfSection(doc, MG, y, 'VEHICLE / DISPATCH LEGS', W - MG * 2);
        y = DOC.pdfTable(doc,
            ['#', 'Vehicle No.', 'Transporter', 'LR / Doc No.', 'Driver', 'Mobile'],
            d.legs.length ? d.legs.map(l => [l.i, l.veh || '\u2014', l.trans || '\u2014', l.lr || '\u2014', l.drv || '\u2014', l.mob || '\u2014'])
                : [['', 'No legs recorded', '', '', '', '']],
            { startY: y, accent: accent, extra: { columnStyles: { 0: { cellWidth: 8 }, 1: { fontStyle: 'bold' } } } }) + 5;
    }

    const head = d.priced
        ? ['#', 'Description', 'HSN', 'Qty', 'UOM', 'Rate', 'GST%', 'Taxable', 'Tax', 'Total']
        : ['#', 'Description', 'HSN', 'Ord Qty', 'Disp Qty', 'UOM', 'Batch / Lot', 'Serial No.'];
    const body = d.rows.length ? d.rows.map(r => d.priced
        ? [r.i, r.name, r.hsn || '\u2014', DOC.qty(r.dq), r.uom || '\u2014', DOC.money(r.rate), r.gr + '%', DOC.money(r.amt), DOC.money(r.tAmt), DOC.money(r.total)]
        : [r.i, r.name + (r.done ? '' : '  (not dispatched)'), r.hsn || '\u2014', DOC.qty(r.oq), DOC.qty(r.dq), r.uom || '\u2014', r.batch || '\u2014', r.serial || '\u2014'])
        : [['', 'No items', '', '', '', '', '', '', '', ''].slice(0, d.priced ? 10 : 8)];

    y = DOC.pdfSection(doc, MG, y, 'GOODS DISPATCHED', W - MG * 2);
    const colStyles = d.priced
        ? { 0: { cellWidth: 8 }, 3: { halign: 'right' }, 5: { halign: 'right' }, 6: { halign: 'right' }, 7: { halign: 'right' }, 8: { halign: 'right' }, 9: { halign: 'right', fontStyle: 'bold' } }
        : { 0: { cellWidth: 8 }, 3: { halign: 'right' }, 4: { halign: 'right', fontStyle: 'bold' } };
    y = DOC.pdfTable(doc, head, body, { startY: y, accent: accent, extra: { columnStyles: colStyles } }) + 5;

    const tRows = [];
    if (d.priced) {
        tRows.push({ label: 'Taxable Value', value: DOC.money(d.taxable) });
        if (d.inter) {
            tRows.push({ label: 'IGST', value: DOC.money(d.tax) });
        } else {
            tRows.push({ label: 'CGST', value: DOC.money(DOC.round2(d.tax / 2)) });
            tRows.push({ label: 'SGST', value: DOC.money(DOC.round2(d.tax - DOC.round2(d.tax / 2))) });
        }
    }
    if (d.f.jobwork) tRows.push({ label: 'Process Charge', value: DOC.money(d.jwCharge) });
    if (DOC.val('dDeclValue') || DOC.val('dInsValue')) {
        tRows.push({ label: 'Declared Value', value: DOC.money(DOC.num('dDeclValue')) });
        tRows.push({ label: 'Insured Value', value: DOC.money(DOC.num('dInsValue')) });
    }
    tRows.push({ label: 'Total Items Dispatched', value: String(d.rows.length) });
    tRows.push({ label: 'Total Dispatch Qty', value: DOC.qty(d.dispQty) });
    tRows.push({ label: 'Items Not Dispatched', value: String(d.notDisp) });
    tRows.push({ rule: true, gap: 2 });
    tRows.push({ label: d.priced ? 'Challan Value' : 'Total Particulars', value: d.priced ? DOC.money(d.grand) : String(d.rows.length), bold: true, size: 10.5, color: accent, gap: 5 });
    y = DOC.pdfTotals(doc, W, y, tRows);

    if (d.words && d.grand > 0) {
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Amount in words: ' + DOC.inWords(d.grand), MG, y); y += 5.5;
        doc.setFont('helvetica', 'normal');
    }
    if (DOC.val('dNotes')) {
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Remarks:', MG, y); y += 4;
        doc.setFont('helvetica', 'normal');
        const nl = doc.splitTextToSize(DOC.val('dNotes'), W - MG * 2);
        doc.text(nl, MG, y); y += nl.length * 3.6 + 2;
    }

    if (d.sig) {
        if (y > doc.internal.pageSize.getHeight() - 34) { doc.addPage(); y = 20; }
        const tm = d.sigTime ? dcTimeLabel(DOC.val('dDispTime')) : '____';
        const colW = (W - MG * 2) / 3;
        const sy = y + 13;
        doc.setDrawColor(148, 163, 184); doc.setLineWidth(0.3);
        [0, 1, 2].forEach(i => {
            const x = MG + colW * i + colW / 2;
            doc.line(x - 29, sy, x + 29, sy);
        });
        doc.setFontSize(7.5); doc.setTextColor(100, 116, 139);
        doc.text('Received in good condition', MG + colW * 0.5, sy + 4, { align: 'center' });
        doc.text('Consignee \u00B7 Time: ' + tm, MG + colW * 0.5, sy + 8, { align: 'center' });
        doc.text('Loaded by', MG + colW * 1.5, sy + 4, { align: 'center' });
        doc.text('Dispatcher \u00B7 Time: ' + tm, MG + colW * 1.5, sy + 8, { align: 'center' });
        doc.text('Checked by', MG + colW * 2.5, sy + 4, { align: 'center' });
        doc.text('Gate / Security \u00B7 Time: ' + tm, MG + colW * 2.5, sy + 8, { align: 'center' });
        y = sy + 14;
    }

    DOC.pdfFooter(doc, y, (DOC.val('dNo') || 'delivery challan') + (d.f.eway && DOC.val('dEwayNo') ? '  \u00B7  E-Way Bill ' + DOC.val('dEwayNo') : ''));
    doc.save(((DOC.val('dNo') || 'delivery-challan').replace(/[^\w\-]+/g, '_')) + '.pdf');
}

/* ---------- Boot ---------- */
(function () {
    DOC.fillStates('dFromState', 'Select state');
    DOC.fillStates('dToState', 'Select state');
    document.getElementById('dDate').value = DOC.todayISO();
    document.getElementById('dDispDate').value = DOC.todayISO();
    document.getElementById('dEwayDate').value = DOC.todayISO();
    dcRenderFormats();
    dcAddRow();
    dcAddLeg();

    const ids = ['dNo', 'dDate', 'dDispDate', 'dDispTime', 'dMode', 'dVehNo', 'dVehType', 'dDriver', 'dDriverPhone',
        'dTransName', 'dTransGst', 'dLrNo', 'dFreight', 'dCourier', 'dDocket', 'dHandBy', 'dGateNo',
        'dFromName', 'dFromGst', 'dFromAddr', 'dFromState', 'dToName', 'dToGst', 'dToAddr', 'dToState', 'dToPhone',
        'dEwayNo', 'dEwayDate', 'dEwayInv', 'dEwayDist',
        'dJwDir', 'dJwNo', 'dJwParty', 'dJwCharge',
        'dDeclValue', 'dInsValue', 'dNotes',
        'dcOptSig', 'dcOptTime', 'dcOptValue', 'dcOptWords'];
    DOC.bind(ids, dcUpdate);

    DOC.bindSwatches('data-dc-accent', dcUpdate);

    dcUpdate();
})();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
