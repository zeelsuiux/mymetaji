<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';

$pageTitle = 'Dashboard';

/* ── Subadmin Dashboard ── */
if (is_subadmin()) {
    $today = date('Y-m-d');
    $id = current_member_id();
    $name = current_member_name();
    $allI = db_get_all('invoices');
    $allP = db_get_all('payments');
    $myI = array_values(array_filter($allI, fn($r) => ((string)($r['created_by_id'] ?? '') === $id) || (strcasecmp(trim((string)($r['created_by'] ?? '')), $name) === 0)));
    $myP = array_values(array_filter($allP, fn($r) => ((string)($r['created_by_id'] ?? '') === $id) || (strcasecmp(trim((string)($r['created_by'] ?? '')), $name) === 0)));
    $todaySelling = 0;
    $todayCollection = 0;
    $pending = 0;
    $myNos = [];
    foreach ($myI as $r) {
        $myNos[$r['invoice_no'] ?? ''] = true;
        if (substr((string)($r['created_at'] ?? $r['date'] ?? ''), 0, 10) === $today) $todaySelling += (float)($r['amount'] ?? 0);
        $paid = 0;
        foreach ($allP as $p) if (($p['invoice_no'] ?? '') === ($r['invoice_no'] ?? '')) $paid += (float)($p['amount'] ?? 0);
        $pending += max(0, (float)($r['amount'] ?? 0) - $paid);
    }
    foreach ($myP as $p) if (substr((string)($p['date'] ?? $p['created_at'] ?? ''), 0, 10) === $today && (($p['invoice_no'] ?? '') === '' || isset($myNos[$p['invoice_no'] ?? '']))) $todayCollection += (float)($p['amount'] ?? 0);

    // Recent invoices for subadmin
    $recentInvoices = array_slice(array_reverse($myI), 0, 5);

    require __DIR__ . '/layout_top.php'; ?>
    <main class="dashboard-page">

        <!-- Greeting -->
        <div class="db-greeting">
            <div>
                <h2><?php $h = (int)date('H'); echo $h < 12 ? 'Good Morning' : ($h < 17 ? 'Good Afternoon' : 'Good Evening'); ?>, <?= htmlspecialchars(current_member_name()) ?></h2>
                <p>Today's summary</p>
            </div>
        </div>

        <!-- Hero Actions -->
        <div class="db-hero-actions">
            <a href="module.php?m=invoices&action=new" class="db-hero-btn primary">
                <span class="db-hero-icon">📄</span>
                <span class="db-hero-text">
                    <span class="db-hero-label">New Invoice</span>
                    <span class="db-hero-sub">Create bill</span>
                </span>
            </a>
            <a href="module.php?m=quotations&action=new" class="db-hero-btn secondary">
                <span class="db-hero-icon">📋</span>
                <span class="db-hero-text">
                    <span class="db-hero-label">New Quotation</span>
                    <span class="db-hero-sub">Send quote</span>
                </span>
            </a>
        </div>

        <!-- Today's KPIs -->
        <div class="db-kpi-row">
            <a href="module.php?m=invoices" class="db-kpi green">
                <div class="db-kpi-icon">💰</div>
                <div class="db-kpi-info">
                    <span class="db-kpi-label">Sales</span>
                    <span class="db-kpi-value">₹<?= number_format($todaySelling, 0) ?></span>
                </div>
            </a>
            <a href="finance.php" class="db-kpi blue">
                <div class="db-kpi-icon">📥</div>
                <div class="db-kpi-info">
                    <span class="db-kpi-label">Collected</span>
                    <span class="db-kpi-value">₹<?= number_format($todayCollection, 0) ?></span>
                </div>
            </a>
            <a href="finance.php" class="db-kpi orange">
                <div class="db-kpi-icon">⏳</div>
                <div class="db-kpi-info">
                    <span class="db-kpi-label">Pending</span>
                    <span class="db-kpi-value">₹<?= number_format($pending, 0) ?></span>
                </div>
            </a>
        </div>

        <!-- Quick Access Row -->
        <div class="db-quick-row">
            <a href="module.php?m=customers" class="db-quick-link">
                <span>👤</span> Customers
            </a>
            <a href="module.php?m=leads" class="db-quick-link">
                <span>🎯</span> Leads
            </a>
            <a href="module.php?m=tasks" class="db-quick-link">
                <span>✅</span> Tasks
            </a>
            <a href="module.php?m=invoices" class="db-quick-link">
                <span>📑</span> All Bills
            </a>
        </div>

        <!-- Collapsible Recent Invoices -->
        <?php if (!empty($recentInvoices)): ?>
        <div class="db-collapsible" id="section-recent-sub">
            <button class="db-collapse-toggle" onclick="toggleSection('recent-sub')">
                <span>📑 Recent Invoices</span>
                <span class="db-collapse-arrow" id="arrow-recent-sub">›</span>
            </button>
            <div class="db-collapse-content" id="content-recent-sub">
                <div class="db-recent-list">
                    <?php foreach ($recentInvoices as $inv): 
                        $invDate = date('d M', strtotime($inv['created_at'] ?? $inv['date'] ?? ''));
                        $paid = 0;
                        foreach ($allP as $p) if (($p['invoice_no'] ?? '') === ($inv['invoice_no'] ?? '')) $paid += (float)($p['amount'] ?? 0);
                        $status = $paid >= (float)($inv['amount'] ?? 0) ? 'Paid' : 'Pending';
                    ?>
                    <a href="module.php?m=invoices&action=view&id=<?= $inv['id'] ?>" class="db-recent-item">
                        <div class="db-recent-left">
                            <span class="db-recent-no">#<?= htmlspecialchars($inv['invoice_no'] ?? '-') ?></span>
                            <span class="db-recent-meta"><?= htmlspecialchars($inv['customer'] ?? 'Walk-in') ?> · <?= $invDate ?></span>
                        </div>
                        <div class="db-recent-right">
                            <span class="db-recent-amount">₹<?= number_format((float)($inv['amount'] ?? 0), 0) ?></span>
                            <span class="db-recent-badge <?= $status === 'Paid' ? 'green' : 'orange' ?>"><?= $status ?></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <a href="module.php?m=invoices" class="db-collapse-viewall">View All Invoices →</a>
            </div>
        </div>
        <?php endif; ?>

    </main>

<script>
function toggleSection(name) {
    var content = document.getElementById('content-' + name);
    var arrow = document.getElementById('arrow-' + name);
    if (content.classList.contains('open')) {
        content.classList.remove('open');
        arrow.classList.remove('rotated');
    } else {
        content.classList.add('open');
        arrow.classList.add('rotated');
    }
}
</script>
<?php require __DIR__ . '/layout_bottom.php';
    exit;
}

/* ══════════════════════════════════════════════════════════════
 *  ADMIN DASHBOARD
 * ══════════════════════════════════════════════════════════════ */
$data = db_load();
$today = date('Y-m-d');
$hour = (int)date('H');

// ── Today's stats ──
$todayInvoices = 0;
$todayAmount = 0;
$todayCollection = 0;
foreach ($data['invoices'] as $i) {
    if (substr($i['created_at'] ?? $i['date'] ?? '', 0, 10) === $today) {
        $todayInvoices++;
        $todayAmount += (float)($i['amount'] ?? 0);
    }
}
foreach ($data['payments'] as $p) {
    if (substr($p['created_at'] ?? $p['date'] ?? '', 0, 10) === $today) {
        $todayCollection += (float)($p['amount'] ?? 0);
    }
}

// ── Balances ──
$shopProfile = shop_profile();
$cashOpening = (float)($shopProfile['cash_opening_balance'] ?? 0);
$bankOpening = (float)($shopProfile['bank_opening_balance'] ?? 0);
$totalInvoiceAmt = array_sum(array_map(fn($i) => (float)($i['amount'] ?? 0), $data['invoices']));
$totalPaymentAmt = array_sum(array_map(fn($p) => (float)($p['amount'] ?? 0), $data['payments']));
$cashCollected = 0; $onlineCollected = 0;
foreach ($data['payments'] as $p) {
    if (strcasecmp($p['mode'] ?? '', 'cash') === 0) $cashCollected += (float)($p['amount'] ?? 0);
    else $onlineCollected += (float)($p['amount'] ?? 0);
}
$cashExpenses = 0; $onlineExpenses = 0;
foreach ($data['expenses'] as $e) {
    if (strcasecmp($e['mode'] ?? '', 'online') === 0) $onlineExpenses += (float)($e['amount'] ?? 0);
    else $cashExpenses += (float)($e['amount'] ?? 0);
}
$cashBalance = $cashOpening + $cashCollected - $cashExpenses;
$onlineBalance = $bankOpening + $onlineCollected - $onlineExpenses;
$totalPending = max(0, $totalInvoiceAmt - $totalPaymentAmt);
$totalExpense = array_sum(array_map(fn($e) => (float)($e['amount'] ?? 0), $data['expenses']));

// ── Alerts ──
$overdueInvoices = get_overdue_invoices();
$pendingTasks = get_pending_tasks();
$lowStock = array_values(array_filter($data['products'] ?? [], fn($p) => (float)($p['stock_qty'] ?? 0) <= 5 && (float)($p['stock_qty'] ?? 0) > 0));
$riskyCustomers = get_risky_customers(10, 0);
$alerts = [];
if (!empty($overdueInvoices)) $alerts[] = ['icon' => '⚠️', 'text' => count($overdueInvoices) . ' overdue invoice(s)', 'color' => '#F44336', 'link' => 'module.php?m=invoices'];
if (!empty($pendingTasks)) $alerts[] = ['icon' => '📋', 'text' => count($pendingTasks) . ' pending task(s)', 'color' => '#2196F3', 'link' => 'module.php?m=tasks'];
if (!empty($lowStock)) $alerts[] = ['icon' => '📦', 'text' => count($lowStock) . ' product(s) low stock', 'color' => '#FF9800', 'link' => 'module.php?m=products'];
if (!empty($riskyCustomers)) $alerts[] = ['icon' => '🛡️', 'text' => count($riskyCustomers) . ' risky customer(s)', 'color' => '#9C27B0', 'link' => 'module.php?m=customers'];

// ── Recent invoices ──
$recentInvoices = array_slice(array_reverse($data['invoices']), 0, 8);

// ── Graph data (last 6 months) ──
$graphMonths = [];
$graphCursor = new DateTime('first day of this month');
for ($i = 5; $i >= 0; $i--) {
    $month = (clone $graphCursor)->modify('-' . $i . ' months');
    $key = $month->format('Y-m');
    $graphMonths[$key] = ['label' => $month->format('M'), 'invoices' => 0.0, 'payments' => 0.0];
}
foreach ($data['invoices'] as $inv) {
    $d = substr($inv['date'] ?? $inv['created_at'] ?? '', 0, 7);
    if (isset($graphMonths[$d])) $graphMonths[$d]['invoices'] += (float)($inv['amount'] ?? 0);
}
foreach ($data['payments'] as $pay) {
    $d = substr($pay['date'] ?? $pay['created_at'] ?? '', 0, 7);
    if (isset($graphMonths[$d])) $graphMonths[$d]['payments'] += (float)($pay['amount'] ?? 0);
}
$graphMax = 1.0;
foreach ($graphMonths as $m) $graphMax = max($graphMax, $m['invoices'], $m['payments']);

require __DIR__ . '/layout_top.php';
?>

<main class="dashboard-page">

    <!-- ═══ GREETING (compact) ═══ -->
    <div class="db-greeting">
        <div>
            <h2><?php echo $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening'); ?>, <?= htmlspecialchars(current_member_name()) ?></h2>
            <p><?= date('d M Y') ?> · <?= $todayInvoices ?> bills today</p>
        </div>
    </div>

    <!-- ═══ QUICK ACTIONS (HERO) ═══ -->
    <div class="db-hero-actions">
        <a href="module.php?m=invoices&action=new" class="db-hero-btn primary">
            <span class="db-hero-icon">📄</span>
            <span class="db-hero-text">
                <span class="db-hero-label">New Invoice</span>
                <span class="db-hero-sub">Create bill</span>
            </span>
        </a>
        <a href="module.php?m=quotations&action=new" class="db-hero-btn secondary">
            <span class="db-hero-icon">📋</span>
            <span class="db-hero-text">
                <span class="db-hero-label">New Quotation</span>
                <span class="db-hero-sub">Send quote</span>
            </span>
        </a>
        <a href="module.php?m=payments&action=new" class="db-hero-btn tertiary">
            <span class="db-hero-icon">💵</span>
            <span class="db-hero-text">
                <span class="db-hero-label">Add Payment</span>
                <span class="db-hero-sub">Record payment</span>
            </span>
        </a>
        <a href="module.php?m=customers&action=new" class="db-hero-btn quaternary">
            <span class="db-hero-icon">👤</span>
            <span class="db-hero-text">
                <span class="db-hero-label">Add Customer</span>
                <span class="db-hero-sub">New contact</span>
            </span>
        </a>
    </div>

    <!-- ═══ TODAY'S KPIs ═══ -->
    <div class="db-kpi-row">
        <a href="module.php?m=invoices" class="db-kpi green">
            <div class="db-kpi-icon">📄</div>
            <div class="db-kpi-info">
                <span class="db-kpi-label">Bills</span>
                <span class="db-kpi-value"><?= $todayInvoices ?></span>
            </div>
        </a>
        <a href="module.php?m=invoices" class="db-kpi blue">
            <div class="db-kpi-icon">💰</div>
            <div class="db-kpi-info">
                <span class="db-kpi-label">Sales</span>
                <span class="db-kpi-value">₹<?= number_format($todayAmount, 0) ?></span>
            </div>
        </a>
        <a href="finance.php" class="db-kpi teal">
            <div class="db-kpi-icon">📥</div>
            <div class="db-kpi-info">
                <span class="db-kpi-label">Collected</span>
                <span class="db-kpi-value">₹<?= number_format($todayCollection, 0) ?></span>
            </div>
        </a>
        <a href="finance.php" class="db-kpi orange">
            <div class="db-kpi-icon">⏳</div>
            <div class="db-kpi-info">
                <span class="db-kpi-label">Pending</span>
                <span class="db-kpi-value">₹<?= number_format($totalPending, 0) ?></span>
            </div>
        </a>
    </div>

    <!-- ═══ BALANCES ═══ -->
    <div class="db-balance-row">
        <div class="db-balance-card cash">
            <div class="db-balance-header">
                <span class="db-balance-dot green"></span>
                <span>Cash</span>
            </div>
            <div class="db-balance-amount">₹<?= number_format($cashBalance, 0) ?></div>
            <div class="db-balance-detail">In ₹<?= number_format($cashCollected, 0) ?> · Out ₹<?= number_format($cashExpenses, 0) ?></div>
        </div>
        <div class="db-balance-card bank">
            <div class="db-balance-header">
                <span class="db-balance-dot blue"></span>
                <span>Bank / UPI</span>
            </div>
            <div class="db-balance-amount">₹<?= number_format($onlineBalance, 0) ?></div>
            <div class="db-balance-detail">In ₹<?= number_format($onlineCollected, 0) ?> · Out ₹<?= number_format($onlineExpenses, 0) ?></div>
        </div>
    </div>

    <!-- ═══ ALERTS (compact single banner) ═══ -->
    <?php if (!empty($alerts)): ?>
    <div class="db-alerts-compact">
        <?php foreach ($alerts as $i => $alert): ?>
        <a href="<?= $alert['link'] ?>" class="db-alert-pill" style="background:<?= $alert['color'] ?>15;color:<?= $alert['color'] ?>">
            <span><?= $alert['icon'] ?></span>
            <span><?= $alert['text'] ?></span>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- ═══ QUICK ACCESS ROW ═══ -->
    <div class="db-quick-row">
        <a href="module.php?m=leads" class="db-quick-link">
            <span>🎯</span> Leads
        </a>
        <a href="module.php?m=tasks" class="db-quick-link">
            <span>✅</span> Tasks
        </a>
        <a href="finance.php" class="db-quick-link">
            <span>📊</span> Finance
        </a>
        <a href="module.php?m=invoices" class="db-quick-link">
            <span>📑</span> All Bills
        </a>
    </div>

    <!-- ═══ COLLAPSIBLE: RECENT INVOICES ═══ -->
    <div class="db-collapsible" id="section-recent">
        <button class="db-collapse-toggle" onclick="toggleSection('recent')">
            <span>📑 Recent Invoices</span>
            <span class="db-collapse-arrow" id="arrow-recent">›</span>
        </button>
        <div class="db-collapse-content" id="content-recent">
            <?php if (empty($recentInvoices)): ?>
                <div class="db-empty">No invoices yet.</div>
            <?php else: ?>
            <div class="db-recent-list">
                <?php foreach ($recentInvoices as $inv):
                    $invDate = date('d M', strtotime($inv['created_at'] ?? $inv['date'] ?? ''));
                    $paidAmt = 0;
                    foreach ($data['payments'] as $p) {
                        if (($p['invoice_no'] ?? '') === ($inv['invoice_no'] ?? '')) $paidAmt += (float)($p['amount'] ?? 0);
                    }
                    $invAmount = (float)($inv['amount'] ?? 0);
                    $invStatus = $paidAmt >= $invAmount ? 'Paid' : ($paidAmt > 0 ? 'Partial' : 'Pending');
                    $invStatusColor = $invStatus === 'Paid' ? 'green' : ($invStatus === 'Partial' ? 'blue' : 'orange');
                ?>
                <a href="module.php?m=invoices&action=view&id=<?= $inv['id'] ?>" class="db-recent-item">
                    <div class="db-recent-left">
                        <span class="db-recent-no">#<?= htmlspecialchars($inv['invoice_no'] ?? '-') ?></span>
                        <span class="db-recent-meta"><?= htmlspecialchars($inv['customer'] ?? 'Walk-in') ?> · <?= $invDate ?></span>
                    </div>
                    <div class="db-recent-right">
                        <span class="db-recent-amount">₹<?= number_format($invAmount, 0) ?></span>
                        <span class="db-recent-badge <?= $invStatusColor ?>"><?= $invStatus ?></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <a href="module.php?m=invoices" class="db-collapse-viewall">View All Invoices →</a>
        </div>
    </div>

    <!-- ═══ REVENUE CHART (Chart.js) ═══ -->
    <div class="db-chart-section">
        <div class="db-chart-header">
            <h3 class="db-chart-title">📈 Revenue Overview</h3>
            <div class="db-chart-legend">
                <span><i class="legend-invoice"></i> Invoiced</span>
                <span><i class="legend-payment"></i> Collected</span>
            </div>
        </div>
        <div class="db-chart-canvas-wrap">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- ═══ COLLAPSIBLE: RISKY CUSTOMERS ═══ -->
    <?php if (!empty($riskyCustomers)): ?>
    <?php
        $totalRiskAmount = array_sum(array_map(fn($rc) => $rc['pending_amount'], $riskyCustomers));
        $highRiskCount = count(array_filter($riskyCustomers, fn($rc) => $rc['risk_rating'] === 'High'));
        $medRiskCount = count(array_filter($riskyCustomers, fn($rc) => $rc['risk_rating'] === 'Medium'));
    ?>
    <div class="db-collapsible db-risk-collapsible" id="section-risk">
        <button class="db-collapse-toggle db-risk-toggle" onclick="toggleSection('risk')">
            <span>🛡️ Risky Customers (<?= count($riskyCustomers) ?>)</span>
            <span class="db-collapse-arrow" id="arrow-risk">›</span>
        </button>
        <div class="db-collapse-content" id="content-risk">
            <div class="db-risk-chips">
                <div class="db-risk-chip danger">
                    <span class="db-risk-chip-count"><?= $highRiskCount ?></span>
                    <span class="db-risk-chip-label">High</span>
                </div>
                <div class="db-risk-chip warning">
                    <span class="db-risk-chip-count"><?= $medRiskCount ?></span>
                    <span class="db-risk-chip-label">Medium</span>
                </div>
                <div class="db-risk-chip amount">
                    <span class="db-risk-chip-count">₹<?= number_format($totalRiskAmount, 0) ?></span>
                    <span class="db-risk-chip-label">At Risk</span>
                </div>
            </div>
            <div class="db-risk-list">
                <?php foreach ($riskyCustomers as $rc):
                    $rcColor = risk_color_class($rc['risk_rating']);
                    $rcBg = risk_bg_class($rc['risk_rating']);
                    $riskPercent = min(100, max(10, $rc['risk_score'] * 10));
                ?>
                <a href="<?= $rc['id'] > 0 ? 'customer_detail.php?id=' . $rc['id'] : 'module.php?m=customers' ?>" class="db-risk-card">
                    <div class="db-risk-card-top">
                        <div class="db-risk-avatar" style="background:<?= $rcBg ?>;color:<?= $rcColor ?>;">
                            <?= strtoupper(substr($rc['name'], 0, 1)) ?>
                        </div>
                        <div class="db-risk-card-info">
                            <span class="db-risk-card-name"><?= htmlspecialchars($rc['name']) ?></span>
                            <span class="db-risk-card-meta">
                                <?= $rc['overdue_count'] ?> overdue · <?= $rc['payment_delay'] ?>d delay
                                <?php if ($rc['credit_hold'] === 'Yes'): ?>
                                    <span class="db-risk-hold-badge">HOLD</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="db-risk-card-score" style="background:<?= $rcBg ?>;color:<?= $rcColor ?>;">
                            <?= $rc['risk_score'] ?>
                        </div>
                    </div>
                    <div class="db-risk-card-bottom">
                        <div class="db-risk-bar-wrap">
                            <div class="db-risk-bar" style="width:<?= $riskPercent ?>%;background:<?= $rcColor ?>;"></div>
                        </div>
                        <div class="db-risk-card-footer">
                            <span class="db-risk-badge <?= strtolower($rc['risk_rating']) ?>"><?= $rc['risk_rating'] ?></span>
                            <span class="db-risk-card-amount">₹<?= number_format($rc['pending_amount'], 0) ?></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <a href="module.php?m=customers" class="db-collapse-viewall">View All Customers →</a>
        </div>
    </div>
    <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function() {
    var labels = <?= json_encode(array_values(array_column($graphMonths, 'label'))) ?>;
    var invoiceData = <?= json_encode(array_values(array_column($graphMonths, 'invoices'))) ?>;
    var paymentData = <?= json_encode(array_values(array_column($graphMonths, 'payments'))) ?>;

    var ctx = document.getElementById('revenueChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Invoiced',
                    data: invoiceData,
                    backgroundColor: 'rgba(37, 99, 235, 0.85)',
                    borderColor: '#2563EB',
                    borderWidth: 0,
                    borderRadius: 6,
                    borderSkipped: false
                },
                {
                    label: 'Collected',
                    data: paymentData,
                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                    borderColor: '#10B981',
                    borderWidth: 0,
                    borderRadius: 6,
                    borderSkipped: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1E293B',
                    titleFont: { size: 12, weight: '600' },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ₹' + context.parsed.y.toLocaleString('en-IN');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11, weight: '600' }, color: '#94A3B8' },
                    border: { display: false }
                },
                y: {
                    grid: { color: '#F1F5F9' },
                    ticks: {
                        font: { size: 11, weight: '500' },
                        color: '#94A3B8',
                        callback: function(value) { return '₹' + value.toLocaleString('en-IN'); }
                    },
                    border: { display: false }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
})();
</script>

<script>
function toggleSection(name) {
    var content = document.getElementById('content-' + name);
    var arrow = document.getElementById('arrow-' + name);
    if (content.classList.contains('open')) {
        content.classList.remove('open');
        arrow.classList.remove('rotated');
    } else {
        content.classList.add('open');
        arrow.classList.add('rotated');
    }
}
</script>

<?php require __DIR__ . '/layout_bottom.php'; ?>
