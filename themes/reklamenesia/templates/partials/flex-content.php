<?php
/* Renders admin flex_blocks + grid_icon_box for a given $flex_position.
   Usage: $flex_position = 'home_after_hero'; [$current_layanan_id = ...]
          include theme_path('templates/partials/flex-content.php');     */
if (!isset($flex_position)) return;
if (!isset($db)) $db = Database::getInstance();

$pos = $flex_position;
$layanan_id_ctx = $current_layanan_id ?? null;
$is_layanan_detail = strpos($pos, 'layanan_detail_') === 0;

try {
    if ($is_layanan_detail && $layanan_id_ctx) {
        $blocks = $db->fetchAll(
            "SELECT * FROM flex_blocks WHERE posisi = ? AND is_active = 1
             AND (layanan_id IS NULL OR layanan_id = ?) ORDER BY urutan", [$pos, $layanan_id_ctx]);
    } else {
        $blocks = $db->fetchAll("SELECT * FROM flex_blocks WHERE posisi = ? AND is_active = 1 ORDER BY urutan", [$pos]);
    }
} catch (Throwable $e) { $blocks = []; }

foreach ($blocks as $b):
    $bg = $b['bg_color'] ?: 'transparent';
    $align = $b['align'] ?: 'center';
?>
<section class="section tight" style="text-align:<?= $align ?>;background:<?= htmlspecialchars($bg) ?>" data-reveal>
  <div class="container" style="max-width:900px">
    <?php if (!empty($b['judul'])): ?>
      <h2 style="font-size:var(--fs-h2);margin-bottom:16px"><?= htmlspecialchars($b['judul']) ?></h2>
    <?php endif; ?>
    <div class="prose" style="color:var(--rk-text-soft)"><?= $b['konten'] ?></div>
  </div>
</section>
<?php endforeach; ?>

<?php
try {
    if ($is_layanan_detail && $layanan_id_ctx) {
        $grids = $db->fetchAll(
            "SELECT * FROM grid_icon_box WHERE posisi = ? AND is_active = 1
             AND (layanan_id IS NULL OR layanan_id = ?) ORDER BY urutan", [$pos, $layanan_id_ctx]);
    } else {
        $grids = $db->fetchAll("SELECT * FROM grid_icon_box WHERE posisi = ? AND is_active = 1 ORDER BY urutan", [$pos]);
    }
} catch (Throwable $e) { $grids = []; }

foreach ($grids as $g):
    try { $items = $db->fetchAll("SELECT * FROM grid_icon_box_items WHERE grid_id = ? ORDER BY urutan", [$g['id']]); }
    catch (Throwable $e) { $items = []; }
    if (empty($items)) continue;
    $cols = max(1, (int)($g['kolom'] ?? 3));
?>
<section class="section bg-cream" data-reveal>
  <div class="container" style="max-width:1080px">
    <?php if (!empty($g['judul_section'])): ?>
      <h2 style="font-size:var(--fs-h2);text-align:center;margin-bottom:40px"><?= htmlspecialchars($g['judul_section']) ?></h2>
    <?php endif; ?>
    <div style="display:grid;grid-template-columns:repeat(<?= $cols ?>,1fr);gap:16px" data-stagger>
      <?php foreach ($items as $it): $tag = !empty($it['link']) ? 'a' : 'div'; ?>
        <<?= $tag ?> <?= !empty($it['link']) ? 'href="'.htmlspecialchars($it['link']).'"' : '' ?>
          style="background:#fff;border:1px solid var(--rk-line);border-radius:var(--r-lg);padding:28px;text-decoration:none;color:inherit;transition:transform .3s var(--ease),box-shadow .3s var(--ease)"
          onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='var(--shadow-md)'"
          onmouseout="this.style.transform='';this.style.boxShadow=''">
          <?php if (!empty($it['icon'])): ?><div style="font-size:34px;margin-bottom:14px"><?= htmlspecialchars($it['icon']) ?></div><?php endif; ?>
          <h3 style="font-size:18px;font-weight:600;margin-bottom:10px"><?= htmlspecialchars($it['judul']) ?></h3>
          <?php if (!empty($it['deskripsi'])): ?><p style="font-size:14px;color:var(--rk-text-mute);line-height:1.6"><?= htmlspecialchars($it['deskripsi']) ?></p><?php endif; ?>
        </<?= $tag ?>>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endforeach; ?>
<?php unset($flex_position); ?>
