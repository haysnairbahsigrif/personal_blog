<?php

declare(strict_types=1);
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$q     = trim($_GET['q'] ?? '');
$posts = [];

if ($q !== '') {
    $stmt = $pdo->prepare(
        "SELECT * FROM posts
         WHERE status = 'published'
           AND (title LIKE :q1 OR content LIKE :q2 OR excerpt LIKE :q3)
         ORDER BY created_at DESC"
    );
    $like = '%' . $q . '%';
    $stmt->execute([':q1' => $like, ':q2' => $like, ':q3' => $like]);
    $posts = $stmt->fetchAll();
}

$pageTitle = $q !== '' ? 'Pencarian: ' . $q : 'Pencarian';
require __DIR__ . '/includes/header.php';
?>

<section class="container">
    <h1 class="page-title">Hasil Pencarian</h1>
    <p style="text-align:center;color:var(--muted);margin-bottom:2.5rem;">
        <?php if ($q === ''): ?>
            Masukkan kata kunci pencarian.
        <?php else: ?>
            <strong style="color:var(--text);"><?= count($posts) ?></strong>
            hasil untuk &ldquo;<?= e($q) ?>&rdquo;
        <?php endif; ?>
    </p>

    <?php if ($q !== '' && empty($posts)): ?>
        <div class="empty-state">
            <?= icon('search', 'icon-lg') ?>
            <p>Tidak ada tulisan yang cocok dengan pencarianmu.</p>
            <a href="<?= BASE_URL ?>/index.php" class="page-btn">
                <?= icon('arrow-left') ?> Kembali ke Beranda
            </a>
        </div>
    <?php elseif (!empty($posts)): ?>
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
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>