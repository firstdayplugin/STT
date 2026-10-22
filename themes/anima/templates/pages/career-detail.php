<?php
/**
 * Anima — Career detail + application (route: /career/[slug]). "Role Spotlight + Apply":
 * a compact dark hero (role/location chips + title + meta + Apply CTA) and a two-column
 * body — job details (About / Responsibilities / Requirements) with a sticky apply card.
 * The application POST (validate, Turnstile + honeypot, secure CV upload ≤1MB,
 * insert into job_applications) is UNCHANGED — only the markup is redesigned.
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$job = $career_data ?? [];
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_form'] ?? '') === 'apply') {
    $nama    = trim($_POST['nama'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $cover   = trim($_POST['cover_letter'] ?? '');
    $honey   = trim($_POST['website'] ?? '');
    if ($honey !== '') {
        redirect(url('career/' . $job['slug'] . '?sent=1'));
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
                    [(int)($job['id'] ?? 0), $job['judul'] ?? '', $nama, $email, $telepon, $subject, $cover, $cv, $_SERVER['REMOTE_ADDR'] ?? '']
                );
            } catch (\Throwable $e) { /* never expose DB errors */ }
            redirect(url('career/' . $job['slug'] . '?sent=1'));
        }
    }
}
$sent = isset($_GET['sent']);
$fmt  = fn($d) => $d ? date('M j, Y', strtotime($d)) : '';
$jid  = (int)($job['id'] ?? 0);
$T    = fn($f) => tr_field('career', $jid, $f, $job[$f] ?? '');
$txt  = fn($s) => htmlspecialchars(trim(html_entity_decode(strip_tags((string)$s), ENT_QUOTES | ENT_HTML5, 'UTF-8')), ENT_QUOTES, 'UTF-8');
$j_judul = $T('judul'); $j_resp = $T('responsibilities'); $j_req = $T('requirements'); $j_desc = $T('deskripsi');
$seo = ['title' => ($j_judul ?: 'Career') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($j_desc), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
?>
<main class="page-body cd-page">

  <!-- Compact spotlight hero -->
  <section class="cd-hero">
    <canvas class="cd-net" id="cdNet" aria-hidden="true"></canvas>
    <div class="cd-wrap">
      <a class="cd-back" href="<?= htmlspecialchars(url('career')) ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg> <?= htmlspecialchars(t('all_open_roles', 'All open roles')) ?></a>
      <div class="cd-chips">
        <?php if (!empty($job['role'])): ?><span class="cd-chip role"><?= $txt($job['role']) ?></span><?php endif; ?>
        <?php if (!empty($job['lokasi'])): ?><span class="cd-chip loc"><?= $txt($job['lokasi']) ?></span><?php endif; ?>
        <?php if (trim((string)($job['pengalaman'] ?? '')) !== ''): ?><span class="cd-chip typ"><?= $txt($job['pengalaman']) ?></span><?php endif; ?>
      </div>
      <h1 class="notranslate" translate="no"><?= $txt($j_judul) ?></h1>
      <div class="cd-meta">
        <?php if (trim((string)$T('jenjang')) !== ''): ?><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10L12 5 2 10l10 5 10-5z"/><path d="M6 12v5c0 1 2.7 2.5 6 2.5s6-1.5 6-2.5v-5"/></svg> <?= $txt($T('jenjang')) ?></span><?php endif; ?>
        <?php if (trim((string)($job['pengalaman'] ?? '')) !== ''): ?><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg> <?= $txt($job['pengalaman']) ?></span><?php endif; ?>
        <?php if (!empty($job['deadline'])): ?><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> <?= htmlspecialchars(t('apply_until', 'Apply until') . ' ' . $fmt($job['deadline'])) ?></span><?php endif; ?>
      </div>
      <div class="cd-cta">
        <a class="cd-btn cd-btn-p" href="#cd-apply"><?= htmlspecialchars(t('apply_now', 'Apply now')) ?> <?= $arrow ?></a>
        <button type="button" class="cd-btn cd-btn-ghost" id="cdShare"><?= htmlspecialchars(t('share', 'Share')) ?></button>
      </div>
    </div>
  </section>

  <section class="cd-body"><div class="cd-wrap"><div class="cd-grid">
    <!-- details -->
    <div class="cd-card cd-main">
      <?php if (trim(strip_tags($j_desc)) !== ''): ?>
      <div class="cd-blk"><h2><?= htmlspecialchars(t('about_role', 'About the role')) ?></h2><div class="cd-prose"><?= $j_desc ?></div></div>
      <?php endif; ?>
      <?php if (trim(strip_tags($j_resp)) !== ''): ?>
      <div class="cd-blk"><h2><?= htmlspecialchars(t('responsibilities', 'Responsibilities')) ?></h2><div class="cd-prose cd-check"><?= $j_resp ?></div></div>
      <?php endif; ?>
      <?php if (trim(strip_tags($j_req)) !== ''): ?>
      <div class="cd-blk"><h2><?= htmlspecialchars(t('requirements', 'Requirements')) ?></h2><div class="cd-prose cd-check"><?= $j_req ?></div></div>
      <?php endif; ?>
    </div>

    <!-- sticky apply -->
    <div class="cd-aside">
      <div class="cd-card cd-apply" id="cd-apply">
        <h2><?= htmlspecialchars(t('apply_for_role', 'Apply for this role')) ?></h2>
        <div class="cd-apply-sub"><?= htmlspecialchars(t('apply_sub', 'Takes ~3 minutes. We reply to every application.')) ?></div>
        <div class="cd-recap">
          <?php if (!empty($job['role'])): ?><div><span><?= htmlspecialchars(t('role', 'Role')) ?></span><span><?= $txt($job['role']) ?></span></div><?php endif; ?>
          <?php if (!empty($job['lokasi'])): ?><div><span><?= htmlspecialchars(t('location', 'Location')) ?></span><span><?= $txt($job['lokasi']) ?></span></div><?php endif; ?>
          <?php if (!empty($job['deadline'])): ?><div><span><?= htmlspecialchars(t('deadline', 'Deadline')) ?></span><span><?= htmlspecialchars($fmt($job['deadline'])) ?></span></div><?php endif; ?>
        </div>

        <?php if ($sent): ?>
          <div class="cd-sent"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>
            <div><b><?= htmlspecialchars(t('apply_sent_title', 'Application received!')) ?></b><p><?= htmlspecialchars(t('apply_thanks', 'Thank you! We have received your application and will reach out if it is a match.')) ?></p></div>
          </div>
        <?php else: ?>
          <?php if ($err !== ''): ?><div class="cd-err"><?= icon('warning', 16) ?> <?= htmlspecialchars($err) ?></div><?php endif; ?>
          <form method="POST" action="<?= htmlspecialchars(url('career/' . $job['slug'])) ?>#cd-apply" enctype="multipart/form-data" class="cd-form">
            <input type="hidden" name="_form" value="apply">
            <div class="cd-f"><label>Full Name <b>*</b></label><input type="text" name="nama" required placeholder="<?= htmlspecialchars(t('ph_fullname', 'Enter your full name')) ?>" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"></div>
            <div class="cd-f"><label>Email <b>*</b></label><input type="email" name="email" required placeholder="you@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></div>
            <div class="cd-f"><label>Phone Number <b>*</b></label><input type="text" name="telepon" placeholder="+62 ..." value="<?= htmlspecialchars($_POST['telepon'] ?? '') ?>"></div>
            <div class="cd-f"><label>Subject <b>*</b></label><input type="text" name="subject" value="<?= htmlspecialchars($_POST['subject'] ?? $j_judul) ?>"></div>
            <div class="cd-f"><label>Cover Letter <b>*</b></label><textarea name="cover_letter" rows="4" placeholder="<?= htmlspecialchars(t('ph_cover', 'Tell us why you are a great fit')) ?>"><?= htmlspecialchars($_POST['cover_letter'] ?? '') ?></textarea></div>
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
    </div>
  </div></div></section>

</main>
<script src="<?= theme_asset('assets/js/careerdetail.js') ?>" defer></script>
<?= turnstile_script() ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
