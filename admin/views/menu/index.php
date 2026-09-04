<?php
$page_title = 'Menu Navigasi';

// All POST actions (save_structure) are handled via AJAX in admin/index.php
// to bypass layout rendering and return clean JSON.

// ============================================
// Load all data
// ============================================
$all_menus = $db->fetchAll("SELECT * FROM menus ORDER BY urutan ASC, id ASC");

// Build tree
$menu_tree = build_category_tree($all_menus);

// Item picker data
$pages_ref     = $db->fetchAll("SELECT slug, judul FROM pages WHERE status='published' ORDER BY judul");
$layanan_ref   = $db->fetchAll("SELECT slug, nama FROM layanan WHERE is_active=1 ORDER BY nama");
$blog_ref      = $db->fetchAll("SELECT slug, judul FROM blog WHERE status='published' ORDER BY created_at DESC LIMIT 50");
$produk_ref    = $db->fetchAll("SELECT slug, nama FROM produk WHERE status='aktif' ORDER BY nama LIMIT 50");
$blog_kat_ref  = $db->fetchAll("SELECT slug, nama FROM blog_kategori ORDER BY nama");
$produk_kat_ref = $db->fetchAll("SELECT slug, nama FROM produk_kategori ORDER BY nama");

$csrf = generate_csrf();
?>

<div class="page-header">
    <div>
        <h1>🧭 Menu Navigasi</h1>
        <div class="page-header-sub">Bangun menu navigasi website. Drag &amp; drop untuk reorder, geser ke kanan untuk membuat sub-menu.</div>
    </div>
    <div class="page-actions">
        <button type="button" id="save-menu-btn" class="btn btn-primary">💾 Simpan Menu</button>
    </div>
</div>

<div id="menu-feedback" style="margin-bottom:14px"></div>

<div class="menu-builder">
  <!-- LEFT: item picker -->
  <aside class="menu-picker">
    <div class="card menu-picker-card">
      <div class="card-header"><div class="card-title">📌 Tambah Item ke Menu</div></div>
      <div class="card-body" style="padding:0">
        
        <details class="menu-picker-group" open>
          <summary><strong>📄 Halaman</strong></summary>
          <div class="menu-picker-list">
            <label class="menu-picker-item"><input type="checkbox" data-label="Beranda" data-url="/"> Beranda</label>
            <label class="menu-picker-item"><input type="checkbox" data-label="Tentang Kami" data-url="/tentang-kami"> Tentang Kami</label>
            <label class="menu-picker-item"><input type="checkbox" data-label="Layanan" data-url="/layanan"> Layanan</label>
            <label class="menu-picker-item"><input type="checkbox" data-label="Galeri" data-url="/gallery"> Galeri</label>
            <label class="menu-picker-item"><input type="checkbox" data-label="Blog" data-url="/blog"> Blog</label>
            <label class="menu-picker-item"><input type="checkbox" data-label="Produk" data-url="/produk"> Produk</label>
            <label class="menu-picker-item"><input type="checkbox" data-label="Hubungi Kami" data-url="/hubungi-kami"> Hubungi Kami</label>
            <?php foreach ($pages_ref as $p): ?>
            <label class="menu-picker-item">
              <input type="checkbox" data-label="<?= htmlspecialchars($p['judul']) ?>" data-url="/<?= htmlspecialchars($p['slug']) ?>">
              <?= htmlspecialchars($p['judul']) ?>
            </label>
            <?php endforeach; ?>
            <button type="button" class="menu-add-selected" data-group="pages">+ Tambah Terpilih</button>
          </div>
        </details>
        
        <?php if (!empty($layanan_ref)): ?>
        <details class="menu-picker-group">
          <summary><strong>🎨 Layanan</strong></summary>
          <div class="menu-picker-list">
            <?php foreach ($layanan_ref as $l): ?>
            <label class="menu-picker-item">
              <input type="checkbox" data-label="<?= htmlspecialchars($l['nama']) ?>" data-url="/layanan/<?= htmlspecialchars($l['slug']) ?>">
              <?= htmlspecialchars($l['nama']) ?>
            </label>
            <?php endforeach; ?>
            <button type="button" class="menu-add-selected" data-group="layanan">+ Tambah Terpilih</button>
          </div>
        </details>
        <?php endif; ?>
        
        <?php if (!empty($blog_kat_ref)): ?>
        <details class="menu-picker-group">
          <summary><strong>🏷️ Kategori Blog</strong></summary>
          <div class="menu-picker-list">
            <?php foreach ($blog_kat_ref as $k): ?>
            <label class="menu-picker-item">
              <input type="checkbox" data-label="<?= htmlspecialchars($k['nama']) ?>" data-url="/blog?kategori=<?= htmlspecialchars($k['slug']) ?>">
              <?= htmlspecialchars($k['nama']) ?>
            </label>
            <?php endforeach; ?>
            <button type="button" class="menu-add-selected" data-group="blog_kat">+ Tambah Terpilih</button>
          </div>
        </details>
        <?php endif; ?>
        
        <?php if (!empty($blog_ref)): ?>
        <details class="menu-picker-group">
          <summary><strong>📰 Artikel Blog</strong> <small>(50 terbaru)</small></summary>
          <div class="menu-picker-list">
            <?php foreach ($blog_ref as $b): ?>
            <label class="menu-picker-item">
              <input type="checkbox" data-label="<?= htmlspecialchars($b['judul']) ?>" data-url="/blog/<?= htmlspecialchars($b['slug']) ?>">
              <?= htmlspecialchars(mb_substr($b['judul'], 0, 40)) ?>
            </label>
            <?php endforeach; ?>
            <button type="button" class="menu-add-selected" data-group="blog">+ Tambah Terpilih</button>
          </div>
        </details>
        <?php endif; ?>
        
        <?php if (!empty($produk_kat_ref)): ?>
        <details class="menu-picker-group">
          <summary><strong>🗂️ Kategori Produk</strong></summary>
          <div class="menu-picker-list">
            <?php foreach ($produk_kat_ref as $k): ?>
            <label class="menu-picker-item">
              <input type="checkbox" data-label="<?= htmlspecialchars($k['nama']) ?>" data-url="/produk?kategori=<?= htmlspecialchars($k['slug']) ?>">
              <?= htmlspecialchars($k['nama']) ?>
            </label>
            <?php endforeach; ?>
            <button type="button" class="menu-add-selected" data-group="produk_kat">+ Tambah Terpilih</button>
          </div>
        </details>
        <?php endif; ?>
        
        <?php if (!empty($produk_ref)): ?>
        <details class="menu-picker-group">
          <summary><strong>📦 Produk</strong> <small>(50 pertama)</small></summary>
          <div class="menu-picker-list">
            <?php foreach ($produk_ref as $p): ?>
            <label class="menu-picker-item">
              <input type="checkbox" data-label="<?= htmlspecialchars($p['nama']) ?>" data-url="/produk/<?= htmlspecialchars($p['slug']) ?>">
              <?= htmlspecialchars(mb_substr($p['nama'], 0, 40)) ?>
            </label>
            <?php endforeach; ?>
            <button type="button" class="menu-add-selected" data-group="produk">+ Tambah Terpilih</button>
          </div>
        </details>
        <?php endif; ?>
        
        <details class="menu-picker-group">
          <summary><strong>🔗 Custom Link</strong></summary>
          <div class="menu-picker-list" style="padding:12px">
            <input type="text" id="custom-link-label" placeholder="Label (cth: Toko Online)" style="width:100%;padding:8px;margin-bottom:6px;border:1px solid var(--border);border-radius:6px">
            <input type="text" id="custom-link-url" placeholder="URL: https://... atau /halaman" style="width:100%;padding:8px;margin-bottom:6px;border:1px solid var(--border);border-radius:6px">
            <button type="button" id="add-custom-link" class="menu-add-selected">+ Tambah Custom Link</button>
          </div>
        </details>
        
        <details class="menu-picker-group">
          <summary><strong>⚓ Anchor Link</strong></summary>
          <div class="menu-picker-list">
            <label class="menu-picker-item"><input type="checkbox" data-label="FAQ" data-url="#faq"> #faq</label>
            <label class="menu-picker-item"><input type="checkbox" data-label="Testimoni" data-url="#testimoni"> #testimoni</label>
            <label class="menu-picker-item"><input type="checkbox" data-label="Kontak" data-url="#kontak"> #kontak</label>
            <label class="menu-picker-item"><input type="checkbox" data-label="Tentang" data-url="#about"> #about</label>
            <button type="button" class="menu-add-selected" data-group="anchor">+ Tambah Terpilih</button>
          </div>
        </details>
        
      </div>
    </div>
  </aside>

  <!-- RIGHT: structure (drag-drop) -->
  <main class="menu-structure">
    <div class="card">
      <div class="card-header">
        <div class="card-title">📋 Struktur Menu</div>
        <div style="font-size:12px;color:var(--text-muted)">Drag untuk reorder. Geser ke kanan untuk membuat sub-menu.</div>
      </div>
      <div class="card-body">
        <div id="menu-tree" class="menu-tree">
          <?php
          // Auto-injected items for /layanan, /blog?kategori=*, /produk?kategori=* etc.
          // Frontend automatically lists these when parent has no explicit children.
          // We show them as read-only placeholders so admin understands.
          $auto_layanan = $db->fetchAll("SELECT slug, nama FROM layanan WHERE is_active = 1 ORDER BY urutan, nama");
          
          $render_node = function($nodes) use (&$render_node, $auto_layanan) {
              foreach ($nodes as $n) {
                  $url_raw = $n['url'] ?? '#';
                  $is_layanan_root = ($url_raw === '/layanan' || $url_raw === '/layanan/');
                  $has_children = !empty($n['children']);
                  // Show auto-injected indicator if frontend will auto-fill
                  $will_auto_inject = $is_layanan_root && !$has_children && !empty($auto_layanan);
                  
                  echo '<div class="menu-node" data-id="'.htmlspecialchars($n['id']).'">';
                  echo   '<div class="menu-node-header">';
                  echo     '<span class="menu-drag-handle" title="Drag untuk reorder">⋮⋮</span>';
                  echo     '<span class="menu-node-label">'.htmlspecialchars($n['label']).'</span>';
                  echo     '<span class="menu-node-meta">'.htmlspecialchars($url_raw).'</span>';
                  if ($will_auto_inject) {
                      echo '<span class="menu-auto-badge" title="Frontend otomatis menampilkan semua layanan sebagai sub-menu">⚡ Auto: '.count($auto_layanan).' layanan</span>';
                  }
                  echo     '<button type="button" class="menu-node-edit" onclick="this.closest(\'.menu-node\').classList.toggle(\'editing\')">✏️</button>';
                  echo     '<button type="button" class="menu-node-delete" onclick="if(confirm(\'Hapus item menu ini?\')) this.closest(\'.menu-node\').remove()">🗑</button>';
                  echo   '</div>';
                  echo   '<div class="menu-node-edit-panel">';
                  echo     '<div class="form-grid"><div class="form-group"><label>Navigation Label</label><input type="text" class="menu-field" data-field="label" value="'.htmlspecialchars($n['label']).'"></div>';
                  echo     '<div class="form-group"><label>URL</label><input type="text" class="menu-field" data-field="url" value="'.htmlspecialchars($url_raw).'"></div></div>';
                  echo     '<div class="form-grid"><div class="form-group"><label>Open</label><select class="menu-field" data-field="target"><option value="_self"'.((($n['target']??'_self')==='_self')?' selected':'').'>Same window</option><option value="_blank"'.((($n['target']??'')==='_blank')?' selected':'').'>New tab</option></select></div>';
                  echo     '<div class="form-group"><label>CSS Class (opsional)</label><input type="text" class="menu-field" data-field="css_class" value="'.htmlspecialchars($n['css_class'] ?? '').'" placeholder="cth: highlight-cta"></div></div>';
                  echo     '<div class="form-group"><label>Icon emoji/text (opsional)</label><input type="text" class="menu-field" data-field="icon" value="'.htmlspecialchars($n['icon'] ?? '').'" placeholder="cth: 🏠"></div>';
                  echo   '</div>';
                  
                  // Visual placeholder for auto-injected items (BEFORE children container, so admin sees it)
                  if ($will_auto_inject) {
                      echo '<div class="menu-auto-preview" title="Otomatis dari Layanan aktif di database">';
                      echo   '<div class="menu-auto-preview-head">⚡ <strong>Auto-injected sub-menu:</strong> Frontend otomatis menampilkan semua layanan di sini. Untuk override, tambahkan item manual ke menu ini lewat panel kiri.</div>';
                      foreach ($auto_layanan as $al) {
                          echo '<div class="menu-auto-preview-item">↳ '.htmlspecialchars($al['nama']).' <span class="menu-auto-preview-url">/layanan/'.htmlspecialchars($al['slug']).'</span></div>';
                      }
                      echo '</div>';
                  }
                  
                  // children container — ALWAYS present so drag can drop into it
                  echo   '<div class="menu-children menu-sortable">';
                  if ($has_children) $render_node($n['children']);
                  echo   '</div>';
                  echo '</div>';
              }
          };
          if (empty($menu_tree)) {
              echo '<div style="text-align:center;color:var(--text-muted);padding:40px">Belum ada item menu. Pilih dari panel kiri untuk menambahkan.</div>';
          } else {
              $render_node($menu_tree);
          }
          ?>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- SortableJS for drag-drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<style>
.menu-builder { display: grid; grid-template-columns: 320px 1fr; gap: 20px; align-items: start; }
@media (max-width: 1024px) { .menu-builder { grid-template-columns: 1fr; } }

.menu-picker-card { position: sticky; top: 80px; max-height: calc(100vh - 100px); display: flex; flex-direction: column; overflow: hidden; }
.menu-picker-card .card-body { overflow-y: auto; }
.menu-picker-group { border-bottom: 1px solid var(--border); }
.menu-picker-group summary { padding: 10px 14px; cursor: pointer; user-select: none; list-style: none; font-size: 13px; }
.menu-picker-group summary::-webkit-details-marker { display: none; }
.menu-picker-group summary::before { content: '▸'; display: inline-block; margin-right: 8px; transition: transform 0.15s; opacity: 0.5; }
.menu-picker-group[open] summary::before { transform: rotate(90deg); }
.menu-picker-group summary:hover { background: var(--surface-2, #f5f7fa); }
.menu-picker-list { padding: 4px 14px 12px; max-height: 280px; overflow-y: auto; }
.menu-picker-item { display: flex; gap: 8px; align-items: center; padding: 5px 4px; font-size: 13px; cursor: pointer; }
.menu-picker-item:hover { background: var(--surface-2, #f5f7fa); border-radius: 4px; }
.menu-picker-item input { margin: 0; }
.menu-add-selected { width: 100%; padding: 8px; background: var(--accent, #2563eb); color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; margin-top: 8px; }
.menu-add-selected:hover { opacity: 0.9; }

/* Tree structure */
.menu-tree { min-height: 200px; }
.menu-node { background: white; border: 1px solid var(--border); border-radius: 8px; margin-bottom: 8px; }
.menu-node-header { display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: white; border-radius: 8px; }
.menu-drag-handle { cursor: grab; color: #999; font-size: 16px; user-select: none; padding: 0 4px; letter-spacing: -3px; }
.menu-drag-handle:active { cursor: grabbing; }
.menu-node-label { font-weight: 600; color: var(--text); flex-shrink: 0; }
.menu-node-meta { font-size: 12px; color: var(--text-muted); flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding: 0 8px; font-family: monospace; }
.menu-node-edit, .menu-node-delete { background: transparent; border: none; cursor: pointer; padding: 4px 8px; font-size: 14px; border-radius: 4px; }
.menu-node-edit:hover { background: var(--surface-2, #f5f7fa); }
.menu-node-delete:hover { background: #fee; }
.menu-node-edit-panel { display: none; padding: 14px; border-top: 1px solid var(--border); background: var(--surface-2, #f5f7fa); }
.menu-node.editing > .menu-node-edit-panel { display: block; }
.menu-node-edit-panel .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
.menu-node-edit-panel .form-group { margin: 0; }
.menu-node-edit-panel label { font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; display: block; }
.menu-node-edit-panel input, .menu-node-edit-panel select { width: 100%; padding: 7px 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; }
.menu-children { margin-left: 28px; padding-left: 4px; border-left: 2px dashed var(--border); min-height: 8px; padding-top: 4px; }
.menu-children:empty { padding: 0; }
.sortable-ghost { opacity: 0.4; }
.sortable-drag { box-shadow: 0 8px 24px rgba(0,0,0,0.15); }

/* Auto-injected items (frontend-only, e.g. Layanan list under /layanan) */
.menu-auto-badge {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 4px;
    border: 1px solid #fcd34d;
    margin-right: 8px;
    white-space: nowrap;
}
.menu-auto-preview {
    margin: 0 0 0 28px;
    padding: 12px 14px;
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
    border-left: 3px solid #f59e0b;
    border-radius: 0 8px 8px 0;
    font-size: 13px;
}
.menu-auto-preview-head {
    color: #92400e;
    font-size: 12px;
    line-height: 1.5;
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px dashed #fcd34d;
}
.menu-auto-preview-item {
    padding: 4px 0;
    color: #78350f;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.menu-auto-preview-url {
    color: #a16207;
    font-family: monospace;
    font-size: 11px;
    margin-left: auto;
    opacity: 0.7;
}
</style>

<script>
(function(){
  const tree = document.getElementById('menu-tree');
  const csrf = '<?= $csrf ?>';
  
  // === SortableJS setup ===
  function initSortable(el) {
    new Sortable(el, {
      group: 'menus',
      handle: '.menu-drag-handle',
      animation: 150,
      fallbackOnBody: true,
      swapThreshold: 0.65,
      ghostClass: 'sortable-ghost',
      dragClass: 'sortable-drag',
    });
  }
  // Init on root + every children container
  initSortable(tree);
  tree.querySelectorAll('.menu-children').forEach(initSortable);
  
  // === Add item from picker ===
  function makeNodeHtml(label, url, id = '') {
    const safeLabel = label.replace(/"/g, '&quot;').replace(/</g, '&lt;');
    const safeUrl = url.replace(/"/g, '&quot;').replace(/</g, '&lt;');
    return `<div class="menu-node" data-id="${id}">
      <div class="menu-node-header">
        <span class="menu-drag-handle" title="Drag untuk reorder">⋮⋮</span>
        <span class="menu-node-label">${safeLabel}</span>
        <span class="menu-node-meta">${safeUrl}</span>
        <button type="button" class="menu-node-edit" onclick="this.closest('.menu-node').classList.toggle('editing')">✏️</button>
        <button type="button" class="menu-node-delete" onclick="if(confirm('Hapus item menu ini?')) this.closest('.menu-node').remove()">🗑</button>
      </div>
      <div class="menu-node-edit-panel">
        <div class="form-grid">
          <div class="form-group"><label>Navigation Label</label><input type="text" class="menu-field" data-field="label" value="${safeLabel}"></div>
          <div class="form-group"><label>URL</label><input type="text" class="menu-field" data-field="url" value="${safeUrl}"></div>
        </div>
        <div class="form-grid">
          <div class="form-group"><label>Open</label><select class="menu-field" data-field="target"><option value="_self">Same window</option><option value="_blank">New tab</option></select></div>
          <div class="form-group"><label>CSS Class</label><input type="text" class="menu-field" data-field="css_class" placeholder="cth: highlight-cta"></div>
        </div>
        <div class="form-group"><label>Icon</label><input type="text" class="menu-field" data-field="icon" placeholder="cth: 🏠"></div>
      </div>
      <div class="menu-children menu-sortable"></div>
    </div>`;
  }
  
  function addNode(label, url) {
    // Remove "empty state" if present
    const empty = tree.querySelector('div[style*="text-align:center"]');
    if (empty) empty.remove();
    
    const wrapper = document.createElement('div');
    wrapper.innerHTML = makeNodeHtml(label, url);
    const node = wrapper.firstElementChild;
    tree.appendChild(node);
    initSortable(node.querySelector('.menu-children'));
  }
  
  document.querySelectorAll('.menu-add-selected').forEach(btn => {
    btn.addEventListener('click', function() {
      const group = btn.closest('.menu-picker-group');
      
      // Special-case: custom link
      if (btn.id === 'add-custom-link') {
        const label = document.getElementById('custom-link-label').value.trim();
        const url   = document.getElementById('custom-link-url').value.trim();
        if (!label || !url) { alert('Label dan URL wajib diisi'); return; }
        addNode(label, url);
        document.getElementById('custom-link-label').value = '';
        document.getElementById('custom-link-url').value = '';
        return;
      }
      
      const checkboxes = group.querySelectorAll('input[type="checkbox"]:checked');
      if (checkboxes.length === 0) { alert('Pilih minimal satu item'); return; }
      checkboxes.forEach(cb => {
        addNode(cb.dataset.label, cb.dataset.url);
        cb.checked = false;
      });
    });
  });
  
  // === Save ===
  function collectStructure(container) {
    const out = [];
    container.querySelectorAll(':scope > .menu-node').forEach(node => {
      // Read fields from edit panel (which is the source of truth)
      const fields = {};
      node.querySelectorAll(':scope > .menu-node-edit-panel .menu-field').forEach(f => {
        fields[f.dataset.field] = f.value;
      });
      // Sync header display
      node.querySelector(':scope > .menu-node-header .menu-node-label').textContent = fields.label || 'Untitled';
      node.querySelector(':scope > .menu-node-header .menu-node-meta').textContent = fields.url || '#';
      
      const childContainer = node.querySelector(':scope > .menu-children');
      out.push({
        id: node.dataset.id ? parseInt(node.dataset.id) : 0,
        label: fields.label || 'Untitled',
        url: fields.url || '#',
        target: fields.target || '_self',
        css_class: fields.css_class || '',
        icon: fields.icon || '',
        is_active: 1,
        children: childContainer ? collectStructure(childContainer) : []
      });
    });
    return out;
  }
  
  document.getElementById('save-menu-btn').addEventListener('click', async function() {
    const btn = this;
    const fb = document.getElementById('menu-feedback');
    btn.disabled = true; btn.textContent = '⏳ Menyimpan...';
    fb.innerHTML = '';
    
    const struct = collectStructure(tree);
    const fd = new FormData();
    fd.append('csrf_token', csrf);
    fd.append('structure', JSON.stringify(struct));
    
    try {
      const r = await fetch('<?= admin_url('?ajax=save_menu_structure') ?>', { method: 'POST', body: fd, headers: {'Accept': 'application/json'} });
      const data = await r.json();
      if (data.ok) {
        fb.innerHTML = `<div class="alert alert-success" style="padding:12px;background:#dcfce7;border:1px solid #86efac;border-radius:8px;color:#15803d">✅ Menu berhasil disimpan! (${data.kept} item)</div>`;
        setTimeout(() => location.reload(), 1000);
      } else {
        fb.innerHTML = `<div class="alert alert-error" style="padding:12px;background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;color:#b91c1c">❌ ${data.error || 'Gagal menyimpan'}</div>`;
      }
    } catch (e) {
      fb.innerHTML = `<div class="alert alert-error" style="padding:12px;background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;color:#b91c1c">❌ Network error: ${e.message}</div>`;
    } finally {
      btn.disabled = false; btn.textContent = '💾 Simpan Menu';
    }
  });
})();
</script>
