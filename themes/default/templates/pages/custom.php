<?php
if (empty($page)) { http_response_code(404); require theme_path('templates/pages/404.php'); exit; }

$seo = [
  'title' => seo_title($page['meta_title'] ?: $page['judul']),
  'description' => $page['meta_description'] ?: '',
];

if ($page['template'] === 'blank') { echo $page['konten']; exit; }

include theme_path('templates/layouts/header.php');
?>

<section class="page-hero-wrap">
  <?php include theme_path('templates/partials/navbar.php'); ?>
  <div class="page-hero">
  <div class="page-hero-breadcrumb">
    <a href="<?= url('/') ?>">Home</a>
    <span class="sep">/</span>
    <span><?= htmlspecialchars($page['judul']) ?></span>
  </div>
  <h1 class="page-hero-title"><?= htmlspecialchars($page['judul']) ?></h1>
</div>
</section>

<section style="padding: 60px 0; background: white">
  <div class="container" style="max-width:<?= $page['template'] === 'fullwidth' ? '1200' : '780' ?>px">
    <div style="line-height:1.85;font-size:16px;color:var(--text-dark)">
      <?= $page['konten'] ?>
    </div>
  </div>
</section>

<?php include theme_path('templates/layouts/footer.php'); ?>
