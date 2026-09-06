<?php
/**
 * Admin — Solutions (cube / prism animation slides on the Home page).
 * Feeds the `solution_slides` table; the Home template injects it into anima.js (§14.2).
 * Each slide: eyebrow/heading/desc, watermark label, optional image OR short video panel,
 * gradient colors (fallback texture), partner logos (uploads/URLs/built-in keys), link, order.
 */
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

/** Decode the logos JSON column to a clean array of strings. */
$logos_of = function ($raw): array {
    if (empty($raw)) return [];
    $d = json_decode($raw, true);
    return is_array($d) ? array_values(array_filter(array_map('strval', $d), fn($s) => $s !== '')) : [];
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
        redirect(admin_url('?page=solusi'));
    }
    $act = $_POST['action'] ?? '';

    if ($act === 'delete' && $id > 0) {
        $db->execute("DELETE FROM solution_slides WHERE id = ?", [$id]);
        log_activity('delete', 'Hapus solution slide #' . $id);
        set_flash('success', 'Slide dihapus.');
        redirect(admin_url('?page=solusi'));
    }

    $data = [
        'eyebrow'      => trim($_POST['eyebrow'] ?? ''),
        'judul'        => trim($_POST['judul'] ?? ''),
        'deskripsi'    => trim($_POST['deskripsi'] ?? ''),
        'label'        => trim($_POST['label'] ?? ''),
        'warna_dark'   => trim($_POST['warna_dark'] ?? '#0a1430') ?: '#0a1430',
        'warna_mid'    => trim($_POST['warna_mid'] ?? '#123a6a') ?: '#123a6a',
        'warna_accent' => trim($_POST['warna_accent'] ?? '#42a0ff') ?: '#42a0ff',
        'url'          => trim($_POST['url'] ?? ''),
        'urutan'       => (int)($_POST['urutan'] ?? 0),
        'is_active'    => isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($data['judul'] === '' && $data['eyebrow'] === '') {
        set_flash('error', 'Minimal isi Eyebrow atau Judul.');
        redirect(admin_url('?page=solusi&action=' . ($act === 'update' ? 'edit&id=' . $id : 'create')));
    }

    $current = ($act === 'update' && $id > 0) ? $db->fetchOne("SELECT * FROM solution_slides WHERE id=?", [$id]) : null;

    // --- Panel image ---
    $gambar = $current['gambar'] ?? null; $set_gambar = true;
    if (!empty($_FILES['gambar']['name'])) {
        $up = upload_image($_FILES['gambar'], 'solutions');
        if ($up) $gambar = $up; else set_flash('error', 'Upload gambar gagal.');
    } elseif (!empty($_POST['hapus_gambar'])) {
        $gambar = null;
    }

    // --- Panel video (upload or external URL) ---
    $video = $current['video_url'] ?? null;
    if (!empty($_FILES['video']['name'])) {
        $uv = upload_video($_FILES['video'], 'solutions');
        if ($uv) $video = $uv; else set_flash('error', 'Upload video gagal (mp4/webm, max 15MB).');
    } elseif (trim($_POST['video_url_ext'] ?? '') !== '') {
        $video = trim($_POST['video_url_ext']);
    } elseif (!empty($_POST['hapus_video'])) {
        $video = null;
    }

    // --- Logos: keep (minus removed) + text entries + uploaded files ---
    $logos = $logos_of($current['logos'] ?? '');
    $remove = $_POST['logo_remove'] ?? [];
    if (is_array($remove) && $remove) {
        $logos = array_values(array_filter($logos, fn($l) => !in_array($l, $remove, true)));
    }
    foreach (preg_split('/[\r\n,]+/', (string)($_POST['logos_text'] ?? '')) as $line) {
        $line = trim($line);
        if ($line !== '') $logos[] = $line;
    }
    if (!empty($_FILES['logos_new']['name'][0])) {
        $cnt = count($_FILES['logos_new']['name']);
        for ($i = 0; $i < $cnt; $i++) {
            if (($_FILES['logos_new']['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) continue;
            $one = ['name'=>$_FILES['logos_new']['name'][$i],'type'=>$_FILES['logos_new']['type'][$i],
                    'tmp_name'=>$_FILES['logos_new']['tmp_name'][$i],'error'=>$_FILES['logos_new']['error'][$i],
                    'size'=>$_FILES['logos_new']['size'][$i]];
            $up = upload_image($one, 'solutions/logos');
            if ($up) $logos[] = $up;
        }
    }
    $logos = array_values(array_unique($logos));
    $logos_json = $logos ? json_encode($logos, JSON_UNESCAPED_SLASHES) : null;

    if ($act === 'create') {
        $db->execute(
            "INSERT INTO solution_slides (eyebrow,judul,deskripsi,label,gambar,video_url,warna_dark,warna_mid,warna_accent,logos,url,urutan,is_active)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$data['eyebrow'],$data['judul'],$data['deskripsi'],$data['label'],$gambar,$video,
             $data['warna_dark'],$data['warna_mid'],$data['warna_accent'],$logos_json,$data['url'],$data['urutan'],$data['is_active']]
        );
        log_activity('create', 'Tambah solution slide: ' . ($data['eyebrow'] ?: $data['judul']));
        set_flash('success', 'Slide ditambahkan.');
    } elseif ($act === 'update' && $id > 0) {
        $db->execute(
            "UPDATE solution_slides SET eyebrow=?,judul=?,deskripsi=?,label=?,gambar=?,video_url=?,warna_dark=?,warna_mid=?,warna_accent=?,logos=?,url=?,urutan=?,is_active=? WHERE id=?",
            [$data['eyebrow'],$data['judul'],$data['deskripsi'],$data['label'],$gambar,$video,
             $data['warna_dark'],$data['warna_mid'],$data['warna_accent'],$logos_json,$data['url'],$data['urutan'],$data['is_active'],$id]
        );
        log_activity('update', 'Update solution slide: ' . ($data['eyebrow'] ?: $data['judul']));
        set_flash('success', 'Slide diperbarui.');
    }
    redirect(admin_url('?page=solusi'));
}

$items = $db->fetchAll("SELECT * FROM solution_slides ORDER BY urutan ASC, id ASC");
$edit_item = ($action === 'edit' && $id > 0) ? $db->fetchOne("SELECT * FROM solution_slides WHERE id=?", [$id]) : null;
$edit_logos = $edit_item ? $logos_of($edit_item['logos'] ?? '') : [];
// A logo entry is a full URL/absolute path, an uploads-relative path (has a slash),
// or a built-in key (bare token -> resolved by anima.js, shown as text here).
$is_abs   = fn($s) => (bool)preg_match('#^(https?:|/|data:)#', $s);
$is_asset = fn($s) => $is_abs($s) || str_contains($s, '/');       // renders as an <img>
$logo_src = fn($s) => $is_abs($s) ? $s : uploads_url($s);
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1><?= icon('box', 18) ?> Solutions — Cube</h1>
    <div class="page-header-sub">Slide animasi cube (Three.js) di halaman Home. Tiap panel bisa diisi foto atau video pendek + logo partner.</div>
  </div>
  <?php if ($action === 'list'): ?>
  <div class="page-actions">
    <a href="<?= url('/') ?>#solutions" target="_blank" class="btn btn-secondary"><?= icon('eye', 16) ?> Lihat di Home</a>
    <a href="<?= admin_url('?page=solusi&action=create') ?>" class="btn btn-primary"><?= icon('plus', 16) ?> Tambah Slide</a>
  </div>
  <?php endif; ?>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>
  <form method="POST" enctype="multipart/form-data" class="card">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
    <div class="card-header"><div class="card-title"><?= $action === 'create' ? 'Tambah' : 'Edit' ?> Slide Solusi</div></div>
    <div class="card-body">

      <div class="form-row">
        <div class="form-group">
          <label>Eyebrow</label>
          <input type="text" name="eyebrow" value="<?= htmlspecialchars($edit_item['eyebrow'] ?? '') ?>" placeholder="Modernize Infrastructure">
        </div>
        <div class="form-group">
          <label>Watermark Label</label>
          <input type="text" name="label" maxlength="60" value="<?= htmlspecialchars($edit_item['label'] ?? '') ?>" placeholder="INFRA">
          <div class="form-hint">Teks besar di texture panel (dipakai kalau tanpa foto/video).</div>
        </div>
      </div>

      <div class="form-group">
        <label>Judul (heading)</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($edit_item['judul'] ?? '') ?>" placeholder="Modernize &lt;b&gt;Infrastructure&lt;/b&gt;">
        <div class="form-hint">Boleh pakai <code>&lt;b&gt;...&lt;/b&gt;</code> untuk penekanan.</div>
      </div>

      <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="deskripsi" rows="3" class="no-wysiwyg"><?= htmlspecialchars($edit_item['deskripsi'] ?? '') ?></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Foto Panel (opsional)</label>
          <?php if (!empty($edit_item['gambar'])): ?>
            <div class="img-upload-row" style="margin-bottom:8px">
              <div class="img-preview" style="width:96px;height:54px"><img src="<?= uploads_url($edit_item['gambar']) ?>" alt=""></div>
              <label class="checkbox-label" style="font-size:12px"><input type="checkbox" name="hapus_gambar" value="1"> Hapus</label>
            </div>
          <?php endif; ?>
          <input type="file" name="gambar" accept="image/*">
          <div class="form-hint">Foto jadi wajah panel cube. JPG/PNG/WebP, max 5MB.</div>
        </div>
        <div class="form-group">
          <label>Video Panel (opsional)</label>
          <?php if (!empty($edit_item['video_url'])): ?>
            <div style="margin-bottom:8px;font-size:12px;color:var(--text-muted)">
              Video saat ini: <?= htmlspecialchars(basename($edit_item['video_url'])) ?>
              <label class="checkbox-label" style="font-size:12px"><input type="checkbox" name="hapus_video" value="1"> Hapus</label>
            </div>
          <?php endif; ?>
          <input type="file" name="video" accept="video/mp4,video/webm">
          <input type="text" name="video_url_ext" style="margin-top:6px" placeholder="atau URL video eksternal (https://...)">
          <div class="form-hint">Video mengalahkan foto. Upload mp4/webm (max 15MB) atau tempel URL. Otomatis muted &amp; loop.</div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Warna Dark</label>
          <div style="display:flex;gap:8px;align-items:center">
            <input type="color" value="<?= htmlspecialchars($edit_item['warna_dark'] ?? '#0a1430') ?>" data-sync="warna_dark" style="width:44px;height:38px;padding:2px">
            <input type="text" id="warna_dark" name="warna_dark" value="<?= htmlspecialchars($edit_item['warna_dark'] ?? '#0a1430') ?>" style="flex:1">
          </div>
        </div>
        <div class="form-group">
          <label>Warna Mid</label>
          <div style="display:flex;gap:8px;align-items:center">
            <input type="color" value="<?= htmlspecialchars($edit_item['warna_mid'] ?? '#123a6a') ?>" data-sync="warna_mid" style="width:44px;height:38px;padding:2px">
            <input type="text" id="warna_mid" name="warna_mid" value="<?= htmlspecialchars($edit_item['warna_mid'] ?? '#123a6a') ?>" style="flex:1">
          </div>
        </div>
        <div class="form-group">
          <label>Warna Accent</label>
          <div style="display:flex;gap:8px;align-items:center">
            <input type="color" value="<?= htmlspecialchars($edit_item['warna_accent'] ?? '#42a0ff') ?>" data-sync="warna_accent" style="width:44px;height:38px;padding:2px">
            <input type="text" id="warna_accent" name="warna_accent" value="<?= htmlspecialchars($edit_item['warna_accent'] ?? '#42a0ff') ?>" style="flex:1">
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>Logo Partner</label>
        <?php if ($edit_logos): ?>
          <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:10px">
            <?php foreach ($edit_logos as $lg): ?>
              <label style="display:flex;flex-direction:column;align-items:center;gap:4px;border:1px solid var(--border);border-radius:8px;padding:8px;min-width:84px">
                <?php if ($is_asset($lg)): ?>
                  <img src="<?= htmlspecialchars($logo_src($lg)) ?>" alt="" style="height:26px;object-fit:contain;max-width:70px">
                <?php else: ?>
                  <span style="font-family:monospace;font-size:11px;padding:6px 4px"><?= htmlspecialchars($lg) ?></span>
                <?php endif; ?>
                <span style="font-size:11px;color:var(--danger,#dc2626)"><input type="checkbox" name="logo_remove[]" value="<?= htmlspecialchars($lg) ?>"> hapus</span>
              </label>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <input type="file" name="logos_new[]" accept="image/*" multiple>
        <textarea name="logos_text" rows="2" class="no-wysiwyg" style="margin-top:8px" placeholder="Tambah via teks (satu per baris): built-in key (dell, intel, microsoft...) atau URL/path"></textarea>
        <div class="form-hint">Upload beberapa logo sekaligus, atau ketik key bawaan / URL. Semua digabung.</div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Link (opsional)</label>
          <input type="text" name="url" value="<?= htmlspecialchars($edit_item['url'] ?? '') ?>" placeholder="solutions/infrastructure atau https://...">
        </div>
        <div class="form-group">
          <label>Urutan</label>
          <input type="number" name="urutan" value="<?= (int)($edit_item['urutan'] ?? 0) ?>">
        </div>
      </div>

      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox" name="is_active" <?= ($edit_item['is_active'] ?? 1) ? 'checked' : '' ?>> Tampilkan di website
        </label>
      </div>

      <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border)">
        <a href="<?= admin_url('?page=solusi') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan</button>
      </div>
    </div>
  </form>

<?php elseif (empty($items)): ?>
  <div class="card"><div class="empty-state">
    <div class="empty-state-icon"><?= icon('box', 40) ?></div>
    <div>Belum ada slide solusi.</div>
    <a href="<?= admin_url('?page=solusi&action=create') ?>" class="btn btn-primary mt-2"><?= icon('plus', 16) ?> Tambah Slide Pertama</a>
  </div></div>
<?php else: ?>
  <div class="table-wrap"><table>
    <thead><tr>
      <th style="width:80px">Panel</th><th>Eyebrow / Judul</th><th style="width:90px">Media</th>
      <th style="width:70px">Logo</th><th style="width:70px">Urutan</th><th style="width:80px">Status</th><th style="width:130px">Aksi</th>
    </tr></thead>
    <tbody>
    <?php foreach ($items as $it): $lg = $logos_of($it['logos'] ?? ''); ?>
      <tr>
        <td>
          <?php if (!empty($it['gambar'])): ?>
            <div class="img-preview" style="width:64px;height:36px"><img src="<?= uploads_url($it['gambar']) ?>" alt=""></div>
          <?php else: ?>
            <div style="width:64px;height:36px;border-radius:6px;background:linear-gradient(135deg,<?= htmlspecialchars($it['warna_dark']) ?>,<?= htmlspecialchars($it['warna_mid']) ?>);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.6);font-size:9px;font-weight:700"><?= htmlspecialchars($it['label']) ?></div>
          <?php endif; ?>
        </td>
        <td>
          <div style="font-weight:600"><?= htmlspecialchars($it['eyebrow'] ?: strip_tags($it['judul'] ?? '')) ?></div>
          <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars(strip_tags($it['judul'] ?? '')) ?></div>
        </td>
        <td>
          <?php if (!empty($it['video_url'])): ?><span class="badge badge-info"><?= icon('film', 13) ?> Video</span>
          <?php elseif (!empty($it['gambar'])): ?><span class="badge badge-info"><?= icon('image', 13) ?> Foto</span>
          <?php else: ?><span class="badge badge-gray">Generate</span><?php endif; ?>
        </td>
        <td><?= count($lg) ?></td>
        <td><?= (int)$it['urutan'] ?></td>
        <td><?= $it['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
        <td><div class="table-actions">
          <a href="<?= admin_url('?page=solusi&action=edit&id=' . $it['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Hapus slide ini?')">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="action" value="delete">
            <button type="submit" class="btn btn-danger btn-sm" formaction="<?= admin_url('?page=solusi&action=delete&id=' . $it['id']) ?>"><?= icon('trash', 15) ?></button>
          </form>
        </div></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
<?php endif; ?>
