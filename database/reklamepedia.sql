-- ============================================================
-- REKLAMEPEDIA CMS — DATABASE SCHEMA (regenerated to match the application code)
-- The previous schema shipped table/column names that did not match the code
-- (services vs layanan, blog_posts vs blog, custom_texts vs content_blocks,
--  themes.versi/preview_image/status vs version/screenshot/author/is_installed, ...).
-- This version aligns names/columns with what the code actually queries.
-- ============================================================
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";
SET NAMES utf8mb4;

-- NOTE: Portable import. No CREATE DATABASE / USE here, so this file imports into
-- whatever database you select (e.g. shared hosting like u12345_stt). In phpMyAdmin:
-- select the target DB first, then Import. On a machine where you can create DBs,
-- run:  CREATE DATABASE mydb; USE mydb;  before sourcing this file.

-- ---------- Settings ----------
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `setting_group` varchar(50) DEFAULT 'general',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`), UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`setting_key`,`setting_value`,`setting_group`) VALUES
('site_name','Sapta Tunas Teknologi','general'),
('site_title','Sapta Tunas Teknologi — Enterprise Solution Provider','general'),
('site_tagline','Enterprise Solution Provider','general'),
('site_description','Sapta Tunas Teknologi — established 2015. Business Technology Solutions & Services di Indonesia: IT & Cloud Infrastructure, Cybersecurity, Data & AI.','general'),
('site_logo','','general'),('site_favicon','','general'),
('site_email','marketing@saptatunas.com','general'),
('site_email_prosupport','prosupport@saptatunas.com','general'),
('site_phone','+62 21-5028 1717','general'),
('site_phone_prosupport','021-2410 1568','general'),
('site_address','Komplek Perkantoran Agung Sedayu Blok H No.28-30, Jl. Arteri Mangga Dua Raya, Jakarta Pusat, DKI Jakarta, Indonesia 10730','general'),
('site_maps_embed','','general'),
('wa_number','6282110001087','whatsapp'),
('wa_display','+62 821-1000-1087','whatsapp'),
('wa_text','Halo, saya ingin konsultasi solusi IT.','whatsapp'),
('wa_float_enabled','1','whatsapp'),
('linkedin_url','#','social'),('instagram_url','#','social'),('facebook_url','#','social'),('youtube_url','#','social'),
('active_theme','anima','general'),
('default_lang','id','i18n'),('languages','id,en','i18n'),
('setup_completed','1','general'),
('meta_title_default','Sapta Tunas Teknologi','seo'),
('meta_desc_default','Enterprise Solution Provider','seo'),
('accent_color','#2478E0','tampilan');

-- ---------- Users ----------
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','penulis','admin_produk','tim_ads') NOT NULL DEFAULT 'penulis',
  `foto` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`), UNIQUE KEY `username` (`username`), UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- password default: "password"
INSERT INTO `users` (`nama`,`username`,`email`,`password`,`role`) VALUES
('Super Admin','admin','admin@saptatunas.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','superadmin');

-- ---------- Menus (multi-location: header/footer) ----------
CREATE TABLE `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `lokasi` varchar(30) NOT NULL DEFAULT 'header',
  `target` varchar(10) NOT NULL DEFAULT '_self',
  `urutan` int(11) NOT NULL DEFAULT 0,
  `parent_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `menus` (`nama`,`url`,`lokasi`,`urutan`,`is_active`,`is_default`) VALUES
('About Us','/tentang-kami','header',1,1,1),
('Solutions','/solutions','header',2,1,1),
('Services','/services','header',3,1,1),
('Industry','/industri','header',4,1,1),
('What''s New','/blog','header',5,1,1),
('Career','/career','header',6,1,1),
('Contact Us','/hubungi-kami','header',7,1,1);

-- ---------- Content blocks (editable text per page, per language) ----------
CREATE TABLE `content_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_key` varchar(80) NOT NULL,
  `block_key` varchar(120) NOT NULL,
  `lang` varchar(5) NOT NULL DEFAULT 'id',
  `block_label` varchar(200) DEFAULT NULL,
  `block_type` varchar(30) NOT NULL DEFAULT 'text',
  `konten` longtext DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`), UNIQUE KEY `page_block_lang` (`page_key`,`block_key`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- i18n (§D): UI strings + per-row content field translations ----------
CREATE TABLE `translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(5) NOT NULL,
  `grp` varchar(40) NOT NULL DEFAULT 'ui',
  `k` varchar(190) NOT NULL,
  `v` text DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `lang_grp_k` (`lang`,`grp`,`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `content_i18n` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tabel` varchar(64) NOT NULL,
  `row_id` int(11) NOT NULL,
  `field` varchar(64) NOT NULL,
  `lang` varchar(5) NOT NULL,
  `nilai` longtext DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `cell` (`tabel`,`row_id`,`field`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Per-page SEO ----------
CREATE TABLE `page_seo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_key` varchar(80) NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `robots` varchar(80) DEFAULT 'index,follow',
  PRIMARY KEY (`id`), UNIQUE KEY `page_key` (`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Custom pages ----------
CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `konten` longtext DEFAULT NULL,
  `template` varchar(80) DEFAULT 'default',
  `show_in_nav` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('draft','published') NOT NULL DEFAULT 'published',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`), UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `pages` (`judul`,`slug`,`konten`,`status`,`is_active`) VALUES
('Privacy Policy','privacy-policy','<p>Privacy Policy.</p>','published',1),
('Compliance Policy','compliance-policy','<p>Compliance Policy.</p>','published',1);

-- ---------- Layanan (services) ----------
CREATE TABLE `layanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `deskripsi_pendek` text DEFAULT NULL,
  `deskripsi` longtext DEFAULT NULL,
  `icon` varchar(120) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `consult_title` varchar(255) DEFAULT NULL,
  `consult_desc` text DEFAULT NULL,
  `section_types_title` varchar(255) DEFAULT NULL,
  `section_types_desc` text DEFAULT NULL,
  `section_gallery_title` varchar(255) DEFAULT NULL,
  `section_gallery_desc` text DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`), UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `layanan_sub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `layanan_id` int(11) NOT NULL,
  `nama` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `icon` varchar(120) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), KEY `layanan_id` (`layanan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Produk ----------
CREATE TABLE `produk_kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL, `slug` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL, `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL, `slug` varchar(255) NOT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `short_description` text DEFAULT NULL, `deskripsi` longtext DEFAULT NULL,
  `harga` decimal(15,2) DEFAULT NULL, `harga_coret` decimal(15,2) DEFAULT NULL,
  `gambar_utama` varchar(255) DEFAULT NULL, `badge` varchar(60) DEFAULT NULL, `label` varchar(60) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL, `berat` int(11) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `urutan` int(11) NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL, `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`), UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produk_kategori_rel` (
  `produk_id` int(11) NOT NULL, `kategori_id` int(11) NOT NULL,
  PRIMARY KEY (`produk_id`,`kategori_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produk_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT, `produk_id` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL, `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), KEY `produk_id` (`produk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produk_marketplace` (
  `id` int(11) NOT NULL AUTO_INCREMENT, `produk_id` int(11) NOT NULL,
  `platform` varchar(40) NOT NULL, `url` varchar(500) NOT NULL,
  PRIMARY KEY (`id`), KEY `produk_id` (`produk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Blog ----------
CREATE TABLE `blog_kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL, `slug` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL, `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(500) NOT NULL, `slug` varchar(500) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `konten` longtext DEFAULT NULL, `excerpt` text DEFAULT NULL,
  `gambar_utama` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','scheduled') NOT NULL DEFAULT 'draft',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL, `meta_description` text DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`), UNIQUE KEY `slug` (`slug`), KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blog_kategori_rel` (
  `blog_id` int(11) NOT NULL, `kategori_id` int(11) NOT NULL,
  PRIMARY KEY (`blog_id`,`kategori_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blog_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT, `nama` varchar(100) NOT NULL, `slug` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blog_tags_rel` (
  `blog_id` int(11) NOT NULL, `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`blog_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Gallery ----------
CREATE TABLE `gallery_kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT, `nama` varchar(100) NOT NULL, `slug` varchar(100) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0, PRIMARY KEY (`id`), UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL, `slug` varchar(255) DEFAULT NULL,
  `gambar` varchar(255) NOT NULL, `kategori_id` int(11) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL, `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `urutan` int(11) NOT NULL DEFAULT 0, `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`), KEY `kategori_id` (`kategori_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Testimonial ----------
CREATE TABLE `testimonial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL, `jabatan` varchar(150) DEFAULT NULL, `perusahaan` varchar(150) DEFAULT NULL,
  `isi` text NOT NULL, `rating` tinyint(1) NOT NULL DEFAULT 5, `foto` varchar(255) DEFAULT NULL,
  `tipe` enum('text','video') NOT NULL DEFAULT 'text', `video_url` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0, `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- FAQ ----------
CREATE TABLE `faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pertanyaan` text NOT NULL, `jawaban` longtext NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0, `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `faq_layanan_rel` (
  `faq_id` int(11) NOT NULL, `layanan_id` int(11) NOT NULL,
  PRIMARY KEY (`faq_id`,`layanan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Client logos ----------
CREATE TABLE `klien_logo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL, `logo` varchar(255) DEFAULT NULL, `url` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0, `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Hero slides ----------
CREATE TABLE `hero_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) DEFAULT NULL, `subtitle` text DEFAULT NULL, `gambar` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,          -- optional MP4/WebM for a video hero slide
  `cta_text` varchar(100) DEFAULT NULL, `cta_url` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0, `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Flexible content blocks ----------
CREATE TABLE `flex_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) DEFAULT NULL, `konten` longtext DEFAULT NULL,
  `posisi` varchar(60) NOT NULL DEFAULT 'home_middle', `urutan` int(11) NOT NULL DEFAULT 0,
  `align` varchar(20) DEFAULT 'left', `bg_color` varchar(20) DEFAULT NULL,
  `layanan_id` int(11) DEFAULT NULL, `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Grid icon box ----------
CREATE TABLE `grid_icon_box` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul_section` varchar(255) DEFAULT NULL, `kolom` int(11) NOT NULL DEFAULT 3,
  `posisi` varchar(60) NOT NULL DEFAULT 'home_middle', `urutan` int(11) NOT NULL DEFAULT 0,
  `layanan_id` int(11) DEFAULT NULL, `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `grid_icon_box_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT, `grid_id` int(11) NOT NULL,
  `icon` varchar(120) DEFAULT NULL, `judul` varchar(200) DEFAULT NULL, `deskripsi` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL, `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), KEY `grid_id` (`grid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- WhatsApp contacts + click tracking ----------
CREATE TABLE `wa_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL, `nomor` varchar(30) NOT NULL, `jabatan` varchar(100) DEFAULT NULL,
  `pesan` varchar(255) DEFAULT NULL, `urutan` int(11) NOT NULL DEFAULT 0, `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wa_clicks` (
  `id` int(11) NOT NULL AUTO_INCREMENT, `contact_id` int(11) NOT NULL,
  `clicks` int(11) NOT NULL DEFAULT 0, `created_at` date NOT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `contact_date` (`contact_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Visitor stats ----------
CREATE TABLE `statistik_visitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT, `tanggal` date NOT NULL,
  `page` varchar(255) NOT NULL DEFAULT '/', `views` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), UNIQUE KEY `tanggal_page` (`tanggal`,`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Activity log ----------
CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` int(11) DEFAULT NULL,
  `aksi` varchar(255) NOT NULL, `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`), KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Plugins (feature toggles) ----------
CREATE TABLE `plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL, `slug` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL, `is_active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `plugins` (`nama`,`slug`,`deskripsi`,`is_active`) VALUES
('Marketplace','marketplace','Tombol beli ke marketplace pada halaman produk.',0);

-- ---------- Themes ----------
CREATE TABLE `themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL, `nama` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL, `author` varchar(120) DEFAULT NULL,
  `version` varchar(20) NOT NULL DEFAULT '1.0', `screenshot` varchar(255) DEFAULT NULL,
  `demo_url` varchar(255) DEFAULT NULL,
  `is_installed` tinyint(1) NOT NULL DEFAULT 1, `is_active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `themes` (`slug`,`nama`,`deskripsi`,`author`,`version`,`screenshot`,`is_installed`,`is_active`) VALUES
('default','Default','Tema default bawaan CMS.','Reklamepedia','1.0','',1,0),
('anima','Anima','Tema enterprise sinematik Sapta Tunas (Home dari master, halaman lain dari Figma). CSP-safe.','Anima','0.1.0','/themes/anima/screenshot.png',1,1);

-- ---------- Seed: blog categories + starter articles (What's New) ----------
INSERT INTO `blog_kategori` (`nama`,`slug`,`urutan`) VALUES
('Awards','awards',1),('Articles & News','articles-news',2),('Event','event',3),
('Insight','insight',4),('Security','security',5),('Program Promo','program-promo',6);

INSERT INTO `blog` (`judul`,`slug`,`user_id`,`konten`,`excerpt`,`gambar_utama`,`status`,`created_at`) VALUES
('Andalkan AI Assistant untuk Troubleshooting Cepat dan Navigasi Proteksi Data Perusahaan Anda','ai-assistant-troubleshooting',1,'<p>Tim IT Anda masih troubleshooting secara manual? Saatnya andalkan AI Assistant untuk mempercepat navigasi dan proteksi data perusahaan.</p>','Tim IT Anda masih troubleshooting secara manual di tengah kompleksitas infrastruktur modern…','','published','2026-07-15 09:00:00'),
('Mengatasi Kompleksitas Jaringan Enterprise Lewat Pendekatan Otomatisasi Cisco AgenticOps','cisco-agenticops',1,'<p>Paradoks baru dunia TI: sisi positif dan tantangan di balik kehadiran AI.</p>','Paradoks baru dunia TI: sisi positif dan tantangan di balik kehadiran AI. Perkembangan teknologi kecerdasan buatan…','','published','2026-07-09 10:00:00'),
('Era Agentic AI: Solusi Infrastruktur IT untuk Inovasi Bisnis Skala Besar','era-agentic-ai',1,'<p>Adopsi kecerdasan buatan di dunia bisnis telah mencapai titik balik yang signifikan.</p>','Adopsi kecerdasan buatan (Artificial Intelligence) di dunia bisnis telah mencapai titik balik yang signifikan…','','published','2026-02-09 10:00:00'),
('Platform SecOps Terpadu: Deteksi, Investigasi, dan Respons Keamanan Perusahaan','platform-secops-terpadu',1,'<p>Hari ini taktik serangan siber tidak lagi mengetuk pintu depan secara terang-terangan.</p>','Hari ini taktik yang digunakan dalam serangan siber tidak lagi mengetuk pintu depan secara terang-terangan…','','published','2026-07-09 11:00:00'),
('Menjaga Rahasia Enterprise di Era LLM: Pentingnya Solusi Keamanan Data yang Cerdas','menjaga-rahasia-enterprise-llm',1,'<p>Tantangan baru keamanan data di era adopsi AI enterprise.</p>','Tantangan Baru Keamanan Data di Era Adopsi AI Enterprise. Mayoritas pemimpin perusahaan saat ini sepakat…','','published','2026-02-09 12:00:00'),
('Membangun Infrastruktur Cloud yang Resilient untuk Skala Enterprise','infrastruktur-cloud-resilient',1,'<p>Strategi arsitektur cloud modern yang menjaga uptime, keamanan, dan efisiensi biaya.</p>','Strategi arsitektur cloud modern yang menjaga uptime, keamanan, dan efisiensi biaya di tengah pertumbuhan bisnis…','','published','2026-01-22 09:00:00');

INSERT INTO `blog_kategori_rel` (`blog_id`,`kategori_id`) VALUES
(1,4),(2,1),(3,2),(4,1),(5,2),(6,4);

-- ---------- Seed: testimonials (home) ----------
INSERT INTO `testimonial` (`nama`,`jabatan`,`perusahaan`,`isi`,`rating`,`tipe`,`urutan`,`is_active`) VALUES
('Yonathan Moniaga','Chief Information Officer','Erha Clinic Indonesia','Kami sangat mengapresiasi STT dalam mendukung managed service IT infrastructure kami. Responsivitas tim dan keterbukaan terhadap masukan menjadikan kolaborasi kami produktif dan positif.',5,'video',1,1),
('IT Director','Financial Services','','Migrasi sistem transaksi kami berjalan mulus dan aman. Tim STT memahami kebutuhan compliance industri finansial dengan baik.',5,'text',2,1),
('Head of Operations','Manufacture & FMCG','','Otomatisasi supply chain dari STT memangkas waktu proses secara signifikan. Partner yang benar-benar paham operasional pabrik.',5,'video',3,1),
('Chief Technology Officer','E-Commerce Platform','','Platform kami kini scalable menghadapi lonjakan traffic. Arsitektur yang dirancang STT terbukti andal saat peak season.',5,'text',4,1),
('VP Technology','Enterprise IT','','Implementasi cloud, data, dan AI berjalan sesuai roadmap. Eksekusi rapi dan komunikasi transparan sepanjang proyek.',5,'video',5,1),
('IT Manager','Healthcare Group','','Dukungan managed IT 24/7 membuat operasional rumah sakit kami jauh lebih tenang. Highly recommended.',5,'text',6,1);

-- ---------- Seed: hero slides (Home cinematic slider) ----------
INSERT INTO `hero_slides` (`judul`,`subtitle`,`gambar`,`urutan`,`is_active`) VALUES
('Growing The Global','Technology Industry','',1,1),
('AI-driven','Smart Hospital','',2,1),
('Resilient','Cloud Infrastructure','',3,1),
('Cybersecurity','Without Compromise','',4,1),
('Data-driven','Intelligence','',5,1);

-- ============================================================
--  Home animations — data-driven (editable text + media)
--  §14.2: cube (Solutions prism) & orbit (Our Industries) cards
--  are 100% editable from admin and support image/photo (+ video
--  for the cube). PHP injects config CSP-safely via data-* attrs.
-- ============================================================

-- ---------- Our Industries (orbit animation cards) ----------
CREATE TABLE `industri` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL,                       -- short label on the card + center eyebrow
  `slug` varchar(160) DEFAULT NULL,                    -- detail-page slug (/industri/[slug])
  `judul` varchar(255) DEFAULT NULL,                   -- center title (HTML allowed: <b>..</b>)
  `subtitle` varchar(255) DEFAULT NULL,                -- center sub-line
  `intro` text DEFAULT NULL,                           -- detail-page intro paragraph
  `gambar` varchar(255) DEFAULT NULL,                  -- card image/photo (uploads/); overrides gradient
  `hero_image` varchar(255) DEFAULT NULL,              -- detail-page hero image
  `warna1` varchar(20) NOT NULL DEFAULT '#0f2a54',     -- gradient start (fallback when no image)
  `warna2` varchar(20) NOT NULL DEFAULT '#357be0',     -- gradient end
  `url` varchar(255) DEFAULT NULL,                     -- link target for the card
  `urutan` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default gradient (#1d478c -> #3f80e2) matches the theme's .ind2-card so an unedited
-- install renders exactly like the master design; admin can recolor per card.
INSERT INTO `industri` (`label`,`slug`,`judul`,`subtitle`,`gambar`,`warna1`,`warna2`,`urutan`,`is_active`) VALUES
('Financial','financial','Financial Services <b>& E-Commerce</b>','Secure digital transactions','','#1d478c','#3f80e2',1,1),
('Education','education','<b>Education</b>','E-learning & smart campus','','#1d478c','#3f80e2',2,1),
('Manufacture','manufacture','Manufacture <b>& FMCG</b>','Supply chain automation','','#1d478c','#3f80e2',3,1),
('Healthcare','healthcare','<b>Healthcare</b>','Secure patient data','','#1d478c','#3f80e2',4,1),
('Law Enforce','law-enforcement','Law <b>Enforcement</b>','Encrypted data systems','','#1d478c','#3f80e2',5,1),
('Energy','energy','<b>Energy</b>','Smart grid monitoring','','#1d478c','#3f80e2',6,1),
('Telecom','telecom','Telecommunication <b>(ICT)</b>','High-speed cloud network','','#1d478c','#3f80e2',7,1),
('Cross Industry','cross-industry','Cross <b>Industry</b>','Custom IT solutions','','#1d478c','#3f80e2',8,1);

-- ---------- Solutions (cube / prism animation slides) ----------
CREATE TABLE `solution_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eyebrow` varchar(150) DEFAULT NULL,                 -- caption eyebrow
  `judul` varchar(255) DEFAULT NULL,                   -- caption heading (HTML allowed)
  `deskripsi` text DEFAULT NULL,                       -- caption paragraph
  `label` varchar(60) DEFAULT NULL,                    -- watermark on the generated panel texture
  `gambar` varchar(255) DEFAULT NULL,                  -- panel image/photo (uploads/); overrides generated
  `video_url` varchar(255) DEFAULT NULL,               -- panel short video (uploads/ or URL); overrides image
  `warna_dark` varchar(20) NOT NULL DEFAULT '#0a1430', -- gradient dark (fallback texture)
  `warna_mid` varchar(20) NOT NULL DEFAULT '#123a6a',  -- gradient mid
  `warna_accent` varchar(20) NOT NULL DEFAULT '#42a0ff',
  `logos` text DEFAULT NULL,                           -- JSON array of partner logos (uploads path/URL, or built-in key)
  `url` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `solution_slides` (`eyebrow`,`judul`,`deskripsi`,`label`,`gambar`,`video_url`,`warna_dark`,`warna_mid`,`warna_accent`,`logos`,`urutan`,`is_active`) VALUES
('Modernize Infrastructure','Modernize <b>Infrastructure</b>','Private cloud, migrasi, hingga cloud repatriation — dirancang untuk kebutuhan Anda.','INFRA','','','#0a1430','#123a6a','#42a0ff','["dell","nut","vmware","sangfor"]',1,1),
('Cybersecurity','<b>Cybersecurity</b>','Perlindungan menyeluruh untuk aset digital dan operasional bisnis.','SECURITY','','','#101430','#20306e','#6f8bff','["sangfor","redhat","microsoft"]',2,1),
('Data','Data & <b>Analytics</b>','Dari data mentah menjadi keputusan cerdas yang terlindungi.','DATA','','','#0c1838','#164079','#4f9bff','["comm","hyu","vee","redhat"]',3,1),
('AI','Artificial <b>Intelligence</b>','AI yang berjalan di atas infrastruktur nyata dan andal.','AI','','','#0a1c3a','#12386e','#3f8bff','["microsoft","intel","amd"]',4,1),
('AI Platform Application','AI Platform <b>Application</b>','Aplikasi cerdas siap pakai untuk mempercepat bisnis Anda.','APPS','','','#0e1630','#1a4079','#4fb0ff','["infra","microsoft","dell","intel"]',5,1);

-- ---------- Solutions LANDING PAGE sections (/solutions — Figma "Our Solutions") ----------
-- One row per solution block: alternating image/text, illustration, partner logo strip,
-- optional secondary CTA (e.g. SatuAI). All fields CMS-editable; EN via content_i18n.
CREATE TABLE `solutions_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,                 -- heading; <b>...</b> renders blue
  `solusi` text DEFAULT NULL,                    -- "Solution:" paragraph
  `teks_warna` varchar(20) NOT NULL DEFAULT '',  -- '' = default, 'red' = red solution text (Figma AI Platform)
  `gambar` varchar(255) DEFAULT NULL,            -- illustration image (uploads/)
  `partner_img` varchar(255) DEFAULT NULL,       -- partner-logo strip image (uploads/)
  `url` varchar(255) DEFAULT NULL,               -- "See More" link
  `cta_label` varchar(80) DEFAULT NULL,          -- optional secondary button label (e.g. SatuAI)
  `cta_url` varchar(255) DEFAULT NULL,           -- optional secondary button link
  `urutan` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `solutions_section` (`judul`,`solusi`,`teks_warna`,`gambar`,`partner_img`,`url`,`cta_label`,`cta_url`,`urutan`,`is_active`) VALUES
('Modernize <b>Infrastructure</b>','Enterprise Data Centre Infrastructure, Edge Data Center Infrastructure, Data Center Managed Services, Private Cloud, Enterprise Private Cloud and Hybrid Cloud Solutions.','','solutions/illus-infra.png','solutions/partners-infra.png','#','','',1,1),
('<b>CyberSecurity</b>','Data Protection, Cyber Resiliency, Network Security, Endpoint Security, Security Operation Center, Vulnerability Assessment.','','solutions/illus-cyber.png','solutions/partners-cyber.png','#','','',2,1),
('<b>Data</b>','Big Data Solution, Data Analytics, IoT and Real-time Data Streaming Analytics.','','solutions/illus-data.png','solutions/partners-data.png','#','','',3,1),
('<b>AI</b>','Image and Video Analytics using AI and Large Language Model using Generative AI.','','solutions/illus-ai.png','solutions/partners-ai.png','#','SatuAI','#',4,1),
('AI Platform <b>Application</b>','Data Protection, Cyber Resiliency, Network Security, Endpoint Security, Security Operation Center, Vulnerability Assessment.','red','solutions/illus-platform.png','solutions/partners-platform.png','#','','',5,1);

-- Solutions landing intro + Coming Soon banner (content_blocks; ID + EN) --------------
INSERT INTO `content_blocks` (`page_key`,`block_key`,`lang`,`block_label`,`block_type`,`konten`,`is_active`) VALUES
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

-- ---------- Solution pillars (Solutions landing + Industry detail tabs) ----------
CREATE TABLE `solusi_pilar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) NOT NULL,
  `slug` varchar(160) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,            -- Lucide name OR uploads path
  `gambar` varchar(255) DEFAULT NULL,          -- optional card/hero image
  `url` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`), UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `solusi_pilar` (`nama`,`slug`,`deskripsi`,`icon`,`urutan`,`is_active`) VALUES
('Modernize Infrastructure','modernize-infrastructure','Private cloud, migrasi, hingga cloud repatriation — dirancang untuk kebutuhan Anda.','layers',1,1),
('Data & AI','data-ai','Dari data mentah menjadi keputusan cerdas yang terlindungi, ditenagai AI yang andal.','sparkles',2,1),
('Cybersecurity','cybersecurity','Perlindungan menyeluruh untuk aset digital dan operasional bisnis Anda.','lock',3,1),
('Managed Services','managed-services','Operasional TI dikelola penuh oleh tim ahli, 24/7, dengan SLA yang jelas.','settings',4,1);

-- ---------- Industry × Pillar matrix (per-cell content) ----------
CREATE TABLE `industri_pilar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `industri_id` int(11) NOT NULL,
  `pilar_id` int(11) NOT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `konten` longtext DEFAULT NULL,               -- rich text
  `fitur` text DEFAULT NULL,                     -- JSON: [{icon,judul,teks} x <=4]
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cell` (`industri_id`,`pilar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- ---------- Career / Jobs + applications ----------
CREATE TABLE `career` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `role` varchar(120) DEFAULT NULL,             -- job role / department (filter)
  `lokasi` varchar(120) DEFAULT NULL,           -- location (filter)
  `tipe` varchar(60) DEFAULT 'Full-time',       -- Full-time / Contract / Internship / Remote
  `jenjang` varchar(120) DEFAULT NULL,          -- education level (e.g. Bachelor/S1)
  `pengalaman` varchar(120) DEFAULT NULL,       -- e.g. Experienced / Fresh Graduate
  `gaji` varchar(120) DEFAULT NULL,             -- optional salary range
  `deskripsi` text DEFAULT NULL,                -- short intro
  `responsibilities` longtext DEFAULT NULL,     -- rich
  `requirements` longtext DEFAULT NULL,         -- rich
  `deadline` date DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`), UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `career_id` int(11) DEFAULT NULL,
  `posisi` varchar(200) DEFAULT NULL,           -- snapshot of the job title at apply time
  `nama` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `telepon` varchar(60) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `cv_file` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `status` enum('baru','review','interview','ditolak','diterima') NOT NULL DEFAULT 'baru',
  `ip` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`), KEY `career_id` (`career_id`), KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- About Us repeaters (Mission bullets, ICARE values, Milestones, Awards, Quality
-- standards, Certifications) — all editable from the "Tentang Kami" admin module.
CREATE TABLE `about_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seksi` enum('mission','value','milestone','award','quality','cert') NOT NULL,
  `kode` varchar(20) DEFAULT NULL,       -- e.g. ICARE letter (I/C/A/R/E)
  `judul` varchar(200) DEFAULT NULL,     -- value name / award org / ISO code / vendor / milestone label
  `teks` text DEFAULT NULL,              -- mission bullet / value desc / award title / milestone paragraph
  `gambar` varchar(255) DEFAULT NULL,    -- uploaded image: ICARE letter, milestone image, award/quality/cert logo
  `grup` varchar(60) DEFAULT NULL,       -- grouping for sliders (e.g. cert brand "DELL")
  `galeri` text DEFAULT NULL,            -- optional extra images (JSON array of paths) e.g. milestone thumbnails
  `tahun` varchar(20) DEFAULT NULL,      -- milestone / award year
  `urutan` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`), KEY `seksi` (`seksi`), KEY `urutan` (`urutan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `about_items` (`seksi`,`kode`,`judul`,`teks`,`tahun`,`urutan`) VALUES
('mission',NULL,NULL,'Build a wide and constructive relationship with client for the mutual long-term business achievement.',NULL,1),
('mission',NULL,NULL,'Endless learning to ensure the high quality of people performance through time.',NULL,2),
('mission',NULL,NULL,'Nurture the high commitment of honesty, integrity, and professional ethics to achieve the highest value to stakeholder.',NULL,3),
('mission',NULL,NULL,'Ensure excellent and reliable support to clients.',NULL,4),
('mission',NULL,NULL,'Always making innovations to provide our clients with the best latest technologies.',NULL,5),
('mission',NULL,NULL,'Responsible and maintain our core values to ensure customer success.',NULL,6),
('value','I','INTEGRITY','Employ high ethical standards, demonstrating honesty and fairness.',NULL,1),
('value','C','COLLABORATE','Coming together is a beginning, keeping together is progress, working together is success.',NULL,2),
('value','A','ACCOUNTABILITY','Responsibility for our decision and actions.',NULL,3),
('value','R','RESPONSIVE','Swift attitude to ensure the best service response and service level to our business partner.',NULL,4),
('value','E','EXCELLENCE','Striving for the best in every aspect of the business solution.',NULL,5),
('milestone',NULL,'Awal Perjalanan','Sapta Tunas Teknologi didirikan pada 2015 dengan komitmen menghadirkan Business Technology Solutions & Services untuk enterprise di Indonesia.','2015',1),
('milestone',NULL,'Ekspansi Kapabilitas','Memperluas kapabilitas infrastruktur, cloud, dan data center seiring bertambahnya kepercayaan klien enterprise di berbagai industri.','2017',2),
('milestone',NULL,'Kemitraan Strategis','Menjalin kemitraan strategis dengan para principal teknologi kelas dunia, termasuk pencapaian status Dell Technologies Titanium Partner.','2023',3),
('milestone',NULL,'Cybersecurity & AI','Memperkuat lini Cybersecurity, Data Management, dan solusi AI untuk mendukung transformasi digital pelanggan secara menyeluruh.','2025',4),
('milestone','now','Hari Ini','Dengan tim engineer bersertifikasi, STT terus mendampingi ratusan klien enterprise dalam perjalanan transformasi digital menuju pertumbuhan bisnis berkelanjutan.','Present',5),
('award',NULL,'Dana Indonesia','Best Performing Vendor 2022','2022',1),
('award',NULL,'PT Bintang Toedjoe','Best Platinum Vendor Award 2023','2023',2),
('award',NULL,'PT Saka Farma Laboratories','Excellent Vendor Award 2024','2024',3),
('award',NULL,'PT Bintang Toedjoe','Best Platinum Vendor Award 2024','2024',4),
('award',NULL,'PT Pratha Widyahusada Tbk','Vendor Excellence Award 2024','2024',5),
('award',NULL,'PT Kalbe Morinaga Indonesia','Excellent Vendor Performance Award 2025','2025',6),
('quality',NULL,'ISO 9001',NULL,NULL,1),
('quality',NULL,'ISO 14001',NULL,NULL,2),
('quality',NULL,'ISO 45001',NULL,NULL,3),
('quality',NULL,'ISO 37001',NULL,NULL,4),
('quality',NULL,'ISO 27001',NULL,NULL,5),
('cert',NULL,'Dell Technologies',NULL,NULL,1),
('cert',NULL,'VMware',NULL,NULL,2),
('cert',NULL,'Microsoft',NULL,NULL,3),
('cert',NULL,'Nutanix',NULL,NULL,4),
('cert',NULL,'Red Hat',NULL,NULL,5),
('cert',NULL,'Veeam',NULL,NULL,6);

-- Inbound messages: contact form, request-proposal, and newsletter subscriptions.
CREATE TABLE `pesan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipe` enum('kontak','proposal','newsletter') NOT NULL DEFAULT 'kontak',
  `nama` varchar(150) DEFAULT NULL,
  `email` varchar(190) DEFAULT NULL,
  `telepon` varchar(60) DEFAULT NULL,
  `perusahaan` varchar(150) DEFAULT NULL,
  `subjek` varchar(200) DEFAULT NULL,
  `pesan` text DEFAULT NULL,
  `halaman` varchar(190) DEFAULT NULL,           -- page the message was sent from
  `ip` varchar(64) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`), KEY `tipe` (`tipe`), KEY `is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `career` (`judul`,`slug`,`role`,`lokasi`,`tipe`,`jenjang`,`pengalaman`,`deskripsi`,`responsibilities`,`requirements`,`deadline`,`urutan`,`is_active`) VALUES
('Backend Engineer (PHP)','backend-engineer-php','Software Engineering & Developer','Jakarta','Full-time','Bachelor/S1','Experienced',
 'Bangun dan pelihara layanan backend yang andal untuk platform enterprise kami.',
 '<ul><li>Merancang & mengembangkan API dan layanan backend.</li><li>Menjaga performa, keamanan, dan skalabilitas.</li><li>Berkolaborasi dengan tim produk & infrastruktur.</li></ul>',
 '<ul><li>Menguasai PHP & MySQL, minimal 3 tahun.</li><li>Paham REST API, Git, dan pola keamanan web.</li><li>Nilai plus: Docker, CI/CD.</li></ul>','2027-01-31',1,1),
('Cyber Security Analyst','cyber-security-analyst','Cyber Security & IT Infrastructure','Bandung','Full-time','Bachelor/S1','Experienced',
 'Pantau, deteksi, dan tanggapi ancaman siber untuk klien enterprise.',
 '<ul><li>Monitoring SOC & analisis insiden.</li><li>Threat hunting & incident response.</li><li>Menyusun laporan keamanan berkala.</li></ul>',
 '<ul><li>Pengalaman SOC/blue team.</li><li>Paham SIEM, EDR, dan framework keamanan.</li><li>Sertifikasi (mis. CEH, Security+) nilai plus.</li></ul>','2027-02-28',2,1),
('Customer Support Specialist','customer-support-specialist','Customer Service & Support','Surabaya','Full-time','Diploma/D3','Fresh Graduate',
 'Jadi garda depan dukungan pelanggan kami dengan layanan yang ramah dan cepat.',
 '<ul><li>Menangani pertanyaan & keluhan pelanggan.</li><li>Eskalasi teknis ke tim terkait.</li><li>Menjaga kepuasan pelanggan.</li></ul>',
 '<ul><li>Komunikasi baik lisan & tulisan.</li><li>Bisa bekerja shift.</li><li>Berpengalaman customer service nilai plus.</li></ul>',NULL,3,1);


-- ============================================================
-- SERVICES MODULE
-- ============================================================
-- ============================================================
-- SERVICES MODULE (5-page hierarchy per client content handoff)
--   service_pages       : the pages (landing / capability / package)
--   service_pillars     : landing pillars + supporting capabilities
--   service_areas       : capability-page areas (Consult/Deploy/Manage, ...)
--   service_area_items  : capability list items under an area
--   service_tiers       : package tiers (Gold/Platinum/Diamond)
--   service_matrix      : package comparison rows (grouped, per-tier values)
-- All editable from the "Services" admin module. Content seeded verbatim.
-- ============================================================

CREATE TABLE `service_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(120) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `tipe` enum('landing','capability','package') NOT NULL DEFAULT 'capability',
  `eyebrow` varchar(60) DEFAULT NULL,
  `headline` varchar(300) DEFAULT NULL,
  `tagline` varchar(300) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `cta_label` varchar(120) DEFAULT NULL,
  `cta_target` varchar(190) DEFAULT NULL,        -- a service slug or absolute URL
  `extra1` text DEFAULT NULL,                     -- landing: supporting statement
  `extra2` varchar(300) DEFAULT NULL,             -- landing: lifecycle line
  `extra3` varchar(300) DEFAULT NULL,             -- landing: closing message
  `hero_image` varchar(255) DEFAULT NULL,
  `visual_note` text DEFAULT NULL,
  `data_ai_title` varchar(200) DEFAULT NULL,      -- capability: "Data & AI pada ..." heading
  `data_ai_body` text DEFAULT NULL,               -- capability: Data & AI bullets (one per line)
  `urutan` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`), UNIQUE KEY `slug` (`slug`), KEY `tipe` (`tipe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `service_pillars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `kategori` enum('pillar','supporting') NOT NULL DEFAULT 'pillar',
  `badge` varchar(60) DEFAULT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tags` varchar(300) DEFAULT NULL,               -- comma list (Consulting,Deployment,Managed)
  `link_slug` varchar(120) DEFAULT NULL,          -- target service page slug
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `service_areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `kode` varchar(10) DEFAULT NULL,                -- 01 / 02 / 03
  `nama` varchar(60) DEFAULT NULL,                -- CONSULT / DEPLOY / MANAGE
  `judul` varchar(200) NOT NULL,                  -- Consulting Services
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `visual_note` varchar(300) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `service_area_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area_id` int(11) NOT NULL,
  `teks` varchar(300) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), KEY `area_id` (`area_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `service_tiers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `nama` varchar(60) NOT NULL,                    -- GOLD / PLATINUM / DIAMOND
  `judul` varchar(200) DEFAULT NULL,              -- Essential Infrastructure Support
  `deskripsi` text DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `service_matrix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `grup` varchar(160) DEFAULT NULL,               -- optional matrix group heading
  `baris` varchar(200) NOT NULL,                  -- row label
  `v_gold` varchar(120) DEFAULT NULL,
  `v_platinum` varchar(120) DEFAULT NULL,
  `v_diamond` varchar(120) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---- Services content seed (verbatim from client handoff) ----

INSERT INTO `service_pages`
 (`slug`,`judul`,`tipe`,`eyebrow`,`headline`,`tagline`,`body`,`cta_label`,`cta_target`,`extra1`,`extra2`,`extra3`,`visual_note`,`data_ai_title`,`data_ai_body`,`urutan`) VALUES
('services','Services','landing','Enterprise Solution Provider',
 'Satu Partner untuk Seluruh Siklus Teknologi Enterprise',NULL,
 'STT sebagai Enterprise Solution Provider hadir sebagai partner teknologi end-to-end, bukan sekadar penyedia solusi IT. STT membantu organisasi mulai dari memahami kebutuhan bisnis, merancang solusi, melakukan implementasi dan integrasi teknologi, mengelola operasional, melindungi environment, hingga melakukan optimasi secara berkelanjutan.',
 'Konsultasikan Kebutuhan Anda','hubungi-kami',
 'Bukan sekadar menjual produk, tetapi mendampingi customer dalam seluruh siklus teknologi enterprise.',
 'Rencanakan → Bangun → Kelola → Lindungi → Optimalkan','One Partner. End-to-End Capability.',
 NULL,NULL,NULL,1),

('services/infrastructure-services','Infrastructure Services','capability',NULL,
 'Infrastructure Services','Bangun. Modernisasi. Kelola.',
 'STT membantu organisasi memahami kondisi existing, merancang architecture, melakukan implementasi dan integrasi teknologi, hingga menjaga environment tetap sehat dan optimal melalui Managed Infrastructure Services.',
 'Lihat Managed Infrastructure Service Packages','services/managed-infrastructure-packages',
 NULL,NULL,NULL,
 'Gunakan foto/visual yang langsung menunjukkan aktivitas layanan — technical workshop, server deployment/racking, dan NOC managed operations.',
 'Data & AI pada Infrastructure Services',
 'Data Platform & Analytics Infrastructure untuk mendukung data-intensive workload dan analytics platform.\nAI Infrastructure untuk compute/GPU, storage, network, serta platform readiness bagi AI workload.\nAI Applications & Industry Use Cases sebagai layer business value di atas infrastructure yang dibangun.',2),

('services/managed-infrastructure-packages','Managed Infrastructure Service Packages','package',NULL,
 'Managed Infrastructure Service Packages','Dukungan Infrastruktur Sesuai Tingkat Kritikalitas Bisnis.',
 'STT menyediakan pilihan Managed Infrastructure Services berdasarkan tingkat kritikalitas environment, kebutuhan operational support, maintenance frequency, onsite support, dan coverage yang dibutuhkan.',
 'Diskusikan Kebutuhan SLA','hubungi-kami',
 NULL,NULL,NULL,NULL,NULL,NULL,3),

('services/cybersecurity-services','Cybersecurity Services','capability',NULL,
 'Cybersecurity Services','Identifikasi Risiko. Perkuat Pertahanan. Tingkatkan Resiliensi.',
 'STT membantu organisasi mengidentifikasi security weaknesses, memperkuat governance dan compliance, serta menyediakan managed cybersecurity capability untuk menghadapi ancaman yang terus berkembang.',
 'Explore SOC as a Service','services/soc-as-a-service',
 NULL,NULL,NULL,
 'Gunakan foto SOC milik STT sebagai visual utama agar halaman lebih autentik dan menunjukkan kapabilitas internal STT.',
 'Data & AI pada Cybersecurity Services',
 'Security Analytics & Data Integration untuk meningkatkan visibility dan analisis security events.\nAI-enabled Security Use Cases untuk detection, analytics, dan operational use cases sesuai kebutuhan customer.\nIntegrasi dengan platform Data & AI bila dibutuhkan sebagai bagian dari solution architecture.',4),

('services/soc-as-a-service','SOC as a Service','package',NULL,
 'SOC as a Service','Pantau. Deteksi. Investigasi. Respons.',
 'STT SOC as a Service membantu organisasi meningkatkan security visibility, mempercepat detection dan investigation, serta meningkatkan kesiapan dalam merespons cyber incident.',
 'Diskusikan Kebutuhan SOC','hubungi-kami',
 NULL,'Monitor → Detect → Investigate → Respond → Improve',NULL,NULL,NULL,NULL,5);

-- Landing pillars + supporting
INSERT INTO `service_pillars` (`page_id`,`kategori`,`badge`,`judul`,`deskripsi`,`tags`,`link_slug`,`urutan`) VALUES
((SELECT id FROM service_pages WHERE slug='services'),'pillar','BUILD & RUN','Infrastructure Services',
 'Membangun, memodernisasi, dan mengelola fondasi teknologi enterprise yang resilient, scalable, dan siap mendukung workload bisnis kritikal.',
 'Consulting,Deployment,Managed Infrastructure','services/infrastructure-services',1),
((SELECT id FROM service_pages WHERE slug='services'),'pillar','PROTECT & RESPOND','Cybersecurity Services',
 'Membantu organisasi mengidentifikasi risiko, memperkuat security posture, memenuhi kebutuhan compliance, serta meningkatkan cyber resilience.',
 'Assess,Govern,Protect','services/cybersecurity-services',2),
((SELECT id FROM service_pages WHERE slug='services'),'supporting','DATA','Data Platform & Analytics',
 'Foundation untuk data platform, analytics workload, dan integrasi data.',NULL,NULL,3),
((SELECT id FROM service_pages WHERE slug='services'),'supporting','AI INFRASTRUCTURE','AI Infrastructure',
 'Compute/GPU, storage, network, dan platform readiness untuk AI workload.',NULL,NULL,4),
((SELECT id FROM service_pages WHERE slug='services'),'supporting','AI APPLICATIONS','AI Applications & Industry Use Cases',
 'Intelligent applications dan industry-specific use cases yang mendukung business value.',NULL,NULL,5);

-- ===== Infrastructure Services — areas + items =====
SET @pg := (SELECT id FROM service_pages WHERE slug='services/infrastructure-services');
INSERT INTO service_areas (page_id,kode,nama,judul,visual_note,urutan) VALUES (@pg,'01','CONSULT','Consulting Services','technical workshop / sticky-note session',1);
SET @a := LAST_INSERT_ID();
INSERT INTO service_area_items (area_id,teks,urutan) VALUES
(@a,'Blueprint Solution & Reference Architecture',1),(@a,'Infrastructure Readiness Assessment',2),
(@a,'Capacity Planning',3),(@a,'Virtualization & Cloud Consulting',4),
(@a,'Data Center & Disaster Recovery',5),(@a,'Data Protection Consulting',6),
(@a,'Server & Storage Consulting',7),(@a,'Modernization Roadmap',8);

INSERT INTO service_areas (page_id,kode,nama,judul,visual_note,urutan) VALUES (@pg,'02','DEPLOY','Enterprise Deployment Services','engineer racking / deploying server infrastructure',2);
SET @a := LAST_INSERT_ID();
INSERT INTO service_area_items (area_id,teks,urutan) VALUES
(@a,'Rack & Stack',1),(@a,'Server, Storage & Network',2),(@a,'Virtualization Platform',3),
(@a,'Hyperconverged Infrastructure',4),(@a,'Cloud Infrastructure',5),(@a,'Microservices Platform',6),
(@a,'Backup & Data Protection',7),(@a,'Data Platform',8),(@a,'AI Infrastructure',9),
(@a,'Migration & Upgrade',10),(@a,'Data Center Relocation',11),(@a,'Multi-vendor Integration',12);

INSERT INTO service_areas (page_id,kode,nama,judul,visual_note,urutan) VALUES (@pg,'03','MANAGE','Managed Infrastructure Services','NOC monitoring / managed operations',3);
SET @a := LAST_INSERT_ID();
INSERT INTO service_area_items (area_id,teks,urutan) VALUES
(@a,'Infrastructure Monitoring',1),(@a,'Preventive Maintenance',2),(@a,'Corrective Maintenance',3),
(@a,'Firmware & Software Lifecycle Support',4),(@a,'Patching',5),(@a,'L1 Technical Support',6),
(@a,'Vendor / Principal Escalation',7),(@a,'Health Review',8),(@a,'Service Reporting',9),
(@a,'Move, Add & Change Support',10);

-- ===== Cybersecurity Services — areas + items =====
SET @pg := (SELECT id FROM service_pages WHERE slug='services/cybersecurity-services');
INSERT INTO service_areas (page_id,kode,nama,judul,visual_note,urutan) VALUES (@pg,'01','ASSESS','Cybersecurity Assessment & Testing',NULL,1);
SET @a := LAST_INSERT_ID();
INSERT INTO service_area_items (area_id,teks,urutan) VALUES
(@a,'Vulnerability Assessment & Penetration Testing (VAPT)',1),(@a,'Red Teaming Security Testing',2),
(@a,'Compromise Assessment',3),(@a,'Security Posture Assessment',4),(@a,'IoT / ICS Assessment',5),
(@a,'ATM / ITM Assessment',6),(@a,'Configuration & Policy Assessment',7);

INSERT INTO service_areas (page_id,kode,nama,judul,visual_note,urutan) VALUES (@pg,'02','GOVERN','Security Risk & Compliance',NULL,2);
SET @a := LAST_INSERT_ID();
INSERT INTO service_area_items (area_id,teks,urutan) VALUES
(@a,'Forensic Readiness',1),(@a,'ISMS ISO 27001 Consulting',2),(@a,'OJK Regulatory Compliance',3),
(@a,'Bank Indonesia Regulatory Compliance',4),(@a,'Personal Data Protection / PDP Compliance',5);

INSERT INTO service_areas (page_id,kode,nama,judul,visual_note,urutan) VALUES (@pg,'03','PROTECT','Managed Cybersecurity',NULL,3);
SET @a := LAST_INSERT_ID();
INSERT INTO service_area_items (area_id,teks,urutan) VALUES
(@a,'Managed SIEM & Incident Response',1),(@a,'Managed Detection & Response',2),(@a,'NDR / EDR / XDR / SOAR',3),
(@a,'Managed Threat Intelligence',4),(@a,'Managed ICS / OT Security',5),(@a,'Cyber Patrol',6),(@a,'SOC as a Service',7);

-- ===== Managed Infrastructure Packages — tiers + matrix =====
SET @pg := (SELECT id FROM service_pages WHERE slug='services/managed-infrastructure-packages');
INSERT INTO service_tiers (page_id,nama,judul,deskripsi,urutan) VALUES
(@pg,'GOLD','Essential Infrastructure Support','Untuk environment yang membutuhkan fundamental operational support dan scheduled maintenance.',1),
(@pg,'PLATINUM','Enhanced Operational Support','Untuk infrastructure yang lebih kritikal dan membutuhkan maintenance serta support lebih intensif.',2),
(@pg,'DIAMOND','Comprehensive Managed Infrastructure','Untuk mission-critical environment dengan operational coverage paling lengkap.',3);
INSERT INTO service_matrix (page_id,grup,baris,v_gold,v_platinum,v_diamond,urutan) VALUES
(@pg,'Service Coverage','Helpdesk Service SLA','24 x 7','24 x 7','24 x 7',1),
(@pg,'Service Coverage','Response Time','Max. 1 Hour','Max. 1 Hour','Max. 1 Hour',2),
(@pg,'Service Coverage','Preventive Maintenance','2x / year','4x / year','Monthly',3),
(@pg,'Service Coverage','Hardware Firmware Upgrade & Software Patching','Yes','Yes','Yes',4),
(@pg,'Service Coverage','Preventive Maintenance Report','2x / year','4x / year','Monthly',5),
(@pg,'Service Coverage','Corrective Maintenance','6 Tickets','12 Tickets','Unlimited',6),
(@pg,'Service Coverage','Onsite Part Replacement (with Principal Engineer)','Yes','Yes','Yes',7),
(@pg,'Service Coverage','Move, Add & Change Management','2 Tickets','6 Tickets','Unlimited',8),
(@pg,'Service Coverage','On-call Support Engineer','Yes','Yes','Yes',9),
(@pg,'Service Coverage','Onsite Standby Engineer (Office Hour)','No','No','1 Engineer',10),
(@pg,'Service Coverage','Maintenance Review','-','Yearly','2x / year',11),
(@pg,'Service Coverage','Minimum Period','1 Year','1 Year','1 Year',12);

-- ===== SOC as a Service — tiers + matrix (two groups) =====
SET @pg := (SELECT id FROM service_pages WHERE slug='services/soc-as-a-service');
INSERT INTO service_tiers (page_id,nama,judul,deskripsi,urutan) VALUES
(@pg,'GOLD','Essential SOC Monitoring','Untuk organisasi yang membutuhkan fundamental security monitoring dan visibility terhadap security events.',1),
(@pg,'PLATINUM','Managed Detection & Investigation','Untuk organisasi yang membutuhkan 24x7 monitoring, faster response, dan co-managed incident handling.',2),
(@pg,'DIAMOND','Comprehensive Cyber Defense','Untuk mission-critical organization yang membutuhkan 24x7 security operations dan advanced incident readiness.',3);
INSERT INTO service_matrix (page_id,grup,baris,v_gold,v_platinum,v_diamond,urutan) VALUES
(@pg,'Security Monitoring & Incident Response','Security Monitoring SLA','8 x 5','24 x 7','24 x 7',1),
(@pg,'Security Monitoring & Incident Response','Log Monitoring Coverage','Up to 75 Assets / Source Log','Up to 150 Assets / Source Log','Up to 300 Assets / Source Log',2),
(@pg,'Security Monitoring & Incident Response','Response Time (MTTA)','1 Hour','30 Min','15 Min',3),
(@pg,'Security Monitoring & Incident Response','Incident Handling','No','Co-Managed','Fully Managed',4),
(@pg,'Security Monitoring & Incident Response','Retention Logs','90 Days','90 Days','90 Days',5),
(@pg,'Security Monitoring & Incident Response','Security Reporting','Quarterly','Monthly','Monthly & Quarterly',6),
(@pg,'Security Monitoring & Incident Response','Threat Hunting','No','No','Yes',7),
(@pg,'Security Monitoring & Incident Response','Infosec Advisory','No','Yes','Yes',8),
(@pg,'Advanced Services','Vulnerability Assessment','No','Yes','Yes',9),
(@pg,'Advanced Services','Digital Forensic Capabilities','No','Yes','Yes',10),
(@pg,'Advanced Services','Penetration Testing','No','No','Yes',11),
(@pg,'Advanced Services','Cyber Drill / Tabletop Exercise','No','No','Yes',12);
