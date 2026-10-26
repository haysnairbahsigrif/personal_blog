<?php
if (!isset($pageTitle))       $pageTitle       = SITE_NAME;
if (!isset($pageDescription)) $pageDescription = SITE_TAGLINE;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($pageDescription) ?>">
    <title><?= e($pageTitle) ?> — <?= e(SITE_NAME) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">

    <script>
        (function() {
            const saved = localStorage.getItem('theme');
            const sys = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', saved || sys);
        })();
    </script>
</head>

<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="<?= BASE_URL ?>/index.php" class="brand" aria-label="Beranda">
                <span class="brand-mark" aria-hidden="true">✒</span>
                <span class="brand-text"><?= e(SITE_NAME) ?></span>
            </a>

            <nav class="site-nav" id="siteNav" aria-label="Navigasi utama">
                <a href="<?= BASE_URL ?>/index.php">Beranda</a>
                <a href="<?= BASE_URL ?>/category.php">Kategori</a>
                <a href="<?= BASE_URL ?>/about.php">Tentang</a>
            </nav>

            <div class="header-actions">
                <button class="icon-btn" id="searchToggle" aria-label="Cari">
                    <?= icon('search') ?>
                </button>
                <button class="icon-btn" id="themeToggle" aria-label="Ganti tema">
                    <?= icon('moon', 'theme-icon theme-icon-dark') ?>
                    <?= icon('sun', 'theme-icon theme-icon-light') ?>
                </button>
                <button class="icon-btn menu-toggle" id="menuToggle" aria-label="Menu" aria-expanded="false">
                    <?= icon('menu') ?>
                </button>
            </div>
        </div>

        <div class="search-bar" id="searchBar" hidden>
            <form action="<?= BASE_URL ?>/search.php" method="get" class="container search-form">
                <?= icon('search') ?>
                <input type="search" name="q" placeholder="Cari judul atau isi tulisan…"
                    value="<?= e($_GET['q'] ?? '') ?>"
                    autocomplete="off" aria-label="Kata kunci pencarian">
            </form>
        </div>
    </header>

    <main class="site-main">