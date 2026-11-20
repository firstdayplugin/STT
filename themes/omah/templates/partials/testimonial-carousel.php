<?php
/* OMAH THEME — Testimonial partial, called from about.php etc */
if (!isset($db)) $db = Database::getInstance();
$testis = $db->fetchAll("SELECT * FROM testimonial WHERE is_active=1 ORDER BY urutan ASC LIMIT 6");
if (empty($testis)) return;
$star_svg = '<svg width="14" height="14" viewBox="0 0 24 24" fill="#f7c547"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
?>
<section class="section-testi" style="padding-block:var(--sec-y);padding-inline:var(--pad-x);">
  <div style="max-width:var(--container-max);margin-inline:auto;display:flex;flex-direction:column;gap:40px;">
    <div class="sec-head-center">
      <span class="eyebrow" data-reveal><?= htmlspecialchars(get_content('home','testi_label','Testimoni')) ?></span>
      <h2 class="word-anim"><?= htmlspecialchars(get_content('home','testi_title','Apa Kata Pelanggan Kami?')) ?></h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;" data-stagger>
      <?php foreach ($testis as $t): ?>
      <div class="testi-card">
        <div class="testi-card-top">
          <div class="testi-stars"><?php $r=max(1,min(5,(int)($t['rating']??5)));for($i=0;$i<$r;$i++) echo $star_svg; ?></div>
          <p class="testi-review"><?= htmlspecialchars($t['ulasan']??$t['isi']??'') ?></p>
        </div>
        <div class="testi-reviewer">
          <div class="testi-avatar">
            <?php if (!empty($t['foto'])): ?><img src="<?= uploads_url($t['foto']) ?>" alt="" loading="lazy"><?php else: ?><div style="width:100%;height:100%;background:var(--om-gray-lt);display:flex;align-items:center;justify-content:center;"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--om-gray)" stroke-width="1.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div><?php endif; ?>
          </div>
          <div class="testi-info">
            <div class="testi-name"><?= htmlspecialchars($t['nama']) ?></div>
            <div class="testi-role"><?= htmlspecialchars($t['jabatan']??$t['posisi']??'') ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
