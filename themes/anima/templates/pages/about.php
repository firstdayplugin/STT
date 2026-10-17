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
// Awards are shown as ONE list sorted by year (oldest → newest), matching saptatunas.com.
usort($awards, fn($a, $b) => ((int)($a['tahun'] ?? 0)) <=> ((int)($b['tahun'] ?? 0)));
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
// About hero photo: admin-editable (Konten Halaman → Tentang Kami → "Foto Hero"),
// with a sensible existing default when nothing has been uploaded yet.
$abhero = aimg('about', 'hero_img', uploads_url('design3/towers-up.jpg'));
?>
<main class="page-body">

  <?php
  // Scale.com-style constellation hero: two parent capabilities + their service areas,
  // joined by animated connector lines. Boxes drift organically and follow the cursor;
  // hovering a card zooms the photo and its label together. Photos reuse existing site
  // assets (design3/*) — admin can swap them later. Boxes are decorative (not links).
  // [key, label, image(uploads path), isParent, left, top, size(px)]
  $abh_boxes = [
    ['infra',         'Infrastructure Services',    'design3/towers-up.jpg',      true,  '7%',  '20%', 150],
    ['consult',       'Consulting',                 'design3/team-meeting.jpg',   false, '25%', '17%', 112],
    ['deploy',        'Enterprise Deployment',      'design3/office-desks.jpg',   false, '22%', '62%', 128],
    ['managed-infra', 'Managed Infrastructure',     'design3/office-corridor.jpg',false, '6%',  '57%', 112],
    ['cyber',         'Cybersecurity Services',     'design3/monitor-dark.jpg',   true,  '80%', '19%', 150],
    ['assess',        'Assessment & Testing',       'design3/person-laptop.jpg',  false, '65%', '16%', 112],
    ['risk',          'Security Risk & Compliance', 'design3/glass-facade.jpg',   false, '62%', '62%', 120],
    ['managed-cyber', 'Managed Cybersecurity',      'design3/desk-red.jpg',       false, '79%', '57%', 130],
  ];
  // Dummy video for the dark scene card (admin can replace uploads/about/vision.mp4).
  $abhero_video = uploads_url('about/vision.mp4');
  ?>
  <div class="abh-stack">
  <section class="abh" id="abhero" aria-label="Enterprise infrastructure and security">
    <svg class="abh-links" id="abhLinks" aria-hidden="true"></svg>
    <div class="abh-copy">
      <h1>The infrastructure and security your business runs on.</h1>
      <p>Enterprises across Indonesia trust Sapta Tunas Teknologi to build, protect, and operate the systems behind their operations.</p>
    </div>
    <div class="abh-stage" id="abhStage">
      <?php foreach ($abh_boxes as $b): list($k, $label, $img, $parent, $lx, $ty, $sz) = $b; ?>
        <div class="abh-box<?= $parent ? ' parent' : '' ?>" data-k="<?= htmlspecialchars($k) ?>" style="left:<?= $lx ?>;top:<?= $ty ?>;--w:<?= (int)$sz ?>px;--h:<?= (int)$sz ?>px">
          <div class="abh-inner">
            <div class="abh-frame" style="width:var(--w);height:var(--h)"><img src="<?= htmlspecialchars(uploads_url($img)) ?>" alt="<?= htmlspecialchars($label) ?>"></div>
            <div class="abh-lbl"><span class="abh-tick"></span><?= htmlspecialchars($label) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="abh-hint" aria-hidden="true"><span>Scroll to explore</span><span class="abh-k"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M6 13l6 6 6-6"/></svg></span></div>
  </section>

  <!-- Dark "cover" scene: rises over the hero, presents OUR VISION with a video
       card, then the card shrinks + lifts and OUR MISSION appears as glass cards
       wired to the card by flowing cables. Scroll-driven motion in aboutscene.js;
       collapses to a static stack on mobile. Replaces the old flat Vision+Mission. -->
  <div class="abd-wrap" id="abdWrap">
    <div class="abd-pin">
      <div class="abd-stage">
        <canvas class="abd-net" id="abdNet" aria-hidden="true"></canvas>

        <div class="abd-v" id="abdV">
          <h2><?= ac('about', 'vision_title') ?: 'Our Vision' ?></h2>
          <p><?= ac('about', 'vision_body') ?></p>
        </div>

        <div class="abd-card" id="abdCard">
          <?php if (!empty($abhero_video)): ?>
          <video src="<?= htmlspecialchars($abhero_video) ?>" muted loop playsinline autoplay preload="auto"></video>
          <?php endif; ?>
          <canvas id="abdCardArt" aria-hidden="true"></canvas>
        </div>

        <div class="abd-miss" id="abdMiss">
          <h3><?= ac('about', 'mission_title') ?: 'Our Mission' ?></h3>
          <div class="abd-grid">
            <?php foreach ($mission as $i => $m): ?>
              <div class="abd-mcard"><span class="n"><?= sprintf('%02d', (int)$i + 1) ?></span><p><?= htmlspecialchars($atr($m, 'teks')) ?></p></div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div><!-- /.abh-stack -->
  <script src="<?= theme_asset('assets/js/abouthero.js') ?>" defer></script>
  <script src="<?= theme_asset('assets/js/aboutscene.js') ?>" defer></script>

  <div class="page-shell ab">

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
      <div class="ab-head ab-mile-head"><h2><?= ac('about', 'milestone_title', true) ?></h2><?php $mile_body = ac('about', 'milestone_body', true); if (trim(strip_tags($mile_body)) !== ''): ?><p><?= $mile_body ?></p><?php endif; ?></div>
      <div class="ab-mile" data-milestones="<?= htmlspecialchars(json_encode($mile_json, JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>">
        <div class="ab-mile-top">
          <div class="ab-mile-text" data-mile-text>
            <h3 data-mile-title></h3>
            <p data-mile-body></p>
          </div>
          <div class="ab-mile-media">
            <div class="ab-mile-frame phb"><img data-mile-img alt="" data-fallback="bg"></div>
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

    <!-- Awards — one continuous, year-sorted carousel (like saptatunas.com), scroll with arrows. -->
    <?php if ($awards): ?>
    <section class="ab-sec ab-awards-sec">
      <div class="ab-head"><h2><?= ac('about', 'awards_title') ?></h2><p><?= ac('about', 'awards_intro') ?></p></div>
      <div class="ab-rail-wrap" data-carousel>
        <button class="ab-rail-arrow ab-rail-prev" data-carousel-prev aria-label="Sebelumnya"><?= $arrowL ?></button>
        <div class="ab-rail" data-carousel-track>
          <?php foreach ($awards as $a): $ai = $aimgu($a);
            $a_brand = trim((string)$atr($a, 'teks'));       // Brand/Pemberi (e.g. Dell EMC)
            $a_year  = trim((string)($a['tahun'] ?? ''));     // Year (e.g. 2019)
          ?>
            <div class="ab-card ab-award">
              <?php if ($a_year !== ''): ?><div class="yr"><?= htmlspecialchars($a_year) ?></div><?php endif; ?>
              <div class="ab-card-img phb"><?php if ($ai): ?><img src="<?= htmlspecialchars($ai) ?>" alt="<?= htmlspecialchars($atr($a, 'judul')) ?>" loading="lazy" data-fallback="bg"><?php else: ?><span class="ab-ph">Sertifikat</span><?php endif; ?></div>
              <?php if ($a_brand !== ''): ?><div class="org"><?= htmlspecialchars($a_brand) ?></div><?php endif; ?>
              <div class="ttl"><?= htmlspecialchars($atr($a, 'judul')) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
        <button class="ab-rail-arrow ab-rail-next" data-carousel-next aria-label="Berikutnya"><?= $arrowRt ?></button>
      </div>
    </section>
    <?php endif; ?>

    <!-- Quality Standards SLIDER (horizontal carousel) -->
    <?php if ($quality): ?>
    <section class="ab-sec">
      <div class="ab-head"><h2><?= ac('about', 'quality_title', true) ?></h2><p><?= ac('about', 'quality_intro') ?></p></div>
      <div class="ab-quality-grid">
        <?php foreach ($quality as $q): $qi = $aimgu($q); ?>
          <div class="ab-card ab-quality">
            <div class="ab-card-img phb"><?php if ($qi): ?><img src="<?= htmlspecialchars($qi) ?>" alt="<?= htmlspecialchars($atr($q, 'judul')) ?>" loading="lazy" data-fallback="bg"><?php else: ?><span class="ab-ph"><?= htmlspecialchars($atr($q, 'judul')) ?: 'Logo' ?></span><?php endif; ?></div>
            <?php if (trim((string)$atr($q, 'judul')) !== ''): ?><div class="cap"><?= htmlspecialchars($atr($q, 'judul')) ?></div><?php endif; ?>
          </div>
        <?php endforeach; ?>
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
                <div class="ab-card-img phb"><?php if ($ci): ?><img src="<?= htmlspecialchars($ci) ?>" alt="<?= htmlspecialchars($atr($c, 'judul')) ?>" loading="lazy" data-fallback="bg"><?php else: ?><span class="ab-ph">Badge</span><?php endif; ?></div>
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
