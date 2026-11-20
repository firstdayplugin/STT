-- ============================================================
-- Fix "AI Platform & Applications" (solutions_section id 5) + logos.
--   * Remove the stray red text colour.
--   * Correct the "Solution:" summary (was Cybersecurity text by mistake)
--     to the AI Platform capabilities from the client .doc.
--   * MeshDefend: use the full logo (was a mark-only SVG).
--   * Add its partner logos: SATU AI, SenseTime, Soca, Xeratic
--     (also feeds the Home prism, matched by section anchor).
-- Idempotent.
-- ============================================================
SET NAMES utf8mb4;

UPDATE solutions_section
SET teks_warna='',
    solusi='Enterprise AI Platform, AI Application Development, Enterprise AI Assistant & Copilot, AI Agent & Agentic Workflow, AI Digital Human & Virtual Assistant, AI Content Generation Platform, AI Integration with Enterprise Applications.'
WHERE id=5;

UPDATE solution_logos SET gambar='solutions/logos/meshdefend.png' WHERE gambar='solutions/logos/meshdefend.svg';

DELETE FROM solution_logos WHERE solution_id=5;
INSERT INTO solution_logos (solution_id,gambar,nama,urutan,is_active) VALUES
(5,'solutions/logos/satu-ai.png','SATU AI',1,1),
(5,'solutions/logos/sensetime.png','SenseTime',2,1),
(5,'solutions/logos/soca.png','Soca',3,1),
(5,'solutions/logos/xeratic.png','Xeratic',4,1);
