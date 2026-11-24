-- ============================================================
-- Migration: Contact/About slugs + office map location (CMS, not hardcoded)
-- - Renames the nav menu links to /contact-us and /about-us
-- - Sets the Google Maps embed for the office (Settings → General → site_maps_embed)
-- Safe to run repeatedly.
-- ============================================================

-- Nav menu slugs
UPDATE `menus` SET `url`='/contact-us' WHERE `url` IN ('/hubungi-kami','hubungi-kami');
UPDATE `menus` SET `url`='/about-us'   WHERE `url` IN ('/tentang-kami','tentang-kami');

-- Office location map (inserted into the CMS setting, editable in Admin → Pengaturan)
INSERT INTO `settings` (`setting_key`,`setting_value`,`setting_group`) VALUES
('site_maps_embed','https://www.google.com/maps?q=Sapta+Tunas+Teknologi+PT,+Blok+H+No.28-30,+Jl.+Arteri+Mangga+Dua+Raya,+Jakarta+Pusat+10730&z=16&output=embed','general')
ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);

-- hero CTA default (if still pointing at the old slug)
UPDATE `settings` SET `setting_value`='/contact-us' WHERE `setting_key`='hero_cta_url' AND `setting_value` IN ('/hubungi-kami','hubungi-kami');
