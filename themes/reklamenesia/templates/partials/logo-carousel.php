<?php
/* Reusable client logo carousel — marquee. Same settings contract. */
if (!isset($db)) $db = Database::getInstance();
$lc_logos = $db->fetchAll("SELECT * FROM klien_logo WHERE is_active = 1 ORDER BY urutan ASC");
if (empty($lc_logos)) return;

$lc_anim      = get_setting('logo_carousel_animation', 'slide');
$lc_grayscale = get_setting('logo_carousel_grayscale', '0') === '1';
$lc_speed     = max(10, (int) get_setting('logo_carousel_speed', '40'));

$lc_label = $carousel_label ?? get_content('about', 'brand_label', 'Who We Work With');
$lc_title = $carousel_title ?? get_content('about', 'brand_title', 'Brands That Trust Our Expertise');
$lc_desc  = $carousel_desc  ?? get_content('about', 'brand_desc', 'Beragam perusahaan telah mempercayakan kebutuhan reklame mereka kepada kami.');
$compact  = !empty($carousel_compact);
?>
<section class="section<?= $compact ? ' tight' : '' ?> bg-white">
  <div class="container">
    <?php if (empty($carousel_hide_header)): ?>
    <div class="sec-head center" data-reveal>
      <span class="eyebrow center"><?= htmlspecialchars($lc_label) ?></span>
      <h2><?= nl2br(htmlspecialchars($lc_title)) ?></h2>
      <?php if ($lc_desc): ?><p class="lead" style="margin-inline:auto"><?= htmlspecialchars($lc_desc) ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($lc_anim === 'static'): ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:36px;align-items:center;margin-top:40px" data-stagger>
        <?php foreach ($lc_logos as $logo): ?>
          <div style="display:grid;place-items:center;min-height:90px">
            <?php if ($logo['url']): ?><a href="<?= htmlspecialchars($logo['url']) ?>" target="_blank" rel="noopener"><?php endif; ?>
              <img src="<?= uploads_url($logo['logo']) ?>" alt="<?= htmlspecialchars($logo['nama']) ?>" style="max-height:66px;width:auto;<?= $lc_grayscale ? 'filter:grayscale(1);opacity:.7' : '' ?>" loading="lazy">
            <?php if ($logo['url']): ?></a><?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="marquee" style="margin-top:40px">
        <div class="marquee-track" style="--gap:56px;--speed:<?= $lc_speed ?>s;align-items:center">
          <?php foreach ($lc_logos as $logo): ?>
            <div style="flex:none;display:grid;place-items:center;min-width:170px;min-height:90px">
              <?php if ($logo['url']): ?><a href="<?= htmlspecialchars($logo['url']) ?>" target="_blank" rel="noopener"><?php endif; ?>
                <img src="<?= uploads_url($logo['logo']) ?>" alt="<?= htmlspecialchars($logo['nama']) ?>" style="max-height:66px;width:auto;<?= $lc_grayscale ? 'filter:grayscale(1);opacity:.7' : '' ?>" loading="lazy">
              <?php if ($logo['url']): ?></a><?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>
