<?php

declare(strict_types=1);

/**
 * Konfigurasi & koneksi database.
 * Menggunakan SQLite agar tidak perlu setup MySQL.
 * Untuk produksi bisa diganti ke MySQL/PostgreSQL dengan PDO yang sama.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ====== KONFIGURASI SITUS ======
define('SITE_NAME', 'Tinta Rasa');
define('SITE_TAGLINE', 'Tulisan Personal & Fiksi');
define('POSTS_PER_PAGE', 6);

// Auto-detect base URL agar tidak perlu setting manual
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script   = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$dir      = dirname($script);
if (basename($dir) === 'admin') {
    $dir = dirname($dir);
}
$dir = rtrim(str_replace('\\', '/', $dir), '/');
define('BASE_URL', $protocol . '://' . $host . $dir);

// ====== DATABASE ======
$dbDir = __DIR__ . '/../database';
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}
define('DB_PATH', $dbDir . '/blog.sqlite');

try {
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA journal_mode = WAL');

    // Auto-create schema jika belum ada
    $pdo->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            name TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            excerpt TEXT,
            content TEXT NOT NULL,
            category TEXT NOT NULL DEFAULT 'Umum',
            cover_image TEXT,
            status TEXT NOT NULL DEFAULT 'draft' CHECK(status IN ('draft','published')),
            views INTEGER NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE INDEX IF NOT EXISTS idx_posts_slug     ON posts(slug);
        CREATE INDEX IF NOT EXISTS idx_posts_status   ON posts(status);
        CREATE INDEX IF NOT EXISTS idx_posts_category ON posts(category);
        CREATE INDEX IF NOT EXISTS idx_posts_created  ON posts(created_at);
    SQL);
} catch (PDOException $e) {
    http_response_code(500);
    die('Koneksi database gagal: ' . htmlspecialchars($e->getMessage()));
}
