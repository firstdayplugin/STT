-- ============================================================
-- About Us — fill Figma images into the DB (no hardcoding).
-- Images live under uploads/about/. Idempotent: safe to re-run.
-- ============================================================
SET NAMES utf8mb4;

-- ---- Vision illustrations (content_blocks; default-lang row is enough) ----
INSERT INTO content_blocks (page_key, block_key, lang, block_label, block_type, konten, is_active)
VALUES ('about','vision_img1','id','Gambar kiri — atas','image','about/vision-1.png',1)
ON DUPLICATE KEY UPDATE konten = VALUES(konten), block_type = 'image', is_active = 1;
INSERT INTO content_blocks (page_key, block_key, lang, block_label, block_type, konten, is_active)
VALUES ('about','vision_img2','id','Gambar kiri — bawah','image','about/vision-2.png',1)
ON DUPLICATE KEY UPDATE konten = VALUES(konten), block_type = 'image', is_active = 1;

-- ---- Milestone illustration (same render for every era; editable per-item in CMS) ----
UPDATE about_items SET gambar = 'about/milestone.png'
 WHERE seksi = 'milestone' AND (gambar IS NULL OR gambar = '');

-- ---- Awards: one certificate/trophy image per item (matched by order) ----
UPDATE about_items SET gambar='about/award-1.png' WHERE seksi='award' AND urutan=1 AND (gambar IS NULL OR gambar='');
UPDATE about_items SET gambar='about/award-2.png' WHERE seksi='award' AND urutan=2 AND (gambar IS NULL OR gambar='');
UPDATE about_items SET gambar='about/award-3.png' WHERE seksi='award' AND urutan=3 AND (gambar IS NULL OR gambar='');
UPDATE about_items SET gambar='about/award-4.png' WHERE seksi='award' AND urutan=4 AND (gambar IS NULL OR gambar='');
UPDATE about_items SET gambar='about/award-5.png' WHERE seksi='award' AND urutan=5 AND (gambar IS NULL OR gambar='');
UPDATE about_items SET gambar='about/award-6.png' WHERE seksi='award' AND urutan=6 AND (gambar IS NULL OR gambar='');

-- ---- Quality Standards: Figma groups these into 3 cards (matched by ISO code) ----
UPDATE about_items SET judul='ISO 9001 • ISO 14001 • ISO 45001', gambar='about/quality-1.png'
 WHERE seksi='quality' AND judul='ISO 9001';
DELETE FROM about_items WHERE seksi='quality' AND judul IN ('ISO 14001','ISO 45001');
UPDATE about_items SET gambar='about/quality-2.png' WHERE seksi='quality' AND judul='ISO 37001';
UPDATE about_items SET gambar='about/quality-3.png' WHERE seksi='quality' AND judul='ISO 27001';

-- ---- Certifications: Figma shows the DELL brand tab with 6 product badges. ----
-- Repurpose the vendor placeholder rows into the six Dell product certifications
-- (all grouped under the "DELL" brand tab), matched by their original vendor name.
UPDATE about_items SET judul='PowerStore Deploy',              teks='Proven Professional · 2023', grup='DELL', gambar='about/cert-1.png' WHERE seksi='cert' AND judul='Dell Technologies';
UPDATE about_items SET judul='PowerScale Deploy',             teks='Proven Professional · 2023', grup='DELL', gambar='about/cert-2.png' WHERE seksi='cert' AND judul='VMware';
UPDATE about_items SET judul='ECS Deploy',                    teks='Proven Professional · 2023', grup='DELL', gambar='about/cert-3.png' WHERE seksi='cert' AND judul='Microsoft';
UPDATE about_items SET judul='PowerProtect Data Domain Deploy',teks='Proven Professional · 2023', grup='DELL', gambar='about/cert-4.png' WHERE seksi='cert' AND judul='Nutanix';
UPDATE about_items SET judul='PowerProtect Cyber Recovery',   teks='Proven Professional · 2023', grup='DELL', gambar='about/cert-5.png' WHERE seksi='cert' AND judul='Red Hat';
UPDATE about_items SET judul='PowerProtect Data Manager Deploy',teks='Proven Professional · 2023', grup='DELL', gambar='about/cert-6.png' WHERE seksi='cert' AND judul='Veeam';
