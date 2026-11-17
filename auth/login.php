<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/app.php';

if (!empty($_SESSION['user_id'])) {
    redirect('dashboard/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $statement = $pdo->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
    $statement->execute([$email]);
    $user = $statement->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        redirect('dashboard/index.php');
    }
    $error = 'The email or password you entered is incorrect.';
}
$flash = get_flash();
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Sign in · Maison</title><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="<?= e(app_url('assets/css/style.css')) ?>"></head>
<body class="auth-page"><main class="auth-card">
    <a class="brand auth-brand" href="<?= e(app_url()) ?>"><span class="brand-mark">M</span><span class="brand-copy"><strong>Maison</strong><small>HOTEL OPERATIONS</small></span></a>
    <p class="eyebrow">WELCOME BACK</p><h1>Sign in to your workspace</h1><p class="muted">Keep your property running beautifully.</p>
    <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label for="email">Email address</label><input id="email" name="email" type="email" autocomplete="email" required value="<?= e($_POST['email'] ?? '') ?>">
        <label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required>
        <button class="button button-primary button-full" type="submit">Sign in</button>
    </form>
    <p class="auth-switch">New here? <a href="<?= e(app_url('auth/register.php')) ?>">Create an account</a></p>
</main></body></html>
