<?php
if (!isset($db)) $db = Database::getInstance();

/* ---- SEO ---- */
$page_seo = get_page_seo('home');
$seo = [
  'title'       => $page_seo['meta_title']       ?? get_setting('meta_title_default', get_setting('site_name', 'Reklamenesia')),
  'description' => $page_seo['meta_description'] ?? get_setting('meta_desc_default', 'One-stop solution untuk keperluan signage Anda!'),
  'image'       => $page_seo['og_image']         ?? '',
];

/* ---- HERO (settings) ---- */
$hero_judul    = get_setting('hero_judul', 'Solusi Reklame Berkualitas untuk Kebutuhan Brand Anda');
$hero_subtitle = get_setting('hero_subtitle', 'Solusi terpercaya untuk kebutuhan reklame Anda. Dari strategi hingga eksekusi, kami bantu brand Anda tampil maksimal.');
$hero_cta_text = get_setting('hero_cta_text', 'Lihat Produk Kami');
$hero_cta_url  = get_setting('hero_cta_url', '');
$hero_image    = get_setting('hero_gambar', '');
$wa_link       = wa_url(get_setting('wa_number'), get_setting('wa_text', 'Halo, saya ingin konsultasi.'));
$hero_link     = $hero_cta_url ? url($hero_cta_url) : url('/produk');

/* ---- DATA ---- */
$layanan_list = $db->fetchAll("SELECT * FROM layanan WHERE is_active = 1 ORDER BY urutan ASC");
$layanan_tiles= array_slice($layanan_list, 0, 7);

// image marquee: featured gallery → any gallery → layanan images
$marquee_imgs = $db->fetchAll("SELECT gambar, judul FROM gallery ORDER BY is_featured DESC, created_at DESC LIMIT 10");
if (empty($marquee_imgs)) {
  foreach ($layanan_list as $l) { if (!empty($l['gambar'])) $marquee_imgs[] = ['gambar' => $l['gambar'], 'judul' => $l['nama']]; }
}

$faq_limit_home = max(1, (int) get_setting('faq_limit_home', '100'));
try {
  $faqs = $db->fetchAll("SELECT f.* FROM faq f WHERE f.is_active = 1
    AND NOT EXISTS (SELECT 1 FROM faq_layanan_rel r WHERE r.faq_id = f.id)
    ORDER BY f.urutan ASC LIMIT $faq_limit_home");
} catch (Throwable $e) {
  $faqs = $db->fetchAll("SELECT * FROM faq WHERE is_active = 1 ORDER BY urutan ASC LIMIT $faq_limit_home");
}

$navbar_dark = true;
include theme_path('templates/layouts/header.php');

/* stat icons */
$ic_clients = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-4-4M9 20H4v-1a4 4 0 014-4h4a4 4 0 014 4v1H9zm3-9a4 4 0 100-8 4 4 0 000 8zm6-2a3 3 0 100-6"/></svg>';
$ic_project = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5l-7 7 7 7M4 12h12a4 4 0 004-4V5"/></svg>';
$ic_award   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8m-4-4v4m-5-17h10v4a5 5 0 01-10 0V4zM5 4H3v2a3 3 0 003 3m13-5h2v2a3 3 0 01-3 3"/></svg>';
$ic_exp     = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
$asterisk   = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l1.8 6.5L20 6l-4.2 5.1L22 14l-6.4-.4L16 20l-4-5-4 5 .4-6.4L2 14l6.2-2.9L4 6l6.2 2.5z"/></svg>';

/* Split a stat value like "10k", "700+", "10.000+" into prefix / number / suffix
   and emit a span the JS counter animates on scroll into view. */
if (!function_exists('rk_counter')) {
  function rk_counter($value) {
    $value = trim((string)$value);
    if (!preg_match('/^(\D*)([\d.,]+)(.*)$/u', $value, $m)) {
      return '<span class="stat-num">' . htmlspecialchars($value) . '</span>';
    }
    $prefix = $m[1]; $num = (int) preg_replace('/\D/', '', $m[2]); $suffix = $m[3];
    return '<span class="stat-num rk-count" data-to="' . $num . '"'
         . ' data-prefix="' . htmlspecialchars($prefix, ENT_QUOTES) . '"'
         . ' data-suffix="' . htmlspecialchars($suffix, ENT_QUOTES) . '">'
         . htmlspecialchars($prefix . '0' . $suffix) . '</span>';
  }
}
?>

<!-- ===== HERO ===== -->
<section class="hero">
  <?php if ($hero_image): ?><div class="hero-bg"><img src="<?= uploads_url($hero_image) ?>" alt=""></div><?php endif; ?>
  <div class="hero-inner">
    <div class="hero-copy">
      <h1 data-hero="1"><?= htmlspecialchars($hero_judul) ?></h1>
      <p data-hero="2"><?= htmlspecialchars($hero_subtitle) ?></p>
      <div class="hero-actions" data-hero="3">
        <a href="<?= $hero_link ?>" class="btn light">
          <span class="ic"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
          <?= htmlspecialchars($hero_cta_text) ?>
        </a>
      </div>
    </div>
  </div>
  <div class="hero-scroll"><span>Scroll</span><span class="mouse"></span></div>
</section>

<!-- ===== IMAGE MARQUEE ===== -->
<?php if (!empty($marquee_imgs)): ?>
<div class="img-marquee bg-white" style="padding-block:clamp(28px,4vw,56px)">
  <div class="marquee">
    <div class="marquee-track" style="--speed:52s">
      <?php foreach ($marquee_imgs as $m): ?>
        <div class="mq-img"><img src="<?= uploads_url($m['gambar']) ?>" alt="<?= htmlspecialchars($m['judul'] ?? '') ?>" loading="lazy"></div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ===== SERVICE PILLS MARQUEE (single row, slides right) ===== -->
<?php if (!empty($layanan_list)): ?>
<div class="pill-marquee">
  <div class="marquee" data-dir="right">
    <div class="marquee-track">
      <?php foreach ($layanan_list as $l): ?>
        <a href="<?= url('/layanan/'.$l['slug']) ?>" class="svc-pill"><span class="star"><?= $asterisk ?></span><?= htmlspecialchars($l['nama']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<?php $flex_position = 'home_after_hero'; include theme_path('templates/partials/flex-content.php'); ?>

<!-- ===== ABOUT + STATS ===== -->
<section class="section about">
  <div class="container">
    <div class="about-grid">
      <div class="about-copy" data-reveal="left">
        <span class="eyebrow"><?= htmlspecialchars(get_content('home', 'about_label', 'About us')) ?></span>
        <h2><?= nl2br(htmlspecialchars(get_content('home', 'about_title', 'Mewujudkan Identitas Visual Brand yang Lebih Kuat'))) ?></h2>
        <p class="lead"><?= htmlspecialchars(get_content('home', 'about_text', 'Kami telah membantu berbagai bisnis menghadirkan reklame berkualitas selama lebih dari 15 tahun. Setiap produk dirancang untuk memperkuat citra brand melalui tampilan yang profesional, modern, dan berdaya tarik tinggi.')) ?></p>
        <a href="<?= url('/tentang-kami') ?>" class="btn">
          <span class="ic"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
          <?= htmlspecialchars(get_content('home', 'about_btn', 'About Our Story')) ?>
        </a>
      </div>
      <div class="stats">
        <div class="stat" data-reveal>
          <div class="stat-ic"><?= $ic_clients ?></div>
          <div><?= rk_counter(get_content('home', 'perf_stat1_value', '500+')) ?>
          <div class="stat-label"><?= htmlspecialchars(get_content('home', 'perf_stat1_label', 'Happy Clients')) ?></div></div>
        </div>
        <div class="stat" data-reveal>
          <div class="stat-ic"><?= $ic_project ?></div>
          <div><?= rk_counter(get_content('home', 'perf_stat2_value', '300+')) ?>
          <div class="stat-label"><?= htmlspecialchars(get_content('home', 'perf_stat2_label', 'Released Projects')) ?></div></div>
        </div>
        <div class="stat" data-reveal>
          <div class="stat-ic"><?= $ic_award ?></div>
          <div><?= rk_counter(get_content('home', 'perf_stat3_value', '75+')) ?>
          <div class="stat-label"><?= htmlspecialchars(get_content('home', 'perf_stat3_label', 'Awards & Recognitions')) ?></div></div>
        </div>
        <div class="stat" data-reveal>
          <div class="stat-ic"><?= $ic_exp ?></div>
          <div><?= rk_counter(get_content('home', 'perf_stat4_value', '25+')) ?>
          <div class="stat-label"><?= htmlspecialchars(get_content('home', 'perf_stat4_label', 'Years of Experience')) ?></div></div>
        </div>
        <div class="asterisk"><?= $asterisk ?></div>
      </div>
    </div>
  </div>
</section>

<!-- ===== SERVICES (image tiles + sticky watermark) ===== -->
<?php if (!empty($layanan_tiles)): ?>
<section class="services">
  <div class="container">
    <div class="sec-head" data-reveal>
      <span class="eyebrow"><?= htmlspecialchars(get_content('home', 'services_label', 'Our Services')) ?></span>
      <h2><?= nl2br(htmlspecialchars(get_content('home', 'services_title', 'Layanan Reklame & Signage Kami'))) ?></h2>
    </div>
  </div>
  <div class="services-watermark" aria-hidden="true">OUR SERVICES</div>
  <div class="container">
    <div class="services-list">
      <?php foreach ($layanan_tiles as $i => $l): ?>
      <a href="<?= url('/layanan/'.$l['slug']) ?>" class="svc-card">
        <?php if (!empty($l['gambar'])): ?><img src="<?= uploads_url($l['gambar']) ?>" alt="<?= htmlspecialchars($l['nama']) ?>" loading="lazy"><?php endif; ?>
        <span class="chip-arrow"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
        <div class="svc-card-meta">
          <span class="svc-card-num"><?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?> — Reklamenesia</span>
          <span class="svc-card-title"><?= htmlspecialchars($l['nama']) ?></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php $flex_position = 'home_middle'; include theme_path('templates/partials/flex-content.php'); ?>

<!-- ===== TESTIMONIALS ===== -->
<?php include theme_path('templates/partials/testimonial-carousel.php'); ?>

<!-- ===== FAQ ===== -->
<?php if (!empty($faqs)): ?>
<section class="section bg-white">
  <div class="container">
    <div class="faq-grid">
      <div class="faq-aside" data-reveal="left">
        <span class="eyebrow"><?= htmlspecialchars(get_content('home', 'faq_label', 'FAQ')) ?></span>
        <h2><?= nl2br(htmlspecialchars(get_content('home', 'faq_title', 'Frequently Asked Questions!'))) ?></h2>
        <p class="lead"><?= htmlspecialchars(get_content('home', 'faq_desc', 'Temukan jawaban dari pertanyaan umum seputar layanan, harga, dan proses kerja kami.')) ?></p>
        <a href="<?= url('/hubungi-kami') ?>" class="btn">
          <span class="ic"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
          Contact Us
        </a>
      </div>
      <div class="faq-list" data-reveal="right">
        <?php foreach ($faqs as $i => $f): ?>
        <div class="faq-item<?= $i === 0 ? ' active' : '' ?>">
          <div class="faq-q">
            <span class="faq-n">{<?= $i+1 ?>}</span>
            <span class="txt"><?= htmlspecialchars($f['pertanyaan']) ?></span>
            <span class="faq-ic"></span>
          </div>
          <div class="faq-a"><div class="faq-a-inner"><?= nl2br(htmlspecialchars($f['jawaban'])) ?></div></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php $flex_position = 'home_before_footer'; include theme_path('templates/partials/flex-content.php'); ?>

<!-- ===== HOMEPAGE BLOG ===== -->
<?php
$hb_enabled = get_setting('homepage_blog_enabled', '0') === '1';
if ($hb_enabled):
  $hb_count = max(1, (int) get_setting('homepage_blog_count', '3'));
  $hb_type  = get_setting('homepage_blog_type', 'recent');
  $order_sql = match($hb_type) { 'popular' => 'views DESC, created_at DESC', 'random' => 'RAND()', default => 'created_at DESC' };
  try {
    $hb_posts = $db->fetchAll("SELECT b.*, u.nama as author_nama FROM blog b LEFT JOIN users u ON u.id=b.user_id WHERE b.status='published' ORDER BY $order_sql LIMIT $hb_count");
  } catch (Throwable $e) { $hb_posts = []; }
  if (!empty($hb_posts)):
?>
<section class="section bg-cream">
  <div class="container">
    <div class="sec-head center" data-reveal>
      <span class="eyebrow center"><?= htmlspecialchars(get_setting('homepage_blog_label', 'Latest Articles')) ?></span>
      <h2><?= nl2br(htmlspecialchars(get_setting('homepage_blog_title', 'Insights & Tips dari Blog Kami'))) ?></h2>
    </div>
    <div class="blog-grid" style="margin-top:48px" data-stagger>
      <?php foreach ($hb_posts as $bp): ?>
      <a href="<?= url('/blog/'.$bp['slug']) ?>" class="blog-card">
        <div class="blog-card-img">
          <?php if ($bp['gambar_utama']): ?><img src="<?= uploads_url($bp['gambar_utama']) ?>" alt="<?= htmlspecialchars($bp['judul']) ?>" loading="lazy"><?php else: ?><div class="blog-card-noimg">✳</div><?php endif; ?>
        </div>
        <div class="blog-card-body">
          <div class="blog-card-meta"><?= date('d M Y', strtotime($bp['created_at'])) ?><?= $bp['author_nama'] ? ' · '.htmlspecialchars($bp['author_nama']) : '' ?></div>
          <h3 class="blog-card-title"><?= htmlspecialchars($bp['judul']) ?></h3>
          <p class="blog-card-ex"><?= htmlspecialchars(excerpt($bp['excerpt'] ?: strip_tags($bp['konten']), 100)) ?></p>
          <span class="blog-card-cta">Baca Artikel →</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <div class="text-center" style="margin-top:40px"><a href="<?= url('/blog') ?>" class="btn ghost">Lihat Semua Artikel</a></div>
  </div>
</section>
<?php endif; endif; ?>

<?php include theme_path('templates/layouts/footer.php'); ?>
