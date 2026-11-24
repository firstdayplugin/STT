<?php
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('produk');
$seo = [
  'title'       => $page_seo['meta_title']       ?? seo_title('Properti'),
  'description' => $page_seo['meta_description'] ?? 'Katalog properti DIFA Property — hunian berkualitas, strategis, dan bernilai investasi tinggi.',
];

$per_page     = 12;
$current_page = max(1, (int)($_GET['p'] ?? 1));
$search       = trim($_GET['q'] ?? '');
$kat_slug     = trim($_GET['kategori'] ?? '');

$where = ["p.status = 'aktif'"]; $params = [];
if ($search !== '') {
  $where[] = "(p.nama LIKE ? OR p.deskripsi LIKE ?)";
  $params[] = "%$search%"; $params[] = "%$search%";
}
$kat_data = null;
if ($kat_slug !== '') {
  $kat_data = $db->fetchOne("SELECT * FROM produk_kategori WHERE slug = ?", [$kat_slug]);
  if ($kat_data) {
    $raw = $db->fetchAll("SELECT id, parent_id FROM produk_kategori");
    $ids = array_merge([(int)$kat_data['id']], get_descendant_ids($raw, $kat_data['id']));
    $ph  = implode(',', array_fill(0, count($ids), '?'));
    $where[] = "EXISTS (SELECT 1 FROM produk_kategori_rel r WHERE r.produk_id=p.id AND r.kategori_id IN ($ph))";
    $params  = array_merge($params, $ids);
  }
}
$where_sql = implode(' AND ', $where);
$total     = $db->fetchOne("SELECT COUNT(*) c FROM produk p WHERE $where_sql", $params)['c'] ?? 0;
$total_pages   = max(1, (int)ceil($total / $per_page));
$current_page  = min($current_page, $total_pages);
$offset        = ($current_page - 1) * $per_page;
$products      = $db->fetchAll("SELECT p.* FROM produk p WHERE $where_sql ORDER BY p.created_at DESC LIMIT $per_page OFFSET $offset", $params);

$kat_list = [];
try { $kat_list = $db->fetchAll("SELECT nama, slug FROM produk_kategori ORDER BY nama ASC"); } catch (Throwable $e) {}

function prod_q_omah($extra = []) {
  $q = array_merge(array_filter(['q'=>$_GET['q']??'','kategori'=>$_GET['kategori']??'']), $extra);
  return $q ? '?' . http_build_query($q) : '';
}

include theme_path('templates/layouts/header.php');
?>
<?php $flex_position = 'produk_top'; include theme_path('templates/partials/flex-content.php'); ?>

<div class="page-hero">
  <div class="page-hero-inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><span>Properti</span></div>
    <h1><?= htmlspecialchars(get_content('produk','hero_title','Katalog Properti')) ?></h1>
    <p><?= htmlspecialchars(get_content('produk','hero_desc','Temukan hunian impian Anda dari pilihan properti berkualitas kami.')) ?></p>
  </div>
</div>

<section class="section bg-white" style="padding-block:var(--sec-y);padding-inline:var(--pad-x);">
  <div style="max-width:var(--container-max);margin-inline:auto;">
    <!-- Search -->
    <form class="blog-search" method="get" action="<?= url('/produk') ?>">
      <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari properti…" aria-label="Cari">
      <button type="submit">Cari</button>
    </form>

    <?php if (!empty($kat_list)): ?>
    <div class="gal-filters" data-reveal>
      <a href="<?= url('/produk') ?>" class="gal-filter <?= !$kat_slug ? 'active' : '' ?>">Semua</a>
      <?php foreach ($kat_list as $c): if (!$c['slug']) continue; ?>
        <a href="<?= url('/produk?kategori='.htmlspecialchars($c['slug'])) ?>" class="gal-filter <?= $kat_slug === $c['slug'] ? 'active' : '' ?>"><?= htmlspecialchars($c['nama']) ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($products)): ?>
      <div class="empty-state">Belum ada properti<?= $search ? ' untuk "'.htmlspecialchars($search).'"' : '' ?>.</div>
    <?php else: ?>
    <div class="prod-grid" data-stagger>
      <?php foreach ($products as $p): ?>
      <a href="<?= url('/produk/'.$p['slug']) ?>" class="prod-card">
        <div class="prod-card-img">
          <?php if ($p['gambar_utama']): ?>
            <img src="<?= uploads_url($p['gambar_utama']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" loading="lazy">
          <?php else: ?>
            <div class="blog-card-noimg">🏠</div>
          <?php endif; ?>
          <?php if (!empty($p['badge'])): ?><span class="prod-badge-inline"><?= htmlspecialchars($p['badge']) ?></span><?php endif; ?>
        </div>
        <div class="prod-card-body">
          <h3 class="prod-card-title"><?= htmlspecialchars($p['nama']) ?></h3>
          <div class="prod-price">
            <?php if ($p['harga'] > 0): ?>
              <span class="now"><?= format_rupiah($p['harga']) ?></span>
            <?php else: ?>
              <span class="now">Hubungi Kami</span>
            <?php endif; ?>
            <?php if (!empty($p['harga_coret']) && $p['harga_coret'] > $p['harga']): ?>
              <span class="was"><?= format_rupiah($p['harga_coret']) ?></span>
            <?php endif; ?>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="pagination">
      <?php if ($current_page > 1): ?><a href="<?= url('/produk'.prod_q_omah(['p'=>$current_page-1])) ?>">‹</a><?php endif; ?>
      <?php for ($i=1; $i<=$total_pages; $i++): ?>
        <?php if ($i === $current_page): ?><span class="active"><?= $i ?></span><?php else: ?><a href="<?= url('/produk'.prod_q_omah(['p'=>$i])) ?>"><?= $i ?></a><?php endif; ?>
      <?php endfor; ?>
      <?php if ($current_page < $total_pages): ?><a href="<?= url('/produk'.prod_q_omah(['p'=>$current_page+1])) ?>">›</a><?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php $flex_position = 'produk_bottom'; include theme_path('templates/partials/flex-content.php'); ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
