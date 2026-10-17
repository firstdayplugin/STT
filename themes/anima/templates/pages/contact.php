<?php
/**
 * Anima theme — Contact page (route: /contact-us). Built from the Figma "Contact Us" design.
 * Labels/copy are editable via ac('contact', key); contact values come from get_setting() (white-label).
 * Shares layouts/header.php (nav solid via page-inner) and layouts/footer.php with all pages.
 */
if (!isset($db) && class_exists('Database')) { $db = Database::getInstance(); }

// --- Contact form submit (PRG): honeypot + Turnstile anti-spam, then save to `pesan`. ---
$ct_err = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['_form'] ?? '') === 'contact') {
    $nama  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $msg   = trim($_POST['message'] ?? '');
    $honey = trim($_POST['website'] ?? ''); // honeypot — must stay empty
    if ($honey !== '') {
        redirect(url('contact-us') . '?sent=1');                // silent drop for bots
    } elseif ($nama === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $ct_err = 'Nama dan email yang valid wajib diisi.';
    } elseif ($msg === '') {
        $ct_err = 'Pesan tidak boleh kosong.';
    } elseif (!turnstile_verify($_POST['cf-turnstile-response'] ?? null)) {
        $ct_err = 'Verifikasi anti-spam gagal. Silakan coba lagi.';
    } else {
        $lead = ['nama' => $nama, 'email' => $email, 'telepon' => $_POST['phone'] ?? '',
                 'pesan' => $msg, 'halaman' => 'contact-us'];
        save_lead('kontak', $lead);
        notify_lead('kontak', $lead);
        redirect(url('contact-us') . '?sent=1');
    }
}
$ct_sent = (($_GET['sent'] ?? '') === '1');

// --- Request Proposal submit (moved here from Home): honeypot + Turnstile, then save to `pesan`. ---
$rp_err = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['_form'] ?? '') === 'proposal') {
    $nama     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $company  = trim($_POST['company'] ?? '');
    $jobrole  = trim($_POST['job_role'] ?? '');
    $industry = trim($_POST['industry'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $solution = trim($_POST['solution'] ?? '');
    $msg      = trim($_POST['message'] ?? '');
    $honey    = trim($_POST['website'] ?? '');
    if ($honey !== '') {
        redirect(url('contact-us') . '?sent=proposal#request-proposal');  // silent drop for bots
    } elseif ($nama === '') {
        $rp_err = 'Nama wajib diisi.';
    } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $rp_err = 'Masukkan alamat email yang valid.';
    } elseif (!turnstile_verify($_POST['cf-turnstile-response'] ?? null)) {
        $rp_err = 'Verifikasi anti-spam gagal. Silakan coba lagi.';
    } else {
        $detail = [];
        if ($jobrole  !== '') $detail[] = 'Job Role: '   . $jobrole;
        if ($industry !== '') $detail[] = 'Industries: ' . $industry;
        if ($location !== '') $detail[] = 'Locations: '  . $location;
        if ($solution !== '') $detail[] = 'Solutions: '  . $solution;
        $pesan_full = $msg;
        if ($detail) $pesan_full = ($msg !== '' ? $msg . "\n\n" : '') . "— Detail —\n" . implode("\n", $detail);
        $lead = ['nama' => $nama, 'email' => $email, 'telepon' => $phone, 'perusahaan' => $company,
                 'subjek' => 'Request Proposal' . ($solution !== '' ? ' — ' . $solution : ''),
                 'pesan' => $pesan_full, 'halaman' => 'contact-us'];
        save_lead('proposal', $lead);
        notify_lead('proposal', $lead);
        redirect(url('contact-us') . '?sent=proposal#request-proposal');
    }
}
$rp_sent = (($_GET['sent'] ?? '') === 'proposal');

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

    <section class="ct" id="request-proposal">
      <div class="ct-card">
        <!-- Left: form -->
        <div class="ct-form">
          <h2><?= ac('contact', 'form_title', true) ?></h2>
          <p><?= ac('contact', 'form_sub') ?></p>
          <?php if ($rp_sent): ?>
            <div class="ct-alert ok">Terima kasih! Permintaan Anda sudah kami terima. Tim kami akan segera menghubungi Anda.</div>
          <?php elseif ($rp_err !== ''): ?>
            <div class="ct-alert err"><?= htmlspecialchars($rp_err) ?></div>
          <?php endif; ?>
          <form method="post" action="<?= htmlspecialchars(url('contact-us') . '#request-proposal') ?>" novalidate>
            <input type="hidden" name="_form" value="proposal">
            <div class="ct-hp" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
            <?php $rp_solutions = ['Modernize Infrastructure','Cybersecurity','Data Management','Artificial Intelligence (AI)','AI Platform & Applications','Other']; ?>
            <div class="ct-field"><label for="nm">Full Name</label><input id="nm" name="name" type="text" placeholder="Your full name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"></div>
            <div class="ct-2col">
              <div class="ct-field"><label for="em">Email</label><input id="em" name="email" type="email" placeholder="you@company.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></div>
              <div class="ct-field"><label for="ph">Mobile Phone</label><input id="ph" name="phone" type="tel" placeholder="+62 8xx-xxxx-xxxx" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"></div>
            </div>
            <div class="ct-2col">
              <div class="ct-field"><label for="co">Company / Organization</label><input id="co" name="company" type="text" placeholder="Your company / organization" value="<?= htmlspecialchars($_POST['company'] ?? '') ?>"></div>
              <div class="ct-field"><label for="jr">Job Role</label><input id="jr" name="job_role" type="text" placeholder="e.g. IT Manager" value="<?= htmlspecialchars($_POST['job_role'] ?? '') ?>"></div>
            </div>
            <div class="ct-2col">
              <div class="ct-field"><label for="ind">Industries</label><input id="ind" name="industry" type="text" placeholder="e.g. Financial Services" value="<?= htmlspecialchars($_POST['industry'] ?? '') ?>"></div>
              <div class="ct-field"><label for="loc">Locations</label><input id="loc" name="location" type="text" placeholder="e.g. Jakarta, Indonesia" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>"></div>
            </div>
            <div class="ct-field"><label for="sol">Select Solutions</label>
              <select id="sol" name="solution" class="ct-select">
                <option value="" <?= empty($_POST['solution']) ? 'selected' : '' ?>>Select Solutions</option>
                <?php foreach ($rp_solutions as $__sol): ?><option value="<?= htmlspecialchars($__sol) ?>"<?= (($_POST['solution'] ?? '') === $__sol) ? ' selected' : '' ?>><?= htmlspecialchars($__sol) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="ct-field"><label for="ms">Message</label><textarea id="ms" name="message" placeholder="Please type your request solution / product here!"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea></div>
            <?php if (turnstile_enabled()): ?><div class="ct-field"><?= turnstile_widget() ?></div><?php endif; ?>
            <button class="btn btn-primary ct-submit" type="submit">
              <?= ac('contact', 'f_submit') ?: 'Submit' ?>
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
              <span class="ct-ic"><svg viewBox="0 0 24 24"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L16 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z"/></svg></span>
              <div><div class="lbl"><?= ac('contact', 'l_phone') ?></div><div class="val"><a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/','',$phone)) ?>"><?= htmlspecialchars($phone) ?></a></div></div>
            </li>
            <?php endif; ?>
            <?php if ($prophone): ?>
            <li class="ct-item">
              <span class="ct-ic"><svg viewBox="0 0 24 24"><path d="M4 13v-1a8 8 0 0116 0v1"/><rect x="2.5" y="13" width="3.5" height="6" rx="1.5"/><rect x="18" y="13" width="3.5" height="6" rx="1.5"/><path d="M18 19a4 4 0 01-4 3h-1.5"/></svg></span>
              <div><div class="lbl"><?= ac('contact', 'l_prophone') ?></div><div class="val"><a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/','',$prophone)) ?>"><?= htmlspecialchars($prophone) ?></a></div></div>
            </li>
            <?php endif; ?>
            <?php if ($wa_num): ?>
            <li class="ct-item">
              <span class="ct-ic"><svg viewBox="0 0 24 24"><path d="M20 11.5a8 8 0 01-11.9 7L4 20l1.6-4A8 8 0 1120 11.5z"/><path d="M8.8 8.5c-.3 0-.6.1-.8.4-.3.3-.9.9-.9 2.1s.9 2.4 1 2.6c.1.2 1.8 2.9 4.5 3.9 2.2.8 2.7.7 3.2.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2 0-.1-.3-.2-.6-.4l-1.5-.7c-.2-.1-.4-.1-.6.1l-.6.8c-.1.2-.3.2-.5.1-.7-.3-1.4-.6-2.1-1.5-.5-.6-.9-1.3-1-1.5-.1-.2 0-.4.1-.5l.4-.5c.1-.2.1-.3 0-.5l-.7-1.6c-.2-.4-.3-.4-.5-.4z"/></svg></span>
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
              <span class="ct-ic"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3.4"/><path d="M5.4 5.4l3.6 3.6M15 15l3.6 3.6M18.6 5.4L15 9M9 15l-3.6 3.6"/></svg></span>
              <div><div class="lbl"><?= ac('contact', 'l_proemail') ?></div><div class="val"><a href="mailto:<?= htmlspecialchars($proemail) ?>"><?= htmlspecialchars($proemail) ?></a></div></div>
            </li>
            <?php endif; ?>
          </ul>

          <div class="ct-social">
            <div class="lbl"><?= ac('contact', 'social_title') ?></div>
            <div class="ct-social-row">
              <?php $__any_social = false; foreach ($socials as $s): $su = trim((string)$s['url']); if ($su === '' || $su === '#') continue; $__any_social = true; ?>
                <a href="<?= htmlspecialchars($su) ?>" target="_blank" rel="noopener" aria-label="social"><svg viewBox="0 0 24 24"><?= $s['path'] ?></svg></a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <?php $m = trim((string)$maps); if ($m !== ''): /* Google Maps shows ONLY when set in CMS; hidden by default */ ?>
      <div class="ct-map">
        <?php if (stripos($m, '<iframe') !== false || str_starts_with($m, '<')): ?>
          <?= $m /* full embed HTML from settings */ ?>
        <?php else: ?>
          <iframe src="<?= htmlspecialchars($m) ?>" width="100%" height="420" style="border:0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Lokasi kantor"></iframe>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </section>
  </div>
</main>
<?= turnstile_script() ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
