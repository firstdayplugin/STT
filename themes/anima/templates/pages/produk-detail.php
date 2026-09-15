<?php $pr=$produk ?? []; $seo=['title'=>($pr['nama']??'Produk').' — '.get_setting('site_name','')]; $anima_body_class='page-inner';
include theme_path('templates/layouts/header.php'); ?>
<main class="page-body"><div class="page-shell">
  <div class="page-hero"><h1><?= htmlspecialchars($pr['nama'] ?? 'Produk') ?></h1></div>
  <article class="page-prose"><?= $pr['deskripsi'] ?? '' ?></article>
</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
