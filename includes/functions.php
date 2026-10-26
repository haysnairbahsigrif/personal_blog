<?php

declare(strict_types=1);

/** Escape string untuk output HTML (mencegah XSS) */
function e(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/** Buat slug dari judul */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9-]+/', '-', $text) ?? '';
    $text = preg_replace('/-+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'post-' . time();
}

/** Format tanggal dalam Bahasa Indonesia */
function formatDate(string $date, string $format = 'd F Y'): string
{
    $bulan = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember',
        'Jan' => 'Jan',
        'Feb' => 'Feb',
        'Mar' => 'Mar',
        'Apr' => 'Apr',
        'May' => 'Mei',
        'Jun' => 'Jun',
        'Jul' => 'Jul',
        'Aug' => 'Agu',
        'Sep' => 'Sep',
        'Oct' => 'Okt',
        'Nov' => 'Nov',
        'Dec' => 'Des',
    ];
    return strtr(date($format, strtotime($date)), $bulan);
}

/** Estimasi waktu baca (200 kata/menit) */
function readingTime(string $content): int
{
    $wordCount = str_word_count(strip_tags($content));
    return max(1, (int) ceil($wordCount / 200));
}

/** Potong teks untuk excerpt */
function excerpt(string $content, int $length = 160): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($content)) ?? '');
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . '…';
}

/** Render konten (paragraf terpisah dengan baris kosong) */
function renderContent(string $content): string
{
    $escaped    = e($content);
    $paragraphs = preg_split('/\n\s*\n/', trim($escaped)) ?: [];
    $html       = '';
    foreach ($paragraphs as $p) {
        $p = nl2br(trim($p));
        if ($p !== '') {
            $html .= "<p>{$p}</p>\n";
        }
    }
    return $html;
}

/** Auth helpers */
function isLoggedIn(): bool
{
    return isset($_SESSION['admin_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

/** CSRF protection */
function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verifyCsrf(?string $token): bool
{
    return isset($_SESSION['csrf']) && $token !== null && hash_equals($_SESSION['csrf'], $token);
}

/** Flash message (pesan sekali tampil) */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

/** Redirect helper */
function redirect(string $path): never
{
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

/**
 * Render SVG icon dari assets/icons/
 *
 * @param string $name  Nama file tanpa .svg (mis. 'search' untuk search.svg)
 * @param string $class Class CSS tambahan (mis. 'icon-lg')
 * @param int    $size  Ukuran px. 0 = ikut font-size (1em). Pakai nilai px untuk fixed size.
 */
function icon(string $name, string $class = '', int $size = 0): string
{
    static $cache = [];
    $key = $name . '|' . $class . '|' . $size;
    if (isset($cache[$key])) {
        return $cache[$key];
    }

    $path = __DIR__ . '/../assets/icons/' . $name . '.svg';
    if (!file_exists($path)) {
        return '';
    }

    $svg = (string) file_get_contents($path);
    $classes = 'icon' . ($class !== '' ? ' ' . $class : '');
    $sizeAttr = $size > 0 ? ' width="' . $size . '" height="' . $size . '"' : '';

    $svg = preg_replace(
        '/<svg\b([^>]*)>/',
        '<svg$1 class="' . e($classes) . '"' . $sizeAttr . ' aria-hidden="true">',
        $svg,
        1
    ) ?? $svg;

    return $cache[$key] = $svg;
}

/**
 * Mendapatkan URL lengkap untuk gambar cover.
 * Mendukung URL eksternal (http/https) dan file lokal di folder uploads/.
 */
function getCoverUrl(?string $cover): string
{
    if (empty($cover)) {
        return '';
    }
    if (str_starts_with($cover, 'http://') || str_starts_with($cover, 'https://')) {
        return $cover;
    }
    return BASE_URL . '/' . ltrim($cover, '/');
}
