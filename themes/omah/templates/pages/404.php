<?php
$seo = ['title' => seo_title('Halaman Tidak Ditemukan'), 'description' => '404 Not Found'];
include theme_path('templates/layouts/header.php');
?>
<div style="min-height:60vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:80px var(--pad-x);gap:24px;">
  <div style="font-size:120px;font-weight:600;line-height:1;color:var(--om-gray-lt);">404</div>
  <h1 style="font-size:32px;font-weight:600;color:var(--om-dark);text-transform:none;">Halaman Tidak Ditemukan</h1>
  <p style="font-size:18px;font-weight:300;color:var(--om-gray);max-width:480px;">Halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
  <a href="<?= url('/') ?>" style="display:inline-flex;align-items:center;gap:8px;background:var(--om-dark);color:#fff;padding:12px 28px;border-radius:99px;font-size:16px;font-weight:600;text-decoration:none;">
    Kembali ke Beranda
  </a>
</div>
<?php include theme_path('templates/layouts/footer.php'); ?>
