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
    "SELECT f.* FROM faq f JOIN faq_layanan_rel r ON r.faq_id = f.id
     WHERE r.layanan_id = ? AND f.is_active = 1 ORDER BY f.urutan LIMIT $faq_limit_detail", [$layanan_data['id']]);
} catch (Throwable $e) { $faqs_detail = []; }

$kat_slug = $layanan_data['slug'];
$gallery_items = $db->fetchAll(
  "SELECT g.* FROM gallery g JOIN gallery_kategori gk ON g.kategori_id = gk.id
   WHERE gk.slug = ? ORDER BY g.urutan ASC LIMIT 9", [$kat_slug]);

$wa_link = wa_url(get_setting('wa_number'), 'Halo, saya ingin konsultasi mengenai layanan ' . $layanan_data['nama'] . '.');

$tagline        = $layanan_data['tagline'] ?: $layanan_data['deskripsi_pendek'];
$sec_types_title= $layanan_data['section_types_title'] ?: ('Layanan ' . $layanan_data['nama'] . ' Berkualitas & Terjangkau');
$sec_types_desc = $layanan_data['section_types_desc']  ?: 'Diproduksi dengan material pilihan dan pengerjaan presisi untuk memastikan daya tahan serta tampilan maksimal.';
$sec_gal_title  = $layanan_data['section_gallery_title'] ?: ('Galeri Proyek ' . $layanan_data['nama']);
$sec_gal_desc   = $layanan_data['section_gallery_desc']  ?: ('Dokumentasi hasil dan proses produksi ' . $layanan_data['nama'] . ' yang telah kami kerjakan.');
$consult_title  = $layanan_data['consult_title'] ?: 'Maksimalkan Budget, Optimalkan Hasil';
$consult_desc   = $layanan_data['consult_desc']  ?: 'Diskusikan kebutuhan dan anggaran Anda bersama tim kami untuk solusi terbaik yang efisien dan berkualitas.';
$footer_img     = $layanan_data['gambar_footer'] ?: ($layanan_data['gambar'] ?: '');

$lc_show_services = get_setting('logo_carousel_show_services', '0') === '1';
$lc_position = get_setting('logo_carousel_position', 'before_footer');
$current_layanan_id = $layanan_data['id'];
$navbar_dark = true; // red hero → transparent white navbar, solid on scroll
include theme_path('templates/layouts/header.php');
?>

<section class="detail-hero">
  <div class="container" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><a href="<?= url('/layanan') ?>">Services</a><span class="sep">/</span><span><?= htmlspecialchars($layanan_data['nama']) ?></span></div>
    <h1><?= htmlspecialchars($layanan_data['nama']) ?></h1>
    <?php if ($tagline): ?><p><?= htmlspecialchars($tagline) ?></p><?php endif; ?>
    <div class="detail-hero-actions"><a href="<?= $wa_link ?>" target="_blank" rel="noopener" class="btn light"><span class="ic"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>Konsultasi Sekarang</a></div>
  </div>
</section>

<?php if (!empty($layanan_data['gambar'])): ?>
<div class="container">
  <div class="detail-figure" data-reveal="scale"><img src="<?= uploads_url($layanan_data['gambar']) ?>" alt="<?= htmlspecialchars($layanan_data['nama']) ?>"></div>
</div>
<?php endif; ?>

<?php if (!empty($layanan_data['deskripsi'])): ?>
<section class="section bg-white">
  <div class="container" style="max-width:820px">
    <div class="prose" data-reveal><?= $layanan_data['deskripsi'] ?></div>
  </div>
</section>
<?php endif; ?>

<?php $flex_position = 'layanan_detail_top'; include theme_path('templates/partials/flex-content.php'); ?>
<?php if ($lc_show_services && $lc_position === 'after_hero') { $carousel_compact = true; include theme_path('templates/partials/logo-carousel.php'); } ?>

<?php if (!empty($sub_layanan)): ?>
<section class="section bg-white">
  <div class="container">
    <div class="sec-head center" data-reveal>
      <span class="eyebrow center">Our Services</span>
      <h2><?= nl2br(htmlspecialchars($sec_types_title)) ?></h2>
      <p class="lead" style="margin-inline:auto"><?= htmlspecialchars($sec_types_desc) ?></p>
    </div>
    <div class="var-list" data-stagger>
      <?php foreach ($sub_layanan as $sub): ?>
      <div class="var-row">
        <div class="var-text">
          <h3><?= htmlspecialchars($sub['nama']) ?></h3>
          <?php if (!empty($sub['deskripsi'])): ?><p><?= htmlspecialchars($sub['deskripsi']) ?></p><?php endif; ?>
        </div>
        <?php if (!empty($sub['gambar'])): ?>
          <div class="var-img"><img src="<?= uploads_url($sub['gambar']) ?>" alt="<?= htmlspecialchars($sub['nama']) ?>" loading="lazy"></div>
        <?php else: ?>
          <div class="var-img noimg"><?= $sub['icon'] ?: '✳' ?></div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($gallery_items)): ?>
<section class="services" style="background:var(--rk-paper)">
  <div class="services-watermark" aria-hidden="true">GALLERY</div>
  <div class="container">
    <div class="sec-head center" data-reveal>
      <span class="eyebrow center">Project Showcase</span>
      <h2><?= nl2br(htmlspecialchars($sec_gal_title)) ?></h2>
      <p class="lead" style="margin-inline:auto"><?= htmlspecialchars($sec_gal_desc) ?></p>
    </div>
    <div class="gal-grid" style="margin-top:48px" data-reveal>
      <?php foreach ($gallery_items as $item): ?>
        <div class="gal-item"><img src="<?= uploads_url($item['gambar']) ?>" alt="<?= htmlspecialchars($item['judul']) ?>" loading="lazy"></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($faqs_detail)): ?>
<section class="section bg-white">
  <div class="container">
    <div class="faq-grid">
      <div class="faq-aside" data-reveal="left">
        <span class="eyebrow">FAQ</span>
        <h2>Pertanyaan Seputar <?= htmlspecialchars($layanan_data['nama']) ?></h2>
        <a href="<?= $wa_link ?>" target="_blank" rel="noopener" class="btn"><span class="ic"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>Tanya Kami</a>
      </div>
      <div class="faq-list" data-reveal="right">
        <?php foreach ($faqs_detail as $i => $f): ?>
        <div class="faq-item<?= $i === 0 ? ' active' : '' ?>">
          <div class="faq-q"><span class="faq-n">{<?= $i+1 ?>}</span><span class="txt"><?= htmlspecialchars($f['pertanyaan']) ?></span><span class="faq-ic"></span></div>
          <div class="faq-a"><div class="faq-a-inner"><?= nl2br(htmlspecialchars($f['jawaban'])) ?></div></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"FAQPage","mainEntity":[<?php foreach ($faqs_detail as $i => $f): ?>{"@type":"Question","name":<?= json_encode($f['pertanyaan'], JSON_UNESCAPED_UNICODE) ?>,"acceptedAnswer":{"@type":"Answer","text":<?= json_encode($f['jawaban'], JSON_UNESCAPED_UNICODE) ?>}}<?= $i < count($faqs_detail)-1 ? ',' : '' ?><?php endforeach; ?>]}
</script>
<?php endif; ?>

<?php $flex_position = 'layanan_detail_middle'; include theme_path('templates/partials/flex-content.php'); ?>
<?php if ($lc_show_services && $lc_position === 'middle') { $carousel_compact = true; include theme_path('templates/partials/logo-carousel.php'); } ?>
<?php if (get_setting('testimonial_show_services','0') === '1') { include theme_path('templates/partials/testimonial-carousel.php'); } ?>

<!-- Consult -->
<section class="section bg-cream">
  <div class="container">
    <div class="about-grid">
      <div data-reveal="left">
        <span class="eyebrow">Consult With Us</span>
        <h2 style="font-size:var(--fs-h2);margin-top:16px"><?= nl2br(htmlspecialchars($consult_title)) ?></h2>
        <p class="lead" style="margin-top:18px"><?= htmlspecialchars($consult_desc) ?></p>
        <a href="<?= $wa_link ?>" target="_blank" rel="noopener" class="btn red" style="margin-top:28px"><span class="ic"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>Hubungi Kami!</a>
      </div>
      <div data-reveal="right">
        <div class="svc-card" style="aspect-ratio:4/3;min-height:280px;<?= $footer_img ? '' : 'background:var(--rk-ink-3)' ?>">
          <?php if ($footer_img): ?><img src="<?= uploads_url($footer_img) ?>" alt=""><?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php $flex_position = 'layanan_detail_bottom'; include theme_path('templates/partials/flex-content.php'); ?>
<?php if ($lc_show_services && $lc_position === 'before_footer') { $carousel_compact = true; include theme_path('templates/partials/logo-carousel.php'); } ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
