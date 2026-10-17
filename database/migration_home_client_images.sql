-- ============================================================
-- Home — swap placeholder media for client-provided images.
--   * Portfolio backgrounds (pf_img1..4) and Why cards (why1..4_img)
--     point to the client photos under uploads/home/.
--   NOTE: the Hero slider is handled by migration_hero_v2.sql (plain
--   images + CMS text overlay) — run that one for the hero, not here.
-- Idempotent.
-- ============================================================
SET NAMES utf8mb4;

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
