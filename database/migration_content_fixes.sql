-- ============================================================
-- Content accuracy + link integrity fixes (per client PDFs).
-- 1) Our Solutions naming to match Our_Solutions.pdf exactly.
-- 2) Home prism solution slides renamed to match.
-- 3) Orbit "Our Industries" cards get real links to each industry page.
-- Idempotent + non-destructive.
-- ============================================================
SET NAMES utf8mb4;

-- ---- Solutions page (solutions_section) — exact PDF names -----------------
-- 01 Modernize Infrastructure / 02 Cybersecurity / 03 Data Management /
-- 04 Artificial Intelligence (AI) / 05 AI Platform & Applications
UPDATE solutions_section SET judul='Modernize <b>Infrastructure</b>'        WHERE id=1;
UPDATE solutions_section SET judul='<b>Cybersecurity</b>'                    WHERE id=2;
UPDATE solutions_section SET judul='Data <b>Management</b>'                  WHERE id=3;
UPDATE solutions_section SET judul='Artificial Intelligence <b>(AI)</b>'     WHERE id=4;
UPDATE solutions_section SET judul='AI Platform <b>& Applications</b>'       WHERE id=5;

-- ---- Home prism (solution_slides) — same names ---------------------------
UPDATE solution_slides SET judul='Modernize <b>Infrastructure</b>'      WHERE id=1;
UPDATE solution_slides SET judul='<b>Cybersecurity</b>'                 WHERE id=2;
UPDATE solution_slides SET judul='Data <b>Management</b>'               WHERE id=3;
UPDATE solution_slides SET judul='Artificial <b>Intelligence (AI)</b>' WHERE id=4;
UPDATE solution_slides SET judul='AI Platform <b>& Applications</b>'    WHERE id=5;

-- ---- Our Industries orbit — link every card to its industry page ---------
-- Detail route is /industri/<slug>; orbit href reads industri.url.
UPDATE industri SET url = CONCAT('industri/', slug)
  WHERE slug IS NOT NULL AND slug <> '' AND (url IS NULL OR url = '' OR url = '#');
