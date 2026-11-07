<?php
$p = $page ?? [];
$seo=['title'=>($p['meta_title']??$p['judul']??'').' — '.get_setting('site_name',''),'description'=>$p['meta_description']??''];
$anima_body_class='page-inner';
include theme_path('templates/layouts/header.php'); ?>
<main class="page-body"><div class="page-shell">
  <div class="page-hero"><h1><?= htmlspecialchars($p['judul'] ?? '') ?></h1></div>
  <article class="page-prose"><?= $p['konten'] ?? '' ?></article>
</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
