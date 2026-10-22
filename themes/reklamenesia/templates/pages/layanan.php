<?php
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('layanan');
$seo = [
  'title'       => $page_seo['meta_title']       ?? seo_title('Layanan'),
  'description' => $page_seo['meta_description'] ?? 'Layanan reklame & signage lengkap dari Reklamenesia.',
];
$layanan_list = $db->fetchAll("SELECT * FROM layanan WHERE is_active = 1 ORDER BY urutan ASC");
include theme_path('templates/layouts/header.php');
?>

<section class="page-hero">
  <div class="inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><span>Services</span></div>
    <h1><?= htmlspecialchars(get_content('layanan', 'hero_title', 'Layanan Kami')) ?></h1>
    <p><?= htmlspecialchars(get_content('layanan', 'hero_desc', 'Kami menyediakan layanan lengkap mulai dari Neon Box, Billboard, Huruf Timbul, Pylon Sign, LED Flex, hingga Car Branding.')) ?></p>
  </div>
</section>

<?php $flex_position = 'layanan_top'; include theme_path('templates/partials/flex-content.php'); ?>

<section class="services" style="background:var(--rk-paper)">
  <div class="services-watermark" aria-hidden="true">SERVICES</div>
  <div class="container">
    <?php if (!empty($layanan_list)): ?>
    <div class="svc-grid" data-stagger>
      <?php foreach ($layanan_list as $i => $l): ?>
      <a href="<?= url('/layanan/'.$l['slug']) ?>" class="svc-card">
        <?php if (!empty($l['gambar'])): ?><img src="<?= uploads_url($l['gambar']) ?>" alt="<?= htmlspecialchars($l['nama']) ?>" loading="lazy"><?php endif; ?>
        <span class="chip-arrow"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
        <div class="svc-card-meta">
          <span class="svc-card-num"><?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?></span>
          <span class="svc-card-title"><?= htmlspecialchars($l['nama']) ?></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
      <div class="empty-state">Belum ada layanan yang ditambahkan.</div>
    <?php endif; ?>
  </div>
</section>

<!-- Why choose us -->
<section class="section bg-white">
  <div class="container">
    <div class="sec-head center" data-reveal>
      <span class="eyebrow center"><?= htmlspecialchars(get_content('layanan', 'why_label', 'Why Choose Us')) ?></span>
      <h2><?= nl2br(htmlspecialchars(get_content('layanan', 'why_title', 'Where Quality Meets Reliability'))) ?></h2>
      <p class="lead" style="margin-inline:auto"><?= htmlspecialchars(get_content('layanan', 'why_desc', 'Setiap proyek dirancang untuk memperkuat visibilitas brand dan bertahan dalam jangka panjang.')) ?></p>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:48px" data-stagger>
      <?php
      $why = [
        ['why_card1', $about1 = get_setting('about_foto_1','')],
        ['why_card2', get_setting('about_foto_2','')],
        ['why_card3', get_setting('about_foto_3','')],
      ];
      $why_default = [
        'Berpengalaman lebih dari 15 tahun dan dipercaya ratusan klien dengan tingkat kepuasan tinggi.',
        'Dikerjakan tim ahli berpengalaman — profesional, tepat waktu, dan presisi tinggi.',
        'Setiap produk dilengkapi garansi dan layanan purna jual sebagai komitmen kualitas kami.',
      ];
      foreach ($why as $k => $w): ?>
        <div class="svc-card" style="aspect-ratio:4/5;min-height:300px;<?= $w[1] ? '' : 'background:var(--rk-ink-3)' ?>">
          <?php if ($w[1]): ?><img src="<?= uploads_url($w[1]) ?>" alt=""><?php endif; ?>
          <div class="svc-card-meta"><div><span class="svc-card-title" style="font-size:17px;font-weight:500;line-height:1.4"><?= htmlspecialchars(get_content('layanan', $w[0], $why_default[$k])) ?></span></div></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php $flex_position = 'layanan_bottom'; include theme_path('templates/partials/flex-content.php'); ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
