<?php
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'carousel_settings') {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        $cb = ['logo_carousel_grayscale','logo_carousel_show_services'];
        foreach (['logo_carousel_cols_desktop','logo_carousel_cols_tablet','logo_carousel_cols_mobile',
                  'logo_carousel_animation','logo_carousel_position','logo_carousel_speed'] as $k) {
            if (isset($_POST[$k])) update_setting($k, sanitize($_POST[$k]));
        }
        $cb = array_merge($cb, ['logo_carousel_autoplay','logo_carousel_pause_hover']);
        foreach ($cb as $k) update_setting($k, isset($_POST[$k]) ? '1' : '0');
        set_flash('success', '✅ Pengaturan carousel logo disimpan.');
    }
    redirect(admin_url('?page=klien-logo'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
     redirect(admin_url('?page=' . ($_GET['page'] ?? 'dashboard'))); } else {
        $data = [
            'nama'      => trim($_POST['nama'] ?? ''),
            'url'       => trim($_POST['url'] ?? ''),
            'urutan'    => (int)($_POST['urutan'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        if (!empty($_FILES['logo']['name'])) {
            $upl_path = upload_image($_FILES['logo'], 'klien');
            if ($upl_path) $data['logo'] = $upl_path;
            else set_flash('error', 'Upload logo gagal. Cek ukuran/format file.');
        }
        if ($_POST['action'] === 'create' && !empty($data['logo'])) {
            $db->execute("INSERT INTO klien_logo (nama, logo, url, urutan, is_active) VALUES (?,?,?,?,?)",
                [$data['nama'], $data['logo'], $data['url'], $data['urutan'], $data['is_active']]);
            set_flash('success', 'Logo klien berhasil ditambahkan.');
        } elseif ($_POST['action'] === 'update' && $id > 0) {
            $set = "nama=?, url=?, urutan=?, is_active=?"; 
            $params = [$data['nama'], $data['url'], $data['urutan'], $data['is_active']];
            if (isset($data['logo'])) { $set .= ", logo=?"; $params[] = $data['logo']; }
            $params[] = $id;
            $db->execute("UPDATE klien_logo SET $set WHERE id = ?", $params);
            set_flash('success', 'Logo klien berhasil diupdate.');
        } elseif ($_POST['action'] === 'delete' && $id > 0) {
            $db->execute("DELETE FROM klien_logo WHERE id = ?", [$id]);
            set_flash('success', 'Logo klien berhasil dihapus.');
        }
        redirect(admin_url('?page=klien-logo'));
    }
}

$items = $db->fetchAll("SELECT * FROM klien_logo ORDER BY urutan ASC, id DESC");
$edit_item = ($action === 'edit' && $id > 0) ? $db->fetchOne("SELECT * FROM klien_logo WHERE id = ?", [$id]) : null;
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1>🏢 Logo Klien</h1>
    <div class="page-header-sub">Kelola logo klien yang tampil di halaman About</div>
  </div>
  <?php if ($action === 'list'): ?>
  <div class="page-actions"><a href="<?= admin_url('?page=klien-logo&action=create') ?>" class="btn btn-primary">+ Tambah Logo</a></div>
  <?php endif; ?>
</div>

<?php if ($action === 'list'): 
  $csrf_lc = generate_csrf();
  $cols_d = get_setting('logo_carousel_cols_desktop','5');
  $cols_t = get_setting('logo_carousel_cols_tablet','3');
  $cols_m = get_setting('logo_carousel_cols_mobile','2');
  $anim   = get_setting('logo_carousel_animation','slide');
  $pos    = get_setting('logo_carousel_position','before_footer');
?>
<form method="POST" class="card" style="margin-bottom:20px">
  <input type="hidden" name="csrf_token" value="<?= $csrf_lc ?>">
  <input type="hidden" name="_action" value="carousel_settings">
  <div class="card-header"><div>
    <div class="card-title">🎠 Pengaturan Tampilan Carousel Logo</div>
    <div class="card-subtitle">Atur jumlah kolom, animasi, warna, dan tampil di halaman layanan</div>
  </div></div>
  <div class="card-body">
    <div class="form-row" style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
      <div class="form-group"><label>Kolom Desktop</label>
        <input type="number" name="logo_carousel_cols_desktop" class="form-control" min="2" max="8" value="<?= htmlspecialchars($cols_d) ?>"></div>
      <div class="form-group"><label>Kolom Tablet</label>
        <input type="number" name="logo_carousel_cols_tablet" class="form-control" min="2" max="6" value="<?= htmlspecialchars($cols_t) ?>"></div>
      <div class="form-group"><label>Kolom Mobile</label>
        <input type="number" name="logo_carousel_cols_mobile" class="form-control" min="1" max="4" value="<?= htmlspecialchars($cols_m) ?>"></div>
    </div>
    <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
      <div class="form-group"><label>Animasi</label>
        <select name="logo_carousel_animation" class="form-control">
          <option value="slide" <?= $anim==='slide'?'selected':'' ?>>Slide (carousel + panah) — cocok untuk showcase</option>
          <option value="fade" <?= $anim==='fade'?'selected':'' ?>>Fade (ringan) — cocok untuk performa</option>
          <option value="static" <?= $anim==='static'?'selected':'' ?>>Statis (tanpa animasi)</option>
        </select></div>
      <div class="form-group"><label>Posisi di Halaman Layanan</label>
        <select name="logo_carousel_position" class="form-control">
          <option value="after_hero" <?= $pos==='after_hero'?'selected':'' ?>>Bawah Hero</option>
          <option value="middle" <?= $pos==='middle'?'selected':'' ?>>Tengah Halaman</option>
          <option value="before_footer" <?= $pos==='before_footer'?'selected':'' ?>>Atas Footer</option>
        </select></div>
    </div>
    <div style="display:flex;gap:20px;flex-wrap:wrap;margin-top:8px">
      <label class="checkbox-label">
        <input type="checkbox" name="logo_carousel_show_services" value="1" <?= get_setting('logo_carousel_show_services','0')==='1'?'checked':'' ?>>
        Tampilkan carousel di semua halaman layanan (trust building)
      </label>
      <label class="checkbox-label">
        <input type="checkbox" name="logo_carousel_grayscale" value="1" <?= get_setting('logo_carousel_grayscale','0')==='1'?'checked':'' ?>>
        Tampilkan logo grayscale (default: warna penuh)
      </label>
    </div>
    
    <div style="margin-top:18px;padding-top:18px;border-top:1px solid var(--border)">
      <div style="font-weight:600;font-size:13px;margin-bottom:10px">⚙️ Pengaturan Animasi Slide (hanya untuk mode "Slide")</div>
      <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:10px">
        <div class="form-group" style="margin-bottom:0">
          <label>Kecepatan Animasi</label>
          <select name="logo_carousel_speed" class="form-control">
            <?php $sp = get_setting('logo_carousel_speed','40'); ?>
            <option value="20" <?= $sp==='20'?'selected':'' ?>>Cepat (20 detik per loop)</option>
            <option value="30" <?= $sp==='30'?'selected':'' ?>>Sedang (30 detik per loop)</option>
            <option value="40" <?= $sp==='40'?'selected':'' ?>>Normal (40 detik per loop)</option>
            <option value="60" <?= $sp==='60'?'selected':'' ?>>Lambat (60 detik per loop)</option>
            <option value="90" <?= $sp==='90'?'selected':'' ?>>Sangat lambat (90 detik)</option>
          </select>
          <div class="form-help">Semakin lama, semakin pelan slide bergerak</div>
        </div>
      </div>
      <div style="display:flex;gap:20px;flex-wrap:wrap">
        <label class="checkbox-label">
          <input type="checkbox" name="logo_carousel_autoplay" value="1" <?= get_setting('logo_carousel_autoplay','1')==='1'?'checked':'' ?>>
          Autoplay (slide bergerak otomatis)
        </label>
        <label class="checkbox-label">
          <input type="checkbox" name="logo_carousel_pause_hover" value="1" <?= get_setting('logo_carousel_pause_hover','1')==='1'?'checked':'' ?>>
          Pause saat mouse hover
        </label>
      </div>
    </div>
    <div style="text-align:right;margin-top:12px">
      <button type="submit" class="btn btn-primary">💾 Simpan Pengaturan Carousel</button>
    </div>
  </div>
</form>
<?php endif; ?>

<?php if ($action === 'create' || $action === 'edit'): ?>
  <div class="card">
    <div class="card-header"><div class="card-title"><?= $action === 'create' ? 'Tambah' : 'Edit' ?> Logo Klien</div></div>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
      <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
      <div class="form-group">
        <label>Nama Klien *</label>
        <input type="text" name="nama" required value="<?= htmlspecialchars($edit_item['nama'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Logo *</label>
        <?php if (!empty($edit_item['logo'])): ?>
          <div class="img-upload-row" style="margin-bottom:8px">
            <div class="img-preview" style="width:120px;height:60px;background:white"><img src="<?= uploads_url($edit_item['logo']) ?>" style="object-fit:contain"></div>
            <span class="text-muted" style="font-size:12px">Logo saat ini</span>
          </div>
        <?php endif; ?>
        <input type="file" name="logo" accept="image/*" <?= !$edit_item ? 'required' : '' ?>>
        <div class="form-hint">PNG transparan direkomendasikan, max 1MB</div>
      </div>
      <div class="form-group">
        <label>URL Website Klien (opsional)</label>
        <input type="url" name="url" placeholder="https://..." value="<?= htmlspecialchars($edit_item['url'] ?? '') ?>">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Urutan</label>
          <input type="number" name="urutan" value="<?= $edit_item['urutan'] ?? 0 ?>">
        </div>
        <div class="form-group">
          <label class="checkbox-label" style="margin-top:22px">
            <input type="checkbox" name="is_active" <?= ($edit_item['is_active'] ?? 1) ? 'checked' : '' ?>>
            Tampilkan
          </label>
        </div>
      </div>
      <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border)">
        <a href="<?= admin_url('?page=klien-logo') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">💾 Simpan</button>
      </div>
    </form>
  </div>
<?php else: ?>
  <?php if (empty($items)): ?>
    <div class="card"><div class="empty-state">
      <div class="empty-state-icon">🏢</div>
      <div>Belum ada logo klien.</div>
      <a href="<?= admin_url('?page=klien-logo&action=create') ?>" class="btn btn-primary mt-2">+ Tambah Logo Pertama</a>
    </div></div>
  <?php else: ?>
    <div class="grid grid-3">
      <?php foreach ($items as $k): ?>
        <div class="card" style="padding:16px">
          <div style="background:var(--surface-2);border-radius:8px;padding:20px;display:flex;align-items:center;justify-content:center;height:100px;margin-bottom:12px">
            <img src="<?= uploads_url($k['logo']) ?>" alt="<?= htmlspecialchars($k['nama']) ?>" style="max-height:60px;max-width:100%;object-fit:contain">
          </div>
          <div style="font-weight:600;margin-bottom:8px"><?= htmlspecialchars($k['nama']) ?></div>
          <div style="display:flex;gap:6px">
            <a href="<?= admin_url('?page=klien-logo&action=edit&id='.$k['id']) ?>" class="btn btn-secondary btn-sm flex-1">Edit</a>
            <form method="POST" action="<?= admin_url('?page=klien-logo&action=delete&id='.$k['id']) ?>" style="display:inline" onsubmit="return confirm('Hapus logo '.<?= json_encode($k['nama']) ?>'?')">
              <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
              <input type="hidden" name="action" value="delete">
              <button type="submit" class="btn btn-danger btn-sm">🗑</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
<?php endif; ?>
