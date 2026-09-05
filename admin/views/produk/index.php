<?php
$page_title = 'Manajemen Produk';

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
        redirect(admin_url('?page=produk'));
    }
    $a = $_POST['_action'] ?? '';

    if ($a === 'delete') {
        $del_id = (int)($_POST['del_id'] ?? 0);
        if ($del_id) {
            $db->execute("DELETE FROM produk WHERE id = ?", [$del_id]);
            log_activity('delete', 'Hapus produk #'.$del_id, $user['id']);
            set_flash('success', 'Produk berhasil dihapus.');
        }
        redirect(admin_url('?page=produk'));
    }

    if ($a === 'save') {
        $pid         = (int)($_POST['id'] ?? 0);
        $nama        = trim($_POST['nama'] ?? '');
        $slug        = trim($_POST['slug'] ?? '');
        $deskripsi   = $_POST['deskripsi'] ?? '';
        $short_desc  = trim($_POST['short_description'] ?? '');
        $harga       = parse_rupiah($_POST['harga'] ?? '0');
        $harga_coret = parse_rupiah($_POST['harga_coret'] ?? '0');
        $status      = in_array($_POST['status'] ?? '', ['aktif','nonaktif']) ? $_POST['status'] : 'aktif';
        $badge       = trim($_POST['badge'] ?? '');
        $stok        = (int)($_POST['stok'] ?? -1);
        $berat       = (float)($_POST['berat'] ?? 0);
        $meta_title  = trim($_POST['meta_title'] ?? '');
        $meta_desc   = trim($_POST['meta_desc'] ?? '');
        $kategori_ids= $_POST['kategori'] ?? [];

        if (empty($nama)) { set_flash('error', 'Nama produk wajib diisi.'); redirect(admin_url('?page=produk&action='.($pid?'edit&id='.$pid:'create'))); }
        if (empty($slug)) $slug = make_slug($nama);

        $exist = $db->fetchOne("SELECT id FROM produk WHERE slug = ? AND id != ?", [$slug, $pid]);
        if ($exist) $slug .= '-'.time();

        // Upload gambar utama
        $gambar_utama = '';
        if (!empty($_FILES['gambar_utama']['name'])) {
            $up = upload_image($_FILES['gambar_utama'], 'produk');
            if ($up) $gambar_utama = $up;
        }

        $data = [
            'nama'         => $nama,
            'slug'         => $slug,
            'deskripsi'    => $deskripsi,
            'short_description' => $short_desc,
            'harga'        => $harga,
            'harga_coret'  => $harga_coret ?: null,
            'status'       => $status,
            'badge'        => $badge,
            'stok'         => $stok,
            'berat'        => $berat,
            'meta_title'   => $meta_title,
            'meta_description' => $meta_desc,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];
        if ($gambar_utama) $data['gambar_utama'] = $gambar_utama;

        if ($pid) {
            $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
            $db->execute("UPDATE produk SET $set WHERE id = ?", [...array_values($data), $pid]);
            log_activity('update', 'Update produk: '.$nama, $user['id']);
            $new_id = $pid;
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $new_id = $db->insert('produk', $data);
            log_activity('create', 'Tambah produk: '.$nama, $user['id']);
        }

        // Kategori
        $db->execute("DELETE FROM produk_kategori_rel WHERE produk_id = ?", [$new_id]);
        foreach ($kategori_ids as $kid) {
            $db->execute("INSERT IGNORE INTO produk_kategori_rel (produk_id, kategori_id) VALUES (?,?)", [$new_id, (int)$kid]);
        }

        // Marketplace URLs
        $mp_platforms = $_POST['mp_platform'] ?? [];
        $mp_urls      = $_POST['mp_url'] ?? [];
        $db->execute("DELETE FROM produk_marketplace WHERE produk_id = ?", [$new_id]);
        foreach ($mp_platforms as $i => $plat) {
            $url = trim($mp_urls[$i] ?? '');
            if ($plat && $url) {
                $db->insert('produk_marketplace', ['produk_id'=>$new_id,'platform'=>$plat,'url'=>$url]);
            }
        }

        // Gallery upload
        if (!empty($_FILES['gallery_images']['name'][0])) {
            foreach ($_FILES['gallery_images']['name'] as $gi => $gname) {
                if (!$gname) continue;
                $gfile = [
                    'name'     => $gname,
                    'type'     => $_FILES['gallery_images']['type'][$gi],
                    'tmp_name' => $_FILES['gallery_images']['tmp_name'][$gi],
                    'error'    => $_FILES['gallery_images']['error'][$gi],
                    'size'     => $_FILES['gallery_images']['size'][$gi],
                ];
                $gup = upload_image($gfile, 'produk');
                if ($gup) $db->insert('produk_gallery', ['produk_id'=>$new_id,'gambar'=>$gup,'urutan'=>$gi]);
            }
        }

        set_flash('success', 'Produk berhasil disimpan!');
        redirect(admin_url('?page=produk'));
    }

    if ($a === 'delete_gallery') {
        $gid = (int)($_POST['gallery_id'] ?? 0);
        if ($gid) $db->execute("DELETE FROM produk_gallery WHERE id = ?", [$gid]);
        redirect(admin_url('?page=produk&action=edit&id='.$id));
    }
}

// Edit data
$edit = null;
$edit_kategori = [];
$edit_marketplace = [];
$edit_gallery = [];
if ($action === 'edit' && $id) {
    $edit = $db->fetchOne("SELECT * FROM produk WHERE id = ?", [$id]);
    if (!$edit) { set_flash('error', 'Produk tidak ditemukan.'); redirect(admin_url('?page=produk')); }
    $edit_kategori    = array_column($db->fetchAll("SELECT kategori_id FROM produk_kategori_rel WHERE produk_id = ?", [$id]), 'kategori_id');
    $edit_marketplace = $db->fetchAll("SELECT * FROM produk_marketplace WHERE produk_id = ?", [$id]);
    $edit_gallery     = $db->fetchAll("SELECT * FROM produk_gallery WHERE produk_id = ? ORDER BY urutan", [$id]);
}

$all_kategori = $db->fetchAll("SELECT * FROM produk_kategori ORDER BY nama");
$mp_list = ['Tokopedia','Shopee','Lazada','Blibli','Bukalapak','Website','Lainnya'];

// List
$search = trim($_GET['search'] ?? '');
$filter_kat = (int)($_GET['kat'] ?? 0);
$per_page = 15;
$cp = max(1,(int)($_GET['p'] ?? 1));
$offset = ($cp-1)*$per_page;

$where = '1=1'; $params = [];
if ($search) { $where .= ' AND p.nama LIKE ?'; $params[] = "%$search%"; }
if ($filter_kat) { $where .= ' AND EXISTS(SELECT 1 FROM produk_kategori_rel pkr WHERE pkr.produk_id=p.id AND pkr.kategori_id=?)'; $params[] = $filter_kat; }

$total = $db->fetchOne("SELECT COUNT(*) as c FROM produk p WHERE $where", $params)['c'];
$products = $db->fetchAll("SELECT p.* FROM produk p WHERE $where ORDER BY p.created_at DESC LIMIT $per_page OFFSET $offset", $params);

$csrf = generate_csrf();

if ($action === 'create' || $action === 'edit'):
$page_title = $action === 'edit' ? 'Edit Produk' : 'Tambah Produk';
$breadcrumbs = [['label'=>'Produk','url'=>admin_url('?page=produk')]];
?>

<div class="page-header">
    <div class="page-title"><?= $page_title ?></div>
    <a href="<?= admin_url('?page=produk') ?>" class="btn btn-secondary"><?= icon('arrow-left', 16) ?> Kembali</a>
</div>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="_action" value="save">
    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">

<div class="grid-2" style="align-items:start">
<div style="display:flex;flex-direction:column;gap:20px">

    <div class="card">
        <div class="card-header"><div class="card-title">Informasi Produk</div></div>
        <div class="card-body">
            <div class="form-group">
                <label>Nama Produk <span class="required">*</span></label>
                <input type="text" name="nama" class="form-control" id="nama-input"
                       value="<?= htmlspecialchars($edit['nama'] ?? '') ?>" required
                       placeholder="Nama produk...">
            </div>
            <div class="form-group">
                <label>Slug</label>
                <input type="text" name="slug" class="form-control" id="slug-input"
                       value="<?= htmlspecialchars($edit['slug'] ?? '') ?>"
                       placeholder="otomatis-dari-nama">
            </div>
            <div class="form-group">
                <label>Deskripsi Singkat (Short Description)</label>
                <textarea name="short_description" class="form-control wysiwyg" rows="3"
                          placeholder="Ringkasan singkat yang tampil di atas tombol WhatsApp pada halaman detail produk. 1-3 kalimat."><?= htmlspecialchars($edit['short_description'] ?? '') ?></textarea>
                <div class="form-hint">Tampil di area atas (dekat CTA). Singkat & to-the-point. Kosongkan untuk pakai potongan deskripsi panjang.</div>
            </div>
            <div class="form-group mb-0">
                <label>Deskripsi Lengkap</label>
                <textarea name="deskripsi" class="form-control wysiwyg" rows="6"
                          placeholder="Deskripsi detail produk. Tampil di section bawah halaman detail produk."><?= htmlspecialchars($edit['deskripsi'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title">Harga & Stok</div></div>
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group mb-0">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control"
                           value="<?= isset($edit['harga']) && $edit['harga'] !== null ? (int)$edit['harga'] : '' ?>"
                           placeholder="0" min="0" step="1000">
                    <div class="form-help">Kosongkan jika "Hubungi Kami"</div>
                </div>
                <div class="form-group mb-0">
                    <label>Harga Coret (Rp)</label>
                    <input type="number" name="harga_coret" class="form-control"
                           value="<?= isset($edit['harga_coret']) && $edit['harga_coret'] !== null ? (int)$edit['harga_coret'] : '' ?>"
                           placeholder="0" min="0" step="1000">
                    <div class="form-help">Harga asli sebelum diskon</div>
                </div>
                <div class="form-group mb-0">
                    <label>Stok</label>
                    <input type="number" name="stok" class="form-control"
                           value="<?= $edit['stok'] ?? -1 ?>"
                           placeholder="-1 = unlimited">
                    <div class="form-help">-1 = tidak terbatas</div>
                </div>
                <div class="form-group mb-0">
                    <label>Berat (kg)</label>
                    <input type="number" name="berat" class="form-control"
                           value="<?= $edit['berat'] ?? '' ?>"
                           placeholder="0.5" step="0.1" min="0">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title"><?= icon('shopping-bag', 16) ?> Link Marketplace</div></div>
        <div class="card-body">
            <div id="mp-list">
            <?php
            $rows = !empty($edit_marketplace) ? $edit_marketplace : [['platform'=>'','url'=>'']];
            foreach ($rows as $ri => $mp): ?>
            <div class="mp-row" style="display:flex;gap:10px;margin-bottom:10px;align-items:center">
                <select name="mp_platform[]" class="form-control" style="flex:0 0 140px">
                    <option value="">— Platform —</option>
                    <?php foreach ($mp_list as $mp_name): ?>
                    <option value="<?= $mp_name ?>" <?= ($mp['platform'] ?? '') === $mp_name ? 'selected' : '' ?>><?= $mp_name ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="url" name="mp_url[]" class="form-control"
                       value="<?= htmlspecialchars($mp['url'] ?? '') ?>"
                       placeholder="https://...">
                <button type="button" onclick="this.closest('.mp-row').remove()"
                        class="btn btn-xs btn-danger" style="flex-shrink:0">×</button>
            </div>
            <?php endforeach; ?>
            </div>
            <button type="button" onclick="addMpRow()" class="btn btn-sm btn-secondary mt-16">
                + Tambah Link
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title">SEO Produk</div></div>
        <div class="card-body">
            <div class="form-group">
                <label>Meta Title</label>
                <input type="text" name="meta_title" class="form-control"
                       value="<?= htmlspecialchars($edit['meta_title'] ?? '') ?>"
                       placeholder="Kosongkan untuk gunakan nama produk">
            </div>
            <div class="form-group mb-0">
                <label>Meta Description</label>
                <textarea name="meta_desc" class="form-control no-wysiwyg" rows="3"
                          placeholder="Max 160 karakter"><?= htmlspecialchars($edit['meta_description'] ?? '') ?></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Sidebar -->
<div style="display:flex;flex-direction:column;gap:20px">

    <div class="card">
        <div class="card-header"><div class="card-title">Status & Badge</div></div>
        <div class="card-body">
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="aktif" <?= ($edit['status'] ?? 'aktif') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= ($edit['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <div class="form-group mb-0">
                <label>Badge Label</label>
                <input type="text" name="badge" class="form-control"
                       value="<?= htmlspecialchars($edit['badge'] ?? '') ?>"
                       placeholder="Baru, Promo, Bestseller...">
                <div class="form-help">Tampil sebagai label di kartu produk</div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary w-full" style="justify-content:center">
                <?= icon('save', 16) ?> Simpan Produk
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title">Gambar Utama</div></div>
        <div class="card-body">
            <?php if (!empty($edit['gambar_utama'])): ?>
            <div class="img-preview-wrap mb-12">
                <img src="<?= uploads_url($edit['gambar_utama']) ?>" alt="">
            </div>
            <?php endif; ?>
            <input type="file" name="gambar_utama" class="form-control" accept="image/*">
            <div class="form-help">JPG/PNG/WebP, maks 2MB</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title">Galeri Produk</div></div>
        <div class="card-body">
            <?php if (!empty($edit_gallery)): ?>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:12px">
            <?php foreach ($edit_gallery as $gi): ?>
                <div style="position:relative" data-gallery-item="<?= $gi['id'] ?>">
                    <img src="<?= uploads_url($gi['gambar']) ?>" style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:8px;border:1px solid var(--border)">
                    <button type="button" class="btn btn-xs btn-danger" style="position:absolute;top:4px;right:4px"
                            onclick="deleteGalleryAjax(<?= $gi['id'] ?>, this)">×</button>
                </div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
            <div class="form-help">Pilih beberapa foto sekaligus</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title">Kategori Produk</div></div>
        <div class="card-body">
            <div id="kategori-list-wrap" style="max-height:280px;overflow-y:auto;margin-bottom:12px">
              <?php 
              // Build tree: parents first, children indented
              $parents = array_filter($all_kategori, fn($k) => empty($k['parent_id']));
              $children_by_parent = [];
              foreach ($all_kategori as $k) {
                  if (!empty($k['parent_id'])) $children_by_parent[$k['parent_id']][] = $k;
              }
              if (empty($all_kategori)): ?>
                <p class="text-sm text-muted" id="no-kat-msg" style="margin:0">Belum ada kategori. Tambah di bawah <?= icon('arrow-down', 16) ?></p>
              <?php else: 
                foreach ($parents as $kat): ?>
                  <div class="form-check mb-8" data-kat-id="<?= $kat['id'] ?>">
                    <input type="checkbox" name="kategori[]" id="pkat-<?= $kat['id'] ?>" value="<?= $kat['id'] ?>"
                           <?= in_array($kat['id'], $edit_kategori) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="pkat-<?= $kat['id'] ?>" style="font-weight:600"><?= htmlspecialchars($kat['nama']) ?></label>
                  </div>
                  <?php foreach (($children_by_parent[$kat['id']] ?? []) as $child): ?>
                    <div class="form-check mb-8" data-kat-id="<?= $child['id'] ?>" style="padding-left:22px">
                      <input type="checkbox" name="kategori[]" id="pkat-<?= $child['id'] ?>" value="<?= $child['id'] ?>"
                             <?= in_array($child['id'], $edit_kategori) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="pkat-<?= $child['id'] ?>" style="color:var(--text-muted)"><?= icon('corner-down-right', 16) ?> <?= htmlspecialchars($child['nama']) ?></label>
                    </div>
                  <?php endforeach; ?>
                <?php endforeach;
              endif; ?>
            </div>
            
            <!-- Inline create (AJAX, NO nested form) -->
            <div style="border-top:1px solid var(--border);padding-top:12px;margin-top:4px">
              <div id="new-kat-form" style="display:none">
                <div class="form-group mb-8">
                  <input type="text" id="new-kat-input" placeholder="Nama kategori baru" 
                         style="width:100%;padding:9px 11px;font-size:13px;border:1px solid var(--border-2);border-radius:7px">
                </div>
                <div class="form-group mb-8">
                  <select id="new-kat-parent" style="width:100%;padding:9px 11px;font-size:13px;border:1px solid var(--border-2);border-radius:7px">
                    <option value="">— Kategori Induk (opsional) —</option>
                    <?php foreach ($parents as $kat): ?>
                      <option value="<?= $kat['id'] ?>"><?= htmlspecialchars($kat['nama']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div style="display:flex;gap:6px">
                  <button type="button" onclick="saveKategoriAjax()" class="btn btn-primary btn-sm" id="save-kat-btn" style="flex:1"><?= icon('check', 16) ?> Simpan Kategori</button>
                  <button type="button" onclick="document.getElementById('new-kat-form').style.display='none';document.getElementById('add-kat-trigger').style.display='block'" class="btn btn-secondary btn-sm">Batal</button>
                </div>
                <div id="kat-feedback" style="font-size:12px;margin-top:6px"></div>
              </div>
              <button type="button" id="add-kat-trigger"
                      onclick="document.getElementById('new-kat-form').style.display='block';this.style.display='none';document.getElementById('new-kat-input').focus()" 
                      class="btn btn-secondary btn-sm btn-block">+ Tambah Kategori Baru</button>
            </div>
        </div>
    </div>
    
    <script>
      const KAT_CSRF = '<?= $csrf ?? generate_csrf() ?>';
      const KAT_AJAX_URL = '<?= admin_url('?page=produk') ?>';
      
      function saveKategoriAjax() {
        const input = document.getElementById('new-kat-input');
        const parent = document.getElementById('new-kat-parent');
        const btn = document.getElementById('save-kat-btn');
        const feedback = document.getElementById('kat-feedback');
        const name = input.value.trim();
        
        if (!name) { input.focus(); feedback.innerHTML = '<span style="color:#dc2626">Nama wajib diisi</span>'; return; }
        
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
        feedback.innerHTML = '';
        
        const fd = new FormData();
        fd.append('csrf_token', KAT_CSRF);
        fd.append('nama', name);
        fd.append('parent_id', parent.value || '0');
        
        fetch(KAT_AJAX_URL + '&ajax=create_kategori', { method: 'POST', body: fd })
          .then(r => r.json())
          .then(data => {
            btn.disabled = false;
            btn.textContent = 'Simpan Kategori';
            if (data.ok) {
              // Add checkbox to list, auto-checked
              const wrap = document.getElementById('kategori-list-wrap');
              const noMsg = document.getElementById('no-kat-msg');
              if (noMsg) noMsg.remove();
              const k = data.kategori;
              const div = document.createElement('div');
              div.className = 'form-check mb-8';
              div.setAttribute('data-kat-id', k.id);
              if (k.parent_id) div.style.paddingLeft = '22px';
              const prefix = k.parent_id ? '' : '';
              const weight = k.parent_id ? 'color:var(--text-muted)' : 'font-weight:600';
              div.innerHTML = '<input type="checkbox" name="kategori[]" id="pkat-'+k.id+'" value="'+k.id+'" checked>' +
                              '<label class="form-check-label" for="pkat-'+k.id+'" style="'+weight+'">'+prefix+escapeHtml(k.nama)+'</label>';
              wrap.appendChild(div);
              
              // Add to parent dropdown if top-level
              if (!k.parent_id) {
                const opt = document.createElement('option');
                opt.value = k.id; opt.textContent = k.nama;
                document.getElementById('new-kat-parent').appendChild(opt);
              }
              
              // Reset form
              input.value = '';
              parent.value = '';
              feedback.innerHTML = '<span style="color:#16a34a">"'+escapeHtml(k.nama)+'" ditambahkan & otomatis dicentang</span>';
              setTimeout(() => { feedback.innerHTML = ''; }, 3000);
              input.focus();
            } else {
              feedback.innerHTML = '<span style="color:#dc2626">' + escapeHtml(data.error || 'Gagal') + '</span>';
            }
          })
          .catch(err => {
            btn.disabled = false;
            btn.textContent = 'Simpan Kategori';
            feedback.innerHTML = '<span style="color:#dc2626">Error koneksi: ' + escapeHtml(err.message) + '</span>';
          });
      }
      
      function escapeHtml(s) {
        const d = document.createElement('div'); d.textContent = s; return d.innerHTML;
      }
      
      document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('new-kat-input');
        if (input) input.addEventListener('keypress', e => {
          if (e.key === 'Enter') { e.preventDefault(); saveKategoriAjax(); }
        });
      });
      
      function deleteGalleryAjax(galleryId, btn) {
        if (!confirm('Hapus foto ini?')) return;
        const fd = new FormData();
        fd.append('csrf_token', KAT_CSRF);
        fd.append('gallery_id', galleryId);
        fetch(KAT_AJAX_URL + '&ajax=delete_gallery', { method: 'POST', body: fd })
          .then(r => r.json())
          .then(data => {
            if (data.ok) {
              const item = document.querySelector('[data-gallery-item="'+galleryId+'"]');
              if (item) item.remove();
            } else {
              alert('Gagal hapus: ' + (data.error || 'unknown'));
            }
          })
          .catch(err => alert('Error: ' + err.message));
      }
    </script>
</div>
</div>
</form>

<script>
// Auto-slug
const namaInput = document.getElementById('nama-input');
const slugInput = document.getElementById('slug-input');
function makeSlug(s) { return s.toLowerCase().replace(/[^\w\s-]/g,'').replace(/\s+/g,'-').replace(/-+/g,'-').trim(); }
namaInput.addEventListener('input', () => { if (!slugInput.dataset.m) slugInput.value = makeSlug(namaInput.value); });
slugInput.addEventListener('input', () => { slugInput.dataset.m = '1'; });

function addMpRow() {
    const plats = <?= json_encode($mp_list) ?>;
    const div = document.createElement('div');
    div.className = 'mp-row';
    div.style.cssText = 'display:flex;gap:10px;margin-bottom:10px;align-items:center';
    div.innerHTML = `<select name="mp_platform[]" class="form-control" style="flex:0 0 140px">
        <option value="">— Platform —</option>
        ${plats.map(p=>`<option>${p}</option>`).join('')}
    </select>
    <input type="url" name="mp_url[]" class="form-control" placeholder="https://...">
    <button type="button" onclick="this.closest('.mp-row').remove()" class="btn btn-xs btn-danger" style="flex-shrink:0">×</button>`;
    document.getElementById('mp-list').appendChild(div);
}
</script>

<?php else: // LIST ?>

<div class="page-header">
    <div class="page-title"><?= icon('product', 16) ?> Produk
        <small><?= number_format($total) ?> produk total</small>
    </div>
    <a href="<?= admin_url('?page=produk&action=create') ?>" class="btn btn-primary">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Tambah Produk
    </a>
</div>

<div class="filter-bar">
    <form method="GET" style="display:contents">
        <input type="hidden" name="page" value="produk">
        <div class="search-input-wrap">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari produk...">
        </div>
        <select name="kat" class="form-control" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            <?php foreach ($all_kategori as $kat): ?>
            <option value="<?= $kat['id'] ?>" <?= $filter_kat === $kat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($kat['nama']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th style="width:60px">Foto</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th style="width:110px">Aksi</th></tr>
            </thead>
            <tbody>
            <?php if (empty($products)): ?>
            <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><?= icon('product', 40) ?></div><div class="empty-title">Belum ada produk</div></div></td></tr>
            <?php else: ?>
            <?php foreach ($products as $p): ?>
            <?php $kats = $db->fetchAll("SELECT pk.nama FROM produk_kategori pk JOIN produk_kategori_rel pkr ON pk.id=pkr.kategori_id WHERE pkr.produk_id=?",[$p['id']]); ?>
            <tr>
                <td>
                    <?php if ($p['gambar_utama']): ?>
                    <img src="<?= uploads_url($p['gambar_utama']) ?>" class="table-img">
                    <?php else: ?>
                    <div style="width:48px;height:40px;background:var(--surface3);border-radius:6px;display:flex;align-items:center;justify-content:center"><?= icon('product', 16) ?></div>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="font-weight:500"><?= htmlspecialchars($p['nama']) ?></div>
                    <?php if ($p['badge']): ?><span class="badge badge-accent"><?= htmlspecialchars($p['badge']) ?></span><?php endif; ?>
                </td>
                <td><?php foreach ($kats as $k): ?><span class="badge badge-muted"><?= htmlspecialchars($k['nama']) ?></span><?php endforeach; ?></td>
                <td class="text-sm">
                    <?= $p['harga'] ? format_rupiah($p['harga']) : '<span class="text-muted">Hubungi</span>' ?>
                    <?php if ($p['harga_coret']): ?>
                    <div class="text-xs text-muted" style="text-decoration:line-through"><?= format_rupiah($p['harga_coret']) ?></div>
                    <?php endif; ?>
                </td>
                <td class="text-sm"><?= $p['stok'] < 0 ? '∞' : $p['stok'] ?></td>
                <td>
                    <?php if ($p['status'] === 'aktif'): ?>
                    <span class="badge badge-success">Aktif</span>
                    <?php else: ?>
                    <span class="badge badge-danger">Nonaktif</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="display:flex;gap:6px">
                        <a href="<?= admin_url('?page=produk&action=edit&id='.$p['id']) ?>" class="btn btn-xs btn-secondary"><?= icon('pencil', 16) ?></a>
                        <a href="<?= url('/produk/'.$p['slug']) ?>" target="_blank" class="btn btn-xs btn-secondary"><?= icon('link', 16) ?></a>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                            <input type="hidden" name="_action" value="delete">
                            <input type="hidden" name="del_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-xs btn-danger" data-confirm="Hapus produk '<?= htmlspecialchars(addslashes($p['nama'])) ?>'?"><?= icon('trash', 16) ?></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $total_pages = ceil($total/$per_page); if ($total_pages > 1): ?>
<div class="pagination">
    <?php if ($cp > 1): ?><a href="?page=produk&p=<?=$cp-1?>&search=<?=urlencode($search)?>&kat=<?=$filter_kat?>" class="page-link"><?= icon('arrow-left', 16) ?></a><?php endif; ?>
    <?php for ($i=max(1,$cp-2);$i<=min($total_pages,$cp+2);$i++): ?>
    <a href="?page=produk&p=<?=$i?>&search=<?=urlencode($search)?>&kat=<?=$filter_kat?>" class="page-link <?=$i===$cp?'active':''?>"><?=$i?></a>
    <?php endfor; ?>
    <?php if ($cp < $total_pages): ?><a href="?page=produk&p=<?=$cp+1?>&search=<?=urlencode($search)?>&kat=<?=$filter_kat?>" class="page-link"><?= icon('arrow-right', 16) ?></a><?php endif; ?>
</div>
<?php endif; ?>
<?php endif; ?>
