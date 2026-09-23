-- ============================================================
-- Seed partner logos per Solutions category (solution_logos).
-- Each logo is a separate file/row. Idempotent for this seed set.
-- Managed in Admin > Solutions Page > Edit section > Partner Logos.
-- ============================================================
SET NAMES utf8mb4;
DELETE FROM solution_logos WHERE solution_id IN (1,2,3,4,5);
INSERT INTO solution_logos (solution_id,gambar,nama,urutan,is_active) VALUES
  (1,'solutions/logos/dell.png','Dell Technologies',1,1),
  (1,'solutions/logos/vmware.png','VMware by Broadcom',2,1),
  (1,'solutions/logos/microsoft.png','Microsoft',3,1),
  (1,'solutions/logos/redhat.png','Red Hat',4,1),
  (1,'solutions/logos/nutanix.png','Nutanix',5,1),
  (1,'solutions/logos/veeam.png','Veeam',6,1),
  (1,'solutions/logos/commvault.png','Commvault',7,1),
  (1,'solutions/logos/hycu.png','HYCU',8,1),
  (1,'solutions/logos/sangfor.png','Sangfor Technologies',9,1),
  (1,'solutions/logos/amd.png','AMD',10,1),
  (1,'solutions/logos/intel.svg','Intel',11,1),
  (1,'solutions/logos/infraon.svg','Infraon',12,1),
  (4,'solutions/logos/nvidia.png','NVIDIA',1,1),
  (4,'solutions/logos/sensetime.png','SenseTime',2,1),
  (4,'solutions/logos/rafay.png','Rafay',3,1),
  (4,'solutions/logos/soca.png','Soca',4,1),
  (4,'solutions/logos/meshdefend.png','Meshdefend',5,1),
  (5,'solutions/logos/satu-ai.png','SATU AI',1,1),
  (5,'solutions/logos/sensetime.png','SenseTime',2,1),
  (5,'solutions/logos/soca.png','Soca',3,1),
  (5,'solutions/logos/xeratic.png','Xeratic',4,1),
  (3,'solutions/logos/weka.png','WEKA',1,1),
  (3,'solutions/logos/xeratic.png','Xeratic',2,1),
  (2,'solutions/logos/cisco.png','Cisco',1,1),
  (2,'solutions/logos/fortinet.png','Fortinet',2,1),
  (2,'solutions/logos/elastic.svg','Elastic',3,1),
  (2,'solutions/logos/cyble.png','Cyble',4,1),
  (2,'solutions/logos/t-innoware.png','T-Innoware',5,1);
