-- =============================================================================
-- Services — English content sync (content_i18n, lang='en')
-- Source: Konten_Services_Eng.docx (final client content). Makes the EN
-- rendering of /services, /services/infrastructure-services,
-- /services/cybersecurity-services (+ the two package pages) match the doc
-- verbatim. The Indonesian base rows are left untouched; this only sets the
-- EN translation layer that tr_field() reads. Rows targeted by slug/urutan/
-- nama so live auto-increment ids don't matter. Safe to re-run.
-- Run in phpMyAdmin on the live database.
-- =============================================================================

START TRANSACTION;

-- ---------- service_pages (by slug) ----------
-- services
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'headline', 'en', 'One Partner for the Entire Enterprise Technology Lifecycle'
  FROM `service_pages` WHERE slug='services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'body', 'en', 'STT, as an Enterprise Solution Provider, serves as an end-to-end technology partner, not merely an IT solution provider. STT helps organizations from understanding business needs, designing solutions, implementing and integrating technology, and managing operations, to protecting the environment and continuously optimizing performance.'
  FROM `service_pages` WHERE slug='services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'extra1', 'en', 'Not just selling products, but accompanying customers throughout the entire enterprise technology lifecycle.'
  FROM `service_pages` WHERE slug='services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'extra2', 'en', 'Plan → Build → Manage → Protect → Optimize'
  FROM `service_pages` WHERE slug='services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'extra3', 'en', 'One Partner. End-to-End Capability.'
  FROM `service_pages` WHERE slug='services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'cta_label', 'en', 'Consult Your Needs'
  FROM `service_pages` WHERE slug='services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);

-- services/infrastructure-services
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'tagline', 'en', 'Build. Modernize. Manage.'
  FROM `service_pages` WHERE slug='services/infrastructure-services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'body', 'en', 'STT helps organizations understand their existing conditions, design architecture, implement and integrate technology, and keep the environment healthy and optimal through Managed Infrastructure Services.'
  FROM `service_pages` WHERE slug='services/infrastructure-services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'cta_label', 'en', 'View Managed Infrastructure Service Packages'
  FROM `service_pages` WHERE slug='services/infrastructure-services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'data_ai_title', 'en', 'Data & AI in Infrastructure Services'
  FROM `service_pages` WHERE slug='services/infrastructure-services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'data_ai_body', 'en', 'Data Platform & Analytics Infrastructure to support data-intensive workloads and analytics platforms.
AI Infrastructure for compute/GPU, storage, network, and platform readiness for AI workloads.
AI Applications & Industry Use Cases as the business value layer built on top of the infrastructure.'
  FROM `service_pages` WHERE slug='services/infrastructure-services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);

-- services/managed-infrastructure-packages
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'tagline', 'en', 'Infrastructure Support Aligned to Business Criticality Level.'
  FROM `service_pages` WHERE slug='services/managed-infrastructure-packages'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'body', 'en', 'STT provides a choice of Managed Infrastructure Services based on the environment''s criticality level, operational support requirements, maintenance frequency, onsite support, and required coverage.'
  FROM `service_pages` WHERE slug='services/managed-infrastructure-packages'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'cta_label', 'en', 'Discuss Your SLA Needs'
  FROM `service_pages` WHERE slug='services/managed-infrastructure-packages'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);

-- services/cybersecurity-services
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'tagline', 'en', 'Identify Risk. Strengthen Defense. Enhance Resilience.'
  FROM `service_pages` WHERE slug='services/cybersecurity-services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'body', 'en', 'STT helps organizations identify security weaknesses, strengthen governance and compliance, and provide managed cybersecurity capability to address ever-evolving threats.'
  FROM `service_pages` WHERE slug='services/cybersecurity-services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'cta_label', 'en', 'Explore SOC as a Service'
  FROM `service_pages` WHERE slug='services/cybersecurity-services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'data_ai_title', 'en', 'Data & AI in Cybersecurity Services'
  FROM `service_pages` WHERE slug='services/cybersecurity-services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'data_ai_body', 'en', 'Security Analytics & Data Integration to improve visibility and analysis of security events.
AI-enabled Security Use Cases for detection, analytics, and operational use cases tailored to customer needs.
Integration with the Data & AI platform when required as part of the solution architecture.'
  FROM `service_pages` WHERE slug='services/cybersecurity-services'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);

-- services/soc-as-a-service
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'tagline', 'en', 'Monitor. Detect. Investigate. Respond.'
  FROM `service_pages` WHERE slug='services/soc-as-a-service'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'body', 'en', 'STT SOC as a Service helps organizations improve security visibility, accelerate detection and investigation, and enhance readiness to respond to cyber incidents.'
  FROM `service_pages` WHERE slug='services/soc-as-a-service'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'cta_label', 'en', 'Discuss Your SOC Needs'
  FROM `service_pages` WHERE slug='services/soc-as-a-service'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pages', id, 'extra2', 'en', 'Monitor → Detect → Investigate → Respond → Improve'
  FROM `service_pages` WHERE slug='services/soc-as-a-service'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);

-- ---------- service_pillars.deskripsi (landing: 2 pillars + 3 supporting, by urutan) ----------
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pillars', sp.id, 'deskripsi', 'en', 'Building, modernizing, and managing a resilient, scalable enterprise technology foundation that is ready to support critical business workloads.'
  FROM `service_pillars` sp
  WHERE sp.page_id=(SELECT id FROM `service_pages` WHERE slug='services')
    AND sp.urutan=1
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pillars', sp.id, 'deskripsi', 'en', 'Helping organizations identify risks, strengthen security posture, meet compliance requirements, and enhance cyber resilience.'
  FROM `service_pillars` sp
  WHERE sp.page_id=(SELECT id FROM `service_pages` WHERE slug='services')
    AND sp.urutan=2
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pillars', sp.id, 'deskripsi', 'en', 'Foundation for data platforms, analytics workloads, and data integration.'
  FROM `service_pillars` sp
  WHERE sp.page_id=(SELECT id FROM `service_pages` WHERE slug='services')
    AND sp.urutan=3
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pillars', sp.id, 'deskripsi', 'en', 'Compute/GPU, storage, network, and platform readiness for AI workloads.'
  FROM `service_pillars` sp
  WHERE sp.page_id=(SELECT id FROM `service_pages` WHERE slug='services')
    AND sp.urutan=4
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_pillars', sp.id, 'deskripsi', 'en', 'Intelligent applications and industry-specific use cases that support business value.'
  FROM `service_pillars` sp
  WHERE sp.page_id=(SELECT id FROM `service_pages` WHERE slug='services')
    AND sp.urutan=5
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);

-- ---------- service_tiers.deskripsi (package pages, by page slug + nama) ----------
-- services/managed-infrastructure-packages
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_tiers', st.id, 'deskripsi', 'en', 'For environments that require fundamental operational support and scheduled maintenance.'
  FROM `service_tiers` st
  WHERE st.page_id=(SELECT id FROM `service_pages` WHERE slug='services/managed-infrastructure-packages')
    AND st.nama='GOLD'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_tiers', st.id, 'deskripsi', 'en', 'For infrastructure that is more critical and requires more intensive maintenance and support.'
  FROM `service_tiers` st
  WHERE st.page_id=(SELECT id FROM `service_pages` WHERE slug='services/managed-infrastructure-packages')
    AND st.nama='PLATINUM'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_tiers', st.id, 'deskripsi', 'en', 'For mission-critical environments requiring the most comprehensive operational coverage.'
  FROM `service_tiers` st
  WHERE st.page_id=(SELECT id FROM `service_pages` WHERE slug='services/managed-infrastructure-packages')
    AND st.nama='DIAMOND'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);

-- services/soc-as-a-service
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_tiers', st.id, 'deskripsi', 'en', 'For organizations that require fundamental security monitoring and visibility into security events.'
  FROM `service_tiers` st
  WHERE st.page_id=(SELECT id FROM `service_pages` WHERE slug='services/soc-as-a-service')
    AND st.nama='GOLD'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_tiers', st.id, 'deskripsi', 'en', 'For organizations that require 24x7 monitoring, faster response, and co-managed incident handling.'
  FROM `service_tiers` st
  WHERE st.page_id=(SELECT id FROM `service_pages` WHERE slug='services/soc-as-a-service')
    AND st.nama='PLATINUM'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);
INSERT INTO `content_i18n` (`tabel`,`row_id`,`field`,`lang`,`nilai`)
  SELECT 'service_tiers', st.id, 'deskripsi', 'en', 'For mission-critical organizations that require 24x7 security operations and advanced incident readiness.'
  FROM `service_tiers` st
  WHERE st.page_id=(SELECT id FROM `service_pages` WHERE slug='services/soc-as-a-service')
    AND st.nama='DIAMOND'
  ON DUPLICATE KEY UPDATE `nilai`=VALUES(`nilai`);

COMMIT;
