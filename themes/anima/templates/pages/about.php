<?php
/**
 * Anima theme — About Us (route: /about-us). Full Figma layout, fully CMS-driven.
 * Singular copy + vision images: ac()/aimg('about', key) (Konten Halaman → Tentang Kami).
 * Repeaters (mission, ICARE values, milestones, awards, quality, certs) come from `about_items`
 * (admin "Tentang Kami"), each language-aware via tr_field and image-capable.
 * Sliders (milestone cross-fade, awards/quality/cert) are driven by anima-ui.js.
 */
if (!isset($db) && class_exists('Database')) { $db = Database::getInstance(); }
$seo = [
  'title'       => get_setting('site_title_about', 'About Us — ' . get_setting('site_name', 'Sapta Tunas Teknologi')),
  'description' => get_setting('site_description_about', 'Sapta Tunas Teknologi — Enterprise Solution Provider sejak 2015.'),
];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');

$about_q = function (string $seksi) use ($db) {
    try { return $db ? $db->fetchAll("SELECT * FROM about_items WHERE seksi=? AND is_active=1 ORDER BY urutan, id", [$seksi]) : []; }
    catch (\Throwable $e) { return []; }
};
$mission    = $about_q('mission');
$values     = $about_q('value');
$milestones = $about_q('milestone');
$awards     = $about_q('award');
$quality    = $about_q('quality');
$certs      = $about_q('cert');
$atr = fn($row, $field) => tr_field('about_items', (int)($row['id'] ?? 0), $field, $row[$field] ?? '');
$aimgu = fn($row) => !empty($row['gambar']) ? uploads_url($row['gambar']) : '';

// group helper (awards by year, certs by brand) preserving order
$group_by = function (array $rows, string $key) {
    $g = [];
    foreach ($rows as $r) { $k = trim((string)($r[$key] ?? '')); if ($k === '') $k = '—'; $g[$k][] = $r; }
    return $g;
};
$awardsByYear = $group_by($awards, 'tahun');
$certsByBrand = $group_by($certs, 'grup');

// Milestone data for the cross-fade slider (JSON injected CSP-safely).
$mile_json = [];
foreach ($milestones as $m) {
    $mile_json[] = [
        'year'  => (string)($m['tahun'] ?? ''),
        'now'   => (($m['kode'] ?? '') === 'now'),
        'title' => (string)$atr($m, 'judul'),
        'text'  => (string)$atr($m, 'teks'),
        'img'   => $aimgu($m),
    ];
}

$check = '<svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>';
$arrowL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>';
$arrowR = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6 6-6" transform="rotate(-90 12 12)"/></svg>';
$arrowRt = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>';
$vimg1 = aimg('about', 'vision_img1', '');
$vimg2 = aimg('about', 'vision_img2', '');
?>
<main class="page-body">
  <div class="page-shell ab">

    <!-- Intro -->
    <section class="ab-intro">
      <div class="eyebrow"><?= ac('about', 'intro_eyebrow') ?></div>
      <h1><?= ac('about', 'intro_title') ?></h1>
      <p class="lead"><?= ac('about', 'intro_body', true) ?></p>
    </section>

    <!-- Vision + Mission (image LEFT, text RIGHT) -->
    <section class="ab-sec ab-vm">
      <div class="ab-vm-media">
        <div class="ab-vm-img ab-vm-img1"><?php if ($vimg1): ?><img src="<?= htmlspecialchars($vimg1) ?>" alt="" data-fallback="bg"><?php else: ?><span class="ab-ph">Gambar 1</span><?php endif; ?></div>
        <div class="ab-vm-img ab-vm-img2"><?php if ($vimg2): ?><img src="<?= htmlspecialchars($vimg2) ?>" alt="" data-fallback="bg"><?php else: ?><span class="ab-ph">Gambar 2</span><?php endif; ?></div>
      </div>
      <div class="ab-vm-text">
        <div class="ab-vcard">
          <h2><?= ac('about', 'vision_title') ?></h2>
          <p><?= ac('about', 'vision_body') ?></p>
        </div>
        <div class="ab-mcard">
          <h2><?= ac('about', 'mission_title') ?></h2>
          <ul class="ab-mlist">
            <?php foreach ($mission as $m): ?>
              <li><span class="chk"><?= $check ?></span><span><?= htmlspecialchars($atr($m, 'teks')) ?></span></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </section>

    <!-- Values (ICARE) — each letter has an uploadable icon/3D image -->
    <section class="ab-sec">
      <div class="ab-head"><h2><?= ac('about', 'values_eyebrow') ?> <span class="blue"><?= ac('about', 'values_title', true) ?></span></h2></div>
      <div class="ab-values-grid">
        <?php foreach ($values as $v): $vi = $aimgu($v); ?>
          <div class="ab-val">
            <div class="ab-val-badge"><?php if ($vi): ?><img src="<?= htmlspecialchars($vi) ?>" alt="<?= htmlspecialchars($v['kode'] ?? '') ?>"><?php else: ?><span><?= htmlspecialchars($v['kode'] ?? '') ?></span><?php endif; ?></div>
            <h3><?= htmlspecialchars($atr($v, 'judul')) ?></h3>
            <p><?= htmlspecialchars($atr($v, 'teks')) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Milestone SLIDER (cross-fade; timeline drives the text+image above) -->
    <?php if ($milestones): ?>
    <section class="ab-sec ab-mile-sec">
      <div class="ab-head ab-head-left"><h2><?= ac('about', 'milestone_title', true) ?></h2></div>
      <div class="ab-mile" data-milestones="<?= htmlspecialchars(json_encode($mile_json, JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>">
        <div class="ab-mile-top">
          <div class="ab-mile-text" data-mile-text>
            <h3 data-mile-title></h3>
            <p data-mile-body></p>
          </div>
          <div class="ab-mile-media">
            <div class="ab-mile-frame"><img data-mile-img alt="" data-fallback="bg"></div>
          </div>
        </div>
        <div class="ab-mile-timeline">
          <button class="ab-mile-arrow" data-mile-prev aria-label="Sebelumnya"><?= $arrowL ?></button>
          <div class="ab-mile-track" data-mile-track>
            <?php foreach ($milestones as $i => $m): $now = (($m['kode'] ?? '') === 'now'); ?>
              <button class="ab-mile-node<?= $i === 0 ? ' on' : '' ?><?= $now ? ' now' : '' ?>" data-mile-node="<?= $i ?>">
                <span class="dot"></span><span class="yr"><?= htmlspecialchars($m['tahun'] ?? '') ?></span>
              </button>
            <?php endforeach; ?>
          </div>
          <button class="ab-mile-arrow" data-mile-next aria-label="Berikutnya"><?= $arrowRt ?></button>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- Awards SLIDER (by year) -->
    <?php if ($awards): ?>
    <section class="ab-sec">
      <div class="ab-head"><h2><?= ac('about', 'awards_title') ?></h2><p><?= ac('about', 'awards_intro') ?></p></div>
      <div class="ab-slider" data-year-slider>
        <div class="ab-slider-head">
          <button class="ab-mile-arrow" data-year-prev aria-label="Tahun sebelumnya"><?= $arrowL ?></button>
          <div class="ab-slider-year" data-year-label><?= htmlspecialchars((string)array_key_first($awardsByYear)) ?></div>
          <button class="ab-mile-arrow" data-year-next aria-label="Tahun berikutnya"><?= $arrowRt ?></button>
        </div>
        <?php foreach ($awardsByYear as $yr => $items): ?>
        <div class="ab-slider-page" data-year-page="<?= htmlspecialchars($yr) ?>"<?= $yr === array_key_first($awardsByYear) ? '' : ' hidden' ?>>
          <div class="ab-cards ab-cards-award">
            <?php foreach ($items as $a): $ai = $aimgu($a); ?>
              <div class="ab-card ab-award">
                <div class="ab-card-img"><?php if ($ai): ?><img src="<?= htmlspecialchars($ai) ?>" alt="" data-fallback="bg"><?php else: ?><span class="ab-ph">Sertifikat</span><?php endif; ?></div>
                <div class="org"><?= htmlspecialchars($atr($a, 'judul')) ?></div>
                <div class="ttl"><?= htmlspecialchars($atr($a, 'teks')) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <!-- Quality Standards SLIDER (horizontal carousel) -->
    <?php if ($quality): ?>
    <section class="ab-sec">
      <div class="ab-head"><h2><?= ac('about', 'quality_title', true) ?></h2><p><?= ac('about', 'quality_intro') ?></p></div>
      <div class="ab-carousel" data-carousel>
        <button class="ab-mile-arrow" data-carousel-prev aria-label="Sebelumnya"><?= $arrowL ?></button>
        <div class="ab-carousel-track" data-carousel-track>
          <?php foreach ($quality as $q): $qi = $aimgu($q); ?>
            <div class="ab-card ab-quality">
              <div class="ab-card-img"><?php if ($qi): ?><img src="<?= htmlspecialchars($qi) ?>" alt="" data-fallback="bg"><?php else: ?><span class="ab-ph"><?= htmlspecialchars($atr($q, 'judul')) ?: 'Logo' ?></span><?php endif; ?></div>
              <?php if (trim((string)$atr($q, 'judul')) !== ''): ?><div class="cap"><?= htmlspecialchars($atr($q, 'judul')) ?></div><?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
        <button class="ab-mile-arrow" data-carousel-next aria-label="Berikutnya"><?= $arrowRt ?></button>
      </div>
    </section>
    <?php endif; ?>

    <!-- Certifications SLIDER (by brand) -->
    <?php if ($certs): ?>
    <section class="ab-sec">
      <div class="ab-head"><h2><?= ac('about', 'certs_title', true) ?></h2><p><?= ac('about', 'certs_intro') ?></p></div>
      <div class="ab-slider" data-year-slider>
        <div class="ab-slider-head">
          <button class="ab-mile-arrow" data-year-prev aria-label="Brand sebelumnya"><?= $arrowL ?></button>
          <div class="ab-slider-year" data-year-label><?= htmlspecialchars((string)array_key_first($certsByBrand)) ?></div>
          <button class="ab-mile-arrow" data-year-next aria-label="Brand berikutnya"><?= $arrowRt ?></button>
        </div>
        <?php foreach ($certsByBrand as $brand => $items): ?>
        <div class="ab-slider-page" data-year-page="<?= htmlspecialchars($brand) ?>"<?= $brand === array_key_first($certsByBrand) ? '' : ' hidden' ?>>
          <div class="ab-cards ab-cards-cert">
            <?php foreach ($items as $c): $ci = $aimgu($c); ?>
              <div class="ab-card ab-cert">
                <div class="ab-card-img"><?php if ($ci): ?><img src="<?= htmlspecialchars($ci) ?>" alt="" data-fallback="bg"><?php else: ?><span class="ab-ph">Badge</span><?php endif; ?></div>
                <?php if (trim((string)$atr($c, 'judul')) !== ''): ?><div class="cap"><?= htmlspecialchars($atr($c, 'judul')) ?></div><?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

  </div>
</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
