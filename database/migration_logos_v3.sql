-- =============================================================================
-- Partner logos: switch to trimmed "-v3" images (uniform optical size on the
-- site via a single CSS height). Point every solution_logos row at the -v3 file.
-- Handles rows currently ending in "-v2.png" OR plain ".png".
-- Safe to re-run (rows already on -v3 are left unchanged).
-- Run in phpMyAdmin (or mysql CLI) against the site database.
-- =============================================================================

UPDATE `solution_logos`
SET `gambar` = REGEXP_REPLACE(`gambar`, '(-v[23])?\\.png$', '-v3.png')
WHERE `gambar` REGEXP '\\.png$';

-- If any home hero slides store logos inline (solution_slides.logos JSON that
-- references .png paths), bump those too. Comment out if not used.
UPDATE `solution_slides`
SET `logos` = REGEXP_REPLACE(`logos`, '(-v[23])?\\.png', '-v3.png')
WHERE `logos` LIKE '%.png%';
