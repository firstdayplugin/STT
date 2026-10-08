<?php
/**
 * Reusable Logo Carousel / Brands section
 * Settings-driven: columns (desktop/tablet/mobile), animation (fade/slide/static), full color.
 * Usage: include this partial. Optionally set $carousel_compact = true for service pages.
 */
if (!isset($db)) $db = Database::getInstance();
$lc_logos = $db->fetchAll("SELECT * FROM klien_logo WHERE is_active = 1 ORDER BY urutan ASC");
if (empty($lc_logos)) return;

$lc_cols_desktop = (int) get_setting('logo_carousel_cols_desktop', '5');
$lc_cols_tablet  = (int) get_setting('logo_carousel_cols_tablet', '3');
$lc_cols_mobile  = (int) get_setting('logo_carousel_cols_mobile', '2');
$lc_anim         = get_setting('logo_carousel_animation', 'slide'); // slide | fade | static
$lc_grayscale    = get_setting('logo_carousel_grayscale', '0') === '1';
$lc_autoplay     = get_setting('logo_carousel_autoplay', '1') === '1';
$lc_speed        = max(10, (int) get_setting('logo_carousel_speed', '40')); // seconds for full loop
$lc_pause_hover  = get_setting('logo_carousel_pause_hover', '1') === '1';

$lc_label = $carousel_label ?? get_content('about', 'brand_label', 'Who We Work With');
$lc_title = $carousel_title ?? get_content('about', 'brand_title', "Brands That Trust\nOur Expertise");
$lc_desc  = $carousel_desc  ?? get_content('about', 'brand_desc', 'Beragam perusahaan telah mempercayakan kebutuhan reklame mereka kepada kami.');
$lc_uid = 'lc' . substr(md5(uniqid()), 0, 6);
?>
<section class="brands-section" style="<?= !empty($carousel_compact) ? 'padding:60px 0' : '' ?>">
  <div class="container">
    <?php if (empty($carousel_hide_header)): ?>
    <div class="brands-header">
      <div class="section-label" style="justify-content:center"><?= htmlspecialchars($lc_label) ?></div>
      <h2 class="section-title"><?= nl2br(htmlspecialchars($lc_title)) ?></h2>
      <p class="section-desc" style="margin: 16px auto 0"><?= htmlspecialchars($lc_desc) ?></p>
    </div>
    <?php endif; ?>

    <?php if ($lc_anim === 'slide'): ?>
      <!-- INFINITE AUTO-SLIDE marquee (duplicates set for seamless loop) -->
      <div class="logo-carousel-wrap lc-marquee<?= $lc_pause_hover ? ' lc-pause-hover' : '' ?>" id="<?= $lc_uid ?>"
           data-autoplay="<?= $lc_autoplay ? '1' : '0' ?>"
           style="--lc-d:<?= $lc_cols_desktop ?>;--lc-t:<?= $lc_cols_tablet ?>;--lc-m:<?= $lc_cols_mobile ?>;--lc-speed:<?= $lc_speed ?>s">
        <div class="lc-marquee-viewport">
          <div class="lc-marquee-track" style="<?= $lc_autoplay ? '' : 'animation:none' ?>">
            <?php foreach ($lc_logos as $logo): ?>
              <div class="lc-item">
                <?php if ($logo['url']): ?><a href="<?= htmlspecialchars($logo['url']) ?>" target="_blank" rel="noopener"><?php endif; ?>
                  <img src="<?= uploads_url($logo['logo']) ?>" alt="<?= htmlspecialchars($logo['nama']) ?>"
                       class="<?= $lc_grayscale ? 'lc-grayscale' : '' ?>" loading="lazy">
                <?php if ($logo['url']): ?></a><?php endif; ?>
              </div>
            <?php endforeach; ?>
            <!-- Duplicate set for seamless infinite loop -->
            <?php foreach ($lc_logos as $logo): ?>
              <div class="lc-item" aria-hidden="true">
                <?php if ($logo['url']): ?><a href="<?= htmlspecialchars($logo['url']) ?>" target="_blank" rel="noopener" tabindex="-1"><?php endif; ?>
                  <img src="<?= uploads_url($logo['logo']) ?>" alt=""
                       class="<?= $lc_grayscale ? 'lc-grayscale' : '' ?>" loading="lazy">
                <?php if ($logo['url']): ?></a><?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

    <?php elseif ($lc_anim === 'fade'): ?>
      <!-- FADE animation (lightweight) -->
      <div class="logo-carousel-fade" id="<?= $lc_uid ?>"
           style="--lc-d:<?= $lc_cols_desktop ?>;--lc-t:<?= $lc_cols_tablet ?>;--lc-m:<?= $lc_cols_mobile ?>">
        <?php foreach ($lc_logos as $logo): ?>
          <div class="lc-item">
            <?php if ($logo['url']): ?><a href="<?= htmlspecialchars($logo['url']) ?>" target="_blank" rel="noopener"><?php endif; ?>
              <img src="<?= uploads_url($logo['logo']) ?>" alt="<?= htmlspecialchars($logo['nama']) ?>"
                   class="<?= $lc_grayscale ? 'lc-grayscale' : '' ?>" loading="lazy">
            <?php if ($logo['url']): ?></a><?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>

    <?php else: ?>
      <!-- STATIC grid -->
      <div class="logo-carousel-static"
           style="--lc-d:<?= $lc_cols_desktop ?>;--lc-t:<?= $lc_cols_tablet ?>;--lc-m:<?= $lc_cols_mobile ?>">
        <?php foreach ($lc_logos as $logo): ?>
          <div class="lc-item">
            <?php if ($logo['url']): ?><a href="<?= htmlspecialchars($logo['url']) ?>" target="_blank" rel="noopener"><?php endif; ?>
              <img src="<?= uploads_url($logo['logo']) ?>" alt="<?= htmlspecialchars($logo['nama']) ?>"
                   class="<?= $lc_grayscale ? 'lc-grayscale' : '' ?>" loading="lazy">
            <?php if ($logo['url']): ?></a><?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
