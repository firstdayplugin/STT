<?php
if (!isset($db)) $db = Database::getInstance();
if (empty($blog_data)) { http_response_code(404); require theme_path('templates/pages/404.php'); exit; }

$seo = [
  'title'       => seo_title($blog_data['meta_title'] ?: $blog_data['judul']),
  'description' => $blog_data['meta_description'] ?: excerpt(strip_tags($blog_data['konten']), 160),
  'image'       => $blog_data['gambar_utama'] ? uploads_url($blog_data['gambar_utama']) : '',
];
$related = $db->fetchAll("SELECT * FROM blog WHERE status='published' AND id != ? ORDER BY created_at DESC LIMIT 3", [$blog_data['id']]);
include theme_path('templates/layouts/header.php');
?>

<section class="page-hero">
  <div class="inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><a href="<?= url('/blog') ?>">Blog</a></div>
    <h1 style="font-size:clamp(28px,4vw,52px)"><?= htmlspecialchars($blog_data['judul']) ?></h1>
    <p style="margin-top:20px"><?= date('d M Y', strtotime($blog_data['created_at'])) ?><?php if (!empty($blog_data['penulis_nama'])): ?> · <?= htmlspecialchars($blog_data['penulis_nama']) ?><?php endif; ?></p>
  </div>
</section>

<article class="section bg-white">
  <div class="container" style="max-width:800px">
    <?php if ($blog_data['gambar_utama']): ?>
      <img src="<?= uploads_url($blog_data['gambar_utama']) ?>" alt="<?= htmlspecialchars($blog_data['judul']) ?>" style="width:100%;border-radius:var(--r-lg);margin-bottom:44px" data-reveal="scale">
    <?php endif; ?>
    <div class="prose" data-reveal><?= $blog_data['konten'] ?></div>
  </div>
</article>

<?php if (!empty($related)): ?>
<section class="section bg-cream">
  <div class="container">
    <div class="sec-head center" data-reveal><span class="eyebrow center">Baca Juga</span><h2>Artikel Terkait</h2></div>
    <div class="blog-grid" style="margin-top:44px" data-stagger>
      <?php foreach ($related as $rel): ?>
      <a href="<?= url('/blog/'.$rel['slug']) ?>" class="blog-card">
        <div class="blog-card-img"><?php if ($rel['gambar_utama']): ?><img src="<?= uploads_url($rel['gambar_utama']) ?>" alt="<?= htmlspecialchars($rel['judul']) ?>" loading="lazy"><?php else: ?><div class="blog-card-noimg">✳</div><?php endif; ?></div>
        <div class="blog-card-body">
          <div class="blog-card-meta"><?= date('d M Y', strtotime($rel['created_at'])) ?></div>
          <h3 class="blog-card-title"><?= htmlspecialchars($rel['judul']) ?></h3>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include theme_path('templates/layouts/footer.php'); ?>
