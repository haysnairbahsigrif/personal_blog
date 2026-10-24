/**
 * admin.js — Interaksi halaman admin
 * - Konfirmasi sebelum delete (via data-confirm)
 * - Auto-slug dari judul (aktif saat post-edit.php dibuat)
 * - Counter karakter excerpt
 * - Warning sebelum meninggalkan form dengan perubahan belum disimpan
 */
(function () {
    'use strict';

    /* ---------- Konfirmasi delete ---------- */
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            const msg = form.dataset.confirm || 'Yakin?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    /* ---------- Auto-slug dari judul ---------- */
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');

    if (titleInput && slugInput) {
        let slugTouched = slugInput.value.trim() !== '';

        slugInput.addEventListener('input', () => {
            slugTouched = true;
        });

        titleInput.addEventListener('input', () => {
            if (slugTouched) return;
            const slug = titleInput.value
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            slugInput.value = slug;
        });
    }

    /* ---------- Counter excerpt ---------- */
    const excerptInput = document.getElementById('excerpt');
    const excerptCount = document.getElementById('excerptCount');
    if (excerptInput && excerptCount) {
        const update = () => {
            excerptCount.textContent = excerptInput.value.length;
        };
        update();
        excerptInput.addEventListener('input', update);
    }

    /* ---------- Warning before leaving with unsaved content ---------- */
    const editorForm = document.querySelector('.editor-form');
    if (editorForm) {
        let dirty = false;
        const mark = () => {
            dirty = true;
        };
        editorForm.querySelectorAll('input, textarea, select').forEach((el) => {
            el.addEventListener('input', mark);
            el.addEventListener('change', mark);
        });
        editorForm.addEventListener('submit', () => {
            dirty = false;
        });

        window.addEventListener('beforeunload', (e) => {
            if (dirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    }
})();