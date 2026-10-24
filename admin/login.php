<?php

declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Sudah login? Redirect ke dashboard
if (isLoggedIn()) {
    redirect('admin/index.php');
}

// Belum ada admin? Arahkan ke setup
$hasAdmin = (bool) $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
if (!$hasAdmin) {
    redirect('setup.php');
}

$error = '';
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? null)) {
        $error = 'Sesi tidak valid. Silakan coba lagi.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']   = (int) $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            redirect('admin/index.php');
        }
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — <?= e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>

<body class="auth-body">
    <main class="auth-card">
        <div class="auth-brand">
            <span class="brand-mark" style="font-size:2rem;">✒</span>
            <h1><?= e(SITE_NAME) ?></h1>
            <p>Login untuk mengelola tulisanmu.</p>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>">
                <?= icon('check') ?> <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= icon('error-warning') ?> <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="auth-form">
            <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <?= icon('login-box') ?> Masuk
            </button>
        </form>

        <p class="auth-footer">
            <a href="<?= BASE_URL ?>/index.php"><?= icon('arrow-left') ?> Kembali ke situs</a>
        </p>
    </main>
</body>

</html>