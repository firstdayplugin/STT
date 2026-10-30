<?php
if (empty($page)) { http_response_code(404); require theme_path('templates/pages/404.php'); exit; }
$seo = [
  'title'       => seo_title($page['meta_title'] ?: $page['judul']),
  'description' => $page['meta_description'] ?: '',
];
if ($page['template'] === 'blank') { echo $page['konten']; exit; }
include theme_path('templates/layouts/header.php');
?>

<section class="page-hero">
  <div class="inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><span><?= htmlspecialchars($page['judul']) ?></span></div>
    <h1><?= htmlspecialchars($page['judul']) ?></h1>
  </div>
</section>

<section class="section bg-white">
  <div class="container" style="max-width:<?= $page['template'] === 'fullwidth' ? '1100' : '800' ?>px">
    <div class="prose" data-reveal><?= $page['konten'] ?></div>
  </div>
</section>

<?php include theme_path('templates/layouts/footer.php'); ?>
