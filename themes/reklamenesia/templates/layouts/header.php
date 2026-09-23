<?php
/* ============================================================
   REKLAMENESIA THEME — header / document head
   Honors existing CMS settings contract. Defaults to Framer red.
   ============================================================ */
if (!isset($db)) $db = Database::getInstance();
$seo        = $seo ?? [];
$page_title = $seo['title']       ?? get_setting('meta_title_default', get_setting('site_name', 'Reklamenesia'));
$page_desc  = $seo['description'] ?? get_setting('meta_desc_default', 'One-stop solution untuk keperluan signage Anda!');
$page_image = $seo['image']       ?? '';
$site_name  = get_setting('site_name', 'Reklamenesia');
$favicon    = get_setting('favicon');

/* --- Brand color resolution ---
   Use admin accent if it has been deliberately set; otherwise default to
   the Framer red so the theme matches the source out of the box.        */
$accent_raw = strtolower(trim(get_setting('accent_color', '')));
$amber_defaults = ['', '#e8a020', 'e8a020']; // legacy default = ignore → use red
$rk_red   = in_array($accent_raw, $amber_defaults, true) ? '#C21F1F' : get_setting('accent_color');
$rk_ink   = get_setting('dark_color', '#0C0C0C');
$rk_cream = get_setting('cream_color', '#FFF5EC');

$custom_css = get_setting('custom_css', '');
$head_script= get_setting('custom_head_script', '');
$body_script= get_setting('custom_body_script', '');
$ga_id      = get_setting('ga_id', '');
$gtm_id     = get_setting('gtm_id', '');
$meta_pixel = get_setting('meta_pixel_id', '');
$google_verify = get_setting('google_verification', '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<script>document.documentElement.classList.add('js');</script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
  <?php if ($page_image): ?><meta property="og:image" content="<?= htmlspecialchars($page_image) ?>"><?php endif; ?>
  <meta property="og:type" content="website">
  <meta name="theme-color" content="<?= htmlspecialchars($rk_red) ?>">
  <?php if ($google_verify): ?><meta name="google-site-verification" content="<?= htmlspecialchars($google_verify) ?>"><?php endif; ?>
  <link rel="icon" href="<?= $favicon ? uploads_url($favicon) : theme_url('assets/images/favicon.png') ?>" type="image/png">
  <link rel="apple-touch-icon" href="<?= $favicon ? uploads_url($favicon) : theme_url('assets/images/favicon.png') ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= theme_url('assets/css/style.css') ?>?v=1.7">

  <style>:root{
    --rk-red: <?= htmlspecialchars($rk_red) ?>;
    --rk-ink: <?= htmlspecialchars($rk_ink) ?>;
    --rk-cream: <?= htmlspecialchars($rk_cream) ?>;
    --accent: <?= htmlspecialchars($rk_red) ?>;
    --text-dark: <?= htmlspecialchars($rk_ink) ?>;
    --bg-dark: <?= htmlspecialchars($rk_ink) ?>;
    --bg-cream: <?= htmlspecialchars($rk_cream) ?>;
  }</style>
  <?php if ($custom_css): ?><style><?= $custom_css ?></style><?php endif; ?>

  <?php if ($ga_id): ?>
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($ga_id) ?>"></script>
  <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= htmlspecialchars($ga_id) ?>');</script>
  <?php endif; ?>
  <?php if ($gtm_id): ?>
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?= htmlspecialchars($gtm_id) ?>');</script>
  <?php endif; ?>
  <?php if ($meta_pixel): ?>
  <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','<?= htmlspecialchars($meta_pixel) ?>');fbq('track','PageView');</script>
  <?php endif; ?>

  <?= $head_script ?>
</head>
<body>
<?php if ($gtm_id): ?>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= htmlspecialchars($gtm_id) ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php endif; ?>
<?= $body_script ?>

<?php include theme_path('templates/partials/mobile-nav.php'); ?>
<?php include theme_path('templates/partials/navbar.php'); ?>
