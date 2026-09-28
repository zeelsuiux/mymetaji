<?php
/**
 * MY METAJI - WhatsApp Settings Page
 * Configure WhatsApp API, message templates, and auto-send options
 */
require_once __DIR__ . '/auth.php';
require_admin();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/whatsapp.php';

$activeModule = 'whatsapp_settings';
$pageTitle = 'WhatsApp Settings';
$config = whatsapp_config();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'save';
    
    if ($action === 'send_test') {
        $testPhone = trim((string)($_POST['test_phone'] ?? ''));
        $testPhone = whatsapp_normalize_phone($testPhone);
        if (strlen($testPhone) < 10) {
            $errors[] = 'Please enter a valid phone number for testing.';
        } else {
            $shop = shop_profile();
            $testMsg = "Hi! 👋\n\nThis is a test message from " . ($shop['name'] ?? 'My Metaji') . ".\n\n✅ WhatsApp integration is working correctly!\n\n— " . ($shop['name'] ?? 'My Metaji');
            $result = whatsapp_send($testPhone, $testMsg);
            if ($result['success']) {
                // For direct wa.me mode, redirect to WhatsApp
                if (($result['method'] ?? '') === 'fallback' || ($result['method'] ?? '') === 'direct_wa_me') {
                    header('Location: ' . $result['url']);
                    exit;
                }
                $success = 'Test message sent! Method: ' . ($result['method'] ?? 'unknown');
            } else {
                $errors[] = 'Failed to send test message: ' . ($result['error'] ?? $result['message'] ?? 'Unknown error');
            }
        }
    } else {
        // Save settings
        $config = [
            'enabled'           => isset($_POST['enabled']),
            'provider'          => trim((string)($_POST['provider'] ?? 'wati')),
            'api_url'           => trim((string)($_POST['api_url'] ?? '')),
            'api_key'           => trim((string)($_POST['api_key'] ?? '')),
            'phone_number_id'   => trim((string)($_POST['phone_number_id'] ?? '')),
            'business_phone'    => trim((string)($_POST['business_phone'] ?? '')),
            'account_sid'       => trim((string)($_POST['account_sid'] ?? '')),
            'auth_token'        => trim((string)($_POST['auth_token'] ?? '')),
            'auto_payment'      => isset($_POST['auto_payment']),
            'auto_quotation'    => isset($_POST['auto_quotation']),
            'auto_invoice'      => isset($_POST['auto_invoice']),
            'auto_meeting'      => isset($_POST['auto_meeting']),
            'auto_overdue'      => isset($_POST['auto_overdue']),
            'overdue_days'      => array_map('intval', array_filter(explode(',', $_POST['overdue_days'] ?? '1,3,7'))),
            'auto_due_soon'     => isset($_POST['auto_due_soon']),
            'due_soon_days'     => array_map('intval', array_filter(explode(',', $_POST['due_soon_days'] ?? '2'))),
            'message_template_payment'    => trim((string)($_POST['message_template_payment'] ?? '')),
            'message_template_quotation'  => trim((string)($_POST['message_template_quotation'] ?? '')),
            'message_template_invoice'    => trim((string)($_POST['message_template_invoice'] ?? '')),
            'message_template_meeting'    => trim((string)($_POST['message_template_meeting'] ?? '')),
            'message_template_overdue'    => trim((string)($_POST['message_template_overdue'] ?? '')),
            'message_template_due_soon'   => trim((string)($_POST['message_template_due_soon'] ?? '')),
        ];
        
        whatsapp_config_save($config);
        $success = 'WhatsApp settings saved successfully!';
        $config = whatsapp_config();
    }
}

// Get log stats
$logs = whatsapp_log_all();
$totalSent = count(array_filter($logs, fn($l) => ($l['status'] ?? '') === 'sent'));
$totalFailed = count(array_filter($logs, fn($l) => ($l['status'] ?? '') === 'failed'));
$recentLogs = array_slice($logs, 0, 20);

require __DIR__ . '/layout_top.php';
?>

<style>
.wa-settings { max-width: 900px; margin: 0 auto; }
.wa-section { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px; margin-bottom: 16px; }
.wa-section h3 { margin: 0 0 16px; font-size: 16px; color: #0f172a; display: flex; align-items: center; gap: 8px; }
.wa-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.wa-full { grid-column: 1 / -1; }
.wa-templates { display: grid; gap: 16px; }
.wa-template-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; }
.wa-template-item label { font-weight: 600; font-size: 13px; color: #334155; display: block; margin-bottom: 6px; }
.wa-template-item textarea { width: 100%; min-height: 80px; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; font-family: inherit; font-size: 13px; resize: vertical; }
.wa-template-item small { color: #64748b; font-size: 11px; display: block; margin-top: 4px; }
.wa-log-table { width: 100%; border-collapse: collapse; }
.wa-log-table th, .wa-log-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; text-align: left; font-size: 13px; }
.wa-log-table th { color: #64748b; font-weight: 600; font-size: 12px; }
.wa-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px; }
.wa-stat { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; text-align: center; }
.wa-stat .value { font-size: 24px; font-weight: 700; }
.wa-stat .label { font-size: 12px; color: #64748b; margin-top: 4px; }
@media(max-width:600px) { .wa-grid { grid-template-columns: 1fr; } .wa-stats { grid-template-columns: 1fr; } }
</style>

<div class="wa-settings">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <h2 style="margin:0;">💬 WhatsApp Settings</h2>
    <a href="notifications.php" class="btn btn-outline btn-sm">← Back to Notifications</a>
  </div>

  <?php if ($success): ?><div class="alert"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>

  <!-- Direct Mode Info -->
  <?php if (!$config['enabled'] || ($config['api_url'] === '' && $config['api_key'] === '' && $config['account_sid'] === '')): ?>
  <div style="background:#E8F5E9;border:1px solid #A5D6A7;border-radius:10px;padding:14px 18px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
    <span style="font-size:24px;">✅</span>
    <div>
      <strong style="color:#2E7D32;">Direct WhatsApp Mode Active</strong>
      <p style="margin:2px 0 0;font-size:13px;color:#555;">No API needed! Clicking "Send" will open WhatsApp with pre-filled message. Just press Send in WhatsApp.</p>
    </div>
  </div>
  <?php endif; ?>

  <!-- Stats -->
  <div class="wa-stats">
    <div class="wa-stat">
      <div class="value" style="color:#2F7D32;"><?= $totalSent ?></div>
      <div class="label">Messages Sent</div>
    </div>
    <div class="wa-stat">
      <div class="value" style="color:#F44336;"><?= $totalFailed ?></div>
      <div class="label">Failed</div>
    </div>
    <div class="wa-stat">
      <div class="value" style="color:#2196F3;"><?= count($logs) ?></div>
      <div class="label">Total Log Entries</div>
    </div>
  </div>

  <form method="post">
    <!-- Enable/Disable -->
    <div class="wa-section">
      <h3>⚡ Enable WhatsApp Notifications</h3>
      <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
        <input type="checkbox" name="enabled" value="1" <?= $config['enabled'] ? 'checked' : '' ?> style="width:18px;height:18px;">
        <span>Enable automatic WhatsApp messages</span>
      </label>
    </div>

    <!-- API Configuration -->
    <div class="wa-section">
      <h3>🔑 API Configuration</h3>
      <div class="wa-grid">
        <div class="form-group">
          <label>Provider</label>
          <select name="provider">
            <option value="wati" <?= $config['provider'] === 'wati' ? 'selected' : '' ?>>WATI</option>
            <option value="twilio" <?= $config['provider'] === 'twilio' ? 'selected' : '' ?>>Twilio</option>
            <option value="meta" <?= $config['provider'] === 'meta' ? 'selected' : '' ?>>Meta Cloud API</option>
            <option value="generic" <?= $config['provider'] === 'generic' ? 'selected' : '' ?>>Generic API</option>
          </select>
        </div>
        <div class="form-group">
          <label>Business Phone</label>
          <input type="text" name="business_phone" value="<?= htmlspecialchars($config['business_phone']) ?>" placeholder="+91-XXXXXXXXXX">
        </div>
        <div class="form-group">
          <label>API URL</label>
          <input type="url" name="api_url" value="<?= htmlspecialchars($config['api_url']) ?>" placeholder="https://api.wati.io/api/v1/...">
        </div>
        <div class="form-group">
          <label>API Key / Token</label>
          <input type="password" name="api_key" value="<?= htmlspecialchars($config['api_key']) ?>" placeholder="Your API key">
        </div>
        <div class="form-group">
          <label>Phone Number ID</label>
          <input type="text" name="phone_number_id" value="<?= htmlspecialchars($config['phone_number_id']) ?>" placeholder="WhatsApp Business phone number">
        </div>
        <div class="form-group">
          <label>Account SID (Twilio only)</label>
          <input type="text" name="account_sid" value="<?= htmlspecialchars($config['account_sid']) ?>" placeholder="Twilio Account SID">
        </div>
        <div class="form-group">
          <label>Auth Token (Twilio only)</label>
          <input type="password" name="auth_token" value="<?= htmlspecialchars($config['auth_token']) ?>" placeholder="Twilio Auth Token">
        </div>
      </div>
    </div>

    <!-- Auto-Send Toggles -->
    <div class="wa-section">
      <h3>🚀 Auto-Send Triggers</h3>
      <div style="display:grid;gap:10px;">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
          <input type="checkbox" name="auto_payment" value="1" <?= $config['auto_payment'] ? 'checked' : '' ?> style="width:18px;height:18px;">
          <span>✅ Send confirmation when <strong>Payment Received</strong></span>
        </label>
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
          <input type="checkbox" name="auto_quotation" value="1" <?= $config['auto_quotation'] ? 'checked' : '' ?> style="width:18px;height:18px;">
          <span>📋 Send notification when <strong>Quotation Created</strong></span>
        </label>
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
          <input type="checkbox" name="auto_invoice" value="1" <?= $config['auto_invoice'] ? 'checked' : '' ?> style="width:18px;height:18px;">
          <span>🧾 Send notification when <strong>Invoice Created</strong></span>
        </label>
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
          <input type="checkbox" name="auto_meeting" value="1" <?= $config['auto_meeting'] ? 'checked' : '' ?> style="width:18px;height:18px;">
          <span>📅 Send confirmation when <strong>Meeting Booked</strong></span>
        </label>
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
          <input type="checkbox" name="auto_due_soon" value="1" <?= $config['auto_due_soon'] ? 'checked' : '' ?> style="width:18px;height:18px;">
          <span>🔔 Send advance reminders <strong>Before Due Date</strong></span>
        </label>
        <div class="form-group" style="margin-top:8px;">
          <label>Advance Reminder Days (comma separated)</label>
          <input type="text" name="due_soon_days" value="<?= htmlspecialchars(implode(',', $config['due_soon_days'])) ?>" placeholder="2">
          <small>Send reminder X days before due date (e.g., 2 = 2 days before due date)</small>
        </div>
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
          <input type="checkbox" name="auto_overdue" value="1" <?= $config['auto_overdue'] ? 'checked' : '' ?> style="width:18px;height:18px;">
          <span>⚠️ Send reminders for <strong>Overdue Invoices</strong></span>
        </label>
        <div class="form-group" style="margin-top:8px;">
          <label>Overdue Reminder Days (comma separated)</label>
          <input type="text" name="overdue_days" value="<?= htmlspecialchars(implode(',', $config['overdue_days'])) ?>" placeholder="1,3,7">
          <small>Send reminders on these days after due date (e.g., 1,3,7 = 1 day, 3 days, 7 days overdue)</small>
        </div>
      </div>
    </div>

    <!-- Message Templates -->
    <div class="wa-section">
      <h3>📝 Message Templates</h3>
      <p style="color:#64748b;font-size:13px;margin:0 0 16px;">Leave empty to use default messages. Available variables shown in each template.</p>
      
      <div class="wa-templates">
        <div class="wa-template-item">
          <label>✅ Payment Received Message</label>
          <textarea name="message_template_payment" placeholder="Leave empty for default message"><?= htmlspecialchars($config['message_template_payment']) ?></textarea>
          <small>Variables: {customer}, {amount}, {invoice_no}, {mode}, {date}, {shop}</small>
        </div>
        
        <div class="wa-template-item">
          <label>📋 Quotation Created Message</label>
          <textarea name="message_template_quotation" placeholder="Leave empty for default message"><?= htmlspecialchars($config['message_template_quotation']) ?></textarea>
          <small>Variables: {customer}, {amount}, {quotation_no}, {valid_until}, {shop}, {link}</small>
        </div>
        
        <div class="wa-template-item">
          <label>🧾 Invoice Created Message</label>
          <textarea name="message_template_invoice" placeholder="Leave empty for default message"><?= htmlspecialchars($config['message_template_invoice']) ?></textarea>
          <small>Variables: {customer}, {amount}, {invoice_no}, {due_date}, {shop}, {link}</small>
        </div>
        
        <div class="wa-template-item">
          <label>📅 Meeting Booked Message</label>
          <textarea name="message_template_meeting" placeholder="Leave empty for default message"><?= htmlspecialchars($config['message_template_meeting']) ?></textarea>
          <small>Variables: {title}, {date}, {time}, {location}, {with_person}, {shop}</small>
        </div>
        
        <div class="wa-template-item">
          <label>🔔 Due Date Reminder (Advance)</label>
          <textarea name="message_template_due_soon" placeholder="Leave empty for default message"><?= htmlspecialchars($config['message_template_due_soon']) ?></textarea>
          <small>Variables: {customer}, {amount}, {pending}, {invoice_no}, {due_date}, {days}, {shop}</small>
        </div>

        <div class="wa-template-item">
          <label>⚠️ Overdue Reminder Message</label>
          <textarea name="message_template_overdue" placeholder="Leave empty for default message"><?= htmlspecialchars($config['message_template_overdue']) ?></textarea>
          <small>Variables: {customer}, {amount}, {invoice_no}, {days}, {shop}</small>
        </div>
      </div>
    </div>

    <!-- Cron Setup Instructions -->
    <div class="wa-section">
      <h3>⏰ Auto-Reminder Cron Setup (Windows Task Scheduler)</h3>
      <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px;">
        <p style="margin:0 0 12px;font-size:13px;color:#475569;">
          Due-soon and overdue reminders are sent automatically when this cron endpoint is called daily.
          Set up <strong>Windows Task Scheduler</strong> to run this command daily:
        </p>
        <div style="background:#1e293b;color:#e2e8f0;border-radius:8px;padding:12px 16px;font-family:monospace;font-size:12px;overflow-x:auto;margin-bottom:12px;">
          <code>C:\xampp\php\php.exe "D:\xampp\htdocs\projects\Mymetaji-Website\admin\cron_reminders.php"</code>
        </div>
        <p style="margin:0 0 8px;font-size:13px;color:#475569;"><strong>Steps:</strong></p>
        <ol style="margin:0;padding-left:20px;font-size:12px;color:#64748b;line-height:1.8;">
          <li>Open <strong>Task Scheduler</strong> (search in Start menu)</li>
          <li>Click <strong>Create Basic Task</strong></li>
          <li>Name: <code>Metaji Payment Reminders</code></li>
          <li>Trigger: <strong>Daily</strong> at 9:00 AM</li>
          <li>Action: <strong>Start a Program</strong></li>
          <li>Program: <code>C:\xampp\php\php.exe</code></li>
          <li>Arguments: <code>"D:\xampp\htdocs\projects\Mymetaji-Website\admin\cron_reminders.php"</code></li>
        </ol>
        <p style="margin:12px 0 0;font-size:12px;color:#64748b;">
          🔗 <strong>Test manually:</strong> 
          <a href="cron_reminders.php?key=metaji_cron_2024" target="_blank" style="color:#2563EB;">Click here to run now</a>
        </p>
      </div>
    </div>

    <!-- Test Message -->
    <div class="wa-section">
      <h3>🧪 Send Test Message</h3>
      <div class="wa-grid">
        <div class="form-group">
          <label>Test Phone Number</label>
          <input type="text" name="test_phone" placeholder="9876543210" required>
          <small>Enter a valid WhatsApp number to send a test message</small>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end;">
          <button type="submit" name="action" value="send_test" class="btn btn-outline" style="width:100%;">📤 Send Test</button>
        </div>
      </div>
    </div>

    <div style="text-align:right;margin-top:8px;">
      <button type="submit" name="action" value="save" class="btn btn-primary">💾 Save Settings</button>
    </div>
  </form>

  <!-- Recent Logs -->
  <?php if (!empty($recentLogs)): ?>
  <div class="wa-section" style="margin-top:20px;">
    <h3>📋 Recent Message Log</h3>
    <div style="overflow-x:auto;">
      <table class="wa-log-table">
        <thead>
          <tr><th>Date</th><th>Phone</th><th>Status</th><th>Provider</th><th>Message</th></tr>
        </thead>
        <tbody>
          <?php foreach ($recentLogs as $log): ?>
            <tr>
              <td><?= htmlspecialchars($log['timestamp'] ?? '') ?></td>
              <td><?= htmlspecialchars($log['phone'] ?? '') ?></td>
              <td>
                <?php if (($log['status'] ?? '') === 'sent'): ?>
                  <span style="color:#2F7D32;font-weight:600;">✅ Sent</span>
                <?php elseif (($log['status'] ?? '') === 'failed'): ?>
                  <span style="color:#F44336;font-weight:600;">❌ Failed</span>
                <?php elseif (($log['status'] ?? '') === 'fallback'): ?>
                  <span style="color:#2196F3;font-weight:600;">💬 Direct</span>
                <?php else: ?>
                  <span style="color:#FF9800;font-weight:600;">⚠️ <?= ucfirst($log['status'] ?? '') ?></span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($log['provider'] ?? '') ?></td>
              <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($log['message'] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/layout_bottom.php'; ?>
