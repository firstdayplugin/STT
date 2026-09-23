# 🎨 Reklamepedia CMS

CMS khusus untuk perusahaan periklanan & reklame. Dibangun dengan PHP Native + MySQL tanpa framework berat.

---

## ✨ Fitur Utama

| Fitur | Keterangan |
|-------|-----------|
| 🏠 Frontend | Homepage, Tentang, Layanan, Galeri, Blog, Produk, Kontak |
| ⚙️ Admin Panel | Dashboard, CRUD semua konten, pengaturan lengkap |
| 📦 Manajemen Produk | Katalog produk + gallery + link marketplace |
| 🗂️ Manajemen Layanan | Layanan dengan sub-layanan dinamis |
| 🖼️ Galeri | Multi-upload drag & drop, kategori, featured |
| ✏️ Blog/Artikel | Editor TinyMCE, kategori, tags, SEO per artikel |
| 💬 WhatsApp | Floating panel WA, tracking klik, multi kontak |
| 📊 Analytics | GA4, GTM, Meta Pixel, TikTok Pixel, statistik visitor |
| 🔍 SEO | Sitemap otomatis, robots.txt editor, meta per halaman |
| 🔐 Multi Role | 5 role: superadmin, admin, penulis, admin_produk, tim_ads |
| 🚀 Setup Wizard | 8-langkah wizard untuk konfigurasi awal |
| 🧩 Plugin System | Aktif/nonaktif fitur tambahan |

---

## 📋 Persyaratan Server

- **PHP** 8.0 atau lebih baru
- **MySQL** 5.7+ atau MariaDB 10.3+
- **Apache** dengan `mod_rewrite` aktif (atau Nginx)
- **Ekstensi PHP**: PDO, PDO_MySQL, GD, Fileinfo, mbstring

---

## 🚀 Instalasi

### Cara 1: Via Installer (Rekomendasi)

1. Upload semua file ke server/hosting
2. Buka `https://domain-anda.com/install.php`
3. Ikuti langkah-langkah di installer
4. **Hapus `install.php`** setelah selesai!

### Cara 2: Manual

1. **Buat database** MySQL baru
2. **Import schema**: jalankan `database.sql` via phpMyAdmin atau CLI:
   ```bash
   mysql -u username -p nama_database < database.sql
   ```
3. **Edit konfigurasi** di `core/config/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'nama_database');
   define('DB_USER', 'username');
   define('DB_PASS', 'password');
   ```
4. **Set izin folder** `uploads/` agar dapat ditulis (755):
   ```bash
   chmod -R 755 uploads/
   ```
5. Buka website dan login ke `/admin/`

---

## 🔑 Akses Admin Default

| Field    | Value                    |
|----------|--------------------------|
| URL      | `/admin/` atau `/admin/login.php` |
| Username | `admin`                  |
| Password | `Admin@123`              |

> ⚠️ **Segera ganti password** setelah login pertama via Setup Wizard!

---

## 📁 Struktur Folder

```
reklamepedia/
├── admin/               # Admin panel
│   ├── assets/css/      # CSS admin (dark theme)
│   ├── views/           # View files per modul
│   │   ├── blog/
│   │   ├── dashboard/
│   │   ├── gallery/
│   │   ├── layanan/
│   │   ├── menu/
│   │   ├── pages/
│   │   ├── pengaturan/
│   │   ├── plugin/
│   │   ├── produk/
│   │   ├── seo/
│   │   ├── users/
│   │   └── wizard/
│   ├── index.php        # Admin router
│   └── login.php        # Login page
│
├── core/                # Core system
│   ├── config/          # Database & app config
│   ├── database/        # Database class + setting helpers
│   ├── helpers/         # Helper functions
│   └── router/          # Sitemap & robots.txt
│
├── themes/              # Frontend themes
│   └── default/
│       ├── assets/css/  # Frontend CSS
│       └── templates/   # PHP templates
│           ├── layouts/ # Header & footer
│           ├── pages/   # Page templates
│           └── partials/# Navbar, WA float, dll
│
├── uploads/             # File upload storage
├── database.sql         # Schema + default data
├── index.php            # Main router
├── install.php          # Installer (hapus setelah install!)
└── .htaccess            # URL rewriting
```

---

## 🎨 Kustomisasi

### Warna & Font
Buka **Admin → Pengaturan → Tampilan** untuk mengubah:
- Warna aksen (default: amber `#e8a020`)
- Font heading & body
- Custom CSS tambahan

### Konten Homepage
Semua teks di homepage dapat diedit via:
- **Admin → Pengaturan → Umum** (nama, tagline, alamat)
- **Admin → Pengaturan → Hero** (banner utama)
- Tambah **Layanan**, **Testimoni**, **FAQ** lewat menu masing-masing

### WhatsApp
- **Admin → Pengaturan → Kontak & WA**: atur nomor dan pesan default
- Tambah banyak kontak WA (CS, Sales, Tim Teknis) untuk panel float

---

## 🔒 Keamanan

- CSRF protection di semua form
- Brute-force protection di login (5 percobaan → lockout 5 menit)
- Upload file divalidasi tipe MIME
- Folder `admin/`, `core/`, `uploads/` dilindungi `.htaccess`
- Password di-hash dengan `bcrypt`
- Session timeout 2 jam

---

## 📱 Modul Admin

| Modul | Deskripsi |
|-------|-----------|
| Dashboard | Statistik, quick actions, chart pengunjung, log aktivitas |
| Blog | CRUD artikel + TinyMCE editor + SEO fields |
| Produk | Katalog produk + gallery + link marketplace |
| Layanan | Layanan + sub-layanan dinamis |
| Galeri | Upload batch + kategori + featured |
| Halaman | Custom static pages |
| Menu | Navigasi website |
| Iklan & Tracking | GA4, GTM, Pixel, custom scripts |
| SEO Tools | Sitemap, robots.txt, SEO checklist |
| Pengaturan | Semua konfigurasi website |
| Plugin | Aktif/nonaktif fitur |
| Pengguna | Manajemen user + roles |
| Wizard | Setup awal step-by-step |

---

## 🆘 Troubleshooting

**Halaman 500 Error**
- Aktifkan DEBUG_MODE di `core/config/config.php`: `define('DEBUG_MODE', true);`
- Periksa error di log server

**Upload gagal**
- Pastikan folder `uploads/` memiliki izin `755`
- Cek `upload_max_filesize` dan `post_max_size` di `php.ini`

**Clean URL tidak bekerja**
- Pastikan `mod_rewrite` aktif: `a2enmod rewrite`
- Cek `AllowOverride All` di konfigurasi Apache

**Tidak bisa login**
- Reset password langsung di database:
  ```sql
  UPDATE users SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE username = 'admin';
  ```
  (Password: `password`)

---

## 📄 Lisensi

Dikembangkan khusus untuk **Reklamepedia**. Hak cipta dilindungi.

---

*Dibuat dengan ❤️ menggunakan PHP Native + MySQL*
