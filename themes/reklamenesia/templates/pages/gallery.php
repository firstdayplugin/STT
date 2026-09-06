<?php
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('gallery');
$seo = [
  'title'       => $page_seo['meta_title']       ?? seo_title('Gallery'),
  'description' => $page_seo['meta_description'] ?? 'Dokumentasi proyek reklame terbaik kami.',
];
$filter_kat = trim($_GET['kat'] ?? '');
$kategori_list = $db->fetchAll("SELECT * FROM gallery_kategori ORDER BY nama ASC");
$where = '1=1'; $params = [];
if ($filter_kat) { $where .= ' AND gk.slug = ?'; $params[] = $filter_kat; }
$items = $db->fetchAll(
  "SELECT g.*, gk.nama as kat_nama FROM gallery g
   LEFT JOIN gallery_kategori gk ON g.kategori_id = gk.id
   WHERE $where ORDER BY g.is_featured DESC, g.created_at DESC", $params);
include theme_path('templates/layouts/header.php');
?>

<section class="page-hero">
  <div class="inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><span>Gallery</span></div>
    <h1><?= htmlspecialchars(get_content('gallery', 'hero_title', 'Galeri Proyek')) ?></h1>
    <p><?= htmlspecialchars(get_content('gallery', 'hero_desc', 'Dokumentasi proyek-proyek terbaik kami dari berbagai layanan reklame yang telah dipercaya klien.')) ?></p>
  </div>
</section>

<section class="section bg-white">
  <div class="container">
    <?php if (!empty($kategori_list)): ?>
    <div class="gal-filters" data-reveal>
      <a href="<?= url('/gallery') ?>" class="gal-filter <?= !$filter_kat ? 'active' : '' ?>">Semua</a>
      <?php foreach ($kategori_list as $kat): ?>
        <a href="<?= url('/gallery?kat='.($kat['slug'] ?? '')) ?>" class="gal-filter <?= $filter_kat === ($kat['slug'] ?? '') ? 'active' : '' ?>"><?= htmlspecialchars($kat['nama'] ?? '') ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
      <div class="empty-state">Belum ada foto di galeri.</div>
    <?php else:
      $gal_initial = (int) get_setting('gallery_initial_load', '20');
      $gal_step    = (int) get_setting('gallery_load_step', '20');
      $total_items = count($items);
    ?>
    <div class="gal-grid" id="gal-grid" data-reveal>
      <?php foreach ($items as $idx => $item): ?>
      <div class="gal-item<?= $idx < $gal_initial ? '' : ' gal-hidden' ?>">
        <img src="<?= uploads_url($item['gambar']) ?>" alt="<?= htmlspecialchars($item['judul']) ?>" loading="lazy">
      </div>
      <?php endforeach; ?>
    </div>
    <?php if ($total_items > $gal_initial): ?>
    <div class="gal-loadmore-wrap" id="gal-lm-wrap">
      <button type="button" id="gal-lm" class="gal-loadmore">
        Load More <span id="gal-count">(<?= $gal_initial ?> / <?= $total_items ?>)</span>
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
      </button>
    </div>
    <script>
    (function(){
      var grid=document.getElementById('gal-grid'),btn=document.getElementById('gal-lm'),wrap=document.getElementById('gal-lm-wrap'),c=document.getElementById('gal-count');
      var total=<?= $total_items ?>,step=<?= $gal_step ?>,shown=<?= $gal_initial ?>;
      btn.addEventListener('click',function(){
        var hidden=grid.querySelectorAll('.gal-hidden'),r=0;
        for(var i=0;i<hidden.length && r<step;i++){hidden[i].classList.remove('gal-hidden');hidden[i].classList.add('gal-fade-in');r++;}
        shown+=r;c.textContent='('+Math.min(shown,total)+' / '+total+')';
        if(shown>=total)wrap.style.display='none';
      });
    })();
    </script>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php include theme_path('templates/layouts/footer.php'); ?>
