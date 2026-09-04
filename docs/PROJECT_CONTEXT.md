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

---
_Master desain: `design/master/home.html`. CMS: root repo. Dokumen ini diperbarui seiring keputusan proyek._
