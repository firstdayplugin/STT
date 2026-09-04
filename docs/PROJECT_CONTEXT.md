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

---
_Master desain: `design/master/home.html`. Dokumen ini diperbarui seiring keputusan proyek._
