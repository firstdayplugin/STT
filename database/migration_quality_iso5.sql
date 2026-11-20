-- ============================================================
-- About Us > Our Quality Standards — 5 badge ISO terpisah (revisi klien):
-- ISO 9001, ISO 14001, ISO 45001, ISO/IEC 27001:2022, ISO 37001:2016.
-- Menggantikan 3 kartu lama (27001, 37001, gabungan 9001·14001·45001).
-- Gambar: uploads/about/quality/  — Idempotent; hanya menyentuh seksi='quality'.
-- ============================================================
SET NAMES utf8mb4;
DELETE ci FROM content_i18n ci JOIN about_items ai ON ci.tabel='about_items' AND ci.row_id=ai.id WHERE ai.seksi='quality';
DELETE FROM about_items WHERE seksi='quality';

INSERT INTO about_items (seksi,judul,gambar,urutan,is_active) VALUES ('quality','ISO 9001','about/quality/01-iso-9001.png',1,1);
INSERT INTO about_items (seksi,judul,gambar,urutan,is_active) VALUES ('quality','ISO 14001','about/quality/02-iso-14001.png',2,1);
INSERT INTO about_items (seksi,judul,gambar,urutan,is_active) VALUES ('quality','ISO 45001','about/quality/03-iso-45001.png',3,1);
INSERT INTO about_items (seksi,judul,gambar,urutan,is_active) VALUES ('quality','ISO/IEC 27001:2022','about/quality/04-iso-iec-27001-2022.png',4,1);
INSERT INTO about_items (seksi,judul,gambar,urutan,is_active) VALUES ('quality','ISO 37001:2016','about/quality/05-iso-37001-2016.png',5,1);
