<?php
/**
 * My Metaji - Balance Sheet Format
 * Traditional (Schedule III) Indian-format balance sheet with a live balance check,
 * five statement variants, live preview and a landscape A4 PDF download.
 */
require_once __DIR__ . '/config.php';
$page_title = 'Free Balance Sheet Format — Vaivasthavittan Template PDF | ' . BRAND_NAME;
$page_desc  = 'Free balance sheet format for Indian firms in Schedule III, horizontal, SNC or condensed style. Enter assets, liabilities and equity, check that it balances, download PDF.';
include __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="gst-hero">
    <div class="gst-wrap">
        <div class="gst-hero-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"/></svg>
            Balance sheet that always balances &middot; No sign-up required
        </div>
        <h1>Free <span>Balance Sheet</span></h1>
        <p>Build a traditional Indian-format balance sheet (Vaivasthavittan) in the Schedule&nbsp;III vertical style your CA, banker and GST officer expect &mdash; enter equity, liabilities and assets and the tool proves that both sides are equal.</p>
        <div class="gst-hero-chips">
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
                5 statement formats
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
                Live balance check
            </span>
            <span class="gst-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M3 12h18M3 6h18M3 18h18"/><path d="M7 9v6M17 9v6"/></svg>
                A4 landscape PDF
            </span>
        </div>
    </div>
</section>

<!-- ===================== TOOL ===================== -->
<section class="gst-tool" id="balance-sheet-tool">
    <div class="gst-wrap">
        <div class="qm-layout">

            <!-- ===== LEFT: FORM ===== -->
            <div>

                <!-- 1. FORMAT, BASIS & PERIOD -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                        </div>
                        <div>
                            <h3>Choose Your Statement Format</h3>
                            <p>The format decides the vertical order of the heads and the reporting columns.</p>
                        </div>
                        <span class="qm-step">Step 1</span>
                    </div>

                    <div class="fmt-grid" id="bsFmtGrid" role="group" aria-label="Balance sheet format"></div>

                    <div class="note-box" style="margin-top:16px;">
                        <div class="note-ico" id="bsFmtIco">&#128202;</div>
                        <div>
                            <h4 id="bsFmtTitle">Traditional Balance Sheet</h4>
                            <p id="bsFmtDesc">The Schedule III vertical presentation: Equity and Liabilities on the left, Assets on the right, each group closed with a ruled sub-total.</p>
                            <p style="margin:0;"><strong>Heads shown:</strong> <span id="bsFmtFields">Share capital, reserves, non-current and current liabilities, property plant and equipment, current assets</span></p>
                        </div>
                    </div>

                    <p class="inv-gen-section-title" style="margin:20px 0 14px;">Firm Details</p>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Firm / Company Name</label><input type="text" id="bsCoName" placeholder="As recorded with the GST department and PAN"></div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Registered Address</label><input type="text" id="bsCoAddr" placeholder="Street, city, state, PIN"></div>
                        <div class="inv-gen-field"><label>State</label><select id="bsCoState"></select></div>
                    </div>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>PAN</label><input type="text" id="bsCoPan" placeholder="AAAAA0000A" maxlength="10"></div>
                        <div class="inv-gen-field"><label>GSTIN</label><input type="text" id="bsCoGstin" placeholder="24AAAAA0000A1Z5" maxlength="15"></div>
                    </div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>Type of Entity</label>
                            <select id="bsEntity">
                                <option>Proprietorship</option>
                                <option>Partnership Firm</option>
                                <option>LLP</option>
                                <option>Private Limited Company</option>
                            </select>
                        </div>
                        <div class="inv-gen-field"><label>Financial Year</label><input type="text" id="bsFy" placeholder="2025-26"></div>
                    </div>

                    <p class="inv-gen-section-title" style="margin:20px 0 14px;">Period, Basis &amp; Units</p>
                    <div class="doc-split" style="margin-bottom:10px;">
                        <div class="inv-gen-field"><label>Balance Sheet &ldquo;As on&rdquo; Date</label><input type="date" id="bsAsOn"></div>
                        <div class="inv-gen-field"><label>Previous Year &ldquo;As on&rdquo; Date</label><input type="date" id="bsPrevOn"></div>
                    </div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>Accounting Basis</label>
                            <select id="bsBasis"><option>Accrual</option><option>Cash</option></select>
                        </div>
                        <div class="inv-gen-field"><label>Figures in</label>
                            <select id="bsScale">
                                <option value="1">Rupees (&#8377;)</option>
                                <option value="1000">Thousands (&#8217;000)</option>
                                <option value="100000">Lakhs (&#8377; lakh)</option>
                                <option value="10000000">Crores (&#8377; crore)</option>
                            </select>
                        </div>
                    </div>
                    <p style="font-size:11.5px;color:#64748B;margin:8px 0 0;">The unit multiplies every figure printed in the statement &mdash; pick Lakhs for a mid-size firm and Crores for a company.</p>

                    <div class="doc-split" style="margin-top:18px;">
                        <div>
                            <div class="doc-opt">
                                <span>Accent colour</span>
                                <div class="doc-swatches">
                                    <button type="button" class="doc-swatch active" data-bs-accent="blue" style="background:#2563EB" aria-label="Blue"></button>
                                    <button type="button" class="doc-swatch" data-bs-accent="indigo" style="background:#4F46E5" aria-label="Indigo"></button>
                                    <button type="button" class="doc-swatch" data-bs-accent="green" style="background:#059669" aria-label="Green"></button>
                                    <button type="button" class="doc-swatch" data-bs-accent="amber" style="background:#D97706" aria-label="Amber"></button>
                                    <button type="button" class="doc-swatch" data-bs-accent="red" style="background:#DC2626" aria-label="Red"></button>
                                    <button type="button" class="doc-swatch" data-bs-accent="slate" style="background:#334155" aria-label="Slate"></button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="doc-switches">
                                <label class="doc-chk"><input type="checkbox" id="bsOptComp"> Comparative figures</label>
                                <label class="doc-chk"><input type="checkbox" id="bsOptZero" checked> Show zero-value lines</label>
                                <label class="doc-chk"><input type="checkbox" id="bsOptSig" checked> Signature block</label>
                                <label class="doc-chk"><input type="checkbox" id="bsOptNotes" checked> Notes to accounts</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. EQUITY -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="M7 7h7a3 3 0 0 1 0 6H7"/></svg>
                        </div>
                        <div>
                            <h3>Equity &amp; Shareholders&rsquo; Funds</h3>
                            <p>What the owners have brought in and what the business has earned and kept.</p>
                        </div>
                        <span class="qm-step">Step 2</span>
                    </div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label id="bsCapitalLbl">Share Capital / Owner&rsquo;s Capital (&#8377;)</label><input type="number" id="bsEqCapital" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                        <div class="inv-gen-field"><label>Reserves and Surplus / Retained Earnings (&#8377;)</label><input type="number" id="bsEqReserves" value="0" step="0.01" oninput="bsUpdate()"></div>
                    </div>
                    <p style="font-size:11.5px;color:#64748B;margin:10px 0 0;">Profit for the period is never shown as a separate head &mdash; carry it into Reserves and Surplus, otherwise the statement will not balance.</p>
                </div>

                <!-- 3. NON-CURRENT LIABILITIES -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="13" rx="2"/><path d="M2 10h20M6 15h4"/></svg>
                        </div>
                        <div>
                            <h3>Non-Current Liabilities</h3>
                            <p>Long-term money &mdash; anything repayable after twelve months goes here.</p>
                        </div>
                        <span class="qm-step">Step 3</span>
                    </div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>Long-term Borrowings (&#8377;)</label><input type="number" id="bsNclBorrow" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                        <div class="inv-gen-field"><label>Lease Liabilities (&#8377;)</label><input type="number" id="bsNclLease" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                    </div>
                    <div class="inv-gen-field" style="margin-top:10px;"><label>Provisions &mdash; Employee Benefit and Others (&#8377;)</label><input type="number" id="bsNclProv" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                </div>

                <!-- 4. CURRENT LIABILITIES -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M4 12h16M4 17h10"/><circle cx="20" cy="17" r="2"/></svg>
                        </div>
                        <div>
                            <h3>Current Liabilities</h3>
                            <p>Payable within twelve months &mdash; this is what your current ratio is measured against.</p>
                        </div>
                        <span class="qm-step">Step 4</span>
                    </div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>Trade Payables (&#8377;)</label><input type="number" id="bsClTrade" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                        <div class="inv-gen-field"><label>Short-term Borrowings (&#8377;)</label><input type="number" id="bsClBorrow" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                    </div>
                    <div class="doc-split" style="margin-top:10px;">
                        <div class="inv-gen-field"><label>Other Payables (&#8377;)</label><input type="number" id="bsClOther" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                        <div class="inv-gen-field"><label>Provisions for Tax and Expenses (&#8377;)</label><input type="number" id="bsClProv" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                    </div>
                </div>

                <!-- 5. NON-CURRENT ASSETS -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V8l7-5 7 5v13"/><path d="M9 21v-6h6v6"/></svg>
                        </div>
                        <div>
                            <h3>Non-Current Assets</h3>
                            <p>Fixed and long-term assets, shown at book value after depreciation.</p>
                        </div>
                        <span class="qm-step">Step 5</span>
                    </div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>Property, Plant &amp; Equipment &mdash; Net (&#8377;)</label><input type="number" id="bsNcaPpe" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                        <div class="inv-gen-field"><label>Intangible Assets (&#8377;)</label><input type="number" id="bsNcaIntang" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                    </div>
                    <div class="inv-gen-field" style="margin-top:10px;"><label>Long-term Investments (&#8377;)</label><input type="number" id="bsNcaInvest" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>

                    <div id="bsProjForm" style="display:none; margin-top:20px;">
                        <p class="inv-gen-section-title" style="margin:0 0 10px;">Project / Fund-wise Asset Allocation</p>
                        <div class="doc-items" id="bsProjItems" style="--doc-cols: 2fr 1.3fr 1fr auto">
                            <div class="doc-item-hd"><span>Project / Fund</span><span>Asset Head</span><span>Amount (&#8377;)</span><span></span></div>
                        </div>
                        <button type="button" class="inv-gen-add-item" onclick="bsAddProj()">+ Add Project / Fund</button>
                    </div>
                </div>

                <!-- 6. CURRENT ASSETS + BALANCE CHECK -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M6 9v6M18 9v6"/></svg>
                        </div>
                        <div>
                            <h3>Current Assets</h3>
                            <p>Money, stock and receivables that convert into cash within a year.</p>
                        </div>
                        <span class="qm-step">Step 6</span>
                    </div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>Inventories (&#8377;)</label><input type="number" id="bsCaInv" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                        <div class="inv-gen-field"><label>Trade Receivables (&#8377;)</label><input type="number" id="bsCaTrade" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                    </div>
                    <div class="doc-split" style="margin-top:10px;">
                        <div class="inv-gen-field"><label>Cash &amp; Cash Equivalents (&#8377;)</label><input type="number" id="bsCaCash" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                        <div class="inv-gen-field"><label>Other Current Assets (&#8377;)</label><input type="number" id="bsCaOther" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                    </div>

                    <div class="note-box green" id="bsBalBox" style="margin-top:16px;">
                        <div class="note-ico" id="bsBalIco">&#10003;</div>
                        <div>
                            <h4 id="bsBalTitle">Balance sheet balanced</h4>
                            <p id="bsBalText" style="margin:0;">Both sides are equal.</p>
                            <p id="bsBalHint" style="margin:6px 0 0;font-size:11.5px;"></p>
                        </div>
                    </div>
                </div>

                <!-- 7. COMPARATIVES -->
                <div class="qm-card" id="bsCompCard" style="display:none;">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/></svg>
                        </div>
                        <div>
                            <h3>Previous Year Comparatives</h3>
                            <p>Closing figures of the immediately preceding period. Every value here is in rupees.</p>
                        </div>
                        <span class="qm-step">Step 7</span>
                    </div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>Capital (prev.) (&#8377;)</label><input type="number" id="bsPYCapital" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                        <div class="inv-gen-field"><label>Reserves &amp; Surplus (prev.) (&#8377;)</label><input type="number" id="bsPYReserves" value="0" step="0.01" oninput="bsUpdate()"></div>
                    </div>
                    <div class="doc-split" style="margin-top:10px;">
                        <div class="inv-gen-field"><label>Long-term Borrowings (prev.) (&#8377;)</label><input type="number" id="bsPYNclBorrow" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                        <div class="inv-gen-field"><label>Lease Liabilities (prev.) (&#8377;)</label><input type="number" id="bsPYNclLease" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                    </div>
                    <div class="inv-gen-field" style="margin-top:10px;"><label>Provisions &mdash; Employee Benefit (prev.) (&#8377;)</label><input type="number" id="bsPYNclProv" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                    <div class="doc-split" style="margin-top:10px;">
                        <div class="inv-gen-field"><label>Trade Payables (prev.) (&#8377;)</label><input type="number" id="bsPYClTrade" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                        <div class="inv-gen-field"><label>Short-term Borrowings (prev.) (&#8377;)</label><input type="number" id="bsPYClBorrow" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                    </div>
                    <div class="doc-split" style="margin-top:10px;">
                        <div class="inv-gen-field"><label>Other Payables (prev.) (&#8377;)</label><input type="number" id="bsPYClOther" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                        <div class="inv-gen-field"><label>Provisions for Tax (prev.) (&#8377;)</label><input type="number" id="bsPYClProv" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                    </div>
                    <div class="doc-split" style="margin-top:10px;">
                        <div class="inv-gen-field"><label>Fixed Assets (prev.) (&#8377;)</label><input type="number" id="bsPYPpe" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                        <div class="inv-gen-field"><label>Intangible Assets (prev.) (&#8377;)</label><input type="number" id="bsPYIntang" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                    </div>
                    <div class="doc-split" style="margin-top:10px;">
                        <div class="inv-gen-field"><label>Long-term Investments (prev.) (&#8377;)</label><input type="number" id="bsPYInvest" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                        <div class="inv-gen-field"><label>Inventories (prev.) (&#8377;)</label><input type="number" id="bsPYInv" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                    </div>
                    <div class="doc-split" style="margin-top:10px;">
                        <div class="inv-gen-field"><label>Trade Receivables (prev.) (&#8377;)</label><input type="number" id="bsPYTrade" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                        <div class="inv-gen-field"><label>Cash &amp; Bank (prev.) (&#8377;)</label><input type="number" id="bsPYCash" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                    </div>
                    <div class="inv-gen-field" style="margin-top:10px;"><label>Other Current Assets (prev.) (&#8377;)</label><input type="number" id="bsPYOther" value="0" min="0" step="0.01" oninput="bsUpdate()"></div>
                </div>

                <!-- 8. NOTES & SIGN-OFF -->
                <div class="qm-card">
                    <div class="qm-card-head">
                        <div class="qm-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>
                        </div>
                        <div>
                            <h3>Notes to Accounts &amp; Sign-off</h3>
                            <p>Each line you type becomes a numbered note under the statement.</p>
                        </div>
                        <span class="qm-step">Step 8</span>
                    </div>
                    <div class="inv-gen-field" style="margin-bottom:10px;"><label>Notes to Accounts (one note per line)</label><textarea id="bsNotes" rows="4" placeholder="Fixed assets are stated at cost less accumulated depreciation.&#10;Trade receivables are considered fully recoverable."></textarea></div>
                    <div class="doc-split">
                        <div class="inv-gen-field"><label>Prepared By</label><input type="text" id="bsPrepBy" placeholder="Name of preparer"></div>
                        <div class="inv-gen-field"><label>Checked By</label><input type="text" id="bsChkBy" placeholder="Name of reviewer"></div>
                    </div>
                    <div class="inv-gen-field" style="margin-top:10px;"><label>Partner / Authorised Signatory</label><input type="text" id="bsPartner" placeholder="Name of the partner signing"></div>
                </div>

                <!-- ACTIONS -->
                <div class="qm-actions doc-no-print">
                    <button type="button" class="qm-btn qm-btn-primary" onclick="bsPreviewJump()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        Preview
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="bsPrint()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="1"/><path d="M6 17h12v4H6z"/></svg>
                        Print
                    </button>
                    <button type="button" class="qm-btn qm-btn-light" onclick="bsReset()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                        Reset
                    </button>
                    <button type="button" class="qm-btn qm-btn-primary" style="flex:2" onclick="bsPDF()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="M7 11l5 5 5-5"/><path d="M4 21h16"/></svg>
                        Download PDF
                    </button>
                </div>
            </div>

            <!-- ===== RIGHT: LIVE PREVIEW ===== -->
            <div class="qm-preview-card">
                <div class="qm-preview-head">
                    <h3><span class="qm-live-dot"></span> Live Preview</h3>
                    <span class="qm-preview-hint" id="pHint">A4 Landscape &middot; My Metaji</span>
                </div>
                <div class="inv-gen-preview" id="bsPreview">
                    <div class="doc-sheet doc-sheet-lg" id="bsSheet">
                        <div class="doc-hd">
                            <div class="doc-org">
                                <h2 id="pCoName">Your Firm</h2>
                                <p id="pCoAddr" style="display:none"></p>
                                <p><strong>PAN:</strong> <span id="pCoPan">&mdash;</span> &middot; <strong>GSTIN:</strong> <span id="pCoGstin">&mdash;</span></p>
                                <p id="pCoStateRow" style="display:none"><span id="pCoState"></span></p>
                            </div>
                            <div class="doc-id">
                                <div class="doc-title" id="pTitle">BALANCE SHEET</div>
                                <p id="pAsOn">as at &mdash;</p>
                                <p><strong>Financial Year:</strong> <span id="pFy">&mdash;</span></p>
                                <p><strong>Figures in:</strong> <span id="pScaleLbl">&mdash;</span></p>
                                <p><strong>Basis:</strong> <span id="pBasis">&mdash;</span> &middot; <span id="pEntity">&mdash;</span></p>
                            </div>
                        </div>

                        <div class="bs-2up" id="bsTwoUp">
                            <table class="bs-table">
                                <thead>
                                    <tr><th colspan="2">Equity and Liabilities</th></tr>
                                    <tr><th>Particulars</th><th class="num" data-bs-amthd>Amount (&#8377;)</th></tr>
                                </thead>
                                <tbody id="bsLeftBody"></tbody>
                            </table>
                            <table class="bs-table">
                                <thead>
                                    <tr><th colspan="2">Assets</th></tr>
                                    <tr><th>Particulars</th><th class="num" data-bs-amthd>Amount (&#8377;)</th></tr>
                                </thead>
                                <tbody id="bsRightBody"></tbody>
                            </table>
                        </div>

                        <div id="bsHorizWrap" style="display:none;">
                            <table class="bs-table">
                                <thead>
                                    <tr>
                                        <th>Particulars</th>
                                        <th class="num" data-bs-amthd>Amount (&#8377;)</th>
                                        <th class="num bs-prev-col" data-bs-prevhd>Previous Year</th>
                                        <th class="num bs-prev-col" data-bs-chghd>Change %</th>
                                    </tr>
                                </thead>
                                <tbody id="bsHorizBody"></tbody>
                            </table>
                        </div>

                        <div class="bs-2up" id="bsSncWrap" style="display:none;">
                            <table class="bs-table">
                                <thead>
                                    <tr><th colspan="2">A. Sources of Funds</th></tr>
                                    <tr><th>Particulars</th><th class="num" data-bs-amthd>Amount (&#8377;)</th></tr>
                                </thead>
                                <tbody id="bsSncABody"></tbody>
                            </table>
                            <table class="bs-table">
                                <thead>
                                    <tr><th colspan="2">B. Application of Funds</th></tr>
                                    <tr><th>Particulars</th><th class="num" data-bs-amthd>Amount (&#8377;)</th></tr>
                                </thead>
                                <tbody id="bsSncBBody"></tbody>
                            </table>
                        </div>

                        <table class="bs-table" style="margin-top:14px;">
                            <thead>
                                <tr><th>Balance Check</th><th class="num" data-bs-amthd>Amount (&#8377;)</th></tr>
                            </thead>
                            <tbody id="bsFootBody"></tbody>
                        </table>

                        <div id="bsProjWrap" style="display:none; margin-top:14px;">
                            <p class="doc-sec-title">Project / Fund-wise Asset Allocation</p>
                            <table class="bs-table">
                                <thead>
                                    <tr><th>Project / Fund</th><th>Asset Head</th><th class="num" data-bs-amthd>Amount (&#8377;)</th><th class="num">% of Total Assets</th></tr>
                                </thead>
                                <tbody id="bsProjBody"></tbody>
                            </table>
                        </div>

                        <div id="bsNotesWrap" style="display:none;">
                            <p class="doc-sec-title">Notes to Accounts</p>
                            <ol id="bsNotesList" style="font-size:10px;color:#475569;margin:0;padding-left:18px;line-height:1.7;"></ol>
                        </div>

                        <div class="doc-sig" id="bsSigRow">
                            <div><div class="doc-sig-line">Prepared By</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;" id="pPrepBy">&mdash;</div></div>
                            <div><div class="doc-sig-line">Checked By</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;" id="pChkBy">&mdash;</div></div>
                            <div><div class="doc-sig-line">Partner / Authorised Signatory</div><div style="font-size:8.5px;color:#94A3B8;margin-top:2px;" id="pPartner">&mdash;</div></div>
                        </div>
                        <div class="doc-ft">This is a computer-generated statement &middot; Generated by <strong>My Metaji</strong></div>
                    </div>
                </div>

                <div class="doc-stats" style="margin-top:14px;">
                    <div class="doc-stat"><span>Total Assets</span><strong id="pStatAssets">&#8377;0.00</strong></div>
                    <div class="doc-stat"><span>Total Equity &amp; Liabilities</span><strong id="pStatTel">&#8377;0.00</strong></div>
                    <div class="doc-stat amber"><span>Net Worth</span><strong id="pStatNw">&#8377;0.00</strong></div>
                    <div class="doc-stat grey"><span>Working Capital (CA &minus; CL)</span><strong id="pStatWc">&#8377;0.00</strong></div>
                </div>
            </div>

<!-- ===================== ENTITY GUIDE ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>Which Balance Sheet Format Suits Your Entity?</h2>
        <p>The Companies (Accounting Standards) Rules and Schedule III of the Companies Act, 2013 decide the vertical format for a company. A proprietorship or partnership firm has no such prescription, so Indian practice follows the same Schedule III skeleton &mdash; pick the variant that matches what your bank, buyer or tax department expects to see.</p>
        <div class="fmt-table-wrap" style="margin-top:20px;">
            <table class="fmt-table">
                <thead>
                    <tr><th>Entity</th><th>Statement Expected</th><th>Pick This Format</th><th>Must Show</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Proprietorship</strong><br><span style="font-size:11.5px;color:#64748B;">Sole trader / HUF</span></td>
                        <td>Statement of financial position drawn by the CA for a bank loan or GST scrutiny</td>
                        <td><strong>Traditional</strong> or <strong>Condensed</strong></td>
                        <td><span class="fmt-req yes">Owner&rsquo;s capital</span> <span class="fmt-req yes">Both sides equal</span></td>
                    </tr>
                    <tr>
                        <td><strong>Partnership Firm</strong><br><span style="font-size:11.5px;color:#64748B;">Registered or unregistered</span></td>
                        <td>Balance sheet drawn on the basis of the Indian Partnership Act, 1932</td>
                        <td><strong>Traditional</strong>, with partners&rsquo; capital and current accounts in reserves</td>
                        <td><span class="fmt-req yes">Partners&rsquo; capital</span> <span class="fmt-req cond">Firm&rsquo;s P&amp;L in reserves</span></td>
                    </tr>
                    <tr>
                        <td><strong>LLP</strong><br><span style="font-size:11.5px;color:#64748B;">Limited Liability Partnership</span></td>
                        <td>Statement of financial position under the LLP Rules, 2009</td>
                        <td><strong>SNC</strong> or <strong>Horizontal</strong></td>
                        <td><span class="fmt-req yes">Contribution of partners</span> <span class="fmt-req yes">Profit for the year</span></td>
                    </tr>
                    <tr>
                        <td><strong>Private Limited Company</strong><br><span style="font-size:11.5px;color:#64748B;">Unlisted company</span></td>
                        <td>Schedule III vertical balance sheet audited by a statutory auditor</td>
                        <td><strong>Traditional</strong> (Schedule III), or <strong>Project-wise</strong> for project finance</td>
                        <td><span class="fmt-req yes">Share capital</span> <span class="fmt-req yes">Reserves &amp; surplus</span> <span class="fmt-req yes">Notes to accounts</span></td>
                    </tr>
                    <tr>
                        <td><strong>Multi-project / NEP</strong><br><span style="font-size:11.5px;color:#64748B;">Agri, infra, real estate</span></td>
                        <td>Balance sheet plus a project-wise asset and fund statement for lending banks</td>
                        <td><strong>Project-wise / Fund Balance Sheet</strong></td>
                        <td><span class="fmt-req yes">Project allocation table</span> <span class="fmt-req cond">Loan sanction heads</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="note-box gold" style="margin-top:20px;">
            <div class="note-ico">&#128203;</div>
            <div>
                <h4>Why a balance sheet must balance &mdash; and the Schedule III basics</h4>
                <ul>
                    <li><strong>Accounting identity:</strong> every rupee the business owns (asset) has been funded either by the owners (equity) or by somebody you owe (liability). So Total Equity and Liabilities can never differ from Total Assets &mdash; a difference is always a mistake in a line, never a real position.</li>
                    <li><strong>Vertical order is fixed:</strong> Schedule III runs Share Capital &rarr; Reserves &amp; Surplus &rarr; Non-Current Liabilities &rarr; Current Liabilities on one side, and Non-Current Assets &rarr; Current Assets on the other. Auditors and bankers check the heads in exactly that sequence.</li>
                    <li><strong>Profit becomes a reserve:</strong> profit for the period is transferred to Reserves and Surplus on the equity side. Leave it out and the equity side falls short, which is the single most common reason a hand-made balance sheet refuses to tally.</li>
                    <li><strong>Sub-totals are derived, never typed:</strong> every sub-total and grand total on this page is computed from the lines above it, so the total can never drift away from the sum of its parts.</li>
                    <li><strong>Always show the previous year:</strong> a balance sheet is read as a movement. Banks and lenders judge the trend, not a single date, which is why the horizontal format carries a change-percentage column.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===================== HOW TO ===================== -->
<section class="gst-faq" style="background:#fff;">
    <div class="gst-wrap">
        <h2>How to Prepare a Balance Sheet</h2>
        <div class="gst-how-grid">
            <div class="gst-how-card">
                <div class="gst-how-num">1</div>
                <h3>Pick the Format</h3>
                <p>Choose Traditional, Horizontal, SNC, Condensed or Project-wise. The heads regroup, the preview switches layout and the PDF orientation follows the format.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">2</div>
                <h3>Enter the Assets</h3>
                <p>Type closing stock, sundry debtors, bank and cash balance, fixed assets at net book value and investments. Keep the trial balance open beside you.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">3</div>
                <h3>Enter Equity &amp; Liabilities</h3>
                <p>Add capital, move the profit for the period into reserves, then list payables, bank overdraft, loans and provisions under current and non-current liabilities.</p>
            </div>
            <div class="gst-how-card">
                <div class="gst-how-num">4</div>
                <h3>Balance, Sign &amp; Download</h3>
                <p>Let the balance check turn green, then download the landscape A4 PDF with the notes, the stated balance and the Prepared By / Checked By sign-off.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SEO + RELATED ===================== -->
<section class="gst-faq">
    <div class="gst-wrap">
        <h2>What is a Balance Sheet?</h2>
        <p>A <strong>balance sheet</strong> (Vaivasthavittan) is a statement of financial position that shows what a business owns on one side and where that money came from on the other, as at a single date &mdash; normally the last day of the financial year, the last day of a quarter, or simply the date of a bank loan application. The left-hand side is <strong>Equity and Liabilities</strong>: share capital or the owner&rsquo;s capital, reserves and surplus, long-term borrowings, trade payables and other current liabilities. The right-hand side is <strong>Assets</strong>: property, plant and equipment at net book value, investments, inventories, trade receivables and cash. Every rupee of assets has been financed by one of those sources, which is why the two totals must be identical &mdash; this accounting identity is the foundation of double-entry bookkeeping.</p>
        <p>For Indian businesses the presentation is not a free choice. A company must follow the <strong>vertical format of Schedule III</strong> of the Companies Act, 2013 read with the Indian Accounting Standards, which is exactly the two-column layout this tool draws: equity and liabilities on the left, assets on the right, with fixed heads in a prescribed order. A proprietorship or partnership firm has no statutory format, but banks, buyers and the GST officer are used to the same skeleton, so a <strong>condensed</strong> statement with only the legally useful heads works just as well for a small shop. Two practical points follow. First, under the accrual system the profit for the period is never a separate head &mdash; it is carried to Reserves and Surplus on the equity side, and any business filing GST returns must tie the turnover declared in GSTR-1 and GSTR-3B back to the revenue and closing stock in these books. Second, a balance sheet is a comparison, not a snapshot: lenders almost always ask for the previous year&rsquo;s figures beside the current ones, which is why the horizontal variant prints a change-percentage column. Unclaimed input tax credit sitting in the GST ledger, tax deducted at source and payable to the government, and drawings that should have reduced capital are the lines most often left out of hand-made balance sheets &mdash; and each omission is exactly what the balance check on this page catches.</p>

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
        <h2>Balance Sheets That Close Themselves</h2>
        <p><?= BRAND_NAME ?> pulls opening balances, GST ledgers and bank statements straight into your financial statements, so the balance sheet builds itself every month without a single manual entry.</p>
        <a href="#download" class="gst-cta-btn">Download <?= BRAND_NAME ?> Free &rarr;</a>
    </div>
</section>

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="assets/js/doc-utils.js"></script>
<script>
/* ---------- Line item schema: one editable input per head ---------- */
const BS_ITEMS = {
    capital:   { id: 'bsEqCapital',  prev: 'bsPYCapital',  lbl: 'Share Capital' },
    reserves:  { id: 'bsEqReserves', prev: 'bsPYReserves', lbl: 'Reserves and Surplus' },
    nclBorrow: { id: 'bsNclBorrow', prev: 'bsPYNclBorrow', lbl: 'Long-term Borrowings' },
    nclLease:  { id: 'bsNclLease',  prev: 'bsPYNclLease',  lbl: 'Lease Liabilities' },
    nclProv:   { id: 'bsNclProv',   prev: 'bsPYNclProv',   lbl: 'Provisions \u2014 employee benefit & others' },
    clTrade:   { id: 'bsClTrade',   prev: 'bsPYClTrade',   lbl: 'Trade Payables' },
    clBorrow:  { id: 'bsClBorrow',  prev: 'bsPYClBorrow',  lbl: 'Short-term Borrowings (bank OD & others)' },
    clOther:   { id: 'bsClOther',   prev: 'bsPYClOther',   lbl: 'Other Payables (accruals & advances)' },
    clProv:    { id: 'bsClProv',    prev: 'bsPYClProv',    lbl: 'Provisions for tax & expenses' },
    ppe:       { id: 'bsNcaPpe',    prev: 'bsPYPpe',       lbl: 'Property, Plant & Equipment (net block)' },
    intang:    { id: 'bsNcaIntang', prev: 'bsPYIntang',    lbl: 'Intangible Assets' },
    invest:    { id: 'bsNcaInvest', prev: 'bsPYInvest',    lbl: 'Long-term Investments' },
    inv:       { id: 'bsCaInv',     prev: 'bsPYInv',       lbl: 'Inventories (stock & stores)' },
    tradeRec:  { id: 'bsCaTrade',   prev: 'bsPYTrade',     lbl: 'Trade Receivables (sundry debtors)' },
    cash:      { id: 'bsCaCash',    prev: 'bsPYCash',      lbl: 'Cash & Cash Equivalents' },
    otherCa:   { id: 'bsCaOther',   prev: 'bsPYOther',     lbl: 'Other Current Assets (advance & prepaid)' }
};

const BS_PROJ_HEADS = ['Non-Current Assets', 'Fixed Assets & Capital Work in Progress', 'Investments',
    'Inventories', 'Trade Receivables', 'Cash & Bank Balances', 'Equity Contribution'];

/* ---------- Statement format variants ---------- */
const BS_FORMATS = {
    traditional: {
        label: 'Traditional Balance Sheet', tag: 'Schedule III vertical', ico: '\u{1F4CA}', accent: '#2563EB',
        tags: ['Schedule III', 'Two column', 'Banker ready'],
        title: 'BALANCE SHEET', layout: 'twoup', project: false,
        leftTot: 'Total Equity and Liabilities', rightTot: 'Total Assets',
        desc: 'The Schedule III vertical presentation used by Indian CAs and bankers: Equity and Liabilities on the left, Assets on the right, every group closed with a ruled sub-total.',
        fields: 'Share capital, reserves & surplus, non-current liabilities, current liabilities, property plant & equipment, current assets',
        groups: {
            left: [
                { tag: 'eq', sect: 'EQUITY AND LIABILITIES', items: ['capital', 'reserves'], sub: null },
                { tag: 'ncl', sect: 'NON-CURRENT LIABILITIES', items: ['nclBorrow', 'nclLease', 'nclProv'], sub: 'Sub-total \u2014 Non-Current Liabilities' },
                { tag: 'cl', sect: 'CURRENT LIABILITIES', items: ['clTrade', 'clBorrow', 'clOther', 'clProv'], sub: 'Sub-total \u2014 Current Liabilities' }
            ],
            right: [
                { tag: 'nca', sect: 'NON-CURRENT ASSETS', items: ['ppe', 'intang', 'invest'], sub: 'Sub-total \u2014 Non-Current Assets' },
                { tag: 'ca', sect: 'CURRENT ASSETS', items: ['inv', 'tradeRec', 'cash', 'otherCa'], sub: 'Sub-total \u2014 Current Assets' }
            ]
        }
    },
    horizontal: {
        label: 'Horizontal Balance Sheet', tag: 'Current year vs previous year', ico: '\u{1F4C8}', accent: '#0D9488',
        tags: ['Comparative', 'Change %', 'Period columns'],
        title: 'HORIZONTAL BALANCE SHEET', layout: 'horiz', project: false,
        leftTot: 'Total Equity and Liabilities', rightTot: 'Total Assets',
        desc: 'Every head listed once with the current year, the previous year and the change in percentage side by side \u2014 the layout bankers and lenders use while appraising credit.',
        fields: 'All Schedule III heads with current year, previous year and change % columns',
        groups: {
            left: [
                { tag: 'eq', sect: 'EQUITY AND LIABILITIES', items: ['capital', 'reserves'], sub: null },
                { tag: 'ncl', sect: 'NON-CURRENT LIABILITIES', items: ['nclBorrow', 'nclLease', 'nclProv'], sub: 'Sub-total \u2014 Non-Current Liabilities' },
                { tag: 'cl', sect: 'CURRENT LIABILITIES', items: ['clTrade', 'clBorrow', 'clOther', 'clProv'], sub: 'Sub-total \u2014 Current Liabilities' }
            ],
            right: [
                { tag: 'nca', sect: 'NON-CURRENT ASSETS', items: ['ppe', 'intang', 'invest'], sub: 'Sub-total \u2014 Non-Current Assets' },
                { tag: 'ca', sect: 'CURRENT ASSETS', items: ['inv', 'tradeRec', 'cash', 'otherCa'], sub: 'Sub-total \u2014 Current Assets' }
            ]
        }
    },
    snc: {
        label: 'SNC (Simplified) Balance Sheet', tag: 'Statement of financial position', ico: '\u{1F4CB}', accent: '#4F46E5',
        tags: ['SNC', 'Sources & application', 'Readable'],
        title: 'BALANCE SHEET (SNC)', layout: 'snc', project: false,
        leftTot: 'Total (A) \u2014 Sources of Funds', rightTot: 'Total (B) \u2014 Application of Funds',
        desc: 'Statement of Financial Position in the simplified SNC style: funds raised on the left, funds applied on the right, closing as Total (A) and Total (B) which must be equal.',
        fields: 'Sources of funds (equity, non-current and current liabilities) and application of funds (fixed assets, investments, current assets)',
        groups: {
            left: [
                { tag: 'eq', sect: 'A. EQUITY AND LIABILITIES', items: ['capital', 'reserves'], sub: null },
                { tag: 'ncl', sect: 'NON-CURRENT LIABILITIES', items: ['nclBorrow', 'nclLease', 'nclProv'], sub: 'Sub-total \u2014 Non-Current Liabilities' },
                { tag: 'cl', sect: 'CURRENT LIABILITIES', items: ['clTrade', 'clBorrow', 'clOther', 'clProv'], sub: 'Sub-total \u2014 Current Liabilities' }
            ],
            right: [
                { tag: 'nca', sect: 'FIXED ASSETS & INTANGIBLES', items: ['ppe', 'intang'], sub: 'Sub-total \u2014 Fixed & Intangible Assets' },
                { tag: 'inv', sect: 'INVESTMENTS', items: ['invest'], sub: 'Sub-total \u2014 Investments' },
                { tag: 'ca', sect: 'CURRENT ASSETS', items: ['inv', 'tradeRec', 'cash', 'otherCa'], sub: 'Sub-total \u2014 Current Assets' }
            ]
        }
    },
    condensed: {
        label: 'Condensed / Startup Balance Sheet', tag: 'Minimum usable heads', ico: '\u{1F4A3}', accent: '#D97706',
        tags: ['Startup', 'Merged heads', 'Quick close'],
        title: 'BALANCE SHEET (CONDENSED)', layout: 'twoup', project: false,
        leftTot: 'Total Equity and Liabilities', rightTot: 'Total Assets',
        desc: 'The smallest statement a small proprietorship can defend: related heads are merged into single lines while every rupee you typed still lands on one of the two sides.',
        fields: 'Owner\u2019s funds, borrowings, leases & provisions, trade & other payables, fixed assets, investments, current assets',
        groups: {
            left: [
                { tag: 'eq', sect: 'EQUITY AND LIABILITIES', items: ['capital', 'reserves'], sub: null },
                { tag: 'ncl', sect: 'LIABILITIES', items: ['nclBorrow', 'nclLease', 'nclProv', 'clBorrow'], mergedLbl: 'Borrowings, Leases & Provisions', sub: 'Sub-total \u2014 Liabilities' },
                { tag: 'cl', sect: 'LIABILITIES (CONTINUED)', items: ['clTrade', 'clOther', 'clProv'], mergedLbl: 'Trade & Other Payables', sub: 'Sub-total \u2014 Payables' }
            ],
            right: [
                { tag: 'nca', sect: 'FIXED & INTANGIBLE ASSETS', items: ['ppe', 'intang'], mergedLbl: 'Fixed Assets (net block)', sub: 'Sub-total \u2014 Fixed & Intangible Assets' },
                { tag: 'inv', sect: 'INVESTMENTS', items: ['invest'], sub: 'Sub-total \u2014 Investments' },
                { tag: 'ca', sect: 'CURRENT ASSETS', items: ['inv', 'tradeRec', 'cash', 'otherCa'] }
            ]
        }
    },
    project: {
        label: 'Project-wise / Fund Balance Sheet', tag: 'With project allocation table', ico: '\u{1F3D7}', accent: '#059669',
        tags: ['Project finance', 'Fund flow', 'Lender ready'],
        title: 'BALANCE SHEET (PROJECT-WISE)', layout: 'twoup', project: true,
        leftTot: 'Total Equity and Liabilities', rightTot: 'Total Assets',
        desc: 'The full statement plus a project / fund-wise asset allocation table showing how much of the total assets is committed to each project and each asset head.',
        fields: 'All Schedule III heads plus project / fund-wise asset allocation with share of total assets',
        groups: {
            left: [
                { tag: 'eq', sect: 'EQUITY AND LIABILITIES', items: ['capital', 'reserves'], sub: null },
                { tag: 'ncl', sect: 'NON-CURRENT LIABILITIES', items: ['nclBorrow', 'nclLease', 'nclProv'], sub: 'Sub-total \u2014 Non-Current Liabilities' },
                { tag: 'cl', sect: 'CURRENT LIABILITIES', items: ['clTrade', 'clBorrow', 'clOther', 'clProv'], sub: 'Sub-total \u2014 Current Liabilities' }
            ],
            right: [
                { tag: 'nca', sect: 'NON-CURRENT ASSETS', items: ['ppe', 'intang', 'invest'], sub: 'Sub-total \u2014 Non-Current Assets' },
                { tag: 'ca', sect: 'CURRENT ASSETS', items: ['inv', 'tradeRec', 'cash', 'otherCa'], sub: 'Sub-total \u2014 Current Assets' }
            ]
        }
    }
};

let bsFmt = 'traditional';
let bsRowId = 0;
let bsLast = null;

/* ---------- helpers ---------- */
function bsScale() { const v = parseFloat(DOC.val('bsScale')); return v > 0 ? v : 1; }
function bsUnit(s) { return s === 10000000 ? 'Crores' : s === 100000 ? 'Lakhs' : s === 1000 ? 'Thousands' : 'Rupees'; }
function bsEsc(t) { return String(t === undefined || t === null ? '' : t).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
function bsGrp(n) { return Math.abs(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
function bsSum(o, keys) { return DOC.round2(keys.reduce(function (a, k) { return a + (o[k] || 0); }, 0)); }

/* screen amount, scaled to the chosen unit */
function bsAmt(v) {
    const n = DOC.round2((Number(v) || 0) / bsScale());
    return (n < 0 ? '\u2212' : '') + DOC.RUPEE + bsGrp(n);
}
/* prose amount with the unit spelled out */
function bsUnitMoney(v) { const s = bsScale(); return bsAmt(v) + (s === 1 ? '' : ' ' + bsUnit(s)); }
/* PDF amount \u2014 helvetica has no rupee glyph, so the statement prints "Rs." */
function bsRs(v) {
    const n = DOC.round2((Number(v) || 0) / bsScale());
    return n < 0 ? '(Rs. ' + bsGrp(n) + ')' : 'Rs. ' + bsGrp(n);
}
/* unscaled rupees, for narrative text and the amount in words */
function bsRsRaw(v) { return 'Rs. ' + bsGrp(DOC.round2(Number(v) || 0)); }

function bsLabel(key, d) {
    if (key === 'capital') {
        if (/Company/i.test(d.co.entity)) return 'Share Capital';
        if (/LLP|Partnership/i.test(d.co.entity)) return 'Partners\u2019 Capital Accounts';
        return 'Owner\u2019s Capital';
    }
    if (key === 'reserves') {
        if (/Company/i.test(d.co.entity)) return 'Reserves and Surplus (including P&L surplus)';
        return 'Reserves and Surplus / Retained Earnings';
    }
    return BS_ITEMS[key].lbl;
}

function bsEqSub(d) {
    if (/Company/i.test(d.co.entity)) return 'Sub-total \u2014 Shareholders\u2019 Funds';
    if (/LLP|Partnership/i.test(d.co.entity)) return 'Sub-total \u2014 Partners\u2019 Funds';
    return 'Sub-total \u2014 Owner\u2019s Funds';
}

/* ---------- Format picker ---------- */
function bsRenderFormats() {
    const grid = document.getElementById('bsFmtGrid');
    let h = '';
    Object.keys(BS_FORMATS).forEach(function (k) {
        const f = BS_FORMATS[k];
        h += '<button type="button" class="fmt-card' + (k === bsFmt ? ' active' : '') + '" data-bs-fmt="' + k + '" aria-pressed="' + (k === bsFmt) + '">'
            + '<span class="fmt-mini" style="--fmt-accent:' + f.accent + '">'
            + '<span class="fmt-mini-top"><span class="fmt-mini-brand"></span><span class="fmt-mini-tag"></span></span>'
            + '<span class="fmt-mini-ln w70"></span><span class="fmt-mini-ln w45"></span><span class="fmt-mini-ln w70"></span>'
            + '<span class="fmt-mini-tot"></span></span>'
            + '<span class="fmt-card-body">'
            + '<span class="fmt-card-title">' + f.ico + ' ' + f.label + '</span>'
            + '<span class="fmt-card-sub">' + f.tag + '</span>'
            + '<span class="fmt-tags">' + f.tags.map(function (t) { return '<span class="fmt-tag">' + t + '</span>'; }).join('') + '</span>'
            + '</span></button>';
    });
    grid.innerHTML = h;
    grid.querySelectorAll('[data-bs-fmt]').forEach(function (b) {
        b.addEventListener('click', function () { bsSetFormat(b.dataset.bsFmt); });
    });
    bsSetFormat(bsFmt, true);
}

function bsSetFormat(k, quiet) {
    const f = BS_FORMATS[k];
    if (!f) return;
    bsFmt = k;
    document.querySelectorAll('[data-bs-fmt]').forEach(function (b) {
        const on = b.dataset.bsFmt === k;
        b.classList.toggle('active', on);
        b.setAttribute('aria-pressed', on);
    });
    document.getElementById('bsFmtIco').textContent = f.ico;
    document.getElementById('bsFmtTitle').textContent = f.label;
    document.getElementById('bsFmtDesc').textContent = f.desc;
    document.getElementById('bsFmtFields').textContent = f.fields;
    DOC.text('pTitle', f.title);
    DOC.showIf('bsProjForm', f.project);
    if (f.layout === 'horiz') DOC.setToggle('bsOptComp', true);
    if (!quiet) bsUpdate();
}

/* ---------- Project / fund rows ---------- */
function bsAddProj(seed) {
    seed = seed || {};
    const id = bsRowId++;
    const opts = BS_PROJ_HEADS.map(function (h, i) {
        return '<option' + (i === 0 ? ' selected' : '') + '>' + h + '</option>';
    }).join('');
    document.getElementById('bsProjItems').insertAdjacentHTML('beforeend',
        '<div class="doc-item" data-prow="' + id + '">'
        + '<input type="text" data-bs-pname placeholder="Project or fund name" oninput="bsUpdate()">'
        + '<select data-bs-phead onchange="bsUpdate()">' + opts + '</select>'
        + '<input type="number" data-bs-pamt value="' + (seed.amt || 0) + '" min="0" step="0.01" oninput="bsUpdate()">'
        + '<button type="button" class="doc-item-del" onclick="bsDelProj(' + id + ')" title="Remove project" aria-label="Remove project">&times;</button>'
        + '</div>');
    bsUpdate();
}

function bsDelProj(id) {
    const e = document.querySelector('[data-prow="' + id + '"]');
    if (e) e.remove();
    bsUpdate();
}
/* ---------- Collect everything + compute every sub-total ---------- */
function bsCollect() {
    const f = BS_FORMATS[bsFmt];
    const scale = bsScale();
    const comp = !!DOC.val('bsOptComp');
    const cur = {}, prev = {};
    Object.keys(BS_ITEMS).forEach(function (k) {
        cur[k] = DOC.num(BS_ITEMS[k].id, 0);
        prev[k] = comp ? DOC.num(BS_ITEMS[k].prev, 0) : 0;
    });

    const K = {
        eq: ['capital', 'reserves'],
        ncl: ['nclBorrow', 'nclLease', 'nclProv'],
        cl: ['clTrade', 'clBorrow', 'clOther', 'clProv'],
        nca: ['ppe', 'intang', 'invest'],
        ca: ['inv', 'tradeRec', 'cash', 'otherCa']
    };
    const eq = bsSum(cur, K.eq), ncl = bsSum(cur, K.ncl), cl = bsSum(cur, K.cl);
    const nca = bsSum(cur, K.nca), ca = bsSum(cur, K.ca);
    const peq = bsSum(prev, K.eq), pncl = bsSum(prev, K.ncl), pcl = bsSum(prev, K.cl);
    const pnca = bsSum(prev, K.nca), pca = bsSum(prev, K.ca);
    const tel = DOC.round2(eq + ncl + cl), ta = DOC.round2(nca + ca);
    const ptel = DOC.round2(peq + pncl + pcl), pta = DOC.round2(pnca + pca);

    const projs = [];
    let projTotal = 0;
    document.querySelectorAll('#bsProjItems .doc-item').forEach(function (el) {
        const name = (el.querySelector('[data-bs-pname]').value || '').trim();
        const head = el.querySelector('[data-bs-phead]').value || '';
        const amt = DOC.round2(parseFloat(el.querySelector('[data-bs-pamt]').value) || 0);
        if (!name && amt === 0) return;
        projTotal += amt;
        projs.push({ name: name, head: head, amt: amt, pct: ta > 0 ? DOC.round2(amt / ta * 100).toFixed(2) : '0.00' });
    });

    const d = {
        f: f, scale: scale, unit: bsUnit(scale), comp: comp,
        cur: cur, prev: prev,
        eq: eq, ncl: ncl, cl: cl, nca: nca, ca: ca,
        peq: peq, pncl: pncl, pcl: pcl, pnca: pnca, pca: pca,
        tel: tel, ta: ta, ptel: ptel, pta: pta, diff: DOC.round2(tel - ta),
        nw: eq, pnw: peq, wc: DOC.round2(ca - cl), pwc: DOC.round2(pca - pcl),
        projs: projs, projTotal: DOC.round2(projTotal),
        co: {
            name: DOC.val('bsCoName'), addr: DOC.val('bsCoAddr'), pan: DOC.val('bsCoPan'),
            gstin: DOC.val('bsCoGstin'), state: DOC.val('bsCoState'), entity: DOC.val('bsEntity')
        },
        p: { fy: DOC.val('bsFy'), asOn: DOC.val('bsAsOn'), prevOn: DOC.val('bsPrevOn'), basis: DOC.val('bsBasis') },
        prep: DOC.val('bsPrepBy'), chk: DOC.val('bsChkBy'), signer: DOC.val('bsPartner')
    };
    d.ok = Math.abs(d.diff) < 0.01;
    d.notes = bsNotes(d);
    d.rows = bsBuildRows(d);
    return d;
}

function bsBuildRows(d) {
    const f = d.f;
    const showZero = !!DOC.val('bsOptZero');
    const out = { left: [], right: [], horiz: [] };
    const push = function (arr, t, lbl, c, p) {
        arr.push({ t: t, lbl: lbl, cur: DOC.round2(c), prev: DOC.round2(p) });
    };
    const side = function (arr, gp) {
        if (gp.sect) push(arr, 'sect', gp.sect, 0, 0);
        if (gp.mergedLbl) {
            push(arr, 'item', gp.mergedLbl, bsSum(d.cur, gp.items), bsSum(d.prev, gp.items));
        } else {
            gp.items.forEach(function (k) {
                const c = d.cur[k], p = d.prev[k];
                if (!showZero && c === 0 && p === 0) return;
                push(arr, 'item', bsLabel(k, d), c, p);
            });
        }
        if (gp.sect) push(arr, 'sub', gp.tag === 'eq' ? bsEqSub(d) : (gp.sub == null ? null : gp.sub), bsSum(d.cur, gp.items), bsSum(d.prev, gp.items));
    };
    f.groups.left.forEach(function (gp) { side(out.left, gp); });
    f.groups.right.forEach(function (gp) { side(out.right, gp); });
    push(out.left, 'total', f.leftTot, d.tel, d.ptel);
    push(out.right, 'total', f.rightTot, d.ta, d.pta);

    /* horizontal = one list of heads, period columns added by the renderer */
    f.groups.left.forEach(function (gp) { side(out.horiz, gp); });
    f.groups.right.forEach(function (gp) { side(out.horiz, gp); });
    push(out.horiz, 'rule', '', 0, 0);
    push(out.horiz, 'total', f.leftTot, d.tel, d.ptel);
    push(out.horiz, 'total', f.rightTot, d.ta, d.pta);
    return out;
}

function bsNotes(d) {
    const typed = DOC.val('bsNotes').split('\n')
        .map(function (l) { return l.trim(); })
        .filter(function (l) { return l.length; });
    if (typed.length) return typed;
    const out = [d.f.rightTot + ' as at ' + (DOC.fmtDate(d.p.asOn) || 'the date stated above') + ' amount to ' + bsRsRaw(d.ta) + ' (' + DOC.inWords(d.ta) + ').'];
    if (d.comp) out.push('Previous year figures relate to the position as at ' + (DOC.fmtDate(d.p.prevOn) || 'the immediately preceding period end') + '.');
    out.push('The statement is prepared on the ' + String(d.p.basis || 'accrual').toLowerCase() + ' basis from the books of account of ' + (d.co.name || 'the firm') + '; amounts are stated after providing for all known liabilities and contingent liabilities, if any, are not provided for.');
    return out;
}

function bsHint(d, brief) {
    const big = d.nca >= d.ca ? 'Property, Plant & Equipment' : 'Current Assets';
    if (brief) {
        return d.tel > d.ta
            ? 'Recheck Reserves and Surplus (closing profit of the period) and ' + big + '.'
            : 'Recheck ' + big + ' and the cash and bank balance against your statement of affairs.';
    }
    return 'Equity and Liabilities ' + (d.tel > d.ta ? 'exceed' : 'fall short of') + ' Total Assets by ' + bsUnitMoney(Math.abs(d.diff))
        + '. Recheck the largest asset head (' + big + '), carry the profit or loss of the period into '
        + (d.tel > d.ta ? 'Reserves and Surplus on the equity side' : 'the profit & loss account and then into Reserves and Surplus')
        + ', and check every merged line of the chosen format. Sub-totals and totals are computed \u2014 only the input lines are editable.';
}

/* ---------- Sheet row rendering ---------- */
function bsRowAttr(r) {
    if (r.t === 'total') return ' class="bs-total' + (r.cur < 0 ? ' bs-negative' : '') + '"';
    if (r.t === 'sub') return ' class="bs-sub bs-rule"';
    if (r.t === 'rule') return ' class="bs-rule"';
    return '';
}
function bsPrevCell(d, r) {
    if (!d.comp) return '';
    return '<div style="font-size:8.5px;color:#94A3B8;font-weight:400;line-height:1.35;margin-top:1px;">' + bsAmt(r.prev) + '</div>';
}
function bsRow2Up(d, r) {
    if (r.t === 'sect') return '<tr class="bs-sect"><td colspan="2">' + r.lbl + '</td></tr>';
    if (r.t === 'rule') return '<tr class="bs-rule"><td colspan="2"></td></tr>';
    return '<tr' + bsRowAttr(r) + '><td>' + r.lbl + '</td><td class="num">' + bsAmt(r.cur) + bsPrevCell(d, r) + '</td></tr>';
}
function bsChg(comp, c, p) {
    if (!comp) return '\u2014';
    if (p === 0) return c === 0 ? '\u2014' : 'New';
    const v = DOC.round2((c - p) / Math.abs(p) * 100);
    return (v > 0 ? '+' : v < 0 ? '\u2212' : '') + Math.abs(v).toFixed(2) + '%';
}
function bsRowHoriz(d, r) {
    if (r.t === 'sect') return '<tr class="bs-sect"><td colspan="4">' + r.lbl + '</td></tr>';
    if (r.t === 'rule') return '<tr class="bs-rule"><td colspan="4"></td></tr>';
    return '<tr' + bsRowAttr(r) + '><td>' + r.lbl + '</td>'
        + '<td class="num">' + bsAmt(r.cur) + '</td>'
        + '<td class="num bs-prev-col">' + bsAmt(r.prev) + '</td>'
        + '<td class="num bs-prev-col">' + bsChg(d.comp, r.cur, r.prev) + '</td></tr>';
}

/* ---------- Preview render ---------- */
function bsUpdate() {
    const d = bsCollect();
    bsLast = d;
    const f = d.f;

    /* header */
    DOC.text('pCoName', d.co.name);
    DOC.text('pCoAddr', d.co.addr);
    DOC.showIf('pCoAddr', !!d.co.addr);
    DOC.text('pCoPan', d.co.pan);
    DOC.text('pCoGstin', d.co.gstin);
    DOC.text('pCoState', d.co.state);
    DOC.showIf('pCoStateRow', !!d.co.state);
    DOC.text('pTitle', f.title);
    DOC.text('pAsOn', 'as at ' + (DOC.fmtDate(d.p.asOn) || '\u2014'));
    DOC.text('pFy', d.p.fy);
    DOC.text('pScaleLbl', d.scale > 1 ? d.unit : 'Rupees (\u20B9)');
    DOC.text('pBasis', d.p.basis);
    DOC.text('pEntity', d.co.entity);
    DOC.text('pHint', 'A4 Landscape \u00B7 Figures in ' + (d.scale > 1 ? d.unit : 'Rupees'));
    document.getElementById('bsCapitalLbl').textContent = bsLabel('capital', d) + ' (\u20B9)';
    document.querySelectorAll('[data-bs-amthd]').forEach(function (t) {
        t.textContent = 'Amount (' + (d.scale > 1 ? '\u20B9 ' + d.unit : '\u20B9') + ')';
    });
    document.querySelectorAll('[data-bs-prevhd]').forEach(function (t) {
        t.textContent = 'Previous Year (' + (DOC.fmtDate(d.p.prevOn) || 'as per books') + ')';
    });

    /* layout switch */
    DOC.showIf('bsTwoUp', f.layout === 'twoup');
    DOC.showIf('bsSncWrap', f.layout === 'snc');
    DOC.showIf('bsHorizWrap', f.layout === 'horiz');

    DOC.html('bsLeftBody', d.rows.left.map(function (r) { return bsRow2Up(d, r); }).join(''));
    DOC.html('bsRightBody', d.rows.right.map(function (r) { return bsRow2Up(d, r); }).join(''));
    DOC.html('bsSncABody', d.rows.left.map(function (r) { return bsRow2Up(d, r); }).join(''));
    DOC.html('bsSncBBody', d.rows.right.map(function (r) { return bsRow2Up(d, r); }).join(''));
    DOC.html('bsHorizBody', d.rows.horiz.map(function (r) { return bsRowHoriz(d, r); }).join(''));

    /* balance proof row */
    let foot = '<tr class="bs-sub"><td>' + f.leftTot + '</td><td class="num">' + bsAmt(d.tel) + '</td></tr>';
    foot += '<tr class="bs-sub"><td>' + f.rightTot + '</td><td class="num">' + bsAmt(d.ta) + '</td></tr>';
    foot += '<tr class="bs-total"><td colspan="2" style="' + (d.ok ? 'color:#047857;' : 'color:#B91C1C;') + '">'
        + (d.ok ? '\u2713 Balance sheet balanced \u2014 both sides are equal' : '\u2717 Out of balance by ' + bsUnitMoney(Math.abs(d.diff)) + ' \u2014 the two sides must be equal')
        + '</td></tr>';
    DOC.html('bsFootBody', foot);

    /* balance status in the form */
    const box = DOC.el('bsBalBox');
    box.className = 'note-box ' + (d.ok ? 'green' : 'red');
    DOC.el('bsBalIco').textContent = d.ok ? '\u2713' : '\u2717';
    DOC.text('bsBalTitle', d.ok ? 'Balance sheet balanced' : 'Balance sheet does not balance');
    DOC.text('bsBalText', d.ok
        ? 'Total Equity and Liabilities and Total Assets are both ' + bsUnitMoney(d.tel) + '.'
        : 'Out of balance by ' + bsUnitMoney(Math.abs(d.diff)) + ' \u2014 the two sides must be equal.');
    DOC.text('bsBalHint', d.ok
        ? 'Net worth is ' + bsUnitMoney(d.nw) + ' and working capital is ' + bsUnitMoney(d.wc) + '. Every sub-total above is computed, so nothing has been typed over a total.'
        : bsHint(d));

    /* project allocation */
    DOC.showIf('bsProjWrap', f.project);
    if (f.project) {
        let pb = d.projs.map(function (p) {
            return '<tr><td>' + bsEsc(p.name || '\u2014') + '</td><td>' + bsEsc(p.head) + '</td><td class="num">' + bsAmt(p.amt) + '</td><td class="num">' + p.pct + '%</td></tr>';
        }).join('');
        if (!d.projs.length) pb = '<tr><td colspan="4" style="text-align:center;color:#94A3B8;">No project added yet</td></tr>';
        const over = d.projTotal > d.ta + 0.01;
        pb += '<tr class="bs-total"><td>Total Allocated</td><td></td><td class="num' + (over ? ' bs-negative' : '') + '">' + bsAmt(d.projTotal) + '</td><td class="num">'
            + (d.ta > 0 ? DOC.round2(d.projTotal / d.ta * 100).toFixed(2) : '0.00') + '%</td></tr>';
        if (Math.abs(d.projTotal - d.ta) > 0.01) {
            pb += '<tr><td colspan="4" style="font-size:9px;color:#92400E;">Allocation of ' + bsAmt(d.projTotal)
                + ' is ' + (over ? 'more than' : 'less than') + ' Total Assets of ' + bsAmt(d.ta)
                + '. The difference is held centrally or is unallocated.</td></tr>';
        }
        DOC.html('bsProjBody', pb);
    }

    /* notes */
    DOC.showIf('bsNotesWrap', !!DOC.val('bsOptNotes'));
    if (DOC.val('bsOptNotes')) {
        DOC.html('bsNotesList', d.notes.map(function (n) { return '<li>' + bsEsc(n) + '</li>'; }).join(''));
    }

    /* signatures */
    DOC.showIf('bsSigRow', !!DOC.val('bsOptSig'));
    DOC.text('pPrepBy', d.prep);
    DOC.text('pChkBy', d.chk);
    DOC.text('pPartner', d.signer);

    /* live stats */
    DOC.text('pStatAssets', bsAmt(d.ta));
    DOC.text('pStatTel', bsAmt(d.tel));
    DOC.text('pStatNw', bsAmt(d.nw));
    DOC.text('pStatWc', bsAmt(d.wc));

    /* comparative columns */
    bsSyncComp();
}

function bsSyncComp() {
    const on = !!DOC.val('bsOptComp');
    const card = document.getElementById('bsCompCard');
    if (card) card.style.display = on ? '' : 'none';
    document.querySelectorAll('.bs-prev-col').forEach(function (c) { c.style.display = on ? '' : 'none'; });
}

/* ---------- Actions ---------- */
function bsPreviewJump() { bsUpdate(); DOC.scrollToPreview('bsPreview'); }
function bsPrint() { bsUpdate(); window.print(); }

function bsReset() {
    ['bsCoName', 'bsCoAddr', 'bsCoPan', 'bsCoGstin', 'bsFy', 'bsNotes', 'bsPrepBy', 'bsChkBy', 'bsPartner'].forEach(function (id) {
        const e = document.getElementById(id); if (e) e.value = '';
    });
    document.getElementById('bsCoState').value = '';
    document.getElementById('bsEntity').value = 'Proprietorship';
    document.getElementById('bsBasis').value = 'Accrual';
    document.getElementById('bsScale').value = '1';
    document.getElementById('bsAsOn').value = DOC.todayISO();
    document.getElementById('bsPrevOn').value = DOC.addDaysISO(-365);
    Object.keys(BS_ITEMS).forEach(function (k) {
        ['id', 'prev'].forEach(function (p) {
            const e = document.getElementById(BS_ITEMS[k][p]); if (e) e.value = 0;
        });
    });
    document.getElementById('bsProjItems').querySelectorAll('.doc-item').forEach(function (e) { e.remove(); });
    bsAddProj();
    DOC.setToggle('bsOptComp', false);
    DOC.setToggle('bsOptZero', true);
    DOC.setToggle('bsOptSig', true);
    DOC.setToggle('bsOptNotes', true);
    bsSetFormat('traditional', true);
    bsUpdate();
}
/* ---------- PDF: landscape A4, two side-by-side statements ---------- */
const MG_PDF = 12;

function bsPdfSide(doc, x, w, y, rows, d, accent, opts) {
    opts = opts || {};
    const W = doc.internal.pageSize.getWidth();
    const nCols = opts.change ? 4 : (d.comp ? 3 : 2);
    const body = [], meta = [];
    rows.forEach(function (r) {
        const a = [r.lbl];
        if (r.t === 'sect' || r.t === 'rule') {
            for (let i = 1; i < nCols; i++) a.push('');
            meta.push(r.t);
            body.push(a);
            return;
        }
        a.push(bsRs(r.cur));
        if (opts.change) { a.push(bsRs(r.prev)); a.push(bsChg(d.comp, r.cur, r.prev)); }
        else if (d.comp) a.push(bsRs(r.prev));
        meta.push(r.t);
        body.push(a);
    });
    const head = ['Particulars', opts.amtHd || 'Current Year'];
    if (opts.change) head.push('Previous Year', 'Change %');
    else if (d.comp) head.push('Previous Year');

    const nw = nCols === 2 ? 30 : 24;
    const styles = { 0: { cellWidth: DOC.round2(w - nw * (nCols - 1)) } };
    for (let i = 1; i < nCols; i++) styles[i] = { cellWidth: nw, halign: 'right' };
    if (nCols === 2) styles[1].fontStyle = 'bold';

    return DOC.pdfTable(doc, head, body, {
        startY: y, accent: accent,
        extra: {
            margin: { left: x, right: DOC.round2(W - x - w), top: MG_PDF, bottom: MG_PDF },
            tableWidth: DOC.round2(w),
            columnStyles: styles,
            didParseCell: function (c) {
                if (c.section !== 'body') return;
                const t = meta[c.row.index] || 'item';
                if (t === 'sect') {
                    c.cell.styles.fontStyle = 'bold';
                    c.cell.styles.fontSize = 7;
                    c.cell.styles.fillColor = [241, 245, 249];
                    c.cell.styles.textColor = [30, 41, 59];
                } else if (t === 'sub') {
                    c.cell.styles.fontStyle = 'bold';
                } else if (t === 'total') {
                    c.cell.styles.fontStyle = 'bold';
                    c.cell.styles.fillColor = [252, 253, 254];
                    c.cell.styles.lineWidth = 0.5;
                    c.cell.styles.lineColor = [30, 41, 59];
                }
            }
        }
    });
}

function bsPDF() {
    bsUpdate();
    const d = bsLast;
    const Ctor = window.jspdf && window.jspdf.jsPDF;
    if (!Ctor) { alert('PDF library is still loading. Please try again in a moment.'); return; }
    /* two columns do not fit portrait A4, so the statement is always landscape */
    const doc = new Ctor('p', 'mm', 'landscape', 'a4');
    const W = doc.internal.pageSize.getWidth();
    const H = doc.internal.pageSize.getHeight();
    const MG = MG_PDF;
    const accent = DOC.currentAccentRGB();
    const f = d.f;
    const amtHd = d.scale > 1 ? 'Amount (Rs. in ' + d.unit + ')' : 'Amount (Rs.)';

    let y = DOC.pdfHeader(doc, {
        org: d.co.name || 'Your Firm',
        orgSize: 16,
        left: [
            { value: d.co.addr || 'Registered office address' },
            { value: 'PAN: ' + (d.co.pan || '\u2014') + (d.co.gstin ? '   \u00B7   GSTIN: ' + d.co.gstin : '') },
            { value: (d.co.state ? d.co.state + '   \u00B7   ' : '') + d.co.entity }
        ],
        title: f.title,
        titleSize: 12.5,
        right: [
            { label: 'As on', value: DOC.fmtDate(d.p.asOn) || '\u2014' },
            { label: 'Financial year', value: d.p.fy || '\u2014' },
            { label: 'Basis', value: d.p.basis },
            { label: 'Figures in', value: d.scale > 1 ? 'Rs. in ' + d.unit : 'Rs.' }
        ],
        accent: accent, margin: MG, height: 40
    });

    if (f.layout === 'horiz') {
        y = bsPdfSide(doc, MG, W - MG * 2, y, d.rows.horiz, d, accent, { change: d.comp, amtHd: 'Current Year' }) + 6;
    } else {
        const gap = 6;
        const halfW = (W - MG * 2 - gap) / 2;
        const endA = bsPdfSide(doc, MG, halfW, y, d.rows.left, d, accent, { amtHd: amtHd });
        const endB = bsPdfSide(doc, MG + halfW + gap, halfW, y, d.rows.right, d, accent, { amtHd: amtHd });
        y = Math.max(endA, endB) + 6;
    }

    /* the balance, stated in words as well as figures */
    doc.setFontSize(8.2);
    doc.setFont('helvetica', 'bold');
    if (d.ok) {
        doc.setTextColor(4, 120, 87);
        doc.text('Balance sheet balanced \u2014 ' + f.leftTot + ' ' + bsRs(d.tel) + ' equals ' + f.rightTot + ' ' + bsRs(d.ta) + '.', MG, y);
    } else {
        doc.setTextColor(185, 28, 28);
        doc.text('Out of balance by ' + bsRsRaw(Math.abs(d.diff)) + ' \u2014 ' + bsHint(d, true), MG, y);
    }
    y += 5;
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(7.6);
    doc.setTextColor(71, 85, 105);
    doc.text('Total assets amount to ' + bsRsRaw(d.ta) + ' (' + DOC.inWords(d.ta) + ').', MG, y);
    y += 6;
    doc.setTextColor(51, 65, 85);

    /* project allocation */
    if (f.project) {
        const pbody = d.projs.length
            ? d.projs.map(function (p) { return [p.name || '\u2014', p.head, bsRs(p.amt), p.pct + '%']; })
            : [['No project added', '\u2014', '\u2014', '\u2014']];
        pbody.push(['Total Allocated', '', bsRs(d.projTotal), d.ta > 0 ? DOC.round2(d.projTotal / d.ta * 100).toFixed(2) + '%' : '0.00%']);
        if (y > H - 40) { doc.addPage(); y = 18; }
        y = DOC.pdfSection(doc, MG, y, 'PROJECT / FUND-WISE ASSET ALLOCATION', 100) + 2;
        y = DOC.pdfTable(doc, ['Project / Fund', 'Asset Head', 'Amount (' + (d.scale > 1 ? 'Rs. in ' + d.unit : 'Rs.') + ')', '% of Total Assets'], pbody, {
            startY: y, accent: accent,
            extra: {
                margin: { left: MG, right: MG, top: MG, bottom: MG },
                columnStyles: { 2: { halign: 'right', fontStyle: 'bold' }, 3: { halign: 'right' } }
            }
        }) + 6;
    }

    /* notes to accounts */
    if (DOC.val('bsOptNotes') && d.notes.length) {
        if (y > H - 30) { doc.addPage(); y = 18; }
        y = DOC.pdfSection(doc, MG, y, 'NOTES TO ACCOUNTS', 100) + 2;
        d.notes.forEach(function (n, i) {
            doc.setFontSize(7.6);
            doc.setTextColor(71, 85, 105);
            const lines = doc.splitTextToSize((i + 1) + '. ' + n, W - MG * 2 - 5);
            doc.text(lines, MG, y);
            y += lines.length * 3.4;
        });
        y += 4;
    }

    /* sign-off */
    if (DOC.val('bsOptSig')) {
        if (y > H - 30) { doc.addPage(); y = 18; }
        const colW = (W - MG * 2) / 3;
        const sy = y + 12;
        doc.setDrawColor(148, 163, 184);
        doc.setLineWidth(0.3);
        [0, 1, 2].forEach(function (i) {
            const x = MG + colW * i + colW / 2;
            doc.line(x - 38, sy, x + 38, sy);
        });
        doc.setFontSize(7.4);
        doc.setTextColor(100, 116, 139);
        doc.text('Prepared By', MG + colW * 0.5, sy + 4, { align: 'center' });
        doc.text('Checked By', MG + colW * 1.5, sy + 4, { align: 'center' });
        doc.setFont('helvetica', 'bold');
        doc.text('Partner / Authorised Signatory', MG + colW * 2.5, sy + 4, { align: 'center' });
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(7.2);
        doc.text(d.prep || '', MG + colW * 0.5, sy + 8, { align: 'center' });
        doc.text(d.chk || '', MG + colW * 1.5, sy + 8, { align: 'center' });
        doc.setFont('helvetica', 'bold');
        doc.text(d.signer || ('For ' + (d.co.name || 'the firm')), MG + colW * 2.5, sy + 8, { align: 'center' });
        doc.setFont('helvetica', 'normal');
        y = sy + 12;
    }

    DOC.pdfFooter(doc, y, (d.co.name || 'Balance sheet') + (d.p.asOn ? '  \u00B7  As on ' + DOC.fmtDate(d.p.asOn) : ''));
    doc.save(((d.co.name || 'balance-sheet').replace(/[^\w\-]+/g, '_')) + '-balance-sheet-' + (d.p.asOn || 'as-on') + '.pdf');
}

/* ---------- Boot ---------- */
(function () {
    DOC.fillStates('bsCoState', 'Select state');
    const y = new Date().getFullYear();
    document.getElementById('bsFy').value = (y - 1) + '-' + String(y).slice(2);
    document.getElementById('bsAsOn').value = DOC.todayISO();
    document.getElementById('bsPrevOn').value = DOC.addDaysISO(-365);
    bsRenderFormats();
    bsAddProj();

    const ids = ['bsCoName', 'bsCoAddr', 'bsCoPan', 'bsCoGstin', 'bsCoState', 'bsEntity', 'bsFy',
        'bsAsOn', 'bsPrevOn', 'bsBasis', 'bsScale', 'bsNotes', 'bsPrepBy', 'bsChkBy', 'bsPartner',
        'bsOptComp', 'bsOptZero', 'bsOptSig', 'bsOptNotes'];
    Object.keys(BS_ITEMS).forEach(function (k) { ids.push(BS_ITEMS[k].id, BS_ITEMS[k].prev); });
    DOC.bind(ids, bsUpdate);
    DOC.bindSwatches('data-bs-accent', bsUpdate);

    bsUpdate();
})();
</script>

        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
