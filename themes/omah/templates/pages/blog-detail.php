<?php
if (!isset($db)) $db = Database::getInstance();
// CMS passes $blog_data for blog detail pages
if (empty($blog_data)) { http_response_code(404); require theme_path('templates/pages/404.php'); exit; }

$seo = [
  'title'       => seo_title($blog_data['meta_title'] ?: $blog_data['judul']),
  'description' => $blog_data['meta_description'] ?: excerpt(strip_tags($blog_data['konten']), 160),
  'image'       => $blog_data['gambar_utama'] ? uploads_url($blog_data['gambar_utama']) : '',
];
$related = $db->fetchAll("SELECT * FROM blog WHERE status='published' AND id != ? ORDER BY created_at DESC LIMIT 3", [$blog_data['id']]);
include theme_path('templates/layouts/header.php');
?>

<div class="page-hero">
  <div class="page-hero-inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><a href="<?= url('/blog') ?>">Blog</a><span class="sep">/</span><span><?= htmlspecialchars(excerpt($blog_data['judul'],50)) ?></span></div>
    <h1 style="text-transform:none;font-size:clamp(24px,3vw,44px);"><?= htmlspecialchars($blog_data['judul']) ?></h1>
    <p style="font-size:14px;color:var(--om-gray);margin-top:8px;">
      <?= date('d M Y', strtotime($blog_data['created_at'])) ?>
      <?php if (!empty($blog_data['penulis_nama'])): ?> · <?= htmlspecialchars($blog_data['penulis_nama']) ?><?php endif; ?>
    </p>
  </div>
</div>

<article style="padding-block:var(--sec-y);padding-inline:var(--pad-x);background:var(--om-white);">
  <div style="max-width:780px;margin-inline:auto;">
    <?php if ($blog_data['gambar_utama']): ?>
      <div style="border-radius:16px;overflow:hidden;margin-bottom:40px;height:400px;" data-reveal>
        <img src="<?= uploads_url($blog_data['gambar_utama']) ?>" alt="<?= htmlspecialchars($blog_data['judul']) ?>" style="width:100%;height:100%;object-fit:cover;">
      </div>
    <?php endif; ?>
    <div class="prose" data-reveal><?= $blog_data['konten'] ?></div>
    <div style="margin-top:48px;padding-top:24px;border-top:1px solid var(--om-border);">
      <a href="<?= url('/blog') ?>" style="font-size:15px;font-weight:600;color:var(--om-dark);display:inline-flex;align-items:center;gap:8px;text-decoration:none;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Kembali ke Blog
      </a>
    </div>
  </div>
</article>

<?php if (!empty($related)): ?>
<section style="padding-block:var(--sec-y);padding-inline:var(--pad-x);background:var(--om-gray-lt);">
  <div style="max-width:var(--container-max);margin-inline:auto;">
    <div class="blog-grid" data-stagger>
      <?php foreach ($related as $rel): ?>
      <a href="<?= url('/blog/'.$rel['slug']) ?>" class="blog-card">
        <div class="blog-card-img">
          <?php if ($rel['gambar_utama']): ?><img src="<?= uploads_url($rel['gambar_utama']) ?>" alt="<?= htmlspecialchars($rel['judul']) ?>" loading="lazy"><?php else: ?><div class="blog-card-noimg">✳</div><?php endif; ?>
        </div>
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
