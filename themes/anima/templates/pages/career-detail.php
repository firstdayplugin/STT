<?php
/**
 * Anima — Career detail + application form (route: /career/[slug]).
 * index.php passes $career_data. Handles the application POST (PRG): validates,
 * Turnstile + honeypot anti-spam, secure CV upload, inserts into job_applications.
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
    $honey   = trim($_POST['website'] ?? ''); // honeypot (must stay empty)

    if ($honey !== '') {
        // Silent drop for bots.
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
            } catch (\Throwable $e) { /* swallow — never expose DB errors to visitors */ }
            redirect(url('career/' . $job['slug'] . '?sent=1'));
        }
    }
}
$sent = isset($_GET['sent']);
$fmt = fn($d) => $d ? date('M j, Y', strtotime($d)) : '';
// Language-aware field values (fall back to the base/default-language value).
$jid = (int)($job['id'] ?? 0);
$T = fn($f) => tr_field('career', $jid, $f, $job[$f] ?? '');
$j_judul = $T('judul'); $j_desk = $T('deskripsi'); $j_resp = $T('responsibilities'); $j_req = $T('requirements');
$seo = ['title' => ($j_judul ?: 'Career') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => $job['meta_description'] ?? mb_substr(strip_tags($j_desk), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body"><div class="page-shell cr-detail">

  <a class="bl-back" href="<?= url('career') ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 6l-6 6 6 6"/></svg> <?= htmlspecialchars(t('back_to_jobs', 'Semua Lowongan')) ?></a>

  <div class="cr-detail-head">
    <h1><?= htmlspecialchars($j_judul) ?></h1>
    <div class="cr-meta">
      <?php if (!empty($job['role'])): ?><span class="cr-chip"><?= htmlspecialchars($job['role']) ?></span><?php endif; ?>
      <?php if (!empty($job['lokasi'])): ?><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0116 0z"/><circle cx="12" cy="10" r="3"/></svg> <?= htmlspecialchars($job['lokasi']) ?></span><?php endif; ?>
      <?php if (!empty($job['tipe'])): ?><span><?= icon('clock', 15) ?> <?= htmlspecialchars($job['tipe']) ?></span><?php endif; ?>
      <?php if (!empty($job['jenjang'])): ?><span><?= icon('star', 15) ?> <?= htmlspecialchars($job['jenjang']) ?></span><?php endif; ?>
      <?php if (!empty($job['pengalaman'])): ?><span><?= icon('briefcase', 15) ?> <?= htmlspecialchars($job['pengalaman']) ?></span><?php endif; ?>
      <?php if (!empty($job['deadline'])): ?><span class="cr-deadline">Sampai <?= htmlspecialchars($fmt($job['deadline'])) ?></span><?php endif; ?>
    </div>
  </div>

  <div class="cr-detail-body">
    <div class="cr-content">
      <?php if ($j_desk !== ''): ?><p class="cr-lead"><?= htmlspecialchars($j_desk) ?></p><?php endif; ?>
      <?php if ($j_resp !== ''): ?>
        <h2><?= htmlspecialchars(t('responsibilities', 'Responsibilities')) ?></h2>
        <div class="page-prose"><?= $j_resp ?></div>
      <?php endif; ?>
      <?php if ($j_req !== ''): ?>
        <h2><?= htmlspecialchars(t('requirements', 'Requirements')) ?></h2>
        <div class="page-prose"><?= $j_req ?></div>
      <?php endif; ?>
    </div>

    <aside class="cr-apply" id="apply">
      <h2><?= htmlspecialchars(t('apply_here', 'Lamar Posisi Ini')) ?></h2>
      <?php if ($sent): ?>
        <div class="cr-sent"><?= icon('success', 20) ?> Terima kasih! Lamaran Anda sudah kami terima. Tim kami akan menghubungi jika cocok.</div>
      <?php else: ?>
        <?php if ($err !== ''): ?><div class="cr-err"><?= icon('warning', 16) ?> <?= htmlspecialchars($err) ?></div><?php endif; ?>
        <form method="POST" action="<?= url('career/' . $job['slug']) ?>#apply" enctype="multipart/form-data" class="cr-form">
          <input type="hidden" name="_form" value="apply">
          <div class="cr-field"><label>Nama Lengkap *</label><input type="text" name="nama" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"></div>
          <div class="cr-field"><label>Email *</label><input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></div>
          <div class="cr-field"><label>Nomor Telepon</label><input type="text" name="telepon" value="<?= htmlspecialchars($_POST['telepon'] ?? '') ?>"></div>
          <div class="cr-field"><label>Subject</label><input type="text" name="subject" value="<?= htmlspecialchars($_POST['subject'] ?? ($job['judul'] ?? '')) ?>"></div>
          <div class="cr-field"><label>Cover Letter</label><textarea name="cover_letter" rows="4"><?= htmlspecialchars($_POST['cover_letter'] ?? '') ?></textarea></div>
          <div class="cr-field"><label>Upload CV * <span class="cr-hint">(PDF/DOC/DOCX, maks 1MB)</span></label><input type="file" name="cv" accept=".pdf,.doc,.docx" required></div>
          <div class="cr-hp" aria-hidden="true"><label>Website</label><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
          <?= turnstile_widget() ?>
          <button type="submit" class="btn btn-primary cr-submit"><?= htmlspecialchars(t('submit_application', 'Kirim Lamaran')) ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
        </form>
      <?php endif; ?>
    </aside>
  </div>

</div></main>
<?= turnstile_script() ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
