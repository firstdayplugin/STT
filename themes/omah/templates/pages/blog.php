<?php
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('blog');
$seo = ['title' => $page_seo['meta_title'] ?? seo_title('Blog'), 'description' => $page_seo['meta_description'] ?? 'Artikel & tips properti dari DIFA Property.'];

$per_page     = 9;
$current_page = max(1, (int)($_GET['p'] ?? 1));
$search       = trim($_GET['q'] ?? '');
$where        = ["status='published'"]; $params = [];
if ($search !== '') { $where[] = "(judul LIKE ? OR konten LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
$where_sql    = implode(' AND ', $where);
$total        = $db->fetchOne("SELECT COUNT(*) c FROM blog WHERE $where_sql", $params)['c'] ?? 0;
$total_pages  = max(1, (int)ceil($total / $per_page));
$current_page = min($current_page, $total_pages);
$offset       = ($current_page - 1) * $per_page;
$posts        = $db->fetchAll("SELECT b.*, u.nama as author_nama FROM blog b LEFT JOIN users u ON u.id=b.user_id WHERE $where_sql ORDER BY b.created_at DESC LIMIT $per_page OFFSET $offset", $params);

function blog_q_omah($extra=[]) {
  $q = array_merge(array_filter(['q'=>$_GET['q']??'']), $extra);
  return $q ? '?'.http_build_query($q) : '';
}
include theme_path('templates/layouts/header.php');
?>
<?php $flex_position = 'blog_top'; include theme_path('templates/partials/flex-content.php'); ?>

<div class="page-hero">
  <div class="page-hero-inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><span>Blog</span></div>
    <h1><?= htmlspecialchars(get_content('blog','hero_title','Blog & Artikel')) ?></h1>
    <p><?= htmlspecialchars(get_content('blog','hero_desc','Tips, panduan, dan informasi seputar properti.')) ?></p>
  </div>
</div>

<section style="padding-block:var(--sec-y);padding-inline:var(--pad-x);background:var(--om-white);">
  <div style="max-width:var(--container-max);margin-inline:auto;">
    <form class="blog-search" method="get" action="<?= url('/blog') ?>">
      <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari artikel…">
      <button type="submit">Cari</button>
    </form>

    <?php if (empty($posts)): ?>
      <div class="empty-state">Belum ada artikel<?= $search ? ' untuk "'.htmlspecialchars($search).'"' : '' ?>.</div>
    <?php else: ?>
    <div class="blog-grid" data-stagger>
      <?php foreach ($posts as $bp): ?>
      <a href="<?= url('/blog/'.$bp['slug']) ?>" class="blog-card">
        <div class="blog-card-img">
          <?php if ($bp['gambar_utama']): ?>
            <img src="<?= uploads_url($bp['gambar_utama']) ?>" alt="<?= htmlspecialchars($bp['judul']) ?>" loading="lazy">
          <?php else: ?>
            <div class="blog-card-noimg">✳</div>
          <?php endif; ?>
        </div>
        <div class="blog-card-body">
          <div class="blog-card-meta"><?= date('d M Y', strtotime($bp['created_at'])) ?><?= $bp['author_nama'] ? ' · '.htmlspecialchars($bp['author_nama']) : '' ?></div>
          <h3 class="blog-card-title"><?= htmlspecialchars($bp['judul']) ?></h3>
          <p class="blog-card-ex"><?= htmlspecialchars(excerpt($bp['excerpt'] ?: strip_tags($bp['konten']), 100)) ?></p>
          <span class="blog-card-cta">Baca Artikel →</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="pagination">
      <?php if ($current_page > 1): ?><a href="<?= url('/blog'.blog_q_omah(['p'=>$current_page-1])) ?>">‹</a><?php endif; ?>
      <?php for ($i=1; $i<=$total_pages; $i++): ?>
        <?php if ($i===$current_page): ?><span class="active"><?= $i ?></span><?php else: ?><a href="<?= url('/blog'.blog_q_omah(['p'=>$i])) ?>"><?= $i ?></a><?php endif; ?>
      <?php endfor; ?>
      <?php if ($current_page < $total_pages): ?><a href="<?= url('/blog'.blog_q_omah(['p'=>$current_page+1])) ?>">›</a><?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php $flex_position = 'blog_bottom'; include theme_path('templates/partials/flex-content.php'); ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
