-- ============================================================
-- Partner logos per Solutions category — each logo uploaded SEPARATELY
-- (replaces the single combined partner_img strip). Managed in the CMS
-- (Admin → Solutions Page → edit a section → Partner Logos).
-- ============================================================
SET NAMES utf8mb4;
CREATE TABLE IF NOT EXISTS solution_logos (
  id INT(11) NOT NULL AUTO_INCREMENT,
  solution_id INT(11) NOT NULL,
  gambar VARCHAR(255) NOT NULL,
  nama VARCHAR(120) DEFAULT NULL,
  urutan INT(11) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  KEY idx_solution (solution_id, urutan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
