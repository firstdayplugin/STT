-- ============================================================
-- About Us improvements:
--   1) Milestone timeline completed 2015 -> Present (per client deck).
--   2) Award titles cleaned: drop the "Sapta Tunas Teknologi" prefix
--      (and the FY211 typo) so the award name reads cleanly.
-- Idempotent.
-- ============================================================
SET NAMES utf8mb4;

-- ---------- 1) MILESTONES ----------
-- Remove old milestone rows + any stale i18n, then insert the full timeline.
DELETE FROM content_i18n WHERE tabel='about_items' AND row_id IN (SELECT id FROM about_items WHERE seksi='milestone');
DELETE FROM about_items WHERE seksi='milestone';

INSERT INTO about_items (seksi, kode, tahun, judul, teks, gambar, urutan, is_active) VALUES
('milestone', NULL, '2015',    'Awal Perjalanan',      'Sapta Tunas Teknologi resmi berdiri pada 8 Mei 2015, berkomitmen menghadirkan Business Technology Solutions & Services untuk enterprise di Indonesia.', 'design3/office-plant.jpg',    1, 1),
('milestone', NULL, '2016',    'Kemitraan Pertama',    'Dipercaya sebagai Trusted Solution Provider Dell Technologies dan Solution Provider Professional untuk VMware by Broadcom.',                          'design3/office-desks.jpg',    2, 1),
('milestone', NULL, '2017',    'Naik Kelas',           'Meraih status Dell Technologies Platinum Partner dan menjalin kemitraan dengan Microsoft.',                                                          'design3/team-meeting.jpg',    3, 1),
('milestone', NULL, '2018',    'Titanium Partner',     'Menjadi Dell Technologies Titanium Partner serta Advanced Partner untuk VMware by Broadcom.',                                                        'design3/glass-facade.jpg',    4, 1),
('milestone', NULL, '2019',    'Perlindungan Data',    'Memperkuat lini data protection melalui kemitraan dengan Veeam dan Commvault.',                                                                      'design3/office-open.jpg',     5, 1),
('milestone', NULL, '2020',    'Keamanan & Jaringan',  'Melengkapi solusi keamanan dan jaringan bersama Cisco dan Fortinet.',                                                                                'design3/monitor-dark.jpg',    6, 1),
('milestone', NULL, '2021',    'Open Source Enterprise','Menghadirkan solusi open source enterprise melalui kemitraan dengan Red Hat.',                                                                      'design3/desk-window.jpg',     7, 1),
('milestone', NULL, '2022',    'Era AI & Data',        'Memasuki era AI dan high-performance data lewat kemitraan dengan NVIDIA dan Weka.',                                                                  'design3/ai-phone.jpg',        8, 1),
('milestone', NULL, '2023',    'Kemitraan Strategis',  'Memperluas ekosistem solusi bersama Sangfor, Nutanix, dan HYCU.',                                                                                    'design3/office-corridor.jpg', 9, 1),
('milestone', NULL, '2024',    'Cybersecurity & Cloud','Menambah kapabilitas lewat Dell Advanced Performing Services, Elastic, Cyble, dan Rafay.',                                                          'design3/team-work.jpg',      10, 1),
('milestone', NULL, '2025',    'AI & Analytics',       'Memperkuat lini AI dan analytics bersama infraon, SenseTime, Xeratic, dan Soca.',                                                                    'design3/imac-design.jpg',    11, 1),
('milestone', 'now','Present', 'Hari Ini',             'Terus bertumbuh dengan kemitraan AMD, T-Innoware, dan Redis — didukung tim engineer bersertifikasi untuk transformasi teknologi enterprise.',        'design3/towers-up.jpg',      12, 1);

-- ---------- 2) AWARD TITLES ----------
UPDATE about_items SET judul = TRIM(REPLACE(judul, 'Sapta Tunas Teknologi ', ''))
  WHERE seksi='award' AND judul LIKE 'Sapta Tunas Teknologi %';
UPDATE about_items SET judul = REPLACE(judul, 'FY211', 'FY21')
  WHERE seksi='award' AND judul LIKE '%FY211%';
-- Same cleanup for any EN i18n overrides on award titles.
UPDATE content_i18n SET nilai = TRIM(REPLACE(nilai, 'Sapta Tunas Teknologi ', ''))
  WHERE tabel='about_items' AND field='judul' AND nilai LIKE 'Sapta Tunas Teknologi %';
