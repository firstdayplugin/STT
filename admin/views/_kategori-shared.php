<?php
/**
 * Shared kategori management UI. Expects these variables to be set by the caller:
 *   $TABLE        — main table name (e.g. blog_kategori)
 *   $REL          — relation table
 *   $REL_COL      — item FK column in relation table
 *   $ITEM_TBL     — main items table (e.g. blog, produk)
 *   $LABEL        — "Artikel" / "Produk"
 *   $BASE_URL_KEY — admin page slug
 *   $BACK_URL     — URL to return to item listing
 *   $action, $id, $csrf, $db, $user
 */

// Load flat list with item-count
$flat = $db->fetchAll(
    "SELECT k.*,
            (SELECT COUNT(DISTINCT r.{$REL_COL}) FROM {$REL} r WHERE r.kategori_id = k.id) AS jml_item
     FROM {$TABLE} k
     ORDER BY k.urutan ASC, k.nama ASC"
);
// Get edit data if editing
$edit_item = null;
if ($action === 'edit' && $id > 0) {
    $edit_item = $db->fetchOne("SELECT * FROM {$TABLE} WHERE id = ?", [$id]);
    if (!$edit_item) {
        set_flash('error', 'Kategori tidak ditemukan.');
        redirect(admin_url("?page=$BASE_URL_KEY"));
    }
}

// Build flat tree (with depth) for ordered display
$tree = build_category_tree($flat);
$ordered = [];
$flatten = function($nodes, $depth=0) use (&$flatten, &$ordered) {
    foreach ($nodes as $n) {
        $n['_depth'] = $depth;
        $ordered[] = $n;
        if (!empty($n['children'])) $flatten($n['children'], $depth+1);
    }
};
$flatten($tree);
?>

<div class="page-header">
  <div>
    <h1>🏷️ Kategori <?= htmlspecialchars($LABEL) ?></h1>
    <div class="page-header-sub">Kelola kategori dan hierarki untuk <?= strtolower(htmlspecialchars($LABEL)) ?>.</div>
  </div>
  <div class="page-actions" style="display:flex;gap:8px">
    <a href="<?= htmlspecialchars($BACK_URL) ?>" class="btn btn-secondary">← Kembali ke <?= htmlspecialchars($LABEL) ?></a>
    <?php if ($action !== 'create' && $action !== 'edit'): ?>
    <a href="<?= admin_url("?page=$BASE_URL_KEY&action=create") ?>" class="btn btn-primary">+ Tambah Kategori</a>
    <?php endif; ?>
  </div>
</div>

<div class="grid" style="grid-template-columns:1fr 1.7fr;gap:20px;align-items:start">
  
  <!-- LEFT: Form (create or edit) -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">
        <?= $action === 'edit' ? '✏️ Edit Kategori' : '➕ Tambah Kategori Baru' ?>
      </div>
    </div>
    <form method="POST" class="card-body">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
      <input type="hidden" name="_action" value="<?= $action === 'edit' ? 'edit' : 'create' ?>">
      
      <div class="form-group">
        <label>Nama <span style="color:#e11">*</span></label>
        <input type="text" name="nama" class="form-control" required
               value="<?= htmlspecialchars($edit_item['nama'] ?? '') ?>"
               placeholder="contoh: Tips & Trik">
      </div>
      
      <div class="form-group">
        <label>Slug <small style="color:var(--text-muted);font-weight:400">(opsional, otomatis)</small></label>
        <input type="text" name="slug" class="form-control"
               value="<?= htmlspecialchars($edit_item['slug'] ?? '') ?>"
               placeholder="otomatis dari nama jika kosong">
        <div class="form-help">URL-friendly, hanya huruf kecil, angka, tanda hubung.</div>
      </div>
      
      <div class="form-group">
        <label>Parent Kategori</label>
        <select name="parent_id" class="form-control">
          <option value="0">— Kategori Utama (tanpa parent) —</option>
          <?php
          // Render dropdown excluding self & descendants
          $exclude_ids = [];
          if ($edit_item) {
              $exclude_ids = array_merge([(int)$edit_item['id']], get_descendant_ids($flat, $edit_item['id']));
          }
          $render_opts = function($nodes, $depth=0) use (&$render_opts, $exclude_ids, $edit_item) {
              foreach ($nodes as $n) {
                  if (in_array((int)$n['id'], $exclude_ids, true)) continue;
                  $sel = ($edit_item && (int)$edit_item['parent_id'] === (int)$n['id']) ? ' selected' : '';
                  echo '<option value="'.$n['id'].'"'.$sel.'>'.str_repeat('— ', $depth).htmlspecialchars($n['nama']).'</option>';
                  if (!empty($n['children'])) $render_opts($n['children'], $depth+1);
              }
          };
          $render_opts($tree);
          ?>
        </select>
        <div class="form-help">Pilih parent untuk membuat sub-kategori. Unlimited nesting didukung.</div>
      </div>
      
      <div class="form-group">
        <label>Urutan</label>
        <input type="number" name="urutan" class="form-control"
               value="<?= htmlspecialchars($edit_item['urutan'] ?? '0') ?>"
               min="0">
        <div class="form-help">Angka kecil tampil duluan. Default: 0</div>
      </div>
      
      <button type="submit" class="btn btn-primary btn-block">
        <?= $action === 'edit' ? '💾 Simpan Perubahan' : '+ Tambah Kategori' ?>
      </button>
      <?php if ($action === 'edit'): ?>
        <a href="<?= admin_url("?page=$BASE_URL_KEY") ?>" class="btn btn-secondary btn-block" style="margin-top:8px;text-align:center">Batal</a>
      <?php endif; ?>
    </form>
  </div>
  
  <!-- RIGHT: Listing -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Daftar Kategori (<?= count($flat) ?>)</div>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Nama</th>
            <th>Slug</th>
            <th>Parent</th>
            <th style="width:90px;text-align:center">Jumlah</th>
            <th style="width:140px;text-align:center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($ordered)): ?>
          <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:32px">
            Belum ada kategori. Tambahkan menggunakan form di kiri.
          </td></tr>
          <?php else: foreach ($ordered as $k):
            $parent_name = '—';
            if (!empty($k['parent_id'])) {
                foreach ($flat as $p) if ((int)$p['id'] === (int)$k['parent_id']) { $parent_name = $p['nama']; break; }
            }
          ?>
          <tr>
            <td style="font-weight:600">
              <?php if (!empty($k['_depth'])): ?>
                <span style="display:inline-block;width:<?= $k['_depth'] * 20 ?>px"></span>
                <span style="color:var(--text-muted);font-weight:400">↳ </span>
              <?php endif; ?>
              <?= htmlspecialchars($k['nama']) ?>
            </td>
            <td><code style="font-size:11.5px;background:var(--surface-2,#f5f7fa);padding:3px 7px;border-radius:4px;color:var(--text-muted)"><?= htmlspecialchars($k['slug']) ?></code></td>
            <td style="color:var(--text-muted);font-size:13px"><?= htmlspecialchars($parent_name) ?></td>
            <td style="text-align:center"><?= (int) $k['jml_item'] ?></td>
            <td style="text-align:center">
              <a href="<?= admin_url("?page=$BASE_URL_KEY&action=edit&id={$k['id']}") ?>" class="btn btn-sm btn-secondary">Edit</a>
              <form method="POST" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus kategori &quot;<?= htmlspecialchars($k['nama'], ENT_QUOTES) ?>&quot;?<?php if ($k['jml_item'] > 0): ?>\n\n⚠️ Kategori ini punya <?= (int)$k['jml_item'] ?> item terkait — relasi akan hilang tapi item tetap aman.<?php endif; ?>')">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="_action" value="delete">
                <input type="hidden" name="id" value="<?= $k['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">🗑</button>
              </form>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
