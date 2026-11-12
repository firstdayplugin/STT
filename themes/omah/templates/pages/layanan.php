<?php
// Omah theme repurposes layanan as informational pages about property services
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('layanan');
$seo = ['title' => $page_seo['meta_title'] ?? seo_title('Layanan'), 'description' => $page_seo['meta_description'] ?? ''];
$layanan_list = $db->fetchAll("SELECT * FROM layanan WHERE is_active=1 ORDER BY urutan ASC");
include theme_path('templates/layouts/header.php');
?>
<?php $flex_position = 'layanan_top'; include theme_path('templates/partials/flex-content.php'); ?>

<div class="page-hero">
  <div class="page-hero-inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><span>Layanan</span></div>
    <h1><?= htmlspecialchars(get_content('layanan','hero_title','Layanan Kami')) ?></h1>
    <p><?= htmlspecialchars(get_content('layanan','hero_desc','Layanan lengkap untuk kebutuhan properti Anda.')) ?></p>
  </div>
</div>

<section style="padding-block:var(--sec-y);padding-inline:var(--pad-x);background:var(--om-white);">
  <div style="max-width:var(--container-max);margin-inline:auto;">
    <?php if (empty($layanan_list)): ?>
      <div class="empty-state">Belum ada layanan.</div>
    <?php else: ?>
    <div class="why-grid" data-stagger>
      <?php foreach ($layanan_list as $l): ?>
      <a href="<?= url('/layanan/'.$l['slug']) ?>" class="why-card" style="text-decoration:none;">
        <?php if ($l['gambar']): ?>
        <div style="width:100%;height:180px;border-radius:12px;overflow:hidden;">
          <img src="<?= uploads_url($l['gambar']) ?>" alt="<?= htmlspecialchars($l['nama']) ?>" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <?php else: ?>
        <div class="why-card-icon">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2c2c2c" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        </div>
        <?php endif; ?>
        <div class="why-card-body">
          <div class="why-card-title"><?= htmlspecialchars($l['nama']) ?></div>
          <?php if ($l['deskripsi_singkat'] ?? $l['short_description'] ?? ''): ?>
          <div class="why-card-desc"><?= htmlspecialchars(excerpt(strip_tags($l['deskripsi_singkat'] ?? $l['short_description'] ?? ''), 120)) ?></div>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php $flex_position = 'layanan_bottom'; include theme_path('templates/partials/flex-content.php'); ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
