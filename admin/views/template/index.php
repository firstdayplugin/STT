<?php
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
     redirect(admin_url('?page=' . ($_GET['page'] ?? 'dashboard'))); } else {
        $act = $_POST['action'] ?? '';
        
        if ($act === 'activate') {
            $slug = trim($_POST['slug'] ?? '');
            // Verify theme exists in filesystem
            $theme_dir = BASE_PATH . '/themes/' . $slug;
            if (!is_dir($theme_dir)) {
                set_flash('error', 'Theme tidak ditemukan di folder /themes/. Pastikan file template sudah diupload.');
            } else {
                $db->execute("UPDATE themes SET is_active = 0");
                $db->execute("UPDATE themes SET is_active = 1 WHERE slug = ?", [$slug]);
                update_setting('active_theme', $slug);
                log_activity('update', "Activated theme: $slug");
                set_flash('success', "Template '$slug' berhasil diaktifkan.");
            }
        } elseif ($act === 'add_theme') {
            $data = [
                'slug'       => trim($_POST['slug'] ?? ''),
                'nama'       => trim($_POST['nama'] ?? ''),
                'deskripsi'  => trim($_POST['deskripsi'] ?? ''),
                'author'     => trim($_POST['author'] ?? ''),
                'version'    => trim($_POST['version'] ?? '1.0'),
                'screenshot' => trim($_POST['screenshot'] ?? ''),
                'demo_url'   => trim($_POST['demo_url'] ?? ''),
                'is_installed' => 1,
                'is_active'    => 0,
            ];
            if (!empty($_FILES['screenshot_file']['name'])) {
                $up = upload_image($_FILES['screenshot_file'], 'themes');
                if ($up) $data['screenshot'] = uploads_url($up);
            }
            $existing = $db->fetchOne("SELECT id FROM themes WHERE slug = ?", [$data['slug']]);
            if ($existing) {
                $db->execute("UPDATE themes SET nama=?, deskripsi=?, author=?, version=?, screenshot=?, demo_url=? WHERE slug=?",
                    [$data['nama'], $data['deskripsi'], $data['author'], $data['version'], $data['screenshot'], $data['demo_url'], $data['slug']]);
                set_flash('success', 'Template berhasil diupdate.');
            } else {
                $cols = array_keys($data);
                $placeholders = implode(',', array_fill(0, count($cols), '?'));
                $db->execute("INSERT INTO themes (" . implode(',', $cols) . ") VALUES ($placeholders)", array_values($data));
                set_flash('success', 'Template berhasil didaftarkan.');
            }
        } elseif ($act === 'delete') {
            $slug = trim($_POST['slug'] ?? '');
            if ($slug === 'default') {
                set_flash('error', 'Template default tidak boleh dihapus.');
            } else {
                $is_active = $db->fetchOne("SELECT is_active FROM themes WHERE slug = ?", [$slug])['is_active'] ?? 0;
                if ($is_active) {
                    set_flash('error', 'Aktifkan template lain dulu sebelum menghapus template ini.');
                } else {
                    $db->execute("DELETE FROM themes WHERE slug = ?", [$slug]);
                    set_flash('success', 'Template berhasil dihapus dari daftar.');
                }
            }
        }
        redirect(admin_url('?page=template'));
    }
}

// Auto-detect themes in /themes folder that aren't in DB.
// Metadata is read from each theme's theme.json when present (name, description, author,
// version, screenshot). Falls back to the folder name when theme.json is missing/invalid.
$themes_dir = BASE_PATH . '/themes';
if (is_dir($themes_dir)) {
    foreach (scandir($themes_dir) as $dir) {
        if ($dir === '.' || $dir === '..' || !is_dir($themes_dir . '/' . $dir)) continue;

        $meta = [];
        $tj = $themes_dir . '/' . $dir . '/theme.json';
        if (is_file($tj)) {
            $j = json_decode((string) file_get_contents($tj), true);
            if (is_array($j)) $meta = $j;
        }
        $nama       = $meta['name']        ?? ucfirst(str_replace(['-', '_'], ' ', $dir));
        $deskripsi  = $meta['description'] ?? '';
        $author     = $meta['author']      ?? '';
        $version    = $meta['version']     ?? '1.0';
        $screenshot = !empty($meta['screenshot'])
            ? THEMES_URL . '/' . $dir . '/' . ltrim($meta['screenshot'], '/')
            : '';

        // NOTE: the shipped database/reklamepedia.sql `themes` table is out of sync with this code
        // (it ships versi/preview_image/status instead of version/screenshot/author/is_installed) — part
        // of a wider schema<->code mismatch that must be reconciled (see docs). Wrapped in try/catch so a
        // column mismatch degrades gracefully (theme just isn't auto-registered) instead of a 500.
        try {
            $exists = $db->fetchOne("SELECT id, deskripsi FROM themes WHERE slug = ?", [$dir]);
            if (!$exists) {
                $db->execute(
                    "INSERT INTO themes (slug, nama, deskripsi, author, version, screenshot, is_installed, is_active)
                     VALUES (?, ?, ?, ?, ?, ?, 1, 0)",
                    [$dir, $nama, $deskripsi, $author, $version, $screenshot]
                );
            } elseif ($meta && empty($exists['deskripsi'])) {
                // Backfill metadata from theme.json for rows detected before (without overwriting manual edits).
                $db->execute(
                    "UPDATE themes SET nama = ?, deskripsi = ?, author = ?, version = ?, screenshot = ? WHERE slug = ?",
                    [$nama, $deskripsi, $author, $version, $screenshot, $dir]
                );
            }
        } catch (\Throwable $e) {
            // schema mismatch or DB error — skip auto-registration for this theme
        }
    }
}

$themes = $db->fetchAll("SELECT * FROM themes ORDER BY is_active DESC, nama ASC");
$active_theme = get_active_theme();
$csrf = generate_csrf();
$show_add = $_GET['action'] ?? '' === 'add';
?>

<div class="page-header">
  <div>
    <h1>🎨 Template Manager</h1>
    <div class="page-header-sub">Kelola template website. Aktifkan satu template sekaligus.</div>
  </div>
  <div class="page-actions">
    <a href="<?= admin_url('?page=template&action=add') ?>" class="btn btn-primary">+ Daftarkan Template Baru</a>
  </div>
</div>

<?php if ($_GET['action'] ?? '' === 'add'): ?>

<div class="card">
  <div class="card-header">
    <div>
      <div class="card-title">📦 Daftarkan Template Baru</div>
      <div class="card-subtitle">Tambah entry template baru. File template harus sudah diupload ke folder <code>/themes/[slug]/</code></div>
    </div>
  </div>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="action" value="add_theme">
    
    <div class="form-row">
      <div class="form-group">
        <label>Slug Template *</label>
        <input type="text" name="slug" required pattern="[a-z0-9-]+" placeholder="modern-business">
        <div class="form-hint">Harus sama dengan nama folder di <code>/themes/[slug]/</code></div>
      </div>
      <div class="form-group">
        <label>Nama Template *</label>
        <input type="text" name="nama" required placeholder="Modern Business">
      </div>
    </div>
    
    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="deskripsi" rows="2" placeholder="Template modern untuk bisnis advertising..."></textarea>
    </div>
    
    <div class="form-row cols-3">
      <div class="form-group">
        <label>Author</label>
        <input type="text" name="author" placeholder="Reklamepedia">
      </div>
      <div class="form-group">
        <label>Versi</label>
        <input type="text" name="version" placeholder="1.0" value="1.0">
      </div>
      <div class="form-group">
        <label>URL Demo</label>
        <input type="url" name="demo_url" placeholder="https://demo.example.com">
      </div>
    </div>
    
    <div class="form-row">
      <div class="form-group">
        <label>Screenshot URL (jika hosted di luar)</label>
        <input type="url" name="screenshot" placeholder="https://...">
      </div>
      <div class="form-group">
        <label>Atau Upload Screenshot</label>
        <input type="file" name="screenshot_file" accept="image/*">
      </div>
    </div>
    
    <div class="alert alert-info">
      ℹ️ <strong>Cara menambah template baru:</strong>
      <ol style="margin:8px 0 0 20px;line-height:1.7">
        <li>Upload folder template ke <code>/themes/[slug]/</code> via FTP/File Manager</li>
        <li>Pastikan ada file: <code>/themes/[slug]/templates/pages/home.php</code> dan lainnya</li>
        <li>Daftarkan template di form ini dengan slug yang sama</li>
        <li>Klik tombol "Aktifkan" pada template baru</li>
      </ol>
    </div>
    
    <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;border-top:1px solid var(--border)">
      <a href="<?= admin_url('?page=template') ?>" class="btn btn-secondary">Batal</a>
      <button type="submit" class="btn btn-primary">💾 Daftarkan</button>
    </div>
  </form>
</div>

<?php else: ?>

<div class="grid grid-3">
  <?php foreach ($themes as $t): 
    $is_active = (int)$t['is_active'];
    $theme_dir_exists = is_dir(BASE_PATH . '/themes/' . $t['slug']);
  ?>
  <div class="card" style="<?= $is_active ? 'border:2px solid var(--primary);box-shadow:0 0 0 4px rgba(37,99,235,0.08)' : '' ?>;padding:0;overflow:hidden">
    
    <!-- Screenshot -->
    <div style="aspect-ratio:16/10;background:var(--surface-2);position:relative;overflow:hidden">
      <?php if ($t['screenshot']): ?>
        <img src="<?= htmlspecialchars($t['screenshot']) ?>" style="width:100%;height:100%;object-fit:cover" alt="<?= htmlspecialchars($t['nama']) ?>">
      <?php else: ?>
        <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:48px;opacity:0.3">🎨</div>
      <?php endif; ?>
      <?php if ($is_active): ?>
        <div style="position:absolute;top:12px;left:12px;background:var(--primary);color:white;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600">
          ✓ AKTIF
        </div>
      <?php endif; ?>
      <?php if (!$theme_dir_exists): ?>
        <div style="position:absolute;top:12px;right:12px;background:var(--danger);color:white;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600">
          ⚠ File Hilang
        </div>
      <?php endif; ?>
    </div>
    
    <!-- Info -->
    <div style="padding:18px">
      <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:6px">
        <div style="font-size:16px;font-weight:700"><?= htmlspecialchars($t['nama']) ?></div>
        <span class="badge badge-gray">v<?= htmlspecialchars($t['version']) ?></span>
      </div>
      <div style="font-size:11px;color:var(--text-muted);margin-bottom:10px">
        oleh <?= htmlspecialchars($t['author'] ?: 'Unknown') ?>
      </div>
      <div style="font-size:13px;color:var(--text-muted);line-height:1.6;margin-bottom:16px;min-height:60px">
        <?= htmlspecialchars($t['deskripsi'] ?: 'Tidak ada deskripsi.') ?>
      </div>
      
      <div style="display:flex;gap:8px">
        <?php if ($is_active): ?>
          <button class="btn btn-success flex-1" disabled>✓ Sedang Aktif</button>
        <?php elseif (!$theme_dir_exists): ?>
          <button class="btn btn-secondary flex-1" disabled title="Folder template tidak ditemukan">Tidak Tersedia</button>
        <?php else: ?>
          <form method="POST" style="flex:1" onsubmit="return confirm('Aktifkan template &quot;<?= htmlspecialchars($t['nama']) ?>&quot;? Tampilan website akan langsung berubah.')">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="action" value="activate">
            <input type="hidden" name="slug" value="<?= htmlspecialchars($t['slug']) ?>">
            <button type="submit" class="btn btn-primary btn-block">Aktifkan</button>
          </form>
        <?php endif; ?>
        
        <?php if ($t['demo_url']): ?>
          <a href="<?= htmlspecialchars($t['demo_url']) ?>" target="_blank" class="btn btn-secondary">👁 Demo</a>
        <?php endif; ?>
      </div>
      
      <?php if (!$is_active && $t['slug'] !== 'default'): ?>
      <form method="POST" style="margin-top:8px" onsubmit="return confirm('Hapus template ini dari daftar? (File template di folder TIDAK akan terhapus)')">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="slug" value="<?= htmlspecialchars($t['slug']) ?>">
        <button type="submit" class="btn btn-ghost btn-sm btn-block" style="color:var(--danger)">🗑 Hapus dari Daftar</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="card mt-3" style="background:#EFF6FF;border-color:#BFDBFE">
  <div style="display:flex;gap:14px;align-items:flex-start">
    <div style="font-size:28px">💡</div>
    <div style="flex:1">
      <div style="font-weight:700;margin-bottom:6px">Cara Menambahkan Template Baru</div>
      <div style="font-size:13px;color:var(--text-muted);line-height:1.7">
        <strong>Langkah 1:</strong> Upload folder template via File Manager Hostinger ke <code>public_html/themes/[nama-template]/</code><br>
        <strong>Langkah 2:</strong> Pastikan struktur file sesuai (templates/pages/, assets/, dll)<br>
        <strong>Langkah 3:</strong> Klik tombol <strong>"+ Daftarkan Template Baru"</strong> di atas dan isi info template (nama, deskripsi, URL demo, dll)<br>
        <strong>Langkah 4:</strong> Klik tombol <strong>"Aktifkan"</strong> pada template yang ingin digunakan
      </div>
    </div>
  </div>
</div>

<?php endif; ?>
