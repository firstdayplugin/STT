-- ============================================================
-- The site's content is authored in ENGLISH (theme registry defaults),
-- so English must be the base/default language. The installer seeded 'id'
-- which made: (a) first load report 'id', and (b) Google Translate declare
-- the page as Indonesian (pageLanguage='id') — breaking the EN/ID flags.
-- Fix: default language = English.
-- ============================================================
UPDATE settings SET setting_value = 'en' WHERE setting_key = 'default_lang';
