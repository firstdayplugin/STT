<?php
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('gallery');
$seo = ['title' => $page_seo['meta_title'] ?? seo_title('Galeri'), 'description' => $page_seo['meta_description'] ?? ''];

$kat_slug = trim($_GET['kategori'] ?? '');
$where = ['g.status IS NULL OR g.status != "hidden"']; $params = [];
if ($kat_slug) {
  try {
    $kat = $db->fetchOne("SELECT id FROM gallery_kategori WHERE slug=?", [$kat_slug]);
    if ($kat) { $where[] = 'g.kategori_id=?'; $params[] = $kat['id']; }
  } catch (Throwable $e) {}
}
$photos = $db->fetchAll("SELECT * FROM gallery g WHERE ".implode(' AND ',$where)." ORDER BY g.is_featured DESC, g.created_at DESC", $params);
$kats   = []; try { $kats = $db->fetchAll("SELECT * FROM gallery_kategori ORDER BY nama ASC"); } catch (Throwable $e) {}
include theme_path('templates/layouts/header.php');
?>

<div class="page-hero">
  <div class="page-hero-inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><span>Galeri</span></div>
    <h1><?= htmlspecialchars(get_content('gallery','hero_title','Galeri Properti')) ?></h1>
  </div>
</div>

<section style="padding-block:var(--sec-y);padding-inline:var(--pad-x);background:var(--om-white);">
  <div style="max-width:var(--container-max);margin-inline:auto;">
    <?php if (!empty($kats)): ?>
    <div class="gal-filters" style="margin-bottom:32px;" data-reveal>
      <a href="<?= url('/galeri') ?>" class="gal-filter <?= !$kat_slug?'active':'' ?>">Semua</a>
      <?php foreach ($kats as $k): if (!$k['slug']) continue; ?>
        <a href="<?= url('/galeri?kategori='.htmlspecialchars($k['slug'])) ?>" class="gal-filter <?= $kat_slug===$k['slug']?'active':'' ?>"><?= htmlspecialchars($k['nama']) ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php if (empty($photos)): ?>
      <div class="empty-state">Belum ada foto.</div>
    <?php else: ?>
    <div class="gal-grid" data-stagger>
      <?php foreach ($photos as $ph): ?>
      <div class="gal-item">
        <img src="<?= uploads_url($ph['gambar']) ?>" alt="<?= htmlspecialchars($ph['judul'] ?? '') ?>" loading="lazy">
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include theme_path('templates/layouts/footer.php'); ?>
