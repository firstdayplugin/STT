-- ============================================================
-- Home — swap placeholder media for client-provided images.
--   * Hero slider: 9 client images that already have text baked in,
--     so the DB text overlay (judul/subtitle) is cleared to avoid
--     duplicate/overlapping text on top of the image.
--   * Portfolio backgrounds (pf_img1..4) and Why cards (why1..4_img)
--     point to the client photos under uploads/home/.
-- Idempotent.
-- ============================================================
SET NAMES utf8mb4;

-- ---- Hero slider: 9 client images, NO overlay text (baked into image) ----
DELETE FROM hero_slides;
INSERT INTO hero_slides (id, judul, subtitle, gambar, video_url, cta_text, cta_url, urutan, is_active) VALUES
  (1, '', '', 'slides/hero-1.png', NULL, NULL, NULL, 1, 1),
  (2, '', '', 'slides/hero-2.png', NULL, NULL, NULL, 2, 1),
  (3, '', '', 'slides/hero-3.png', NULL, NULL, NULL, 3, 1),
  (4, '', '', 'slides/hero-4.png', NULL, NULL, NULL, 4, 1),
  (5, '', '', 'slides/hero-5.png', NULL, NULL, NULL, 5, 1),
  (6, '', '', 'slides/hero-6.png', NULL, NULL, NULL, 6, 1),
  (7, '', '', 'slides/hero-7.png', NULL, NULL, NULL, 7, 1),
  (8, '', '', 'slides/hero-8.png', NULL, NULL, NULL, 8, 1),
  (9, '', '', 'slides/hero-9.png', NULL, NULL, NULL, 9, 1);

-- ---- Portfolio section backgrounds (all languages) ----
UPDATE content_blocks SET konten='home/pf-1.png' WHERE page_key='home' AND block_key='pf_img1';
UPDATE content_blocks SET konten='home/pf-2.png' WHERE page_key='home' AND block_key='pf_img2';
UPDATE content_blocks SET konten='home/pf-3.png' WHERE page_key='home' AND block_key='pf_img3';
UPDATE content_blocks SET konten='home/pf-4.png' WHERE page_key='home' AND block_key='pf_img4';

-- ---- Why Us cards (all languages) ----
UPDATE content_blocks SET konten='home/why-1.png' WHERE page_key='home' AND block_key='why1_img';
UPDATE content_blocks SET konten='home/why-2.png' WHERE page_key='home' AND block_key='why2_img';
UPDATE content_blocks SET konten='home/why-3.png' WHERE page_key='home' AND block_key='why3_img';
UPDATE content_blocks SET konten='home/why-4.png' WHERE page_key='home' AND block_key='why4_img';
