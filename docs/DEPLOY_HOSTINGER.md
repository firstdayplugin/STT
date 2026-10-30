# Deploy STT ke Hostinger — Fresh dari 0

Panduan upload ulang bersih (menghapus yang lama, pasang dari awal).

## 0. Prasyarat
- Akses **hPanel** Hostinger + **File Manager** + **phpMyAdmin**.
- **PHP 8.1+** (hPanel → Advanced → PHP Configuration → pilih 8.1/8.2/8.3).
- Sudah punya database `u383853027_stt` (atau buat baru) beserta **user + password**-nya.

## 1. Ambil file website
Dua cara — pilih salah satu:
- **Download ZIP dari GitHub:** buka repo `firstdayplugin/STT`, pindah ke branch
  `claude/home-page-master-design-v425az`, klik **Code → Download ZIP**, lalu extract.
- **Git (kalau paham):** `git clone -b claude/home-page-master-design-v425az <repo> stt`.

> Yang **tidak perlu** diupload (opsional, biar ringan): folder `preview/`, `docs/`,
> `design/`, `.git/`, dan file `install.php` (lihat langkah 8 — wajib dihapus kalau terlanjur ikut).

## 2. Kosongkan web root (fresh)
Di File Manager, masuk ke root domain (biasanya `public_html/` atau `public_html/<domain>/`).
**Hapus semua isi lama** supaya benar-benar bersih.

## 3. Upload semua file
Upload seluruh isi hasil extract ke web root. Struktur akhir harus begini (file `index.php`
ada tepat di web root, **bukan** di dalam subfolder):
```
public_html/
├── index.php
├── .htaccess          ← penting (lihat langkah 7)
├── admin/
├── assets/
├── core/
├── database/
├── themes/
├── uploads/
└── ...
```
Tip: upload sebagai satu file `.zip` lalu **Extract** di File Manager — jauh lebih cepat
daripada upload ratusan file satu per satu.

## 4. Siapkan Database
Di hPanel → **Databases → MySQL Databases**:
- Pastikan database `u383853027_stt` ada dan punya **user** yang di-assign ke DB itu.
- Catat: **DB name**, **DB user**, **DB password**.
- Kalau DB lama masih berisi tabel: buka phpMyAdmin → pilih DB → tab **Structure** →
  centang **Check all → Drop** (kosongkan dulu), supaya import bersih.

## 5. Import 2 file SQL — URUT
Di **phpMyAdmin**, **pilih database `u383853027_stt` dulu** (klik namanya di kiri), lalu tab **Import**:
1. Import `database/reklamepedia.sql`  → skema + seluruh data awal.
2. Import `database/translations_en.sql` → terjemahan Inggris (biar `/en` full English).

> SQL sudah **portable** (tanpa `CREATE DATABASE`/`USE`), jadi langsung masuk ke DB yang dipilih.
> Kalau file `.sql` besar & timeout: naikkan **PHP max upload** atau import via
> hPanel → phpMyAdmin dalam potongan.

## 6. Isi kredensial database di `config.php`
Edit **`core/config/config.php`** (klik kanan → Edit di File Manager). Ganti 4 baris ini
sesuai kredensial Hostinger-mu:
```php
define('DB_HOST', getenv('CMS_DB_HOST') !== false ? getenv('CMS_DB_HOST') : 'localhost');
define('DB_NAME', getenv('CMS_DB_NAME') !== false ? getenv('CMS_DB_NAME') : 'u383853027_stt');
define('DB_USER', getenv('CMS_DB_USER') !== false ? getenv('CMS_DB_USER') : 'u383853027_stt');   // user DB kamu
define('DB_PASS', getenv('CMS_DB_PASS') !== false ? getenv('CMS_DB_PASS') : 'PASSWORD_DB_KAMU');
```
Cukup ubah bagian **setelah tanda titik dua terakhir** (nilai default string). Simpan.

## 7. Pastikan `.htaccess` terpasang
- File `.htaccess` (diawali titik) sering **ter-hide**. Di File Manager: **Settings →
  Show Hidden Files (dotfiles)**.
- Pastikan ada `.htaccess` di **web root** (ini yang bikin URL `/services`, `/industri`,
  dst. tidak 404). Juga ada `admin/.htaccess` dan `uploads/.htaccess`.

## 8. Hapus `install.php` (keamanan)
Kalau `install.php` ikut terupload di web root, **hapus** — itu wizard instalasi dan tidak
boleh dibiarkan bisa diakses publik.

## 9. Izin folder `uploads/`
Pastikan folder `uploads/` (dan subfoldernya) **writable** (permission `755`, atau `775`
kalau perlu). Ini supaya upload gambar/CV/video dari admin berfungsi.

## 10. Tes
Buka domainmu:
- `/` (Home), `/tentang-kami`, `/solutions`, `/services` (+ `/services/infrastructure-services`),
  `/industri`, `/blog`, `/career`, `/hubungi-kami`, dan versi `/en/...`.
- Login admin: **`/admin`** → user `admin`, password `password`.
  **WAJIB langsung ganti password** di Admin → Pengguna.

## 11. Konfigurasi pasca-deploy (dari Admin)
- **Pengaturan → Hero/Slide:** upload gambar/video hero (video: MP4/WebM).
- **Pengaturan → Kontak & WA / Media Sosial:** isi alamat, telepon, WA, email, sosial, embed Google Maps.
- **Konten Halaman & modul (Services/About/Industri/Career/Blog):** ganti teks & foto placeholder.
- **Pengaturan → Conversion:** isi kunci **Turnstile** (site & secret) untuk anti-spam form.
- **Pengaturan → Warna & Font:** sesuaikan warna brand bila perlu.

## Troubleshooting singkat
- **Semua halaman 404 kecuali Home** → `.htaccess` di web root belum keupload (langkah 7).
- **Error koneksi DB / "Access denied"** → kredensial di `config.php` salah (langkah 6),
  atau user belum di-assign ke DB.
- **Import #1044 / CREATE DATABASE** → jangan pakai file lama; file `.sql` sekarang sudah
  portable, dan **pilih DB dulu** sebelum Import.
- **Gambar/logo tidak muncul** → itu placeholder; upload aset asli dari Admin.
