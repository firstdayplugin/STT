-- =============================================================================
-- "Why Sapta Tunas" section (home): update the intro line to the client's copy.
-- The eyebrow "Why Sapta Tunas" is removed in the template markup (no DB change
-- needed). Run in phpMyAdmin / mysql CLI. Updates every language row.
-- =============================================================================

UPDATE `content_blocks`
SET `konten` = 'Trusted Expertise. Proven Partnerships. Meaningful Impact.'
WHERE `page_key` = 'home' AND `block_key` = 'why_intro';

-- If the row does not exist yet, create it (id + en).
INSERT INTO `content_blocks` (`page_key`,`block_key`,`lang`,`block_label`,`block_type`,`konten`,`is_active`)
SELECT * FROM (
  SELECT 'home','why_intro','id','Why — intro','text','Trusted Expertise. Proven Partnerships. Meaningful Impact.',1
  UNION ALL
  SELECT 'home','why_intro','en','Why — intro','text','Trusted Expertise. Proven Partnerships. Meaningful Impact.',1
) AS t
WHERE NOT EXISTS (
  SELECT 1 FROM `content_blocks` c WHERE c.`page_key`='home' AND c.`block_key`='why_intro'
);
