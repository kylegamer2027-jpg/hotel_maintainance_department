<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_login();
$title = 'Reports';
$summary = [
    ['Rooms', 'SELECT COUNT(*) FROM rooms'],
    ['Open maintenance requests', "SELECT COUNT(*) FROM maintenance_requests WHERE status IN ('Open', 'In progress', 'On hold')"],
    ['Completed work orders', "SELECT COUNT(*) FROM work_orders WHERE status = 'Completed'"],
    ['Repairs this month', 'SELECT COUNT(*) FROM repairs WHERE repair_date >= DATE_FORMAT(CURRENT_DATE, \'%Y-%m-01\')'],
    ['Repair spend this month', 'SELECT COALESCE(SUM(cost), 0) FROM repairs WHERE repair_date >= DATE_FORMAT(CURRENT_DATE, \'%Y-%m-01\')'],
    ['Items to reorder', 'SELECT COUNT(*) FROM inventory WHERE quantity <= reorder_level'],
];
$reportData = [];
foreach ($summary as [$label, $sql]) {
    $reportData[] = ['label' => $label, 'value' => $pdo->query($sql)->fetchColumn()];
}
$lowStock = $pdo->query('SELECT name, quantity, unit, reorder_level FROM inventory WHERE quantity <= reorder_level ORDER BY quantity ASC LIMIT 20')->fetchAll();
$recentRepairs = $pdo->query('SELECT r.repair_date, r.description, r.cost, rm.room_number, s.name AS technician FROM repairs r LEFT JOIN rooms rm ON rm.id = r.room_id LEFT JOIN staff s ON s.id = r.technician_id ORDER BY r.repair_date DESC LIMIT 10')->fetchAll();
require APP_ROOT . '/includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">PROPERTY INSIGHTS</p><h1>Reports</h1><p class="muted">A clear view of operations, stock, and maintenance costs.</p></div><button class="button button-secondary no-print" type="button" onclick="window.print()">Print report</button></div>
<section class="metrics-grid report-grid"><?php foreach ($reportData as $item): ?><article class="metric-card"><div class="metric-top"><span><?= e($item['label']) ?></span></div><strong><?= e(in_array($item['label'], ['Repair spend this month'], true) ? '$' . number_format((float) $item['value'], 2) : (string) $item['value']) ?></strong><small>Current month / current inventory</small></article><?php endforeach; ?></section>
<section class="panel dashboard-panel"><div class="section-heading"><div><p class="eyebrow">RESTOCKING</p><h2>Items at reorder level</h2></div><a href="<?= e(app_url('inventory/index.php')) ?>">Manage inventory →</a></div><div class="table-wrap"><table><thead><tr><th>Item</th><th>On hand</th><th>Reorder at</th></tr></thead><tbody><?php foreach ($lowStock as $item): ?><tr><td><?= e($item['name']) ?></td><td><?= (int) $item['quantity'] ?> <?= e($item['unit']) ?></td><td><?= (int) $item['reorder_level'] ?> <?= e($item['unit']) ?></td></tr><?php endforeach; ?><?php if (!$lowStock): ?><tr><td colspan="3" class="empty-state">Everything is stocked above its reorder level.</td></tr><?php endif; ?></tbody></table></div></section>
<section class="panel dashboard-panel"><div class="section-heading"><div><p class="eyebrow">MAINTENANCE SPEND</p><h2>Recent repairs</h2></div><a href="<?= e(app_url('repairs/index.php')) ?>">View repairs →</a></div><div class="table-wrap"><table><thead><tr><th>Date</th><th>Repair</th><th>Room</th><th>Technician</th><th>Cost</th></tr></thead><tbody><?php foreach ($recentRepairs as $repair): ?><tr><td><?= e(format_date($repair['repair_date'])) ?></td><td><?= e($repair['description']) ?></td><td><?= e($repair['room_number'] ?: '—') ?></td><td><?= e($repair['technician'] ?: '—') ?></td><td>$<?= e(number_format((float) $repair['cost'], 2)) ?></td></tr><?php endforeach; ?><?php if (!$recentRepairs): ?><tr><td colspan="5" class="empty-state">No repair costs to report yet.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require APP_ROOT . '/includes/footer.php'; ?>
