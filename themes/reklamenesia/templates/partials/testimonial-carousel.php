<?php
/* Reusable testimonial carousel — center-featured, looped.
   Reads the same settings keys as the default theme.            */
if (!isset($db)) $db = Database::getInstance();
$ts_limit = max(1, (int) get_setting('testimonial_limit_home', '50'));
$ts_items = $db->fetchAll("SELECT * FROM testimonial WHERE is_active = 1 ORDER BY urutan ASC LIMIT $ts_limit");
if (empty($ts_items)) return;

$ts_autoplay = get_setting('testimonial_autoplay', '1') === '1';
$ts_speed    = max(2, (int) get_setting('testimonial_speed', '6'));
$ts_show_dots= get_setting('testimonial_show_dots', '1') === '1';

$ts_label = $testi_label ?? get_content('home', 'testi_label', 'Testimonials');
$ts_title = $testi_title ?? get_content('home', 'testi_title', 'What Our Clients Say');
$ts_desc  = $testi_desc  ?? get_content('home', 'testi_desc', 'Kepercayaan dan kepuasan klien menjadi bukti komitmen kami menghadirkan solusi reklame berkualitas.');

if (!function_exists('rk_stars')) {
  function rk_stars($n) {
    $n = max(0, min(5, (int)$n)); $out = '';
    for ($i = 0; $i < 5; $i++) {
      $fill = $i < $n ? 'currentColor' : 'rgba(12,12,12,.14)';
      $out .= '<svg viewBox="0 0 24 24" fill="'.$fill.'"><path d="M12 2l2.9 6.3 6.9.7-5.2 4.6 1.5 6.8L12 17.8 5.9 20.4l1.5-6.8L2.2 9l6.9-.7z"/></svg>';
    }
    return $out;
  }
}
?>
<section class="section testi">
  <div class="container">
    <?php if (empty($ts_hide_header)): ?>
    <div class="sec-head center" data-reveal>
      <span class="eyebrow center"><?= htmlspecialchars($ts_label) ?></span>
      <h2><?= nl2br(htmlspecialchars($ts_title)) ?></h2>
      <?php if ($ts_desc): ?><p class="lead" style="margin-inline:auto"><?= htmlspecialchars($ts_desc) ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="testi-stage" data-reveal data-testi
         data-autoplay="<?= $ts_autoplay ? '1' : '0' ?>" data-speed="<?= $ts_speed * 1000 ?>">
      <div class="testi-viewport">
        <div class="testi-track">
          <?php foreach ($ts_items as $t): ?>
          <div class="testi-slide">
            <div class="testi-card">
              <div class="testi-photo">
                <?php if (!empty($t['foto'])): ?>
                  <img src="<?= uploads_url($t['foto']) ?>" alt="<?= htmlspecialchars($t['nama']) ?>" loading="lazy">
                <?php else: ?>
                  <div class="ph"><?= strtoupper(mb_substr($t['nama'], 0, 1)) ?></div>
                <?php endif; ?>
              </div>
              <div>
                <p class="testi-quote">&ldquo;<?= htmlspecialchars($t['isi']) ?>&rdquo;</p>
                <div class="testi-author">
                  <div class="testi-name"><?= htmlspecialchars($t['nama']) ?></div>
                  <div class="testi-role"><?= htmlspecialchars(trim(($t['jabatan'] ?? '') . (!empty($t['perusahaan']) ? ' · ' . $t['perusahaan'] : ''), ' ·')) ?></div>
                  <div class="testi-stars"><?= rk_stars($t['rating'] ?? 5) ?></div>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php if (count($ts_items) > 1): ?>
      <div class="testi-nav">
        <button class="testi-btn" data-testi-prev aria-label="Sebelumnya"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
        <?php if ($ts_show_dots): ?><div class="testi-dots"></div><?php endif; ?>
        <button class="testi-btn" data-testi-next aria-label="Berikutnya"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
