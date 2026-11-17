<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/app.php';

if (!empty($_SESSION['user_id'])) {
    redirect('dashboard/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $error = 'Enter a name, a valid email, and a password of at least 8 characters.';
    } else {
        $count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $role = $count === 0 ? 'admin' : 'staff';
        try {
            $statement = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
            $statement->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;
            redirect('dashboard/index.php');
        } catch (PDOException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }
            $error = 'An account with that email already exists.';
        }
    }
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Create account · Maison</title><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="<?= e(app_url('assets/css/style.css')) ?>"></head>
<body class="auth-page"><main class="auth-card">
    <a class="brand auth-brand" href="<?= e(app_url()) ?>"><span class="brand-mark">M</span><span class="brand-copy"><strong>Maison</strong><small>HOTEL OPERATIONS</small></span></a>
    <p class="eyebrow">GET STARTED</p><h1>Create your account</h1><p class="muted">Your first account becomes the workspace administrator.</p>
    <?php if ($error !== ''): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label for="name">Full name</label><input id="name" name="name" type="text" autocomplete="name" required value="<?= e($_POST['name'] ?? '') ?>">
        <label for="email">Email address</label><input id="email" name="email" type="email" autocomplete="email" required value="<?= e($_POST['email'] ?? '') ?>">
        <label for="password">Password <span class="field-hint">(8 characters minimum)</span></label><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
        <button class="button button-primary button-full" type="submit">Create account</button>
    </form>
    <p class="auth-switch">Already registered? <a href="<?= e(app_url('auth/login.php')) ?>">Sign in</a></p>
</main></body></html>
