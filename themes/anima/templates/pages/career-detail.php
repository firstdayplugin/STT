<?php
/**
 * Anima — Career detail + application (route: /career/[slug]). Figma "Job Application".
 * index.php passes $career_data. Layout: filter sidebar + job card (responsibilities /
 * requirements) + "Form Application" card. Handles the PRG application POST (validate,
 * Turnstile + honeypot, secure CV upload ≤1MB, insert into job_applications).
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
$j_judul = $T('judul'); $j_resp = $T('responsibilities'); $j_req = $T('requirements');
$sub = array_filter([$T('jenjang'), $job['pengalaman'] ?? '']);
$seo = ['title' => ($j_judul ?: 'Career') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($T('deskripsi')), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body"><div class="cr-wrap">
  <div class="cr-dhead"><h1><?= htmlspecialchars(t('job_application', 'Job Application')) ?></h1></div>

  <div class="cr-layout">
    <?php include theme_path('templates/pages/_career-filters.php'); ?>

    <div class="cr-dmain">
      <div class="cr-djob">
        <div class="cr-djob-top">
          <div>
            <h2><?= htmlspecialchars($j_judul) ?></h2>
            <?php if ($sub): ?><div class="crj-sub"><?= htmlspecialchars(implode('  |  ', $sub)) ?></div><?php endif; ?>
          </div>
          <?php if (!empty($job['deadline'])): ?><span class="crj-until"><?= htmlspecialchars(t('until', 'until') . ' ' . $fmt($job['deadline'])) ?></span><?php endif; ?>
        </div>
        <hr class="cr-djob-rule">
        <?php if ($j_resp !== ''): ?>
          <h3><?= htmlspecialchars(t('responsibilities', 'Responsibilities')) ?></h3>
          <div class="cr-prose"><?= $j_resp ?></div>
        <?php endif; ?>
        <?php if ($j_req !== ''): ?>
          <h3><?= htmlspecialchars(t('requirements', 'Requirements')) ?></h3>
          <div class="cr-prose"><?= $j_req ?></div>
        <?php endif; ?>
      </div>

      <div class="cr-formcard" id="apply">
        <h2><?= htmlspecialchars(t('form_application', 'Form Application')) ?></h2>
        <hr class="cr-djob-rule">
        <?php if ($sent): ?>
          <div class="cr-sent"><?= icon('success', 20) ?> <?= htmlspecialchars(t('apply_thanks', 'Terima kasih! Lamaran Anda sudah kami terima. Tim kami akan menghubungi jika cocok.')) ?></div>
        <?php else: ?>
          <?php if ($err !== ''): ?><div class="cr-err"><?= icon('warning', 16) ?> <?= htmlspecialchars($err) ?></div><?php endif; ?>
          <form method="POST" action="<?= url('career/' . $job['slug']) ?>#apply" enctype="multipart/form-data" class="cr-form">
            <input type="hidden" name="_form" value="apply">
            <div class="cr-field"><label>Full Name <b>*</b></label><input type="text" name="nama" required placeholder="Enter your Full Name" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"></div>
            <div class="cr-field"><label>Email <b>*</b></label><input type="email" name="email" required placeholder="Enter your email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></div>
            <div class="cr-field"><label>Phone Number <b>*</b></label><input type="text" name="telepon" placeholder="Enter your phone number" value="<?= htmlspecialchars($_POST['telepon'] ?? '') ?>"></div>
            <div class="cr-field"><label>Subject (Job You Wanted to Apply) <b>*</b></label><input type="text" name="subject" placeholder="Enter your Subject (Job You Wanted to Apply)" value="<?= htmlspecialchars($_POST['subject'] ?? $j_judul) ?>"></div>
            <div class="cr-field"><label>Cover Letter <b>*</b></label><textarea name="cover_letter" rows="4" placeholder="Enter your Cover letter"><?= htmlspecialchars($_POST['cover_letter'] ?? '') ?></textarea></div>
            <div class="cr-field"><label>Upload Your CV (File Max 1 MB) <b>*</b></label><input type="file" name="cv" accept=".pdf,.doc,.docx" required></div>
            <div class="ct-hp" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
            <?php if (turnstile_enabled()): ?><div class="cr-field"><?= turnstile_widget() ?></div><?php endif; ?>
            <button type="submit" class="btn btn-primary cr-submit"><?= htmlspecialchars(t('submit', 'Submit')) ?></button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div></main>
<?= turnstile_script() ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
