<?php
/**
 * Render flex_blocks + grid_icon_box for a given position.
 * Usage:
 *   $flex_position = 'home_after_hero';
 *   $current_layanan_id = $layanan_data['id'] ?? null;  // optional, for detail layanan pages
 *   include theme_path('templates/partials/flex-content.php');
 */
if (!isset($flex_position)) return;
if (!isset($db)) $db = Database::getInstance();

$pos = $flex_position;
$layanan_id_ctx = $current_layanan_id ?? null;

// For layanan_detail_* positions, filter by layanan_id (NULL = show on all, specific = only for that layanan)
$is_layanan_detail = strpos($pos, 'layanan_detail_') === 0;

try {
    if ($is_layanan_detail && $layanan_id_ctx) {
        $blocks = $db->fetchAll(
            "SELECT * FROM flex_blocks WHERE posisi = ? AND is_active = 1 
             AND (layanan_id IS NULL OR layanan_id = ?) ORDER BY urutan",
            [$pos, $layanan_id_ctx]
        );
    } else {
        $blocks = $db->fetchAll("SELECT * FROM flex_blocks WHERE posisi = ? AND is_active = 1 ORDER BY urutan", [$pos]);
    }
} catch (Throwable $e) { $blocks = []; }

foreach ($blocks as $b):
    $bg = $b['bg_color'] ?: 'transparent';
    $align = $b['align'] ?: 'center';
?>
<section class="flex-block-section" style="padding:60px 24px;text-align:<?= $align ?>;background:<?= htmlspecialchars($bg) ?>">
  <div class="container" style="max-width:900px;margin:0 auto">
    <?php if (!empty($b['judul'])): ?>
      <h2 style="font-size:clamp(24px, 3vw, 36px);font-weight:700;margin-bottom:16px;line-height:1.2">
        <?= htmlspecialchars($b['judul']) ?>
      </h2>
    <?php endif; ?>
    <div style="font-size:15px;line-height:1.7;color:var(--text-muted)">
      <?= $b['konten'] ?>
    </div>
  </div>
</section>
<?php endforeach; ?>

<?php
// Grid icon boxes
try {
    if ($is_layanan_detail && $layanan_id_ctx) {
        $grids = $db->fetchAll(
            "SELECT * FROM grid_icon_box WHERE posisi = ? AND is_active = 1 
             AND (layanan_id IS NULL OR layanan_id = ?) ORDER BY urutan",
            [$pos, $layanan_id_ctx]
        );
    } else {
        $grids = $db->fetchAll("SELECT * FROM grid_icon_box WHERE posisi = ? AND is_active = 1 ORDER BY urutan", [$pos]);
    }
} catch (Throwable $e) { $grids = []; }

foreach ($grids as $g):
    $items = $db->fetchAll("SELECT * FROM grid_icon_box_items WHERE grid_id = ? ORDER BY urutan", [$g['id']]);
    if (empty($items)) continue;
?>
<section style="padding:80px 24px;background:var(--bg-cream)">
  <div class="container" style="max-width:1080px;margin:0 auto">
    <?php if (!empty($g['judul_section'])): ?>
      <h2 style="font-size:clamp(24px, 3vw, 36px);font-weight:700;text-align:center;margin-bottom:40px;line-height:1.2">
        <?= htmlspecialchars($g['judul_section']) ?>
      </h2>
    <?php endif; ?>
    <div style="display:grid;grid-template-columns:repeat(<?= (int)$g['kolom'] ?>, 1fr);gap:16px">
      <?php foreach ($items as $it): ?>
        <?php $tag = !empty($it['link']) ? 'a' : 'div'; ?>
        <<?= $tag ?> <?= !empty($it['link']) ? 'href="' . htmlspecialchars($it['link']) . '"' : '' ?>
          style="background:white;border-radius:16px;padding:28px;text-align:left;text-decoration:none;color:inherit;transition:transform 0.2s,box-shadow 0.2s"
          onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(0,0,0,0.08)'"
          onmouseout="this.style.transform='';this.style.boxShadow=''">
          <?php if (!empty($it['icon'])): ?>
            <div style="font-size:36px;margin-bottom:14px"><?= htmlspecialchars($it['icon']) ?></div>
          <?php endif; ?>
          <h3 style="font-size:18px;font-weight:700;margin-bottom:10px;line-height:1.3"><?= htmlspecialchars($it['judul']) ?></h3>
          <?php if (!empty($it['deskripsi'])): ?>
            <p style="font-size:13px;color:var(--text-muted);line-height:1.6;margin:0"><?= htmlspecialchars($it['deskripsi']) ?></p>
          <?php endif; ?>
        </<?= $tag ?>>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endforeach; ?>

<?php unset($flex_position); ?>
