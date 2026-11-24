<?php
/**
 * Anima — General application / "Send your CV" (route: /career/apply).
 * A form-ONLY page for candidates who don't find a matching role: same fields and
 * secure CV upload as the job-detail apply form, but with NO job description and no
 * per-role recap. Reuses the .cd-* styling (centered single column via .ca-*).
 * POST handling mirrors career-detail.php: honeypot + Turnstile, CV ≤1MB, insert
 * into job_applications with career_id = 0 (open application).
 */
$db  = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_form'] ?? '') === 'apply') {
    $nama    = trim($_POST['nama'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $cover   = trim($_POST['cover_letter'] ?? '');
    $honey   = trim($_POST['website'] ?? '');
    if ($honey !== '') {
        redirect(url('career/apply') . '?sent=1');
    } elseif ($nama === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Nama dan email yang valid wajib diisi.';
    } elseif (!turnstile_verify($_POST['cf-turnstile-response'] ?? null)) {
        $err = 'Verifikasi anti-spam gagal. Silakan coba lagi.';
    } elseif (empty($_FILES['cv']['name'])) {
        $err = 'CV wajib diunggah (PDF/DOC/DOCX, maks 1MB).';
    } else {
        $cv = upload_document($_FILES['cv'], 'cv', 1048576);
        if (!$cv) {
            $err = 'Unggah CV gagal. Pastikan PDF/DOC/DOCX dan maksimal 1MB.';
        } else {
            try {
                $db->execute(
                    "INSERT INTO job_applications (career_id,posisi,nama,email,telepon,subject,cover_letter,cv_file,ip)
                     VALUES (?,?,?,?,?,?,?,?,?)",
                    [0, ($subject !== '' ? $subject : 'Open Application'), $nama, $email, $telepon, $subject, $cover, $cv, $_SERVER['REMOTE_ADDR'] ?? '']
                );
            } catch (\Throwable $e) { /* never expose DB errors */ }
            redirect(url('career/apply') . '?sent=1');
        }
    }
}
$sent = isset($_GET['sent']);
$txt  = fn($s) => htmlspecialchars(trim(html_entity_decode(strip_tags((string)$s), ENT_QUOTES | ENT_HTML5, 'UTF-8')), ENT_QUOTES, 'UTF-8');
$cc   = fn(string $k, string $d = '') => get_content('career', $k, $d);

$seo = ['title' => ($cc('sendcv_title', 'Send your CV')) . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => 'Submit a general application to Sapta Tunas Teknologi. Don\'t see your perfect role? Send us your CV and we will reach out when something fits.'];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
?>
<main class="page-body cd-page ca-page">

  <!-- Compact centered hero -->
  <section class="cd-hero">
    <canvas class="cd-net" id="cdNet" aria-hidden="true"></canvas>
    <div class="cd-wrap ca-hero">
      <a class="cd-back" href="<?= htmlspecialchars(url('career')) ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg> <?= htmlspecialchars(t('all_open_roles', 'All open roles')) ?></a>
      <h1><?php $st = trim((string)$cc('sendcv_title', '')); echo $st !== '' ? $txt($st) : 'Send your <span class="g">CV</span>'; ?></h1>
      <p class="ca-lead"><?= $txt($cc('sendcv_sub', "Don't see your perfect role? Submit a general application and we'll reach out when something fits.")) ?></p>
    </div>
  </section>

  <section class="cd-body"><div class="cd-wrap ca-wrap">
    <div class="cd-card cd-apply ca-card" id="cd-apply">
      <h2><?= htmlspecialchars(t('open_application', 'Open application')) ?></h2>
      <div class="cd-apply-sub"><?= htmlspecialchars(t('apply_sub', 'Takes ~3 minutes. We reply to every application.')) ?></div>

      <?php if ($sent): ?>
        <div class="cd-sent"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>
          <div><b><?= htmlspecialchars(t('apply_sent_title', 'Application received!')) ?></b><p><?= htmlspecialchars(t('apply_thanks', 'Thank you! We have received your application and will reach out if it is a match.')) ?></p></div>
        </div>
      <?php else: ?>
        <?php if ($err !== ''): ?><div class="cd-err"><?= icon('warning', 16) ?> <?= htmlspecialchars($err) ?></div><?php endif; ?>
        <form method="POST" action="<?= htmlspecialchars(url('career/apply')) ?>#cd-apply" enctype="multipart/form-data" class="cd-form">
          <input type="hidden" name="_form" value="apply">
          <div class="cd-f"><label>Full Name <b>*</b></label><input type="text" name="nama" required placeholder="<?= htmlspecialchars(t('ph_fullname', 'Enter your full name')) ?>" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"></div>
          <div class="cd-f"><label>Email <b>*</b></label><input type="email" name="email" required placeholder="you@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></div>
          <div class="cd-f"><label>Phone Number <b>*</b></label><input type="text" name="telepon" placeholder="+62 ..." value="<?= htmlspecialchars($_POST['telepon'] ?? '') ?>"></div>
          <div class="cd-f"><label><?= htmlspecialchars(t('position_interest', 'Position of interest')) ?></label><input type="text" name="subject" placeholder="<?= htmlspecialchars(t('ph_position', 'e.g. Backend Engineer (optional)')) ?>" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>"></div>
          <div class="cd-f"><label>Cover Letter</label><textarea name="cover_letter" rows="4" placeholder="<?= htmlspecialchars(t('ph_cover', 'Tell us why you are a great fit')) ?>"><?= htmlspecialchars($_POST['cover_letter'] ?? '') ?></textarea></div>
          <div class="cd-f"><label>Upload Your CV <b>*</b></label>
            <label class="cd-cv">
              <input type="file" name="cv" id="cdCv" accept=".pdf,.doc,.docx" required>
              <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M7 8l5-5 5 5"/><path d="M5 21h14"/></svg></span>
              <span class="tx"><span class="t" id="cdCvT"><?= htmlspecialchars(t('cv_click', 'Click to upload your CV')) ?></span><span class="h">PDF, DOC, DOCX &middot; <?= htmlspecialchars(t('cv_max', 'max 1 MB')) ?></span></span>
            </label>
          </div>
          <div class="ct-hp" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
          <?php if (turnstile_enabled()): ?><div class="cd-f"><?= turnstile_widget() ?></div><?php endif; ?>
          <button type="submit" class="cd-btn cd-btn-p cd-submit"><?= htmlspecialchars(t('submit_application', 'Submit application')) ?> <?= $arrow ?></button>
          <div class="cd-note"><?= htmlspecialchars(t('apply_privacy', 'By applying you agree to our privacy policy.')) ?></div>
        </form>
      <?php endif; ?>
    </div>
  </div></section>

</main>
<script src="<?= theme_asset('assets/js/careerdetail.js') ?>" defer></script>
<?= turnstile_script() ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
