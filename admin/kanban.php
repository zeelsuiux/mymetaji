<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';

$module = $_GET['m'] ?? 'tasks';
if (!in_array($module, ['tasks', 'quotations', 'leads'], true)) $module = 'tasks';
if (!can_access_module($module)) {
  http_response_code(403);
  die('You do not have permission to access this board.');
}
$activeModule = $module;
$pageTitle = $MODULES[$module]['label'] . ' — Board View';
$config = $MODULES[$module];

$stageField = $module === 'leads' ? 'stage' : 'status';
$columns = $config['fields'][$stageField]['options'] ?? [];
$rows = member_visible_rows($module, db_get_all($module));
if ($module === 'leads') {
  $leadInvoices = db_get_all('invoices');
  $leadProducts = db_get_all('products');
  foreach ($rows as &$leadRow) $leadRow['stage'] = lead_automatic_stage($leadRow, $leadInvoices, $leadProducts);
  unset($leadRow);
}

$byStatus = [];
foreach ($columns as $col) $byStatus[$col] = [];
foreach ($rows as $row) {
  $status = $row[$stageField] ?? ($columns[0] ?? '');
    if (!isset($byStatus[$status])) $byStatus[$status] = [];
  $byStatus[$status][] = $row;
}

function priority_badge($p) {
    $cls = $p === 'High' ? 'red' : ($p === 'Medium' ? 'orange' : 'gray');
    return '<span class="badge ' . $cls . ' kc-priority">' . htmlspecialchars($p ?: 'Low') . ' priority</span>';
}

require __DIR__ . '/layout_top.php';
?>

<div class="panel">
  <div class="panel-header">
    <h2><?= icon($config['icon']) ?> <?= htmlspecialchars($config['label']) ?> <span class="count-pill">(<?= count($rows) ?>)</span></h2>
    <div class="toolbar">
      <div class="view-switch">
        <a href="module.php?m=<?= $module ?>&view=list"><?= icon('list', 15) ?> List</a>
        <a href="kanban.php?m=<?= $module ?>" class="active"><?= icon('board', 15) ?> Board</a>
      </div>
      <?php if ($module === 'leads'): ?>
        <label class="search-box" aria-label="Search leads">
          <span class="search-icon"><?= icon('search', 15) ?></span>
          <input type="search" id="kanban-lead-search" placeholder="Search leads" autocomplete="off">
        </label>
      <?php endif; ?>
      <?php if (can_manage_module($module)): ?><a href="module.php?m=<?= $module ?>&action=new" class="btn btn-primary"><?= icon('add', 16) ?> Add New</a><?php endif; ?>
    </div>
  </div>
  <div class="panel-body">
    <p style="color:var(--text-muted);font-size:13px;margin-top:0;">Drag a card between columns to update its status.</p>

    <div class="kanban-board">
      <?php foreach ($columns as $col): ?>
        <div class="kanban-col" data-status="<?= htmlspecialchars($col) ?>">
          <div class="kanban-col-head">
            <span class="title"><?= htmlspecialchars($col) ?></span>
            <span class="badge gray"><?= count($byStatus[$col]) ?></span>
          </div>
          <div class="kanban-col-body">
            <?php if (empty($byStatus[$col])): ?>
              <div class="kanban-empty">No <?= strtolower($module === 'quotations' ? 'quotations' : 'tasks') ?></div>
            <?php endif; ?>
            <?php foreach ($byStatus[$col] as $row): ?>
              <div class="kanban-card" draggable="<?= can_manage_module($module) && ($module === 'leads' || is_admin() || can_manage_record($module, $row)) ? 'true' : 'false' ?>" data-id="<?= $row['id'] ?>">
                <div class="kc-title"><?= htmlspecialchars($module === 'quotations' ? ($row['quotation_no'] ?? '-') : ($module === 'leads' ? ($row['name'] ?? '-') : ($row['title'] ?? '-'))) ?></div>
                <div class="kc-meta">
                  <?php if ($module === 'leads' && !empty($row['company'])): ?><span><?= icon('building', 12) ?> <?= htmlspecialchars($row['company']) ?></span><?php endif; ?>
                  <?php if ($module === 'leads' && !empty($row['phone'])): ?><span><?= icon('phone', 12) ?> <?= htmlspecialchars($row['phone']) ?></span><?php endif; ?>
                  <?php if (!empty($row['customer'])): ?><span><?= icon('customers', 12) ?> <?= htmlspecialchars($row['customer']) ?></span><?php endif; ?>
                  <?php if ($module === 'tasks' && !empty($row['assigned_to'])): ?><span><?= icon('customers', 12) ?> <?= htmlspecialchars($row['assigned_to']) ?></span><?php endif; ?>
                  <?php if (!empty($row['due_date'])): ?><span><?= icon('calendar', 12) ?> <?= htmlspecialchars(format_display_date($row['due_date'])) ?></span><?php endif; ?>
                </div>
                <?php if ($module === 'tasks'): ?><?= priority_badge($row['priority'] ?? 'Low') ?><?php endif; ?>
                <?php if (is_admin() || is_subadmin()): ?>
                  <div style="margin-top:8px;display:flex;gap:6px;flex-wrap:wrap;">
                    <a class="btn btn-outline btn-sm" href="module.php?m=<?= $module ?>&action=view&id=<?= $row['id'] ?>"><?= icon('view', 13) ?> View</a>
                    <?php if (is_admin() || (is_subadmin() && can_manage_record($module, $row))): ?>
                      <a class="btn btn-outline btn-sm" href="module.php?m=<?= $module ?>&action=edit&id=<?= $row['id'] ?>"><?= icon('edit', 13) ?></a>
                      <a class="btn btn-danger btn-sm" href="module.php?m=<?= $module ?>&action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Delete this record?')"><?= icon('delete', 13) ?></a>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const cards = document.querySelectorAll('.kanban-card');
  const cols = document.querySelectorAll('.kanban-col');
  let dragId = null;

  const leadSearch = document.getElementById('kanban-lead-search');
  if (leadSearch) {
    leadSearch.addEventListener('input', function () {
      const query = leadSearch.value.trim().toLowerCase();
      document.querySelectorAll('.kanban-col').forEach((column) => {
        let visibleCount = 0;
        column.querySelectorAll('.kanban-card').forEach((card) => {
          const matches = query === '' || card.textContent.toLowerCase().includes(query);
          card.hidden = !matches;
          if (matches) visibleCount++;
        });
        const countBadge = column.querySelector('.kanban-col-head .badge');
        if (countBadge) countBadge.textContent = visibleCount;
      });
    });
  }

  cards.forEach(card => {
    card.addEventListener('dragstart', () => {
      dragId = card.dataset.id;
      card.style.opacity = '0.5';
    });
    card.addEventListener('dragend', () => { card.style.opacity = '1'; });
  });

  cols.forEach(col => {
    col.addEventListener('dragover', e => {
      e.preventDefault();
      col.classList.add('drag-over');
    });
    col.addEventListener('dragleave', () => col.classList.remove('drag-over'));
    col.addEventListener('drop', e => {
      e.preventDefault();
      col.classList.remove('drag-over');
      if (!dragId) return;
      const newStatus = col.dataset.status;
      fetch('kanban_update.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'module=<?= $module ?>&id=' + encodeURIComponent(dragId) + '&status=' + encodeURIComponent(newStatus)
      }).then(() => location.reload());
    });
  });
});
</script>

<?php require __DIR__ . '/layout_bottom.php'; ?>
