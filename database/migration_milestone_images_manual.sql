-- ============================================================================
-- Point the About-Us "Milestone" cards at manually-uploaded images.
-- ----------------------------------------------------------------------------
-- Use this when the CMS image upload is not replacing the photo. Steps:
--   1) In hPanel File Manager, open the site's uploads folder:  <site-root>/uploads/about/
--      (same level as index.php; on Hostinger usually public_html/uploads/about/)
--   2) Upload the client's 6 photos there, renamed EXACTLY like below:
--        milestone-2019.png  milestone-2022.png  milestone-2023.png
--        milestone-2024.png  milestone-2025.png  milestone-2026.png
--      (If your files are .jpg/.webp, keep that extension and change it below too.)
--   3) Run this SQL in phpMyAdmin (SQL tab). Done — no CMS needed.
--
-- Matches by seksi='milestone' + tahun, so it only touches the right cards.
-- Re-runnable. Clean filenames also dodge the browser-cache "won't change" issue.
-- ============================================================================
SET NAMES utf8mb4;

UPDATE about_items SET gambar='about/milestone-2019.png' WHERE seksi='milestone' AND tahun='2019';
UPDATE about_items SET gambar='about/milestone-2022.png' WHERE seksi='milestone' AND tahun='2022';
UPDATE about_items SET gambar='about/milestone-2023.png' WHERE seksi='milestone' AND tahun='2023';
UPDATE about_items SET gambar='about/milestone-2024.png' WHERE seksi='milestone' AND tahun='2024';
UPDATE about_items SET gambar='about/milestone-2025.png' WHERE seksi='milestone' AND tahun='2025';
UPDATE about_items SET gambar='about/milestone-2026.png' WHERE seksi='milestone' AND tahun='2026';

-- Check the result:
-- SELECT tahun, gambar FROM about_items WHERE seksi='milestone' ORDER BY urutan, id;
