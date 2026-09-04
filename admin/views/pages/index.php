<?php
$page_title = 'Manajemen Halaman';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error','Token invalid.'); redirect(admin_url('?page=pages')); }
    $a = $_POST['_action'] ?? '';

    if ($a === 'delete') {
        $del_id = (int)($_POST['del_id'] ?? 0);
        if ($del_id) { $db->execute("DELETE FROM pages WHERE id=?",[$del_id]); set_flash('success','Halaman dihapus.'); }
        redirect(admin_url('?page=pages'));
    }

    if ($a === 'save') {
        $pid     = (int)($_POST['id'] ?? 0);
        $judul   = trim($_POST['judul'] ?? '');
        $slug    = trim($_POST['slug'] ?? '');
        $konten  = $_POST['konten'] ?? '';
        $status  = in_array($_POST['status']??'',['published','draft'])?$_POST['status']:'draft';
        $meta_title = trim($_POST['meta_title'] ?? '');
        $meta_desc  = trim($_POST['meta_desc'] ?? '');
        $template   = trim($_POST['template'] ?? 'default');
        $show_in_nav = isset($_POST['show_in_nav']) ? 1 : 0;

        if (!$judul) { set_flash('error','Judul wajib diisi.'); redirect(admin_url('?page=pages&action='.($pid?'edit&id='.$pid:'create'))); }
        if (!$slug) $slug = make_slug($judul);
        $exist = $db->fetchOne("SELECT id FROM pages WHERE slug=? AND id!=?",[$slug,$pid]);
        if ($exist) $slug .= '-'.time();

        $data = ['judul'=>$judul,'slug'=>$slug,'konten'=>$konten,'status'=>$status,'meta_title'=>$meta_title,'meta_description'=>$meta_desc,'template'=>$template,'show_in_nav'=>$show_in_nav,'updated_at'=>date('Y-m-d H:i:s')];

        if ($pid) { $set=implode(',',array_map(fn($k)=>"$k=?",array_keys($data))); $db->execute("UPDATE pages SET $set WHERE id=?", [...array_values($data),$pid]); }
        else { $data['created_at']=date('Y-m-d H:i:s'); $db->insert('pages',$data); }

        set_flash('success','Halaman disimpan!');
        redirect(admin_url('?page=pages'));
    }
}

$edit = null;
if ($action === 'edit' && $id) {
    $edit = $db->fetchOne("SELECT * FROM pages WHERE id=?",[$id]);
    if (!$edit) { set_flash('error','Halaman tidak ditemukan.'); redirect(admin_url('?page=pages')); }
}

$all_pages = $db->fetchAll("SELECT * FROM pages ORDER BY created_at DESC");
$csrf = generate_csrf();

if ($action === 'create' || $action === 'edit'):
$page_title = $action==='edit'?'Edit Halaman':'Buat Halaman Baru';
$breadcrumbs = [['label'=>'Halaman','url'=>admin_url('?page=pages')]];
?>
<div class="page-header">
    <div class="page-title"><?= $page_title ?></div>
    <a href="<?= admin_url('?page=pages') ?>" class="btn btn-secondary">← Kembali</a>
</div>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="_action" value="save">
    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">

<div class="grid-2" style="align-items:start">
<div style="display:flex;flex-direction:column;gap:20px">
    <div class="card">
        <div class="card-body">
            <div class="form-group"><label>Judul Halaman <span class="required">*</span></label>
                <input type="text" name="judul" class="form-control" id="judul-p"
                       value="<?= htmlspecialchars($edit['judul'] ?? '') ?>" required></div>
            <div class="form-group"><label>Slug URL</label>
                <input type="text" name="slug" class="form-control" id="slug-p"
                       value="<?= htmlspecialchars($edit['slug'] ?? '') ?>">
                <div class="form-help"><?= url('/') ?>/<span id="slug-prev"><?= $edit['slug'] ?? 'slug-halaman' ?></span></div></div>
            <div class="form-group mb-0"><label>Konten Halaman</label>
                <textarea name="konten" class="form-control wysiwyg" rows="16"
                          placeholder="Konten halaman dalam HTML atau teks..."><?= htmlspecialchars($edit['konten'] ?? '') ?></textarea></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><div class="card-title">SEO</div></div>
        <div class="card-body">
            <div class="form-group"><label>Meta Title</label>
                <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($edit['meta_title'] ?? '') ?>"></div>
            <div class="form-group mb-0"><label>Meta Description</label>
                <textarea name="meta_desc" class="form-control" rows="3"><?= htmlspecialchars($edit['meta_description'] ?? '') ?></textarea></div>
        </div>
    </div>
</div>
<div style="display:flex;flex-direction:column;gap:20px">
    <div class="card">
        <div class="card-header"><div class="card-title">Pengaturan</div></div>
        <div class="card-body">
            <div class="form-group"><label>Status</label>
                <select name="status" class="form-control">
                    <option value="draft" <?= ($edit['status']??'draft')==='draft'?'selected':'' ?>>📝 Draft</option>
                    <option value="published" <?= ($edit['status']??'')==='published'?'selected':'' ?>>🌐 Published</option>
                </select></div>
            <div class="form-group"><label>Template</label>
                <select name="template" class="form-control">
                    <option value="default">Default (dengan header/footer)</option>
                    <option value="blank" <?= ($edit['template']??'')==='blank'?'selected':'' ?>>Blank (tanpa layout)</option>
                    <option value="fullwidth" <?= ($edit['template']??'')==='fullwidth'?'selected':'' ?>>Full Width</option>
                </select></div>
            <div class="form-group mb-0">
                <div class="form-check">
                    <input type="checkbox" name="show_in_nav" id="sn" value="1" <?= ($edit['show_in_nav']??0)?'checked':'' ?>>
                    <label class="form-check-label" for="sn">Tampilkan di menu navigasi</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary w-full" style="justify-content:center">💾 Simpan Halaman</button>
        </div>
    </div>
</div>
</div>
</form>
<script>
const jp=document.getElementById('judul-p'),sp=document.getElementById('slug-p'),prev=document.getElementById('slug-prev');
function slug(s){return s.toLowerCase().replace(/[^\w\s-]/g,'').replace(/\s+/g,'-').replace(/-+/g,'-').trim();}
jp.addEventListener('input',()=>{if(!sp.dataset.m){sp.value=slug(jp.value);prev.textContent=sp.value||'slug-halaman';}});
sp.addEventListener('input',()=>{sp.dataset.m='1';prev.textContent=sp.value;});
</script>

<?php else: ?>
<div class="page-header">
    <div class="page-title">📄 Halaman <small><?= count($all_pages) ?> halaman</small></div>
    <a href="<?= admin_url('?page=pages&action=create') ?>" class="btn btn-primary">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Buat Halaman
    </a>
</div>
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead><tr><th>Judul</th><th>Slug</th><th>Template</th><th>Status</th><th>Di Nav</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($all_pages)): ?>
            <tr><td colspan="6"><div class="empty-state"><div class="empty-icon">📄</div><div class="empty-title">Belum ada halaman kustom</div></div></td></tr>
            <?php else: ?>
            <?php foreach ($all_pages as $p): ?>
            <tr>
                <td style="font-weight:500"><?= htmlspecialchars($p['judul']) ?></td>
                <td><code class="text-sm" style="color:var(--accent)">/<?= htmlspecialchars($p['slug']) ?></code></td>
                <td><span class="badge badge-muted"><?= $p['template'] ?></span></td>
                <td><?= $p['status']==='published'?'<span class="badge badge-success">Publish</span>':'<span class="badge badge-muted">Draft</span>' ?></td>
                <td><?= $p['show_in_nav'] ? '✅' : '—' ?></td>
                <td>
                    <div style="display:flex;gap:6px">
                        <a href="<?= admin_url('?page=pages&action=edit&id='.$p['id']) ?>" class="btn btn-xs btn-secondary">✏️</a>
                        <a href="<?= url('/'.$p['slug']) ?>" target="_blank" class="btn btn-xs btn-secondary">🔗</a>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                            <input type="hidden" name="_action" value="delete">
                            <input type="hidden" name="del_id" value="<?= $p['id'] ?>">
                            <button class="btn btn-xs btn-danger" data-confirm="Hapus halaman ini?">🗑️</button>
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
<?php endif; ?>
