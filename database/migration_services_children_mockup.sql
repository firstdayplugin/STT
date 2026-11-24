-- ============================================================================
-- Services CHILD pages (/services/<slug>) — selaraskan copy & matrix ke mockup
-- klien (EN). Idempotent: hanya menyentuh 4 baris service_pages + areanya +
-- matrix 2 halaman package. Struktur tabel tidak diubah.
--   Hero eyebrow = service_pages.tagline, H1 = headline, lead = body.
--   Tombol/AT A GLANCE/steps/cross-sell/plan-teaser didefinisikan di template
--   (default mockup) & tetap bisa dioverride via content_blocks(slug,key).
-- ============================================================================
SET NAMES utf8mb4;

-- ---------- Hero copy (tagline=eyebrow, headline=H1, body=lead) ----------
UPDATE `service_pages` SET
  `tagline`  = 'Build. Run. Manage.',
  `headline` = 'End-to-End Infrastructure Services for Business Continuity.',
  `body`     = 'We assess your environment, design the architecture, deploy and integrate the technology, and keep it healthy and optimal through Managed Infrastructure Services to support your business at every stage.',
  `cta_label`= 'Discuss Your SLA Needs', `cta_target`='contact-us'
WHERE `slug`='services/infrastructure-services';

UPDATE `service_pages` SET
  `tagline`  = 'Identify Risk. Strengthen Defense. Enhance Resilience.',
  `headline` = 'Secure Your Business. Strengthen Your Resilience.',
  `body`     = 'We identify security risks, strengthen governance and compliance, and protect your business against evolving cyber threats through expert-led cybersecurity and managed security services.',
  `cta_label`= 'Discuss Your SOC Needs', `cta_target`='contact-us'
WHERE `slug`='services/cybersecurity-services';

UPDATE `service_pages` SET
  `tagline`  = 'Infrastructure Support Aligned to Business Criticality Level.',
  `headline` = 'Managed Infrastructure Service Packages',
  `body`     = 'STT provides a choice of Managed Infrastructure Services based on the environment''s criticality level, operational support requirements, maintenance frequency, onsite support, and required coverage.',
  `cta_label`= 'Discuss Your SLA Needs', `cta_target`='contact-us'
WHERE `slug`='services/managed-infrastructure-packages';

UPDATE `service_pages` SET
  `tagline`  = 'Monitor. Detect. Investigate. Respond.',
  `headline` = 'SOC as a Service',
  `body`     = 'STT SOC as a Service helps organizations improve security visibility, accelerate detection and investigation, and enhance readiness to respond to cyber incidents.',
  `cta_label`= 'Discuss Your SOC Needs', `cta_target`='contact-us'
WHERE `slug`='services/soc-as-a-service';

-- ---------- Service area intros (service_areas.deskripsi) ----------
SET @infra := (SELECT id FROM service_pages WHERE slug='services/infrastructure-services');
UPDATE `service_areas` SET `deskripsi`='Know where you stand and where to go next.'        WHERE page_id=@infra AND kode='01';
UPDATE `service_areas` SET `deskripsi`='Implement and integrate without disrupting operations.' WHERE page_id=@infra AND kode='02';
UPDATE `service_areas` SET `deskripsi`='Keep the environment healthy and optimal.'          WHERE page_id=@infra AND kode='03';

SET @cyber := (SELECT id FROM service_pages WHERE slug='services/cybersecurity-services');
UPDATE `service_areas` SET `deskripsi`='Find weaknesses before attackers do.'               WHERE page_id=@cyber AND kode='01';
UPDATE `service_areas` SET `deskripsi`='Meet regulatory and governance requirements.'       WHERE page_id=@cyber AND kode='02';
UPDATE `service_areas` SET `deskripsi`='Detect and respond to threats as they happen.'      WHERE page_id=@cyber AND kode='03';

-- ---------- Managed Infrastructure Packages — matrix (regroup + mockup values) ----------
SET @mg := (SELECT id FROM service_pages WHERE slug='services/managed-infrastructure-packages');
DELETE FROM `service_matrix` WHERE page_id=@mg;
INSERT INTO `service_matrix` (page_id,grup,baris,v_gold,v_platinum,v_diamond,urutan) VALUES
(@mg,'Service Coverage','Helpdesk Service SLA','24 × 7','24 × 7','24 × 7',1),
(@mg,'Service Coverage','Response Time','Max. 1 hour','Max. 1 hour','Max. 1 hour',2),
(@mg,'Preventive Maintenance','Preventive Maintenance','2× / year','4× / year','Monthly',3),
(@mg,'Preventive Maintenance','Hardware Firmware Upgrade & Software Patching','Yes','Yes','Yes',4),
(@mg,'Preventive Maintenance','Preventive Maintenance Report','2× / year','4× / year','Monthly',5),
(@mg,'Corrective Maintenance & Changes','Corrective Maintenance','6 tickets','12 tickets','Unlimited',6),
(@mg,'Corrective Maintenance & Changes','Onsite Part Replacement (with Principal Engineer)','Yes','Yes','Yes',7),
(@mg,'Corrective Maintenance & Changes','Move, Add & Change Management','2 tickets','6 tickets','Unlimited',8),
(@mg,'Corrective Maintenance & Changes','On-call Support Engineer','Yes','Yes','Yes',9),
(@mg,'Onsite Support, Review & Contract','Onsite Standby Engineer (Office Hour)','No','No','1 engineer',10),
(@mg,'Onsite Support, Review & Contract','Maintenance Review','No','Yearly','2× / year',11),
(@mg,'Onsite Support, Review & Contract','Minimum Period','1 year','1 year','1 year',12);

-- ---------- SOC as a Service — matrix (mockup values; [confirm] = Yes) ----------
SET @soc := (SELECT id FROM service_pages WHERE slug='services/soc-as-a-service');
DELETE FROM `service_matrix` WHERE page_id=@soc;
INSERT INTO `service_matrix` (page_id,grup,baris,v_gold,v_platinum,v_diamond,urutan) VALUES
(@soc,'Security Monitoring & Incident Response','Security Monitoring SLA','8 × 5','24 × 7','24 × 7',1),
(@soc,'Security Monitoring & Incident Response','Log Monitoring Coverage','Up to 25 assets','Up to 50 assets','Up to 100 assets',2),
(@soc,'Security Monitoring & Incident Response','Response Time (MTTA)','15 min','15 min','15 min',3),
(@soc,'Security Monitoring & Incident Response','Incident Handling','No','Co-managed','Fully managed',4),
(@soc,'Security Monitoring & Incident Response','Retention Logs','30 days','30 days','30 days',5),
(@soc,'Security Monitoring & Incident Response','Security Reporting','Monthly','Monthly + Annual','Monthly, Half, & Annual',6),
(@soc,'Advanced Services','Threat Hunting','No','No','2 Credits / Year',7),
(@soc,'Advanced Services','Infosec Advisory','No','Checklist','Checklist',8),
(@soc,'Advanced Services','Vulnerability Assessment','No','4 Credits / Year','4 Credits / Year',9),
(@soc,'Advanced Services','Digital Forensic Capabilities','No','2 Credits / Year','4 Credits / Year',10),
(@soc,'Advanced Services','Penetration Testing','No','No','2 Credits / Year',11),
(@soc,'Advanced Services','Cyber Drill / Tabletop Exercise','No','No','Yes',12);
