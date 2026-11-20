<?php
$db = Database::getInstance();

// Stats
$total_blog = $db->fetchOne("SELECT COUNT(*) as c FROM blog WHERE status='published'")['c'] ?? 0;
$total_produk = $db->fetchOne("SELECT COUNT(*) as c FROM produk WHERE status='aktif'")['c'] ?? 0;
$total_gallery = $db->fetchOne("SELECT COUNT(*) as c FROM gallery")['c'] ?? 0;
$total_layanan = $db->fetchOne("SELECT COUNT(*) as c FROM layanan WHERE is_active=1")['c'] ?? 0;
$total_testi = $db->fetchOne("SELECT COUNT(*) as c FROM testimonial WHERE is_active=1")['c'] ?? 0;
$total_faq = $db->fetchOne("SELECT COUNT(*) as c FROM faq WHERE is_active=1")['c'] ?? 0;

// WhatsApp clicks
$wa_clicks_today = 0;
$wa_clicks_week = 0;
$wa_clicks_total = 0;
try {
    $wa_clicks_today = $db->fetchOne("SELECT IFNULL(SUM(clicks), 0) as c FROM wa_clicks WHERE created_at = CURDATE()")['c'] ?? 0;
    $wa_clicks_week = $db->fetchOne("SELECT IFNULL(SUM(clicks), 0) as c FROM wa_clicks WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")['c'] ?? 0;
    $wa_clicks_total = $db->fetchOne("SELECT IFNULL(SUM(clicks), 0) as c FROM wa_clicks")['c'] ?? 0;
} catch (Exception $e) {}

// Visitor stats
$visitor_today = 0;
$visitor_week = 0;
$visitor_month = 0;
try {
    $visitor_today = $db->fetchOne("SELECT SUM(views) as c FROM statistik_visitor WHERE tanggal = CURDATE()")['c'] ?? 0;
    $visitor_week = $db->fetchOne("SELECT SUM(views) as c FROM statistik_visitor WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")['c'] ?? 0;
    $visitor_month = $db->fetchOne("SELECT SUM(views) as c FROM statistik_visitor WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")['c'] ?? 0;
} catch (Exception $e) {}

// Latest items
$latest_blog = $db->fetchAll("SELECT id, judul, slug, created_at FROM blog WHERE status='published' ORDER BY created_at DESC LIMIT 5");
$latest_produk = $db->fetchAll("SELECT id, nama, slug, gambar_utama, created_at FROM produk WHERE status='aktif' ORDER BY created_at DESC LIMIT 5");
$recent_activity = [];
try {
    $recent_activity = $db->fetchAll("SELECT al.*, u.nama as user_nama FROM activity_log al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 8");
} catch (Exception $e) {}

// Storage used (rough estimate)
$uploads_size = 0;
$uploads_path = BASE_PATH . '/uploads';
if (is_dir($uploads_path)) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploads_path, RecursiveDirectoryIterator::SKIP_DOTS)) as $f) {
        if ($f->isFile()) $uploads_size += $f->getSize();
    }
}
$uploads_mb = round($uploads_size / 1024 / 1024, 2);

// SEO status
$seo_pages = $db->fetchAll("SELECT page_key, meta_title, meta_description FROM page_seo");
$seo_complete = 0;
foreach ($seo_pages as $sp) {
    if (!empty($sp['meta_title']) && !empty($sp['meta_description'])) $seo_complete++;
}
$seo_total = count(['home','about','layanan','gallery','blog','produk','kontak']);

$site_name = get_setting('site_name');
$has_wa = (bool)get_setting('wa_number');
$has_logo = (bool)get_setting('logo');
$has_hero = (bool)get_setting('hero_gambar');
$active_theme = get_active_theme();
?>

<div class="page-header">
  <div>
    <h1>Selamat datang, <?= htmlspecialchars($user['nama'] ?? $user['username']) ?></h1>
    <div class="page-header-sub">Berikut ringkasan website Anda hari ini</div>
  </div>
  <div class="page-actions">
    <a href="<?= admin_url('?page=wizard') ?>" class="btn btn-primary btn-lg"><?= icon('rocket', 16) ?> Setup Wizard</a>
  </div>
</div>

<!-- Top Stats -->
<div class="grid grid-4 mb-3">
  <div class="stat-card">
    <div class="stat-card-icon" style="background:#DCFCE7;color:#16A34A"><?= icon('message', 16) ?></div>
    <div class="stat-card-label">Klik WhatsApp Hari Ini</div>
    <div class="stat-card-value"><?= number_format($wa_clicks_today) ?></div>
    <div class="stat-card-change">Total semua: <?= number_format($wa_clicks_total) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon" style="background:#DBEAFE;color:#2563EB"><?= icon('eye', 16) ?></div>
    <div class="stat-card-label">Visitor Hari Ini</div>
    <div class="stat-card-value"><?= number_format($visitor_today) ?></div>
    <div class="stat-card-change">7 hari: <?= number_format($visitor_week) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon" style="background:#FEF3C7;color:#D97706"><?= icon('blog', 16) ?></div>
    <div class="stat-card-label">Total Konten</div>
    <div class="stat-card-value"><?= number_format($total_blog + $total_produk + $total_gallery) ?></div>
    <div class="stat-card-change">Blog, Produk, Galeri</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon" style="background:#FCE7F3;color:#DB2777"><?= icon('save', 16) ?></div>
    <div class="stat-card-label">Storage Gambar</div>
    <div class="stat-card-value"><?= $uploads_mb ?> <span style="font-size:14px;color:var(--text-muted)">MB</span></div>
    <div class="stat-card-change"><?= $total_gallery ?> foto galeri</div>
  </div>
</div>

<!-- Shortcuts -->
<div class="card mb-3">
  <div class="card-header">
    <div class="card-title"><?= icon('zap', 16) ?> Akses Cepat</div>
  </div>
  <div class="grid grid-4" style="gap:10px">
    <a href="<?= admin_url('?page=content') ?>" class="shortcut-card">
      <div class="shortcut-icon"><?= icon('content', 16) ?></div>
      <div class="shortcut-text">
        <div class="shortcut-title">Edit Konten</div>
        <div class="shortcut-sub">Semua text bisa diedit</div>
      </div>
    </a>
    <a href="<?= admin_url('?page=blog') ?>" class="shortcut-card">
      <div class="shortcut-icon" style="background:#FEF3C7;color:#D97706"><?= icon('blog', 16) ?></div>
      <div class="shortcut-text">
        <div class="shortcut-title">Artikel Blog</div>
        <div class="shortcut-sub"><?= $total_blog ?> artikel</div>
      </div>
    </a>
    <a href="<?= admin_url('?page=produk') ?>" class="shortcut-card">
      <div class="shortcut-icon" style="background:#DCFCE7;color:#16A34A"><?= icon('product', 16) ?></div>
      <div class="shortcut-text">
        <div class="shortcut-title">Produk</div>
        <div class="shortcut-sub"><?= $total_produk ?> produk</div>
      </div>
    </a>
    <a href="<?= admin_url('?page=gallery') ?>" class="shortcut-card">
      <div class="shortcut-icon" style="background:#FCE7F3;color:#DB2777"><?= icon('image', 16) ?></div>
      <div class="shortcut-text">
        <div class="shortcut-title">Galeri</div>
        <div class="shortcut-sub"><?= $total_gallery ?> foto</div>
      </div>
    </a>
    <a href="<?= admin_url('?page=layanan') ?>" class="shortcut-card">
      <div class="shortcut-icon" style="background:#EDE9FE;color:#7C3AED"><?= icon('palette', 16) ?></div>
      <div class="shortcut-text">
        <div class="shortcut-title">Layanan</div>
        <div class="shortcut-sub"><?= $total_layanan ?> layanan</div>
      </div>
    </a>
    <a href="<?= admin_url('?page=testimonial') ?>" class="shortcut-card">
      <div class="shortcut-icon" style="background:#FED7AA;color:#EA580C"><?= icon('message', 16) ?></div>
      <div class="shortcut-text">
        <div class="shortcut-title">Testimoni</div>
        <div class="shortcut-sub"><?= $total_testi ?> testimoni</div>
      </div>
    </a>
    <a href="<?= admin_url('?page=faq') ?>" class="shortcut-card">
      <div class="shortcut-icon" style="background:#DBEAFE;color:#2563EB"><?= icon('faq', 16) ?></div>
      <div class="shortcut-text">
        <div class="shortcut-title">FAQ</div>
        <div class="shortcut-sub"><?= $total_faq ?> FAQ</div>
      </div>
    </a>
    <a href="<?= admin_url('?page=seo') ?>" class="shortcut-card">
      <div class="shortcut-icon" style="background:#DCFCE7;color:#16A34A"><?= icon('search', 16) ?></div>
      <div class="shortcut-text">
        <div class="shortcut-title">SEO</div>
        <div class="shortcut-sub"><?= $seo_complete ?>/<?= $seo_total ?> halaman</div>
      </div>
    </a>
  </div>
</div>

<div class="grid grid-2 mb-3">
  <!-- System Status -->
  <div class="card">
    <div class="card-header">
      <div class="card-title"><?= icon('settings', 16) ?> Status Sistem</div>
    </div>
    <div style="display:flex;flex-direction:column;gap:12px">
      <div style="display:flex;align-items:center;gap:10px">
        <span style="font-size:18px"><?= $has_logo ? icon('success', 18) : icon('warning', 18) ?></span>
        <div style="flex:1">
          <div style="font-weight:600;font-size:13px">Logo Website</div>
          <div style="font-size:11px;color:var(--text-muted)"><?= $has_logo ? 'Logo sudah diupload' : 'Belum ada logo (text default digunakan)' ?></div>
        </div>
        <a href="<?= admin_url('?page=pengaturan') ?>" class="btn btn-ghost btn-sm"><?= icon('arrow-right', 16) ?></a>
      </div>
      <div style="display:flex;align-items:center;gap:10px">
        <span style="font-size:18px"><?= $has_wa ? icon('success', 18) : icon('warning', 18) ?></span>
        <div style="flex:1">
          <div style="font-weight:600;font-size:13px">WhatsApp</div>
          <div style="font-size:11px;color:var(--text-muted)"><?= $has_wa ? 'Nomor sudah terdaftar' : 'Belum ada nomor WhatsApp' ?></div>
        </div>
        <a href="<?= admin_url('?page=pengaturan') ?>" class="btn btn-ghost btn-sm"><?= icon('arrow-right', 16) ?></a>
      </div>
      <div style="display:flex;align-items:center;gap:10px">
        <span style="font-size:18px"><?= $has_hero ? icon('success', 18) : icon('warning', 18) ?></span>
        <div style="flex:1">
          <div style="font-weight:600;font-size:13px">Hero Image</div>
          <div style="font-size:11px;color:var(--text-muted)"><?= $has_hero ? 'Gambar hero sudah ada' : 'Belum upload gambar hero' ?></div>
        </div>
        <a href="<?= admin_url('?page=pengaturan') ?>" class="btn btn-ghost btn-sm"><?= icon('arrow-right', 16) ?></a>
      </div>
      <div style="display:flex;align-items:center;gap:10px">
        <span style="font-size:18px"><?= icon('palette', 16) ?></span>
        <div style="flex:1">
          <div style="font-weight:600;font-size:13px">Template Aktif</div>
          <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars($active_theme) ?></div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:10px">
        <span style="font-size:18px"><?= $seo_complete === $seo_total ? icon('success', 18) : icon('search', 18) ?></span>
        <div style="flex:1">
          <div style="font-weight:600;font-size:13px">SEO Setup</div>
          <div style="font-size:11px;color:var(--text-muted)"><?= $seo_complete ?>/<?= $seo_total ?> halaman sudah di-setup SEO-nya</div>
        </div>
        <a href="<?= admin_url('?page=seo') ?>" class="btn btn-ghost btn-sm"><?= icon('arrow-right', 16) ?></a>
      </div>
    </div>
  </div>
  
  <!-- Recent Activity -->
  <div class="card">
    <div class="card-header">
      <div class="card-title"><?= icon('clock', 16) ?> Aktivitas Terbaru</div>
    </div>
    <?php if (empty($recent_activity)): ?>
      <div class="empty-state" style="padding:30px">
        <div class="empty-state-icon"><?= icon('block', 16) ?></div>
        <div style="font-size:13px">Belum ada aktivitas</div>
      </div>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:10px;max-height:300px;overflow-y:auto">
      <?php foreach ($recent_activity as $a): ?>
        <div style="display:flex;gap:10px;padding:8px;border-radius:8px;background:var(--surface-2)">
          <div style="font-size:18px;flex-shrink:0"><?= match($a['aksi'] ?? '') { 'login' => icon('unlock', 16), 'logout' => icon('lock', 16), 'content_update' => icon('content', 16), 'create' => icon('plus', 16), 'update' => icon('pencil', 16), 'delete' => icon('trash', 16), 'activate' => icon('zap', 16), default => icon('circle', 16) } ?></div>
          <div style="flex:1;font-size:12px">
            <div style="font-weight:500;color:var(--text)"><?= htmlspecialchars($a['keterangan'] ?: ('Aksi: ' . ($a['aksi'] ?? 'unknown'))) ?></div>
            <div style="color:var(--text-muted);font-size:11px;margin-top:2px">
              <?= htmlspecialchars($a['user_nama'] ?? 'System') ?> · <?= time_ago($a['created_at']) ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="grid grid-2 mb-3">
  <!-- Latest Blog -->
  <div class="card">
    <div class="card-header">
      <div class="card-title"><?= icon('blog', 16) ?> Artikel Terbaru</div>
      <a href="<?= admin_url('?page=blog') ?>" class="btn btn-ghost btn-sm">Lihat Semua <?= icon('arrow-right', 16) ?></a>
    </div>
    <?php if (empty($latest_blog)): ?>
      <div class="empty-state" style="padding:30px">
        <div class="empty-state-icon"><?= icon('blog', 16) ?></div>
        <div style="font-size:13px">Belum ada artikel</div>
        <a href="<?= admin_url('?page=blog&action=create') ?>" class="btn btn-primary btn-sm mt-2">+ Tulis Artikel</a>
      </div>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:10px">
      <?php foreach ($latest_blog as $b): ?>
        <a href="<?= admin_url('?page=blog&action=edit&id=' . $b['id']) ?>" 
           style="display:flex;justify-content:space-between;padding:10px;background:var(--surface-2);border-radius:8px;text-decoration:none;color:inherit">
          <div style="flex:1">
            <div style="font-weight:600;font-size:13px"><?= htmlspecialchars($b['judul']) ?></div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:2px"><?= format_date($b['created_at']) ?></div>
          </div>
          <div style="color:var(--text-muted);font-size:12px;align-self:center"><?= icon('arrow-right', 16) ?></div>
        </a>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Latest Products -->
  <div class="card">
    <div class="card-header">
      <div class="card-title"><?= icon('product', 16) ?> Produk Terbaru</div>
      <a href="<?= admin_url('?page=produk') ?>" class="btn btn-ghost btn-sm">Lihat Semua <?= icon('arrow-right', 16) ?></a>
    </div>
    <?php if (empty($latest_produk)): ?>
      <div class="empty-state" style="padding:30px">
        <div class="empty-state-icon"><?= icon('product', 16) ?></div>
        <div style="font-size:13px">Belum ada produk</div>
        <a href="<?= admin_url('?page=produk&action=create') ?>" class="btn btn-primary btn-sm mt-2">+ Tambah Produk</a>
      </div>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:10px">
      <?php foreach ($latest_produk as $p): ?>
        <a href="<?= admin_url('?page=produk&action=edit&id=' . $p['id']) ?>" 
           style="display:flex;gap:10px;padding:8px;background:var(--surface-2);border-radius:8px;text-decoration:none;color:inherit">
          <?php if ($p['gambar_utama']): ?>
            <div class="img-preview" style="width:40px;height:40px;flex-shrink:0"><img src="<?= uploads_url($p['gambar_utama']) ?>"></div>
          <?php else: ?>
            <div class="img-preview" style="width:40px;height:40px;flex-shrink:0"><?= icon('product', 16) ?></div>
          <?php endif; ?>
          <div style="flex:1">
            <div style="font-weight:600;font-size:13px"><?= htmlspecialchars($p['nama']) ?></div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:2px"><?= format_date($p['created_at']) ?></div>
          </div>
        </a>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Tips & Help -->
<div class="card" style="background:linear-gradient(135deg, #EFF6FF, #DBEAFE);border-color:#BFDBFE">
  <div style="display:flex;gap:16px;align-items:flex-start">
    <div style="font-size:32px"><?= icon('lightbulb', 16) ?></div>
    <div style="flex:1">
      <div style="font-weight:700;font-size:15px;margin-bottom:6px">Tips Penggunaan</div>
      <div style="font-size:13px;color:var(--text-muted);line-height:1.7">
        • <strong>Edit text:</strong> Buka menu <strong>"Konten Halaman"</strong> untuk mengubah text di semua halaman, per elemen<br>
        • <strong>Edit gambar:</strong> Buka menu <strong>"Pengaturan"</strong> <?= icon('arrow-right', 16) ?> tab "Tampilan" untuk ganti hero, logo, foto About<br>
        • <strong>SEO:</strong> Atur meta title & description di menu <strong>"SEO per Halaman"</strong><br>
        • <strong>Tracking:</strong> Pasang Google Analytics, Pixel, dll di menu <strong>"Iklan & Pixel"</strong>
      </div>
      <div style="margin-top:12px">
        <a href="<?= get_setting('docs_url', '#') ?>" target="_blank" class="btn btn-primary btn-sm"><?= icon('docs', 16) ?> Lihat Dokumentasi Lengkap</a>
      </div>
    </div>
  </div>
</div>
