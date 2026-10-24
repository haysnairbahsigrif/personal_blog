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
    </div>

    <?php if (empty($posts)): ?>
        <div class="empty-state">
            <?= icon('book-open', 'icon-lg') ?>
            <p>Belum ada tulisan. Editor tulisan akan tersedia di langkah berikutnya.</p>
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/_footer.php'; ?>