<?php

declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$post = [
    'id'          => 0,
    'title'       => '',
    'slug'        => '',
    'excerpt'     => '',
    'content'     => '',
    'category'    => 'Umum',
    'cover_image' => '',
    'status'      => 'draft',
];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        setFlash('error', 'Tulisan tidak ditemukan.');
        redirect('admin/index.php');
    }
    $post = $found;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? null)) {
        $errors[] = 'Sesi tidak valid. Coba lagi.';
    } else {
        $title   = trim($_POST['title'] ?? '');
        $slugRaw = trim($_POST['slug'] ?? '');
        $slug    = slugify($slugRaw !== '' ? $slugRaw : $title);
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $cat     = trim($_POST['category'] ?? 'Umum');
        $status  = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';

        // Default: pakai gambar lama (kalau mode edit)
        $cover = $post['cover_image'];

        // Proses upload gambar cover baru
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $file    = $_FILES['cover_image'];
            $maxSize = 2 * 1024 * 1024;

            if ($file['size'] > $maxSize) {
                $errors[] = 'Ukuran gambar maksimal 2MB.';
            } else {
                $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                $finfo   = finfo_open(FILEINFO_MIME_TYPE);
                $mime    = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (!in_array($mime, $allowed, true)) {
                    $errors[] = 'Format gambar tidak didukung. Gunakan JPG, PNG, WebP, atau GIF.';
                } else {
                    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) ?: 'jpg';
                    $newName  = 'cover-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $destPath = __DIR__ . '/../uploads/' . $newName;

                    if (move_uploaded_file($file['tmp_name'], $destPath)) {
                        // Hapus gambar lama (jika lokal)
                        if (!empty($post['cover_image']) && !str_starts_with($post['cover_image'], 'http')) {
                            $oldPath = __DIR__ . '/../' . $post['cover_image'];
                            if (file_exists($oldPath)) {
                                unlink($oldPath);
                            }
                        }
                        $cover = 'uploads/' . $newName;
                    } else {
                        $errors[] = 'Gagal mengunggah gambar. Pastikan folder uploads/ dapat ditulis (chmod 775).';
                    }
                }
            }
        } elseif (isset($_POST['remove_cover']) && $_POST['remove_cover'] === '1') {
            // User centang "Hapus gambar ini"
            if (!empty($post['cover_image']) && !str_starts_with($post['cover_image'], 'http')) {
                $oldPath = __DIR__ . '/../' . $post['cover_image'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $cover = null;
        }

        if ($title === '')   $errors[] = 'Judul wajib diisi.';
        if ($content === '') $errors[] = 'Konten wajib diisi.';
        if ($cat === '')     $cat = 'Umum';

        // Pastikan slug unik
        $check = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
        $check->execute([$slug, $id]);
        if ($check->fetch()) {
            $slug .= '-' . time();
        }

        if (empty($errors)) {
            if ($id > 0) {
                $stmt = $pdo->prepare(
                    "UPDATE posts
                     SET title = ?, slug = ?, excerpt = ?, content = ?, category = ?,
                         cover_image = ?, status = ?, updated_at = CURRENT_TIMESTAMP
                     WHERE id = ?"
                );
                $stmt->execute([
                    $title,
                    $slug,
                    $excerpt ?: null,
                    $content,
                    $cat,
                    $cover ?: null,
                    $status,
                    $id,
                ]);
                setFlash('success', 'Tulisan berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO posts (title, slug, excerpt, content, category, cover_image, status)
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([
                    $title,
                    $slug,
                    $excerpt ?: null,
                    $content,
                    $cat,
                    $cover ?: null,
                    $status,
                ]);
                setFlash('success', 'Tulisan berhasil dibuat.');
            }
            redirect('admin/index.php');
        }
    }
}

// Kategori yang sudah dipakai (untuk datalist)
$cats = $pdo->query("SELECT DISTINCT category FROM posts ORDER BY category")
    ->fetchAll(PDO::FETCH_COLUMN);

$adminTitle = $id > 0 ? 'Edit Tulisan' : 'Tulis Baru';
require __DIR__ . '/_header.php';
?>

<div class="editor-header">
    <h1 class="admin-title"><?= $id > 0 ? 'Edit Tulisan' : 'Tulis Baru' ?></h1>
    <p class="admin-subtitle"><?= $id > 0 ? 'Perbarui tulisanmu di sini.' : 'Tuangkan ide dan ceritamu.' ?></p>
</div>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <?= icon('error-warning') ?>
        <ul style="margin:0;padding-left:1.25rem;">
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" class="editor-form" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">

    <div class="editor-grid">
        <div class="editor-main">
            <div class="form-group">
                <label for="title">Judul</label>
                <input type="text" id="title" name="title" required
                    value="<?= e($_POST['title'] ?? $post['title']) ?>"
                    placeholder="Judul tulisan…" class="input-lg">
            </div>

            <div class="form-group">
                <label for="slug">Slug <small>(URL — otomatis dari judul kalau kosong)</small></label>
                <input type="text" id="slug" name="slug"
                    value="<?= e($_POST['slug'] ?? $post['slug']) ?>"
                    placeholder="otomatis-dari-judul">
            </div>

            <div class="form-group">
                <label for="excerpt">Ringkasan <small>(opsional — untuk kartu & SEO)</small></label>
                <textarea id="excerpt" name="excerpt" rows="3" maxlength="500"
                    placeholder="Ringkasan singkat…"><?= e($_POST['excerpt'] ?? $post['excerpt']) ?></textarea>
                <div class="char-count"><span id="excerptCount">0</span>/500</div>
            </div>

            <div class="form-group">
                <label for="content">Konten</label>
                <textarea id="content" name="content" rows="18" required
                    placeholder="Tulis di sini… Pisahkan paragraf dengan baris kosong."><?= e($_POST['content'] ?? $post['content']) ?></textarea>
                <div class="char-count">Pisahkan paragraf dengan baris kosong.</div>
            </div>
        </div>

        <aside class="editor-side">
            <div class="side-card">
                <h3>Publikasi</h3>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="draft" <?= ($post['status'] === 'draft')     ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= ($post['status'] === 'published') ? 'selected' : '' ?>>Terbit</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <?= icon('save') ?> Simpan
                </button>
                <a href="<?= BASE_URL ?>/admin/index.php" class="btn btn-ghost btn-block" style="margin-top:.5rem;">
                    <?= icon('close') ?> Batal
                </a>
            </div>

            <div class="side-card">
                <h3>Detail</h3>
                <div class="form-group">
                    <label for="category">Kategori</label>
                    <input type="text" id="category" name="category" list="catList"
                        value="<?= e($_POST['category'] ?? $post['category']) ?>"
                        placeholder="Mis. Fiksi, Personal">
                    <datalist id="catList">
                        <?php foreach ($cats as $c): ?>
                            <option value="<?= e($c) ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                </div>

                <div class="form-group">
                    <label for="cover_image">Gambar Cover <small>(opsional)</small></label>

                    <?php if (!empty($post['cover_image'])): ?>
                        <div class="cover-preview-wrap">
                            <img src="<?= e(getCoverUrl($post['cover_image'])) ?>" alt="Cover" class="cover-preview">
                            <label class="remove-cover-label">
                                <input type="checkbox" name="remove_cover" value="1">
                                <span>Hapus gambar ini</span>
                            </label>
                        </div>
                    <?php endif; ?>

                    <input type="file" id="cover_image" name="cover_image"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                        class="file-input">
                    <div class="char-count">Max 2MB. Format: JPG, PNG, WebP, GIF.</div>
                </div>
            </div>
        </aside>
    </div>
</form>

<?php require __DIR__ . '/_footer.php'; ?>