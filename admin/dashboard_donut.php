<?php
$donutTotal = max(1.0, $totalInvoiceAmt + $totalExpenseAmt);
$donutCollected = max(0, $totalPaymentAmt);
$donutPending = max(0, $totalPendingAmt);
$donutExpenses = max(0, $totalExpenseAmt);
$donutCash = max(0, $cashBalance);
$donutSegments = [
  ['label' => 'Collected', 'value' => $donutCollected, 'color' => '#36b878', 'class' => 'donut-active'],
  ['label' => 'Pending', 'value' => $donutPending, 'color' => '#ee555b', 'class' => 'donut-expired'],
  ['label' => 'Expenses', 'value' => $donutExpenses, 'color' => '#f3ad27', 'class' => 'donut-trial'],
  ['label' => 'Cash Balance', 'value' => $donutCash, 'color' => '#4b83df', 'class' => 'donut-pending'],
];
$donutStart = 0;
$donutStops = [];
foreach ($donutSegments as $segment) {
  $donutEnd = $donutStart + (($segment['value'] / $donutTotal) * 100);
  $donutStops[] = $segment['color'] . ' ' . round($donutStart, 2) . '% ' . round($donutEnd, 2) . '%';
  $donutStart = $donutEnd;
}
?>
<div class="dashboard-donut panel">
  <div class="panel-header"><h2>Finance Status</h2><span class="count-pill">Live overview</span></div>
  <div class="dashboard-donut-body">
    <div class="dashboard-donut-ring" style="background: conic-gradient(<?= implode(', ', $donutStops) ?>);"><div><strong>₹<?= number_format($donutTotal, 0) ?></strong><span>Total</span></div></div>
    <div class="dashboard-donut-legend">
      <?php foreach ($donutSegments as $segment): ?><div><i class="subscription-dot <?= $segment['class'] ?>"></i><span><?= htmlspecialchars($segment['label']) ?></span><b>₹<?= number_format($segment['value'], 0) ?></b></div><?php endforeach; ?>
    </div>
  </div>
</div>