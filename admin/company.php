<?php
/**
 * MY METAJI - Company Profile
 * Edit these constants to match your business. Used on Invoice & Quotation PDFs.
 */
$profile = function_exists('shop_profile') ? shop_profile() : [];
define('COMPANY_NAME', $profile['name'] ?? '');
define('COMPANY_TAGLINE', $profile['tagline'] ?? '');
define('COMPANY_ADDRESS', $profile['address'] ?? '');
define('COMPANY_PHONE', $profile['phone'] ?? '');
define('COMPANY_EMAIL', $profile['email'] ?? '');
define('COMPANY_GSTIN', $profile['gstin'] ?? '');
define('COMPANY_BANK_NAME', $profile['bank_name'] ?? '');
define('COMPANY_BANK_ACC', $profile['bank_acc'] ?? '');
define('COMPANY_BANK_IFSC', $profile['bank_ifsc'] ?? '');
define('COMPANY_UPI', $profile['upi'] ?? '');
define('COMPANY_UPI_QR', $profile['upi_qr'] ?? '');
define('COMPANY_LOGO', !empty($profile['logo']) ? $profile['logo'] : 'assets/logo.png');
define('COMPANY_SELLER_STATE', $profile['seller_state'] ?? '');
define('COMPANY_SELLER_STATE_CODE', $profile['seller_state_code'] ?? '');
