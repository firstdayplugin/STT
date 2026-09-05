<?php
$db = Database::getInstance();

$pages_list = [
    'home'    => 'Halaman Home',
    'about'   => 'Halaman Tentang Kami',
    'layanan' => 'Halaman Layanan',
    'gallery' => 'Halaman Galeri',
    'blog'    => 'Halaman Blog',
    'produk'  => 'Halaman Produk',
    'kontak'  => 'Halaman Kontak',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
     redirect(admin_url('?page=' . ($_GET['page'] ?? 'dashboard'))); } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'save_page_seo') {
            $page_key = $_POST['page_key'] ?? '';
            $data = [
                'meta_title'       => trim($_POST['meta_title'] ?? ''),
                'meta_description' => trim($_POST['meta_description'] ?? ''),
                'meta_keywords'    => trim($_POST['meta_keywords'] ?? ''),
                'og_image'         => trim($_POST['og_image'] ?? ''),
                'canonical_url'    => trim($_POST['canonical_url'] ?? ''),
                'robots'           => trim($_POST['robots'] ?? 'index,follow'),
            ];
            $existing = $db->fetchOne("SELECT id FROM page_seo WHERE page_key = ?", [$page_key]);
            if ($existing) {
                $db->execute(
                    "UPDATE page_seo SET meta_title=?, meta_description=?, meta_keywords=?, og_image=?, canonical_url=?, robots=? WHERE page_key=?",
                    [$data['meta_title'], $data['meta_description'], $data['meta_keywords'], $data['og_image'], $data['canonical_url'], $data['robots'], $page_key]
                );
            } else {
                $db->execute(
                    "INSERT INTO page_seo (page_key, meta_title, meta_description, meta_keywords, og_image, canonical_url, robots) VALUES (?,?,?,?,?,?,?)",
                    [$page_key, $data['meta_title'], $data['meta_description'], $data['meta_keywords'], $data['og_image'], $data['canonical_url'], $data['robots']]
                );
            }
            set_flash('success', 'SEO untuk halaman "' . ($pages_list[$page_key] ?? $page_key) . '" berhasil disimpan.');
        } elseif ($action === 'save_global') {
            $robots_txt = trim($_POST['robots_txt'] ?? '');
            $sitemap_extras = trim($_POST['sitemap_extras'] ?? '');
            update_setting('robots_txt', $robots_txt);
            update_setting('sitemap_extras', $sitemap_extras);
            set_flash('success', 'Pengaturan robots.txt & sitemap berhasil disimpan.');
        }
        redirect(admin_url('?page=seo' . (isset($_POST['page_key']) ? '&p=' . urlencode($_POST['page_key']) : '')));
    }
}

$current_page = $_GET['p'] ?? 'home';
if (!isset($pages_list[$current_page])) $current_page = 'home';

$seo_data = $db->fetchOne("SELECT * FROM page_seo WHERE page_key = ?", [$current_page]);
if (!$seo_data) $seo_data = ['meta_title'=>'', 'meta_description'=>'', 'meta_keywords'=>'', 'og_image'=>'', 'canonical_url'=>'', 'robots'=>'index,follow'];

$robots_txt = get_setting('robots_txt', "User-agent: *\nAllow: /\n\nSitemap: " . SITEMAP_URL);
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1><?= icon('search', 16) ?> SEO per Halaman</h1>
    <div class="page-header-sub">Atur meta title, description, dan SEO untuk setiap halaman</div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-title mb-2">Pilih Halaman:</div>
  <div class="grid grid-4" style="gap:10px">
    <?php foreach ($pages_list as $key => $label): ?>
    <a href="<?= admin_url('?page=seo&p=' . urlencode($key)) ?>"
       class="shortcut-card" style="<?= $current_page === $key ? 'border-color:var(--primary);background:var(--primary-soft)' : '' ?>">
      <div class="shortcut-icon"><?= icon('search', 16) ?></div>
      <div class="shortcut-text">
        <div class="shortcut-title"><?= htmlspecialchars($label) ?></div>
        <div class="shortcut-sub"><?= $key ?></div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div>
      <div class="card-title">SEO — <?= htmlspecialchars($pages_list[$current_page]) ?></div>
      <div class="card-subtitle">Optimalkan halaman ini untuk search engine</div>
    </div>
  </div>
  
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="action" value="save_page_seo">
    <input type="hidden" name="page_key" value="<?= htmlspecialchars($current_page) ?>">
    
    <div class="form-group">
      <label>Meta Title</label>
      <input type="text" name="meta_title" maxlength="70"
             value="<?= htmlspecialchars($seo_data['meta_title'] ?? '') ?>"
             placeholder="Reklamepedia | Solusi Periklanan Profesional">
      <div class="form-hint">Optimal 50-60 karakter. Tampil di tab browser & hasil pencarian Google.</div>
    </div>
    
    <div class="form-group">
      <label>Meta Description</label>
      <textarea class="no-wysiwyg" name="meta_description" rows="3" maxlength="160"
                placeholder="Deskripsi singkat tentang halaman ini..."><?= htmlspecialchars($seo_data['meta_description'] ?? '') ?></textarea>
      <div class="form-hint">Optimal 150-160 karakter. Tampil di bawah judul hasil pencarian Google.</div>
    </div>
    
    <div class="form-group">
      <label>Meta Keywords</label>
      <input type="text" name="meta_keywords"
             value="<?= htmlspecialchars($seo_data['meta_keywords'] ?? '') ?>"
             placeholder="reklame, neon box, papan nama, advertising">
      <div class="form-hint">Pisahkan dengan koma. Tidak terlalu penting untuk Google, tapi tetap berguna.</div>
    </div>
    
    <div class="form-row">
      <div class="form-group">
        <label>OG Image (URL)</label>
        <input type="url" name="og_image"
               value="<?= htmlspecialchars($seo_data['og_image'] ?? '') ?>"
               placeholder="https://...">
        <div class="form-hint">Gambar yang tampil saat dibagikan ke social media (1200×630px ideal).</div>
      </div>
      <div class="form-group">
        <label>Robots</label>
        <select name="robots">
          <?php foreach (['index,follow'=>'Index, Follow (default)','noindex,follow'=>'No-Index, Follow','index,nofollow'=>'Index, No-Follow','noindex,nofollow'=>'No-Index, No-Follow'] as $val => $lbl): ?>
            <option value="<?= $val ?>" <?= ($seo_data['robots'] ?? 'index,follow') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    
    <div class="form-group">
      <label>Canonical URL (opsional)</label>
      <input type="url" name="canonical_url"
             value="<?= htmlspecialchars($seo_data['canonical_url'] ?? '') ?>"
             placeholder="Kosongkan untuk default">
    </div>
    
    <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;border-top:1px solid var(--border)">
      <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan SEO Halaman</button>
    </div>
  </form>
</div>

<div class="card mt-3">
  <div class="card-header"><div class="card-title">Pengaturan Global SEO</div></div>
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="action" value="save_global">
    <div class="form-group">
      <label>robots.txt</label>
      <textarea class="no-wysiwyg" name="robots_txt" rows="6" style="font-family:monospace;font-size:12px"><?= htmlspecialchars($robots_txt) ?></textarea>
      <div class="form-hint">Akan tersedia di <a href="<?= url('/robots.txt') ?>" target="_blank"><?= url('/robots.txt') ?></a></div>
    </div>
    <div class="form-group">
      <label>Sitemap</label>
      <div style="padding:12px;background:var(--surface-2);border-radius:8px">
        Sitemap otomatis tersedia di: <a href="<?= SITEMAP_URL ?>" target="_blank"><?= SITEMAP_URL ?></a>
      </div>
    </div>
    <div style="display:flex;justify-content:flex-end">
      <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan</button>
    </div>
  </form>
</div>
