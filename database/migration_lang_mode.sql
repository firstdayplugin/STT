-- ============================================================
-- Language mode toggle (Admin → Pengaturan → Tampilan):
--   'manual'     = manual EN/ID switcher (CMS translations) — DEFAULT
--   'gtranslate' = hide manual switcher, show Google Translate EN/ID flags
-- Safe to skip: get_setting() falls back to 'manual' if the row is absent.
-- ============================================================
INSERT INTO settings (setting_key, setting_value)
VALUES ('lang_mode', 'manual')
ON DUPLICATE KEY UPDATE setting_value = setting_value;
