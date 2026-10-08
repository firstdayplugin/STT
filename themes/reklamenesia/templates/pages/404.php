<?php
$seo = ['title' => '404 — Halaman Tidak Ditemukan', 'description' => ''];
include theme_path('templates/layouts/header.php');
?>

<section class="page-hero" style="min-height:80vh;display:flex;align-items:center;justify-content:center">
  <div class="inner" data-reveal>
    <div class="crumb" style="justify-content:center"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><span>404</span></div>
    <h1 class="display" style="font-size:clamp(80px,16vw,200px);color:var(--rk-red)">404</h1>
    <p>Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
    <div style="margin-top:30px;display:flex;justify-content:center">
      <a href="<?= url('/') ?>" class="btn red"><span class="ic"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>Kembali ke Beranda</a>
    </div>
  </div>
</section>

<?php include theme_path('templates/layouts/footer.php'); ?>
