-- ============================================================
-- Testimonial detail pages + CMS video/text control
-- Idempotent: safe to run multiple times on an existing DB.
-- ============================================================
SET NAMES utf8mb4;

-- New columns (MariaDB supports IF NOT EXISTS on ADD COLUMN).
ALTER TABLE testimonial ADD COLUMN IF NOT EXISTS slug VARCHAR(160) NULL AFTER nama;
ALTER TABLE testimonial ADD COLUMN IF NOT EXISTS detail TEXT NULL AFTER isi;
ALTER TABLE testimonial ADD COLUMN IF NOT EXISTS video_poster VARCHAR(255) NULL AFTER video_url;

-- Backfill slugs where missing (basic lowercase/hyphenate of the name;
-- the admin regenerates a clean unique slug on the next save).
UPDATE testimonial
   SET slug = TRIM(BOTH '-' FROM
              REGEXP_REPLACE(
                REGEXP_REPLACE(LOWER(nama), '[^a-z0-9]+', '-'),
              '-+', '-'))
 WHERE slug IS NULL OR slug = '';

-- Ensure detail always has content for the detail page (fallback to the short quote).
UPDATE testimonial
   SET detail = CONCAT('<p>', REPLACE(TRIM(isi), '\n', '</p><p>'), '</p>')
 WHERE (detail IS NULL OR detail = '') AND TRIM(isi) <> '';

-- Rich demo stories for the seeded testimonials (only where the client has
-- not written their own story yet, so this never overwrites edits).
UPDATE testimonial SET detail='<p>Sebagai penyedia layanan klinik kecantikan dengan jaringan cabang nasional, ketersediaan sistem IT adalah hal yang tidak bisa ditawar. Sapta Tunas Teknologi mendampingi kami mengelola infrastruktur end-to-end, mulai dari monitoring proaktif, preventive maintenance, hingga dukungan teknis yang responsif.</p><p>Yang paling kami hargai adalah keterbukaan tim STT terhadap masukan. Setiap kebutuhan kami didengar, dianalisa, lalu diterjemahkan menjadi solusi yang benar-benar relevan dengan operasional bisnis. Kolaborasi ini membuat tim internal kami bisa fokus pada layanan pasien, bukan memadamkan masalah IT.</p>'
 WHERE nama='Yonathan Moniaga' AND (detail IS NULL OR detail='' OR detail LIKE CONCAT('<p>', TRIM(isi), '</p>'));
UPDATE testimonial SET detail='<p>Migrasi sistem transaksi di industri finansial punya toleransi risiko yang sangat rendah. Kami membutuhkan partner yang tidak hanya paham teknologi, tetapi juga memahami kebutuhan compliance dan keamanan data.</p><p>STT merancang skenario migrasi yang matang, melakukan pengujian menyeluruh, dan mengeksekusi cut-over tanpa mengganggu layanan nasabah. Prosesnya berjalan mulus dan aman, persis sesuai roadmap yang kami sepakati di awal.</p>'
 WHERE nama='IT Director' AND (detail IS NULL OR detail='' OR detail LIKE CONCAT('<p>', TRIM(isi), '</p>'));
UPDATE testimonial SET detail='<p>Di lini manufaktur, setiap menit downtime berdampak langsung pada output produksi. STT membantu kami mengotomatisasi proses supply chain sehingga alur informasi dari gudang hingga lini produksi menjadi jauh lebih cepat dan akurat.</p><p>Mereka benar-benar memahami konteks operasional pabrik, bukan sekadar memasang teknologi, tetapi menyesuaikannya dengan cara kerja tim di lapangan.</p>'
 WHERE nama='Head of Operations' AND (detail IS NULL OR detail='' OR detail LIKE CONCAT('<p>', TRIM(isi), '</p>'));
UPDATE testimonial SET detail='<p>Sebagai platform e-commerce, lonjakan trafik saat campaign besar adalah ujian sesungguhnya. Bersama STT kami membangun arsitektur yang scalable sehingga platform tetap stabil meski beban melonjak berkali-kali lipat.</p><p>Skalabilitas ini memberi kami ketenangan untuk tumbuh tanpa khawatir infrastruktur menjadi penghambat.</p>'
 WHERE nama='Chief Technology Officer' AND (detail IS NULL OR detail='' OR detail LIKE CONCAT('<p>', TRIM(isi), '</p>'));
UPDATE testimonial SET detail='<p>Transformasi digital kami mencakup cloud, data, dan AI sekaligus. STT mengawal implementasi ini secara bertahap dengan eksekusi yang rapi dan komunikasi yang transparan di setiap milestone.</p><p>Hasilnya, adopsi teknologi baru berjalan lancar dan tim kami merasa didampingi, bukan ditinggalkan setelah proyek selesai.</p>'
 WHERE nama='VP Technology' AND (detail IS NULL OR detail='' OR detail LIKE CONCAT('<p>', TRIM(isi), '</p>'));
UPDATE testimonial SET detail='<p>Operasional rumah sakit berjalan 24 jam, dan begitu pula kebutuhan dukungan IT-nya. Layanan managed IT 24/7 dari STT membuat kami tenang karena setiap kendala ditangani dengan cepat, kapan pun terjadi.</p><p>Dukungan yang konsisten ini berdampak langsung pada kelancaran pelayanan kepada pasien.</p>'
 WHERE nama='IT Manager' AND (detail IS NULL OR detail='' OR detail LIKE CONCAT('<p>', TRIM(isi), '</p>'));
