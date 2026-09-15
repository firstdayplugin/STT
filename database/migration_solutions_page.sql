-- ============================================================
-- Migration: Solutions LANDING PAGE (Figma "Our Solutions")
-- Safe to run on an existing database. Idempotent:
--   • CREATE TABLE IF NOT EXISTS   (won't touch an existing table)
--   • INSERT IGNORE                (won't duplicate seeded rows)
-- In phpMyAdmin: select your DB, open the SQL tab, paste this, Go.
-- After running: upload the images in /uploads/solutions/ (bundled in this
-- release) OR replace them via Admin → Solutions (Page).
-- ============================================================

CREATE TABLE IF NOT EXISTS `solutions_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `solusi` text DEFAULT NULL,
  `teks_warna` varchar(20) NOT NULL DEFAULT '',
  `gambar` varchar(255) DEFAULT NULL,
  `partner_img` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `cta_label` varchar(80) DEFAULT NULL,
  `cta_url` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed the 5 Figma blocks only if the table is currently empty.
INSERT INTO `solutions_section` (`judul`,`solusi`,`teks_warna`,`gambar`,`partner_img`,`url`,`cta_label`,`cta_url`,`urutan`,`is_active`)
SELECT * FROM (
  SELECT 'Modernize <b>Infrastructure</b>' AS a,'Enterprise Data Centre Infrastructure, Edge Data Center Infrastructure, Data Center Managed Services, Private Cloud, Enterprise Private Cloud and Hybrid Cloud Solutions.' AS b,'' AS c,'solutions/illus-infra.png' AS d,'solutions/partners-infra.png' AS e,'#' AS f,'' AS g,'' AS h,1 AS i,1 AS j UNION ALL
  SELECT '<b>CyberSecurity</b>','Data Protection, Cyber Resiliency, Network Security, Endpoint Security, Security Operation Center, Vulnerability Assessment.','','solutions/illus-cyber.png','solutions/partners-cyber.png','#','','',2,1 UNION ALL
  SELECT '<b>Data</b>','Big Data Solution, Data Analytics, IoT and Real-time Data Streaming Analytics.','','solutions/illus-data.png','solutions/partners-data.png','#','','',3,1 UNION ALL
  SELECT '<b>AI</b>','Image and Video Analytics using AI and Large Language Model using Generative AI.','','solutions/illus-ai.png','solutions/partners-ai.png','#','SatuAI','#',4,1 UNION ALL
  SELECT 'AI Platform <b>Application</b>','Data Protection, Cyber Resiliency, Network Security, Endpoint Security, Security Operation Center, Vulnerability Assessment.','red','solutions/illus-platform.png','solutions/partners-platform.png','#','','',5,1
) seed
WHERE NOT EXISTS (SELECT 1 FROM `solutions_section` LIMIT 1);

-- Header copy + Coming Soon banner (ID + EN). INSERT IGNORE keeps any values you
-- have already edited in the CMS.
INSERT IGNORE INTO `content_blocks` (`page_key`,`block_key`,`lang`,`block_label`,`block_type`,`konten`,`is_active`) VALUES
('solutions','title','id','Judul','text','Our Solutions',1),
('solutions','lead','id','Intro','html','Kami memahami setiap industri punya tantangan dan kebutuhan yang unik. Karena itu STT menghadirkan solusi yang beragam sekaligus disesuaikan dengan kebutuhan klien. Didukung kemitraan strategis serta tim sales, presales, dan teknis bersertifikasi, kami siap membantu Anda menemukan solusi terbaik untuk kebutuhan perusahaan Anda.',1),
('solutions','banner_img','id','Banner Coming Soon','image','solutions/coming-soon-banner.png',1),
('solutions','banner_url','id','Banner — link','text','#',1),
('solutions','label_solution','id','Label "Solution:"','text','Solution:',1),
('solutions','label_partner','id','Label "Partner:"','text','Partner:',1),
('solutions','card_cta','id','Teks tombol kartu','text','See More',1),
('solutions','title','en','Judul','text','Our Solutions',1),
('solutions','lead','en','Intro','html','We understand that every industry has its own unique challenges and needs. That''s why STT provides solutions that are not only diverse but also tailored to client needs. Supported by our partnerships, our certified sales, presales, and technical team, we can assist you to find the best solution that fit into your company requirement.',1),
('solutions','banner_img','en','Banner Coming Soon','image','solutions/coming-soon-banner.png',1),
('solutions','banner_url','en','Banner — link','text','#',1),
('solutions','label_solution','en','Label "Solution:"','text','Solution:',1),
('solutions','label_partner','en','Label "Partner:"','text','Partner:',1),
('solutions','card_cta','en','Teks tombol kartu','text','See More',1);
