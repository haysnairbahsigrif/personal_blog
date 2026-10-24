<?php

declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/index.php');
}

// CSRF check
if (!verifyCsrf($_POST['csrf'] ?? null)) {
    setFlash('error', 'Sesi tidak valid.');
    redirect('admin/index.php');
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    setFlash('error', 'ID tidak valid.');
    redirect('admin/index.php');
}

$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$id]);

setFlash('success', 'Tulisan berhasil dihapus.');
redirect('admin/index.php');
