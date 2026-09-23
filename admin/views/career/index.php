<?php
/**
 * Admin — Career / Jobs CRUD (module: career). Public routes /career + /career/[slug].
 */
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error', 'Token tidak valid.'); redirect(admin_url('?page=career')); }
    $act = $_POST['action'] ?? '';

    if ($act === 'delete' && $id > 0) {
        $db->execute("DELETE FROM career WHERE id = ?", [$id]);
        log_activity('delete', 'Hapus lowongan #' . $id);
        set_flash('success', 'Lowongan dihapus.');
        redirect(admin_url('?page=career'));
    }

    $judul = trim($_POST['judul'] ?? '');
    if ($judul === '') { set_flash('error', 'Judul wajib diisi.'); redirect(admin_url('?page=career&action=' . ($act === 'update' ? 'edit&id=' . $id : 'create'))); }
    $slug = trim($_POST['slug'] ?? ''); if ($slug === '') $slug = slug($judul);
    $base = $slug; $n = 1;
    while ($db->fetchOne("SELECT id FROM career WHERE slug=? AND id<>?", [$slug, $id])) { $slug = $base . '-' . (++$n); }

    $data = [
        'judul' => $judul, 'slug' => $slug,
        'role' => trim($_POST['role'] ?? ''), 'lokasi' => trim($_POST['lokasi'] ?? ''),
        'tipe' => trim($_POST['tipe'] ?? 'Full-time'), 'jenjang' => trim($_POST['jenjang'] ?? ''),
        'pengalaman' => trim($_POST['pengalaman'] ?? ''), 'gaji' => trim($_POST['gaji'] ?? ''),
        'deskripsi' => trim($_POST['deskripsi'] ?? ''),
        'responsibilities' => trim($_POST['responsibilities'] ?? ''), 'requirements' => trim($_POST['requirements'] ?? ''),
        'deadline' => trim($_POST['deadline'] ?? '') ?: null,
        'meta_title' => trim($_POST['meta_title'] ?? ''), 'meta_description' => trim($_POST['meta_description'] ?? ''),
        'urutan' => (int)($_POST['urutan'] ?? 0), 'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
    $cols = array_keys($data);
    if ($act === 'create') {
        $ph = implode(',', array_fill(0, count($cols), '?'));
        $db->execute("INSERT INTO career (" . implode(',', $cols) . ") VALUES ($ph)", array_values($data));
        $new_id = (int) $db->lastInsertId();
        save_i18n_fields('career', $new_id, $_POST);
        log_activity('create', 'Tambah lowongan: ' . $judul);
        set_flash('success', 'Lowongan ditambahkan.');
    } elseif ($act === 'update' && $id > 0) {
        $set = implode(',', array_map(fn($c) => "$c=?", $cols));
        $db->execute("UPDATE career SET $set WHERE id=?", [...array_values($data), $id]);
        save_i18n_fields('career', $id, $_POST);
        log_activity('update', 'Update lowongan: ' . $judul);
        set_flash('success', 'Lowongan diperbarui.');
    }
    redirect(admin_url('?page=career'));
}

$items = $db->fetchAll("SELECT c.*, (SELECT COUNT(*) FROM job_applications a WHERE a.career_id=c.id) apps FROM career c ORDER BY c.urutan, c.created_at DESC");
$edit = ($action === 'edit' && $id > 0) ? $db->fetchOne("SELECT * FROM career WHERE id=?", [$id]) : null;
$csrf = generate_csrf();
$fmt = fn($d) => $d ? date('d M Y', strtotime($d)) : '—';
?>

<div class="page-header">
  <div>
    <h1><?= icon('briefcase', 18) ?> Career / Lowongan</h1>
    <div class="page-header-sub">Kelola lowongan kerja yang tampil di halaman /career.</div>
  </div>
  <?php if ($action === 'list'): ?>
  <div class="page-actions">
    <a href="<?= admin_url('?page=career-lamaran') ?>" class="btn btn-secondary"><?= icon('users', 16) ?> Lamaran Masuk</a>
    <a href="<?= url('career') ?>" target="_blank" class="btn btn-secondary"><?= icon('eye', 16) ?> Lihat Halaman</a>
    <a href="<?= admin_url('?page=career&action=create') ?>" class="btn btn-primary"><?= icon('plus', 16) ?> Tambah Lowongan</a>
  </div>
  <?php endif; ?>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>
  <form method="POST" class="card">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
    <div class="card-header"><div class="card-title"><?= $action === 'create' ? 'Tambah' : 'Edit' ?> Lowongan</div></div>
    <div class="card-body">
      <div class="form-row">
        <div class="form-group"><label>Judul Posisi *</label>
          <input type="text" name="judul" required value="<?= htmlspecialchars($edit['judul'] ?? '') ?>" placeholder="Backend Engineer (PHP)"></div>
        <div class="form-group"><label>Slug</label>
          <input type="text" name="slug" value="<?= htmlspecialchars($edit['slug'] ?? '') ?>" placeholder="otomatis dari judul"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Job Role (filter)</label>
          <input type="text" name="role" value="<?= htmlspecialchars($edit['role'] ?? '') ?>" placeholder="Software Engineering & Developer"></div>
        <div class="form-group"><label>Lokasi (filter)</label>
          <input type="text" name="lokasi" value="<?= htmlspecialchars($edit['lokasi'] ?? '') ?>" placeholder="Jakarta"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Tipe</label>
          <input type="text" name="tipe" value="<?= htmlspecialchars($edit['tipe'] ?? 'Full-time') ?>" placeholder="Full-time / Contract / Internship"></div>
        <div class="form-group"><label>Jenjang Pendidikan</label>
          <input type="text" name="jenjang" value="<?= htmlspecialchars($edit['jenjang'] ?? '') ?>" placeholder="Bachelor/S1"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Pengalaman</label>
          <input type="text" name="pengalaman" value="<?= htmlspecialchars($edit['pengalaman'] ?? '') ?>" placeholder="Experienced / Fresh Graduate"></div>
        <div class="form-group"><label>Gaji (opsional)</label>
          <input type="text" name="gaji" value="<?= htmlspecialchars($edit['gaji'] ?? '') ?>" placeholder="Negotiable"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Deadline</label>
          <input type="date" name="deadline" value="<?= htmlspecialchars($edit['deadline'] ?? '') ?>"></div>
        <div class="form-group"><label>Urutan</label>
          <input type="number" name="urutan" value="<?= (int)($edit['urutan'] ?? 0) ?>"></div>
      </div>
      <div class="form-group"><label>Deskripsi Singkat</label>
        <textarea name="deskripsi" rows="2" class="no-wysiwyg"><?= htmlspecialchars($edit['deskripsi'] ?? '') ?></textarea></div>
      <div class="form-group"><label>Responsibilities</label>
        <textarea name="responsibilities" class="wysiwyg" rows="5"><?= htmlspecialchars($edit['responsibilities'] ?? '') ?></textarea></div>
      <div class="form-group"><label>Requirements</label>
        <textarea name="requirements" class="wysiwyg" rows="5"><?= htmlspecialchars($edit['requirements'] ?? '') ?></textarea></div>
      <div class="form-row">
        <div class="form-group"><label>Meta Title (SEO)</label>
          <input type="text" name="meta_title" class="no-wysiwyg" value="<?= htmlspecialchars($edit['meta_title'] ?? '') ?>"></div>
        <div class="form-group"><label>Meta Description (SEO)</label>
          <input type="text" name="meta_description" class="no-wysiwyg" value="<?= htmlspecialchars($edit['meta_description'] ?? '') ?>"></div>
      </div>
      <div class="form-group"><label class="checkbox-label">
        <input type="checkbox" name="is_active" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>> Aktif (tampil di website)</label></div>

      <?php if ($action === 'edit'): ?>
      <?= i18n_fields_editor('career', (int)$edit['id'], [
        'judul'            => 'Judul Posisi',
        'deskripsi'        => ['label' => 'Deskripsi Singkat', 'type' => 'textarea'],
        'responsibilities' => ['label' => 'Responsibilities', 'type' => 'wysiwyg'],
        'requirements'     => ['label' => 'Requirements', 'type' => 'wysiwyg'],
      ]) ?>
      <?php else: ?>
        <?php if (function_exists('is_multilang') && is_multilang()): ?><div class="form-hint" style="margin-top:12px"><?= icon('info', 13) ?> Simpan dulu, lalu terjemahan per bahasa muncul di sini saat edit.</div><?php endif; ?>
      <?php endif; ?>

      <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border)">
        <a href="<?= admin_url('?page=career') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan</button>
      </div>
    </div>
  </form>

<?php elseif (empty($items)): ?>
  <div class="card"><div class="empty-state">
    <div class="empty-state-icon"><?= icon('briefcase', 40) ?></div>
    <div>Belum ada lowongan.</div>
    <a href="<?= admin_url('?page=career&action=create') ?>" class="btn btn-primary mt-2"><?= icon('plus', 16) ?> Tambah Lowongan</a>
  </div></div>
<?php else: ?>
  <div class="table-wrap"><table>
    <thead><tr><th>Posisi</th><th style="width:180px">Role</th><th style="width:110px">Lokasi</th><th style="width:110px">Deadline</th><th style="width:90px">Lamaran</th><th style="width:80px">Status</th><th style="width:130px">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($items as $it): ?>
      <tr>
        <td><div style="font-weight:600"><?= htmlspecialchars($it['judul']) ?></div>
          <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars($it['tipe'] ?? '') ?><?= !empty($it['jenjang']) ? ' • ' . htmlspecialchars($it['jenjang']) : '' ?></div></td>
        <td style="font-size:12px"><?= htmlspecialchars($it['role'] ?? '') ?></td>
        <td style="font-size:12px"><?= htmlspecialchars($it['lokasi'] ?? '') ?></td>
        <td style="font-size:12px"><?= htmlspecialchars($fmt($it['deadline'])) ?></td>
        <td><a href="<?= admin_url('?page=career-lamaran&job=' . $it['id']) ?>" class="badge badge-info"><?= (int)$it['apps'] ?> lamaran</a></td>
        <td><?= $it['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
        <td><div class="table-actions">
          <a href="<?= admin_url('?page=career&action=edit&id=' . $it['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Hapus lowongan ini?')">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>"><input type="hidden" name="action" value="delete">
            <button type="submit" class="btn btn-danger btn-sm" formaction="<?= admin_url('?page=career&action=delete&id=' . $it['id']) ?>"><?= icon('trash', 15) ?></button>
          </form>
        </div></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
<?php endif; ?>
