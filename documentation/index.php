<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/app.php';
header('Location: ' . app_url('documentation/download.php'), true, 302);
exit;
