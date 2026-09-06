<?php
if (!isset($db)) $db = Database::getInstance();
if (empty($layanan_data)) { http_response_code(404); require theme_path('templates/pages/404.php'); exit; }

$seo = [
  'title'       => seo_title($layanan_data['meta_title'] ?: $layanan_data['nama']),
  'description' => $layanan_data['meta_description'] ?: $layanan_data['deskripsi_pendek'],
  'image'       => $layanan_data['gambar'] ? uploads_url($layanan_data['gambar']) : '',
];

$sub_layanan = $db->fetchAll("SELECT * FROM layanan_sub WHERE layanan_id = ? ORDER BY urutan", [$layanan_data['id']]);
try {
    $faq_limit_detail = max(1, (int) get_setting('faq_limit_detail', '100'));
    $faqs_detail = $db->fetchAll(
        "SELECT f.* FROM faq f
         JOIN faq_layanan_rel r ON r.faq_id = f.id
         WHERE r.layanan_id = ? AND f.is_active = 1
         ORDER BY f.urutan
         LIMIT $faq_limit_detail", [$layanan_data['id']]
    );
} catch (Throwable $e) { $faqs_detail = []; }
// Logo carousel auto-display config for service pages
$lc_show_services = get_setting('logo_carousel_show_services', '0') === '1';
$lc_position = get_setting('logo_carousel_position', 'before_footer'); // after_hero | middle | before_footer
$lc_compact_render = function() {
    $GLOBALS['carousel_compact'] = true;
    include theme_path('templates/partials/logo-carousel.php');
};
$kat_slug = $layanan_data['slug'];
$gallery_items = $db->fetchAll(
  "SELECT g.* FROM gallery g 
   JOIN gallery_kategori gk ON g.kategori_id = gk.id 
   WHERE gk.slug = ? ORDER BY g.urutan ASC LIMIT 9", [$kat_slug]
);

$wa_link = wa_url(get_setting('wa_number'), 'Halo, saya ingin konsultasi mengenai layanan ' . $layanan_data['nama'] . '.');

// Editable per-layanan elements (all fallback to defaults if not set)
$tagline = $layanan_data['tagline'] ?: $layanan_data['deskripsi_pendek'];
$sec_types_title = $layanan_data['section_types_title'] ?: ('Layanan ' . $layanan_data['nama'] . ' Berkualitas & Terjangkau');
$sec_types_desc  = $layanan_data['section_types_desc']  ?: 'Diproduksi dengan material pilihan dan pengerjaan yang presisi untuk memastikan daya tahan serta tampilan maksimal.';
$sec_gal_title = $layanan_data['section_gallery_title'] ?: ('Galeri Proyek ' . $layanan_data['nama']);
$sec_gal_desc  = $layanan_data['section_gallery_desc']  ?: ('Dokumentasi hasil dan proses produksi ' . $layanan_data['nama'] . ' yang telah kami kerjakan.');
$consult_title = $layanan_data['consult_title'] ?: 'Maksimalkan Budget, Optimalkan Hasil';
$consult_desc  = $layanan_data['consult_desc']  ?: 'Diskusikan kebutuhan dan anggaran Anda bersama tim kami. Kami akan membantu merekomendasikan solusi terbaik agar proyek tetap efisien dan berkualitas.';
$footer_img    = $layanan_data['gambar_footer'] ?: ($layanan_data['gambar'] ?: 'settings/about-01.webp');

$current_layanan_id = $layanan_data['id'];
include theme_path('templates/layouts/header.php');
?>

<section class="page-hero-wrap">
  <?php include theme_path('templates/partials/navbar.php'); ?>
  <div class="page-hero">
    <div class="page-hero-breadcrumb">
      <a href="<?= url('/') ?>">Home</a>
      <span class="sep">/</span>
      <a href="<?= url('/layanan') ?>">Services</a>
      <span class="sep">/</span>
      <span><?= htmlspecialchars($layanan_data['nama']) ?></span>
    </div>
    <h1 class="page-hero-title"><?= htmlspecialchars($layanan_data['nama']) ?></h1>
    <p class="page-hero-desc"><?= htmlspecialchars($tagline) ?></p>
  </div>
</section>

<?php $flex_position = 'layanan_detail_top'; include theme_path('templates/partials/flex-content.php'); ?>

<?php if ($lc_show_services && $lc_position === 'after_hero') { $carousel_compact = true; include theme_path('templates/partials/logo-carousel.php'); } ?>

<?php if (!empty($sub_layanan)): ?>
<section class="service-types-section">
  <div class="container">
    <div class="service-types-header">
      <div class="section-label" style="justify-content:center">Our Services</div>
      <h2 class="section-title"><?= nl2br(htmlspecialchars($sec_types_title)) ?></h2>
      <p class="section-desc" style="margin: 16px auto 0"><?= htmlspecialchars($sec_types_desc) ?></p>
    </div>
    
    <div class="service-types-grid">
      <?php foreach ($sub_layanan as $idx => $sub): 
        $sub_img = $sub['gambar'] ?? ''; // manual photo per sub-layanan (admin-selected)
      ?>
      <div class="service-type-card">
        <div class="service-type-img">
          <?php if ($sub_img): ?>
            <img src="<?= uploads_url($sub_img) ?>" alt="<?= htmlspecialchars($sub['nama']) ?>">
          <?php else: ?>
            <div style="background:var(--bg-cream-2);width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:48px;opacity:0.3"><?= $sub['icon'] ?: '🎨' ?></div>
          <?php endif; ?>
        </div>
        <div class="service-type-body">
          <div class="service-type-title"><?= htmlspecialchars($sub['nama']) ?></div>
          <div class="service-type-desc"><?= htmlspecialchars($sub['deskripsi']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($gallery_items)): ?>
<section style="padding: 20px 0">
  <div class="showcase-section">
    <div class="showcase-header">
      <div class="section-label light" style="justify-content:center">Project Showcase</div>
      <h2 class="section-title light"><?= nl2br(htmlspecialchars($sec_gal_title)) ?></h2>
      <p style="color: rgba(255,255,255,0.6); margin-top: 16px; max-width: 460px; margin-left: auto; margin-right: auto">
        <?= htmlspecialchars($sec_gal_desc) ?>
      </p>
    </div>
    <div class="showcase-grid">
      <?php foreach (array_slice($gallery_items, 0, 9) as $item): ?>
      <div class="showcase-item">
        <img src="<?= uploads_url($item['gambar']) ?>" alt="<?= htmlspecialchars($item['judul']) ?>">
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($faqs_detail)): ?>
<section style="padding:80px 24px;background:var(--bg-cream)">
  <div class="container" style="max-width:900px;margin:0 auto">
    <div style="text-align:center;margin-bottom:40px">
      <div class="section-label" style="justify-content:center">FAQ</div>
      <h2 class="section-title">Pertanyaan Seputar <?= htmlspecialchars($layanan_data['nama']) ?></h2>
    </div>
    <div class="faq-list">
      <?php foreach ($faqs_detail as $i => $f): ?>
        <div class="faq-item <?= $i === 0 ? 'active' : '' ?>">
          <div class="faq-question">
            <span><?= htmlspecialchars($f['pertanyaan']) ?></span>
            <span class="faq-icon">+</span>
          </div>
          <div class="faq-answer"><?= htmlspecialchars($f['jawaban']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FAQ JSON-LD Schema for SEO -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    <?php foreach ($faqs_detail as $i => $f): ?>
    {
      "@type": "Question",
      "name": <?= json_encode($f['pertanyaan'], JSON_UNESCAPED_UNICODE) ?>,
      "acceptedAnswer": {
        "@type": "Answer",
        "text": <?= json_encode($f['jawaban'], JSON_UNESCAPED_UNICODE) ?>
      }
    }<?= $i < count($faqs_detail) - 1 ? ',' : '' ?>
    <?php endforeach; ?>
  ]
}
</script>
<?php endif; ?>

<?php $flex_position = 'layanan_detail_middle'; include theme_path('templates/partials/flex-content.php'); ?>

<?php if ($lc_show_services && $lc_position === 'middle') { $carousel_compact = true; include theme_path('templates/partials/logo-carousel.php'); } ?>

<?php if (get_setting('testimonial_show_services','0') === '1') { include theme_path('templates/partials/testimonial-carousel.php'); } ?>

<section class="consult-section">
  <div class="container">
    <div class="consult-grid">
      <div class="consult-text">
        <div class="section-label">Consult With Us</div>
        <h2 class="consult-title"><?= nl2br(htmlspecialchars($consult_title)) ?></h2>
        <p class="consult-desc"><?= htmlspecialchars($consult_desc) ?></p>
        <a href="<?= $wa_link ?>" target="_blank" class="consult-cta">
          <span class="arrow-circle">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
          </span>
          Hubungi Kami!
        </a>
      </div>
      <div class="consult-images">
        <?php 
        $consult_imgs = array_slice($gallery_items, 0, 2);
        // Image 1: layanan's footer image (or main image)
        ?>
        <div class="consult-img-item">
          <img src="<?= uploads_url($footer_img) ?>" alt="">
        </div>
        <?php for ($i = 0; $i < 2; $i++): 
          $img_src = $consult_imgs[$i]['gambar'] ?? ('settings/about-0'.(($i%3)+1).'.webp');
        ?>
          <div class="consult-img-item">
            <img src="<?= uploads_url($img_src) ?>" alt="">
          </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</section>

<?php $flex_position = 'layanan_detail_bottom'; include theme_path('templates/partials/flex-content.php'); ?>

<?php if ($lc_show_services && $lc_position === 'before_footer') { $carousel_compact = true; include theme_path('templates/partials/logo-carousel.php'); } ?>

<?php include theme_path('templates/layouts/footer.php'); ?>
