<?php require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';

// Notification counts for sidebar badges
$notificationCounts = [];
if (is_admin()) {
    $overdueInvoices = get_overdue_invoices();
    if (!empty($overdueInvoices)) $notificationCounts['overdue'] = count($overdueInvoices);
    $pendingTasks = get_pending_tasks();
    if (!empty($pendingTasks)) $notificationCounts['tasks'] = count($pendingTasks);
    $expiringQuotations = get_expiring_quotations(7);
    if (!empty($expiringQuotations)) $notificationCounts['quotations'] = count($expiringQuotations);
}
$totalNotifs = ($notificationCounts['overdue'] ?? 0) + ($notificationCounts['tasks'] ?? 0) + ($notificationCounts['quotations'] ?? 0);
$notifications = [];
$readNotifs = [];
?>
<?php $desktopDownloadPath = 'downloads/My-Metaji-Setup.exe'; ?>
<?php $androidDownloadPath = 'downloads/My-Metaji.apk'; $androidAppAvailable = is_file(__DIR__ . '/downloads/My-Metaji.apk'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'My Metaji') ?></title>
    <link rel="stylesheet" href="assets/style.css?v=20260921g">
    <link rel="stylesheet" href="assets/dashboard.css?v=20260921g">
    <link rel="stylesheet" href="assets/modules-modern.css?v=20260921g">
    <link rel="stylesheet" href="assets/enhancements.css?v=20260921g">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.6/JsBarcode.all.min.js"></script>
    <style>
    /* Simplified Dashboard Override */
    .dashboard-page .db-greeting{padding:14px 18px!important;margin-bottom:12px!important;border-radius:12px!important}
    .dashboard-page .db-greeting h2{font-size:15px!important}
    .dashboard-page .db-greeting p{font-size:11px!important}
    .dashboard-page .db-hero-actions{gap:10px!important;margin-bottom:14px!important}
    .dashboard-page .db-hero-btn{padding:16px!important;border-radius:14px!important;min-height:70px!important}
    .dashboard-page .db-hero-icon{width:48px!important;height:48px!important;font-size:28px!important;border-radius:12px!important}
    .dashboard-page .db-hero-label{font-size:14px!important}
    .dashboard-page .db-hero-sub{font-size:11px!important}
    .dashboard-page .db-kpi-row{gap:10px!important;margin-bottom:14px!important}
    .dashboard-page .db-kpi{padding:12px 14px!important;gap:10px!important;border-radius:12px!important;min-height:52px!important}
    .dashboard-page .db-kpi-icon{width:36px!important;height:36px!important;font-size:18px!important;border-radius:10px!important}
    .dashboard-page .db-kpi-label{font-size:10px!important;margin-bottom:1px!important}
    .dashboard-page .db-kpi-value{font-size:17px!important}
    .dashboard-page .db-balance-row{gap:10px!important;margin-bottom:14px!important}
    .dashboard-page .db-balance-card{padding:14px 16px!important;border-radius:12px!important}
    .dashboard-page .db-balance-amount{font-size:20px!important}
    .dashboard-page .db-balance-detail{font-size:11px!important}
    .dashboard-page .db-alerts-compact{gap:6px!important;margin-bottom:14px!important}
    .dashboard-page .db-alert-pill{padding:8px 14px!important;font-size:12px!important;border-radius:20px!important;min-height:36px!important}
    .dashboard-page .db-quick-row{gap:8px!important;margin-bottom:14px!important}
    .dashboard-page .db-quick-link{padding:12px 8px!important;font-size:12px!important;border-radius:10px!important;min-height:44px!important}
    .dashboard-page .db-collapsible{margin-bottom:10px!important;border-radius:12px!important}
    .dashboard-page .db-collapse-toggle{padding:14px 16px!important;font-size:13px!important;min-height:48px!important}
    .dashboard-page .db-collapse-content.open{padding:0 16px 14px!important}
    .dashboard-page .db-collapse-viewall{padding:10px!important;font-size:12px!important;border-radius:8px!important}
    .dashboard-page .db-recent-item{padding:8px 6px!important;border-radius:8px!important}
    .dashboard-page .db-recent-no{font-size:12px!important}
    .dashboard-page .db-recent-meta{font-size:10.5px!important}
    .dashboard-page .db-recent-amount{font-size:13px!important}
    .dashboard-page .db-recent-badge{font-size:10px!important;padding:2px 8px!important}
    .dashboard-page .db-risk-chips{gap:8px!important;padding:10px 16px!important}
    .dashboard-page .db-risk-card{padding:12px 16px!important}
    .dashboard-page .db-risk-card-name{font-size:12.5px!important}
    .dashboard-page .db-risk-card-amount{font-size:13px!important}
    .dashboard-page .chart-bars{height:130px!important;padding:12px 0 0!important}
    .dashboard-page .chart-track{max-width:36px!important;gap:3px!important}
    .dashboard-page .chart-bar{border-radius:4px 4px 1px 1px!important}
    .dashboard-page .chart-values{font-size:9px!important;gap:2px!important;margin-bottom:5px!important}
    .dashboard-page .chart-month small{font-size:10px!important;margin-top:6px!important}
    .dashboard-page .db-empty{padding:24px 14px!important;font-size:12px!important}
    .dashboard-page .chart-legend{font-size:11px!important;gap:14px!important}
    </style>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#2563EB">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="My Metaji">
    <link rel="apple-touch-icon" href="assets/icon-192.png">
</head>

<body>
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
    <div class="shell">
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                <img class="brand-logo" src="assets/light-logo.png" alt="My Metaji">
            </div>
            <nav>
                <!-- ★ PRIORITY 1: MAIN -->
                <a href="index.php" class="<?= empty($activeModule) ? 'active' : '' ?>">
                    <?= icon('dashboard') ?> Dashboard
                </a>

                <!-- ★ PRIORITY 2: BILLING -->
                <span class="sidebar-section">Billing</span>
                <?php if (can_access_module('invoices')): ?>
                    <a href="module.php?m=invoices" class="<?= ($activeModule ?? '') === 'invoices' ? 'active' : '' ?>"><?= icon('invoices') ?> Invoices<?php if (!empty($notificationCounts['overdue'])): ?><span class="sidebar-badge red"><?= $notificationCounts['overdue'] ?></span><?php endif; ?></a>
                <?php endif; ?>
                <?php if (can_access_module('quotations')): ?>
                    <a href="module.php?m=quotations" class="<?= ($activeModule ?? '') === 'quotations' ? 'active' : '' ?>"><?= icon('quotations') ?> Quotations<?php if (!empty($notificationCounts['quotations'])): ?><span class="sidebar-badge orange"><?= $notificationCounts['quotations'] ?></span><?php endif; ?></a>
                <?php endif; ?>
                <?php if (is_admin()): ?>
                    <a href="finance.php" class="<?= ($activeModule ?? '') === 'finance' ? 'active' : '' ?>"><?= icon('chart') ?> Finance</a>
                <?php endif; ?>

                <!-- ★ PRIORITY 3: PEOPLE -->
                <span class="sidebar-section">People</span>
                <?php if (can_access_module('customers')): ?>
                    <a href="module.php?m=customers" class="<?= ($activeModule ?? '') === 'customers' ? 'active' : '' ?>"><?= icon('customers') ?> Customers</a>
                <?php endif; ?>
                <?php if (is_admin()): ?>
                    <a href="module.php?m=team" class="<?= ($activeModule ?? '') === 'team' ? 'active' : '' ?>"><?= icon('customers') ?> Wholeseller</a>
                <?php endif; ?>

                <!-- ★ PRIORITY 4: PRODUCTS & SALES -->
                <span class="sidebar-section">Products & Sales</span>
                <?php if (is_admin()): ?>
                    <a href="module.php?m=products" class="<?= ($activeModule ?? '') === 'products' ? 'active' : '' ?>"><?= icon('products') ?> Products</a>
                <?php endif; ?>
                <?php if (can_access_module('leads')): ?>
                    <a href="module.php?m=leads" class="<?= ($activeModule ?? '') === 'leads' ? 'active' : '' ?>"><?= icon('leads') ?> Leads</a>
                <?php endif; ?>

                <!-- ★ PRIORITY 5: OPERATIONS -->
                <span class="sidebar-section">Operations</span>
                <?php if (can_access_module('tasks')): ?>
                    <a href="module.php?m=tasks" class="<?= ($activeModule ?? '') === 'tasks' ? 'active' : '' ?>"><?= icon('tasks') ?> Tasks<?php if (!empty($notificationCounts['tasks'])): ?><span class="sidebar-badge blue"><?= $notificationCounts['tasks'] ?></span><?php endif; ?></a>
                <?php endif; ?>
                <?php if (can_access_module('calendar')): ?>
                    <a href="calendar.php" class="<?= ($activeModule ?? '') === 'calendar' ? 'active' : '' ?>"><?= icon('calendar') ?> Calendar</a>
                <?php endif; ?>
                <?php if (is_admin()): ?>
                    <a href="module.php?m=meetings" class="<?= ($activeModule ?? '') === 'meetings' ? 'active' : '' ?>"><?= icon('calendar') ?> Meetings</a>
                <?php endif; ?>

                <!-- ★ PRIORITY 6: ACCOUNTING -->
                <?php if (is_admin()): ?>
                    <span class="sidebar-section">Accounting</span>
                    <a href="module.php?m=debit_notes" class="<?= ($activeModule ?? '') === 'debit_notes' ? 'active' : '' ?>"><?= icon('invoices') ?> Debit Notes</a>
                    <a href="module.php?m=credit_notes" class="<?= ($activeModule ?? '') === 'credit_notes' ? 'active' : '' ?>"><?= icon('payments') ?> Credit Notes</a>
                <?php endif; ?>

                <!-- ★ PRIORITY 7: GST REPORTS -->
                <?php if (is_admin()): ?>
                    <span class="sidebar-section">GST Reports</span>
                    <a href="gstr1.php" class="<?= ($activeModule ?? '') === 'gstr1' ? 'active' : '' ?>"><?= icon('chart') ?> GSTR-1</a>
                    <a href="gstr3b.php" class="<?= ($activeModule ?? '') === 'gstr3b' ? 'active' : '' ?>"><?= icon('chart') ?> GSTR-3B</a>

                    <span class="sidebar-section">Reports</span>
                    <a href="reports.php" class="<?= ($activeModule ?? '') === 'reports' ? 'active' : '' ?>"><?= icon('chart') ?> All Reports</a>
                    <a href="report_sales.php" class="<?= ($activeModule ?? '') === 'report_sales' ? 'active' : '' ?>"><?= icon('invoices') ?> Sales Report</a>
                    <a href="report_expense.php" class="<?= ($activeModule ?? '') === 'report_expense' ? 'active' : '' ?>"><?= icon('payments') ?> Expense Report</a>
                    <a href="report_profit_loss.php" class="<?= ($activeModule ?? '') === 'report_profit_loss' ? 'active' : '' ?>"><?= icon('chart') ?> Profit & Loss</a>
                    <a href="report_outstanding.php" class="<?= ($activeModule ?? '') === 'report_outstanding' ? 'active' : '' ?>"><?= icon('invoices') ?> Outstanding</a>
                    <a href="report_product_sales.php" class="<?= ($activeModule ?? '') === 'report_product_sales' ? 'active' : '' ?>"><?= icon('products') ?> Product Sales</a>
                    <a href="report_payments.php" class="<?= ($activeModule ?? '') === 'report_payments' ? 'active' : '' ?>"><?= icon('payments') ?> Payment Report</a>
                    <a href="report_hsn.php" class="<?= ($activeModule ?? '') === 'report_hsn' ? 'active' : '' ?>"><?= icon('chart') ?> HSN Summary</a>
                    <a href="report_state_gst.php" class="<?= ($activeModule ?? '') === 'report_state_gst' ? 'active' : '' ?>"><?= icon('chart') ?> State GST</a>
                    <a href="report_quotation.php" class="<?= ($activeModule ?? '') === 'report_quotation' ? 'active' : '' ?>"><?= icon('quotations') ?> Quote Conversion</a>
                    <a href="report_aging.php" class="<?= ($activeModule ?? '') === 'report_aging' ? 'active' : '' ?>"><?= icon('invoices') ?> Aging Report</a>
                    <a href="report_revenue.php" class="<?= ($activeModule ?? '') === 'report_revenue' ? 'active' : '' ?>"><?= icon('chart') ?> Revenue Trend</a>
                <?php endif; ?>

                <!-- ★ PRIORITY 7: ADMIN -->
                <?php if (is_admin()): ?>
                    <span class="sidebar-section">Admin</span>
                    <a href="subadmin.php" class="<?= ($activeModule ?? '') === 'subadmin' ? 'active' : '' ?>"><?= icon('customers') ?> Subadmin</a>
                    <a href="settings.php" class="<?= ($activeModule ?? '') === 'settings' ? 'active' : '' ?>"><?= icon('settings') ?> Shop Settings</a>
                    <a href="notifications.php" class="<?= ($activeModule ?? '') === 'notifications' ? 'active' : '' ?>" style="position:relative;"><?= icon('alert') ?> Notifications<?php if ($totalNotifs > 0): ?><span class="sidebar-badge red"><?= $totalNotifs ?></span><?php endif; ?></a>
                    <a href="whatsapp_settings.php" class="<?= ($activeModule ?? '') === 'whatsapp_settings' ? 'active' : '' ?>"><?= icon('chat') ?> WhatsApp</a>
                <?php endif; ?>

                <a href="logout.php" class="sidebar-logout"><?= icon('logout') ?> Logout</a>
            </nav>
        </aside>
        <div class="main">
            <div class="topbar">
                <div style="display:flex;align-items:center;gap:10px;flex:1;"><button class="menu-toggle" onclick="var s=document.getElementById('sidebar');var b=document.getElementById('sidebar-backdrop');s.classList.toggle('open');if(b){b.classList.toggle('visible',s.classList.contains('open'));}"><?= icon('menu', 20) ?></button>
                    <h1><?= htmlspecialchars($pageTitle ?? '') ?></h1>
                    </div>
                    <!-- Global Search -->
                    <div style="flex:0 1 300px;margin:0 16px;position:relative;" class="mm-global-search-wrap">
                      <input type="search" id="mm-global-search" placeholder="🔍 Search invoices, customers, products... (Ctrl+F)" style="width:100%;padding:8px 14px;border:1px solid #E2E6EF;border-radius:8px;font-size:13px;font-family:inherit;background:#F4F6FA;" autocomplete="off">
                      <div id="mm-global-results" class="mm-autocomplete-dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:9999;"></div>
                    </div>
                    <!-- Notification Bell Removed -->
                    <?php if ($member = current_team_member()): ?><div class="user-menu"><span><?= htmlspecialchars($member['name']) ?> (<?= htmlspecialchars($member['role'] ?? 'Member') ?>)</span><?= function_exists('license_renewal_summary') ? license_renewal_summary() : '' ?><a class="btn btn-outline btn-sm mobile-hide-download" href="<?= htmlspecialchars($desktopDownloadPath) ?>" download>Download Desktop App</a><?php if ($androidAppAvailable): ?><a class="btn btn-outline btn-sm mobile-hide-download" href="<?= htmlspecialchars($androidDownloadPath) ?>" download>Download Android App</a><?php endif; ?><a href="logout.php" class="btn btn-outline btn-sm">Logout</a></div><?php endif; ?>
            </div>
            <div class="content">
                <?php $licenseBanner = function_exists('license_banner') ? license_banner() : null; ?>
                <?php if ($licenseBanner): ?>
                    <div class="license-banner license-banner-<?= htmlspecialchars($licenseBanner['type']) ?>">
                        <?= icon($licenseBanner['type'] === 'expired' ? 'delete' : 'calendar', 18) ?>
                        <span><?= htmlspecialchars($licenseBanner['text']) ?></span>
                    </div>
                <?php endif; ?>
                <?= function_exists('license_renewal_popup') ? license_renewal_popup() : '' ?>
                <script>
                    (function() {
                        if ('serviceWorker' in navigator) navigator.serviceWorker.register('service-worker.js').catch(function() {});
                    }());
                </script>