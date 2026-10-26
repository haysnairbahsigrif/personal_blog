<?php

declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/index.php');
}

if (!verifyCsrf($_POST['csrf'] ?? null)) {
    setFlash('error', 'Sesi tidak valid.');
    redirect('admin/index.php');
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    setFlash('error', 'ID tidak valid.');
    redirect('admin/index.php');
}

// Ambil info cover sebelum delete
$stmt = $pdo->prepare("SELECT cover_image FROM posts WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

// Hapus file fisik jika ada
if ($row && !empty($row['cover_image']) && !str_starts_with($row['cover_image'], 'http')) {
    $filePath = __DIR__ . '/../' . $row['cover_image'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

// Hapus dari database
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$id]);

setFlash('success', 'Tulisan berhasil dihapus.');
redirect('admin/index.php');
