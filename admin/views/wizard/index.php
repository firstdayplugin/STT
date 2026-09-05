<?php
$page_title = 'Setup Wizard';

$total_steps = 8;
$current_step = max(1, min($total_steps, (int)($_GET['step'] ?? (int)get_setting('wizard_step', 1))));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error','Token invalid.'); redirect(admin_url('?page=wizard&step='.$current_step)); }

    $next_step = $current_step + 1;

    // Save data from each step
    switch ($current_step) {
        case 1: // Business Info
            foreach (['site_name','site_tagline','site_description','site_email','site_phone','site_address'] as $k) {
                if (isset($_POST[$k])) update_setting($k, sanitize($_POST[$k]));
            }
            if (!empty($_FILES['logo']['name'])) { $up=upload_image($_FILES['logo'],'settings'); if($up) update_setting('logo',$up); }
            break;
        case 2: // Hero
            foreach (['hero_judul','hero_subtitle','hero_cta_text','hero_mode'] as $k) {
                if (isset($_POST[$k])) update_setting($k, sanitize($_POST[$k]));
            }
            break;
        case 3: // WhatsApp
            foreach (['wa_number','wa_text','wa_panel_title','wa_business_hours'] as $k) {
                if (isset($_POST[$k])) update_setting($k, sanitize($_POST[$k]));
            }
            // Add first WA contact
            $wname = trim($_POST['wa_contact_name'] ?? '');
            $wnum  = trim($_POST['wa_contact_num'] ?? get_setting('wa_number'));
            if ($wname && $wnum) {
                $exist = $db->fetchOne("SELECT id FROM wa_contacts WHERE nomor=?",[$wnum]);
                if (!$exist) $db->insert('wa_contacts',['nama'=>$wname,'nomor'=>$wnum,'deskripsi'=>'Customer Service','urutan'=>0,'is_active'=>1]);
            }
            break;
        case 4: // Social Media
            foreach (['sosial_instagram','sosial_facebook','sosial_tiktok','sosial_youtube'] as $k) {
                if (isset($_POST[$k])) update_setting($k, sanitize($_POST[$k]));
            }
            break;
        case 5: // SEO
            foreach (['meta_title_default','meta_desc_default','meta_keywords'] as $k) {
                if (isset($_POST[$k])) update_setting($k, sanitize($_POST[$k]));
            }
            break;
        case 6: // Tracking
            foreach (['ga_id','gtm_id','meta_pixel_id'] as $k) {
                if (isset($_POST[$k])) update_setting($k, sanitize($_POST[$k]));
            }
            break;
        case 7: // Change Password
            $new_pass = $_POST['new_password'] ?? '';
            if ($new_pass && strlen($new_pass) >= 8) {
                $db->execute("UPDATE users SET password=? WHERE id=?", [password_hash($new_pass, PASSWORD_BCRYPT), $user['id']]);
                set_flash('success','Password berhasil diubah!');
            }
            break;
        case 8: // Complete
            update_setting('wizard_complete', 1);
            update_setting('wizard_step', $total_steps);
            set_flash('success','Setup selesai! Website Anda siap digunakan.');
            redirect(admin_url());
    }

    update_setting('wizard_step', max((int)get_setting('wizard_step',1), $next_step));
    redirect(admin_url('?page=wizard&step='.$next_step));
}

$csrf = generate_csrf();

$step_labels = ['Info Bisnis','Hero Section','WhatsApp','Media Sosial','SEO','Tracking','Keamanan','Selesai'];
?>

<div class="page-header">
    <div class="page-title"><?= icon('rocket', 16) ?> Setup Wizard
        <small>Langkah <?= $current_step ?> dari <?= $total_steps ?></small>
    </div>
    <a href="<?= admin_url() ?>" class="btn btn-secondary btn-sm">Lewati <?= icon('arrow-right', 16) ?></a>
</div>

<!-- Progress -->
<div class="wizard-progress mb-24">
    <?php for ($s = 1; $s <= $total_steps; $s++): ?>
        <div class="wizard-step">
            <div class="step-circle <?= $s < $current_step ? 'done' : ($s === $current_step ? 'active' : '') ?>">
                <?= $s < $current_step ? '' : $s ?>
            </div>
            <?php if ($s < $total_steps): ?>
            <div class="step-line <?= $s < $current_step ? 'done' : '' ?>"></div>
            <?php endif; ?>
        </div>
    <?php endfor; ?>
</div>
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:24px">
<?php foreach ($step_labels as $si => $sl): ?>
<span style="font-size:11px;color:<?= ($si+1)===$current_step?'var(--accent)':($si+1<$current_step?'var(--success)':'var(--text-dim)') ?>">
    <?= ($si+1<$current_step?'':'') . $sl ?>
</span>
<?php if ($si < count($step_labels)-1): ?><span style="color:var(--text-dim);font-size:11px">·</span><?php endif; ?>
<?php endforeach; ?>
</div>

<div style="max-width:640px">
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

<?php if ($current_step === 1): ?>
<div class="card">
    <div class="card-header"><div class="card-title"><?= icon('client', 16) ?> Informasi Bisnis Anda</div></div>
    <div class="card-body">
        <div class="form-group"><label>Nama Brand / Perusahaan <span class="required">*</span></label>
            <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars(get_setting('site_name','')) ?>" placeholder="Reklamepedia" required></div>
        <div class="form-group"><label>Tagline</label>
            <input type="text" name="site_tagline" class="form-control" value="<?= htmlspecialchars(get_setting('site_tagline','')) ?>" placeholder="Solusi Periklanan Terbaik"></div>
        <div class="form-group"><label>Deskripsi Bisnis</label>
            <textarea name="site_description" class="form-control no-wysiwyg" rows="3" placeholder="Ceritakan singkat tentang bisnis Anda..."><?= htmlspecialchars(get_setting('site_description','')) ?></textarea></div>
        <div class="form-grid">
            <div class="form-group mb-0"><label>Email</label>
                <input type="email" name="site_email" class="form-control" value="<?= htmlspecialchars(get_setting('site_email','')) ?>" placeholder="info@bisnis.com"></div>
            <div class="form-group mb-0"><label>Telepon</label>
                <input type="text" name="site_phone" class="form-control" value="<?= htmlspecialchars(get_setting('site_phone','')) ?>" placeholder="0811-xxxx-xxxx"></div>
        </div>
        <div class="form-group" style="margin-top:18px"><label>Alamat</label>
            <textarea name="site_address" class="form-control no-wysiwyg" rows="2" placeholder="Jl. Contoh No. 1, Kota, Provinsi"><?= htmlspecialchars(get_setting('site_address','')) ?></textarea></div>
        <div class="form-group mb-0"><label>Logo Perusahaan</label>
            <?php $logo=get_setting('logo'); if($logo): ?><img src="<?= uploads_url($logo) ?>" style="height:40px;margin-bottom:8px"><br><?php endif; ?>
            <input type="file" name="logo" class="form-control" accept="image/*"></div>
    </div>
</div>

<?php elseif ($current_step === 2): ?>
<div class="card">
    <div class="card-header"><div class="card-title"><?= icon('image', 16) ?> Tampilan Hero / Banner Utama</div></div>
    <div class="card-body">
        <div class="form-group"><label>Mode Hero</label>
            <select name="hero_mode" class="form-control">
                <option value="single" <?= get_setting('hero_mode')==='single'?'selected':'' ?>>Single Image</option>
                <option value="slideshow" <?= get_setting('hero_mode')==='slideshow'?'selected':'' ?>>Slideshow Otomatis</option>
            </select></div>
        <div class="form-group"><label>Judul Utama (Heading)</label>
            <input type="text" name="hero_judul" class="form-control" value="<?= htmlspecialchars(get_setting('hero_judul','')) ?>" placeholder="Solusi Periklanan Premium Terpercaya"></div>
        <div class="form-group"><label>Sub-judul</label>
            <textarea name="hero_subtitle" class="form-control no-wysiwyg" rows="3" placeholder="Deskripsi singkat yang menarik perhatian..."><?= htmlspecialchars(get_setting('hero_subtitle','')) ?></textarea></div>
        <div class="form-group mb-0"><label>Teks Tombol CTA</label>
            <input type="text" name="hero_cta_text" class="form-control" value="<?= htmlspecialchars(get_setting('hero_cta_text','Hubungi Kami')) ?>" placeholder="Hubungi Kami"></div>
    </div>
</div>

<?php elseif ($current_step === 3): ?>
<div class="card">
    <div class="card-header"><div class="card-title"><?= icon('message', 16) ?> WhatsApp Business</div></div>
    <div class="card-body">
        <div class="form-group"><label>Nomor WhatsApp Utama <span class="required">*</span></label>
            <input type="text" name="wa_number" class="form-control" id="wa-num"
                   value="<?= htmlspecialchars(get_setting('wa_number','')) ?>"
                   placeholder="628123456789" required>
            <div class="form-help">Format: 628xxx (tanpa tanda + atau spasi)</div></div>
        <div class="form-group"><label>Pesan Sambutan WA</label>
            <input type="text" name="wa_text" class="form-control" value="<?= htmlspecialchars(get_setting('wa_text','')) ?>" placeholder="Halo, saya ingin bertanya tentang layanan Anda..."></div>
        <div class="form-group"><label>Jam Operasional</label>
            <input type="text" name="wa_business_hours" class="form-control" value="<?= htmlspecialchars(get_setting('wa_business_hours','')) ?>" placeholder="Senin – Sabtu, 08.00 – 17.00"></div>
        <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:16px">
            <div class="form-group"><label>Nama Person / Tim</label>
                <input type="text" name="wa_contact_name" class="form-control" placeholder="CS / Sales / Tim Teknis"></div>
            <div class="form-group mb-0"><label>Nomor (bisa berbeda dengan nomor utama)</label>
                <input type="text" name="wa_contact_num" class="form-control" id="wa-person" placeholder="628xxx"></div>
        </div>
        <div class="form-help">Ini akan muncul di panel float WhatsApp di website</div>
    </div>
</div>
<script>document.getElementById('wa-num').addEventListener('input',function(){document.getElementById('wa-person').placeholder=this.value||'628xxx';});</script>

<?php elseif ($current_step === 4): ?>
<div class="card">
    <div class="card-header"><div class="card-title"><?= icon('smartphone', 16) ?> Media Sosial</div></div>
    <div class="card-body">
        <?php $socials=[['sosial_instagram','Instagram','https://instagram.com/...'],['sosial_facebook','Facebook','https://facebook.com/...'],['sosial_tiktok','TikTok','https://tiktok.com/@...'],['sosial_youtube','YouTube','https://youtube.com/channel/...']]; ?>
        <?php foreach ($socials as [$k,$l,$ph]): ?>
        <div class="form-group"><label><?= $l ?></label>
            <input type="url" name="<?= $k ?>" class="form-control" value="<?= htmlspecialchars(get_setting($k,'')) ?>" placeholder="<?= $ph ?>"></div>
        <?php endforeach; ?>
        <div class="alert alert-info mb-0"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Kosongkan jika tidak memiliki akun di platform tersebut.</div>
    </div>
</div>

<?php elseif ($current_step === 5): ?>
<div class="card">
    <div class="card-header"><div class="card-title"><?= icon('search', 16) ?> Pengaturan SEO</div></div>
    <div class="card-body">
        <div class="form-group"><label>Meta Title Default</label>
            <input type="text" name="meta_title_default" class="form-control" value="<?= htmlspecialchars(get_setting('meta_title_default','')) ?>"
                   placeholder="<?= htmlspecialchars(get_setting('site_name','Brand')) ?> | Solusi Periklanan Terbaik"></div>
        <div class="form-group"><label>Meta Description Default</label>
            <textarea name="meta_desc_default" class="form-control no-wysiwyg" rows="4" placeholder="Deskripsi bisnis yang akan tampil di hasil pencarian Google. Max 160 karakter."><?= htmlspecialchars(get_setting('meta_desc_default','')) ?></textarea></div>
        <div class="form-group mb-0"><label>Kata Kunci (Keywords)</label>
            <input type="text" name="meta_keywords" class="form-control" value="<?= htmlspecialchars(get_setting('meta_keywords','')) ?>"
                   placeholder="reklame, papan nama, neon box, spanduk, ...">
            <div class="form-help">Pisahkan dengan koma. Google sudah tidak terlalu mengandalkan ini, tapi tidak ada salahnya diisi.</div></div>
    </div>
</div>

<?php elseif ($current_step === 6): ?>
<div class="card">
    <div class="card-header"><div class="card-title"><?= icon('chart', 16) ?> Tracking & Analytics (Opsional)</div></div>
    <div class="card-body">
        <div class="alert alert-info mb-16"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Semua field opsional. Isi jika Anda sudah memiliki akun di platform tersebut.</div>
        <div class="form-group"><label>Google Analytics 4 — Measurement ID</label>
            <input type="text" name="ga_id" class="form-control" value="<?= htmlspecialchars(get_setting('ga_id','')) ?>" placeholder="G-XXXXXXXXXX"></div>
        <div class="form-group"><label>Google Tag Manager — Container ID</label>
            <input type="text" name="gtm_id" class="form-control" value="<?= htmlspecialchars(get_setting('gtm_id','')) ?>" placeholder="GTM-XXXXXXX"></div>
        <div class="form-group mb-0"><label>Meta (Facebook) Pixel ID</label>
            <input type="text" name="meta_pixel_id" class="form-control" value="<?= htmlspecialchars(get_setting('meta_pixel_id','')) ?>" placeholder="1234567890123456"></div>
    </div>
</div>

<?php elseif ($current_step === 7): ?>
<div class="card">
    <div class="card-header"><div class="card-title"><?= icon('lock', 16) ?> Keamanan Akun</div></div>
    <div class="card-body">
        <div class="alert alert-warning mb-16"><?= icon('warning', 16) ?> Sangat disarankan untuk mengganti password default sekarang.</div>
        <div class="form-group"><label>Password Baru (opsional, min 8 karakter)</label>
            <input type="password" name="new_password" class="form-control" autocomplete="new-password" placeholder="Min 8 karakter..."></div>
        <div class="form-group mb-0"><label>Konfirmasi Password</label>
            <input type="password" id="confirm-pass" class="form-control" autocomplete="new-password" placeholder="Ulangi password baru..."></div>
    </div>
</div>
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const np = document.querySelector('[name="new_password"]').value;
    const cp = document.getElementById('confirm-pass').value;
    if (np && np !== cp) { e.preventDefault(); alert('Password tidak cocok!'); }
    if (np && np.length < 8) { e.preventDefault(); alert('Password minimal 8 karakter!'); }
});
</script>

<?php elseif ($current_step === 8): ?>
<div class="card">
    <div class="card-header"><div class="card-title"><?= icon('party', 16) ?> Setup Selesai!</div></div>
    <div class="card-body" style="text-align:center;padding:40px">
        <div style="font-size:64px;margin-bottom:20px"><?= icon('rocket', 16) ?></div>
        <h2 style="font-size:22px;font-weight:700;margin-bottom:12px">Website Anda Siap!</h2>
        <p class="text-muted" style="margin-bottom:24px">Semua pengaturan dasar telah dikonfigurasi. Anda bisa mulai menambahkan konten.</p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;max-width:400px;margin:0 auto 24px">
            <?php
            $summary = [
                ['Nama Brand', get_setting('site_name','-')],
                ['WhatsApp', get_setting('wa_number','-')],
                ['Email', get_setting('site_email','-')],
                ['Analytics', get_setting('ga_id') ? 'Terpasang' : 'Belum'],
            ];
            foreach ($summary as [$lbl,$val]): ?>
            <div style="background:var(--surface2);border-radius:10px;padding:12px">
                <div class="text-xs text-muted"><?= $lbl ?></div>
                <div class="text-sm fw-700"><?= htmlspecialchars($val) ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="quick-actions" style="max-width:400px;margin:0 auto">
            <a href="<?= admin_url('?page=blog&action=create') ?>" class="quick-action">
                <div class="qa-icon" style="background:rgba(232,160,32,0.12)"><?= icon('pencil', 16) ?></div>Tulis Artikel
            </a>
            <a href="<?= admin_url('?page=gallery&action=create') ?>" class="quick-action">
                <div class="qa-icon" style="background:rgba(96,165,250,0.12)"><?= icon('camera', 16) ?></div>Upload Galeri
            </a>
            <a href="<?= url('/') ?>" target="_blank" class="quick-action">
                <div class="qa-icon" style="background:rgba(74,222,128,0.12)"><?= icon('globe', 16) ?></div>Lihat Website
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:20px">
        <?php if ($current_step > 1): ?>
        <a href="?page=wizard&step=<?= $current_step-1 ?>" class="btn btn-secondary"><?= icon('arrow-left', 16) ?> Sebelumnya</a>
        <?php else: ?>
        <span></span>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">
            <?= $current_step === $total_steps ? 'Selesaikan Setup' : 'Lanjutkan' ?>
        </button>
    </div>
</form>
</div>
