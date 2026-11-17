<?php
$currentPath = trim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$navigation = [
    ['dashboard/index.php', 'Overview', '⌂'],
    ['rooms/index.php', 'Rooms', '▦'],
    ['cleaning/index.php', 'Cleaning', '✧'],
    ['inspections/index.php', 'Inspections', '✓'],
    ['maintenance_requests/index.php', 'Requests', '⚑'],
    ['work_orders/index.php', 'Work orders', '▤'],
    ['repairs/index.php', 'Repairs', '⚒'],
    ['equipment/index.php', 'Equipment', '◈'],
    ['inventory/index.php', 'Inventory', '▧'],
    ['lost_found/index.php', 'Lost & found', '⌕'],
    ['schedules/index.php', 'Schedules', '◷'],
    ['staff/index.php', 'Staff', '♙'],
    ['reports/index.php', 'Reports', '▥'],
    ['documentation/download.php', 'Documentation', '▣'],
];
?>
<aside class="sidebar">
    <a class="brand" href="<?= e(app_url('dashboard/index.php')) ?>"><span class="brand-mark">M</span><span class="brand-copy"><strong>Maison</strong><small>HOTEL OPERATIONS</small></span></a>
    <div class="sidebar-label">WORKSPACE</div>
    <nav class="side-nav">
        <?php foreach ($navigation as [$path, $label, $icon]):
            $section = strtok($path, '/');
            $active = $section === 'dashboard' ? $currentPath === 'dashboard' : str_contains($currentPath, $section);
            ?>
            <a class="<?= $active ? 'active' : '' ?>" href="<?= e(app_url($path)) ?>"><span class="nav-icon"><?= e($icon) ?></span><?= e($label) ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer"><div class="status-dot"></div><span>Operations online</span></div>
</aside>
