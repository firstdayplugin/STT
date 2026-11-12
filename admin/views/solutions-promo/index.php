<?php
/**
 * Admin — Solutions Promo. Drives the "News & Promotions" spotlight below the
 * /solutions hero (solutions_promo table). One row = one promo slide: photo +
 * date + title + blurb + CTA, language-aware via content_i18n.
 */
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
        redirect(admin_url('?page=solutions-promo'));
    }
    $act = $_POST['action'] ?? '';

    if ($act === 'delete' && $id > 0) {
        $db->execute("DELETE FROM solutions_promo WHERE id = ?", [$id]);
        log_activity('delete', 'Hapus solutions promo #' . $id);
        set_flash('success', 'Promo dihapus.');
        redirect(admin_url('?page=solutions-promo'));
    }

    $judul = trim($_POST['judul'] ?? '');
    if ($judul === '') {
        set_flash('error', 'Judul wajib diisi.');
        redirect(admin_url('?page=solutions-promo&action=' . ($act === 'update' ? 'edit&id=' . $id : 'create')));
    }

    $data = [
        'judul'     => $judul,
        'tanggal'   => trim($_POST['tanggal'] ?? ''),
        'blurb'     => trim($_POST['blurb'] ?? ''),
        'cta_text'  => trim($_POST['cta_text'] ?? ''),
        'url'       => trim($_POST['url'] ?? ''),
        'urutan'    => (int)($_POST['urutan'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    // image: [value, should_set]
    $gambar = null; $set_gambar = false;
    if (!empty($_FILES['gambar']['name'])) {
        $up = upload_image($_FILES['gambar'], 'solutions/promo');
        if ($up) { $gambar = $up; $set_gambar = true; }
        else set_flash('error', 'Upload gambar gagal.');
    } elseif (!empty($_POST['hapus_gambar'])) { $set_gambar = true; }

    $promo_id = 0;
    if ($act === 'create') {
        $db->execute(
            "INSERT INTO solutions_promo (gambar,judul,tanggal,blurb,cta_text,url,urutan,is_active) VALUES (?,?,?,?,?,?,?,?)",
            [$gambar, $data['judul'], $data['tanggal'], $data['blurb'], $data['cta_text'], $data['url'], $data['urutan'], $data['is_active']]
        );
        $promo_id = (int)$db->lastInsertId();
        save_i18n_fields('solutions_promo', $promo_id, $_POST);
        log_activity('create', 'Tambah solutions promo: ' . strip_tags($judul));
        set_flash('success', 'Promo ditambahkan.');
    } elseif ($act === 'update' && $id > 0) {
        $promo_id = $id;
        $set = "judul=?,tanggal=?,blurb=?,cta_text=?,url=?,urutan=?,is_active=?";
        $params = [$data['judul'], $data['tanggal'], $data['blurb'], $data['cta_text'], $data['url'], $data['urutan'], $data['is_active']];
        if ($set_gambar) { $set .= ",gambar=?"; $params[] = $gambar; }
        $params[] = $id;
        $db->execute("UPDATE solutions_promo SET $set WHERE id=?", $params);
        save_i18n_fields('solutions_promo', $id, $_POST);
        log_activity('update', 'Update solutions promo: ' . strip_tags($judul));
        set_flash('success', 'Promo diperbarui.');
    }
    redirect(admin_url('?page=solutions-promo'));
}

$items = $db->fetchAll("SELECT * FROM solutions_promo ORDER BY urutan, id");
$edit_item = ($action === 'edit' && $id > 0) ? $db->fetchOne("SELECT * FROM solutions_promo WHERE id=?", [$id]) : null;
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1><?= icon('image', 18) ?> Solutions Promo</h1>
    <div class="page-header-sub">Banner promo (spotlight "News &amp; Promotions") di bawah hero <code>/solutions</code>. Beberapa slide berputar otomatis.</div>
  </div>
  <?php if ($action === 'list'): ?>
  <div class="page-actions">
    <a href="<?= url('solutions') ?>" target="_blank" class="btn btn-secondary"><?= icon('eye', 16) ?> Lihat Halaman</a>
    <a href="<?= admin_url('?page=solutions-promo&action=create') ?>" class="btn btn-primary"><?= icon('plus', 16) ?> Tambah Promo</a>
  </div>
  <?php endif; ?>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>
  <form method="POST" enctype="multipart/form-data" class="card">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
    <div class="card-header"><div class="card-title"><?= $action === 'create' ? 'Tambah' : 'Edit' ?> Promo</div></div>
    <div class="card-body">
      <div class="form-group"><label>Judul *</label>
        <input type="text" name="judul" required value="<?= htmlspecialchars($edit_item['judul'] ?? '') ?>" placeholder="Coming Soon: STT × [MeshDefend]">
        <div class="form-hint">Bungkus kata dengan <code>[kurung siku]</code> untuk memberi aksen gradien biru&ndash;teal, mis. <code>STT × [MeshDefend]</code>.</div></div>
      <div class="form-row">
        <div class="form-group"><label>Tanggal / label kecil</label>
          <input type="text" name="tanggal" value="<?= htmlspecialchars($edit_item['tanggal'] ?? '') ?>" placeholder="July 15, 2026  ·  atau  Webinar · Dec 2026"></div>
        <div class="form-group"><label>Urutan</label>
          <input type="number" name="urutan" value="<?= (int)($edit_item['urutan'] ?? 0) ?>"></div>
      </div>
      <div class="form-group"><label>Blurb (deskripsi singkat)</label>
        <textarea name="blurb" rows="3" class="no-wysiwyg"><?= htmlspecialchars($edit_item['blurb'] ?? '') ?></textarea></div>
      <div class="form-row">
        <div class="form-group"><label>Teks tombol (CTA)</label>
          <input type="text" name="cta_text" value="<?= htmlspecialchars($edit_item['cta_text'] ?? '') ?>" placeholder="Read More"></div>
        <div class="form-group"><label>Link tujuan</label>
          <input type="text" name="url" value="<?= htmlspecialchars($edit_item['url'] ?? '') ?>" placeholder="# atau https://… atau blog/judul-artikel"></div>
      </div>
      <div class="form-group"><label>Foto / visual (background)</label>
        <?php if (!empty($edit_item['gambar'])): ?>
          <div class="img-upload-row" style="margin-bottom:8px">
            <div class="img-preview" style="width:200px;height:72px"><img src="<?= uploads_url($edit_item['gambar']) ?>" alt="" style="object-fit:cover"></div>
            <label class="checkbox-label" style="font-size:12px"><input type="checkbox" name="hapus_gambar" value="1"> Hapus</label>
          </div>
        <?php endif; ?>
        <input type="file" name="gambar" accept="image/*">
        <div class="form-hint">Foto landscape (ideal ±1600×580). Teks di-overlay otomatis + lapisan gelap biru, jadi foto apa pun tetap terbaca.</div></div>
      <div class="form-group"><label class="checkbox-label">
        <input type="checkbox" name="is_active" <?= ($edit_item['is_active'] ?? 1) ? 'checked' : '' ?>> Tampilkan</label></div>
      <?php if ($action === 'edit') echo i18n_fields_editor('solutions_promo', (int)$edit_item['id'], [
        'judul'    => 'Judul',
        'tanggal'  => 'Tanggal / label kecil',
        'blurb'    => ['label' => 'Blurb', 'type' => 'textarea'],
        'cta_text' => 'Teks tombol (CTA)',
      ]); ?>
      <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border)">
        <a href="<?= admin_url('?page=solutions-promo') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan</button>
      </div>
    </div>
  </form>

<?php elseif (empty($items)): ?>
  <div class="card"><div class="empty-state">
    <div class="empty-state-icon"><?= icon('image', 40) ?></div>
    <div>Belum ada promo. Jalankan <code>database/migration_solutions_promo.sql</code> untuk seed awal, atau tambah manual.</div>
    <a href="<?= admin_url('?page=solutions-promo&action=create') ?>" class="btn btn-primary mt-2"><?= icon('plus', 16) ?> Tambah Promo</a>
  </div></div>
<?php else: ?>
  <div class="table-wrap"><table>
    <thead><tr><th style="width:180px">Foto</th><th>Judul</th><th style="width:140px">Tanggal</th><th style="width:70px">Urutan</th><th style="width:80px">Status</th><th style="width:130px">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($items as $it): ?>
      <tr>
        <td><?php if (!empty($it['gambar'])): ?><div class="img-preview" style="width:160px;height:58px"><img src="<?= uploads_url($it['gambar']) ?>" alt="" style="object-fit:cover"></div>
          <?php else: ?><span style="color:var(--primary)"><?= icon('image', 22) ?></span><?php endif; ?></td>
        <td><div style="font-weight:600"><?= htmlspecialchars(str_replace(['[', ']'], '', strip_tags($it['judul']))) ?></div>
          <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars(mb_substr(strip_tags($it['blurb'] ?? ''), 0, 70)) ?></div></td>
        <td style="font-size:12px"><?= htmlspecialchars($it['tanggal'] ?? '') ?></td>
        <td><?= (int)$it['urutan'] ?></td>
        <td><?= $it['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
        <td><div class="table-actions">
          <a href="<?= admin_url('?page=solutions-promo&action=edit&id=' . $it['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Hapus promo ini?')">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>"><input type="hidden" name="action" value="delete">
            <button type="submit" class="btn btn-danger btn-sm" formaction="<?= admin_url('?page=solutions-promo&action=delete&id=' . $it['id']) ?>"><?= icon('trash', 15) ?></button>
          </form>
        </div></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
<?php endif; ?>
