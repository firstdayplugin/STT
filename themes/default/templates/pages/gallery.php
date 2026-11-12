<?php
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('gallery');
$seo = [
    'title'       => $page_seo['meta_title'] ?? seo_title('Gallery'),
    'description' => $page_seo['meta_description'] ?? 'Dokumentasi proyek-proyek terbaik kami dari berbagai layanan reklame yang telah dipercaya oleh klien.',
];

$filter_kat = trim($_GET['kat'] ?? '');
$kategori_list = $db->fetchAll("SELECT * FROM gallery_kategori ORDER BY nama ASC");

$where = '1=1'; $params = [];
if ($filter_kat) {
  $where .= ' AND gk.slug = ?'; $params[] = $filter_kat;
}

$items = $db->fetchAll(
  "SELECT g.*, gk.nama as kat_nama FROM gallery g 
   LEFT JOIN gallery_kategori gk ON g.kategori_id = gk.id 
   WHERE $where ORDER BY g.is_featured DESC, g.created_at DESC", $params
);

include theme_path('templates/layouts/header.php');
?>

<section class="page-hero-wrap">
  <?php include theme_path('templates/partials/navbar.php'); ?>
  <div class="page-hero">
  <div class="page-hero-breadcrumb">
    <a href="<?= url('/') ?>">Home</a>
    <span class="sep">/</span>
    <span>Gallery</span>
  </div>
  <h1 class="page-hero-title"><?= htmlspecialchars(get_content('gallery', 'hero_title', 'Gallery')) ?></h1>
  <p class="page-hero-desc"><?= htmlspecialchars(get_content('gallery', 'hero_desc', 'Dokumentasi proyek-proyek terbaik kami dari berbagai layanan reklame yang telah dipercaya oleh klien.')) ?></p>
</div>
</section>

<section class="gallery-section">
  <div class="container">
    <?php if (!empty($kategori_list)): ?>
    <div class="gallery-filters">
      <a href="<?= url('/gallery') ?>" class="gallery-filter <?= !$filter_kat ? 'active' : '' ?>">Semua</a>
      <?php foreach ($kategori_list as $kat): ?>
        <a href="<?= url('/gallery?kat='.$kat['slug']) ?>" 
           class="gallery-filter <?= $filter_kat === $kat['slug'] ? 'active' : '' ?>">
          <?= htmlspecialchars($kat['nama']) ?>
        </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <?php if (empty($items)): ?>
      <div style="text-align:center;padding:60px;color:var(--text-muted)">
        Belum ada foto di galeri.
      </div>
    <?php else: 
      $gal_initial = (int) get_setting('gallery_initial_load', '20');
      $gal_step    = (int) get_setting('gallery_load_step', '20');
      $total_items = count($items);
    ?>
    <div class="gallery-grid" id="gallery-grid" data-initial="<?= $gal_initial ?>" data-step="<?= $gal_step ?>">
      <?php foreach ($items as $idx => $item): ?>
      <div class="gallery-item<?= $idx < $gal_initial ? '' : ' gal-hidden' ?>">
        <img src="<?= uploads_url($item['gambar']) ?>" alt="<?= htmlspecialchars($item['judul']) ?>" loading="lazy">
      </div>
      <?php endforeach; ?>
    </div>
    <?php if ($total_items > $gal_initial): ?>
    <div class="gallery-loadmore-wrap" id="gallery-loadmore-wrap">
      <button type="button" id="gallery-loadmore" class="gallery-loadmore">
        <span class="loadmore-text">Load More</span>
        <span class="loadmore-count" id="loadmore-count">(<?= $gal_initial ?> / <?= $total_items ?>)</span>
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" style="margin-left:6px"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
      </button>
    </div>
    <script>
    (function(){
      const grid = document.getElementById('gallery-grid');
      const btn = document.getElementById('gallery-loadmore');
      const wrap = document.getElementById('gallery-loadmore-wrap');
      const countEl = document.getElementById('loadmore-count');
      const total = <?= $total_items ?>;
      const step = <?= $gal_step ?>;
      let shown = <?= $gal_initial ?>;
      btn.addEventListener('click', function(){
        const hidden = grid.querySelectorAll('.gal-hidden');
        let revealed = 0;
        for (const el of hidden) {
          if (revealed >= step) break;
          el.classList.remove('gal-hidden');
          el.classList.add('gal-fade-in');
          revealed++;
        }
        shown += revealed;
        countEl.textContent = '(' + Math.min(shown, total) + ' / ' + total + ')';
        if (shown >= total) wrap.style.display = 'none';
      });
    })();
    </script>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php include theme_path('templates/layouts/footer.php'); ?>
