<?php
$page_title = 'Manajemen Galeri';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error','Token invalid.'); redirect(admin_url('?page=gallery')); }
    $a = $_POST['_action'] ?? '';

    if ($a === 'delete') {
        $del_id = (int)($_POST['del_id'] ?? 0);
        if ($del_id) { $db->execute("DELETE FROM gallery WHERE id=?",[$del_id]); set_flash('success','Foto dihapus.'); }
        redirect(admin_url('?page=gallery'));
    }

    if ($a === 'save_kategori') {
        $kn = trim($_POST['kategori_nama'] ?? '');
        if ($kn) { $db->insert('gallery_kategori',['nama'=>$kn,'slug'=>make_slug($kn)]); set_flash('success','Kategori ditambahkan.'); }
        redirect(admin_url('?page=gallery'));
    }

    if ($a === 'delete_kategori') {
        $del_kat = (int)($_POST['kategori_id'] ?? 0);
        if ($del_kat > 0) {
            // Detach foto dari kategori ini (set kategori_id NULL), lalu hapus kategori
            $used = $db->fetchOne("SELECT COUNT(*) c FROM gallery WHERE kategori_id=?", [$del_kat])['c'] ?? 0;
            $db->execute("UPDATE gallery SET kategori_id=NULL WHERE kategori_id=?", [$del_kat]);
            $db->execute("DELETE FROM gallery_kategori WHERE id=?", [$del_kat]);
            log_activity('delete', 'Kategori galeri ID ' . $del_kat);
            $msg = '🗑️ Kategori berhasil dihapus.';
            if ($used > 0) $msg .= " $used foto sekarang tidak berkategori (foto tidak terhapus).";
            set_flash('success', $msg);
        }
        redirect(admin_url('?page=gallery'));
    }

    if ($a === 'upload') {
        $kategori_id = (int)($_POST['kategori_id'] ?? 0) ?: null;
        $judul_base  = trim($_POST['judul'] ?? '');
        $deskripsi   = trim($_POST['deskripsi'] ?? '');
        $project     = trim($_POST['project'] ?? '');
        $uploaded_count = 0;

        if (!empty($_FILES['gambar']['name'][0])) {
            foreach ($_FILES['gambar']['name'] as $fi => $fname) {
                if (!$fname || $_FILES['gambar']['error'][$fi]) continue;
                $fdata = [
                    'name'=>$fname,'type'=>$_FILES['gambar']['type'][$fi],
                    'tmp_name'=>$_FILES['gambar']['tmp_name'][$fi],
                    'error'=>$_FILES['gambar']['error'][$fi],'size'=>$_FILES['gambar']['size'][$fi]
                ];
                $up = upload_image($fdata,'gallery');
                if ($up) {
                    $db->insert('gallery',[
                        'gambar'      => $up,
                        'judul'       => $judul_base ?: pathinfo($fname,PATHINFO_FILENAME),
                        'deskripsi'   => $deskripsi,
                        'kategori_id' => $kategori_id,
                        'project'     => $project,
                        'urutan'      => 0,
                        'created_at'  => date('Y-m-d H:i:s'),
                    ]);
                    $uploaded_count++;
                }
            }
        }
        set_flash('success', "$uploaded_count foto berhasil diupload!");
        redirect(admin_url('?page=gallery'));
    }

    if ($a === 'edit_save') {
        $gid = (int)($_POST['id'] ?? 0);
        if ($gid) {
            $db->execute("UPDATE gallery SET judul=?,deskripsi=?,kategori_id=?,project=?,is_featured=? WHERE id=?", [
                trim($_POST['judul'] ?? ''),
                trim($_POST['deskripsi'] ?? ''),
                (int)($_POST['kategori_id'] ?? 0) ?: null,
                trim($_POST['project'] ?? ''),
                isset($_POST['is_featured']) ? 1 : 0,
                $gid
            ]);
            set_flash('success','Foto diperbarui.');
        }
        redirect(admin_url('?page=gallery'));
    }
}

// Edit single
$edit_item = null;
if ($action === 'edit' && $id) {
    $edit_item = $db->fetchOne("SELECT * FROM gallery WHERE id=?",[$id]);
}

$all_kat = $db->fetchAll("SELECT * FROM gallery_kategori ORDER BY nama");
$filter_kat = (int)($_GET['kat'] ?? 0);
$per_page = 20;
$cp = max(1,(int)($_GET['p'] ?? 1));
$offset = ($cp-1)*$per_page;

$where = '1=1'; $params = [];
if ($filter_kat) { $where .= ' AND kategori_id=?'; $params[] = $filter_kat; }

$total = $db->fetchOne("SELECT COUNT(*) as c FROM gallery WHERE $where",$params)['c'];
$items = $db->fetchAll("SELECT g.*, gk.nama as kat_nama FROM gallery g LEFT JOIN gallery_kategori gk ON g.kategori_id=gk.id WHERE $where ORDER BY g.created_at DESC LIMIT $per_page OFFSET $offset",$params);

$csrf = generate_csrf();
?>

<div class="page-header">
    <div class="page-title">🖼️ Galeri
        <small><?= number_format($total) ?> foto</small>
    </div>
</div>

<!-- Upload Area -->
<div class="card mb-24">
    <div class="card-header">
        <div class="card-title">📤 Upload Foto Baru</div>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="_action" value="upload">
            <div class="form-grid mb-16">
                <div class="form-group mb-0">
                    <label>Judul (opsional)</label>
                    <input type="text" name="judul" class="form-control" placeholder="Judul foto / project">
                </div>
                <div class="form-group mb-0">
                    <label>Kategori</label>
                    <select name="kategori_id" class="form-control">
                        <option value="">— Tanpa Kategori —</option>
                        <?php foreach ($all_kat as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label>Nama Project</label>
                    <input type="text" name="project" class="form-control" placeholder="Nama client / project">
                </div>
                <div class="form-group mb-0">
                    <label>Deskripsi</label>
                    <input type="text" name="deskripsi" class="form-control" placeholder="Deskripsi singkat">
                </div>
            </div>

            <!-- Dropzone -->
            <div class="upload-area" id="dropzone" onclick="document.getElementById('file-input').click()">
                <div class="upload-icon">📁</div>
                <div class="upload-text">Klik atau <strong>drag & drop</strong> foto di sini</div>
                <div class="upload-hint">JPG, PNG, WebP — Bisa pilih banyak sekaligus — Maks 5MB per file</div>
                <div id="file-count" style="margin-top:8px;color:var(--accent);font-weight:600"></div>
            </div>
            <input type="file" id="file-input" name="gambar[]" accept="image/*" multiple style="display:none">

            <!-- Preview -->
            <div id="preview-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:8px;margin-top:12px"></div>

            <div style="margin-top:16px;text-align:right">
                <button type="submit" class="btn btn-primary" id="upload-btn" disabled>
                    📤 Upload Foto
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Category -->
<div style="display:flex;gap:12px;align-items:center;margin-bottom:20px;flex-wrap:wrap">
    <form method="POST" style="display:flex;gap:10px;align-items:center">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="_action" value="save_kategori">
        <input type="text" name="kategori_nama" class="form-control" style="width:200px" placeholder="Nama kategori baru...">
        <button type="submit" class="btn btn-secondary btn-sm">+ Tambah Kategori</button>
    </form>

    <!-- Filter + delete kategori -->
    <div style="margin-left:auto;display:flex;gap:8px;flex-wrap:wrap">
        <a href="<?= admin_url('?page=gallery') ?>" class="btn btn-sm <?= !$filter_kat?'btn-primary':'btn-secondary' ?>">Semua</a>
        <?php foreach ($all_kat as $k): 
            $used_count = $db->fetchOne("SELECT COUNT(*) c FROM gallery WHERE kategori_id=?", [$k['id']])['c'] ?? 0;
            $confirm_msg = $used_count > 0
                ? "Hapus kategori '{$k['nama']}'? {$used_count} foto akan tidak berkategori (tidak terhapus)."
                : "Hapus kategori '{$k['nama']}'?";
        ?>
        <div class="gal-kat-pill" style="display:inline-flex;align-items:stretch;position:relative">
            <a href="<?= admin_url('?page=gallery&kat='.$k['id']) ?>" class="btn btn-sm <?= $filter_kat===$k['id']?'btn-primary':'btn-secondary' ?>" style="padding-right:8px">
                <?= htmlspecialchars($k['nama']) ?>
            </a>
            <form method="POST" style="display:inline" onsubmit="return confirm(<?= htmlspecialchars(json_encode($confirm_msg), ENT_QUOTES) ?>)">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="_action" value="delete_kategori">
                <input type="hidden" name="kategori_id" value="<?= $k['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger" style="padding:4px 8px;margin-left:2px;line-height:1;font-size:14px" title="Hapus kategori" aria-label="Hapus kategori <?= htmlspecialchars($k['nama']) ?>">×</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Gallery Grid -->
<?php if (empty($items)): ?>
<div class="card"><div class="empty-state">
    <div class="empty-icon">🖼️</div>
    <div class="empty-title">Belum ada foto di galeri</div>
    <div class="empty-text">Upload foto pertama menggunakan form di atas.</div>
</div></div>
<?php else: ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px">
<?php foreach ($items as $item): ?>
<div class="gallery-card" style="background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;transition:all 0.2s">
    <div style="position:relative;aspect-ratio:4/3;overflow:hidden">
        <img src="<?= uploads_url($item['gambar']) ?>" alt="<?= htmlspecialchars($item['judul']) ?>"
             style="width:100%;height:100%;object-fit:cover;transition:transform 0.3s"
             onmouseover="this.style.transform='scale(1.05)'"
             onmouseout="this.style.transform='scale(1)'">
        <?php if ($item['is_featured']): ?>
        <div style="position:absolute;top:8px;left:8px"><span class="badge badge-accent">⭐ Featured</span></div>
        <?php endif; ?>
        <div style="position:absolute;top:8px;right:8px;display:flex;gap:6px">
            <button onclick="openEdit(<?= $item['id'] ?>)" class="btn btn-xs btn-secondary" style="backdrop-filter:blur(8px)">✏️</button>
            <form method="POST" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="_action" value="delete">
                <input type="hidden" name="del_id" value="<?= $item['id'] ?>">
                <button type="submit" class="btn btn-xs btn-danger" data-confirm="Hapus foto ini?">🗑️</button>
            </form>
        </div>
    </div>
    <div style="padding:10px">
        <div style="font-size:13px;font-weight:500;truncate"><?= htmlspecialchars($item['judul'] ?: 'Tanpa Judul') ?></div>
        <?php if ($item['kat_nama']): ?>
        <span class="badge badge-muted" style="margin-top:4px"><?= htmlspecialchars($item['kat_nama']) ?></span>
        <?php endif; ?>
        <?php if ($item['project']): ?>
        <div class="text-xs text-muted" style="margin-top:4px">📁 <?= htmlspecialchars($item['project']) ?></div>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
</div>

<?php $total_pages=ceil($total/$per_page); if($total_pages>1): ?>
<div class="pagination mt-16">
    <?php if($cp>1): ?><a href="?page=gallery&p=<?=$cp-1?>&kat=<?=$filter_kat?>" class="page-link">←</a><?php endif; ?>
    <?php for($i=max(1,$cp-2);$i<=min($total_pages,$cp+2);$i++): ?>
    <a href="?page=gallery&p=<?=$i?>&kat=<?=$filter_kat?>" class="page-link <?=$i===$cp?'active':''?>"><?=$i?></a>
    <?php endfor; ?>
    <?php if($cp<$total_pages): ?><a href="?page=gallery&p=<?=$cp+1?>&kat=<?=$filter_kat?>" class="page-link">→</a><?php endif; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<!-- Edit Modal -->
<div class="modal-overlay" id="edit-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Edit Info Foto</div>
            <button class="modal-close" onclick="closeEdit()">✕</button>
        </div>
        <form method="POST" id="edit-form">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="_action" value="edit_save">
            <input type="hidden" name="id" id="edit-id">
            <div class="modal-body">
                <img id="edit-preview" src="" style="width:100%;border-radius:8px;margin-bottom:16px;max-height:200px;object-fit:cover">
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="judul" id="edit-judul" class="form-control">
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori_id" id="edit-kat" class="form-control">
                        <option value="">— Tanpa Kategori —</option>
                        <?php foreach ($all_kat as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Project / Client</label>
                    <input type="text" name="project" id="edit-project" class="form-control">
                </div>
                <div class="form-group mb-0">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" id="edit-desc" class="form-control wysiwyg" rows="3"></textarea>
                </div>
                <div class="form-check mt-16">
                    <input type="checkbox" name="is_featured" id="edit-featured" value="1">
                    <label class="form-check-label" for="edit-featured">⭐ Featured (tampil menonjol di galeri)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeEdit()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
// Upload preview
const fileInput = document.getElementById('file-input');
const previewGrid = document.getElementById('preview-grid');
const uploadBtn = document.getElementById('upload-btn');
const fileCount = document.getElementById('file-count');
const dropzone = document.getElementById('dropzone');

fileInput.addEventListener('change', showPreview);

['dragover','dragenter'].forEach(ev => {
    dropzone.addEventListener(ev, e => { e.preventDefault(); dropzone.classList.add('dragover'); });
});
['dragleave','drop'].forEach(ev => {
    dropzone.addEventListener(ev, e => { e.preventDefault(); dropzone.classList.remove('dragover'); });
});
dropzone.addEventListener('drop', e => {
    fileInput.files = e.dataTransfer.files;
    showPreview();
});

function showPreview() {
    previewGrid.innerHTML = '';
    const files = fileInput.files;
    fileCount.textContent = files.length + ' foto dipilih';
    uploadBtn.disabled = files.length === 0;
    Array.from(files).forEach(f => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:100%;aspect-ratio:1;object-fit:cover;border-radius:8px;border:1px solid var(--border)';
            previewGrid.appendChild(img);
        };
        reader.readAsDataURL(f);
    });
}

// Gallery data for edit modal
const galleryData = <?= json_encode(array_map(fn($it) => [
    'id'          => $it['id'],
    'gambar'      => uploads_url($it['gambar']),
    'judul'       => $it['judul'],
    'kat'         => $it['kategori_id'],
    'project'     => $it['project'],
    'deskripsi'   => $it['deskripsi'],
    'is_featured' => $it['is_featured'],
], $items)) ?>;

function openEdit(id) {
    const item = galleryData.find(i => i.id == id);
    if (!item) return;
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-preview').src = item.gambar;
    document.getElementById('edit-judul').value = item.judul;
    document.getElementById('edit-kat').value = item.kat || '';
    document.getElementById('edit-project').value = item.project;
    document.getElementById('edit-desc').value = item.deskripsi;
    document.getElementById('edit-featured').checked = !!item.is_featured;
    document.getElementById('edit-modal').classList.add('open');
}
function closeEdit() {
    document.getElementById('edit-modal').classList.remove('open');
}
document.getElementById('edit-modal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeEdit();
});
</script>
