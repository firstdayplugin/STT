<?php
/**
 * Admin — Our Industries (orbit animation cards on the Home page).
 * Feeds the `industri` table; the Home template injects it into anima.js (§14.2).
 * Each card: label, center title/subtitle, optional photo, gradient colors, link, order.
 */
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
        redirect(admin_url('?page=industri'));
    }
    $act = $_POST['action'] ?? '';

    if ($act === 'delete' && $id > 0) {
        $db->execute("DELETE FROM industri WHERE id = ?", [$id]);
        log_activity('delete', 'Hapus industri #' . $id);
        set_flash('success', 'Kartu industri dihapus.');
        redirect(admin_url('?page=industri'));
    }

    $data = [
        'label'     => trim($_POST['label'] ?? ''),
        'judul'     => trim($_POST['judul'] ?? ''),
        'subtitle'  => trim($_POST['subtitle'] ?? ''),
        'warna1'    => trim($_POST['warna1'] ?? '#1d478c') ?: '#1d478c',
        'warna2'    => trim($_POST['warna2'] ?? '#3f80e2') ?: '#3f80e2',
        'url'       => trim($_POST['url'] ?? ''),
        'urutan'    => (int)($_POST['urutan'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($data['label'] === '') {
        set_flash('error', 'Label wajib diisi.');
        redirect(admin_url('?page=industri&action=' . ($act === 'update' ? 'edit&id=' . $id : 'create')));
    }

    // Optional photo. "hapus_gambar" clears it.
    $gambar = null; $set_gambar = false;
    if (!empty($_FILES['gambar']['name'])) {
        $up = upload_image($_FILES['gambar'], 'industri');
        if ($up) { $gambar = $up; $set_gambar = true; }
        else set_flash('error', 'Upload foto gagal (cek ukuran/format).');
    } elseif (!empty($_POST['hapus_gambar'])) {
        $gambar = null; $set_gambar = true;
    }

    if ($act === 'create') {
        $db->execute(
            "INSERT INTO industri (label,judul,subtitle,gambar,warna1,warna2,url,urutan,is_active) VALUES (?,?,?,?,?,?,?,?,?)",
            [$data['label'],$data['judul'],$data['subtitle'],$gambar,$data['warna1'],$data['warna2'],$data['url'],$data['urutan'],$data['is_active']]
        );
        log_activity('create', 'Tambah industri: ' . $data['label']);
        set_flash('success', 'Kartu industri "' . $data['label'] . '" ditambahkan.');
    } elseif ($act === 'update' && $id > 0) {
        $set = "label=?,judul=?,subtitle=?,warna1=?,warna2=?,url=?,urutan=?,is_active=?";
        $params = [$data['label'],$data['judul'],$data['subtitle'],$data['warna1'],$data['warna2'],$data['url'],$data['urutan'],$data['is_active']];
        if ($set_gambar) { $set .= ",gambar=?"; $params[] = $gambar; }
        $params[] = $id;
        $db->execute("UPDATE industri SET $set WHERE id=?", $params);
        log_activity('update', 'Update industri: ' . $data['label']);
        set_flash('success', 'Kartu industri diperbarui.');
    }
    redirect(admin_url('?page=industri'));
}

$items = $db->fetchAll("SELECT * FROM industri ORDER BY urutan ASC, id ASC");
$edit_item = ($action === 'edit' && $id > 0) ? $db->fetchOne("SELECT * FROM industri WHERE id=?", [$id]) : null;
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1><?= icon('compass', 18) ?> Our Industries — Orbit</h1>
    <div class="page-header-sub">Kartu industri yang berputar (animasi orbit) di halaman Home. Bisa diisi foto per kartu.</div>
  </div>
  <?php if ($action === 'list'): ?>
  <div class="page-actions">
    <a href="<?= url('/') ?>#industries" target="_blank" class="btn btn-secondary"><?= icon('eye', 16) ?> Lihat di Home</a>
    <a href="<?= admin_url('?page=industri&action=create') ?>" class="btn btn-primary"><?= icon('plus', 16) ?> Tambah Kartu</a>
  </div>
  <?php endif; ?>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>
  <form method="POST" enctype="multipart/form-data" class="card">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
    <div class="card-header"><div class="card-title"><?= $action === 'create' ? 'Tambah' : 'Edit' ?> Kartu Industri</div></div>
    <div class="card-body">

      <div class="form-row">
        <div class="form-group">
          <label>Label Kartu *</label>
          <input type="text" name="label" required maxlength="100" value="<?= htmlspecialchars($edit_item['label'] ?? '') ?>" placeholder="Financial">
          <div class="form-hint">Teks pendek di kartu + eyebrow tengah saat di-hover.</div>
        </div>
        <div class="form-group">
          <label>Urutan</label>
          <input type="number" name="urutan" value="<?= (int)($edit_item['urutan'] ?? 0) ?>">
        </div>
      </div>

      <div class="form-group">
        <label>Judul Tengah</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($edit_item['judul'] ?? '') ?>" placeholder="Financial Services &lt;b&gt;&amp; E-Commerce&lt;/b&gt;">
        <div class="form-hint">Tampil di tengah orbit saat kartu di-hover. Boleh pakai <code>&lt;b&gt;...&lt;/b&gt;</code> untuk penekanan.</div>
      </div>

      <div class="form-group">
        <label>Sub-teks Tengah</label>
        <input type="text" name="subtitle" value="<?= htmlspecialchars($edit_item['subtitle'] ?? '') ?>" placeholder="Secure digital transactions">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Foto Kartu (opsional)</label>
          <?php if (!empty($edit_item['gambar'])): ?>
            <div class="img-upload-row" style="margin-bottom:8px">
              <div class="img-preview" style="width:80px;height:52px"><img src="<?= uploads_url($edit_item['gambar']) ?>" alt=""></div>
              <label class="checkbox-label" style="font-size:12px"><input type="checkbox" name="hapus_gambar" value="1"> Hapus foto</label>
            </div>
          <?php endif; ?>
          <input type="file" name="gambar" accept="image/*">
          <div class="form-hint">Kalau diisi, foto menggantikan gradient sebagai wajah kartu. JPG/PNG/WebP, max 5MB.</div>
        </div>
        <div class="form-group">
          <label>Link (opsional)</label>
          <input type="text" name="url" value="<?= htmlspecialchars($edit_item['url'] ?? '') ?>" placeholder="industri/financial atau https://...">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Warna Gradient 1</label>
          <div style="display:flex;gap:8px;align-items:center">
            <input type="color" value="<?= htmlspecialchars($edit_item['warna1'] ?? '#1d478c') ?>" data-sync="warna1" style="width:44px;height:38px;padding:2px">
            <input type="text" id="warna1" name="warna1" value="<?= htmlspecialchars($edit_item['warna1'] ?? '#1d478c') ?>" style="flex:1">
          </div>
          <div class="form-hint">Dipakai kalau tidak ada foto.</div>
        </div>
        <div class="form-group">
          <label>Warna Gradient 2</label>
          <div style="display:flex;gap:8px;align-items:center">
            <input type="color" value="<?= htmlspecialchars($edit_item['warna2'] ?? '#3f80e2') ?>" data-sync="warna2" style="width:44px;height:38px;padding:2px">
            <input type="text" id="warna2" name="warna2" value="<?= htmlspecialchars($edit_item['warna2'] ?? '#3f80e2') ?>" style="flex:1">
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox" name="is_active" <?= ($edit_item['is_active'] ?? 1) ? 'checked' : '' ?>> Tampilkan di website
        </label>
      </div>

      <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border)">
        <a href="<?= admin_url('?page=industri') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan</button>
      </div>
    </div>
  </form>

<?php elseif (empty($items)): ?>
  <div class="card"><div class="empty-state">
    <div class="empty-state-icon"><?= icon('compass', 40) ?></div>
    <div>Belum ada kartu industri.</div>
    <a href="<?= admin_url('?page=industri&action=create') ?>" class="btn btn-primary mt-2"><?= icon('plus', 16) ?> Tambah Kartu Pertama</a>
  </div></div>
<?php else: ?>
  <div class="table-wrap"><table>
    <thead><tr>
      <th style="width:70px">Wajah</th><th>Label / Judul</th><th style="width:120px">Warna</th>
      <th style="width:70px">Urutan</th><th style="width:80px">Status</th><th style="width:130px">Aksi</th>
    </tr></thead>
    <tbody>
    <?php foreach ($items as $it): ?>
      <tr>
        <td>
          <?php if (!empty($it['gambar'])): ?>
            <div class="img-preview" style="width:56px;height:36px"><img src="<?= uploads_url($it['gambar']) ?>" alt=""></div>
          <?php else: ?>
            <div style="width:56px;height:36px;border-radius:6px;background:linear-gradient(135deg,<?= htmlspecialchars($it['warna1']) ?>,<?= htmlspecialchars($it['warna2']) ?>)"></div>
          <?php endif; ?>
        </td>
        <td>
          <div style="font-weight:600"><?= htmlspecialchars($it['label']) ?></div>
          <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars(strip_tags($it['judul'] ?? '')) ?></div>
        </td>
        <td><span style="font-family:monospace;font-size:11px"><?= htmlspecialchars($it['warna1']) ?><br><?= htmlspecialchars($it['warna2']) ?></span></td>
        <td><?= (int)$it['urutan'] ?></td>
        <td><?= $it['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
        <td><div class="table-actions">
          <a href="<?= admin_url('?page=industri&action=edit&id=' . $it['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Hapus kartu industri ini?')">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="action" value="delete">
            <button type="submit" class="btn btn-danger btn-sm" formaction="<?= admin_url('?page=industri&action=delete&id=' . $it['id']) ?>"><?= icon('trash', 15) ?></button>
          </form>
        </div></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
<?php endif; ?>
