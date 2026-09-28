<?php
/**
 * MY METAJI - WhatsApp Notification System
 * Auto-sends WhatsApp messages for: Payment, Quotation, Invoice, Meeting
 * 
 * Supports:
 * 1. WhatsApp Business API (via WATI / Twilio / Meta Cloud API)
 * 2. Fallback: wa.me link (opens WhatsApp with pre-filled message)
 */

// ============ WHATSAPP CONFIGURATION ============

// Ensure COMPANY_NAME etc. are available for public_invoice_url()
if (!defined('COMPANY_NAME')) {
    if (file_exists(__DIR__ . '/company.php')) {
        require_once __DIR__ . '/company.php';
    } else {
        define('COMPANY_NAME', '');
        define('COMPANY_PHONE', '');
    }
}

function whatsapp_config_file() {
    return panel_database_dir() . '/whatsapp_config.json';
}

function whatsapp_config_defaults() {
    return [
        'enabled'           => false,
        'provider'          => 'wati',        // wati | twilio | meta | generic
        'api_url'           => '',            // API endpoint
        'api_key'           => '',            // API key / token
        'phone_number_id'   => '',            // WhatsApp Business phone number
        'business_phone'    => '',            // Your business phone (display)
        'account_sid'       => '',            // Twilio Account SID (if using Twilio)
        'auth_token'        => '',            // Twilio Auth Token (if using Twilio)
        'auto_payment'      => true,          // Auto-send on payment received
        'auto_quotation'    => true,          // Auto-send on quotation created
        'auto_invoice'      => true,          // Auto-send on invoice created
        'auto_meeting'      => true,          // Auto-send on meeting booked
        'auto_overdue'      => true,          // Auto-send overdue reminders
        'overdue_days'      => [1, 3, 7],     // Send reminder on these days overdue
        'auto_due_soon'     => true,          // Auto-send advance due date reminder
        'due_soon_days'     => [2],           // Send reminder X days before due date
        'message_template_payment'    => '',   // Custom template (empty = default)
        'message_template_quotation'  => '',
        'message_template_invoice'    => '',
        'message_template_meeting'    => '',
        'message_template_overdue'    => '',
        'message_template_due_soon'   => '',
    ];
}

function whatsapp_config() {
    $config = whatsapp_config_defaults();
    $file = whatsapp_config_file();
    if (file_exists($file)) {
        $saved = json_decode(file_get_contents($file), true);
        if (is_array($saved)) $config = array_merge($config, $saved);
    }
    return $config;
}

function whatsapp_config_save($config) {
    $file = whatsapp_config_file();
    $dir = dirname($file);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $merged = array_merge(whatsapp_config_defaults(), $config);
    file_put_contents($file, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

// ============ PUBLIC INVOICE URL ============

function public_invoice_url($invoiceId) {
    $secret = 'metaji_inv_' . md5(COMPANY_NAME . COMPANY_PHONE);
    $token = md5($invoiceId . '_' . $secret);
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Get the path relative to document root
    $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    $baseUrl = $scheme . '://' . $host . $scriptDir;
    // Include tenant code so public_invoice.php knows which shop database to use
    $tenant = panel_tenant_code();
    $url = $baseUrl . '/public_invoice.php?id=' . (int)$invoiceId . '&t=' . $token;
    if ($tenant !== '') $url .= '&tenant=' . rawurlencode($tenant);
    return $url;
}

// ============ PHONE NUMBER HELPER ============

function whatsapp_normalize_phone($phone) {
    $phone = trim((string)$phone);
    // Remove all non-digits
    $phone = preg_replace('/[^0-9]/', '', $phone);
    // If starts with 91 and has 10 digits after, it's Indian
    if (strlen($phone) === 10) {
        $phone = '91' . $phone;
    }
    // If starts with 0, remove it and add 91
    if (strlen($phone) > 10 && substr($phone, 0, 1) === '0') {
        $phone = '91' . substr($phone, 1);
    }
    return $phone;
}

function whatsapp_get_customer_phone($customerName) {
    $name = trim((string)$customerName);
    if ($name === '') return '';
    
    // Check customers
    foreach (db_get_all('customers') as $customer) {
        if (strcasecmp(trim((string)($customer['name'] ?? '')), $name) === 0) {
            return $customer['phone'] ?? '';
        }
    }
    // Check team (wholesellers)
    foreach (db_get_all('team') as $member) {
        if (strcasecmp(trim((string)($member['name'] ?? '')), $name) === 0) {
            return $member['phone'] ?? '';
        }
    }
    return '';
}

// ============ MESSAGE TEMPLATES ============

function whatsapp_message_payment($payment, $invoice = null) {
    $config = whatsapp_config();
    $shop = shop_profile();
    
    // Generate link if invoice_no is available
    $publicUrl = '';
    if (!empty($payment['invoice_no'])) {
        // Find invoice by invoice_no
        foreach (db_get_all('invoices') as $inv) {
            if (($inv['invoice_no'] ?? '') === $payment['invoice_no']) {
                $publicUrl = public_invoice_url($inv['id'] ?? 0);
                break;
            }
        }
    }
    
    if (!empty($config['message_template_payment'])) {
        $template = $config['message_template_payment'];
        $template = str_replace('{customer}', $payment['customer'] ?? '', $template);
        $template = str_replace('{amount}', number_format((float)($payment['amount'] ?? 0), 2), $template);
        $template = str_replace('{invoice_no}', $payment['invoice_no'] ?? '', $template);
        $template = str_replace('{mode}', $payment['mode'] ?? 'Cash', $template);
        $template = str_replace('{date}', format_display_date($payment['date'] ?? date('Y-m-d')), $template);
        $template = str_replace('{shop}', $shop['name'] ?? '', $template);
        $template = str_replace('{link}', $publicUrl, $template);
        return $template;
    }
    
    $msg = "Hi " . ($payment['customer'] ?? '') . " 👋\n\n";
    $msg .= "✅ *Payment Received - Confirmation*\n\n";
    $msg .= "Amount: *₹" . number_format((float)($payment['amount'] ?? 0), 2) . "*\n";
    if (!empty($payment['invoice_no'])) $msg .= "Invoice: " . $payment['invoice_no'] . "\n";
    $msg .= "Mode: " . ($payment['mode'] ?? 'Cash') . "\n";
    $msg .= "Date: " . format_display_date($payment['date'] ?? date('Y-m-d')) . "\n\n";
    if ($publicUrl) $msg .= "📄 *View Invoice:* " . $publicUrl . "\n\n";
    $msg .= "Thank you for your payment! 🙏\n\n";
    $msg .= "— " . ($shop['name'] ?? 'My Metaji');
    return $msg;
}

function whatsapp_message_quotation($quotation) {
    $config = whatsapp_config();
    $shop = shop_profile();
    
    // Calculate amount from line items (same logic as PDF generation)
    $items = normalize_line_items($quotation['items'] ?? []);
    $amount = !empty($items) ? line_items_total($items) : (float)($quotation['amount'] ?? 0);
    
    // Generate public link
    $quotationId = $quotation['id'] ?? 0;
    $publicUrl = $quotationId ? public_invoice_url($quotationId) : '';
    
    if (!empty($config['message_template_quotation'])) {
        $template = $config['message_template_quotation'];
        $template = str_replace('{customer}', $quotation['customer'] ?? '', $template);
        $template = str_replace('{amount}', number_format($amount, 2), $template);
        $template = str_replace('{quotation_no}', $quotation['quotation_no'] ?? '', $template);
        $template = str_replace('{valid_until}', format_display_date($quotation['valid_until'] ?? ''), $template);
        $template = str_replace('{shop}', $shop['name'] ?? '', $template);
        $template = str_replace('{link}', $publicUrl, $template);
        return $template;
    }
    
    $msg = "Hi " . ($quotation['customer'] ?? '') . " 👋\n\n";
    $msg .= "📋 *New Quotation Created*\n\n";
    $msg .= "Quotation No: *" . ($quotation['quotation_no'] ?? '') . "*\n";
    $msg .= "Amount: *₹" . number_format($amount, 2) . "*\n";
    if (!empty($quotation['valid_until'])) $msg .= "Valid Until: " . format_display_date($quotation['valid_until']) . "\n";
    if ($publicUrl) $msg .= "\n📄 *View Quotation:* " . $publicUrl . "\n";
    $msg .= "\nPlease review and let us know.\n\n";
    $msg .= "— " . ($shop['name'] ?? 'My Metaji');
    return $msg;
}

function whatsapp_message_invoice($invoice) {
    $config = whatsapp_config();
    $shop = shop_profile();
    
    // Calculate amount from line items (same logic as PDF generation)
    $items = normalize_line_items($invoice['items'] ?? []);
    $amount = !empty($items) ? line_items_total($items) : (float)($invoice['amount'] ?? 0);
    
    // Generate public link
    $invoiceId = $invoice['id'] ?? 0;
    $publicUrl = $invoiceId ? public_invoice_url($invoiceId) : '';
    
    if (!empty($config['message_template_invoice'])) {
        $template = $config['message_template_invoice'];
        $template = str_replace('{customer}', $invoice['customer'] ?? '', $template);
        $template = str_replace('{amount}', number_format($amount, 2), $template);
        $template = str_replace('{invoice_no}', $invoice['invoice_no'] ?? '', $template);
        $template = str_replace('{due_date}', format_display_date($invoice['due_date'] ?? ''), $template);
        $template = str_replace('{shop}', $shop['name'] ?? '', $template);
        $template = str_replace('{link}', $publicUrl, $template);
        return $template;
    }
    
    $msg = "Hi " . ($invoice['customer'] ?? '') . " 👋\n\n";
    $msg .= "🧾 *New Invoice Created*\n\n";
    $msg .= "Invoice No: *" . ($invoice['invoice_no'] ?? '') . "*\n";
    $msg .= "Amount: *₹" . number_format($amount, 2) . "*\n";
    if (!empty($invoice['due_date'])) $msg .= "Due Date: " . format_display_date($invoice['due_date']) . "\n";
    if ($publicUrl) $msg .= "\n📄 *View Invoice:* " . $publicUrl . "\n";
    $msg .= "\nPlease make the payment at your earliest convenience.\n\n";
    $msg .= "— " . ($shop['name'] ?? 'My Metaji');
    return $msg;
}

function whatsapp_message_meeting($meeting) {
    $config = whatsapp_config();
    $shop = shop_profile();
    
    if (!empty($config['message_template_meeting'])) {
        $template = $config['message_template_meeting'];
        $template = str_replace('{title}', $meeting['title'] ?? '', $template);
        $template = str_replace('{date}', format_display_date($meeting['date'] ?? ''), $template);
        $template = str_replace('{time}', $meeting['time'] ?? '', $template);
        $template = str_replace('{location}', $meeting['location'] ?? 'TBD', $template);
        $template = str_replace('{with_person}', $meeting['with_person'] ?? '', $template);
        $template = str_replace('{shop}', $shop['name'] ?? '', $template);
        return $template;
    }
    
    $person = !empty($meeting['with_person']) ? $meeting['with_person'] : 'you';
    $msg = "Hi " . $person . " 👋\n\n";
    $msg .= "📅 *Meeting Scheduled*\n\n";
    $msg .= "Title: *" . ($meeting['title'] ?? 'Meeting') . "*\n";
    $msg .= "Date: " . format_display_date($meeting['date'] ?? '') . "\n";
    $msg .= "Time: " . ($meeting['time'] ?? '') . "\n";
    if (!empty($meeting['location'])) $msg .= "Location: " . $meeting['location'] . "\n";
    if (!empty($meeting['notes'])) $msg .= "\nNotes: " . $meeting['notes'] . "\n";
    $msg .= "\nSee you there! 🤝\n\n";
    $msg .= "— " . ($shop['name'] ?? 'My Metaji');
    return $msg;
}

function whatsapp_message_overdue($invoice, $daysOverdue) {
    $config = whatsapp_config();
    $shop = shop_profile();
    
    // Calculate amount from line items (same logic as PDF generation)
    $items = normalize_line_items($invoice['items'] ?? []);
    $amount = !empty($items) ? line_items_total($items) : (float)($invoice['amount'] ?? 0);
    
    if (!empty($config['message_template_overdue'])) {
        $template = $config['message_template_overdue'];
        $template = str_replace('{customer}', $invoice['customer'] ?? '', $template);
        $template = str_replace('{amount}', number_format($amount, 2), $template);
        $template = str_replace('{invoice_no}', $invoice['invoice_no'] ?? '', $template);
        $template = str_replace('{days}', (string)$daysOverdue, $template);
        $template = str_replace('{shop}', $shop['name'] ?? '', $template);
        return $template;
    }
    
    $msg = "Hi " . ($invoice['customer'] ?? '') . " 👋\n\n";
    $msg .= "⚠️ *Payment Reminder*\n\n";
    $msg .= "Invoice: *" . ($invoice['invoice_no'] ?? '') . "*\n";
    $msg .= "Amount Due: *₹" . number_format($amount, 2) . "*\n";
    $msg .= "Overdue by: *" . $daysOverdue . " day" . ($daysOverdue > 1 ? 's' : '') . "*\n\n";
    $msg .= "Please make the payment at your earliest convenience.\n\n";
    $msg .= "— " . ($shop['name'] ?? 'My Metaji');
    return $msg;
}

// ============ SEND WHATSAPP MESSAGE ============

function whatsapp_send($phone, $message) {
    $config = whatsapp_config();
    
    $phone = whatsapp_normalize_phone($phone);
    if (strlen($phone) < 10) {
        return ['success' => false, 'method' => 'error', 'message' => 'Invalid phone number'];
    }
    
    // If disabled or no API configured, always use wa.me fallback (free, no API needed)
    if (!$config['enabled'] || ($config['api_url'] === '' && $config['api_key'] === '' && $config['account_sid'] === '')) {
        return whatsapp_send_fallback($phone, $message, 'direct_wa_me');
    }
    
    $provider = $config['provider'] ?? 'wati';
    
    switch ($provider) {
        case 'wati':
            return whatsapp_send_wati($phone, $message, $config);
        case 'twilio':
            return whatsapp_send_twilio($phone, $message, $config);
        case 'meta':
            return whatsapp_send_meta($phone, $message, $config);
        case 'generic':
            return whatsapp_send_generic($phone, $message, $config);
        default:
            return whatsapp_send_fallback($phone, $message, 'unknown_provider');
    }
}

function whatsapp_send_wati($phone, $message, $config) {
    $apiUrl = rtrim($config['api_url'] ?? '', '/') . '/api/v1/send-session-message';
    $token = $config['api_key'] ?? '';
    
    if ($apiUrl === '' || $token === '') {
        return whatsapp_send_fallback($phone, $message, 'wati_not_configured');
    }
    
    $data = [
        'phone_number' => '+' . $phone,
        'message' => $message,
    ];
    
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        whatsapp_log($phone, $message, 'sent', 'wati');
        return ['success' => true, 'method' => 'wati', 'response' => $result];
    }
    
    whatsapp_log($phone, $message, 'failed', 'wati', $response);
    return ['success' => false, 'method' => 'wati', 'error' => $response];
}

function whatsapp_send_twilio($phone, $message, $config) {
    $accountSid = $config['account_sid'] ?? '';
    $authToken = $config['auth_token'] ?? '';
    $fromNumber = $config['phone_number_id'] ?? '';
    
    if ($accountSid === '' || $authToken === '' || $fromNumber === '') {
        return whatsapp_send_fallback($phone, $message, 'twilio_not_configured');
    }
    
    $apiUrl = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json";
    
    $data = http_build_query([
        'From' => 'whatsapp:' . $fromNumber,
        'To' => 'whatsapp:+' . $phone,
        'Body' => $message,
    ]);
    
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => [
            'Authorization: Basic ' . base64_encode($accountSid . ':' . $authToken),
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        whatsapp_log($phone, $message, 'sent', 'twilio');
        return ['success' => true, 'method' => 'twilio', 'response' => $result];
    }
    
    whatsapp_log($phone, $message, 'failed', 'twilio', $response);
    return ['success' => false, 'method' => 'twilio', 'error' => $response];
}

function whatsapp_send_meta($phone, $message, $config) {
    $phoneNumberId = $config['phone_number_id'] ?? '';
    $token = $config['api_key'] ?? '';
    
    if ($phoneNumberId === '' || $token === '') {
        return whatsapp_send_fallback($phone, $message, 'meta_not_configured');
    }
    
    $apiUrl = "https://graph.facebook.com/v18.0/{$phoneNumberId}/messages";
    
    $data = [
        'messaging_product' => 'whatsapp',
        'to' => $phone,
        'type' => 'text',
        'text' => ['body' => $message],
    ];
    
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        whatsapp_log($phone, $message, 'sent', 'meta');
        return ['success' => true, 'method' => 'meta', 'response' => $result];
    }
    
    whatsapp_log($phone, $message, 'failed', 'meta', $response);
    return ['success' => false, 'method' => 'meta', 'error' => $response];
}

function whatsapp_send_generic($phone, $message, $config) {
    $apiUrl = $config['api_url'] ?? '';
    $token = $config['api_key'] ?? '';
    
    if ($apiUrl === '') {
        return whatsapp_send_fallback($phone, $message, 'generic_not_configured');
    }
    
    $data = [
        'phone' => $phone,
        'message' => $message,
    ];
    
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        whatsapp_log($phone, $message, 'sent', 'generic');
        return ['success' => true, 'method' => 'generic'];
    }
    
    whatsapp_log($phone, $message, 'failed', 'generic', $response);
    return ['success' => false, 'method' => 'generic', 'error' => $response];
}

// ============ FALLBACK: wa.me LINK ============

function whatsapp_send_fallback($phone, $message, $reason = '') {
    // Log the attempt
    whatsapp_log($phone, $message, 'fallback', $reason);
    
    // Build wa.me URL
    $encodedMsg = urlencode($message);
    $url = "https://wa.me/{$phone}?text={$encodedMsg}";
    
    return [
        'success' => true,
        'method' => 'fallback',
        'url' => $url,
        'message' => 'WhatsApp API not configured. Message ready to send manually.',
    ];
}

function whatsapp_open_link($phone, $message) {
    $phone = whatsapp_normalize_phone($phone);
    $encodedMsg = urlencode($message);
    return "https://wa.me/{$phone}?text={$encodedMsg}";
}

// ============ ACTIVITY LOG ============

function whatsapp_log($phone, $message, $status, $provider = '', $error = '') {
    $logFile = panel_database_dir() . '/whatsapp_log.json';
    $dir = dirname($logFile);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    
    $logs = [];
    if (file_exists($logFile)) {
        $logs = json_decode(file_get_contents($logFile), true);
        if (!is_array($logs)) $logs = [];
    }
    
    $logs[] = [
        'id' => empty($logs) ? 1 : max(array_map(fn($r) => (int)($r['id'] ?? 0), $logs)) + 1,
        'phone' => $phone,
        'message' => mb_substr($message, 0, 200),
        'status' => $status,
        'provider' => $provider,
        'error' => $error !== '' ? mb_substr($error, 0, 500) : '',
        'timestamp' => date('Y-m-d H:i:s'),
    ];
    
    // Keep last 500 entries
    if (count($logs) > 500) {
        $logs = array_slice($logs, -500);
    }
    
    file_put_contents($logFile, json_encode(array_values($logs), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function whatsapp_log_all() {
    $logFile = panel_database_dir() . '/whatsapp_log.json';
    if (!file_exists($logFile)) return [];
    $logs = json_decode(file_get_contents($logFile), true);
    return is_array($logs) ? array_reverse($logs) : [];
}

// ============ AUTO-SEND FUNCTIONS ============

function whatsapp_auto_payment($payment) {
    $config = whatsapp_config();
    if (!$config['auto_payment']) return null;
    
    $phone = whatsapp_get_customer_phone($payment['customer'] ?? '');
    if ($phone === '') return null;
    
    $message = whatsapp_message_payment($payment);
    return whatsapp_send($phone, $message);
}

function whatsapp_auto_quotation($quotation) {
    $config = whatsapp_config();
    if (!$config['auto_quotation']) return null;
    
    $phone = whatsapp_get_customer_phone($quotation['customer'] ?? '');
    if ($phone === '') return null;
    
    $message = whatsapp_message_quotation($quotation);
    return whatsapp_send($phone, $message);
}

function whatsapp_auto_invoice($invoice) {
    $config = whatsapp_config();
    if (!$config['auto_invoice']) return null;
    
    $phone = whatsapp_get_customer_phone($invoice['customer'] ?? '');
    if ($phone === '') return null;
    
    $message = whatsapp_message_invoice($invoice);
    return whatsapp_send($phone, $message);
}

function whatsapp_auto_meeting($meeting) {
    $config = whatsapp_config();
    if (!$config['auto_meeting']) return null;
    
    // Try to find phone for the person
    $phone = whatsapp_get_customer_phone($meeting['with_person'] ?? '');
    if ($phone === '') return null;
    
    $message = whatsapp_message_meeting($meeting);
    return whatsapp_send($phone, $message);
}

// ============ DUE SOON REMINDER ============

function whatsapp_message_due_soon($invoice, $daysUntilDue) {
    $config = whatsapp_config();
    $shop = shop_profile();
    
    // Calculate amount from line items (same logic as PDF generation)
    $items = normalize_line_items($invoice['items'] ?? []);
    $amount = !empty($items) ? line_items_total($items) : (float)($invoice['amount'] ?? 0);
    
    if (!empty($config['message_template_due_soon'])) {
        $template = $config['message_template_due_soon'];
        $template = str_replace('{customer}', $invoice['customer'] ?? '', $template);
        $template = str_replace('{amount}', number_format($amount, 2), $template);
        $template = str_replace('{pending}', number_format($amount, 2), $template);
        $template = str_replace('{invoice_no}', $invoice['invoice_no'] ?? '', $template);
        $template = str_replace('{due_date}', format_display_date($invoice['due_date'] ?? ''), $template);
        $template = str_replace('{days}', (string)$daysUntilDue, $template);
        $template = str_replace('{shop}', $shop['name'] ?? '', $template);
        return $template;
    }
    
    $pending = number_format($amount, 2);
    $msg = "Hi " . ($invoice['customer'] ?? '') . " 👋\n\n";
    $msg .= "🔔 *Payment Due Date Reminder*\n\n";
    $msg .= "This is a friendly reminder that your payment is due in *{$daysUntilDue} day" . ($daysUntilDue > 1 ? 's' : '') . "*.\n\n";
    $msg .= "Invoice: *" . ($invoice['invoice_no'] ?? '') . "*\n";
    $msg .= "Amount Due: *₹{$pending}*\n";
    $msg .= "Due Date: *" . format_display_date($invoice['due_date'] ?? '') . "*\n\n";
    $msg .= "Please make the payment before the due date to avoid any late charges.\n\n";
    $msg .= "Thank you! 🙏\n\n";
    $msg .= "— " . ($shop['name'] ?? 'My Metaji');
    return $msg;
}

function whatsapp_auto_due_soon_reminders() {
    $config = whatsapp_config();
    if (!$config['auto_due_soon']) return [];
    
    $allowedDays = $config['due_soon_days'] ?? [2];
    $results = [];
    
    $dueSoonInvoices = get_due_soon_invoices(max($allowedDays));
    
    foreach ($dueSoonInvoices as $invoice) {
        $daysUntilDue = $invoice['_days_until_due'] ?? 0;
        if (!in_array($daysUntilDue, $allowedDays, true)) continue;
        
        $phone = whatsapp_get_customer_phone($invoice['customer'] ?? '');
        if ($phone === '') continue;
        
        $message = whatsapp_message_due_soon($invoice, $daysUntilDue);
        $result = whatsapp_send($phone, $message);
        $results[] = array_merge($result, [
            'invoice_no' => $invoice['invoice_no'] ?? '',
            'customer'   => $invoice['customer'] ?? '',
            'due_date'   => $invoice['due_date'] ?? '',
            'days'       => $daysUntilDue,
        ]);
    }
    return $results;
}

function whatsapp_auto_overdue_reminders() {
    $config = whatsapp_config();
    if (!$config['auto_overdue']) return;
    
    $overdueInvoices = get_overdue_invoices();
    $allowedDays = $config['overdue_days'] ?? [1, 3, 7];
    
    foreach ($overdueInvoices as $invoice) {
        $daysOverdue = $invoice['_days_overdue'] ?? 0;
        if (!in_array($daysOverdue, $allowedDays, true)) continue;
        
        $phone = whatsapp_get_customer_phone($invoice['customer'] ?? '');
        if ($phone === '') continue;
        
        $message = whatsapp_message_overdue($invoice, $daysOverdue);
        whatsapp_send($phone, $message);
    }
}

// ============ BULK SEND ============

function whatsapp_send_bulk($recipients, $message) {
    $results = [];
    foreach ($recipients as $recipient) {
        $phone = is_array($recipient) ? ($recipient['phone'] ?? '') : $recipient;
        $name = is_array($recipient) ? ($recipient['name'] ?? '') : '';
        
        $personalizedMsg = str_replace('{customer}', $name, $message);
        $result = whatsapp_send($phone, $personalizedMsg);
        $results[] = array_merge($result, ['phone' => $phone, 'name' => $name]);
        
        // Rate limit: 1 message per second
        usleep(1000000);
    }
    return $results;
}
