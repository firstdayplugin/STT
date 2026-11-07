<?php
if (!isset($db)) $db = Database::getInstance();
$seo = $seo ?? [];
$page_title = $seo['title'] ?? get_setting('meta_title_default', 'Reklamepedia');
$page_desc  = $seo['description'] ?? get_setting('meta_desc_default', '');
$page_image = $seo['image'] ?? '';
$site_name  = get_setting('site_name', 'Reklamepedia');
$favicon    = get_setting('favicon');
$accent     = get_setting('accent_color', '#E8A020');
$dark_color = get_setting('dark_color', '#252830');
$cream_color= get_setting('cream_color', '#EEEAE3');
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
  <?php if ($page_image): ?><meta property="og:image" content="<?= htmlspecialchars($page_image) ?>"><?php endif; ?>
  <meta property="og:type" content="website">
  <?php if ($google_verify): ?><meta name="google-site-verification" content="<?= htmlspecialchars($google_verify) ?>"><?php endif; ?>
  <?php if ($favicon): ?><link rel="icon" href="<?= uploads_url($favicon) ?>"><?php endif; ?>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= theme_url('assets/css/style.css') ?>?v=4">

  <style>:root {
    --accent: <?= htmlspecialchars($accent) ?>;
    --accent-hover: <?= htmlspecialchars($accent) ?>cc;
    --accent-soft:  <?= htmlspecialchars($accent) ?>20;
    --text-dark:    <?= htmlspecialchars($dark_color) ?>;
    --bg-dark:      <?= htmlspecialchars($dark_color) ?>;
    --bg-cream:     <?= htmlspecialchars($cream_color) ?>;
    --bg-cream-2:   <?= htmlspecialchars($cream_color) ?>;
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
