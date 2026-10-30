<?php
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('blog');

// FILTER + SEARCH
$per_page = 9;
$current_page = max(1, (int)($_GET['p'] ?? 1));
$search   = trim($_GET['q'] ?? '');
$kat_slug = trim($_GET['kategori'] ?? '');

$where = ["b.status = 'published'"];
$params = [];
if ($search !== '') {
    $where[] = "(b.judul LIKE ? OR b.konten LIKE ?)";
    $params[] = "%$search%"; $params[] = "%$search%";
}

$kat_data = null;
$kat_filter_ids = [];
if ($kat_slug !== '') {
    try {
        $kat_data = $db->fetchOne("SELECT * FROM blog_kategori WHERE slug = ?", [$kat_slug]);
        if ($kat_data) {
            $b_has_parent = in_array('parent_id', $db->getColumns('blog_kategori'));
            if ($b_has_parent) {
                $blog_kats_raw = $db->fetchAll("SELECT id, parent_id FROM blog_kategori");
                $kat_filter_ids = array_merge([(int)$kat_data['id']], get_descendant_ids($blog_kats_raw, $kat_data['id']));
            } else {
                $kat_filter_ids = [(int)$kat_data['id']];
            }
            $placeholders = implode(',', array_fill(0, count($kat_filter_ids), '?'));
            $where[] = "EXISTS (SELECT 1 FROM blog_kategori_rel r WHERE r.blog_id = b.id AND r.kategori_id IN ($placeholders))";
            $params = array_merge($params, $kat_filter_ids);
        }
    } catch (Throwable $e) { /* table may not exist */ }
}
$where_sql = implode(' AND ', $where);

$total = $db->fetchOne("SELECT COUNT(*) c FROM blog b WHERE $where_sql", $params)['c'] ?? 0;
$total_pages = max(1, (int)ceil($total / $per_page));
$current_page = min($current_page, $total_pages);
$offset = ($current_page - 1) * $per_page;

$posts = $db->fetchAll(
    "SELECT b.*, u.nama as author_nama
     FROM blog b LEFT JOIN users u ON b.user_id = u.id
     WHERE $where_sql ORDER BY b.created_at DESC LIMIT $per_page OFFSET $offset",
    $params
);

// Sidebar data
$blog_categories = [];
$blog_cats_tree = [];
try {
    $bcols = $db->getColumns('blog_kategori');
    $b_has_parent = in_array('parent_id', $bcols);
    $b_has_urutan = in_array('urutan', $bcols);
    $select_parent = $b_has_parent ? 'k.parent_id,' : 'NULL as parent_id,';
    $order_cols = $b_has_urutan ? 'k.urutan ASC, k.nama ASC' : 'k.nama ASC';
    $blog_categories = $db->fetchAll(
        "SELECT k.id, k.nama, k.slug, $select_parent
                (SELECT COUNT(DISTINCT r.blog_id) FROM blog_kategori_rel r JOIN blog bb ON bb.id = r.blog_id
                 WHERE r.kategori_id = k.id AND bb.status = 'published') as count
         FROM blog_kategori k ORDER BY $order_cols"
    );
    $blog_cats_tree = $b_has_parent ? build_category_tree($blog_categories) : array_map(fn($c) => $c + ['children' => []], $blog_categories);
} catch (Throwable $e) { /* ignore */ }

$recent_posts = $db->fetchAll("SELECT id, judul, slug, gambar_utama, created_at FROM blog WHERE status='published' ORDER BY created_at DESC LIMIT 4");

function blog_url(array $overrides = []): string {
    $params = array_filter(array_merge([
        'q' => $_GET['q'] ?? '',
        'kategori' => $_GET['kategori'] ?? '',
        'p' => $_GET['p'] ?? '',
    ], $overrides), fn($v) => $v !== '' && $v !== null);
    return url('/blog') . (empty($params) ? '' : '?' . http_build_query($params));
}

$seo = [
    'title'       => $page_seo['meta_title'] ?? seo_title(($kat_data ? $kat_data['nama'] : 'Blog & Insights') . ($current_page > 1 ? ' - Halaman ' . $current_page : '')),
    'description' => $page_seo['meta_description'] ?? 'Tips, panduan, dan informasi seputar dunia reklame.',
];

include theme_path('templates/layouts/header.php');
?>

<section class="page-hero-wrap" style="padding-bottom:24px">
  <?php include theme_path('templates/partials/navbar.php'); ?>
  <div class="page-hero" style="padding-bottom:20px">
    <div class="page-hero-breadcrumb" style="display:flex;align-items:center;justify-content:center;gap:8px;flex-wrap:wrap">
      <a href="<?= url('/') ?>">Home</a>
      <span class="sep">/</span>
      <a href="<?= url('/blog') ?>">Blog</a>
      <?php if ($kat_data): ?>
      <span class="sep">/</span>
      <span><?= htmlspecialchars($kat_data['nama']) ?></span>
      <?php endif; ?>
    </div>
    <h1 class="page-hero-title">
      <?= htmlspecialchars($kat_data ? $kat_data['nama'] : get_content('blog', 'hero_title', 'Blog & Insights')) ?>
    </h1>
    <p class="page-hero-desc">
      <?= htmlspecialchars(get_content('blog', 'hero_desc', 'Tips, panduan, dan informasi seputar dunia reklame.')) ?>
    </p>
  </div>
</section>

<section class="blog-section">
  <div class="container blog-container">
    <!-- MAIN -->
    <main class="blog-main">
      <?php if ($search): ?>
      <div class="blog-search-info">
        Hasil pencarian untuk: "<strong><?= htmlspecialchars($search) ?></strong>" (<?= $total ?> artikel)
        <a href="<?= blog_url(['q'=>null,'p'=>null]) ?>" class="blog-clear">× Reset</a>
      </div>
      <?php endif; ?>

      <?php if (empty($posts)): ?>
        <div class="blog-empty">
          <div class="blog-empty-icon">📝</div>
          <h3>Belum ada artikel</h3>
          <p><?= $search || $kat_slug ? 'Coba ubah pencarian atau pilih kategori lain.' : 'Artikel akan segera hadir.' ?></p>
          <?php if ($search || $kat_slug): ?>
            <a href="<?= url('/blog') ?>" class="btn btn-primary" style="background:var(--accent);color:#fff;padding:10px 22px;border-radius:30px;text-decoration:none;font-weight:600;display:inline-block;margin-top:12px">Reset Filter</a>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="blog-grid">
          <?php foreach ($posts as $post): ?>
          <a href="<?= url('/blog/'.$post['slug']) ?>" class="blog-card">
            <div class="blog-card-img">
              <?php if ($post['gambar_utama']): ?>
                <img src="<?= uploads_url($post['gambar_utama']) ?>" alt="<?= htmlspecialchars($post['judul']) ?>" loading="lazy">
              <?php else: ?>
                <div style="background:var(--bg-cream-2);width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:48px;opacity:0.3">📝</div>
              <?php endif; ?>
            </div>
            <div class="blog-card-body">
              <div class="blog-card-meta">
                <span><?= date('d M Y', strtotime($post['created_at'])) ?></span>
                <?php if ($post['author_nama']): ?>
                  <span>•</span><span><?= htmlspecialchars($post['author_nama']) ?></span>
                <?php endif; ?>
              </div>
              <h3 class="blog-card-title"><?= htmlspecialchars($post['judul']) ?></h3>
              <p class="blog-card-desc"><?= htmlspecialchars(excerpt($post['excerpt'] ?: strip_tags($post['konten']), 100)) ?></p>
            </div>
          </a>
          <?php endforeach; ?>
        </div>

        <!-- PAGINATION -->
        <?php if ($total_pages > 1): ?>
        <nav class="blog-pagination" aria-label="Pagination">
          <?php
          $window = 2;
          $show = [];
          for ($i = 1; $i <= $total_pages; $i++) {
              if ($i === 1 || $i === $total_pages || abs($i - $current_page) <= $window) $show[] = $i;
          }
          $last = 0;
          ?>
          <?php if ($current_page > 1): ?>
            <a href="<?= blog_url(['p'=>$current_page-1]) ?>" class="blog-pg-arrow" rel="prev" aria-label="Sebelumnya">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
              <span>Sebelumnya</span>
            </a>
          <?php endif; ?>
          <?php foreach ($show as $p): ?>
            <?php if ($last && $p - $last > 1): ?><span class="blog-pg-gap">…</span><?php endif; ?>
            <a href="<?= blog_url(['p'=>$p]) ?>" class="blog-pg <?= $p === $current_page ? 'active' : '' ?>" <?= $p === $current_page ? 'aria-current="page"' : '' ?>><?= $p ?></a>
            <?php $last = $p; ?>
          <?php endforeach; ?>
          <?php if ($current_page < $total_pages): ?>
            <a href="<?= blog_url(['p'=>$current_page+1]) ?>" class="blog-pg-arrow" rel="next" aria-label="Berikutnya">
              <span>Berikutnya</span>
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          <?php endif; ?>
        </nav>

        <!-- SEO: rel prev/next links -->
        <?php if ($current_page > 1): ?>
        <link rel="prev" href="<?= blog_url(['p'=>$current_page-1]) ?>">
        <?php endif; ?>
        <?php if ($current_page < $total_pages): ?>
        <link rel="next" href="<?= blog_url(['p'=>$current_page+1]) ?>">
        <?php endif; ?>
        <?php endif; ?>
      <?php endif; ?>
    </main>

    <!-- SIDEBAR -->
    <aside class="blog-sidebar">
      <!-- Search -->
      <div class="blog-side-card">
        <h3 class="blog-side-title">Cari Artikel</h3>
        <form method="GET" action="<?= url('/blog') ?>" class="blog-search-form">
          <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari artikel..." autocomplete="off">
          <?php if ($kat_slug): ?><input type="hidden" name="kategori" value="<?= htmlspecialchars($kat_slug) ?>"><?php endif; ?>
          <button type="submit" aria-label="Cari">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </button>
        </form>
      </div>

      <!-- Categories -->
      <?php if (!empty($blog_categories)): ?>
      <div class="blog-side-card">
        <h3 class="blog-side-title">Kategori</h3>
        <ul class="blog-cat-list cat-tree">
          <li><a href="<?= blog_url(['kategori'=>null,'p'=>null]) ?>" class="<?= !$kat_slug ? 'active' : '' ?>">Semua Artikel <span><?= $db->fetchOne("SELECT COUNT(*) c FROM blog WHERE status='published'")['c'] ?? 0 ?></span></a></li>
          <?php
          function render_blog_cat_tree($nodes, $depth, $kat_slug, $current_kat_id, $all_cats_flat) {
              foreach ($nodes as $cat):
                $has_children = !empty($cat['children']);
                $is_active = $kat_slug === $cat['slug'];
                $descendant_ids = get_descendant_ids($all_cats_flat, $cat['id']);
                $is_in_active_path = $is_active || in_array($current_kat_id, $descendant_ids);
                $is_expanded = $is_in_active_path;
              ?>
              <li class="cat-node depth-<?= $depth ?> <?= $is_expanded ? 'expanded' : '' ?>">
                <div class="cat-row">
                  <a href="<?= blog_url(['kategori'=>$cat['slug'],'p'=>null]) ?>" class="<?= $is_active ? 'active' : '' ?>">
                    
                    <?php if ($has_children): ?>
                      <button type="button" class="cat-toggle" onclick="this.closest('.cat-node').classList.toggle('expanded');event.preventDefault();event.stopPropagation();" aria-label="Toggle">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                      </button>
                    <?php else: ?>
                      <span class="cat-bullet"></span>
                    <?php endif; ?>
                    <span class="cat-name"><?= htmlspecialchars($cat['nama']) ?></span>
                    <span class="cat-count"><?= $cat['count'] ?></span>
                  </a>
                </div>
                <?php if ($has_children): ?>
                <ul class="cat-children">
                  <?php render_blog_cat_tree($cat['children'], $depth + 1, $kat_slug, $current_kat_id, $all_cats_flat); ?>
                </ul>
                <?php endif; ?>
              </li>
              <?php
              endforeach;
          }
          $current_kat_id = $kat_data ? (int)$kat_data['id'] : 0;
          render_blog_cat_tree($blog_cats_tree, 0, $kat_slug, $current_kat_id, $blog_categories);
          ?>
        </ul>
      </div>
      <?php endif; ?>

      <!-- Recent Posts -->
      <?php if (!empty($recent_posts)): ?>
      <div class="blog-side-card">
        <h3 class="blog-side-title">Artikel Terbaru</h3>
        <ul class="blog-recent-list">
          <?php foreach ($recent_posts as $rp): ?>
          <li>
            <a href="<?= url('/blog/'.$rp['slug']) ?>" class="blog-recent-item">
              <div class="blog-recent-thumb">
                <?php if ($rp['gambar_utama']): ?>
                  <img src="<?= uploads_url($rp['gambar_utama']) ?>" alt="" loading="lazy">
                <?php else: ?>
                  <div style="background:var(--bg-cream-2);width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:18px;opacity:0.4">📝</div>
                <?php endif; ?>
              </div>
              <div class="blog-recent-meta">
                <h4><?= htmlspecialchars(mb_strlen($rp['judul']) > 55 ? mb_substr($rp['judul'],0,55).'…' : $rp['judul']) ?></h4>
                <time><?= date('d M Y', strtotime($rp['created_at'])) ?></time>
              </div>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
    </aside>
  </div>
</section>

<?php include theme_path('templates/layouts/footer.php'); ?>
