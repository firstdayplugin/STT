<?php
$page_title  = $page_title  ?? ucfirst($request);
$page_icon   = $page_icon   ?? '';
$breadcrumbs = $breadcrumbs ?? [];

$site_name = get_setting('site_name', 'Reklamepedia');
$logo      = get_setting('logo');
$doc_url   = get_setting('docs_url', '#');

// Sidebar navigation - LIGHT THEME, simple icons
$nav_sections = [
    'Utama' => [
        ['page'=>'dashboard', 'label'=>'Dashboard', 'icon'=>'🏠'],
        ['page'=>'wizard',    'label'=>'Setup Wizard', 'icon'=>'🚀'],
    ],
    'Konten' => [
        ['page'=>'content',   'label'=>'Konten Halaman', 'icon'=>'📝', 'highlight'=>true],
        ['page'=>'blog',      'label'=>'Blog / Artikel', 'icon'=>'📰',
            'children'=>[
                ['url'=>admin_url('?page=blog'),                          'label'=>'Semua Artikel', 'match'=>fn($p,$a)=>$p==='blog' && !in_array($a,['create','kategori'])],
                ['url'=>admin_url('?page=blog&action=create'),            'label'=>'Tambah Artikel', 'match'=>fn($p,$a)=>$p==='blog' && $a==='create'],
                ['url'=>admin_url('?page=blog-kategori'),                 'label'=>'Kategori', 'match'=>fn($p,$a)=>$p==='blog-kategori'],
            ]],
        ['page'=>'produk',    'label'=>'Produk',         'icon'=>'📦',
            'children'=>[
                ['url'=>admin_url('?page=produk'),                        'label'=>'Semua Produk', 'match'=>fn($p,$a)=>$p==='produk' && !in_array($a,['create','kategori'])],
                ['url'=>admin_url('?page=produk&action=create'),          'label'=>'Tambah Produk', 'match'=>fn($p,$a)=>$p==='produk' && $a==='create'],
                ['url'=>admin_url('?page=produk-kategori'),               'label'=>'Kategori', 'match'=>fn($p,$a)=>$p==='produk-kategori'],
            ]],
        ['page'=>'layanan',   'label'=>'Layanan',        'icon'=>'🎨'],
        ['page'=>'gallery',   'label'=>'Galeri',         'icon'=>'🖼️'],
        ['page'=>'testimonial','label'=>'Testimoni',     'icon'=>'💬'],
        ['page'=>'faq',       'label'=>'FAQ',            'icon'=>'❓'],
        ['page'=>'klien-logo','label'=>'Logo Klien',     'icon'=>'🏢'],
        ['page'=>'flex-blocks','label'=>'Content Block', 'icon'=>'📋'],
        ['page'=>'grid-icon', 'label'=>'Grid Icon Box',  'icon'=>'🎁'],
        ['page'=>'pages',     'label'=>'Halaman Custom', 'icon'=>'📄'],
        ['page'=>'menu',      'label'=>'Menu Navigasi',  'icon'=>'☰'],
    ],
    'Marketing' => [
        ['page'=>'seo',       'label'=>'SEO per Halaman', 'icon'=>'🔍'],
        ['page'=>'ads',       'label'=>'Iklan & Pixel',   'icon'=>'📊'],
    ],
    'Sistem' => [
        ['page'=>'pengaturan','label'=>'Pengaturan',  'icon'=>'⚙️'],
        ['page'=>'template',  'label'=>'Template',    'icon'=>'🎨'],
        ['page'=>'plugin',    'label'=>'Plugin',      'icon'=>'🔌'],
        ['page'=>'users',     'label'=>'Pengguna',    'icon'=>'👥'],
    ],
];

function can_access_safe($role, $page) {
    if (function_exists('can_access')) return can_access($role, $page);
    return true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?> — Admin <?= htmlspecialchars($site_name) ?></title>
<meta name="robots" content="noindex,nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=DM+Serif+Display:ital@1&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= admin_url('assets/css/admin.css') ?>?v=5">
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>

<div class="admin-layout">

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <a href="<?= admin_url() ?>" style="text-decoration:none">
    <div class="sidebar-brand">
      <div class="sidebar-brand-logo">
        <?php if ($logo): ?>
          <img src="<?= uploads_url($logo) ?>" alt="<?= htmlspecialchars($site_name) ?>" style="height:28px">
        <?php else: 
          $brand_first = mb_substr($site_name, 0, 1);
          $brand_rest  = mb_substr($site_name, 1);
        ?>
          <span class="logo-r"><?= htmlspecialchars($brand_first) ?></span>
          <span><?= htmlspecialchars($brand_rest) ?></span>
        <?php endif; ?>
      </div>
      <div class="sidebar-brand-sub">Admin Panel</div>
    </div>
  </a>

  <div class="sidebar-menu">
    <?php foreach ($nav_sections as $section_name => $items): 
      $visible = array_filter($items, fn($it) => can_access_safe($user['role'] ?? 'superadmin', $it['page']));
      if (empty($visible)) continue;
    ?>
      <div class="sidebar-section"><?= $section_name ?></div>
      <?php 
        $current_action = $_GET['action'] ?? '';
        foreach ($visible as $item):
          $has_children = !empty($item['children']);
          $any_child_active = false;
          if ($has_children) {
            foreach ($item['children'] as $child) {
              if ($child['match']($request, $current_action)) { $any_child_active = true; break; }
            }
          }
          $parent_active = ($request === $item['page']) || $any_child_active
              || ($item['page'] === 'blog' && $request === 'blog-kategori')
              || ($item['page'] === 'produk' && $request === 'produk-kategori');
      ?>
        <?php if ($has_children): ?>
          <div class="sidebar-group <?= $parent_active ? 'open active' : '' ?>">
            <button type="button" class="sidebar-item sidebar-group-toggle <?= $parent_active ? 'active' : '' ?>" onclick="this.parentElement.classList.toggle('open')">
              <span class="sidebar-item-icon"><?= $item['icon'] ?></span>
              <span><?= htmlspecialchars($item['label']) ?></span>
              <svg class="sidebar-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:auto"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
            <div class="sidebar-submenu">
              <?php foreach ($item['children'] as $child):
                $is_active = $child['match']($request, $current_action);
              ?>
              <a href="<?= htmlspecialchars($child['url']) ?>" class="sidebar-subitem <?= $is_active ? 'active' : '' ?>">
                <span class="sidebar-sub-dash">—</span>
                <span><?= htmlspecialchars($child['label']) ?></span>
              </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php else: ?>
          <a href="<?= admin_url('?page=' . $item['page']) ?>"
             class="sidebar-item <?= $request === $item['page'] ? 'active' : '' ?>">
            <span class="sidebar-item-icon"><?= $item['icon'] ?></span>
            <span><?= htmlspecialchars($item['label']) ?></span>
          </a>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endforeach; ?>

    <div class="sidebar-section">Bantuan</div>
    <a href="<?= htmlspecialchars($doc_url ?: '#') ?>" 
       <?= ($doc_url && $doc_url !== '#') ? 'target="_blank"' : '' ?>
       class="sidebar-item">
      <span class="sidebar-item-icon">📚</span>
      <span>Dokumentasi</span>
    </a>
    <a href="<?= url('/') ?>" target="_blank" class="sidebar-item">
      <span class="sidebar-item-icon">🌐</span>
      <span>Lihat Website</span>
    </a>
  </div>
</aside>

<!-- MAIN -->
<div class="main">
  <header class="topbar">
    <div class="flex items-center gap-3">
      <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
      <div>
        <div class="topbar-title"><?= htmlspecialchars($page_title) ?></div>
        <?php if (!empty($breadcrumbs)): ?>
        <div style="font-size:11px;color:var(--text-muted);margin-top:2px">
          <?php foreach ($breadcrumbs as $i => $bc): ?>
            <?php if ($i > 0): ?><span style="opacity:0.5"> / </span><?php endif; ?>
            <?= is_array($bc) ? '<a href="'.htmlspecialchars($bc['url']).'">'.htmlspecialchars($bc['label']).'</a>' : htmlspecialchars($bc) ?>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
    
    <div class="topbar-right">
      <div class="topbar-user">
        <div class="user-avatar"><?= strtoupper(substr($user['nama'] ?? $user['username'] ?? 'A', 0, 1)) ?></div>
        <div>
          <div class="user-info-name"><?= htmlspecialchars($user['nama'] ?? $user['username'] ?? 'Admin') ?></div>
          <div class="user-info-role"><?= htmlspecialchars(ucfirst($user['role'] ?? 'admin')) ?></div>
        </div>
      </div>
      <a href="<?= admin_url('?page=logout') ?>" 
         onclick="return confirm('Keluar dari panel admin?')"
         class="btn btn-ghost btn-sm">Keluar</a>
    </div>
  </header>

  <main class="content">
    <?= flash_html() ?>
    <?php if (isset($view_file) && file_exists($view_file)) include $view_file; ?>
  </main>
</div>

</div>

<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
}

// Auto-dismiss success alerts after 4 seconds
document.querySelectorAll('.alert-success').forEach(alert => {
  setTimeout(() => {
    alert.style.transition = 'all 0.4s';
    alert.style.opacity = '0';
    alert.style.transform = 'translateY(-10px)';
    setTimeout(() => alert.remove(), 400);
  }, 4000);
});
</script>
<!-- Global TinyMCE init: any <textarea class="wysiwyg"> gets rich text editor -->
<script>
(function(){
  function initWysiwyg() {
    if (typeof tinymce === 'undefined') { setTimeout(initWysiwyg, 200); return; }
    tinymce.init({
      selector: 'textarea.wysiwyg',
      skin: 'oxide',
      content_css: 'default',
      height: 420,
      menubar: 'edit insert view format table',
      branding: false,
      promotion: false,
      relative_urls: false,
      remove_script_host: false,
      convert_urls: true,
      plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount fullscreen code hr',
      toolbar: 'undo redo | blocks fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table blockquote hr | removeformat code fullscreen',
      content_style: 'body { font-family: Inter, system-ui, sans-serif; font-size: 14px; line-height: 1.7; padding: 12px; } img { max-width: 100%; height: auto; }',
      // Image upload integration: POSTs to admin/?ajax=upload_editor_image
      images_upload_url: '<?= admin_url('?ajax=upload_editor_image') ?>',
      images_upload_credentials: true,
      automatic_uploads: true,
      file_picker_types: 'image',
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWysiwyg);
  } else {
    initWysiwyg();
  }
})();
</script>
</body>
</html>
