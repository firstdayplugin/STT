<?php
/**
 * Konten Halaman — registry-driven editor.
 * Editable fields come from the theme content registry (themes/<theme>/registry/<page>.php),
 * so every string the templates read via ac()/hc()/aimg() is editable here — pre-filled with
 * the stored value or the registry default. Saving upserts rows into `content_blocks` keyed by
 * the same page_key/lang the frontend reads. Supports text, html (WYSIWYG) and image fields.
 */
$db = Database::getInstance();

// Pages that have a content registry (label + icon for the picker). page_key MUST match
// what the templates pass to ac()/hc() (home, about, contact).
$pages_meta = [
    'home'      => ['label' => 'Halaman Home / Beranda', 'icon' => 'home'],
    'about'     => ['label' => 'Halaman Tentang Kami',   'icon' => 'users'],
    'solutions' => ['label' => 'Halaman Solutions',      'icon' => 'layers'],
    'industri'  => ['label' => 'Halaman Industries',     'icon' => 'compass'],
    'blog'      => ['label' => "Halaman What's New",     'icon' => 'blog'],
    'contact'   => ['label' => 'Halaman Kontak',         'icon' => 'phone'],
];

$theme_registry = function (string $page): array {
    $safe = preg_replace('/[^a-z0-9_-]/', '', $page);
    $f = theme_path('registry/' . $safe . '.php');
    if (!is_file($f)) return [];
    $r = include $f;
    return is_array($r) ? $r : [];
};

$current_page = $_GET['p'] ?? 'home';
if (!isset($pages_meta[$current_page])) $current_page = 'home';

$def_lang  = function_exists('default_lang') ? default_lang() : 'id';
$all_langs = function_exists('available_langs') ? available_langs() : ['id'];

// ---- Save ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_content'])) {
    $page_key = $_POST['page_key'] ?? $current_page;
    if (!isset($pages_meta[$page_key])) $page_key = 'home';
    $save_lang = $_POST['lang'] ?? $def_lang;
    if (!in_array($save_lang, $all_langs, true)) $save_lang = $def_lang;

    $redirect_to = admin_url('?page=content&p=' . urlencode($page_key) . '&lang=' . urlencode($save_lang));
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token keamanan tidak valid. Refresh lalu coba lagi.');
        redirect($redirect_to);
    }

    $reg = $theme_registry($page_key);
    $saved = 0;

    // upsert helper (per page_key/block_key/lang)
    $put = function (string $key, string $val, string $label, string $type) use ($db, $page_key, $save_lang, &$saved) {
        $existing = $db->fetchOne("SELECT id FROM content_blocks WHERE page_key=? AND block_key=? AND lang=?", [$page_key, $key, $save_lang]);
        if ($existing) {
            $db->execute("UPDATE content_blocks SET konten=?, block_label=?, block_type=? WHERE id=?", [$val, $label, $type, $existing['id']]);
        } else {
            $db->execute("INSERT INTO content_blocks (page_key, block_key, lang, block_label, block_type, konten, is_active) VALUES (?,?,?,?,?,?,1)", [$page_key, $key, $save_lang, $label, $type, $val]);
        }
        $saved++;
    };

    try {
        // Text / HTML fields.
        foreach (($_POST['blocks'] ?? []) as $key => $val) {
            if (!isset($reg[$key])) continue;                       // only known registry keys
            $type = $reg[$key]['type'] ?? 'text';
            if ($type === 'image') continue;                        // images handled below
            $put((string)$key, (string)$val, (string)($reg[$key]['label'] ?? $key), $type);
        }

        // Image fields (default language only — one image shared across languages).
        if ($save_lang === $def_lang) {
            foreach ($reg as $key => $conf) {
                if (($conf['type'] ?? '') !== 'image') continue;
                $label = (string)($conf['label'] ?? $key);
                // Remove?
                if (!empty($_POST['img_clear'][$key])) { $put((string)$key, '', $label, 'image'); continue; }
                // New upload?
                if (!empty($_FILES['img']['name'][$key])) {
                    $file = [
                        'name'     => $_FILES['img']['name'][$key],
                        'type'     => $_FILES['img']['type'][$key],
                        'tmp_name' => $_FILES['img']['tmp_name'][$key],
                        'error'    => $_FILES['img']['error'][$key],
                        'size'     => $_FILES['img']['size'][$key],
                    ];
                    $stored = upload_image($file, 'content/' . $page_key);
                    if ($stored) { $put((string)$key, $stored, $label, 'image'); }
                    else { set_flash('error', 'Sebagian gambar gagal diunggah (pastikan JPG/PNG/WEBP dan ukuran wajar).'); }
                }
                // Manual URL entry (optional) — only when no file uploaded.
                elseif (isset($_POST['img_url'][$key]) && trim($_POST['img_url'][$key]) !== '') {
                    $put((string)$key, trim($_POST['img_url'][$key]), $label, 'image');
                }
            }
        }

        unset($GLOBALS['__content_cache']);
        log_activity('content_update', "Update $saved field konten di halaman: " . ($pages_meta[$page_key]['label'] ?? $page_key) . " ($save_lang)");
        set_flash('success', "Tersimpan. $saved field diperbarui.");
    } catch (Throwable $e) {
        set_flash('error', 'Gagal menyimpan: ' . $e->getMessage());
    }
    redirect($redirect_to);
}

// ---- Load ----
$edit_lang = $_GET['lang'] ?? $def_lang;
if (!in_array($edit_lang, $all_langs, true)) $edit_lang = $def_lang;
$translating = ($edit_lang !== $def_lang);

$reg = $theme_registry($current_page);
// Stored values for edit language + default language (for the "default" hint / image source).
$valEdit = []; $valDef = [];
try {
    foreach ($db->fetchAll("SELECT block_key, konten FROM content_blocks WHERE page_key=? AND lang=?", [$current_page, $edit_lang]) as $r) $valEdit[$r['block_key']] = $r['konten'];
    if ($translating) foreach ($db->fetchAll("SELECT block_key, konten FROM content_blocks WHERE page_key=? AND lang=?", [$current_page, $def_lang]) as $r) $valDef[$r['block_key']] = $r['konten'];
} catch (Throwable $e) { /* table issue → registry defaults still render */ }

// Group registry fields by 'group'.
$groups = [];
foreach ($reg as $key => $conf) {
    $g = $conf['group'] ?? 'Umum';
    $groups[$g][$key] = $conf;
}
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1><?= icon('content', 16) ?> Konten Halaman</h1>
    <div class="page-header-sub">Semua teks &amp; gambar tiap halaman — semuanya bisa diedit di sini.</div>
  </div>
</div>

<!-- Page selector -->
<div class="card mb-3">
  <div style="font-size:13px;font-weight:600;color:var(--text-muted);margin-bottom:12px">Pilih Halaman:</div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px">
    <?php foreach ($pages_meta as $key => $meta): $on = $current_page === $key; ?>
    <a href="<?= admin_url('?page=content&p=' . urlencode($key)) ?>"
       style="display:flex;align-items:center;gap:10px;padding:12px 14px;border:1.5px solid <?= $on ? 'var(--primary)' : 'var(--border)' ?>;border-radius:10px;background:<?= $on ? 'var(--primary-soft,#EFF6FF)' : 'white' ?>;text-decoration:none;color:inherit">
      <div style="flex-shrink:0"><?= icon($meta['icon'], 22) ?></div>
      <div><div style="font-size:13px;font-weight:600;color:<?= $on ? 'var(--primary)' : 'var(--text)' ?>"><?= htmlspecialchars($meta['label']) ?></div>
        <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars($key) ?></div></div>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php if (count($all_langs) > 1): ?>
<div class="card mb-3" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
  <span style="font-size:12px;color:var(--text-muted);font-weight:600"><?= icon('globe', 15) ?> BAHASA</span>
  <?php foreach ($all_langs as $L): ?>
    <a href="<?= admin_url('?page=content&p=' . urlencode($current_page) . '&lang=' . urlencode($L)) ?>"
       class="btn btn-sm <?= $edit_lang === $L ? 'btn-primary' : 'btn-secondary' ?>"><?= htmlspecialchars(strtoupper($L)) ?><?= $L === $def_lang ? ' (default)' : '' ?></a>
  <?php endforeach; ?>
  <?php if ($translating): ?><span style="font-size:12px;color:var(--text-muted);margin-left:auto"><?= icon('info', 13) ?> Kosongkan field untuk memakai teks <?= htmlspecialchars(strtoupper($def_lang)) ?>. Gambar hanya diedit di bahasa default.</span><?php endif; ?>
</div>
<?php endif; ?>

<?php if ($current_page === 'home'): ?>
<div class="card" style="background:#FFF7E0;border:1px solid #F0B100;margin-bottom:16px">
  <div style="display:flex;gap:12px;align-items:flex-start;padding:14px 16px">
    <div style="flex-shrink:0"><?= icon('lightbulb', 16) ?></div>
    <div style="font-size:13px;color:#7C5A00;line-height:1.6">
      <strong>Hero homepage (judul, subtitle, gambar slider, CTA)</strong> diatur di
      <a href="<?= admin_url('?page=pengaturan&tab=hero') ?>" style="color:#7C5A00;text-decoration:underline;font-weight:700">Pengaturan → Hero/Slide</a>.
      Animasi Cube &amp; Orbit diatur di menu <strong>Solutions (Cube)</strong> &amp; <strong>Industries (Orbit)</strong>.
    </div>
  </div>
</div>
<?php endif; ?>

<?php if (empty($reg)): ?>
  <div class="card"><div class="empty-state"><div class="empty-state-icon"><?= icon('content', 40) ?></div>
    <div class="empty-title">Registry konten untuk halaman ini belum tersedia.</div></div></div>
<?php else: ?>

<form method="POST" id="content-form" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
  <input type="hidden" name="save_content" value="1">
  <input type="hidden" name="page_key" value="<?= htmlspecialchars($current_page) ?>">
  <input type="hidden" name="lang" value="<?= htmlspecialchars($edit_lang) ?>">

  <?php foreach ($groups as $gname => $fields): ?>
  <div class="card" style="margin-bottom:16px">
    <div class="card-header"><div class="card-title"><?= htmlspecialchars($gname) ?></div></div>
    <div class="card-body">
      <?php foreach ($fields as $key => $conf):
        $type   = $conf['type'] ?? 'text';
        $label  = $conf['label'] ?? $key;
        $default= (string)($conf['default'] ?? '');
        $stored = $valEdit[$key] ?? '';
        // Effective value shown in the field.
        $value  = $stored !== '' ? $stored : ($translating ? '' : $default);
      ?>
      <div class="form-group">
        <label style="display:flex;align-items:center;justify-content:space-between">
          <span><?= htmlspecialchars($label) ?></span>
          <code style="font-size:10px;color:var(--text-muted);font-weight:normal"><?= htmlspecialchars($key) ?></code>
        </label>

        <?php if ($type === 'image'): ?>
          <?php
            $imgStored = $valEdit[$key] ?? '';
            $imgUrl = $imgStored !== '' ? (preg_match('#^https?://#i', $imgStored) ? $imgStored : uploads_url($imgStored)) : '';
          ?>
          <?php if ($translating): ?>
            <div style="font-size:12px;color:var(--text-muted)"><?= icon('info', 13) ?> Gambar dikelola di bahasa default (<?= htmlspecialchars(strtoupper($def_lang)) ?>).</div>
          <?php else: ?>
            <div style="display:flex;gap:14px;align-items:flex-start;flex-wrap:wrap">
              <div style="width:120px;height:80px;border:1px solid var(--border);border-radius:8px;overflow:hidden;background:#f3f6fb;display:grid;place-items:center;flex-shrink:0">
                <?php if ($imgUrl): ?><img src="<?= htmlspecialchars($imgUrl) ?>" alt="" style="width:100%;height:100%;object-fit:cover"><?php else: ?><span style="font-size:11px;color:var(--text-muted)">kosong</span><?php endif; ?>
              </div>
              <div style="flex:1;min-width:220px">
                <input type="file" name="img[<?= htmlspecialchars($key) ?>]" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                <input type="text" name="img_url[<?= htmlspecialchars($key) ?>]" placeholder="atau tempel URL gambar (opsional)" style="margin-top:6px">
                <?php if ($imgUrl): ?>
                <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-muted);margin-top:6px;font-weight:normal">
                  <input type="checkbox" name="img_clear[<?= htmlspecialchars($key) ?>]" value="1" style="width:auto"> Hapus gambar ini
                </label>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>

        <?php elseif ($type === 'html'): ?>
          <?php if ($translating && ($valDef[$key] ?? $default) !== ''): ?>
            <div style="font-size:12px;color:var(--text-muted);background:var(--bg-soft,#f6f9fd);border:1px solid var(--border);border-radius:8px;padding:7px 10px;margin-bottom:6px"><?= htmlspecialchars(strtoupper($def_lang)) ?>: <?= htmlspecialchars(mb_substr(strip_tags($valDef[$key] ?? $default), 0, 180)) ?></div>
          <?php endif; ?>
          <textarea name="blocks[<?= htmlspecialchars($key) ?>]" rows="3" class="wysiwyg"><?= htmlspecialchars($value) ?></textarea>

        <?php else: ?>
          <?php if ($translating && ($valDef[$key] ?? $default) !== ''): ?>
            <div style="font-size:12px;color:var(--text-muted);background:var(--bg-soft,#f6f9fd);border:1px solid var(--border);border-radius:8px;padding:7px 10px;margin-bottom:6px"><?= htmlspecialchars(strtoupper($def_lang)) ?>: <?= htmlspecialchars(mb_substr(strip_tags($valDef[$key] ?? $default), 0, 180)) ?></div>
          <?php endif; ?>
          <input type="text" name="blocks[<?= htmlspecialchars($key) ?>]" value="<?= htmlspecialchars($value) ?>"<?= $translating ? ' placeholder="' . htmlspecialchars(mb_substr(strip_tags($valDef[$key] ?? $default), 0, 120)) . '"' : '' ?>>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>

  <div style="position:sticky;bottom:0;background:white;padding:16px;border-top:2px solid var(--border);margin:16px -16px -16px;display:flex;justify-content:space-between;align-items:center;z-index:10;box-shadow:0 -4px 12px rgba(0,0,0,0.04)">
    <div style="font-size:13px;color:var(--text-muted)"><?= icon('save', 16) ?> <?= count($reg) ?> field<?= $translating ? ' · bahasa ' . htmlspecialchars(strtoupper($edit_lang)) : '' ?></div>
    <button type="submit" class="btn btn-primary btn-lg"><?= icon('save', 16) ?> Simpan Perubahan</button>
  </div>
</form>

<?php endif; ?>
