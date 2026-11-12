-- ============================================================
-- Home "prism" (cubic Solutions animation) — sync with /solutions:
-- same 5 solutions, same order (Modernize Infrastructure first), and each
-- slide links to its section on /solutions. Idempotent.
-- ============================================================
SET NAMES utf8mb4;

UPDATE solution_slides SET urutan=1, eyebrow='Modernize Infrastructure',     url='solutions#modernize-infrastructure' WHERE id=1;
UPDATE solution_slides SET urutan=2, eyebrow='Cybersecurity',                 url='solutions#cybersecurity'            WHERE id=2;
UPDATE solution_slides SET urutan=3, eyebrow='Data Management',               url='solutions#data-management'          WHERE id=3;
UPDATE solution_slides SET urutan=4, eyebrow='Artificial Intelligence (AI)',  url='solutions#artificial-intelligence-ai' WHERE id=4;
UPDATE solution_slides SET urutan=5, eyebrow='AI Platform & Applications',    url='solutions#ai-platform-applications' WHERE id=5;
