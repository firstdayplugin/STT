-- ============================================================
-- Clean "blue placeholder" look: drop the low-res demo photos so the
-- template's blue placeholders show. Images stay fully CMS-editable —
-- uploading a real photo replaces the placeholder again.
-- Also tops up Quality Standards so its slider is navigable.
-- Idempotent + non-destructive.
-- ============================================================
SET NAMES utf8mb4;

-- About: clear milestone / award / quality / cert photos.
UPDATE about_items SET gambar = NULL WHERE seksi IN ('milestone','award','quality','cert');

-- About: clear the two Vision illustrations (content blocks).
UPDATE content_blocks SET konten = '' WHERE page_key='about' AND block_key IN ('vision_img1','vision_img2');

-- Blog: clear featured images so the grid shows clean blue placeholders.
UPDATE blog SET gambar_utama = NULL;

-- Quality Standards: add placeholder cards so the carousel can scroll left/right.
INSERT INTO about_items (seksi,judul,gambar,urutan,is_active)
  SELECT 'quality','ISO 20000-1',NULL,4,1 FROM DUAL
  WHERE NOT EXISTS (SELECT 1 FROM about_items WHERE seksi='quality' AND judul='ISO 20000-1');
INSERT INTO about_items (seksi,judul,gambar,urutan,is_active)
  SELECT 'quality','ISO 22301',NULL,5,1 FROM DUAL
  WHERE NOT EXISTS (SELECT 1 FROM about_items WHERE seksi='quality' AND judul='ISO 22301');
INSERT INTO about_items (seksi,judul,gambar,urutan,is_active)
  SELECT 'quality','ISO 20000',NULL,6,1 FROM DUAL
  WHERE NOT EXISTS (SELECT 1 FROM about_items WHERE seksi='quality' AND judul='ISO 20000');
INSERT INTO about_items (seksi,judul,gambar,urutan,is_active)
  SELECT 'quality','PCI DSS',NULL,7,1 FROM DUAL
  WHERE NOT EXISTS (SELECT 1 FROM about_items WHERE seksi='quality' AND judul='PCI DSS');

-- Awards: add a couple more placeholder cards so year pages feel fuller.
INSERT INTO about_items (seksi,judul,teks,tahun,gambar,urutan,is_active)
  SELECT 'award','Client Recognition','Best Technology Partner 2023','2023',NULL,7,1 FROM DUAL
  WHERE NOT EXISTS (SELECT 1 FROM about_items WHERE seksi='award' AND judul='Client Recognition');
INSERT INTO about_items (seksi,judul,teks,tahun,gambar,urutan,is_active)
  SELECT 'award','Vendor Excellence','Outstanding Delivery 2025','2025',NULL,8,1 FROM DUAL
  WHERE NOT EXISTS (SELECT 1 FROM about_items WHERE seksi='award' AND judul='Vendor Excellence');
