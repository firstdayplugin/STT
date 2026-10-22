<?php
if (!isset($db)) $db = Database::getInstance();

// SEO - per page
$page_seo = get_page_seo('home');
$seo = [
    'title'       => $page_seo['meta_title']       ?? get_setting('meta_title_default', get_setting('site_name')),
    'description' => $page_seo['meta_description'] ?? get_setting('meta_desc_default'),
    'image'       => $page_seo['og_image']         ?? '',
];

// HERO — single source of truth: SETTINGS (managed in Pengaturan > Hero)
$hero_mode     = get_setting('hero_mode', 'single'); // single | slideshow
$hero_judul    = get_setting('hero_judul', 'Mitra Terpercaya untuk Kebutuhan Reklame & Advertising');
$hero_subtitle = get_setting('hero_subtitle', 'Bangun visibilitas dan perkuat branding bisnis Anda melalui solusi reklame yang strategis dan berdampak. Lebih dari 1.200 klien di seluruh Indonesia telah mempercayakan kebutuhan advertising mereka kepada kami.');
$hero_cta_text = get_setting('hero_cta_text', 'Hubungi Kami!');
$hero_cta_url  = get_setting('hero_cta_url', '');
$hero_image    = get_setting('hero_gambar', 'settings/hero-01.webp');
$hero_overlay  = (float) get_setting('hero_overlay', '0.5'); // 0..1
$hero_slides   = [];
if ($hero_mode === 'slideshow') {
    $hero_slides = $db->fetchAll("SELECT * FROM hero_slides WHERE is_active=1 ORDER BY urutan ASC");
    if (empty($hero_slides)) { $hero_mode = 'single'; } // fallback if no slides
}
$wa_link    = wa_url(get_setting('wa_number'), get_setting('wa_text', 'Halo, saya ingin konsultasi.'));

$layanan_list = $db->fetchAll("SELECT * FROM layanan WHERE is_active = 1 ORDER BY urutan ASC LIMIT 6");
// Testimonials: configurable limit (used internally; actual carousel partial fetches its own)
$testi_limit_home = max(1, (int) get_setting('testimonial_limit_home', '50'));
$testimonials = $db->fetchAll("SELECT * FROM testimonial WHERE is_active = 1 ORDER BY urutan ASC LIMIT $testi_limit_home");
$faq_limit_home = max(1, (int) get_setting('faq_limit_home', '100'));
try {
    $faqs = $db->fetchAll("SELECT f.* FROM faq f WHERE f.is_active = 1 
                           AND NOT EXISTS (SELECT 1 FROM faq_layanan_rel r WHERE r.faq_id = f.id)
                           ORDER BY f.urutan ASC LIMIT $faq_limit_home");
} catch (Throwable $e) {
    $faqs = $db->fetchAll("SELECT * FROM faq WHERE is_active = 1 ORDER BY urutan ASC LIMIT $faq_limit_home");
}

include theme_path('templates/layouts/header.php');
?>

<section class="hero-wrap" style="--hero-overlay: <?= max(0, min(1, $hero_overlay)) ?>">
  <?php if ($hero_mode === 'slideshow' && !empty($hero_slides)): ?>
    <!-- Slideshow background -->
    <div class="hero-wrap-bg hero-slideshow" data-slideshow="1">
      <?php foreach ($hero_slides as $si => $sl): ?>
        <div class="hero-slide<?= $si === 0 ? ' active' : '' ?>" data-slide-i="<?= $si ?>">
          <img src="<?= uploads_url($sl['gambar'] ?: $hero_image) ?>" alt="">
          <div class="hero-slide-overlay"></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="hero-wrap-bg">
      <img src="<?= uploads_url($hero_image) ?>" alt="">
    </div>
  <?php endif; ?>

  <?php $navbar_dark = true; include theme_path('templates/partials/navbar.php'); ?>

  <div class="hero-content">
    <?php if ($hero_mode === 'slideshow' && !empty($hero_slides)): ?>
      <?php foreach ($hero_slides as $si => $sl): ?>
        <div class="hero-slide-text<?= $si === 0 ? ' active' : '' ?>" data-slide-i="<?= $si ?>">
          <h1 class="hero-title"><?= htmlspecialchars($sl['judul'] ?: $hero_judul) ?></h1>
          <div class="hero-divider"></div>
          <p class="hero-subtitle"><?= htmlspecialchars($sl['subtitle'] ?: $hero_subtitle) ?></p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <h1 class="hero-title"><?= htmlspecialchars($hero_judul) ?></h1>
      <div class="hero-divider"></div>
      <p class="hero-subtitle"><?= htmlspecialchars($hero_subtitle) ?></p>
    <?php endif; ?>
    <a href="<?= $hero_cta_url ? url($hero_cta_url) : $wa_link ?>" <?= $hero_cta_url ? '' : 'target="_blank"' ?> class="hero-cta">
      <span class="arrow-icon">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
        </svg>
      </span>
      <?= htmlspecialchars($hero_cta_text) ?>
    </a>
    <?php if ($hero_mode === 'slideshow' && count($hero_slides) > 1): ?>
      <div class="hero-dots">
        <?php foreach ($hero_slides as $si => $sl): ?>
          <button class="hero-dot<?= $si === 0 ? ' active' : '' ?>" data-go="<?= $si ?>" aria-label="Slide <?= $si+1 ?>"></button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
  <?php if ($hero_mode === 'slideshow' && count($hero_slides) > 1): ?>
  <script>
  (function(){
    const slides = document.querySelectorAll('.hero-slide');
    const texts  = document.querySelectorAll('.hero-slide-text');
    const dots   = document.querySelectorAll('.hero-dot');
    let cur = 0;
    const total = slides.length;
    function go(i) {
      i = ((i % total) + total) % total;
      slides.forEach((s,idx)=>s.classList.toggle('active', idx===i));
      texts.forEach((t,idx)=>t.classList.toggle('active', idx===i));
      dots.forEach((d,idx)=>d.classList.toggle('active', idx===i));
      cur = i;
    }
    dots.forEach(d => d.addEventListener('click', () => go(+d.dataset.go)));
    // Auto-advance every 6s
    setInterval(() => go(cur + 1), 6000);
  })();
  </script>
  <?php endif; ?>
</section>

<?php $flex_position = 'home_after_hero'; include theme_path('templates/partials/flex-content.php'); ?>

<!-- About card overlapping hero -->
<section class="about-section">
  <div class="about-card">
    <div class="about-grid">
      <div>
        <div class="section-label"><?= htmlspecialchars(get_content('home', 'about_label', 'About Us')) ?></div>
      </div>
      <p class="about-text">
        <?= htmlspecialchars(get_content('home', 'about_text', 'Reklamepedia hadir untuk membantu meningkatkan visibilitas dan memperkuat identitas bisnis anda melalui solusi reklame berkualitas berpengalaman lebih dari 15 tahun.')) ?>
      </p>
    </div>
  </div>
</section>

<!-- Performance stats -->
<section class="performance-section">
  <div class="performance-grid">
    <div class="performance-left">
      <div class="section-label"><?= htmlspecialchars(get_content('home', 'perf_label', 'Our Performance')) ?></div>
      <h2><?= nl2br(htmlspecialchars(get_content('home', 'perf_title', "Built on Experience,\nDriven by Results"))) ?></h2>
      <div class="section-divider"></div>
      <p class="section-desc">
        <?= htmlspecialchars(get_content('home', 'perf_desc', 'Selama lebih dari 15 tahun, kami telah membantu berbagai bisnis memperkuat identitas dan meningkatkan visibilitas brand melalui solusi reklame yang konsisten dan terpercaya.')) ?>
      </p>
    </div>
    <div class="performance-stats">
      <div class="stat-card stat-card-1">
        <div class="stat-card-label"><?= nl2br(htmlspecialchars(get_content('home', 'perf_stat1_label', 'Klien Bisnis Terlayani'))) ?></div>
        <div class="stat-card-value"><?= htmlspecialchars(get_content('home', 'perf_stat1_value', '10k')) ?></div>
      </div>
      <div class="stat-card stat-card-2">
        <div class="stat-card-label"><?= nl2br(htmlspecialchars(get_content('home', 'perf_stat2_label', 'Proyek Reklame Selesai'))) ?></div>
        <div class="stat-card-value"><?= htmlspecialchars(get_content('home', 'perf_stat2_value', '700+')) ?></div>
      </div>
      <div class="stat-card stat-card-3">
        <div class="stat-card-label"><?= nl2br(htmlspecialchars(get_content('home', 'perf_stat3_label', 'Kota Layanan Di Indonesia'))) ?></div>
        <div class="stat-card-value"><?= htmlspecialchars(get_content('home', 'perf_stat3_value', '14')) ?></div>
      </div>
    </div>
  </div>
</section>

<!-- Services -->
<section class="services-section">
  <div class="services-header">
    <div>
      <div class="section-label light"><?= htmlspecialchars(get_content('home', 'services_label', 'Our Core Thing')) ?></div>
      <h2 class="section-title light"><?= nl2br(htmlspecialchars(get_content('home', 'services_title', "Complete Solutions\nfor Your Brand Visibility"))) ?></h2>
    </div>
    <div class="services-header-line"></div>
  </div>
  <div class="services-grid">
    <?php foreach ($layanan_list as $i => $l): ?>
    <a href="<?= url('/layanan/'.$l['slug']) ?>" class="service-card">
      <div class="service-card-num"><?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?>.</div>
      <h3 class="service-card-title"><?= htmlspecialchars($l['nama']) ?></h3>
      <p class="service-card-desc"><?= htmlspecialchars($l['deskripsi_pendek']) ?></p>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<?php $flex_position = 'home_middle'; include theme_path('templates/partials/flex-content.php'); ?>

<!-- Testimonial (auto-slide carousel) -->
<?php include theme_path('templates/partials/testimonial-carousel.php'); ?>

<!-- FAQ -->
<?php if (!empty($faqs)): ?>
<section class="faq-section">
  <div class="faq-container">
    <div>
      <div class="section-label"><?= htmlspecialchars(get_content('home', 'faq_label', 'FAQ')) ?></div>
      <h2 class="section-title"><?= nl2br(htmlspecialchars(get_content('home', 'faq_title', "Have Questions?\nWe Have Answers"))) ?></h2>
      <p class="section-desc" style="margin-top:16px">
        <?= htmlspecialchars(get_content('home', 'faq_desc', 'Temukan jawaban dari pertanyaan-pertanyaan umum disini.')) ?>
      </p>
    </div>
    <div class="faq-list">
      <?php foreach ($faqs as $i => $f): ?>
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
<?php endif; ?>

<?php $flex_position = 'home_before_footer'; include theme_path('templates/partials/flex-content.php'); ?>

<!-- Blog Section (homepage) -->
<?php
$hb_enabled = get_setting('homepage_blog_enabled', '0') === '1';
if ($hb_enabled):
    $hb_count = max(1, (int) get_setting('homepage_blog_count', '3'));
    $hb_type  = get_setting('homepage_blog_type', 'recent'); // recent | popular | random
    $hb_label = get_setting('homepage_blog_label', 'Latest Articles');
    $hb_title = get_setting('homepage_blog_title', "Insights & Tips\nFrom Our Blog");
    $hb_desc  = get_setting('homepage_blog_desc', 'Artikel terbaru seputar dunia reklame, branding, dan tips bisnis.');
    
    $order_sql = match($hb_type) {
        'popular' => 'views DESC, created_at DESC',
        'random'  => 'RAND()',
        default   => 'created_at DESC',
    };
    try {
        $hb_posts = $db->fetchAll(
            "SELECT b.*, u.nama as author_nama
             FROM blog b LEFT JOIN users u ON u.id = b.user_id
             WHERE b.status = 'published'
             ORDER BY $order_sql LIMIT $hb_count"
        );
    } catch (Throwable $e) { $hb_posts = []; }
?>
<?php if (!empty($hb_posts)): ?>
<section class="home-blog-section">
  <div class="container">
    <div class="home-blog-header">
      <div class="section-label" style="justify-content:center"><?= htmlspecialchars($hb_label) ?></div>
      <h2 class="section-title"><?= nl2br(htmlspecialchars($hb_title)) ?></h2>
      <p class="section-desc" style="margin:14px auto 0;max-width:580px"><?= htmlspecialchars($hb_desc) ?></p>
    </div>
    <div class="home-blog-grid">
      <?php foreach ($hb_posts as $bp): ?>
      <a href="<?= url('/blog/'.$bp['slug']) ?>" class="home-blog-card">
        <div class="home-blog-img">
          <?php if ($bp['gambar_utama']): ?>
            <img src="<?= uploads_url($bp['gambar_utama']) ?>" alt="<?= htmlspecialchars($bp['judul']) ?>" loading="lazy">
          <?php else: ?>
            <div class="home-blog-noimg">📝</div>
          <?php endif; ?>
        </div>
        <div class="home-blog-body">
          <div class="home-blog-meta">
            <?= date('d M Y', strtotime($bp['created_at'])) ?>
            <?php if ($bp['author_nama']): ?> · <?= htmlspecialchars($bp['author_nama']) ?><?php endif; ?>
          </div>
          <h3 class="home-blog-title"><?= htmlspecialchars($bp['judul']) ?></h3>
          <p class="home-blog-excerpt"><?= htmlspecialchars(excerpt($bp['excerpt'] ?: strip_tags($bp['konten']), 110)) ?></p>
          <span class="home-blog-cta">Baca Artikel
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" style="margin-left:4px;vertical-align:-2px"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:36px">
      <a href="<?= url('/blog') ?>" class="home-blog-all">Lihat Semua Artikel</a>
    </div>
  </div>
</section>
<?php endif; ?>
<?php endif; ?>

<?php include theme_path('templates/layouts/footer.php'); ?>
