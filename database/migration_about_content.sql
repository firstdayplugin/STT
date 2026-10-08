-- ============================================================
-- About Us — align Mission + Core Values (ICARE) text to the client's
-- "About Us STT (Pak Ari)" document, exactly.
-- Matches by stable keys (mission: leading phrase; value: kode) so it is
-- id-independent. Idempotent + non-destructive.
-- (Intro / Vision / Milestone / Quality / Certs copy live in the theme
--  registry defaults — updated in themes/anima/registry/about.php.)
-- ============================================================
SET NAMES utf8mb4;

-- ---- Mission (6 points, exact document wording) -------------------------
UPDATE about_items SET teks='Build a wide and constructive relationship'                 WHERE seksi='mission' AND teks LIKE 'Build a wide%';
UPDATE about_items SET teks='Endless learning, Lasting excellence'                        WHERE seksi='mission' AND teks LIKE 'Endless learning%';
UPDATE about_items SET teks='Nurture the high commitment of integrity and professional ethics' WHERE seksi='mission' AND teks LIKE 'Nurture%';
UPDATE about_items SET teks='Ensure excellent and reliable support'                       WHERE seksi='mission' AND teks LIKE 'Ensure excellent%';
UPDATE about_items SET teks='Always innovate to provide latest technologies'              WHERE seksi='mission' AND teks LIKE 'Always%';
UPDATE about_items SET teks='Responsible and maintain our core values'                    WHERE seksi='mission' AND teks LIKE 'Responsible%';

-- ---- Core Values ICARE (title + description, exact document wording) -----
UPDATE about_items SET judul='Integrity',      teks='Honesty, Fairness and Strong ethic'        WHERE seksi='value' AND kode='I';
UPDATE about_items SET judul='Collaborate',    teks='Succeeding together through teamwork'       WHERE seksi='value' AND kode='C';
UPDATE about_items SET judul='Accountability', teks='Owning our decisions and actions.'          WHERE seksi='value' AND kode='A';
UPDATE about_items SET judul='Responsive',     teks='Swift and reliable services'                WHERE seksi='value' AND kode='R';
UPDATE about_items SET judul='Excellence',     teks='Delivering the best Solutions'              WHERE seksi='value' AND kode='E';
