-- ============================================================================
-- Reorder partner logos per Solutions category to match the reference layout.
-- Affects BOTH /solutions and the Home prism (both read solution_logos).
-- Matches by section title + logo name/filename (version-proof: dell-v4.png etc.),
-- so it works regardless of exact file versions. Logos NOT in the reference list
-- keep their place but move AFTER the ordered ones. Re-runnable.
-- Run in phpMyAdmin (SQL tab).
-- ============================================================================
SET NAMES utf8mb4;

SET @infra := (SELECT id FROM solutions_section WHERE judul LIKE '%Infrastructure%' ORDER BY id LIMIT 1);
SET @cyber := (SELECT id FROM solutions_section WHERE judul LIKE '%Cybersecurity%'  ORDER BY id LIMIT 1);
SET @data  := (SELECT id FROM solutions_section WHERE judul LIKE '%Data%'           ORDER BY id LIMIT 1);
SET @ai    := (SELECT id FROM solutions_section WHERE judul LIKE '%Artificial%'     ORDER BY id LIMIT 1);
SET @aip   := (SELECT id FROM solutions_section WHERE judul LIKE '%Platform%'       ORDER BY id LIMIT 1);

-- Push every logo back first; the ordered ones below reclaim 1..N, extras trail after.
UPDATE solution_logos SET urutan = urutan + 100 WHERE solution_id IN (@infra,@cyber,@data,@ai,@aip);

-- 1) ENTERPRISE INFRASTRUCTURE
UPDATE solution_logos SET urutan=1  WHERE solution_id=@infra AND (LOWER(gambar) LIKE '%dell%'      OR LOWER(nama) LIKE '%dell%');
UPDATE solution_logos SET urutan=2  WHERE solution_id=@infra AND (LOWER(gambar) LIKE '%amd%'       OR LOWER(nama) LIKE '%amd%');
UPDATE solution_logos SET urutan=3  WHERE solution_id=@infra AND (LOWER(gambar) LIKE '%intel%'     OR LOWER(nama) LIKE '%intel%');
UPDATE solution_logos SET urutan=4  WHERE solution_id=@infra AND (LOWER(gambar) LIKE '%vmware%'    OR LOWER(nama) LIKE '%vmware%');
UPDATE solution_logos SET urutan=5  WHERE solution_id=@infra AND (LOWER(gambar) LIKE '%nutanix%'   OR LOWER(nama) LIKE '%nutanix%');
UPDATE solution_logos SET urutan=6  WHERE solution_id=@infra AND (LOWER(gambar) LIKE '%sangfor%'   OR LOWER(nama) LIKE '%sangfor%');
UPDATE solution_logos SET urutan=7  WHERE solution_id=@infra AND (LOWER(gambar) LIKE '%redhat%'    OR LOWER(nama) LIKE '%red hat%' OR LOWER(nama) LIKE '%redhat%');
UPDATE solution_logos SET urutan=8  WHERE solution_id=@infra AND (LOWER(gambar) LIKE '%commvault%' OR LOWER(nama) LIKE '%commvault%');
UPDATE solution_logos SET urutan=9  WHERE solution_id=@infra AND (LOWER(gambar) LIKE '%hycu%'      OR LOWER(nama) LIKE '%hycu%');
UPDATE solution_logos SET urutan=10 WHERE solution_id=@infra AND (LOWER(gambar) LIKE '%veeam%'     OR LOWER(nama) LIKE '%veeam%');
UPDATE solution_logos SET urutan=11 WHERE solution_id=@infra AND (LOWER(gambar) LIKE '%infraon%'   OR LOWER(nama) LIKE '%infraon%');

-- 2) CYBERSECURITY
UPDATE solution_logos SET urutan=1 WHERE solution_id=@cyber AND (LOWER(nama) LIKE '%satu%soc%' OR LOWER(gambar) LIKE '%satu-soc%' OR LOWER(gambar) LIKE '%satusoc%');
UPDATE solution_logos SET urutan=2 WHERE solution_id=@cyber AND (LOWER(gambar) LIKE '%cyble%'    OR LOWER(nama) LIKE '%cyble%');
UPDATE solution_logos SET urutan=3 WHERE solution_id=@cyber AND (LOWER(gambar) LIKE '%cisco%'    OR LOWER(nama) LIKE '%cisco%');
UPDATE solution_logos SET urutan=4 WHERE solution_id=@cyber AND (LOWER(gambar) LIKE '%fortinet%' OR LOWER(nama) LIKE '%fortinet%');
UPDATE solution_logos SET urutan=5 WHERE solution_id=@cyber AND (LOWER(gambar) LIKE '%elastic%'  OR LOWER(nama) LIKE '%elastic%');
UPDATE solution_logos SET urutan=6 WHERE solution_id=@cyber AND (LOWER(gambar) LIKE '%f5%'       OR LOWER(nama) LIKE '%f5%');

-- 3) DATA MANAGEMENT
UPDATE solution_logos SET urutan=1 WHERE solution_id=@data AND (LOWER(gambar) LIKE '%xeratic%' OR LOWER(nama) LIKE '%xeratic%');
UPDATE solution_logos SET urutan=2 WHERE solution_id=@data AND (LOWER(gambar) LIKE '%weka%'    OR LOWER(nama) LIKE '%weka%');

-- 4) ARTIFICIAL INTELLIGENCE
UPDATE solution_logos SET urutan=1 WHERE solution_id=@ai AND (LOWER(gambar) LIKE '%sensetime%'  OR LOWER(nama) LIKE '%sensetime%');
UPDATE solution_logos SET urutan=2 WHERE solution_id=@ai AND (LOWER(gambar) LIKE '%meshdefend%' OR LOWER(nama) LIKE '%meshdefend%');
UPDATE solution_logos SET urutan=3 WHERE solution_id=@ai AND (LOWER(gambar) LIKE '%nvidia%'     OR LOWER(nama) LIKE '%nvidia%');
UPDATE solution_logos SET urutan=4 WHERE solution_id=@ai AND (LOWER(gambar) LIKE '%soca%'       OR LOWER(nama) LIKE '%soca%');
UPDATE solution_logos SET urutan=5 WHERE solution_id=@ai AND (LOWER(gambar) LIKE '%rafay%'      OR LOWER(nama) LIKE '%rafay%');

-- 5) AI PLATFORMS & APPLICATIONS
UPDATE solution_logos SET urutan=1 WHERE solution_id=@aip AND (LOWER(gambar) LIKE '%satu-ai%'  OR LOWER(nama) LIKE '%satu ai%' OR LOWER(nama) LIKE '%satu.ai%');
UPDATE solution_logos SET urutan=2 WHERE solution_id=@aip AND (LOWER(gambar) LIKE '%sensetime%' OR LOWER(nama) LIKE '%sensetime%');
UPDATE solution_logos SET urutan=3 WHERE solution_id=@aip AND (LOWER(gambar) LIKE '%soca%'      OR LOWER(nama) LIKE '%soca%');
