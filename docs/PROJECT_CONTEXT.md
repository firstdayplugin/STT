# Sapta Tunas Teknologi — Project Context

> Dokumen ini adalah **sumber kebenaran (source of truth)** untuk konteks proyek.
> Disimpan lebih dulu sebelum coding, sesuai permintaan. Belum ada implementasi CMS.

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

> Daftar ini akan bertambah saat proses "rapihin & benerin bug".

## 10. Sumber Desain

- **Home**: `design/master/home.html` (master, sudah ada).
- **Halaman lain**: Figma "dummy Web Sapta Tunas Alternatif 3"
  (`https://www.figma.com/design/L5oo8aa3AodyCSE1GSncJx/...`).
  Figma tidak bisa diakses langsung oleh asisten (SPA + butuh login), jadi desain dibawa
  sebagai **export PNG per halaman** yang diupload ke chat.

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

## 14. Editabilitas Konten Menyeluruh, Media, Urutan Menu Admin, & Blog

Fondasi yang sudah ada di CMS (dimanfaatkan, bukan bikin dari nol):
- `content_blocks` (helper `get_content(page_key, block_key, default)` / `update_content()`,
  kolom `page_key, block_key, block_label, block_type, konten, is_active`) = teks editable per
  halaman/section berbasis key. Admin: `admin/views/content/`.
- `flex_blocks` (`posisi`, `urutan`, `judul`, dll) = block builder ringan (pengganti sebagian Elementor).
- `menu` + `get_menu_tree()` mendukung banyak lokasi menu → footer menu tinggal tambah lokasi `footer`.
- `gallery`, `grid-icon`, `klien-logo`, `testimonial`, `faq`, `pages`, upload = konten & media terstruktur.

### 14.1 Semua teks editable (header + body + footer), 100% tanpa terkecuali
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
- Klarifikasi "ikon": **ikon UI (Lucide)** = elemen desain tetap (tidak editable). Yang editable =
  **logo/ikon konten** (logo industri, logo klien, ikon layanan, hero image/video) = media, dikelola admin.

### 14.3 Urutan menu admin (UX baca yang enak)
Sidebar dikelompokkan & diurut logis (usulan):
Dashboard → **Konten** (Halaman, Blog, Produk, Layanan, Galeri, Blok/Section) →
**Tampilan** (Menu, Homepage/Section, Theme) → **Media** → **SEO & Tracking** →
**Pengaturan** → **Plugin** → **Pengguna**. (Final menyusul saat rombak admin.)

### 14.4 Blog/News mendekati WordPress
- Sudah ada: kategori, tag, SEO per artikel, TinyMCE, views, penulis, slug, status.
- Kandidat tambahan agar mendekati WP: jadwal terbit (scheduled), draft/pending, featured image +
  excerpt rapi, revisi/riwayat, komentar + moderasi, related posts, RSS feed, reading time,
  search & pagination. *(Komentar menambah attack surface — scope diputuskan; lihat tanya-jawab.)*

---
_Master desain: `design/master/home.html`. CMS: root repo. Dokumen ini diperbarui seiring keputusan proyek._
