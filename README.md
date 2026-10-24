# ✒️ Tinta Rasa — Personal Blog

Web app blog personal untuk menuangkan tulisan personal & fiksi. Dibangun dengan PHP native (8.1+), HTML, CSS, dan JavaScript — tanpa framework, untuk mempertajam fundamental.

![Home](screenshots/01-home.png)

## ✨ Fitur

- 📝 Tulis & kelola tulisan (draft/published) dari dashboard admin
- 🌗 Dark mode persisten (light/dark) dengan toggle
- 🔍 Pencarian tulisan
- 📂 Kategori untuk mengelompokkan tulisan
- ⏱️ Estimasi waktu baca otomatis
- 📊 Statistik view per tulisan
- 📱 Responsive penuh (desktop → mobile)
- 🔒 Autentikasi admin dengan password hashing + CSRF protection
- 📖 Reading progress bar di halaman tulisan

## 🖼️ Screenshot

| Beranda | Detail Tulisan |
|---|---|
| ![Home](screenshots/01-home.png) | ![Post](screenshots/02-post.png) |

| Dark Mode | Admin Dashboard |
|---|---|
| ![Dark](screenshots/03-dark.png) | ![Admin](screenshots/04-admin.png) |

## 🛠️ Tech Stack

- **Backend:** PHP 8.1+ (native, PDO)
- **Database:** SQLite (mudah dipindah ke MySQL/PostgreSQL)
- **Frontend:** HTML5, CSS3 (custom properties, grid, flexbox), Vanilla JavaScript
- **Typography:** Lora (heading) & Inter (body) — Google Fonts
- **Icons:** Remix Icon

## 🏗️ Struktur Project

```
personal-blog/
├── config/         # Konfigurasi & koneksi DB
├── includes/       # Komponen reusable (header, footer, helper)
├── admin/          # Panel admin (login, CRUD tulisan)
├── assets/         # CSS & JavaScript
├── database/       # SQLite (di-gitignore)
├── screenshots/    # Gambar untuk README
└── *.php           # Halaman publik
```

## 🚀 Cara Menjalankan Secara Lokal

**Prasyarat:** PHP 8.1+ dengan ekstensi `pdo_sqlite`.

```bash
# 1. Clone repo
git clone https://github.com/USERNAME/personal-blog.git
cd personal-blog

# 2. Jalankan dengan PHP built-in server
php -S localhost:8000

# 3. Buka browser
# http://localhost:8000/setup.php  → buat akun admin pertama
# http://localhost:8000/index.php   → halaman publik
# http://localhost:8000/admin/      → panel admin
```

Alternatif: taruh di `htdocs` XAMPP atau `www` Laragon.

## 🔒 Keamanan

- Password di-hash dengan `password_hash()` (bcrypt)
- Semua query pakai **prepared statements** (anti SQL injection)
- Semua output di-escape dengan `htmlspecialchars()` (anti XSS)
- CSRF token di semua form admin
- Session regenerated saat login
- File database diblok dari akses via `.htaccess`

## 🗺️ Roadmap

- [ ] Upload gambar cover (bukan hanya URL)
- [ ] Rich text editor (Markdown atau WYSIWYG)
- [ ] Komentar pembaca
- [ ] RSS feed
- [ ] Tag (selain kategori)

## 👤 Author

**Firgi Shabriansyah**
- GitHub: https://github.com/haysnairbahsigrif

## 📄 Lisensi

MIT License

Copyright c 2026 Firgi Shabriansyah

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.