<?php
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('about');
$seo = [
  'title'       => $page_seo['meta_title']       ?? seo_title('Tentang Kami'),
  'description' => $page_seo['meta_description'] ?? get_setting('meta_desc_default'),
];
$about_1 = get_setting('about_foto_1', '');
$about_2 = get_setting('about_foto_2', '');
$about_3 = get_setting('about_foto_3', '');
include theme_path('templates/layouts/header.php');
?>

<section class="page-hero">
  <div class="inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><span>About Us</span></div>
    <h1><?= htmlspecialchars(get_content('about', 'hero_title', 'Tentang Reklamenesia')) ?></h1>
    <p><?= htmlspecialchars(get_content('about', 'hero_desc', 'Lebih dari 15 tahun menghadirkan solusi reklame berkualitas untuk memperkuat identitas visual brand di seluruh Indonesia.')) ?></p>
  </div>
</section>

<?php $flex_position = 'about_top'; include theme_path('templates/partials/flex-content.php'); ?>

<section class="section bg-white">
  <div class="container">
    <div class="sec-head center" data-reveal>
      <span class="eyebrow center"><?= htmlspecialchars(get_content('about', 'adv_label', 'Our Service Commitment')) ?></span>
      <h2><?= nl2br(htmlspecialchars(get_content('about', 'adv_title', 'The Advantage of Working With Us'))) ?></h2>
      <p class="lead" style="margin-inline:auto"><?= htmlspecialchars(get_content('about', 'adv_desc', 'Kami memastikan setiap proyek reklame berjalan optimal dan sesuai ekspektasi Anda.')) ?></p>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:48px" data-stagger class="about-adv">
      <div class="stat" style="border-radius:var(--r-lg);min-height:220px;background:var(--rk-cream);justify-content:flex-end">
        <h3 style="font-size:20px"><?= nl2br(htmlspecialchars(get_content('about', 'adv_card1_title', 'Free Consultation & Site Survey'))) ?></h3>
        <p class="testi-role" style="margin-top:8px;font-size:14px;line-height:1.6"><?= htmlspecialchars(get_content('about', 'adv_card1_desc', 'Konsultasi dan survey lokasi gratis untuk memastikan solusi dan penempatan reklame yang tepat.')) ?></p>
      </div>
      <div class="svc-card" style="aspect-ratio:auto;min-height:220px;<?= $about_2 ? '' : 'background:var(--rk-ink-3)' ?>">
        <?php if ($about_2): ?><img src="<?= uploads_url($about_2) ?>" alt=""><?php endif; ?>
        <div class="svc-card-meta"><div><span class="svc-card-title" style="font-size:20px"><?= htmlspecialchars(get_content('about', 'adv_card2_title', 'Quality Assurance & Warranty')) ?></span></div></div>
      </div>
      <div class="stat" style="border-radius:var(--r-lg);min-height:220px;background:var(--rk-cream);justify-content:flex-end">
        <h3 style="font-size:20px"><?= nl2br(htmlspecialchars(get_content('about', 'adv_card3_title', 'Experienced Professional Team'))) ?></h3>
        <p class="testi-role" style="margin-top:8px;font-size:14px;line-height:1.6"><?= htmlspecialchars(get_content('about', 'adv_card3_desc', 'Dikerjakan oleh tenaga ahli berpengalaman dengan hasil rapi, presisi, dan berkualitas.')) ?></p>
      </div>
    </div>
  </div>
</section>

<?php include theme_path('templates/partials/logo-carousel.php'); ?>
<?php include theme_path('templates/partials/testimonial-carousel.php'); ?>
<?php $flex_position = 'about_bottom'; include theme_path('templates/partials/flex-content.php'); ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
