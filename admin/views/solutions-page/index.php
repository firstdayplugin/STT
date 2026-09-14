<?php
/**
 * Admin — Solutions (Page) sections. Drives the /solutions landing (Figma "Our Solutions").
 * One row per block: illustration, partner-logo strip, solution text, optional CTA.
 * Header copy + Coming Soon banner live under Admin → Konten Halaman → "Halaman Solutions".
 */
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
        redirect(admin_url('?page=solutions-page'));
    }
    $act = $_POST['action'] ?? '';

    if ($act === 'delete' && $id > 0) {
        $db->execute("DELETE FROM solutions_section WHERE id = ?", [$id]);
        log_activity('delete', 'Hapus solution section #' . $id);
        set_flash('success', 'Section dihapus.');
        redirect(admin_url('?page=solutions-page'));
    }

    $judul = trim($_POST['judul'] ?? '');
    if ($judul === '') {
        set_flash('error', 'Judul wajib diisi.');
        redirect(admin_url('?page=solutions-page&action=' . ($act === 'update' ? 'edit&id=' . $id : 'create')));
    }

    $data = [
        'judul'      => $judul,
        'solusi'     => trim($_POST['solusi'] ?? ''),
        'detail'     => trim($_POST['detail'] ?? ''),
        'teks_warna' => (($_POST['teks_warna'] ?? '') === 'red') ? 'red' : '',
        'url'        => trim($_POST['url'] ?? ''),
        'cta_label'  => trim($_POST['cta_label'] ?? ''),
        'cta_url'    => trim($_POST['cta_url'] ?? ''),
        'urutan'     => (int)($_POST['urutan'] ?? 0),
        'is_active'  => isset($_POST['is_active']) ? 1 : 0,
    ];

    // image helper: returns [value, should_set]
    $handle_img = function (string $field) {
        if (!empty($_FILES[$field]['name'])) {
            $up = upload_image($_FILES[$field], 'solutions');
            if ($up) return [$up, true];
            set_flash('error', 'Upload gambar gagal.');
            return [null, false];
        }
        if (!empty($_POST['hapus_' . $field])) return [null, true];
        return [null, false];
    };
    [$gambar, $set_gambar] = $handle_img('gambar');
    [$partner, $set_partner] = $handle_img('partner_img');

    if ($act === 'create') {
        $db->execute(
            "INSERT INTO solutions_section (judul,solusi,detail,teks_warna,gambar,partner_img,url,cta_label,cta_url,urutan,is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
            [$data['judul'],$data['solusi'],$data['detail'],$data['teks_warna'],$gambar,$partner,$data['url'],$data['cta_label'],$data['cta_url'],$data['urutan'],$data['is_active']]
        );
        save_i18n_fields('solutions_section', (int)$db->lastInsertId(), $_POST);
        log_activity('create', 'Tambah solution section: ' . strip_tags($judul));
        set_flash('success', 'Section ditambahkan.');
    } elseif ($act === 'update' && $id > 0) {
        $set = "judul=?,solusi=?,detail=?,teks_warna=?,url=?,cta_label=?,cta_url=?,urutan=?,is_active=?";
        $params = [$data['judul'],$data['solusi'],$data['detail'],$data['teks_warna'],$data['url'],$data['cta_label'],$data['cta_url'],$data['urutan'],$data['is_active']];
        if ($set_gambar)  { $set .= ",gambar=?";      $params[] = $gambar; }
        if ($set_partner) { $set .= ",partner_img=?"; $params[] = $partner; }
        $params[] = $id;
        $db->execute("UPDATE solutions_section SET $set WHERE id=?", $params);
        save_i18n_fields('solutions_section', $id, $_POST);
        log_activity('update', 'Update solution section: ' . strip_tags($judul));
        set_flash('success', 'Section diperbarui.');
    }
    redirect(admin_url('?page=solutions-page'));
}

$items = $db->fetchAll("SELECT * FROM solutions_section ORDER BY urutan, id");
$edit_item = ($action === 'edit' && $id > 0) ? $db->fetchOne("SELECT * FROM solutions_section WHERE id=?", [$id]) : null;
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1><?= icon('layers', 18) ?> Solutions (Page)</h1>
    <div class="page-header-sub">Blok solusi di halaman <code>/solutions</code>. Header &amp; banner "Coming Soon" diatur di <a href="<?= admin_url('?page=content&p=solutions') ?>">Konten Halaman → Solutions</a>.</div>
  </div>
  <?php if ($action === 'list'): ?>
  <div class="page-actions">
    <a href="<?= url('solutions') ?>" target="_blank" class="btn btn-secondary"><?= icon('eye', 16) ?> Lihat Halaman</a>
    <a href="<?= admin_url('?page=solutions-page&action=create') ?>" class="btn btn-primary"><?= icon('plus', 16) ?> Tambah Section</a>
  </div>
  <?php endif; ?>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>
  <form method="POST" enctype="multipart/form-data" class="card">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
    <div class="card-header"><div class="card-title"><?= $action === 'create' ? 'Tambah' : 'Edit' ?> Section</div></div>
    <div class="card-body">
      <div class="form-group"><label>Judul *</label>
        <input type="text" name="judul" required value="<?= htmlspecialchars($edit_item['judul'] ?? '') ?>" placeholder="Modernize &lt;b&gt;Infrastructure&lt;/b&gt;">
        <div class="form-hint">Bungkus bagian yang ingin berwarna biru dengan <code>&lt;b&gt;...&lt;/b&gt;</code>.</div></div>
      <div class="form-group"><label>Solution (teks paragraf)</label>
        <textarea name="solusi" rows="3" class="no-wysiwyg"><?= htmlspecialchars($edit_item['solusi'] ?? '') ?></textarea></div>
      <div class="form-group"><label>Detail Popup "See More"</label>
        <textarea name="detail" rows="10" class="wysiwyg"><?= htmlspecialchars($edit_item['detail'] ?? '') ?></textarea>
        <div class="form-hint">Isi popup yang muncul saat tombol <strong>See More</strong> diklik (tagline, penjelasan, daftar kapabilitas). Boleh dikosongkan &mdash; kalau kosong, tombol See More jadi link biasa ke URL section.</div></div>
      <div class="form-row">
        <div class="form-group"><label>Warna teks Solution</label>
          <select name="teks_warna">
            <option value="" <?= (($edit_item['teks_warna'] ?? '') !== 'red') ? 'selected' : '' ?>>Default (abu gelap)</option>
            <option value="red" <?= (($edit_item['teks_warna'] ?? '') === 'red') ? 'selected' : '' ?>>Merah</option>
          </select></div>
        <div class="form-group"><label>Urutan</label>
          <input type="number" name="urutan" value="<?= (int)($edit_item['urutan'] ?? 0) ?>"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Ilustrasi (gambar besar)</label>
          <?php if (!empty($edit_item['gambar'])): ?>
            <div class="img-upload-row" style="margin-bottom:8px">
              <div class="img-preview" style="width:120px;height:78px"><img src="<?= uploads_url($edit_item['gambar']) ?>" alt=""></div>
              <label class="checkbox-label" style="font-size:12px"><input type="checkbox" name="hapus_gambar" value="1"> Hapus</label>
            </div>
          <?php endif; ?>
          <input type="file" name="gambar" accept="image/*"></div>
        <div class="form-group"><label>Strip Logo Partner (gambar)</label>
          <?php if (!empty($edit_item['partner_img'])): ?>
            <div class="img-upload-row" style="margin-bottom:8px">
              <div class="img-preview" style="width:160px;height:52px;background:#fff"><img src="<?= uploads_url($edit_item['partner_img']) ?>" alt="" style="object-fit:contain"></div>
              <label class="checkbox-label" style="font-size:12px"><input type="checkbox" name="hapus_partner_img" value="1"> Hapus</label>
            </div>
          <?php endif; ?>
          <input type="file" name="partner_img" accept="image/*">
          <div class="form-hint">Gambar berisi deretan logo partner (background terang).</div></div>
      </div>
      <div class="form-group"><label>Link "See More"</label>
        <input type="text" name="url" value="<?= htmlspecialchars($edit_item['url'] ?? '') ?>" placeholder="# atau solutions/modernize-infrastructure"></div>
      <div class="form-row">
        <div class="form-group"><label>Tombol tambahan — teks (opsional)</label>
          <input type="text" name="cta_label" value="<?= htmlspecialchars($edit_item['cta_label'] ?? '') ?>" placeholder="mis. SatuAI">
          <div class="form-hint">Kosongkan bila tidak ada. (Figma: hanya di blok AI.)</div></div>
        <div class="form-group"><label>Tombol tambahan — link</label>
          <input type="text" name="cta_url" value="<?= htmlspecialchars($edit_item['cta_url'] ?? '') ?>" placeholder="#"></div>
      </div>
      <div class="form-group"><label class="checkbox-label">
        <input type="checkbox" name="is_active" <?= ($edit_item['is_active'] ?? 1) ? 'checked' : '' ?>> Tampilkan</label></div>
      <?php if ($action === 'edit') echo i18n_fields_editor('solutions_section', (int)$edit_item['id'], [
        'judul'     => 'Judul',
        'solusi'    => ['label' => 'Solution (teks)', 'type' => 'textarea'],
        'cta_label' => 'Tombol tambahan — teks',
      ]); ?>
      <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border)">
        <a href="<?= admin_url('?page=solutions-page') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan</button>
      </div>
    </div>
  </form>

<?php elseif (empty($items)): ?>
  <div class="card"><div class="empty-state">
    <div class="empty-state-icon"><?= icon('layers', 40) ?></div>
    <div>Belum ada section.</div>
    <a href="<?= admin_url('?page=solutions-page&action=create') ?>" class="btn btn-primary mt-2"><?= icon('plus', 16) ?> Tambah Section</a>
  </div></div>
<?php else: ?>
  <div class="table-wrap"><table>
    <thead><tr><th style="width:110px">Ilustrasi</th><th>Judul</th><th style="width:70px">Urutan</th><th style="width:80px">Status</th><th style="width:130px">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($items as $it): ?>
      <tr>
        <td><?php if (!empty($it['gambar'])): ?><div class="img-preview" style="width:96px;height:60px"><img src="<?= uploads_url($it['gambar']) ?>" alt=""></div>
          <?php else: ?><span style="color:var(--primary)"><?= icon('image', 22) ?></span><?php endif; ?></td>
        <td><div style="font-weight:600"><?= strip_tags($it['judul']) ?></div>
          <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars(mb_substr(strip_tags($it['solusi'] ?? ''), 0, 70)) ?></div></td>
        <td><?= (int)$it['urutan'] ?></td>
        <td><?= $it['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
        <td><div class="table-actions">
          <a href="<?= admin_url('?page=solutions-page&action=edit&id=' . $it['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Hapus section ini?')">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>"><input type="hidden" name="action" value="delete">
            <button type="submit" class="btn btn-danger btn-sm" formaction="<?= admin_url('?page=solutions-page&action=delete&id=' . $it['id']) ?>"><?= icon('trash', 15) ?></button>
          </form>
        </div></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
<?php endif; ?>
