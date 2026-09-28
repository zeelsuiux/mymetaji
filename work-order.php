<?php
/**
 * My Metaji - Work Order Format
 * Raise a job card for manufacturing, maintenance, repair, job work or installation, track the stages and download it as a PDF.
 */
require_once __DIR__ . '/config.php';
$page_title = 'Free Work Order Format — Job Card & Service Worksheet | ' . BRAND_NAME;
$page_desc  = 'Create a free work order format as a job card. Manufacturing, maintenance, repair, job work or installation work order with stage tracking and PDF.';
include __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="gst-hero">
    <div class="gst-wrap">
        <div class="gst-hero-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l2 2 4-4"/><path d="M14 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"/></svg>
            Job card &middot; Stage tracking &middot; Instant PDF
        </div>
        <h1>Free <span>Work Order</span></h1>
        <p>A work order is the job card that travels with the work through the shop floor or the service van. Pick manufacturing, maintenance, repair, job work or installation and download a print-ready sheet with stages, materials, labour and sign-off.</p>
        <div class="gst-hero-chips">
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
                5 work order types
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6L9 17l-5-5"/></svg>
                Tickable stage checklist
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 3v18M7 7h7a3 3 0 0 1 0 6H7"/></svg>
                Material &amp; labour costing
            </span>
        </div>
    </div>
</section>

<!-- ===================== TOOL ===================== -->
<section class="gst-tool" id="work-order-tool">
    <div class="gst-wrap">
        <div class="qm-layout">

            <!-- ===== LEFT: FORM ===== -->
            <div>

                <!-- 1. TYPE PICKER -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>
                        </div>
                        <div>
                            <h3>Choose Your Work Order Type</h3>
                            <p>The type decides which details, which stage checklist and which costing blocks print.</p>
                        </div>
                        <span class="qm-step">Step 1</span>
                    </div>

                    <div class="fmt-grid" id="woFmtGrid" role="group" aria-label="Work order type"></div>

                    <div class="note-box gold" style="margin-top:16px;">
                        <div class="note-ico" id="woInfoIco">&#127981;</div>
                        <div>
                            <h4 id="woInfoTitle">Manufacturing Work Order</h4>
                            <p id="woInfoDesc">A job card issued to the shop floor for production. It carries the batch and drawing reference, records material issued against the job and closes only after quality check.</p>
                            <p style="margin:0;"><strong>Key particulars:</strong> <span id="woInfoFields">Work order number and date, customer, job description, priority, expected start and completion, batch and drawing number, stage checklist, materials and labour</span></p>
                        </div>
                    </div>

                    <div class="doc-split" style="margin-top:18px;">
                        <div>
                            <div class="doc-opt">
                                <span>Accent colour</span>
                                <div class="doc-swatches">
                                    <button type="button" class="doc-swatch active" data-wo-accent="blue" style="background:#2563EB" aria-label="Blue"></button>
                                    <button type="button" class="doc-swatch" data-wo-accent="indigo" style="background:#4F46E5" aria-label="Indigo"></button>
                                    <button type="button" class="doc-swatch" data-wo-accent="green" style="background:#059669" aria-label="Green"></button>
                                    <button type="button" class="doc-swatch" data-wo-accent="teal" style="background:#0D9488" aria-label="Teal"></button>
                                    <button type="button" class="doc-swatch" data-wo-accent="amber" style="background:#D97706" aria-label="Amber"></button>
                                    <button type="button" class="doc-swatch" data-wo-accent="violet" style="background:#7C3AED" aria-label="Violet"></button>
                                    <button type="button" class="doc-swatch" data-wo-accent="slate" style="background:#334155" aria-label="Slate"></button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="doc-switches">
                                <label class="doc-chk"><input type="checkbox" id="woOptStatus" checked> Job status</label>
                                <label class="doc-chk"><input type="checkbox" id="woOptCheck" checked> Stage checklist</label>
                                <label class="doc-chk"><input type="checkbox" id="woOptAppr" checked> Approval &amp; sign-off</label>
                                <label class="doc-chk"><input type="checkbox" id="woOptEst" checked> Estimate &amp; variance</label>
                                <label class="doc-chk"><input type="checkbox" id="woOptGst" checked> GST note</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. WORKSHOP -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14"/><path d="M10 12h4M10 16h4"/></svg>
                        </div>
                        <div>
                            <h3>Workshop / Service Provider</h3>
                            <p>Who is issuing the work order &mdash; printed at the top of the job card.</p>
                        </div>
                        <span class="qm-step">Step 2</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Company / Unit Name</label><input type="text" id="wCoName" placeholder="As registered with GST"></div>
                        <div class="inv-gen-field"><label>GSTIN</label><input type="text" id="wCoGst" placeholder="24AAAAA0000A1Z5" maxlength="15"></div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Workshop / Site Address</label><input type="text" id="wCoAddr" placeholder="Unit, street, city, state, PIN"></div>
                    <div class="inv-gen-field"><label>Phone</label><input type="tel" id="wCoPhone" placeholder="98765 43210"></div>
                </div>

                <!-- 3. WORK ORDER + CUSTOMER -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                        </div>
                        <div>
                            <h3>Work Order &amp; Customer</h3>
                            <p>Who asked for the work, what the job is and when it is due.</p>
                        </div>
                        <span class="qm-step">Step 3</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Work Order Number</label><input type="text" id="wNo" placeholder="WO-001"></div>
                        <div class="inv-gen-field"><label>Work Order Date</label><input type="date" id="wDate"></div>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Customer / Client Name</label><input type="text" id="wClient" placeholder="Customer or company name"></div>
                        <div class="inv-gen-field"><label>Customer Phone</label><input type="tel" id="wPhone" placeholder="98765 43210"></div>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Job Title</label><input type="text" id="wTitle" placeholder="e.g. Annual maintenance of 5-ton AC unit"></div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Job Description / Scope of Work</label><textarea id="wDesc" rows="3" placeholder="Describe the work to be carried out, the specification and anything excluded."></textarea></div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Site Address</label><input type="text" id="wAddr" placeholder="Where the work is to be carried out"></div>
                    <div class="doc-split" id="wSiteRow" style="display:none; margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Site / Project Type</label>
                            <select id="wSiteType">
                                <option value="Residential">Residential</option>
                                <option value="Commercial">Commercial</option>
                                <option value="Industrial">Industrial</option>
                                <option value="Retail Outlet">Retail Outlet</option>
                                <option value="Site Office">Site Office</option>
                            </select>
                        </div>
                        <div class="inv-gen-field"><label>Installation Team</label><input type="text" id="wTeam" placeholder="Team / technician names"></div>
                    </div>
                    <div class="doc-split">
                        <div class="inv-gen-field">
                            <label>Priority</label>
                            <select id="wPriority">
                                <option value="Low">Low</option>
                                <option value="Normal" selected>Normal</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="inv-gen-field"><label>Expected Start</label><input type="date" id="wStart"></div>
                    </div>
                    <div class="doc-split" style="margin-top:10px;">
                        <div class="inv-gen-field"><label>Expected Completion</label><input type="date" id="wEnd"></div>
                        <div class="inv-gen-field"><label>Estimated Job Cost (&#8377;)</label><input type="number" id="wEstimate" value="0" min="0" step="0.01"></div>
                    </div>
                </div>

                <!-- 4. TYPE-SPECIFIC DETAILS -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.9 6.3 6.9.8-5.1 4.7 1.4 6.8L12 17.3 5.9 20.6l1.4-6.8L2.2 9.1l6.9-.8z"/></svg>
                        </div>
                        <div>
                            <h3>Type-Specific Details</h3>
                            <p>Only the block that matches your chosen work order type is shown.</p>
                        </div>
                        <span class="qm-step">Step 4</span>
                    </div>

                    <div id="wManuBox" style="display:none;">
                        <div class="doc-split" style="margin-bottom:10px;">
                            <div class="inv-gen-field"><label>Batch / Lot Number</label><input type="text" id="wBatch" placeholder="B-2026-014"></div>
                            <div class="inv-gen-field"><label>Drawing Number</label><input type="text" id="wDrawing" placeholder="DRG-2210 rev B"></div>
                        </div>
                        <div class="doc-split" style="margin-bottom:10px;">
                            <div class="inv-gen-field"><label>Material Issued (Qty &amp; UOM)</label><input type="text" id="wMaterial" placeholder="e.g. 120 kg MS plate"></div>
                            <div class="inv-gen-field"><label>Machine / Work Station</label><input type="text" id="wMachine" placeholder="Press line 2"></div>
                        </div>
                        <div class="inv-gen-field"><label>Shop Floor Instructions</label><textarea id="wManuNote" rows="2" placeholder="Tolerance, finish, inspection points."></textarea></div>
                    </div>

                    <div id="wMaintBox" style="display:none;">
                        <div class="doc-split" style="margin-bottom:10px;">
                            <div class="inv-gen-field"><label>Complaint / Service No.</label><input type="text" id="wComplaint" placeholder="CMP-1180"></div>
                            <div class="inv-gen-field"><label>Reported By</label><input type="text" id="wReported" placeholder="Name of person reporting"></div>
                        </div>
                        <div class="doc-split" style="margin-bottom:10px;">
                            <div class="inv-gen-field"><label>Technician</label><input type="text" id="wTech" placeholder="Assigned technician"></div>
                            <div class="inv-gen-field"><label>Visit Date</label><input type="date" id="wVisit"></div>
                        </div>
                        <div class="inv-gen-field" style="margin-bottom:10px;"><label>Fault Description</label><textarea id="wFault" rows="2" placeholder="Complaint as reported on site."></textarea></div>
                        <div class="inv-gen-field" style="margin-bottom:10px;"><label>Resolution / Work Carried Out</label><textarea id="wResolution" rows="2" placeholder="What was actually done to make the equipment functional."></textarea></div>
                        <div class="inv-gen-field"><label>Next Service Due</label><input type="date" id="wNext"></div>
                    </div>

                    <div id="wRepairBox" style="display:none;">
                        <div class="doc-split" style="margin-bottom:10px;">
                            <div class="inv-gen-field"><label>Device / Item Repaired</label><input type="text" id="wDevice" placeholder="Make, model, serial no."></div>
                            <div class="inv-gen-field"><label>Warranty Claim</label>
                                <select id="wWarranty">
                                    <option value="No">Not under warranty</option>
                                    <option value="Yes">Under warranty</option>
                                </select>
                            </div>
                        </div>
                        <div class="doc-split" style="margin-bottom:10px;">
                            <div class="inv-gen-field"><label>Claim / Ticket No.</label><input type="text" id="wClaimNo" placeholder="Claim number"></div>
                            <div class="inv-gen-field"><label>Total Labour Hours</label><input type="number" id="wHours" value="0" min="0" step="0.25"></div>
                        </div>
                        <div class="inv-gen-field" style="margin-bottom:10px;"><label>Problem Reported</label><textarea id="wProblem" rows="2" placeholder="Symptom as reported."></textarea></div>
                        <div class="inv-gen-field" style="margin-bottom:10px;"><label>Diagnosis</label><textarea id="wDiagnosis" rows="2" placeholder="Root cause found after testing."></textarea></div>
                        <div class="inv-gen-field"><label>Parts Replaced</label><textarea id="wParts" rows="2" placeholder="Part, part number and quantity replaced."></textarea></div>
                    </div>

                    <div id="wJobBox" style="display:none;">
                        <div class="doc-split" style="margin-bottom:10px;">
                            <div class="inv-gen-field"><label>Job Worker / Subcontractor</label><input type="text" id="wWorker" placeholder="Name of the job worker"></div>
                            <div class="inv-gen-field"><label>Job Worker GSTIN</label><input type="text" id="wWorkerGst" placeholder="24AAAAA0000A1Z5" maxlength="15"></div>
                        </div>
                        <div class="inv-gen-field" style="margin-bottom:10px;"><label>Job Worker Address</label><input type="text" id="wWorkerAddr" placeholder="Unit, city, state, PIN"></div>
                        <div class="doc-split" style="margin-bottom:10px;">
                            <div class="inv-gen-field"><label>Material Sent (Qty &amp; UOM)</label><input type="text" id="wMaterialSent" placeholder="e.g. 200 nos blanks"></div>
                            <div class="inv-gen-field"><label>Rate Per Piece (&#8377;)</label><input type="number" id="wRatePiece" value="0" min="0" step="0.01"></div>
                        </div>
                        <div class="doc-split">
                            <div class="inv-gen-field"><label>Qty Ordered</label><input type="number" id="wQtyOrder" value="0" min="0" step="1"></div>
                            <div class="inv-gen-field"><label>Qty Accepted</label><input type="number" id="wQtyAccept" value="0" min="0" step="1"></div>
                        </div>
                        <div class="inv-gen-field" style="margin-top:10px;"><label>Rejection Count</label><input type="number" id="wReject" value="0" min="0" step="1"></div>
                    </div>

                    <div id="wInstBox" style="display:none;">
                        <div class="inv-gen-field" style="margin-bottom:10px;"><label>Installation Team / Crew</label><input type="text" id="wInstTeam" placeholder="Names of the installation crew"></div>
                        <div class="doc-split" style="margin-bottom:10px;">
                            <div class="inv-gen-field"><label>Site Readiness</label>
                                <select id="wSiteReady">
                                    <option value="Ready">Site ready</option>
                                    <option value="Partially ready">Partially ready</option>
                                    <option value="Not ready">Not ready</option>
                                </select>
                            </div>
                            <div class="inv-gen-field"><label>Handover Date</label><input type="date" id="wHandover"></div>
                        </div>
                        <div class="inv-gen-field"><label>Installation Notes</label><textarea id="wInstNote" rows="2" placeholder="Access, power, civil work and handover conditions."></textarea></div>
                    </div>
                </div>

                <!-- 5. STAGES -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                        </div>
                        <div>
                            <h3>Job Stages</h3>
                            <p>Tick a stage as it is completed. The job status below the sheet updates itself.</p>
                        </div>
                        <span class="qm-step">Step 5</span>
                    </div>
                    <div class="chk-hd" style="--chk-cols:26px 1fr auto;">
                        <span></span><span>Stage</span><span>Completed On</span>
                    </div>
                    <div class="chk-list" id="woStages"></div>
                    <div class="note-box" style="margin-top:14px;">
                        <div class="note-ico">&#128202;</div>
                        <div>
                            <p style="margin:0;">Job status: <strong id="wStatusText">Pending</strong> &mdash; derived from the ticked stages. Nothing ticked is Pending, most stages done is In Progress, all but the last is Ready, and everything ticked is Closed.</p>
                        </div>
                    </div>
                </div>

                <!-- 6. MATERIALS + LABOUR -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M3 12h18M3 18h12"/></svg>
                        </div>
                        <div>
                            <h3>Materials &amp; Labour</h3>
                            <p>What was issued or consumed against the job, and who worked on it for how long.</p>
                        </div>
                        <span class="qm-step">Step 6</span>
                    </div>

                    <p class="inv-gen-section-title" style="margin-bottom:8px;">Materials / Spares Consumed</p>
                    <div class="doc-items" id="woMats" style="--doc-cols: 2.1fr .6fr .7fr .7fr .5fr .75fr .85fr auto;">
                        <div class="doc-item-hd">
                            <span>Item / Spare</span><span>HSN</span><span>Qty Issued</span><span>Qty Returned</span><span>Unit</span><span>Rate</span><span style="text-align:right">Amount</span><span></span>
                        </div>
                    </div>
                    <button type="button" class="inv-gen-add-item" onclick="woAddMat()">+ Add Material / Spare</button>

                    <p class="inv-gen-section-title" style="margin:18px 0 8px;">Labour</p>
                    <div class="doc-items" id="woLabs" style="--doc-cols: 1.5fr 1.7fr .6fr .75fr .85fr auto;">
                        <div class="doc-item-hd">
                            <span>Worker Name</span><span>Task</span><span>Hours</span><span>Rate / Hr</span><span style="text-align:right">Amount</span><span></span>
                        </div>
                    </div>
                    <button type="button" class="inv-gen-add-item" onclick="woAddLab()">+ Add Labour Entry</button>
                </div>

                <!-- 7. APPROVAL + NOTES -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>
                        </div>
                        <div>
                            <h3>Approval Trail &amp; Notes</h3>
                            <p>Who prepared the order, who approved it and which job card the work is booked against.</p>
                        </div>
                        <span class="qm-step">Step 7</span>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Prepared By</label><input type="text" id="wPrepared" placeholder="Name of preparer"></div>
                        <div class="inv-gen-field"><label>Approved By</label><input type="text" id="wApproved" placeholder="Name of approver"></div>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Job Card No.</label><input type="text" id="wJobCard" placeholder="JC-0451"></div>
                        <div class="inv-gen-field"><label>Supervisor</label><input type="text" id="wSupervisor" placeholder="Supervisor / foreman"></div>
                    </div>
                    <div class="inv-gen-field"><label>Notes / Terms</label><textarea id="wNotes" rows="3" placeholder="Payment on delivery. Parts under warranty are not chargeable."></textarea></div>
                </div>

                <!-- ACTIONS -->
                <div class="qm-actions doc-no-print">
                    <button type="button" class="qm-btn qm-btn-primary" onclick="woPreviewJump()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        Preview
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="woPrint()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="1"/><path d="M6 17h12v4H6z"/></svg>
                        Print
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="woReset()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                        Reset
                    </button>
                    <button type="button" class="qm-btn qm-btn-primary" style="flex:2" onclick="woPDF()">
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
                <div class="inv-gen-preview" id="woPreview">
                    <div class="doc-sheet doc-sheet-lg" id="woSheet">

                        <div class="doc-hd">
                            <div class="doc-org">
                                <h2 id="pCoName">Your Company</h2>
                                <p id="pCoAddr">Address</p>
                                <p><span id="pCoPhone">Phone: &mdash;</span></p>
                                <p><strong>GSTIN:</strong> <span id="pCoGst">&mdash;</span></p>
                            </div>
                            <div class="doc-id">
                                <div class="doc-title" id="pTitle">MANUFACTURING WORK ORDER</div>
                                <p><strong>Work Order No.:</strong> <span id="pWoNo">&mdash;</span></p>
                                <p><strong>Date:</strong> <span id="pWoDate">&mdash;</span></p>
                                <p id="pStatusRow"><strong>Job Status:</strong> <span id="pStatus">Pending</span></p>
                            </div>
                        </div>

                        <div class="doc-split" style="margin-bottom:12px;">
                            <div>
                                <p class="doc-sec-title">Customer / Client</p>
                                <p style="font-weight:700;margin:3px 0;font-size:11px;" id="pClient">&mdash;</p>
                                <p id="pClientPhone" style="font-size:9.5px;color:#475569;margin:1px 0;">Ph: &mdash;</p>
                                <p id="pSiteAddr" style="font-size:9.5px;color:#475569;margin:1px 0;">Site: &mdash;</p>
                            </div>
                            <div>
                                <p class="doc-sec-title">Job Particulars</p>
                                <p style="font-weight:700;margin:3px 0;font-size:11px;" id="pJobTitle">&mdash;</p>
                                <p id="pPriorityRow" style="font-size:9.5px;color:#475569;margin:1px 0;"><strong>Priority:</strong> <span id="pPriority">Normal</span></p>
                                <p id="pDueRow" style="font-size:9.5px;color:#475569;margin:1px 0;">Expected: <span id="pDue">&mdash;</span></p>
                            </div>
                        </div>

                        <div id="pDescRow" style="display:none;font-size:9.5px;color:#475569;margin-bottom:10px;"><strong>Scope:</strong> <span id="pDesc"></span></div>

                        <div class="kv-grid" style="margin-bottom:12px;" id="pTypeBox"></div>

                        <div id="pCheckBox" style="margin-bottom:12px;">
                            <p class="doc-sec-title">Job Stages</p>
                            <div class="chk-list" id="pStages"></div>
                        </div>

                        <p class="doc-sec-title">Materials / Spares Consumed</p>
                        <table style="width:100%;border-collapse:collapse;margin-bottom:12px;font-size:10px;">
                            <thead><tr>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">#</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">Item / Spare</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Issued</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Retd</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Rate</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Amount</th>
                            </tr></thead>
                            <tbody id="pMatRows"><tr><td colspan="6" style="padding:12px;text-align:center;color:#94A3B8;">No materials recorded</td></tr></tbody>
                        </table>

                        <p class="doc-sec-title">Labour</p>
                        <table style="width:100%;border-collapse:collapse;margin-bottom:12px;font-size:10px;">
                            <thead><tr>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">#</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">Worker</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:left;font-size:9px;">Task</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Hrs</th>
                                <th style="background:#334155;color:#fff;padding:5px 6px;text-align:right;font-size:9px;">Amount</th>
                            </tr></thead>
                            <tbody id="pLabRows"><tr><td colspan="5" style="padding:12px;text-align:center;color:#94A3B8;">No labour recorded</td></tr></tbody>
                        </table>

                        <div class="doc-stats" style="margin-bottom:12px;">
                            <div class="doc-stat"><span>Total Material Cost</span><strong id="pMatTotal">&#8377;0.00</strong></div>
                            <div class="doc-stat"><span>Total Labour Cost</span><strong id="pLabTotal">&#8377;0.00</strong></div>
                            <div class="doc-stat"><span>Total Job Cost</span><strong id="pJobTotal">&#8377;0.00</strong></div>
                            <div class="doc-stat amber" id="pVarBox"><span>Est. vs Actual</span><strong id="pVariance">&#8377;0.00</strong></div>
                        </div>

                        <div class="note-box" id="pGstBox" style="padding:9px 12px;margin-bottom:12px;">
                            <div class="note-ico" style="font-size:15px;">&#128176;</div>
                            <p style="margin:0;font-size:9px;line-height:1.6;" id="pGstNote">Keep the material and labour columns separate. A job work service supplied to a principal is a supply of service, and job work received by a notified job worker is exempt &mdash; the split on this sheet is what proves both.</p>
                        </div>

                        <div id="pNotesRow" style="display:none;font-size:9px;color:#475569;padding:6px 0;border-top:1px dashed #E2E8F0;margin-bottom:6px;">
                            <strong>Notes:</strong> <span id="pNotes"></span>
                        </div>

                        <div class="doc-sig" id="pApprRow" style="grid-template-columns:repeat(4,1fr);">
                            <div>
                                <div class="doc-sig-line">Prepared By</div>
                                <div style="font-size:8.5px;color:#94A3B8;margin-top:2px;"><span id="pPrepared">&mdash;</span> &middot; Date: ____</div>
                            </div>
                            <div>
                                <div class="doc-sig-line">Approved By</div>
                                <div style="font-size:8.5px;color:#94A3B8;margin-top:2px;"><span id="pApproved">&mdash;</span> &middot; Date: ____</div>
                            </div>
                            <div>
                                <div class="doc-sig-line">Job Card No.</div>
                                <div style="font-size:8.5px;color:#94A3B8;margin-top:2px;"><span id="pJobCard">&mdash;</span></div>
                            </div>
                            <div>
                                <div class="doc-sig-line">Supervisor</div>
                                <div style="font-size:8.5px;color:#94A3B8;margin-top:2px;"><span id="pSupervisor">&mdash;</span></div>
                            </div>
                        </div>
                        <div class="doc-ft">This is a computer-generated work order &middot; Generated by <strong>My Metaji</strong></div>
                    </div>
                </div>
                <div class="qm-total-strip">
                    <span>Total Job Cost</span>
                    <strong id="woStripTotal">&#8377;0.00</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== TYPE COMPARISON ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>Which Work Order Do You Need?</h2>
        <p>The work order is the document that carries an instruction to the workshop or the service team. Its shape depends on whether you are producing something, maintaining an asset, repairing a failed item, outsourcing a process or installing a site.</p>
        <div class="fmt-table-wrap" style="margin-top:20px;">
            <table class="fmt-table">
                <thead>
                    <tr><th>Type</th><th>Who uses it</th><th>Stage checklist</th><th>Costing focus</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Manufacturing Work Order</strong></td>
                        <td>Production floor, batch release, drawing traceability</td>
                        <td>7 stages from material issued to ready for dispatch</td>
                        <td>Material issued plus direct labour per batch</td>
                    </tr>
                    <tr>
                        <td><strong>Maintenance / Service</strong></td>
                        <td>AMC and annual maintenance contracts</td>
                        <td><span class="fmt-req yes">5 stages</span> diagnosed to customer sign-off</td>
                        <td>Visit cost, spares consumed, next service due</td>
                    </tr>
                    <tr>
                        <td><strong>Repair Work Order</strong></td>
                        <td>Service desk and warranty desk</td>
                        <td>Received to delivered</td>
                        <td>Parts replaced and labour hours, warranty flag</td>
                    </tr>
                    <tr>
                        <td><strong>Job Work Order</strong></td>
                        <td>Principal sending work to a job worker</td>
                        <td>Material received to ready for dispatch</td>
                        <td>Rate per piece, ordered vs accepted, rejections</td>
                    </tr>
                    <tr>
                        <td><strong>Installation &amp; Site Work</strong></td>
                        <td>Project teams handling customer sites</td>
                        <td>Site survey to handover</td>
                        <td>Installation team, readiness, handover date</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="note-box gold" style="margin-top:20px;">
            <div class="note-ico">&#128269;</div>
            <div>
                <h4>Why a work order matters beyond the workshop</h4>
                <ul>
                    <li><strong>Traceability.</strong> Batch number, drawing number, job card number and the signed stage trail let you answer a customer complaint six months later without guessing.</li>
                    <li><strong>Costing.</strong> Separating material issued from labour booked is the only reliable way to know the true cost of a job, and it is the base for any quotation you quote next month.</li>
                    <li><strong>GST on job work.</strong> When a job worker supplies job work services to a principal, it is a supply of service and GST is charged on the processing charge. When a principal supplies job work to a job worker who is notified under the <strong>Job Work exemption</strong>, the supply is <strong>exempt</strong> &mdash; so both sides keep their material outside that valuation.</li>
                    <li><strong>Labour vs material split.</strong> GST is charged on the service value, not on the goods that go into it. A work order that shows only a single lump-sum figure cannot support a correct invoice.</li>
                    <li><strong>Warranty.</strong> Marking the job as under warranty tells the service desk whether consumables are chargeable to the customer or to the manufacturer&rsquo;s warranty account.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===================== HOW TO ===================== -->
<section class="gst-faq" style="background:#fff;">
    <div class="gst-wrap">
        <h2>How to Create a Work Order</h2>
        <div class="gst-how-grid">
            <div class="gst-how-card">
                <div class="gst-how-num">1</div>
                <h3>Pick the Type</h3>
                <p>Manufacturing, maintenance, repair, job work or installation. The details block and the stage checklist change to match.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">2</div>
                <h3>Fill the Job</h3>
                <p>Customer, job title, priority and expected dates go in first, then the type-specific details such as batch, complaint or fault.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">3</div>
                <h3>Tick the Stages</h3>
                <p>Work stages get ticked off as the job moves. Material issued, labour booked and the totals update live in the preview.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">4</div>
                <h3>Sign and Download</h3>
                <p>Print the sheet for the floor or the van. Prepared by, approved by, job card number and supervisor sign at the bottom.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SEO + RELATED ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>What is a Work Order?</h2>
        <p>A <strong>work order</strong> is the written instruction that starts a job inside a workshop, a factory or a service van. It is a job card: one printed sheet that moves with the work from the moment it is raised until it is closed, so that anyone picking it up at any stage can see what was asked for, which materials were issued against it, who worked on it, how long it took and who signed it off. Because the sheet travels with the job, it doubles as a production record, a cost sheet and a service history &mdash; the same sheet for the same machine next year is the evidence of the last service and of the parts that were replaced.</p>
        <p>For a manufacturer, the work order is where the <strong>batch number</strong> and the <strong>drawing number</strong> are fixed, so that a recall can be traced back to a specific heat, a specific shift and a specific operator. For a service business, the same document carries the <strong>complaint number</strong>, the fault as reported, the diagnosis, the parts replaced and the next service due date, which is what turns an annual maintenance contract from a promise into a measurable routine. For businesses that outsource a process, the work order is the record that goes out with the material to a job worker and comes back with the finished goods, carrying the quantity ordered, the quantity accepted and the rejection count &mdash; figures that decide the invoice and, under the GST rules, decide whether the job work is a taxable supply of service or an exempt supply under the notified job work exemption.</p>

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
        <h2>Stop Chasing Job Cards on Paper Slips</h2>
        <p><?= BRAND_NAME ?> raises work orders against your stock, books material and labour to the job, tracks every stage and converts the finished job into a GST invoice in one click.</p>
        <a href="#download" class="gst-cta-btn">Download <?= BRAND_NAME ?> Free &rarr;</a>
    </div>
</section>

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="assets/js/doc-utils.js"></script>
<script>
/* ---------- Work order types ---------- */
const WO_STAGES_MFG = ['Material Issued', 'Cutting', 'Welding', 'Assembly', 'Quality Check', 'Painting', 'Ready for Dispatch'];
const WO_STAGES_JOB = ['Material Received', 'Cutting', 'Welding', 'Assembly', 'Quality Check', 'Painting', 'Ready for Dispatch'];
const WO_STAGES_MAINT = ['Diagnosed', 'Repaired', 'Tested', 'Parts Replaced', 'Customer Sign-off'];
const WO_STAGES_REPAIR = ['Received', 'Diagnosed', 'Repaired', 'Parts Replaced', 'Tested', 'Delivered'];
const WO_STAGES_INST = ['Site Survey', 'Civil Work', 'Wiring / Roughing', 'Installation', 'Testing', 'Handover Done'];

const WO_TYPES = {
    manufacturing: {
        label: 'Manufacturing Work Order', tag: 'Job card for production', tags: ['Batch no.', 'Drawing no.', 'Material issued'],
        ico: '\u{1F6ED}', color: '#2563EB', stages: WO_STAGES_MFG,
        desc: 'A job card issued to the shop floor for production. It carries the batch and drawing reference, records material issued against the job and closes only after quality check.',
        fields: 'Work order number and date, customer, job description, priority, expected dates, batch and drawing number, machine, stage checklist, materials and labour',
        title: 'MANUFACTURING WORK ORDER',
        gst: 'Material issued to the job is charged as goods, labour booked to the same job card becomes part of the manufacturing cost and is billed to the customer on the tax invoice.'
    },
    maintenance: {
        label: 'Maintenance / Service Work Order', tag: 'AMC &amp; service visit', tags: ['Complaint no.', 'Technician', 'Next service due'],
        ico: '\u{1F527}', color: '#0D9488', stages: WO_STAGES_MAINT,
        desc: 'Raised for a planned or breakdown service visit. It records the complaint reference, who reported the fault, the technician assigned, the resolution and the date the next service falls due.',
        fields: 'Complaint number, reported by, fault description, technician, visit date, resolution, next service due, stage checklist, spares consumed and labour',
        title: 'MAINTENANCE / SERVICE WORK ORDER',
        gst: 'A maintenance visit is a supply of service. Spares fitted to the asset are shown separately from the service labour so the invoice can carry both correctly.'
    },
    repair: {
        label: 'Repair Work Order', tag: 'Service or warranty desk', tags: ['Diagnosis', 'Parts replaced', 'Warranty'],
        ico: '\u{1F6E0}', color: '#D97706', stages: WO_STAGES_REPAIR,
        desc: 'For a failed item brought in for repair. It records the device, the reported problem, the diagnosis, the parts replaced, the labour hours and whether the job is under warranty.',
        fields: 'Device and serial number, problem reported, diagnosis, parts replaced, labour hours, warranty claim number, stage checklist and total cost',
        title: 'REPAIR WORK ORDER',
        gst: 'A repair job supplied to a customer is a composite supply. Value the service on the labour and use the parts properly, or declare the whole job under the composite supply treatment of Section 17(5).'
    },
    jobwork: {
        label: 'Job Work Order (Subcontract)', tag: 'Given to a job worker', tags: ['Material sent', 'Rate per piece', 'Rejections'],
        ico: '\u{1F91D}', color: '#7C3AED', stages: WO_STAGES_JOB,
        desc: 'Issued when work is given out to a job worker. It records the material sent out, the rate per piece, how many pieces were ordered against how many were accepted and how many were rejected.',
        fields: 'Job worker name, GSTIN and address, material sent, rate per piece, quantity ordered, quantity accepted, rejection count, stage checklist and process value',
        title: 'JOB WORK ORDER (SUBCONTRACT)',
        gst: 'Job work supplied by a job worker to a principal is a taxable supply of service. The same job work received by a notified job worker is exempt under the Job Work exemption, so record who is on which side of that line.'
    },
    installation: {
        label: 'Installation &amp; Site Work Order', tag: 'On a customer site', tags: ['Site address', 'Install team', 'Handover'],
        ico: '\u{1F3D7}', color: '#059669', stages: WO_STAGES_INST,
        desc: 'For work carried out at a customer site. It records the site address and type, the installation crew, the readiness of the site and the date the installation is formally handed over.',
        fields: 'Site address and type, installation crew, site readiness, handover date, task checklist to tick, materials issued and installation labour',
        title: 'INSTALLATION &amp; SITE WORK ORDER',
        gst: 'Installation carried out at the customer&rsquo;s premises is a supply of service. The goods installed keep their own invoice, and the installation service is invoiced separately on this work order.'
    }
};

let woType = 'manufacturing';
let woMatId = 0;
let woLabId = 0;

/* ---------- Type picker ---------- */
function woRenderTypes() {
    const grid = document.getElementById('woFmtGrid');
    let h = '';
    Object.keys(WO_TYPES).forEach(k => {
        const t = WO_TYPES[k];
        h += '<button type="button" class="fmt-card' + (k === woType ? ' active' : '') + '" data-wo-type="' + k + '" aria-pressed="' + (k === woType) + '">'
            + '<span class="fmt-mini" style="--fmt-accent:' + t.color + '">'
            + '<span class="fmt-mini-top"><span class="fmt-mini-brand"></span><span class="fmt-mini-tag"></span></span>'
            + '<span class="fmt-mini-ln w70"></span><span class="fmt-mini-ln w45"></span><span class="fmt-mini-ln w70"></span>'
            + '<span class="fmt-mini-tot"></span>'
            + '</span>'
            + '<span class="fmt-card-body">'
            + '<span class="fmt-card-title">' + t.ico + ' ' + t.label + '</span>'
            + '<span class="fmt-card-sub">' + t.tag + '</span>'
            + '<span class="fmt-tags">' + t.tags.map(x => '<span class="fmt-tag">' + x + '</span>').join('') + '</span>'
            + '</span></button>';
    });
    grid.innerHTML = h;
    grid.querySelectorAll('[data-wo-type]').forEach(b => b.addEventListener('click', () => woSetType(b.dataset.woType)));
    woSetType(woType, true);
}

function woSetType(k, quiet) {
    const t = WO_TYPES[k];
    if (!t) return;
    woType = k;

    document.querySelectorAll('[data-wo-type]').forEach(b => {
        const on = b.dataset.woType === k;
        b.classList.toggle('active', on);
        b.setAttribute('aria-pressed', on);
    });

    document.getElementById('woInfoIco').textContent = t.ico;
    document.getElementById('woInfoTitle').textContent = t.label;
    document.getElementById('woInfoDesc').textContent = t.desc;
    document.getElementById('woInfoFields').textContent = t.fields;

    const show = (id, on) => { const e = document.getElementById(id); if (e) e.style.display = on ? '' : 'none'; };
    show('wManuBox', k === 'manufacturing');
    show('wMaintBox', k === 'maintenance');
    show('wRepairBox', k === 'repair');
    show('wJobBox', k === 'jobwork');
    show('wInstBox', k === 'installation');
    show('wSiteRow', k === 'installation');

    woRenderStages();
    if (!quiet) woUpdate();
}

/* ---------- Stage checklist ---------- */
function woRenderStages() {
    const box = document.getElementById('woStages');
    const keep = {};
    Array.prototype.forEach.call(box.querySelectorAll('[data-stage]'), e => { keep[e.dataset.stage] = e.querySelector('.chk-box').classList.contains('on'); });
    box.innerHTML = WO_TYPES[woType].stages.map(function (s, i) {
        const on = keep[s] === undefined ? false : keep[s];
        return '<div class="chk-row' + (on ? ' is-done' : '') + '" data-stage="' + s + '" style="--chk-cols:26px 1fr auto;">'
            + '<button type="button" class="chk-box' + (on ? ' on' : '') + '" onclick="woToggleStage(' + i + ')" aria-pressed="' + on + '" aria-label="Mark stage ' + s + ' as done" title="Mark as done">'
            + '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>'
            + '</button>'
            + '<span class="chk-txt">' + s + '</span>'
            + '<span class="inv-gen-field" style="max-width:136px;">'
            + '<label style="font-size:9px;margin-bottom:2px;">Done on</label>'
            + '<input type="date" data-wo-stage-date aria-label="Date ' + s + ' was completed" oninput="woUpdate()">'
            + '</span></div>';
    }).join('');
    woUpdate();
}

function woToggleStage(i) {
    const rows = document.querySelectorAll('#woStages .chk-row');
    const el = rows[i];
    if (!el) return;
    const on = !el.classList.contains('is-done');
    el.classList.toggle('is-done', on);
    const b = el.querySelector('.chk-box');
    if (b) { b.classList.toggle('on', on); b.setAttribute('aria-pressed', on ? 'true' : 'false'); }
    woUpdate();
}

/* ---------- Material rows ---------- */
function woAddMat(seed) {
    seed = seed || {};
    const id = woMatId++;
    const esc = v => String(v === undefined || v === null ? '' : v).replace(/"/g, '&quot;');
    document.getElementById('woMats').insertAdjacentHTML('beforeend',
        '<div class="doc-item" data-mat="' + id + '">'
        + '<input type="text" data-wo-mat-name aria-label="Material or spare name" placeholder="Item / spare" value="' + esc(seed.name) + '" oninput="woUpdate()">'
        + '<input type="text" data-wo-mat-hsn aria-label="HSN code" placeholder="HSN" maxlength="8" oninput="woUpdate()">'
        + '<input type="number" data-wo-mat-qi aria-label="Quantity issued" value="' + (seed.qi || 0) + '" min="0" step="0.01" oninput="woUpdate()">'
        + '<input type="number" data-wo-mat-qr aria-label="Quantity returned" value="' + (seed.qr || 0) + '" min="0" step="0.01" oninput="woUpdate()">'
        + '<input type="text" data-wo-mat-uom aria-label="Unit of measure" placeholder="NOS" maxlength="8" value="' + esc(seed.uom) + '" oninput="woUpdate()">'
        + '<input type="number" data-wo-mat-rate aria-label="Rate per unit" value="' + (seed.rate || 0) + '" min="0" step="0.01" oninput="woUpdate()">'
        + '<span class="doc-item-amt" data-wo-mat-amt aria-label="Material amount">&#8377;0.00</span>'
        + '<button type="button" class="doc-item-del" onclick="woDelMat(' + id + ')" title="Remove material" aria-label="Remove material">&times;</button>'
        + '</div>');
    woUpdate();
}

function woDelMat(id) {
    const e = document.querySelector('#woMats [data-mat="' + id + '"]');
    if (e) e.remove();
    woUpdate();
}

/* ---------- Labour rows ---------- */
function woAddLab(seed) {
    seed = seed || {};
    const id = woLabId++;
    const esc = v => String(v === undefined || v === null ? '' : v).replace(/"/g, '&quot;');
    document.getElementById('woLabs').insertAdjacentHTML('beforeend',
        '<div class="doc-item" data-lab="' + id + '">'
        + '<input type="text" data-wo-lab-name aria-label="Worker name" placeholder="Worker" value="' + esc(seed.name) + '" oninput="woUpdate()">'
        + '<input type="text" data-wo-lab-task aria-label="Task performed" placeholder="Task" value="' + esc(seed.task) + '" oninput="woUpdate()">'
        + '<input type="number" data-wo-lab-hrs aria-label="Hours worked" value="' + (seed.hrs || 0) + '" min="0" step="0.25" oninput="woUpdate()">'
        + '<input type="number" data-wo-lab-rate aria-label="Rate per hour" value="' + (seed.rate || 0) + '" min="0" step="0.01" oninput="woUpdate()">'
        + '<span class="doc-item-amt" data-wo-lab-amt aria-label="Labour amount">&#8377;0.00</span>'
        + '<button type="button" class="doc-item-del" onclick="woDelLab(' + id + ')" title="Remove labour entry" aria-label="Remove labour entry">&times;</button>'
        + '</div>');
    woUpdate();
}

function woDelLab(id) {
    const e = document.querySelector('#woLabs [data-lab="' + id + '"]');
    if (e) e.remove();
    woUpdate();
}

/* ---------- Collect ---------- */
function woCollect() {
    const t = WO_TYPES[woType];

    const stages = [];
    document.querySelectorAll('#woStages .chk-row[data-stage]').forEach(function (el, i) {
        const dt = el.querySelector('[data-wo-stage-date]');
        stages.push({ i: i, name: el.dataset.stage, done: el.classList.contains('is-done'), date: dt ? dt.value : '' });
    });
    const doneN = stages.filter(s => s.done).length;
    let status = 'Pending';
    if (doneN === stages.length && stages.length) status = 'Closed';
    else if (doneN === stages.length - 1) status = 'Ready';
    else if (doneN > 0) status = 'In Progress';

    const mats = [];
    let matTotal = 0;
    document.querySelectorAll('#woMats .doc-item[data-mat]').forEach((el, i) => {
        const g = s => { const e = el.querySelector(s); return e ? String(e.value || '').trim() : ''; };
        const n = s => { const e = el.querySelector(s); return e ? (parseFloat(e.value) || 0) : 0; };
        const qi = n('[data-wo-mat-qi]'), qr = n('[data-wo-mat-qr]'), rate = n('[data-wo-mat-rate]');
        const amt = DOC.round2(qi * rate);
        const cell = el.querySelector('[data-wo-mat-amt]');
        if (cell) cell.textContent = DOC.money(amt);
        matTotal += amt;
        mats.push({ i: i + 1, name: g('[data-wo-mat-name]') || 'Item', hsn: g('[data-wo-mat-hsn]'), qi: qi, qr: qr, uom: g('[data-wo-mat-uom]'), rate: rate, amt: amt });
    });

    const labs = [];
    let labTotal = 0, hoursTotal = 0;
    document.querySelectorAll('#woLabs .doc-item[data-lab]').forEach((el, i) => {
        const g = s => { const e = el.querySelector(s); return e ? String(e.value || '').trim() : ''; };
        const n = s => { const e = el.querySelector(s); return e ? (parseFloat(e.value) || 0) : 0; };
        const hrs = n('[data-wo-lab-hrs]'), rate = n('[data-wo-lab-rate]');
        const amt = DOC.round2(hrs * rate);
        const cell = el.querySelector('[data-wo-lab-amt]');
        if (cell) cell.textContent = DOC.money(amt);
        labTotal += amt; hoursTotal += hrs;
        labs.push({ i: i + 1, name: g('[data-wo-lab-name]') || 'Worker', task: g('[data-wo-lab-task]'), hrs: hrs, rate: rate, amt: amt });
    });

    matTotal = DOC.round2(matTotal);
    labTotal = DOC.round2(labTotal);
    const jobTotal = DOC.round2(matTotal + labTotal);
    const estimate = DOC.num('wEstimate');
    const variance = DOC.round2(jobTotal - estimate);

    let gstd = t.gst;
    if (woType === 'jobwork') {
        const proc = DOC.round2(DOC.num('wQtyAccept') * DOC.num('wRatePiece'));
        if (proc > 0) gstd = t.gst + ' Billed job work value on this sheet is ' + DOC.money(proc) + ' for ' + DOC.qty(DOC.num('wQtyAccept')) + ' accepted pieces.';
    }

    return {
        t: t, stages: stages, doneN: doneN, status: status,
        mats: mats, labs: labs, matTotal: matTotal, labTotal: labTotal,
        hoursTotal: DOC.round2(hoursTotal), jobTotal: jobTotal, estimate: estimate, variance: variance,
        gstd: gstd
    };
}

/* ---------- Type-specific preview rows ---------- */
function woTypeRows() {
    const v = id => DOC.val(id);
    if (woType === 'manufacturing') {
        return [['Batch No.', v('wBatch')], ['Drawing No.', v('wDrawing')], ['Material Issued', v('wMaterial')], ['Machine', v('wMachine')], ['Shop Floor Note', v('wManuNote')]];
    }
    if (woType === 'maintenance') {
        return [['Complaint No.', v('wComplaint')], ['Reported By', v('wReported')], ['Technician', v('wTech')], ['Visit Date', DOC.fmtDate(v('wVisit'))], ['Fault', v('wFault')], ['Resolution', v('wResolution')], ['Next Service Due', DOC.fmtDate(v('wNext'))]];
    }
    if (woType === 'repair') {
        return [['Device / Item', v('wDevice')], ['Warranty Claim', v('wWarranty')], ['Claim No.', v('wClaimNo')], ['Labour Hours', DOC.qty(DOC.num('wHours')) + ' hrs'], ['Problem', v('wProblem')], ['Diagnosis', v('wDiagnosis')], ['Parts Replaced', v('wParts')]];
    }
    if (woType === 'jobwork') {
        return [['Job Worker', v('wWorker')], ['Job Worker GSTIN', v('wWorkerGst')], ['Material Sent', v('wMaterialSent')], ['Rate Per Piece', DOC.money(DOC.num('wRatePiece'))], ['Qty Ordered / Accepted', DOC.qty(DOC.num('wQtyOrder')) + ' / ' + DOC.qty(DOC.num('wQtyAccept'))], ['Rejections', DOC.qty(DOC.num('wReject'))]];
    }
    return [['Site Type', v('wSiteType')], ['Installation Crew', v('wInstTeam')], ['Site Readiness', v('wSiteReady')], ['Handover Date', DOC.fmtDate(v('wHandover'))], ['Notes', v('wInstNote')]];
}

/* ---------- Preview render ---------- */
function woUpdate() {
    const d = woCollect();

    DOC.text('pCoName', DOC.val('wCoName'));
    DOC.text('pCoAddr', DOC.val('wCoAddr'));
    DOC.text('pCoPhone', 'Phone: ' + (DOC.val('wCoPhone') || '\u2014'));
    DOC.text('pCoGst', DOC.val('wCoGst'));

    DOC.text('pTitle', d.t.title);
    DOC.text('pWoNo', DOC.val('wNo'));
    DOC.text('pWoDate', DOC.fmtDate(DOC.val('wDate')));
    const showStatus = document.getElementById('woOptStatus').checked;
    DOC.showIf('pStatusRow', showStatus);
    DOC.text('pStatus', d.status);
    document.getElementById('wStatusText').textContent = d.status;

    DOC.text('pClient', DOC.val('wClient') || '\u2014');
    DOC.text('pClientPhone', 'Ph: ' + (DOC.val('wPhone') || '\u2014'));
    DOC.text('pSiteAddr', 'Site: ' + (DOC.val('wAddr') || '\u2014'));
    DOC.text('pJobTitle', DOC.val('wTitle') || '\u2014');
    DOC.text('pPriority', DOC.val('wPriority') || 'Normal');

    const s1 = DOC.fmtDate(DOC.val('wStart')), e1 = DOC.fmtDate(DOC.val('wEnd'));
    DOC.text('pDue', s1 && e1 ? (s1 + ' to ' + e1) : (s1 || e1 || '\u2014'));
    DOC.showIf('pDueRow', !!(s1 || e1));

    const desc = DOC.val('wDesc');
    DOC.showIf('pDescRow', !!desc);
    if (desc) document.getElementById('pDesc').textContent = desc;

    const rows = woTypeRows().filter(r => r[1] !== '');
    DOC.html('pTypeBox', rows.length ? rows.map(r => '<div class="kv-item' + (r[1].length > 40 ? ' wide' : '') + '"><div class="kv-k">' + r[0] + '</div><div class="kv-v">' + r[1] + '</div></div>').join('')
        : '<div class="kv-item wide"><div class="kv-k">Type Details</div><div class="kv-v is-empty">Fill the type-specific block on the left</div></div>');

    DOC.showIf('pCheckBox', document.getElementById('woOptCheck').checked);
    if (document.getElementById('woOptCheck').checked) {
        const box = document.getElementById('pStages');
        box.innerHTML = d.stages.map(s =>
            '<div class="chk-row' + (s.done ? ' is-done' : '') + '" style="--chk-cols:26px 1fr auto;">'
            + '<span class="chk-box' + (s.done ? ' on' : '') + '" aria-hidden="true"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>'
            + '<span class="chk-txt">' + s.name + '</span>'
            + '<span class="chk-txt" style="text-align:right;">' + (s.date ? DOC.fmtDate(s.date) : '____') + '</span>'
            + '</div>').join('');
    }

    const mtb = document.getElementById('pMatRows');
    if (!d.mats.length) {
        mtb.innerHTML = '<tr><td colspan="6" style="padding:12px;text-align:center;color:#94A3B8;">No materials recorded</td></tr>';
    } else {
        mtb.innerHTML = d.mats.map(m => '<tr>'
            + '<td>' + m.i + '</td>'
            + '<td>' + m.name + (m.hsn ? ' <span style="color:#94A3B8;">[' + m.hsn + ']</span>' : '') + '</td>'
            + '<td style="text-align:right;">' + DOC.qty(m.qi) + (m.uom ? ' ' + m.uom : '') + '</td>'
            + '<td style="text-align:right;">' + DOC.qty(m.qr) + '</td>'
            + '<td style="text-align:right;">' + DOC.money(m.rate) + '</td>'
            + '<td style="text-align:right;font-weight:700;">' + DOC.money(m.amt) + '</td>'
            + '</tr>').join('');
    }

    const ltb = document.getElementById('pLabRows');
    if (!d.labs.length) {
        ltb.innerHTML = '<tr><td colspan="5" style="padding:12px;text-align:center;color:#94A3B8;">No labour recorded</td></tr>';
    } else {
        ltb.innerHTML = d.labs.map(l => '<tr>'
            + '<td>' + l.i + '</td>'
            + '<td>' + l.name + '</td>'
            + '<td>' + (l.task || '\u2014') + '</td>'
            + '<td style="text-align:right;">' + DOC.qty(l.hrs) + '</td>'
            + '<td style="text-align:right;font-weight:700;">' + DOC.money(l.amt) + '</td>'
            + '</tr>').join('');
    }

    document.getElementById('pMatTotal').textContent = DOC.money(d.matTotal);
    document.getElementById('pLabTotal').textContent = DOC.money(d.labTotal);
    document.getElementById('pJobTotal').textContent = DOC.money(d.jobTotal);
    document.getElementById('woStripTotal').textContent = DOC.money(d.jobTotal);

    const estOn = document.getElementById('woOptEst').checked;
    DOC.showIf('pVarBox', estOn);
    if (estOn) {
        document.getElementById('pVariance').textContent = d.estimate > 0
            ? (d.variance > 0 ? '+' : (d.variance < 0 ? '\u2212' : '')) + DOC.money(Math.abs(d.variance))
            : '\u2014';
    }

    DOC.showIf('pGstBox', document.getElementById('woOptGst').checked);
    if (document.getElementById('woOptGst').checked) document.getElementById('pGstNote').textContent = d.gstd;

    const notes = DOC.val('wNotes');
    DOC.showIf('pNotesRow', !!notes);
    if (notes) document.getElementById('pNotes').textContent = notes;

    DOC.showIf('pApprRow', document.getElementById('woOptAppr').checked);
    DOC.text('pPrepared', DOC.val('wPrepared'));
    DOC.text('pApproved', DOC.val('wApproved'));
    DOC.text('pJobCard', DOC.val('wJobCard'));
    DOC.text('pSupervisor', DOC.val('wSupervisor'));
}

/* ---------- Actions ---------- */
function woPreviewJump() { woUpdate(); DOC.scrollToPreview('woPreview'); }
function woPrint() { woUpdate(); window.print(); }

function woReset() {
    ['wNo', 'wCoName', 'wCoGst', 'wCoAddr', 'wCoPhone', 'wClient', 'wPhone', 'wTitle', 'wDesc', 'wAddr',
        'wBatch', 'wDrawing', 'wMaterial', 'wMachine', 'wManuNote',
        'wComplaint', 'wReported', 'wTech', 'wFault', 'wResolution',
        'wDevice', 'wClaimNo', 'wProblem', 'wDiagnosis', 'wParts',
        'wWorker', 'wWorkerGst', 'wWorkerAddr', 'wMaterialSent',
        'wInstTeam', 'wInstNote',
        'wPrepared', 'wApproved', 'wJobCard', 'wSupervisor', 'wNotes'].forEach(id => { const e = document.getElementById(id); if (e) e.value = ''; });
    ['wHours', 'wRatePiece', 'wQtyOrder', 'wQtyAccept', 'wReject', 'wEstimate'].forEach(id => { const e = document.getElementById(id); if (e) e.value = 0; });
    document.getElementById('wPriority').value = 'Normal';
    document.getElementById('wWarranty').value = 'No';
    document.getElementById('wSiteType').value = 'Residential';
    document.getElementById('wSiteReady').value = 'Ready';
    const today = DOC.todayISO();
    document.getElementById('wDate').value = today;
    document.getElementById('wStart').value = today;
    document.getElementById('wEnd').value = DOC.addDaysISO(7);
    document.getElementById('wVisit').value = today;
    document.getElementById('wHandover').value = today;
    document.getElementById('wNext').value = DOC.addDaysISO(180);
    DOC.setToggle('woOptStatus', true);
    DOC.setToggle('woOptCheck', true);
    DOC.setToggle('woOptAppr', true);
    DOC.setToggle('woOptEst', true);
    DOC.setToggle('woOptGst', true);
    document.getElementById('woMats').querySelectorAll('.doc-item').forEach(e => e.remove());
    document.getElementById('woLabs').querySelectorAll('.doc-item').forEach(e => e.remove());
    document.querySelectorAll('#woStages [data-wo-stage-date]').forEach(e => { e.value = ''; });
    woAddMat();
    woAddLab();
    woSetType('manufacturing');
    woUpdate();
}

/* ---------- PDF ---------- */
function woPDF() {
    woUpdate();
    const d = woCollect();
    const doc = DOC.newDoc();
    if (!doc) return;

    const W = doc.internal.pageSize.getWidth();
    const MG = 14;
    const accent = DOC.currentAccentRGB();

    const y0 = DOC.pdfHeader(doc, {
        org: DOC.val('wCoName') || 'Your Company',
        left: [
            { value: DOC.val('wCoAddr') || 'Address' },
            { value: 'Phone: ' + (DOC.val('wCoPhone') || '\u2014') },
            { value: 'GSTIN: ' + (DOC.val('wCoGst') || '\u2014') }
        ],
        title: d.t.title,
        right: [
            { label: 'Work Order No.', value: DOC.val('wNo') || '\u2014' },
            { label: 'Date', value: DOC.fmtDate(DOC.val('wDate')) || '\u2014' },
            { label: 'Job Status', value: d.status }
        ],
        accent: accent, height: 44
    });

    let y = y0;
    y = DOC.pdfSection(doc, MG, y, 'CUSTOMER & JOB PARTICULARS', W - MG * 2);
    doc.setFontSize(8.5);
    doc.setFont('helvetica', 'bold'); doc.setTextColor(15, 23, 42);
    doc.text(DOC.val('wClient') || 'Customer', MG, y); y += 4.2;
    doc.setFont('helvetica', 'normal'); doc.setTextColor(51, 65, 85);
    doc.text('Ph: ' + (DOC.val('wPhone') || '\u2014') + '  \u00B7  Site: ' + (DOC.val('wAddr') || '\u2014'), MG, y); y += 4.2;
    doc.setFont('helvetica', 'bold');
    doc.text(DOC.val('wTitle') || 'Job', MG, y); y += 4.2;
    doc.setFont('helvetica', 'normal');
    const s1 = DOC.fmtDate(DOC.val('wStart')), e1 = DOC.fmtDate(DOC.val('wEnd'));
    doc.text('Priority: ' + (DOC.val('wPriority') || 'Normal') + '  \u00B7  Expected: ' + (s1 || '\u2014') + ' to ' + (e1 || '\u2014'), MG, y); y += 5;
    if (DOC.val('wDesc')) {
        const dl = doc.splitTextToSize(DOC.val('wDesc'), W - MG * 2);
        doc.text(dl, MG, y); y += dl.length * 3.6 + 2;
    }

    const rows = woTypeRows().filter(r => r[1] !== '');
    if (rows.length) {
        y = DOC.pdfSection(doc, MG, y, 'TYPE-SPECIFIC DETAILS', W - MG * 2);
        doc.setFontSize(8);
        rows.forEach(r => {
            doc.setFont('helvetica', 'bold'); doc.text(r[0] + ':', MG, y);
            doc.setFont('helvetica', 'normal');
            const rl = doc.splitTextToSize(r[1], W - MG * 2 - 34);
            doc.text(rl, MG + 34, y);
            y += rl.length * 3.4 + 0.8;
        });
        y += 3;
    }

    if (document.getElementById('woOptCheck').checked) {
        y = DOC.pdfSection(doc, MG, y, 'JOB STAGES', W - MG * 2);
        y = DOC.pdfTable(doc,
            ['#', 'Stage', 'Status', 'Completed On'],
            d.stages.map(s => [s.i + 1, s.name, s.done ? 'Done' : 'Pending', s.date ? DOC.fmtDate(s.date) : '\u2014']),
            { startY: y, accent: accent, extra: { columnStyles: { 0: { cellWidth: 8 }, 2: { halign: 'right' }, 3: { halign: 'right' } } } }) + 5;
    }

    y = DOC.pdfSection(doc, MG, y, 'MATERIALS / SPARES CONSUMED', W - MG * 2);
    y = DOC.pdfTable(doc,
        ['#', 'Item / Spare', 'HSN', 'Qty Issued', 'Qty Returned', 'Unit', 'Rate', 'Amount'],
        d.mats.length ? d.mats.map(m => [m.i, m.name, m.hsn || '\u2014', DOC.qty(m.qi), DOC.qty(m.qr), m.uom || '\u2014', DOC.money(m.rate), DOC.money(m.amt)])
            : [['', 'No materials recorded', '', '', '', '', '', '']],
        {
            startY: y, accent: accent,
            extra: { columnStyles: { 0: { cellWidth: 8 }, 3: { halign: 'right' }, 4: { halign: 'right' }, 6: { halign: 'right' }, 7: { halign: 'right', fontStyle: 'bold' } } }
        }) + 5;

    y = DOC.pdfSection(doc, MG, y, 'LABOUR', W - MG * 2);
    y = DOC.pdfTable(doc,
        ['#', 'Worker', 'Task', 'Hours', 'Rate / Hr', 'Amount'],
        d.labs.length ? d.labs.map(l => [l.i, l.name, l.task || '\u2014', DOC.qty(l.hrs), DOC.money(l.rate), DOC.money(l.amt)])
            : [['', 'No labour recorded', '', '', '', '']],
        {
            startY: y, accent: accent,
            extra: { columnStyles: { 0: { cellWidth: 8 }, 3: { halign: 'right' }, 4: { halign: 'right' }, 5: { halign: 'right', fontStyle: 'bold' } } }
        }) + 5;

    const tRows = [
        { label: 'Total Material Cost', value: DOC.money(d.matTotal) },
        { label: 'Total Labour Hours', value: DOC.qty(d.hoursTotal) + ' hrs' },
        { label: 'Total Labour Cost', value: DOC.money(d.labTotal) },
        { rule: true, gap: 2 }
    ];
    if (document.getElementById('woOptEst').checked) {
        tRows.push({ label: 'Estimated Cost', value: d.estimate > 0 ? DOC.money(d.estimate) : '\u2014' });
        tRows.push({ label: 'Variance (Actual \u2212 Estimate)', value: d.estimate > 0 ? (d.variance > 0 ? '+' : (d.variance < 0 ? '\u2212' : '')) + DOC.money(Math.abs(d.variance)) : '\u2014' });
    }
    tRows.push({ label: 'Total Job Cost', value: DOC.money(d.jobTotal), bold: true, size: 10.5, color: accent, gap: 5 });
    y = DOC.pdfTotals(doc, W, y, tRows);

    if (document.getElementById('woOptGst').checked) {
        if (y > doc.internal.pageSize.getHeight() - 26) { doc.addPage(); y = 18; }
        doc.setFont('helvetica', 'bold'); doc.setFontSize(7.5); doc.setTextColor(146, 64, 14);
        doc.text('GST note:', MG, y); y += 3.8;
        doc.setFont('helvetica', 'normal');
        const gl = doc.splitTextToSize(d.gstd, W - MG * 2);
        doc.text(gl, MG, y); y += gl.length * 3.4 + 2;
    }
    if (DOC.val('wNotes')) {
        if (y > doc.internal.pageSize.getHeight() - 24) { doc.addPage(); y = 18; }
        doc.setFont('helvetica', 'bold'); doc.setFontSize(8); doc.setTextColor(71, 85, 105);
        doc.text('Notes:', MG, y); y += 4;
        doc.setFont('helvetica', 'normal');
        const nl = doc.splitTextToSize(DOC.val('wNotes'), W - MG * 2);
        doc.text(nl, MG, y); y += nl.length * 3.6 + 2;
    }

    if (document.getElementById('woOptAppr').checked) {
        if (y > doc.internal.pageSize.getHeight() - 32) { doc.addPage(); y = 20; }
        const colW = (W - MG * 2) / 4;
        const sy = y + 13;
        doc.setDrawColor(148, 163, 184); doc.setLineWidth(0.3);
        [0, 1, 2, 3].forEach(i => {
            const x = MG + colW * i + colW / 2;
            doc.line(x - 21, sy, x + 21, sy);
        });
        doc.setFontSize(7.5); doc.setTextColor(100, 116, 139);
        const lbl = ['Prepared By', 'Approved By', 'Job Card No.', 'Supervisor'];
        const nm = [DOC.val('wPrepared'), DOC.val('wApproved'), DOC.val('wJobCard'), DOC.val('wSupervisor')];
        lbl.forEach((l, i) => {
            doc.text(l, MG + colW * i + colW / 2, sy + 4, { align: 'center' });
            doc.text(nm[i] || '\u2014', MG + colW * i + colW / 2, sy + 8, { align: 'center' });
        });
        y = sy + 14;
    }

    DOC.pdfFooter(doc, y, (DOC.val('wNo') || 'work-order') + '  \u00B7  Status: ' + d.status);
    doc.save(((DOC.val('wNo') || 'work-order').replace(/[^\w\-]+/g, '_')) + '.pdf');
}

/* ---------- Boot ---------- */
(function () {
    const today = DOC.todayISO();
    document.getElementById('wDate').value = today;
    document.getElementById('wStart').value = today;
    document.getElementById('wEnd').value = DOC.addDaysISO(7);
    document.getElementById('wVisit').value = today;
    document.getElementById('wHandover').value = today;
    document.getElementById('wNext').value = DOC.addDaysISO(180);
    woRenderTypes();
    woAddMat();
    woAddLab();

    const ids = ['wNo', 'wDate', 'wCoName', 'wCoGst', 'wCoAddr', 'wCoPhone',
        'wClient', 'wPhone', 'wTitle', 'wDesc', 'wAddr', 'wPriority', 'wStart', 'wEnd', 'wEstimate',
        'wSiteType', 'wTeam',
        'wBatch', 'wDrawing', 'wMaterial', 'wMachine', 'wManuNote',
        'wComplaint', 'wReported', 'wTech', 'wVisit', 'wFault', 'wResolution', 'wNext',
        'wDevice', 'wWarranty', 'wClaimNo', 'wHours', 'wProblem', 'wDiagnosis', 'wParts',
        'wWorker', 'wWorkerGst', 'wWorkerAddr', 'wMaterialSent', 'wRatePiece', 'wQtyOrder', 'wQtyAccept', 'wReject',
        'wInstTeam', 'wSiteReady', 'wHandover', 'wInstNote',
        'wPrepared', 'wApproved', 'wJobCard', 'wSupervisor', 'wNotes',
        'woOptStatus', 'woOptCheck', 'woOptAppr', 'woOptEst', 'woOptGst'];
    DOC.bind(ids, woUpdate);

    DOC.bindSwatches('data-wo-accent', woUpdate);

    woUpdate();
})();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
