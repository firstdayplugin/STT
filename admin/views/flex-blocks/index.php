<?php
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
     redirect(admin_url('?page=' . ($_GET['page'] ?? 'dashboard'))); } else {
        $act = $_POST['action'] ?? '';
        $layanan_id_post = (int)($_POST['layanan_id'] ?? 0);
        $data = [
            'judul'     => trim($_POST['judul'] ?? ''),
            'konten'    => $_POST['konten'] ?? '',
            'posisi'    => $_POST['posisi'] ?? 'home_after_hero',
            'layanan_id' => $layanan_id_post ?: null,
            'align'     => $_POST['align'] ?? 'center',
            'bg_color'  => trim($_POST['bg_color'] ?? ''),
            'urutan'    => (int)($_POST['urutan'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        $data = $db->filterColumns('flex_blocks', $data); // skip missing cols (e.g. layanan_id before migration)
        try {
        if ($act === 'create') {
            $cols = array_keys($data);
            $ph = implode(',', array_fill(0, count($cols), '?'));
            $db->execute("INSERT INTO flex_blocks (" . implode(',', $cols) . ") VALUES ($ph)", array_values($data));
            set_flash('success', '✅ Content block berhasil ditambahkan.');
        } elseif ($act === 'update' && $id > 0) {
            $set = implode(',', array_map(fn($k) => "$k=?", array_keys($data)));
            $params = array_values($data);
            $params[] = $id;
            $db->execute("UPDATE flex_blocks SET $set WHERE id=?", $params);
            set_flash('success', '✅ Content block berhasil diupdate.');
        } elseif ($act === 'delete' && $id > 0) {
            $db->execute("DELETE FROM flex_blocks WHERE id=?", [$id]);
            set_flash('success', '🗑️ Content block berhasil dihapus.');
        }
        } catch (Throwable $e) {
            set_flash('error', '❌ Gagal: ' . $e->getMessage());
        }
        redirect(admin_url('?page=flex-blocks'));
    }
}

$items = $db->fetchAll("SELECT * FROM flex_blocks ORDER BY posisi, urutan");
$edit_item = ($action === 'edit' && $id > 0) ? $db->fetchOne("SELECT * FROM flex_blocks WHERE id=?", [$id]) : null;

$position_labels = [
    'home_after_hero'        => '🏠 Home — Setelah Hero',
    'home_middle'            => '🏠 Home — Tengah halaman',
    'home_before_footer'     => '🏠 Home — Sebelum Footer',
    'about_top'              => '👥 About — Atas',
    'about_bottom'           => '👥 About — Bawah',
    'layanan_top'            => '🎨 Halaman Layanan (list) — Atas',
    'layanan_bottom'         => '🎨 Halaman Layanan (list) — Bawah',
    'layanan_detail_top'     => '✨ Detail Layanan — Setelah Hero',
    'layanan_detail_middle'  => '✨ Detail Layanan — Tengah',
    'layanan_detail_bottom'  => '✨ Detail Layanan — Sebelum Footer',
];

// Get all layanan for dropdown
$all_layanan = $db->fetchAll("SELECT id, nama FROM layanan WHERE is_active=1 ORDER BY urutan, nama");
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1>📝 Flexible Content Block</h1>
    <div class="page-header-sub">Tambah area text fleksibel di posisi mana saja pada halaman</div>
  </div>
  <?php if ($action === 'list'): ?>
  <div class="page-actions"><a href="<?= admin_url('?page=flex-blocks&action=create') ?>" class="btn btn-primary">+ Tambah Block</a></div>
  <?php endif; ?>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>

<div class="card">
  <div class="card-header"><div class="card-title"><?= $action === 'create' ? 'Tambah' : 'Edit' ?> Content Block</div></div>
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
    
    <div class="form-row">
      <div class="form-group">
        <label>Judul (opsional)</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($edit_item['judul'] ?? '') ?>" placeholder="Judul section">
      </div>
      <div class="form-group">
        <label>Posisi di Website *</label>
        <select name="posisi" required id="posisi-select" onchange="toggleLayananSelector(this.value)">
          <?php foreach ($position_labels as $val => $lbl): ?>
            <option value="<?= $val ?>" <?= ($edit_item['posisi'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    
    <!-- Layanan selector — only show when posisi is layanan_detail_* -->
    <div class="form-group" id="layanan-selector-wrap" style="display:none">
      <label>Tampilkan di Halaman Detail Layanan *</label>
      <select name="layanan_id">
        <option value="">— Semua Layanan (tampil di semua detail layanan) —</option>
        <?php foreach ($all_layanan as $l): ?>
          <option value="<?= $l['id'] ?>" <?= ($edit_item['layanan_id'] ?? 0) == $l['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($l['nama']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div class="form-hint">Kosongkan untuk tampil di SEMUA halaman detail layanan. Pilih spesifik untuk tampil di 1 layanan saja.</div>
    </div>
    
    <script>
      function toggleLayananSelector(val) {
        const wrap = document.getElementById('layanan-selector-wrap');
        if (wrap) wrap.style.display = val && val.startsWith('layanan_detail') ? '' : 'none';
      }
      document.addEventListener('DOMContentLoaded', () => {
        const sel = document.getElementById('posisi-select');
        if (sel) toggleLayananSelector(sel.value);
      });
    </script>
    
    <div class="form-group">
      <label>Konten *</label>
      <textarea name="konten" rows="6" required class="wysiwyg"><?= htmlspecialchars($edit_item['konten'] ?? '') ?></textarea>
      <div class="form-hint">Bisa pakai HTML sederhana: &lt;p&gt;, &lt;strong&gt;, &lt;a href=""&gt;, &lt;br&gt;</div>
    </div>
    
    <div class="form-row cols-3">
      <div class="form-group">
        <label>Alignment</label>
        <select name="align">
          <option value="left" <?= ($edit_item['align'] ?? '') === 'left' ? 'selected' : '' ?>>Kiri</option>
          <option value="center" <?= ($edit_item['align'] ?? 'center') === 'center' ? 'selected' : '' ?>>Tengah</option>
          <option value="right" <?= ($edit_item['align'] ?? '') === 'right' ? 'selected' : '' ?>>Kanan</option>
        </select>
      </div>
      <div class="form-group">
        <label>Background Color (opsional)</label>
        <input type="text" name="bg_color" value="<?= htmlspecialchars($edit_item['bg_color'] ?? '') ?>" placeholder="#EEEAE3 atau transparent">
      </div>
      <div class="form-group">
        <label>Urutan</label>
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
      <a href="<?= admin_url('?page=flex-blocks') ?>" class="btn btn-secondary">Batal</a>
      <button type="submit" class="btn btn-primary">💾 Simpan</button>
    </div>
  </form>
</div>

<?php else: ?>

<?php if (empty($items)): ?>
  <div class="card"><div class="empty-state">
    <div class="empty-state-icon">📝</div>
    <div class="empty-title">Belum ada content block</div>
    <div class="empty-text">Tambahkan text area fleksibel untuk tampil di halaman</div>
    <a href="<?= admin_url('?page=flex-blocks&action=create') ?>" class="btn btn-primary mt-2">+ Tambah Block Pertama</a>
  </div></div>
<?php else: ?>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Judul</th><th>Posisi</th><th>Preview</th><th>Status</th><th style="width:120px">Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($items as $b): ?>
        <tr>
          <td><strong><?= htmlspecialchars($b['judul'] ?: '(Tanpa judul)') ?></strong></td>
          <td>
            <span class="badge badge-info"><?= $position_labels[$b['posisi']] ?? $b['posisi'] ?></span>
            <?php if (!empty($b['layanan_id'])): 
              $l_nama = $db->fetchOne("SELECT nama FROM layanan WHERE id=?", [$b['layanan_id']])['nama'] ?? '?';
            ?>
              <div style="font-size:11px;color:var(--text-muted);margin-top:4px">→ <?= htmlspecialchars($l_nama) ?></div>
            <?php endif; ?>
          </td>
          <td style="max-width:300px;color:var(--text-muted);font-size:12px"><?= excerpt(strip_tags($b['konten']), 80) ?></td>
          <td><?= $b['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
          <td>
            <div class="table-actions">
              <a href="<?= admin_url('?page=flex-blocks&action=edit&id='.$b['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
              <form method="POST" action="<?= admin_url('?page=flex-blocks&action=delete&id='.$b['id']) ?>" style="display:inline" onsubmit="return confirm('Hapus block ini?')">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="btn btn-danger btn-sm">🗑</button>
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
