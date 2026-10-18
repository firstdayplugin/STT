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

  <?php
  // Our Milestone — official validated timeline (exact copy of the client's data).
  // Horizontal journey: scroll travels sideways through the years. Photos are
  // placeholders (design3/*), swappable here. [year, items[], image(uploads path)]
  $mile_data = [
    ['2015', ['Established on May 8th, 2015'], 'design3/office-plant.jpg'],
    ['2016', ['Trusted Solution Provider Dell Technologies', 'Solution Provider Professional of VMware by Broadcom'], 'design3/office-desks.jpg'],
    ['2017', ['Dell Technologies Platinum Partner', 'Partnership with Microsoft'], 'design3/team-meeting.jpg'],
    ['2018', ['Dell Technologies Titanium Partner', 'Advanced VMware by Broadcom'], 'design3/glass-facade.jpg'],
    ['2019', ['Partnership with Veeam', 'Partnership with Commvault'], 'design3/office-open.jpg'],
    ['2020', ['Partnership with Cisco', 'Partnership with Fortinet'], 'design3/monitor-dark.jpg'],
    ['2021', ['Partnership with RedHat'], 'design3/desk-window.jpg'],
    ['2022', ['Partnership with NVIDIA', 'Partnership with Weka'], 'design3/ai-phone.jpg'],
    ['2023', ['Partnership with Sangfor', 'Partnership with Nutanix', 'Partnership with HYCU'], 'design3/office-corridor.jpg'],
    ['2024', ['Dell Advanced Performing Services', 'Partnership with Elastic', 'Partnership with Cyble', 'Partnership with Rafay'], 'design3/team-work.jpg'],
    ['2025', ['Partnership with infraon', 'Partnership with SenseTime', 'Partnership with Xeratic', 'Partner with Soca'], 'design3/imac-design.jpg'],
    ['Present', ['Partnership with AMD', 'Partnership with T-Innoware', 'Partnership with Redis'], 'design3/towers-up.jpg'],
  ];
  $mile_n = count($mile_data);
  ?>
  <section class="mj-wrap" id="mjWrap" aria-label="Our Milestone">
    <div class="mj-pin">
      <div class="mj-head">
        <div class="mj-eyebrow">Our Journey</div>
        <h2>Our <span class="b">Milestone</span></h2>
      </div>
      <div class="mj-stage"><div class="mj-track" id="mjTrack">
        <?php foreach ($mile_data as $m): list($yr, $items, $img) = $m; ?>
          <div class="mj-slide">
            <div class="mj-txt">
              <div class="mj-year"><?= htmlspecialchars($yr) ?></div>
              <?php if (count($items) > 1): ?>
                <ul class="mj-list"><?php foreach ($items as $it): ?><li><?= htmlspecialchars($it) ?></li><?php endforeach; ?></ul>
              <?php else: ?>
                <div class="mj-single"><?= htmlspecialchars($items[0] ?? '') ?></div>
              <?php endif; ?>
            </div>
            <div class="mj-media"><img src="<?= htmlspecialchars(uploads_url($img)) ?>" alt=""></div>
          </div>
        <?php endforeach; ?>
      </div></div>
      <div class="mj-rail" id="mjRail">
        <div class="mj-line"></div><div class="mj-fill" id="mjFill"></div><div class="mj-head-dot" id="mjDot"></div>
        <?php foreach ($mile_data as $i => $m): ?>
          <div class="mj-yr" style="left:<?= $mile_n > 1 ? (($i / ($mile_n - 1)) * 100) : 0 ?>%"><b></b><?= htmlspecialchars($m[0]) ?></div>
        <?php endforeach; ?>
      </div>
      <div class="mj-hint" aria-hidden="true">scroll to travel through the years &rarr;</div>
    </div>
  </section>
  <script src="<?= theme_asset('assets/js/aboutmile.js') ?>" defer></script>

  <!-- Our Value — ICARE constellation (dark, scroll-scrubbed). The ICARE letters
       (wireframe + corner ticks) stay readable; scrolling wires each value card in
       one-by-one via a drawing cable, with the active letter lit. Motion in
       aboutvalue.js; collapses to a static stack on mobile. CMS: about_items 'value'. -->
  <section class="icv-wrap" id="icvWrap" aria-label="Our Value — ICARE">
    <div class="icv-pin">
      <div class="icv-inner">
        <canvas class="icv-net" id="icvNet" aria-hidden="true"></canvas>
        <div class="icv-head">
          <h2><?= ac('about', 'values_eyebrow') ?: 'Our' ?> <span class="hl"><?= ac('about', 'values_title') ?: 'Value' ?></span></h2>
        </div>
        <div class="icv-letters" id="icvLetters">
          <?php foreach ($values as $v): ?>
            <div class="icv-ltr" data-k="<?= htmlspecialchars($v['kode'] ?? '') ?>"><i class="tl"></i><i class="br"></i><?= htmlspecialchars($v['kode'] ?? '') ?><span class="cap"><?= htmlspecialchars($atr($v, 'judul')) ?></span></div>
          <?php endforeach; ?>
        </div>
        <div class="icv-cards" id="icvCards">
          <?php foreach ($values as $v): ?>
            <div class="icv-card"><span class="lb"><?= htmlspecialchars($v['kode'] ?? '') ?></span><div class="t"><?= htmlspecialchars(mb_convert_case($atr($v, 'judul'), MB_CASE_TITLE, 'UTF-8')) ?></div><div class="d"><?= htmlspecialchars($atr($v, 'teks')) ?></div></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
  <script src="<?= theme_asset('assets/js/aboutvalue.js') ?>" defer></script>

  <!-- Awards — "3D Spotlight Coverflow" (light, scroll-scrubbed). Trophies rotate
       through a central spotlight; the centred card is big & clear with its year,
       and the org + title read below. Motion in aboutawards.js; static grid on
       mobile. CMS: about_items 'award' (tahun / teks=org / judul / gambar). -->
  <?php if ($awards): $awn = count($awards); ?>
  <section class="aw-wrap" id="awWrap" aria-label="Awards" style="height:<?= max(480, ($awn + 1) * 86) ?>vh">
    <div class="aw-pin">
      <div class="aw-glow" aria-hidden="true"></div>
      <div class="aw-head">
        <div class="aw-eyebrow">Recognition</div>
        <h2 class="aw-title"><?= ac('about', 'awards_title') ?: 'Awards' ?></h2>
        <p><?= ac('about', 'awards_intro') ?></p>
      </div>
      <div class="aw-stage" id="awStage">
        <?php foreach ($awards as $a): $ai = $aimgu($a);
          $a_org = trim((string)$atr($a, 'teks'));
          $a_ttl = trim((string)$atr($a, 'judul'));
          $a_year = trim((string)($a['tahun'] ?? ''));
        ?>
          <div class="aw-card" data-org="<?= htmlspecialchars($a_org) ?>" data-ttl="<?= htmlspecialchars($a_ttl) ?>">
            <?php if ($a_year !== ''): ?><span class="yr"><?= htmlspecialchars($a_year) ?></span><?php endif; ?>
            <div class="ph"><?php if ($ai): ?><img src="<?= htmlspecialchars($ai) ?>" alt="<?= htmlspecialchars($a_ttl) ?>" loading="lazy" data-fallback="bg"><?php else: ?><span class="ab-ph">Sertifikat</span><?php endif; ?></div>
            <div class="cap"><?php if ($a_org !== ''): ?><span class="co"><?= htmlspecialchars($a_org) ?></span><?php endif; ?><?= htmlspecialchars($a_ttl) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="aw-info" id="awInfo"><div class="org"></div><div class="ttl"></div></div>
      <div class="aw-count" aria-hidden="true"><b><?= $awn ?>+</b><span>awards &amp;<br>recognitions</span></div>
    </div>
  </section>
  <script src="<?= theme_asset('assets/js/aboutawards.js') ?>" defer></script>
  <?php endif; ?>

  <!-- Our Quality Standards — "Trust Seals" (dark band). International standard
       marks become framed plaques on a dark wall; each reveals with a light sweep
       as the band enters view, tied by a glowing trust rail. Motion in
       aboutquality.js; static stack on mobile. CMS: about_items 'quality'. -->
  <?php if ($quality): $qn = count($quality); ?>
  <section class="qs" id="qsSec" aria-label="Our Quality Standards">
    <canvas class="qs-bg" id="qsBg" aria-hidden="true"></canvas>
    <div class="qs-head">
      <div class="qs-eyebrow">Accredited &amp; Certified</div>
      <h2><?= ac('about', 'quality_title', true) ?></h2>
      <p><?= ac('about', 'quality_intro') ?></p>
    </div>
    <div class="qs-rail-wrap">
      <div class="qs-rail" aria-hidden="true"><i id="qsFill"></i></div>
      <div class="qs-grid" id="qsGrid" style="--n:<?= min(3, max(1, $qn)) ?>">
        <?php foreach ($quality as $q): $qi = $aimgu($q);
          $q_ttl = trim((string)$atr($q, 'judul'));
          $q_sub = trim((string)$atr($q, 'teks'));
        ?>
          <div class="qs-card">
            <span class="qs-node" aria-hidden="true"></span>
            <div class="qs-frame"><?php if ($qi): ?><img src="<?= htmlspecialchars($qi) ?>" alt="<?= htmlspecialchars($q_ttl) ?>" loading="lazy" data-fallback="bg"><?php else: ?><span class="ab-ph"><?= htmlspecialchars($q_ttl) ?: 'Logo' ?></span><?php endif; ?></div>
            <?php if ($q_ttl !== '' || $q_sub !== ''): ?>
            <div class="qs-meta"><?php if ($q_ttl !== ''): ?><div class="t"><?= htmlspecialchars($q_ttl) ?></div><?php endif; ?><?php if ($q_sub !== ''): ?><div class="s"><?= htmlspecialchars($q_sub) ?></div><?php endif; ?></div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="qs-foot">Independently audited &amp; internationally accredited</div>
  </section>
  <script src="<?= theme_asset('assets/js/aboutquality.js') ?>" defer></script>
  <?php endif; ?>

  <!-- List of Certification — "Brand Credential Wall" (light, pinned & scroll-
       scrubbed). Vertical scroll walks through vendor categories; each category's
       badges light up one-by-one into a wall, the active category index highlights,
       and the tabs are clickable. Motion in aboutcert.js; static per-category grid
       on mobile. CMS: about_items 'cert' (grup = category). -->
  <?php if ($certs): $cwn = count($certsByBrand); $cwi = 0; ?>
  <section class="cw-wrap" id="cwWrap" aria-label="List of Certification" style="height:<?= max(320, $cwn * 115) ?>vh">
    <div class="cw-pin">
      <div class="cw-head">
        <div class="cw-eyebrow">Proven Professional</div>
        <h2><?= ac('about', 'certs_title', true) ?></h2>
        <p><?= ac('about', 'certs_intro') ?></p>
      </div>
      <div class="cw-body">
        <aside class="cw-side">
          <div class="cw-catwrap">
            <?php $cwi = 0; foreach ($certsByBrand as $brand => $items): ?>
              <div class="cw-cat<?= $cwi === 0 ? ' on' : '' ?>"><div class="n"><?= htmlspecialchars($brand) ?></div><div class="c"><b><?= count($items) ?></b>certifications</div></div>
            <?php $cwi++; endforeach; ?>
          </div>
          <ol class="cw-nav" id="cwNav">
            <?php $cwi = 0; foreach ($certsByBrand as $brand => $items): ?>
              <li class="<?= $cwi === 0 ? 'on' : '' ?>" data-i="<?= $cwi ?>"><?= htmlspecialchars($brand) ?></li>
            <?php $cwi++; endforeach; ?>
          </ol>
        </aside>
        <div class="cw-wall" id="cwWall">
          <?php $cwi = 0; foreach ($certsByBrand as $brand => $items): ?>
            <div class="cw-layer<?= $cwi === 0 ? ' on' : '' ?><?= count($items) <= 3 ? ' few' : '' ?>">
              <div class="cw-mcat"><?= htmlspecialchars($brand) ?></div>
              <?php foreach ($items as $c): $ci = $aimgu($c); $c_cap = trim((string)$atr($c, 'judul')); ?>
                <div class="cw-badge">
                  <div class="bx"><?php if ($ci): ?><img src="<?= htmlspecialchars($ci) ?>" alt="<?= htmlspecialchars($c_cap) ?>" loading="lazy" data-fallback="bg"><?php else: ?><span class="ab-ph">Badge</span><?php endif; ?></div>
                  <?php if ($c_cap !== ''): ?><div class="cap"><?= htmlspecialchars($c_cap) ?></div><?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php $cwi++; endforeach; ?>
        </div>
      </div>
    </div>
  </section>
  <script src="<?= theme_asset('assets/js/aboutcert.js') ?>" defer></script>
  <?php endif; ?>

</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
