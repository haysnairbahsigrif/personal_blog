/**
 * admin.js — Interaksi halaman admin
 * - Konfirmasi sebelum delete (via data-confirm)
 * - Auto-slug dari judul (aktif saat post-edit.php dibuat)
 * - Counter karakter excerpt
 * - Warning sebelum meninggalkan form dengan perubahan belum disimpan
 */

/* ---------- Sidebar toggle (mobile) ---------- */
const sidebar = document.getElementById('adminSidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebarOverlay = document.getElementById('sidebarOverlay');

if (sidebar && sidebarToggle && sidebarOverlay) {
    function openSidebar() {
        sidebar.classList.add('open');
        sidebarOverlay.hidden = false;
        sidebarToggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        sidebarOverlay.hidden = true;
        sidebarToggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    sidebarToggle.addEventListener('click', openSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);

    // Tutup saat klik link menu (navigasi akan redirect anyway, tapi untuk safety)
    sidebar.querySelectorAll('.sidebar-link').forEach((link) => {
        link.addEventListener('click', () => {
            // Biarkan navigasi default; tutup setelah delay kecil
            setTimeout(closeSidebar, 50);
        });
    });

    // Esc untuk tutup
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) {
            closeSidebar();
        }
    });
}

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