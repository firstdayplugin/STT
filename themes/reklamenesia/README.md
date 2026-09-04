# Reklamenesia — Tema Reklamepedia CMS

Konversi **pixel-faithful** dari situs Framer `reklamenesia.framer.website` menjadi tema PHP yang tinggal pasang (drop-in) untuk **Reklamepedia CMS**. Tidak ada perubahan apa pun pada backend/database — tema ini hanya berisi layer tampilan dan mengikuti *data contract* yang sama dengan tema `default`.

Dibangun dengan: **PHP native + HTML semantik + CSS modular + JS vanilla** (Lenis sebagai progressive enhancement). Tanpa React/Vue/Tailwind.

---

## ✨ Yang direplikasi dari Framer

- **Palet**: brand merah `#C21F1F`, near-black `#0C0C0C`, cream `#FFF5EC`.
- **Tipografi**: Instrument Sans (display/UI) + Inter (body) via Google Fonts.
- **Navbar** transparan di atas hero merah, jadi solid + blur saat scroll (logo putih ⇄ gelap otomatis).
- **Hero** gradien merah gelap dengan animasi load bertahap.
- **Marquee** gambar proyek + **pill layanan** auto-scroll tak terputus (Framer Ticker style).
- **About + 4 kartu statistik** dengan asterisk merah berputar.
- **Services** kartu image-tile besar dengan watermark "OUR SERVICES" dan tombol panah (glass → merah saat hover).
- **Testimonials** carousel center-featured dengan prev/next + dots.
- **FAQ** akordeon bernomor `{1}…{n}`.
- **CTA** gelap + **footer** 4 kolom dengan newsletter.
- **Scroll reveal** (IntersectionObserver) + easing Framer `cubic-bezier(.44,0,.56,1)`, hormati `prefers-reduced-motion`.

---

## 🚀 Cara pasang

1. **Upload** folder `reklamenesia/` ke `themes/` di instalasi CMS Anda:
   ```
   themes/
     default/
     reklamenesia/   ← ini
   ```
2. Masuk **Admin → Template Manager**. Tema akan terdeteksi otomatis sebagai *Reklamenesia*. Klik **Aktifkan**.
   *(Alternatif: set setting `active_theme` = `reklamenesia`.)*
3. Selesai. Buka beranda.

---

## ⚙️ Rekomendasi pengaturan (agar 100% seperti Framer)

Semua konten tetap **dinamis** dari database. Beberapa setting opsional:

| Lokasi admin | Setting | Nilai disarankan |
|---|---|---|
| Pengaturan → Tampilan | **Warna Aksen** | `#C21F1F` *(jika masih default amber, tema otomatis pakai merah)* |
| Pengaturan → Tampilan | Logo (light) | logo dengan teks gelap (untuk navbar solid) |
| Pengaturan → Tampilan | Logo Dark | logo dengan teks **putih** (untuk navbar di atas hero + footer) |
| Pengaturan → Hero | Judul / Subtitle / CTA | teks hero |

> Jika logo belum diupload, tema otomatis memakai logo bawaan di `assets/images/` (`logo-white.png` untuk latar gelap, `logo-dark-text.png` untuk latar terang).

### Konten yang mengisi tiap bagian
- **Hero** → Pengaturan → Hero.
- **Marquee gambar** → tabel **Galeri** (foto *featured* didahulukan).
- **Pill layanan** & **kartu Services** → tabel **Layanan** (gunakan field *gambar* untuk tiap layanan).
- **Statistik About** → blok konten `home`: `perf_stat1..4_value/_label`.
- **Testimoni / FAQ / Blog / Produk / Klien** → modul masing-masing seperti biasa.

Teks eyebrow & judul tiap section dapat diedit lewat *content blocks* (`get_content`) persis seperti tema default.

---

## 🎨 Kustomisasi warna

Tema memetakan warna admin ke variabel CSS:
- `accent_color` → `--rk-red`
- `dark_color` → `--rk-ink`
- `cream_color` → `--rk-cream`

Untuk penyesuaian lanjut, gunakan **Pengaturan → Custom CSS** (override variabel `:root` apa pun) — tidak perlu menyentuh file tema.

---

## 📁 Struktur

```
reklamenesia/
├── theme.json
├── screenshot.png
├── assets/
│   ├── css/style.css        # design system (tokens, komponen, responsif, motion)
│   ├── js/main.js           # reveal, navbar, marquee, carousel, accordion, mobile nav
│   └── images/              # logo-white.png, logo-dark-text.png, favicon.png
└── templates/
    ├── layouts/  header.php, footer.php
    ├── pages/    home, about, layanan, layanan-detail, gallery,
    │             blog, blog-detail, produk, produk-detail, contact, custom, 404
    └── partials/ navbar, mobile-nav, wa-float, testimonial-carousel,
                  logo-carousel, flex-content
```

## ✅ Kompatibilitas
- Nama file template & *data contract* identik dengan tema `default` → tidak perlu ubah router/DB.
- Mendukung: hero settings, flex_blocks, grid_icon_box, posisi flex (`home_*`, `about_*`, `layanan_*`, `layanan_detail_*`), tracking GA4/GTM/Meta Pixel, WA float + tracking klik, SEO per halaman, FAQ JSON-LD.
- PHP 8.0+, browser modern.

## 🔒 Catatan keamanan
File `core/config/config.php` pada paket CMS memuat kredensial database secara hardcoded. Sebaiknya **ganti password tersebut** dan jangan commit ke repositori publik. Tema ini tidak menyimpan kredensial apa pun.

---

## 🔄 Changelog v1.1.0 (revisi akurasi Framer)

1. **Dropdown menu** — area hover diperbesar + ada "jembatan" tak terlihat di antara tombol dan submenu, plus dukungan keyboard (`focus-within`) dan z-index lebih tinggi, sehingga tidak hilang saat kursor bergerak ke submenu.
2. **Hero** — teks (judul, subtitle, CTA) kini rata tengah; background diganti menjadi **merah solid `#C21F1F`** persis seperti Framer (sebelumnya gradasi maroon yang terlalu gelap).
3. **Animasi** — heading raksasa "OUR SERVICES" kini *sticky* (menempel) sementara kartu layanan menumpuk/scroll di atasnya; reveal & entrance lebih hidup.
4. **Footer** — spacing, tipografi, ukuran ikon, kolom (Email · Stay Connected · Alamat), dan hover (sosial → merah) disamakan dengan Framer.
5. **Running text layanan** — kini **dua baris berlawanan arah** (atas ← kiri, bawah → kanan), infinite & seamless.
6. **Statistik** — angka beranimasi menghitung dari `0 → target` saat masuk viewport (sekali jalan, easing halus, mendukung mobile). Mengikuti nilai dari admin (mis. `10k`, `700+`, `25+`).
7. **Detail layanan** — kartu varian, spacing, dan tipografi dirapikan; kartu gambar tidak lagi keliru "sticky".

> Aset memakai cache-busting `?v=1.1`. Jika setelah update masih terlihat versi lama, lakukan **hard refresh** (Ctrl/Cmd + Shift + R).

## 🔄 Changelog v1.2.0
1. **Running text layanan** — dikembalikan menjadi **1 baris**, **background putih**, pill putih ber-border tipis, dan bergeser **ke kanan** (sesuai Framer). Versi 2-baris gelap dihapus.
3. **Footer dibangun ulang total** agar identik dengan Framer: blok gelap rounded-top, **logo asterisk merah + "reklamenesia."**, deskripsi, email besar rata kanan, garis pemisah, **"Stay Connected!"** dengan input newsletter gaya garis-bawah + tombol "↳ Submit", **"Alamat:"** rata kanan, copyright, pola grid kotak halus, dan watermark raksasa **REKLAMENESIA**. (Catatan: footer memakai tanda asterisk + nama situs; jika ingin pakai gambar logo sendiri, upload di setting **Logo Dark**.)

> Cache-busting `?v=1.2`. Lakukan **hard refresh** (Ctrl/Cmd + Shift + R) setelah update.

## 🔄 Changelog v1.3.0
1. **Kartu layanan (homepage)** — dibuat **lebih kecil & rata tengah** (maks ~860px) seperti Framer, rasio lebih tinggi, sehingga watermark "OUR SERVICES" terlihat di kiri-kanan kartu.
2. **Navbar** — dipindah ke level paling atas (keluar dari section hero) agar **tidak lagi tertutup** section lain saat scroll; **z-index 1000** (paling atas), dan pada halaman dalam kini **selalu ada background** (putih + blur), tidak transparan lagi. Menu mobile dinaikkan di atas navbar.
3. **Detail layanan** — overlay gelap pada kartu gambar dibatasi hanya untuk kartu yang punya judul, jadi gambar hero/consult tampil bersih (memperbaiki kartu yang tampak hitam).
4. **Audit spasi** — padding/margin diseragamkan lewat token (`--pad-x`, `--sec-y`, skala tipografi fluid); padding atas halaman dalam memberi ruang aman untuk navbar fixed.

> Cache-busting `?v=1.3`. **Hard refresh** (Ctrl/Cmd + Shift + R) setelah update.

## 🔄 Changelog v1.4.0
1. **Detail layanan dibangun ulang** mengikuti Framer: **hero merah** (judul + subtitle rata tengah) dengan **kartu gambar bersih yang menumpuk** dari merah ke putih (memperbaiki gambar atas yang sebelumnya terlihat buruk); section varian kini berupa **baris horizontal** (teks kiri + gambar kanan) dalam kartu abu lembut — bukan grid kartu lagi. Navbar di halaman ini transparan putih di atas hero merah, lalu solid saat scroll.
2. **Audit spasi & polish** — ritme antar-section, padding, dan tipografi diseragamkan lewat token; ditambah pengaman **no-JS** (konten ber-animasi tidak akan "hilang" bila JS gagal dimuat) dan z-index yang rapi.
3. **Animasi kartu layanan di mobile** — efek **stacking** kini aktif juga di mobile (sebelumnya dimatikan), jadi tidak statis lagi.

> Cache-busting `?v=1.4`. **Hard refresh** setelah update.

## 🔄 Changelog v1.4.1 (perbaikan penting)
1. **Halaman /layanan (index) HANCUR — diperbaiki.** Penyebab: kartu grid layanan ter-set `position: static`, sehingga gambar di dalamnya (yang `position:absolute`) "lepas" jadi full-layar & teks bertumpuk. Kini kartu `position: relative` lagi → gambar kembali rapi di dalam kartu.
2. **CTA (semua halaman) margin atas/bawah seimbang.** Sebelumnya margin atas 0 dan bawah kecil (tidak balance). Kini margin vertikal simetris dan jarak section di atasnya tidak lagi dobel, sehingga kartu CTA punya ruang atas = bawah yang rapi.

> Cache-busting `?v=1.5`. **Hard refresh** setelah update.

## 🔄 Changelog v1.5.0
- **Footer "Stay Connected!"** — form email diganti dengan **ikon sosial media berwarna** (WhatsApp, Email, Instagram, Facebook, TikTok, YouTube, X) memakai warna brand masing-masing. Ikon otomatis muncul sesuai sosial media yang diisi di Pengaturan (sosial_instagram, sosial_facebook, dst) + WhatsApp & Email. Tidak ada lagi form email.

> Cache-busting `?v=1.6`. **Hard refresh** setelah update.

## 🔄 Changelog v1.5.1
1. **Marquee (galeri & pill layanan)** tidak lagi berhenti saat di-hover — slide jalan terus.
2. **Logo klien** diperbesar (tinggi maks 46→66px, area lebih lega) biar tidak kekecilan.
3. **Mobile — section "Complete Solutions…"**: watermark jadi label in-flow dengan jarak jelas, jadi heading tidak lagi mepet/ketabrak kartu di bawahnya.
4. **Stat box (4 kotak)**: jarak icon ↔ angka dilegakan (gap 30px), plus **reveal animasi bertahap** (tiap kartu muncul berurutan saat masuk layar).

> Cache-busting `?v=1.7`. **Hard refresh** setelah update.
