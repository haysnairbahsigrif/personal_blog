<?php
if (!isset($adminTitle)) $adminTitle = 'Dashboard';

// Menu aktif
$current = basename($_SERVER['PHP_SELF']);
$userName = $_SESSION['admin_name'] ?? 'Admin';
$userInitial = mb_strtoupper(mb_substr($userName, 0, 1));
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

    <div class="admin-layout">

        <!-- ================= SIDEBAR ================= -->
        <aside class="admin-sidebar" id="adminSidebar" aria-label="Navigasi admin">
            <a href="<?= BASE_URL ?>/admin/index.php" class="sidebar-brand">
                <span class="brand-mark">✒</span>
                <span>
                    <span class="sidebar-brand-name"><?= e(SITE_NAME) ?></span>
                    <span class="sidebar-brand-badge">Admin</span>
                </span>
            </a>

            <nav class="sidebar-nav">
                <a href="<?= BASE_URL ?>/admin/index.php"
                    class="sidebar-link <?= $current === 'index.php' ? 'active' : '' ?>">
                    <?= icon('dashboard') ?> <span>Dashboard</span>
                </a>
                <a href="<?= BASE_URL ?>/admin/post-edit.php"
                    class="sidebar-link <?= $current === 'post-edit.php' ? 'active' : '' ?>">
                    <?= icon('add') ?> <span>Tulis Baru</span>
                </a>

                <div class="sidebar-divider"></div>

                <a href="<?= BASE_URL ?>/index.php" target="_blank" rel="noopener" class="sidebar-link">
                    <?= icon('external-link') ?> <span>Lihat Situs</span>
                </a>

                <div class="sidebar-divider"></div>

                <a href="<?= BASE_URL ?>/admin/logout.php" class="sidebar-link sidebar-link-danger">
                    <?= icon('logout-box-r') ?> <span>Keluar</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <span class="sidebar-avatar"><?= e($userInitial) ?></span>
                    <span class="sidebar-user-info">
                        <span class="sidebar-user-name"><?= e($userName) ?></span>
                        <span class="sidebar-user-role">Admin</span>
                    </span>
                </div>
            </div>
        </aside>

        <div class="sidebar-overlay" id="sidebarOverlay" hidden></div>

        <!-- ================= MAIN ================= -->
        <div class="admin-content-wrap">

            <!-- Top bar (mobile only) -->
            <header class="admin-topbar">
                <button class="icon-btn" id="sidebarToggle" aria-label="Buka menu" aria-expanded="false">
                    <?= icon('menu') ?>
                </button>
                <span class="topbar-title"><?= e($adminTitle) ?></span>
            </header>

            <main class="admin-main">
                <div class="container">
                    <?php if ($flash = getFlash()): ?>
                        <div class="alert alert-<?= e($flash['type']) ?>">
                            <?= icon($flash['type'] === 'success' ? 'check' : 'error-warning') ?>
                            <?= e($flash['message']) ?>
                        </div>
                    <?php endif; ?>