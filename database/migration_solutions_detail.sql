-- ============================================================
-- Solutions "See More" popup content (from Our_Solutions.pdf).
-- Adds a rich, CMS-editable detail body per solution section.
-- Idempotent + non-destructive (only fills empty detail).
-- ============================================================
SET NAMES utf8mb4;

ALTER TABLE solutions_section ADD COLUMN IF NOT EXISTS detail MEDIUMTEXT NULL AFTER solusi;

UPDATE solutions_section SET detail=
'<p class="sm-tag">Build a Resilient Foundation for the Digital Enterprise</p>
<p>Modern businesses require infrastructure that can continuously adapt to changing workloads, increasing data volumes, evolving applications, and growing security requirements.</p>
<p>Sapta Tunas Teknologi helps organizations modernize their technology infrastructure by designing and implementing high-availability, high-performance, scalable, and resilient environments across data centers, private cloud, hybrid cloud, networking, compute, storage, and backup systems. Our approach goes beyond technology refresh: we simplify operations, improve utilization, reduce complexity, and establish a stronger foundation for digital transformation.</p>
<h4>Our Capabilities</h4>
<ul><li>Data Center Modernization</li><li>Compute &amp; Virtualization Infrastructure</li><li>Enterprise Storage &amp; Software-Defined Storage</li><li>Private &amp; Hybrid Cloud Infrastructure</li><li>Enterprise Networking &amp; Software-Defined Networking</li><li>Hyperconverged Infrastructure</li><li>Backup, Disaster Recovery &amp; Business Continuity</li><li>High Availability &amp; Multi-Site Architecture</li><li>Infrastructure Automation &amp; Orchestration</li><li>Infrastructure Monitoring &amp; Performance Optimization</li></ul>'
WHERE judul LIKE '%Modernize%' AND (detail IS NULL OR detail='');

UPDATE solutions_section SET detail=
'<p class="sm-tag">Protect Your Business in an Increasingly Complex Digital Environment</p>
<p>Cyber threats are no longer only an IT concern. They represent direct risks to business continuity, customer trust, regulatory compliance, operational stability, and corporate reputation.</p>
<p>Sapta Tunas Teknologi delivers a comprehensive cybersecurity approach designed to protect organizations across users, devices, applications, networks, cloud environments, workloads, and data. Our security architecture combines preventive, detective, and responsive capabilities while applying modern principles such as Zero Trust, identity-centric security, segmentation, continuous monitoring, and threat intelligence.</p>
<h4>Our Capabilities</h4>
<ul><li>Next-Generation Firewall &amp; Network Security</li><li>Zero Trust Security Architecture</li><li>Secure Access Service Edge (SASE)</li><li>Identity &amp; Access Management</li><li>Endpoint Detection &amp; Response (EDR/XDR)</li><li>Network Detection &amp; Response (NDR)</li><li>Security Information &amp; Event Management (SIEM)</li><li>Security Operations Center (SOC)</li><li>Email, Web &amp; Application Security</li><li>Data Protection &amp; Data Loss Prevention</li><li>Vulnerability Assessment &amp; Penetration Testing</li><li>Digital Forensics &amp; Incident Response</li><li>Cybersecurity Assessment &amp; Hardening</li><li>Cyber Drill &amp; Tabletop Exercise</li></ul>'
WHERE judul LIKE '%yber%ecurity%' AND (detail IS NULL OR detail='');

UPDATE solutions_section SET detail=
'<p class="sm-tag">Turn Enterprise Data into a Trusted Business Asset</p>
<p>Organizations generate massive volumes of data across applications, infrastructure, users, machines, and digital services. Without the right strategy and architecture, that data can quickly become fragmented, difficult to manage, expensive to store, and vulnerable to loss.</p>
<p>Sapta Tunas Teknologi helps organizations manage the entire data lifecycle, from creation and storage to protection, governance, cleansing, integration, analytics, and long-term retention. We design data environments that maintain availability, integrity, security, scalability, and accessibility while preparing enterprise data for analytics and AI-driven use cases.</p>
<h4>Our Capabilities</h4>
<ul><li>Data Lifecycle Management</li><li>Data Integration &amp; Data Pipeline</li><li>Data Lakehouse &amp; Data Platform</li><li>Analytics Infrastructure</li><li>AI-Ready Data Architecture</li></ul>'
WHERE judul LIKE '%<b>Data</b>%' AND (detail IS NULL OR detail='');

UPDATE solutions_section SET detail=
'<p class="sm-tag">Transform Data into Intelligence and Business Impact</p>
<p>Artificial Intelligence is transforming how organizations operate, make decisions, engage customers, manage risk, and create new business opportunities.</p>
<p>Sapta Tunas Teknologi helps enterprises move beyond AI experimentation toward practical, scalable, and business-oriented AI adoption. We combine enterprise infrastructure, accelerated computing, data platforms, AI models, and industry-specific expertise to develop AI solutions that address real operational and business challenges.</p>
<h4>Our Capabilities</h4>
<ul><li>Enterprise AI Infrastructure</li><li>Generative AI</li><li>Large Language Models (LLM)</li><li>Enterprise Knowledge AI &amp; Retrieval-Augmented Generation</li><li>Computer Vision &amp; Video Analytics</li><li>AI Model Development &amp; Integration</li></ul>
<h4>Industry AI Use Cases</h4>
<ul><li>Smart City &amp; Public Safety</li><li>Healthcare &amp; Medical Imaging</li><li>Manufacturing &amp; Industrial Operations</li><li>Agriculture</li><li>Media &amp; Broadcasting (AI Avatar &amp; Video Generation)</li></ul>'
WHERE judul LIKE '%<b>AI</b>%' AND (detail IS NULL OR detail='');

UPDATE solutions_section SET detail=
'<p class="sm-tag">Turn AI Capabilities into Real Business Applications</p>
<p>AI delivers real value when it becomes part of everyday business operations.</p>
<p>Sapta Tunas Teknologi develops AI platforms and intelligent applications that embed artificial intelligence directly into enterprise workflows, customer experiences, operational processes, and decision-making systems. Rather than implementing isolated AI tools, we help organizations build an AI application ecosystem that can continuously evolve as business requirements change.</p>
<h4>Our Capabilities</h4>
<ul><li>Enterprise AI Platform</li><li>AI Application Development</li><li>Enterprise AI Assistant &amp; Copilot</li><li>AI Agent &amp; Agentic Workflow</li><li>AI Digital Human &amp; Virtual Assistant</li><li>AI Content Generation Platform</li><li>AI Integration with Enterprise Applications</li></ul>'
WHERE judul LIKE '%AI Platform%' AND (detail IS NULL OR detail='');
