<?php
/* ============================================================
   OMAH THEME — Homepage v1.3 FINAL
   - No hardcoded text (all from admin)
   - No "Kenapa Memilih Kami" section (use flex_blocks/grid_icon_box instead)
   - CTA: single #2c2c2c color, connected to global admin
   - Fasilitas from adv_card fields
   - About: foto from about_foto_1, no hardcoded titles
   ============================================================ */
if (!isset($db)) $db = Database::getInstance();

$page_seo = get_page_seo('home');
$seo = [
  'title'       => $page_seo['meta_title']       ?? get_setting('meta_title_default', get_setting('site_name','DIFA Property')),
  'description' => $page_seo['meta_description'] ?? get_setting('meta_desc_default',''),
  'image'       => $page_seo['og_image']         ?? '',
];

/* ---- Hero — from admin settings ---- */
$hero_judul    = get_setting('hero_judul', '');
$hero_subtitle = get_setting('hero_subtitle', '');
$hero_cta_text = get_setting('hero_cta_text', 'Konsultasi Gratis');
$hero_cta_url  = get_setting('hero_cta_url', '');
$hero_image    = get_setting('hero_gambar', '');
$wa_number     = get_setting('wa_number');
$wa_link       = $wa_number ? wa_url($wa_number, get_setting('wa_text','Halo, saya ingin konsultasi properti.')) : url('/hubungi-kami');
$hero_link     = $hero_cta_url ? url($hero_cta_url) : $wa_link;

/* ---- CTA — from global admin (admin/?page=content&p=global) ---- */
$cta_title = get_content('global', 'cta_title', '');
$cta_desc  = get_content('global', 'cta_desc',  '');
$cta_btn   = get_content('global', 'cta_btn',   'Hubungi Sales via WhatsApp');

/* ---- Produk ---- */
$products = $db->fetchAll("SELECT * FROM produk WHERE status = 'aktif' ORDER BY created_at DESC LIMIT 6");

/* ---- FAQ ---- */
$faq_limit = max(1, (int) get_setting('faq_limit_home', '8'));
try {
  $faqs = $db->fetchAll("SELECT f.* FROM faq f WHERE f.is_active = 1
    AND NOT EXISTS (SELECT 1 FROM faq_layanan_rel r WHERE r.faq_id = f.id)
    ORDER BY f.urutan ASC LIMIT $faq_limit");
} catch (Throwable $e) {
  $faqs = $db->fetchAll("SELECT * FROM faq WHERE is_active = 1 ORDER BY urutan ASC LIMIT $faq_limit");
}

/* ---- Testimonials ---- */
$testis = $db->fetchAll("SELECT * FROM testimonial WHERE is_active = 1 ORDER BY urutan ASC LIMIT 6");

/* ---- Fasilitas from adv_card admin fields ---- */
$fasilitas = [];
for ($i = 1; $i <= 6; $i++) {
  $t = get_content('about', "adv_card{$i}_title", '');
  $d = get_content('about', "adv_card{$i}_desc",  '');
  if (empty(trim($t))) break;
  $fasilitas[] = ['num' => str_pad($i, 2, '0', STR_PAD_LEFT), 'label' => $t, 'desc' => $d];
}

/* ---- About image ---- */
$about_foto = get_setting('about_foto_1','') ?: get_setting('about_gambar','');

/* ---- Stats ---- */
$stats = [
  ['val'=>get_content('home','stat1_val','200'),'suf'=>'+','lbl'=>get_content('home','stat1_lbl','Rumah Dipasarkan')],
  ['val'=>get_content('home','stat2_val','190'),'suf'=>'+','lbl'=>get_content('home','stat2_lbl','Rumah Terbangun')],
  ['val'=>get_content('home','stat3_val','95'), 'suf'=>'%','lbl'=>get_content('home','stat3_lbl','Pelanggan Puas')],
  ['val'=>get_content('home','stat4_val','10'), 'suf'=>'+','lbl'=>get_content('home','stat4_lbl','Tahun Pengalaman')],
];

$star_svg = '<svg width="14" height="14" viewBox="0 0 24 24" fill="#f7c547"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';

$navbar_dark = true;
include theme_path('templates/layouts/header.php');
?>

<?php $flex_position = 'home_top'; include theme_path('templates/partials/flex-content.php'); ?>

<!-- ===== HERO ===== -->
<section class="section-hero" id="hero">
  <div class="hero-card">
    <?php if ($hero_image): ?>
    <div class="hero-bg-img"><img src="<?= uploads_url($hero_image) ?>" alt="" loading="eager"></div>
    <?php endif; ?>
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <div class="hero-left">
        <?php if ($hero_judul): ?>
          <h1 class="hero-title" data-hero="1"><?= htmlspecialchars($hero_judul) ?></h1>
        <?php endif; ?>
        <?php if ($hero_subtitle): ?>
          <p class="hero-subtitle" data-hero="2"><?= htmlspecialchars($hero_subtitle) ?></p>
        <?php endif; ?>
        <div data-hero="3">
          <a href="<?= $hero_link ?>" class="btn btn-white" <?= ($wa_link === $hero_link) ? 'target="_blank" rel="noopener"' : '' ?>>
            <?= htmlspecialchars($hero_cta_text) ?>
            <span class="btn-circle"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php $flex_position = 'home_after_hero'; include theme_path('templates/partials/flex-content.php'); ?>

<!-- ===== FASILITAS — from adv_card admin fields ===== -->
<?php if (!empty($fasilitas)): ?>
<section id="feature-service" style="padding-block:var(--sec-y);padding-inline:var(--pad-x);background:var(--om-white);">
  <div style="max-width:var(--container-max);margin-inline:auto;display:flex;flex-direction:column;gap:48px;">

    <!-- Section head — only show if admin filled in content -->
    <?php
      $fas_label = get_content('home','fasilitas_label','');
      $fas_title = get_content('home','fasilitas_title','');
      $fas_desc  = get_content('home','fasilitas_desc','');
    ?>
    <?php if ($fas_label || $fas_title || $fas_desc): ?>
    <div style="display:flex;flex-direction:column;gap:16px;max-width:780px;">
      <?php if ($fas_label): ?><span class="eyebrow" data-reveal><?= htmlspecialchars($fas_label) ?></span><?php endif; ?>
      <?php if ($fas_title): ?><h2 class="word-anim"><?= htmlspecialchars($fas_title) ?></h2><?php endif; ?>
      <?php if ($fas_desc): ?><p class="body-text" data-reveal><?= htmlspecialchars($fas_desc) ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="om-fasilitas-wrap" id="om-fasilitas">
      <!-- Left: active content -->
      <div class="om-fas-left">
        <?php foreach ($fasilitas as $i => $fas): ?>
        <div class="om-fas-content<?= $i === 0 ? ' active' : '' ?>" data-fas-idx="<?= $i ?>">
          <div style="display:flex;flex-direction:column;gap:20px;">
            <div style="display:flex;align-items:flex-start;gap:14px;">
              <div style="width:52px;height:52px;background:var(--om-gray-lt);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;font-weight:700;color:var(--om-dark);"><?= htmlspecialchars($fas['num']) ?></div>
              <h3 style="font-size:clamp(18px,2vw,26px);font-weight:600;line-height:1.1em;color:var(--om-dark);text-transform:uppercase;margin-top:10px;"><?= htmlspecialchars($fas['label']) ?></h3>
            </div>
            <p style="font-size:17px;font-weight:300;line-height:1.55em;color:var(--om-dark);"><?= htmlspecialchars($fas['desc']) ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Right: tall panels -->
      <div class="om-fas-right">
        <?php foreach ($fasilitas as $i => $fas): ?>
        <div class="om-fas-panel<?= $i === 0 ? ' active' : '' ?>"
             data-fas-panel="<?= $i ?>"
             onclick="omFasSelect(<?= $i ?>)"
             <?= $i > 0 ? 'style="border-left:1px solid var(--om-border);"' : '' ?>>
          <div class="om-fas-panel-text">
            <span class="om-fas-num"><?= htmlspecialchars($fas['num']) ?></span>
            <span class="om-fas-label"><?= htmlspecialchars($fas['label']) ?></span>
          </div>
          <div class="om-fas-active-dot"></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php $flex_position = 'home_after_fasilitas'; include theme_path('templates/partials/flex-content.php'); ?>

<!-- ===== #5: "Kenapa Memilih Kami" REMOVED — use flex_blocks/grid_icon_box instead ===== -->

<?php $flex_position = 'home_kenapa'; include theme_path('templates/partials/flex-content.php'); ?>

<!-- ===== PRODUK ===== -->
<?php if (!empty($products)): ?>
<?php
  $prop_label = get_content('home','prop_label','');
  $prop_title = get_content('home','prop_title','');
?>
<section class="section-property" id="property">
  <?php if ($prop_label || $prop_title): ?>
  <div class="sec-head-left" style="max-width:var(--container-max);width:100%;margin-inline:auto;">
    <?php if ($prop_label): ?><span class="eyebrow" data-reveal><?= htmlspecialchars($prop_label) ?></span><?php endif; ?>
    <?php if ($prop_title): ?><h2 class="word-anim"><?= htmlspecialchars($prop_title) ?></h2><?php endif; ?>
  </div>
  <?php endif; ?>
  <div class="prop-grid" style="max-width:var(--container-max);margin-inline:auto;">
    <?php foreach ($products as $p): ?>
    <a href="<?= url('/produk/'.$p['slug']) ?>" class="prop-card" data-nce-scroll-link="true">
      <div class="prop-card-img-wrap">
        <?php if ($p['gambar_utama']): ?>
          <img src="<?= uploads_url($p['gambar_utama']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" loading="lazy">
        <?php elseif ($about_foto): ?>
          <img src="<?= uploads_url($about_foto) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" loading="lazy" style="object-fit:cover;">
        <?php else: ?>
          <div style="width:100%;height:100%;background:var(--om-gray-lt);display:flex;align-items:center;justify-content:center;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--om-gray-mid)" stroke-width="1.2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
          </div>
        <?php endif; ?>
        <?php if (!empty($p['badge'])): ?><span class="prop-badge"><?= htmlspecialchars($p['badge']) ?></span><?php endif; ?>
      </div>
      <div class="prop-card-info">
        <div class="prop-loc">
          <svg viewBox="0 0 256 256" fill="currentColor" width="20" height="20"><path d="M128,16a88.1,88.1,0,0,0-88,88c0,75.3,80,132.17,83.41,134.55a8,8,0,0,0,9.18,0C136,236.17,216,179.3,216,104A88.1,88.1,0,0,0,128,16Zm0,56a32,32,0,1,1-32,32A32,32,0,0,1,128,72Z"/></svg>
          <?php
            $short = trim($p['short_description'] ?? '');
            $loc_parts = $short ? explode("\n", $short) : [];
            $lokasi = !empty($loc_parts[0]) ? $loc_parts[0] : get_setting('site_address','');
            if ($lokasi) echo htmlspecialchars(excerpt($lokasi,40));
          ?>
        </div>
        <div class="prop-name"><?= htmlspecialchars($p['nama']) ?></div>
        <?php
          $kt = $loc_parts[1] ?? ''; $km = $loc_parts[2] ?? ''; $luas = $loc_parts[3] ?? '';
          if ($kt || $km || $luas):
        ?>
        <div class="prop-specs">
          <?php if ($kt): ?><div class="prop-spec"><svg viewBox="0 0 256 256" fill="none" stroke="currentColor" stroke-width="12" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M216,74H30V48a6,6,0,0,0-12,0V208a6,6,0,0,0,12,0V174H242v34a6,6,0,0,0,12,0V112A38,38,0,0,0,216,74ZM30,86h76v76H30Zm88,76V86h98a26,26,0,0,1,26,26v50Z"/></svg><?= htmlspecialchars($kt) ?></div><?php endif; ?>
          <?php if ($km): ?><div class="prop-spec"><svg viewBox="0 0 256 256" fill="none" stroke="currentColor" stroke-width="12" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M240,98H206V96a6,6,0,0,0-6-6H136a6,6,0,0,0-6,6v2H62V52A14,14,0,0,1,76,38,14.47,14.47,0,0,1,90.12,49.19a6,6,0,1,0,11.76-2.38A26.32,26.32,0,0,0,76,26,26,26,0,0,0,50,52V98H16a6,6,0,0,0-6,6v40a54.06,54.06,0,0,0,54,54h2v18a6,6,0,0,0,12,0V198H178v18a6,6,0,0,0,12,0V198h2a54.06,54.06,0,0,0,54-54V104A6,6,0,0,0,240,98Zm-98,4h52v36H142Zm92,42a42,42,0,0,1-42,42H64a42,42,0,0,1-42-42V110H130v34a6,6,0,0,0,6,6h64a6,6,0,0,0,6-6V110h28Z"/></svg><?= htmlspecialchars($km) ?></div><?php endif; ?>
          <?php if ($luas): ?><div class="prop-spec"><svg viewBox="0 0 256 256" fill="none" stroke="currentColor" stroke-width="12" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M110,48V208a6,6,0,0,1-12,0V134H38.49l21.75,21.76a6,6,0,1,1-8.48,8.48l-32-32a6,6,0,0,1,0-8.48l32-32a6,6,0,0,1,8.48,8.48L38.49,122H98V48a6,6,0,0,1,12,0Zm126.24,75.76-32-32a6,6,0,0,0-8.48,8.48L217.51,122H158V48a6,6,0,0,0-12,0V208a6,6,0,0,0,12,0V134h59.51l-21.75,21.76a6,6,0,1,0,8.48,8.48l32-32A6,6,0,0,0,236.24,123.76Z"/></svg><?= htmlspecialchars($luas) ?></div><?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="prop-price">
          <span class="prop-price-val"><?= $p['harga'] > 0 ? format_rupiah($p['harga']) : 'Hubungi Kami' ?></span>
          <?php if (!empty($p['harga_coret']) && $p['harga_coret'] > $p['harga']): ?>
            <span style="font-size:14px;font-weight:300;color:var(--om-gray);text-decoration:line-through;margin-left:8px;"><?= format_rupiah($p['harga_coret']) ?></span>
          <?php endif; ?>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php $prop_cta = get_content('home','prop_cta',''); if ($prop_cta): ?>
  <div style="max-width:var(--container-max);width:100%;margin-inline:auto;">
    <a href="<?= url('/produk') ?>" class="btn btn-ghost" data-reveal>
      <?= htmlspecialchars($prop_cta) ?>
      <span class="btn-circle"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
    </a>
  </div>
  <?php endif; ?>
</section>
<?php endif; ?>

<?php $flex_position = 'home_after_property'; include theme_path('templates/partials/flex-content.php'); ?>

<!-- ===== ABOUT — no hardcoded titles, all from admin ===== -->
<?php
  $about_label = get_content('home','about_label','');
  $about_title = get_content('home','about_title','');
  $about_text  = get_content('home','about_text','');
  $has_about   = ($about_label || $about_title || $about_text || $about_foto || !empty($stats));
?>
<?php if ($has_about): ?>
<section class="section-about" id="about">
  <div class="about-inner">
    <!-- Left -->
    <div class="about-left">
      <div class="about-text-wrap" data-reveal="left" style="display:flex;flex-direction:column;gap:16px;">
        <?php if ($about_label): ?><span class="eyebrow"><?= htmlspecialchars($about_label) ?></span><?php endif; ?>
        <?php if ($about_title): ?><h2 class="word-anim"><?= htmlspecialchars($about_title) ?></h2><?php endif; ?>
        <?php if ($about_text): ?><p class="about-body"><?= htmlspecialchars($about_text) ?></p><?php endif; ?>
      </div>
      <?php if (!empty($stats) && array_filter(array_column($stats,'val'))): ?>
      <div class="about-stats" data-stagger>
        <?php foreach ($stats as $st): if (!$st['val']) continue; ?>
        <div class="stat-item">
          <div class="stat-number-wrap">
            <span class="stat-number om-count" data-to="<?= htmlspecialchars($st['val']) ?>" data-suffix="<?= htmlspecialchars($st['suf']) ?>">0<?= htmlspecialchars($st['suf']) ?></span>
          </div>
          <div class="stat-label" style="color:var(--om-gray);"><?= htmlspecialchars($st['lbl']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Right: photo from about_foto_1 only -->
    <div style="flex:0 0 70%;max-width:70%;" data-reveal="right">
      <div style="height:500px;border-radius:var(--radius-lg);overflow:hidden;background:var(--om-gray-lt);">
        <?php if ($about_foto): ?>
          <img src="<?= uploads_url($about_foto) ?>" alt="<?= htmlspecialchars(get_setting('site_name','')) ?>" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
        <?php else: ?>
          <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="var(--om-gray-mid)" stroke-width="1.2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
            <p style="font-size:13px;color:var(--om-gray);text-align:center;max-width:180px;">Upload "Foto About 1" di Admin → Pengaturan</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php $flex_position = 'home_after_about'; include theme_path('templates/partials/flex-content.php'); ?>

<!-- ===== TESTIMONIALS ===== -->
<?php if (!empty($testis)): ?>
<?php
  $testi_label = get_content('home','testi_label','');
  $testi_title = get_content('home','testi_title','');
  $testi_desc  = get_content('home','testi_desc','');
?>
<section class="section-testi" id="testimonials">
  <div class="testi-inner">
    <div class="testi-left">
      <div class="testi-text-wrap" data-reveal="left" style="display:flex;flex-direction:column;gap:16px;">
        <?php if ($testi_label): ?><span class="eyebrow"><?= htmlspecialchars($testi_label) ?></span><?php endif; ?>
        <?php if ($testi_title): ?><h2 class="word-anim"><?= htmlspecialchars($testi_title) ?></h2><?php endif; ?>
      </div>
      <?php if ($testi_desc): ?><p class="testi-body" data-reveal><?= htmlspecialchars($testi_desc) ?></p><?php endif; ?>
    </div>
    <div class="testi-grid" data-stagger>
      <?php foreach ($testis as $t): ?>
      <div class="testi-card">
        <div class="testi-card-top">
          <div class="testi-stars"><?php $r=max(1,min(5,(int)($t['rating']??5)));for($i=0;$i<$r;$i++) echo $star_svg; ?></div>
          <p class="testi-review"><?= htmlspecialchars($t['ulasan']??$t['isi']??'') ?></p>
        </div>
        <div class="testi-reviewer">
          <div class="testi-avatar">
            <?php if (!empty($t['foto'])): ?><img src="<?= uploads_url($t['foto']) ?>" alt="" loading="lazy"><?php else: ?><div style="width:100%;height:100%;background:var(--om-gray-lt);display:flex;align-items:center;justify-content:center;"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--om-gray)" stroke-width="1.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div><?php endif; ?>
          </div>
          <div class="testi-info">
            <div class="testi-name"><?= htmlspecialchars($t['nama']) ?></div>
            <div class="testi-role"><?= htmlspecialchars($t['jabatan']??$t['posisi']??'') ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Logo klien -->
<?php include theme_path('templates/partials/logo-carousel.php'); ?>

<?php $flex_position = 'home_after_testimonials'; include theme_path('templates/partials/flex-content.php'); ?>

<!-- ===== FAQ ===== -->
<?php if (!empty($faqs)): ?>
<?php
  $faq_label = get_content('home','faq_label','');
  $faq_title = get_content('home','faq_title','');
?>
<section class="section-faq" id="faq">
  <div class="faq-inner">
    <div class="faq-left">
      <?php if ($faq_label): ?><span class="eyebrow" data-reveal><?= htmlspecialchars($faq_label) ?></span><?php endif; ?>
      <?php if ($faq_title): ?><h2 class="word-anim"><?= htmlspecialchars($faq_title) ?></h2><?php endif; ?>
    </div>
    <div class="faq-right">
      <?php foreach ($faqs as $i => $f): ?>
      <div class="faq-item<?= $i===0?' active':'' ?>">
        <div class="faq-q">
          <span class="faq-q-text"><?= htmlspecialchars($f['pertanyaan']) ?></span>
          <span class="faq-toggle"><svg viewBox="0 0 256 256" fill="currentColor" width="20" height="20"><path d="M224,128a8,8,0,0,1-8,8H136v80a8,8,0,0,1-16,0V136H40a8,8,0,0,1,0-16h80V40a8,8,0,0,1,16,0v80h80A8,8,0,0,1,224,128Z"/></svg></span>
        </div>
        <div class="faq-a"><div class="faq-a-inner"><?= nl2br(htmlspecialchars($f['jawaban'])) ?></div></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php $flex_position = 'home_after_faq'; include theme_path('templates/partials/flex-content.php'); ?>

<!-- ===== CTA — from global admin, single #2c2c2c color ===== -->
<?php if ($cta_title || $cta_desc || $wa_number): ?>
<section style="background:#2c2c2c;padding:80px var(--pad-x);display:flex;flex-direction:column;align-items:center;gap:28px;" id="contact">
  <?php if ($cta_title): ?>
  <h2 style="font-size:clamp(22px,3.5vw,48px);font-weight:600;line-height:1.15em;text-transform:uppercase;color:#fff;text-align:center;max-width:700px;margin:0;" data-reveal>
    <?= htmlspecialchars($cta_title) ?>
  </h2>
  <?php endif; ?>
  <?php if ($cta_desc): ?>
  <p style="font-size:17px;font-weight:300;line-height:1.5em;color:rgba(255,255,255,.7);text-align:center;max-width:560px;margin:0;" data-reveal>
    <?= htmlspecialchars($cta_desc) ?>
  </p>
  <?php endif; ?>
  <?php if ($wa_number): ?>
  <a href="<?= $wa_link ?>" target="_blank" rel="noopener"
     style="display:inline-flex;align-items:center;gap:12px;background:#25d366;color:#fff;padding:15px 36px;border-radius:99px;font-size:17px;font-weight:600;text-decoration:none;transition:background .2s,transform .15s;"
     onmouseover="this.style.background='#1ebe59';this.style.transform='translateY(-2px)'"
     onmouseout="this.style.background='#25d366';this.style.transform='none'"
     data-reveal>
    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.22-.64.08-.3-.15-1.26-.46-2.39-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.92-2.21-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35M12.05 21.79a9.87 9.87 0 01-5.04-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 01-1.51-5.26c0-5.45 4.44-9.88 9.89-9.88 2.64 0 5.12 1.03 6.99 2.9a9.83 9.83 0 012.89 6.99c0 5.45-4.44 9.88-9.89 9.88m8.41-18.3A11.82 11.82 0 0012.05 0C5.5 0 .16 5.34.16 11.89c0 2.1.55 4.14 1.59 5.95L.06 24l6.3-1.65a11.88 11.88 0 005.69 1.45h.01c6.55 0 11.89-5.34 11.89-11.89 0-3.18-1.24-6.17-3.48-8.42"/></svg>
    <?= htmlspecialchars($cta_btn) ?>
  </a>
  <?php endif; ?>
</section>
<?php endif; ?>

<?php $flex_position = 'home_bottom'; include theme_path('templates/partials/flex-content.php'); ?>

<?php include theme_path('templates/layouts/footer.php'); ?>
