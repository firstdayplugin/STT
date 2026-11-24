<?php
if (!isset($db)) $db = Database::getInstance();
if (empty($halaman)) { http_response_code(404); require theme_path('templates/pages/404.php'); exit; }
$seo = [
  'title'       => seo_title($halaman['meta_title'] ?: $halaman['judul']),
  'description' => $halaman['meta_description'] ?? excerpt(strip_tags($halaman['konten'] ?? ''), 160),
];
include theme_path('templates/layouts/header.php');
?>
<div class="page-hero">
  <div class="page-hero-inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><span><?= htmlspecialchars($halaman['judul']) ?></span></div>
    <h1 style="text-transform:none;"><?= htmlspecialchars($halaman['judul']) ?></h1>
  </div>
</div>
<section style="padding-block:var(--sec-y);padding-inline:var(--pad-x);background:var(--om-white);">
  <div style="max-width:800px;margin-inline:auto;" data-reveal>
    <div class="prose"><?= $halaman['konten'] ?></div>
  </div>
</section>
<?php include theme_path('templates/layouts/footer.php'); ?>
