<?php
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Handle carousel settings save (before main testimonial CRUD)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'carousel_settings') {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        foreach (['testimonial_limit_home','testimonial_cols_desktop','testimonial_cols_tablet',
                  'testimonial_cols_mobile','testimonial_speed'] as $k) {
            if (isset($_POST[$k])) update_setting($k, sanitize($_POST[$k]));
        }
        foreach (['testimonial_autoplay','testimonial_loop','testimonial_show_dots','testimonial_show_nav','testimonial_show_services'] as $k) {
            update_setting($k, isset($_POST[$k]) ? '1' : '0');
        }
        set_flash('success', '✅ Pengaturan testimoni disimpan.');
    }
    redirect(admin_url('?page=testimonial'));
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
     redirect(admin_url('?page=' . ($_GET['page'] ?? 'dashboard'))); } else {
        $data = [
            'nama'      => trim($_POST['nama'] ?? ''),
            'jabatan'   => trim($_POST['jabatan'] ?? ''),
            'perusahaan'=> trim($_POST['perusahaan'] ?? ''),
            'isi'       => trim($_POST['isi'] ?? ''),
            'rating'    => (int)($_POST['rating'] ?? 5),
            'urutan'    => (int)($_POST['urutan'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        
        // Handle photo upload
        if (!empty($_FILES['foto']['name'])) {
            $upl_path = upload_image($_FILES['foto'], 'testimonial');
            if ($upl_path) $data['foto'] = $upl_path;
            else set_flash('error', 'Upload foto gagal. Cek ukuran/format file.');
        }
        
        // Column-aware: gracefully omit 'perusahaan' if migration hasn't run
        $has_perusahaan = in_array('perusahaan', $db->getColumns('testimonial'));
        
        if ($_POST['action'] === 'create') {
            if ($has_perusahaan) {
                $sql = "INSERT INTO testimonial (nama, jabatan, perusahaan, isi, rating, foto, urutan, is_active) VALUES (?,?,?,?,?,?,?,?)";
                $db->execute($sql, [$data['nama'], $data['jabatan'], $data['perusahaan'], $data['isi'], $data['rating'], $data['foto'] ?? null, $data['urutan'], $data['is_active']]);
            } else {
                $sql = "INSERT INTO testimonial (nama, jabatan, isi, rating, foto, urutan, is_active) VALUES (?,?,?,?,?,?,?)";
                $db->execute($sql, [$data['nama'], $data['jabatan'], $data['isi'], $data['rating'], $data['foto'] ?? null, $data['urutan'], $data['is_active']]);
            }
            set_flash('success', 'Testimoni berhasil ditambahkan.');
        } elseif ($_POST['action'] === 'update' && $id > 0) {
            if ($has_perusahaan) {
                $set = "nama=?, jabatan=?, perusahaan=?, isi=?, rating=?, urutan=?, is_active=?";
                $params = [$data['nama'], $data['jabatan'], $data['perusahaan'], $data['isi'], $data['rating'], $data['urutan'], $data['is_active']];
            } else {
                $set = "nama=?, jabatan=?, isi=?, rating=?, urutan=?, is_active=?";
                $params = [$data['nama'], $data['jabatan'], $data['isi'], $data['rating'], $data['urutan'], $data['is_active']];
            }
            if (isset($data['foto'])) { $set .= ", foto=?"; $params[] = $data['foto']; }
            $params[] = $id;
            $db->execute("UPDATE testimonial SET $set WHERE id = ?", $params);
            set_flash('success', 'Testimoni berhasil diupdate.');
        } elseif ($_POST['action'] === 'delete' && $id > 0) {
            $db->execute("DELETE FROM testimonial WHERE id = ?", [$id]);
            set_flash('success', 'Testimoni berhasil dihapus.');
        }
        redirect(admin_url('?page=testimonial'));
    }
}

$items = $db->fetchAll("SELECT * FROM testimonial ORDER BY urutan ASC, id DESC");
$edit_item = null;
if ($action === 'edit' && $id > 0) {
    $edit_item = $db->fetchOne("SELECT * FROM testimonial WHERE id = ?", [$id]);
}
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1>💬 Testimoni Klien</h1>
    <div class="page-header-sub">Kelola testimoni yang tampil di halaman home</div>
  </div>
  <?php if ($action === 'list'): ?>
  <div class="page-actions">
    <a href="<?= admin_url('?page=testimonial&action=create') ?>" class="btn btn-primary">+ Tambah Testimoni</a>
  </div>
  <?php endif; ?>
</div>

<?php if ($action === 'list'):
  $csrf_ts = generate_csrf();
?>
<form method="POST" class="card" style="margin-bottom:20px">
  <input type="hidden" name="csrf_token" value="<?= $csrf_ts ?>">
  <input type="hidden" name="_action" value="carousel_settings">
  <div class="card-header"><div>
    <div class="card-title">🎠 Pengaturan Tampilan Carousel Testimoni</div>
    <div class="card-subtitle">Auto-slide, jumlah kolom, dan tampil di halaman layanan</div>
  </div></div>
  <div class="card-body">
    <div class="form-row" style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px">
      <div class="form-group"><label>Maksimum Tampil</label>
        <input type="number" name="testimonial_limit_home" class="form-control" min="1" max="50" value="<?= htmlspecialchars(get_setting('testimonial_limit_home','50')) ?>">
        <div class="form-help">Jumlah testimoni yang dimuat ke carousel</div>
      </div>
      <div class="form-group"><label>Kolom Desktop</label>
        <input type="number" name="testimonial_cols_desktop" class="form-control" min="1" max="4" value="<?= htmlspecialchars(get_setting('testimonial_cols_desktop','3')) ?>">
      </div>
      <div class="form-group"><label>Kolom Tablet</label>
        <input type="number" name="testimonial_cols_tablet" class="form-control" min="1" max="3" value="<?= htmlspecialchars(get_setting('testimonial_cols_tablet','2')) ?>">
      </div>
      <div class="form-group"><label>Kolom Mobile</label>
        <input type="number" name="testimonial_cols_mobile" class="form-control" min="1" max="2" value="<?= htmlspecialchars(get_setting('testimonial_cols_mobile','1')) ?>">
      </div>
    </div>
    <div class="form-row" style="display:grid;grid-template-columns:1fr;gap:14px;margin-top:12px">
      <div class="form-group"><label>Kecepatan Auto-Slide</label>
        <select name="testimonial_speed" class="form-control">
          <?php $sp = get_setting('testimonial_speed','6'); ?>
          <option value="3" <?= $sp==='3'?'selected':'' ?>>Cepat (3 detik per slide)</option>
          <option value="5" <?= $sp==='5'?'selected':'' ?>>Sedang (5 detik per slide)</option>
          <option value="6" <?= $sp==='6'?'selected':'' ?>>Normal (6 detik per slide)</option>
          <option value="8" <?= $sp==='8'?'selected':'' ?>>Lambat (8 detik per slide)</option>
          <option value="12" <?= $sp==='12'?'selected':'' ?>>Sangat lambat (12 detik)</option>
        </select>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-top:12px">
      <label class="checkbox-label">
        <input type="checkbox" name="testimonial_autoplay" value="1" <?= get_setting('testimonial_autoplay','1')==='1'?'checked':'' ?>>
        Autoplay (auto-slide otomatis)
      </label>
      <label class="checkbox-label">
        <input type="checkbox" name="testimonial_loop" value="1" <?= get_setting('testimonial_loop','1')==='1'?'checked':'' ?>>
        Loop infinite
      </label>
      <label class="checkbox-label">
        <input type="checkbox" name="testimonial_show_dots" value="1" <?= get_setting('testimonial_show_dots','1')==='1'?'checked':'' ?>>
        Tampilkan pagination dots
      </label>
      <label class="checkbox-label">
        <input type="checkbox" name="testimonial_show_nav" value="1" <?= get_setting('testimonial_show_nav','0')==='1'?'checked':'' ?>>
        Tampilkan navigation arrows
      </label>
      <label class="checkbox-label" style="grid-column:span 2;border-top:1px solid var(--border);padding-top:12px;margin-top:4px">
        <input type="checkbox" name="testimonial_show_services" value="1" <?= get_setting('testimonial_show_services','0')==='1'?'checked':'' ?>>
        <strong>Tampilkan testimoni di semua halaman layanan</strong> (untuk meningkatkan trust & conversion)
      </label>
    </div>
    <div style="text-align:right;margin-top:14px">
      <button type="submit" class="btn btn-primary">💾 Simpan Pengaturan Carousel</button>
    </div>
  </div>
</form>
<?php endif; ?>

<?php if ($action === 'create' || $action === 'edit'): ?>
  <div class="card">
    <div class="card-header">
      <div class="card-title"><?= $action === 'create' ? 'Tambah' : 'Edit' ?> Testimoni</div>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
      <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
      
      <div class="form-row">
        <div class="form-group">
          <label>Nama *</label>
          <input type="text" name="nama" required value="<?= htmlspecialchars($edit_item['nama'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Jabatan</label>
          <input type="text" name="jabatan" placeholder="CEO, Owner, dll" value="<?= htmlspecialchars($edit_item['jabatan'] ?? '') ?>">
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label>Perusahaan</label>
          <input type="text" name="perusahaan" value="<?= htmlspecialchars($edit_item['perusahaan'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Rating (1-5)</label>
          <input type="number" name="rating" min="1" max="5" value="<?= $edit_item['rating'] ?? 5 ?>">
        </div>
      </div>
      
      <div class="form-group">
        <label>Isi Testimoni *</label>
        <textarea name="isi" rows="4" required class="wysiwyg"><?= htmlspecialchars($edit_item['isi'] ?? '') ?></textarea>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label>Foto Klien</label>
          <?php if (!empty($edit_item['foto'])): ?>
            <div class="img-upload-row" style="margin-bottom:8px">
              <div class="img-preview"><img src="<?= uploads_url($edit_item['foto']) ?>" alt=""></div>
              <span class="text-muted" style="font-size:12px">Foto saat ini</span>
            </div>
          <?php endif; ?>
          <input type="file" name="foto" accept="image/*">
          <div class="form-hint">JPG/PNG/WebP, max 2MB</div>
        </div>
        <div class="form-group">
          <label>Urutan Tampilan</label>
          <input type="number" name="urutan" value="<?= $edit_item['urutan'] ?? 0 ?>">
        </div>
      </div>
      
      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox" name="is_active" <?= ($edit_item['is_active'] ?? 1) ? 'checked' : '' ?>>
          Tampilkan di website
        </label>
      </div>
      
      <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border)">
        <a href="<?= admin_url('?page=testimonial') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">💾 Simpan</button>
      </div>
    </form>
  </div>

<?php else: ?>

  <?php if (empty($items)): ?>
    <div class="card">
      <div class="empty-state">
        <div class="empty-state-icon">💬</div>
        <div>Belum ada testimoni.</div>
        <a href="<?= admin_url('?page=testimonial&action=create') ?>" class="btn btn-primary mt-2">+ Tambah Testimoni Pertama</a>
      </div>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:60px">Foto</th>
            <th>Nama / Jabatan</th>
            <th>Testimoni</th>
            <th style="width:80px">Rating</th>
            <th style="width:80px">Status</th>
            <th style="width:120px">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $t): ?>
          <tr>
            <td>
              <?php if ($t['foto']): ?>
                <div class="img-preview" style="width:40px;height:40px"><img src="<?= uploads_url($t['foto']) ?>"></div>
              <?php else: ?>
                <div class="user-avatar"><?= strtoupper(substr($t['nama'], 0, 1)) ?></div>
              <?php endif; ?>
            </td>
            <td>
              <div style="font-weight:600"><?= htmlspecialchars($t['nama']) ?></div>
              <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars($t['jabatan'] ?? '') ?><?= !empty($t['perusahaan']) ? ' • '.htmlspecialchars($t['perusahaan']) : '' ?></div>
            </td>
            <td style="max-width:400px"><?= excerpt(htmlspecialchars($t['isi']), 100) ?></td>
            <td><?= str_repeat('⭐', (int)$t['rating']) ?></td>
            <td><?= $t['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
            <td>
              <div class="table-actions">
                <a href="<?= admin_url('?page=testimonial&action=edit&id='.$t['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
                <form method="POST" style="display:inline" onsubmit="return confirm('Hapus testimoni ini?')">
                  <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $t['id'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm" formaction="<?= admin_url('?page=testimonial&action=delete&id='.$t['id']) ?>">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
<?php endif; ?>
