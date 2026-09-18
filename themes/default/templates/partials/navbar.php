<?php
if (!isset($db)) $db = Database::getInstance();
// nav items loaded via get_menu_tree() inside render block
$current_path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
// Subfolder install: strip BASE_URL's path component so menu URL comparison matches
$base_path = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
if ($base_path !== '' && $base_path !== '/' && str_starts_with($current_path, $base_path)) {
    $current_path = substr($current_path, strlen($base_path));
    if ($current_path === '' || $current_path === false) $current_path = '/';
}
$navbar_dark = $navbar_dark ?? false;
$logo_light = get_setting('logo');
$logo_dark  = get_setting('logo_dark') ?: $logo_light;
$logo = $navbar_dark ? $logo_dark : $logo_light;
$site_name = get_setting('site_name', 'Reklamepedia');
$nav_layanan = $db->fetchAll("SELECT slug, nama FROM layanan WHERE is_active = 1 ORDER BY urutan ASC");
?>
<nav class="navbar">
  <div class="navbar-inner">
    <a href="<?= url('/') ?>" class="navbar-logo">
      <?php if ($logo): ?>
        <img src="<?= uploads_url($logo) ?>" alt="<?= htmlspecialchars($site_name) ?>">
      <?php else: ?>
        <span class="logo-r">R</span>
        <span>eklamepedia</span>
      <?php endif; ?>
    </a>

    <div class="navbar-menu">
      <?php
      // Recursive hierarchical render — supports unlimited nesting via parent_id.
      $menu_items_d = get_menu_tree();
      $render_nav_default = function($item, $level=0) use (&$render_nav_default, $current_path, $nav_layanan) {
          $url_raw = $item['url_raw'] ?? $item['url'] ?? '#';
          $u = rtrim($url_raw, '/'); $p = rtrim($current_path, '/');
          $is_active  = ($u === $p) || ($u !== '' && $u !== '/' && strpos($p, $u) === 0);
          $is_contact = (stripos($item['label'], 'kontak') !== false || stripos($item['label'], 'contact') !== false) || $url_raw === '/hubungi-kami';
          if ($is_contact && $level === 0) return;
          $has_children = !empty($item['children']);
          $is_layanan_auto = !$has_children && ($url_raw === '/layanan' || $url_raw === '/layanan/');
          
          if ($has_children || $is_layanan_auto):
      ?>
        <div class="nav-dropdown">
          <a href="<?= htmlspecialchars(menu_url($url_raw)) ?>" class="nav-dropdown-toggle <?= $is_active ? 'active' : '' ?>">
            <?= htmlspecialchars($item['label']) ?>
            <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </a>
          <div class="nav-dropdown-menu">
            <?php if ($is_layanan_auto): ?>
              <a href="<?= url('/layanan') ?>" class="nav-dropdown-item dropdown-all">Semua Layanan →</a>
              <?php foreach ($nav_layanan as $l): ?>
                <a href="<?= url('/layanan/'.$l['slug']) ?>" class="nav-dropdown-item"><?= htmlspecialchars($l['nama']) ?></a>
              <?php endforeach; ?>
            <?php else:
              $render_drop_item = function($child, $depth=1) use (&$render_drop_item) {
                  $indent = $depth > 1 ? 'padding-left:'.(16 + $depth*12).'px;font-size:13px' : '';
                  $prefix = $depth > 1 ? str_repeat('&nbsp;&nbsp;', $depth-1) . '↳ ' : '';
              ?>
                <a href="<?= htmlspecialchars(($child['url'] ?? menu_url($child['url_raw'] ?? '#'))) ?>" target="<?= htmlspecialchars($child['target'] ?? '_self') ?>" class="nav-dropdown-item" style="<?= $indent ?>">
                  <?= $prefix ?><?= htmlspecialchars($child['label']) ?>
                </a>
                <?php if (!empty($child['children'])):
                    foreach ($child['children'] as $gc) $render_drop_item($gc, $depth+1);
                endif;
              };
              foreach ($item['children'] as $child) $render_drop_item($child, 1);
            ?>
            <?php endif; ?>
          </div>
        </div>
      <?php else: ?>
        <a href="<?= htmlspecialchars(menu_url($url_raw)) ?>" target="<?= htmlspecialchars($item['target'] ?? '_self') ?>" class="<?= $is_active ? 'active' : '' ?>">
          <?= htmlspecialchars($item['label']) ?>
        </a>
      <?php endif;
      };
      foreach ($menu_items_d as $mi) $render_nav_default($mi);
      ?>
    </div>

    <a href="<?= url('/hubungi-kami') ?>" class="navbar-cta <?= strpos($current_path, '/hubungi-kami') !== false ? 'active' : '' ?>">
      Contact Us
    </a>

    <button class="navbar-toggle" onclick="toggleMobileNav()" aria-label="Menu">
      <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
  </div>
</nav>
