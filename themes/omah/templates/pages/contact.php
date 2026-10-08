<?php
/* ============================================================
   OMAH THEME — Hubungi Kami
   FIX #5: Right column = Google Maps embed ONLY.
   No extra card. Consistent with other themes.
   ============================================================ */
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('contact');
$seo = [
  'title'       => $page_seo['meta_title']       ?? seo_title('Hubungi Kami'),
  'description' => $page_seo['meta_description'] ?? 'Hubungi tim DIFA Property untuk konsultasi properti gratis.',
];
$site_name    = get_setting('site_name', 'DIFA Property');
$site_phone   = get_setting('site_phone');
$site_email   = get_setting('site_email');
$site_address = get_setting('site_address');
$wa_number    = get_setting('wa_number');
$wa_link      = $wa_number ? wa_url($wa_number, get_setting('wa_text','Halo, saya ingin konsultasi properti.')) : '#';

// Google Maps embed — support both embed URL and iframe HTML
$maps_embed   = get_setting('maps_embed', '');   // iframe HTML or src URL
$maps_src     = '';
if ($maps_embed) {
  // If it's already an iframe tag, extract src
  if (preg_match('/src="([^"]+)"/i', $maps_embed, $m)) {
    $maps_src = $m[1];
  } else {
    $maps_src = $maps_embed; // assume it's already a URL
  }
}

// Fallback: build search URL from address
if (!$maps_src && $site_address) {
  $maps_src = 'https://maps.google.com/maps?q=' . urlencode($site_address) . '&output=embed&hl=id&z=15';
}

include theme_path('templates/layouts/header.php');
?>
<?php $flex_position = 'contact_top'; include theme_path('templates/partials/flex-content.php'); ?>

<div class="page-hero">
  <div class="page-hero-inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><span>Hubungi Kami</span></div>
    <h1><?= htmlspecialchars(get_content('contact','hero_title','Hubungi Kami')) ?></h1>
    <p><?= htmlspecialchars(get_content('contact','hero_desc','Tim kami siap membantu konsultasi properti Anda.')) ?></p>
  </div>
</div>

<section style="padding-block:var(--sec-y);padding-inline:var(--pad-x);background:var(--om-white);">
  <div style="max-width:var(--container-max);margin-inline:auto;display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:flex-start;">

    <!-- Left: contact info -->
    <div data-reveal="left">
      <h2 style="font-size:clamp(24px,2.5vw,36px);font-weight:600;margin-bottom:32px;text-transform:none;">Informasi Kontak</h2>
      <div style="display:flex;flex-direction:column;gap:20px;">

        <?php if ($site_phone): ?>
        <div style="display:flex;align-items:flex-start;gap:12px;">
          <div style="width:40px;height:40px;background:var(--om-gray-lt);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--om-dark)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8a19.79 19.79 0 01-3.07-8.7A2 2 0 012 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.91 7.09a16 16 0 006 6l.94-.94a2 2 0 012.11-.45c.93.369 1.877.662 2.81.7A2 2 0 0122 14.92v2z"/></svg>
          </div>
          <div>
            <div style="font-size:13px;font-weight:400;color:var(--om-gray);margin-bottom:2px;">Telepon</div>
            <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/','',$site_phone)) ?>" style="font-size:18px;font-weight:600;color:var(--om-dark);"><?= htmlspecialchars($site_phone) ?></a>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($site_email): ?>
        <div style="display:flex;align-items:flex-start;gap:12px;">
          <div style="width:40px;height:40px;background:var(--om-gray-lt);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--om-dark)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </div>
          <div>
            <div style="font-size:13px;font-weight:400;color:var(--om-gray);margin-bottom:2px;">Email</div>
            <a href="mailto:<?= htmlspecialchars($site_email) ?>" style="font-size:18px;font-weight:600;color:var(--om-dark);"><?= htmlspecialchars($site_email) ?></a>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($site_address): ?>
        <div style="display:flex;align-items:flex-start;gap:12px;">
          <div style="width:40px;height:40px;background:var(--om-gray-lt);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--om-dark)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          </div>
          <div>
            <div style="font-size:13px;font-weight:400;color:var(--om-gray);margin-bottom:2px;">Alamat</div>
            <div style="font-size:16px;font-weight:300;line-height:1.5em;color:var(--om-dark);"><?= nl2br(htmlspecialchars($site_address)) ?></div>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($wa_number): ?>
        <a href="<?= $wa_link ?>" target="_blank" rel="noopener"
           style="display:inline-flex;align-items:center;gap:10px;background:#25d366;color:#fff;padding:14px 28px;border-radius:99px;font-size:16px;font-weight:600;text-decoration:none;margin-top:8px;width:fit-content;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.22-.64.08-.3-.15-1.26-.46-2.39-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.92-2.21-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35M12.05 21.79a9.87 9.87 0 01-5.04-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 01-1.51-5.26c0-5.45 4.44-9.88 9.89-9.88 2.64 0 5.12 1.03 6.99 2.9a9.83 9.83 0 012.89 6.99c0 5.45-4.44 9.88-9.89 9.88m8.41-18.3A11.82 11.82 0 0012.05 0C5.5 0 .16 5.34.16 11.89c0 2.1.55 4.14 1.59 5.95L.06 24l6.3-1.65a11.88 11.88 0 005.69 1.45h.01c6.55 0 11.89-5.34 11.89-11.89 0-3.18-1.24-6.17-3.48-8.42"/></svg>
          Chat WhatsApp
        </a>
        <?php endif; ?>

      </div>
    </div>

    <!-- Right: Google Maps embed ONLY — no extra cards -->
    <div data-reveal="right">
      <?php if ($maps_src): ?>
        <div style="width:100%;height:420px;border-radius:var(--radius-lg);overflow:hidden;border:1px solid var(--om-border);">
          <iframe
            src="<?= htmlspecialchars($maps_src) ?>"
            width="100%"
            height="100%"
            style="border:0;display:block;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      <?php else: ?>
        <!-- Placeholder when no maps configured -->
        <div style="width:100%;height:420px;border-radius:var(--radius-lg);background:var(--om-gray-lt);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;border:1px solid var(--om-border);">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--om-gray)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <p style="font-size:14px;color:var(--om-gray);text-align:center;max-width:200px;">Tambahkan embed URL Google Maps di Admin → Pengaturan → Maps Embed</p>
        </div>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php $flex_position = 'contact_bottom'; include theme_path('templates/partials/flex-content.php'); ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
