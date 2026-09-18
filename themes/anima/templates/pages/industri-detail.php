<?php
/**
 * Anima — Industry detail (route: /industri/[slug]). Figma "Industries / <Name>".
 * index.php passes $industri_data. Layout: centered title + intro, full-width hero image,
 * pill tabs (pillars), a content card (heading + rich text), and a feature card (circle
 * badges + labels). Every cell (heading, text, features) is CMS-editable via industri_pilar.
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$Q  = function (string $sql, array $p = []) use ($db) { try { return $db ? $db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };
$ind = $industri_data ?? [];
$iid = (int)($ind['id'] ?? 0);

$pillars = $Q("SELECT * FROM solusi_pilar WHERE is_active=1 ORDER BY urutan, id");
$cells = [];
foreach ($Q("SELECT * FROM industri_pilar WHERE industri_id = ?", [$iid]) as $cc) { $cells[(int)$cc['pilar_id']] = $cc; }

$feat_icon = function ($ic) {
    $ic = trim((string)$ic);
    if ($ic === '') return icon('circle', 30);
    if (str_contains($ic, '/') || str_contains($ic, '.')) return '<img src="' . htmlspecialchars(uploads_url($ic)) . '" alt="" data-fallback="remove">';
    return icon($ic, 30);
};

$grp = 'ipk';
$ind_label = tr_field('industri', $iid, 'label', $ind['label'] ?? '');
$intro = tr_field('industri', $iid, 'intro', $ind['intro'] ?? '');
if (trim($intro) === '') $intro = get_content('industri', 'detail_intro', '');
$hero_img = !empty($ind['hero_image']) ? uploads_url($ind['hero_image']) : (!empty($ind['gambar']) ? uploads_url($ind['gambar']) : '');
$seo = ['title' => ($ind_label ?: 'Industry') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($intro), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body idt2">
  <div class="idt2-wrap">

    <div class="indpg-head">
      <h1><?= htmlspecialchars($ind_label) ?></h1>
      <?php if (trim($intro) !== ''): ?><p><?= $intro ?></p><?php endif; ?>
    </div>

    <?php if ($hero_img !== ''): ?>
    <div class="idt2-hero"><img src="<?= htmlspecialchars($hero_img) ?>" alt="<?= htmlspecialchars($ind_label) ?>" data-fallback="bg"></div>
    <?php endif; ?>

    <?php if ($pillars): ?>
    <!-- Pillars now read top-to-bottom (scroll). The sticky rail is a scroll-spy:
         it highlights the current pillar and lets you jump — but reading needs no clicks. -->
    <nav class="idt2-nav" id="idtNav" aria-label="<?= htmlspecialchars($ind_label) ?> — pilar solusi">
      <?php foreach ($pillars as $i => $p): ?>
        <button type="button" class="idt2-navlink<?= $i === 0 ? ' on' : '' ?>" data-spy-to="pilar-<?= (int)$p['id'] ?>"><?= htmlspecialchars(tr_field('solusi_pilar', (int)$p['id'], 'nama', $p['nama'])) ?></button>
      <?php endforeach; ?>
    </nav>

    <div class="idt2-sections">
    <?php foreach ($pillars as $i => $p):
      $cell = $cells[(int)$p['id']] ?? null;
      $pilar_nama = tr_field('solusi_pilar', (int)$p['id'], 'nama', $p['nama']);
      $pilar_desk = tr_field('solusi_pilar', (int)$p['id'], 'deskripsi', $p['deskripsi'] ?? '');
      $heading = ($cell && $cell['heading'] !== null && $cell['heading'] !== '')
               ? tr_field('industri_pilar', (int)$cell['id'], 'heading', $cell['heading']) : $pilar_nama;
      $konten  = ($cell && $cell['konten'] !== null && $cell['konten'] !== '')
               ? tr_field('industri_pilar', (int)$cell['id'], 'konten', $cell['konten']) : ('<p>' . htmlspecialchars($pilar_desk) . '</p>');
      $fitur = [];
      if (!empty($cell['fitur'])) { $d = json_decode($cell['fitur'], true); if (is_array($d)) $fitur = $d; }
      $fcols = count($fitur) <= 4 ? max(1, count($fitur)) : 6;
    ?>
      <section class="idt2-panel" id="pilar-<?= (int)$p['id'] ?>" data-spy-section="pilar-<?= (int)$p['id'] ?>">
        <div class="idt2-card">
          <div class="idt2-eyebrow"><span class="idt2-num"><?= sprintf('%02d', $i + 1) ?></span><span class="idt2-kicker"><?= htmlspecialchars($pilar_nama) ?></span></div>
          <h2><?= htmlspecialchars($heading) ?></h2>
          <div class="idt2-prose"><?= $konten ?></div>
        </div>
        <?php if ($fitur): ?>
        <div class="idt2-feats-card">
          <div class="idt2-feats" style="grid-template-columns:repeat(<?= (int)$fcols ?>,1fr)">
            <?php foreach ($fitur as $f): ?>
            <div class="idt2-feat">
              <span class="idt2-feat-ic"><?= $feat_icon($f['icon'] ?? '') ?></span>
              <span class="idt2-feat-label"><?= htmlspecialchars($f['judul'] ?? '') ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </section>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </div>
</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
