<?php
/**
 * MY METAJI - Notifications Page
 * Shows all notifications: overdue invoices, pending tasks, expiring quotations
 */
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';

$activeModule = 'notifications';
$pageTitle = 'Notifications';

// Mark as read handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'mark_read') {
        $notifId = (string)($_POST['notif_id'] ?? '');
        if ($notifId !== '') {
            $readNotifs = $_SESSION['read_notifications'] ?? [];
            $readNotifs[] = $notifId;
            $_SESSION['read_notifications'] = array_unique($readNotifs);
        }
    } elseif ($action === 'mark_all_read') {
        $_SESSION['read_notifications'] = ['*'];
    }
    header('Location: notifications.php');
    exit;
}

$readNotifs = $_SESSION['read_notifications'] ?? [];

// Build notifications
$notifications = [];

// 1. Overdue Invoices
$overdueInvoices = get_overdue_invoices();
foreach ($overdueInvoices as $inv) {
    $notifId = 'overdue_' . ($inv['id'] ?? md5($inv['invoice_no'] ?? ''));
    $notifications[] = [
        'id' => $notifId,
        'type' => 'overdue',
        'icon' => 'alert',
        'color' => '#F44336',
        'bg' => '#FFEBEE',
        'title' => 'Overdue Invoice: ' . ($inv['invoice_no'] ?? ''),
        'message' => '₹' . number_format((float)($inv['_pending'] ?? 0), 2) . ' pending for ' . htmlspecialchars($inv['customer'] ?? '') . ' — ' . ($inv['_days_overdue'] ?? 0) . ' days overdue',
        'link' => 'module.php?m=invoices&action=view&id=' . (int)($inv['id'] ?? 0),
        'date' => $inv['due_date'] ?? '',
        'priority' => 'high',
    ];
}

// 2. Pending Tasks
$pendingTasks = get_pending_tasks();
foreach ($pendingTasks as $task) {
    $notifId = 'task_' . ($task['id'] ?? '');
    $isOverdue = !empty($task['due_date']) && $task['due_date'] < date('Y-m-d');
    $notifications[] = [
        'id' => $notifId,
        'type' => 'task',
        'icon' => 'tasks',
        'color' => $isOverdue ? '#F44336' : '#2196F3',
        'bg' => $isOverdue ? '#FFEBEE' : '#E3F2FD',
        'title' => ($isOverdue ? '⚠️ Overdue: ' : '') . htmlspecialchars($task['title'] ?? ''),
        'message' => 'Assigned to: ' . htmlspecialchars($task['assigned_to'] ?? 'Unassigned') . ($task['due_date'] ? ' • Due: ' . format_display_date($task['due_date']) : ''),
        'link' => 'module.php?m=tasks&action=view&id=' . (int)($task['id'] ?? 0),
        'date' => $task['due_date'] ?? '',
        'priority' => $isOverdue ? 'high' : 'medium',
    ];
}

// 3. Expiring Quotations (within 7 days)
$expiringQuotations = get_expiring_quotations(7);
foreach ($expiringQuotations as $quo) {
    $notifId = 'quote_' . ($quo['id'] ?? '');
    $daysLeft = (int)((strtotime($quo['valid_until']) - strtotime('today')) / 86400);
    $notifications[] = [
        'id' => $notifId,
        'type' => 'quotation',
        'icon' => 'quotations',
        'color' => '#FF9800',
        'bg' => '#FFF3E0',
        'title' => 'Quotation Expiring: ' . ($quo['quotation_no'] ?? ''),
        'message' => '₹' . number_format((float)($quo['amount'] ?? 0), 2) . ' for ' . htmlspecialchars($quo['customer'] ?? '') . ' — ' . $daysLeft . ' day' . ($daysLeft !== 1 ? 's' : '') . ' left',
        'link' => 'module.php?m=quotations&action=view&id=' . (int)($quo['id'] ?? 0),
        'date' => $quo['valid_until'] ?? '',
        'priority' => 'medium',
    ];
}

// 4. Low Stock Products
foreach (db_get_all('products') as $product) {
    $stock = (int)($product['stock_qty'] ?? 0);
    if ($stock <= 5 && $stock >= 0) {
        $notifId = 'stock_' . ($product['id'] ?? '');
        $notifications[] = [
            'id' => $notifId,
            'type' => 'stock',
            'icon' => 'products',
            'color' => $stock === 0 ? '#F44336' : '#FF9800',
            'bg' => $stock === 0 ? '#FFEBEE' : '#FFF3E0',
            'title' => ($stock === 0 ? 'Out of Stock: ' : 'Low Stock: ') . htmlspecialchars($product['name'] ?? ''),
            'message' => $stock . ' unit' . ($stock !== 1 ? 's' : '') . ' remaining',
            'link' => 'module.php?m=products&action=view&id=' . (int)($product['id'] ?? 0),
            'date' => '',
            'priority' => $stock === 0 ? 'high' : 'low',
        ];
    }
}

// Sort: high priority first, then by date
usort($notifications, function ($a, $b) {
    $priorityOrder = ['high' => 0, 'medium' => 1, 'low' => 2];
    $pa = $priorityOrder[$a['priority']] ?? 3;
    $pb = $priorityOrder[$b['priority']] ?? 3;
    if ($pa !== $pb) return $pa <=> $pb;
    return strcmp($b['date'], $a['date']);
});

// Count unread
$unreadCount = 0;
foreach ($notifications as $n) {
    if (!in_array('*', $readNotifs, true) && !in_array($n['id'], $readNotifs, true)) {
        $unreadCount++;
    }
}

// Filter
$filter = $_GET['filter'] ?? 'all';
if ($filter === 'unread') {
    $notifications = array_values(array_filter($notifications, fn($n) => !in_array('*', $readNotifs, true) && !in_array($n['id'], $readNotifs, true)));
} elseif ($filter !== 'all') {
    $notifications = array_values(array_filter($notifications, fn($n) => $n['type'] === $filter));
}

require __DIR__ . '/layout_top.php';
?>

<a href="index.php" class="page-back-link">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
  Back to Dashboard
</a>

<style>
.notif-page { max-width: 900px; margin: 0 auto; }
.notif-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.notif-header h2 { margin: 0; font-size: 22px; display: flex; align-items: center; gap: 10px; }
.notif-filters { display: flex; gap: 6px; flex-wrap: wrap; }
.notif-filter-btn { padding: 6px 14px; border: 1px solid #d9dee7; border-radius: 8px; background: #fff; color: #475569; font-size: 13px; cursor: pointer; transition: all 0.15s; }
.notif-filter-btn:hover { border-color: var(--primary, #0d6e6e); color: var(--primary, #0d6e6e); }
.notif-filter-btn.active { background: var(--primary, #0d6e6e); color: #fff; border-color: var(--primary, #0d6e6e); }
.notif-list { display: flex; flex-direction: column; gap: 8px; }
.notif-item { display: flex; align-items: flex-start; gap: 14px; padding: 14px 16px; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; transition: all 0.15s; text-decoration: none; color: inherit; }
.notif-item:hover { box-shadow: 0 2px 12px rgba(0,0,0,0.06); border-color: #d1d5db; }
.notif-item.unread { border-left: 3px solid; background: #fafcff; }
.notif-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 18px; }
.notif-content { flex: 1; min-width: 0; }
.notif-title { font-size: 14px; font-weight: 600; color: #0f172a; margin: 0 0 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.notif-message { font-size: 12px; color: #64748b; margin: 0; line-height: 1.4; }
.notif-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; flex-shrink: 0; }
.notif-time { font-size: 11px; color: #94a3b8; white-space: nowrap; }
.notif-badge-dot { width: 8px; height: 8px; border-radius: 50%; background: #2196F3; }
.notif-actions { display: flex; gap: 8px; margin-bottom: 20px; }
.notif-empty { text-align: center; padding: 50px 20px; color: #64748b; }
.notif-empty-icon { font-size: 48px; margin-bottom: 12px; }
.notif-count { background: #e2e8f0; color: #475569; font-size: 12px; padding: 2px 8px; border-radius: 10px; font-weight: 600; }
@media(max-width:600px) { .notif-header { flex-direction: column; align-items: flex-start; } .notif-meta { display: none; } }
</style>

<div class="notif-page">
  <div class="notif-header">
    <h2>🔔 Notifications <span class="notif-count"><?= $unreadCount ?> unread</span></h2>
    <div class="notif-filters">
      <a href="?filter=all" class="notif-filter-btn <?= $filter === 'all' ? 'active' : '' ?>">All</a>
      <a href="?filter=unread" class="notif-filter-btn <?= $filter === 'unread' ? 'active' : '' ?>">Unread</a>
      <a href="?filter=overdue" class="notif-filter-btn <?= $filter === 'overdue' ? 'active' : '' ?>">Overdue</a>
      <a href="?filter=task" class="notif-filter-btn <?= $filter === 'task' ? 'active' : '' ?>">Tasks</a>
      <a href="?filter=quotation" class="notif-filter-btn <?= $filter === 'quotation' ? 'active' : '' ?>">Quotations</a>
      <a href="?filter=stock" class="notif-filter-btn <?= $filter === 'stock' ? 'active' : '' ?>">Stock</a>
    </div>
  </div>

  <div class="notif-actions">
    <form method="post" style="display:inline;">
      <input type="hidden" name="action" value="mark_all_read">
      <button type="submit" class="btn btn-outline btn-sm">✓ Mark All as Read</button>
    </form>
  </div>

  <?php if (empty($notifications)): ?>
    <div class="notif-empty">
      <div class="notif-empty-icon">🎉</div>
      <h3>All caught up!</h3>
      <p>No notifications right now.</p>
    </div>
  <?php else: ?>
    <div class="notif-list">
      <?php foreach ($notifications as $notif):
        $isRead = in_array('*', $readNotifs, true) || in_array($notif['id'], $readNotifs, true);
      ?>
        <div class="notif-item <?= $isRead ? '' : 'unread' ?>" style="<?= !$isRead ? 'border-left-color:' . $notif['color'] : '' ?>">
          <div class="notif-icon" style="background:<?= $notif['bg'] ?>;color:<?= $notif['color'] ?>;">
            <?= icon($notif['icon'], 20) ?>
          </div>
          <div class="notif-content">
            <div class="notif-title"><?= $notif['title'] ?></div>
            <div class="notif-message"><?= $notif['message'] ?></div>
          </div>
          <div class="notif-meta">
            <?php if ($notif['date']): ?>
              <span class="notif-time"><?= htmlspecialchars(format_display_date($notif['date'])) ?></span>
            <?php endif; ?>
            <div style="display:flex;gap:6px;align-items:center;">
              <?php if (!$isRead): ?>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="action" value="mark_read">
                  <input type="hidden" name="notif_id" value="<?= htmlspecialchars($notif['id']) ?>">
                  <button type="submit" title="Mark as read" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:12px;">✓</button>
                </form>
              <?php endif; ?>
              <a href="<?= htmlspecialchars($notif['link']) ?>" class="btn btn-outline btn-sm" style="padding:4px 10px;font-size:11px;">View →</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/layout_bottom.php'; ?>
