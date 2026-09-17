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
      <?php
        // Render the five "delivering" pillars as a clean icon-card grid.
        // Source stays CMS-editable (about → intro_deliver, a <ul> of <li><strong>Title</strong> — desc</li>).
        $intro_deliver = ac('about', 'intro_deliver', true);
        $pillars = [];
        if (preg_match_all('/<li>\s*<strong>(.*?)<\/strong>\s*[—–\-:]*\s*(.*?)<\/li>/is', $intro_deliver, $mm, PREG_SET_ORDER)) {
            foreach ($mm as $x) $pillars[] = [
                trim(html_entity_decode(strip_tags($x[1]), ENT_QUOTES, 'UTF-8')),
                trim(html_entity_decode(strip_tags($x[2]), ENT_QUOTES, 'UTF-8'))];
        }
        $pillar_icon = function (string $t): string {
            $t = strtolower($t);
            if (str_contains($t, 'infrastruct')) return '<path d="M5 5h14v5H5zM5 14h14v5H5z"/><path d="M8 7.5h.01M8 16.5h.01"/>';
            if (str_contains($t, 'cyber') || str_contains($t, 'security')) return '<path d="M12 3l7 3v6c0 4-3 7-7 8-4-1-7-4-7-8V6z"/><path d="M9.5 12l1.8 1.8L15 10"/>';
            if (str_contains($t, 'data')) return '<ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v6c0 1.7 3.1 3 7 3s7-1.3 7-3V6M5 12v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"/>';
            if (str_contains($t, 'platform') || str_contains($t, 'application')) return '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>';
            return '<rect x="4" y="4" width="16" height="16" rx="3"/><path d="M9 12l2 2 4-4"/>'; // AI / default
        };
      ?>
      <?php if ($pillars): ?>
        <div class="ab-pillars">
          <?php foreach ($pillars as $p): ?>
            <div class="ab-pillar">
              <span class="ab-pillar-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?= $pillar_icon($p[0]) ?></svg></span>
              <h3><?= htmlspecialchars($p[0]) ?></h3>
              <p><?= htmlspecialchars($p[1]) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php elseif (trim(strip_tags($intro_deliver)) !== ''): ?>
        <div class="ab-deliver-wrap"><?= $intro_deliver ?></div>
      <?php endif; ?>
    </section>

    <!-- Vision + Mission (image LEFT, text RIGHT) -->
    <section class="ab-sec ab-vm">
      <div class="ab-vm-media">
        <div class="ab-vm-img ab-vm-img1 phb"><?php if ($vimg1): ?><img src="<?= htmlspecialchars($vimg1) ?>" alt="" data-fallback="bg"><?php else: ?><span class="ab-ph">Gambar 1</span><?php endif; ?></div>
        <div class="ab-vm-img ab-vm-img2 phb"><?php if ($vimg2): ?><img src="<?= htmlspecialchars($vimg2) ?>" alt="" data-fallback="bg"><?php else: ?><span class="ab-ph">Gambar 2</span><?php endif; ?></div>
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
                <div class="ab-card-img phb"><?php if ($ai): ?><img src="<?= htmlspecialchars($ai) ?>" alt="<?= htmlspecialchars($atr($a, 'judul')) ?>" loading="lazy" data-fallback="bg"><?php else: ?><span class="ab-ph">Sertifikat</span><?php endif; ?></div>
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
