-- ============================================================================
-- Services LANDING (/services) — selaraskan hero copy + node lifecycle ke mockup.
-- Section baru (stats, trusted-by, what-we-deliver, two-areas, bundle, CTA) tampil
-- dari default template (EN) dan tetap bisa di-override per bahasa lewat CMS nanti.
-- Idempotent; hanya menyentuh baris service_pages slug='services'.
-- ============================================================================
SET NAMES utf8mb4;

UPDATE `service_pages` SET
  `eyebrow`   = 'Enterprise Solution Provider',
  `headline`  = 'One Partner. Your [Entire Technology Lifecycle]',
  `extra2`    = 'Assess → Design → Build → Secure → Run → Optimize',
  `cta_label` = 'Request Proposal'
WHERE `slug` = 'services';

-- Buang override hero_lead lama (jika ada) agar teks hero baru dari template tampil.
DELETE FROM `content_blocks` WHERE `page_key`='services' AND `block_key`='hero_lead';
