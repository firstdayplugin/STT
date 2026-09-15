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
('lead_notify_emails','marketing@saptatunas.com','kontak'),
('site_phone','+62 21-5028 1717','general'),
('site_phone_prosupport','021-2410 1568','general'),
('site_address','Komplek Perkantoran Agung Sedayu Blok H No.28-30, Jl. Arteri Mangga Dua Raya, Jakarta Pusat, DKI Jakarta, Indonesia 10730','general'),
('site_maps_embed','https://www.google.com/maps?q=Sapta+Tunas+Teknologi+PT,+Blok+H+No.28-30,+Jl.+Arteri+Mangga+Dua+Raya,+Jakarta+Pusat+10730&z=16&output=embed','general'),
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
('About Us','/about-us','header',1,1,1),
('Solutions','/solutions','header',2,1,1),
('Services','/services','header',3,1,1),
('Industry','/industri','header',4,1,1),
('What''s New','/blog','header',5,1,1),
('Career','/career','header',6,1,1),
('Contact Us','/contact-us','header',7,1,1);

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
  `nama` varchar(100) NOT NULL, `slug` varchar(160) DEFAULT NULL,
  `jabatan` varchar(150) DEFAULT NULL, `perusahaan` varchar(150) DEFAULT NULL,
  `isi` text NOT NULL, `detail` text DEFAULT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5, `foto` varchar(255) DEFAULT NULL,
  `tipe` enum('text','video') NOT NULL DEFAULT 'text',
  `video_url` varchar(255) DEFAULT NULL, `video_poster` varchar(255) DEFAULT NULL,
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
('Awards','awards',1),('Event','event',2),('Articles & News','articles-news',3),('Program Promo','program-promo',4);

INSERT INTO `blog` (`judul`,`slug`,`user_id`,`konten`,`excerpt`,`gambar_utama`,`status`,`created_at`) VALUES
('Andalkan AI Assistant untuk Troubleshooting Cepat dan Navigasi Proteksi Data Perusahaan Anda','ai-assistant-troubleshooting',1,'<p>Tim IT Anda masih troubleshooting secara manual di 2026? Setiap menit downtime akibat ancaman ransomware bisa merugikan perusahaan Anda ratusan juta rupiah belum termasuk kerugian reputasi dan kepercayaan klien yang jauh lebih sulit dipulihkan. Kini ada IT solutions yang lebih cerdas, dan AI Assistant hadir sebagai jawabannya.</p>
<p>Di era ketika serangan siber di Indonesia melonjak 47% sepanjang tahun ini, proses troubleshooting yang lambat bukan lagi sekadar masalah teknis, hal itu adalah risiko bisnis yang nyata. Perusahaan yang masih mengandalkan IT solutions konvensional dan troubleshooting manual kini menjadi target paling rentan. IT solutions modern yang didukung AI Assistant adalah standar baru yang tidak bisa ditunda. Inilah cara AI Assistant dalam PowerProtect memandu tim Anda menyelesaikan masalah kritis tanpa harus membuka manual, tanpa eskalasi yang membuang waktu berharga.</p>
<h2>AI Assistant: Akhir Era Troubleshooting Manual</h2>
<p>Dell PowerProtect Data Manager kini hadir dengan AI Assistant yang tertanam langsung dalam antarmuka sistem. Saat insiden terjadi, AI Assistant memberikan panduan troubleshooting secara kontekstual dan real-time, bukan sekadar menampilkan pesan error, melainkan langkah penyelesaian yang spesifik sesuai kondisi sistem Anda.</p>
<p>Proses troubleshooting yang biasanya memakan waktu berjam-jam kini selesai dalam hitungan menit. Dengan IT solutions berbasis kecerdasan buatan ini, tim Anda tidak lagi terjebak dalam rutinitas maintenance yang menyita energi. AI Assistant mengambil alih pekerjaan diagnostik berulang, sehingga tim Anda bisa fokus pada inisiatif strategis yang mendorong pertumbuhan bisnis.</p>
<p>AI Assistant juga memandu navigasi compliance audit secara otomatis, memastikan konfigurasi sistem memenuhi regulasi. Dengan IT solutions yang dilengkapi AI Assistant, persiapan audit bukan lagi momen yang mendebarkan. AI Assistant memastikan setiap langkah compliance terstandarisasi, terdokumentasi, dan efisien.</p>
<h2>Visibilitas Penuh, Respons Lebih Proaktif</h2>
<p>Tantangan terbesar dalam troubleshooting infrastruktur modern adalah data yang tersebar di banyak sistem sekaligus. IT solutions dari PowerProtect menjawabnya lewat Unified Dashboard, satu tampilan yang mengkonsolidasikan seluruh ekosistem data Anda: cloud, edge, dan on-premises sekaligus. AI Assistant terintegrasi penuh dalam dashboard ini, siap memberikan panduan dari titik mana pun ditemukan anomali. IT Manager tidak perlu lagi berpindah antar konsol yang membuang waktu. Seluruh status proteksi, alert aktif, dan anomali tersaji dalam satu layar, mempercepat proses troubleshooting secara signifikan. Respons menjadi lebih cepat dari dashboard yang sama.</p>
<p>Enhanced Anomaly Detection turut memperkuat pertahanan dengan mendeteksi perilaku mencurigakan dalam snapshot PowerStore secara real-time. Ketika pola ransomware teridentifikasi sejak dini, tim Anda dapat merespons sebelum dampaknya meluas. Inilah standar baru IT solutions untuk perusahaan modern: dari troubleshooting reaktif menuju pertahanan proaktif yang sesungguhnya.</p>
<p>Bagi perusahaan menengah dan kantor-kantor cabang, Dell menghadirkan PowerProtect Data Domain DD3410 appliance kompak 2U dengan kapasitas skalabel BTB hingga 40TB. IT solutions ini membawa keamanan kelas enterprise ke remote sites tanpa kompleksitas yang berlebihan, dan tetap didukung penuh oleh AI Assistant untuk troubleshooting jarak jauh.</p>
<p>Dengan rasio reduksi data 75:1 yang terverifikasi dari pelanggan nyata, efisiensi storage meningkat drastis, 75TB data hanya membutuhkan 1TB kapasitas fisik. Penghematan ini langsung berdampak pada anggaran IT yang lebih terkendali, sekaligus menjadi argumen kuat bagi IT Manager saat berdiskusi ROI dengan CFO.</p>
<p>Seluruh IT solutions dalam ekosistem PowerProtect juga mendukung TLS 1.3 dan standar NIST, memastikan infrastruktur Anda compliance-ready sejak awal. Troubleshooting konfigurasi keamanan pun jauh lebih sederhana karena standar sudah terintegrasi dalam sistem.</p>
<h2>Mengapa STT?</h2>
<p>Sapta Tunas Teknologi (STT), IT Solutions yang memiliki komitmen yang tinggi dalam membantu pelanggan mencapai tujuan organisasi dan merancang IT Solutions sesuai dengan kebutuhan pelanggan yang mengikuti perkembangan tren teknologi di pasar saat ini.</p>
<ul><li>Dedicated Teams</li><li>Certified Engineer</li><li>Award-Winning</li><li>Demo Solutions Center</li><li>Trusted Partner</li></ul>
<p>Referensi: <a href="https://www.dell.com/en-us/blog/powerprotect-accelerate-innovation-trust-your-resilience/" target="_blank" rel="noopener">dell.com/en-us/blog/powerprotect-accelerate-innovation-trust-your-resilience</a></p>','Tim IT Anda masih troubleshooting secara manual di tengah kompleksitas infrastruktur modern…','blog/ai-assistant.png','published','2026-07-15 09:00:00'),
('Mengatasi Kompleksitas Jaringan Enterprise Lewat Pendekatan Otomatisasi Cisco AgenticOps','cisco-agenticops',1,'<p>Kompleksitas jaringan enterprise terus meningkat seiring adopsi cloud, edge computing, dan aplikasi terdistribusi. Tim operasional dituntut menjaga performa, keamanan, dan ketersediaan layanan di tengah skala yang makin besar dan perubahan yang makin cepat.</p>
<p>Pendekatan otomatisasi Cisco AgenticOps menghadirkan orkestrasi berbasis kebijakan (policy-driven) yang memungkinkan jaringan mengonfigurasi, memvalidasi, dan memulihkan diri secara otomatis. Dengan observability end-to-end, tim dapat mendeteksi anomali lebih dini dan menurunkan mean time to resolution secara signifikan.</p>
<p>Hasilnya adalah operasional jaringan yang lebih efisien, konsisten, dan aman, sekaligus membebaskan tim untuk fokus pada inisiatif strategis alih-alih pekerjaan manual yang berulang.</p>','Paradoks baru dunia TI: sisi positif dan tantangan di balik kehadiran AI. Perkembangan teknologi kecerdasan buatan…','blog/cisco-agenticops.png','published','2026-07-09 10:00:00'),
('Era Agentic AI: Solusi Infrastruktur IT untuk Inovasi Bisnis Skala Besar','era-agentic-ai',1,'<p>Adopsi kecerdasan buatan di dunia bisnis telah mencapai titik balik yang signifikan. Agentic AI, sistem AI yang mampu merencanakan dan mengeksekusi tugas secara mandiri, kini menjadi katalis inovasi pada skala enterprise.</p>
<p>Untuk menopang beban kerja Agentic AI, dibutuhkan infrastruktur IT yang modern: komputasi berperforma tinggi, penyimpanan cepat, jaringan andal, serta platform data yang tergovernance dengan baik. Fondasi inilah yang menentukan seberapa jauh organisasi dapat memanfaatkan AI secara produktif dan aman.</p>
<p>Dengan arsitektur yang tepat, perusahaan dapat menghadirkan use case AI yang berdampak nyata, dari otomatisasi proses hingga pengambilan keputusan berbasis data, tanpa mengorbankan keamanan dan kepatuhan.</p>','Adopsi kecerdasan buatan (Artificial Intelligence) di dunia bisnis telah mencapai titik balik yang signifikan…','blog/era-agentic-ai.png','published','2026-02-09 10:00:00'),
('Platform SecOps Terpadu: Deteksi, Investigasi, dan Respons Keamanan Perusahaan','platform-secops-terpadu',1,'<p>Hari ini taktik yang digunakan dalam serangan siber tidak lagi mengetuk pintu depan secara terang-terangan. Penyerang bergerak diam-diam, memanfaatkan celah kecil, dan berpindah lateral di dalam jaringan sebelum akhirnya melancarkan dampak yang merusak.</p>
<p>Platform SecOps terpadu menyatukan deteksi, investigasi, dan respons dalam satu alur kerja yang terkoordinasi. Dengan kombinasi EDR, NDR, XDR, SIEM terpusat, dan SOAR berbasis AI, tim keamanan memperoleh visibilitas menyeluruh serta kemampuan merespons insiden dengan cepat dan presisi.</p>
<p>Pendekatan ini mempersempit ruang gerak penyerang, memangkas waktu respons, dan memperkuat postur keamanan perusahaan secara berkelanjutan.</p>','Hari ini taktik yang digunakan dalam serangan siber tidak lagi mengetuk pintu depan secara terang-terangan…','blog/platform-secops.png','published','2026-07-09 11:00:00'),
('Menjaga Rahasia Enterprise di Era LLM: Pentingnya Solusi Keamanan Data yang Cerdas','menjaga-rahasia-enterprise-llm',1,'<p>Tantangan baru keamanan data muncul di era adopsi Large Language Models (LLM) pada skala enterprise. Mayoritas pemimpin perusahaan sepakat bahwa AI membuka peluang besar, namun juga menghadirkan risiko kebocoran data sensitif jika tidak dikelola dengan benar.</p>
<p>Solusi keamanan data yang cerdas memastikan informasi rahasia perusahaan tetap terlindungi sepanjang siklus penggunaan AI, mulai dari klasifikasi data, kontrol akses, hingga pemantauan penggunaan model. Private LLM dan data governance yang ketat menjadi kunci menjaga kerahasiaan tanpa menghambat inovasi.</p>
<p>Dengan strategi yang tepat, perusahaan dapat mengadopsi AI generatif secara aman, menjaga kepercayaan pelanggan, dan memenuhi kewajiban kepatuhan.</p>','Tantangan Baru Keamanan Data di Era Adopsi AI Enterprise. Mayoritas pemimpin perusahaan saat ini sepakat…','blog/menjaga-rahasia.png','published','2026-02-09 12:00:00'),
('Membangun Infrastruktur Cloud yang Resilient untuk Skala Enterprise','infrastruktur-cloud-resilient',1,'<p>Membangun infrastruktur cloud yang resilient adalah fondasi bagi perusahaan yang ingin tumbuh tanpa mengorbankan keandalan. Arsitektur modern harus mampu menjaga uptime, keamanan, dan efisiensi biaya di tengah lonjakan kebutuhan bisnis.</p>
<p>Melalui pendekatan high availability, redundansi lintas zona, serta backup dan disaster recovery yang teruji, layanan kritikal tetap berjalan meski terjadi gangguan. Otomatisasi dan observability memastikan tim dapat merespons perubahan beban kerja secara proaktif.</p>
<p>Hasilnya adalah platform cloud yang scalable, aman, dan hemat biaya, siap menopang pertumbuhan enterprise dalam jangka panjang.</p>','Strategi arsitektur cloud modern yang menjaga uptime, keamanan, dan efisiensi biaya di tengah pertumbuhan bisnis…','solutions/illus-infra.png','published','2026-01-22 09:00:00');

INSERT INTO `blog_kategori_rel` (`blog_id`,`kategori_id`) VALUES
(1,3),(2,1),(3,3),(4,1),(5,3),(6,3);

-- ---------- Seed: testimonials (home) ----------
INSERT INTO `testimonial` (`nama`,`slug`,`jabatan`,`perusahaan`,`isi`,`detail`,`rating`,`tipe`,`urutan`,`is_active`) VALUES
('Yonathan Moniaga','yonathan-moniaga-erha-clinic-indonesia','Chief Information Officer','Erha Clinic Indonesia','Kami sangat mengapresiasi STT dalam mendukung managed service IT infrastructure kami. Responsivitas tim dan keterbukaan terhadap masukan menjadikan kolaborasi kami produktif dan positif.','<p>Sebagai penyedia layanan klinik kecantikan dengan jaringan cabang nasional, ketersediaan sistem IT adalah hal yang tidak bisa ditawar. Sapta Tunas Teknologi mendampingi kami mengelola infrastruktur end-to-end, mulai dari monitoring proaktif, preventive maintenance, hingga dukungan teknis yang responsif.</p><p>Yang paling kami hargai adalah keterbukaan tim STT terhadap masukan. Setiap kebutuhan kami didengar, dianalisa, lalu diterjemahkan menjadi solusi yang benar-benar relevan dengan operasional bisnis. Kolaborasi ini membuat tim internal kami bisa fokus pada layanan pasien, bukan memadamkan masalah IT.</p>',5,'video',1,1),
('IT Director','it-director','Financial Services','','Migrasi sistem transaksi kami berjalan mulus dan aman. Tim STT memahami kebutuhan compliance industri finansial dengan baik.','<p>Migrasi sistem transaksi di industri finansial punya toleransi risiko yang sangat rendah. Kami membutuhkan partner yang tidak hanya paham teknologi, tetapi juga memahami kebutuhan compliance dan keamanan data.</p><p>STT merancang skenario migrasi yang matang, melakukan pengujian menyeluruh, dan mengeksekusi cut-over tanpa mengganggu layanan nasabah. Prosesnya berjalan mulus dan aman, persis sesuai roadmap yang kami sepakati di awal.</p>',5,'text',2,1),
('Head of Operations','head-of-operations','Manufacture & FMCG','','Otomatisasi supply chain dari STT memangkas waktu proses secara signifikan. Partner yang benar-benar paham operasional pabrik.','<p>Di lini manufaktur, setiap menit downtime berdampak langsung pada output produksi. STT membantu kami mengotomatisasi proses supply chain sehingga alur informasi dari gudang hingga lini produksi menjadi jauh lebih cepat dan akurat.</p><p>Mereka benar-benar memahami konteks operasional pabrik, bukan sekadar memasang teknologi, tetapi menyesuaikannya dengan cara kerja tim di lapangan.</p>',5,'video',3,1),
('Chief Technology Officer','chief-technology-officer','E-Commerce Platform','','Platform kami kini scalable menghadapi lonjakan traffic. Arsitektur yang dirancang STT terbukti andal saat peak season.','<p>Sebagai platform e-commerce, lonjakan trafik saat campaign besar adalah ujian sesungguhnya. Bersama STT kami membangun arsitektur yang scalable sehingga platform tetap stabil meski beban melonjak berkali-kali lipat.</p><p>Skalabilitas ini memberi kami ketenangan untuk tumbuh tanpa khawatir infrastruktur menjadi penghambat.</p>',5,'text',4,1),
('VP Technology','vp-technology','Enterprise IT','','Implementasi cloud, data, dan AI berjalan sesuai roadmap. Eksekusi rapi dan komunikasi transparan sepanjang proyek.','<p>Transformasi digital kami mencakup cloud, data, dan AI sekaligus. STT mengawal implementasi ini secara bertahap dengan eksekusi yang rapi dan komunikasi yang transparan di setiap milestone.</p><p>Hasilnya, adopsi teknologi baru berjalan lancar dan tim kami merasa didampingi, bukan ditinggalkan setelah proyek selesai.</p>',5,'video',5,1),
('IT Manager','it-manager','Healthcare Group','','Dukungan managed IT 24/7 membuat operasional rumah sakit kami jauh lebih tenang. Highly recommended.','<p>Operasional rumah sakit berjalan 24 jam, dan begitu pula kebutuhan dukungan IT-nya. Layanan managed IT 24/7 dari STT membuat kami tenang karena setiap kendala ditangani dengan cepat, kapan pun terjadi.</p><p>Dukungan yang konsisten ini berdampak langsung pada kelancaran pelayanan kepada pasien.</p>',5,'text',6,1);

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
  `icon` varchar(255) DEFAULT NULL,                    -- landing card icon (Lucide name OR uploads path)
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
INSERT INTO `industri` (`id`,`label`,`slug`,`judul`,`subtitle`,`icon`,`hero_image`,`warna1`,`warna2`,`urutan`,`is_active`) VALUES
(1,'Financial Services & E-Commerce','financial','Financial Services <b>& E-Commerce</b>','Secure digital transactions','industri/icon/financial.png','','#1d478c','#3f80e2',1,1),
(2,'Manufacture & FMCG','manufacture','Manufacture <b>& FMCG</b>','Supply chain automation','industri/icon/manufacture.png','','#1d478c','#3f80e2',2,1),
(3,'Healthcare','healthcare','<b>Healthcare</b>','Secure patient data','industri/icon/healthcare.png','industri/healthcare-hero.png','#1d478c','#3f80e2',3,1),
(4,'Law Enforcement','law-enforcement','Law <b>Enforcement</b>','Encrypted data systems','industri/icon/law-enforcement.png','','#1d478c','#3f80e2',4,1),
(5,'Energy','energy','<b>Energy</b>','Smart grid monitoring','industri/icon/energy.png','','#1d478c','#3f80e2',5,1),
(6,'Telecommunication (ICT)','telecom','Telecommunication <b>(ICT)</b>','High-speed cloud network','industri/icon/telecom.png','','#1d478c','#3f80e2',6,1),
(7,'Cross Industry','cross-industry','Cross <b>Industry</b>','Custom IT solutions','industri/icon/cross-industry.png','','#1d478c','#3f80e2',7,1);

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
  `detail` mediumtext DEFAULT NULL,              -- rich body for the "See More" popup
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

-- "See More" popup detail per solution (source: Our Solutions PDF). See also database/migration_solutions_detail.sql.
UPDATE `solutions_section` SET `detail`='<p class="sm-tag">Build a Resilient Foundation for the Digital Enterprise</p><p>Modern businesses require infrastructure that can continuously adapt to changing workloads, increasing data volumes, evolving applications, and growing security requirements.</p><p>Sapta Tunas Teknologi helps organizations modernize their technology infrastructure by designing and implementing high-availability, high-performance, scalable, and resilient environments across data centers, private cloud, hybrid cloud, networking, compute, storage, and backup systems. Our approach goes beyond technology refresh: we simplify operations, improve utilization, reduce complexity, and establish a stronger foundation for digital transformation.</p><h4>Our Capabilities</h4><ul><li>Data Center Modernization</li><li>Compute &amp; Virtualization Infrastructure</li><li>Enterprise Storage &amp; Software-Defined Storage</li><li>Private &amp; Hybrid Cloud Infrastructure</li><li>Enterprise Networking &amp; Software-Defined Networking</li><li>Hyperconverged Infrastructure</li><li>Backup, Disaster Recovery &amp; Business Continuity</li><li>High Availability &amp; Multi-Site Architecture</li><li>Infrastructure Automation &amp; Orchestration</li><li>Infrastructure Monitoring &amp; Performance Optimization</li></ul>' WHERE `judul` LIKE '%Modernize%';
UPDATE `solutions_section` SET `detail`='<p class="sm-tag">Protect Your Business in an Increasingly Complex Digital Environment</p><p>Cyber threats are no longer only an IT concern. They represent direct risks to business continuity, customer trust, regulatory compliance, operational stability, and corporate reputation.</p><p>Sapta Tunas Teknologi delivers a comprehensive cybersecurity approach designed to protect organizations across users, devices, applications, networks, cloud environments, workloads, and data. Our security architecture combines preventive, detective, and responsive capabilities while applying modern principles such as Zero Trust, identity-centric security, segmentation, continuous monitoring, and threat intelligence.</p><h4>Our Capabilities</h4><ul><li>Next-Generation Firewall &amp; Network Security</li><li>Zero Trust Security Architecture</li><li>Secure Access Service Edge (SASE)</li><li>Identity &amp; Access Management</li><li>Endpoint Detection &amp; Response (EDR/XDR)</li><li>Network Detection &amp; Response (NDR)</li><li>Security Information &amp; Event Management (SIEM)</li><li>Security Operations Center (SOC)</li><li>Email, Web &amp; Application Security</li><li>Data Protection &amp; Data Loss Prevention</li><li>Vulnerability Assessment &amp; Penetration Testing</li><li>Digital Forensics &amp; Incident Response</li><li>Cybersecurity Assessment &amp; Hardening</li><li>Cyber Drill &amp; Tabletop Exercise</li></ul>' WHERE `judul` LIKE '%yber%ecurity%';
UPDATE `solutions_section` SET `detail`='<p class="sm-tag">Turn Enterprise Data into a Trusted Business Asset</p><p>Organizations generate massive volumes of data across applications, infrastructure, users, machines, and digital services. Without the right strategy and architecture, that data can quickly become fragmented, difficult to manage, expensive to store, and vulnerable to loss.</p><p>Sapta Tunas Teknologi helps organizations manage the entire data lifecycle, from creation and storage to protection, governance, cleansing, integration, analytics, and long-term retention. We design data environments that maintain availability, integrity, security, scalability, and accessibility while preparing enterprise data for analytics and AI-driven use cases.</p><h4>Our Capabilities</h4><ul><li>Data Lifecycle Management</li><li>Data Integration &amp; Data Pipeline</li><li>Data Lakehouse &amp; Data Platform</li><li>Analytics Infrastructure</li><li>AI-Ready Data Architecture</li></ul>' WHERE `judul` LIKE '%<b>Data</b>%';
UPDATE `solutions_section` SET `detail`='<p class="sm-tag">Transform Data into Intelligence and Business Impact</p><p>Artificial Intelligence is transforming how organizations operate, make decisions, engage customers, manage risk, and create new business opportunities.</p><p>Sapta Tunas Teknologi helps enterprises move beyond AI experimentation toward practical, scalable, and business-oriented AI adoption. We combine enterprise infrastructure, accelerated computing, data platforms, AI models, and industry-specific expertise to develop AI solutions that address real operational and business challenges.</p><h4>Our Capabilities</h4><ul><li>Enterprise AI Infrastructure</li><li>Generative AI</li><li>Large Language Models (LLM)</li><li>Enterprise Knowledge AI &amp; Retrieval-Augmented Generation</li><li>Computer Vision &amp; Video Analytics</li><li>AI Model Development &amp; Integration</li></ul><h4>Industry AI Use Cases</h4><ul><li>Smart City &amp; Public Safety</li><li>Healthcare &amp; Medical Imaging</li><li>Manufacturing &amp; Industrial Operations</li><li>Agriculture</li><li>Media &amp; Broadcasting (AI Avatar &amp; Video Generation)</li></ul>' WHERE `judul` LIKE '%<b>AI</b>%';
UPDATE `solutions_section` SET `detail`='<p class="sm-tag">Turn AI Capabilities into Real Business Applications</p><p>AI delivers real value when it becomes part of everyday business operations.</p><p>Sapta Tunas Teknologi develops AI platforms and intelligent applications that embed artificial intelligence directly into enterprise workflows, customer experiences, operational processes, and decision-making systems. Rather than implementing isolated AI tools, we help organizations build an AI application ecosystem that can continuously evolve as business requirements change.</p><h4>Our Capabilities</h4><ul><li>Enterprise AI Platform</li><li>AI Application Development</li><li>Enterprise AI Assistant &amp; Copilot</li><li>AI Agent &amp; Agentic Workflow</li><li>AI Digital Human &amp; Virtual Assistant</li><li>AI Content Generation Platform</li><li>AI Integration with Enterprise Applications</li></ul>' WHERE `judul` LIKE '%AI Platform%';

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

-- ===== Industry x Pillar seed (from client PDF, 7 industries x 4 pillars) =====
INSERT INTO `industri_pilar` (`industri_id`,`pilar_id`,`heading`,`konten`,`fitur`,`urutan`) VALUES
(1,1,'Infrastruktur Digital Always-On untuk FSI dan E-Commerce','<p>Bangun fondasi digital yang tangguh, aman, dan siap tumbuh bersama kebutuhan bisnis Anda melalui solusi High-Availability Data Center dan Hybrid Cloud Architecture yang dirancang khusus untuk industri Financial Services Industry (FSI) dan e-commerce.</p><p>Solusi ini menghadirkan infrastruktur yang scalable, secure, resilient, dan compliant terhadap kebutuhan regulasi seperti OJK dan Bank Indonesia, sehingga bisnis dapat menjalankan layanan digital kritikal dengan tingkat ketersediaan, performa, dan keamanan yang optimal.</p><p>Dengan arsitektur yang dirancang untuk mendukung transaksi finansial dan digital commerce berskala besar, perusahaan dapat memproses jutaan transaksi secara real-time dengan latency rendah, performa stabil, serta kapasitas yang fleksibel mengikuti pertumbuhan trafik dan volume bisnis.</p><p>Kami membantu memastikan kontinuitas layanan melalui desain infrastruktur berlapis, mekanisme recovery, monitoring proaktif, serta pengelolaan operasional yang terstruktur untuk meminimalkan risiko downtime dan menjaga layanan tetap berjalan dalam berbagai kondisi bisnis.</p><p>failover, disaster Dengan dukungan infrastruktur digital yang always-on, bisnis Anda dapat menghadirkan pengalaman pengguna yang cepat, stabil, aman, dan terpercaya - mulai dari transaksi perbankan, pembayaran digital, marketplace, loyalty platform, hingga layanan commerce berskala enterprise.</p>','[{"icon": "industri/feat/modernize-infrastructure-1.png", "judul": "High-Availability Data Center", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-2.png", "judul": "Hybrid Cloud Architecture", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-3.png", "judul": "Scalable, secure, resilient", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-4.png", "judul": "Compliance Governance Infrastructure", "teks": ""}]',1),
(1,2,'Hadirkan AI Avatar yang Siap Melayani, Menjual, dan Berinteraksi 24/7','<p>Tingkatkan pengalaman nasabah dan pelanggan dengan solusi Generative AI Avatar yang mampu berinteraksi secara natural, personal, dan real-time. Dengan menggabungkan solusi Large Language Models (LLM), data management, dan otomasi cerdas, solusi ini membantu bisnis menghadirkan layanan digital yang lebih cepat, konsisten, dan relevan di setiap interaksi.</p><p>Di sisi keamanan fisik, solusi ini diperkuat dengan AI Surveillance Camera yang mampu memantau area perbankan, kantor cabang, ATM, gudang, dan area operasional penting secara proaktif. Teknologi pengawasan cerdas ini dapat membantu mendeteksi aktivitas mencurigakan, pola perilaku, potensi akses tidak sah, hingga insiden keamanan secara real-time, sehingga risiko dapat diidentifikasi dan ditangani lebih cepat.</p><p>Bagi industri Financial Services, AI Avatar dapat menjadi konsultan virtual yang membantu menjelaskan produk, menjawab kebutuhan nasabah, dan meningkatkan kualitas layanan tanpa batasan jam operasional.</p><p>Bagi industri E-Commerce, AI Avatar dapat berperan sebagai host Live Commerce yang melakukan siaran langsung secara mandiri, mempromosikan produk, menjawab pertanyaan pelanggan di chat, memberikan rekomendasi, dan membantu mendorong transaksi secara real-time.</p><p>Dengan solusi ini, perusahaan dapat meningkatkan engagement, mempercepat layanan, memperoleh insight bisnis yang lebih akurat, serta mendorong konversi penjualan dan loyalitas pelanggan melalui pengalaman digital yang lebih interaktif.</p>','[{"icon": "industri/feat/data-ai-1.png", "judul": "Generative AI", "teks": ""}, {"icon": "industri/feat/data-ai-2.png", "judul": "Private LLM", "teks": ""}, {"icon": "industri/feat/data-ai-3.png", "judul": "Analytics Surveillance Camera", "teks": ""}, {"icon": "industri/feat/data-ai-4.png", "judul": "AI Fraud Detection", "teks": ""}, {"icon": "industri/feat/data-ai-5.png", "judul": "AI Automation Report", "teks": ""}, {"icon": "industri/feat/data-ai-6.png", "judul": "AI Automation Operations", "teks": ""}, {"icon": "industri/feat/data-ai-7.png", "judul": "CX With AI Agent", "teks": ""}]',2),
(1,3,'Keamanan Siber Berlapis untuk Melindungi Transaksi, Data, dan Reputasi Bisnis Digital Anda','<p>Di industri FSI dan e-commerce, setiap detik downtime, kebocoran data, serangan siber, atau langsung pada revenue, kepatuhan, dan penyalahgunaan brand dapat berdampak kepercayaan pelanggan. Karena itu, kami menghadirkan solusi cybersecurity terintegrasi yang menggabungkan teknologi deteksi, proteksi, monitoring, threat intelligence, dan otomatisasi respons dalam satu ekosistem keamanan yang menyeluruh.</p><p>Dengan dukungan EDR, NDR, XDR, Next-Generation Firewall, WAF, SIEM terpusat, dan SOAR berbasis AI, bisnis Anda dapat mendeteksi ancaman lebih cepat, merespons insiden secara lebih presisi, serta mengurangi risiko gangguan pada layanan digital utama.</p><p>Kami juga menyediakan Threat Intelligence proaktif untuk memantau potensi kebocoran data, phishing, fraud, penyalahgunaan identitas brand, konten hoax, serta penyebaran data sensitif di internet dan media sosial. Jika ditemukan ancaman yang berisiko terhadap bisnis, kami membantu proses eskalasi dan take-down untuk meminimalkan dampak terhadap reputasi dan kepercayaan publik.</p><p>Solusi ini dirancang untuk membantu organisasi menjaga keamanan transaksi, perlindungan data, kepatuhan, stabilitas operasional, dan ketahanan bisnis digital secara berkelanjutan.</p>','[{"icon": "industri/feat/cybersecurity-1.png", "judul": "EDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-2.png", "judul": "NDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-3.png", "judul": "XDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-4.png", "judul": "Next-Generation Firewall", "teks": ""}, {"icon": "industri/feat/cybersecurity-5.png", "judul": "WAF", "teks": ""}, {"icon": "industri/feat/cybersecurity-6.png", "judul": "SIEM", "teks": ""}, {"icon": "industri/feat/cybersecurity-7.png", "judul": "SOAR", "teks": ""}, {"icon": "industri/feat/cybersecurity-8.png", "judul": "Threat Intelligence", "teks": ""}]',3),
(1,4,'Managed Service IT & Cybersecurity untuk Bisnis yang Tidak Boleh Berhenti','<p>Pastikan operasional digital Anda berjalan aman, stabil, dan selalu siap menghadapi ancaman dengan layanan Managed Service STT. Kami tidak hanya melakukan monitoring infrastruktur dan keamanan secara proaktif 24/7/365, tetapi juga membantu memperkuat ketahanan sistem melalui layanan advanced seperti Vulnerability Assessment & Penetration Testing (VAPT), Digital Forensic, Cyber Drill, dan Table Top Exercise.</p><p>Dengan dukungan patch management berkala, pengelolaan operasional IT menyeluruh, serta respons cepat terhadap potensi gangguan dan serangan siber, bisnis Anda dapat menjaga performa tinggi, keamanan berkelanjutan, dan kontinuitas layanan di setiap transaksi digital.</p><p>Hasilnya: sistem lebih tangguh, risiko lebih terkendali, tim internal lebih fokus pada bisnis inti, dan pelanggan tetap mendapatkan pengalaman layanan yang cepat, aman, dan tanpa hambatan.</p>','[{"icon": "industri/feat/managed-services-1.png", "judul": "VAPT", "teks": ""}, {"icon": "industri/feat/managed-services-2.png", "judul": "Digital Forensic", "teks": ""}, {"icon": "industri/feat/managed-services-3.png", "judul": "Cyber Drill", "teks": ""}, {"icon": "industri/feat/managed-services-4.png", "judul": "Table Top Exercise", "teks": ""}, {"icon": "industri/feat/managed-services-5.png", "judul": "24/7/365", "teks": ""}, {"icon": "industri/feat/managed-services-6.png", "judul": "Patch management", "teks": ""}]',4),
(2,1,'Percepat Produksi, Hubungkan Seluruh Site, dan Ambil Keputusan Berbasis Data Real-Time','<p>Dalam industri manufaktur dan FMCG, keterlambatan data, gangguan konektivitas, dan kurangnya visibilitas antar-site dapat langsung berdampak pada produktivitas, kualitas produksi, dan kecepatan distribusi. Karena itu, kami menghadirkan solusi infrastruktur IT modern berbasis Edge Computing dan SD-WAN untuk menciptakan operasional yang lebih cepat, stabil, aman, dan terintegrasi.</p><p>Dengan Edge Computing, data dari mesin produksi, sensor, sistem quality control, dan aplikasi pabrik dapat diproses secara real-time langsung di area operasional tanpa bergantung penuh pada koneksi ke pusat data. Hasilnya, perusahaan dapat merespons kondisi produksi lebih cepat, mengurangi latensi, dan mendukung otomasi proses yang lebih presisi.</p><p>Melalui SD-WAN, kantor pusat, pabrik, gudang logistik, dan titik distribusi dapat terhubung dalam jaringan yang lebih seamless, efisien, dan aman. Komunikasi data antar-site menjadi lebih stabil untuk mendukung koordinasi produksi, inventory, supply chain, distribusi, dan monitoring operasional secara menyeluruh.</p><p>Dengan visibilitas data yang lebih baik dan konektivitas yang andal, perusahaan dapat meningkatkan efisiensi produksi, mempercepat pengambilan keputusan, mengurangi risiko downtime, serta membangun rantai operasional yang lebih adaptif terhadap perubahan permintaan pasar.</p>','[{"icon": "industri/feat/modernize-infrastructure-1.png", "judul": "High-Availability Data Center", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-2.png", "judul": "Hybrid Cloud Architecture", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-3.png", "judul": "Scalable, secure, resilient", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-4.png", "judul": "Compliance Governance Infrastructure", "teks": ""}]',1),
(2,2,'Kurangi Defect, Tingkatkan Safety, dan Amankan Area Produksi dengan Computer Vision','<p>Dalam industri manufaktur dan FMCG, kualitas produk, keselamatan kerja, dan keamanan area produksi tidak boleh bergantung pada pengawasan manual semata. Dibutuhkan sistem cerdas yang mampu memantau, mendeteksi, dan memberikan insight secara real-time agar operasional berjalan lebih presisi, aman, dan konsisten.</p><p>Kami menghadirkan solusi Computer Vision untuk membantu perusahaan melakukan kontrol kualitas otomatis, mendeteksi defect produksi, dan memastikan standar produk tetap terjaga di setiap proses. Sistem dapat mengidentifikasi anomali pada produk, kemasan, label, bentuk, warna, maupun parameter visual lainnya untuk mendukung proses quality control yang lebih cepat dan akurat.</p><p>Di sisi keselamatan kerja, AI Surveillance Camera membantu memantau kepatuhan penggunaan Alat Pelindung Diri (APD) secara real-time, sehingga perusahaan dapat memperkuat penerapan standar K3 dan mengurangi risiko kecelakaan kerja di area operasional.</p><p>Untuk area yang membutuhkan perlindungan lebih ketat, Computer Vision Smart Access membantu mengontrol akses ke ruang produksi, gudang, laboratorium, dan restricted area lainnya agar hanya personel berwenang yang dapat masuk.</p><p>Dengan solusi ini, perusahaan dapat meningkatkan konsistensi kualitas, mempercepat proses inspeksi, memperkuat budaya keselamatan, serta menjaga keamanan operasional di seluruh area produksi dan logistik.</p>','[{"icon": "industri/feat/data-ai-1.png", "judul": "Generative AI", "teks": ""}, {"icon": "industri/feat/data-ai-2.png", "judul": "Private LLM", "teks": ""}, {"icon": "industri/feat/data-ai-3.png", "judul": "Analytics Surveillance Camera", "teks": ""}, {"icon": "industri/feat/data-ai-4.png", "judul": "AI Fraud Detection", "teks": ""}, {"icon": "industri/feat/data-ai-5.png", "judul": "AI Automation Report", "teks": ""}, {"icon": "industri/feat/data-ai-6.png", "judul": "AI Automation Operations", "teks": ""}, {"icon": "industri/feat/data-ai-7.png", "judul": "CX With AI Agent", "teks": ""}]',2),
(2,3,'Cybersecurity Terintegrasi untuk Melindungi IT, OT/IoT, dan Operasional Manufaktur','<p>Amankan ekosistem manufaktur dan FMCG Anda dengan solusi cybersecurity menyeluruh yang menyatukan perlindungan IT, Operational Technology (OT), dan IoT dalam satu pendekatan keamanan yang terintegrasi. Solusi ini dirancang untuk menjaga kontinuitas operasional pabrik, melindungi data produksi, serta mengurangi risiko serangan siber yang dapat mengganggu rantai pasok, sistem produksi, dan reputasi brand.</p><p>Kami menghadirkan pertahanan berlapis melalui teknologi EDR, NDR, XDR, Next-Generation Firewall, WAF, SIEM terpusat, dan SOAR berbasis AI untuk memberikan visibilitas menyeluruh terhadap aktivitas endpoint, jaringan, aplikasi, server, hingga lingkungan operasional pabrik. Dengan kemampuan deteksi ancaman yang presisi dan otomatisasi respons insiden, perusahaan dapat mengidentifikasi risiko lebih cepat, mempercepat penanganan insiden, dan meminimalkan dampak terhadap operasional.</p><p>Solusi ini juga didukung Threat Intelligence yang secara proaktif memantau potensi kebocoran data rahasia perusahaan, termasuk formula produk, dokumen produksi, data riset, kredensial, informasi rantai pasok, serta data sensitif lain yang tersebar di internet, dark web, maupun media sosial. Selain itu, kami membantu mendeteksi ancaman phishing, penyalahgunaan identitas brand, dan potensi serangan terhadap ekosistem supplier maupun partner bisnis.</p><p>Jika ditemukan risiko yang dapat berdampak pada bisnis, kami menyediakan dukungan eskalasi dan proses take-down untuk membantu menekan penyebaran data sensitif, melindungi kekayaan intelektual, menjaga integritas data produksi, serta mempertahankan kepercayaan pelanggan dan mitra bisnis.</p><p>terintegrasi, proaktif, dan berorientasi pada Dengan pendekatan keamanan yang keberlanjutan operasional, perusahaan manufaktur dan FMCG dapat menjalankan proses produksi dengan lebih aman, stabil, dan terlindungi dari ancaman siber modern.</p>','[{"icon": "industri/feat/cybersecurity-1.png", "judul": "EDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-2.png", "judul": "NDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-3.png", "judul": "XDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-4.png", "judul": "Next-Generation Firewall", "teks": ""}, {"icon": "industri/feat/cybersecurity-5.png", "judul": "WAF", "teks": ""}, {"icon": "industri/feat/cybersecurity-6.png", "judul": "SIEM", "teks": ""}, {"icon": "industri/feat/cybersecurity-7.png", "judul": "SOAR", "teks": ""}, {"icon": "industri/feat/cybersecurity-8.png", "judul": "Threat Intelligence", "teks": ""}]',3),
(2,4,'Managed Services untuk Operasional Manufaktur dan FMCG yang Stabil, Aman, dan Selalu Berjalan','<p>Pastikan operasional manufaktur dan FMCG tetap berjalan tanpa henti melalui layanan Managed Services 24/7 yang dirancang untuk menjaga stabilitas infrastruktur IT, jaringan pabrik, sistem produksi, dan ekosistem digital pendukung rantai operasional.</p><p>Kami menyediakan monitoring perangkat jaringan pabrik secara remote dan proaktif untuk membantu mendeteksi potensi gangguan lebih awal, mempercepat penanganan insiden, serta memastikan konektivitas antar-area produksi, gudang, kantor pusat, dan site operasional tetap stabil.</p><p>Layanan ini mencakup pengelolaan operasional IT end-to-end, mulai dari monitoring performa sistem, manajemen kapasitas penyimpanan data, pemeliharaan infrastruktur, hingga backup & disaster recovery untuk menjaga ketersediaan data dan memastikan kelangsungan operasional saat terjadi gangguan, kegagalan sistem, atau insiden tidak terduga.</p><p>Untuk memperkuat ketahanan keamanan, kami juga menghadirkan Managed Security Services yang mencakup Vulnerability Assessment & Penetration Testing (VAPT), Digital Forensic, serta Cyber Drill dan Table Top Exercise. Pendekatan ini membantu perusahaan mengidentifikasi celah keamanan, memahami dampak insiden, menguji kesiapan respons, dan memperkuat mitigasi risiko siber secara berkelanjutan.</p><p>Dengan dukungan Managed Services yang terintegrasi, perusahaan dapat menjaga stabilitas produksi, meningkatkan efisiensi operasional, melindungi data kritikal, dan meminimalkan risiko disruption di seluruh rantai produksi.</p>','[{"icon": "industri/feat/managed-services-1.png", "judul": "VAPT", "teks": ""}, {"icon": "industri/feat/managed-services-2.png", "judul": "Digital Forensic", "teks": ""}, {"icon": "industri/feat/managed-services-3.png", "judul": "Cyber Drill", "teks": ""}, {"icon": "industri/feat/managed-services-4.png", "judul": "Table Top Exercise", "teks": ""}, {"icon": "industri/feat/managed-services-5.png", "judul": "24/7/365", "teks": ""}, {"icon": "industri/feat/managed-services-6.png", "judul": "Patch management", "teks": ""}]',4),
(3,1,'Infrastruktur Digital Kesehatan untuk Layanan Medis yang Cepat, Aman, dan Selalu Tersedia','<p>Bangun fondasi IT rumah sakit yang andal, aman, dan siap berkembang untuk mendukung layanan kesehatan modern yang semakin bergantung pada data, konektivitas, dan kecepatan akses informasi klinis.</p><p>Solusi kami menghadirkan sistem penyimpanan data medis berkapasitas besar dan berperforma tinggi untuk mendukung kebutuhan medical imaging, radiologi, dan Picture Archiving and Communication System (PACS). Dengan performa akses data yang cepat dan stabil, tenaga medis dapat membuka, menyimpan, dan menganalisis hasil pemeriksaan radiologi secara lebih efisien dan presisi.</p><p>Didukung jaringan rumah sakit yang stabil, redundant, dan always-on, solusi ini memastikan sistem penting seperti Electronic Medical Record (EMR), e-prescribing, sistem administrasi rumah sakit, laboratorium, farmasi, dan layanan klinis digital dapat berjalan tanpa gangguan berarti.</p><p>Dengan arsitektur yang scalable, secure, dan high availability, rumah sakit dapat meningkatkan kecepatan layanan, menjaga ketersediaan data pasien, memperkuat kontinuitas operasional, serta menghadirkan pengalaman layanan kesehatan yang lebih cepat, akurat, dan terpercaya bagi pasien maupun tenaga medis.</p>','[{"icon": "industri/feat/modernize-infrastructure-1.png", "judul": "High-Availability Data Center", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-2.png", "judul": "Hybrid Cloud Architecture", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-3.png", "judul": "Scalable, secure, resilient", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-4.png", "judul": "Compliance Governance Infrastructure", "teks": ""}]',1),
(3,2,'Percepat Diagnosis, Optimalkan Alur Klinis, dan Hadirkan Pengalaman Pasien yang Lebih Cerdas','<p>Dalam layanan kesehatan modern, kecepatan akses data, ketepatan analisis medis, dan pengalaman pasien yang terintegrasi menjadi faktor penting dalam meningkatkan kualitas layanan. Kami menghadirkan solusi Data & AI untuk Healthcare yang membantu rumah sakit dan institusi kesehatan mengoptimalkan proses klinis dari tahap awal konsultasi hingga perawatan lanjutan.</p><p>Dengan teknologi Computer Vision untuk radiologi dan patologi, AI membantu meningkatkan kualitas citra medis, termasuk optimalisasi citra MRI (1.5T → 3T), mendukung analisis CT-Scan serta patologi digital. Solusi ini dirancang untuk mempercepat workflow diagnostik, meningkatkan konsistensi pembacaan, dan membantu tenaga medis dalam pengambilan keputusan klinis berbasis data.</p><p>Melalui Smart Patient Assist, pengalaman pasien menjadi lebih terarah dan personal. Sistem triage cerdas membantu mengidentifikasi kebutuhan pasien sejak awal, merekomendasikan spesialis yang tepat, dan mengotomatisasi proses booking. Selama konsultasi, AI mendukung analisis data medis dan ringkasan informasi pasien secara real-time. Setelah konsultasi, sistem membantu follow-up, pengingat obat, jadwal kontrol, dan komunikasi lanjutan secara lebih konsisten.</p><p>Dengan pendekatan end-to-end, solusi ini membantu institusi kesehatan meningkatkan efisiensi operasional, mempercepat layanan, mendukung akurasi klinis, serta menciptakan pengalaman pasien yang lebih nyaman, personal, dan berkelanjutan.</p>','[{"icon": "industri/feat/data-ai-1.png", "judul": "Generative AI", "teks": ""}, {"icon": "industri/feat/data-ai-2.png", "judul": "Private LLM", "teks": ""}, {"icon": "industri/feat/data-ai-3.png", "judul": "Analytics Surveillance Camera", "teks": ""}, {"icon": "industri/feat/data-ai-4.png", "judul": "AI Fraud Detection", "teks": ""}, {"icon": "industri/feat/data-ai-5.png", "judul": "AI Automation Report", "teks": ""}, {"icon": "industri/feat/data-ai-6.png", "judul": "AI Automation Operations", "teks": ""}, {"icon": "industri/feat/data-ai-7.png", "judul": "CX With AI Agent", "teks": ""}]',2),
(3,3,'Lindungi Data Pasien, Sistem Rumah Sakit, dan Perangkat Medis dari Ancaman Siber','<p>Dalam layanan kesehatan, serangan siber bukan sekadar gangguan teknologi. Ransomware, kebocoran data pasien, akses ilegal, atau kompromi pada perangkat Medical IoT/IoMT dapat layanan klinis, mengganggu operasional rumah sakit, dan merusak menghambat kepercayaan pasien.</p><p>Kami menghadirkan solusi cybersecurity terintegrasi untuk melindungi ekosistem layanan kesehatan secara menyeluruh, mulai dari infrastruktur IT, aplikasi klinis, jaringan rumah sakit, hingga perangkat medis terhubung. Dengan kombinasi EDR, NDR, XDR, Next-Generation Firewall, WAF, SIEM terpusat, SOAR berbasis AI, dan Threat Intelligence, rumah sakit mendapatkan visibilitas yang lebih luas, deteksi ancaman yang lebih presisi, serta respons insiden yang lebih cepat dan terkoordinasi.</p><p>Melalui Threat Intelligence proaktif, kami membantu memantau potensi kebocoran data pasien, ancaman ransomware, pencurian kredensial, penyebaran data sensitif, serta penyalahgunaan informasi rumah sakit di internet maupun media sosial. Jika ditemukan risiko yang dapat berdampak pada privasi pasien atau reputasi institusi, kami mendukung proses eskalasi dan take-down untuk membantu meminimalkan dampak.</p><p>Dengan pendekatan keamanan yang menyatukan IT dan Medical IoT/IoMT, institusi integritas sistem medis, kesehatan dapat menjaga privasi data pasien, melindungi memastikan layanan klinis tetap berjalan, dan memperkuat kepercayaan publik terhadap rumah sakit.</p>','[{"icon": "industri/feat/cybersecurity-1.png", "judul": "EDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-2.png", "judul": "NDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-3.png", "judul": "XDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-4.png", "judul": "Next-Generation Firewall", "teks": ""}, {"icon": "industri/feat/cybersecurity-5.png", "judul": "WAF", "teks": ""}, {"icon": "industri/feat/cybersecurity-6.png", "judul": "SIEM", "teks": ""}, {"icon": "industri/feat/cybersecurity-7.png", "judul": "SOAR", "teks": ""}, {"icon": "industri/feat/cybersecurity-8.png", "judul": "Threat Intelligence", "teks": ""}]',3),
(3,4,'Managed Services untuk Operasional Rumah Sakit yang Stabil, Aman, dan Selalu Siap Melayani','<p>Pastikan operasional rumah sakit dan layanan kesehatan tetap berjalan optimal melalui Managed Services 24/7 yang dirancang untuk menjaga ketersediaan, performa, dan keamanan infrastruktur IT serta sistem kritis layanan pasien.</p><p>Kami menyediakan monitoring remote dan proaktif terhadap infrastruktur IT rumah sakit, jaringan, server, sistem penyimpanan data medis, aplikasi klinis, serta platform pendukung seperti EMR, PACS, e-prescribing, sistem laboratorium, farmasi, dan administrasi pasien. Dengan pemantauan berkelanjutan, potensi gangguan dapat terdeteksi lebih awal dan ditangani sebelum berdampak pada layanan klinis.</p><p>Layanan ini mencakup pengelolaan operasional IT end-to-end, termasuk manajemen kapasitas penyimpanan data medis, pemeliharaan performa sistem, backup berkala, serta disaster recovery untuk memastikan data penting tetap tersedia dan layanan pasien dapat dipulihkan dengan cepat saat terjadi insiden, kegagalan sistem, atau kondisi darurat.</p><p>Untuk memperkuat ketahanan keamanan, kami juga menghadirkan Managed Security Services yang mencakup Vulnerability Assessment & Penetration Testing (VAPT), Digital Forensic, serta Cyber Drill dan Table Top Exercise. Pendekatan ini membantu rumah sakit mengidentifikasi celah keamanan, menganalisis insiden, menguji kesiapan respons, dan memitigasi risiko siber yang menargetkan data sensitif pasien maupun sistem layanan kesehatan.</p><p>Dengan dukungan Managed Services yang terintegrasi, institusi kesehatan dapat menjaga stabilitas operasional, melindungi data pasien, mengurangi beban tim internal, serta memastikan layanan medis tetap berjalan aman, efisien, dan minim gangguan.</p>','[{"icon": "industri/feat/managed-services-1.png", "judul": "VAPT", "teks": ""}, {"icon": "industri/feat/managed-services-2.png", "judul": "Digital Forensic", "teks": ""}, {"icon": "industri/feat/managed-services-3.png", "judul": "Cyber Drill", "teks": ""}, {"icon": "industri/feat/managed-services-4.png", "judul": "Table Top Exercise", "teks": ""}, {"icon": "industri/feat/managed-services-5.png", "judul": "24/7/365", "teks": ""}, {"icon": "industri/feat/managed-services-6.png", "judul": "Patch management", "teks": ""}]',4),
(6,1,'Infrastruktur Hyperscale Data Center untuk Ekosistem Telekomunikasi dan ICT yang Siap Tumbuh','<p>industri Bangun pondasi digital berskala besar untuk mendukung pertumbuhan telekomunikasi dan ICT melalui infrastruktur Hyperscale Data Center yang dirancang dengan ketersediaan tinggi, performa optimal, keamanan berlapis, dan skalabilitas yang siap mengikuti peningkatan trafik, data, serta layanan digital.</p><p>Solusi ini memanfaatkan Edge Computing untuk mendukung kebutuhan jaringan modern seperti 5G, aplikasi low-latency, IoT, video streaming, layanan enterprise, dan digital service berskala besar. Dengan pemrosesan data yang lebih dekat ke pengguna dan titik layanan, operator dapat meningkatkan kecepatan respons, mengurangi latensi, serta menghadirkan pengalaman digital yang lebih stabil dan konsisten.</p><p>Melalui implementasi Software-Defined Networking (SDN) dan SD-WAN, pengelolaan jaringan menjadi lebih agile, efisien, dan terpusat. Infrastruktur dapat dikonfigurasi, dioptimalkan, dan diskalakan dengan lebih cepat untuk mendukung kebutuhan operasional jaringan, interkoneksi data center, konektivitas enterprise, serta ekspansi layanan di berbagai wilayah.</p><p>Didukung arsitektur Hybrid Cloud, High Availability, dan Disaster Recovery, solusi ini membantu menjaga ketersediaan layanan kritikal, melindungi data dan aplikasi penting, serta mempercepat pemulihan operasional saat terjadi gangguan. Dengan desain yang resilient dan secure, perusahaan telekomunikasi dan penyedia layanan ICT dapat menjaga performa layanan, meningkatkan efisiensi operasional, dan membangun ekosistem digital yang lebih siap menghadapi pertumbuhan masa depan.</p>','[{"icon": "industri/feat/modernize-infrastructure-1.png", "judul": "High-Availability Data Center", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-2.png", "judul": "Hybrid Cloud Architecture", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-3.png", "judul": "Scalable, secure, resilient", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-4.png", "judul": "Compliance Governance Infrastructure", "teks": ""}]',1),
(6,2,'GenAI Avatar untuk Layanan Pelanggan Telekomunikasi yang Lebih Personal, Responsif, dan Selalu Aktif','<p>Tingkatkan pengalaman pelanggan digital melalui solusi GenAI Avatar yang didukung oleh Large Language Models (LLM) untuk menghadirkan interaksi yang lebih natural, personal, dan responsif di seluruh kanal layanan telekomunikasi.</p><p>Solusi ini membantu perusahaan menangani pertanyaan, keluhan, dan kebutuhan pelanggan secara 24/7, mulai dari informasi paket layanan, pengecekan tagihan, troubleshooting koneksi, aktivasi layanan, panduan penggunaan aplikasi, hingga rekomendasi produk yang lebih relevan berdasarkan kebutuhan pengguna.</p><p>Dengan kemampuan memahami konteks percakapan dan memberikan respons yang cepat, konsisten, dan empatik, GenAI Avatar membantu mempercepat penyelesaian masalah pelanggan sekaligus mengurangi beban contact center. Pelanggan dapat memperoleh bantuan secara instan tanpa harus menunggu antrean layanan manual.</p><p>Dilengkapi integrasi data dan sistem operasional, solusi ini dapat mendukung layanan yang lebih akurat, personal, dan terukur. Setiap interaksi pelanggan dapat diolah menjadi insight untuk memahami kebutuhan pengguna, meningkatkan kualitas layanan, serta menciptakan pengalaman digital yang lebih seamless.</p><p>Dengan GenAI Avatar, perusahaan telekomunikasi dapat meningkatkan kepuasan pelanggan, memperkuat loyalitas pengguna, mengoptimalkan efisiensi operasional, dan menghadirkan layanan digital yang lebih modern, cepat, dan kompetitif.</p>','[{"icon": "industri/feat/data-ai-1.png", "judul": "Generative AI", "teks": ""}, {"icon": "industri/feat/data-ai-2.png", "judul": "Private LLM", "teks": ""}, {"icon": "industri/feat/data-ai-3.png", "judul": "Analytics Surveillance Camera", "teks": ""}, {"icon": "industri/feat/data-ai-4.png", "judul": "AI Fraud Detection", "teks": ""}, {"icon": "industri/feat/data-ai-5.png", "judul": "AI Automation Report", "teks": ""}, {"icon": "industri/feat/data-ai-6.png", "judul": "AI Automation Operations", "teks": ""}, {"icon": "industri/feat/data-ai-7.png", "judul": "CX With AI Agent", "teks": ""}]',2),
(6,3,'Lindungi Jaringan 4G/5G dari DDoS, Fraud, dan Ancaman Siber Berskala Besar','<p>Dalam industri telekomunikasi, gangguan jaringan bukan hanya masalah teknis. Serangan DDoS, fraud digital, penyalahgunaan akun, dan kompromi sistem dapat langsung berdampak pada kualitas layanan, kepercayaan pelanggan, serta reputasi operator.</p><p>solusi cybersecurity Kami menghadirkan jaringan telekomunikasi, layanan digital, sistem pelanggan, dan infrastruktur kritikal dari ancaman siber modern. Dengan kombinasi DDoS Protection, EDR, NDR, XDR, SIEM terpusat, SOAR berbasis AI, dan Threat Intelligence, operator mendapatkan visibilitas yang lebih luas, deteksi ancaman yang lebih presisi, serta respons insiden yang lebih cepat dan terkoordinasi.</p><p>terintegrasi untuk melindungi Perlindungan DDoS membantu menjaga ketersediaan layanan 4G/5G, portal pelanggan, aplikasi digital, dan sistem operasional saat menghadapi lonjakan trafik berbahaya. Sementara itu, SIEM dan SOAR membantu mempercepat korelasi insiden, mengotomatisasi respons, dan mengurangi waktu penanganan ancaman.</p><p>Melalui Threat Intelligence proaktif, operator dapat memantau aktivitas mencurigakan, potensi penyalahgunaan akun, phishing, pencurian kredensial, dan fraud digital yang dapat merugikan pelanggan maupun bisnis. Dengan pendekatan keamanan berlapis, perusahaan telekomunikasi dapat menjaga stabilitas jaringan, melindungi data pelanggan, dan membangun layanan yang lebih aman, andal, serta dipercaya.</p>','[{"icon": "industri/feat/cybersecurity-1.png", "judul": "EDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-2.png", "judul": "NDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-3.png", "judul": "XDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-4.png", "judul": "Next-Generation Firewall", "teks": ""}, {"icon": "industri/feat/cybersecurity-5.png", "judul": "WAF", "teks": ""}, {"icon": "industri/feat/cybersecurity-6.png", "judul": "SIEM", "teks": ""}, {"icon": "industri/feat/cybersecurity-7.png", "judul": "SOAR", "teks": ""}, {"icon": "industri/feat/cybersecurity-8.png", "judul": "Threat Intelligence", "teks": ""}]',3),
(6,4,'Managed Services untuk Jaringan Telekomunikasi yang Stabil, Efisien, dan Siap Menghadapi Lonjakan Trafik','<p>Dukung operasional jaringan telekomunikasi dengan layanan Managed Services yang dirancang untuk menjaga stabilitas, performa, dan ketersediaan sistem secara berkelanjutan. Solusi ini membantu operator dan penyedia layanan ICT mengelola infrastruktur kritikal secara lebih proaktif, efisien, dan terkontrol.</p><p>Kami menyediakan monitoring dan pengelolaan operasional terhadap server, storage, jaringan, sistem pendukung layanan, serta infrastruktur telekomunikasi yang menjadi tulang punggung konektivitas pelanggan. Dengan pemantauan berkelanjutan, potensi gangguan dapat dideteksi lebih awal dan ditangani sebelum berdampak pada kualitas layanan.</p><p>Layanan ini juga mencakup optimasi kapasitas server dan storage secara proaktif untuk mengantisipasi lonjakan trafik, pertumbuhan pelanggan, ekspansi layanan digital, serta peningkatan volume data. Dengan manajemen kapasitas yang lebih terencana, perusahaan dapat menjaga performa layanan tetap optimal tanpa pemborosan sumber daya.</p><p>Untuk memperkuat ketahanan keamanan, kami menghadirkan layanan advanced seperti Vulnerability Assessment & Penetration Testing (VAPT), Digital Forensic, serta Table Top Exercise dan Cyber Drill. Pendekatan ini membantu mengidentifikasi celah keamanan, menganalisis insiden, menguji kesiapan respons, dan memitigasi risiko siber yang dapat mengganggu infrastruktur telekomunikasi.</p><p>telekomunikasi dapat Dengan Managed Services yang meningkatkan efisiensi operasional, menjaga stabilitas jaringan, mempercepat respons terhadap gangguan, serta mendukung pengalaman pelanggan yang lebih andal dan konsisten.</p><p>terintegrasi, perusahaan</p>','[{"icon": "industri/feat/managed-services-1.png", "judul": "VAPT", "teks": ""}, {"icon": "industri/feat/managed-services-2.png", "judul": "Digital Forensic", "teks": ""}, {"icon": "industri/feat/managed-services-3.png", "judul": "Cyber Drill", "teks": ""}, {"icon": "industri/feat/managed-services-4.png", "judul": "Table Top Exercise", "teks": ""}, {"icon": "industri/feat/managed-services-5.png", "judul": "24/7/365", "teks": ""}, {"icon": "industri/feat/managed-services-6.png", "judul": "Patch management", "teks": ""}]',4),
(4,1,'Infrastruktur Andal untuk Operasional yang Mission-Critical','<p>Sistem teknologi pada institusi penegakan hukum merupakan bagian dari mission-critical operation. Aplikasi operasional, database, command center, sistem investigasi, video surveillance, hingga digital evidence membutuhkan infrastruktur yang tersedia secara konsisten, kapan pun dibutuhkan.</p><p>Sapta Tunas Teknologi membangun arsitektur infrastruktur yang mengutamakan availability, scalability, performance, connectivity, serta business continuity.</p><p>Data Center yang Aman dan Berketersediaan Tinggi Data center menjadi pusat dari seluruh aplikasi dan data operasional. Infrastruktur harus mampu menangani pertumbuhan volume data sekaligus memastikan sistem kritikal tetap berjalan, bahkan ketika terjadi kegagalan pada salah satu komponen.</p><p>Sapta Tunas Teknologi membangun arsitektur compute, server, enterprise storage, network, dan data protection dengan pendekatan high availability — sehingga workload penting tidak bergantung pada satu perangkat atau satu titik kegagalan.</p><p>Arsitektur ini dirancang untuk mendukung berbagai kebutuhan, mulai dari command center, operational application, database, video management system, analytic platform, hingga digital evidence repository.</p><p>Tujuannya adalah menghadirkan fondasi digital yang aman dan tangguh, yang mampu berkembang seiring kebutuhan institusi.</p><p>Virtualisasi & Private Cloud Tidak seluruh workload institusi penegakan hukum dapat ditempatkan pada public cloud. Aspek keamanan, kendali atas data, kepatuhan regulasi, serta kebutuhan performa menjadikan private infrastructure sebagai pilihan strategis.</p><p>Virtualisasi dan Private Cloud memungkinkan resource server, storage, dan network dikelola sebagai fleksibel — mempercepat provisioning workload, mengoptimalkan utilisasi infrastruktur, dan mengurangi ketergantungan pada physical server di setiap aplikasi.</p><p>satu kesatuan yang Melalui pendekatan ini, data center bertransformasi dari sekadar kumpulan perangkat menjadi agile private digital infrastructure.</p><p>Jaringan & Konektivitas yang Andal Institusi penegakan hukum umumnya beroperasi lintas lokasi — mulai dari kantor pusat, kantor wilayah, command center, hingga unit lapangan. Setiap lokasi membutuhkan konektivitas yang stabil dan aman agar informasi senantiasa tersedia secara konsisten.</p><p>Sapta Tunas Teknologi membangun enterprise networking yang menghubungkan berbagai lokasi, data center, dan aplikasi dalam satu lingkungan jaringan yang terintegrasi. Monitoring dan keamanan diterapkan secara terpusat, memberikan visibilitas menyeluruh atas kondisi infrastruktur dan lalu lintas jaringan.</p><p>Edge Computing untuk Operasional Lapangan Tidak setiap data perlu dikirimkan lebih dahulu ke data center pusat. Untuk kebutuhan lapangan seperti video analytics dan pengolahan data berskala besar, Edge Computing memungkinkan sebagian proses dijalankan lebih dekat dengan sumber data.</p><p>Pendekatan ini menekan latensi jaringan, mengefisienkan penggunaan bandwidth, dan menjaga kelangsungan proses meski konektivitas ke data center pusat terbatas. Infrastruktur edge tetap terintegrasi dengan data center pusat untuk kebutuhan konsolidasi, analitik, dan manajemen.</p><p>Backup & Disaster Recovery Data operasional dan bukti digital memiliki nilai yang sangat tinggi — kehilangan atau ketidaktersediaannya dapat berdampak signifikan terhadap operasional maupun investigasi. Karena itu, backup semata tidaklah cukup.</p><p>Sapta Tunas Teknologi membantu merancang Backup, Data Protection, Disaster Recovery, dan Cyber Recovery untuk memastikan aplikasi dan data kritikal memiliki mekanisme pemulihan yang andal, menghadapi kegagalan perangkat, human error, bencana, maupun serangan siber.</p><p>Dengan arsitektur business continuity yang tepat, institusi dapat meminimalkan downtime sekaligus memperkuat kesiapan menghadapi berbagai skenario gangguan.</p>','[{"icon": "industri/feat/modernize-infrastructure-1.png", "judul": "High-Availability Data Center", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-2.png", "judul": "Hybrid Cloud Architecture", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-3.png", "judul": "Scalable, secure, resilient", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-4.png", "judul": "Compliance Governance Infrastructure", "teks": ""}]',1),
(4,2,'Mengubah Informasi Menjadi Kecerdasan yang Actionable','<p>Institusi penegakan hukum mengelola data dalam jumlah besar — laporan, dokumen, database kasus, video, gambar, dan berbagai sumber informasi lainnya. Tantangan utamanya bukan lagi sekadar menyimpan data, melainkan menemukan hubungan, pola, dan informasi relevan secara lebih cepat.</p><p>Sapta Tunas Teknologi mengintegrasikan Data Platform, Analytics, Computer Vision, dan Artificial Intelligence untuk membantu mengubah data menjadi kecerdasan yang dapat ditindaklanjuti.</p><p>Intelijen Kriminal & Analisis Kasus Data suatu kasus sering tersebar di berbagai aplikasi, dokumen, dan database. Data analytics memungkinkan terotorisasi untuk dikonsolidasikan dan dianalisis, sehingga investigator maupun analyst memperoleh gambaran yang lebih menyeluruh.</p><p>informasi dari berbagai sumber yang telah Platform analitik membantu menemukan hubungan antar-data, pola aktivitas, dan timeline peristiwa yang sulit terlihat ketika data dianalisis secara terpisah. AI berperan sebagai decision-support tool, sementara interpretasi dan keputusan akhir tetap berada di tangan petugas berwenang sesuai prosedur yang berlaku.</p><p>Pengenalan Wajah & Analisis Video Ribuan kamera di command center maupun area publik menghasilkan video dalam skala sangat besar, yang sulit dipantau secara manual.</p><p>Computer Vision mengubah video menjadi sumber data yang dapat dianalisis — mendukung object detection, visual recognition, event detection, hingga verifikasi identitas sesuai kewenangan dan kebijakan yang berlaku.</p><p>Teknologi dapat membantu melakukan object detection, visual recognition, event detection maupun authorized identity verification sesuai kewenangan dan kebijakan yang berlaku.</p><p>Implementasinya tetap disertai governance, otorisasi, kontrol privasi, dan audit trail yang sesuai dengan regulasi.</p><p>Analisis Prediktif & Risiko Operasional Data historis dapat digunakan untuk memahami pola kejadian yang sebelumnya sulit terlihat. Melalui statistical analytics dan machine learning, organisasi dapat mengidentifikasi pola, tren, maupun anomali sebagai masukan dalam perencanaan operasi dan alokasi sumber daya.</p><p>Contohnya mencakup analisis tren kejadian berdasarkan waktu dan lokasi, identifikasi perubahan pola tertentu, hingga analisis risiko operasional.</p><p>Pendekatan ini digunakan sebagai analytical support, bukan sebagai mekanisme otomatis untuk menentukan apakah seseorang melakukan atau akan melakukan suatu tindakan. Dengan demikian, AI berperan meningkatkan situational awareness tanpa menggantikan pertimbangan manusia dan prosedur penegakan hukum yang berlaku.</p><p>Data Lakehouse & Dashboard Real-Time Data silo menjadi salah satu tantangan terbesar bagi organisasi berskala besar. Informasi yang tersebar di berbagai aplikasi dan database pada setiap Kementerian/Lembaga membuat consolidated view sulit diperoleh secara cepat — padahal kecepatan akses informasi kerap menjadi penentu ketepatan keputusan.</p><p>Sapta Tunas Teknologi menghadirkan solusi melalui Data Lakehouse atau modern enterprise data platform, yang mengonsolidasikan data dari berbagai sistem ke dalam satu arsitektur terpadu, siap mendukung kebutuhan analytics maupun AI.</p><p>Data tersebut kemudian dihadirkan melalui real-time operational dashboard, menghadirkan situational awareness yang menyeluruh bagi command center maupun jajaran manajemen, kapan pun dibutuhkan.</p><p>Melalui data yang telah diperkaya (enriched), dashboard menyajikan indicative maupun predictive analytics, tren, peta sebaran, status insiden, hingga metrik operasional — menghadirkan fondasi pengambilan keputusan yang lebih cepat, akurat, dan berbasis data aktual.</p><p>Manajemen Dokumen & Analisis Bukti Digital Investigasi kerap menghasilkan ribuan bahkan jutaan halaman dokumen, gambar, dan electronic files — volume yang sulit dikelola secara manual.</p><p>Dengan AI dan Intelligent Document Processing, Sapta Tunas Teknologi menghadirkan kemampuan OCR, klasifikasi, ekstraksi metadata, hingga indexing otomatis, sehingga dokumen dapat ditemukan dan dikelompokkan jauh lebih cepat.</p><p>Melalui evidence analytics, investigator dapat langsung mencari informasi spesifik tanpa perlu membaca seluruh dokumen satu per satu — mempercepat proses investigasi secara signifikan.</p><p>Seluruh data tetap tersimpan dalam repository yang dilengkapi access control, audit log, dan mekanisme perlindungan menyeluruh, menjaga integritas dan keterlacakan (traceability) setiap informasi digital.</p>','[{"icon": "industri/feat/data-ai-1.png", "judul": "Generative AI", "teks": ""}, {"icon": "industri/feat/data-ai-2.png", "judul": "Private LLM", "teks": ""}, {"icon": "industri/feat/data-ai-3.png", "judul": "Analytics Surveillance Camera", "teks": ""}, {"icon": "industri/feat/data-ai-4.png", "judul": "AI Fraud Detection", "teks": ""}, {"icon": "industri/feat/data-ai-5.png", "judul": "AI Automation Report", "teks": ""}, {"icon": "industri/feat/data-ai-6.png", "judul": "AI Automation Operations", "teks": ""}, {"icon": "industri/feat/data-ai-7.png", "judul": "CX With AI Agent", "teks": ""}]',2),
(4,3,'Melindungi Sistem Kritikal, Data Sensitif, dan Bukti Digital','<p>Di era digital, keamanan siber menjadi fondasi utama operasional penegakan hukum. Ancaman dapat muncul dari mana saja — peretas eksternal, malware, akun yang disusupi, aplikasi rentan, hingga akses tidak sah dari dalam organisasi. Perlindungan pun harus menyeluruh, mencakup pengguna, perangkat, dan jaringan, hingga aplikasi, server, dan data.</p><p>Threat Detection & Security Operations Center Security Operations Center (SOC) memberikan visibilitas terpusat atas seluruh aktivitas keamanan di lingkungan organisasi. Log dari server, endpoint, firewall, perangkat jaringan, dan berbagai sistem keamanan lainnya dikumpulkan dan dianalisis secara terintegrasi melalui SIEM. Tim SOC memantau setiap potensi ancaman secara berkelanjutan, membantu organisasi bertransformasi dari pendekatan reaktif menjadi deteksi dan respons yang proaktif.</p><p>Perlindungan Endpoint Laptop, desktop, server, dan perangkat pengguna adalah titik masuk utama bagi serangan siber.</p><p>Endpoint Security serta EDR/XDR memantau aktivitas perangkat, mendeteksi perilaku mencurigakan, dan memungkinkan respons cepat saat ditemukan indikasi kompromi.</p><p>Dengan visibilitas endpoint yang terpusat, tim keamanan dapat melakukan investigasi dan penanganan insiden secara lebih sigap.</p><p>Keamanan Jaringan & Segmentasi Keamanan jaringan tidak hanya menahan serangan dari luar, tetapi juga membatasi ruang gerak penyerang bila suatu sistem berhasil disusupi.</p><p>Melalui segmentasi jaringan, sistem dapat dikelompokkan berdasarkan fungsi dan tingkat sensitivitasnya — misalnya jaringan untuk pengguna, server, sistem manajemen, lingkungan pengawasan, hingga bukti digital, masing-masing dengan kebijakan keamanan tersendiri.</p><p>Dengan pendekatan ini, akses antar-sistem dapat dikendalikan berdasarkan prinsip hak akses minimum (least privilege).</p><p>Akses Aman & Manajemen Identitas Identitas pengguna adalah elemen inti dari arsitektur keamanan modern. Manajemen Akses memastikan hanya pihak yang berwenang yang dapat mengakses aplikasi dan data tertentu.</p><p>Penerapan autentikasi, otorisasi, akses berbasis peran, dan kontrol akses khusus (privileged access) membantu meminimalkan risiko akses yang tidak sah.</p><p>Setiap aktivitas pengguna turut tercatat sebagai jejak audit yang siap digunakan untuk kebutuhan investigasi maupun kepatuhan.</p><p>Perlindungan Data & Enkripsi Data investigasi, informasi pribadi, dan bukti digital perlu dilindungi baik saat tersimpan maupun saat dipindahkan antar-sistem.</p><p>Enkripsi, kontrol akses, backup yang aman, dan proteksi data lainnya menjaga kerahasiaan dan integritas informasi. Untuk memastikan kelangsungan operasional, proteksi data juga dilengkapi dengan backup, disaster recovery, dan cyber recovery — memberikan jaminan bahwa organisasi tetap memiliki salinan data yang siap digunakan kapan pun insiden terjadi pada lingkungan utama.</p>','[{"icon": "industri/feat/cybersecurity-1.png", "judul": "EDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-2.png", "judul": "NDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-3.png", "judul": "XDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-4.png", "judul": "Next-Generation Firewall", "teks": ""}, {"icon": "industri/feat/cybersecurity-5.png", "judul": "WAF", "teks": ""}, {"icon": "industri/feat/cybersecurity-6.png", "judul": "SIEM", "teks": ""}, {"icon": "industri/feat/cybersecurity-7.png", "judul": "SOAR", "teks": ""}, {"icon": "industri/feat/cybersecurity-8.png", "judul": "Threat Intelligence", "teks": ""}]',3),
(4,4,'Managed Services untuk Operasi Penegakan Hukum yang Stabil, Aman, dan Siap Mendukung Layanan Publik','<p>Dalam operasi penegakan hukum, gangguan pada jaringan, server pelaporan masyarakat, database investigasi, atau arsip video CCTV dapat langsung menghambat respons, koordinasi, dan kualitas layanan publik. Karena itu, kami menghadirkan Managed Services untuk membantu institusi menjaga infrastruktur IT kritikal tetap stabil, aman, dan siap digunakan setiap saat.</p><p>Layanan kami mencakup pengelolaan siklus hidup infrastruktur IT, penyimpanan data video CCTV berkapasitas besar, pemeliharaan perangkat jaringan di berbagai kantor, serta monitoring proaktif terhadap sistem kritikal. Dengan pemantauan berkelanjutan, potensi gangguan dapat dideteksi lebih awal dan ditangani sebelum menghambat operasional penegakan hukum.</p><p>Untuk mendukung lebih responsif, kami membantu menjaga ketersediaan server pelaporan masyarakat agar tetap dapat diakses secara andal, mendukung proses pelaporan, transparansi layanan, dan respons cepat terhadap kebutuhan masyarakat.</p><p>layanan publik yang Diperkuat dengan layanan keamanan tingkat lanjut seperti VAPT, Digital Forensic, dan Table Top Exercise, institusi dapat meningkatkan ketahanan sistem, menguji kesiapan tim, serta memitigasi risiko siber yang menargetkan data sensitif dan sistem operasional penegakan hukum.</p><p>Dengan pendekatan end-to-end, solusi ini membantu menjaga stabilitas operasional, keamanan data, kesiapan layanan publik, dan efektivitas koordinasi antar-unit dalam mendukung penegakan hukum yang lebih modern, responsif, dan terpercaya.</p>','[{"icon": "industri/feat/managed-services-1.png", "judul": "VAPT", "teks": ""}, {"icon": "industri/feat/managed-services-2.png", "judul": "Digital Forensic", "teks": ""}, {"icon": "industri/feat/managed-services-3.png", "judul": "Cyber Drill", "teks": ""}, {"icon": "industri/feat/managed-services-4.png", "judul": "Table Top Exercise", "teks": ""}, {"icon": "industri/feat/managed-services-5.png", "judul": "24/7/365", "teks": ""}, {"icon": "industri/feat/managed-services-6.png", "judul": "Patch management", "teks": ""}]',4),
(5,1,'Infrastruktur Tangguh untuk Operasional Energi yang Kritikal','<p>Infrastruktur teknologi di industri Oil & Gas, Mining, dan Utilities harus mampu mendukung lingkungan operasional yang tersebar, beban kerja yang semakin kompleks, serta tuntutan ketersediaan sistem yang tinggi. Arsitektur teknologi pun tidak cukup hanya berfokus pada data center pusat, tetapi juga harus memastikan aplikasi dan data tetap dapat diakses di plant, site, dan lokasi operasional terpencil.</p><p>Sapta Tunas Teknologi membantu organisasi membangun arsitektur infrastruktur yang scalable dan resilient — mencakup compute, enterprise storage, networking, dan virtualization, hingga private/hybrid cloud, data protection, dan disaster recovery.</p><p>Kapabilitas Business Continuity STT meliputi Hybrid Cloud Infrastructure, Enterprise Infrastructure Modernization, Networking Solution, Disaster and Cyber Resiliency, serta arsitektur HCI dan Non-HCI.</p><p>Komputasi Berkinerja Tinggi Digitalisasi operasi Energi mendorong kebutuhan compute yang terus meningkat, termasuk kemampuan parallel computing untuk menjalankan berbagai analisis di bidang energi — mulai dari simulasi, operational analytics, machine learning, computer vision, hingga artificial intelligence.</p><p>Sapta Tunas Teknologi membantu membangun modern data center dengan scalable server, enterprise storage, dan high-speed networking, sehingga infrastruktur dapat disesuaikan dengan karakteristik masing-masing workload.</p><p>Untuk kebutuhan AI dan analytics dengan processing capability yang lebih besar, arsitektur lanjut menggunakan accelerated computing dan GPU lebih dapat dikembangkan infrastructure.</p><p>Dengan pendekatan ini, data center tidak lagi sekadar menjalankan aplikasi enterprise, melainkan berkembang menjadi digital processing platform yang mendukung kebutuhan operasional, analytics, dan AI secara menyeluruh.</p><p>Infrastruktur juga dirancang secara modular, sehingga kapasitas compute maupun storage dapat ditingkatkan seiring pertumbuhan data — tanpa perlu melakukan redesign terhadap keseluruhan environment.</p><p>Menghubungkan Setiap Titik Operasi dalam Satu Ekosistem yang Aman dan Andal Operasional perusahaan energi kerap tersebar di wilayah yang luas dan menantang. Kantor pusat, data center, plant, tambang, area pengeboran, control room, hingga fasilitas terpencil—semuanya membutuhkan konektivitas yang aman dan andal untuk saling bertukar informasi secara real-time.</p><p>Sapta Tunas Teknologi menghadirkan solusi jaringan yang mengintegrasikan seluruh lokasi tersebut ke dalam satu infrastruktur yang terhubung secara menyeluruh.</p><p>Di lingkup enterprise, jaringan dirancang untuk menghubungkan pengguna, aplikasi, dan data center secara efisien. Sementara pada area operasional, segmentasi jaringan diterapkan untuk memisahkan zona IT, OT, serta sistem kritikal lainnya sesuai kebutuhan dan tingkat risiko masing-masing.</p><p>Melalui pemantauan jaringan yang terpusat, tim administrator memperoleh visibilitas penuh atas kondisi konektivitas dan infrastruktur, sehingga setiap gangguan dapat terdeteksi dan ditangani dengan lebih cepat.</p><p>Hasilnya adalah connected operation—sebuah ekosistem di mana data mengalir dengan lokasi operasional menuju data platform dan command center, tanpa lancar dari mengorbankan keamanan maupun ketersediaan sistem.</p><p>Menempatkan Setiap Workload pada Infrastruktur yang Tepat Tidak semua aplikasi di sektor energi memiliki kebutuhan infrastruktur yang sama. Sebagian workload dapat berjalan optimal di cloud, sementara aplikasi operasional yang kritikal, data sensitif, atau workload dengan sensitivitas latency tinggi umumnya lebih sesuai ditempatkan pada infrastruktur private.</p><p>Hybrid Cloud hadir sebagai solusi yang memberikan fleksibilitas untuk mengombinasikan kedua lingkungan tersebut secara optimal.</p><p>Sapta Tunas Teknologi membantu perusahaan mengintegrasikan infrastruktur on-premise dengan cloud environment, sehingga organisasi dapat menentukan penempatan workload berdasarkan kebutuhan performa, keamanan, kepatuhan (compliance), dan kontinuitas operasional.</p><p>Pendekatan ini juga memungkinkan perusahaan mengembangkan kapasitas infrastruktur secara bertahap, tanpa harus menempatkan seluruh workload pada satu arsitektur tunggal.</p><p>Tujuan kami bukan sekadar mendorong perusahaan untuk "berpindah ke cloud", melainkan membangun the right infrastructure for the right workload—infrastruktur yang tepat, untuk kebutuhan yang tepat.</p><p>Infrastruktur Konvergensi IT & OT Menghubungkan Dunia IT dan OT secara Aman dan Terkendali Transformasi digital mendorong IT dan Operational Technology untuk semakin terintegrasi. Informasi yang sebelumnya hanya tersimpan di sistem industrial kini dapat disalurkan ke enterprise data platform untuk kebutuhan monitoring, analitik, hingga artificial intelligence.</p><p>Namun, integrasi ini tidak boleh dilakukan dengan menghubungkan kedua lingkungan secara langsung tanpa arsitektur dan kontrol keamanan yang memadai.</p><p>Sapta Tunas Teknologi membangun infrastructure layer yang memungkinkan komunikasi antara IT dan OT berlangsung secara terkontrol, melalui segmentasi jaringan, kebijakan akses, monitoring, serta arsitektur keamanan yang sesuai standar industri.</p><p>Dengan pendekatan ini, organisasi tetap dapat memanfaatkan data operasional secara maksimal, tanpa membuka akses yang tidak diperlukan terhadap sistem industrial yang kritikal.</p><p>Fondasi inilah yang menjadi dasar bagi implementasi Industrial IoT, operational analytics, predictive maintenance, dan operasi berbasis AI di masa depan.</p><p>Backup, Disaster Recovery & Cyber Resiliency Membangun Ketahanan Operasional, Bukan Sekadar Backup Di industri energi, kehilangan data atau tidak tersedianya sistem dapat berdampak jauh lebih luas daripada sekadar gangguan administratif—perlindungan data karenanya harus dirancang sebagai bagian integral dari strategi business continuity.</p><p>Sapta Tunas Teknologi membantu organisasi membangun arsitektur yang mencakup backup, replikasi, disaster recovery, hingga cyber resiliency secara menyeluruh.</p><p>Setiap workload kritikal dapat diprioritaskan berdasarkan Recovery Time Objective (RTO) dan Recovery Point Objective (RPO), sehingga organisasi memiliki strategi pemulihan yang disesuaikan dengan tingkat kritikalitas masing-masing aplikasi.</p><p>Dengan arsitektur yang tepat, perusahaan memiliki mekanisme pemulihan yang andal saat menghadapi hardware failure, human error, bencana, maupun serangan siber—memastikan sistem dan data dapat kembali beroperasi sesuai waktu yang telah direncanakan.</p><p>Business continuity yang sesungguhnya bukan hanya soal memiliki backup, melainkan membangun operational resilience yang menyeluruh.</p>','[{"icon": "industri/feat/modernize-infrastructure-1.png", "judul": "High-Availability Data Center", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-2.png", "judul": "Hybrid Cloud Architecture", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-3.png", "judul": "Scalable, secure, resilient", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-4.png", "judul": "Compliance Governance Infrastructure", "teks": ""}]',1),
(5,2,'Mengubah Data Operasional Menjadi Actionable Intelligence','<p>Dari Data Melimpah Menuju Insight yang Bernilai Energi merupakan salah satu industri dengan volume data terbesar—mulai dari sistem inspeksi, aplikasi engineering, aplikasi laporan operasional, sensor, mesin, kamera, enterprise, hingga citra satelit.</p><p>Namun, nilai sesungguhnya dari data baru terwujud ketika perusahaan mampu insight yang dapat meningkatkan reliability, efisiensi, dan mengubahnya menjadi keselamatan operasional.</p><p>Sapta Tunas Teknologi menghadirkan portofolio lengkap Data Management, Data Analytics, Data Lakehouse, AI Platform, Computer Vision, dan Agentic AI sebagai fondasi pengembangan kapabilitas AI dan analitik perusahaan Anda.</p><p>Predictive Maintenance & Asset Intelligence Mendeteksi Masalah Sebelum Terjadi Pendekatan maintenance tradisional umumnya bersifat terjadwal atau baru dilakukan setelah kerusakan terjadi. Dengan memanfaatkan data historis dan parameter kondisi peralatan, pendekatan ini dapat dikembangkan menjadi condition-based dan predictive maintenance yang lebih efektif.</p><p>Data seperti kondisi operasional, riwayat performa, event log, hingga catatan maintenance dapat dikonsolidasikan ke dalam satu data platform untuk dianalisis secara menyeluruh.</p><p>Machine Learning kemudian membantu mengidentifikasi pola abnormal atau perubahan karakteristik kondisi (degradation)—memberikan tim maintenance insight tambahan untuk menentukan aset mana yang perlu mendapat perhatian lebih awal.</p><p>berpotensi mengindikasikan penurunan peralatan yang Pendekatan ini bukan bertujuan menggantikan penilaian engineering, melainkan membantu organisasi bertransformasi dari:</p><p>Reactive Maintenance → Preventive Maintenance → Predictive Maintenance Dengan strategi maintenance yang lebih terencana, perusahaan berpotensi menekan unplanned downtime, meningkatkan utilisasi aset, serta memperpanjang usia pakai peralatan-peralatan kritikal.</p><p>Analitik Produksi & Operasional Dari Sekadar Memantau, Menuju Memahami Operasional Secara Menyeluruh Data dari berbagai sistem operasional sering kali sudah tersedia, namun tersebar dalam silo yang terpisah-pisah.</p><p>Akibatnya, management maupun tim operasional harus menggabungkan informasi tersebut secara manual sebelum memperoleh gambaran utuh mengenai kondisi operasional perusahaan.</p><p>Modern Data Platform hadir untuk mengonsolidasikan data operasional dan enterprise ke dalam satu environment terintegrasi yang siap digunakan untuk analitik.</p><p>Dashboard dapat menampilkan production performance, asset utilization, operational KPI, downtime, konsumsi energi, dan berbagai parameter lain sesuai kebutuhan spesifik organisasi Anda.</p><p>Model analitik selanjutnya dapat digunakan untuk mengidentifikasi korelasi, pola, maupun bottleneck operasional secara lebih mendalam:</p><p>● ● ● Oil & Gas — memahami hubungan antara kondisi aset dan performa produksi Mining — menganalisis utilisasi dan performa aset operasional Utilities — memberikan visibilitas menyeluruh terhadap performa infrastruktur dan layanan Dengan pendekatan ini, organisasi bertransformasi dari sekadar memantau "apa yang terjadi", menuju kemampuan memahami "mengapa hal itu terjadi dan apa yang memerlukan perhatian."</p><p>Deteksi Anomali & Pemantauan Keselamatan Mengubah Pengawasan Pasif Menjadi Kewaspadaan Operasional yang Proaktif Lingkungan industri energi memiliki risiko keselamatan yang tinggi, sehingga kemampuan mendeteksi kondisi abnormal sedini mungkin menjadi sangat bernilai bagi keberlangsungan operasional.</p><p>Artificial Intelligence dapat menganalisis data operasional untuk mengidentifikasi pola atau perilaku yang menyimpang dari kondisi normal.</p><p>Selain data numerik, Sapta Tunas Teknologi juga menghadirkan kemampuan Computer Vision yang mendukung Industrial Visual Inspection serta Safety & Compliance Monitoring.</p><p>Melalui teknologi ini, kamera yang sebelumnya hanya berfungsi sebagai alat surveillance dapat bertransformasi menjadi intelligent sensor. Computer Vision, misalnya, mampu membantu mendeteksi:</p><p>● ● ● ● ● Penggunaan Alat Pelindung Diri (APD) Keberadaan personel di area terbatas (restricted area) Objek atau kondisi visual tertentu Kondisi keselamatan spesifik pada area operasional Proses visual inspection secara otomatis AI memberikan alert dan informasi kepada operator, sementara validasi serta tindakan tetap sepenuhnya berada di tangan personel yang berwenang.</p><p>Dengan pendekatan ini, sistem CCTV berkembang dari sekadar passive monitoring system menjadi bagian integral dari proactive operational awareness perusahaan Anda.</p><p>Industrial Visual Inspection Memperluas Kapasitas Inspeksi Melalui Kecerdasan Visual Visual inspection masih menjadi bagian penting dari proses maintenance dan quality control di berbagai lingkungan industri. Namun, pemeriksaan manual memiliki keterbatasan dalam hal konsistensi, cakupan area, serta kemampuan melakukan monitoring secara berkelanjutan.</p><p>Computer Vision hadir untuk melakukan automated visual analysis terhadap image secara akurat. inspection terhadap maupun Implementasinya dapat disesuaikan untuk mendukung proses equipment, fasilitas, maupun area operasional sesuai kebutuhan masing-masing use case.</p><p>guna mengidentifikasi kondisi-kondisi tertentu video Sapta Tunas Teknologi secara khusus menghadirkan Industrial Visual Inspection sebagai bagian integral dari kemampuan Computer Vision yang kami tawarkan.</p><p>Model AI berperan sebagai first-level screening, sehingga operator maupun engineer dapat lebih fokus mencurahkan perhatian pada kondisi-kondisi yang benar-benar membutuhkan review lebih lanjut.</p><p>Melalui pendekatan ini, AI tidak menggantikan peran inspection engineer, melainkan memperluas kapasitas mereka untuk melakukan inspeksi pada skala yang jauh lebih besar.</p><p>IoT Data Platform & Real-Time Analytics Membangun Fondasi Data Tunggal untuk Berbagai Kebutuhan Operasional Industrial IoT membuka kemampuan untuk mengumpulkan data operasional dari perangkat yang jumlahnya terus bertambah. Namun, banyaknya sensor tidak serta-merta menghasilkan nilai bisnis—organisasi membutuhkan platform yang mampu mengumpulkan, menyimpan, mengelola, dan menganalisis data tersebut secara menyeluruh.</p><p>Sapta Tunas Teknologi membantu membangun data architecture yang mengonsolidasikan telemetry dan informasi operasional ke dalam modern Data Platform atau Data Lakehouse.</p><p>Data yang terkonsolidasi kemudian dapat dimanfaatkan untuk dashboard, historical analysis, model AI, maupun berbagai aplikasi operasional lainnya. Real-time analytics memungkinkan setiap event penting terdeteksi dengan lebih cepat dan ditampilkan langsung melalui operational dashboard.</p><p>Arsitektur seperti ini menciptakan satu fondasi data tunggal yang dapat mendukung berbagai use case—mulai dari predictive maintenance, asset monitoring, analisis efisiensi energi, hingga operational intelligence secara menyeluruh.</p><p>Kecerdasan Satelit & Pemantauan Lingkungan Wawasan Menyeluruh untuk Area Operasional yang Luas Bagi perusahaan Oil & Gas, Mining, dan Utilities dengan wilayah operasional yang luas, pemantauan kondisi area sering kali sulit dilakukan secara menyeluruh melalui metode konvensional.</p><p>Citra satelit hadir sebagai sumber informasi tambahan yang memungkinkan large-scale observation secara lebih efisien. Melalui portofolio AI Sapta Tunas Teknologi, Artificial Intelligence dapat menganalisis citra satelit dan membandingkan kondisi suatu wilayah dari waktu ke waktu.</p><p>Penerapannya dapat disesuaikan dengan kebutuhan masing-masing sektor:</p><p>● ● ● Mining — memantau perubahan area operasi maupun kondisi lingkungan sekitar Oil & Gas — mendukung monitoring asset corridor dan area operasional yang luas Utilities — meningkatkan visibilitas terhadap infrastruktur yang tersebar secara geografis Keunggulan utama pendekatan ini terletak pada kemampuannya melakukan monitoring at scale terhadap area yang selama ini sulit, memakan biaya besar, atau membutuhkan waktu panjang apabila diperiksa sepenuhnya secara manual.</p><p>Analitik Lingkungan & Keberlanjutan Konsolidasi Data untuk Mendukung Pelaporan dan Praktik Keberlanjutan Data operasional tidak hanya dibutuhkan untuk mendukung produksi. Perusahaan energi kini juga semakin memerlukan kemampuan untuk mengonsolidasikan informasi mengenai pemanfaatan sumber daya, kondisi lingkungan, serta indikator keberlanjutan (sustainability).</p><p>Modern Data Platform membantu menggabungkan informasi dari berbagai sistem ke dalam dashboard dan reporting environment yang lebih terstruktur dan mudah dipahami.</p><p>Ketika dikombinasikan dengan satellite intelligence dan environmental monitoring, perusahaan memperoleh visibilitas yang lebih luas terhadap perubahan kondisi operasional maupun lingkungan sekitar.</p><p>AI dan analytics pada area ini berperan untuk mendukung proses monitoring dan pelaporan, sementara parameter serta metodologi yang digunakan tetap disesuaikan dengan standar, regulasi, dan kebijakan sustainability yang berlaku di masing-masing perusahaan.</p>','[{"icon": "industri/feat/data-ai-1.png", "judul": "Generative AI", "teks": ""}, {"icon": "industri/feat/data-ai-2.png", "judul": "Private LLM", "teks": ""}, {"icon": "industri/feat/data-ai-3.png", "judul": "Analytics Surveillance Camera", "teks": ""}, {"icon": "industri/feat/data-ai-4.png", "judul": "AI Fraud Detection", "teks": ""}, {"icon": "industri/feat/data-ai-5.png", "judul": "AI Automation Report", "teks": ""}, {"icon": "industri/feat/data-ai-6.png", "judul": "AI Automation Operations", "teks": ""}, {"icon": "industri/feat/data-ai-7.png", "judul": "CX With AI Agent", "teks": ""}]',2),
(5,3,'Melindungi Ekosistem IT dan Operational Technology','<p>Keamanan Menyeluruh untuk Dua Dunia yang Saling Terhubung Cybersecurity di industri energi memiliki tantangan tersendiri, karena organisasi harus lingkungan sekaligus: Information Technology (IT) dan Operational melindungi dua Technology (OT/ICS).</p><p>Jika serangan pada sistem enterprise umumnya berdampak pada kehilangan informasi atau downtime aplikasi, insiden cybersecurity pada OT berpotensi memengaruhi proses fisik dan kegiatan operasional secara langsung—dengan konsekuensi yang jauh lebih besar.</p><p>Karena itu, Sapta Tunas Teknologi menghadirkan pendekatan cybersecurity yang komprehensif, mencakup enterprise IT sekaligus environment industrial secara terintegrasi.</p><p>Kemampuan Managed Cybersecurity kami secara khusus mencakup Managed ICS/OT, sementara layanan Security Assessment turut mencakup IoT/ICS Assessment—memastikan perlindungan menyeluruh di setiap lapisan operasional perusahaan Anda.</p><p>Keamanan OT / ICS Keamanan Industrial yang Selaras dengan Stabilitas Operasional Industrial Control System (ICS) memiliki karakteristik yang berbeda dari lingkungan IT enterprise pada umumnya.</p><p>Di lingkungan IT, proses patching atau restart sistem relatif dapat dilakukan dengan mudah dan cepat.</p><p>Namun di lingkungan OT, setiap perubahan konfigurasi maupun downtime harus secara terhadap mempertimbangkan menyeluruh—kesalahan sekecil apa pun dapat berdampak langsung pada kelangsungan produksi.</p><p>operasional dampaknya proses Karena itu, arsitektur keamanan untuk OT/ICS harus dirancang secara cermat agar dapat melindungi sistem tanpa mengorbankan stabilitas maupun ketersediaan (availability) dari industrial system yang sedang berjalan.</p><p>Perlindungan & Segmentasi Jaringan Industri Membatasi Dampak, Memperkuat Ketahanan Sistem Salah satu prinsip fundamental dalam OT Cybersecurity adalah memastikan seluruh sistem tidak berada dalam satu flat network yang sama.</p><p>Jika satu perangkat berhasil dikompromikan, penyerang tidak boleh dapat bergerak bebas ke seluruh lingkungan sistem.</p><p>Network segmentation memisahkan tingkat kritikalitasnya, sehingga komunikasi antar-zona hanya diizinkan sesuai kebutuhan yang telah ditetapkan secara ketat.</p><p>lingkungan berdasarkan fungsi dan Firewall, kebijakan akses, serta network monitoring bekerja secara bersinergi untuk membatasi traffic yang tidak diperlukan, sekaligus meningkatkan visibilitas terhadap setiap aktivitas jaringan.</p><p>Tujuan utama dari pendekatan ini adalah reducing the blast radius—meminimalkan dampak yang timbul apabila suatu insiden keamanan terjadi.</p><p>Deteksi Ancaman di Seluruh Lingkungan IT & OT Visibilitas Terpusat untuk Perlindungan yang Lebih Menyeluruh Organisasi tidak mungkin melindungi lingkungan yang tidak dapat mereka pantau.</p><p>Karena itu, cybersecurity yang efektif membutuhkan visibilitas yang terpusat di seluruh sistem.</p><p>Security logs dan event dari berbagai sistem dikumpulkan menuju SIEM/SOC untuk dianalisis secara terintegrasi.</p><p>Pada lingkungan enterprise IT, monitoring mencakup endpoint, server, firewall, aplikasi, hingga identity.</p><p>Sementara pada lingkungan operasional, monitoring dikembangkan untuk memberikan visibilitas terhadap security event yang relevan pada OT/ICS, tanpa mengganggu jalannya proses industrial.</p><p>Dengan mengorelasikan informasi dari berbagai sumber, tim security memperoleh gambaran insiden yang jauh lebih lengkap dan akurat—memungkinkan SOC menjalankan siklus:</p><p>Detection → Analysis → Investigation → Response Sapta Tunas Teknologi menghadirkan layanan cybersecurity yang komprehensif, mencakup SOC as a Service, Managed SIEM & Incident Response, MDR (Managed Detection & Response), Managed Threat Intelligence, serta Managed ICS/OT—memastikan perlindungan menyeluruh bagi seluruh ekosistem IT dan OT perusahaan Anda.</p><p>Manajemen Kerentanan & Postur Keamanan Memahami Risiko Sebelum Menjadi Insiden Risiko keamanan tidak hanya berasal dari malware atau serangan pihak luar. Sistem yang usang, konfigurasi yang tidak aman, layanan yang terekspos, hingga kebijakan yang kurang tepat juga dapat membuka celah bagi terjadinya compromise.</p><p>Sapta Tunas Teknologi menghadirkan berbagai security assessment untuk mengidentifikasi celah keamanan tersebut secara menyeluruh, meliputi:</p><p>● ● ● ● Vulnerability Assessment & Penetration Testing Security Posture Assessment Configuration & Policy Assessment IoT / ICS Assessment ● ● Compromise Assessment Red Teaming Security Testing Setiap assessment memberikan organisasi baseline yang jelas mengenai kondisi cybersecurity saat ini. Temuan dari hasil assessment kemudian dapat digunakan untuk menentukan langkah remediation berdasarkan tingkat risiko dan kritikalitasnya.</p><p>Khusus untuk lingkungan OT, proses remediation tetap mempertimbangkan operational constraints yang berlaku, sehingga penanganan setiap vulnerability disesuaikan secara cermat—tidak selalu mengikuti pendekatan yang sama seperti pada enterprise IT.</p><p>Respons Insiden & Ketahanan Siber Kesiapan Menghadapi Insiden, Bukan Sekadar Pencegahan Dalam cybersecurity, pertanyaan yang perlu dijawab bukan hanya "Bagaimana mencegah serangan?", melainkan juga "Apa yang harus dilakukan ketika serangan berhasil terjadi?"</p><p>Sapta Tunas Teknologi menghadirkan Incident Response & Recovery serta Digital Forensics sebagai bagian dari layanan Managed SOC.</p><p>Incident Response membantu organisasi melakukan investigasi, containment, dan recovery terhadap sistem yang terdampak, sementara Digital Forensics membantu mengidentifikasi root cause, attack vector, dan dampak dari insiden keamanan secara mendalam.</p><p>Informasi tersebut menjadi kunci untuk memahami bagaimana insiden terjadi, sekaligus mencegah serangan serupa terulang di kemudian hari.</p><p>Ketika dikombinasikan dengan backup, cyber recovery, dan disaster recovery, perusahaan memperoleh pendekatan keamanan yang jauh lebih komprehensif:</p><p>Prevent → Detect → Respond → Recover Tujuan akhirnya bukan membangun lingkungan yang mengasumsikan cyberattack tidak akan pernah terjadi, melainkan membangun organisasi yang tetap resilient—mampu bertahan dan pulih dengan cepat ketika insiden benar-benar terjadi.</p><p>Dari Operasi yang Terhubung Menuju Operasi yang Cerdas Membangun Ekosistem Digital yang Terintegrasi secara Menyeluruh Transformasi digital di industri energi tidak dapat dicapai hanya dengan menambah sensor, membeli server, atau mengimplementasikan satu aplikasi AI secara terpisah.</p><p>Nilai terbesar justru muncul ketika seluruh lapisan—infrastruktur, data, hingga kecerdasan buatan—saling terhubung dan bekerja sebagai satu kesatuan yang utuh.</p>','[{"icon": "industri/feat/cybersecurity-1.png", "judul": "EDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-2.png", "judul": "NDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-3.png", "judul": "XDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-4.png", "judul": "Next-Generation Firewall", "teks": ""}, {"icon": "industri/feat/cybersecurity-5.png", "judul": "WAF", "teks": ""}, {"icon": "industri/feat/cybersecurity-6.png", "judul": "SIEM", "teks": ""}, {"icon": "industri/feat/cybersecurity-7.png", "judul": "SOAR", "teks": ""}, {"icon": "industri/feat/cybersecurity-8.png", "judul": "Threat Intelligence", "teks": ""}]',3),
(5,4,'Managed Services untuk Operasional Energi yang Stabil, Aman, dan Siap Mendukung Site','<p>Remote Pastikan operasional sektor energi tetap stabil dan terkendali di lokasi remote seperti tambang, kilang, offshore, pembangkit, dan fasilitas energi lainnya melalui layanan Managed Services 24/7 yang dirancang untuk mendukung lingkungan kerja kritikal dan menantang.</p><p>Kami menyediakan dukungan IT remote yang responsif serta monitoring proaktif terhadap infrastruktur IT, jaringan operasional, perangkat konektivitas, sistem pendukung site, dan lingkungan digital yang digunakan dalam aktivitas energi. Dengan pemantauan berkelanjutan, potensi gangguan dapat dideteksi lebih awal dan ditangani lebih cepat sebelum berdampak pada produktivitas, keselamatan, maupun kontinuitas operasional.</p><p>Untuk memperkuat ketahanan siber pada infrastruktur kritis, layanan ini dilengkapi dengan kemampuan advanced seperti Vulnerability Assessment & Penetration Testing (VAPT), Digital Forensic, serta Table Top Exercise. Pendekatan ini membantu perusahaan mengidentifikasi celah keamanan, menganalisis insiden, menguji kesiapan respons, dan memitigasi risiko siber yang dapat mengganggu sistem operasional energi.</p><p>Kami juga mendukung pengelolaan kepatuhan terhadap standar regulasi dan praktik keamanan infrastruktur kritis, sehingga perusahaan dapat menjaga tata kelola, keamanan, dan kesiapan operasional di seluruh site secara lebih terstruktur.</p><p>Dengan Managed Services yang terintegrasi, perusahaan energi dapat meningkatkan stabilitas sistem, mempercepat respons teknis, mengurangi beban tim internal, serta menjaga operasional site tetap aman, andal, dan berkelanjutan.</p>','[{"icon": "industri/feat/managed-services-1.png", "judul": "VAPT", "teks": ""}, {"icon": "industri/feat/managed-services-2.png", "judul": "Digital Forensic", "teks": ""}, {"icon": "industri/feat/managed-services-3.png", "judul": "Cyber Drill", "teks": ""}, {"icon": "industri/feat/managed-services-4.png", "judul": "Table Top Exercise", "teks": ""}, {"icon": "industri/feat/managed-services-5.png", "judul": "24/7/365", "teks": ""}, {"icon": "industri/feat/managed-services-6.png", "judul": "Patch management", "teks": ""}]',4),
(7,1,'Infrastruktur yang Fleksibel & Adaptif','<p>Infrastruktur yang Bertumbuh Bersama Bisnis Pertumbuhan bisnis hampir selalu diikuti oleh bertambahnya aplikasi, pengguna, dan volume data. Infrastruktur yang sebelumnya sudah mencukupi dapat berubah menjadi bottleneck ketika workload meningkat, atau ketika organisasi mulai menjalankan kebutuhan baru seperti analytics dan Artificial Intelligence.</p><p>Sapta Tunas Teknologi membantu organisasi membangun scalable infrastructure architecture melalui compute, enterprise storage, virtualization, networking, serta pilihan Hyperconverged maupun Non-Hyperconverged Infrastructure sesuai kebutuhan.</p><p>Arsitektur dirancang agar kapasitas dapat berkembang secara bertahap sesuai kebutuhan, tanpa harus melakukan perubahan besar terhadap keseluruhan environment setiap kali muncul workload baru.</p><p>Virtualization turut memungkinkan resource compute dan storage dimanfaatkan secara lebih fleksibel, sehingga provisioning workload dapat dilakukan jauh lebih cepat infrastructure yang terlalu bergantung pada dibandingkan pendekatan traditional dedicated physical server.</p><p>Dengan pendekatan ini, perusahaan dapat bertransformasi dari infrastruktur yang bersifat statis menuju agile digital infrastructure—infrastruktur yang lebih siap beradaptasi terhadap setiap perubahan kebutuhan bisnis.</p><p>Multi-Cloud & Hybrid Cloud Menempatkan Setiap Workload pada Platform yang Tepat Workload yang Tepat, di Platform yang Tepat Tidak semua aplikasi perusahaan memiliki karakteristik yang sama. Critical database mungkin membutuhkan kontrol penuh dan performa yang predictable dari infrastruktur private. Aplikasi lain mungkin lebih membutuhkan scalability yang ditawarkan public cloud. Sementara workload tertentu justru memerlukan kombinasi dari keduanya.</p><p>Karena itu, strategi cloud yang tepat tidak seharusnya berarti memindahkan seluruh aplikasi ke public cloud secara menyeluruh.</p><p>Sapta Tunas Teknologi membantu perusahaan membangun Hybrid Cloud Architecture yang mengintegrasikan on-premise infrastructure, private cloud, dan public cloud sesuai kebutuhan masing-masing workload. Pemilihan platform mempertimbangkan berbagai faktor—mulai dari performa, keamanan, compliance, lokasi data, scalability, hingga efisiensi biaya operasional.</p><p>Hasilnya adalah arsitektur yang memberikan organisasi fleksibilitas cloud tanpa harus kehilangan kontrol terhadap aplikasi kritikal dan data enterprise.</p><p>Portofolio Business Continuity kami turut menghadirkan Hybrid Cloud Infrastructure sebagai kemampuan untuk mengintegrasikan environment on-premise dan cloud, sekaligus mendukung data availability dan disaster recovery secara menyeluruh.</p><p>Prinsip akhirnya sederhana:</p><p>Workload yang Tepat. Infrastruktur yang Tepat. Lokasi yang Tepat.</p><p>Tempat Kerja Modern & Kolaborasi Mendukung Cara Kerja yang Aman dan Produktif dari Mana Saja Kerja Fleksibel, Kolaborasi Tanpa Batas Cara perusahaan bekerja telah berubah. Kolaborasi tidak lagi selalu berlangsung di dalam satu kantor—karyawan kini dapat bekerja dari head office, branch office, lokasi klien, maupun remote environment, namun tetap membutuhkan akses terhadap aplikasi, dokumen, dan collaboration tools secara konsisten.</p><p>Modern Workplace menghubungkan pengguna, perangkat, aplikasi produktivitas, dan collaboration platform ke dalam satu digital working environment yang terintegrasi.</p><p>Implementasinya dapat mencakup productivity application, document collaboration, communication platform, endpoint management, hingga secure remote access, disesuaikan dengan kebutuhan spesifik organisasi.</p><p>Tujuannya bukan sekadar memungkinkan karyawan bekerja dari lokasi berbeda, melainkan menciptakan pengalaman kerja yang konsisten dan produktif, dengan tetap menjaga keamanan serta kontrol penuh terhadap informasi enterprise.</p><p>Modern Workplace juga menjadi fondasi penting bagi implementasi Artificial Intelligence pada lingkungan produktivitas di masa mendatang.</p><p>Dengan pendekatan ini, transformasi digital tidak hanya terjadi di data center, tetapi juga hadir dalam cara manusia berinteraksi dengan teknologi setiap hari.</p><p>Kontinuitas Bisnis & Perlindungan Data Menjaga Bisnis Tetap Berjalan di Tengah Gangguan Kesiapan Menghadapi yang Tak Terduga Ketergantungan perusahaan terhadap teknologi membuat downtime memiliki dampak yang semakin besar terhadap kelangsungan bisnis. Hardware failure, human error, ransomware, system corruption, hingga bencana dapat menyebabkan aplikasi dan data menjadi tidak tersedia kapan saja.</p><p>Karena itu, organisasi membutuhkan strategi yang jauh lebih menyeluruh daripada sekadar melakukan backup.</p><p>Sapta Tunas Teknologi membantu membangun Business Continuity, Backup, Data Protection, Disaster Recovery, dan Cyber Resiliency, sehingga sistem-sistem kritikal memiliki mekanisme pemulihan yang telah direncanakan dengan matang.</p><p>Setiap aplikasi dapat diklasifikasikan berdasarkan tingkat kritikalitasnya, sehingga organisasi dapat menentukan Recovery Time Objective (RTO) dan Recovery Point Objective (RPO) sesuai kebutuhan masing-masing sistem. Data protection menjaga ketersediaan dan kemampuan pemulihan data, sementara disaster recovery menyediakan mekanisme untuk menjalankan kembali aplikasi apabila infrastruktur utama mengalami gangguan.</p><p>Portofolio kami secara khusus menempatkan Disaster and Cyber Resiliency sebagai bagian dari Business Continuity, untuk memastikan rapid recovery sekaligus melindungi organisasi dari risiko kehilangan data dan ancaman siber.</p><p>Pada akhirnya, business continuity bukan sekadar soal:</p><p>"Apakah kita memiliki backup?"</p><p>melainkan:</p><p>"Mampukah bisnis kita pulih ketika sesuatu terjadi?"</p><p>Optimalisasi Biaya Mengoptimalkan Infrastruktur Tanpa Mengorbankan Performa Setiap Investasi Teknologi, Menghasilkan Value yang Optimal Modernisasi teknologi tidak selalu berarti menambah lebih banyak perangkat. Salah satu tujuan utama dari infrastructure transformation adalah memastikan resource yang sudah dimiliki perusahaan dimanfaatkan secara optimal.</p><p>infrastructure consolidation, HCI, workload placement, dan capacity Virtualization, planning membantu organisasi meningkatkan utilisasi sekaligus mengurangi infrastructure sprawl yang tidak efisien.</p><p>Resource monitoring juga memungkinkan organisasi memahami pola penggunaan CPU, memory, storage, dan network secara akurat, sehingga capacity expansion dapat dilakukan sesuai kebutuhan aktual—bukan sekadar asumsi.</p><p>Sapta Tunas Teknologi membantu melakukan assessment menyeluruh terhadap infrastructure environment perusahaan, serta menentukan arsitektur yang seimbang antara performa, availability, scalability, dan efisiensi biaya.</p><p>turut menghadirkan kemampuan Aggregated & Disaggregated Portofolio kami Solution—sebuah pendekatan yang menyediakan flexible infrastructure architecture sekaligus mengoptimalkan performa dan efisiensi biaya secara bersamaan.</p><p>Tujuan kami bukan sekadar menekan biaya, melainkan memastikan setiap investasi teknologi menghasilkan nilai yang optimal bagi pertumbuhan bisnis Anda.</p>','[{"icon": "industri/feat/modernize-infrastructure-1.png", "judul": "High-Availability Data Center", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-2.png", "judul": "Hybrid Cloud Architecture", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-3.png", "judul": "Scalable, secure, resilient", "teks": ""}, {"icon": "industri/feat/modernize-infrastructure-4.png", "judul": "Compliance Governance Infrastructure", "teks": ""}]',1),
(7,2,'Business Intelligence & Advanced Analytics','<p>Dari Data Menuju Pengambilan Keputusan yang Lebih Baik Setiap hari, perusahaan menghasilkan data dari ERP, CRM, aplikasi finance, sistem operasional, website, interaksi pelanggan, hingga berbagai aplikasi lainnya. Namun, memiliki banyak data tidak serta-merta membuat perusahaan menjadi data-driven.</p><p>Business Intelligence membantu mengonsolidasikan seluruh informasi tersebut ke dalam dashboard yang terintegrasi, sehingga management dapat memahami kondisi bisnis dengan lebih cepat dan akurat.</p><p>Advanced Analytics kemudian membawa kemampuan ini selangkah lebih jauh. Jika dashboard tradisional menjawab pertanyaan "Apa yang terjadi?", Advanced Analytics membantu menjawab "Mengapa hal itu terjadi?" dan lebih jauh lagi, "Apa yang memerlukan perhatian kita?"</p><p>Penerapannya dapat dikembangkan untuk berbagai kebutuhan, seperti sales analytics, inventory, supply chain, service financial performance, operational monitoring, performance, hingga management reporting.</p><p>Dengan data yang lebih terintegrasi, pengambilan keputusan dapat dilakukan berdasarkan informasi yang lebih aktual, akurat, dan konsisten di setiap lini bisnis.</p><p>AI/ML for Automation & Insight Mengubah Artificial Intelligence Menjadi Nilai Bisnis Nyata Kecerdasan Buatan yang Memberikan Dampak Terukur Artificial Intelligence membuka peluang besar untuk meningkatkan produktivitas serta mengotomatisasi pekerjaan yang sebelumnya membutuhkan proses manual. AI dan Machine Learning dapat dimanfaatkan untuk melakukan classification, recommendation, information extraction, hingga prediction berdasarkan data anomaly detection, perusahaan.</p><p>Generative AI juga dapat dikembangkan untuk membantu pengguna mencari informasi, membuat ringkasan, memahami dokumen, maupun memperoleh insight dari enterprise knowledge secara lebih cepat dan mudah.</p><p>Sementara itu, Agentic AI dapat dirancang untuk menjalankan serangkaian workflow secara otomatis, berdasarkan akses dan business rules yang telah ditentukan sebelumnya.</p><p>Portofolio Data & AI Sapta Tunas Teknologi mencakup Agentic AI, Computer Vision, dan AI Platform, selain kemampuan Data Management, Analytics, dan Data Lakehouse.</p><p>Implementasi AI idealnya dimulai dari business process yang memiliki tujuan jelas dan terukur. Pendekatan kami bukan menerapkan "AI karena semua orang menggunakan AI," melainkan menghadirkan "AI di mana ia menciptakan nilai bisnis yang nyata dan terukur."</p><p>Analitik & Pengalaman Pelanggan Memahami Pelanggan Melalui Data Data yang Bertanggung Jawab, untuk Pengalaman yang Lebih Relevan Organisasi berinteraksi dengan pelanggan melalui berbagai touchpoint—sales, customer service, sistem transaksi, digital channel, hingga loyalty program, yang semuanya menghasilkan informasi berharga mengenai pelanggan.</p><p>Namun, ketika data tersebut tersebar pada sistem yang berbeda-beda, perusahaan sering kali kesulitan memperoleh gambaran pelanggan secara menyeluruh dan utuh.</p><p>Customer Analytics membantu mengonsolidasikan data tersebut untuk memahami behavior, preferensi, interaksi, dan customer journey secara lebih komprehensif. Melalui analitik ini, organisasi dapat melakukan customer segmentation, memahami pola layanan, mengidentifikasi perubahan behavior, hingga mengukur efektivitas dari campaign dan customer engagement yang dijalankan.</p><p>Intelligence kemudian berperan sebagai supporting Artificial layer, menghasilkan recommendation, classification, maupun insight yang lebih mendalam berdasarkan data tersebut.</p><p>Tujuan akhirnya bukan sekadar mengumpulkan lebih banyak data pelanggan, melainkan jawab untuk menciptakan pengalaman memanfaatkan data secara bertanggung pelanggan yang lebih relevan dan konsisten di setiap interaksi.</p><p>Tata Kelola & Kualitas Data Data yang Terpercaya, Fondasi bagi AI yang Andal Trusted AI Dimulai dari Trusted Data Artificial Intelligence hanya akan menghasilkan insight yang baik apabila didukung oleh data yang berkualitas. Jika informasi memiliki duplicate record, format yang tidak konsisten, nilai yang keliru, atau ownership yang tidak jelas, analytics dan AI pun akan menghasilkan output yang tidak dapat diandalkan.</p><p>Karena itu, Data Governance dan Data Quality menjadi fondasi fundamental dalam modern data architecture.</p><p>Data Governance membantu organisasi menentukan ownership, klasifikasi, akses, dan lifecycle dari enterprise data secara terstruktur. Sementara Data Quality memastikan data memiliki tingkat completeness, consistency, dan accuracy yang memadai untuk digunakan oleh aplikasi, analytics, maupun AI.</p><p>Governance menjadi semakin krusial ketika perusahaan mulai mengimplementasikan Generative AI—tidak semua dokumen atau informasi enterprise seharusnya dapat diakses oleh seluruh pengguna. Access control harus tetap mengikuti otoritas dan klasifikasi dari sumber data asli, tanpa terkecuali.</p><p>Prinsip kami sederhana:</p><p>Trusted AI Dimulai dari Trusted Data.</p><p>Enterprise Data Platform Satu Fondasi Data untuk Analytics dan AI Mengakhiri Data Silo, Membangun Satu Enterprise Asset Salah satu tantangan terbesar bagi enterprise adalah data silo. Informasi tersebar di berbagai database, aplikasi, document repository, dan cloud environment, sehingga sulit dimanfaatkan sebagai satu aset perusahaan yang utuh.</p><p>Sapta Tunas Teknologi membantu membangun modern Enterprise Data Platform yang mampu mengonsolidasikan data structured maupun unstructured ke dalam satu environment terintegrasi, dengan Data Lakehouse sebagai fondasi utama bagi Data Analytics dan Artificial Intelligence.</p><p>Enterprise Data Platform kemudian berperan sebagai penghubung antara source system dengan berbagai consumer, meliputi:</p><p>● ● ● ● ● Business Intelligence Advanced Analytics Machine Learning Generative AI Enterprise Applications Dengan pendekatan ini, perusahaan tidak perlu membangun database baru setiap kali muncul satu use case baru.</p><p>Sebaliknya, organisasi memiliki single scalable data foundation yang dapat mendukung berbagai kebutuhan bisnis di masa depan.</p><p>Portofolio kami menempatkan Data Management, Data Analytics, dan Data Lakehouse sebagai komponen utama dalam arsitektur Data & AI—memastikan setiap keputusan bisnis didukung oleh fondasi data yang kokoh dan terpercaya.</p>','[{"icon": "industri/feat/data-ai-1.png", "judul": "Generative AI", "teks": ""}, {"icon": "industri/feat/data-ai-2.png", "judul": "Private LLM", "teks": ""}, {"icon": "industri/feat/data-ai-3.png", "judul": "Analytics Surveillance Camera", "teks": ""}, {"icon": "industri/feat/data-ai-4.png", "judul": "AI Fraud Detection", "teks": ""}, {"icon": "industri/feat/data-ai-5.png", "judul": "AI Automation Report", "teks": ""}, {"icon": "industri/feat/data-ai-6.png", "judul": "AI Automation Operations", "teks": ""}, {"icon": "industri/feat/data-ai-7.png", "judul": "CX With AI Agent", "teks": ""}]',2),
(7,3,'Zero Trust Security','<p>Jangan Pernah Percaya Otomatis. Selalu Verifikasi.</p><p>Cybersecurity tradisional banyak bergantung pada konsep perimeter—pengguna yang internal network sering kali langsung dianggap trusted. Namun, telah masuk ke penggunaan cloud, mobile device, remote working, dan SaaS membuat batas antara inside dan outside network menjadi semakin tidak jelas.</p><p>Zero Trust hadir dengan pendekatan yang berbeda. Akses diberikan berdasarkan identity, device, policy, context, serta tingkat otorisasi—bukan semata-mata berdasarkan lokasi pengguna berada.</p><p>Prinsip dasarnya sederhana namun tegas:</p><p>Jangan Pernah Percaya Otomatis. Selalu Verifikasi. Berikan Hanya Akses yang Diperlukan.</p><p>Identity and Access Management, endpoint protection, network segmentation, serta continuous monitoring kemudian bekerja secara bersinergi untuk meminimalkan risiko unauthorized access dan lateral movement di dalam sistem.</p><p>Portofolio cybersecurity kami mencakup Access Management, Endpoint Security, dan Network Security sebagai lapisan perlindungan menyeluruh terhadap pengguna, perangkat, dan jaringan perusahaan Anda.</p><p>Managed Security Service Continuous Security Without Building Everything Yourself Cybersecurity membutuhkan monitoring secara terus-menerus.</p><p>Namun membangun security operation sendiri membutuhkan investasi besar terhadap technology, process dan skilled cybersecurity personnel.</p><p>Managed Security Service memberikan alternatif.</p><p>Sapta Tunas Teknologi dapat membantu melakukan centralized monitoring melalui SOC as a Service, Managed SIEM, Managed Detection & Response, Threat Intelligence dan Incident Response.</p><p>Security log dari server, firewall, endpoint dan application dikumpulkan dan dianalisis untuk mendeteksi suspicious activity.</p><p>Ketika sebuah potential incident ditemukan, analyst dapat melakukan investigation dan membantu menentukan response berikutnya.</p><p>Managed Cybersecurity STT secara eksplisit mencakup SOC as a Service, Managed SIEM & Incident Response, Managed Detection & Response dan Managed Threat Intelligence.</p><p>Dengan pendekatan ini perusahaan memperoleh continuous security visibility tanpa harus membangun seluruh Security Operations Center sendiri dari awal.</p><p>Email & Data Protection Melindungi Dua Target Serangan Paling Bernilai Protect the Communication. Protect the Identity. Protect the Data.</p><p>Email merupakan salah satu jalur komunikasi utama enterprise, sekaligus salah satu attack vector yang paling sering dimanfaatkan untuk menyusup ke dalam organisasi. Phishing, lampiran berbahaya, akun yang terkompromi, hingga social engineering kerap menjadi titik awal dari sebuah insiden siber.</p><p>Karena itu, perlindungan email perlu dikombinasikan dengan identity security dan endpoint protection secara menyeluruh.</p><p>Di sisi lain, tujuan akhir dari sebagian besar cyberattack adalah data enterprise itu sendiri. Data perlu dilindungi di setiap tahap—saat disimpan, digunakan, dikirim, maupun ketika harus dipulihkan setelah terjadi insiden.</p><p>Encryption, access control, backup, dan data protection bekerja bersama untuk menjaga confidentiality, integrity, serta availability dari informasi enterprise Anda.</p><p>Pendekatan kami sederhana namun menyeluruh:</p><p>Lindungi Komunikasi. Lindungi Identitas. Lindungi Data.</p><p>Cloud Security Posture Management Visibilitas dan Kontrol Menyeluruh di Seluruh Cloud Environment Bukan Sekadar Cloud Infrastructure, tapi Cloud Infrastructure yang Aman dan Terkelola Perpindahan workload ke cloud menciptakan model keamanan yang berbeda dari traditional data center. Cloud memang menawarkan agility, namun konfigurasi yang tidak tepat dapat membuka celah security exposure yang signifikan.</p><p>Storage yang salah dikonfigurasi, excessive privilege, layanan yang terekspos, atau kebijakan yang tidak konsisten dapat meningkatkan risiko siber secara nyata.</p><p>Cloud Security Posture Management membantu organisasi memperoleh visibilitas menyeluruh terhadap konfigurasi dan security posture dari cloud environment yang digunakan. Tujuannya adalah mengidentifikasi misconfiguration, security exposure, maupun deviasi terhadap kebijakan, sehingga remediation dapat dilakukan sebelum berkembang menjadi insiden keamanan yang serius.</p><p>Pada lingkungan multi-cloud, visibilitas terpusat menjadi semakin krusial, mengingat setiap platform cloud dapat memiliki konfigurasi dan security model yang berbeda-beda.</p><p>Dengan pendekatan ini, organisasi tidak hanya memiliki Cloud Infrastructure, tetapi:</p><p>Cloud Infrastructure yang Governed dan Aman.</p><p>Kesadaran Keamanan & Kepatuhan Cybersecurity Adalah Teknologi, Proses, dan Manusia Melindungi dari Tiga Sisi: People, Process, Technology Teknologi security yang sangat baik pun tetap dapat gagal apabila pengguna tidak memahami risiko cybersecurity. Phishing, social engineering, dan credential theft justru sering menargetkan manusia, bukan infrastruktur.</p><p>Karena itu, cybersecurity yang efektif harus mencakup tiga unsur secara seimbang: People, Process, dan Technology.</p><p>Security Awareness membantu meningkatkan pemahaman karyawan mengenai ancaman keamanan, safe behavior, serta tanggung jawab dalam menjaga informasi enterprise. Sementara compliance membantu organisasi memastikan kebijakan, kontrol, dan prosedur yang berlaku telah sesuai dengan requirement yang relevan.</p><p>Portofolio Cybersecurity Services kami mencakup Security Risk Compliance, Forensic Readiness, ISO 27001 Consulting, serta compliance terkait Personal Data Protection.</p><p>Assessment seperti Vulnerability Assessment, Security Posture Assessment, dan Configuration & Policy Assessment turut digunakan untuk mengevaluasi tingkat maturity dari security environment perusahaan Anda.</p><p>Dengan demikian, keamanan bukan hanya soal "Apakah kita sudah terlindungi?", tetapi juga "Apakah tim kita siap, dan apakah kontrol kita telah dikelola dengan baik?"</p>','[{"icon": "industri/feat/cybersecurity-1.png", "judul": "EDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-2.png", "judul": "NDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-3.png", "judul": "XDR", "teks": ""}, {"icon": "industri/feat/cybersecurity-4.png", "judul": "Next-Generation Firewall", "teks": ""}, {"icon": "industri/feat/cybersecurity-5.png", "judul": "WAF", "teks": ""}, {"icon": "industri/feat/cybersecurity-6.png", "judul": "SIEM", "teks": ""}, {"icon": "industri/feat/cybersecurity-7.png", "judul": "SOAR", "teks": ""}, {"icon": "industri/feat/cybersecurity-8.png", "judul": "Threat Intelligence", "teks": ""}]',3),
(7,4,'Managed Services untuk Ekosistem Digital Lintas Industri yang Stabil, Aman, dan Selalu Siap Digunakan','<p>Baik dalam mengelola jaringan sekolah dan kampus, smart building, maupun hub distribusi, gangguan pada infrastruktur IT, konektivitas jaringan, atau aplikasi kritikal dapat langsung menghambat aktivitas operasional sehari-hari, kualitas layanan, hingga pengalaman para pemangku kepentingan. Managed Services kami membantu organisasi di sektor pendidikan, real estate, hingga transportasi & logistik menjaga infrastruktur IT tetap stabil, aman, dan siap digunakan kapan pun dibutuhkan.</p><p>Layanan kami mencakup pengelolaan end-to-end terhadap lingkungan IT yang tersebar di berbagai lokasi — mulai dari jaringan kampus, server pembelajaran, dan sistem pendukung akademik, hingga jaringan Smart Building, CCTV, access control, dan sistem manajemen gedung, serta konektivitas kantor cabang, gudang, hub, dan pusat distribusi. Melalui monitoring proaktif 24/7/365, potensi gangguan dapat terdeteksi lebih awal dan ditangani sebelum berdampak pada kelancaran proses belajar mengajar, kenyamanan penghuni maupun pengunjung, atau performa pengiriman dan distribusi.</p><p>Untuk memperkuat ketahanan terhadap ancaman siber yang terus berkembang, layanan ini dilengkapi dengan kemampuan advanced seperti Vulnerability Assessment & Penetration Testing (VAPT), Digital Forensic, Cyber Drill, dan Table Top Exercise. Pendekatan ini membantu organisasi mengidentifikasi celah keamanan, menganalisis insiden secara akurat dan terstruktur, menguji kesiapan respons, serta memitigasi risiko siber yang menargetkan data sensitif dan sistem operasional kritikal.</p><p>Dengan Managed Services yang terintegrasi secara end-to-end, organisasi di berbagai sektor ini dapat menjaga stabilitas jaringan dan sistem, mempercepat respons teknis, mengurangi beban tim internal, serta melindungi data sensitif — memastikan operasional tetap aman, andal, dan tanpa gangguan berarti, baik bagi siswa dan tenaga pengajar, penghuni dan pengunjung, maupun mitra dan pelanggan di sepanjang rantai pasok.</p>','[{"icon": "industri/feat/managed-services-1.png", "judul": "VAPT", "teks": ""}, {"icon": "industri/feat/managed-services-2.png", "judul": "Digital Forensic", "teks": ""}, {"icon": "industri/feat/managed-services-3.png", "judul": "Cyber Drill", "teks": ""}, {"icon": "industri/feat/managed-services-4.png", "judul": "Table Top Exercise", "teks": ""}, {"icon": "industri/feat/managed-services-5.png", "judul": "24/7/365", "teks": ""}, {"icon": "industri/feat/managed-services-6.png", "judul": "Patch management", "teks": ""}]',4);

-- Industry dropdown children (parent = Industry menu id=4). Services intentionally has NO children.
INSERT INTO `menus` (`nama`,`url`,`lokasi`,`target`,`urutan`,`parent_id`,`is_active`) VALUES
('Financial Services & E-Commerce','/industri/financial','header','_self',1,4,1),
('Manufacture & FMCG','/industri/manufacture','header','_self',2,4,1),
('Healthcare','/industri/healthcare','header','_self',3,4,1),
('Law Enforcement','/industri/law-enforcement','header','_self',4,4,1),
('Energy','/industri/energy','header','_self',5,4,1),
('Telecommunication (ICT)','/industri/telecom','header','_self',6,4,1),
('Cross Industry','/industri/cross-industry','header','_self',7,4,1),
('All Industries','/industri','header','_self',8,4,1);

-- Industries landing + detail intro (content_blocks; ID + EN)
INSERT INTO `content_blocks` (`page_key`,`block_key`,`lang`,`block_label`,`block_type`,`konten`,`is_active`) VALUES
('industri','title','id','Judul','text','Our Industries',1),
('industri','lead','id','Intro','html','Kami memahami setiap industri punya tantangan dan kebutuhan yang unik. Karena itu STT menghadirkan solusi yang beragam sekaligus disesuaikan dengan kebutuhan klien. Didukung kemitraan strategis serta tim sales, presales, dan teknis bersertifikasi, kami siap membantu Anda menemukan solusi terbaik untuk kebutuhan perusahaan Anda.',1),
('industri','banner_img','id','Banner Coming Soon','image','solutions/coming-soon-banner.png',1),
('industri','banner_url','id','Banner — link','text','#',1),
('industri','detail_intro','id','Intro halaman detail','html','Kami memahami setiap industri punya tantangan dan kebutuhan yang unik. Karena itu STT menghadirkan solusi yang beragam sekaligus disesuaikan dengan kebutuhan klien. Didukung kemitraan strategis serta tim sales, presales, dan teknis bersertifikasi, kami siap membantu Anda menemukan solusi terbaik untuk kebutuhan perusahaan Anda.',1),
('industri','title','en','Judul','text','Our Industries',1),
('industri','lead','en','Intro','html','We understand that every industry has its own unique challenges and needs. That''s why STT provides solutions that are not only diverse but also tailored to client needs. Supported by our partnerships, our certified sales, presales, and technical team, we can assist you to find the best solution that fit into your company requirement.',1),
('industri','banner_img','en','Banner Coming Soon','image','solutions/coming-soon-banner.png',1),
('industri','banner_url','en','Banner — link','text','#',1),
('industri','detail_intro','en','Intro halaman detail','html','We understand that every industry has its own unique challenges and needs. That''s why STT provides solutions that are not only diverse but also tailored to client needs. Supported by our partnerships, our certified sales, presales, and technical team, we can assist you to find the best solution that fit into your company requirement.',1);

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

INSERT INTO `about_items` (`seksi`,`kode`,`judul`,`teks`,`gambar`,`grup`,`tahun`,`urutan`) VALUES
('mission',NULL,NULL,'Build a wide and constructive relationship with client for the mutual long-term business achievement.',NULL,NULL,NULL,1),
('mission',NULL,NULL,'Endless learning to ensure the high quality of people performance through time.',NULL,NULL,NULL,2),
('mission',NULL,NULL,'Nurture the high commitment of honesty, integrity, and professional ethics to achieve the highest value to stakeholder.',NULL,NULL,NULL,3),
('mission',NULL,NULL,'Ensure excellent and reliable support to clients.',NULL,NULL,NULL,4),
('mission',NULL,NULL,'Always making innovations to provide our clients with the best latest technologies.',NULL,NULL,NULL,5),
('mission',NULL,NULL,'Responsible and maintain our core values to ensure customer success.',NULL,NULL,NULL,6),
('value','I','INTEGRITY','Employ high ethical standards, demonstrating honesty and fairness.',NULL,NULL,NULL,1),
('value','C','COLLABORATE','Coming together is a beginning, keeping together is progress, working together is success.',NULL,NULL,NULL,2),
('value','A','ACCOUNTABILITY','Responsibility for our decision and actions.',NULL,NULL,NULL,3),
('value','R','RESPONSIVE','Swift attitude to ensure the best service response and service level to our business partner.',NULL,NULL,NULL,4),
('value','E','EXCELLENCE','Striving for the best in every aspect of the business solution.',NULL,NULL,NULL,5),
('milestone',NULL,'Awal Perjalanan','Sapta Tunas Teknologi didirikan pada 2015 dengan komitmen menghadirkan Business Technology Solutions & Services untuk enterprise di Indonesia.','about/milestone.png',NULL,'2015',1),
('milestone',NULL,'Ekspansi Kapabilitas','Memperluas kapabilitas infrastruktur, cloud, dan data center seiring bertambahnya kepercayaan klien enterprise di berbagai industri.','about/milestone.png',NULL,'2017',2),
('milestone',NULL,'Kemitraan Strategis','Menjalin kemitraan strategis dengan para principal teknologi kelas dunia, termasuk pencapaian status Dell Technologies Titanium Partner.','about/milestone.png',NULL,'2023',3),
('milestone',NULL,'Cybersecurity & AI','Memperkuat lini Cybersecurity, Data Management, dan solusi AI untuk mendukung transformasi digital pelanggan secara menyeluruh.','about/milestone.png',NULL,'2025',4),
('milestone','now','Hari Ini','Dengan tim engineer bersertifikasi, STT terus mendampingi ratusan klien enterprise dalam perjalanan transformasi digital menuju pertumbuhan bisnis berkelanjutan.','about/milestone.png',NULL,'Present',5),
('award',NULL,'Dana Indonesia','Best Performing Vendor 2022','about/award-1.png',NULL,'2022',1),
('award',NULL,'PT Bintang Toedjoe','Best Platinum Vendor Award 2023','about/award-2.png',NULL,'2023',2),
('award',NULL,'PT Saka Farma Laboratories','Excellent Vendor Award 2024','about/award-3.png',NULL,'2024',3),
('award',NULL,'PT Bintang Toedjoe','Best Platinum Vendor Award 2024','about/award-4.png',NULL,'2024',4),
('award',NULL,'PT Pratha Widyahusada Tbk','Vendor Excellence Award 2024','about/award-5.png',NULL,'2024',5),
('award',NULL,'PT Kalbe Morinaga Indonesia','Excellent Vendor Performance Award 2025','about/award-6.png',NULL,'2025',6),
('quality',NULL,'ISO 9001 • ISO 14001 • ISO 45001',NULL,'about/quality-1.png',NULL,NULL,1),
('quality',NULL,'ISO 37001',NULL,'about/quality-2.png',NULL,NULL,2),
('quality',NULL,'ISO 27001',NULL,'about/quality-3.png',NULL,NULL,3),
('cert',NULL,'PowerStore Deploy','Proven Professional · 2023','about/cert-1.png','DELL',NULL,1),
('cert',NULL,'PowerScale Deploy','Proven Professional · 2023','about/cert-2.png','DELL',NULL,2),
('cert',NULL,'ECS Deploy','Proven Professional · 2023','about/cert-3.png','DELL',NULL,3),
('cert',NULL,'PowerProtect Data Domain Deploy','Proven Professional · 2023','about/cert-4.png','DELL',NULL,4),
('cert',NULL,'PowerProtect Cyber Recovery','Proven Professional · 2023','about/cert-5.png','DELL',NULL,5),
('cert',NULL,'PowerProtect Data Manager Deploy','Proven Professional · 2023','about/cert-6.png','DELL',NULL,6);

-- About Us — vision illustrations (image content blocks, default language)
INSERT INTO `content_blocks` (`page_key`,`block_key`,`lang`,`block_label`,`block_type`,`konten`,`is_active`) VALUES
('about','vision_img1','id','Gambar kiri — atas','image','about/vision-1.png',1),
('about','vision_img2','id','Gambar kiri — bawah','image','about/vision-2.png',1);

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

-- ============================================================
-- Clean "blue placeholder" look (see database/migration_placeholders.sql).
-- Drops the low-res demo photos so the template placeholders show; images
-- stay CMS-editable. Runs last so it clears the seeded image paths above.
-- ============================================================
UPDATE `about_items` SET `gambar`=NULL WHERE `seksi` IN ('milestone','award','quality','cert');
UPDATE `content_blocks` SET `konten`='' WHERE `page_key`='about' AND `block_key` IN ('vision_img1','vision_img2');
UPDATE `blog` SET `gambar_utama`=NULL;
INSERT INTO `about_items` (`seksi`,`judul`,`urutan`,`is_active`) VALUES
  ('quality','ISO 20000-1',4,1),('quality','ISO 22301',5,1),('quality','ISO 20000',6,1),('quality','PCI DSS',7,1);
INSERT INTO `about_items` (`seksi`,`judul`,`teks`,`tahun`,`urutan`,`is_active`) VALUES
  ('award','Client Recognition','Best Technology Partner 2023','2023',7,1),
  ('award','Vendor Excellence','Outstanding Delivery 2025','2025',8,1);
-- Placeholder round 2: clear partner logos + more award filler (see migration_placeholders.sql).
UPDATE `solutions_section` SET `partner_img`=NULL;
INSERT INTO `about_items` (`seksi`,`judul`,`teks`,`tahun`,`urutan`,`is_active`) VALUES
  ('award','Innovation Award','Technology Innovation 2021','2021',9,1),
  ('award','Rising Partner','Fastest Growing Partner 2021','2021',10,1),
  ('award','Top Solution Provider','Top Solution Provider 2022','2022',11,1),
  ('award','Digital Excellence','Digital Excellence 2023','2023',12,1),
  ('award','Best Enterprise Partner','Best Enterprise Partner 2026','2026',13,1),
  ('award','Technology Leadership','Technology Leadership 2026','2026',14,1);
