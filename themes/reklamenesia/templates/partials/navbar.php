<?php
/* Navbar — transparent over dark hero (home), solid on inner pages.
   $navbar_dark = true  → over hero (light text)                       */
if (!isset($db)) $db = Database::getInstance();
$menus = get_menus();
$current_path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$navbar_dark  = $navbar_dark ?? false;
$logo_light   = get_setting('logo');                          // dark-text logo (for light bg)
$logo_dark    = get_setting('logo_dark') ?: '';               // white logo (for dark bg)
$site_name    = get_setting('site_name', 'Reklamenesia');
$nav_layanan  = $db->fetchAll("SELECT slug, nama FROM layanan WHERE is_active = 1 ORDER BY urutan ASC");

// wordmark fallback: split last word for accent (reklame|nesia style)
$wm = htmlspecialchars($site_name);

$arrow_ic = '<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H8M17 7V16"/></svg>';
?>
<nav class="nav<?= $navbar_dark ? '' : ' on-light' ?>">
  <div class="nav-inner">
    <a href="<?= url('/') ?>" class="nav-logo" aria-label="<?= $wm ?>">
      <?php
        // Prefer admin-uploaded logos; otherwise use the theme's bundled brand logos.
        $img_for_dark  = $logo_dark  ? uploads_url($logo_dark)  : theme_url('assets/images/logo-white.png');
        $img_for_light = $logo_light ? uploads_url($logo_light) : theme_url('assets/images/logo-dark-text.png');
      ?>
      <img class="logo-light" src="<?= htmlspecialchars($img_for_dark) ?>" alt="<?= $wm ?>">
      <img class="logo-dark"  src="<?= htmlspecialchars($img_for_light) ?>" alt="<?= $wm ?>">
    </a>

    <div class="nav-menu">
      <?php foreach ($menus as $menu):
        $u = rtrim($menu['url'], '/'); $p = rtrim($current_path, '/');
        $is_active  = ($u === $p) || ($u !== '' && $u !== '/' && strpos($p, $u) === 0);
        $is_layanan = ($menu['url'] === '/layanan' || $menu['url'] === '/layanan/');
        $is_contact = ($menu['url'] === '/hubungi-kami' || stripos($menu['label'], 'kontak') !== false || stripos($menu['label'], 'contact') !== false);
        if ($is_contact) continue;
        if ($is_layanan && !empty($nav_layanan)):
      ?>
        <div class="nav-drop">
          <a href="<?= htmlspecialchars($menu['url']) ?>" class="<?= $is_active ? 'active' : '' ?>">
            <?= htmlspecialchars($menu['label']) ?>
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </a>
          <div class="nav-drop-menu">
            <a href="<?= url('/layanan') ?>" class="nav-drop-item all">Semua Layanan
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:auto"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
            <?php foreach ($nav_layanan as $l): ?>
              <a href="<?= url('/layanan/'.$l['slug']) ?>" class="nav-drop-item">
                <span class="nav-drop-ic"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 7.6L22 12l-7.6 2.4L12 22l-2.4-7.6L2 12l7.6-2.4z"/></svg></span>
                <?= htmlspecialchars($l['nama']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php else: ?>
        <a href="<?= htmlspecialchars($menu['url']) ?>" target="<?= htmlspecialchars($menu['target'] ?? '_self') ?>" class="<?= $is_active ? 'active' : '' ?>">
          <?= htmlspecialchars($menu['label']) ?>
        </a>
      <?php endif; endforeach; ?>
    </div>

    <div class="nav-right">
      <a href="<?= url('/hubungi-kami') ?>" class="btn <?= $navbar_dark ? 'light' : '' ?> sm">
        <span class="ic"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H8M17 7V16"/></svg></span>
        Contact
      </a>
      <button class="nav-toggle" onclick="rkOpenMenu()" aria-label="Buka menu">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
    </div>
  </div>
</nav>
