-- ============================================================
-- DESIGN OPTION 3 — real photos in EVERY CMS image slot.
-- Points all image fields at the curated photos under uploads/design3/*.jpg
-- Run AFTER uploading the design3/ image folder into uploads/.
--
-- Scope: "Design 3" preview only (same layout as the final blueviolet design,
-- but with real photos so the client sees the full finished look).
-- Idempotent + non-destructive. To revert to blue placeholders re-run
-- migration_placeholders.sql (and clear the slots below via CMS).
-- ============================================================
SET NAMES utf8mb4;

-- ---- Home hero slides (match by title) ----------------------------------
UPDATE hero_slides SET gambar='design3/city-skyline.jpg'  WHERE judul='Growing The Global';
UPDATE hero_slides SET gambar='design3/ai-phone.jpg'      WHERE judul='AI-driven';
UPDATE hero_slides SET gambar='design3/glass-facade.jpg'  WHERE judul='Resilient';
UPDATE hero_slides SET gambar='design3/monitor-dark.jpg'  WHERE judul='Cybersecurity';
UPDATE hero_slides SET gambar='design3/desk-window.jpg'   WHERE judul='Data-driven';

-- ---- Home: Portfolio cross-fade images (content_blocks page 'home') ------
INSERT INTO content_blocks (page_key,block_key,lang,block_label,block_type,konten,is_active) VALUES
 ('home','pf_img1','id','Portfolio image 1','image','design3/towers-up.jpg',1),
 ('home','pf_img1','en','Portfolio image 1','image','design3/towers-up.jpg',1),
 ('home','pf_img2','id','Portfolio image 2','image','design3/office-open.jpg',1),
 ('home','pf_img2','en','Portfolio image 2','image','design3/office-open.jpg',1),
 ('home','pf_img3','id','Portfolio image 3','image','design3/city-skyline.jpg',1),
 ('home','pf_img3','en','Portfolio image 3','image','design3/city-skyline.jpg',1),
 ('home','pf_img4','id','Portfolio image 4','image','design3/glass-facade.jpg',1),
 ('home','pf_img4','en','Portfolio image 4','image','design3/glass-facade.jpg',1)
ON DUPLICATE KEY UPDATE konten=VALUES(konten), block_type='image', is_active=1;

-- ---- Home: Why Us cards (content_blocks page 'home') ---------------------
INSERT INTO content_blocks (page_key,block_key,lang,block_label,block_type,konten,is_active) VALUES
 ('home','why1_img','id','Why image 1','image','design3/office-plant.jpg',1),
 ('home','why1_img','en','Why image 1','image','design3/office-plant.jpg',1),
 ('home','why2_img','id','Why image 2','image','design3/office-desks.jpg',1),
 ('home','why2_img','en','Why image 2','image','design3/office-desks.jpg',1),
 ('home','why3_img','id','Why image 3','image','design3/team-work.jpg',1),
 ('home','why3_img','en','Why image 3','image','design3/team-work.jpg',1),
 ('home','why4_img','id','Why image 4','image','design3/ai-phone.jpg',1),
 ('home','why4_img','en','Why image 4','image','design3/ai-phone.jpg',1)
ON DUPLICATE KEY UPDATE konten=VALUES(konten), block_type='image', is_active=1;

-- ---- Home: Our Industries orbit (sector cards) --------------------------
UPDATE industri SET gambar='design3/towers-up.jpg'      WHERE id=1;  -- Financial & E-Commerce
UPDATE industri SET gambar='design3/office-open.jpg'    WHERE id=2;  -- Manufacture & FMCG
UPDATE industri SET gambar='design3/desk-window.jpg'    WHERE id=3;  -- Healthcare
UPDATE industri SET gambar='design3/glass-facade.jpg'   WHERE id=4;  -- Law Enforcement
UPDATE industri SET gambar='design3/city-skyline.jpg'   WHERE id=5;  -- Energy
UPDATE industri SET gambar='design3/transit.jpg'        WHERE id=6;  -- Telecommunication (ICT)
UPDATE industri SET gambar='design3/office-corridor.jpg' WHERE id=7; -- Cross Industry

-- ---- Home: Solutions prism (3D cube slides) -----------------------------
UPDATE solution_slides SET gambar='design3/office-open.jpg'  WHERE id=1; -- Infrastructure
UPDATE solution_slides SET gambar='design3/monitor-dark.jpg' WHERE id=2; -- Cybersecurity
UPDATE solution_slides SET gambar='design3/desk-window.jpg'  WHERE id=3; -- Data & Analytics
UPDATE solution_slides SET gambar='design3/ai-phone.jpg'     WHERE id=4; -- AI
UPDATE solution_slides SET gambar='design3/imac-design.jpg'  WHERE id=5; -- AI Platform

-- ---- Home / What They Say: testimonial avatars --------------------------
UPDATE testimonial SET foto='design3/avatar-1.jpg' WHERE id=1;
UPDATE testimonial SET foto='design3/avatar-2.jpg' WHERE id=2;
UPDATE testimonial SET foto='design3/avatar-3.jpg' WHERE id=3;
UPDATE testimonial SET foto='design3/avatar-4.jpg' WHERE id=4;
UPDATE testimonial SET foto='design3/avatar-5.jpg' WHERE id=5;
UPDATE testimonial SET foto='design3/avatar-6.jpg' WHERE id=6;

-- ---- About: Vision & Mission illustrations ------------------------------
INSERT INTO content_blocks (page_key,block_key,lang,block_label,block_type,konten,is_active) VALUES
 ('about','vision_img1','id','Vision image 1','image','design3/glass-facade.jpg',1),
 ('about','vision_img1','en','Vision image 1','image','design3/glass-facade.jpg',1),
 ('about','vision_img2','id','Vision image 2','image','design3/city-skyline.jpg',1),
 ('about','vision_img2','en','Vision image 2','image','design3/city-skyline.jpg',1)
ON DUPLICATE KEY UPDATE konten=VALUES(konten), block_type='image', is_active=1;

-- ---- Career: team photo -------------------------------------------------
INSERT INTO content_blocks (page_key,block_key,lang,block_label,block_type,konten,is_active) VALUES
 ('career','team_image','id','Team photo','image','design3/office-open.jpg',1),
 ('career','team_image','en','Team photo','image','design3/office-open.jpg',1)
ON DUPLICATE KEY UPDATE konten=VALUES(konten), block_type='image', is_active=1;

-- ---- About: milestone timeline (match by year) --------------------------
UPDATE about_items SET gambar='design3/office-plant.jpg' WHERE seksi='milestone' AND tahun='2015';
UPDATE about_items SET gambar='design3/office-desks.jpg' WHERE seksi='milestone' AND tahun='2017';
UPDATE about_items SET gambar='design3/office-open.jpg'  WHERE seksi='milestone' AND tahun='2023';
UPDATE about_items SET gambar='design3/monitor-dark.jpg' WHERE seksi='milestone' AND tahun='2025';
UPDATE about_items SET gambar='design3/towers-up.jpg'    WHERE seksi='milestone' AND tahun='Present';

-- ---- About: Awards / Quality / Certifications (rotate the pool) ----------
-- ELT() picks the Nth path; (id MOD n) spreads photos across the cards.
UPDATE about_items SET gambar = ELT(1+(id MOD 8),
  'design3/towers-up.jpg','design3/glass-facade.jpg','design3/city-skyline.jpg',
  'design3/office-corridor.jpg','design3/office-open.jpg','design3/office-desks.jpg',
  'design3/transit.jpg','design3/desk-imac.jpg') WHERE seksi='award';

UPDATE about_items SET gambar = ELT(1+(id MOD 7),
  'design3/office-corridor.jpg','design3/office-desks.jpg','design3/office-open.jpg',
  'design3/desk-window.jpg','design3/desk-imac.jpg','design3/imac-design.jpg',
  'design3/monitor-dark.jpg') WHERE seksi='quality';

UPDATE about_items SET gambar = ELT(1+(id MOD 6),
  'design3/glass-facade.jpg','design3/towers-up.jpg','design3/city-skyline.jpg',
  'design3/ai-phone.jpg','design3/monitor-dark.jpg','design3/transit.jpg') WHERE seksi='cert';

-- ---- Blog: featured + grid images (seeded demo posts) -------------------
UPDATE blog SET gambar_utama='design3/ai-phone.jpg'     WHERE id=1;
UPDATE blog SET gambar_utama='design3/office-open.jpg'  WHERE id=2;
UPDATE blog SET gambar_utama='design3/glass-facade.jpg' WHERE id=3;
UPDATE blog SET gambar_utama='design3/monitor-dark.jpg' WHERE id=4;
UPDATE blog SET gambar_utama='design3/desk-imac.jpg'    WHERE id=5;
UPDATE blog SET gambar_utama='design3/city-skyline.jpg' WHERE id=6;

-- ---- Solutions: per-row illustrations (/solutions page) -----------------
UPDATE solutions_section SET gambar='design3/office-open.jpg'  WHERE id=1; -- Infrastructure
UPDATE solutions_section SET gambar='design3/monitor-dark.jpg' WHERE id=2; -- Cybersecurity
UPDATE solutions_section SET gambar='design3/desk-window.jpg'  WHERE id=3; -- Data
UPDATE solutions_section SET gambar='design3/ai-phone.jpg'     WHERE id=4; -- AI
UPDATE solutions_section SET gambar='design3/imac-design.jpg'  WHERE id=5; -- AI Platform Application
