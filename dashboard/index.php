<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_login();
$title = 'Overview';
$metrics = [
    ['Rooms', 'rooms', 'room_number', 'rooms/index.php', 'Currently on the property'],
    ['Open requests', 'maintenance_requests', 'id', 'maintenance_requests/index.php', 'Awaiting resolution'],
    ['Active work orders', 'work_orders', 'id', 'work_orders/index.php', 'In progress or open'],
    ['Low stock items', 'inventory', 'id', 'inventory/index.php', 'At or below reorder level'],
];
$counts = [];
foreach ($metrics as $index => [$label, $table, $column]) {
    $condition = match ($index) {
        1 => " WHERE status IN ('Open', 'In progress', 'On hold')",
        2 => " WHERE status IN ('Open', 'In progress', 'On hold')",
        3 => ' WHERE quantity <= reorder_level',
        default => '',
    };
    $counts[$index] = (int) $pdo->query("SELECT COUNT(`{$column}`) FROM `{$table}`{$condition}")->fetchColumn();
}
$recentOrders = $pdo->query('SELECT w.id, w.title, w.priority, w.status, w.due_date, r.room_number FROM work_orders w LEFT JOIN rooms r ON r.id = w.room_id ORDER BY w.created_at DESC LIMIT 5')->fetchAll();
require APP_ROOT . '/includes/header.php';
?>
<section class="welcome-banner"><div><p class="eyebrow">TUESDAY, <?= e(strtoupper(date('F j, Y'))) ?></p><h1>A thoughtful stay starts behind the scenes.</h1><p>Here’s what’s happening across your property today.</p></div><div class="banner-flower">✿</div></section>
<div class="page-heading compact-heading"><div><p class="eyebrow">AT A GLANCE</p><h2>Property overview</h2></div></div>
<section class="metrics-grid">
    <?php foreach ($metrics as $index => [$label, $table, $column, $link, $description]): ?>
        <a class="metric-card" href="<?= e(app_url($link)) ?>"><div class="metric-top"><span><?= e($label) ?></span><span class="metric-icon"><?= ['▦', '⚑', '▤', '▧'][$index] ?></span></div><strong><?= $counts[$index] ?></strong><small><?= e($description) ?></small></a>
    <?php endforeach; ?>
</section>
<section class="panel dashboard-panel"><div class="section-heading"><div><p class="eyebrow">KEEP THINGS MOVING</p><h2>Recent work orders</h2></div><a href="<?= e(app_url('work_orders/index.php')) ?>">View all →</a></div>
    <div class="table-wrap"><table><thead><tr><th>Work order</th><th>Room</th><th>Priority</th><th>Status</th><th>Due</th></tr></thead><tbody>
    <?php foreach ($recentOrders as $order): ?><tr><td><a class="table-title" href="<?= e(app_url('work_orders/view.php?id=' . (int) $order['id'])) ?>"><?= e($order['title']) ?></a></td><td><?= e($order['room_number'] ?: '—') ?></td><td><?= e($order['priority']) ?></td><td><span class="status-pill"><?= e($order['status']) ?></span></td><td><?= e(format_date($order['due_date'])) ?></td></tr><?php endforeach; ?>
    <?php if (!$recentOrders): ?><tr><td class="empty-state" colspan="5">No work orders yet. Create one when something needs attention.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
<?php require APP_ROOT . '/includes/footer.php'; ?>
