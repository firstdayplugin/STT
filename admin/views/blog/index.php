<?php
$page_title = 'Blog & Artikel';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
        redirect(admin_url('?page=blog'));
    }

    $a = $_POST['_action'] ?? '';

    if ($a === 'delete') {
        $del_id = (int)($_POST['del_id'] ?? 0);
        if ($del_id) {
            $db->execute("DELETE FROM blog WHERE id = ?", [$del_id]);
            log_activity('delete', 'Hapus blog #' . $del_id, $user['id']);
            set_flash('success', 'Artikel berhasil dihapus.');
        }
        redirect(admin_url('?page=blog'));
    }

    if ($a === 'save') {
        $bid     = (int)($_POST['id'] ?? 0);
        $judul   = trim($_POST['judul'] ?? '');
        $slug    = trim($_POST['slug'] ?? '');
        $konten  = $_POST['konten'] ?? '';
        $excerpt = trim($_POST['excerpt'] ?? '');
        $status  = in_array($_POST['status'] ?? '', ['published','draft']) ? $_POST['status'] : 'draft';
        $kategori_ids = $_POST['kategori'] ?? [];
        $tags_str     = trim($_POST['tags'] ?? '');
        $meta_title   = trim($_POST['meta_title'] ?? '');
        $meta_desc    = trim($_POST['meta_desc'] ?? '');
        $featured_img = '';

        if (empty($judul)) {
            set_flash('error', 'Judul wajib diisi.');
            redirect(admin_url('?page=blog&action=' . ($bid ? 'edit&id='.$bid : 'create')));
        }

        if (empty($slug)) $slug = make_slug($judul);

        // Check slug unique
        $existing = $db->fetchOne("SELECT id FROM blog WHERE slug = ? AND id != ?", [$slug, $bid]);
        if ($existing) $slug = $slug . '-' . time();

        // Upload featured image
        if (!empty($_FILES['gambar_utama']['name'])) {
            $uploaded = upload_image($_FILES['gambar_utama'], 'blog');
            if ($uploaded) $featured_img = $uploaded;
        }


        if ($bid) {
            $update = [
                'judul'          => $judul,
                'slug'           => $slug,
                'konten'         => $konten,
                'excerpt'        => $excerpt,
                'status'         => $status,
                'meta_title'     => $meta_title,
                'meta_description'=> $meta_desc,
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
            if ($featured_img) $update['gambar_utama'] = $featured_img;

            $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($update)));
            $db->execute("UPDATE blog SET $set WHERE id = ?", [...array_values($update), $bid]);
            log_activity('update', 'Update blog: ' . $judul, $user['id']);
            $new_id = $bid;
        } else {
            $new_id = $db->insert('blog', [
                'judul'            => $judul,
                'slug'             => $slug,
                'konten'           => $konten,
                'excerpt'          => $excerpt,
                'status'           => $status,
                'gambar_utama'   => $featured_img,
                'meta_title'       => $meta_title,
                'meta_description' => $meta_desc,
                'user_id'        => $user['id'],
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
            log_activity('create', 'Tambah blog: ' . $judul, $user['id']);
        }

        // Sync kategori
        $db->execute("DELETE FROM blog_kategori_rel WHERE blog_id = ?", [$new_id]);
        foreach ($kategori_ids as $kid) {
            $db->execute("INSERT IGNORE INTO blog_kategori_rel (blog_id, kategori_id) VALUES (?, ?)", [$new_id, (int)$kid]);
        }

        // Sync tags
        if ($tags_str) {
            $db->execute("DELETE FROM blog_tags_rel WHERE blog_id = ?", [$new_id]);
            foreach (explode(',', $tags_str) as $tag_name) {
                $tag_name = trim($tag_name);
                if (!$tag_name) continue;
                $tag_slug = $tag_name;
                $tag = $db->fetchOne("SELECT id FROM blog_tags WHERE nama = ?", [$tag_slug]);
                if (!$tag) {
                    $tag_id = $db->insert('blog_tags', ['nama' => $tag_name]);
                } else {
                    $tag_id = $tag['id'];
                }
                $db->execute("INSERT IGNORE INTO blog_tags_rel (blog_id, tag_id) VALUES (?, ?)", [$new_id, $tag_id]);
            }
        }

        set_flash('success', 'Artikel berhasil disimpan!');
        redirect(admin_url('?page=blog'));
    }
}

// Fetch for edit
$edit_post = null;
$post_tags = '';
$post_kategori = [];
if ($action === 'edit' && $id) {
    $edit_post = $db->fetchOne("SELECT * FROM blog WHERE id = ?", [$id]);
    if (!$edit_post) { set_flash('error', 'Artikel tidak ditemukan.'); redirect(admin_url('?page=blog')); }
    $kat_rows = $db->fetchAll("SELECT kategori_id FROM blog_kategori_rel WHERE blog_id = ?", [$id]);
    $post_kategori = array_column($kat_rows, 'kategori_id');
    $tag_rows = $db->fetchAll("SELECT bt.nama FROM blog_tags bt JOIN blog_tags_rel btr ON bt.id = btr.tag_id WHERE btr.blog_id = ?", [$id]);
    $post_tags = implode(', ', array_column($tag_rows, 'nama'));
}

$all_kategori = $db->fetchAll("SELECT * FROM blog_kategori ORDER BY nama");

// List
$search = trim($_GET['search'] ?? '');
$filter_status = $_GET['status'] ?? '';
$per_page = 15;
$current_page = max(1, (int)($_GET['p'] ?? 1));
$offset = ($current_page - 1) * $per_page;

$where = '1=1';
$params = [];
if ($search) { $where .= ' AND judul LIKE ?'; $params[] = "%$search%"; }
if ($filter_status) { $where .= ' AND status = ?'; $params[] = $filter_status; }

$total_posts = $db->fetchOne("SELECT COUNT(*) as c FROM blog WHERE $where", $params)['c'];
$posts = $db->fetchAll("SELECT b.*, u.nama as author_nama FROM blog b LEFT JOIN users u ON b.user_id = u.id WHERE $where ORDER BY b.created_at DESC LIMIT $per_page OFFSET $offset", $params);

$csrf = generate_csrf();

if ($action === 'kategori'):
    $bk_has_parent_q = in_array('parent_id', $db->getColumns('blog_kategori'));
    if ($bk_has_parent_q) {
        $kategori_list_flat = $db->fetchAll("SELECT bk.*, (SELECT COUNT(*) FROM blog_kategori_rel r WHERE r.kategori_id = bk.id) as jml FROM blog_kategori bk ORDER BY bk.nama");
        // Order by hierarchy: render tree then flatten with depth
        $kt = build_category_tree($kategori_list_flat);
        $kategori_list = [];
        $flatten = function($nodes, $depth=0) use (&$flatten, &$kategori_list) {
            foreach ($nodes as $n) {
                $n['_depth'] = $depth;
                $kategori_list[] = $n;
                if (!empty($n['children'])) $flatten($n['children'], $depth + 1);
            }
        };
        $flatten($kt);
    } else {
        $kategori_list = $db->fetchAll("SELECT bk.*, (SELECT COUNT(*) FROM blog_kategori_rel r WHERE r.kategori_id = bk.id) as jml FROM blog_kategori bk ORDER BY bk.nama");
    }
?>
<div class="page-header">
  <div>
    <h1>🏷️ Kategori Artikel</h1>
    <div class="page-header-sub">Kelola kategori untuk artikel blog</div>
  </div>
  <div class="page-actions"><a href="<?= admin_url('?page=blog') ?>" class="btn btn-secondary">← Kembali ke Artikel</a></div>
</div>

<?php $bk_has_parent = in_array('parent_id', $db->getColumns('blog_kategori')); ?>
<div class="grid" style="grid-template-columns:1fr 1.5fr;gap:20px;align-items:start">
  <!-- Add form -->
  <div class="card">
    <div class="card-header"><div class="card-title">Tambah Kategori Baru</div></div>
    <div class="card-body">
      <div class="form-group">
        <label>Nama Kategori</label>
        <input type="text" id="new-bkat-input" placeholder="contoh: Tips & Trik" style="width:100%;padding:9px 11px;border:1px solid var(--border-2);border-radius:7px">
      </div>
      <?php if ($bk_has_parent): ?>
      <div class="form-group">
        <label>Parent Kategori <small style="color:var(--text-muted);font-weight:normal">(opsional)</small></label>
        <select id="new-bkat-parent" style="width:100%;padding:9px 11px;border:1px solid var(--border-2);border-radius:7px">
          <option value="0">— Kategori Utama (tanpa parent) —</option>
          <?php
          // Render dropdown with hierarchy indent
          $bk_flat = $db->fetchAll("SELECT id, nama, parent_id FROM blog_kategori ORDER BY nama");
          $bk_tree = build_category_tree($bk_flat);
          function render_bkat_options($nodes, $depth=0) {
              foreach ($nodes as $n) {
                  echo '<option value="'.$n['id'].'">'.str_repeat('— ', $depth).htmlspecialchars($n['nama']).'</option>';
                  if (!empty($n['children'])) render_bkat_options($n['children'], $depth+1);
              }
          }
          render_bkat_options($bk_tree);
          ?>
        </select>
        <div class="form-help" style="font-size:11px;color:var(--text-muted);margin-top:4px">Biarkan kosong jika ini kategori utama. Pilih parent untuk membuat sub-kategori.</div>
      </div>
      <?php endif; ?>
      <button type="button" onclick="saveBlogKat()" id="save-bkat-btn" class="btn btn-primary btn-block">+ Tambah Kategori</button>
      <div id="bkat-feedback" style="font-size:12px;margin-top:8px"></div>
    </div>
  </div>
  
  <!-- List -->
  <div class="card">
    <div class="card-header"><div class="card-title">Daftar Kategori</div></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Nama</th><th>Slug</th><th style="width:80px">Artikel</th><th style="width:70px">Aksi</th></tr></thead>
        <tbody id="bkat-tbody">
          <?php if (empty($kategori_list)): ?>
            <tr id="bkat-empty"><td colspan="4" style="text-align:center;color:var(--text-muted);padding:24px">Belum ada kategori</td></tr>
          <?php else: foreach ($kategori_list as $k): ?>
            <tr data-bkat-id="<?= $k['id'] ?>">
              <td style="font-weight:600">
                <?php if (!empty($k['_depth'])): ?><span style="display:inline-block;width:<?= $k['_depth'] * 18 ?>px"></span>↳ <?php endif; ?>
                <?= htmlspecialchars($k['nama']) ?>
              </td>
              <td><code style="font-size:11px;background:var(--surface-2);padding:2px 6px;border-radius:4px"><?= htmlspecialchars($k['slug']) ?></code></td>
              <td><?= $k['jml'] ?></td>
              <td><button type="button" class="btn btn-danger btn-sm" onclick="delBlogKat(<?= $k['id'] ?>, this)">🗑</button></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
const BKAT_CSRF = '<?= $csrf ?>';
const BKAT_URL = '<?= admin_url('?page=blog') ?>';
function saveBlogKat() {
  const input = document.getElementById('new-bkat-input');
  const btn = document.getElementById('save-bkat-btn');
  const fb = document.getElementById('bkat-feedback');
  const nama = input.value.trim();
  if (!nama) { input.focus(); return; }
  btn.disabled = true; btn.textContent = '⏳ Menyimpan...';
  const parentSel = document.getElementById('new-bkat-parent');
  const fd = new FormData();
  fd.append('csrf_token', BKAT_CSRF); fd.append('nama', nama);
  if (parentSel) fd.append('parent_id', parentSel.value);
  fetch(BKAT_URL + '&ajax=create_blog_kategori', {method:'POST', body:fd})
    .then(r => r.json()).then(d => {
      btn.disabled = false; btn.textContent = '+ Tambah Kategori';
      if (d.ok) {
        // Reload page so the hierarchy + parent dropdown reflect the new item
        location.reload();
        return;
        const empty = document.getElementById('bkat-empty'); if (empty) empty.remove();
        const tbody = document.getElementById('bkat-tbody');
        const tr = document.createElement('tr');
        tr.setAttribute('data-bkat-id', d.kategori.id);
        tr.innerHTML = '<td style="font-weight:600">'+escBK(d.kategori.nama)+'</td>'+
          '<td><code style="font-size:11px;background:var(--surface-2);padding:2px 6px;border-radius:4px">'+escBK(d.kategori.slug)+'</code></td>'+
          '<td>0</td><td><button type="button" class="btn btn-danger btn-sm" onclick="delBlogKat('+d.kategori.id+', this)">🗑</button></td>';
        tbody.appendChild(tr);
        input.value = '';
        fb.innerHTML = '<span style="color:#16a34a">✓ Kategori ditambahkan</span>';
        setTimeout(()=>fb.innerHTML='', 2500);
        input.focus();
      } else {
        fb.innerHTML = '<span style="color:#dc2626">✕ '+escBK(d.error||'Gagal')+'</span>';
      }
    }).catch(e => { btn.disabled=false; btn.textContent='+ Tambah Kategori'; fb.innerHTML='<span style="color:#dc2626">✕ '+escBK(e.message)+'</span>'; });
}
function delBlogKat(id, btn) {
  if (!confirm('Hapus kategori ini? Artikel tidak akan terhapus.')) return;
  const fd = new FormData();
  fd.append('csrf_token', BKAT_CSRF); fd.append('id', id);
  fetch(BKAT_URL + '&ajax=delete_blog_kategori', {method:'POST', body:fd})
    .then(r => r.json()).then(d => {
      if (d.ok) { const row = document.querySelector('[data-bkat-id="'+id+'"]'); if (row) row.remove(); }
      else alert('Gagal: '+(d.error||'unknown'));
    }).catch(e => alert('Error: '+e.message));
}
function escBK(s){const d=document.createElement('div');d.textContent=s;return d.innerHTML;}
document.addEventListener('DOMContentLoaded',()=>{
  const i=document.getElementById('new-bkat-input');
  if(i)i.addEventListener('keypress',e=>{if(e.key==='Enter'){e.preventDefault();saveBlogKat();}});
});
</script>

<?php
elseif ($action === 'create' || $action === 'edit'):
$breadcrumbs = [['label'=>'Blog','url'=> admin_url('?page=blog')]];
$page_title = $action === 'edit' ? 'Edit Artikel' : 'Tulis Artikel Baru';
?>

<div class="page-header">
    <div class="page-title"><?= $action === 'edit' ? 'Edit Artikel' : '✏️ Tulis Artikel Baru' ?></div>
    <div class="page-actions">
        <a href="<?= admin_url('?page=blog') ?>" class="btn btn-secondary">← Kembali</a>
    </div>
</div>

<form method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="_action" value="save">
    <input type="hidden" name="id" value="<?= $edit_post['id'] ?? '' ?>">

    <div class="grid-2" style="align-items:start">
        <!-- Main Column -->
        <div style="display:flex;flex-direction:column;gap:20px">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Judul Artikel <span class="required">*</span></label>
                        <input type="text" name="judul" class="form-control"
                               value="<?= htmlspecialchars($edit_post['judul'] ?? '') ?>"
                               placeholder="Judul artikel yang menarik..." required
                               id="judul-input">
                    </div>
                    <div class="form-group">
                        <label>Slug URL</label>
                        <input type="text" name="slug" class="form-control" id="slug-input"
                               value="<?= htmlspecialchars($edit_post['slug'] ?? '') ?>"
                               placeholder="otomatis-dari-judul">
                        <div class="form-help">URL: <?= url('/blog/') ?><span id="slug-preview"><?= htmlspecialchars($edit_post['slug'] ?? 'otomatis-dari-judul') ?></span></div>
                    </div>
                    <div class="form-group mb-0">
                        <label>Konten Artikel <span class="required">*</span></label>
                        <textarea name="konten" id="editor" class="form-control wysiwyg" rows="20"
                                  placeholder="Tulis konten artikel di sini..."><?= htmlspecialchars($edit_post['konten'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><div class="card-title">SEO</div></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" class="form-control"
                               value="<?= htmlspecialchars($edit_post['meta_title'] ?? '') ?>"
                               placeholder="Kosongkan untuk gunakan judul artikel">
                        <div class="form-help" id="meta-title-count">0 / 60 karakter</div>
                    </div>
                    <div class="form-group mb-0">
                        <label>Meta Description</label>
                        <textarea name="meta_desc" class="form-control" rows="3"
                                  id="meta-desc-input"
                                  placeholder="Deskripsi singkat untuk mesin pencari (max 160 karakter)"><?= htmlspecialchars($edit_post['meta_description'] ?? '') ?></textarea>
                        <div class="form-help" id="meta-desc-count">0 / 160 karakter</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div style="display:flex;flex-direction:column;gap:20px">
            <div class="card">
                <div class="card-header"><div class="card-title">Publikasi</div></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="draft" <?= ($edit_post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>📝 Draft</option>
                            <option value="published" <?= ($edit_post['status'] ?? '') === 'published' ? 'selected' : '' ?>>🌐 Published</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label>Ringkasan / Excerpt</label>
                        <textarea name="excerpt" class="form-control wysiwyg" rows="3"
                                  placeholder="Ringkasan singkat artikel (opsional)"><?= htmlspecialchars($edit_post['excerpt'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content:space-between">
                    <button type="submit" name="status_submit" value="draft" class="btn btn-secondary btn-sm"
                            onclick="document.querySelector('[name=status]').value='draft'">
                        Simpan Draft
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"
                            onclick="document.querySelector('[name=status]').value='published'">
                        Publish →
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><div class="card-title">Gambar Featured</div></div>
                <div class="card-body">
                    <?php if (!empty($edit_post['gambar_utama'])): ?>
                    <div class="img-preview-wrap mb-12">
                        <img src="<?= uploads_url($edit_post['gambar_utama']) ?>" alt="Featured">
                    </div>
                    <p class="text-sm text-muted mb-12">Upload baru untuk ganti gambar</p>
                    <?php endif; ?>
                    <input type="file" name="gambar_utama" class="form-control"
                           accept="image/jpeg,image/png,image/webp">
                    <div class="form-help">JPG, PNG, WebP. Maks 2MB.</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><div class="card-title">Kategori</div></div>
                <div class="card-body">
                    <?php if (empty($all_kategori)): ?>
                        <p class="text-muted text-sm">Belum ada kategori. <a href="<?= admin_url('?page=blog-kategori') ?>" style="color:var(--accent)">Buat kategori</a></p>
                    <?php else: ?>
                    <?php foreach ($all_kategori as $kat): ?>
                    <div class="form-check mb-8">
                        <input type="checkbox" name="kategori[]"
                               id="kat-<?= $kat['id'] ?>"
                               value="<?= $kat['id'] ?>"
                               <?= in_array($kat['id'], $post_kategori) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="kat-<?= $kat['id'] ?>"><?= htmlspecialchars($kat['nama']) ?></label>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><div class="card-title">Tags</div></div>
                <div class="card-body">
                    <input type="text" name="tags" class="form-control"
                           value="<?= htmlspecialchars($post_tags) ?>"
                           placeholder="tag1, tag2, tag3">
                    <div class="form-help">Pisahkan dengan koma</div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Auto-slug from title
const judulInput = document.getElementById('judul-input');
const slugInput  = document.getElementById('slug-input');
const slugPrev   = document.getElementById('slug-preview');

function makeSlug(str) {
    return str.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
}

judulInput.addEventListener('input', function() {
    if (!slugInput.dataset.manual) {
        const s = makeSlug(this.value);
        slugInput.value = s;
        slugPrev.textContent = s || 'otomatis-dari-judul';
    }
});
slugInput.addEventListener('input', function() {
    this.dataset.manual = '1';
    slugPrev.textContent = this.value || 'otomatis-dari-judul';
});

// Meta char counters
function charCounter(inputId, counterId, max) {
    const el = document.getElementById(inputId) || document.querySelector('[name="'+inputId+'"]');
    const counter = document.getElementById(counterId);
    if (!el || !counter) return;
    function update() {
        const len = el.value.length;
        counter.textContent = len + ' / ' + max + ' karakter';
        counter.style.color = len > max ? 'var(--danger)' : 'var(--text-muted)';
    }
    el.addEventListener('input', update);
    update();
}
charCounter('meta_title', 'meta-title-count', 60);
document.querySelector('[name="meta_desc"]')?.addEventListener('input', function() {
    const c = document.getElementById('meta-desc-count');
    if (c) { c.textContent = this.value.length + ' / 160 karakter'; }
});

// Old per-page TinyMCE init disabled — global init in admin/views/layout.php now binds to .wysiwyg class.
// Konten textarea already has class="wysiwyg" so editor will load there automatically.
</script>

<?php else: // LIST VIEW ?>

<div class="page-header">
    <div class="page-title">📝 Blog & Artikel
        <small><?= number_format($total_posts) ?> artikel total</small>
    </div>
    <div class="page-actions" style="display:flex;gap:8px">
        <a href="<?= admin_url('?page=blog-kategori') ?>" class="btn btn-secondary">🏷️ Kelola Kategori</a>
        <a href="<?= admin_url('?page=blog&action=create') ?>" class="btn btn-primary">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tulis Artikel
        </a>
    </div>
</div>

<div class="filter-bar">
    <form method="GET" action="" style="display:contents">
        <input type="hidden" name="page" value="blog">
        <div class="search-input-wrap">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari artikel...">
        </div>
        <select name="status" class="form-control" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="published" <?= $filter_status === 'published' ? 'selected' : '' ?>>Published</option>
            <option value="draft"     <?= $filter_status === 'draft'     ? 'selected' : '' ?>>Draft</option>
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
        <?php if ($search || $filter_status): ?>
        <a href="<?= admin_url('?page=blog') ?>" class="btn btn-sm" style="color:var(--danger)">× Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width:60px">Gambar</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Penulis</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th style="width:100px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($posts)): ?>
                <tr><td colspan="7">
                    <div class="empty-state">
                        <div class="empty-icon">📝</div>
                        <div class="empty-title">Belum ada artikel</div>
                        <div class="empty-text">Mulai menulis artikel pertama Anda.</div>
                    </div>
                </td></tr>
            <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <?php
                $kats = $db->fetchAll("SELECT bk.nama FROM blog_kategori bk JOIN blog_kategori_rel bkr ON bk.id = bkr.kategori_id WHERE bkr.blog_id = ?", [$post['id']]);
                ?>
                <tr>
                    <td>
                        <?php if ($post['gambar_utama']): ?>
                            <img src="<?= uploads_url($post['gambar_utama']) ?>" class="table-img" alt="">
                        <?php else: ?>
                            <div style="width:48px;height:40px;background:var(--surface3);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--text-dim)">📷</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight:500"><?= htmlspecialchars($post['judul']) ?></div>
                        <div class="text-xs text-muted">/blog/<?= htmlspecialchars($post['slug']) ?></div>
                    </td>
                    <td>
                        <?php foreach ($kats as $kat): ?>
                            <span class="badge badge-muted"><?= htmlspecialchars($kat['nama']) ?></span>
                        <?php endforeach; ?>
                    </td>
                    <td class="text-sm text-muted"><?= htmlspecialchars($post['author_nama'] ?? '-') ?></td>
                    <td>
                        <?php if ($post['status'] === 'published'): ?>
                            <span class="badge badge-success">Publish</span>
                        <?php else: ?>
                            <span class="badge badge-muted">Draft</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-sm text-muted"><?= date('d/m/Y', strtotime($post['created_at'])) ?></td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="<?= admin_url('?page=blog&action=edit&id='.$post['id']) ?>"
                               class="btn btn-xs btn-secondary" title="Edit">✏️</a>
                            <a href="<?= url('/blog/'.$post['slug']) ?>" target="_blank"
                               class="btn btn-xs btn-secondary" title="Lihat">🔗</a>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                <input type="hidden" name="_action" value="delete">
                                <input type="hidden" name="del_id" value="<?= $post['id'] ?>">
                                <button type="submit" class="btn btn-xs btn-danger"
                                        data-confirm="Hapus artikel '<?= htmlspecialchars(addslashes($post['judul'])) ?>'?">🗑️</button>
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

<?php
// Pagination
$total_pages = ceil($total_posts / $per_page);
if ($total_pages > 1):
?>
<div class="pagination">
    <?php if ($current_page > 1): ?>
        <a href="?page=blog&p=<?= $current_page-1 ?>&search=<?= urlencode($search) ?>&status=<?= $filter_status ?>" class="page-link">←</a>
    <?php endif; ?>
    <?php for ($i = max(1,$current_page-2); $i <= min($total_pages,$current_page+2); $i++): ?>
        <a href="?page=blog&p=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $filter_status ?>"
           class="page-link <?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($current_page < $total_pages): ?>
        <a href="?page=blog&p=<?= $current_page+1 ?>&search=<?= urlencode($search) ?>&status=<?= $filter_status ?>" class="page-link">→</a>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php endif; ?>
