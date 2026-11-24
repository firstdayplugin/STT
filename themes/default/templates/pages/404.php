<?php
$seo = ['title' => '404 - Halaman Tidak Ditemukan', 'description' => ''];
include theme_path('templates/layouts/header.php');
?>

<section class="page-hero-wrap">
  <?php include theme_path('templates/partials/navbar.php'); ?>
  <div class="page-hero">
  <div class="page-hero-breadcrumb">
    <a href="<?= url('/') ?>">Home</a>
    <span class="sep">/</span>
    <span>404</span>
  </div>
  <h1 class="page-hero-title">404</h1>
  <p class="page-hero-desc">
    Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.
  </p>
  <a href="<?= url('/') ?>" class="hero-cta" style="margin-top:32px;background:var(--accent);color:var(--text-dark)">
    Kembali ke Beranda
    <span class="arrow-icon" style="background:var(--text-dark);color:var(--accent)">
      <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
      </svg>
    </span>
  </a>
</div>
</section>

<?php include theme_path('templates/layouts/footer.php'); ?>
