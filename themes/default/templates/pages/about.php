<?php
if (!isset($db)) $db = Database::getInstance();

$page_seo = get_page_seo('about');
$seo = [
    'title'       => $page_seo['meta_title']       ?? seo_title('Tentang Kami'),
    'description' => $page_seo['meta_description'] ?? get_setting('meta_desc_default'),
];

$klien_logos = $db->fetchAll("SELECT * FROM klien_logo WHERE is_active = 1 ORDER BY urutan ASC LIMIT 6");
$about_1 = get_setting('about_foto_1', 'settings/about-01.webp');
$about_2 = get_setting('about_foto_2', 'settings/about-02.webp');
$about_3 = get_setting('about_foto_3', 'settings/about-03.webp');

include theme_path('templates/layouts/header.php');
?>

<section class="page-hero-wrap">
  <?php include theme_path('templates/partials/navbar.php'); ?>
  <div class="page-hero">
    <div class="page-hero-breadcrumb">
      <a href="<?= url('/') ?>">Home</a>
      <span class="sep">/</span>
      <span>About Us</span>
    </div>
    <h1 class="page-hero-title"><?= htmlspecialchars(get_content('about', 'hero_title', 'About Us')) ?></h1>
    <p class="page-hero-desc">
      <?= htmlspecialchars(get_content('about', 'hero_desc', 'Bangun visibilitas dan perkuat branding bisnis Anda melalui solusi reklame yang strategis dan berdampak. Lebih dari 1.200 klien di seluruh Indonesia telah mempercayakan kebutuhan advertising mereka kepada kami.')) ?>
    </p>
  </div>
</section>

<?php $flex_position = 'about_top'; include theme_path('templates/partials/flex-content.php'); ?>

<section class="advantage-section">
  <div class="container">
    <div class="advantage-header">
      <div class="section-label" style="justify-content:center"><?= htmlspecialchars(get_content('about', 'adv_label', 'Our Service Commitment')) ?></div>
      <h2 class="section-title"><?= nl2br(htmlspecialchars(get_content('about', 'adv_title', "The Advantage of\nWorking With Us"))) ?></h2>
      <p class="section-desc" style="margin: 16px auto 0">
        <?= htmlspecialchars(get_content('about', 'adv_desc', 'Kami memastikan setiap proyek reklame berjalan optimal dan sesuai ekspektasi Anda.')) ?>
      </p>
    </div>
    
    <div class="advantage-grid">
      <!-- Col 1, Row 1: cream Free Consultation -->
      <div class="advantage-card cream">
        <h3 class="advantage-title"><?= nl2br(htmlspecialchars(get_content('about', 'adv_card1_title', 'Free Consultation & Site Survey'))) ?></h3>
        <p class="advantage-desc"><?= htmlspecialchars(get_content('about', 'adv_card1_desc', 'Konsultasi dan survey lokasi gratis untuk memastikan solusi dan penempatan reklame yang tepat.')) ?></p>
      </div>
      <!-- Col 2, spans both rows: dark Quality Assurance (text at bottom) -->
      <div class="advantage-card image span-2-rows" style="background-image:url('<?= uploads_url($about_2) ?>')">
        <h3 class="advantage-title"><?= nl2br(htmlspecialchars(get_content('about', 'adv_card2_title', 'Quality Assurance & Warranty'))) ?></h3>
        <p class="advantage-desc"><?= htmlspecialchars(get_content('about', 'adv_card2_desc', 'Setiap produk dilengkapi jaminan kualitas untuk memastikan kepuasan dan kepercayaan pelanggan.')) ?></p>
      </div>
      <!-- Col 3, Row 1: image only -->
      <div class="advantage-card image" style="background-image:url('<?= uploads_url($about_3) ?>')"></div>
      <!-- Col 1, Row 2: image only -->
      <div class="advantage-card image" style="background-image:url('<?= uploads_url($about_1) ?>')"></div>
      <!-- Col 3, Row 2: cream Experienced Team -->
      <div class="advantage-card cream">
        <h3 class="advantage-title"><?= nl2br(htmlspecialchars(get_content('about', 'adv_card3_title', 'Experienced Professional Team'))) ?></h3>
        <p class="advantage-desc"><?= htmlspecialchars(get_content('about', 'adv_card3_desc', 'Dikerjakan oleh tenaga ahli berpengalaman dengan hasil rapi, presisi, dan berkualitas.')) ?></p>
      </div>
    </div>
  </div>
</section>

<?php include theme_path('templates/partials/logo-carousel.php'); ?>

<?php $flex_position = 'about_bottom'; include theme_path('templates/partials/flex-content.php'); ?>

<?php include theme_path('templates/layouts/footer.php'); ?>
