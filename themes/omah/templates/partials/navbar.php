<?php
/* OMAH THEME — Navbar
   ALWAYS visible: solid white background with dark text.
   On homepage only: once user scrolls past the white area into
   the dark hero card, nav becomes transparent with white text.
   All other pages: permanently solid. */
if (!isset($db)) $db = Database::getInstance();
$menus      = get_menus();
$current    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$is_home    = $navbar_dark ?? false;
$logo_light = get_setting('logo');       // dark-text logo (for white bg)
$logo_dark  = get_setting('logo_dark');  // white logo (for dark bg)
$site_name  = get_setting('site_name', 'DIFA Property');

$img_light = $logo_light ? uploads_url($logo_light) : theme_url('assets/images/logo-dark-text.png');
$img_dark  = $logo_dark  ? uploads_url($logo_dark)  : theme_url('assets/images/logo-white.png');

$arrow_ic = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7M17 7H8M17 7V16"/></svg>';
?>
<nav class="nav nav-solid" id="om-nav" data-is-home="<?= $is_home ? '1' : '0' ?>">
  <div class="nav-inner">

    <!-- Logo: dark-text version by default, swaps to white when in hero -->
    <a href="<?= url('/') ?>" class="nav-logo" aria-label="<?= htmlspecialchars($site_name) ?>">
      <img class="logo-dark"  src="<?= htmlspecialchars($img_light) ?>" alt="<?= htmlspecialchars($site_name) ?>">
      <img class="logo-light" src="<?= htmlspecialchars($img_dark) ?>"  alt="<?= htmlspecialchars($site_name) ?>" style="display:none;">
    </a>

    <!-- Menu links -->
    <div class="nav-menu">
      <?php foreach ($menus as $menu):
        $u = rtrim($menu['url'],'/'); $p = rtrim($current,'/');
        $active = ($u === $p) || ($u !== '' && $u !== '/' && strpos($p,$u) === 0);
        $skip = ($menu['url'] === '/hubungi-kami')
             || stripos($menu['label'],'hubungi') !== false
             || stripos($menu['label'],'kontak')  !== false;
        if ($skip) continue;
      ?>
        <a href="<?= htmlspecialchars($menu['url']) ?>"
           target="<?= htmlspecialchars($menu['target'] ?? '_self') ?>"
           class="<?= $active ? 'active' : '' ?>">
          <?= htmlspecialchars($menu['label']) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- CTA + hamburger -->
    <div class="nav-cta">
      <a href="<?= url('/hubungi-kami') ?>" class="btn-nav" aria-label="Hubungi Kami">
        Hubungi Kami
        <span class="arrow-circle"><?= $arrow_ic ?></span>
      </a>
      <button class="nav-toggle" onclick="rkOpenMenu()" aria-label="Buka menu">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
        </svg>
      </button>
    </div>
  </div>
</nav>
