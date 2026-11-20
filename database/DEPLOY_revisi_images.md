# Deploy — Revisi Images + Sync (Home & Solutions)

Urutan wajib. Semua bisa dijalankan berulang (idempotent).

## 1. Upload file (Hostinger File Manager / FTP)
Timpa 2 folder ini apa adanya:
- `themes/anima/`  (template + CSS + JS yang berubah)
- `uploads/`        (gambar baru: hero, portfolio, why, logo partner)

File gambar baru yang masuk:
- `uploads/slides/hero-1.png` … `hero-9.png`  (9 hero baru)
- `uploads/home/pf-1.png` … `pf-4.png`         (background Portfolio)
- `uploads/home/why-1.png` … `why-4.png`        (kartu Why Us)
- `uploads/solutions/logos/*`                   (24 logo partner terpisah)

## 2. Jalankan SQL di phpMyAdmin (URUT dari atas ke bawah)
1. `database/migration_prism_sync.sql`        — animasi cubic (prism) urut + nge-link ke Solutions
2. `database/migration_solution_logos.sql`    — bikin tabel `solution_logos` (WAJIB sebelum no.3)
3. `database/seed_solution_logos.sql`          — isi 24 logo partner per kategori
4. `database/migration_home_client_images.sql` — hero pakai gambar klien + hapus teks hero + portfolio + why

Selesai. Refresh web (Ctrl+F5).

## Yang berubah untuk klien
- **Hero Home**: 9 gambar klien (teks sudah menyatu di gambar; teks database dikosongkan supaya tidak dobel).
- **Portfolio & Why Us**: pakai foto klien.
- **Animasi cubic (Solutions)**: urut mulai Modernize Infrastructure, tiap kubus nge-link ke solusinya.
- **Logo Partner /solutions**: sekarang TERPISAH per logo, bisa di-upload sendiri lewat
  Admin → Solutions Page → Edit kategori → **Partner Logos** (upload banyak / hapus satu-satu).

## Belum diganti (sengaja)
- **Icon Industry (SVG)** & **background orbit "Our Industries"**: aset klien berupa icon garis putih,
  tidak cocok jadi background foto full-bleed orbit — dibiarkan supaya tampilan tidak rusak.
- **Award/ISO/Certification di About**: sudah pakai gambar klien dari revisi sebelumnya.
