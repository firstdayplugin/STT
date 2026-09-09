-- ============================================================
-- English (EN) translation seed for the Anima theme.
-- Import AFTER reklamepedia.sql (select the same database first).
-- Idempotent: it clears the rows it manages, then re-inserts them, so it is
-- safe to run again after edits. Strings whose default copy is already English
-- are intentionally omitted — the frontend falls back to the default for those.
-- ============================================================
SET NAMES utf8mb4;

-- ---------- UI strings (t()) ----------
DELETE FROM `translations` WHERE `lang`='en' AND `grp`='ui';
INSERT INTO `translations` (`lang`,`grp`,`k`,`v`) VALUES
('en','ui','all','All'),
('en','ui','search','Search'),
('en','ui','view_detail','View detail'),
('en','ui','read_more','Read more'),
('en','ui','learn_more','Learn more'),
('en','ui','contact_us','Contact Us'),
('en','ui','explore','Explore'),
('en','ui','consult_with_us','Consult With Us'),
('en','ui','newsletter_email_ph','Enter your email here'),
('en','ui','footer_col_solutions','Solutions'),
('en','ui','footer_col_company','Company'),
('en','ui','footer_col_help','Help & Support'),
('en','ui','all_rights_reserved','All Rights Reserved'),
('en','ui','privacy_policy','Privacy Policy'),
('en','ui','compliance_policy','Compliance Policy'),
('en','ui','search_jobs','Search positions...'),
('en','ui','job_role','Job Role'),
('en','ui','location','Location'),
('en','ui','back_to_jobs','All Jobs'),
('en','ui','apply_here','Apply for This Position'),
('en','ui','submit_application','Submit Application'),
('en','ui','responsibilities','Responsibilities'),
('en','ui','requirements','Requirements'),
('en','ui','application_sent','Thank you! We have received your application. Our team will reach out if there is a match.');

-- ---------- Page copy that is Indonesian by default -> English ----------
-- Home
DELETE FROM `content_blocks` WHERE `lang`='en' AND `page_key`='home' AND `block_key` IN
  ('industries_sub','news_empty','why_intro','why1_sub','why2_sub','why3_sub','why4_sub','testi_intro','testi_empty');
INSERT INTO `content_blocks` (`page_key`,`block_key`,`lang`,`block_label`,`block_type`,`konten`,`is_active`) VALUES
('home','industries_sub','en','Industries — sub','text','Technology solutions for a wide range of industries.',1),
('home','news_empty','en','News — empty text','text','No articles yet. Add them from the Blog / Articles menu.',1),
('home','why_intro','en','Why — intro','text','Why enterprises trust STT with their technology transformation.',1),
('home','why1_sub','en','Why 1 — sub','text','10+ years of experience — solutions tailored specifically for you.',1),
('home','why2_sub','en','Why 2 — sub','text','From infrastructure and cloud to AI, all under one roof.',1),
('home','why3_sub','en','Why 3 — sub','text','SatuAI is ready to help whenever you need it.',1),
('home','why4_sub','en','Why 4 — sub','text','Success-first, fully committed on every project.',1),
('home','testi_intro','en','Testimonials — intro','text','Real stories from clients across industries. Click to see the full testimonial — video or written.',1),
('home','testi_empty','en','Testimonials — empty text','text','Testimonials will appear here. Add them from the Testimonials menu.',1);

-- Blog
DELETE FROM `content_blocks` WHERE `lang`='en' AND `page_key`='blog' AND `block_key` IN ('empty_text');
INSERT INTO `content_blocks` (`page_key`,`block_key`,`lang`,`block_label`,`block_type`,`konten`,`is_active`) VALUES
('blog','empty_text','en','Empty text','text','No articles yet.',1);

-- Contact
DELETE FROM `content_blocks` WHERE `lang`='en' AND `page_key`='contact' AND `block_key` IN ('hero_sub','map_placeholder');
INSERT INTO `content_blocks` (`page_key`,`block_key`,`lang`,`block_label`,`block_type`,`konten`,`is_active`) VALUES
('contact','hero_sub','en','Sub judul','text','Have a question or an IT solution need? Our team is ready to help.',1),
('contact','map_placeholder','en','Teks placeholder peta','text','The location map will appear here (set the Google Maps embed in Settings).',1);
