<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
$pageTitle = isset($title) ? $title : 'Hotel Maintenance';
$flash = get_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> · Maison Maintenance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(app_url('assets/css/style.css')) ?>">
    <script defer src="<?= e(app_url('assets/js/script.js')) ?>"></script>
</head>
<body>
<div class="app-shell">
    <?php require APP_ROOT . '/includes/sidebar.php'; ?>
    <main class="main-content">
        <header class="topbar"><div class="topbar-kicker">PROPERTY OPERATIONS</div><div class="user-menu"><span class="user-avatar"><?= e(strtoupper(substr(current_user()['name'] ?: 'U', 0, 1))) ?></span><span><?= e(current_user()['name']) ?></span><form class="logout-form" method="post" action="<?= e(app_url('auth/logout.php')) ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="link-button" type="submit">Sign out</button></form></div></header>
        <div class="content-area">
        <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div><?php endif; ?>
