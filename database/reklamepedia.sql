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

CREATE DATABASE IF NOT EXISTS `reklamepedia` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `reklamepedia`;

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
('Service','#','header',3,1,1),
('Industry','#','header',4,1,1),
('What''s New','/blog','header',5,1,1),
('Career','/career','header',6,1,1),
('Contact Us','/hubungi-kami','header',7,1,1);

-- ---------- Content blocks (editable text per page) ----------
CREATE TABLE `content_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_key` varchar(80) NOT NULL,
  `block_key` varchar(120) NOT NULL,
  `block_label` varchar(200) DEFAULT NULL,
  `block_type` varchar(30) NOT NULL DEFAULT 'text',
  `konten` longtext DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`), UNIQUE KEY `page_block` (`page_key`,`block_key`)
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
