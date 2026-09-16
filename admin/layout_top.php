<?php require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/icons.php'; ?>
<?php $desktopDownloadPath = 'downloads/Prisha-ERP-Setup.exe'; ?>
<?php $androidDownloadPath = 'downloads/Prisha-ERP.apk'; $androidAppAvailable = is_file(__DIR__ . '/downloads/Prisha-ERP.apk'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Prisha Ayurvedic ERP') ?></title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#071f2e">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Prisha ERP">
    <link rel="apple-touch-icon" href="assets/icon-192.png">
</head>

<body>
    <div class="shell">
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                <img class="brand-logo" src="assets/light-logo.png" alt="Prisha Ayurvedic">
            </div>
            <nav>
                <a href="index.php" class="<?= empty($activeModule) ? 'active' : '' ?>">
                    <?= icon('dashboard') ?> Dashboard
                </a>
                <?php if (is_admin()): ?>
                    <a href="module.php?m=products" class="<?= ($activeModule ?? '') === 'products' ? 'active' : '' ?>"><?= icon('products') ?> Products</a><a href="module.php?m=leads" class="<?= ($activeModule ?? '') === 'leads' ? 'active' : '' ?>"><?= icon('leads') ?> Leads</a><a href="module.php?m=customers" class="<?= ($activeModule ?? '') === 'customers' ? 'active' : '' ?>"><?= icon('customers') ?> Customers</a><a href="module.php?m=team" class="<?= ($activeModule ?? '') === 'team' ? 'active' : '' ?>"><?= icon('customers') ?> Wholeseller</a><a href="subadmin.php" class="<?= ($activeModule ?? '') === 'subadmin' ? 'active' : '' ?>"><?= icon('customers') ?> Subadmin</a>
                    <a href="settings.php" class="<?= ($activeModule ?? '') === 'settings' ? 'active' : '' ?>"><?= icon('settings') ?> Shop Settings
                    </a>
                <?php elseif (is_subadmin()): ?><a href="module.php?m=leads" class="<?= ($activeModule ?? '') === 'leads' ? 'active' : '' ?>"><?= icon('leads') ?> Leads</a><a href="module.php?m=customers" class="<?= ($activeModule ?? '') === 'customers' ? 'active' : '' ?>"><?= icon('customers') ?> Customers</a><?php endif; ?>
                <?php if (can_access_module('quotations')): ?><a href="module.php?m=quotations" class="<?= ($activeModule ?? '') === 'quotations' ? 'active' : '' ?>"><?= icon('quotations') ?> Quotations</a><?php endif; ?><?php if (can_access_module('invoices')): ?><a href="module.php?m=invoices" class="<?= ($activeModule ?? '') === 'invoices' ? 'active' : '' ?>"><?= icon('invoices') ?> Invoices</a><?php endif; ?><?php if (can_access_module('tasks')): ?><a href="module.php?m=tasks" class="<?= ($activeModule ?? '') === 'tasks' ? 'active' : '' ?>"><?= icon('tasks') ?> Tasks</a><?php endif; ?><?php if (can_access_module('calendar')): ?><a href="calendar.php" class="<?= ($activeModule ?? '') === 'calendar' ? 'active' : '' ?>"><?= icon('calendar') ?> Calendar</a><?php endif; ?>
                <?php if (is_admin()): ?><a href="module.php?m=meetings" class="<?= ($activeModule ?? '') === 'meetings' ? 'active' : '' ?>"><?= icon('calendar') ?> Meetings</a><a href="finance.php" class="<?= ($activeModule ?? '') === 'finance' ? 'active' : '' ?>"><?= icon('chart') ?> Finance</a><?php endif; ?><a href="logout.php" class="sidebar-logout"><?= icon('logout') ?> Logout</a>
            </nav>
        </aside>
        <div class="main">
            <div class="topbar">
                <div style="display:flex;align-items:center;gap:10px;"><button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><?= icon('menu', 20) ?></button>
                    <h1><?= htmlspecialchars($pageTitle ?? '') ?></h1>
                </div><?php if ($member = current_team_member()): ?><div class="user-menu"><span><?= htmlspecialchars($member['name']) ?> (<?= htmlspecialchars($member['role'] ?? 'Member') ?>)</span><?= function_exists('license_renewal_summary') ? license_renewal_summary() : '' ?><a class="btn btn-outline btn-sm" href="<?= htmlspecialchars($desktopDownloadPath) ?>" download>Download Desktop App</a><?php if ($androidAppAvailable): ?><a class="btn btn-outline btn-sm" href="<?= htmlspecialchars($androidDownloadPath) ?>" download>Download Android App</a><?php endif; ?><button type="button" id="install-app-button" class="btn btn-outline btn-sm" hidden>Install App</button><a href="logout.php" class="btn btn-outline btn-sm">Logout</a></div><?php endif; ?>
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
                        var installButton = document.getElementById('install-app-button');
                        var deferredPrompt;
                        if (!installButton) return;
                        window.addEventListener('beforeinstallprompt', function(event) {
                            event.preventDefault();
                            deferredPrompt = event;
                            installButton.hidden = false;
                        });
                        installButton.addEventListener('click', async function() {
                            if (!deferredPrompt) {
                                var isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
                                var message = isIos
                                    ? 'Safari ma Share button dabavo, pachhi Add to Home Screen select karo.'
                                    : 'Chrome menu (three dots) kholo ane Add to Home screen athva Install App select karo.';
                                alert(message);
                                return;
                            }
                            deferredPrompt.prompt();
                            await deferredPrompt.userChoice;
                            deferredPrompt = null;
                            installButton.hidden = true;
                        });
                        window.addEventListener('appinstalled', function() {
                            installButton.hidden = true;
                        });
                        setTimeout(function() {
                            if (!window.matchMedia('(display-mode: standalone)').matches && !navigator.standalone) installButton.hidden = false;
                        }, 1500);
                        if ('serviceWorker' in navigator) navigator.serviceWorker.register('service-worker.js').catch(function() {});
                    }());
                </script>