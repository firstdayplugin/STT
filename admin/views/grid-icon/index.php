<?php
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
     redirect(admin_url('?page=' . ($_GET['page'] ?? 'dashboard'))); } else {
        $act = $_POST['action'] ?? '';
        
        if ($act === 'create' || $act === 'update') {
            $layanan_id_post = (int)($_POST['layanan_id'] ?? 0);
            $data = [
                'judul_section' => trim($_POST['judul_section'] ?? ''),
                'posisi'    => $_POST['posisi'] ?? 'home_middle',
                'layanan_id'=> $layanan_id_post ?: null,
                'kolom'     => (int)($_POST['kolom'] ?? 3),
                'urutan'    => (int)($_POST['urutan'] ?? 0),
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
            ];
            $data = $db->filterColumns('grid_icon_box', $data); // skip missing cols (layanan_id before migration)
            if ($act === 'create') {
                $cols = array_keys($data);
                $ph = implode(',', array_fill(0, count($cols), '?'));
                $db->execute("INSERT INTO grid_icon_box (" . implode(',', $cols) . ") VALUES ($ph)", array_values($data));
                $id = $db->lastInsertId();
            } else {
                $set = implode(',', array_map(fn($k) => "$k=?", array_keys($data)));
                $params = array_values($data);
                $params[] = $id;
                $db->execute("UPDATE grid_icon_box SET $set WHERE id=?", $params);
            }
            
            // Items
            if (!empty($_POST['items'])) {
                $db->execute("DELETE FROM grid_icon_box_items WHERE grid_id = ?", [$id]);
                foreach ($_POST['items'] as $i => $item) {
                    if (empty(trim($item['judul'] ?? ''))) continue;
                    $db->execute("INSERT INTO grid_icon_box_items (grid_id, icon, judul, deskripsi, link, urutan) VALUES (?,?,?,?,?,?)",
                        [$id, trim($item['icon'] ?? ''), trim($item['judul']), trim($item['deskripsi'] ?? ''), trim($item['link'] ?? ''), $i]);
                }
            }
            set_flash('success', 'Grid icon box berhasil disimpan.');
        } elseif ($act === 'delete' && $id > 0) {
            $db->execute("DELETE FROM grid_icon_box_items WHERE grid_id=?", [$id]);
            $db->execute("DELETE FROM grid_icon_box WHERE id=?", [$id]);
            set_flash('success', 'Grid icon box berhasil dihapus.');
        }
        redirect(admin_url('?page=grid-icon'));
    }
}

$items = $db->fetchAll("SELECT * FROM grid_icon_box ORDER BY posisi, urutan");
$edit_item = ($action === 'edit' && $id > 0) ? $db->fetchOne("SELECT * FROM grid_icon_box WHERE id=?", [$id]) : null;
$edit_box_items = ($edit_item) ? $db->fetchAll("SELECT * FROM grid_icon_box_items WHERE grid_id=? ORDER BY urutan", [$id]) : [];

$position_labels = [
    'home_after_hero'        => 'Home — Setelah Hero',
    'home_middle'            => 'Home — Tengah halaman',
    'home_before_footer'     => 'Home — Sebelum Footer',
    'about_top'              => 'About — Atas',
    'about_bottom'           => 'About — Bawah',
    'layanan_top'            => 'Halaman Layanan (list) — Atas',
    'layanan_bottom'         => 'Halaman Layanan (list) — Bawah',
    'layanan_detail_top'     => 'Detail Layanan — Setelah Hero',
    'layanan_detail_middle'  => 'Detail Layanan — Tengah',
    'layanan_detail_bottom'  => 'Detail Layanan — Sebelum Footer',
];

$all_layanan = $db->fetchAll("SELECT id, nama FROM layanan WHERE is_active=1 ORDER BY urutan, nama");
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1><?= icon('grid', 16) ?> Grid Icon Box</h1>
    <div class="page-header-sub">Tambahkan grid icon + text (3 atau 4 kolom) di halaman</div>
  </div>
  <?php if ($action === 'list'): ?>
  <div class="page-actions"><a href="<?= admin_url('?page=grid-icon&action=create') ?>" class="btn btn-primary">+ Tambah Grid</a></div>
  <?php endif; ?>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>

<form method="POST">
  <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
  <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
  
  <div class="card">
    <div class="card-header"><div class="card-title">Pengaturan Section</div></div>
    
    <div class="form-row">
      <div class="form-group">
        <label>Judul Section (opsional)</label>
        <input type="text" name="judul_section" value="<?= htmlspecialchars($edit_item['judul_section'] ?? '') ?>" placeholder="Mengapa Memilih Kami">
      </div>
      <div class="form-group">
        <label>Posisi *</label>
        <select name="posisi" required id="grid-posisi-select" onchange="toggleGridLayanan(this.value)">
          <?php foreach ($position_labels as $val => $lbl): ?>
            <option value="<?= $val ?>" <?= ($edit_item['posisi'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    
    <div class="form-group" id="grid-layanan-wrap" style="display:none">
      <label>Tampilkan di Halaman Detail Layanan</label>
      <select name="layanan_id">
        <option value="">— Semua Layanan —</option>
        <?php foreach ($all_layanan as $l): ?>
          <option value="<?= $l['id'] ?>" <?= ($edit_item['layanan_id'] ?? 0) == $l['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($l['nama']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div class="form-hint">Kosongkan untuk tampil di SEMUA detail layanan. Pilih spesifik untuk tampil di 1 layanan saja.</div>
    </div>
    
    <script>
      function toggleGridLayanan(val) {
        const wrap = document.getElementById('grid-layanan-wrap');
        if (wrap) wrap.style.display = val && val.startsWith('layanan_detail') ? '' : 'none';
      }
      document.addEventListener('DOMContentLoaded', () => {
        const sel = document.getElementById('grid-posisi-select');
        if (sel) toggleGridLayanan(sel.value);
      });
    </script>
    
    <div class="form-row cols-3">
      <div class="form-group">
        <label>Jumlah Kolom</label>
        <select name="kolom">
          <option value="2" <?= ($edit_item['kolom'] ?? 3) == 2 ? 'selected' : '' ?>>2 Kolom</option>
          <option value="3" <?= ($edit_item['kolom'] ?? 3) == 3 ? 'selected' : '' ?>>3 Kolom</option>
          <option value="4" <?= ($edit_item['kolom'] ?? 3) == 4 ? 'selected' : '' ?>>4 Kolom</option>
        </select>
      </div>
      <div class="form-group">
        <label>Urutan</label>
        <input type="number" name="urutan" value="<?= $edit_item['urutan'] ?? 0 ?>">
      </div>
      <div class="form-group">
        <label class="checkbox-label" style="margin-top:22px">
          <input type="checkbox" name="is_active" <?= ($edit_item['is_active'] ?? 1) ? 'checked' : '' ?>>
          Tampilkan
        </label>
      </div>
    </div>
  </div>
  
  <div class="card">
    <div class="card-header">
      <div><div class="card-title">Item Box</div><div class="card-subtitle">Isi icon + judul + deskripsi setiap kotak</div></div>
      <button type="button" onclick="addItem()" class="btn btn-secondary btn-sm">+ Tambah Item</button>
    </div>
    
    <div id="items-container">
      <?php foreach ($edit_box_items as $i => $it): ?>
      <div class="grid-item" style="padding:14px;background:var(--surface-2);border-radius:10px;margin-bottom:10px">
        <div class="form-row">
          <div class="form-group" style="margin-bottom:8px">
            <label>Icon (emoji)</label>
            <input type="text" name="items[<?= $i ?>][icon]" value="<?= htmlspecialchars($it['icon']) ?>" placeholder="" maxlength="10">
          </div>
          <div class="form-group" style="margin-bottom:8px">
            <label>Judul *</label>
            <input type="text" name="items[<?= $i ?>][judul]" value="<?= htmlspecialchars($it['judul']) ?>" required>
          </div>
        </div>
        <div class="form-group" style="margin-bottom:8px">
          <label>Deskripsi</label>
          <textarea class="no-wysiwyg" name="items[<?= $i ?>][deskripsi]" rows="2"><?= htmlspecialchars($it['deskripsi']) ?></textarea>
        </div>
        <div class="form-row" style="align-items:end">
          <div class="form-group" style="margin-bottom:0">
            <label>Link (opsional)</label>
            <input type="text" name="items[<?= $i ?>][link]" value="<?= htmlspecialchars($it['link']) ?>" placeholder="/layanan/neon-box">
          </div>
          <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.grid-item').remove()"><?= icon('trash', 16) ?> Hapus Item</button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    
    <?php if (empty($edit_box_items)): ?>
    <div class="empty-state" style="padding:20px">
      <div style="color:var(--text-muted);font-size:13px">Belum ada item. Klik "+ Tambah Item" untuk menambahkan.</div>
    </div>
    <?php endif; ?>
  </div>
  
  <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:16px">
    <a href="<?= admin_url('?page=grid-icon') ?>" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-primary btn-lg"><?= icon('save', 16) ?> Simpan Grid</button>
  </div>
</form>

<script>
let itemIdx = <?= count($edit_box_items) ?>;
function addItem() {
  const container = document.getElementById('items-container');
  const i = itemIdx++;
  container.insertAdjacentHTML('beforeend', `
    <div class="grid-item" style="padding:14px;background:var(--surface-2);border-radius:10px;margin-bottom:10px">
      <div class="form-row">
        <div class="form-group" style="margin-bottom:8px"><label>Icon (emoji)</label><input type="text" name="items[${i}][icon]" placeholder="" maxlength="10"></div>
        <div class="form-group" style="margin-bottom:8px"><label>Judul *</label><input type="text" name="items[${i}][judul]" required></div>
      </div>
      <div class="form-group" style="margin-bottom:8px"><label>Deskripsi</label><textarea class="no-wysiwyg" name="items[${i}][deskripsi]" rows="2"></textarea></div>
      <div class="form-row" style="align-items:end">
        <div class="form-group" style="margin-bottom:0"><label>Link</label><input type="text" name="items[${i}][link]" placeholder="/layanan/neon-box"></div>
        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.grid-item').remove()">Hapus</button>
      </div>
    </div>
  `);
}
</script>

<?php else: ?>

<?php if (empty($items)): ?>
  <div class="card"><div class="empty-state">
    <div class="empty-state-icon"><?= icon('grid', 16) ?></div>
    <div class="empty-title">Belum ada Grid Icon Box</div>
    <div class="empty-text">Tambahkan grid 3 atau 4 kolom dengan icon + text</div>
    <a href="<?= admin_url('?page=grid-icon&action=create') ?>" class="btn btn-primary mt-2">+ Tambah Grid Pertama</a>
  </div></div>
<?php else: ?>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Judul Section</th><th>Posisi</th><th>Kolom</th><th>Jumlah Item</th><th>Status</th><th style="width:120px">Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($items as $g): 
        $count = $db->fetchOne("SELECT COUNT(*) as c FROM grid_icon_box_items WHERE grid_id=?", [$g['id']])['c'] ?? 0;
      ?>
        <tr>
          <td><strong><?= htmlspecialchars($g['judul_section'] ?: '(Tanpa judul)') ?></strong></td>
          <td>
            <span class="badge badge-info"><?= $position_labels[$g['posisi']] ?? $g['posisi'] ?></span>
            <?php if (!empty($g['layanan_id'])): 
              $l_nama = $db->fetchOne("SELECT nama FROM layanan WHERE id=?", [$g['layanan_id']])['nama'] ?? '?';
            ?>
              <div style="font-size:11px;color:var(--text-muted);margin-top:4px"><?= icon('arrow-right', 16) ?> <?= htmlspecialchars($l_nama) ?></div>
            <?php endif; ?>
          </td>
          <td><?= $g['kolom'] ?> kolom</td>
          <td><?= $count ?> item</td>
          <td><?= $g['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
          <td>
            <div class="table-actions">
              <a href="<?= admin_url('?page=grid-icon&action=edit&id='.$g['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
              <form method="POST" action="<?= admin_url('?page=grid-icon&action=delete&id='.$g['id']) ?>" style="display:inline" onsubmit="return confirm('Hapus grid ini?')">
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
