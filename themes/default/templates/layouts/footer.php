<?php
if (!isset($db)) $db = Database::getInstance();
$site_name  = get_setting('site_name', 'Reklamepedia');
$site_logo  = get_setting('logo');
$logo_footer = get_setting('logo_footer') ?: get_setting('logo_dark') ?: $site_logo;
$footer_desc= get_setting('site_description', 'Reklamepedia adalah perusahaan advertising berpengalaman lebih dari 15 tahun yang menghadirkan solusi reklame berkualitas untuk meningkatkan visibilitas brand di seluruh Indonesia.');
$site_email = get_setting('site_email');
$site_phone = get_setting('site_phone');
$ig = get_setting('sosial_instagram');
$fb = get_setting('sosial_facebook');
$tt = get_setting('sosial_tiktok');
$yt = get_setting('sosial_youtube');
$tw = get_setting('sosial_twitter');
$ln = get_setting('sosial_linkedin');
$footer_copy = get_setting('footer_text', '© ' . date('Y') . ' ' . $site_name . '. All rights reserved.');
$footer_script = get_setting('custom_footer_script', '');
$wa_number = get_setting('wa_number');
$wa_link   = $wa_number ? wa_url($wa_number, get_setting('wa_text')) : '#';
?>

<!-- CTA + Footer combined (dark rounded top section) -->
<div class="cta-footer-wrap">
  <section class="cta-section">
    <h2 class="cta-title">Let's Elevate<br>Your Brand Visibility.</h2>
    <p class="cta-desc">
      Solusi reklame efektif untuk meningkatkan daya tarik<br>dan eksistensi brand Anda.
    </p>
    <a href="<?= $wa_link ?>" target="_blank" class="cta-button">
      <span class="arrow-icon">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
        </svg>
      </span>
      Hubungi Kami!
    </a>
  </section>

  <div class="footer-divider"></div>

  <div class="footer-grid">
    <div class="footer-col">
      <div class="footer-brand-logo">
        <?php if ($logo_footer): ?>
          <img src="<?= uploads_url($logo_footer) ?>" alt="<?= htmlspecialchars($site_name) ?>" style="height:36px">
        <?php else: 
          $f_first = mb_substr($site_name, 0, 1); $f_rest = mb_substr($site_name, 1);
        ?>
          <span class="logo-r"><?= htmlspecialchars($f_first) ?></span><span><?= htmlspecialchars($f_rest) ?></span>
        <?php endif; ?>
      </div>
    </div>

    <div class="footer-col">
      <?php if ($site_email): ?>
        <h5>E-Mail</h5>
        <p><?= htmlspecialchars($site_email) ?></p>
      <?php endif; ?>
      <?php if ($site_phone): ?>
        <h5 style="margin-top:16px">Phone / WhatsApp</h5>
        <p><a href="<?= wa_url($wa_number ?: $site_phone, get_setting('wa_text')) ?>" target="_blank" rel="noopener" style="color:inherit;text-decoration:none;border-bottom:1px dashed rgba(255,255,255,0.3)"><?= htmlspecialchars($site_phone) ?></a></p>
      <?php endif; ?>
    </div>

    <div class="footer-col">
      <p class="footer-desc"><?= htmlspecialchars($footer_desc) ?></p>
      <div class="footer-social">
        <?php if ($wa_number || $site_phone): ?>
          <a href="<?= wa_url($wa_number ?: $site_phone, get_setting('wa_text')) ?>" target="_blank" rel="noopener" class="social-btn" style="background:<?= social_brand_color('whatsapp') ?>" aria-label="WhatsApp"><?= social_icon_svg('whatsapp') ?></a>
        <?php endif; ?>
        <?php if ($site_email): ?><a href="mailto:<?= htmlspecialchars($site_email) ?>" class="social-btn" style="background:<?= social_brand_color('email') ?>" aria-label="Email"><?= social_icon_svg('email') ?></a><?php endif; ?>
        <?php if ($ig): ?><a href="<?= htmlspecialchars($ig) ?>" target="_blank" rel="noopener" class="social-btn" style="background:<?= social_brand_color('instagram') ?>" aria-label="Instagram"><?= social_icon_svg('instagram') ?></a><?php endif; ?>
        <?php if ($fb): ?><a href="<?= htmlspecialchars($fb) ?>" target="_blank" rel="noopener" class="social-btn" style="background:<?= social_brand_color('facebook') ?>" aria-label="Facebook"><?= social_icon_svg('facebook') ?></a><?php endif; ?>
        <?php if ($tt): ?><a href="<?= htmlspecialchars($tt) ?>" target="_blank" rel="noopener" class="social-btn" style="background:<?= social_brand_color('tiktok') ?>" aria-label="TikTok"><?= social_icon_svg('tiktok') ?></a><?php endif; ?>
        <?php if ($yt): ?><a href="<?= htmlspecialchars($yt) ?>" target="_blank" rel="noopener" class="social-btn" style="background:<?= social_brand_color('youtube') ?>" aria-label="YouTube"><?= social_icon_svg('youtube') ?></a><?php endif; ?>
        <?php if ($tw): ?><a href="<?= htmlspecialchars($tw) ?>" target="_blank" rel="noopener" class="social-btn" style="background:<?= social_brand_color('twitter') ?>" aria-label="Twitter/X"><?= social_icon_svg('twitter') ?></a><?php endif; ?>
        <?php if ($ln): ?><a href="<?= htmlspecialchars($ln) ?>" target="_blank" rel="noopener" class="social-btn" style="background:<?= social_brand_color('linkedin') ?>" aria-label="LinkedIn"><?= social_icon_svg('linkedin') ?></a><?php endif; ?>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <?= htmlspecialchars($footer_copy) ?>
  </div>
</div>

<?php include theme_path('templates/partials/wa-float.php'); ?>

<?= $footer_script ?>

<script>
// FAQ accordion
document.querySelectorAll('.faq-item').forEach(item => {
  item.addEventListener('click', () => {
    const wasActive = item.classList.contains('active');
    document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
    if (!wasActive) item.classList.add('active');
  });
});
</script>

</body>
</html>
