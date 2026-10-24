<?php

declare(strict_types=1);
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$name = trim($_GET['name'] ?? '');

// Mode daftar kategori
if ($name === '') {
    $cats = $pdo->query(
        "SELECT category, COUNT(*) AS total FROM posts
         WHERE status = 'published'
         GROUP BY category
         ORDER BY category"
    )->fetchAll();

    $pageTitle = 'Kategori';
    require __DIR__ . '/includes/header.php';
?>
    <section class="container">
        <h1 class="page-title">Kategori</h1>
        <?php if (empty($cats)): ?>
            <div class="empty-state">
                <?= icon('folder', 'icon-lg') ?>
                <p>Belum ada kategori. Tulisan yang dipublikasikan akan muncul di sini.</p>
            </div>
        <?php else: ?>
            <div class="category-grid">
                <?php foreach ($cats as $c): ?>
                    <a href="?name=<?= urlencode($c['category']) ?>" class="category-card">
                        <h3><?= e($c['category']) ?></h3>
                        <span class="category-count"><?= (int) $c['total'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

// Mode filter per kategori
$stmt = $pdo->prepare(
    "SELECT * FROM posts WHERE category = ? AND status = 'published'
     ORDER BY created_at DESC"
);
$stmt->execute([$name]);
$posts = $stmt->fetchAll();

$pageTitle       = 'Kategori: ' . $name;
$pageDescription = 'Tulisan dalam kategori ' . $name;
require __DIR__ . '/includes/header.php';
?>

<section class="container">
    <h1 class="page-title">Kategori: <?= e($name) ?></h1>

    <?php if (empty($posts)): ?>
        <div class="empty-state">
            <?= icon('folder', 'icon-lg') ?>
            <p>Tidak ada tulisan dalam kategori ini.</p>
            <a href="<?= BASE_URL ?>/category.php" class="page-btn">
                <?= icon('arrow-left') ?> Semua Kategori
            </a>
        </div>
    <?php else: ?>
        <div class="post-grid">
            <?php foreach ($posts as $post): ?>
                <article class="post-card">
                    <a href="<?= BASE_URL ?>/post.php?slug=<?= urlencode($post['slug']) ?>" class="post-card-link">
                        <?php if (!empty($post['cover_image'])): ?>
                            <div class="post-card-img">
                                <img src="<?= e($post['cover_image']) ?>" alt="<?= e($post['title']) ?>" loading="lazy">
                            </div>
                        <?php else: ?>
                            <div class="post-card-img post-card-placeholder" aria-hidden="true">
                                <span><?= e(mb_strtoupper(mb_substr($post['category'], 0, 1))) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="post-card-body">
                            <span class="post-card-cat"><?= e($post['category']) ?></span>
                            <h2 class="post-card-title"><?= e($post['title']) ?></h2>
                            <p class="post-card-excerpt">
                                <?= e(excerpt($post['excerpt'] ?: $post['content'], 140)) ?>
                            </p>
                            <div class="post-card-meta">
                                <span><?= icon('calendar') ?> <?= formatDate($post['created_at'], 'd M Y') ?></span>
                                <span><?= icon('time') ?> <?= readingTime($post['content']) ?> menit</span>
                            </div>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center;margin-top:2.5rem;">
            <a href="<?= BASE_URL ?>/category.php" class="page-btn">
                <?= icon('arrow-left') ?> Semua Kategori
            </a>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>