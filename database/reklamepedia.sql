-- ============================================================
-- REKLAMEPEDIA CMS DATABASE SCHEMA
-- Version: 1.0.0
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `reklamepedia` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `reklamepedia`;

-- ============================================================
-- PENGATURAN UMUM WEBSITE
-- ============================================================
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `setting_group` varchar(50) DEFAULT 'general',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('site_name', 'Reklamepedia', 'general'),
('site_tagline', 'Mitra Terpercaya untuk Kebutuhan Reklame & Advertising', 'general'),
('site_description', 'Reklamepedia adalah perusahaan advertising berpengalaman lebih dari 15 tahun yang menghadirkan solusi reklame berkualitas untuk meningkatkan visibilitas brand di seluruh Indonesia.', 'general'),
('site_logo', '', 'general'),
('site_logo_type', 'text', 'general'),
('site_favicon', '', 'general'),
('site_email', 'reklamepedia@gmail.com', 'general'),
('site_phone', '+62 123 456 7890', 'general'),
('site_whatsapp', '6212345678900', 'general'),
('site_address', 'Jl. Contoh No. 123, Yogyakarta', 'general'),
('site_jam_operasional', 'Senin - Sabtu: 08.00 - 17.00 WIB', 'general'),
('site_maps_embed', '', 'general'),
('active_theme', 'default', 'general'),
('setup_completed', '0', 'general'),
('wa_float_enabled', '1', 'whatsapp'),
('wa_float_message', 'Halo, saya ingin konsultasi mengenai reklame', 'whatsapp'),
('hero_type', 'single', 'hero'),
('hero_title', 'Mitra Terpercaya untuk Kebutuhan Reklame & Advertising', 'hero'),
('hero_subtitle', 'Bangun visibilitas dan perkuat branding bisnis Anda melalui solusi reklame yang strategis dan berdampak. Lebih dari 1.200 klien di seluruh Indonesia telah mempercayakan kebutuhan advertising mereka kepada kami.', 'hero'),
('hero_image', '', 'hero'),
('hero_cta_text', 'Hubungi Kami!', 'hero'),
('hero_cta_url', '/contact', 'hero'),
('about_title', 'Reklamepedia hadir untuk membantu meningkatkan visibilitas dan memperkuat identitas bisnis anda melalui solusi reklame berkualitas berpengalaman lebih dari 15 tahun.', 'about'),
('about_description', 'Reklamepedia adalah perusahaan advertising berpengalaman lebih dari 15 tahun yang menghadirkan solusi reklame berkualitas untuk meningkatkan visibilitas brand di seluruh Indonesia.', 'about'),
('stat_projects', '700+', 'stats'),
('stat_cities', '14', 'stats'),
('stat_clients', '10k', 'stats'),
('performance_title', 'Built on Experience, Driven by Results', 'stats'),
('performance_desc', 'Selama lebih dari 15 tahun, kami telah membantu berbagai bisnis memperkuat identitas dan meningkatkan visibilitas brand melalui solusi reklame yang konsisten dan terpercaya.', 'stats'),
('footer_description', 'Reklamepedia adalah perusahaan advertising berpengalaman lebih dari 15 tahun yang menghadirkan solusi reklame berkualitas untuk meningkatkan visibilitas brand di seluruh Indonesia.', 'footer'),
('cta_section_title', "Let's Elevate Your Brand Visibility.", 'cta'),
('cta_section_subtitle', 'Solusi reklame efektif untuk meningkatkan daya tarik dan eksistensi brand Anda.', 'cta'),
('cta_button_text', 'Hubungi Kami!', 'cta'),
('social_instagram', '', 'social'),
('social_facebook', '', 'social'),
('social_tiktok', '', 'social'),
('social_youtube', '', 'social'),
('meta_title', 'Reklamepedia - Solusi Reklame Terpercaya Indonesia', 'seo'),
('meta_description', 'Reklamepedia hadir sebagai mitra terpercaya untuk kebutuhan reklame dan advertising bisnis Anda.', 'seo'),
('meta_keywords', 'reklame, neon box, billboard, huruf timbul, advertising, signage', 'seo'),
('google_analytics_id', '', 'analytics'),
('google_tag_manager', '', 'analytics'),
('meta_pixel_id', '', 'analytics'),
('tiktok_pixel_id', '', 'analytics'),
('custom_header_script', '', 'analytics'),
('custom_footer_script', '', 'analytics'),
('google_search_console', '', 'seo'),
('robots_txt_extra', '', 'seo');

-- ============================================================
-- PENGGUNA ADMIN
-- ============================================================
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','penulis','admin_produk','tim_ads') NOT NULL DEFAULT 'penulis',
  `foto` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`nama`, `username`, `email`, `password`, `role`) VALUES
('Super Admin', 'admin', 'admin@reklamepedia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin');
-- password default: password

-- ============================================================
-- MENU NAVIGASI
-- ============================================================
CREATE TABLE `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `parent_id` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `menus` (`nama`, `url`, `urutan`, `status`, `is_default`) VALUES
('Home', '/', 1, 1, 1),
('Tentang Kami', '/about', 2, 1, 1),
('Layanan', '/services', 3, 1, 1),
('Gallery', '/gallery', 4, 1, 1),
('Blog', '/blog', 5, 1, 1),
('Hubungi Kami', '/contact', 6, 1, 1);

-- ============================================================
-- LAYANAN
-- ============================================================
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `nomor` varchar(10) DEFAULT NULL,
  `deskripsi_singkat` text DEFAULT NULL,
  `deskripsi` longtext DEFAULT NULL,
  `gambar_utama` varchar(255) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `tampil_di_menu` tinyint(1) NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `services` (`nama`, `slug`, `nomor`, `deskripsi_singkat`, `deskripsi`, `urutan`, `status`, `tampil_di_menu`) VALUES
('Neon Box', 'neon-box', '01', 'Spesialis dalam pembuatan Neon Box dengan pengalaman mengerjakan berbagai desain dan bentuk menggunakan beragam material berkualitas.', '<p>Kami menyediakan berbagai jenis Neon Box berkualitas tinggi untuk kebutuhan bisnis Anda.</p>', 1, 1, 1),
('Billboard', 'billboard', '02', 'Solusi billboard premium untuk visibilitas brand yang maksimal di lokasi strategis.', '<p>Billboard kami dirancang untuk menarik perhatian dan memperkuat brand awareness.</p>', 2, 1, 1),
('Huruf Timbul', 'huruf-timbul', '03', 'Spesialis pembuatan Signage Huruf Timbul berpengalaman 15+ tahun, dipercaya 400+ klien.', '<p>Huruf timbul berkualitas tinggi dengan berbagai pilihan material.</p>', 3, 1, 1),
('Pylon Sign', 'pylon-sign', '04', 'Pylon sign monumental untuk identitas bisnis yang kuat dan mudah terlihat dari jarak jauh.', '<p>Pylon sign kami hadir dalam berbagai ukuran dan desain sesuai kebutuhan.</p>', 4, 1, 1),
('Neon LED Flex', 'neon-led-flex', '05', 'Solusi pencahayaan neon LED fleksibel untuk berbagai kebutuhan dekorasi dan branding.', '<p>Neon LED Flex modern dengan efisiensi energi tinggi dan tampilan premium.</p>', 5, 1, 1),
('Vehicle Branding', 'vehicle-branding', '06', 'Transformasi kendaraan menjadi media iklan bergerak yang efektif dan profesional.', '<p>Vehicle branding kami menggunakan material terbaik untuk ketahanan maksimal.</p>', 6, 1, 1);

-- ============================================================
-- SUB LAYANAN (untuk halaman detail layanan)
-- ============================================================
CREATE TABLE `service_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `nama` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `fk_service_items` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `service_items` (`service_id`, `nama`, `deskripsi`, `urutan`) VALUES
(1, 'Neon Box Kotak', 'Neon box kotak menjadi media promosi andalan bagi usaha kios, outlet, perusahaan, sampai instansi pemerintah.', 1),
(1, 'Neon Box Bulat', 'Beberapa jenis bisnis yang sering menggunakan neon box jenis ini adalah cafe, food & beverages, restoran, dan lain sebagainya.', 2),
(1, 'Neon Box Custom', 'Jenis ini paling banyak diminati oleh beberapa pebisnis yang artinya dapat dibentuk sesuai dengan desain yang diinginkan.', 3),
(3, 'Huruf Timbul Akrilik', 'Terbuat dari bahan acrylic dan led berkualitas menjadikannya produk yang menawan dan bermutu tinggi.', 1),
(3, 'Huruf Timbul LED', 'Jasa pembuatan huruf timbul LED. Kami berikan produk yang durable dan berkualitas untuk Anda.', 2),
(3, 'Huruf Timbul Stainless', 'Huruf Timbul Stainless bermutu tinggi. Menggunakan material type 201 & 304 yang berdurabilitas tinggi.', 3),
(3, 'Huruf Timbul Galvanis', 'Menggunakan finishing cat duco menjadikannya tampil sesuai desain yang anda inginkan.', 4),
(3, 'Huruf Timbul Kuningan', 'Menggunakan material terbaik membuat huruf timbul kuningan anda tampil elegan dan mewah.', 5),
(3, 'Huruf Timbul Akrilik Laser', 'Terbuat dari bahan acrylic dan led berkualitas menjadikannya produk yang menawan dan bermutu tinggi.', 6);

-- ============================================================
-- GALERI LAYANAN
-- ============================================================
CREATE TABLE `service_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `fk_service_gallery` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- GALERI UTAMA
-- ============================================================
CREATE TABLE `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- KATEGORI GALERI
-- ============================================================
CREATE TABLE `gallery_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `gallery_categories` (`nama`, `slug`, `urutan`) VALUES
('Neon Box', 'neon-box', 1),
('Billboard', 'billboard', 2),
('Huruf Timbul', 'huruf-timbul', 3),
('Pylon Sign', 'pylon-sign', 4),
('Neon LED Flex', 'neon-led-flex', 5),
('Vehicle Branding', 'vehicle-branding', 6);

-- ============================================================
-- PRODUK / KATALOG
-- ============================================================
CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `deskripsi_singkat` text DEFAULT NULL,
  `deskripsi` longtext DEFAULT NULL,
  `harga` decimal(15,2) DEFAULT NULL,
  `gambar_utama` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_product_gallery` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `tag` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_product_tags` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Plugin marketplace URLs untuk produk
CREATE TABLE `product_marketplace` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `platform` enum('tokopedia','shopee','lazada','blibli','bukalapak') NOT NULL,
  `url` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_product_marketplace` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BLOG
-- ============================================================
CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `blog_categories` (`nama`, `slug`, `urutan`) VALUES
('Tips Reklame', 'tips-reklame', 1),
('Inspirasi Desain', 'inspirasi-desain', 2),
('Berita Industri', 'berita-industri', 3),
('Project Showcase', 'project-showcase', 4);

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(500) NOT NULL,
  `slug` varchar(500) NOT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `konten` longtext DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `gambar_utama` varchar(255) DEFAULT NULL,
  `status` enum('draft','publish','scheduled') NOT NULL DEFAULT 'draft',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `author_id` (`author_id`),
  KEY `kategori_id` (`kategori_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blog_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `tag` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `fk_blog_tags` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- HERO SLIDER
-- ============================================================
CREATE TABLE `hero_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) DEFAULT NULL,
  `subtitle` text DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `cta_text` varchar(100) DEFAULT NULL,
  `cta_url` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TESTIMONI
-- ============================================================
CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `perusahaan` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `komentar` text NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `testimonials` (`nama`, `jabatan`, `komentar`, `rating`, `urutan`, `status`) VALUES
('Budi Santoso', 'CEO', 'Hasil kerja Reklamepedia sangat memuaskan. Neon box yang kami pesan selesai tepat waktu dengan kualitas yang sangat baik. Tim mereka sangat profesional dan responsif.', 5, 1, 1),
('Sari Dewi', 'Marketing Manager', 'Sudah 3 kali menggunakan jasa Reklamepedia dan selalu puas. Kualitas material bagus, harga kompetitif, dan pelayanan ramah. Sangat recommended!', 5, 2, 1),
('Ahmad Fauzi', 'Owner', 'Billboard yang dipasang oleh Reklamepedia benar-benar meningkatkan awareness bisnis kami. Desain sesuai ekspektasi dan pemasangan sangat rapi.', 5, 3, 1);

-- ============================================================
-- FAQ
-- ============================================================
CREATE TABLE `faqs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pertanyaan` text NOT NULL,
  `jawaban` longtext NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `faqs` (`pertanyaan`, `jawaban`, `urutan`) VALUES
('Berapa lama waktu pengerjaan reklame?', 'Waktu pengerjaan bervariasi, umumnya 1–4 minggu tergantung ukuran dan kompleksitas. Kami selalu berkomitmen memberikan hasil tepat waktu tanpa mengurangi kualitas.', 1),
('Apakah desain dapat disesuaikan sesuai dengan brand kamu?', 'Ya, tentu saja! Kami menyediakan layanan desain custom yang sepenuhnya disesuaikan dengan kebutuhan dan identitas brand Anda. Tim desainer kami siap membantu mewujudkan visi Anda.', 2),
('Apakah ada layanan survey lokasi?', 'Ya, kami menyediakan layanan survey lokasi gratis untuk memastikan penempatan reklame yang optimal dan sesuai dengan regulasi setempat.', 3),
('Bagaimana dengan kualitas material yang digunakan?', 'Kami hanya menggunakan material berkualitas tinggi yang telah teruji ketahanannya. Setiap proyek menggunakan material terbaik sesuai kebutuhan dan anggaran klien.', 4),
('Apakah ada garansi untuk reklame yang dibuat?', 'Ya, setiap produk yang kami hasilkan dilengkapi dengan garansi. Durasi garansi bervariasi tergantung jenis produk. Kami berkomitmen memberikan purna jual yang terbaik.', 5);

-- ============================================================
-- LOGO KLIEN / PARTNER
-- ============================================================
CREATE TABLE `client_logos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- KEUNGGULAN / WHY CHOOSE US
-- ============================================================
CREATE TABLE `advantages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `advantages` (`judul`, `deskripsi`, `urutan`, `status`) VALUES
('Free Consultation & Site Survey', 'Konsultasi dan survey lokasi gratis untuk memastikan solusi dan penempatan reklame yang tepat.', 1, 1),
('Quality Assurance & Warranty', 'Setiap produk dilengkapi jaminan kualitas untuk memastikan kepuasan dan kepercayaan pelanggan.', 2, 1),
('Experienced Professional Team', 'Dikerjakan oleh tenaga ahli berpengalaman dengan hasil rapi, presisi, dan berkualitas.', 3, 1);

-- ============================================================
-- GRID ICON BOX (Custom Content Blocks)
-- ============================================================
CREATE TABLE `icon_boxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `posisi` enum('bawah_hero','tengah','atas_footer') NOT NULL DEFAULT 'tengah',
  `kolom` enum('3','4') NOT NULL DEFAULT '3',
  `urutan` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CUSTOM TEXT AREAS
-- ============================================================
CREATE TABLE `custom_texts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) DEFAULT NULL,
  `konten` longtext NOT NULL,
  `posisi` enum('bawah_hero','tengah','atas_footer') NOT NULL DEFAULT 'tengah',
  `urutan` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PESAN / KONTAK
-- ============================================================
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `pesan` text NOT NULL,
  `status` enum('baru','dibaca','dibalas') NOT NULL DEFAULT 'baru',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- VISITOR TRACKING (ringan)
-- ============================================================
CREATE TABLE `visitor_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `total_kunjungan` int(11) NOT NULL DEFAULT 0,
  `unique_visitor` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- WA CLICK TRACKING
-- ============================================================
CREATE TABLE `wa_clicks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ACTIVITY LOG
-- ============================================================
CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `aksi` varchar(255) NOT NULL,
  `detail` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PLUGIN SYSTEM
-- ============================================================
CREATE TABLE `plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `versi` varchar(20) NOT NULL DEFAULT '1.0.0',
  `deskripsi` text DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `installed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `plugins` (`nama`, `slug`, `versi`, `deskripsi`, `author`, `status`) VALUES
('Marketplace URL Produk', 'marketplace-url', '1.0.0', 'Menambahkan URL marketplace (Tokopedia, Shopee, Lazada, Blibli, Bukalapak) pada produk.', 'Reklamepedia', 1);

-- ============================================================
-- TEMA / TEMPLATE SYSTEM
-- ============================================================
CREATE TABLE `themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `versi` varchar(20) NOT NULL DEFAULT '1.0.0',
  `deskripsi` text DEFAULT NULL,
  `preview_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `themes` (`nama`, `slug`, `versi`, `deskripsi`, `status`, `is_active`) VALUES
('Reklamepedia Default', 'default', '1.0.0', 'Template default Reklamepedia dengan desain modern futuristik 2026.', 1, 1);

-- ============================================================
-- SETUP WIZARD PROGRESS
-- ============================================================
CREATE TABLE `setup_wizard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `step` int(11) NOT NULL,
  `nama_step` varchar(100) NOT NULL,
  `selesai` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `step` (`step`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `setup_wizard` (`step`, `nama_step`, `selesai`) VALUES
(1, 'Nama Bisnis', 0), (2, 'Upload Logo', 0), (3, 'Upload Favicon', 0),
(4, 'Tipe Logo', 0), (5, 'Nomor WhatsApp', 0), (6, 'Email', 0),
(7, 'Alamat', 0), (8, 'Jam Operasional', 0), (9, 'Upload Hero Image', 0),
(10, 'Pilih Template', 0), (11, 'Sosial Media', 0), (12, 'Style Warna', 0),
(13, 'Google Maps', 0), (14, 'Setup SEO', 0), (15, 'Floating WhatsApp', 0),
(16, 'Setup Marketplace', 0), (17, 'Google Analytics', 0), (18, 'Google Search Console', 0),
(19, 'Selesai', 0);

-- ============================================================
-- WHATSAPP MULTI NOMOR
-- ============================================================
CREATE TABLE `whatsapp_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `nomor` varchar(20) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `whatsapp_numbers` (`nama`, `nomor`, `jabatan`, `is_default`, `status`) VALUES
('Tim Sales', '6212345678900', 'Customer Service', 1, 1);

-- ============================================================
-- PERMALINK SETTINGS
-- ============================================================
CREATE TABLE `permalink_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `struktur` varchar(255) NOT NULL DEFAULT '/%slug%',
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permalink_settings` (`type`, `struktur`) VALUES
('blog', '/blog/%slug%'),
('produk', '/produk/%slug%'),
('layanan', '/layanan/%slug%'),
('gallery', '/gallery/%slug%');
