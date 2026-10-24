<?php
if (!isset($adminTitle)) $adminTitle = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($adminTitle) ?> — Admin <?= e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
    <script>
        (function() {
            const saved = localStorage.getItem('theme');
            const sys = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', saved || sys);
        })();
    </script>
</head>

<body class="admin-body">

    <header class="admin-header">
        <div class="container admin-header-inner">
            <a href="<?= BASE_URL ?>/admin/index.php" class="brand">
                <span class="brand-mark">✒</span>
                <span class="brand-text"><?= e(SITE_NAME) ?></span>
            </a>

            <nav class="admin-nav" aria-label="Navigasi admin">
                <a href="<?= BASE_URL ?>/admin/index.php"
                    class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
                    <?= icon('dashboard') ?> <span>Dashboard</span>
                </a>
                <a href="<?= BASE_URL ?>/admin/post-edit.php"
                    class="<?= basename($_SERVER['PHP_SELF']) === 'post-edit.php' ? 'active' : '' ?>">
                    <?= icon('add') ?> <span>Tulis Baru</span>
                </a>
                <a href="<?= BASE_URL ?>/index.php" target="_blank" rel="noopener">
                    <?= icon('external-link') ?> <span>Lihat Situs</span>
                </a>
                <a href="<?= BASE_URL ?>/admin/logout.php" class="logout">
                    <?= icon('logout-box-r') ?> <span>Keluar</span>
                </a>
            </nav>
        </div>
    </header>

    <main class="admin-main">
        <div class="container">
            <?php if ($flash = getFlash()): ?>
                <div class="alert alert-<?= e($flash['type']) ?>">
                    <?= icon($flash['type'] === 'success' ? 'check' : 'error-warning') ?>
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>