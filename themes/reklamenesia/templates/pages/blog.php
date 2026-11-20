<?php
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('blog');
$seo = [
  'title'       => $page_seo['meta_title']       ?? seo_title('Blog'),
  'description' => $page_seo['meta_description'] ?? 'Artikel & wawasan seputar dunia reklame dan branding.',
];

$per_page = 9;
$current_page = max(1, (int)($_GET['p'] ?? 1));
$search   = trim($_GET['q'] ?? '');
$kat_slug = trim($_GET['kategori'] ?? '');

$where = ["b.status = 'published'"]; $params = [];
if ($search !== '') { $where[] = "(b.judul LIKE ? OR b.konten LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
$kat_data = null;
if ($kat_slug !== '') {
  try {
    $kat_data = $db->fetchOne("SELECT * FROM blog_kategori WHERE slug = ?", [$kat_slug]);
    if ($kat_data) {
      $ids = [(int)$kat_data['id']];
      if (in_array('parent_id', $db->getColumns('blog_kategori'))) {
        $raw = $db->fetchAll("SELECT id, parent_id FROM blog_kategori");
        $ids = array_merge($ids, get_descendant_ids($raw, $kat_data['id']));
      }
      $ph = implode(',', array_fill(0, count($ids), '?'));
      $where[] = "EXISTS (SELECT 1 FROM blog_kategori_rel r WHERE r.blog_id=b.id AND r.kategori_id IN ($ph))";
      $params = array_merge($params, $ids);
    }
  } catch (Throwable $e) {}
}
$where_sql = implode(' AND ', $where);
$total = $db->fetchOne("SELECT COUNT(*) c FROM blog b WHERE $where_sql", $params)['c'] ?? 0;
$total_pages = max(1, (int)ceil($total / $per_page));
$current_page = min($current_page, $total_pages);
$offset = ($current_page - 1) * $per_page;
$posts = $db->fetchAll("SELECT b.*, u.nama as author_nama FROM blog b LEFT JOIN users u ON b.user_id=u.id WHERE $where_sql ORDER BY b.created_at DESC LIMIT $per_page OFFSET $offset", $params);

$blog_cats = [];
try { $blog_cats = $db->fetchAll("SELECT k.nama, k.slug, COUNT(r.blog_id) c FROM blog_kategori k LEFT JOIN blog_kategori_rel r ON r.kategori_id=k.id GROUP BY k.id ORDER BY k.nama ASC"); } catch (Throwable $e) {}

function blog_q($extra = []) {
  $q = array_merge(array_filter(['q'=>$_GET['q']??'','kategori'=>$_GET['kategori']??'']), $extra);
  return $q ? '?' . http_build_query($q) : '';
}
include theme_path('templates/layouts/header.php');
?>

<section class="page-hero">
  <div class="inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><span>Blog</span></div>
    <h1><?= htmlspecialchars(get_content('blog', 'hero_title', 'Blog & Insights')) ?></h1>
    <p><?= htmlspecialchars(get_content('blog', 'hero_desc', 'Artikel terbaru seputar dunia reklame, branding, dan tips bisnis.')) ?></p>
  </div>
</section>

<section class="section bg-white">
  <div class="container">
    <form class="blog-search" method="get" action="<?= url('/blog') ?>" data-reveal>
      <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari artikel…" aria-label="Cari">
      <button type="submit">Cari</button>
    </form>

    <?php if (!empty($blog_cats)): ?>
    <div class="gal-filters" data-reveal>
      <a href="<?= url('/blog') ?>" class="gal-filter <?= !$kat_slug ? 'active' : '' ?>">Semua</a>
      <?php foreach ($blog_cats as $c): if (!$c['slug']) continue; ?>
        <a href="<?= url('/blog?kategori='.($c['slug'] ?? '')) ?>" class="gal-filter <?= $kat_slug === ($c['slug'] ?? '') ? 'active' : '' ?>"><?= htmlspecialchars($c['nama'] ?? '') ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
      <div class="empty-state">Belum ada artikel<?= $search ? ' untuk "'.htmlspecialchars($search).'"' : '' ?>.</div>
    <?php else: ?>
    <div class="blog-grid" data-stagger>
      <?php foreach ($posts as $bp): ?>
      <a href="<?= url('/blog/'.$bp['slug']) ?>" class="blog-card">
        <div class="blog-card-img">
          <?php if ($bp['gambar_utama']): ?><img src="<?= uploads_url($bp['gambar_utama']) ?>" alt="<?= htmlspecialchars($bp['judul']) ?>" loading="lazy"><?php else: ?><div class="blog-card-noimg">✳</div><?php endif; ?>
        </div>
        <div class="blog-card-body">
          <div class="blog-card-meta"><?= date('d M Y', strtotime($bp['created_at'])) ?><?= $bp['author_nama'] ? ' · '.htmlspecialchars($bp['author_nama']) : '' ?></div>
          <h3 class="blog-card-title"><?= htmlspecialchars($bp['judul']) ?></h3>
          <p class="blog-card-ex"><?= htmlspecialchars(excerpt($bp['excerpt'] ?: strip_tags($bp['konten']), 110)) ?></p>
          <span class="blog-card-cta">Baca Artikel →</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="pagination">
      <?php if ($current_page > 1): ?><a href="<?= url('/blog'.blog_q(['p'=>$current_page-1])) ?>">‹</a><?php endif; ?>
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <?php if ($i == $current_page): ?><span class="active"><?= $i ?></span><?php else: ?><a href="<?= url('/blog'.blog_q(['p'=>$i])) ?>"><?= $i ?></a><?php endif; ?>
      <?php endfor; ?>
      <?php if ($current_page < $total_pages): ?><a href="<?= url('/blog'.blog_q(['p'=>$current_page+1])) ?>">›</a><?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php include theme_path('templates/layouts/footer.php'); ?>
