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

-- ---------- Services pages (EN) — page-level copy that is Indonesian by default ----------
-- Deeper items (pillars/tiers/matrix/area) are English technical terms or translated in admin.
DELETE ci FROM content_i18n ci JOIN service_pages sp ON sp.id=ci.row_id
  WHERE ci.tabel='service_pages' AND ci.lang='en';
INSERT INTO content_i18n (tabel,row_id,field,lang,nilai) VALUES
('service_pages',(SELECT id FROM service_pages WHERE slug='services'),'headline','en','One Partner for the Entire Enterprise Technology Lifecycle'),
('service_pages',(SELECT id FROM service_pages WHERE slug='services'),'body','en','As an Enterprise Solution Provider, STT is an end-to-end technology partner — not merely an IT solution vendor. STT supports organizations from understanding business needs, designing solutions, implementing and integrating technology, managing operations, and protecting the environment, to continuous optimization.'),
('service_pages',(SELECT id FROM service_pages WHERE slug='services'),'extra1','en','Not just selling products, but accompanying customers throughout the entire enterprise technology lifecycle.'),
('service_pages',(SELECT id FROM service_pages WHERE slug='services'),'extra2','en','Plan → Build → Manage → Protect → Optimize'),
('service_pages',(SELECT id FROM service_pages WHERE slug='services'),'cta_label','en','Consult Your Needs'),

('service_pages',(SELECT id FROM service_pages WHERE slug='services/infrastructure-services'),'tagline','en','Build. Modernize. Manage.'),
('service_pages',(SELECT id FROM service_pages WHERE slug='services/infrastructure-services'),'body','en','STT helps organizations understand the existing environment, design the architecture, implement and integrate technology, and keep the environment healthy and optimal through Managed Infrastructure Services.'),
('service_pages',(SELECT id FROM service_pages WHERE slug='services/infrastructure-services'),'data_ai_body','en','Data Platform & Analytics Infrastructure to support data-intensive workloads and analytics platforms.\nAI Infrastructure for compute/GPU, storage, network, and platform readiness for AI workloads.\nAI Applications & Industry Use Cases as the business-value layer on top of the infrastructure built.'),

('service_pages',(SELECT id FROM service_pages WHERE slug='services/managed-infrastructure-packages'),'tagline','en','Infrastructure Support Matched to Business Criticality.'),
('service_pages',(SELECT id FROM service_pages WHERE slug='services/managed-infrastructure-packages'),'body','en','STT offers Managed Infrastructure Services options based on environment criticality, operational support needs, maintenance frequency, onsite support, and required coverage.'),
('service_pages',(SELECT id FROM service_pages WHERE slug='services/managed-infrastructure-packages'),'cta_label','en','Discuss Your SLA Needs'),

('service_pages',(SELECT id FROM service_pages WHERE slug='services/cybersecurity-services'),'tagline','en','Identify Risk. Strengthen Defense. Increase Resilience.'),
('service_pages',(SELECT id FROM service_pages WHERE slug='services/cybersecurity-services'),'body','en','STT helps organizations identify security weaknesses, strengthen governance and compliance, and provides managed cybersecurity capability to face continuously evolving threats.'),
('service_pages',(SELECT id FROM service_pages WHERE slug='services/cybersecurity-services'),'data_ai_body','en','Security Analytics & Data Integration to improve visibility and analysis of security events.\nAI-enabled Security Use Cases for detection, analytics, and operational use cases per customer needs.\nIntegration with the Data & AI platform when required as part of the solution architecture.'),

('service_pages',(SELECT id FROM service_pages WHERE slug='services/soc-as-a-service'),'tagline','en','Monitor. Detect. Investigate. Respond.'),
('service_pages',(SELECT id FROM service_pages WHERE slug='services/soc-as-a-service'),'body','en','STT SOC as a Service helps organizations improve security visibility, accelerate detection and investigation, and increase readiness to respond to cyber incidents.'),
('service_pages',(SELECT id FROM service_pages WHERE slug='services/soc-as-a-service'),'cta_label','en','Discuss Your SOC Needs');
