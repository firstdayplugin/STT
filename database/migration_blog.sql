-- ============================================================
-- Migration: What's New (blog) — featured images + category tabs
-- Adds the article featured images (shipped in /uploads/blog/) WITHOUT
-- touching your existing article text, and trims the category tabs to the
-- Figma set (Awards, Event, Articles & News, Program Promo).
-- ============================================================
UPDATE `blog` SET `gambar_utama`='blog/ai-assistant.png' WHERE `slug`='ai-assistant-troubleshooting' AND (`gambar_utama` IS NULL OR `gambar_utama`='');
UPDATE `blog` SET `gambar_utama`='blog/cisco-agenticops.png' WHERE `slug`='cisco-agenticops' AND (`gambar_utama` IS NULL OR `gambar_utama`='');
UPDATE `blog` SET `gambar_utama`='blog/era-agentic-ai.png' WHERE `slug`='era-agentic-ai' AND (`gambar_utama` IS NULL OR `gambar_utama`='');
UPDATE `blog` SET `gambar_utama`='blog/platform-secops.png' WHERE `slug`='platform-secops-terpadu' AND (`gambar_utama` IS NULL OR `gambar_utama`='');
UPDATE `blog` SET `gambar_utama`='blog/menjaga-rahasia.png' WHERE `slug`='menjaga-rahasia-enterprise-llm' AND (`gambar_utama` IS NULL OR `gambar_utama`='');
UPDATE `blog` SET `gambar_utama`='solutions/illus-infra.png' WHERE `slug`='infrastruktur-cloud-resilient' AND (`gambar_utama` IS NULL OR `gambar_utama`='');

-- Trim extra category tabs to match Figma (reassign their articles to Articles & News first).
SET @an = (SELECT id FROM (SELECT id FROM blog_kategori WHERE slug='articles-news' LIMIT 1) t);
UPDATE `blog_kategori_rel` SET `kategori_id`=@an
  WHERE `kategori_id` IN (SELECT id FROM (SELECT id FROM blog_kategori WHERE slug IN ('insight','security')) x)
  AND @an IS NOT NULL;
DELETE FROM `blog_kategori_rel` WHERE `kategori_id` IN (SELECT id FROM (SELECT id FROM blog_kategori WHERE slug IN ('insight','security')) x);
DELETE FROM `blog_kategori` WHERE slug IN ('insight','security');
UPDATE `blog_kategori` SET urutan=1 WHERE slug='awards';
UPDATE `blog_kategori` SET urutan=2 WHERE slug='event';
UPDATE `blog_kategori` SET urutan=3 WHERE slug='articles-news';
UPDATE `blog_kategori` SET urutan=4 WHERE slug='program-promo';

-- Clean HTML tags left in existing excerpts (excerpt is plain text going forward).
UPDATE `blog` SET `excerpt` = TRIM(
  REPLACE(REPLACE(REPLACE(REPLACE(`excerpt`,'</p>',' '),'<p>',''),'<br>',' '),'<br/>',' ')
) WHERE `excerpt` LIKE '%<%';
