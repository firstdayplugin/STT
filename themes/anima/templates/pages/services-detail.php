<?php
/**
 * Anima — Services SUB-PAGE (route: /services/[slug]).
 * Two archetypes by $service_page['tipe']:
 *   capability -> areas (Consult/Deploy/Manage ...) + items + Data & AI block
 *   package    -> tiers (Gold/Platinum/Diamond) + comparison matrix (table desktop, cards mobile)
 * Data from service_areas/items and service_tiers/matrix. Language-aware via tr_field.
 * Redesigned (.svd-*) to match the /services landing design language (centered hero,
 * premium white cards, gradient icon circles, animated reveal, gradient closing band).
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
<main class="page-body sv2"><div class="sv2-wrap svd">

  <a class="svd-back" href="<?= url('services') ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
    <?= htmlspecialchars(t('services', 'Services')) ?></a>

  <div class="sv2-hero svd-hero">
    <?php if (trim((string)$T('tagline')) !== ''): ?><div class="sv2-eyebrow"><?= htmlspecialchars($T('tagline')) ?></div><?php endif; ?>
    <h1><?= htmlspecialchars($T('headline') ?: ($p['judul'] ?? '')) ?></h1>
    <?php if (trim((string)$T('body')) !== ''): ?><p class="sv2-lead"><?= htmlspecialchars($T('body')) ?></p><?php endif; ?>
    <?php if (!empty($p['cta_label'])): ?>
      <a class="btn btn-primary sv2-cta" href="<?= htmlspecialchars(url($p['cta_target'] ?: 'contact-us')) ?>"><?= htmlspecialchars($T('cta_label')) ?> <?= $arrow ?></a>
    <?php endif; ?>
  </div>

  <?php if ($tipe === 'capability'): ?>

    <?php if ($areas): ?>
    <section class="svd-areas">
      <?php foreach ($areas as $i => $a): $aid = (int)$a['id'];
        $TA = fn($f) => tr_field('service_areas', $aid, $f, $a[$f] ?? ''); ?>
      <article class="svd-area reveal">
        <div class="svd-area-head">
          <span class="svd-area-ic"><?= icon($pick_area_icon($a, $i), 28) ?></span>
          <div class="svd-area-kick">
            <span class="svd-area-step"><?= sprintf('%02d', $i + 1) ?></span>
            <?php if (!empty($a['nama'])): ?><span class="svd-area-name"><?= htmlspecialchars($a['nama']) ?></span><?php endif; ?>
          </div>
          <h2><?= htmlspecialchars($TA('judul')) ?></h2>
          <?php if (trim((string)$TA('deskripsi')) !== ''): ?><p><?= htmlspecialchars($TA('deskripsi')) ?></p><?php endif; ?>
        </div>
        <?php if (!empty($a['items'])): ?>
        <ul class="svd-caps">
          <?php foreach ($a['items'] as $it): ?>
            <li><span class="svd-chk"><?= $check ?></span><span><?= htmlspecialchars(tr_field('service_area_items', (int)$it['id'], 'teks', $it['teks'])) ?></span></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <?php if (trim((string)$T('data_ai_title')) !== '' || $dataAiLines): ?>
    <section class="svd-dataai reveal">
      <span class="svd-dataai-ic"><?= icon('sparkles', 26) ?></span>
      <h2><?= htmlspecialchars($T('data_ai_title') ?: 'Data & AI') ?></h2>
      <ul class="svd-caps">
        <?php foreach ($dataAiLines as $line): ?><li><span class="svd-chk"><?= $check ?></span><span><?= htmlspecialchars($line) ?></span></li><?php endforeach; ?>
      </ul>
    </section>
    <?php endif; ?>

  <?php else: /* ===== package ===== */ ?>

    <?php if ($tiers): ?>
    <section class="sv2-sec">
      <div class="svd-tiers reveal">
        <?php foreach ($tiers as $i => $tr): $tid = (int)$tr['id']; $feat = ($i === 1); ?>
        <div class="svd-tier<?= $feat ? ' featured' : '' ?>">
          <?php if ($feat): ?><span class="svd-tier-flag"><?= htmlspecialchars(t('recommended', 'Recommended')) ?></span><?php endif; ?>
          <span class="svd-tier-name"><?= htmlspecialchars($tr['nama']) ?></span>
          <h3 class="svd-tier-title"><?= htmlspecialchars(tr_field('service_tiers', $tid, 'judul', $tr['judul'] ?? '')) ?></h3>
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
      $cols = ['v_gold' => $tier_labels[0] ?? 'Gold', 'v_platinum' => $tier_labels[1] ?? 'Platinum', 'v_diamond' => $tier_labels[2] ?? 'Diamond'];
    ?>
    <section class="sv2-sec">
      <div class="sv2-head"><h2><?= htmlspecialchars(t('service_coverage', 'Service Coverage')) ?></h2></div>
      <!-- Desktop: comparison table -->
      <div class="svd-matrix reveal">
        <table>
          <thead><tr><th><?= htmlspecialchars(t('service_coverage', 'Service Coverage')) ?></th><?php foreach ($cols as $lbl): ?><th><?= htmlspecialchars($lbl) ?></th><?php endforeach; ?></tr></thead>
          <tbody>
            <?php foreach ($mgroups as $gname => $rows): ?>
              <?php if ($gname !== '' && $gname !== 'Service Coverage'): ?><tr class="svd-mgroup"><th colspan="4"><?= htmlspecialchars($gname) ?></th></tr><?php endif; ?>
              <?php foreach ($rows as $r): ?>
              <tr>
                <td class="svd-mrow"><?= htmlspecialchars(tr_field('service_matrix', (int)$r['id'], 'baris', $r['baris'])) ?></td>
                <td><?= $cell($r['v_gold']) ?></td><td><?= $cell($r['v_platinum']) ?></td><td><?= $cell($r['v_diamond']) ?></td>
              </tr>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <!-- Mobile: one stacked card per tier -->
      <div class="svd-mcards">
        <?php foreach ($cols as $col => $label): ?>
        <div class="svd-mcard">
          <div class="svd-mcard-h"><?= htmlspecialchars($label) ?></div>
          <?php foreach ($mgroups as $gname => $rows): ?>
            <?php if ($gname !== '' && $gname !== 'Service Coverage'): ?><div class="svd-mcard-g"><?= htmlspecialchars($gname) ?></div><?php endif; ?>
            <?php foreach ($rows as $r): ?>
            <div class="svd-mcard-row"><span class="k"><?= htmlspecialchars(tr_field('service_matrix', (int)$r['id'], 'baris', $r['baris'])) ?></span><span class="v"><?= $cell($r[$col]) ?></span></div>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

  <?php endif; ?>

  <section class="sv2-closing">
    <h2><?= htmlspecialchars(t('services_closing', 'One Partner. End-to-End Capability.')) ?></h2>
    <a class="btn sv2-closing-btn" href="<?= htmlspecialchars(url($p['cta_target'] ?: 'contact-us')) ?>"><?= htmlspecialchars($T('cta_label') ?: t('contact_us', 'Contact Us')) ?> <?= $arrow ?></a>
  </section>

</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
