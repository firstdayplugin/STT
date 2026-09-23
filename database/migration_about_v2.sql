-- ============================================================
-- Migration: About Us v2 (image slots + milestone/award slider data)
-- Safe to run on an existing database (idempotent; only fills empty fields).
-- In phpMyAdmin: select your DB, open SQL, paste this, Go.
-- ============================================================

-- 1) New columns on about_items (image, brand group, gallery)
ALTER TABLE `about_items`
  ADD COLUMN IF NOT EXISTS `gambar` VARCHAR(255) DEFAULT NULL AFTER `teks`,
  ADD COLUMN IF NOT EXISTS `grup`   VARCHAR(60)  DEFAULT NULL AFTER `gambar`,
  ADD COLUMN IF NOT EXISTS `galeri` TEXT         DEFAULT NULL AFTER `grup`;

-- 2) Milestone: heading + paragraph per year (only where still empty)
UPDATE `about_items` SET `judul`='Awal Perjalanan',
  `teks`='Sapta Tunas Teknologi didirikan pada 2015 dengan komitmen menghadirkan Business Technology Solutions & Services untuk enterprise di Indonesia.'
  WHERE `seksi`='milestone' AND `tahun`='2015' AND (`teks` IS NULL OR `teks`='');
UPDATE `about_items` SET `judul`='Ekspansi Kapabilitas',
  `teks`='Memperluas kapabilitas infrastruktur, cloud, dan data center seiring bertambahnya kepercayaan klien enterprise di berbagai industri.'
  WHERE `seksi`='milestone' AND `tahun`='2017' AND (`teks` IS NULL OR `teks`='');
UPDATE `about_items` SET `judul`='Kemitraan Strategis',
  `teks`='Menjalin kemitraan strategis dengan para principal teknologi kelas dunia, termasuk pencapaian status Dell Technologies Titanium Partner.'
  WHERE `seksi`='milestone' AND `tahun`='2023' AND (`teks` IS NULL OR `teks`='');
UPDATE `about_items` SET `judul`='Cybersecurity & AI',
  `teks`='Memperkuat lini Cybersecurity, Data Management, dan solusi AI untuk mendukung transformasi digital pelanggan secara menyeluruh.'
  WHERE `seksi`='milestone' AND `tahun`='2025' AND (`teks` IS NULL OR `teks`='');
UPDATE `about_items` SET `judul`='Hari Ini',
  `teks`='Dengan tim engineer bersertifikasi, STT terus mendampingi ratusan klien enterprise dalam perjalanan transformasi digital menuju pertumbuhan bisnis berkelanjutan.'
  WHERE `seksi`='milestone' AND `tahun`='Present' AND (`teks` IS NULL OR `teks`='');

-- 3) Awards: fill the year used by the per-year slider (only where empty)
UPDATE `about_items` SET `tahun`='2022' WHERE `seksi`='award' AND `urutan`=1 AND (`tahun` IS NULL OR `tahun`='');
UPDATE `about_items` SET `tahun`='2023' WHERE `seksi`='award' AND `urutan`=2 AND (`tahun` IS NULL OR `tahun`='');
UPDATE `about_items` SET `tahun`='2024' WHERE `seksi`='award' AND `urutan` IN (3,4,5) AND (`tahun` IS NULL OR `tahun`='');
UPDATE `about_items` SET `tahun`='2025' WHERE `seksi`='award' AND `urutan`=6 AND (`tahun` IS NULL OR `tahun`='');

-- 4) Certifications: brand grouping for the per-brand slider (only where empty)
UPDATE `about_items` SET `grup`='DELL' WHERE `seksi`='cert' AND (`grup` IS NULL OR `grup`='');
