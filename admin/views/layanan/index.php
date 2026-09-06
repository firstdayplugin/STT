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
            $data = [
                'nama'             => trim($_POST['nama'] ?? ''),
                'slug'             => trim($_POST['slug'] ?? '') ?: slug($_POST['nama'] ?? ''),
                'deskripsi_pendek' => trim($_POST['deskripsi_pendek'] ?? ''),
                'deskripsi'        => trim($_POST['deskripsi'] ?? ''),
                'tagline'          => trim($_POST['tagline'] ?? ''),
                'icon'             => trim($_POST['icon'] ?? ''),
                'section_types_title' => trim($_POST['section_types_title'] ?? ''),
                'section_types_desc'  => trim($_POST['section_types_desc'] ?? ''),
                'section_gallery_title'=> trim($_POST['section_gallery_title'] ?? ''),
                'section_gallery_desc' => trim($_POST['section_gallery_desc'] ?? ''),
                'consult_title'    => trim($_POST['consult_title'] ?? ''),
                'consult_desc'     => trim($_POST['consult_desc'] ?? ''),
                'urutan'           => (int)($_POST['urutan'] ?? 0),
                'is_active'        => isset($_POST['is_active']) ? 1 : 0,
                'meta_title'       => trim($_POST['meta_title'] ?? ''),
                'meta_description' => trim($_POST['meta_description'] ?? ''),
            ];
            
            // Image uploads
            if (!empty($_FILES['gambar']['name'])) {
                $upl_path = upload_image($_FILES['gambar'], 'layanan');
            if ($upl_path) $data['gambar'] = $upl_path;
            else set_flash('error', 'Upload gambar gagal. Cek ukuran/format file.');
            }
            if (!empty($_FILES['gambar_footer']['name'])) {
                $upl_path = upload_image($_FILES['gambar_footer'], 'layanan');
            if ($upl_path) $data['gambar_footer'] = $upl_path;
            else set_flash('error', 'Upload gambar_footer gagal. Cek ukuran/format file.');
            }
            
            if (empty($data['nama'])) {
                set_flash('error', 'Nama layanan wajib diisi.');
            } else {
                // Filter to only existing columns (safety if DB not migrated)
                $data_safe = $db->filterColumns('layanan', $data);
                try {
                    if ($act === 'create') {
                        $cols = array_keys($data_safe);
                        $placeholders = implode(',', array_fill(0, count($cols), '?'));
                        $sql = "INSERT INTO layanan (" . implode(',', $cols) . ") VALUES ($placeholders)";
                        $db->execute($sql, array_values($data_safe));
                        $id = $db->lastInsertId();
                        log_activity('create', 'Layanan: '.$data_safe['nama']);
                        set_flash('success', 'Layanan "'.$data_safe['nama'].'" berhasil ditambahkan.');
                    } else {
                        $set_parts = [];
                        $params = [];
                        foreach ($data_safe as $k => $v) {
                            $set_parts[] = "$k = ?";
                            $params[] = $v;
                        }
                        $params[] = $id;
                        $db->execute("UPDATE layanan SET " . implode(',', $set_parts) . " WHERE id = ?", $params);
                        log_activity('update', 'Layanan: '.$data_safe['nama']);
                        set_flash('success', 'Layanan "'.$data_safe['nama'].'" berhasil diupdate.');
                    }
                } catch (Throwable $e) {
                    set_flash('error', 'Gagal menyimpan: ' . $e->getMessage());
                    redirect(admin_url('?page=layanan'));
                }
                
                // Sub-services (with manual per-sub photo)
                if (!empty($_POST['sub_layanan'])) {
                    $sub_has_gambar = in_array('gambar', $db->getColumns('layanan_sub'));
                    $db->execute("DELETE FROM layanan_sub WHERE layanan_id = ?", [$id]);
                    $sub_urutan = 0;
                    foreach ($_POST['sub_layanan'] as $i => $sub) {
                        $sub_nama = trim($sub['nama'] ?? '');
                        if (!$sub_nama) continue;
                        
                        // Determine photo: new upload > old photo > none
                        $sub_gambar = trim($sub['gambar_lama'] ?? '');
                        $file_key = 'sub_gambar_' . $i;
                        if (!empty($_FILES[$file_key]['name'])) {
                            $uploaded = upload_image($_FILES[$file_key], 'layanan');
                            if ($uploaded) $sub_gambar = $uploaded;
                        }
                        
                        if ($sub_has_gambar) {
                            $db->execute(
                                "INSERT INTO layanan_sub (layanan_id, nama, deskripsi, icon, gambar, urutan) VALUES (?,?,?,?,?,?)",
                                [$id, $sub_nama, trim($sub['deskripsi'] ?? ''), trim($sub['icon'] ?? ''), $sub_gambar ?: null, $sub_urutan++]
                            );
                        } else {
                            $db->execute(
                                "INSERT INTO layanan_sub (layanan_id, nama, deskripsi, icon, urutan) VALUES (?,?,?,?,?)",
                                [$id, $sub_nama, trim($sub['deskripsi'] ?? ''), trim($sub['icon'] ?? ''), $sub_urutan++]
                            );
                        }
                    }
                }
                
                redirect(admin_url('?page=layanan&action=edit&id='.$id));
            }
        } elseif ($act === 'delete' && $id > 0) {
            $db->execute("DELETE FROM layanan_sub WHERE layanan_id = ?", [$id]);
            $db->execute("DELETE FROM layanan WHERE id = ?", [$id]);
            log_activity('delete', 'Layanan ID: '.$id);
            set_flash('success', 'Layanan berhasil dihapus.');
            redirect(admin_url('?page=layanan'));
        }
    }
}

try {
    $items = $db->fetchAll("SELECT * FROM layanan ORDER BY urutan ASC, id DESC");
} catch (Throwable $e) {
    $items = [];
    set_flash('error', 'Database belum di-update. Jalankan database.sql terbaru di phpMyAdmin. Error: ' . $e->getMessage());
}
$edit_item = null;
$edit_subs = [];
if ($action === 'edit' && $id > 0) {
    try {
        $edit_item = $db->fetchOne("SELECT * FROM layanan WHERE id = ?", [$id]);
        $edit_subs = $db->fetchAll("SELECT * FROM layanan_sub WHERE layanan_id = ? ORDER BY urutan ASC", [$id]);
    } catch (Throwable $e) {
        set_flash('error', 'Error: ' . $e->getMessage());
        $edit_item = null;
    }
    if (!$edit_item) {
        set_flash('error', "Layanan dengan ID $id tidak ditemukan atau database belum diupdate.");
        redirect(admin_url('?page=layanan'));
    }
}
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1><?= icon('palette', 16) ?> Layanan</h1>
    <div class="page-header-sub">Kelola layanan yang ditawarkan + semua elemen di halaman detail</div>
  </div>
  <?php if ($action === 'list'): ?>
  <div class="page-actions">
    <a href="<?= admin_url('?page=layanan&action=create') ?>" class="btn btn-primary">+ Tambah Layanan</a>
  </div>
  <?php endif; ?>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
    
    <!-- TAB NAVIGATION -->
    <div class="tabs">
      <button type="button" class="tab active" onclick="showTab(this, 'tab-basic')">Info Dasar</button>
      <button type="button" class="tab" onclick="showTab(this, 'tab-types')">Section Types</button>
      <button type="button" class="tab" onclick="showTab(this, 'tab-gallery')">Section Gallery</button>
      <button type="button" class="tab" onclick="showTab(this, 'tab-consult')">Section Consult</button>
      <button type="button" class="tab" onclick="showTab(this, 'tab-subs')">Sub-Layanan</button>
      <button type="button" class="tab" onclick="showTab(this, 'tab-seo')">SEO</button>
    </div>
    
    <!-- TAB 1: BASIC INFO -->
    <div class="tab-content active" id="tab-basic">
      <div class="card">
        <div class="card-header"><div class="card-title"><?= icon('block', 16) ?> Informasi Dasar Layanan</div></div>
        
        <div class="form-row">
          <div class="form-group">
            <label>Nama Layanan *</label>
            <input type="text" name="nama" required value="<?= htmlspecialchars($edit_item['nama'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Slug (URL) *</label>
            <input type="text" name="slug" required pattern="[a-z0-9-]+" value="<?= htmlspecialchars($edit_item['slug'] ?? '') ?>" placeholder="neon-box">
            <div class="form-hint">Akan jadi URL: /layanan/<strong id="slug-preview"><?= htmlspecialchars($edit_item['slug'] ?? 'slug') ?></strong></div>
          </div>
        </div>
        
        <div class="form-group">
          <label>Tagline (Heading kecil di atas section detail)</label>
          <input type="text" name="tagline" value="<?= htmlspecialchars($edit_item['tagline'] ?? '') ?>" placeholder="Spesialis dalam pembuatan Neon Box berkualitas">
        </div>
        
        <div class="form-group">
          <label>Deskripsi Pendek (tampil di card layanan home)</label>
          <textarea name="deskripsi_pendek" rows="2" class="wysiwyg"><?= htmlspecialchars($edit_item['deskripsi_pendek'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
          <label>Deskripsi Lengkap</label>
          <textarea name="deskripsi" rows="4" class="wysiwyg"><?= htmlspecialchars($edit_item['deskripsi'] ?? '') ?></textarea>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label><?= icon('image', 16) ?> Gambar Utama (header hero halaman detail)</label>
            <?php if (!empty($edit_item['gambar'])): ?>
              <div class="img-upload-row" style="margin-bottom:8px">
                <div class="img-preview"><img src="<?= uploads_url($edit_item['gambar']) ?>"></div>
                <span class="text-muted" style="font-size:12px">Gambar saat ini</span>
              </div>
            <?php endif; ?>
            <input type="file" name="gambar" accept="image/*">
          </div>
          <div class="form-group">
            <label><?= icon('image', 16) ?> Gambar Footer (tampil di bagian bawah)</label>
            <?php if (!empty($edit_item['gambar_footer'])): ?>
              <div class="img-upload-row" style="margin-bottom:8px">
                <div class="img-preview"><img src="<?= uploads_url($edit_item['gambar_footer']) ?>"></div>
                <span class="text-muted" style="font-size:12px">Gambar saat ini</span>
              </div>
            <?php endif; ?>
            <input type="file" name="gambar_footer" accept="image/*">
          </div>
        </div>
        
        <div class="form-row cols-3">
          <div class="form-group">
            <label>Icon (emoji)</label>
            <input type="text" name="icon" value="<?= htmlspecialchars($edit_item['icon'] ?? '') ?>" placeholder="">
          </div>
          <div class="form-group">
            <label>Urutan</label>
            <input type="number" name="urutan" value="<?= $edit_item['urutan'] ?? 0 ?>">
          </div>
          <div class="form-group">
            <label class="checkbox-label" style="margin-top:22px">
              <input type="checkbox" name="is_active" <?= ($edit_item['is_active'] ?? 1) ? 'checked' : '' ?>>
              Aktif (tampilkan)
            </label>
          </div>
        </div>
      </div>
    </div>
    
    <!-- TAB 2: SECTION TYPES (sub-layanan) -->
    <div class="tab-content" id="tab-types">
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title"><?= icon('product', 16) ?> Section "Layanan [Nama] Berkualitas"</div>
            <div class="card-subtitle">Heading section dimana sub-layanan ditampilkan</div>
          </div>
        </div>
        <div class="form-group">
          <label>Judul Section</label>
          <input type="text" name="section_types_title" 
                 value="<?= htmlspecialchars($edit_item['section_types_title'] ?? '') ?>"
                 placeholder="Layanan Neon Box Berkualitas & Terjangkau">
        </div>
        <div class="form-group">
          <label>Deskripsi Section</label>
          <textarea name="section_types_desc" rows="2" class="wysiwyg"><?= htmlspecialchars($edit_item['section_types_desc'] ?? '') ?></textarea>
        </div>
      </div>
    </div>
    
    <!-- TAB 3: SECTION GALLERY -->
    <div class="tab-content" id="tab-gallery">
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title"><?= icon('image', 16) ?> Section "Galeri Proyek"</div>
            <div class="card-subtitle">Heading section galeri proyek di halaman detail</div>
          </div>
        </div>
        <div class="form-group">
          <label>Judul Section Gallery</label>
          <input type="text" name="section_gallery_title" 
                 value="<?= htmlspecialchars($edit_item['section_gallery_title'] ?? '') ?>"
                 placeholder="Galeri Proyek Neon Box">
        </div>
        <div class="form-group">
          <label>Deskripsi Section Gallery</label>
          <textarea name="section_gallery_desc" rows="2" class="wysiwyg"><?= htmlspecialchars($edit_item['section_gallery_desc'] ?? '') ?></textarea>
        </div>
        <div class="alert alert-info">
          ℹ️ Foto-foto galeri diatur di menu <a href="<?= admin_url('?page=gallery') ?>"><strong>Galeri</strong></a> dengan kategori sesuai slug layanan ini.
        </div>
      </div>
    </div>
    
    <!-- TAB 4: SECTION CONSULT -->
    <div class="tab-content" id="tab-consult">
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title"><?= icon('briefcase', 16) ?> Section "Consult With Us"</div>
            <div class="card-subtitle">Section ajakan konsultasi di bawah halaman detail</div>
          </div>
        </div>
        <div class="form-group">
          <label>Judul Section</label>
          <input type="text" name="consult_title"
                 value="<?= htmlspecialchars($edit_item['consult_title'] ?? '') ?>"
                 placeholder="Maksimalkan Budget, Optimalkan Hasil">
        </div>
        <div class="form-group">
          <label>Deskripsi Section</label>
          <textarea name="consult_desc" rows="3" class="wysiwyg"><?= htmlspecialchars($edit_item['consult_desc'] ?? '') ?></textarea>
        </div>
      </div>
    </div>
    
    <!-- TAB 5: SUB-LAYANAN (Type variations) -->
    <div class="tab-content" id="tab-subs">
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title"><?= icon('wrench', 16) ?> Variasi / Tipe Layanan</div>
            <div class="card-subtitle">Tampil sebagai grid kartu (contoh: Neon Box Kotak, Neon Box Bulat, Neon Box Custom)</div>
          </div>
          <button type="button" onclick="addSubItem()" class="btn btn-secondary btn-sm">+ Tambah Tipe</button>
        </div>
        <div id="subs-container">
          <?php if (!empty($edit_subs)): foreach ($edit_subs as $i => $sub): ?>
            <div class="sub-item" style="padding:14px;background:var(--surface-2);border-radius:10px;margin-bottom:12px">
              <div style="display:grid;grid-template-columns:130px 1fr;gap:14px;align-items:start">
                <!-- Photo column -->
                <div>
                  <div class="sub-photo-preview" style="width:130px;height:130px;border-radius:8px;border:1px solid var(--border);overflow:hidden;background:var(--bg-cream-2);display:flex;align-items:center;justify-content:center;margin-bottom:6px">
                    <?php if (!empty($sub['gambar'])): ?>
                      <img src="<?= uploads_url($sub['gambar']) ?>" style="width:100%;height:100%;object-fit:cover">
                    <?php else: ?>
                      <span style="opacity:0.35"><?= trim((string)($sub['icon'] ?? '')) !== '' ? htmlspecialchars($sub['icon']) : icon('service', 32) ?></span>
                    <?php endif; ?>
                  </div>
                  <input type="hidden" name="sub_layanan[<?= $i ?>][gambar_lama]" value="<?= htmlspecialchars($sub['gambar'] ?? '') ?>">
                  <input type="file" name="sub_gambar_<?= $i ?>" accept="image/*" style="font-size:11px;width:130px"
                         onchange="previewSubPhoto(this)">
                  <div style="font-size:10px;color:var(--text-muted);margin-top:2px">Foto manual sub-layanan</div>
                </div>
                <!-- Fields column -->
                <div style="display:grid;gap:8px">
                  <div style="display:grid;grid-template-columns:70px 1fr;gap:8px">
                    <input type="text" name="sub_layanan[<?= $i ?>][icon]" value="<?= htmlspecialchars($sub['icon']) ?>" placeholder="">
                    <input type="text" name="sub_layanan[<?= $i ?>][nama]" value="<?= htmlspecialchars($sub['nama']) ?>" placeholder="Nama tipe (cth: Neon Box Kotak)">
                  </div>
                  <textarea class="no-wysiwyg" name="sub_layanan[<?= $i ?>][deskripsi]" rows="3" placeholder="Deskripsi"><?= htmlspecialchars($sub['deskripsi']) ?></textarea>
                  <button type="button" class="btn btn-danger btn-sm" style="justify-self:start" onclick="this.closest('.sub-item').remove()"><?= icon('trash', 16) ?> Hapus tipe ini</button>
                </div>
              </div>
            </div>
          <?php endforeach; endif; ?>
        </div>
        <?php if (empty($edit_subs)): ?>
          <div class="empty-state" style="padding:20px">
            <div style="font-size:13px;color:var(--text-muted)">Belum ada tipe. Klik tombol "+ Tambah Tipe" untuk menambahkan.</div>
          </div>
        <?php endif; ?>
      </div>
    </div>
    
    <!-- TAB 6: SEO -->
    <div class="tab-content" id="tab-seo">
      <div class="card">
        <div class="card-header"><div class="card-title"><?= icon('search', 16) ?> SEO Layanan</div></div>
        <div class="form-group">
          <label>Meta Title</label>
          <input type="text" name="meta_title" value="<?= htmlspecialchars($edit_item['meta_title'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Meta Description</label>
          <textarea class="no-wysiwyg" name="meta_description" rows="3"><?= htmlspecialchars($edit_item['meta_description'] ?? '') ?></textarea>
        </div>
      </div>
    </div>
    
    <!-- SAVE BUTTON (sticky bottom) -->
    <div style="position:sticky;bottom:0;background:white;padding:16px 0;border-top:1px solid var(--border);margin-top:16px;display:flex;justify-content:space-between;align-items:center;z-index:10">
      <a href="<?= admin_url('?page=layanan') ?>" class="btn btn-secondary"><?= icon('arrow-left', 16) ?> Kembali</a>
      <button type="submit" class="btn btn-primary btn-lg"><?= icon('save', 16) ?> Simpan Layanan</button>
    </div>
  </form>

<?php else: ?>
  
  <?php if (empty($items)): ?>
    <div class="card"><div class="empty-state">
      <div class="empty-state-icon"><?= icon('palette', 16) ?></div>
      <div>Belum ada layanan.</div>
      <a href="<?= admin_url('?page=layanan&action=create') ?>" class="btn btn-primary mt-2">+ Tambah Layanan Pertama</a>
    </div></div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:80px">Gambar</th>
            <th>Nama</th>
            <th>Slug</th>
            <th style="width:80px">Urutan</th>
            <th style="width:80px">Status</th>
            <th style="width:160px">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $l): ?>
          <tr>
            <td>
              <?php if ($l['gambar']): ?>
                <div class="img-preview" style="width:50px;height:50px"><img src="<?= uploads_url($l['gambar']) ?>"></div>
              <?php else: ?>
                <div class="img-preview" style="width:50px;height:50px"><?= trim((string)($l['icon'] ?? '')) !== '' ? htmlspecialchars($l['icon']) : icon('service', 26) ?></div>
              <?php endif; ?>
            </td>
            <td>
              <div style="font-weight:600"><?= htmlspecialchars($l['nama']) ?></div>
              <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars(excerpt($l['deskripsi_pendek'] ?: '', 60)) ?></div>
            </td>
            <td><code style="background:var(--surface-2);padding:2px 8px;border-radius:4px;font-size:11px">/layanan/<?= htmlspecialchars($l['slug']) ?></code></td>
            <td><?= $l['urutan'] ?></td>
            <td><?= $l['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
            <td>
              <div class="table-actions">
                <a href="<?= url('/layanan/'.$l['slug']) ?>" target="_blank" class="btn btn-ghost btn-sm"><?= icon('eye', 16) ?></a>
                <a href="<?= admin_url('?page=layanan&action=edit&id='.$l['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
                <form method="POST" action="<?= admin_url('?page=layanan&action=delete&id='.$l['id']) ?>" style="display:inline" onsubmit="return confirm('Hapus layanan <?= htmlspecialchars($l['nama']) ?>?')">
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

<script>
function showTab(btn, id) {
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById(id).classList.add('active');
}

function addSubItem() {
  const container = document.getElementById('subs-container');
  // Use a high unique index based on timestamp to avoid collision with existing items
  const idx = 'new' + Date.now();
  const html = `
    <div class="sub-item" style="padding:14px;background:var(--surface-2);border-radius:10px;margin-bottom:12px">
      <div style="display:grid;grid-template-columns:130px 1fr;gap:14px;align-items:start">
        <div>
          <div class="sub-photo-preview" style="width:130px;height:130px;border-radius:8px;border:1px solid var(--border);overflow:hidden;background:var(--bg-cream-2);display:flex;align-items:center;justify-content:center;margin-bottom:6px">
            <span style="font-size:36px;opacity:0.35"></span>
          </div>
          <input type="hidden" name="sub_layanan[${idx}][gambar_lama]" value="">
          <input type="file" name="sub_gambar_${idx}" accept="image/*" style="font-size:11px;width:130px" onchange="previewSubPhoto(this)">
          <div style="font-size:10px;color:var(--text-muted);margin-top:2px">Foto manual sub-layanan</div>
        </div>
        <div style="display:grid;gap:8px">
          <div style="display:grid;grid-template-columns:70px 1fr;gap:8px">
            <input type="text" name="sub_layanan[${idx}][icon]" placeholder="">
            <input type="text" name="sub_layanan[${idx}][nama]" placeholder="Nama tipe (cth: Neon Box Kotak)">
          </div>
          <textarea class="no-wysiwyg" name="sub_layanan[${idx}][deskripsi]" rows="3" placeholder="Deskripsi"></textarea>
          <button type="button" class="btn btn-danger btn-sm" style="justify-self:start" onclick="this.closest('.sub-item').remove()">Hapus tipe ini</button>
        </div>
      </div>
    </div>`;
  container.insertAdjacentHTML('beforeend', html);
}

function previewSubPhoto(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  const preview = input.closest('.sub-item').querySelector('.sub-photo-preview');
  reader.onload = function(e) {
    preview.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover">';
  };
  reader.readAsDataURL(input.files[0]);
}

// Live slug update
document.querySelector('input[name="nama"]')?.addEventListener('input', function(e) {
  const slugInput = document.querySelector('input[name="slug"]');
  if (slugInput && !slugInput.dataset.touched) {
    slugInput.value = e.target.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    document.getElementById('slug-preview').textContent = slugInput.value;
  }
});
document.querySelector('input[name="slug"]')?.addEventListener('input', function(e) {
  e.target.dataset.touched = '1';
  document.getElementById('slug-preview').textContent = e.target.value;
});
</script>
