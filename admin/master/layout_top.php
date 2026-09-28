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
    <meta name="theme-color" content="#2563EB">
    <title><?= htmlspecialchars($pageTitle ?? 'Master Panel') ?></title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .status-pill{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600;}
        .status-pill.green{background:#d1fae5;color:#065f46;}
        .status-pill.orange{background:#fef3c7;color:#92400e;}
        .status-pill.red{background:#fee2e2;color:#991b1b;}
        .status-pill.gray{background:#f1f5f9;color:#475569;}
        .kpi-grid.master-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:20px;}
    </style>
</head>

<body>
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
    <div class="shell">
        <aside class="sidebar" id="sidebar">
            <div class="brand"><img class="brand-logo" src="assets/light-logo.png" alt="Master Panel"></div>
            <nav>
                <a href="index.php" class="<?= empty($activePage) ? 'active' : '' ?>"><?= icon('dashboard') ?> Dashboard</a>
                <a href="customers.php" class="<?= ($activePage ?? '') === 'customers' ? 'active' : '' ?>"><?= icon('customers') ?> Customers</a>
                <a href="payments.php" class="<?= ($activePage ?? '') === 'payments' ? 'active' : '' ?>"><?= icon('payments') ?> Payments</a>
  <a href="pricing.php" class="<?= ($activePage ?? '') === 'pricing' ? 'active' : '' ?>"><?= icon('payments') ?> Pricing</a>
                <a href="partners.php" class="<?= ($activePage ?? '') === 'partners' ? 'active' : '' ?>"><?= icon('leads') ?> Partners</a>
                <a href="leads.php" class="<?= ($activePage ?? '') === 'leads' ? 'active' : '' ?>"><?= icon('leads') ?> Signup Leads</a>
                <a href="cities.php" class="<?= ($activePage ?? '') === 'cities' ? 'active' : '' ?>"><?= icon('building') ?> City Master</a>
                <a href="settings.php" class="<?= ($activePage ?? '') === 'settings' ? 'active' : '' ?>"><?= icon('edit') ?> Settings</a>
                <a href="logout.php" class="sidebar-logout"><?= icon('logout') ?> Logout</a>
            </nav>
        </aside>
        <div class="main">
            <div class="topbar">
                <div style="display:flex;align-items:center;gap:10px;">
                    <button class="menu-toggle" onclick="var s=document.getElementById('sidebar');var b=document.getElementById('sidebar-backdrop');s.classList.toggle('open');if(b){b.classList.toggle('visible',s.classList.contains('open'));}"><?= icon('menu', 20) ?></button>
                    <h1><?= htmlspecialchars($pageTitle ?? '') ?></h1>
                </div>
                <div class="user-menu"><span><?= htmlspecialchars(current_master_admin()['username'] ?? 'admin') ?></span>
                    <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
                </div>
            </div>
            <div class="content">
