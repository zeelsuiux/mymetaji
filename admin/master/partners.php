<?php
require_once __DIR__ . '/auth.php';
require_master_login();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/icons.php';

$activePage = 'partners';
$pageTitle = 'Partner Registrations';

$partnersFile = __DIR__ . '/database/partners.json';
if (!file_exists($partnersFile)) file_put_contents($partnersFile, "[]\n", LOCK_EX);
$partners = json_decode(file_get_contents($partnersFile), true) ?: [];
$partners = array_reverse($partners);

$statusFilter = $_GET['status'] ?? 'All';
if ($statusFilter !== 'All') {
    $partners = array_filter($partners, fn($p) => ($p['status'] ?? 'New') === $statusFilter);
    $partners = array_values($partners);
}

require __DIR__ . '/layout_top.php';
?>

<style>
    .partner-count-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 24px; }
    .partner-count-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 18px 20px; text-align: center; transition: all 0.2s; }
    .partner-count-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .partner-count-card .num { font-size: 28px; font-weight: 800; color: #111827; }
    .partner-count-card .lbl { font-size: 13px; color: #64748b; margin-top: 4px; }
    .partner-count-card.active { border-color: #2563EB; background: #eff6ff; }
    .partner-count-card.active .num { color: #2563EB; }
    .filter-pills { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
    .filter-pill { padding: 8px 18px; border-radius: 10px; font-size: 14px; font-weight: 500; border: 1px solid #d1d5db; background: #fff; color: #374151; text-decoration: none; transition: all 0.2s; }
    .filter-pill:hover { background: #f3f4f6; }
    .filter-pill.active { background: #2563EB; color: #fff; border-color: #2563EB; }
    .partner-detail { font-size: 14px; color: #64748b; line-height: 2; }
    .partner-detail strong { color: #334155; font-weight: 600; }
    .partner-status { display: inline-block; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
    .partner-status.new { background: #dbeafe; color: #1e40af; }
    .partner-status.contacted { background: #fef3c7; color: #92400e; }
    .partner-status.approved { background: #d1fae5; color: #065f46; }
    .partner-status.rejected { background: #fee2e2; color: #991b1b; }
    .partner-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px 24px; transition: all 0.2s; }
    .partner-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
    .partner-name { font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 8px; }
</style>

<?php
$newCount = count(array_filter(json_decode(file_get_contents($partnersFile), true) ?: [], fn($p) => ($p['status'] ?? 'New') === 'New'));
$contactedCount = count(array_filter(json_decode(file_get_contents($partnersFile), true) ?: [], fn($p) => ($p['status'] ?? '') === 'Contacted'));
$approvedCount = count(array_filter(json_decode(file_get_contents($partnersFile), true) ?: [], fn($p) => ($p['status'] ?? '') === 'Approved'));
$total = count(json_decode(file_get_contents($partnersFile), true) ?: []);
?>

<div class="partner-count-grid">
    <div class="partner-count-card <?= $statusFilter === 'All' ? 'active' : '' ?>"><div class="num"><?= $total ?></div><div class="lbl">Total Partners</div></div>
    <div class="partner-count-card <?= $statusFilter === 'New' ? 'active' : '' ?>"><div class="num" style="color:#2563EB;"><?= $newCount ?></div><div class="lbl">New</div></div>
    <div class="partner-count-card <?= $statusFilter === 'Contacted' ? 'active' : '' ?>"><div class="num" style="color:#f59e0b;"><?= $contactedCount ?></div><div class="lbl">Contacted</div></div>
    <div class="partner-count-card <?= $statusFilter === 'Approved' ? 'active' : '' ?>"><div class="num" style="color:#10b981;"><?= $approvedCount ?></div><div class="lbl">Approved</div></div>
</div>

<div class="filter-pills">
    <?php foreach (['All','New','Contacted','Approved','Rejected'] as $f): ?>
        <a href="?status=<?= urlencode($f) ?>" class="filter-pill <?= $statusFilter === $f ? 'active' : '' ?>"><?= $f ?></a>
    <?php endforeach; ?>
</div>

<?php if (empty($partners)): ?>
    <div class="empty-state" style="padding:40px;">
        <p>No partner registrations yet.</p>
        <p style="font-size:13px;color:#94a3b8;">Share your partner registration link with potential partners.</p>
    </div>
<?php else: ?>
    <div style="display:grid;gap:14px;">
        <?php foreach ($partners as $p):
            $status = $p['status'] ?? 'New';
            $statusClass = strtolower($status);
        ?>
            <div class="partner-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;">
                    <div>
                        <div class="partner-name">
                            <?= htmlspecialchars($p['name'] ?? '') ?>
                            <span class="partner-status <?= $statusClass ?>"><?= htmlspecialchars($status) ?></span>
                        </div>
                        <div class="partner-detail">
                            <strong>Mobile:</strong> <?= htmlspecialchars($p['phone'] ?? '') ?>
                            <?php if (isset($p['age']) && $p['age'] !== ''): ?> · <strong>Age:</strong> <?= htmlspecialchars((string)$p['age']) ?><?php endif; ?>
                            <br><strong>City:</strong> <?= htmlspecialchars($p['city'] ?? '-') ?>
                            · <strong>State:</strong> <?= htmlspecialchars($p['state'] ?? '-') ?>
                            <?php if (!empty($p['address'])): ?><br><strong>Address:</strong> <?= htmlspecialchars($p['address']) ?><?php endif; ?>
                            <br><strong>Registered:</strong> <?= htmlspecialchars($p['created_at'] ?? '') ?>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;flex-shrink:0;">
                        <?php if ($status === 'New'): ?>
                            <a href="?status_filter=Contacted&id=<?= $p['id'] ?>" class="btn btn-outline btn-sm" onclick="return confirm('Mark as Contacted?')">Contacted</a>
                            <a href="?status_filter=Approved&id=<?= $p['id'] ?>" class="btn btn-outline btn-sm" style="color:#16a34a;border-color:#16a34a;" onclick="return confirm('Approve this partner?')">Approve</a>
                        <?php elseif ($status === 'Contacted'): ?>
                            <a href="?status_filter=Approved&id=<?= $p['id'] ?>" class="btn btn-outline btn-sm" style="color:#16a34a;border-color:#16a34a;" onclick="return confirm('Approve this partner?')">Approve</a>
                            <a href="?status_filter=Rejected&id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Reject this partner?')">Reject</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
// Handle status updates
if (isset($_GET['status_filter'], $_GET['id'])) {
    $newStatus = $_GET['status_filter'];
    $updateId = (int)$_GET['id'];
    if (in_array($newStatus, ['Contacted', 'Approved', 'Rejected'])) {
        $allPartners = json_decode(file_get_contents($partnersFile), true) ?: [];
        foreach ($allPartners as &$pp) {
            if ((int)($pp['id'] ?? 0) === $updateId) {
                $pp['status'] = $newStatus;
                break;
            }
        }
        unset($pp);
        file_put_contents($partnersFile, json_encode($allPartners, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
        header('Location: partners.php?status=' . urlencode($statusFilter) . '&updated=1');
        exit;
    }
}
?>

<?php require __DIR__ . '/layout_bottom.php'; ?>
