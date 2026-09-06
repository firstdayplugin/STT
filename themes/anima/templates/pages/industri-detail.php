<?php
/**
 * Anima — Industry detail (route: /industri/[slug]). Industry × Pillar matrix.
 * index.php passes $industri_data. Renders a hero + 4 pillar tabs; each tab pulls its
 * cell from `industri_pilar` (heading, rich konten, up to 4 feature icons), falling back
 * to the pillar's generic description when a cell is empty.
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$Q  = function (string $sql, array $p = []) use ($db) { try { return $db ? $db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };
$ind = $industri_data ?? [];

$pillars = $Q("SELECT * FROM solusi_pilar WHERE is_active=1 ORDER BY urutan, id");
$cells_raw = $Q("SELECT * FROM industri_pilar WHERE industri_id = ?", [(int)($ind['id'] ?? 0)]);
$cells = [];
foreach ($cells_raw as $c) { $cells[(int)$c['pilar_id']] = $c; }

$feat_icon = function ($ic) {
    $ic = trim((string)$ic);
    if ($ic === '') return icon('circle', 18);
    if (str_contains($ic, '/') || str_contains($ic, '.')) return '<img src="' . htmlspecialchars(uploads_url($ic)) . '" alt="" data-fallback="remove" style="width:20px;height:20px;object-fit:contain">';
    return icon($ic, 18);
};

$grp = 'ipk';
$hero_img = !empty($ind['hero_image']) ? uploads_url($ind['hero_image']) : (!empty($ind['gambar']) ? uploads_url($ind['gambar']) : '');
$seo = ['title' => ($ind['label'] ?? 'Industry') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($ind['intro'] ?? ''), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body"><div class="page-shell idt">

  <a class="bl-back" href="<?= url('industri') ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 6l-6 6 6 6"/></svg> Industries</a>

  <div class="idt-hero"<?= $hero_img === '' ? ' style="background:linear-gradient(135deg,' . htmlspecialchars($ind['warna1'] ?? '#0f2a54') . ',' . htmlspecialchars($ind['warna2'] ?? '#357be0') . ')"' : '' ?>>
    <?php if ($hero_img !== ''): ?><img class="idt-hero-bg" src="<?= htmlspecialchars($hero_img) ?>" alt="" data-fallback="remove"><span class="idt-hero-ov"></span><?php endif; ?>
    <div class="idt-hero-in">
      <div class="eyebrow">Industry</div>
      <h1><?= htmlspecialchars($ind['label'] ?? '') ?></h1>
      <?php if (!empty($ind['intro'])): ?><p><?= htmlspecialchars($ind['intro']) ?></p><?php endif; ?>
    </div>
  </div>

  <?php if ($pillars): ?>
  <div class="idt-tabs" role="tablist">
    <?php foreach ($pillars as $i => $p): ?>
      <button type="button" class="idt-tab<?= $i === 0 ? ' on' : '' ?>" data-tab-btn="<?= (int)$p['id'] ?>" data-tab-group="<?= $grp ?>"><?= htmlspecialchars($p['nama']) ?></button>
    <?php endforeach; ?>
  </div>

  <?php foreach ($pillars as $i => $p):
    $cell = $cells[(int)$p['id']] ?? null;
    $heading = $cell['heading'] ?? $p['nama'];
    $konten  = $cell['konten'] ?? ('<p>' . htmlspecialchars($p['deskripsi'] ?? '') . '</p>');
    $fitur = [];
    if (!empty($cell['fitur'])) { $d = json_decode($cell['fitur'], true); if (is_array($d)) $fitur = $d; }
  ?>
    <section class="idt-panel" data-tab-panel="<?= (int)$p['id'] ?>" data-tab-group="<?= $grp ?>"<?= $i === 0 ? '' : ' hidden' ?>>
      <h2><?= htmlspecialchars($heading) ?></h2>
      <div class="page-prose idt-prose"><?= $konten ?></div>
      <?php if ($fitur): ?>
      <div class="idt-feats">
        <?php foreach (array_slice($fitur, 0, 4) as $f): ?>
        <div class="idt-feat">
          <div class="idt-feat-ic"><?= $feat_icon($f['icon'] ?? '') ?></div>
          <div>
            <h4><?= htmlspecialchars($f['judul'] ?? '') ?></h4>
            <p><?= htmlspecialchars($f['teks'] ?? '') ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </section>
  <?php endforeach; ?>
  <?php endif; ?>

  <div class="sol-cta">
    <h2>Solusi untuk industri <?= htmlspecialchars($ind['label'] ?? '') ?>?</h2>
    <p>Tim kami siap membantu merancang solusi yang tepat untuk kebutuhan Anda.</p>
    <a class="btn btn-primary" href="<?= url('hubungi-kami') ?>">Hubungi Kami
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
  </div>

</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
