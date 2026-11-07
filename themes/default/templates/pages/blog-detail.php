<?php
if (!isset($db)) $db = Database::getInstance();
if (empty($blog_data)) { http_response_code(404); require theme_path('templates/pages/404.php'); exit; }

$seo = [
  'title'       => seo_title($blog_data['meta_title'] ?: $blog_data['judul']),
  'description' => $blog_data['meta_description'] ?: excerpt($blog_data['konten'], 160),
  'image'       => $blog_data['gambar_utama'] ? uploads_url($blog_data['gambar_utama']) : '',
];

$related = $db->fetchAll(
  "SELECT * FROM blog WHERE status='published' AND id != ? ORDER BY created_at DESC LIMIT 3",
  [$blog_data['id']]
);

include theme_path('templates/layouts/header.php');
?>

<section class="page-hero-wrap">
  <?php include theme_path('templates/partials/navbar.php'); ?>
  <div class="page-hero">
  <div class="page-hero-breadcrumb" style="display:flex;align-items:center;justify-content:center;gap:8px;flex-wrap:nowrap;max-width:90%;margin:0 auto">
    <a href="<?= url('/') ?>">Home</a>
    <span class="sep">/</span>
    <a href="<?= url('/blog') ?>">Blog</a>
    <span class="sep">/</span>
    <span style="display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:260px;vertical-align:bottom" title="<?= htmlspecialchars($blog_data['judul']) ?>"><?php
      $bc_title = $blog_data['judul'];
      echo htmlspecialchars(mb_strlen($bc_title) > 40 ? mb_substr($bc_title, 0, 40) . '…' : $bc_title);
    ?></span>
  </div>
  <h1 class="page-hero-title" style="font-size:clamp(28px, 4vw, 48px);max-width:800px;margin:0 auto">
    <?= htmlspecialchars($blog_data['judul']) ?>
  </h1>
  <div class="page-hero-desc" style="margin-top:24px">
    <?= date('d M Y', strtotime($blog_data['created_at'])) ?>
    <?php if (!empty($blog_data['penulis_nama'])): ?> · <?= htmlspecialchars($blog_data['penulis_nama']) ?><?php endif; ?>
  </div>
</div>
</section>

<article style="padding: 60px 0; background: white">
  <div class="container" style="max-width:780px">
    <?php if ($blog_data['gambar_utama']): ?>
      <img src="<?= uploads_url($blog_data['gambar_utama']) ?>" alt="" 
           style="width:100%;border-radius:var(--radius-md);margin-bottom:40px">
    <?php endif; ?>
    
    <div style="font-size:16px;line-height:1.85;color:var(--text-dark)">
      <?= $blog_data['konten'] ?>
    </div>
  </div>
</article>

<?php if (!empty($related)): ?>
<section class="blog-section" style="background:var(--bg-cream)">
  <div class="container">
    <h3 class="section-title" style="text-align:center;margin-bottom:40px">Artikel Terkait</h3>
    <div class="blog-grid">
      <?php foreach ($related as $rel): ?>
      <a href="<?= url('/blog/'.$rel['slug']) ?>" class="blog-card">
        <div class="blog-card-img">
          <?php if ($rel['gambar_utama']): ?>
            <img src="<?= uploads_url($rel['gambar_utama']) ?>" alt="">
          <?php endif; ?>
        </div>
        <div class="blog-card-body">
          <div class="blog-card-meta"><?= date('d M Y', strtotime($rel['created_at'])) ?></div>
          <h4 class="blog-card-title"><?= htmlspecialchars($rel['judul']) ?></h4>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include theme_path('templates/layouts/footer.php'); ?>
