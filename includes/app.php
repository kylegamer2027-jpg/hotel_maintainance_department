<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
$documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? APP_ROOT);
$appRoot = realpath(APP_ROOT);
$relativeRoot = $documentRoot !== false && $appRoot !== false
    ? substr($appRoot, strlen($documentRoot))
    : '';
$relativeRoot = trim(str_replace('\\', '/', $relativeRoot), '/');
define('APP_BASE_URL', $relativeRoot === '' ? '' : '/' . $relativeRoot);

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once APP_ROOT . '/config/database.php';

function app_url(string $path = ''): string
{
    if ($path === '') {
        return APP_BASE_URL === '' ? '/' : APP_BASE_URL;
    }

    return APP_BASE_URL . '/' . ltrim($path, '/');
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . app_url($path));
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $submitted = $_POST['csrf_token'] ?? '';
    if (!is_string($submitted) || !hash_equals(csrf_token(), $submitted)) {
        http_response_code(419);
        exit('Your session expired. Please go back, refresh, and try again.');
    }
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return is_array($flash) ? $flash : null;
}

function format_date(?string $date): string
{
    if (!$date) {
        return '—';
    }

    $timestamp = strtotime($date);
    return $timestamp === false ? $date : date('M j, Y', $timestamp);
}
