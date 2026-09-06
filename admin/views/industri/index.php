<?php
/**
 * Admin — Our Industries (orbit animation cards on the Home page).
 * Feeds the `industri` table; the Home template injects it into anima.js (§14.2).
 * Each card: label, center title/subtitle, optional photo, gradient colors, link, order.
 */
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
        redirect(admin_url('?page=industri'));
    }
    $act = $_POST['action'] ?? '';

    if ($act === 'delete' && $id > 0) {
        $db->execute("DELETE FROM industri WHERE id = ?", [$id]);
        $db->execute("DELETE FROM industri_pilar WHERE industri_id = ?", [$id]); // clear matrix cells
        log_activity('delete', 'Hapus industri #' . $id);
        set_flash('success', 'Kartu industri dihapus.');
        redirect(admin_url('?page=industri'));
    }

    // Industry × Pillar matrix save (one form per industry, all 4 pillar cells).
    if ($act === 'save_matrix' && $id > 0) {
        $cells = $_POST['cell'] ?? [];
        foreach ($cells as $pilar_id => $c) {
            $pilar_id = (int) $pilar_id;
            if ($pilar_id <= 0) continue;
            $fitur = [];
            foreach (($c['fitur'] ?? []) as $f) {
                $fi = ['icon' => trim($f['icon'] ?? ''), 'judul' => trim($f['judul'] ?? ''), 'teks' => trim($f['teks'] ?? '')];
                if ($fi['judul'] !== '' || $fi['teks'] !== '') $fitur[] = $fi;
            }
            $fitur_json = $fitur ? json_encode($fitur, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;
            $heading = trim($c['heading'] ?? '');
            $konten  = trim($c['konten'] ?? '');
            $existing = $db->fetchOne("SELECT id FROM industri_pilar WHERE industri_id=? AND pilar_id=?", [$id, $pilar_id]);
            if ($heading === '' && $konten === '' && !$fitur) {
                if ($existing) $db->execute("DELETE FROM industri_pilar WHERE id=?", [$existing['id']]);
                continue;
            }
            if ($existing) {
                $db->execute("UPDATE industri_pilar SET heading=?,konten=?,fitur=? WHERE id=?", [$heading, $konten, $fitur_json, $existing['id']]);
            } else {
                $db->execute("INSERT INTO industri_pilar (industri_id,pilar_id,heading,konten,fitur) VALUES (?,?,?,?,?)", [$id, $pilar_id, $heading, $konten, $fitur_json]);
            }
        }
        log_activity('update', 'Update konten matriks industri #' . $id);
        set_flash('success', 'Konten pilar untuk industri ini disimpan.');
        redirect(admin_url('?page=industri&action=matrix&id=' . $id));
    }

    $data = [
        'label'     => trim($_POST['label'] ?? ''),
        'judul'     => trim($_POST['judul'] ?? ''),
        'subtitle'  => trim($_POST['subtitle'] ?? ''),
        'intro'     => trim($_POST['intro'] ?? ''),
        'warna1'    => trim($_POST['warna1'] ?? '#1d478c') ?: '#1d478c',
        'warna2'    => trim($_POST['warna2'] ?? '#3f80e2') ?: '#3f80e2',
        'url'       => trim($_POST['url'] ?? ''),
        'urutan'    => (int)($_POST['urutan'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($data['label'] === '') {
        set_flash('error', 'Label wajib diisi.');
        redirect(admin_url('?page=industri&action=' . ($act === 'update' ? 'edit&id=' . $id : 'create')));
    }
    $slug = trim($_POST['slug'] ?? '');
    if ($slug === '') $slug = slug($data['label']);
    $base = $slug; $n = 1;
    while ($db->fetchOne("SELECT id FROM industri WHERE slug=? AND id<>?", [$slug, $id])) { $slug = $base . '-' . (++$n); }
    $data['slug'] = $slug;

    // Card photo + detail hero image. "hapus_*" clears.
    $gambar = null; $set_gambar = false;
    if (!empty($_FILES['gambar']['name'])) {
        $up = upload_image($_FILES['gambar'], 'industri');
        if ($up) { $gambar = $up; $set_gambar = true; } else set_flash('error', 'Upload foto gagal.');
    } elseif (!empty($_POST['hapus_gambar'])) { $gambar = null; $set_gambar = true; }

    $hero = null; $set_hero = false;
    if (!empty($_FILES['hero_image']['name'])) {
        $up = upload_image($_FILES['hero_image'], 'industri');
        if ($up) { $hero = $up; $set_hero = true; } else set_flash('error', 'Upload hero gagal.');
    } elseif (!empty($_POST['hapus_hero'])) { $hero = null; $set_hero = true; }

    if ($act === 'create') {
        $db->execute(
            "INSERT INTO industri (label,slug,judul,subtitle,intro,gambar,hero_image,warna1,warna2,url,urutan,is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
            [$data['label'],$data['slug'],$data['judul'],$data['subtitle'],$data['intro'],$gambar,$hero,$data['warna1'],$data['warna2'],$data['url'],$data['urutan'],$data['is_active']]
        );
        save_i18n_fields('industri', (int)$db->lastInsertId(), $_POST);
        log_activity('create', 'Tambah industri: ' . $data['label']);
        set_flash('success', 'Kartu industri "' . $data['label'] . '" ditambahkan.');
    } elseif ($act === 'update' && $id > 0) {
        $set = "label=?,slug=?,judul=?,subtitle=?,intro=?,warna1=?,warna2=?,url=?,urutan=?,is_active=?";
        $params = [$data['label'],$data['slug'],$data['judul'],$data['subtitle'],$data['intro'],$data['warna1'],$data['warna2'],$data['url'],$data['urutan'],$data['is_active']];
        if ($set_gambar) { $set .= ",gambar=?"; $params[] = $gambar; }
        if ($set_hero)   { $set .= ",hero_image=?"; $params[] = $hero; }
        $params[] = $id;
        $db->execute("UPDATE industri SET $set WHERE id=?", $params);
        save_i18n_fields('industri', $id, $_POST);
        log_activity('update', 'Update industri: ' . $data['label']);
        set_flash('success', 'Kartu industri diperbarui.');
    }
    redirect(admin_url('?page=industri'));
}

$items = $db->fetchAll("SELECT * FROM industri ORDER BY urutan ASC, id ASC");
$edit_item = (in_array($action, ['edit','matrix']) && $id > 0) ? $db->fetchOne("SELECT * FROM industri WHERE id=?", [$id]) : null;
$csrf = generate_csrf();
?>

<div class="page-header">
  <div>
    <h1><?= icon('compass', 18) ?> Our Industries — Orbit</h1>
    <div class="page-header-sub">Kartu industri yang berputar (animasi orbit) di halaman Home. Bisa diisi foto per kartu.</div>
  </div>
  <?php if ($action === 'list'): ?>
  <div class="page-actions">
    <a href="<?= url('/') ?>#industries" target="_blank" class="btn btn-secondary"><?= icon('eye', 16) ?> Lihat di Home</a>
    <a href="<?= admin_url('?page=industri&action=create') ?>" class="btn btn-primary"><?= icon('plus', 16) ?> Tambah Kartu</a>
  </div>
  <?php endif; ?>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>
  <form method="POST" enctype="multipart/form-data" class="card">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
    <div class="card-header"><div class="card-title"><?= $action === 'create' ? 'Tambah' : 'Edit' ?> Kartu Industri</div></div>
    <div class="card-body">

      <div class="form-row">
        <div class="form-group">
          <label>Label Kartu *</label>
          <input type="text" name="label" required maxlength="100" value="<?= htmlspecialchars($edit_item['label'] ?? '') ?>" placeholder="Financial">
          <div class="form-hint">Teks pendek di kartu + eyebrow tengah saat di-hover.</div>
        </div>
        <div class="form-group">
          <label>Urutan</label>
          <input type="number" name="urutan" value="<?= (int)($edit_item['urutan'] ?? 0) ?>">
        </div>
      </div>

      <div class="form-group">
        <label>Judul Tengah</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($edit_item['judul'] ?? '') ?>" placeholder="Financial Services &lt;b&gt;&amp; E-Commerce&lt;/b&gt;">
        <div class="form-hint">Tampil di tengah orbit saat kartu di-hover. Boleh pakai <code>&lt;b&gt;...&lt;/b&gt;</code> untuk penekanan.</div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Sub-teks Tengah</label>
          <input type="text" name="subtitle" value="<?= htmlspecialchars($edit_item['subtitle'] ?? '') ?>" placeholder="Secure digital transactions">
        </div>
        <div class="form-group">
          <label>Slug (URL halaman detail)</label>
          <input type="text" name="slug" value="<?= htmlspecialchars($edit_item['slug'] ?? '') ?>" placeholder="otomatis dari label">
          <div class="form-hint">Dipakai di <code>/industri/[slug]</code>. Kosongkan untuk otomatis.</div>
        </div>
      </div>

      <div style="border-top:1px solid var(--border);margin:6px 0 14px;padding-top:14px">
        <div style="font-weight:700;font-size:13px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:10px"><?= icon('compass', 14) ?> Halaman Detail Industri</div>
        <div class="form-group">
          <label>Intro</label>
          <textarea name="intro" rows="2" class="no-wysiwyg" placeholder="Paragraf pembuka di halaman detail industri..."><?= htmlspecialchars($edit_item['intro'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
          <label>Hero Image (halaman detail)</label>
          <?php if (!empty($edit_item['hero_image'])): ?>
            <div class="img-upload-row" style="margin-bottom:8px">
              <div class="img-preview" style="width:120px;height:56px"><img src="<?= uploads_url($edit_item['hero_image']) ?>" alt=""></div>
              <label class="checkbox-label" style="font-size:12px"><input type="checkbox" name="hapus_hero" value="1"> Hapus</label>
            </div>
          <?php endif; ?>
          <input type="file" name="hero_image" accept="image/*">
          <div class="form-hint">Latar hero di halaman <code>/industri/[slug]</code>. Kosong = gradient.</div>
        </div>
        <?php if ($action === 'edit'): ?>
          <a href="<?= admin_url('?page=industri&action=matrix&id=' . (int)$edit_item['id']) ?>" class="btn btn-secondary btn-sm"><?= icon('table', 15) ?> Kelola Konten Pilar (tab)</a>
        <?php else: ?>
          <div class="form-hint"><?= icon('info', 13) ?> Simpan dulu, lalu kelola konten 4 pilar (tab) di halaman detail.</div>
        <?php endif; ?>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Foto Kartu (opsional)</label>
          <?php if (!empty($edit_item['gambar'])): ?>
            <div class="img-upload-row" style="margin-bottom:8px">
              <div class="img-preview" style="width:80px;height:52px"><img src="<?= uploads_url($edit_item['gambar']) ?>" alt=""></div>
              <label class="checkbox-label" style="font-size:12px"><input type="checkbox" name="hapus_gambar" value="1"> Hapus foto</label>
            </div>
          <?php endif; ?>
          <input type="file" name="gambar" accept="image/*">
          <div class="form-hint">Kalau diisi, foto menggantikan gradient sebagai wajah kartu. JPG/PNG/WebP, max 5MB.</div>
        </div>
        <div class="form-group">
          <label>Link (opsional)</label>
          <input type="text" name="url" value="<?= htmlspecialchars($edit_item['url'] ?? '') ?>" placeholder="industri/financial atau https://...">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Warna Gradient 1</label>
          <div style="display:flex;gap:8px;align-items:center">
            <input type="color" value="<?= htmlspecialchars($edit_item['warna1'] ?? '#1d478c') ?>" data-sync="warna1" style="width:44px;height:38px;padding:2px">
            <input type="text" id="warna1" name="warna1" value="<?= htmlspecialchars($edit_item['warna1'] ?? '#1d478c') ?>" style="flex:1">
          </div>
          <div class="form-hint">Dipakai kalau tidak ada foto.</div>
        </div>
        <div class="form-group">
          <label>Warna Gradient 2</label>
          <div style="display:flex;gap:8px;align-items:center">
            <input type="color" value="<?= htmlspecialchars($edit_item['warna2'] ?? '#3f80e2') ?>" data-sync="warna2" style="width:44px;height:38px;padding:2px">
            <input type="text" id="warna2" name="warna2" value="<?= htmlspecialchars($edit_item['warna2'] ?? '#3f80e2') ?>" style="flex:1">
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox" name="is_active" <?= ($edit_item['is_active'] ?? 1) ? 'checked' : '' ?>> Tampilkan di website
        </label>
      </div>

      <?php if ($action === 'edit') echo i18n_fields_editor('industri', (int)$edit_item['id'], [
        'label'    => 'Label Kartu',
        'judul'    => 'Judul Tengah',
        'subtitle' => 'Sub-teks Tengah',
        'intro'    => ['label' => 'Intro (halaman detail)', 'type' => 'textarea'],
      ]); ?>

      <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border)">
        <a href="<?= admin_url('?page=industri') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan</button>
      </div>
    </div>
  </form>

<?php elseif ($action === 'matrix' && $edit_item):
  $pillars = $db->fetchAll("SELECT * FROM solusi_pilar WHERE is_active=1 ORDER BY urutan, id");
  $cells_raw = $db->fetchAll("SELECT * FROM industri_pilar WHERE industri_id=?", [(int)$edit_item['id']]);
  $cells = []; foreach ($cells_raw as $c) { $cells[(int)$c['pilar_id']] = $c; }
?>
  <div class="page-actions" style="margin-bottom:14px">
    <a href="<?= admin_url('?page=industri') ?>" class="btn btn-secondary btn-sm"><?= icon('arrow-left', 15) ?> Semua Industri</a>
    <a href="<?= url('industri/' . ($edit_item['slug'] ?? '')) ?>" target="_blank" class="btn btn-secondary btn-sm"><?= icon('eye', 15) ?> Lihat Halaman</a>
  </div>
  <?php if (empty($pillars)): ?>
    <div class="card"><div class="card-body">Belum ada pilar solusi. Tambahkan dulu di <a href="<?= admin_url('?page=solusi-pilar') ?>">Pilar Solusi</a>.</div></div>
  <?php else: ?>
  <form method="POST" action="<?= admin_url('?page=industri&action=save_matrix&id=' . (int)$edit_item['id']) ?>">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="action" value="save_matrix">
    <div class="card" style="margin-bottom:16px"><div class="card-body" style="display:flex;justify-content:space-between;align-items:center;gap:12px">
      <div><div style="font-weight:700"><?= icon('table', 16) ?> Konten Pilar — <?= htmlspecialchars($edit_item['label']) ?></div>
        <div style="font-size:12px;color:var(--text-muted)">Isi tiap tab (pilar) untuk halaman detail industri ini. Tab yang kosong memakai deskripsi umum pilar.</div></div>
      <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan Semua</button>
    </div></div>

    <?php foreach ($pillars as $p): $cell = $cells[(int)$p['id']] ?? null;
      $fitur = [];
      if (!empty($cell['fitur'])) { $d = json_decode($cell['fitur'], true); if (is_array($d)) $fitur = $d; }
    ?>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><div class="card-title"><?= icon('layers', 15) ?> <?= htmlspecialchars($p['nama']) ?></div></div>
      <div class="card-body">
        <div class="form-group">
          <label>Heading</label>
          <input type="text" name="cell[<?= (int)$p['id'] ?>][heading]" value="<?= htmlspecialchars($cell['heading'] ?? '') ?>" placeholder="<?= htmlspecialchars($p['nama']) ?> untuk industri ini">
        </div>
        <div class="form-group">
          <label>Konten</label>
          <textarea name="cell[<?= (int)$p['id'] ?>][konten]" class="wysiwyg" rows="4"><?= htmlspecialchars($cell['konten'] ?? '') ?></textarea>
        </div>
        <label style="font-weight:600;font-size:13px">Fitur (maks 4)</label>
        <div style="display:grid;gap:8px;margin-top:6px">
          <?php for ($i = 0; $i < 4; $i++): $f = $fitur[$i] ?? ['icon'=>'','judul'=>'','teks'=>'']; ?>
          <div style="display:grid;grid-template-columns:150px 1fr 2fr;gap:8px">
            <input type="text" name="cell[<?= (int)$p['id'] ?>][fitur][<?= $i ?>][icon]" value="<?= htmlspecialchars($f['icon'] ?? '') ?>" placeholder="ikon (lucide)">
            <input type="text" name="cell[<?= (int)$p['id'] ?>][fitur][<?= $i ?>][judul]" value="<?= htmlspecialchars($f['judul'] ?? '') ?>" placeholder="judul fitur">
            <input type="text" name="cell[<?= (int)$p['id'] ?>][fitur][<?= $i ?>][teks]" value="<?= htmlspecialchars($f['teks'] ?? '') ?>" placeholder="deskripsi singkat">
          </div>
          <?php endfor; ?>
        </div>
        <div class="form-hint" style="margin-top:6px">Nama ikon Lucide (mis. <code>layers</code>, <code>lock</code>, <code>chart</code>, <code>zap</code>) atau path gambar upload.</div>
      </div>
    </div>
    <?php endforeach; ?>

    <div style="display:flex;justify-content:flex-end;gap:8px">
      <a href="<?= admin_url('?page=industri') ?>" class="btn btn-secondary">Kembali</a>
      <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan Semua</button>
    </div>
  </form>
  <?php endif; ?>

<?php elseif (empty($items)): ?>
  <div class="card"><div class="empty-state">
    <div class="empty-state-icon"><?= icon('compass', 40) ?></div>
    <div>Belum ada kartu industri.</div>
    <a href="<?= admin_url('?page=industri&action=create') ?>" class="btn btn-primary mt-2"><?= icon('plus', 16) ?> Tambah Kartu Pertama</a>
  </div></div>
<?php else: ?>
  <div class="table-wrap"><table>
    <thead><tr>
      <th style="width:70px">Wajah</th><th>Label / Judul</th><th style="width:120px">Warna</th>
      <th style="width:70px">Urutan</th><th style="width:80px">Status</th><th style="width:130px">Aksi</th>
    </tr></thead>
    <tbody>
    <?php foreach ($items as $it): ?>
      <tr>
        <td>
          <?php if (!empty($it['gambar'])): ?>
            <div class="img-preview" style="width:56px;height:36px"><img src="<?= uploads_url($it['gambar']) ?>" alt=""></div>
          <?php else: ?>
            <div style="width:56px;height:36px;border-radius:6px;background:linear-gradient(135deg,<?= htmlspecialchars($it['warna1']) ?>,<?= htmlspecialchars($it['warna2']) ?>)"></div>
          <?php endif; ?>
        </td>
        <td>
          <div style="font-weight:600"><?= htmlspecialchars($it['label']) ?></div>
          <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars(strip_tags($it['judul'] ?? '')) ?></div>
        </td>
        <td><span style="font-family:monospace;font-size:11px"><?= htmlspecialchars($it['warna1']) ?><br><?= htmlspecialchars($it['warna2']) ?></span></td>
        <td><?= (int)$it['urutan'] ?></td>
        <td><?= $it['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
        <td><div class="table-actions">
          <a href="<?= admin_url('?page=industri&action=matrix&id=' . $it['id']) ?>" class="btn btn-secondary btn-sm" title="Konten 4 pilar (tab detail)"><?= icon('table', 15) ?></a>
          <a href="<?= admin_url('?page=industri&action=edit&id=' . $it['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Hapus kartu industri ini?')">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="action" value="delete">
            <button type="submit" class="btn btn-danger btn-sm" formaction="<?= admin_url('?page=industri&action=delete&id=' . $it['id']) ?>"><?= icon('trash', 15) ?></button>
          </form>
        </div></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
<?php endif; ?>
