<?php
if (!isset($db)) $db = Database::getInstance();

$page_seo = get_page_seo('layanan');
$seo = [
    'title' => $page_seo['meta_title'] ?? seo_title('Layanan'),
    'description' => $page_seo['meta_description'] ?? 'Layanan periklanan lengkap dari Reklamepedia.',
];

$layanan_list = $db->fetchAll("SELECT * FROM layanan WHERE is_active = 1 ORDER BY urutan ASC");

include theme_path('templates/layouts/header.php');
?>

<section class="page-hero-wrap">
  <?php include theme_path('templates/partials/navbar.php'); ?>
  <div class="page-hero">
    <div class="page-hero-breadcrumb">
      <a href="<?= url('/') ?>">Home</a>
      <span class="sep">/</span>
      <span>Services</span>
    </div>
    <h1 class="page-hero-title"><?= htmlspecialchars(get_content('layanan', 'hero_title', 'Services')) ?></h1>
    <p class="page-hero-desc">
      <?= htmlspecialchars(get_content('layanan', 'hero_desc', 'Kami menyediakan layanan lengkap mulai dari Neonbox, Billboard, Huruf Timbul, Pylon Sign, Neon LED Flex, hingga Branding Mobil.')) ?>
    </p>
  </div>
</section>

<?php $flex_position = 'layanan_top'; include theme_path('templates/partials/flex-content.php'); ?>

<section style="padding: 40px 0">
  <div class="services-main">
    <div class="services-main-header">
      <div>
        <div class="section-label light"><?= htmlspecialchars(get_content('layanan', 'services_label', 'Our Performance')) ?></div>
        <h2 class="section-title light"><?= nl2br(htmlspecialchars(get_content('layanan', 'services_title', "Professional\nAdvertising\n& Signage Services"))) ?></h2>
      </div>
      <div style="border-bottom: 2px solid rgba(255,255,255,0.15); height: 1px"></div>
    </div>
    <div class="services-grid-light">
      <?php foreach ($layanan_list as $i => $l): ?>
      <a href="<?= url('/layanan/'.$l['slug']) ?>" class="service-card-light">
        <div class="service-card-num"><?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?>.</div>
        <h3 class="service-card-title"><?= htmlspecialchars($l['nama']) ?></h3>
        <p class="service-card-desc">
          <?= htmlspecialchars($l['deskripsi_pendek'] ?: 'Select the flexible or premium plan that suits your business needs') ?>
        </p>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="why-section">
  <div class="container">
    <div class="why-header">
      <div class="section-label" style="justify-content:center"><?= htmlspecialchars(get_content('layanan', 'why_label', 'Why Choose Us')) ?></div>
      <h2 class="section-title"><?= nl2br(htmlspecialchars(get_content('layanan', 'why_title', "Where\nQuality Meets\nReliability"))) ?></h2>
      <p class="section-desc" style="margin: 16px auto 0">
        <?= htmlspecialchars(get_content('layanan', 'why_desc', 'Setiap proyek dirancang untuk memperkuat visibilitas brand dan bertahan dalam jangka panjang.')) ?>
      </p>
    </div>
    <div class="why-grid">
      <div class="why-card" style="background-image:url('<?= uploads_url(get_setting('about_foto_1', 'settings/about-01.webp')) ?>')">
        <div class="why-card-text"><?= htmlspecialchars(get_content('layanan', 'why_card1', 'Kami berpengalaman lebih dari 15 tahun di bidang advertising dan telah dipercaya ratusan klien dengan tingkat kepuasan yang tinggi.')) ?></div>
      </div>
      <div class="why-card" style="background-image:url('<?= uploads_url(get_setting('about_foto_2', 'settings/about-02.webp')) ?>')">
        <div class="why-card-text"><?= htmlspecialchars(get_content('layanan', 'why_card2', 'Didukung tim ahli berpengalaman, setiap proyek dikerjakan secara profesional, tepat waktu, dan dengan presisi tinggi.')) ?></div>
      </div>
      <div class="why-card" style="background-image:url('<?= uploads_url(get_setting('about_foto_3', 'settings/about-03.webp')) ?>')">
        <div class="why-card-text"><?= htmlspecialchars(get_content('layanan', 'why_card3', 'Setiap produk dilengkapi garansi serta layanan purna jual sebagai bentuk komitmen terhadap kualitas dan kepercayaan pelanggan.')) ?></div>
      </div>
    </div>
  </div>
</section>

<?php $flex_position = 'layanan_bottom'; include theme_path('templates/partials/flex-content.php'); ?>

<?php include theme_path('templates/layouts/footer.php'); ?>
