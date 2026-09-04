<?php
/* ============================================================
   OMAH THEME — flex-content.php
   FIX #7: Full support for flex_blocks AND grid_icon_box
   Renders all admin-configured blocks at given $flex_position
   Usage: $flex_position = 'home_top'; include this file;
   ============================================================ */
if (!isset($flex_position)) return;
if (!isset($db)) $db = Database::getInstance();

$pos = $flex_position;
$layanan_id_ctx = $current_layanan_id ?? null;
$is_layanan_detail = strpos($pos, 'layanan_detail_') === 0;

/* --- Flex Blocks --- */
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
  $bg    = !empty($b['bg_color']) ? $b['bg_color'] : 'transparent';
  $align = !empty($b['align'])    ? $b['align']    : 'left';
?>
<section style="padding-block:var(--sec-y);padding-inline:var(--pad-x);background:<?= htmlspecialchars($bg) ?>;text-align:<?= $align ?>;" data-reveal>
  <div style="max-width:900px;margin-inline:auto;">
    <?php if (!empty($b['judul'])): ?>
      <h2 style="font-size:clamp(22px,2.8vw,36px);font-weight:600;line-height:1.2em;text-transform:uppercase;color:var(--om-dark);margin-bottom:16px;"><?= htmlspecialchars($b['judul']) ?></h2>
    <?php endif; ?>
    <div style="font-size:16px;font-weight:300;line-height:1.6em;color:var(--om-dark);" class="prose"><?= $b['konten'] ?></div>
    <?php if (!empty($b['cta_text']) && !empty($b['cta_url'])): ?>
      <div style="margin-top:24px;">
        <a href="<?= htmlspecialchars($b['cta_url']) ?>" class="btn btn-dark">
          <?= htmlspecialchars($b['cta_text']) ?>
          <span class="btn-circle"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
        </a>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php endforeach; ?>

<?php
/* --- Grid Icon Box --- */
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
<section style="padding-block:var(--sec-y);padding-inline:var(--pad-x);background:var(--om-gray-lt);" data-reveal>
  <div style="max-width:var(--container-max);margin-inline:auto;">
    <?php if (!empty($g['judul_section'])): ?>
      <h2 style="font-size:clamp(22px,2.8vw,36px);font-weight:600;text-align:center;margin-bottom:40px;text-transform:uppercase;color:var(--om-dark);"><?= htmlspecialchars($g['judul_section']) ?></h2>
    <?php endif; ?>
    <div style="display:grid;grid-template-columns:repeat(<?= $cols ?>,1fr);gap:20px;" data-stagger>
      <?php foreach ($items as $it): $tag = !empty($it['link']) ? 'a' : 'div'; ?>
        <<?= $tag ?>
          <?= !empty($it['link']) ? 'href="'.htmlspecialchars($it['link']).'"' : '' ?>
          style="background:var(--om-white);border:1px solid var(--om-border);border-radius:16px;padding:32px 24px;text-decoration:none;color:inherit;display:flex;flex-direction:column;gap:16px;transition:transform .25s var(--ease-framer),box-shadow .25s var(--ease-framer);"
          onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 32px rgba(0,0,0,.08)'"
          onmouseout="this.style.transform='';this.style.boxShadow=''">
          <?php if (!empty($it['icon'])): ?>
            <div style="font-size:36px;line-height:1;"><?= htmlspecialchars($it['icon']) ?></div>
          <?php endif; ?>
          <div style="display:flex;flex-direction:column;gap:8px;">
            <h3 style="font-size:18px;font-weight:600;color:var(--om-dark);margin:0;"><?= htmlspecialchars($it['judul']) ?></h3>
            <?php if (!empty($it['deskripsi'])): ?>
              <p style="font-size:14px;font-weight:300;color:var(--om-gray);line-height:1.6;margin:0;"><?= htmlspecialchars($it['deskripsi']) ?></p>
            <?php endif; ?>
          </div>
        </<?= $tag ?>>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endforeach; ?>
<?php unset($flex_position); ?>
