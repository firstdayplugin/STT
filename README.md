# STT — Sapta Tunas Teknologi

Website **full CMS** untuk Sapta Tunas Teknologi (enterprise IT solution provider, Indonesia),
dibangun di atas **Reklamepedia CMS** (PHP Native + MySQL) buatan sendiri.

Rencana: membuat **theme baru bernama "Anima"** berdasarkan master desain Home, lalu
merapikan CMS dan memperbaiki bug-nya.

## Struktur repo
- **CMS (Reklamepedia CMS)** di root: `index.php`, `admin/`, `core/`, `themes/`, `assets/`, `uploads/`, `database/`, `install.php`.
- [`design/master/home.html`](design/master/home.html) — master desain Home (acuan untuk theme "Anima", jangan diubah).
- [`docs/PROJECT_CONTEXT.md`](docs/PROJECT_CONTEXT.md) — **source of truth** proyek (desain, arsitektur CMS, rencana theme Anima, daftar bug).
- [`docs/CMS_README.md`](docs/CMS_README.md) — README asli Reklamepedia CMS (fitur, instalasi, modul).

## Status
CMS dan master desain sudah tersimpan. **Belum ada perbaikan/refactor** — implementasi
theme "Anima" dan perbaikan bug menunggu instruksi.

> ⚠️ `core/config/config.php` berisi kredensial DB asli (disimpan apa adanya atas keputusan
> pemilik repo). Rotasi/bersihkan bila repo dibagikan.

Baca `docs/PROJECT_CONTEXT.md` sebelum mulai bekerja.
