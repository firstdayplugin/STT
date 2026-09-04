<?php
$page_title = 'Iklan & Tracking Scripts';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error','Token invalid.'); redirect(admin_url('?page=ads')); }

    $keys = ['ga_id','gtm_id','meta_pixel_id','tiktok_pixel_id','custom_head_script','custom_body_script','custom_footer_script'];
    foreach ($keys as $k) {
        if (array_key_exists($k, $_POST)) update_setting($k, $_POST[$k]);
    }
    log_activity('update','Update ads/tracking scripts',$user['id']);
    set_flash('success','Script tracking berhasil disimpan!');
    redirect(admin_url('?page=ads'));
}

$csrf = generate_csrf();
function sv($k) { return htmlspecialchars(get_setting($k,'')); }
?>

<div class="page-header">
    <div class="page-title">📊 Iklan & Tracking Scripts
        <small>Google Analytics, GTM, Meta Pixel, TikTok Pixel, Custom Scripts</small>
    </div>
</div>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

<div class="grid-2" style="align-items:start;gap:20px">

    <!-- Tracking IDs -->
    <div style="display:flex;flex-direction:column;gap:20px">

        <div class="card">
            <div class="card-header">
                <div class="card-title">🔵 Google Analytics (GA4)</div>
                <span class="badge <?= get_setting('ga_id') ? 'badge-success' : 'badge-muted' ?>"><?= get_setting('ga_id') ? 'Aktif' : 'Nonaktif' ?></span>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label>Measurement ID</label>
                    <input type="text" name="ga_id" class="form-control" value="<?= sv('ga_id') ?>"
                           placeholder="G-XXXXXXXXXX">
                    <div class="form-help">Kosongkan untuk menonaktifkan tracking GA4</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">🟠 Google Tag Manager</div>
                <span class="badge <?= get_setting('gtm_id') ? 'badge-success' : 'badge-muted' ?>"><?= get_setting('gtm_id') ? 'Aktif' : 'Nonaktif' ?></span>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label>Container ID</label>
                    <input type="text" name="gtm_id" class="form-control" value="<?= sv('gtm_id') ?>"
                           placeholder="GTM-XXXXXXX">
                    <div class="form-help">GTM akan otomatis inject script di &lt;head&gt; dan &lt;body&gt;</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">🔵 Meta (Facebook) Pixel</div>
                <span class="badge <?= get_setting('meta_pixel_id') ? 'badge-success' : 'badge-muted' ?>"><?= get_setting('meta_pixel_id') ? 'Aktif' : 'Nonaktif' ?></span>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label>Pixel ID</label>
                    <input type="text" name="meta_pixel_id" class="form-control" value="<?= sv('meta_pixel_id') ?>"
                           placeholder="1234567890123456">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">⚫ TikTok Pixel</div>
                <span class="badge <?= get_setting('tiktok_pixel_id') ? 'badge-success' : 'badge-muted' ?>"><?= get_setting('tiktok_pixel_id') ? 'Aktif' : 'Nonaktif' ?></span>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label>Pixel ID</label>
                    <input type="text" name="tiktok_pixel_id" class="form-control" value="<?= sv('tiktok_pixel_id') ?>"
                           placeholder="XXXXXXXXXXXXXXXX">
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Scripts -->
    <div style="display:flex;flex-direction:column;gap:20px">

        <div class="alert alert-info">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <strong>Cara Kerja Script Kustom</strong><br>
                Script kustom di-inject ke semua halaman frontend website. Posisi injeksi:<br>
                <code style="color:var(--accent)">&lt;head&gt;</code> → sebelum &lt;/head&gt;<br>
                <code style="color:var(--accent)">&lt;body&gt;</code> → setelah &lt;body&gt;<br>
                <code style="color:var(--accent)">Footer</code> → sebelum &lt;/body&gt;
            </div>
        </div>

        <div class="card">
            <div class="card-header"><div class="card-title">📄 Script di &lt;head&gt;</div></div>
            <div class="card-body">
                <textarea name="custom_head_script" class="form-control" rows="8"
                          style="font-family:monospace;font-size:13px"
                          placeholder="<!-- Script yang diletakkan sebelum </head> -->"><?= sv('custom_head_script') ?></textarea>
                <div class="form-help">Untuk: meta tags kustom, preload fonts, CSS eksternal, dll</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><div class="card-title">⬆️ Script Awal &lt;body&gt;</div></div>
            <div class="card-body">
                <textarea name="custom_body_script" class="form-control" rows="6"
                          style="font-family:monospace;font-size:13px"
                          placeholder="<!-- Script yang diletakkan setelah <body> -->"><?= sv('custom_body_script') ?></textarea>
                <div class="form-help">Untuk: GTM noscript, live chat widgets, dll</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><div class="card-title">⬇️ Script Akhir (Footer)</div></div>
            <div class="card-body">
                <textarea name="custom_footer_script" class="form-control" rows="6"
                          style="font-family:monospace;font-size:13px"
                          placeholder="<!-- Script yang diletakkan sebelum </body> -->"><?= sv('custom_footer_script') ?></textarea>
                <div class="form-help">Untuk: chat widgets, popup scripts, analytics tambahan</div>
            </div>
        </div>

        <div style="text-align:right">
            <button type="submit" class="btn btn-primary">💾 Simpan Semua Script</button>
        </div>
    </div>

</div>
</form>

<!-- WA Click Stats -->
<div class="card mt-24" style="margin-top:24px">
    <div class="card-header"><div class="card-title">📱 Statistik Klik WhatsApp</div></div>
    <div class="table-wrapper">
        <?php
        $wa_stats = $db->fetchAll(
            "SELECT wc.nama, wc.nomor, SUM(wk.clicks) as total_clicks, MAX(wk.created_at) as last_click
             FROM wa_contacts wc
             LEFT JOIN wa_clicks wk ON wk.contact_id = wc.id
             GROUP BY wc.id ORDER BY total_clicks DESC"
        );
        ?>
        <table>
            <thead><tr><th>Nama Kontak</th><th>Nomor</th><th>Total Klik</th><th>Terakhir Diklik</th></tr></thead>
            <tbody>
            <?php if (empty($wa_stats)): ?>
            <tr><td colspan="4" style="text-align:center;padding:24px" class="text-muted">Belum ada data klik WA</td></tr>
            <?php else: ?>
            <?php foreach ($wa_stats as $ws): ?>
            <tr>
                <td style="font-weight:500"><?= htmlspecialchars($ws['nama']) ?></td>
                <td class="text-muted">+<?= htmlspecialchars($ws['nomor']) ?></td>
                <td><span class="badge badge-accent"><?= number_format($ws['total_clicks'] ?? 0) ?> klik</span></td>
                <td class="text-muted text-sm"><?= $ws['last_click'] ? time_ago($ws['last_click']) : '-' ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
