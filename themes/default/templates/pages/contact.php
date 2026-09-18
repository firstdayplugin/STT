<?php
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('kontak');
$seo = [
    'title'       => $page_seo['meta_title'] ?? seo_title('Contact Us'),
    'description' => $page_seo['meta_description'] ?? 'Tim kami siap membantu Anda menemukan solusi reklame terbaik. Konsultasi gratis, tanpa biaya tersembunyi.',
];

$site_email   = get_setting('site_email');
$site_phone   = get_setting('site_phone');
$site_address = get_setting('site_address');
$maps_embed   = get_setting('site_maps_embed');
$wa_contacts  = $db->fetchAll("SELECT * FROM wa_contacts WHERE is_active = 1 ORDER BY urutan ASC");
$wa_main      = wa_url(get_setting('wa_number'), get_setting('wa_text'));

include theme_path('templates/layouts/header.php');
?>

<section class="page-hero-wrap">
  <?php include theme_path('templates/partials/navbar.php'); ?>
  <div class="page-hero">
  <div class="page-hero-breadcrumb">
    <a href="<?= url('/') ?>">Home</a>
    <span class="sep">/</span>
    <span>Contact Us</span>
  </div>
  <h1 class="page-hero-title"><?= htmlspecialchars(get_content('kontak', 'hero_title', 'Contact Us')) ?></h1>
  <p class="page-hero-desc"><?= htmlspecialchars(get_content('kontak', 'hero_desc', 'Tim kami siap membantu Anda menemukan solusi reklame terbaik. Konsultasi gratis, tanpa biaya tersembunyi.')) ?></p>
</div>
</section>

<section class="contact-section">
  <div class="contact-grid">
    <div class="contact-info-card">
      <div>
        <div class="section-label">Get In Touch</div>
        <h3 style="font-size:24px;font-weight:700;margin-bottom:8px">Mari Terhubung</h3>
        <p style="color:var(--text-muted);font-size:14px;line-height:1.6;margin-bottom:24px">
          Konsultasikan kebutuhan reklame Anda dengan tim kami melalui kontak berikut.
        </p>
      </div>
      
      <?php if ($site_email): ?>
      <div class="contact-info-item">
        <div class="contact-info-icon"><?= social_icon_svg('email') ?></div>
        <div>
          <div class="contact-info-label">Email</div>
          <a href="mailto:<?= htmlspecialchars($site_email) ?>" class="contact-info-value">
            <?= htmlspecialchars($site_email) ?>
          </a>
        </div>
      </div>
      <?php endif; ?>
      
      <?php if ($site_phone): ?>
      <div class="contact-info-item">
        <div class="contact-info-icon"><?= social_icon_svg('whatsapp') ?></div>
        <div>
          <div class="contact-info-label">Telepon / WhatsApp</div>
          <a href="<?= $wa_main ?>" target="_blank" class="contact-info-value">
            <?= htmlspecialchars($site_phone) ?>
          </a>
        </div>
      </div>
      <?php endif; ?>
      
      <?php if ($site_address): ?>
      <div class="contact-info-item">
        <div class="contact-info-icon"><svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 0C7.802 0 4 3.403 4 7.602 4 11.8 7.469 16.812 12 24c4.531-7.188 8-12.2 8-16.398C20 3.403 16.199 0 12 0zm0 11a3 3 0 110-6 3 3 0 010 6z"/></svg></div>
        <div>
          <div class="contact-info-label">Alamat</div>
          <div class="contact-info-value" style="font-size:13px;font-weight:400;color:var(--text-muted);line-height:1.6">
            <?= nl2br(htmlspecialchars($site_address)) ?>
          </div>
        </div>
      </div>
      <?php endif; ?>
      
      <a href="<?= $wa_main ?>" target="_blank" rel="noopener" class="wa-cta-button"
         style="margin-top:8px;background:#25D366;color:#fff;padding:15px 20px;border-radius:50px;text-align:center;font-weight:700;font-size:15px;display:flex;align-items:center;justify-content:center;gap:10px;box-shadow:0 6px 18px rgba(37,211,102,0.35);transition:all 0.2s">
        <span style="display:inline-flex;width:22px;height:22px"><?= social_icon_svg('whatsapp') ?></span>
        Chat WhatsApp Sekarang
      </a>
    </div>
    
    <div>
      <?php if ($maps_embed): ?>
      <div style="border-radius:var(--radius-md);overflow:hidden;height:500px">
        <iframe src="<?= htmlspecialchars($maps_embed) ?>" 
                width="100%" height="100%" style="border:0" 
                allowfullscreen loading="lazy"></iframe>
      </div>
      <?php else: ?>
      <div style="background:var(--bg-cream-2);border-radius:var(--radius-md);height:500px;display:flex;align-items:center;justify-content:center;color:var(--text-muted)">
        <div style="text-align:center">
          <div style="margin-bottom:12px;color:var(--accent)"><svg viewBox="0 0 24 24" fill="currentColor" width="48" height="48"><path d="M12 0C7.802 0 4 3.403 4 7.602 4 11.8 7.469 16.812 12 24c4.531-7.188 8-12.2 8-16.398C20 3.403 16.199 0 12 0zm0 11a3 3 0 110-6 3 3 0 010 6z"/></svg></div>
          <div>Peta lokasi akan ditampilkan di sini</div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if (!empty($wa_contacts) && count($wa_contacts) > 1): ?>
<section style="padding: 0 0 80px">
  <div class="container">
    <div style="text-align:center;margin-bottom:32px">
      <div class="section-label" style="justify-content:center">Customer Service</div>
      <h2 class="section-title">Tim Kami Siap Membantu</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;max-width:900px;margin:0 auto;padding:0 24px">
      <?php foreach ($wa_contacts as $wa): ?>
      <a href="<?= wa_url($wa['nomor'], get_setting('wa_text')) ?>" target="_blank"
         style="background:white;border:1px solid var(--border);border-radius:var(--radius-md);padding:24px;text-align:center;transition:all 0.2s"
         onmouseover="this.style.transform='translateY(-4px)';this.style.borderColor='var(--accent)'"
         onmouseout="this.style.transform='';this.style.borderColor='var(--border)'">
        <div style="width:56px;height:56px;background:#25D366;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px"><span style="width:26px;height:26px;display:inline-flex"><?= social_icon_svg('whatsapp') ?></span></div>
        <div style="font-weight:600;font-size:15px;margin-bottom:4px"><?= htmlspecialchars($wa['nama']) ?></div>
        <div style="font-size:12px;color:var(--text-muted);margin-bottom:12px"><?= htmlspecialchars($wa['deskripsi']) ?></div>
        <div style="background:#25D366;color:white;padding:8px;border-radius:30px;font-size:13px;font-weight:600">
          Chat Sekarang
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include theme_path('templates/layouts/footer.php'); ?>
