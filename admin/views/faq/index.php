<?php
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
        redirect(admin_url('?page=faq'));
    }
    try {
        $action_post = $_POST['action'] ?? '';

        // Delete branches first — they don't need field validation
        if ($action_post === 'delete' && $id > 0) {
            $db->execute("DELETE FROM faq_layanan_rel WHERE faq_id=?", [$id]);
            $db->execute("DELETE FROM faq WHERE id = ?", [$id]);
            log_activity('delete', 'FAQ ID ' . $id);
            set_flash('success', 'FAQ berhasil dihapus.');
            redirect(admin_url('?page=faq'));
        }

        // Create / update — field validation applies here
        $data = [
            'pertanyaan' => trim($_POST['pertanyaan'] ?? ''),
            'jawaban'    => trim($_POST['jawaban'] ?? ''),
            'urutan'     => (int)($_POST['urutan'] ?? 0),
            'is_active'  => isset($_POST['is_active']) ? 1 : 0,
        ];
        $layanan_ids = $_POST['layanan_ids'] ?? [];
        $show_global = isset($_POST['show_global']) ? 1 : 0;
        
        if (empty($data['pertanyaan']) || empty($data['jawaban'])) {
            set_flash('error', 'Pertanyaan & Jawaban wajib diisi.');
        } elseif ($action_post === 'create') {
            $db->execute("INSERT INTO faq (pertanyaan, jawaban, urutan, is_active) VALUES (?,?,?,?)",
                array_values($data));
            $new_id = $db->lastInsertId();
            
            // Save relations
            if (!$show_global) {
                foreach ($layanan_ids as $lid) {
                    if ((int)$lid > 0) {
                        $db->execute("INSERT IGNORE INTO faq_layanan_rel (faq_id, layanan_id) VALUES (?,?)", [$new_id, (int)$lid]);
                    }
                }
            }
            log_activity('create', 'FAQ: ' . substr($data['pertanyaan'], 0, 80));
            set_flash('success', 'FAQ berhasil ditambahkan.');
        } elseif ($action_post === 'update' && $id > 0) {
            $db->execute("UPDATE faq SET pertanyaan=?, jawaban=?, urutan=?, is_active=? WHERE id=?",
                [$data['pertanyaan'], $data['jawaban'], $data['urutan'], $data['is_active'], $id]);
            
            // Reset & re-save relations
            $db->execute("DELETE FROM faq_layanan_rel WHERE faq_id=?", [$id]);
            if (!$show_global) {
                foreach ($layanan_ids as $lid) {
                    if ((int)$lid > 0) {
                        $db->execute("INSERT IGNORE INTO faq_layanan_rel (faq_id, layanan_id) VALUES (?,?)", [$id, (int)$lid]);
                    }
                }
            }
            log_activity('update', 'FAQ ID ' . $id);
            set_flash('success', 'FAQ berhasil diupdate.');
        }
    } catch (Throwable $e) {
        set_flash('error', 'Gagal: ' . $e->getMessage());
    }
    redirect(admin_url('?page=faq'));
}

$all_layanan = $db->fetchAll("SELECT id, nama FROM layanan WHERE is_active=1 ORDER BY urutan, nama");
$items = $db->fetchAll("SELECT * FROM faq ORDER BY urutan, id");
$edit_item = null;
$edit_layanan_ids = [];
if ($action === 'edit' && $id > 0) {
    $edit_item = $db->fetchOne("SELECT * FROM faq WHERE id = ?", [$id]);
    if (!$edit_item) {
        set_flash('error', 'FAQ tidak ditemukan.');
        redirect(admin_url('?page=faq'));
    }
    try {
        $edit_layanan_ids = array_column(
            $db->fetchAll("SELECT layanan_id FROM faq_layanan_rel WHERE faq_id=?", [$id]),
            'layanan_id'
        );
    } catch (Throwable $e) { $edit_layanan_ids = []; }
}
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1><?= icon('faq', 16) ?> FAQ (Frequently Asked Questions)</h1>
    <div class="page-header-sub">Kelola FAQ. Bisa di-assign ke halaman home + detail layanan tertentu.</div>
  </div>
  <?php if ($action === 'list'): ?>
  <div class="page-actions"><a href="<?= admin_url('?page=faq&action=create') ?>" class="btn btn-primary">+ Tambah FAQ</a></div>
  <?php endif; ?>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>

<form method="POST">
  <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
  <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
  
  <div class="card">
    <div class="card-header"><div class="card-title"><?= $action === 'create' ? 'Tambah' : 'Edit' ?> FAQ</div></div>
    
    <div class="form-group">
      <label>Pertanyaan *</label>
      <input type="text" name="pertanyaan" required value="<?= htmlspecialchars($edit_item['pertanyaan'] ?? '') ?>" placeholder="Berapa lama waktu pengerjaan?">
    </div>
    
    <div class="form-group">
      <label>Jawaban *</label>
      <textarea name="jawaban" rows="5" required class="wysiwyg"><?= htmlspecialchars($edit_item['jawaban'] ?? '') ?></textarea>
    </div>
    
    <div class="form-row">
      <div class="form-group">
        <label>Urutan</label>
        <input type="number" name="urutan" value="<?= $edit_item['urutan'] ?? 0 ?>">
      </div>
      <div class="form-group">
        <label class="checkbox-label" style="margin-top:22px">
          <input type="checkbox" name="is_active" <?= ($edit_item['is_active'] ?? 1) ? 'checked' : '' ?>>
          Aktifkan FAQ ini
        </label>
      </div>
    </div>
  </div>
  
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title"><?= icon('pin', 16) ?> Tampilkan di Halaman Mana?</div>
        <div class="card-subtitle">FAQ tampil di halaman Home & detail layanan yang dipilih</div>
      </div>
    </div>
    
    <div class="form-group">
      <label class="checkbox-label" style="font-size:14px;font-weight:600">
        <input type="checkbox" name="show_global" value="1" id="show-global-cb" 
               <?= (empty($edit_layanan_ids) && ($action === 'create' || !empty($edit_item))) ? 'checked' : '' ?>
               onchange="toggleGlobalFaq(this)">
        <?= icon('globe', 16) ?> Tampilkan di Home + Semua Halaman Detail Layanan
      </label>
      <div class="form-hint">Aktifkan jika FAQ bersifat umum. Nonaktifkan untuk pilih layanan spesifik.</div>
    </div>
    
    <div id="layanan-checks-wrap" style="border-top:1px solid var(--border);padding-top:14px">
      <div style="font-size:13px;font-weight:600;margin-bottom:10px">Pilih layanan spesifik:</div>
      <?php if (empty($all_layanan)): ?>
        <div class="alert alert-warning" style="font-size:12px">Belum ada layanan. Buat layanan dulu di menu Layanan.</div>
      <?php else: ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px">
        <?php foreach ($all_layanan as $l): ?>
          <label class="checkbox-label">
            <input type="checkbox" name="layanan_ids[]" value="<?= $l['id'] ?>" 
                   <?= in_array($l['id'], $edit_layanan_ids) ? 'checked' : '' ?>>
            <?= htmlspecialchars($l['nama']) ?>
          </label>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
  
  <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:16px">
    <a href="<?= admin_url('?page=faq') ?>" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-primary btn-lg"><?= icon('save', 16) ?> Simpan FAQ</button>
  </div>
</form>

<script>
function toggleGlobalFaq(cb) {
  const wrap = document.getElementById('layanan-checks-wrap');
  if (wrap) wrap.style.opacity = cb.checked ? '0.4' : '1';
  if (wrap) {
    wrap.querySelectorAll('input[type=checkbox]').forEach(i => i.disabled = cb.checked);
  }
}
document.addEventListener('DOMContentLoaded', () => {
  const cb = document.getElementById('show-global-cb');
  if (cb) toggleGlobalFaq(cb);
});
</script>

<?php else: ?>

<?php if (empty($items)): ?>
  <div class="card"><div class="empty-state">
    <div class="empty-state-icon"><?= icon('faq', 16) ?></div>
    <div class="empty-title">Belum ada FAQ</div>
    <div class="empty-text">Tambah pertanyaan & jawaban yang sering ditanyakan</div>
    <a href="<?= admin_url('?page=faq&action=create') ?>" class="btn btn-primary mt-2">+ Tambah FAQ Pertama</a>
  </div></div>
<?php else: ?>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Pertanyaan</th><th>Tampil di</th><th style="width:80px">Urutan</th><th style="width:80px">Status</th><th style="width:140px">Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($items as $f): 
        try {
          $rels = $db->fetchAll("SELECT l.nama FROM faq_layanan_rel r JOIN layanan l ON r.layanan_id=l.id WHERE r.faq_id=?", [$f['id']]);
        } catch (Throwable $e) { $rels = []; }
      ?>
        <tr>
          <td>
            <div style="font-weight:600;margin-bottom:2px"><?= htmlspecialchars($f['pertanyaan']) ?></div>
            <div style="font-size:11px;color:var(--text-muted)"><?= excerpt($f['jawaban'], 80) ?></div>
          </td>
          <td>
            <?php if (empty($rels)): ?>
              <span class="badge badge-info"><?= icon('globe', 16) ?> Global (Home + Semua Detail)</span>
            <?php else: ?>
              <?php foreach ($rels as $r): ?>
                <span class="badge badge-gray" style="font-size:10px;display:inline-block;margin:2px"><?= htmlspecialchars($r['nama']) ?></span>
              <?php endforeach; ?>
            <?php endif; ?>
          </td>
          <td><?= $f['urutan'] ?></td>
          <td><?= $f['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
          <td>
            <div class="table-actions">
              <a href="<?= admin_url('?page=faq&action=edit&id='.$f['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
              <form method="POST" action="<?= admin_url('?page=faq&action=delete&id='.$f['id']) ?>" style="display:inline" onsubmit="return confirm('Hapus FAQ ini?')">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="btn btn-danger btn-sm"><?= icon('trash', 16) ?></button>
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
