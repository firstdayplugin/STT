<?php
// Konten Halaman - Edit semua text per element per halaman
$db = Database::getInstance();

$pages_meta = [
    'home'    => ['label' => 'Halaman Home / Beranda',   'icon' => 'home'],
    'about'   => ['label' => 'Halaman Tentang Kami',     'icon' => 'users'],
    'layanan' => ['label' => 'Halaman Layanan (List)',   'icon' => 'palette'],
    'gallery' => ['label' => 'Halaman Galeri',           'icon' => 'image'],
    'blog'    => ['label' => 'Halaman Blog',             'icon' => 'blog'],
    'produk'  => ['label' => 'Halaman Produk',           'icon' => 'product'],
    'kontak'  => ['label' => 'Halaman Kontak',           'icon' => 'phone'],
    'global'  => ['label' => 'Global (Footer CTA, dll)', 'icon' => 'globe'],
];

$current_page = $_GET['p'] ?? 'home';
if (!isset($pages_meta[$current_page])) $current_page = 'home';

// Handle save - BULLETPROOF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_content'])) {
    // Always redirect back to this page, even on error
    $redirect_to = admin_url('?page=content&p=' . urlencode($_POST['page_key'] ?? $current_page));
    
    $save_lang = $_POST['lang'] ?? (function_exists('default_lang') ? default_lang() : 'id');
    if (function_exists('available_langs') && !in_array($save_lang, available_langs(), true)) {
        $save_lang = function_exists('default_lang') ? default_lang() : 'id';
    }
    $redirect_to .= '&lang=' . urlencode($save_lang);

    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token keamanan tidak valid. Silakan refresh halaman dan coba lagi.');
        redirect($redirect_to);
    }

    try {
        $page_key = $_POST['page_key'] ?? 'home';
        $blocks   = $_POST['blocks'] ?? [];
        $saved = 0;
        $created = 0;
        $def_lang = $save_lang;
        foreach ($blocks as $key => $val) {
            $val = (string)$val;
            $existing = $db->fetchOne(
                "SELECT id FROM content_blocks WHERE page_key = ? AND block_key = ? AND lang = ?",
                [$page_key, $key, $def_lang]
            );
            if ($existing) {
                $db->execute(
                    "UPDATE content_blocks SET konten = ? WHERE id = ?",
                    [$val, $existing['id']]
                );
                $saved++;
            } else {
                // Create new block if doesn't exist (default language)
                $db->execute(
                    "INSERT INTO content_blocks (page_key, block_key, konten, lang, is_active) VALUES (?, ?, ?, ?, 1)",
                    [$page_key, $key, $val, $def_lang]
                );
                $created++;
            }
        }
        
        // Invalidate any in-memory cache
        unset($GLOBALS['__content_cache']);
        
        log_activity('content_update', "Update $saved blok, buat $created blok baru di halaman: " . ($pages_meta[$page_key]['label'] ?? $page_key));
        
        $msg = "Berhasil menyimpan! ";
        if ($saved > 0) $msg .= "$saved blok diupdate. ";
        if ($created > 0) $msg .= "$created blok baru dibuat.";
        set_flash('success', $msg);
    } catch (Throwable $e) {
        set_flash('error', 'Gagal menyimpan: ' . $e->getMessage());
    }
    
    redirect($redirect_to);
}

// Load blocks for current page
// NOTE: For home page, hero_* blocks are CENTRALIZED in Pengaturan > Hero (single source of truth)
// We exclude them here so admin doesn't get confused which one to edit.
$exclude_hero = ($current_page === 'home');
$def_lang  = function_exists('default_lang') ? default_lang() : 'id';
$all_langs = function_exists('available_langs') ? available_langs() : ['id'];
$edit_lang = $_GET['lang'] ?? $def_lang;
if (!in_array($edit_lang, $all_langs, true)) $edit_lang = $def_lang;
try {
    // Canonical field list = default-language blocks (defines which fields exist).
    $heroFilter = $exclude_hero ? " AND block_key NOT LIKE 'hero_%'" : '';
    $blocks = $db->fetchAll(
        "SELECT * FROM content_blocks WHERE page_key = ? AND lang = ? AND is_active = 1$heroFilter ORDER BY urutan ASC, id ASC",
        [$current_page, $def_lang]
    );
    // Overlay the active-language values when translating.
    $trans = [];
    if ($edit_lang !== $def_lang) {
        foreach ($db->fetchAll("SELECT block_key, konten FROM content_blocks WHERE page_key = ? AND lang = ?", [$current_page, $edit_lang]) as $tr) {
            $trans[$tr['block_key']] = $tr['konten'];
        }
    }
} catch (Throwable $e) {
    set_flash('error', 'Database error: ' . $e->getMessage());
    $blocks = []; $trans = [];
}
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1><?= icon('content', 16) ?> Konten Halaman</h1>
    <div class="page-header-sub">Edit semua text di website. Pilih halaman lalu edit per elemen.</div>
  </div>
</div>

<!-- Page selector -->
<div class="card mb-3">
  <div style="font-size:13px;font-weight:600;color:var(--text-muted);margin-bottom:12px">Pilih Halaman:</div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px">
    <?php foreach ($pages_meta as $key => $meta): ?>
    <a href="<?= admin_url('?page=content&p=' . urlencode($key)) ?>"
       style="display:flex;align-items:center;gap:10px;padding:12px 14px;border:1.5px solid <?= $current_page === $key ? 'var(--primary)' : 'var(--border)' ?>;border-radius:10px;background:<?= $current_page === $key ? 'var(--primary-soft, #EFF6FF)' : 'white' ?>;text-decoration:none;color:inherit;transition:all 0.15s">
      <div style="flex-shrink:0"><?= icon($meta['icon'], 22) ?></div>
      <div>
        <div style="font-size:13px;font-weight:600;color:<?= $current_page === $key ? 'var(--primary)' : 'var(--text)' ?>"><?= htmlspecialchars($meta['label']) ?></div>
        <div style="font-size:11px;color:var(--text-muted)"><?= ucfirst($key) ?></div>
      </div>
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
  <?php if ($edit_lang !== $def_lang): ?><span style="font-size:12px;color:var(--text-muted);margin-left:auto"><?= icon('info', 13) ?> Kosongkan field untuk memakai teks <?= htmlspecialchars(strtoupper($def_lang)) ?>.</span><?php endif; ?>
</div>
<?php endif; ?>

<?php if ($current_page === 'home'): ?>
<div class="card" style="background:#FFF7E0;border:1px solid #F0B100;margin-bottom:16px">
  <div style="display:flex;gap:12px;align-items:flex-start;padding:14px 16px">
    <div style="font-size:22px;flex-shrink:0"><?= icon('lightbulb', 16) ?></div>
    <div style="font-size:13px;color:#7C5A00;line-height:1.6">
      <strong>Pengaturan HERO homepage (judul, subtitle, gambar, slideshow, CTA) sekarang dipusatkan di:</strong>
      <a href="<?= admin_url('?page=pengaturan&tab=hero') ?>" style="color:#7C5A00;text-decoration:underline;font-weight:700">Pengaturan <?= icon('arrow-right', 16) ?> <?= icon('film', 16) ?> Hero/Slide</a>.<br>
      Konten di halaman ini hanya untuk section <em>selain</em> hero (About, Performance, Services, FAQ, dll).
    </div>
  </div>
</div>
<?php endif; ?>

<?php if (empty($blocks)): ?>
  <div class="card">
    <div class="empty-state">
      <div class="empty-state-icon"><?= icon('content', 16) ?></div>
      <div class="empty-title">Belum ada blok konten untuk halaman ini</div>
      <div class="empty-text">Halaman akan menggunakan teks default. Buka halaman ini di website untuk lihat versi default.</div>
    </div>
  </div>
<?php else: ?>

<form method="POST" id="content-form">
  <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
  <input type="hidden" name="save_content" value="1">
  <input type="hidden" name="page_key" value="<?= htmlspecialchars($current_page) ?>">
  <input type="hidden" name="lang" value="<?= htmlspecialchars($edit_lang) ?>">
  
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title"><?= icon($pages_meta[$current_page]['icon'], 18) ?> Edit Konten: <?= htmlspecialchars($pages_meta[$current_page]['label']) ?></div>
        <div class="card-subtitle"><?= count($blocks) ?> elemen tersedia untuk diedit</div>
      </div>
    </div>
    
    <?php foreach ($blocks as $b):
      $type = $b['block_type'] ?? 'text';
      $label = $b['block_label'] ?? $b['block_key'];
      $default_val = $b['konten'] ?? '';
      $translating = ($edit_lang !== $def_lang);
      $value = $translating ? ($trans[$b['block_key']] ?? '') : $default_val;
      $field_name = 'blocks[' . htmlspecialchars($b['block_key']) . ']';
      $ph = $translating ? mb_substr(strip_tags($default_val), 0, 120) : '';
    ?>
    <div class="form-group">
      <label style="display:flex;align-items:center;justify-content:space-between">
        <span><?= htmlspecialchars($label) ?></span>
        <code style="font-size:10px;color:var(--text-muted);font-weight:normal"><?= htmlspecialchars($b['block_key']) ?></code>
      </label>
      <?php if ($translating && $default_val !== ''): ?>
        <div style="font-size:12px;color:var(--text-muted);background:var(--bg-soft,#f6f9fd);border:1px solid var(--border);border-radius:8px;padding:7px 10px;margin-bottom:6px"><?= htmlspecialchars(strtoupper($def_lang)) ?>: <?= htmlspecialchars(mb_substr(strip_tags($default_val), 0, 200)) ?></div>
      <?php endif; ?>
      <?php if ($type === 'textarea' || $type === 'html'): ?>
        <textarea name="<?= $field_name ?>" rows="3" class="wysiwyg"><?= htmlspecialchars($value) ?></textarea>
      <?php else: ?>
        <input type="text" name="<?= $field_name ?>" value="<?= htmlspecialchars($value) ?>"<?= $ph !== '' ? ' placeholder="' . htmlspecialchars($ph) . '"' : '' ?>>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  
  <!-- Sticky save bar -->
  <div style="position:sticky;bottom:0;background:white;padding:16px;border-top:2px solid var(--border);margin:16px -16px -16px;display:flex;justify-content:space-between;align-items:center;z-index:10;box-shadow:0 -4px 12px rgba(0,0,0,0.04)">
    <div style="font-size:13px;color:var(--text-muted)">
      <?= icon('save', 16) ?> <?= count($blocks) ?> field siap disimpan
    </div>
    <button type="submit" class="btn btn-primary btn-lg"><?= icon('save', 16) ?> Simpan Perubahan</button>
  </div>
</form>

<script>
// Show loading state on submit
document.getElementById('content-form')?.addEventListener('submit', function() {
  const btn = this.querySelector('button[type=submit]');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = 'Menyimpan...';
  }
});
</script>

<?php endif; ?>
