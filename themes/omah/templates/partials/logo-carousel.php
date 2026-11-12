<?php
if (!isset($db)) $db = Database::getInstance();
try { $logos = $db->fetchAll("SELECT * FROM klien_logo WHERE is_active=1 ORDER BY urutan ASC"); }
catch (Throwable $e) { return; }
if (empty($logos)) return;
?>
<section style="padding-block:40px;padding-inline:var(--pad-x);background:var(--om-white);border-top:1px solid var(--om-border);">
  <div style="max-width:var(--container-max);margin-inline:auto;">
    <div style="display:flex;gap:40px;align-items:center;flex-wrap:wrap;justify-content:center;">
      <?php foreach ($logos as $l): ?>
      <div style="opacity:.55;transition:opacity .2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='.55'">
        <?php if (!empty($l['url'])): ?><a href="<?= htmlspecialchars($l['url']) ?>" target="_blank" rel="noopener"><?php endif; ?>
        <img src="<?= uploads_url($l['logo']) ?>" alt="<?= htmlspecialchars($l['nama']??'') ?>" style="height:44px;width:auto;object-fit:contain;" loading="lazy">
        <?php if (!empty($l['url'])): ?></a><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
