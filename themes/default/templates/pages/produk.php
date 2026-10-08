<?php
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('produk');

// --- FILTER, SEARCH, SORT, PAGINATION ---
$per_page = 12;
$current_page = max(1, (int)($_GET['p'] ?? 1));
$search   = trim($_GET['q'] ?? '');
$kat_slug = trim($_GET['kategori'] ?? '');
$sort     = $_GET['sort'] ?? 'newest';
$view     = $_GET['view'] ?? 'grid';

// Build WHERE
$where = ["p.status = 'aktif'"];
$params = [];
if ($search !== '') {
    $where[] = "(p.nama LIKE ? OR p.deskripsi LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$kat_data = null;
$kat_filter_ids = [];
if ($kat_slug !== '') {
    $kat_data = $db->fetchOne("SELECT * FROM produk_kategori WHERE slug = ?", [$kat_slug]);
    if ($kat_data) {
        // Include this category + all descendant categories
        $all_kats_raw = $db->fetchAll("SELECT id, parent_id FROM produk_kategori");
        $kat_filter_ids = array_merge([(int)$kat_data['id']], get_descendant_ids($all_kats_raw, $kat_data['id']));
        $placeholders = implode(',', array_fill(0, count($kat_filter_ids), '?'));
        $where[] = "EXISTS (SELECT 1 FROM produk_kategori_rel r WHERE r.produk_id = p.id AND r.kategori_id IN ($placeholders))";
        $params = array_merge($params, $kat_filter_ids);
    }
}
$where_sql = implode(' AND ', $where);

// Sort
$order_sql = match($sort) {
    'price_asc'  => 'p.harga ASC',
    'price_desc' => 'p.harga DESC',
    'name_asc'   => 'p.nama ASC',
    'name_desc'  => 'p.nama DESC',
    'popular'    => 'p.id DESC', // proxy until views column exists
    default      => 'p.created_at DESC',
};

// Count + fetch
$total = $db->fetchOne("SELECT COUNT(*) as c FROM produk p WHERE $where_sql", $params)['c'] ?? 0;
$total_pages = max(1, (int)ceil($total / $per_page));
$current_page = min($current_page, $total_pages);
$offset = ($current_page - 1) * $per_page;
$products = $db->fetchAll(
    "SELECT p.* FROM produk p WHERE $where_sql ORDER BY $order_sql LIMIT $per_page OFFSET $offset",
    $params
);

// Category sidebar - all + counts + parent_id
try {
    $has_parent = in_array('parent_id', $db->getColumns('produk_kategori'));
    $has_urutan = in_array('urutan', $db->getColumns('produk_kategori'));
} catch (Throwable $e) { $has_parent = false; $has_urutan = false; }

$order_cols = $has_urutan ? 'k.urutan ASC, k.nama ASC' : 'k.nama ASC';
$select_parent = $has_parent ? 'k.parent_id,' : 'NULL as parent_id,';
$all_cats = $db->fetchAll(
    "SELECT k.id, k.nama, k.slug, $select_parent
            (SELECT COUNT(DISTINCT r.produk_id) FROM produk_kategori_rel r JOIN produk pp ON pp.id = r.produk_id
             WHERE r.kategori_id = k.id AND pp.status = 'aktif') as count
     FROM produk_kategori k ORDER BY $order_cols"
);
// Build hierarchical tree
$cats_tree = $has_parent ? build_category_tree($all_cats) : array_map(fn($c) => $c + ['children' => []], $all_cats);

// Build URL preserving filters
function product_url(array $overrides = []): string {
    $params = array_filter(array_merge([
        'q' => $_GET['q'] ?? '',
        'kategori' => $_GET['kategori'] ?? '',
        'sort' => $_GET['sort'] ?? '',
        'view' => $_GET['view'] ?? '',
        'p' => $_GET['p'] ?? '',
    ], $overrides), fn($v) => $v !== '' && $v !== null);
    return url('/produk') . (empty($params) ? '' : '?' . http_build_query($params));
}

$seo = [
    'title'       => $page_seo['meta_title'] ?? seo_title($kat_data ? $kat_data['nama'] . ' - Produk' : 'Produk Kami'),
    'description' => $page_seo['meta_description'] ?? 'Pilihan produk berkualitas untuk kebutuhan reklame dan branding bisnis Anda.',
];
$wa_default = get_setting('wa_number');

include theme_path('templates/layouts/header.php');
?>

<section class="page-hero-wrap" style="padding-bottom:24px">
  <?php include theme_path('templates/partials/navbar.php'); ?>
  <div class="page-hero" style="padding-bottom:20px">
    <div class="page-hero-breadcrumb" style="display:flex;align-items:center;justify-content:center;gap:8px;flex-wrap:wrap">
      <a href="<?= url('/') ?>">Home</a>
      <span class="sep">/</span>
      <a href="<?= url('/produk') ?>">Produk</a>
      <?php if ($kat_data): ?>
      <span class="sep">/</span>
      <span><?= htmlspecialchars($kat_data['nama']) ?></span>
      <?php endif; ?>
    </div>
    <h1 class="page-hero-title">
      <?= htmlspecialchars($kat_data ? $kat_data['nama'] : get_content('produk', 'hero_title', 'Produk Kami')) ?>
    </h1>
    <p class="page-hero-desc">
      <?= htmlspecialchars(get_content('produk', 'hero_desc', 'Pilihan produk berkualitas untuk kebutuhan reklame dan branding bisnis Anda.')) ?>
    </p>
  </div>
</section>

<section class="shop-wrap">
  <div class="container shop-container">
    <!-- SIDEBAR -->
    <aside class="shop-sidebar">
      <!-- Search -->
      <div class="shop-card">
        <h3 class="shop-card-title">Cari Produk</h3>
        <form method="GET" action="<?= url('/produk') ?>" class="shop-search">
          <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari produk..." autocomplete="off">
          <?php if ($kat_slug): ?><input type="hidden" name="kategori" value="<?= htmlspecialchars($kat_slug) ?>"><?php endif; ?>
          <button type="submit" aria-label="Cari">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </button>
        </form>
      </div>

      <!-- Categories -->
      <div class="shop-card">
        <h3 class="shop-card-title">Kategori</h3>
        <ul class="shop-cat-list cat-tree">
          <li><a href="<?= product_url(['kategori'=>null,'p'=>null]) ?>" class="<?= !$kat_slug ? 'active' : '' ?>">Semua Produk <span><?= $db->fetchOne("SELECT COUNT(*) c FROM produk WHERE status='aktif'")['c'] ?? 0 ?></span></a></li>
          <?php 
          // Render category tree recursively. Active path = current category + all ancestors expanded.
          function render_cat_tree($nodes, $depth, $kat_slug, $current_kat_id, $all_cats_flat) {
              foreach ($nodes as $cat):
                $has_children = !empty($cat['children']);
                $is_active = $kat_slug === $cat['slug'];
                // Auto-expand if this node OR any descendant is active
                $descendant_ids = get_descendant_ids($all_cats_flat, $cat['id']);
                $is_in_active_path = $is_active || in_array($current_kat_id, $descendant_ids);
                $is_expanded = $is_in_active_path;
              ?>
              <li class="cat-node depth-<?= $depth ?> <?= $is_expanded ? 'expanded' : '' ?>">
                <div class="cat-row">
                  <a href="<?= product_url(['kategori'=>$cat['slug'],'p'=>null]) ?>" class="<?= $is_active ? 'active' : '' ?>">
                    
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
                  <?php render_cat_tree($cat['children'], $depth + 1, $kat_slug, $current_kat_id, $all_cats_flat); ?>
                </ul>
                <?php endif; ?>
              </li>
              <?php
              endforeach;
          }
          $current_kat_id = $kat_data ? (int)$kat_data['id'] : 0;
          render_cat_tree($cats_tree, 0, $kat_slug, $current_kat_id, $all_cats);
          ?>
        </ul>
      </div>

      <?php if ($wa_default): ?>
      <div class="shop-card shop-help">
        <h3 class="shop-card-title">Butuh Bantuan?</h3>
        <p style="font-size:13px;color:var(--text-muted);line-height:1.6;margin:0 0 12px">Tim kami siap membantu Anda memilih produk yang tepat.</p>
        <a href="<?= wa_url($wa_default, 'Halo, saya ingin tanya tentang produk Anda.') ?>" target="_blank" rel="noopener" class="shop-help-cta">
          <span style="width:18px;height:18px;display:inline-flex"><?= social_icon_svg('whatsapp') ?></span>
          Chat WhatsApp
        </a>
      </div>
      <?php endif; ?>
    </aside>

    <!-- MAIN -->
    <main class="shop-main">
      <!-- Toolbar: count + sort + view toggle -->
      <div class="shop-toolbar">
        <div class="shop-toolbar-info">
          <?php if ($total > 0): ?>
            Menampilkan <strong><?= ($offset+1) ?>–<?= min($offset + $per_page, $total) ?></strong> dari <strong><?= $total ?></strong> produk
            <?php if ($search): ?>untuk "<strong><?= htmlspecialchars($search) ?></strong>"<?php endif; ?>
          <?php else: ?>
            Tidak ada produk ditemukan
          <?php endif; ?>
        </div>
        <div class="shop-toolbar-controls">
          <form method="GET" action="<?= url('/produk') ?>" class="shop-sort-form" onchange="this.submit()">
            <?php foreach (['q','kategori','view'] as $hk): if (!empty($_GET[$hk])): ?>
              <input type="hidden" name="<?= $hk ?>" value="<?= htmlspecialchars($_GET[$hk]) ?>">
            <?php endif; endforeach; ?>
            <label>Urutkan:</label>
            <select name="sort">
              <option value="newest"     <?= $sort==='newest'?'selected':'' ?>>Terbaru</option>
              <option value="popular"    <?= $sort==='popular'?'selected':'' ?>>Populer</option>
              <option value="price_asc"  <?= $sort==='price_asc'?'selected':'' ?>>Harga: Rendah → Tinggi</option>
              <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Harga: Tinggi → Rendah</option>
              <option value="name_asc"   <?= $sort==='name_asc'?'selected':'' ?>>Nama: A → Z</option>
              <option value="name_desc"  <?= $sort==='name_desc'?'selected':'' ?>>Nama: Z → A</option>
            </select>
          </form>
          <div class="shop-view-toggle">
            <a href="<?= product_url(['view'=>'grid']) ?>" class="<?= $view==='grid'?'active':'' ?>" aria-label="Grid view">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h6v6H4zM14 6h6v6h-6zM4 16h6v6H4zM14 16h6v6h-6z"/></svg>
            </a>
            <a href="<?= product_url(['view'=>'list']) ?>" class="<?= $view==='list'?'active':'' ?>" aria-label="List view">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </a>
          </div>
        </div>
      </div>

      <?php if (empty($products)): ?>
        <!-- EMPTY state -->
        <div class="shop-empty">
          <div class="shop-empty-icon">
            <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
          </div>
          <h3>Belum ada produk</h3>
          <p>Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
          <?php if ($search || $kat_slug): ?>
            <a href="<?= url('/produk') ?>" class="btn btn-primary" style="background:var(--accent);color:#fff;padding:10px 22px;border-radius:30px;text-decoration:none;font-weight:600;display:inline-block;margin-top:12px">Reset Filter</a>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <!-- GRID / LIST -->
        <div class="shop-products shop-<?= $view === 'list' ? 'list' : 'grid' ?>">
          <?php foreach ($products as $p): 
            $p_img = $p['gambar_utama'] ? uploads_url($p['gambar_utama']) : '';
            $p_short = trim($p['short_description'] ?? '') ?: excerpt($p['deskripsi'] ?? '', 100);
            $p_wa = wa_url($wa_default, 'Halo, saya tertarik dengan produk "'.$p['nama'].'".');
          ?>
          <article class="shop-prod">
            <a href="<?= url('/produk/'.$p['slug']) ?>" class="shop-prod-thumb">
              <?php if ($p_img): ?>
                <img src="<?= $p_img ?>" alt="<?= htmlspecialchars($p['nama']) ?>" loading="lazy">
              <?php else: ?>
                <div class="shop-prod-noimg">📦</div>
              <?php endif; ?>
              <?php if ($p['badge']): ?>
                <span class="shop-prod-badge"><?= htmlspecialchars($p['badge']) ?></span>
              <?php endif; ?>
            </a>
            <div class="shop-prod-body">
              <h3 class="shop-prod-title">
                <a href="<?= url('/produk/'.$p['slug']) ?>"><?= htmlspecialchars($p['nama']) ?></a>
              </h3>
              <?php if ($view === 'list' && $p_short): ?>
              <p class="shop-prod-desc"><?= htmlspecialchars($p_short) ?></p>
              <?php endif; ?>
              <div class="shop-prod-price">
                <?php if ($p['harga']): ?>
                  <strong><?= format_rupiah($p['harga']) ?></strong>
                  <?php if ($p['harga_coret']): ?><span class="old"><?= format_rupiah($p['harga_coret']) ?></span><?php endif; ?>
                <?php else: ?>
                  <strong style="font-size:14px">Hubungi Kami</strong>
                <?php endif; ?>
              </div>
              <div class="shop-prod-actions">
                <a href="<?= url('/produk/'.$p['slug']) ?>" class="shop-prod-detail">Detail</a>
                <a href="<?= $p_wa ?>" target="_blank" rel="noopener" class="shop-prod-wa" aria-label="WhatsApp">
                  <span style="width:16px;height:16px;display:inline-flex"><?= social_icon_svg('whatsapp') ?></span>
                </a>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>

        <!-- PAGINATION -->
        <?php if ($total_pages > 1): ?>
        <nav class="shop-pagination" aria-label="Pagination">
          <?php
          $window = 2;
          $show_pages = [];
          for ($i = 1; $i <= $total_pages; $i++) {
              if ($i === 1 || $i === $total_pages || abs($i - $current_page) <= $window) $show_pages[] = $i;
          }
          $last = 0;
          ?>
          <?php if ($current_page > 1): ?>
            <a href="<?= product_url(['p'=>$current_page-1]) ?>" class="shop-pg-arrow" aria-label="Previous">‹</a>
          <?php endif; ?>
          <?php foreach ($show_pages as $p): ?>
            <?php if ($last && $p - $last > 1): ?><span class="shop-pg-gap">…</span><?php endif; ?>
            <a href="<?= product_url(['p'=>$p]) ?>" class="shop-pg <?= $p === $current_page ? 'active' : '' ?>"><?= $p ?></a>
            <?php $last = $p; ?>
          <?php endforeach; ?>
          <?php if ($current_page < $total_pages): ?>
            <a href="<?= product_url(['p'=>$current_page+1]) ?>" class="shop-pg-arrow" aria-label="Next">›</a>
          <?php endif; ?>
        </nav>
        <?php endif; ?>
      <?php endif; ?>
    </main>
  </div>
</section>

<?php include theme_path('templates/layouts/footer.php'); ?>
