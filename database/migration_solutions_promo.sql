-- ============================================================
-- Migration: Solutions PROMO SPOTLIGHT (multi-banner carousel)
-- Drives the "News & Promotions" spotlight below the /solutions hero.
-- Managed in Admin → "Solutions Promo". Each row = one promo slide
-- (photo + date + title + blurb + CTA), language-aware via content_i18n.
-- Safe & idempotent: CREATE TABLE IF NOT EXISTS + seed only when empty.
-- In phpMyAdmin: select your DB, open the SQL tab, paste this, Go.
-- After running: the 4 seed photos ship in /uploads/solutions/promo/.
-- Tip: wrap a word in the Title with [square brackets] to give it the
-- blue→teal gradient accent, e.g.  Coming Soon: STT × [MeshDefend]
-- ============================================================

CREATE TABLE IF NOT EXISTS `solutions_promo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gambar` varchar(255) DEFAULT NULL,
  `judul` varchar(255) NOT NULL DEFAULT '',
  `tanggal` varchar(100) DEFAULT NULL,
  `blurb` text DEFAULT NULL,
  `cta_text` varchar(80) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed the 4 starter slides only if the table is currently empty.
INSERT INTO `solutions_promo` (`gambar`,`judul`,`tanggal`,`blurb`,`cta_text`,`url`,`urutan`,`is_active`)
SELECT * FROM (
  SELECT 'solutions/promo/promo-1.jpg' AS a,'Coming Soon: STT × [MeshDefend]' AS b,'July 15, 2026' AS c,'A smarter way to simplify Backup and Cyber Resilience Operations — accelerate response and keep your business running seamlessly.' AS d,'Read More' AS e,'#' AS f,1 AS g,1 AS h UNION ALL
  SELECT 'solutions/promo/promo-2.jpg','New Partnership: [NVIDIA]','November 2026','Accelerated AI infrastructure for the most demanding enterprise workloads, now part of our portfolio.','Learn More','#',2,1 UNION ALL
  SELECT 'solutions/promo/promo-3.jpg','Cyber [Resilience] 2026','Webinar · Dec 2026','Join our experts live: ransomware recovery playbooks and a practical path to zero-trust.','Register Now','#',3,1 UNION ALL
  SELECT 'solutions/promo/promo-4.jpg','Dell [Advanced] Services','Now Available','Proactive, performance-tuned support that keeps your Dell estate running at its best.','Explore','#',4,1
) seed
WHERE NOT EXISTS (SELECT 1 FROM `solutions_promo` LIMIT 1);

-- Hero eyebrow (small label above the "Our Solutions" title). INSERT IGNORE keeps
-- any value you have already edited in the CMS.
INSERT IGNORE INTO `content_blocks` (`page_key`,`block_key`,`lang`,`block_label`,`block_type`,`konten`,`is_active`) VALUES
('solutions','hero_eyebrow','id','Hero — eyebrow','text','Enterprise Solution Provider',1),
('solutions','hero_eyebrow','en','Hero — eyebrow','text','Enterprise Solution Provider',1);
