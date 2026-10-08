<?php
/**
 * Admin — Solution Pillars (4 pilar). Drives the Solutions landing (/solutions)
 * and the tabs on every Industry detail page (§B).
 */
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
        redirect(admin_url('?page=solusi-pilar'));
    }
    $act = $_POST['action'] ?? '';

    if ($act === 'delete' && $id > 0) {
        $db->execute("DELETE FROM solusi_pilar WHERE id = ?", [$id]);
        $db->execute("DELETE FROM industri_pilar WHERE pilar_id = ?", [$id]); // clear matrix cells
        log_activity('delete', 'Hapus pilar solusi #' . $id);
        set_flash('success', 'Pilar dihapus.');
        redirect(admin_url('?page=solusi-pilar'));
    }

    $nama = trim($_POST['nama'] ?? '');
    if ($nama === '') {
        set_flash('error', 'Nama pilar wajib diisi.');
        redirect(admin_url('?page=solusi-pilar&action=' . ($act === 'update' ? 'edit&id=' . $id : 'create')));
    }
    $slug = trim($_POST['slug'] ?? '');
    if ($slug === '') $slug = slug($nama);
    // ensure unique slug (excluding self)
    $base = $slug; $n = 1;
    while ($db->fetchOne("SELECT id FROM solusi_pilar WHERE slug = ? AND id <> ?", [$slug, $id])) { $slug = $base . '-' . (++$n); }

    $data = [
        'nama'      => $nama,
        'slug'      => $slug,
        'deskripsi' => trim($_POST['deskripsi'] ?? ''),
        'icon'      => trim($_POST['icon'] ?? ''),
        'url'       => trim($_POST['url'] ?? ''),
        'urutan'    => (int)($_POST['urutan'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
    $gambar = null; $set_gambar = false;
    if (!empty($_FILES['gambar']['name'])) {
        $up = upload_image($_FILES['gambar'], 'solusi-pilar');
        if ($up) { $gambar = $up; $set_gambar = true; } else set_flash('error', 'Upload gambar gagal.');
    } elseif (!empty($_POST['hapus_gambar'])) { $gambar = null; $set_gambar = true; }

    if ($act === 'create') {
        $db->execute(
            "INSERT INTO solusi_pilar (nama,slug,deskripsi,icon,gambar,url,urutan,is_active) VALUES (?,?,?,?,?,?,?,?)",
            [$data['nama'],$data['slug'],$data['deskripsi'],$data['icon'],$gambar,$data['url'],$data['urutan'],$data['is_active']]
        );
        save_i18n_fields('solusi_pilar', (int)$db->lastInsertId(), $_POST);
        log_activity('create', 'Tambah pilar: ' . $nama);
        set_flash('success', 'Pilar ditambahkan.');
    } elseif ($act === 'update' && $id > 0) {
        $set = "nama=?,slug=?,deskripsi=?,icon=?,url=?,urutan=?,is_active=?";
        $params = [$data['nama'],$data['slug'],$data['deskripsi'],$data['icon'],$data['url'],$data['urutan'],$data['is_active']];
        if ($set_gambar) { $set .= ",gambar=?"; $params[] = $gambar; }
        $params[] = $id;
        $db->execute("UPDATE solusi_pilar SET $set WHERE id=?", $params);
        save_i18n_fields('solusi_pilar', $id, $_POST);
        log_activity('update', 'Update pilar: ' . $nama);
        set_flash('success', 'Pilar diperbarui.');
    }
    redirect(admin_url('?page=solusi-pilar'));
}

$items = $db->fetchAll("SELECT * FROM solusi_pilar ORDER BY urutan, id");
$edit_item = ($action === 'edit' && $id > 0) ? $db->fetchOne("SELECT * FROM solusi_pilar WHERE id=?", [$id]) : null;
$is_asset = fn($s) => (bool)preg_match('#[/.]#', (string)$s);
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1><?= icon('layers', 18) ?> Pilar Solusi</h1>
    <div class="page-header-sub">Empat pilar yang tampil di halaman Solutions & jadi tab di tiap halaman Industri.</div>
  </div>
  <?php if ($action === 'list'): ?>
  <div class="page-actions">
    <a href="<?= url('solutions') ?>" target="_blank" class="btn btn-secondary"><?= icon('eye', 16) ?> Lihat Halaman</a>
    <a href="<?= admin_url('?page=solusi-pilar&action=create') ?>" class="btn btn-primary"><?= icon('plus', 16) ?> Tambah Pilar</a>
  </div>
  <?php endif; ?>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>
  <form method="POST" enctype="multipart/form-data" class="card">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
    <div class="card-header"><div class="card-title"><?= $action === 'create' ? 'Tambah' : 'Edit' ?> Pilar</div></div>
    <div class="card-body">
      <div class="form-row">
        <div class="form-group"><label>Nama Pilar *</label>
          <input type="text" name="nama" required value="<?= htmlspecialchars($edit_item['nama'] ?? '') ?>" placeholder="Modernize Infrastructure"></div>
        <div class="form-group"><label>Slug</label>
          <input type="text" name="slug" value="<?= htmlspecialchars($edit_item['slug'] ?? '') ?>" placeholder="otomatis dari nama">
          <div class="form-hint">Dipakai di URL. Kosongkan untuk otomatis.</div></div>
      </div>
      <div class="form-group"><label>Deskripsi</label>
        <textarea name="deskripsi" rows="3" class="no-wysiwyg"><?= htmlspecialchars($edit_item['deskripsi'] ?? '') ?></textarea></div>
      <div class="form-row">
        <div class="form-group"><label>Ikon (nama Lucide)</label>
          <input type="text" name="icon" value="<?= htmlspecialchars($edit_item['icon'] ?? '') ?>" placeholder="layers / sparkles / lock / settings">
          <div class="form-hint">Nama ikon Lucide, atau upload gambar di bawah (gambar menang).</div></div>
        <div class="form-group"><label>Urutan</label>
          <input type="number" name="urutan" value="<?= (int)($edit_item['urutan'] ?? 0) ?>"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Gambar Ikon (opsional)</label>
          <?php if (!empty($edit_item['gambar'])): ?>
            <div class="img-upload-row" style="margin-bottom:8px">
              <div class="img-preview" style="width:56px;height:56px"><img src="<?= uploads_url($edit_item['gambar']) ?>" alt=""></div>
              <label class="checkbox-label" style="font-size:12px"><input type="checkbox" name="hapus_gambar" value="1"> Hapus</label>
            </div>
          <?php endif; ?>
          <input type="file" name="gambar" accept="image/*"></div>
        <div class="form-group"><label>Link (opsional)</label>
          <input type="text" name="url" value="<?= htmlspecialchars($edit_item['url'] ?? '') ?>" placeholder="solutions/modernize-infrastructure"></div>
      </div>
      <div class="form-group"><label class="checkbox-label">
        <input type="checkbox" name="is_active" <?= ($edit_item['is_active'] ?? 1) ? 'checked' : '' ?>> Tampilkan</label></div>
      <?php if ($action === 'edit') echo i18n_fields_editor('solusi_pilar', (int)$edit_item['id'], [
        'nama'      => 'Nama Pilar',
        'deskripsi' => ['label' => 'Deskripsi', 'type' => 'textarea'],
      ]); ?>
      <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border)">
        <a href="<?= admin_url('?page=solusi-pilar') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan</button>
      </div>
    </div>
  </form>

<?php elseif (empty($items)): ?>
  <div class="card"><div class="empty-state">
    <div class="empty-state-icon"><?= icon('layers', 40) ?></div>
    <div>Belum ada pilar.</div>
    <a href="<?= admin_url('?page=solusi-pilar&action=create') ?>" class="btn btn-primary mt-2"><?= icon('plus', 16) ?> Tambah Pilar</a>
  </div></div>
<?php else: ?>
  <div class="table-wrap"><table>
    <thead><tr><th style="width:56px">Ikon</th><th>Nama</th><th>Slug</th><th style="width:70px">Urutan</th><th style="width:80px">Status</th><th style="width:130px">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($items as $it): ?>
      <tr>
        <td><?php if (!empty($it['gambar'])): ?><div class="img-preview" style="width:40px;height:40px"><img src="<?= uploads_url($it['gambar']) ?>" alt=""></div>
          <?php else: ?><span style="color:var(--primary)"><?= icon($is_asset($it['icon']) ? 'layers' : ($it['icon'] ?: 'layers'), 22) ?></span><?php endif; ?></td>
        <td><div style="font-weight:600"><?= htmlspecialchars($it['nama']) ?></div>
          <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars(mb_substr(strip_tags($it['deskripsi'] ?? ''), 0, 60)) ?></div></td>
        <td><span style="font-family:monospace;font-size:11px"><?= htmlspecialchars($it['slug']) ?></span></td>
        <td><?= (int)$it['urutan'] ?></td>
        <td><?= $it['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
        <td><div class="table-actions">
          <a href="<?= admin_url('?page=solusi-pilar&action=edit&id=' . $it['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Hapus pilar ini? Konten matriks industri untuk pilar ini juga terhapus.')">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>"><input type="hidden" name="action" value="delete">
            <button type="submit" class="btn btn-danger btn-sm" formaction="<?= admin_url('?page=solusi-pilar&action=delete&id=' . $it['id']) ?>"><?= icon('trash', 15) ?></button>
          </form>
        </div></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
<?php endif; ?>
