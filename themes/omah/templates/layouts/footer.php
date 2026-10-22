<?php
/* ============================================================
   OMAH THEME — Footer
   FIX #3: Logo ONLY — no duplicate site name text next to logo.
   If logo uploaded → show logo only.
   If no logo → show site name text only.
   ============================================================ */
if (!isset($db)) $db = Database::getInstance();
$site_name    = get_setting('site_name', 'DIFA Property');
$logo_dark    = get_setting('logo_dark') ?: get_setting('logo'); // white logo for dark bg
$site_email   = get_setting('site_email');
$site_phone   = get_setting('site_phone');
$site_address = get_setting('site_address');
$wa_number    = get_setting('wa_number');
$wa_link      = $wa_number ? wa_url($wa_number, get_setting('wa_text','Halo, saya ingin konsultasi properti.')) : url('/hubungi-kami');
$footer_copy  = get_setting('footer_text', '© ' . date('Y') . ' ' . $site_name . '. All Rights Reserved.');
$footer_script = get_setting('custom_footer_script', '');

$socials = [
  'facebook'  => get_setting('sosial_facebook'),
  'instagram' => get_setting('sosial_instagram'),
  'youtube'   => get_setting('sosial_youtube'),
  'linkedin'  => get_setting('sosial_linkedin'),
  'tiktok'    => get_setting('sosial_tiktok'),
];
?>

<!-- ===== FOOTER ===== -->
<footer class="footer">
  <div class="footer-inner">

    <!-- Left: brand + contacts -->
    <div class="footer-left">

      <!-- FIX: Logo ONLY — no text if logo uploaded -->
      <div class="footer-brand">
        <?php if ($logo_dark): ?>
          <!-- Logo contains company name → show logo only, NO extra text -->
          <img src="<?= uploads_url($logo_dark) ?>" alt="<?= htmlspecialchars($site_name) ?>" style="height:48px;width:auto;">
        <?php else: ?>
          <!-- No logo uploaded → show site name text only -->
          <span class="site-name" style="font-size:24px;font-weight:600;color:var(--om-gray-mid);letter-spacing:.04em;text-transform:uppercase;">
            <?= htmlspecialchars($site_name) ?>
          </span>
        <?php endif; ?>
      </div>

      <div class="footer-contacts">
        <?php if ($site_phone): ?>
          <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/','',$site_phone)) ?>"><?= htmlspecialchars($site_phone) ?></a>
        <?php endif; ?>
        <?php if ($site_email): ?>
          <a href="mailto:<?= htmlspecialchars($site_email) ?>"><?= htmlspecialchars($site_email) ?></a>
        <?php endif; ?>
      </div>

      <?php if ($site_address): ?>
        <div class="footer-address"><?= nl2br(htmlspecialchars($site_address)) ?></div>
      <?php endif; ?>
    </div>

    <!-- Right: social icons + copyright -->
    <div class="footer-right">
      <div class="footer-socials">
        <?php if ($wa_number): ?>
          <a href="<?= $wa_link ?>" target="_blank" rel="noopener" class="footer-social-ic" aria-label="WhatsApp">
            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.22-.64.08-.3-.15-1.26-.46-2.39-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.92-2.21-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35M12.05 21.79a9.87 9.87 0 01-5.04-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 01-1.51-5.26c0-5.45 4.44-9.88 9.89-9.88 2.64 0 5.12 1.03 6.99 2.9a9.83 9.83 0 012.89 6.99c0 5.45-4.44 9.88-9.89 9.88m8.41-18.3A11.82 11.82 0 0012.05 0C5.5 0 .16 5.34.16 11.89c0 2.1.55 4.14 1.59 5.95L.06 24l6.3-1.65a11.88 11.88 0 005.69 1.45h.01c6.55 0 11.89-5.34 11.89-11.89 0-3.18-1.24-6.17-3.48-8.42"/></svg>
          </a>
        <?php endif; ?>
        <?php foreach ($socials as $platform => $url): if (!$url) continue; ?>
          <a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener" class="footer-social-ic" aria-label="<?= ucfirst($platform) ?>">
            <?php if ($platform === 'facebook'): ?>
              <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            <?php elseif ($platform === 'instagram'): ?>
              <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
            <?php elseif ($platform === 'youtube'): ?>
              <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            <?php elseif ($platform === 'linkedin'): ?>
              <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            <?php elseif ($platform === 'tiktok'): ?>
              <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.75a4.85 4.85 0 01-1.01-.06z"/></svg>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="footer-copy"><?= htmlspecialchars($footer_copy) ?></div>
    </div>
  </div>
</footer>

<?php include theme_path('templates/partials/wa-float.php'); ?>
<?= $footer_script ?>

<script src="<?= theme_url('assets/js/main.js') ?>?v=1.1" defer></script>
</body>
</html>
