# Inventory Halaman — Figma "dummy Web Sapta Tunas Alternatif 3"

Sumber: Google Drive folder `1E3PrfBVAINaqNwWdvRoCXbwDkvOTlJbO` (± 100 PNG).
Cara akses (terbukti): konektor Google Drive → `download_file_content` (tersimpan ke file, tak masuk
konteks) → decode base64 → PNG → dibaca visual. Halaman diambil per kebutuhan.

**Arti akhiran nama file (state, bukan halaman berbeda):**
- *(tanpa akhiran)* = halaman bersih.
- `_ Service` = mega-menu **Service** terbuka di header.
- `_ Industry` = mega-menu **Industry** terbuka di header.
- `SatuAI` = panel chatbot AI **SatuAI** terbuka (fitur — ditunda, §ProjectContext.SatuAI).
- `SatuAI Sidebar` = panel SatuAI + sidebar riwayatnya.
- File `Ellipse/Line/image N/Nutanix_logo` = potongan aset, bukan halaman.

## Navigasi global (header)
`Logo · About Us · Solutions · Service ▾ · Industry ▾ · What's New · Career · ✦ Ask SatuAI ·
[Contact Us] · ID/EN`

- **Service ▾:** Cybersecurity Services · Cybersecurity Testing & Assessment · Security Risk &
  Compliance · Managed Cybersecurity · Maintenance Service Options · Managed SOC as a Service.
- **Industry ▾:** Financial Services & E-Commerce · Manufacture & FMCG · Healthcare · Law Enforcement ·
  Energy · Telecommunication (ICT) · Cross Industry · All Industries.

## Footer (konsisten semua halaman)
- **Solutions:** Modernize Infrastructure · AI · Data · CyberSecurity · Output UI (Apps)
- **Company:** Company Overview · Grow With Us · News & Events · Why Partner With Us
- **Help & Support:** AI Assistant · Talk to Support · Find Us · Customer Reviews
- **Consult With Us:** input email subscribe · Social: LinkedIn, Instagram, Facebook, YouTube
- © 2026 Sapta Tunas Teknologi · Privacy Policy · Compliance Policy · "Enterprise Solution Provider"

## Katalog halaman & pemetaan ke rute CMS

| # | Halaman Figma | Rute CMS | Template | Status |
|---|---|---|---|---|
| 1 | **Home** | `/` | `home.php` | ✅ ada (= master `design/master/home.html`) |
| 2 | **About Us** | `/tentang-kami` | `about.php` | ✅ ada, konten jauh lebih kaya (lihat model konten) |
| 3 | **Solutions** (landing) | *(baru)* `/solutions` | — | ⚠️ **GAP** rute |
| 4 | **Service** (6 detail) | `/layanan/[slug]` | `layanan-detail.php` | ✅ ada, perlu tabel tier |
| 5 | **Industries** (landing) | *(baru)* `/industri` | — | ⚠️ **GAP** modul |
| 6 | **Industry × pillar** (detail bertab) | *(baru)* | — | ⚠️ **GAP** besar (matriks) |
| 7 | **What's New** (blog list) | `/blog` | `blog.php` | ✅ ada, perlu arsip tahun/kategori/search/sidebar |
| 8 | **Detail News** | `/blog/[slug]` | `blog-detail.php` | ✅ ada |
| 9 | **Career** (list + filter) | *(baru)* `/career` | — | ⚠️ **GAP** modul (lowongan) |
| 10 | **Detail Career** + form lamaran | *(baru)* `/career/[slug]` | — | ⚠️ **GAP** + submissions (upload CV) |
| 11 | **Contact Us** | `/hubungi-kami` | `contact.php` | ✅ ada, perlu info kontak + map |
| 12 | **Privacy Policy** | `/privacy-policy` | `custom.php` (pages) | ✅ via custom page |
| 13 | **Compliance Policy** | `/compliance-policy` | `custom.php` (pages) | ✅ via custom page |
| — | Gallery | `/gallery` | `gallery.php` | ada di CMS, **tidak ada** di Figma Sapta (simpan utk white-label) |
| — | Produk | `/produk` | `produk.php` | ada di CMS, **tidak ada** di Figma Sapta (simpan utk white-label) |

## Model konten per halaman (untuk editable & data-driven)

- **Home:** = master (hero slider, portfolio 425+/200+/100%/26, Our Solutions [orbit/cube WebGL],
  Our Industries [8: Financial, Education, Healthcare, Law Enforce, Manufacture, Telecom, Energy,
  Cross Industry], News, Why Us [4], Testimonials, Request Proposal). Logo partner (Solutions):
  Dell, AMD, Intel, VMware, Nutanix, Sangfor, RedHat, Microsoft, Commvault, HYCU, Veeam, Infraon
  → cocok modul **klien-logo** CMS.
- **About Us:** Vision, Mission (bullets), **Value = I.C.A.R.E** (Integrity, Collaborate,
  Accountability, Responsive, Excellence), **Milestone** (timeline tahun, carousel),
  **Awards** (carousel per tahun, kartu sertifikat), **Quality Standards** (ISO 9001/14001/45001,
  ISO 37001, ISO 27001), **List of Certification** (carousel Dell dll). → butuh model repeatable:
  values, milestones, awards, certifications.
- **Solutions:** cerita scroll WebGL 5 tahap: **Modernize Infrastructure → Cybersecurity → Data →
  AI → AI Platform Application** + baris logo partner. (4 pilar solusi = Modernize Infrastructure,
  Data & AI, Cybersecurity, Managed Services — muncul lagi sebagai tab di halaman Industry.)
- **Service detail:** intro + hero image + **tabel perbandingan tier** (mis. Managed SOC:
  Diamond/Platinum/Gold, baris: Security Monitoring SLA, Log Coverage, Response MTTA, Incident
  Handling, Log Retention, Reporting, Threat Hunting, Infosec Advisory) + tabel **Advanced Services**
  (Vulnerability Assessment, Digital Forensic, Penetration Testing, Cyber Drill). → butuh model
  **feature/tier matrix**.
- **Industry detail (mis. Healthcare):** judul industri + intro + hero image + **tab 4 pilar**
  (Modernize Infrastructure / Data & AI / Cybersecurity / Managed Services), tiap tab: heading +
  paragraf kaya + 4 ikon fitur. → matriks **Industry × Pillar** (konten per sel).
- **What's New (blog):** featured hero article, **search by keywords**, **filter kategori**
  (All, Awards, Event, Articles & News, Program Promo), grid kartu (image, tanggal, kategori, judul,
  excerpt, Read More), sidebar **New Information** (recent) + **Publishing Year** (arsip tahun→bulan
  dengan hitungan, mis. 2026 (24), Januari (16)…). → sejalan rencana blog WordPress-like (§14.4).
- **Detail News:** artikel (judul, tanggal, kategori, konten, gambar).
- **Career (list):** hero "Build the Future with Us", foto tim, **search**, filter **Job Role**
  (Software Engineering/Developer, Cyber Security & IT Infrastructure, Customer Service & Support,
  HR, Legal & Compliance) + **Location** (Jakarta, Bandung, Surabaya, Bali, dgn hitungan), kartu
  lowongan (judul, jenjang "Bachelor/S1", "Experienced", deadline "until Jun 28, 2026", See more).
- **Detail Career:** detail lowongan (**Responsibilities**, **Requirements**) + **Form Application**
  (Full Name, Email, Phone, Subject, Cover Letter, **Upload CV max 1MB**, Submit). → modul lowongan
  + **submissions lamaran** (simpan + notifikasi + anti-spam, upload file aman).
- **Contact Us:** form (Name, Email, Phone, Message) + info: alamat (Komplek Perkantoran Agung
  Sedayu Blok H No.28-30, Jl. Arteri Mangga Dua Raya, Jakarta Pusat 10730), Phone +62 21-5028 1717,
  ProSupport 7x24 021-2410 1568, WhatsApp +62 821-1000-1087, Email marketing@saptatunas.com,
  ProSupport email prosupport@saptatunas.com, sosial, **Google Map embed**. → cocok settings kontak
  + WA CMS (multi-kontak).

## GAP utama (modul/rute baru yang perlu dibuat)
1. **Industries** — modul baru: landing + halaman per-industri dengan **tab 4 pilar solusi**
   (matriks Industry × Pillar). Modul terbesar; belum ada di CMS.
2. **Solutions** — rute/landing + 4 pilar solusi (dipakai ulang di tab Industry).
3. **Career/Jobs** — modul lowongan (role, lokasi, jenjang, pengalaman, deadline, responsibilities,
   requirements) + filter + **submissions lamaran** (upload CV) + notifikasi.
4. **Service tier tables** — model perbandingan fitur bertingkat (Diamond/Platinum/Gold) di
   `layanan-detail`.
5. **About Us rich** — values (ICARE), milestones timeline, awards, certifications (repeatable).
6. **Blog enhancement** — arsip tahun/bulan berhitungan, tab kategori, search, sidebar recent,
   featured article, kategori "Program Promo" (sejalan §14.4).
7. **Ask SatuAI** — asisten AI (ditunda; slot UI disiapkan).

## Catatan penamaan (untuk white-label)
CMS memakai `layanan` (services) & `produk` (products); Figma memakai **Solutions / Service /
Industries**. Perlu strategi pemetaan/label yang bisa diganti per klien (modul generik CMS
diberi label sesuai klien). Modul `gallery`/`produk` tak dipakai Sapta tapi dipertahankan untuk
klien lain.

## Keputusan (terkonfirmasi user)
- **Industries = 8 (dengan Education):** Financial Services & E-Commerce, Education, Healthcare,
  Law Enforcement, Manufacture & FMCG, Telecommunication (ICT), Energy, Cross Industry. Home &
  halaman Industries diselaraskan jadi 8 (halaman Industries di Figma yang menampilkan 7 = kurang Education).
- **Model Industry × Pilar = konten per sel (editable penuh):** tiap kombinasi Industri × Pilar
  (8 × 4 = hingga 32 sel; pilar: Modernize Infrastructure, Data & AI, Cybersecurity, Managed Services)
  punya konten sendiri yang bisa diedit. Data model: entity `industries`, `solution_pillars`, dan
  `industry_pillar_content` (industry_id, pillar_id, heading, body, feature icons) + landing per industri.
- **Career = modul penuh:** lowongan (role, lokasi, jenjang, pengalaman, deadline, responsibilities,
  requirements) + filter (Job Role, Location) + **form lamaran** (Full Name, Email, Phone, Subject,
  Cover Letter, **Upload CV maks 1MB**) → `job_applications` (simpan + notifikasi + anti-spam/Turnstile +
  upload file aman, non-executable, validasi MIME). Masuk rilis pertama.

## Pertanyaan terbuka (sisa)
- **Cybersecurity Services 1/2/3:** apakah 3 halaman terpisah, atau 1 halaman multi-section? (belum final)
- **Gallery & Produk:** dimatikan untuk Sapta, tetap ada untuk white-label (asumsi: ya).
- **Solutions vs Service:** "Solutions" = landing 4 pilar; "Service" = daftar layanan cybersecurity
  spesifik (indikasi kuat: ya) — konfirmasi final saat mulai bangun.

---
_Diambil dari Figma via Google Drive. Referensi visual bisa di-pull ulang kapan saja dari Drive._
