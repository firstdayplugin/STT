# Sapta Tunas Teknologi — Project Context

> Dokumen ini adalah **sumber kebenaran (source of truth)** untuk konteks proyek.
> Disimpan lebih dulu sebelum coding, sesuai permintaan. Belum ada implementasi CMS.

> ## ⛔ ATURAN #1 (PRIORITAS TERTINGGI) — SUMBER DESAIN HOME
> **Halaman Home WAJIB memakai `design/master/home.html`** (file `index.html` yang diupload
> PERTAMA kali). **JANGAN pakai Home versi Figma.** Screenshot Home di Figma (`Home.png`,
> `Home _ Service.png`, `Home SatuAI.png`, dll) **hanya referensi nav/footer**, bukan desain Home.
> Aturan ini mengalahkan hal lain jika ada konflik soal Home.

## 1. Ringkasan Proyek

Membangun **website full CMS** untuk **Sapta Tunas Teknologi (STT)** — sebuah
*enterprise solution provider* di Indonesia (berdiri **2015**), yang menyediakan
*Business Technology Solutions & Services*: IT & Cloud Infrastructure, Cybersecurity,
Data & AI.

Halaman **Home** yang sudah dipresentasikan ke klien menjadi **acuan desain utama
(master design)**. Website final harus mempertahankan tampilan dan nuansa desain ini,
namun kontennya dikelola lewat CMS.

## 2. Status Saat Ini

- **Fase:** menyimpan konteks & master desain. **Belum coding.**
- **Master desain Home:** `design/master/home.html` (disimpan verbatim, jangan diubah).
- Branch pengembangan: `claude/home-page-master-design-v425az`.

> ⚠️ **Jangan mulai implementasi** sampai diinstruksikan. Langkah berikutnya
> (pemilihan CMS/stack, arsitektur, dsb.) menunggu keputusan klien/user.

## 3. Master Desain — Detail Teknis

File: `design/master/home.html` (± 1 MB — besar karena library WebGL & aset di-inline).

### Bahasa & Konten
- `<html lang="id">`, konten campuran Indonesia + Inggris.
- Judul: "Sapta Tunas Teknologi — Enterprise Solution Provider".

### Tipografi
- **Space Grotesk** — heading (`h1–h6`, judul section).
- **Inter** — body text.
- Sumber: Google Fonts.

### Palet Warna (dari `:root`)
| Token | Nilai | Peran |
|-------|-------|-------|
| `--blue` | `#2478E0` | Warna aksen utama |
| `--blue-600` | `#1B63C7` | Hover biru |
| `--blue-050` | `#EAF2FC` | Biru sangat muda |
| `--blue-ink` | `#0E2A4E` | Teks biru gelap |
| `--gold` | `#F0A818` | Aksen emas |
| `--ink` | `#14202E` | Teks utama |
| `--ink-2` | `#3D4C5E` | Teks sekunder |
| `--dim` | `#7A8798` | Teks redup |
| `--line` | `#E4EAF1` | Garis/border |
| `--bg` | `#FFFFFF` | Background |
| `--bg-soft` | `#F4F8FD` | Background lembut |
| `--navy` / `--navy-2` | `#0C1B2E` / `#12233B` | Section gelap |
| `--radius` | `16px` | Border radius |
| `--maxw` | `1180px` | Lebar konten maksimal |

### Teknologi/Efek
- **Three.js / WebGL** di-inline (penyebab utama ukuran file) — dipakai untuk
  animasi Hero, "Prism" (Solutions), dan "Orbit" (Industries).
- Animasi scroll-driven, sticky pin, carousel, word-reveal (statement).
- Header transparan → solid saat scroll (`header.scrolled`), logo invert.
- Logo di-embed sebagai base64.
- Gambar konten memakai **placeholder Pexels** (bukan aset final).

## 4. Struktur Halaman Home (urutan section)

1. **Header / Nav** — sticky, transparan→solid, menu, toggle bahasa (ID/EN), CTA.
2. **Hero** (`#hero`) — slider sinematik gaya "Telkom", eyebrow "Technology Industry",
   headline besar, sub-copy: *"Sapta Tunas Teknologi is established in 2015 with high
   passion and commitment for providing Business Technology Solutions and Services in
   Indonesia."*, blok "Discover Our Company" (link ke tiap section).
3. **Portfolio** (`#portfolio`) — scroll-driven, "Our Track Record". Stats:
   **425+ Project**, **200+ Clients**, **100% Satisfaction**, **26 Awards**.
4. **Solutions** (`#solutions`) — "Prism" WebGL scroll story (caps, dots, logos).
5. **Statement** (`#statement`) — word-reveal, kalimat profil perusahaan.
6. **Industries** (`#industries`) — "Orbit", 8 kartu: Financial, Education, Healthcare,
   Law Enforce, Manufacture, Telecom, Energy, Cross Industry.
7. **News** (`#news`) — carousel artikel (kartu bergambar, tanggal, kategori, "Read More").
8. **Why Us** (`#why`) — "What Sets Us Apart", loop kartu (mis. "Tailored Solutions").
9. **Testimonials** (`#testimonials`) — "What They Say About Us?", grid kartu video/teks.
10. **Contact** (`#contactSection` / `#contact`) — form "Request Proposal"
    (Email, No. Telp/WhatsApp, Message, Submit).
11. **Footer** — gaya "Telkom", kolom: Solutions / Company / Help & Support / Find Us,
    newsletter, sosial media (LinkedIn, Instagram, Facebook, YouTube),
    "© 2026 Sapta Tunas Teknologi", Privacy Policy | Compliance Policy.

## 5. Kandidat Konten yang Perlu Dikelola CMS (untuk perencanaan nanti)

- **Hero slides** (gambar, eyebrow, headline).
- **Portfolio stats** (angka + deskripsi).
- **Solutions** (item + logo).
- **Industries** (nama + link/halaman detail).
- **News/Artikel** (judul, gambar, tanggal, kategori, isi, "Read More").
- **Why Us** (kartu: nomor, judul, subjudul, gambar).
- **Testimonials** (kutipan, nama, jabatan, tipe video/teks, media).
- **Contact form submissions** (endpoint "Request Proposal" — saat ini masih `alert()`).
- **Footer** (menu, newsletter, sosial, teks legal).
- **Global**: logo, toggle bahasa (ID/EN → kandidat multi-bahasa), SEO meta.

## 6. Catatan / Keputusan Terbuka (menunggu user)

- Pemilihan **platform CMS & stack** belum ditentukan.
- Strategi **multi-bahasa** (ID/EN) — sudah ada toggle di desain.
- Penggantian **placeholder Pexels** dengan aset final.
- **Endpoint form** "Request Proposal" & newsletter.
- Halaman lain selain Home (Solutions detail, Industries detail, News detail,
  Company, Career, dll.) — belum didesain.

## 7. CMS yang Dipakai — Reklamepedia CMS

Alih-alih membuat CMS baru, proyek memakai CMS lama buatan sendiri: **Reklamepedia CMS**
(PHP Native + MySQL/PDO, tanpa framework). Sudah pernah dijual, "belum sempurna, masih ada bug".
Diimpor verbatim ke root repo. README asli: [`CMS_README.md`](CMS_README.md).

### Arsitektur inti
- **Router depan** `index.php`: parse URI → `switch` per halaman → `require theme_path('templates/pages/<x>.php')`.
  Rute: `/` & `/home`, `/tentang-kami`, `/layanan[/slug]`, `/gallery`, `/blog[/slug]`, `/produk[/slug]`,
  `/hubungi-kami`, custom page (slug dari tabel `pages`), plus `sitemap.xml`, `robots.txt`, `api/wa-click`.
- **Config** `core/config/config.php`: konstanta DB, `BASE_URL` (auto, support subfolder),
  `THEMES_PATH`, `PLUGINS_PATH` (didefinisikan tapi belum dipakai), dll.
- **DB** `core/database/Database.php` (singleton, `fetchOne/fetchAll/execute`). Schema: `database/reklamepedia.sql`.
- **Helpers** `core/helpers/helpers.php`: `get_setting/update_setting`, `theme_path/theme_url`,
  `get_active_theme`, `get_content/update_content`, `get_menu_tree`, `wa_url`, CSRF, upload, SEO, dll.
- **Admin** `/admin/` (router `admin/index.php` + `admin/views/<modul>/`). Modul: dashboard, blog,
  produk, layanan, gallery, pages, menu, seo/iklan, pengaturan, **plugin**, **template** (theme manager),
  users, wizard. Login default `admin` / `Admin@123`.

### Sistem Theme (dasar untuk "Anima")
- Theme aktif = setting `active_theme` (default `default`). `theme_path('f')` → `themes/<active_theme>/f`.
- **Kontrak folder theme** (`themes/<slug>/`):
  - `theme.json` — metadata (name, slug, version, description, author, tags, screenshot, supports, requires)
  - `screenshot.png`
  - `assets/{css,js,images}`
  - `templates/layouts/` → `header.php`, `footer.php`
  - `templates/pages/` → `home, about, layanan, layanan-detail, gallery, blog, blog-detail,
    produk, produk-detail, contact, custom, 404` (12 file — dipanggil router)
  - `templates/partials/` → `navbar, mobile-nav, wa-float, flex-content, logo-carousel, testimonial-carousel`
- **Theme manager** `admin/views/template/index.php`: `scandir(/themes)` → auto-insert ke tabel `themes`
  → tombol "Aktifkan" set `active_theme` + `themes.is_active`. Theme bawaan: `default`, `omah`, `reklamenesia`.

### Sistem Plugin
- **Feature toggle berbasis DB**: tabel `plugins` (slug, nama, deskripsi, is_active), di-toggle di
  `admin/views/plugin/index.php`. Definisi detail (icon/fitur) masih hardcoded di view. Plugin bawaan: `marketplace`.
- Belum ada plugin-loader dari file / sistem hook — `PLUGINS_PATH` belum dipakai. (Belum modular.)

## 8. Rencana Theme "Anima"

Membuat theme baru `themes/anima/` yang mereproduksi master desain Home (`design/master/home.html`).

- Pecah `index.html` → `templates/layouts/header.php` (head, fonts, nav, buka `<body>`) +
  `templates/pages/home.php` (section-section) + `templates/layouts/footer.php` (footer, script, tutup body).
- Pindah CSS/JS besar (termasuk Three.js WebGL) ke `themes/anima/assets/{css,js}` (jangan inline 1MB di PHP).
- Ganti konten hard-coded → data CMS: `get_setting()` (brand/hero/kontak), query blog untuk News,
  produk/layanan, testimoni, dll. Placeholder Pexels → media dari `uploads/`.
- Lengkapi 12 page template + 6 partial sesuai kontrak. Isi `theme.json` + `screenshot.png`.
- Daftarkan & aktifkan lewat Admin → Template Manager.

> Belum dikerjakan. Menunggu instruksi setelah tahap "rapihin & benerin bug".

## 9. Bug / Gap CMS yang Sudah Teridentifikasi (untuk diperbaiki)

1. **Theme manager tidak membaca `theme.json`** — auto-detect hanya pakai nama folder (`ucfirst`),
   sehingga metadata (deskripsi/versi/author/screenshot) tidak terisi otomatis.
2. **Folder rusak** di `themes/omah/` — ada direktori literal `{assets` dan
   `{assets/{css,js,images},templates` (sisa perintah `mkdir` yang gagal).
3. **Sistem plugin belum modular** — hanya toggle DB + definisi hardcoded; `PLUGINS_PATH` belum dipakai,
   tidak ada plugin-loader/hook.
4. ⚠️ **Kredensial DB asli ter-commit** di `core/config/config.php` (dibiarkan atas keputusan pemilik repo).
5. **Root `.htaccess` tidak ada** di paket, padahal README menyebut clean-URL butuh `mod_rewrite`
   (`.htaccess` hanya ada di `admin/` dan `uploads/`).
6. **Nama file schema beda** — README menyebut `database.sql`, aktualnya `database/reklamepedia.sql`.
7. ✅ **RESOLVED — Schema `database/reklamepedia.sql` diregenerasi agar cocok dengan kode.**
   Sebelumnya SQL yang di-ship memakai penamaan berbeda (schema `services`/`products`/`blog_posts`/
   `custom_texts` & kolom `themes.versi/preview_image/status` vs kode `layanan`/`produk`/`blog`/
   `content_blocks` & `themes.version/screenshot/author/is_installed`) → theme default & seluruh admin 500.
   **`database/reklamepedia.sql` ditulis ulang** (34 tabel, nama/kolom sesuai kode) + perbaikan kolom
   yang ketahuan saat iterasi lawan `error.log`: `users.is_active` (bukan `status`), `produk.gambar_utama`
   (bukan `gambar`). `install.php` diperbaiki merujuk `database/reklamepedia.sql` (bug #6). Anima juga
   dilengkapi 12 template (404/custom/blog/produk/gallery/layanan + detail).
   **Hasil uji (MariaDB lokal): login ✓, admin 22/22 modul 200 tanpa error, frontend 9/9 rute 200,
   `error.log` bersih.** CMS kini benar-benar jalan penuh (frontend + admin + theme Anima).
   *(Metode: bangun schema → import → jalankan rute → baca error.log yang menyebut tabel/kolom hilang →
   perbaiki → ulang, dengan kode sebagai oracle.)*

## 9a. Status Aktivasi Anima di CMS (uji end-to-end — SUDAH)
- `config.php` kini **override via env `CMS_DB_*`** (default hosting tetap; dev/staging bisa override,
  langkah awal mitigasi bug #4 kredensial).
- Theme-manager (`admin/views/template/index.php`) auto-detect kini **membaca `theme.json`**
  (name/description/author/version/screenshot), dibungkus try/catch agar tak fatal saat schema belum selaras.
- **Anima diaktifkan & diuji lewat CMS asli** (MariaDB lokal): `/` , `/hubungi-kami`, `/tentang-kami`
  render **200** end-to-end (index.php → theme_path → Anima). `/layanan/[slug]` masih 500 (router CMS
  query tabel `layanan` yang hilang — efek bug #7, bukan Anima).
- Registrasi live memakai kolom schema aktual (`versi`/`preview_image`/`status`) sebagai workaround
  bug #7; fix theme.json di kode ditulis untuk skema kode (akan benar setelah #7 dibereskan).

## 9b. Menjalankan CMS lokal (dev, tanpa Hostinger)
Sesi ini memasang MariaDB via apt (ada mirror lokal). Langkah:
1. `mariadb-install-db --datadir=/tmp/mariadb-data` lalu jalankan `mariadbd ... --port=3307 --socket=/tmp/mysqld.sock`.
2. Import: `mariadb ... < database/reklamepedia.sql` (membuat DB `reklamepedia`).
3. Buat user dev, lalu jalankan CMS:
   `CMS_DB_HOST='127.0.0.1;port=3307' CMS_DB_NAME='reklamepedia' CMS_DB_USER=... CMS_DB_PASS=... php -S 127.0.0.1:8124 -t . preview/cms-router.php`
4. Frontend Anima aktif via CMS asli. (Preview mock-data tetap: `preview/router.php`.)

> Daftar bug akan bertambah saat proses "rapihin & benerin bug".

## 10. Sumber Desain

- **Home**: `design/master/home.html` (master, sudah ada).
- **Halaman lain**: Figma "dummy Web Sapta Tunas Alternatif 3"
  (`https://www.figma.com/design/L5oo8aa3AodyCSE1GSncJx/...`).
  Dibawa sebagai **export PNG** di **Google Drive** folder `1E3PrfBVAINaqNwWdvRoCXbwDkvOTlJbO`
  (± 100 file). Asisten mengaksesnya via **konektor Google Drive** (bukan link publik):
  `download_file_content` → tersimpan ke file (tak masuk konteks) → decode base64 → PNG → dibaca.
- **Inventory halaman lengkap + pemetaan rute + GAP: lihat [`INVENTORY.md`](INVENTORY.md).**
  Ringkas: Home✅, About Us✅(kaya), Solutions⚠️, Service✅(perlu tabel tier), **Industries⚠️(modul baru)**,
  What's New/blog✅(perlu arsip), **Career⚠️(modul baru + lamaran)**, Contact✅, Privacy/Compliance✅.
  Fitur baru ditemukan: **SatuAI** (chatbot AI — ditunda). Gallery/Produk CMS tak dipakai Sapta
  (disimpan utk white-label).

## 11. Workflow yang Disepakati

**Lingkungan sesi:** PHP 8.4 tersedia (`php -S` untuk preview). **Tidak ada MySQL** dan
**tidak ada konektor Figma**.

**Keputusan cara kerja:**
- **Figma → PNG per halaman.** User export tiap frame jadi gambar dan upload; sebutkan nama halaman.
- **Mulai dari inventory halaman.** Petakan semua halaman Figma → rute/template CMS, temukan gap
  (halaman yang belum ada rutenya, dsb) sebelum membangun.
- **Preview = mock-data mode.** Buat shim data contoh agar template Anima render via `php -S`
  tanpa DB; wiring ke data CMS asli menyusul setelah tampilan disetujui.

**Urutan fase (rencana):**
0. Inventory & pemetaan halaman Figma → rute CMS (temukan gap). ← **tahap berikutnya**
1. Preview harness (mock-data, `php -S`).
2. Fondasi theme `anima/`: design tokens dari `home.html` + header/footer/partial bersama.
3. Konversi Home (dari master) → template theme + wiring data.
4. Konversi halaman lain satu per satu dari PNG Figma (markup → approve → wiring).
5. Rapikan CMS & perbaiki bug (lihat bagian 9), termasuk sistem plugin bila perlu.

**Prinsip per halaman:** markup statis dulu → preview & approve → baru wiring data CMS.
Semua halaman berbagi design tokens & partial yang sama demi konsistensi.

## 12. Keamanan & Target Audit

**Konteks:** Klien = Sapta Tunas Teknologi (`saptatunas.com`), enterprise IT solution provider
yang bisnisnya termasuk **cybersecurity**. Web lama (WordPress) pernah kena issue security dan
mendapat **skor C** dari audit keamanan website pihak ketiga (luar negeri). Target: **minimal B,
idealnya A**. File laporan audit tidak dipegang.

**Analisis penyebab C (dugaan):** Skor huruf A–F untuk "website security" hampir selalu dari
securityheaders.com, Mozilla Observatory, atau Qualys SSL Labs. Penyebab paling mungkin =
**HTTP security headers hilang** (default WordPress tidak mengirim header apa pun) dan/atau
version disclosure + admin/login tanpa rate-limit. **Bukan** karena "WordPress terlalu basic" —
skor soal konfigurasi/higiene, bukan platform. Catatan: pindah ke CMS PHP sendiri **tidak**
otomatis A, dan bisa lebih buruk kalau kode tidak di-harden (WordPress punya ekosistem hardening
yang matang). *(Belum bisa live-scan: egress ke saptatunas.com diblokir dari sesi ini — minta user
menempel hasil securityheaders / Observatory / SSL Labs untuk memastikan.)*

**Keputusan:**
- **Target grade = A** → **CSP ketat (tanpa `unsafe-inline`)**. Ini constraint arsitektur theme.
- **Hosting/CDN = rekomendasi asisten:** origin Hostinger/Apache (header di `.htaccess`) + **Cloudflare**
  di depan (TLS Full-Strict, HSTS, WAF, rate-limit `/admin` & login, sembunyikan origin IP).

**Implikasi "Kejar A" untuk theme Anima (WAJIB saat konversi):**
- Tanpa inline `<script>`/`<style>` → semua ke `assets/js` & `assets/css`.
- Tanpa inline event handler — master Home masih pakai `onclick="alert(...)"` (form) & `onerror="..."`
  (banyak `<img>`); harus diganti `addEventListener` / fallback CSS.
- **Three.js self-host** (`assets/js/`), bukan CDN acak → `script-src 'self'`.
- **Self-host Google Fonts** (Inter + Space Grotesk) → hindari `unsafe`/host eksternal di CSP.
- **Self-host gambar** (ganti placeholder Pexels → `uploads/`) → `img-src 'self'`.
- Inline tak terhindari → pakai **nonce per-request** dari server.

**Header keamanan yang ditargetkan** (origin + Cloudflare): `Strict-Transport-Security`,
`Content-Security-Policy`, `X-Content-Type-Options: nosniff`, `X-Frame-Options`/`frame-ancestors`,
`Referrer-Policy`, `Permissions-Policy`. Plus: cookie sesi `Secure; HttpOnly; SameSite`;
hapus `X-Powered-By`/server tokens; matikan directory listing; `/.well-known/security.txt`.

**Hardening aplikasi (mencegah breach nyata, sebagian dinilai scanner posture):** lindungi `/admin`
(rate-limit/2FA), CSRF (CMS sudah punya — verifikasi menyeluruh), semua query prepared statement
(verifikasi), validasi upload + blokir eksekusi PHP di `uploads/`, secret keluar dari repo/webroot
(lihat bug #4 bagian 9), password bcrypt (sudah).

## 13. Standar UX Admin, Editor, & Aturan Ikon (No-Emoji)

Tiga requirement dari pemilik proyek (menentukan rombakan admin & seluruh UI):

### 13.1 Rombak total halaman admin
Admin harus **sangat intuitif & user-friendly** — "seminimalnya selevel WordPress"; siapa pun bisa
menjalankannya. Notifikasi/warning harus jelas. Ini rombakan **tampilan + UX + komponen** (shell,
layout, komponen), bukan bikin ulang fitur (modul sudah lengkap).

Target UX terukur:
- Layout konsisten (sidebar + topbar + breadcrumb), state aktif jelas.
- List table standar: pencarian, filter, pagination, bulk action, empty-state membantu.
- Aksi utama menonjol; aksi destruktif wajib konfirmasi.
- Validasi form inline + pesan error spesifik.
- Sistem notifikasi konsisten (toast/alert) dengan status success/info/warning/error — **ikon SVG**.
- Microcopy Bahasa Indonesia yang ramah; tooltip bantuan.

### 13.2 Editor di semua textarea konten
- Semua textarea **konten** wajib pakai WYSIWYG yang **enak dipakai** (tidak ada bug spasi
  "enter jadi jauh banget" — ini masalah margin `<p>` default, dibereskan lewat konfigurasi editor,
  bukan ganti editor).
- **Pengecualian:** field pendek/meta (meta description SEO, alamat, judul) **tetap plain text** —
  rich editor di sini membocorkan HTML ke `<meta>` dan merusak SEO.
- Editor **wajib self-host** (bukan CDN) demi CSP-A. Kondisi sekarang: TinyMCE 6.8.2 di-load dari
  cdnjs (`admin/views/layout.php`), global ke `.wysiwyg` → **melanggar CSP ketat, harus diganti self-host**.
- Catatan keamanan: endpoint upload gambar editor (`admin/index.php`) saat ini **tanpa CSRF token**
  (hanya bergantung auth gate) — perlu diperbaiki.
- **KEPUTUSAN editor: TinyMCE, di-self-host + dikonfigurasi ulang.** Alasan: rework terkecil (sudah
  terintegrasi + endpoint upload), output HTML matang untuk CMS PHP, aman CSP-A (self-host,
  tanpa `unsafe-eval`, area edit dalam iframe sendiri). Tugas saat rombak: self-host TinyMCE,
  perbaiki spacing/toolbar/paste, pasang hanya di field konten, tambal CSRF endpoint upload.

### 13.3 ATURAN KERAS: TANPA EMOJI — hanya ikon SVG asli
- **Nol emoji** di mana pun: halaman admin maupun frontend. Aturan mutlak, tidak ada pengecualian.
- Ganti dengan **sistem ikon SVG self-hosted** + helper `icon('nama')` (sprite).
  **KEPUTUSAN set ikon: Lucide** (MIT, garis tipis) — cocok dengan gaya SVG stroke-tipis di master Home,
  dan CSP-friendly. Self-host + helper `icon('nama')` (sprite).
- **Kondisi awal yang harus dibersihkan:** ± **187 kemunculan emoji di 34 file**, terpadat di admin
  (`dashboard` 19, `layanan` 15, `produk` 12, `layout` 11, `wizard` 11, `pengaturan` 11, `menu` 10,
  `template` 9, `flex-blocks` 9, `grid-icon` 8, `plugin` 7, …), sebagian di theme lama (default/omah)
  & komentar core. Emoji di flash/notifikasi juga harus diganti ikon.
- Theme `default`/`omah`/`reklamenesia` = legacy; theme yang dikirim (**Anima**) harus bersih emoji sejak awal.

### 13.4 STATUS — Rombak shell admin (Langkah 1, SELESAI sesi ini)
- **Ikon Lucide self-host.** `core/helpers/icons.php` = helper `icon($name,$size,$class,$attr)` +
  `the_icon()`, 108+ ikon inline-SVG (di-generate dari `lucide-static`, ISC; stroke `currentColor`,
  CSP-safe, tanpa aset eksternal). Di-`require` dari `core/helpers/helpers.php` → tersedia global
  (admin + frontend).
- **Emoji = NOL di seluruh admin.** 414 emoji di 25 file dibersihkan lewat sweep sadar-konteks:
  di HTML text-node → diganti `<?= icon() ?>` (240 ikon), di string PHP/JS/atribut/`<option>` →
  di-strip aman. `layout.php` (sidebar/topbar), `login.php`, semua `views/*` bersih. Diverifikasi: 0 emoji.
- **TinyMCE self-host** di `admin/assets/vendor/tinymce/` (v6.8.2, 4.4MB) — **bukan CDN lagi**.
  Plugin **emoticons DIHAPUS** (aturan no-emoji). `layout.php` init: `base_url` ke vendor, dua tier —
  editor penuh untuk `textarea:not(.no-wysiwyg):not(.wysiwyg-min)`, editor ringkas untuk `.wysiwyg-min`.
  Semua field teknis (CSS/script/robots/meta/alamat) diberi `no-wysiwyg` → tetap plain (tidak regresi).
  Semua aset vendor mereturn 200; editor boot & render terverifikasi (screenshot).
- **CSS** `admin.css` +blok align ikon `.lc`. Sidebar/topbar/dashboard/form terverifikasi rapi
  (WordPress-level), ikon konsisten.
- **BELUM (menyusul):** tambal CSRF endpoint upload editor (§13.2), sweep emoji di theme legacy
  (default/omah) — tidak dipakai Anima, prioritas rendah; naikkan sebagian field `.wysiwyg-min`
  saat rombak per-modul.

## 14. Editabilitas Konten Menyeluruh, Media, Urutan Menu Admin, & Blog

Fondasi yang sudah ada di CMS (dimanfaatkan, bukan bikin dari nol):
- `content_blocks` (helper `get_content(page_key, block_key, default)` / `update_content()`,
  kolom `page_key, block_key, block_label, block_type, konten, is_active`) = teks editable per
  halaman/section berbasis key. Admin: `admin/views/content/`.
- `flex_blocks` (`posisi`, `urutan`, `judul`, dll) = block builder ringan (pengganti sebagian Elementor).
- `menu` + `get_menu_tree()` mendukung banyak lokasi menu → footer menu tinggal tambah lokasi `footer`.
- `gallery`, `grid-icon`, `klien-logo`, `testimonial`, `faq`, `pages`, upload = konten & media terstruktur.

### 14.1 Semua teks editable (header + body + footer), 100% tanpa terkecuali
- **KEPUTUSAN model edit: terstruktur per section** (bukan block-builder). Admin menampilkan field
  per halaman → section (label + hint + thumbnail), urut seperti halaman depan. Paling mudah dicari
  & dirawat. (`flex_blocks` tetap tersedia bila suatu area perlu blok yang bisa diurut.)
- Tidak ada editor visual seperti Elementor Pro → penggantinya **admin terstruktur yang meniru
  struktur halaman depan**. Setiap string Anima dibaca via `get_content()`.
- Buat **"content registry" per template** agar admin otomatis menampilkan **semua** field editable,
  **dikelompokkan per halaman → per section**, urut sama dengan tampilan depan, label jelas + hint
  (idealnya thumbnail section) → "mudah dicari di mana editnya".
- **Footer** (termasuk **menu footer**) wajib editable: menu footer via modul Menu, lokasi baru `footer`.
- Output teks user tetap di-escape/sanitasi saat render (CSP-A + anti-XSS).

### 14.2 Semua aset (foto/video/ikon) editable dari admin — termasuk media animasi Three.js
- Semua media dibaca dari CMS (bukan hardcode); placeholder Pexels di master → media dari `uploads/`.
- **KRUSIAL:** animasi **orbit (Industries)** & **cube/prism (Solutions)** harus **data-driven** —
  daftar gambar/video texture diinject dari PHP ke JS. Karena CSP ketat (no inline JS), caranya via
  **`data-*` attribute** atau **`<script type="application/json">` ber-nonce**, bukan inline script.
  Theme Anima didesain begini sejak awal.
- **Detail requirement (WAJIB, ditegaskan pemilik):**
  - **Cube (prism, Solutions):** array `SLIDES` di `anima.js` (Modernize Infrastructure, Cybersecurity,
    Data & Analytics, Artificial Intelligence, AI Platform Application). Tiap slide **editable**
    (eyebrow/judul/deskripsi) + panel cube bisa diisi **image/foto ATAU video pendek**
    (THREE.TextureLoader / THREE.VideoTexture, ganti texture generate) + baris **logos editable**.
  - **Orbit (Our Industries):** tiap kartu (`.ind2-card`) **editable** (label — sudah via `hc`) + bisa
    diisi **image/foto** per kartu (background-image dari CMS).
- **Status (per sesi ini):**
  - Hero slider ✅ data-driven (`hero_slides`, via `data-hero`).
  - **Cube (Solutions prism) ✅ SUDAH data-driven.** Tabel `solution_slides` (eyebrow/judul/deskripsi/
    label/gambar/video_url/warna_dark|mid|accent/logos(JSON)/url). `home.php` inject `data-slides`
    (JSON) di `<section class="prism">`; `anima.js` baca+override array `SLIDES`, `slideTex()` pilih
    `THREE.VideoTexture` (video) / `THREE.TextureLoader` (image) / `makeTex()` (fallback generate).
    Logos: entri URL/path dipakai langsung, token → base64 built-in `LOGOS`.
  - **Orbit (Our Industries) ✅ SUDAH data-driven.** Tabel `industri` (label/judul/subtitle/gambar/
    warna1/warna2/url). `home.php` render `.ind2-card` dari DB + `data-orbit`, tiap kartu bawa
    `data-img`/`data-c1`/`data-c2`; driver orbit di `anima.js` cat foto (background-image) / gradient
    per kartu via JS style props (CSP-safe, no inline style).
  - Seed default = warna/teks master → install tanpa edit tampil identik; media (foto/video/logo)
    kosong = fallback generate (sama seperti master).
  - **Admin CRUD SELESAI (Langkah 2):** `admin/views/industri/` (kartu orbit: label/judul/subtitle/
    foto/warna/link/urutan) & `admin/views/solusi/` (slide cube: eyebrow/judul/deskripsi/label/
    **foto ATAU video**/3 warna/**logo multi-upload+key/URL**/link/urutan). Helper `upload_video()`
    (mp4/webm, 15MB). Terdaftar di router + sidebar (Konten). Pipeline penuh terverifikasi
    end-to-end: form upload → uploads/ → DB → `home.php` `data-orbit`/`data-slides` → anima.js.
- Klarifikasi "ikon": **ikon UI (Lucide)** = elemen desain tetap (tidak editable). Yang editable =
  **logo/ikon konten** (logo industri, logo klien, ikon layanan, hero image/video) = media, dikelola admin.

### 14.3 Urutan menu admin (UX baca yang enak)
Sidebar dikelompokkan & diurut logis (usulan):
Dashboard → **Konten** (Halaman, Blog, Produk, Layanan, Galeri, Blok/Section) →
**Tampilan** (Menu, Homepage/Section, Theme) → **Media** → **SEO & Tracking** →
**Pengaturan** → **Plugin** → **Pengguna**. (Final menyusul saat rombak admin.)

### 14.4 Blog/News mendekati WordPress
- Sudah ada: kategori, tag, SEO per artikel, TinyMCE, views, penulis, slug, status.
- **KEPUTUSAN scope (wajib ada):**
  - Jadwal terbit + status draft/pending/scheduled (auto-publish saat waktunya).
  - Featured image + excerpt yang rapi (kartu/daftar & share sosial/OG).
  - Related posts (kategori/tag) + reading time.
  - **Komentar + moderasi.**
- **Hardening komentar (karena target A):** moderasi wajib (default *pending*, tampil setelah approve),
  escaping/sanitasi ketat (anti-XSS), CSRF + rate-limit + honeypot, dan **Cloudflare Turnstile**
  (gratis, selaras dgn pemakaian Cloudflare) alih-alih reCAPTCHA. Query pakai prepared statement.
- Tambahan yang mengikuti: search & pagination. (RSS feed & revisi opsional — dievaluasi nanti.)

## 15. Produk Jual-Ulang (White-Label) — Konteks Strategis

**Alasan utama di balik semua requirement "editable":** website ini adalah **produk yang dijual
ulang**, bukan proyek sekali pakai. Setelah **BAST dengan Sapta Tunas**, pemilik akan mengganti
semua teks, susunan menu, footer, logo, warna, dll lalu menjualnya ke klien lain (sudah ada pemesan).

**Implikasi wajib:**
- **Nol hardcoding.** Nama brand, logo (light/dark), **warna aksen/palet**, font, kontak, seluruh copy,
  menu, footer, dan media — semuanya dari settings/DB. Master Home penuh string & warna hardcoded →
  Anima **wajib meng-externalize semuanya**. Tidak boleh ada string "Sapta Tunas" yang hardcoded.
- **Rebrand instan lewat admin.** Theme Anima membaca warna aksen & font dari settings (CMS sudah
  punya pengaturan "Tampilan/warna aksen") → klien baru ganti identitas tanpa sentuh kode.
- **Model deploy = 1 klien : 1 install terpisah** (DB + uploads + config masing-masing), bukan
  multi-tenant. Lebih aman (isolasi data antar klien) & mudah BAST/handover. *(Perlu konfirmasi user.)*
- **Starter content & reset.** Butuh jalur cepat mengganti/mengosongkan konten demo saat jual ulang:
  seed konten awal dan/atau export-import konten. Installer (`install.php`/wizard) jadi kunci dan
  **wajib dihapus setelah instalasi** (keamanan).
- Konsekuensi keamanan: kredensial DB per-install harus di-set oleh installer, bukan hardcoded
  (perkuat perbaikan bug #4 §9) — penting karena kode didistribusikan ke banyak klien.

## 16. Manajemen Menu (Wajib: selevel WordPress, drag & drop, custom link)

**Status:** sebagian besar SUDAH ada di `admin/views/menu/index.php`:
- Drag & drop (SortableJS) untuk reorder + nesting (geser kanan → sub-menu, via `parent_id`).
- Custom Link (label + URL bebas), Open in new tab (`target`), picker halaman/layanan/blog/produk/kategori.
- Fitur auto-inject sub-menu layanan.

**Gap yang harus ditutup:**
1. **SortableJS di-load dari CDN jsdelivr** → melanggar CSP-A → **self-host**.
2. **Belum ada "lokasi menu"** — tabel `menus` tak punya kolom lokasi; baru satu menu. Perlu tambah
   konsep **lokasi menu** (header, footer, mobile) ala WordPress agar footer menu (§14) & jual-ulang
   terdukung.
3. Init script menu inline → buat CSP-safe (nonce / file eksternal).

## 17. Fitur 2 Bahasa (EN & ID)

Prinsip: **opsional + fallback + dua tingkat** agar tidak menyusahkan pemilik tapi klien terlayani.

- **Dua tingkat teks:**
  - **UI/tema** (label generik: "Read More", "Discover", dll) → file bahasa `lang/id.php` & `lang/en.php`
    + helper `t('key')`. Diterjemahkan sekali oleh kita, dikirim bersama theme. Beban pemilik ~nol.
  - **Konten** (content_blocks, halaman, artikel, label menu, tagline, dll) → disimpan **per-bahasa**
    (mis. `konten_id`/`konten_en`), admin menampilkan **tab ID/EN** per field.
- **Fallback wajib:** nilai bahasa kosong → jatuh ke `default_lang` (ID). Situs tak pernah rusak;
  terjemahan bisa bertahap. Ini kunci "tidak menyusahkan".
- **Feature flag:** setting `languages` + `default_lang` per install (i18n bisa dimatikan → switcher hilang).

**KEPUTUSAN:**
- **Strategi URL = path prefix `/en/`.** Bahasa default (ID) tanpa prefix (`/tentang-kami`);
  EN di `/en/tentang-kami`. Tambah tag **`hreflang`** + **`og:locale`** untuk SEO. Router perlu
  mengenali segmen bahasa terdepan (`/en`) → set current lang, sisanya route seperti biasa.
- **Default install = bilingual (ID + EN)** (`default_lang=id`, `languages=['id','en']`).
  Master Home sudah punya toggle ID/EN → switcher bahasa dipertahankan di header.
- Slug: default **satu slug dipakai kedua bahasa** (dibedakan prefix `/en/`); slug terlokalisasi = opsi lanjutan.
- (Opsional, nanti) tombol "sarankan terjemahan EN" via API — ditunda (egress/kualitas).

## 18. Bahasa sebagai Produk (N-language readiness & monetisasi)

Tujuan pemilik: bahasa jadi **fitur yang diuangkan** — mis. jual paket **Mandarin** terpisah dengan
harga premium di masa depan. Syaratnya: menambah bahasa baru harus **murah bagi kita** (tanpa ubah
kode) → desain untuk **N bahasa sejak awal**, bukan 2.

**Yang disiapkan sekarang (cheap now, mahal di-retrofit):**
1. **Registry bahasa (data, bukan kode):** tabel berisi `code, nama, native_name, arah (LTR/RTL),
   font_override, aktif, is_default, urutan`. Tambah bahasa = INSERT 1 baris + aktifkan. Router,
   admin, switcher, tab terjemahan semua **render dinamis dari registry** (tak ada daftar hardcoded).
2. **Penyimpanan konten skalabel — pola BARIS terjemahan** (`translations(entity, field_key,
   lang_code, value)`), BUKAN kolom-per-bahasa. Tambah bahasa = baris baru, **nol migrasi skema**.
   (Evolusi natural dari `content_blocks`; posts/pages/produk/layanan ikut pola sama.)
3. **UI/tema via language pack file** (`lang/<code>.php` atau PO/gettext). **Paket bahasa = unit jual.**
4. **Font & arah per-bahasa:** `font_override` (mis. Noto Sans SC untuk CJK — Inter/Space Grotesk tak
   punya glyph Han); `arah` + **CSS logical properties** di Anima → siap RTL bila jual Arab/Ibrani nanti.
5. **Router N-language:** `/en/` digeneralisasi jadi `/<code>/` (baca segmen pertama thd registry) +
   **fallback chain** ke `default_lang`.
6. **Tooling paket bahasa (penggerak monetisasi):** export/import semua string translatable
   (CSV/JSON/PO). Alur jual bahasa: export → terjemah → import → aktifkan (menit, bukan proyek).

**Pihak ketiga / plugin — kesimpulan:**
- **WPML/Polylang/TranslatePress** = plugin WordPress → **tidak relevan** (stack kita PHP custom).
- **Weglot/Bablic/ConveyThis (SaaS translate runtime)** = **TIDAK worth it**: langganan berulang
  (makan margin jual-ulang), **bentrok CSP-A** (butuh domain eksternal di script/connect-src),
  translate DOM sisi-klien rawan pada home WebGL, dan kita jadi sekadar reseller fitur yang justru
  mau dijual sendiri.
- **Worth it (alat PRODUKSI, bukan runtime):** **DeepL/Google Translate API** untuk draft paket
  bahasa (lalu review manusia) → dipakai saat membuat paket, tak menyentuh situs live/CSP;
  **PO/gettext + Poedit** untuk string UI (tool penerjemah standar); **`ext-intl`** untuk format
  tanggal/angka per-locale.

**KEPUTUSAN:**
- **Penyimpanan = pola BARIS terjemahan (N-language ready).** Tanpa migrasi skema saat tambah bahasa.
  Kompleksitas query diimbangi caching (manfaatkan `$GLOBALS['__content_cache']` yang sudah ada +
  cache per-locale). UI strings tetap via language pack file.
- **Tooling paket bahasa = FASE LANJUTAN.** Arsitektur disiapkan sekarang (registry bahasa + tabel
  translations + fallback + router `/<code>/`), tapi export/import string & MT-assist dibangun setelah
  web inti (Anima + admin) jadi. Rilis pertama: admin manual dengan tab per bahasa.

**Opsi GRATIS (jawaban: "pihak ke-3 gratis yang tetap /en /id"):**
- **`/en` `/id` = router sendiri** (path-prefix, sudah diputuskan) → gratis & milik sendiri; tak perlu
  pihak ketiga. (Bahkan SaaS berbayar baru kasih URL path-based di tier berbayar.)
- **SaaS translate (Weglot/ConveyThis/Bablic):** free tier ada tapi menipu — batas ~1 bahasa/
  ~2.000 kata, URL path `/en/` umumnya fitur berbayar, tetap bentrok CSP-A + langganan + bukan aset.
  **Tidak dipakai.**
- **FOSS gratis yang dipakai (self-host, $0, tanpa masalah CSP):** `symfony/translation` (MIT) atau
  **gettext/PO + Poedit** untuk string UI; **`ext-intl`** untuk format locale; **LibreTranslate/Argos**
  (self-host) sebagai MT gratis untuk draft paket bahasa (alternatif DeepL). DeepL berbayar hanya
  dipertimbangkan bila butuh kualitas Mandarin terbaik — opsional.

---
_Master desain: `design/master/home.html`. CMS: root repo. Dokumen ini diperbarui seiring keputusan proyek._
