-- ============================================================
-- About Us gallery — real Award / ISO (Quality) / Certification images
-- from the client's Konten_Foto_About.zip. Replaces the placeholder rows.
-- Images: uploads/about/**  (resized, no crop, PNG). Idempotent.
-- ============================================================
SET NAMES utf8mb4;
DELETE ci FROM content_i18n ci JOIN about_items ai ON ci.tabel='about_items' AND ci.row_id=ai.id WHERE ai.seksi IN ('award','quality','cert');
DELETE FROM about_items WHERE seksi IN ('award','quality','cert');

INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Customer','Award Customer','Award Dana','','about/awards/customer/01-award-dana.png',1,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Customer','Award Customer','Award Kalbe','','about/awards/customer/02-award-kalbe.png',2,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Customer','Award Customer','Award Kalbe','','about/awards/customer/03-award-kalbe.png',3,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Customer','Award Customer','Award Bintang toedjoe 3','','about/awards/customer/04-award-bintang-toedjoe-3.png',4,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Customer','Award Customer','Award Prodia','','about/awards/customer/05-award-prodia.png',5,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Customer','Award Customer','Bingkai Award Putih','','about/awards/customer/06-bingkai-award-putih.png',6,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Customer','Award Customer','Award Bintang Toedjoe','','about/awards/customer/07-award-bintang-toedjoe.png',7,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Customer','Award Customer','Award Angkasa Pura Indonesia','','about/awards/customer/08-award-angkasa-pura-indonesia.png',8,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Dell EMC Best OEM Contributor Partner','','about/awards/partner/01-sapta-tunas-teknologi-dell-emc-best-oem-contribu.png',9,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Best Solution Client Solution Partner of FY211','','about/awards/partner/02-sapta-tunas-teknologi-best-solution-client-solut.png',10,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Best Medium Business Partner of FY21','','about/awards/partner/03-sapta-tunas-teknologi-best-medium-business-partn.png',11,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Best Solution Client Solution Partner of FY211','','about/awards/partner/04-sapta-tunas-teknologi-best-solution-client-solut.png',12,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Best Solution Client Solution Partner of FY21','','about/awards/partner/05-sapta-tunas-teknologi-best-solution-client-solut.png',13,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Best Solution Focus Solution Platform 1ST Powerstore of FY22','','about/awards/partner/06-sapta-tunas-teknologi-best-solution-focus-soluti.png',14,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Most Valuable Partner of FY22','','about/awards/partner/07-sapta-tunas-teknologi-most-valuable-partner-of-f.png',15,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Dell EMC Best of the Best Platinum Partner South Asia','','about/awards/partner/08-sapta-tunas-teknologi-dell-emc-best-of-the-best-.png',16,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Dell EMC Best Solution ERP South Asia FY19','','about/awards/partner/09-sapta-tunas-teknologi-dell-emc-best-solution-erp.png',17,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Dell EMC Most Valuable Partner of FY19','','about/awards/partner/10-sapta-tunas-teknologi-dell-emc-most-valuable-par.png',18,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Dell EMC Best Solution ERP of FY19','','about/awards/partner/11-sapta-tunas-teknologi-dell-emc-best-solution-erp.png',19,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Most Valuable Partner of FY20','','about/awards/partner/12-sapta-tunas-teknologi-most-valuable-partner-of-f.png',20,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Best Medium Business Partner of FY20','','about/awards/partner/13-sapta-tunas-teknologi-best-medium-business-partn.png',21,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Most Valuable Partner of FY21','','about/awards/partner/14-sapta-tunas-teknologi-most-valuable-partner-of-f.png',22,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Award Cyble 1','','about/awards/partner/15-award-cyble-1.png',23,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Certificate Sangfor','','about/awards/partner/16-certificate-sangfor.png',24,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Dell Titanium Partner Award','','about/awards/partner/17-dell-titanium-partner-award.png',25,1);
INSERT INTO about_items (seksi,grup,tahun,judul,teks,gambar,urutan,is_active) VALUES ('award','Award Partner','Award Partner','Sapta Tunas Teknologi Dell EMC Best of the Best Platinum Partner South Asia','','about/awards/partner/18-sapta-tunas-teknologi-dell-emc-best-of-the-best-.png',26,1);

INSERT INTO about_items (seksi,judul,gambar,urutan,is_active) VALUES ('quality','ISO/IEC 27001:2022','about/quality/01-iso-iec-27001-2022.png',1,1);
INSERT INTO about_items (seksi,judul,gambar,urutan,is_active) VALUES ('quality','ISO 37001:2016','about/quality/02-iso-37001-2016.png',2,1);
INSERT INTO about_items (seksi,judul,gambar,urutan,is_active) VALUES ('quality','ISO 9001 · 14001 · 45001','about/quality/03-iso-9001-14001-45001.png',3,1);

INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Business Continuity','Broadcom vSphere','about/cert/business-continuity/01-broadcom-vsphere.png',1,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Business Continuity','Broadcom vSAN','about/cert/business-continuity/02-broadcom-vsan.png',2,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Business Continuity','Broadcom VCF','about/cert/business-continuity/03-broadcom-vcf.png',3,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Business Continuity','Broadcom NSX','about/cert/business-continuity/04-broadcom-nsx.png',4,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Business Continuity','Broadcom Tanzu','about/cert/business-continuity/05-broadcom-tanzu.png',5,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Business Continuity','Nutanix MCI','about/cert/business-continuity/06-nutanix-mci.png',6,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Business Continuity','Nutanix Unified Storage','about/cert/business-continuity/07-nutanix-unified-storage.png',7,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Business Continuity','Sangfor','about/cert/business-continuity/08-sangfor.png',8,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Business Continuity','RedHat','about/cert/business-continuity/09-redhat.png',9,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Business Continuity','Microsoft MCP','about/cert/business-continuity/10-microsoft-mcp.png',10,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','CyberSecurity','CEH','about/cert/cybersecurity/01-ceh.png',11,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','CyberSecurity','BTL1','about/cert/cybersecurity/02-btl1.png',12,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','CyberSecurity','THM','about/cert/cybersecurity/03-thm.png',13,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','CyberSecurity','Forti','about/cert/cybersecurity/04-forti.png',14,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','CyberSecurity','Forti','about/cert/cybersecurity/05-forti.png',15,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','CyberSecurity','Untitled design','about/cert/cybersecurity/06-untitled-design.png',16,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Data & AI','Rafay GPU','about/cert/data-ai/01-rafay-gpu.png',17,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Data & AI','Weka Certitied','about/cert/data-ai/02-weka-certitied.png',18,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Dell','Dell Server Operate','about/cert/dell/01-dell-server-operate.png',19,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Dell','Dell Server XE GPU','about/cert/dell/02-dell-server-xe-gpu.png',20,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Dell','Dell Storage Design','about/cert/dell/03-dell-storage-design.png',21,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Dell','Dell Storage PowerStore','about/cert/dell/04-dell-storage-powerstore.png',22,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Dell','Dell Storage PowerScale','about/cert/dell/05-dell-storage-powerscale.png',23,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Dell','Dell Storage ECS','about/cert/dell/06-dell-storage-ecs.png',24,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Dell','Dell Backup DD','about/cert/dell/07-dell-backup-dd.png',25,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Dell','Dell Backup CRS','about/cert/dell/08-dell-backup-crs.png',26,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Dell','Dell Backup PPDM','about/cert/dell/09-dell-backup-ppdm.png',27,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Dell','Dell SONiC','about/cert/dell/10-dell-sonic.png',28,1);
INSERT INTO about_items (seksi,grup,judul,gambar,urutan,is_active) VALUES ('cert','Dell','Certif','about/cert/dell/11-certif.png',29,1);
