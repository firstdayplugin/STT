<?php
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('kontak');
$seo = [
  'title'       => $page_seo['meta_title']       ?? seo_title('Contact Us'),
  'description' => $page_seo['meta_description'] ?? 'Tim kami siap membantu menemukan solusi reklame terbaik. Konsultasi gratis.',
];
$site_email   = get_setting('site_email');
$site_phone   = get_setting('site_phone');
$site_address = get_setting('site_address');
$maps_embed   = get_setting('site_maps_embed');
$wa_contacts  = $db->fetchAll("SELECT * FROM wa_contacts WHERE is_active = 1 ORDER BY urutan ASC");
$wa_main      = wa_url(get_setting('wa_number'), get_setting('wa_text'));
$pin_ic = '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 0C7.8 0 4 3.4 4 7.6 4 11.8 7.47 16.81 12 24c4.53-7.19 8-12.2 8-16.4C20 3.4 16.2 0 12 0zm0 11a3 3 0 110-6 3 3 0 010 6z"/></svg>';
include theme_path('templates/layouts/header.php');
?>

<section class="page-hero">
  <div class="inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><span>Contact Us</span></div>
    <h1><?= htmlspecialchars(get_content('kontak', 'hero_title', 'Hubungi Kami')) ?></h1>
    <p><?= htmlspecialchars(get_content('kontak', 'hero_desc', 'Tim kami siap membantu Anda menemukan solusi reklame terbaik. Konsultasi gratis, tanpa biaya tersembunyi.')) ?></p>
  </div>
</section>

<section class="section bg-white">
  <div class="container">
    <div class="about-grid">
      <div data-reveal="left">
        <span class="eyebrow">Get In Touch</span>
        <h2 style="font-size:var(--fs-h3);margin-top:14px">Mari Terhubung</h2>
        <p class="lead" style="margin-top:14px;font-size:15px">Konsultasikan kebutuhan reklame Anda dengan tim kami melalui kontak berikut.</p>

        <div style="margin-top:28px;display:flex;flex-direction:column;gap:18px">
          <?php if ($site_email): ?>
          <div style="display:flex;gap:14px;align-items:center">
            <span class="stat-ic"><?= social_icon_svg('email') ?></span>
            <div><div class="testi-role">Email</div><a href="mailto:<?= htmlspecialchars($site_email) ?>" style="font-weight:600"><?= htmlspecialchars($site_email) ?></a></div>
          </div>
          <?php endif; ?>
          <?php if ($site_phone): ?>
          <div style="display:flex;gap:14px;align-items:center">
            <span class="stat-ic"><?= social_icon_svg('whatsapp') ?></span>
            <div><div class="testi-role">Telepon / WhatsApp</div><a href="<?= $wa_main ?>" target="_blank" rel="noopener" style="font-weight:600"><?= htmlspecialchars($site_phone) ?></a></div>
          </div>
          <?php endif; ?>
          <?php if ($site_address): ?>
          <div style="display:flex;gap:14px;align-items:flex-start">
            <span class="stat-ic"><?= $pin_ic ?></span>
            <div><div class="testi-role">Alamat</div><div style="font-size:14px;color:var(--rk-text-soft);line-height:1.6"><?= nl2br(htmlspecialchars($site_address)) ?></div></div>
          </div>
          <?php endif; ?>
        </div>

        <a href="<?= $wa_main ?>" target="_blank" rel="noopener" class="btn red" style="margin-top:30px"><span class="ic"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>Chat WhatsApp Sekarang</a>
      </div>

      <div data-reveal="right">
        <?php if ($maps_embed): ?>
          <div style="border-radius:var(--r-lg);overflow:hidden;height:480px"><iframe src="<?= htmlspecialchars($maps_embed) ?>" width="100%" height="100%" style="border:0" allowfullscreen loading="lazy"></iframe></div>
        <?php else: ?>
          <div style="background:var(--rk-cream);border-radius:var(--r-lg);height:480px;display:grid;place-items:center;color:var(--rk-text-mute)">
            <div style="text-align:center"><div style="color:var(--rk-red);margin-bottom:10px"><svg viewBox="0 0 24 24" fill="currentColor" width="48" height="48"><path d="M12 0C7.8 0 4 3.4 4 7.6 4 11.8 7.47 16.81 12 24c4.53-7.19 8-12.2 8-16.4C20 3.4 16.2 0 12 0zm0 11a3 3 0 110-6 3 3 0 010 6z"/></svg></div>Peta lokasi akan tampil di sini</div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php if (!empty($wa_contacts) && count($wa_contacts) > 1): ?>
<section class="section bg-cream">
  <div class="container">
    <div class="sec-head center" data-reveal><span class="eyebrow center">Customer Service</span><h2>Tim Kami Siap Membantu</h2></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;max-width:920px;margin:40px auto 0" data-stagger>
      <?php foreach ($wa_contacts as $wa): ?>
      <a href="<?= wa_url($wa['nomor'], get_setting('wa_text')) ?>" target="_blank" rel="noopener" class="stat" style="text-align:center;align-items:center;border-radius:var(--r-lg)">
        <span class="wa-avatar" style="margin:0 auto"><svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0a12 12 0 100 24 12 12 0 000-24zm0 6a3 3 0 110 6 3 3 0 010-6zm0 14.2a7.2 7.2 0 01-5.5-2.6c.03-1.8 3.66-2.8 5.5-2.8s5.47 1 5.5 2.8A7.2 7.2 0 0112 20.2z"/></svg></span>
        <div><div class="wa-cname"><?= htmlspecialchars($wa['nama']) ?></div><div class="testi-role"><?= htmlspecialchars($wa['deskripsi']) ?></div></div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include theme_path('templates/layouts/footer.php'); ?>
