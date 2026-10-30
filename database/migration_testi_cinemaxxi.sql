-- =============================================================================
-- Add Cinema XXI video testimonial as the FIRST testimonial slide (home).
-- urutan=-100 guarantees it sorts first (ORDER BY urutan, id). Re-runnable:
-- only inserts if the same YouTube video is not already present.
-- =============================================================================

INSERT INTO `testimonial` (`nama`,`jabatan`,`perusahaan`,`isi`,`rating`,`tipe`,`video_url`,`urutan`,`is_active`)
SELECT 'Khristianto Herwahyudi','Head of IT','Cinema XXI',
  'Penasaran bagaimana Cinema XXI menjaga performa data center tetap andal dan aman di tengah pesatnya kebutuhan layanan digital?',
  5,'video','https://www.youtube.com/watch?v=tbbFjdwfe-k',-100,1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `testimonial` WHERE `video_url` LIKE '%tbbFjdwfe-k%'
);
