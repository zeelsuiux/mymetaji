<?php
$tenant = isset($_GET['tenant']) && preg_match('/^cst_[0-9]+$/', (string)$_GET['tenant']) ? (string)$_GET['tenant'] : '';
$base = $tenant !== '' ? './' : './';
header('Content-Type: application/manifest+json');
echo json_encode([
    'name' => 'My Metaji' . ($tenant !== '' ? ' - ' . strtoupper($tenant) : ''),
    'short_name' => 'My Metaji',
    'description' => 'Business management workspace',
    'start_url' => $base,
    'scope' => $base,
    'display' => 'standalone',
    'background_color' => '#F8FAFC',
    'theme_color' => '#0d6e6e',
    'orientation' => 'any',
    'id' => $base,
    'icons' => [
        ['src' => 'assets/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
        ['src' => 'assets/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
    ],
], JSON_UNESCAPED_SLASHES);
