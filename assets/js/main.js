/**
 * main.js — Interaksi utama situs
 * - Theme toggle (light/dark) + persistensi
 * - Menu mobile
 * - Panel pencarian
 */
(function () {
    'use strict';

    const root = document.documentElement;
    const themeToggle = document.getElementById('themeToggle');
    const menuToggle = document.getElementById('menuToggle');
    const siteNav = document.getElementById('siteNav');
    const searchToggle = document.getElementById('searchToggle');
    const searchClose = document.getElementById('searchClose');
    const searchBar = document.getElementById('searchBar');

    /* ---------- Theme toggle ---------- */
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const current = root.getAttribute('data-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
        });
    }

    /* ---------- Menu mobile ---------- */
    if (menuToggle && siteNav) {
        menuToggle.addEventListener('click', () => {
            const open = siteNav.classList.toggle('open');
            menuToggle.setAttribute('aria-expanded', String(open));
        });

        document.addEventListener('click', (e) => {
            if (!siteNav.contains(e.target) && !menuToggle.contains(e.target)) {
                siteNav.classList.remove('open');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* ---------- Search panel ---------- */
    function openSearch() {
        if (!searchBar) return;
        searchBar.hidden = false;
        const input = searchBar.querySelector('input');
        if (input) setTimeout(() => input.focus(), 50);
    }

    function closeSearch() {
        if (!searchBar) return;
        searchBar.hidden = true;
    }

    if (searchToggle) searchToggle.addEventListener('click', openSearch);
    if (searchClose) searchClose.addEventListener('click', closeSearch);

    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            openSearch();
        }
        if (e.key === 'Escape') closeSearch();
    });

    /* ---------- Reading progress bar (kalau ada) ---------- */
    const progress = document.getElementById('readingProgress');
    if (progress) {
        window.addEventListener('scroll', () => {
            const scrollTop = window.scrollY;
            const height = document.documentElement.scrollHeight - window.innerHeight;
            const percent = height > 0 ? (scrollTop / height) * 100 : 0;
            progress.style.width = percent + '%';
        }, {
            passive: true
        });
    }
})();