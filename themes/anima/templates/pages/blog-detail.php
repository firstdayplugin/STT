<?php $b=$blog_data ?? []; $seo=['title'=>($b['judul']??'Artikel').' — '.get_setting('site_name','')]; $anima_body_class='page-inner';
include theme_path('templates/layouts/header.php'); ?>
<main class="page-body"><div class="page-shell">
  <div class="page-hero"><h1><?= htmlspecialchars($b['judul'] ?? 'Artikel') ?></h1></div>
  <article class="page-prose"><?= $b['konten'] ?? '' ?></article>
</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
