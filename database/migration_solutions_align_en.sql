-- =============================================================================
-- Solutions page — align BASE content with the client's final copywriting
-- Source: Our_Solutions_Eng.docx. Site default language = English, so the base
-- values render. Updates titles, deck descriptions, the See More popup detail,
-- the partner logos, and the intro + closing CTA. Matched by urutan / nama so
-- live auto-increment ids do not matter. Safe to re-run. Run in phpMyAdmin.
-- NOTE: the doc lists F5 as a Cybersecurity partner, but no F5 logo image exists
--       yet — add uploads/solutions/logos/f5-v4.png and a row to enable it.
-- =============================================================================
SET NAMES utf8mb4;
START TRANSACTION;

-- ---------- 1. Modernize <b>Infrastructure</b> ----------
SET @s1 := (SELECT id FROM solutions_section WHERE urutan=1 LIMIT 1);
UPDATE `solutions_section` SET `judul`='Modernize <b>Infrastructure</b>', `solusi`='Build a Resilient Foundation for the Digital Enterprise', `detail`='<p class="sm-tag">Build a Resilient Foundation for the Digital Enterprise</p><p>Modern businesses require infrastructure that can continuously adapt to changing workloads, increasing data volumes, evolving applications, and growing security requirements.</p><p>Sapta Tunas Teknologi helps organizations modernize their technology infrastructure by designing and implementing high availability, high-performance, scalable, and resilient environments across data centers, private cloud, hybrid cloud, networking, compute, storage, and backup systems.</p><p>Our approach goes beyond technology refresh. We help organizations simplify infrastructure operations, improve utilization, reduce operational complexity, and establish a stronger foundation for digital transformation.</p><h4>Our Capabilities</h4><ul><li>Data Center Modernization</li><li>Compute &amp; Virtualization Infrastructure</li><li>Enterprise Storage &amp; Software-Defined Storage</li><li>Private &amp; Hybrid Cloud Infrastructure</li><li>Enterprise Networking &amp; Software-Defined Networking</li><li>Hyperconverged Infrastructure</li><li>Backup, Disaster Recovery &amp; Business Continuity</li><li>High Availability &amp; Multi-Site Architecture</li><li>Infrastructure Automation &amp; Orchestration</li><li>Infrastructure Monitoring &amp; Performance Optimization</li></ul>' WHERE `urutan`=1;
DELETE FROM `solution_logos` WHERE `solution_id`=@s1;
INSERT INTO `solution_logos` (`solution_id`,`gambar`,`nama`,`urutan`,`is_active`) VALUES
  (@s1,'solutions/logos/dell-v4.png','Dell Technologies',1,1),
  (@s1,'solutions/logos/vmware-v4.png','VMware by Broadcom',2,1),
  (@s1,'solutions/logos/nutanix-v4.png','Nutanix',3,1),
  (@s1,'solutions/logos/sangfor-v4.png','Sangfor',4,1),
  (@s1,'solutions/logos/redhat-v4.png','RedHat',5,1),
  (@s1,'solutions/logos/microsoft-v4.png','Microsoft',6,1),
  (@s1,'solutions/logos/commvault-v4.png','CommVault',7,1),
  (@s1,'solutions/logos/hycu-v4.png','Hycu',8,1),
  (@s1,'solutions/logos/veeam-v4.png','Veeam',9,1),
  (@s1,'solutions/logos/infraon-v4.png','Infraon',10,1);

-- ---------- 2. <b>Cybersecurity</b> ----------
SET @s2 := (SELECT id FROM solutions_section WHERE urutan=2 LIMIT 1);
UPDATE `solutions_section` SET `judul`='<b>Cybersecurity</b>', `solusi`='Protect Your Business in an Increasingly Complex Digital Environment', `detail`='<p class="sm-tag">Protect Your Business in an Increasingly Complex Digital Environment</p><p>Cyber threats are no longer only an IT concern. They represent direct risks to business continuity, customer trust, regulatory compliance, operational stability, and corporate reputation.</p><p>Sapta Tunas Teknologi delivers a comprehensive cybersecurity approach designed to protect organizations across users, devices, applications, networks, cloud environments, workloads, and data.</p><p>Our security architecture combines preventive, detective, and responsive capabilities while applying modern principles such as Zero Trust, identity-centric security, segmentation, continuous monitoring, and threat intelligence.</p><h4>Our Capabilities</h4><ul><li>Next-Generation Firewall &amp; Network Security</li><li>Zero Trust Security Architecture</li><li>Secure Access Service Edge – SASE</li><li>Identity &amp; Access Management</li><li>Endpoint Detection &amp; Response – EDR/XDR</li><li>Network Detection &amp; Response – NDR</li><li>Security Information &amp; Event Management – SIEM</li><li>Security Operations Center – SOC</li><li>Email, Web &amp; Application Security</li><li>Data Protection &amp; Data Loss Prevention</li><li>Vulnerability Assessment &amp; Penetration Testing</li><li>Digital Forensics &amp; Incident Response</li><li>Cybersecurity Assessment &amp; Hardening</li><li>Cyber Drill &amp; Tabletop Exercise</li></ul>' WHERE `urutan`=2;
DELETE FROM `solution_logos` WHERE `solution_id`=@s2;
INSERT INTO `solution_logos` (`solution_id`,`gambar`,`nama`,`urutan`,`is_active`) VALUES
  (@s2,'solutions/logos/cyble-v4.png','Cyble',1,1),
  (@s2,'solutions/logos/cisco-v4.png','Cisco',2,1),
  (@s2,'solutions/logos/fortinet-v4.png','Fortinet',3,1),
  (@s2,'solutions/logos/elastic-v4.png','Elastic',4,1);

-- ---------- 3. <b>Data</b> Management ----------
SET @s3 := (SELECT id FROM solutions_section WHERE urutan=3 LIMIT 1);
UPDATE `solutions_section` SET `judul`='<b>Data</b> Management', `solusi`='Turn Enterprise Data into a Trusted Business Asset', `detail`='<p class="sm-tag">Turn Enterprise Data into a Trusted Business Asset</p><p>Organizations generate massive volumes of data across applications, infrastructure, users, machines, and digital services. Without the right strategy and architecture, that data can quickly become fragmented, difficult to manage, expensive to store, and vulnerable to loss.</p><p>Sapta Tunas Teknologi helps organizations manage the entire data lifecycle—from creation and storage to protection, governance, cleansing, integration, analytics, and long-term retention.</p><p>We design data environments that enable organizations to maintain availability, integrity, security, scalability, and accessibility while preparing enterprise data for analytics and AI-driven use cases.</p><h4>Our Capabilities</h4><ul><li>Data Lifecycle Management</li><li>Data Integration &amp; Data Pipeline</li><li>Data Lakehouse &amp; Data Platform</li><li>Analytics Infrastructure</li><li>AI-Ready Data Architecture</li></ul>' WHERE `urutan`=3;
DELETE FROM `solution_logos` WHERE `solution_id`=@s3;
INSERT INTO `solution_logos` (`solution_id`,`gambar`,`nama`,`urutan`,`is_active`) VALUES
  (@s3,'solutions/logos/xeratic-v4.png','Xeratic',1,1),
  (@s3,'solutions/logos/soca-v4.png','Soca',2,1),
  (@s3,'solutions/logos/weka-v4.png','Weka',3,1);

-- ---------- 4. Artificial <b>Intelligence</b> (AI) ----------
SET @s4 := (SELECT id FROM solutions_section WHERE urutan=4 LIMIT 1);
UPDATE `solutions_section` SET `judul`='Artificial <b>Intelligence</b> (AI)', `solusi`='Transform Data into Intelligence and Business Impact', `detail`='<p class="sm-tag">Transform Data into Intelligence and Business Impact</p><p>Artificial Intelligence is transforming how organizations operate, make decisions, engage customers, manage risk, and create new business opportunities.</p><p>Sapta Tunas Teknologi helps enterprises move beyond AI experimentation toward practical, scalable, and business-oriented AI adoption.</p><p>We combine enterprise infrastructure, accelerated computing, data platforms, AI models, and industry-specific expertise to develop AI solutions that address real operational and business challenges.</p><p>From computer vision and generative AI, we help organizations identify where AI can create measurable value.</p><h4>Our Capabilities</h4><ul><li>Enterprise AI Infrastructure</li><li>Generative AI</li><li>Large Language Models – LLM</li><li>Enterprise Knowledge AI &amp; Retrieval-Augmented Generation</li><li>Computer Vision &amp; Video Analytics</li><li>AI Model Development &amp; Integration</li><li>Industry AI Use Cases</li></ul><h4>Industry AI Use Cases</h4><p>Our AI solutions can support multiple industries and business functions, including:</p><ul><li>Smart City &amp; Public Safety</li><li>Healthcare &amp; Medical Imaging</li><li>Manufacturing &amp; Industrial Operations</li><li>Agriculture</li><li>Media &amp; Broadcasting (AI Avatar &amp; Video Generation)</li></ul>' WHERE `urutan`=4;
DELETE FROM `solution_logos` WHERE `solution_id`=@s4;
INSERT INTO `solution_logos` (`solution_id`,`gambar`,`nama`,`urutan`,`is_active`) VALUES
  (@s4,'solutions/logos/sensetime-v4.png','SenseTime',1,1),
  (@s4,'solutions/logos/soca-v4.png','Soca',2,1),
  (@s4,'solutions/logos/xeratic-v4.png','Xeratic',3,1),
  (@s4,'solutions/logos/rafay-v4.png','Rafay',4,1),
  (@s4,'solutions/logos/nvidia-v4.png','NVidia',5,1);

-- ---------- 5. AI Platform &amp; <b>Applications</b> ----------
SET @s5 := (SELECT id FROM solutions_section WHERE urutan=5 LIMIT 1);
UPDATE `solutions_section` SET `judul`='AI Platform &amp; <b>Applications</b>', `solusi`='Turn AI Capabilities into Real Business Applications', `detail`='<p class="sm-tag">Turn AI Capabilities into Real Business Applications</p><p>AI delivers real value when it becomes part of everyday business operations.</p><p>Sapta Tunas Teknologi develops AI platforms and intelligent applications that embed artificial intelligence directly into enterprise workflows, customer experiences, operational processes, and decision-making systems.</p><p>Our approach bridges the gap between AI technology and business execution. We integrate enterprise data, AI models, APIs, business applications, and user experiences into scalable platforms that can be deployed across private infrastructure or hybrid environments.</p><p>Rather than implementing isolated AI tools, we help organizations build an AI application ecosystem that can continuously evolve as business requirements change.</p><h4>Our Capabilities</h4><ul><li>Enterprise AI Platform</li><li>AI Application Development</li><li>Enterprise AI Assistant &amp; Copilot</li><li>AI Agent &amp; Agentic Workflow</li><li>AI Digital Human &amp; Virtual Assistant</li><li>AI Content Generation Platform</li><li>AI Integration with Enterprise Applications</li></ul>' WHERE `urutan`=5;
DELETE FROM `solution_logos` WHERE `solution_id`=@s5;
INSERT INTO `solution_logos` (`solution_id`,`gambar`,`nama`,`urutan`,`is_active`) VALUES
  (@s5,'solutions/logos/satu-ai-v4.png','SATU AI',1,1),
  (@s5,'solutions/logos/sensetime-v4.png','SenseTime',2,1),
  (@s5,'solutions/logos/soca-v4.png','Soca',3,1),
  (@s5,'solutions/logos/xeratic-v4.png','Xeratic',4,1);

-- ---------- intro + closing CTA (content_blocks, lang='en') ----------
INSERT INTO `content_blocks` (`page_key`,`block_key`,`lang`,`block_label`,`block_type`,`konten`,`is_active`)
  VALUES ('solutions','lead','en','Intro','html','<p>Sapta Tunas Teknologi helps organizations modernize, secure, manage, and transform their technology environments to support long-term business growth.</p><p>Our solutions combine enterprise infrastructure, cybersecurity, data management, artificial intelligence, and intelligent applications into an integrated technology ecosystem designed to improve operational efficiency, strengthen business resilience, and enable continuous innovation.</p><p>From foundational infrastructure to advanced AI adoption, we help enterprises build technology environments that are secure, scalable, resilient, and future-ready.</p>',1)
  ON DUPLICATE KEY UPDATE `konten`=VALUES(`konten`), `is_active`=1;
INSERT INTO `content_blocks` (`page_key`,`block_key`,`lang`,`block_label`,`block_type`,`konten`,`is_active`)
  VALUES ('solutions','cta_eyebrow','en','CTA — eyebrow','text','One Partner. End-to-End Transformation.',1)
  ON DUPLICATE KEY UPDATE `konten`=VALUES(`konten`), `is_active`=1;
INSERT INTO `content_blocks` (`page_key`,`block_key`,`lang`,`block_label`,`block_type`,`konten`,`is_active`)
  VALUES ('solutions','cta_title','en','CTA — judul','text','Build Your [Future-Ready] Enterprise',1)
  ON DUPLICATE KEY UPDATE `konten`=VALUES(`konten`), `is_active`=1;
INSERT INTO `content_blocks` (`page_key`,`block_key`,`lang`,`block_label`,`block_type`,`konten`,`is_active`)
  VALUES ('solutions','cta_text','en','CTA — teks','text','From modernizing your technology foundation to deploying enterprise AI applications, our experts help you design and implement solutions aligned with your business priorities. Let''s transform technology into business advantage.',1)
  ON DUPLICATE KEY UPDATE `konten`=VALUES(`konten`), `is_active`=1;

COMMIT;
