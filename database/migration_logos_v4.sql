-- =============================================================================
-- Partner logos: switch to "-v4" images (OPTICAL BALANCE — each logo
-- pre-rendered on a uniform-height canvas with equal visual weight). Point every
-- solution_logos row at the -v4 file. Handles rows ending in -v2.png / -v3.png /
-- plain .png. Safe to re-run.
-- Run in phpMyAdmin (or mysql CLI) against the site database.
-- =============================================================================

UPDATE `solution_logos`
SET `gambar` = REGEXP_REPLACE(`gambar`, '(-v[234])?\\.png$', '-v4.png')
WHERE `gambar` REGEXP '\\.png$';

-- If any home hero slides store logos inline (solution_slides.logos JSON that
-- references .png paths), bump those too. Comment out if not used.
UPDATE `solution_slides`
SET `logos` = REGEXP_REPLACE(`logos`, '(-v[234])?\\.png', '-v4.png')
WHERE `logos` LIKE '%.png%';
