<?php
require_once __DIR__ . '/auth.php';
require_master_login();
require_once __DIR__ . '/icons.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Master Panel') ?></title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .status-pill{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600;}
        .status-pill.green{background:#E6F7EC;color:#1B8A45;}
        .status-pill.orange{background:#FFF4E0;color:#9A6B00;}
        .status-pill.red{background:#FCEAEA;color:#C0392B;}
        .status-pill.gray{background:#EEF0F3;color:#6B7280;}
        .kpi-grid.master-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:20px;}
    </style>
</head>

<body>
    <div class="shell">
        <aside class="sidebar" id="sidebar">
            <div class="brand"><img class="brand-logo" src="assets/light-logo.png" alt="Master Panel"></div>
            <nav>
                <a href="index.php" class="<?= empty($activePage) ? 'active' : '' ?>"><?= icon('dashboard') ?> Dashboard</a>
                <a href="customers.php" class="<?= ($activePage ?? '') === 'customers' ? 'active' : '' ?>"><?= icon('customers') ?> Customers</a>
                <a href="leads.php" class="<?= ($activePage ?? '') === 'leads' ? 'active' : '' ?>"><?= icon('leads') ?> Signup Leads</a>
                <a href="settings.php" class="<?= ($activePage ?? '') === 'settings' ? 'active' : '' ?>"><?= icon('edit') ?> Settings</a>
                <a href="logout.php" class="sidebar-logout"><?= icon('logout') ?> Logout</a>
            </nav>
        </aside>
        <div class="main">
            <div class="topbar">
                <div style="display:flex;align-items:center;gap:10px;">
                    <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><?= icon('menu', 20) ?></button>
                    <h1><?= htmlspecialchars($pageTitle ?? '') ?></h1>
                </div>
                <div class="user-menu"><span><?= htmlspecialchars(current_master_admin()['username'] ?? 'admin') ?></span>
                    <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
                </div>
            </div>
            <div class="content">
