-- =============================================================================
-- Services — convert BASE content to English (site default language = English)
-- Source: Konten_Services_Eng.docx (final client content). Scope: Services only.
-- The site default is English, and for the default language the engine shows the
-- BASE row values -- so the base must be English (the content_i18n 'en' layer is
-- NOT read for the default language). This updates the Indonesian base fields to
-- English; fields already English (service areas, items, tier names/titles,
-- matrix values) are left as-is. Rows targeted by slug/urutan/nama so live ids
-- don't matter. Safe to re-run.
-- Run in phpMyAdmin on the live database.
-- =============================================================================

START TRANSACTION;

-- ---------- service_pages (by slug) ----------
UPDATE `service_pages` SET `headline`='One Partner for the Entire Enterprise Technology Lifecycle', `body`='STT, as an Enterprise Solution Provider, serves as an end-to-end technology partner, not merely an IT solution provider. STT helps organizations from understanding business needs, designing solutions, implementing and integrating technology, and managing operations, to protecting the environment and continuously optimizing performance.', `extra1`='Not just selling products, but accompanying customers throughout the entire enterprise technology lifecycle.', `extra2`='Plan → Build → Manage → Protect → Optimize', `cta_label`='Consult Your Needs' WHERE `slug`='services';
UPDATE `service_pages` SET `tagline`='Build. Modernize. Manage.', `body`='STT helps organizations understand their existing conditions, design architecture, implement and integrate technology, and keep the environment healthy and optimal through Managed Infrastructure Services.', `cta_label`='View Managed Infrastructure Service Packages', `data_ai_title`='Data & AI in Infrastructure Services', `data_ai_body`='Data Platform & Analytics Infrastructure to support data-intensive workloads and analytics platforms.\nAI Infrastructure for compute/GPU, storage, network, and platform readiness for AI workloads.\nAI Applications & Industry Use Cases as the business value layer built on top of the infrastructure.', `visual_note`='Use photos/visuals that directly show service activities - technical workshops, server deployment/racking, and NOC managed operations.' WHERE `slug`='services/infrastructure-services';
UPDATE `service_pages` SET `tagline`='Infrastructure Support Aligned to Business Criticality Level.', `body`='STT provides a choice of Managed Infrastructure Services based on the environment''s criticality level, operational support requirements, maintenance frequency, onsite support, and required coverage.', `cta_label`='Discuss Your SLA Needs' WHERE `slug`='services/managed-infrastructure-packages';
UPDATE `service_pages` SET `tagline`='Identify Risk. Strengthen Defense. Enhance Resilience.', `body`='STT helps organizations identify security weaknesses, strengthen governance and compliance, and provide managed cybersecurity capability to address ever-evolving threats.', `data_ai_title`='Data & AI in Cybersecurity Services', `data_ai_body`='Security Analytics & Data Integration to improve visibility and analysis of security events.\nAI-enabled Security Use Cases for detection, analytics, and operational use cases tailored to customer needs.\nIntegration with the Data & AI platform when required as part of the solution architecture.', `visual_note`='Use photos of STT''s own SOC as the primary visual so the page feels more authentic and demonstrates STT''s internal capability.' WHERE `slug`='services/cybersecurity-services';
UPDATE `service_pages` SET `tagline`='Monitor. Detect. Investigate. Respond.', `body`='STT SOC as a Service helps organizations improve security visibility, accelerate detection and investigation, and enhance readiness to respond to cyber incidents.', `cta_label`='Discuss Your SOC Needs' WHERE `slug`='services/soc-as-a-service';

-- ---------- service_pillars.deskripsi (landing: 2 pillars + 3 supporting, by urutan) ----------
UPDATE `service_pillars` SET `deskripsi`='Building, modernizing, and managing a resilient, scalable enterprise technology foundation that is ready to support critical business workloads.' WHERE `page_id`=(SELECT id FROM `service_pages` WHERE slug='services') AND `urutan`=1;
UPDATE `service_pillars` SET `deskripsi`='Helping organizations identify risks, strengthen security posture, meet compliance requirements, and enhance cyber resilience.' WHERE `page_id`=(SELECT id FROM `service_pages` WHERE slug='services') AND `urutan`=2;
UPDATE `service_pillars` SET `deskripsi`='Foundation for data platforms, analytics workloads, and data integration.' WHERE `page_id`=(SELECT id FROM `service_pages` WHERE slug='services') AND `urutan`=3;
UPDATE `service_pillars` SET `deskripsi`='Compute/GPU, storage, network, and platform readiness for AI workloads.' WHERE `page_id`=(SELECT id FROM `service_pages` WHERE slug='services') AND `urutan`=4;
UPDATE `service_pillars` SET `deskripsi`='Intelligent applications and industry-specific use cases that support business value.' WHERE `page_id`=(SELECT id FROM `service_pages` WHERE slug='services') AND `urutan`=5;

-- ---------- service_tiers.deskripsi (package pages, by page slug + nama) ----------
UPDATE `service_tiers` SET `deskripsi`='For environments that require fundamental operational support and scheduled maintenance.' WHERE `page_id`=(SELECT id FROM `service_pages` WHERE slug='services/managed-infrastructure-packages') AND `nama`='GOLD';
UPDATE `service_tiers` SET `deskripsi`='For infrastructure that is more critical and requires more intensive maintenance and support.' WHERE `page_id`=(SELECT id FROM `service_pages` WHERE slug='services/managed-infrastructure-packages') AND `nama`='PLATINUM';
UPDATE `service_tiers` SET `deskripsi`='For mission-critical environments requiring the most comprehensive operational coverage.' WHERE `page_id`=(SELECT id FROM `service_pages` WHERE slug='services/managed-infrastructure-packages') AND `nama`='DIAMOND';
UPDATE `service_tiers` SET `deskripsi`='For organizations that require fundamental security monitoring and visibility into security events.' WHERE `page_id`=(SELECT id FROM `service_pages` WHERE slug='services/soc-as-a-service') AND `nama`='GOLD';
UPDATE `service_tiers` SET `deskripsi`='For organizations that require 24x7 monitoring, faster response, and co-managed incident handling.' WHERE `page_id`=(SELECT id FROM `service_pages` WHERE slug='services/soc-as-a-service') AND `nama`='PLATINUM';
UPDATE `service_tiers` SET `deskripsi`='For mission-critical organizations that require 24x7 security operations and advanced incident readiness.' WHERE `page_id`=(SELECT id FROM `service_pages` WHERE slug='services/soc-as-a-service') AND `nama`='DIAMOND';

COMMIT;
