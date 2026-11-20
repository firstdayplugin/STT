-- ============================================================
-- About Us > List of Certification — kategori & badge baru (revisi klien).
-- Kategori: Enterprise Infrastructure, CyberSecurity, Data Management,
-- Artificial Intelligence, AI Platforms & Applications (21 badge).
-- Gambar lama dihapus dari daftar; gambar baru: uploads/about/cert/<kategori>/
-- Idempotent (aman dijalankan ulang). Hanya menyentuh baris seksi='cert'.
-- ============================================================
SET NAMES utf8mb4;
DELETE ci FROM content_i18n ci JOIN about_items ai ON ci.tabel='about_items' AND ci.row_id=ai.id WHERE ai.seksi='cert';
DELETE FROM about_items WHERE seksi='cert';

INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Enterprise Infrastructure','Dell Server XE GPU','about/cert/enterprise-infrastructure/01-dell-server-xe-gpu.png',1,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Enterprise Infrastructure','Dell Storage PowerStore','about/cert/enterprise-infrastructure/02-dell-storage-powerstore.png',2,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Enterprise Infrastructure','Dell Storage ECS','about/cert/enterprise-infrastructure/03-dell-storage-ecs.png',3,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Enterprise Infrastructure','Dell Backup DD','about/cert/enterprise-infrastructure/04-dell-backup-dd.png',4,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Enterprise Infrastructure','Dell SONiC','about/cert/enterprise-infrastructure/05-dell-sonic.png',5,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Enterprise Infrastructure','Broadcom VCF','about/cert/enterprise-infrastructure/06-broadcom-vcf.png',6,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Enterprise Infrastructure','Nutanix MCI','about/cert/enterprise-infrastructure/07-nutanix-mci.png',7,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Enterprise Infrastructure','Sangfor','about/cert/enterprise-infrastructure/08-sangfor.png',8,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Enterprise Infrastructure','RedHat','about/cert/enterprise-infrastructure/09-redhat.png',9,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Enterprise Infrastructure','Professional AI Infrastructure','about/cert/enterprise-infrastructure/10-professional-ai-infrastructure.png',10,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','CyberSecurity','CEH','about/cert/cybersecurity/01-ceh.webp',11,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','CyberSecurity','BTL1','about/cert/cybersecurity/02-btl1.png',12,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','CyberSecurity','THM','about/cert/cybersecurity/03-thm.png',13,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','CyberSecurity','Forti','about/cert/cybersecurity/04-forti.png',14,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','CyberSecurity','Cisco CCNP','about/cert/cybersecurity/05-cisco-ccnp.png',15,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Data Management','Nvidia Accelerated Data Science','about/cert/data-management/01-nvidia-accelerated-data-science.jpg',16,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Data Management','Weka Certified','about/cert/data-management/02-weka-certified.png',17,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Artificial Intelligence','Nvidia AI Advisor Technical Sales','about/cert/artificial-intelligence/01-nvidia-ai-advisor-technical-sales.png',18,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Artificial Intelligence','Rafay GPU','about/cert/artificial-intelligence/02-rafay-gpu.png',19,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','AI Platforms & Applications','Nvidia Gen AI LLMs','about/cert/ai-platforms-applications/01-nvidia-gen-ai-llms.png',20,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','AI Platforms & Applications','Nvidia Agentic AI','about/cert/ai-platforms-applications/02-nvidia-agentic-ai.png',21,1);
