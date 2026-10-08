<?php
/**
 * Anima — shared Career filter sidebar (Job Role + Location) as checkbox-style links.
 * Self-contained. Highlights the active role/location; clicking an active one clears it.
 */
$__db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$__Q  = function (string $sql) use ($__db) { try { return $__db ? $__db->fetchAll($sql) : []; } catch (\Throwable $e) { return []; } };
$f_role = $career_active_role ?? (isset($_GET['role']) ? trim((string)$_GET['role']) : '');
$f_loc  = $career_active_loc  ?? (isset($_GET['lokasi']) ? trim((string)$_GET['lokasi']) : '');
$f_q    = $career_q ?? (isset($_GET['q']) ? trim((string)$_GET['q']) : '');
$f_roles = $__Q("SELECT role, COUNT(*) c FROM career WHERE is_active=1 AND role IS NOT NULL AND role<>'' GROUP BY role ORDER BY role");
$f_locs  = $__Q("SELECT lokasi, COUNT(*) c FROM career WHERE is_active=1 AND lokasi IS NOT NULL AND lokasi<>'' GROUP BY lokasi ORDER BY lokasi");
$f_link = function (array $ov) use ($f_role, $f_loc, $f_q) {
    $p = array_filter(['role'=>$f_role, 'lokasi'=>$f_loc, 'q'=>$f_q], fn($v)=>$v!=='');
    $p = array_merge($p, $ov);
    $p = array_filter($p, fn($v)=>$v!=='' && $v!==null);
    return url('career' . ($p ? '?' . http_build_query($p) : ''));
};
$f_box = '<svg class="crf-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>';
?>
<aside class="cr-side">
  <div class="cr-side-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M7 12h10M10 18h4"/></svg></div>

  <div class="crf-group">
    <h4><?= htmlspecialchars(t('job_role', 'Job Role')) ?></h4>
    <?php foreach ($f_roles as $r): $on = $f_role === $r['role']; ?>
      <a class="crf-item<?= $on ? ' on' : '' ?>" href="<?= htmlspecialchars($f_link(['role' => $on ? '' : $r['role']])) ?>">
        <span class="crf-box"><?= $on ? $f_box : '' ?></span>
        <span class="crf-lbl"><?= htmlspecialchars($r['role']) ?> <span class="crf-n">(<?= (int)$r['c'] ?>)</span></span>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="crf-group">
    <h4><?= htmlspecialchars(t('location', 'Location')) ?></h4>
    <?php foreach ($f_locs as $l): $on = $f_loc === $l['lokasi']; ?>
      <a class="crf-item<?= $on ? ' on' : '' ?>" href="<?= htmlspecialchars($f_link(['lokasi' => $on ? '' : $l['lokasi']])) ?>">
        <span class="crf-box"><?= $on ? $f_box : '' ?></span>
        <span class="crf-lbl"><?= htmlspecialchars($l['lokasi']) ?> <span class="crf-n">(<?= (int)$l['c'] ?>)</span></span>
      </a>
    <?php endforeach; ?>
  </div>
</aside>
