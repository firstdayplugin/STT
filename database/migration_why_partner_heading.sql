-- ============================================================================
-- Revisi home "What Sets Us Apart" -> "Why Partner With Us?"
-- Hanya mengganti TEKS heading + sub-heading. Desain tidak diubah.
--
-- Default teks sudah diperbarui di themes/anima/registry/home.php, jadi migrasi
-- ini HANYA diperlukan bila teks lama pernah di-override lewat CMS admin
-- (tersimpan di tabel content_blocks). Aman dijalankan walau tidak ada row
-- yang cocok (0 rows affected) dan idempotent (boleh diulang).
-- Jalankan di phpMyAdmin pada database live.
-- ============================================================================

UPDATE `content_blocks`
   SET `konten` = 'Why Partner With <span class="b">Us?</span>'
 WHERE `page_key` = 'home'
   AND `block_key` = 'why_title';

UPDATE `content_blocks`
   SET `konten` = 'Combining proven capabilities with a differentiated delivery approach'
 WHERE `page_key` = 'home'
   AND `block_key` = 'why_intro';
