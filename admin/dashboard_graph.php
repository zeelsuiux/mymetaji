<?php
$graphMonths = [];
$graphCursor = new DateTime('first day of this month');
for ($i = 5; $i >= 0; $i--) {
    $month = (clone $graphCursor)->modify('-' . $i . ' months');
    $key = $month->format('Y-m');
    $graphMonths[$key] = ['label' => $month->format('M'), 'invoices' => 0.0, 'payments' => 0.0];
}
$graphData = isset($data) && is_array($data) ? $data : db_load();
foreach (($graphData['invoices'] ?? []) as $invoice) {
    $date = substr((string)($invoice['date'] ?? $invoice['created_at'] ?? ''), 0, 7);
    if (isset($graphMonths[$date])) $graphMonths[$date]['invoices'] += (float)($invoice['amount'] ?? 0);
}
foreach (($graphData['payments'] ?? []) as $payment) {
    $date = substr((string)($payment['date'] ?? $payment['created_at'] ?? ''), 0, 7);
    if (isset($graphMonths[$date])) $graphMonths[$date]['payments'] += (float)($payment['amount'] ?? 0);
}
$graphMax = 1.0;
foreach ($graphMonths as $month) $graphMax = max($graphMax, $month['invoices'], $month['payments']);
?>
<section class="dashboard-chart panel">
  <div class="dashboard-chart-header">
    <div><span class="chart-eyebrow">Performance</span><h2>Revenue overview</h2><p>Invoices raised versus payments collected over the last 6 months.</p></div>
    <div class="chart-legend"><span><i class="legend-invoice"></i> Invoiced</span><span><i class="legend-payment"></i> Collected</span></div>
  </div>
  <div class="chart-bars" aria-label="Revenue overview chart">
    <?php foreach ($graphMonths as $month):
      $invoiceHeight = max(4, (int)(($month['invoices'] / $graphMax) * 100));
      $paymentHeight = max(4, (int)(($month['payments'] / $graphMax) * 100));
    ?>
      <div class="chart-month">
        <div class="chart-values"><span>₹<?= number_format($month['invoices'], 0) ?></span><span>₹<?= number_format($month['payments'], 0) ?></span></div>
        <div class="chart-track"><b class="chart-bar chart-bar-invoice" style="height: <?= $invoiceHeight ?>%"></b><b class="chart-bar chart-bar-payment" style="height: <?= $paymentHeight ?>%"></b></div>
        <small><?= htmlspecialchars($month['label']) ?></small>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Enhanced Charts Section -->
<section class="panel" style="margin-top:16px;">
  <div class="panel-header"><h2>📈 Last 7 Days Trend</h2></div>
  <div class="panel-body">
    <canvas id="mm-weekly-chart" style="width:100%;height:220px;"></canvas>
  </div>
</section>

<!-- Notifications & Alerts Widget -->
<?php
  $overdueInvoices = get_overdue_invoices();
  $pendingTasks = get_pending_tasks();
  $lowStockProducts = array_values(array_filter($data['products'] ?? [], fn($p) => (float)($p['stock_qty'] ?? 0) <= 5 && (float)($p['stock_qty'] ?? 0) > 0));
?>
<?php if (!empty($overdueInvoices) || !empty($pendingTasks) || !empty($lowStockProducts)): ?>
<section class="panel" style="margin-top:16px;">
  <div class="panel-header"><h2>🔔 Alerts & Notifications</h2></div>
  <div class="panel-body">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;">
      <?php if (!empty($overdueInvoices)): ?>
      <div style="background:#fff3f3;border:1px solid #ffcccc;border-radius:10px;padding:16px;">
        <h4 style="color:#F44336;margin:0 0 8px;">⚠️ Overdue Invoices (<?= count($overdueInvoices) ?>)</h4>
        <ul style="margin:0;padding-left:18px;font-size:13px;color:#666;">
          <?php foreach (array_slice($overdueInvoices, 0, 5) as $inv): ?>
            <li>#<?= htmlspecialchars($inv['invoice_no']) ?> — ₹<?= number_format((float)($inv['amount'] ?? 0), 0) ?> — <?= htmlspecialchars($inv['customer'] ?? '') ?></li>
          <?php endforeach; ?>
        </ul>
        <?php if (count($overdueInvoices) > 5): ?><a href="module.php?m=invoices" style="font-size:12px;color:#F44336;">View all →</a><?php endif; ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($pendingTasks)): ?>
      <div style="background:#f0f7ff;border:1px solid #cce0ff;border-radius:10px;padding:16px;">
        <h4 style="color:#2196F3;margin:0 0 8px;">📋 Pending Tasks (<?= count($pendingTasks) ?>)</h4>
        <ul style="margin:0;padding-left:18px;font-size:13px;color:#666;">
          <?php foreach (array_slice($pendingTasks, 0, 5) as $task): ?>
            <li><?= htmlspecialchars($task['title'] ?? '') ?> — <?= htmlspecialchars($task['assigned_to'] ?? 'Unassigned') ?></li>
          <?php endforeach; ?>
        </ul>
        <?php if (count($pendingTasks) > 5): ?><a href="module.php?m=tasks" style="font-size:12px;color:#2196F3;">View all →</a><?php endif; ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($lowStockProducts)): ?>
      <div style="background:#fff8f0;border:1px solid #ffe0b2;border-radius:10px;padding:16px;">
        <h4 style="color:#FF9800;margin:0 0 8px;">📦 Low Stock Alert (<?= count($lowStockProducts) ?>)</h4>
        <ul style="margin:0;padding-left:18px;font-size:13px;color:#666;">
          <?php foreach (array_slice($lowStockProducts, 0, 5) as $prod): ?>
            <li><?= htmlspecialchars($prod['name'] ?? '') ?> — <?= number_format((float)($prod['stock_qty'] ?? 0), 0) ?> left</li>
          <?php endforeach; ?>
        </ul>
        <?php if (count($lowStockProducts) > 5): ?><a href="module.php?m=products" style="font-size:12px;color:#FF9800;">View all →</a><?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Weekly Chart Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  if (typeof MMChart !== 'undefined') {
    var labels = <?= json_encode(array_values(array_map(fn($m) => $m['label'], $graphMonths))) ?>;
    var invoiceData = <?= json_encode(array_values(array_map(fn($m) => $m['invoices'], $graphMonths))) ?>;
    var paymentData = <?= json_encode(array_values(array_map(fn($m) => $m['payments'], $graphMonths))) ?>;
    MMChart.bar('mm-weekly-chart', labels, invoiceData, ['#1D4ED8', '#2563EB', '#1976D2', '#1E88E5', '#2196F3', '#42A5F5']);
  }
});
</script>
