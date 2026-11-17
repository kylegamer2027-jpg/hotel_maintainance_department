<?php
declare(strict_types=1);

require_once __DIR__ . '/app.php';

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        set_flash('error', 'Please sign in to continue.');
        redirect('auth/login.php');
    }
}

function current_user(): array
{
    return [
        'id' => (int) ($_SESSION['user_id'] ?? 0),
        'name' => (string) ($_SESSION['user_name'] ?? ''),
        'email' => (string) ($_SESSION['user_email'] ?? ''),
        'role' => (string) ($_SESSION['user_role'] ?? ''),
    ];
}
