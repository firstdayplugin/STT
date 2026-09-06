<?php
if (!isset($db)) $db = Database::getInstance();
// nav items loaded via get_menu_tree() in render block
$current_path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
// Subfolder install: strip BASE_URL's path component so menu URL comparison matches
$base_path = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
if ($base_path !== '' && $base_path !== '/' && str_starts_with($current_path, $base_path)) {
    $current_path = substr($current_path, strlen($base_path));
    if ($current_path === '' || $current_path === false) $current_path = '/';
}
$wa_number = get_setting('wa_number');
$wa_text   = get_setting('wa_text');
$wa_link   = $wa_number ? wa_url($wa_number, $wa_text) : '#';
$nav_layanan = $db->fetchAll("SELECT slug, nama FROM layanan WHERE is_active = 1 ORDER BY urutan ASC");
?>
<div class="mobile-nav-overlay" id="mobile-overlay" onclick="closeMobileNav()"></div>
<nav class="mobile-nav" id="mobile-nav">
  <button class="mobile-nav-close" onclick="closeMobileNav()" aria-label="Tutup">✕</button>
  <?php
  // Recursive hierarchical render — supports unlimited nesting
  $items_mob_d = get_menu_tree();
  $render_mob_d = function($items, $depth=0) use (&$render_mob_d, $nav_layanan) {
      foreach ($items as $menu):
          $url_raw = $menu['url_raw'] ?? $menu['url'] ?? '#';
          $has_children = !empty($menu['children']);
          $is_layanan_auto = !$has_children && ($url_raw === '/layanan' || $url_raw === '/layanan/');
          $indent_style = $depth > 0 ? 'padding-left:'.(16 + $depth*16).'px;font-size:14px;color:rgba(255,255,255,0.65)' : '';
      ?>
        <a href="<?= htmlspecialchars(menu_url($url_raw)) ?>" target="<?= htmlspecialchars($menu['target'] ?? '_self') ?>" style="<?= $indent_style ?>">
          <?= $depth > 0 ? '• ' : '' ?><?= htmlspecialchars($menu['label']) ?>
        </a>
        <?php if ($has_children): ?>
          <div style="padding-left:8px;background:rgba(255,255,255,0.03);border-radius:8px;margin:4px 0 8px">
            <?php $render_mob_d($menu['children'], $depth+1); ?>
          </div>
        <?php elseif ($is_layanan_auto && !empty($nav_layanan)): ?>
          <div style="padding-left:16px;background:rgba(255,255,255,0.03);border-radius:8px;margin:4px 0 8px">
            <?php foreach ($nav_layanan as $l): ?>
              <a href="<?= url('/layanan/'.$l['slug']) ?>" style="font-size:14px;padding:10px 0;color:rgba(255,255,255,0.65)">
                • <?= htmlspecialchars($l['nama']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php endforeach;
  };
  $render_mob_d($items_mob_d);
  ?>
  <a href="<?= $wa_link ?>" target="_blank" rel="noopener" style="background:#25D366;color:#fff;text-align:center;border-radius:30px;margin-top:24px;font-weight:700;padding:14px;border-bottom:none;display:flex;align-items:center;justify-content:center;gap:9px;box-shadow:0 4px 14px rgba(37,211,102,0.35)">
    <span style="width:20px;height:20px;display:inline-flex"><?= social_icon_svg('whatsapp') ?></span> Hubungi Kami
  </a>
</nav>
<script>
function toggleMobileNav() {
  document.getElementById('mobile-nav').classList.add('open');
  document.getElementById('mobile-overlay').classList.add('open');
}
function closeMobileNav() {
  document.getElementById('mobile-nav').classList.remove('open');
  document.getElementById('mobile-overlay').classList.remove('open');
}
</script>
