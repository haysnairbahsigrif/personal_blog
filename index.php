<?php

declare(strict_types=1);
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Kalau belum ada admin, arahkan ke setup
$hasAdmin = (bool) $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
if (!$hasAdmin) {
    redirect('setup.php');
}

$page   = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * POSTS_PER_PAGE;

$stmt = $pdo->prepare(
    "SELECT * FROM posts WHERE status = 'published'
     ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
);
$stmt->bindValue(':limit',  POSTS_PER_PAGE, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,        PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

$total      = (int) $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
$totalPages = max(1, (int) ceil($total / POSTS_PER_PAGE));

$pageTitle = 'Beranda';
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <h1 class="hero-title">Selamat datang di <em><?= e(SITE_NAME) ?></em></h1>
        <p class="hero-sub"><?= e(SITE_TAGLINE) ?></p>
    </div>
</section>

<section class="container">
    <?php if (empty($posts)): ?>
        <div class="empty-state">
            <?= icon('book-open', 'icon-lg') ?>
            <p>Belum ada tulisan yang dipublikasikan.</p>
        </div>
    <?php else: ?>
        <div class="post-grid">
            <?php foreach ($posts as $post): ?>
                <article class="post-card">
                    <a href="<?= BASE_URL ?>/post.php?slug=<?= urlencode($post['slug']) ?>" class="post-card-link">
                        <?php if (!empty($post['cover_image'])): ?>
                            <div class="post-card-img">
                                <img src="<?= e(getCoverUrl($post['cover_image'])) ?>" alt="<?= e($post['title']) ?>" loading="lazy">
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

        <?php if ($totalPages > 1): ?>
            <nav class="pagination" aria-label="Navigasi halaman">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="page-btn">
                        <?= icon('arrow-left') ?> Sebelumnya
                    </a>
                <?php endif; ?>
                <span class="page-info">Halaman <?= $page ?> dari <?= $totalPages ?></span>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="page-btn">
                        Selanjutnya <?= icon('arrow-right') ?>
                    </a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>