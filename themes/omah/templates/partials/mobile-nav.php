<?php
if (!isset($db)) $db = Database::getInstance();
$menus     = get_menus();
$site_name = get_setting('site_name', 'DIFA Property');
$wa_number = get_setting('wa_number');
$wa_link   = $wa_number ? wa_url($wa_number, get_setting('wa_text', 'Halo, saya ingin konsultasi properti.')) : url('/hubungi-kami');
?>
<div class="m-overlay" id="m-overlay" onclick="rkCloseMenu()"></div>
<nav class="m-nav" id="m-nav" aria-label="Menu mobile">
  <div class="m-nav-head">
    <span class="wordmark"><?= htmlspecialchars($site_name) ?></span>
    <button class="m-close" onclick="rkCloseMenu()" aria-label="Tutup">&times;</button>
  </div>
  <?php foreach ($menus as $menu): ?>
    <a class="m-link" href="<?= htmlspecialchars($menu['url']) ?>"><?= htmlspecialchars($menu['label']) ?></a>
  <?php endforeach; ?>
  <a href="<?= $wa_link ?>" target="_blank" rel="noopener"
     style="margin-top:24px;display:inline-flex;align-items:center;gap:8px;background:#000;color:#fff;padding:12px 20px;border-radius:99px;font-size:16px;font-weight:600;text-decoration:none;">
    Hubungi Kami
  </a>
</nav>
