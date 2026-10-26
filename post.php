<?php

declare(strict_types=1);
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$slug = trim($_GET['slug'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    $pageTitle = 'Tidak Ditemukan';
    require __DIR__ . '/includes/header.php';
?>
    <section class="container empty-state">
        <?= icon('book-open', 'icon-lg') ?>
        <h1>404</h1>
        <p>Tulisan yang kamu cari tidak ditemukan.</p>
        <a href="<?= BASE_URL ?>/index.php" class="page-btn">
            <?= icon('arrow-left') ?> Kembali ke Beranda
        </a>
    </section>
<?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

// Increment view counter
$pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$post['id']]);

$pageTitle       = $post['title'];
$pageDescription = excerpt($post['excerpt'] ?: $post['content'], 160);

require __DIR__ . '/includes/header.php';
?>

<div id="readingProgress"
    style="position:fixed;top:0;left:0;height:3px;background:var(--accent);width:0;z-index:200;transition:width .1s;"></div>

<article class="post-single container">
    <header class="post-single-header">
        <a href="<?= BASE_URL ?>/category.php?name=<?= urlencode($post['category']) ?>"
            class="post-card-cat">
            <?= e($post['category']) ?>
        </a>
        <h1><?= e($post['title']) ?></h1>
        <div class="post-single-meta">
            <span><?= icon('calendar') ?> <?= formatDate($post['created_at'], 'd F Y') ?></span>
            <span><?= icon('time') ?> <?= readingTime($post['content']) ?> menit baca</span>
            <span><?= icon('eye') ?> <?= (int) $post['views'] ?> kali dibaca</span>
        </div>
    </header>

    <?php if (!empty($post['cover_image'])): ?>
        <figure class="post-single-cover">
            <img src="<?= e(getCoverUrl($post['cover_image'])) ?>" alt="<?= e($post['title']) ?>">
        </figure>
    <?php endif; ?>

    <div class="post-content">
        <?= renderContent($post['content']) ?>
    </div>

    <a href="<?= BASE_URL ?>/index.php" class="post-back">
        <?= icon('arrow-left') ?> Kembali ke Beranda
    </a>
</article>

<?php require __DIR__ . '/includes/footer.php'; ?>