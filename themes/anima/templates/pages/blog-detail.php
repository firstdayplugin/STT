<?php
/**
 * Anima theme — Blog / What's New article (route: /blog/[slug]).
 * index.php passes $blog_data (+ $blog_tags, $blog_kategori). Shares header/footer.
 */
$b = $blog_data ?? [];
$tags = $blog_tags ?? [];
$kats = $blog_kategori ?? [];
$fmt  = fn($d) => $d ? date('F j, Y', strtotime($d)) : '';
$seo = ['title' => ($b['judul'] ?? 'Artikel') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => $b['meta_description'] ?? mb_substr(strip_tags($b['excerpt'] ?? ''), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body"><div class="page-shell bl-article">
  <a class="bl-back" href="<?= url('blog') ?>">
    <svg viewBox="0 0 24 24"><path d="M19 12H5M11 6l-6 6 6 6"/></svg> What's New</a>
  <div class="bl-article-meta">
    <span class="bl-date"><?= htmlspecialchars($fmt($b['created_at'] ?? '')) ?></span>
    <?php foreach ($kats as $k): ?><span class="bl-chip"><?= htmlspecialchars($k['nama']) ?></span><?php endforeach; ?>
  </div>
  <h1><?= htmlspecialchars($b['judul'] ?? 'Artikel') ?></h1>
  <?php if (!empty($b['gambar_utama'])): ?>
    <div class="bl-article-img"><img src="<?= htmlspecialchars(uploads_url($b['gambar_utama'])) ?>" data-fallback="remove" alt=""></div>
  <?php endif; ?>
  <article class="page-prose"><?= $b['konten'] ?? '' ?></article>
  <?php if ($tags): ?>
  <div class="bl-tags-row"><?php foreach ($tags as $t): ?><span class="bl-chip">#<?= htmlspecialchars($t['nama']) ?></span><?php endforeach; ?></div>
  <?php endif; ?>
</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
