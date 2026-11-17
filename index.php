<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/app.php';
redirect(empty($_SESSION['user_id']) ? 'auth/login.php' : 'dashboard/index.php');
