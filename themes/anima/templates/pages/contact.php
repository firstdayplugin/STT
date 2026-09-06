<?php
/**
 * Anima theme — Contact page (route: /hubungi-kami). Built from the Figma "Contact Us" design.
 * Labels/copy are editable via ac('contact', key); contact values come from get_setting() (white-label).
 * Shares layouts/header.php (nav solid via page-inner) and layouts/footer.php with all pages.
 */
if (!isset($db) && class_exists('Database')) { $db = Database::getInstance(); }

$seo = [
  'title'       => get_setting('site_title_contact', 'Contact Us — ' . get_setting('site_name', 'Sapta Tunas Teknologi')),
  'description' => get_setting('site_description_contact', 'Hubungi Sapta Tunas Teknologi. Konsultasikan kebutuhan solusi IT, cloud, cybersecurity, dan data & AI Anda.'),
];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');

$address  = get_setting('site_address', '');
$phone    = get_setting('site_phone', '');
$prophone = get_setting('site_phone_prosupport', '');
$wa_num   = preg_replace('/\D/', '', (string) get_setting('wa_number', ''));
$wa_disp  = get_setting('wa_display', get_setting('wa_number', ''));
$email    = get_setting('site_email', '');
$proemail = get_setting('site_email_prosupport', '');
$maps     = get_setting('site_maps_embed', '');
$socials  = [
  ['url'=>get_setting('linkedin_url', ''),  'path'=>'<path d="M4.98 3.5A2.5 2.5 0 002.5 6a2.5 2.5 0 105 0 2.5 2.5 0 00-2.52-2.5zM3 9h4v12H3zM10 9h3.8v1.7h.05c.53-1 1.83-2.06 3.77-2.06 4.03 0 4.78 2.65 4.78 6.1V21h-4v-5.4c0-1.29-.02-2.95-1.8-2.95-1.8 0-2.08 1.4-2.08 2.85V21h-4z"/>'],
  ['url'=>get_setting('instagram_url', ''), 'path'=>'<path d="M12 2.2c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 01-1.38-.9 3.7 3.7 0 01-.9-1.38c-.16-.42-.36-1.06-.41-2.23C2.21 15.58 2.2 15.2 2.2 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.21 8.8 2.2 12 2.2zm0 3.2A6.6 6.6 0 1012 18.6 6.6 6.6 0 0012 5.4zm0 10.9A4.3 4.3 0 1112 7.7a4.3 4.3 0 010 8.6zm6.85-11.2a1.54 1.54 0 11-3.08 0 1.54 1.54 0 013.08 0z"/>'],
  ['url'=>get_setting('facebook_url', ''),  'path'=>'<path d="M22 12a10 10 0 10-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.78-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0022 12z"/>'],
  ['url'=>get_setting('youtube_url', ''),   'path'=>'<path d="M23 12s0-3.2-.4-4.74a2.5 2.5 0 00-1.76-1.77C19.3 5.3 12 5.3 12 5.3s-7.3 0-8.84.19A2.5 2.5 0 001.4 7.26C1 8.8 1 12 1 12s0 3.2.4 4.74a2.5 2.5 0 001.76 1.77c1.54.19 8.84.19 8.84.19s7.3 0 8.84-.19a2.5 2.5 0 001.76-1.77C23 15.2 23 12 23 12zM9.75 15.02V8.98L15.5 12z"/>'],
];
?>
<main class="page-body">
  <div class="page-shell">
    <div class="page-hero">
      <div class="eyebrow"><?= ac('contact', 'hero_title') ?></div>
      <h1><?= ac('contact', 'hero_title') ?></h1>
      <p><?= ac('contact', 'hero_sub') ?></p>
    </div>

    <section class="ct">
      <div class="ct-card">
        <!-- Left: form -->
        <div class="ct-form">
          <h2><?= ac('contact', 'form_title', true) ?></h2>
          <p><?= ac('contact', 'form_sub') ?></p>
          <form method="post" action="<?= htmlspecialchars(url('hubungi-kami')) ?>" novalidate>
            <div class="ct-field">
              <label for="ct-name"><?= ac('contact', 'f_name') ?></label>
              <input id="ct-name" name="name" type="text" placeholder="<?= ac('contact', 'f_name_ph') ?>">
            </div>
            <div class="ct-field">
              <label for="ct-email"><?= ac('contact', 'f_email') ?></label>
              <input id="ct-email" name="email" type="email" placeholder="<?= ac('contact', 'f_email_ph') ?>">
            </div>
            <div class="ct-field">
              <label for="ct-phone"><?= ac('contact', 'f_phone') ?></label>
              <input id="ct-phone" name="phone" type="tel" placeholder="<?= ac('contact', 'f_phone_ph') ?>">
            </div>
            <div class="ct-field">
              <label for="ct-msg"><?= ac('contact', 'f_msg') ?></label>
              <textarea id="ct-msg" name="message" placeholder="<?= ac('contact', 'f_msg_ph') ?>"></textarea>
            </div>
            <button class="btn btn-primary ct-submit" type="submit" data-contact-submit>
              <?= ac('contact', 'f_submit') ?>
              <svg class="ic" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </button>
          </form>
        </div>

        <!-- Right: info -->
        <div class="ct-info">
          <h3><?= ac('contact', 'info_title') ?></h3>
          <ul class="ct-list">
            <?php if ($address): ?>
            <li class="ct-item">
              <span class="ct-ic"><svg viewBox="0 0 24 24"><path d="M12 21s-7-5.3-7-11a7 7 0 1114 0c0 5.7-7 11-7 11z"/><circle cx="12" cy="10" r="2.6"/></svg></span>
              <div><div class="lbl"><?= ac('contact', 'l_address') ?></div><div class="val"><?= htmlspecialchars($address) ?></div></div>
            </li>
            <?php endif; ?>
            <?php if ($phone): ?>
            <li class="ct-item">
              <span class="ct-ic"><svg viewBox="0 0 24 24"><path d="M5 4h4l2 5-3 2a11 11 0 005 5l2-3 5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z"/></svg></span>
              <div><div class="lbl"><?= ac('contact', 'l_phone') ?></div><div class="val"><a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/','',$phone)) ?>"><?= htmlspecialchars($phone) ?></a></div></div>
            </li>
            <?php endif; ?>
            <?php if ($prophone): ?>
            <li class="ct-item">
              <span class="ct-ic"><svg viewBox="0 0 24 24"><path d="M12 2a7 7 0 00-7 7c0 1.5.5 2.7 1.3 3.8L12 20l5.7-7.2A6.6 6.6 0 0019 9a7 7 0 00-7-7z"/><path d="M9 9a3 3 0 106 0 3 3 0 00-6 0z"/></svg></span>
              <div><div class="lbl"><?= ac('contact', 'l_prophone') ?></div><div class="val"><a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/','',$prophone)) ?>"><?= htmlspecialchars($prophone) ?></a></div></div>
            </li>
            <?php endif; ?>
            <?php if ($wa_num): ?>
            <li class="ct-item">
              <span class="ct-ic"><svg viewBox="0 0 24 24"><path d="M20 4A11 11 0 003 19l-1 3 3-1A11 11 0 1020 4z"/><path d="M8.5 8.5c.5 3 3 5.5 6 6l1.5-1.5-2-1-1 1a6 6 0 01-3-3l1-1-1-2z"/></svg></span>
              <div><div class="lbl"><?= ac('contact', 'l_wa') ?></div><div class="val"><a href="https://wa.me/<?= htmlspecialchars($wa_num) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($wa_disp) ?></a></div></div>
            </li>
            <?php endif; ?>
            <?php if ($email): ?>
            <li class="ct-item">
              <span class="ct-ic"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg></span>
              <div><div class="lbl"><?= ac('contact', 'l_email') ?></div><div class="val"><a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></div></div>
            </li>
            <?php endif; ?>
            <?php if ($proemail): ?>
            <li class="ct-item">
              <span class="ct-ic"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg></span>
              <div><div class="lbl"><?= ac('contact', 'l_proemail') ?></div><div class="val"><a href="mailto:<?= htmlspecialchars($proemail) ?>"><?= htmlspecialchars($proemail) ?></a></div></div>
            </li>
            <?php endif; ?>
          </ul>

          <div class="ct-social">
            <div class="lbl"><?= ac('contact', 'social_title') ?></div>
            <div class="ct-social-row">
              <?php foreach ($socials as $s): if (empty($s['url'])) continue; ?>
                <a href="<?= htmlspecialchars($s['url']) ?>" target="_blank" rel="noopener" aria-label="social"><svg viewBox="0 0 24 24"><?= $s['path'] ?></svg></a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="ct-map">
        <?php if (trim((string)$maps) !== ''): ?>
          <?= $maps /* trusted embed HTML from settings */ ?>
        <?php else: ?>
          <div class="ct-map-ph">Peta lokasi akan tampil di sini (atur embed Google Maps di Pengaturan).</div>
        <?php endif; ?>
      </div>
    </section>
  </div>
</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
