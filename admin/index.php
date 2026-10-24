<?php

declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC")->fetchAll();

$stats = [
    'total'     => count($posts),
    'published' => count(array_filter($posts, fn($p) => $p['status'] === 'published')),
    'draft'     => count(array_filter($posts, fn($p) => $p['status'] === 'draft')),
    'views'     => array_sum(array_column($posts, 'views')),
];

$adminTitle = 'Dashboard';
require __DIR__ . '/_header.php';
?>

<h1 class="admin-title">Dashboard</h1>
<p class="admin-subtitle">Halo, <?= e($_SESSION['admin_name']) ?>. Selamat datang kembali.</p>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><?= icon('file-text', 'icon-lg') ?></div>
        <div>
            <span class="stat-label">Total Tulisan</span>
            <strong class="stat-value"><?= $stats['total'] ?></strong>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><?= icon('checkbox-circle', 'icon-lg') ?></div>
        <div>
            <span class="stat-label">Terbit</span>
            <strong class="stat-value"><?= $stats['published'] ?></strong>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning"><?= icon('draft', 'icon-lg') ?></div>
        <div>
            <span class="stat-label">Draft</span>
            <strong class="stat-value"><?= $stats['draft'] ?></strong>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><?= icon('eye', 'icon-lg') ?></div>
        <div>
            <span class="stat-label">Total Dibaca</span>
            <strong class="stat-value"><?= number_format($stats['views'], 0, ',', '.') ?></strong>
        </div>
    </div>
</div>

<div class="admin-section">
    <div class="section-head">
        <h2>Semua Tulisan</h2>
        <a href="<?= BASE_URL ?>/admin/post-edit.php" class="btn btn-primary">
            <?= icon('add') ?> Tulis Baru
        </a>
    </div>

    <?php if (empty($posts)): ?>
        <div class="empty-state">
            <?= icon('book-open', 'icon-lg') ?>
            <p>Belum ada tulisan. Yuk mulai menulis!</p>
            <a href="<?= BASE_URL ?>/admin/post-edit.php" class="btn btn-primary">
                <?= icon('add') ?> Tulis Sekarang
            </a>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="posts-table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Tanggal</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $p): ?>
                        <tr>
                            <td>
                                <div class="table-title"><?= e($p['title']) ?></div>
                                <div class="table-sub">/<?= e($p['slug']) ?></div>
                            </td>
                            <td><span class="badge"><?= e($p['category']) ?></span></td>
                            <td>
                                <span class="badge badge-<?= $p['status'] === 'published' ? 'success' : 'warning' ?>">
                                    <?= $p['status'] === 'published' ? 'Terbit' : 'Draft' ?>
                                </span>
                            </td>
                            <td><?= (int) $p['views'] ?></td>
                            <td class="text-muted"><?= formatDate($p['created_at'], 'd M Y') ?></td>
                            <td class="text-right">
                                <div class="action-group">
                                    <?php if ($p['status'] === 'published'): ?>
                                        <a href="<?= BASE_URL ?>/post.php?slug=<?= urlencode($p['slug']) ?>"
                                            target="_blank" rel="noopener"
                                            class="action-btn" title="Lihat">
                                            <?= icon('eye') ?>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= BASE_URL ?>/admin/post-edit.php?id=<?= (int) $p['id'] ?>"
                                        class="action-btn" title="Edit">
                                        <?= icon('edit') ?>
                                    </a>
                                    <form method="post"
                                        action="<?= BASE_URL ?>/admin/post-delete.php"
                                        class="inline-form"
                                        data-confirm="Yakin ingin menghapus tulisan &quot;<?= e($p['title']) ?>&quot;? Tindakan ini tidak bisa dibatalkan.">
                                        <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                                        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                        <button type="submit" class="action-btn danger" title="Hapus">
                                            <?= icon('delete-bin') ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/_footer.php'; ?>