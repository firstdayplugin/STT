<?php
if (!isset($db)) $db = Database::getInstance();
$site_name   = get_setting('site_name', 'Reklamenesia');
$site_logo   = get_setting('logo_dark') ?: get_setting('logo');
$footer_desc = get_setting('site_description', 'Reklamenesia adalah penyedia produk reklame yang menghadirkan solusi visual berkualitas untuk membantu brand tampil lebih berkesan.');
$site_email  = get_setting('site_email');
$site_phone  = get_setting('site_phone');
$site_address= get_setting('site_address');
$wa_number   = get_setting('wa_number');
$wa_link     = $wa_number ? wa_url($wa_number, get_setting('wa_text')) : url('/hubungi-kami');
$footer_copy = get_setting('footer_text', '© ' . date('Y') . ' ' . $site_name . '. All rights reserved.');
$footer_script = get_setting('custom_footer_script', '');

$cta_title = get_content('global', 'cta_title', "Take Your Business Growth\nFurther with " . $site_name . ".");
$cta_desc  = get_content('global', 'cta_desc', 'Mari bangun strategi visual yang memberikan hasil nyata bagi brand Anda.');
$cta_btn   = get_content('global', 'cta_btn', 'Book a Free Consultation');

$socials = [
  'instagram' => get_setting('sosial_instagram'),
  'facebook'  => get_setting('sosial_facebook'),
  'tiktok'    => get_setting('sosial_tiktok'),
  'youtube'   => get_setting('sosial_youtube'),
  'twitter'   => get_setting('sosial_twitter'),
  'linkedin'  => get_setting('sosial_linkedin'),
];
?>

<!-- CTA -->
<section class="cta" data-reveal="scale">
  <h2><?= nl2br(htmlspecialchars($cta_title)) ?></h2>
  <p><?= htmlspecialchars($cta_desc) ?></p>
  <a href="<?= $wa_link ?>" target="_blank" rel="noopener" class="btn red">
    <span class="ic"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
    <?= htmlspecialchars($cta_btn) ?>
  </a>
</section>

<!-- Footer -->
<?php
$footer_logo = get_setting('logo_dark'); // only an explicit dark logo overrides the asterisk mark
$fmark = '<svg class="mark" viewBox="0 0 100 100" fill="currentColor" aria-hidden="true"><g transform="translate(50 50)"><ellipse cx="0" cy="-25" rx="12.5" ry="25"/><g transform="rotate(60)"><ellipse cx="0" cy="-25" rx="12.5" ry="25"/></g><g transform="rotate(120)"><ellipse cx="0" cy="-25" rx="12.5" ry="25"/></g><g transform="rotate(180)"><ellipse cx="0" cy="-25" rx="12.5" ry="25"/></g><g transform="rotate(240)"><ellipse cx="0" cy="-25" rx="12.5" ry="25"/></g><g transform="rotate(300)"><ellipse cx="0" cy="-25" rx="12.5" ry="25"/></g></g></svg>';
$wm_text = strtoupper(preg_replace('/[^A-Za-z0-9 ]/', '', $site_name));
?>
<footer class="footer">
  <div class="footer-deco" aria-hidden="true"></div>
  <div class="footer-watermark" aria-hidden="true"><?= htmlspecialchars($wm_text) ?></div>
  <div class="footer-inner">

    <!-- Row 1: brand + email -->
    <div class="footer-row">
      <div class="footer-brand">
        <div class="footer-logo">
          <?php if ($footer_logo): ?>
            <img src="<?= uploads_url($footer_logo) ?>" alt="<?= htmlspecialchars($site_name) ?>">
          <?php else: ?>
            <?= $fmark ?><span class="wordmark"><?= htmlspecialchars($site_name) ?>.</span>
          <?php endif; ?>
        </div>
        <p class="footer-desc"><?= htmlspecialchars($footer_desc) ?></p>
      </div>
      <div class="footer-contact">
        <?php if ($site_email): ?>
          <div class="label">Contact us through E-mail</div>
          <a class="email" href="mailto:<?= htmlspecialchars($site_email) ?>"><?= htmlspecialchars($site_email) ?></a>
        <?php elseif ($site_phone): ?>
          <div class="label">Hubungi kami</div>
          <a class="email" href="<?= $wa_link ?>" target="_blank" rel="noopener"><?= htmlspecialchars($site_phone) ?></a>
        <?php endif; ?>
      </div>
    </div>

    <div class="footer-divider"></div>

    <!-- Row 2: newsletter + address -->
    <div class="footer-row">
      <div class="footer-stay">
        <h3>Stay Connected!</h3>
        <div class="footer-socials">
          <?php if ($wa_number): ?><a href="<?= $wa_link ?>" target="_blank" rel="noopener" aria-label="WhatsApp" style="background:<?= social_brand_color('whatsapp') ?>"><?= social_icon_svg('whatsapp') ?></a><?php endif; ?>
          <?php if ($site_email): ?><a href="mailto:<?= htmlspecialchars($site_email) ?>" aria-label="Email" style="background:<?= social_brand_color('email') ?>"><?= social_icon_svg('email') ?></a><?php endif; ?>
          <?php foreach (['instagram','facebook','tiktok','youtube','twitter','linkedin'] as $t): if (!empty($socials[$t])): ?>
            <a href="<?= htmlspecialchars($socials[$t]) ?>" target="_blank" rel="noopener" aria-label="<?= ucfirst($t) ?>" style="background:<?= social_brand_color($t) ?>"><?= social_icon_svg($t) ?></a>
          <?php endif; endforeach; ?>
        </div>
      </div>
      <div class="footer-addr">
        <?php if ($site_address): ?>
          <div class="label">Alamat:</div>
          <div class="val"><?= nl2br(htmlspecialchars($site_address)) ?></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="footer-copy"><?= htmlspecialchars($footer_copy) ?></div>
  </div>
</footer>

<?php include theme_path('templates/partials/wa-float.php'); ?>
<?= $footer_script ?>

<script>window.Lenis&&0;</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lenis/1.1.13/lenis.min.js" defer></script>
<script src="<?= theme_url('assets/js/main.js') ?>?v=1.7" defer></script>
</body>
</html>
