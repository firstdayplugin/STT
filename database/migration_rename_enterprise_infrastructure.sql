-- ============================================================================
-- Global rename: "Modernize Infrastructure"  ->  "Enterprise Infrastructure"
-- ----------------------------------------------------------------------------
-- Replaces ONLY the exact label phrase (and its "<b>" variant) in the database.
-- REPLACE() is case-sensitive, so lowercase prose like "modernize their technology
-- infrastructure" and "Data Center Modernization" are deliberately left untouched.
-- Idempotent (safe to run twice) and reversible (swap the two strings back).
--
-- IMPORTANT — run this together with the code changes. The section's URL anchor
-- changes from #modernize-infrastructure to #enterprise-infrastructure (derived
-- from the title), so the Home prism slide URL is updated here too; skipping this
-- migration would leave the Home prism infrastructure logos unmatched.
-- Run in phpMyAdmin (SQL tab).
-- ============================================================================
SET NAMES utf8mb4;

-- 1) Solutions section title (base value) -----------------------------------
UPDATE solutions_section
   SET judul = REPLACE(judul, 'Modernize <b>Infrastructure</b>', 'Enterprise <b>Infrastructure</b>')
 WHERE judul LIKE '%Modernize <b>Infrastructure</b>%';
UPDATE solutions_section
   SET judul = REPLACE(judul, 'Modernize Infrastructure', 'Enterprise Infrastructure')
 WHERE judul LIKE '%Modernize Infrastructure%';

-- 2) Home prism slide: title, eyebrow AND the #anchor in its URL -------------
--    (the anchor must track the new section anchor so Home<->Solutions stay linked)
UPDATE solution_slides
   SET judul   = REPLACE(judul,   'Modernize <b>Infrastructure</b>', 'Enterprise <b>Infrastructure</b>'),
       eyebrow = REPLACE(eyebrow, 'Modernize Infrastructure',         'Enterprise Infrastructure'),
       url     = REPLACE(url,     '#modernize-infrastructure',        '#enterprise-infrastructure')
 WHERE judul   LIKE '%Modernize%Infrastructure%'
    OR eyebrow LIKE '%Modernize Infrastructure%'
    OR url     LIKE '%modernize-infrastructure%';

-- 3) Per-language overrides (EN/ID) of those row fields ----------------------
UPDATE content_i18n
   SET nilai = REPLACE(REPLACE(nilai,
               'Modernize <b>Infrastructure</b>', 'Enterprise <b>Infrastructure</b>'),
               'Modernize Infrastructure',        'Enterprise Infrastructure')
 WHERE tabel IN ('solutions_section', 'solution_slides')
   AND (nilai LIKE '%Modernize Infrastructure%' OR nilai LIKE '%Modernize <b>Infrastructure</b>%');

-- 4) Editable content blocks (About intro/milestone, etc.) that name the label
UPDATE content_blocks
   SET konten = REPLACE(REPLACE(konten,
                'Modernize <b>Infrastructure</b>', 'Enterprise <b>Infrastructure</b>'),
                'Modernize Infrastructure',        'Enterprise Infrastructure')
 WHERE konten LIKE '%Modernize Infrastructure%' OR konten LIKE '%Modernize <b>Infrastructure</b>%';

-- Verify afterwards:
-- SELECT id, judul FROM solutions_section WHERE judul LIKE '%nfrastructure%';
-- SELECT id, eyebrow, url FROM solution_slides WHERE url LIKE '%infrastructure%';
-- SELECT tabel,row_id,field FROM content_i18n WHERE nilai LIKE '%Modernize%Infrastructure%';  -- should be empty
