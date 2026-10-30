-- ============================================================
-- Hero v2 — plain (text-free) client images + CMS text overlay.
--   The client now provides background images WITHOUT baked text, so the
--   headline/eyebrow/description live in the DB and render as responsive
--   HTML (never cropped, never overlapping the button, mobile-safe).
--   Adds eyebrow + deskripsi columns; subtitle stays empty (the old
--   below-hero blue heading collapses). Supersedes the hero part of
--   migration_home_client_images.sql. Idempotent.
-- ============================================================
SET NAMES utf8mb4;

ALTER TABLE hero_slides ADD COLUMN IF NOT EXISTS eyebrow   VARCHAR(180) DEFAULT NULL AFTER judul;
ALTER TABLE hero_slides ADD COLUMN IF NOT EXISTS deskripsi TEXT         DEFAULT NULL AFTER subtitle;

DELETE FROM hero_slides;
INSERT INTO hero_slides (id, judul, eyebrow, subtitle, deskripsi, gambar, video_url, cta_text, cta_url, urutan, is_active) VALUES
(1,'End-to-End Transformation','One Technology Partner','','From modern infrastructure to enterprise AI applications, we help organizations connect technology to create real impact.','slides/hero-1.png',NULL,NULL,NULL,1,1),
(2,'Building the Future of AI-Powered Data Resilience','STT x MeshDefend','','Enterprise-grade protection that keeps your data resilient, secure, and always available.','slides/hero-2.png',NULL,NULL,NULL,2,1),
(3,'Predictive Intelligence','ST Analytics','','Transform fragmented data into actionable foresight with ST Analytics AI-powered predictive intelligence.','slides/hero-3.png',NULL,NULL,NULL,3,1),
(4,'Smart Surveillance','ST Vision','','ST Vision transforms real-time visual data into actionable intelligence to automate operations, elevate safety, and enhance security.','slides/hero-4.png',NULL,NULL,NULL,4,1),
(5,'Conversational AI','ST Language','','ST Language turns complex enterprise knowledge into seamless, AI-powered conversational workflows.','slides/hero-5.png',NULL,NULL,NULL,5,1),
(6,'Interactive AI','Digital Avatar','','Creates hyper-realistic AI digital humans to elevate interactive engagement and automated content creation.','slides/hero-6.png',NULL,NULL,NULL,6,1),
(7,'Next-Gen Medical AI','Healthcare','','Revolutionize healthcare delivery with AI solutions that elevate diagnostic accuracy and patient outcomes.','slides/hero-7.png',NULL,NULL,NULL,7,1),
(8,'Urban Infrastructure AI','Smart City','','Transform urban governance from reactive to predictive with unified, AI-driven city intelligence.','slides/hero-8.png',NULL,NULL,NULL,8,1),
(9,'Satellite Data Analytics','Geospatial Intelligence','','Unlock large-scale satellite insights with AI-powered geospatial intelligence to monitor environmental and infrastructure changes.','slides/hero-9.png',NULL,NULL,NULL,9,1);
