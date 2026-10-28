<?php
/**
 * Anima — Services SUB-PAGE (route: /services/[slug]).
 * Two archetypes by $service_page['tipe']:
 *   capability -> "Capability Deck" immersive layout: dark constellation hero, a
 *                 pinned scroll deck where each service area (Consult/Deploy/Manage
 *                 ...) snaps into focus with its capabilities staggering in, a
 *                 Data & AI band, and a light finale. Motion in svcdetail.js.
 *   package    -> tiers (Gold/Platinum/Diamond) + comparison matrix (table desktop,
 *                 cards mobile), in the centered .svd-* / .sv2-* layout.
 * Data from service_areas/items and service_tiers/matrix. Language-aware via tr_field.
 */
$db  = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$p   = $service_page ?? [];
$pid = (int)($p['id'] ?? 0);
$tipe = $p['tipe'] ?? 'capability';
$T = fn(string $f) => tr_field('service_pages', $pid, $f, $p[$f] ?? '');

$areas = $tiers = $matrix = [];
try {
    if ($tipe === 'capability') {
        $areas = $db->fetchAll("SELECT * FROM service_areas WHERE page_id=? ORDER BY urutan, id", [$pid]);
        foreach ($areas as &$a) {
            $a['items'] = $db->fetchAll("SELECT * FROM service_area_items WHERE area_id=? ORDER BY urutan, id", [(int)$a['id']]);
        }
        unset($a);
    } else {
        $tiers  = $db->fetchAll("SELECT * FROM service_tiers WHERE page_id=? ORDER BY urutan, id", [$pid]);
        $matrix = $db->fetchAll("SELECT * FROM service_matrix WHERE page_id=? ORDER BY urutan, id", [$pid]);
    }
} catch (\Throwable $e) {}

// Group matrix rows by 'grup' (preserving order).
$mgroups = [];
foreach ($matrix as $row) { $mgroups[(string)($row['grup'] ?? '')][] = $row; }
$dataAiLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string)$T('data_ai_body')))));

// Pick an icon per capability area from its code/name (falls back to a rotation).
$area_icon_map = [
    'consult'=>'search','consulting'=>'search','assess'=>'search','plan'=>'search','discovery'=>'search',
    'deploy'=>'box','deployment'=>'box','build'=>'box','implement'=>'box','integration'=>'box',
    'manage'=>'settings','managed'=>'settings','operate'=>'settings','maintenance'=>'settings','support'=>'settings',
    'govern'=>'lock','protect'=>'lock','secure'=>'lock','security'=>'lock','compliance'=>'lock',
    'optimize'=>'rocket','optimise'=>'rocket','improve'=>'rocket','transform'=>'sparkles',
];
$area_icon_fallback = ['search','box','settings','lock','rocket','layers'];
$pick_area_icon = function (array $a, int $i) use ($area_icon_map, $area_icon_fallback) {
    foreach ([$a['kode'] ?? '', $a['nama'] ?? ''] as $k) {
        $k = strtolower(trim((string)$k));
        if ($k !== '' && isset($area_icon_map[$k])) return $area_icon_map[$k];
        foreach ($area_icon_map as $word => $ic) { if ($k !== '' && str_contains($k, $word)) return $ic; }
    }
    return $area_icon_fallback[$i % count($area_icon_fallback)];
};
// Hero emblem: a shield for security/cyber pages, stacked layers otherwise.
$slugl = strtolower((string)($p['slug'] ?? ''));
$hero_icon = (str_contains($slugl, 'cyber') || str_contains($slugl, 'security') || str_contains($slugl, 'soc')) ? 'lock' : 'layers';
$deck_h = max(360, count($areas) * 140);   // vh — scales with number of areas
// Package pages: an optional service flow (service_pages.extra2, "A -> B -> C").
$flow = array_values(array_filter(array_map('trim', explode('→', (string)$T('extra2')))));
$flow_icons = ['search', 'box', 'settings', 'lock', 'rocket', 'sparkles'];

$seo = ['title' => ($p['judul'] ?? 'Services') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($T('body')), 0, 160)];
$anima_body_class = 'page-inner';
$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
$check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
$cell = function ($v) use ($check) {
    $v = trim((string)$v);
    if ($v === '' || $v === '-') return '<span class="svd-dash">—</span>';
    if (strcasecmp($v, 'Yes') === 0) return '<span class="svd-yes">' . $check . '</span>';
    if (strcasecmp($v, 'No') === 0)  return '<span class="svd-dash">—</span>';
    return '<span class="svd-val">' . htmlspecialchars($v) . '</span>';
};
include theme_path('templates/layouts/header.php');
?>
<?php if ($tipe === 'capability'): /* ===== immersive capability layout ===== */ ?>
<main class="page-body csd">

  <!-- Hero: dark constellation field + glowing emblem (motion in svcdetail.js). -->
  <section class="csd-hero" id="csdHero">
    <canvas class="csd-net" id="csdNet" aria-hidden="true"></canvas>
    <a class="csd-back" href="<?= url('services') ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
      <?= htmlspecialchars(t('services', 'Services')) ?></a>
    <div class="csd-emblem-hero" aria-hidden="true">
      <span class="glow"></span><span class="ring r2"></span><span class="ring r3"></span>
      <span class="ic"><?= icon($hero_icon, 50) ?></span>
    </div>
    <?php if (trim((string)$T('tagline')) !== ''): ?><div class="csd-eye"><?= htmlspecialchars($T('tagline')) ?></div><?php endif; ?>
    <h1 class="csd-h1"><?= htmlspecialchars($T('headline') ?: ($p['judul'] ?? '')) ?></h1>
    <?php if (trim((string)$T('body')) !== ''): ?><p class="csd-lead"><?= htmlspecialchars($T('body')) ?></p><?php endif; ?>
    <?php if (!empty($p['cta_label'])): ?>
      <a class="csd-cta" href="<?= htmlspecialchars(url($p['cta_target'] ?: 'contact-us')) ?>"><?= htmlspecialchars($T('cta_label')) ?> <?= $arrow ?></a>
    <?php endif; ?>
    <div class="csd-scroll" aria-hidden="true"><span></span>Scroll</div>
  </section>

  <?php if ($areas): ?>
  <!-- Pinned area deck: each service area snaps into focus as you scroll. -->
  <section class="csd-deck" id="csdDeck" style="height:<?= (int)$deck_h ?>vh">
    <div class="csd-pin">
      <div class="csd-deck-eye"><?= htmlspecialchars(t('service_areas', 'Service Areas')) ?></div>
      <div class="csd-rail" id="csdRail">
        <?php foreach ($areas as $i => $a): ?>
          <div class="csd-seg<?= $i === 0 ? ' on' : '' ?>">
            <span class="dot"></span><span class="num"><?= sprintf('%02d', $i + 1) ?></span><span class="lbl"><?= htmlspecialchars($a['nama'] ?? '') ?></span>
          </div>
        <?php endforeach; ?>
      </div>
      <?php foreach ($areas as $i => $a): $aid = (int)$a['id'];
        $TA = fn($f) => tr_field('service_areas', $aid, $f, $a[$f] ?? '');
        $items = $a['items'] ?? []; ?>
        <div class="csd-panel<?= $i === 0 ? ' on' : '' ?>" data-p="<?= $i ?>">
          <div class="csd-txt">
            <div class="csd-kick"><span class="step"><?= sprintf('%02d', $i + 1) ?></span><?php if (!empty($a['nama'])): ?><span class="name"><?= htmlspecialchars($a['nama']) ?></span><?php endif; ?></div>
            <h2><?= htmlspecialchars($TA('judul')) ?></h2>
            <?php if (trim((string)$TA('deskripsi')) !== ''): ?><p class="csd-desc"><?= htmlspecialchars($TA('deskripsi')) ?></p><?php endif; ?>
            <?php if ($items): ?>
            <ul class="csd-caps">
              <?php foreach ($items as $k => $it): ?>
                <li style="--i:<?= (int)$k ?>"><span class="chk"><?= $check ?></span><span><?= htmlspecialchars(tr_field('service_area_items', (int)$it['id'], 'teks', $it['teks'])) ?></span></li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
          </div>
          <div class="csd-emblem" aria-hidden="true">
            <span class="halo"></span><span class="ring a"></span><span class="ring b"></span>
            <span class="core"><?= icon($pick_area_icon($a, $i), 64) ?></span>
            <span class="count"><?= count($items) ?> <?= htmlspecialchars(t('capabilities', 'capabilities')) ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if (trim((string)$T('data_ai_title')) !== '' || $dataAiLines): ?>
  <section class="csd-dataai">
    <span class="spark"><?= icon('sparkles', 30) ?></span>
    <h2><?= htmlspecialchars($T('data_ai_title') ?: 'Data & AI') ?></h2>
    <div class="csd-ailines">
      <?php foreach ($dataAiLines as $line): ?><div><span class="chk"><?= $check ?></span><span><?= htmlspecialchars($line) ?></span></div><?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="csd-finale">
    <h2><?= htmlspecialchars(t('services_closing', 'One Partner. End-to-End Capability.')) ?></h2>
    <a class="csd-fbtn" href="<?= htmlspecialchars(url($p['cta_target'] ?: 'contact-us')) ?>"><?= htmlspecialchars($T('cta_label') ?: t('contact_us', 'Contact Us')) ?> <?= $arrow ?></a>
  </section>

</main>
<script src="<?= theme_asset('assets/js/svcdetail.js') ?>" defer></script>

<?php else: /* ===== immersive package layout (tiers + comparison matrix) ===== */ ?>
<main class="page-body csd">

  <!-- Hero: dark constellation (same family as the capability pages). -->
  <section class="csd-hero" id="csdHero">
    <canvas class="csd-net" id="csdNet" aria-hidden="true"></canvas>
    <a class="csd-back" href="<?= url('services') ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
      <?= htmlspecialchars(t('services', 'Services')) ?></a>
    <div class="csd-emblem-hero" aria-hidden="true">
      <span class="glow"></span><span class="ring r2"></span><span class="ring r3"></span>
      <span class="ic"><?= icon($hero_icon, 50) ?></span>
    </div>
    <?php if (trim((string)$T('tagline')) !== ''): ?><div class="csd-eye"><?= htmlspecialchars($T('tagline')) ?></div><?php endif; ?>
    <h1 class="csd-h1"><?= htmlspecialchars($T('headline') ?: ($p['judul'] ?? '')) ?></h1>
    <?php if (trim((string)$T('body')) !== ''): ?><p class="csd-lead"><?= htmlspecialchars($T('body')) ?></p><?php endif; ?>
    <?php if (!empty($p['cta_label'])): ?>
      <a class="csd-cta" href="<?= htmlspecialchars(url($p['cta_target'] ?: 'contact-us')) ?>"><?= htmlspecialchars($T('cta_label')) ?> <?= $arrow ?></a>
    <?php endif; ?>
    <div class="csd-scroll" aria-hidden="true"><span></span>Scroll</div>
  </section>

  <?php if (count($flow) > 1): ?>
  <!-- Service flow (service_pages.extra2) — glowing nodes on a connecting line. -->
  <section class="pkg-flow">
    <div class="eye"><?= htmlspecialchars(t('service_flow', 'Service Flow')) ?></div>
    <div class="pkg-flowline">
      <?php foreach ($flow as $fi => $step): ?>
        <div class="pkg-fn"><span class="node"><?= icon($flow_icons[$fi % count($flow_icons)], 24) ?></span><span class="lbl"><?= htmlspecialchars($step) ?></span></div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($tiers): ?>
  <section class="pkg-tiers-sec">
    <div class="pkg-head"><div class="eye"><?= htmlspecialchars(t('package_positioning', 'Package Positioning')) ?></div><h2><?= htmlspecialchars(t('choose_your_plan', 'Choose Your Plan')) ?></h2></div>
    <div class="pkg-tiers">
      <?php foreach ($tiers as $i => $tr): $tid = (int)$tr['id']; $feat = ($i === 1); ?>
        <div class="pkg-tier pkg-reveal<?= $feat ? ' feat' : '' ?>">
          <?php if ($feat): ?><span class="flag"><?= htmlspecialchars(t('recommended', 'Recommended')) ?></span><?php endif; ?>
          <div class="nm"><?= htmlspecialchars($tr['nama']) ?></div>
          <h3><?= htmlspecialchars(tr_field('service_tiers', $tid, 'judul', $tr['judul'] ?? '')) ?></h3>
          <p><?= htmlspecialchars(tr_field('service_tiers', $tid, 'deskripsi', $tr['deskripsi'] ?? '')) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($matrix):
    $tier_labels = [];
    foreach ($tiers as $tr) { $tier_labels[] = $tr['nama']; }
    if (!$tier_labels) $tier_labels = ['Gold', 'Platinum', 'Diamond'];
  ?>
  <section class="pkg-cmp-sec">
    <div class="pkg-head"><div class="eye"><?= htmlspecialchars(t('compare', 'Compare')) ?></div><h2><?= htmlspecialchars(t('package_comparison', 'Package Comparison')) ?></h2></div>
    <div class="pkg-cmp">
      <div class="crow head">
        <div><?= htmlspecialchars(t('service_coverage', 'Service Coverage')) ?></div>
        <div><?= htmlspecialchars($tier_labels[0] ?? 'Gold') ?></div>
        <div class="feat"><?= htmlspecialchars($tier_labels[1] ?? 'Platinum') ?></div>
        <div><?= htmlspecialchars($tier_labels[2] ?? 'Diamond') ?></div>
      </div>
      <?php foreach ($mgroups as $gname => $rows): ?>
        <?php if ($gname !== '' && $gname !== 'Service Coverage'): ?><div class="cgroup"><?= htmlspecialchars($gname) ?></div><?php endif; ?>
        <?php foreach ($rows as $r): ?>
          <div class="crow item pkg-reveal">
            <div><?= htmlspecialchars(tr_field('service_matrix', (int)$r['id'], 'baris', $r['baris'])) ?></div>
            <div><?= $cell($r['v_gold']) ?></div><div class="feat"><?= $cell($r['v_platinum']) ?></div><div><?= $cell($r['v_diamond']) ?></div>
          </div>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="csd-finale">
    <h2><?= htmlspecialchars(t('services_closing', 'One Partner. End-to-End Capability.')) ?></h2>
    <a class="csd-fbtn" href="<?= htmlspecialchars(url($p['cta_target'] ?: 'contact-us')) ?>"><?= htmlspecialchars($T('cta_label') ?: t('contact_us', 'Contact Us')) ?> <?= $arrow ?></a>
  </section>

</main>
<script src="<?= theme_asset('assets/js/svcdetail.js') ?>" defer></script>
<?php endif; ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
