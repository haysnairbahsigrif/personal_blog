<?php

declare(strict_types=1);
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Kalau sudah ada admin, redirect ke login
$hasAdmin = (bool) $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
if ($hasAdmin) {
    redirect('admin/login.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $name     = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (mb_strlen($username) < 3) {
        $error = 'Username minimal 3 karakter.';
    } elseif (mb_strlen($name) < 2) {
        $error = 'Nama minimal 2 karakter.';
    } elseif (mb_strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO admins (username, password, name) VALUES (?, ?, ?)");
        $stmt->execute([
            $username,
            password_hash($password, PASSWORD_DEFAULT),
            $name,
        ]);
        setFlash('success', 'Akun admin berhasil dibuat. Silakan login.');
        redirect('admin/login.php');
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Awal — <?= e(SITE_NAME) ?></title>
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
            <h1>Setup Awal</h1>
            <p>Buat akun admin pertamamu untuk mulai menulis.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= icon('error-warning') ?> <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="auth-form">
            <div class="form-group">
                <label for="name">Nama Tampilan</label>
                <input type="text" id="name" name="name" required autofocus
                    value="<?= e($_POST['name'] ?? '') ?>" placeholder="Namamu">
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required
                    value="<?= e($_POST['username'] ?? '') ?>"
                    placeholder="minimal 3 karakter" autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required
                    placeholder="minimal 6 karakter" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="confirm">Konfirmasi Password</label>
                <input type="password" id="confirm" name="confirm" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <?= icon('shield-check') ?> Buat Akun Admin
            </button>
        </form>
    </main>
</body>

</html>