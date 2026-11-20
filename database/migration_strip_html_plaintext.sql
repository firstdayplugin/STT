-- ============================================================================
-- Clean stray HTML from PLAIN-TEXT CMS fields
-- ----------------------------------------------------------------------------
-- Some rows were imported/seeded with a <p>...</p> wrapper (or other tags) in
-- fields that are shown as PLAIN TEXT, so the tags rendered as literal text
-- (e.g. "<p>Build a wide...</p>"). The templates now strip tags on output, but
-- this also cleans the stored values so the admin editor shows clean text too.
--
-- Only PLAIN-TEXT columns are touched. Rich-HTML columns (blog.konten,
-- *_pilar.konten, service konten, etc.) are intentionally left as-is.
-- Idempotent: only rows that still contain a tag are updated.
-- Run in phpMyAdmin (SQL tab) on the live database.
-- ============================================================================

-- About Us repeaters: mission points, ICARE values, award names/givers, etc.
UPDATE about_items SET teks  = TRIM(REGEXP_REPLACE(teks,  '<[^>]+>', '')) WHERE teks  REGEXP '<[^>]+>';
UPDATE about_items SET judul = TRIM(REGEXP_REPLACE(judul, '<[^>]+>', '')) WHERE judul REGEXP '<[^>]+>';

-- Testimonial quote (shown as plain text on home + detail)
UPDATE testimonial SET isi = TRIM(REGEXP_REPLACE(isi, '<[^>]+>', '')) WHERE isi REGEXP '<[^>]+>';
