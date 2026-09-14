<?php
/**
 * Anima — Services SUB-PAGE (route: /services/[slug]).
 * Two archetypes by $service_page['tipe']:
 *   capability -> areas (Consult/Deploy/Manage ...) + items + Data & AI block
 *   package    -> tiers (Gold/Platinum/Diamond) + comparison matrix (table desktop, cards mobile)
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

$seo = ['title' => ($p['judul'] ?? 'Services') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($T('body')), 0, 160)];
$anima_body_class = 'page-inner';
$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
$check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
$cell = function ($v) use ($check) {
    $v = trim((string)$v);
    if ($v === '' || $v === '-') return '<span class="sv-dash">—</span>';
    if (strcasecmp($v, 'Yes') === 0) return '<span class="sv-yes">' . $check . '</span>';
    if (strcasecmp($v, 'No') === 0)  return '<span class="sv-no">—</span>';
    return htmlspecialchars($v);
};
include theme_path('templates/layouts/header.php');
?>
<main class="page-body"><div class="page-shell sv sv-detail">

  <a class="bl-back" href="<?= url('services') ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 6l-6 6 6 6"/></svg> Services</a>

  <section class="sv-hero">
    <?php if (trim((string)$T('tagline')) !== ''): ?><div class="eyebrow"><?= htmlspecialchars($T('tagline')) ?></div><?php endif; ?>
    <h1><?= htmlspecialchars($T('headline') ?: ($p['judul'] ?? '')) ?></h1>
    <p class="lead"><?= htmlspecialchars($T('body')) ?></p>
    <?php if (!empty($p['cta_label'])): ?>
      <a class="btn btn-primary sv-cta" href="<?= htmlspecialchars(url($p['cta_target'] ?: 'contact-us')) ?>"><?= htmlspecialchars($T('cta_label')) ?> <?= $arrow ?></a>
    <?php endif; ?>
  </section>

  <?php if ($tipe === 'capability'): ?>

    <?php foreach ($areas as $a): $aid = (int)$a['id'];
      $TA = fn($f) => tr_field('service_areas', $aid, $f, $a[$f] ?? ''); $img = !empty($a['gambar']) ? uploads_url($a['gambar']) : ''; ?>
    <section class="sv-area">
      <div class="sv-area-media">
        <?php if ($img): ?><img src="<?= htmlspecialchars($img) ?>" data-fallback="bg" alt="" loading="lazy">
        <?php else: ?><div class="sv-area-ph"><span><?= htmlspecialchars($a['nama'] ?: $a['kode'] ?: '') ?></span></div><?php endif; ?>
      </div>
      <div class="sv-area-body">
        <div class="sv-area-kicker"><?php if (!empty($a['kode'])): ?><span class="sv-kode"><?= htmlspecialchars($a['kode']) ?></span><?php endif; ?><span class="sv-area-name"><?= htmlspecialchars($a['nama'] ?? '') ?></span></div>
        <h2><?= htmlspecialchars($TA('judul')) ?></h2>
        <?php if (trim((string)$TA('deskripsi')) !== ''): ?><p><?= htmlspecialchars($TA('deskripsi')) ?></p><?php endif; ?>
        <ul class="sv-caps">
          <?php foreach ($a['items'] as $it): ?>
            <li><span class="chk"><?= $check ?></span><span><?= htmlspecialchars(tr_field('service_area_items', (int)$it['id'], 'teks', $it['teks'])) ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>
    <?php endforeach; ?>

    <?php if (trim((string)$T('data_ai_title')) !== '' || $dataAiLines): ?>
    <section class="sv-dataai">
      <h2><?= htmlspecialchars($T('data_ai_title') ?: 'Data & AI') ?></h2>
      <ul class="sv-caps">
        <?php foreach ($dataAiLines as $line): ?><li><span class="chk"><?= $check ?></span><span><?= htmlspecialchars($line) ?></span></li><?php endforeach; ?>
      </ul>
    </section>
    <?php endif; ?>

  <?php else: /* ===== package ===== */ ?>

    <?php if ($tiers): ?>
    <section class="sv-sec">
      <div class="sv-tiers">
        <?php foreach ($tiers as $i => $tr): $tid = (int)$tr['id']; ?>
        <div class="sv-tier sv-tier-<?= strtolower(htmlspecialchars($tr['nama'])) ?><?= $i === 1 ? ' featured' : '' ?>">
          <div class="sv-tier-name"><?= htmlspecialchars($tr['nama']) ?></div>
          <div class="sv-tier-title"><?= htmlspecialchars(tr_field('service_tiers', $tid, 'judul', $tr['judul'] ?? '')) ?></div>
          <p><?= htmlspecialchars(tr_field('service_tiers', $tid, 'deskripsi', $tr['deskripsi'] ?? '')) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <?php if ($matrix): ?>
    <section class="sv-sec">
      <!-- Desktop: comparison table -->
      <div class="sv-matrix-table">
        <table>
          <thead><tr><th>Service Coverage</th><th>Gold</th><th>Platinum</th><th>Diamond</th></tr></thead>
          <tbody>
            <?php foreach ($mgroups as $gname => $rows): ?>
              <?php if ($gname !== '' && $gname !== 'Service Coverage'): ?><tr class="sv-mgroup"><th colspan="4"><?= htmlspecialchars($gname) ?></th></tr><?php endif; ?>
              <?php foreach ($rows as $r): ?>
              <tr>
                <td class="sv-mrow"><?= htmlspecialchars(tr_field('service_matrix', (int)$r['id'], 'baris', $r['baris'])) ?></td>
                <td><?= $cell($r['v_gold']) ?></td><td><?= $cell($r['v_platinum']) ?></td><td><?= $cell($r['v_diamond']) ?></td>
              </tr>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <!-- Mobile: one stacked card per tier -->
      <div class="sv-matrix-cards">
        <?php foreach (['v_gold'=>'Gold','v_platinum'=>'Platinum','v_diamond'=>'Diamond'] as $col => $label): ?>
        <div class="sv-mcard">
          <div class="sv-mcard-h"><?= htmlspecialchars($label) ?></div>
          <?php foreach ($mgroups as $gname => $rows): ?>
            <?php if ($gname !== '' && $gname !== 'Service Coverage'): ?><div class="sv-mcard-g"><?= htmlspecialchars($gname) ?></div><?php endif; ?>
            <?php foreach ($rows as $r): ?>
            <div class="sv-mcard-row"><span class="k"><?= htmlspecialchars(tr_field('service_matrix', (int)$r['id'], 'baris', $r['baris'])) ?></span><span class="v"><?= $cell($r[$col]) ?></span></div>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

  <?php endif; ?>

  <?php if (!empty($p['cta_label'])): ?>
  <section class="sv-ctaband">
    <a class="btn btn-primary" href="<?= htmlspecialchars(url($p['cta_target'] ?: 'contact-us')) ?>"><?= htmlspecialchars($T('cta_label')) ?> <?= $arrow ?></a>
  </section>
  <?php endif; ?>

</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
