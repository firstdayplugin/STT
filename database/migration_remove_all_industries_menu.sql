-- ============================================================================
-- Remove the "All Industries" item from the Industry dropdown menu.
-- The nav is DB-driven (table `menus`); this item was seeded by
-- migration_industry.sql. Matched by name + url + header placement so it only
-- touches that one row. Re-runnable. Run in phpMyAdmin (SQL tab).
-- (Alternatively: Admin → Menu → delete "All Industries".)
-- ============================================================================
DELETE FROM menus
 WHERE nama = 'All Industries'
   AND url  = '/industri'
   AND tipe = 'header';

-- Verify:
-- SELECT id, nama, url, parent_id FROM menus WHERE tipe='header' ORDER BY urutan, id;
