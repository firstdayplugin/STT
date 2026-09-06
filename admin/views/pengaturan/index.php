<?php
$page_title = 'Pengaturan Website';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error','Token invalid.'); redirect(admin_url('?page=pengaturan')); }
    $a = $_POST['_action'] ?? 'general';

    // Generic save settings
    $saveable = [
        'general' => ['site_name','site_tagline','site_description','site_email','site_phone','site_address','site_maps_embed'],
        'hero'    => ['hero_mode','hero_judul','hero_subtitle','hero_cta_text','hero_cta_url','hero_overlay'],
        'homepage'=> ['faq_limit_home','faq_limit_detail','homepage_blog_enabled','homepage_blog_count','homepage_blog_type','homepage_blog_label','homepage_blog_title','homepage_blog_desc'],
        'kontak'  => ['wa_number','wa_text','wa_panel_title','wa_business_hours','office_lat','office_lng','site_maps_embed','wa_greeting_enabled','wa_greeting_title','wa_greeting_text','wa_greeting_delay','wa_greeting_once_per_session'],
        'ads'     => ['gads_conversion_enabled','gads_conversion_id','gads_conversion_label','turnstile_site_key','turnstile_secret'],
        'sosial'  => ['sosial_instagram','sosial_facebook','sosial_tiktok','sosial_youtube','sosial_twitter','sosial_linkedin'],
        'seo_settings' => ['meta_title_default','meta_desc_default','meta_keywords','og_image','google_verification','robots_default'],
        'tampilan'=> ['accent_color','dark_color','cream_color','font_heading','font_body','footer_text','custom_css'],
        'gambar'  => ['docs_url'],
        'docs'    => ['docs_url'],
        'whitelabel' => ['admin_login_title','admin_login_desc'],
    ];

    $fields = $saveable[$a] ?? [];

    // Normalize phone/WhatsApp number BEFORE generic save (prevents double 62 / format issues)
    if (isset($_POST['wa_number'])) {
        $_POST['wa_number'] = normalize_phone($_POST['wa_number']);
    }
    if (isset($_POST['site_phone'])) {
        $_POST['site_phone'] = normalize_phone($_POST['site_phone']);
    }

    // Checkbox fields: must be set to 0 if not present in POST (unchecked)
    $checkbox_fields = ['wa_greeting_enabled','wa_greeting_once_per_session','gads_conversion_enabled','homepage_blog_enabled'];
    foreach ($fields as $key) {
        if (in_array($key, $checkbox_fields)) {
            update_setting($key, isset($_POST[$key]) ? '1' : '0');
        } elseif (isset($_POST[$key])) {
            update_setting($key, sanitize($_POST[$key]));
        }
    }

    // Handle logo uploads (3 versions)
    if ($a === 'general') {
        foreach (['logo','logo_dark','logo_footer'] as $logo_key) {
            if (!empty($_FILES[$logo_key]['name'])) {
                $up = upload_image($_FILES[$logo_key], 'settings');
                if ($up) update_setting($logo_key, $up);
            }
        }
    }
    if ($a === 'general' && !empty($_FILES['favicon']['name'])) {
        $up = upload_image($_FILES['favicon'], 'settings');
        if ($up) update_setting('favicon', $up);
    }
    if ($a === 'seo_settings' && !empty($_FILES['og_image_file']['name'])) {
        $up = upload_image($_FILES['og_image_file'], 'settings');
        if ($up) update_setting('og_image', $up);
    }

    // Login logo + favicon (whitelabel tab)
    if ($a === 'whitelabel') {
        foreach (['admin_login_logo','favicon','logo'] as $key) {
            if (!empty($_FILES[$key]['name'])) {
                $up = upload_image($_FILES[$key], 'settings');
                if ($up) update_setting($key, $up);
            }
        }
    }
    
    // Image uploads (gambar tab)
    if ($a === 'gambar') {
        $image_fields = [
            'hero_gambar', 'about_foto_1', 'about_foto_2', 'about_foto_3',
            'why_foto_1', 'why_foto_2', 'why_foto_3',
            'consult_foto_1', 'consult_foto_2', 'consult_foto_3',
        ];
        foreach ($image_fields as $field) {
            if (!empty($_FILES[$field]['name'])) {
                $up = upload_image($_FILES[$field], 'settings');
                if ($up) update_setting($field, $up);
            }
        }
    }

    // Hero overlay: convert 0-100 slider to 0.00-1.00 decimal
    if ($a === 'hero') {
        $overlay_int = isset($_POST['hero_overlay']) ? max(0, min(100, (int)$_POST['hero_overlay'])) : 50;
        update_setting('hero_overlay', number_format($overlay_int / 100, 2, '.', ''));
    }

    // Hero gambar single image
    if ($a === 'hero' && !empty($_FILES['hero_gambar']['name'])) {
        $up = upload_image($_FILES['hero_gambar'], 'settings');
        if ($up) update_setting('hero_gambar', $up);
    }

    // Hero is centralized in settings now — clean up old content_blocks duplicates for home
    if ($a === 'hero') {
        try {
            $db->execute("DELETE FROM content_blocks WHERE page_key='home' AND block_key LIKE 'hero_%'");
        } catch (Throwable $e) { /* table may not exist on first run */ }
    }
    
    // Hero slides
    if ($a === 'hero') {
        $slide_judul = $_POST['slide_judul'] ?? [];
        $slide_sub   = $_POST['slide_sub'] ?? [];
        $slide_ids   = $_POST['slide_id'] ?? [];
        // Clear and re-insert
        foreach ($slide_judul as $si => $sj) {
            if (!trim($sj)) continue;
            $sid = (int)($slide_ids[$si] ?? 0);
            $sdata = ['judul'=>trim($sj),'subtitle'=>trim($slide_sub[$si]??''),'urutan'=>$si,'is_active'=>1];
            // Upload slide image
            if (!empty($_FILES['slide_gambar']['name'][$si])) {
                $sfdata = ['name'=>$_FILES['slide_gambar']['name'][$si],'type'=>$_FILES['slide_gambar']['type'][$si],
                           'tmp_name'=>$_FILES['slide_gambar']['tmp_name'][$si],'error'=>$_FILES['slide_gambar']['error'][$si],
                           'size'=>$_FILES['slide_gambar']['size'][$si]];
                $sup = upload_image($sfdata,'slides');
                if ($sup) $sdata['gambar'] = $sup;
            }
            if ($sid) {
                $set = implode(',',array_map(fn($k)=>"$k=?",array_keys($sdata)));
                $db->execute("UPDATE hero_slides SET $set WHERE id=?", [...array_values($sdata),$sid]);
            } else {
                $db->insert('hero_slides',$sdata);
            }
        }
    }

    // WA Contacts
    if ($a === 'kontak') {
        // Ensure wa_contacts table exists (graceful for un-migrated installs)
        try {
            $db->execute("CREATE TABLE IF NOT EXISTS wa_contacts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nama VARCHAR(100) NOT NULL,
                nomor VARCHAR(30) NOT NULL,
                deskripsi VARCHAR(255) DEFAULT NULL,
                urutan INT DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (Throwable $e) { /* ignore if no privileges */ }

        $wa_ids    = $_POST['wa_id'] ?? [];
        $wa_namas  = $_POST['wa_nama'] ?? [];
        $wa_nums   = $_POST['wa_num'] ?? [];
        $wa_descs  = $_POST['wa_desc'] ?? [];

        // Explicit deletion: any IDs listed in deleted_wa_ids[] get removed
        $deleted_ids = $_POST['deleted_wa_ids'] ?? [];
        if (is_array($deleted_ids)) {
            foreach ($deleted_ids as $del_id) {
                $del_id = (int) $del_id;
                if ($del_id > 0) {
                    $db->execute("DELETE FROM wa_contacts WHERE id = ?", [$del_id]);
                    log_activity('delete', 'WA contact ID ' . $del_id, $user['id']);
                }
            }
        }

        // Upsert remaining contacts
        foreach ($wa_namas as $wi => $wn) {
            if (!trim($wn)) continue;
            $wdata = ['nama'=>trim($wn),'nomor'=>normalize_phone(trim($wa_nums[$wi]??'')),'deskripsi'=>trim($wa_descs[$wi]??''),'urutan'=>$wi,'is_active'=>1];
            $wid = (int)($wa_ids[$wi] ?? 0);
            if ($wid) {
                // Skip if this id was queued for deletion
                if (is_array($deleted_ids) && in_array($wid, array_map('intval', $deleted_ids), true)) continue;
                $set=implode(',',array_map(fn($k)=>"$k=?",array_keys($wdata))); 
                $db->execute("UPDATE wa_contacts SET $set WHERE id=?", [...array_values($wdata),$wid]);
            } else {
                $db->insert('wa_contacts',$wdata);
            }
        }
    }

    log_activity('update','Update pengaturan: '.$a, $user['id']);
    set_flash('success','Pengaturan berhasil disimpan!');
    redirect(admin_url('?page=pengaturan&tab='.$a));
}

$csrf = generate_csrf();
$tab = $_GET['tab'] ?? 'general';
// One-time auto-migrate: if hero_judul/subtitle/cta in settings is empty but exists in content_blocks, copy over
if ($tab === 'hero') {
    try {
        foreach (['hero_judul'=>'hero_judul', 'hero_subtitle'=>'hero_subtitle', 'hero_cta_text'=>'hero_cta'] as $setting_key => $block_key) {
            $current = get_setting($setting_key, '');
            if ($current === '') {
                $existing = $db->fetchOne("SELECT konten FROM content_blocks WHERE page_key='home' AND block_key=?", [$block_key]);
                if ($existing && !empty($existing['konten'])) update_setting($setting_key, $existing['konten']);
            }
        }
    } catch (Throwable $e) { /* ignore */ }
}
$slides = $db->fetchAll("SELECT * FROM hero_slides ORDER BY urutan");
$wa_contacts = $db->fetchAll("SELECT * FROM wa_contacts ORDER BY urutan");

function s($key, $default='') { return htmlspecialchars(get_setting($key, $default)); }
?>

<div class="page-header">
    <div class="page-title"><?= icon('settings', 16) ?> Pengaturan Website</div>
</div>

<div class="tabs">
    <?php $tabs = ['general'=>'Umum','gambar'=>'Gambar Website','whitelabel'=>'Branding Admin','hero'=>'Hero/Slide','homepage'=>'Homepage','kontak'=>'Kontak & WA','sosial'=>'Media Sosial','ads'=>'Conversion','tampilan'=>'Tampilan','docs'=>'Dokumentasi','seo_settings'=>'SEO Default']; ?>
    <?php foreach ($tabs as $tk => $tl): ?>
    <button type="button" class="tab-btn <?= $tab===$tk?'active':'' ?>" data-tab="<?= $tk ?>" onclick="switchTab('<?= $tk ?>', this)"><?= $tl ?></button>
    <?php endforeach; ?>
</div>

<!-- TAB: GENERAL -->
<form method="POST" enctype="multipart/form-data" class="tab-content <?= $tab==='general'?'active':'' ?>" id="tab-general">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="_action" value="general">
    <div class="grid-2" style="align-items:start;gap:20px">
        <div class="card">
            <div class="card-header"><div class="card-title">Identitas Website</div></div>
            <div class="card-body">
                <div class="form-group"><label>Nama Website / Brand</label>
                    <input type="text" name="site_name" class="form-control" value="<?= s('site_name') ?>"></div>
                <div class="form-group"><label>Tagline</label>
                    <input type="text" name="site_tagline" class="form-control" value="<?= s('site_tagline') ?>"></div>
                <div class="form-group"><label>Deskripsi Singkat</label>
                    <textarea name="site_description" class="form-control no-wysiwyg" rows="3"><?= s('site_description') ?></textarea></div>
                <div class="form-group"><label>Email</label>
                    <input type="email" name="site_email" class="form-control" value="<?= s('site_email') ?>"></div>
                <div class="form-group"><label>Telepon</label>
                    <input type="text" name="site_phone" class="form-control" value="<?= s('site_phone') ?>"></div>
                <div class="form-group"><label>Alamat Lengkap</label>
                    <textarea name="site_address" class="form-control no-wysiwyg" rows="3"><?= s('site_address') ?></textarea></div>
                <div class="form-group mb-0"><label>Google Maps Embed URL</label>
                    <input type="url" name="site_maps_embed" class="form-control" value="<?= s('site_maps_embed') ?>"
                           placeholder="https://www.google.com/maps/embed?pb=..."></div>
            </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:20px">
            <div class="card">
                <div class="card-header"><div>
                    <div class="card-title">Logo Website (3 Versi)</div>
                    <div class="card-subtitle">Versi berbeda agar branding tetap jelas di tiap background</div>
                </div></div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:18px">
                    <div>
                        <label style="font-weight:600;font-size:13px">Logo Utama (untuk background TERANG)</label>
                        <?php $logo=get_setting('logo'); if($logo): ?>
                        <div class="img-preview-wrap mb-12" style="background:#fff;padding:8px;border-radius:8px"><img src="<?= uploads_url($logo) ?>" alt="Logo" style="max-height:50px;width:auto"></div>
                        <?php endif; ?>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <div class="form-help">Dipakai di header halaman dalam (background terang). PNG transparan.</div>
                    </div>
                    <div style="border-top:1px solid var(--border);padding-top:16px">
                        <label style="font-weight:600;font-size:13px">Logo Versi TERANG (untuk background gelap)</label>
                        <?php $logo_dark=get_setting('logo_dark'); if($logo_dark): ?>
                        <div class="img-preview-wrap mb-12" style="background:#1E2127;padding:8px;border-radius:8px"><img src="<?= uploads_url($logo_dark) ?>" alt="Logo terang" style="max-height:50px;width:auto"></div>
                        <?php endif; ?>
                        <input type="file" name="logo_dark" class="form-control" accept="image/*">
                        <div class="form-help">Logo putih/terang untuk hero homepage & footer (background gelap).</div>
                    </div>
                    <div style="border-top:1px solid var(--border);padding-top:16px">
                        <label style="font-weight:600;font-size:13px">Logo Footer (opsional)</label>
                        <?php $logo_footer=get_setting('logo_footer'); if($logo_footer): ?>
                        <div class="img-preview-wrap mb-12" style="background:#1E2127;padding:8px;border-radius:8px"><img src="<?= uploads_url($logo_footer) ?>" alt="Logo footer" style="max-height:50px;width:auto"></div>
                        <?php endif; ?>
                        <input type="file" name="logo_footer" class="form-control" accept="image/*">
                        <div class="form-help">Khusus footer. Kosongkan = pakai Logo Versi Terang.</div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><div class="card-title">Favicon</div></div>
                <div class="card-body">
                    <?php $fav=get_setting('favicon'); if($fav): ?>
                    <img src="<?= uploads_url($fav) ?>" style="width:32px;height:32px;margin-bottom:8px">
                    <?php endif; ?>
                    <input type="file" name="favicon" class="form-control" accept="image/*,.ico">
                    <div class="form-help">ICO atau PNG 32x32px</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body" style="text-align:right">
                    <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan Pengaturan Umum</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- TAB: HERO -->
<form method="POST" enctype="multipart/form-data" class="tab-content <?= $tab==='hero'?'active':'' ?>" id="tab-hero">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="_action" value="hero">
    <div class="grid-2" style="align-items:start;gap:20px">
        <div class="card">
            <div class="card-header"><div class="card-title">Mode Hero</div></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Tipe Hero</label>
                    <select name="hero_mode" class="form-control" onchange="toggleHeroMode(this.value)" id="hero-mode-select">
                        <option value="single" <?= (get_setting('hero_mode','single')==='single')?'selected':'' ?>>Single Image</option>
                        <option value="slideshow" <?= get_setting('hero_mode')==='slideshow'?'selected':'' ?>>Slideshow</option>
                    </select>
                </div>
                
                <div class="form-group" id="hero-single-image-wrap">
                    <label><?= icon('image', 16) ?> Hero Image (untuk Mode Single Image)</label>
                    <?php $hi = get_setting('hero_gambar'); if ($hi): ?>
                      <div style="margin-bottom:8px"><img src="<?= uploads_url($hi) ?>" style="height:80px;border-radius:8px;border:1px solid var(--border)"></div>
                    <?php endif; ?>
                    <input type="file" name="hero_gambar" class="form-control" accept="image/*">
                    <div class="form-hint">Rekomendasi: 1920×1080px. Format JPG/PNG/WebP, max 2MB.</div>
                </div>
                <div class="form-group"><label>Judul Hero</label>
                    <input type="text" name="hero_judul" class="form-control" value="<?= s('hero_judul') ?>"
                           placeholder="Heading utama di hero section"></div>
                <div class="form-group"><label>Sub-judul</label>
                    <textarea name="hero_subtitle" class="form-control no-wysiwyg" rows="3"><?= s('hero_subtitle') ?></textarea></div>
                <div class="form-group"><label>Teks CTA Button</label>
                    <input type="text" name="hero_cta_text" class="form-control" value="<?= s('hero_cta_text','Hubungi Kami') ?>"></div>
                <div class="form-group mb-0"><label>URL CTA Button</label>
                    <input type="text" name="hero_cta_url" class="form-control" value="<?= s('hero_cta_url','/hubungi-kami') ?>"></div>

                <div class="form-group" style="margin-top:18px;padding-top:18px;border-top:1px solid var(--border)">
                    <label>Overlay Gelap Hero <span id="hero-overlay-val" style="float:right;font-weight:600;color:var(--accent)"><?= (int)((float)get_setting('hero_overlay','0.5') * 100) ?>%</span></label>
                    <input type="range" name="hero_overlay" id="hero-overlay-range" min="0" max="100" step="5"
                           value="<?= (int)((float)get_setting('hero_overlay','0.5') * 100) ?>"
                           oninput="document.getElementById('hero-overlay-val').textContent=this.value+'%';document.getElementById('hero-overlay-preview').style.background='rgba(0,0,0,'+(this.value/100)+')';this.form.querySelector('input[name=hero_overlay_decimal]').value=(this.value/100).toFixed(2)"
                           style="width:100%;cursor:pointer">
                    <input type="hidden" name="hero_overlay_decimal" value="<?= htmlspecialchars(get_setting('hero_overlay','0.5')) ?>">
                    <div style="position:relative;height:80px;background-image:url('<?= uploads_url(get_setting('hero_gambar','settings/hero-01.webp')) ?>');background-size:cover;background-position:center;border-radius:8px;overflow:hidden;margin-top:8px">
                        <div id="hero-overlay-preview" style="position:absolute;inset:0;background:rgba(0,0,0,<?= htmlspecialchars(get_setting('hero_overlay','0.5')) ?>);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;font-size:14px">Preview overlay</div>
                    </div>
                    <div class="form-help">Geser untuk atur transparansi overlay gelap di atas gambar hero. 0% = tanpa overlay, 100% = full hitam. Default: 50%</div>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan Hero</button></div>
        </div>

        <!-- Slides -->
        <div class="card" id="slides-card-wrap">
            <div class="card-header">
                <div class="card-title"><?= icon('carousel', 16) ?> Slide (untuk Slideshow)</div>
                <button type="button" onclick="addSlide()" class="btn btn-sm btn-secondary">+ Slide</button>
            </div>
            <div class="card-body" id="slides-list">
            <?php foreach ($slides as $si => $slide): ?>
            <div class="slide-item" style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:10px">
                <input type="hidden" name="slide_id[]" value="<?= $slide['id'] ?>">
                <div class="form-group"><label>Judul Slide <?= $si+1 ?></label>
                    <input type="text" name="slide_judul[]" class="form-control" value="<?= htmlspecialchars($slide['judul']) ?>"></div>
                <div class="form-group"><label>Sub-judul</label>
                    <input type="text" name="slide_sub[]" class="form-control" value="<?= htmlspecialchars($slide['subtitle'] ?? '') ?>"></div>
                <div class="form-group mb-8"><label>Gambar</label>
                    <?php if($slide['gambar']): ?><img src="<?= uploads_url($slide['gambar']) ?>" style="height:50px;margin-bottom:8px;border-radius:6px"><br><?php endif; ?>
                    <input type="file" name="slide_gambar[<?= $si ?>]" class="form-control" accept="image/*"></div>
                <button type="button" onclick="this.closest('.slide-item').remove()" class="btn btn-xs btn-danger">× Hapus</button>
            </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</form>

<!-- TAB: KONTAK -->
<form method="POST" class="tab-content <?= $tab==='homepage'?'active':'' ?>" id="tab-homepage">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="_action" value="homepage">
    
    <div class="card">
        <div class="card-header"><div>
            <div class="card-title"><?= icon('block', 16) ?> Pengaturan FAQ</div>
            <div class="card-subtitle">Jumlah maksimum FAQ yang ditampilkan</div>
        </div></div>
        <div class="card-body">
            <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group mb-0"><label>Max FAQ di Homepage</label>
                    <input type="number" name="faq_limit_home" class="form-control" min="1" max="100" value="<?= s('faq_limit_home','100') ?>">
                    <div class="form-help">Default: 100 (efektif semua FAQ aktif tampil)</div>
                </div>
                <div class="form-group mb-0"><label>Max FAQ di Halaman Layanan</label>
                    <input type="number" name="faq_limit_detail" class="form-control" min="1" max="100" value="<?= s('faq_limit_detail','100') ?>">
                    <div class="form-help">Default: 100. FAQ per layanan tertentu</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div>
            <div class="card-title"><?= icon('blog', 16) ?> Tampilkan Blog di Homepage</div>
            <div class="card-subtitle">Section blog otomatis muncul di bawah FAQ — bagus untuk SEO dan internal linking</div>
        </div></div>
        <div class="card-body">
            <label class="checkbox-label" style="font-weight:600;font-size:14px;border-bottom:1px solid var(--border);padding-bottom:14px;margin-bottom:14px;display:block">
                <input type="checkbox" name="homepage_blog_enabled" value="1" <?= s('homepage_blog_enabled')==='1'?'checked':'' ?>>
                Tampilkan section blog di homepage
            </label>
            
            <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
                <div class="form-group mb-0"><label>Jumlah Artikel</label>
                    <select name="homepage_blog_count" class="form-control">
                        <?php $hbc = s('homepage_blog_count','3'); ?>
                        <option value="3" <?= $hbc==='3'?'selected':'' ?>>3 artikel</option>
                        <option value="6" <?= $hbc==='6'?'selected':'' ?>>6 artikel</option>
                        <option value="9" <?= $hbc==='9'?'selected':'' ?>>9 artikel</option>
                        <option value="12" <?= $hbc==='12'?'selected':'' ?>>12 artikel</option>
                    </select>
                </div>
                <div class="form-group mb-0"><label>Tipe Artikel</label>
                    <select name="homepage_blog_type" class="form-control">
                        <?php $hbt = s('homepage_blog_type','recent'); ?>
                        <option value="recent"  <?= $hbt==='recent'?'selected':'' ?>>Recent (terbaru)</option>
                        <option value="popular" <?= $hbt==='popular'?'selected':'' ?>>Popular (paling banyak views)</option>
                        <option value="random"  <?= $hbt==='random'?'selected':'' ?>>Random (acak)</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:12px">
                <label>Section Label (kecil di atas judul)</label>
                <input type="text" name="homepage_blog_label" class="form-control" value="<?= s('homepage_blog_label','Latest Articles') ?>">
            </div>
            <div class="form-group" style="margin-bottom:12px">
                <label>Judul Section</label>
                <textarea name="homepage_blog_title" class="form-control no-wysiwyg" rows="2"><?= s('homepage_blog_title',"Insights & Tips\nFrom Our Blog") ?></textarea>
                <div class="form-help">Gunakan Enter untuk pindah baris</div>
            </div>
            <div class="form-group mb-0">
                <label>Deskripsi</label>
                <textarea name="homepage_blog_desc" class="form-control no-wysiwyg" rows="2"><?= s('homepage_blog_desc','Artikel terbaru seputar dunia reklame, branding, dan tips bisnis.') ?></textarea>
            </div>
        </div>
    </div>

    <div style="text-align:right;margin-top:20px">
        <button type="submit" class="btn btn-primary btn-lg"><?= icon('save', 16) ?> Simpan Pengaturan Homepage</button>
    </div>
</form>

<form method="POST" class="tab-content <?= $tab==='kontak'?'active':'' ?>" id="tab-kontak">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="_action" value="kontak">
    <div class="grid-2" style="align-items:start;gap:20px">
        <div class="card">
            <div class="card-header"><div class="card-title">Pengaturan WhatsApp</div></div>
            <div class="card-body">
                <div class="form-group"><label>Nomor WA Utama</label>
                    <input type="text" name="wa_number" class="form-control" value="<?= s('wa_number') ?>"
                           placeholder="628123456789 (tanpa +)"></div>
                <div class="form-group"><label>Pesan Default WA</label>
                    <input type="text" name="wa_text" class="form-control" value="<?= s('wa_text') ?>"
                           placeholder="Halo, saya ingin bertanya..."></div>
                <div class="form-group"><label>Judul Panel WA Float</label>
                    <input type="text" name="wa_panel_title" class="form-control" value="<?= s('wa_panel_title','Tim Kami') ?>"></div>
                <div class="form-group mb-0"><label>Jam Operasional</label>
                    <input type="text" name="wa_business_hours" class="form-control" value="<?= s('wa_business_hours') ?>"
                           placeholder="Senin – Sabtu, 08.00 – 17.00"></div>
                
                <hr style="border:none;border-top:1px solid var(--border);margin:18px 0">
                <div style="font-size:13px;font-weight:600;margin-bottom:12px"><?= icon('message', 16) ?> Greeting Bubble (sapaan otomatis)</div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="wa_greeting_enabled" value="1" <?= get_setting('wa_greeting_enabled','1')==='1'?'checked':'' ?>>
                        Tampilkan greeting bubble otomatis
                    </label>
                </div>
                <div class="form-group"><label>Judul Greeting</label>
                    <input type="text" name="wa_greeting_title" class="form-control" value="<?= s('wa_greeting_title','Kami Online!') ?>"></div>
                <div class="form-group"><label>Isi Pesan Greeting</label>
                    <input type="text" name="wa_greeting_text" class="form-control" value="<?= s('wa_greeting_text','Bagaimana saya bisa membantu Anda hari ini?') ?>"></div>
                <div class="form-group"><label><?= icon('clock', 16) ?> Delay Muncul (detik)</label>
                    <input type="number" name="wa_greeting_delay" class="form-control" value="<?= s('wa_greeting_delay','5') ?>" min="0" max="60">
                    <div class="form-hint">Berapa detik setelah halaman dibuka, bubble muncul. Default 5 detik.</div></div>
                <div class="form-group mb-0">
                    <label class="checkbox-label">
                        <input type="checkbox" name="wa_greeting_once_per_session" value="1" <?= get_setting('wa_greeting_once_per_session','1')==='1'?'checked':'' ?>>
                        Hanya tampil sekali per kunjungan (tidak muncul lagi setelah ditutup)
                    </label>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="card-title"><?= icon('users', 16) ?> Kontak WhatsApp (Panel Float)</div>
                <button type="button" onclick="addWa()" class="btn btn-sm btn-secondary">+ Tambah</button>
            </div>
            <div class="card-body" id="wa-list">
            <?php foreach ($wa_contacts as $wi => $wc): ?>
            <div class="wa-item" style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:10px">
                <input type="hidden" name="wa_id[]" value="<?= $wc['id'] ?>">
                <div class="form-grid mb-8">
                    <div class="form-group mb-0"><label>Nama</label>
                        <input type="text" name="wa_nama[]" class="form-control" value="<?= htmlspecialchars($wc['nama']) ?>"></div>
                    <div class="form-group mb-0"><label>Nomor</label>
                        <input type="text" name="wa_num[]" class="form-control" value="<?= htmlspecialchars($wc['nomor']) ?>"></div>
                </div>
                <div class="form-group mb-8"><label>Posisi / Deskripsi</label>
                    <input type="text" name="wa_desc[]" class="form-control" value="<?= htmlspecialchars($wc['deskripsi'] ?? '') ?>"></div>
                <button type="button" data-wa-id="<?= $wc['id'] ?>" onclick="removeWa(this, <?= $wc['id'] ?>)" class="btn btn-xs btn-danger"><?= icon('trash', 16) ?> Hapus Kontak</button>
            </div>
            <?php endforeach; ?>
            </div>
            <!-- Hidden container for IDs to delete on submit -->
            <div id="wa-deleted-container"></div>
        </div>
    </div>
    <div style="margin-top:16px;text-align:right">
        <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan Kontak & WA</button>
    </div>
</form>

<!-- TAB: SOSIAL -->
<form method="POST" class="tab-content <?= $tab==='sosial'?'active':'' ?>" id="tab-sosial">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="_action" value="sosial">
    <div class="card" style="max-width:600px">
        <div class="card-header"><div class="card-title"><?= icon('smartphone', 16) ?> Link Media Sosial</div></div>
        <div class="card-body">
            <?php $socials=[['key'=>'sosial_instagram','label'=>'Instagram','ph'=>'https://instagram.com/...'],['key'=>'sosial_facebook','label'=>'Facebook','ph'=>'https://facebook.com/...'],['key'=>'sosial_tiktok','label'=>'TikTok','ph'=>'https://tiktok.com/@...'],['key'=>'sosial_youtube','label'=>'YouTube','ph'=>'https://youtube.com/...'],['key'=>'sosial_twitter','label'=>'X / Twitter','ph'=>'https://twitter.com/...'],['key'=>'sosial_linkedin','label'=>'LinkedIn','ph'=>'https://linkedin.com/company/...']]; ?>
            <?php foreach ($socials as $sc): ?>
            <div class="form-group"><label><?= $sc['label'] ?></label>
                <input type="url" name="<?= $sc['key'] ?>" class="form-control" value="<?= s($sc['key']) ?>" placeholder="<?= $sc['ph'] ?>"></div>
            <?php endforeach; ?>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan Media Sosial</button></div>
    </div>
</form>

<!-- TAB: TAMPILAN -->
<form method="POST" class="tab-content <?= $tab==='tampilan'?'active':'' ?>" id="tab-tampilan">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="_action" value="tampilan">
    <div class="grid-2" style="align-items:start;gap:20px">
        <div class="card">
            <div class="card-header"><div class="card-title"><?= icon('palette', 16) ?> Warna & Font</div></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Warna Aksen Utama</label>
                    <div class="color-row">
                        <input type="color" id="accent-picker" value="<?= s('accent_color','#e8a020') ?>"
                               oninput="document.getElementById('accent-text').value=this.value">
                        <div class="color-preview" id="accent-swatch"
                             style="background:<?= s('accent_color','#e8a020') ?>"
                             onclick="document.getElementById('accent-picker').click()"></div>
                        <input type="text" name="accent_color" id="accent-text" class="form-control"
                               value="<?= s('accent_color','#e8a020') ?>"
                               oninput="document.getElementById('accent-swatch').style.background=this.value">
                    </div>

                    <div class="form-group" style="margin-top:18px;padding-top:18px;border-top:1px solid var(--border)">
                        <label>Warna Utama Gelap (Dark / Base)</label>
                        <div class="color-input-group" style="display:flex;gap:8px;align-items:center">
                            <input type="color" id="dark-picker" value="<?= s('dark_color','#252830') ?>" class="form-control" style="width:60px;height:40px;padding:2px;cursor:pointer" onchange="document.getElementById('dark-text').value=this.value;document.getElementById('dark-preview').style.background=this.value">
                            <div id="dark-preview" style="background:<?= s('dark_color','#252830') ?>" class="color-preview"></div>
                            <input type="text" name="dark_color" id="dark-text" class="form-control" value="<?= s('dark_color','#252830') ?>" placeholder="#252830" oninput="if(/^#[0-9a-f]{6}$/i.test(this.value)){document.getElementById('dark-picker').value=this.value;document.getElementById('dark-preview').style.background=this.value}">
                        </div>
                        <div class="form-help">Untuk hero gelap, text utama, footer. Default: #252830</div>
                    </div>

                    <div class="form-group" style="margin-top:14px">
                        <label>Warna Background Krem (Cream / Light)</label>
                        <div class="color-input-group" style="display:flex;gap:8px;align-items:center">
                            <input type="color" id="cream-picker" value="<?= s('cream_color','#eeeae3') ?>" class="form-control" style="width:60px;height:40px;padding:2px;cursor:pointer" onchange="document.getElementById('cream-text').value=this.value;document.getElementById('cream-preview').style.background=this.value">
                            <div id="cream-preview" style="background:<?= s('cream_color','#eeeae3') ?>" class="color-preview"></div>
                            <input type="text" name="cream_color" id="cream-text" class="form-control" value="<?= s('cream_color','#eeeae3') ?>" placeholder="#eeeae3" oninput="if(/^#[0-9a-f]{6}$/i.test(this.value)){document.getElementById('cream-picker').value=this.value;document.getElementById('cream-preview').style.background=this.value}">
                        </div>
                        <div class="form-help">Background section terang, card cream. Default: #eeeae3</div>
                    </div>

                </div>
                <div class="form-group">
                    <label>Font Heading</label>
                    <select name="font_heading" class="form-control">
                        <?php foreach(['DM Serif Display','Playfair Display','Merriweather','Georgia'] as $f): ?>
                        <option <?= get_setting('font_heading')===$f?'selected':'' ?>><?= $f ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label>Font Body</label>
                    <select name="font_body" class="form-control">
                        <?php foreach(['Outfit','Inter','Plus Jakarta Sans','Poppins','Nunito'] as $f): ?>
                        <option <?= get_setting('font_body')===$f?'selected':'' ?>><?= $f ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><div class="card-title">Footer & CSS</div></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Teks Footer Copyright</label>
                    <input type="text" name="footer_text" class="form-control" value="<?= s('footer_text') ?>"
                           placeholder="© 2025 Reklamepedia. All rights reserved.">
                </div>
                <div class="form-group mb-0">
                    <label>Custom CSS Tambahan</label>
                    <textarea name="custom_css" class="form-control no-wysiwyg" rows="8"
                              style="font-family:monospace;font-size:13px"
                              placeholder="/* CSS kustom Anda */"><?= s('custom_css') ?></textarea>
                    <div class="form-help">Akan ditambahkan ke semua halaman</div>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan Tampilan</button></div>
        </div>
    </div>
</form>

<!-- TAB: SEO -->
<form method="POST" enctype="multipart/form-data" class="tab-content <?= $tab==='seo_settings'?'active':'' ?>" id="tab-seo_settings">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="_action" value="seo_settings">
    <div class="card" style="max-width:700px">
        <div class="card-header"><div class="card-title"><?= icon('search', 16) ?> SEO Default</div></div>
        <div class="card-body">
            <div class="form-group"><label>Meta Title Default</label>
                <input type="text" name="meta_title_default" class="form-control" value="<?= s('meta_title_default') ?>"
                       placeholder="Reklamepedia | Solusi Periklanan Terbaik"></div>
            <div class="form-group"><label>Meta Description Default</label>
                <textarea name="meta_desc_default" class="form-control no-wysiwyg" rows="3"><?= s('meta_desc_default') ?></textarea></div>
            <div class="form-group"><label>Meta Keywords</label>
                <input type="text" name="meta_keywords" class="form-control" value="<?= s('meta_keywords') ?>"
                       placeholder="reklame, papan nama, neon box, ..."></div>
            <div class="form-group"><label>OG Image Default</label>
                <?php $og=get_setting('og_image'); if($og): ?><div class="img-preview-wrap mb-8"><img src="<?= uploads_url($og) ?>"></div><?php endif; ?>
                <input type="file" name="og_image_file" class="form-control" accept="image/*">
                <div class="form-help">1200×630px direkomendasikan untuk share di sosial media</div></div>
            <div class="form-group"><label>Google Site Verification</label>
                <input type="text" name="google_verification" class="form-control" value="<?= s('google_verification') ?>"
                       placeholder="kode-verifikasi-google"></div>
            <div class="form-group mb-0"><label>Robots Default</label>
                <select name="robots_default" class="form-control">
                    <option value="index,follow" <?= get_setting('robots_default')==='index,follow'?'selected':'' ?>>index, follow (Normal)</option>
                    <option value="noindex,nofollow" <?= get_setting('robots_default')==='noindex,nofollow'?'selected':'' ?>>noindex, nofollow (Sembunyikan)</option>
                </select></div>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan SEO</button></div>
    </div>
</form>

<script>
function switchTab(name, btn) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(c => {
        c.classList.remove('active');
        c.style.display = 'none';
    });
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    // Show selected
    const target = document.getElementById('tab-'+name);
    if (target) {
        target.classList.add('active');
        target.style.display = 'block';
    }
    // Activate button (works whether called via onclick event or programmatically)
    if (btn) {
        btn.classList.add('active');
    } else {
        const tabBtn = document.querySelector('.tab-btn[data-tab="'+name+'"]');
        if (tabBtn) tabBtn.classList.add('active');
    }
    history.replaceState(null,'','?page=pengaturan&tab='+name);
}

// On page load: activate tab from URL (?tab=X) - fixes "perlu refresh" issue
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const activeTab = params.get('tab') || 'general';
    // Ensure only the URL tab is visible (override any inline/class conflicts)
    document.querySelectorAll('.tab-content').forEach(c => {
        const isActive = c.id === 'tab-' + activeTab;
        c.style.display = isActive ? 'block' : 'none';
        c.classList.toggle('active', isActive);
    });
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.toggle('active', b.getAttribute('data-tab') === activeTab);
    });
});

function addSlide() {
    const n = document.querySelectorAll('.slide-item').length + 1;
    const div = document.createElement('div');
    div.className = 'slide-item';
    div.style.cssText = 'background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:10px';
    div.innerHTML = `<input type="hidden" name="slide_id[]" value="">
        <div class="form-group"><label>Judul Slide ${n}</label><input type="text" name="slide_judul[]" class="form-control"></div>
        <div class="form-group"><label>Sub-judul</label><input type="text" name="slide_sub[]" class="form-control"></div>
        <div class="form-group mb-8"><label>Gambar</label><input type="file" name="slide_gambar[${n-1}]" class="form-control" accept="image/*"></div>
        <button type="button" onclick="this.closest('.slide-item').remove()" class="btn btn-xs btn-danger">× Hapus</button>`;
    document.getElementById('slides-list').appendChild(div);
}

function removeWa(btn, id) {
    if (!confirm('Yakin ingin menghapus kontak WhatsApp ini?')) return;
    const item = btn.closest('.wa-item');
    if (id > 0) {
        // Mark this ID for backend deletion on submit
        const container = document.getElementById('wa-deleted-container');
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'deleted_wa_ids[]';
        inp.value = id;
        container.appendChild(inp);
    }
    item.remove();
}
function addWa() {
    const div = document.createElement('div');
    div.className = 'wa-item';
    div.style.cssText = 'background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:10px';
    div.innerHTML = `<input type="hidden" name="wa_id[]" value="">
        <div class="form-grid mb-8">
            <div class="form-group mb-0"><label>Nama</label><input type="text" name="wa_nama[]" class="form-control" placeholder="CS / Sales / Tim Teknis"></div>
            <div class="form-group mb-0"><label>Nomor WA</label><input type="text" name="wa_num[]" class="form-control" placeholder="628xxx"></div>
        </div>
        <div class="form-group mb-8"><label>Posisi</label><input type="text" name="wa_desc[]" class="form-control" placeholder="Customer Service"></div>
        <button type="button" onclick="removeWa(this, 0)" class="btn btn-xs btn-danger">× Hapus</button>`;
    document.getElementById('wa-list').appendChild(div);
}
</script>

<!-- TAB: GAMBAR WEBSITE -->
<div class="tab-content <?= $tab === 'gambar' ? 'active' : '' ?>" id="tab-gambar">
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="_action" value="gambar">
    
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title"><?= icon('image', 16) ?> Gambar Hero Beranda</div>
          <div class="card-subtitle">Background image di section hero halaman beranda</div>
        </div>
      </div>
      <?php $hero_img = get_setting('hero_gambar'); ?>
      <?php if ($hero_img): ?>
        <div class="img-upload-row" style="margin-bottom:10px">
          <div class="img-preview" style="width:200px;height:100px"><img src="<?= uploads_url($hero_img) ?>"></div>
          <span class="text-muted">Gambar saat ini</span>
        </div>
      <?php endif; ?>
      <input type="file" name="hero_gambar" accept="image/*">
      <div class="form-hint">Rekomendasi: 1920×1080px atau lebih besar. Format JPG/PNG/WebP.</div>
    </div>
    
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title"><?= icon('camera', 16) ?> Foto Halaman About / Tentang Kami</div>
          <div class="card-subtitle">3 foto yang muncul di grid "Advantage" di halaman About</div>
        </div>
      </div>
      <div class="grid grid-3">
        <?php foreach ([1,2,3] as $i): $key = "about_foto_$i"; $val = get_setting($key); ?>
        <div>
          <label>Foto About <?= $i ?></label>
          <?php if ($val): ?>
            <div class="img-preview" style="width:100%;height:120px;margin-bottom:8px"><img src="<?= uploads_url($val) ?>"></div>
          <?php endif; ?>
          <input type="file" name="<?= $key ?>" accept="image/*">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title"><?= icon('client', 16) ?> Foto Halaman Services (Why Choose Us)</div>
          <div class="card-subtitle">3 foto di section "Where Quality Meets Reliability" halaman Services</div>
        </div>
      </div>
      <div class="grid grid-3">
        <?php foreach ([1,2,3] as $i): $key = "why_foto_$i"; $val = get_setting($key); ?>
        <div>
          <label>Foto Why <?= $i ?></label>
          <?php if ($val): ?>
            <div class="img-preview" style="width:100%;height:120px;margin-bottom:8px"><img src="<?= uploads_url($val) ?>"></div>
          <?php endif; ?>
          <input type="file" name="<?= $key ?>" accept="image/*">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    
    <div style="display:flex;justify-content:flex-end;padding-top:16px">
      <button type="submit" class="btn btn-primary btn-lg"><?= icon('save', 16) ?> Simpan Gambar</button>
    </div>
  </form>
</div>

<!-- TAB: DOKUMENTASI URL -->
<div class="tab-content <?= $tab === 'docs' ? 'active' : '' ?>" id="tab-docs">
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="_action" value="docs">
    <div class="card">
      <div class="card-header"><div class="card-title"><?= icon('docs', 16) ?> URL Dokumentasi</div></div>
      <div class="form-group">
        <label>URL Dokumentasi (untuk menu Sidebar Admin)</label>
        <input type="url" name="docs_url" value="<?= s('docs_url', '#') ?>" placeholder="https://docs.example.com">
        <div class="form-hint">URL ini akan jadi link "Dokumentasi" di sidebar admin.</div>
      </div>
      <div style="display:flex;justify-content:flex-end">
        <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan</button>
      </div>
    </div>
  </form>
</div>
<script>
function toggleHeroMode(mode) {
  const single = document.getElementById('hero-single-image-wrap');
  const slides = document.getElementById('slides-card-wrap');
  if (single) single.style.display = mode === 'single' ? '' : 'none';
  if (slides) slides.style.display = mode === 'slideshow' ? '' : 'none';
}
document.addEventListener('DOMContentLoaded', () => {
  const select = document.getElementById('hero-mode-select');
  if (select) toggleHeroMode(select.value);
});
</script>

<!-- TAB: WHITE-LABEL / BRANDING ADMIN -->
<form method="POST" enctype="multipart/form-data" class="tab-content <?= $tab === 'whitelabel' ? 'active' : '' ?>" id="tab-whitelabel">
  <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
  <input type="hidden" name="_action" value="whitelabel">
  
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title"><?= icon('tag', 16) ?> Branding Admin (White-Label)</div>
        <div class="card-subtitle">Kustomisasi tampilan halaman login & sidebar admin. Cocok jika sistem dijual ke client.</div>
      </div>
    </div>
    
    <div class="form-row">
      <div class="form-group">
        <label><?= icon('image', 16) ?> Logo Admin (sidebar & favicon admin)</label>
        <?php $cur_logo = get_setting('logo'); if ($cur_logo): ?>
          <div style="margin-bottom:8px;background:#1E2127;padding:12px;border-radius:8px;display:inline-block">
            <img src="<?= uploads_url($cur_logo) ?>" style="height:36px">
          </div>
        <?php endif; ?>
        <input type="file" name="logo" accept="image/*">
        <div class="form-hint">PNG transparan rekomendasi, max height 50px</div>
      </div>
      
      <div class="form-group">
        <label><?= icon('palette', 16) ?> Logo Login (jika beda dari logo admin)</label>
        <?php $cur_llogo = get_setting('admin_login_logo'); if ($cur_llogo): ?>
          <div style="margin-bottom:8px;background:#1E2127;padding:12px;border-radius:8px;display:inline-block">
            <img src="<?= uploads_url($cur_llogo) ?>" style="height:48px">
          </div>
        <?php endif; ?>
        <input type="file" name="admin_login_logo" accept="image/*">
        <div class="form-hint">Kosongkan = pakai Logo Admin di atas</div>
      </div>
    </div>
    
    <div class="form-group">
      <label>Favicon (icon di tab browser)</label>
      <?php $cur_fav = get_setting('favicon'); if ($cur_fav): ?>
        <div style="margin-bottom:8px"><img src="<?= uploads_url($cur_fav) ?>" style="width:32px;height:32px"></div>
      <?php endif; ?>
      <input type="file" name="favicon" accept="image/*">
      <div class="form-hint">Format PNG/ICO, 32×32 atau 64×64</div>
    </div>
    
    <hr style="border:none;border-top:1px solid var(--border);margin:24px 0">
    
    <div class="form-group">
      <label>Judul Halaman Login</label>
      <input type="text" name="admin_login_title" value="<?= s('admin_login_title', 'Kelola website Anda dengan mudah.') ?>">
      <div class="form-hint">Heading besar di kiri halaman login</div>
    </div>
    
    <div class="form-group">
      <label>Deskripsi Halaman Login</label>
      <textarea name="admin_login_desc" rows="3" class="wysiwyg"><?= s('admin_login_desc', 'Login untuk mengakses dashboard admin. Kelola konten, layanan, galeri, blog, produk, dan semua pengaturan website dari satu tempat.') ?></textarea>
    </div>
    
    <div class="alert alert-info">
      <?= icon('lightbulb', 16) ?> <strong>Tip White-Label:</strong> Setelah upload logo admin, hardcoded text "Reklamepedia" otomatis diganti dengan nama dari <strong>Nama Brand</strong> di tab Umum. Nama bisa berbeda untuk tiap client.
    </div>
    
    <div style="display:flex;justify-content:flex-end;padding-top:16px">
      <button type="submit" class="btn btn-primary btn-lg"><?= icon('save', 16) ?> Simpan Branding</button>
    </div>
  </div>
</form>

<!-- TAB: GOOGLE ADS CONVERSION TRACKING -->
<form method="POST" class="tab-content <?= $tab === 'ads' ? 'active' : '' ?>" id="tab-ads">
  <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
  <input type="hidden" name="_action" value="ads">
  
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title"><?= icon('target', 16) ?> Google Ads Conversion Tracking</div>
        <div class="card-subtitle">Track klik tombol WhatsApp sebagai conversion di Google Ads</div>
      </div>
    </div>
    
    <div class="form-group">
      <label class="checkbox-label">
        <input type="checkbox" name="gads_conversion_enabled" value="1" <?= get_setting('gads_conversion_enabled','0')==='1'?'checked':'' ?>>
        <strong>Aktifkan Google Ads Conversion Tracking</strong>
      </label>
      <div class="form-hint">gtag.js akan otomatis dimuat di semua halaman saat fitur ini aktif.</div>
    </div>
    
    <div class="form-row">
      <div class="form-group">
        <label>Conversion ID</label>
        <input type="text" name="gads_conversion_id" class="form-control" value="<?= s('gads_conversion_id') ?>" placeholder="315835552">
        <div class="form-hint">Hanya angka, contoh: <code>315835552</code> (tanpa "AW-" prefix)</div>
      </div>
      <div class="form-group">
        <label>Conversion Label</label>
        <input type="text" name="gads_conversion_label" class="form-control" value="<?= s('gads_conversion_label') ?>" placeholder="I9YPCLSnH-4CEKCJzZYB">
      </div>
    </div>
    
    <div class="alert alert-info">
      <?= icon('pin', 16) ?> <strong>Cara dapat Conversion ID & Label:</strong>
      <ol style="margin:8px 0 0 20px;line-height:1.7;font-size:13px">
        <li>Login ke <a href="https://ads.google.com" target="_blank">Google Ads</a> <?= icon('arrow-right', 16) ?> Tools & Settings <?= icon('arrow-right', 16) ?> Conversions</li>
        <li>Klik "+ New conversion action" <?= icon('arrow-right', 16) ?> pilih "Website"</li>
        <li>Isi nama (misal: "WhatsApp Click"), Category: "Contact", set Value</li>
        <li>Pilih "Use Google tag" sebagai installation method</li>
        <li>Salin <strong>Conversion ID</strong> (angka setelah AW-) dan <strong>Conversion Label</strong></li>
        <li>Paste di form ini <?= icon('arrow-right', 16) ?> Simpan</li>
      </ol>
      <div style="margin-top:10px;font-size:13px"><strong>Test:</strong> Buka website Anda di incognito mode, klik tombol WhatsApp. Cek di Google Ads <?= icon('arrow-right', 16) ?> Conversions setelah ~3 jam.</div>
    </div>
    
    <div class="card" style="margin-top:20px">
      <div class="card-header"><div>
        <div class="card-title"><?= icon('lock', 16) ?> Cloudflare Turnstile (Anti-Spam Form)</div>
        <div class="card-subtitle">Melindungi form lamaran Career & kontak dari bot. Kosongkan untuk menonaktifkan.</div>
      </div></div>
      <div class="card-body">
        <div class="form-row">
          <div class="form-group"><label>Site Key</label>
            <input type="text" name="turnstile_site_key" class="form-control no-wysiwyg" value="<?= s('turnstile_site_key') ?>" placeholder="0x4AAAAAAA...">
          </div>
          <div class="form-group"><label>Secret Key</label>
            <input type="text" name="turnstile_secret" class="form-control no-wysiwyg" value="<?= s('turnstile_secret') ?>" placeholder="0x4AAAAAAA...">
          </div>
        </div>
        <div class="form-hint"><?= icon('info', 13) ?> Dapatkan key gratis di dashboard Cloudflare <?= icon('arrow-right', 14) ?> Turnstile <?= icon('arrow-right', 14) ?> Add site. Verifikasi dilakukan server-side.</div>
      </div>
    </div>

    <div style="display:flex;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border);margin-top:16px">
      <button type="submit" class="btn btn-primary btn-lg"><?= icon('save', 16) ?> Simpan</button>
    </div>
  </div>
</form>

