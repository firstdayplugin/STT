<?php $seo=['title'=>'404 — '.get_setting('site_name','Sapta Tunas Teknologi')]; $anima_body_class='page-inner';
include theme_path('templates/layouts/header.php'); ?>
<main class="page-body"><div class="page-shell"><div class="page-hero">
  <div class="eyebrow">Error 404</div><h1>Halaman tidak ditemukan</h1>
  <p>Maaf, halaman yang Anda cari tidak tersedia.</p>
  <p><a class="btn btn-primary" href="<?= url('') ?>">Kembali ke Beranda</a></p>
</div></div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
