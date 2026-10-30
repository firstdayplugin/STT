<?php
/**
 * Anima theme — Blog / What's New article (route: /blog/[slug]). Figma "Detail News".
 * index.php passes $blog_data (+ $blog_tags, $blog_kategori). Two-column: article card
 * (title, image with date/category ribbon, rich content) + shared sidebar. Shares header/footer.
 */
$b = $blog_data ?? [];
$tags = $blog_tags ?? [];
$kats = $blog_kategori ?? [];
$fmt  = fn($d) => $d ? date('F j, Y', strtotime($d)) : '';
$img  = fn($p) => $p ? (preg_match('#^(https?:|/|data:)#', $p) ? $p : uploads_url($p)) : '';
$cat_name = $kats[0]['nama'] ?? '';
$seo = ['title' => ($b['judul'] ?? 'Artikel') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => $b['meta_description'] ?? mb_substr(strip_tags($b['excerpt'] ?? ''), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
$blog_sidebar_cats = true;
?>
<main class="page-body"><div class="page-shell">
  <div class="bl-layout bl-detail">

    <div class="bl-article-main">
      <h1 class="bl-article-title"><?= htmlspecialchars($b['judul'] ?? 'Artikel') ?></h1>

      <?php if (!empty($b['gambar_utama'])): ?>
      <div class="bl-article-hero">
        <img src="<?= htmlspecialchars($img($b['gambar_utama'])) ?>" data-fallback="bg" alt="<?= htmlspecialchars($b['judul'] ?? '') ?>">
        <div class="bl-ribbon"><span class="d"><?= htmlspecialchars($fmt($b['created_at'] ?? '')) ?></span>
          <?php if ($cat_name !== ''): ?><span class="c"><?= htmlspecialchars($cat_name) ?></span><?php endif; ?></div>
      </div>
      <?php endif; ?>

      <article class="bl-article-card">
        <?php if (empty($b['gambar_utama'])): ?>
          <div class="bl-article-meta">
            <span class="bl-date"><?= htmlspecialchars($fmt($b['created_at'] ?? '')) ?></span>
            <?php foreach ($kats as $k): ?><span class="bl-chip"><?= htmlspecialchars($k['nama']) ?></span><?php endforeach; ?>
          </div>
        <?php endif; ?>
        <div class="bl-prose"><?= $b['konten'] ?? '' ?></div>
        <?php if ($tags): ?>
        <div class="bl-tags-row"><?php foreach ($tags as $t): ?><span class="bl-chip">#<?= htmlspecialchars($t['nama']) ?></span><?php endforeach; ?></div>
        <?php endif; ?>
      </article>

      <a class="bl-back" href="<?= url('blog') ?>">
        <svg viewBox="0 0 24 24"><path d="M19 12H5M11 6l-6 6 6 6"/></svg> <?= ac('blog','hero_title') ?: "What's New" ?></a>
    </div>

    <?php include theme_path('templates/pages/_blog-sidebar.php'); ?>

  </div>
</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
