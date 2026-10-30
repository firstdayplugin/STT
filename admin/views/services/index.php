<?php
/**
 * Admin — Services (module: services). Full CRUD for the 5-page hierarchy:
 *   service_pages (+ pillars for landing, areas+items for capability, tiers+matrix for package).
 * Page-level copy is EN-translatable via the shared i18n editor; sub-entities carry inline EN fields.
 */
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$multilang = function_exists('is_multilang') && is_multilang();
$others    = $multilang ? array_values(array_filter(available_langs(), fn($l) => $l !== default_lang())) : [];

$backEdit = fn($pid) => admin_url('?page=services&action=edit&id=' . (int)$pid);

/* ---------------- POST handlers ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error', 'Token tidak valid.'); redirect(admin_url('?page=services')); }
    $act = $_POST['action'] ?? '';
    $pid = (int)($_POST['page_id'] ?? $id);

    // -- page fields --
    if ($act === 'save_page') {
        // Optional hero image upload
        $hero = null;
        if (!empty($_FILES['hero_image']['name'])) {
            $up = upload_image($_FILES['hero_image'], 'services');
            if ($up) $hero = $up;
        }
        $f = $_POST['p'] ?? [];
        $sql = "UPDATE service_pages SET judul=?, eyebrow=?, headline=?, tagline=?, body=?, cta_label=?, cta_target=?, extra1=?, extra2=?, extra3=?, visual_note=?, data_ai_title=?, data_ai_body=?, is_active=?" . ($hero ? ", hero_image=?" : "") . " WHERE id=?";
        $args = [
            trim($f['judul'] ?? ''), trim($f['eyebrow'] ?? ''), trim($f['headline'] ?? ''), trim($f['tagline'] ?? ''),
            trim($f['body'] ?? ''), trim($f['cta_label'] ?? ''), trim($f['cta_target'] ?? ''),
            trim($f['extra1'] ?? ''), trim($f['extra2'] ?? ''), trim($f['extra3'] ?? ''), trim($f['visual_note'] ?? ''),
            trim($f['data_ai_title'] ?? ''), trim($f['data_ai_body'] ?? ''), isset($f['is_active']) ? 1 : 0,
        ];
        if ($hero) $args[] = $hero;
        $args[] = $pid;
        $db->execute($sql, $args);
        if (function_exists('save_i18n_fields') && !empty($_POST['i18n'])) save_i18n_fields('service_pages', $pid, $_POST);
        set_flash('success', 'Halaman disimpan.');
        redirect($backEdit($pid));
    }

    // -- generic section save/add/delete for pillars, tiers, matrix --
    $tableMap = ['pillar' => 'service_pillars', 'tier' => 'service_tiers', 'matrix' => 'service_matrix', 'area' => 'service_areas'];
    if (preg_match('/^(add|del|save)_(pillar|tier|matrix|area)$/', $act, $m)) {
        $op = $m[1]; $ent = $m[2]; $tbl = $tableMap[$ent];

        if ($op === 'add') {
            $maxo = (int)($db->fetchOne("SELECT COALESCE(MAX(urutan),0) mo FROM $tbl WHERE page_id=?", [$pid])['mo'] ?? 0);
            if ($ent === 'pillar')      $db->execute("INSERT INTO service_pillars (page_id,kategori,judul,urutan) VALUES (?,?,?,?)", [$pid, 'pillar', 'Judul baru', $maxo + 1]);
            elseif ($ent === 'tier')    $db->execute("INSERT INTO service_tiers (page_id,nama,urutan) VALUES (?,?,?)", [$pid, 'TIER', $maxo + 1]);
            elseif ($ent === 'matrix')  $db->execute("INSERT INTO service_matrix (page_id,baris,urutan) VALUES (?,?,?)", [$pid, 'Baris baru', $maxo + 1]);
            elseif ($ent === 'area')    $db->execute("INSERT INTO service_areas (page_id,judul,urutan) VALUES (?,?,?)", [$pid, 'Area baru', $maxo + 1]);
            set_flash('success', 'Item ditambahkan.');
            redirect($backEdit($pid));
        }
        if ($op === 'del') {
            $rid = (int)($_POST['rid'] ?? 0);
            if ($rid > 0) {
                if ($ent === 'area') $db->execute("DELETE FROM service_area_items WHERE area_id=?", [$rid]);
                $db->execute("DELETE FROM $tbl WHERE id=?", [$rid]);
                set_flash('success', 'Item dihapus.');
            }
            redirect($backEdit($pid));
        }
        if ($op === 'save') {
            foreach (($_POST['rows'] ?? []) as $rid => $r) {
                $rid = (int)$rid; if ($rid <= 0) continue;
                if ($ent === 'pillar') {
                    $db->execute("UPDATE service_pillars SET kategori=?, badge=?, judul=?, deskripsi=?, tags=?, link_slug=?, urutan=? WHERE id=?",
                        [in_array($r['kategori'] ?? '', ['pillar','supporting'], true) ? $r['kategori'] : 'pillar',
                         trim($r['badge'] ?? ''), trim($r['judul'] ?? ''), trim($r['deskripsi'] ?? ''), trim($r['tags'] ?? ''),
                         trim($r['link_slug'] ?? '') ?: null, (int)($r['urutan'] ?? 0), $rid]);
                } elseif ($ent === 'tier') {
                    $db->execute("UPDATE service_tiers SET nama=?, judul=?, deskripsi=?, urutan=? WHERE id=?",
                        [trim($r['nama'] ?? ''), trim($r['judul'] ?? ''), trim($r['deskripsi'] ?? ''), (int)($r['urutan'] ?? 0), $rid]);
                } elseif ($ent === 'matrix') {
                    $db->execute("UPDATE service_matrix SET grup=?, baris=?, v_gold=?, v_platinum=?, v_diamond=?, urutan=? WHERE id=?",
                        [trim($r['grup'] ?? '') ?: null, trim($r['baris'] ?? ''), trim($r['v_gold'] ?? ''), trim($r['v_platinum'] ?? ''), trim($r['v_diamond'] ?? ''), (int)($r['urutan'] ?? 0), $rid]);
                } elseif ($ent === 'area') {
                    $db->execute("UPDATE service_areas SET kode=?, nama=?, judul=?, deskripsi=?, visual_note=?, urutan=? WHERE id=?",
                        [trim($r['kode'] ?? ''), trim($r['nama'] ?? ''), trim($r['judul'] ?? ''), trim($r['deskripsi'] ?? ''), trim($r['visual_note'] ?? ''), (int)($r['urutan'] ?? 0), $rid]);
                    // area image upload (rows uploaded as files[<rid>])
                    if (!empty($_FILES['area_img']['name'][$rid])) {
                        $file = ['name'=>$_FILES['area_img']['name'][$rid],'type'=>$_FILES['area_img']['type'][$rid],'tmp_name'=>$_FILES['area_img']['tmp_name'][$rid],'error'=>$_FILES['area_img']['error'][$rid],'size'=>$_FILES['area_img']['size'][$rid]];
                        $up = upload_image($file, 'services');
                        if ($up) $db->execute("UPDATE service_areas SET gambar=? WHERE id=?", [$up, $rid]);
                    }
                    // rewrite items from newline textarea
                    if (isset($r['items'])) {
                        $db->execute("DELETE FROM service_area_items WHERE area_id=?", [$rid]);
                        $u = 0;
                        foreach (preg_split('/\r\n|\r|\n/', (string)$r['items']) as $line) {
                            $line = trim($line); if ($line === '') continue;
                            $db->execute("INSERT INTO service_area_items (area_id,teks,urutan) VALUES (?,?,?)", [$rid, $line, ++$u]);
                        }
                    }
                }
                // inline EN translations for this row
                if ($others && !empty($r['i18n']) && function_exists('save_i18n_fields')) {
                    save_i18n_fields($tbl, $rid, ['i18n' => $r['i18n']]);
                }
            }
            set_flash('success', 'Tersimpan.');
            redirect($backEdit($pid));
        }
    }
}

/* ---------------- LIST ---------------- */
if ($action !== 'edit' || $id <= 0) {
    $pages = $db->fetchAll("SELECT * FROM service_pages ORDER BY urutan, id");
    $tipeBadge = ['landing' => 'info', 'capability' => 'success', 'package' => 'gray'];
    ?>
    <div class="page-header"><div>
      <h1><?= icon('layers', 18) ?> Services</h1>
      <div class="page-header-sub">Struktur 5 halaman: 1 landing, 2 capability, 2 package. Teks judul/hero editable per halaman.</div>
    </div></div>
    <div class="table-wrap"><table>
      <thead><tr><th>Halaman</th><th style="width:120px">Tipe</th><th style="width:260px">URL</th><th style="width:90px">Aktif</th><th style="width:90px">Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($pages as $pg): ?>
        <tr>
          <td style="font-weight:600"><?= htmlspecialchars($pg['judul']) ?></td>
          <td><span class="badge badge-<?= $tipeBadge[$pg['tipe']] ?? 'gray' ?>"><?= htmlspecialchars($pg['tipe']) ?></span></td>
          <td style="font-size:12px;color:var(--text-muted)">/<?= htmlspecialchars($pg['slug']) ?></td>
          <td><?= (int)$pg['is_active'] === 1 ? 'Ya' : 'Tidak' ?></td>
          <td><a href="<?= $backEdit($pg['id']) ?>" class="btn btn-secondary btn-sm">Edit</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <?php
    return;
}

/* ---------------- EDIT ---------------- */
$pg = $db->fetchOne("SELECT * FROM service_pages WHERE id=?", [$id]);
if (!$pg) { echo '<div class="card"><div class="card-body">Halaman tidak ditemukan. <a href="' . admin_url('?page=services') . '">Kembali</a>.</div></div>'; return; }
$tipe = $pg['tipe'];
$csrf = generate_csrf();

// EN inline helper for a row
$enRow = function (string $tbl, int $rid, array $fields) use ($others) {
    if (!$others) return '';
    $out = '';
    foreach ($others as $L) {
        $out .= '<div style="display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end;margin-top:6px;padding-top:6px;border-top:1px dashed var(--border)"><span style="font-size:11px;font-weight:700;color:var(--text-muted);padding-bottom:8px">' . strtoupper($L) . '</span>';
        foreach ($fields as $f => $label) {
            $val = tr_field($tbl, $rid, $f, '', $L);
            $out .= '<div style="flex:1;min-width:160px"><label style="font-size:11px;color:var(--text-muted)">' . htmlspecialchars($label) . '</label>'
                  . '<input type="text" name="rows[' . $rid . '][i18n][' . $L . '][' . $f . ']" value="' . htmlspecialchars($val) . '"></div>';
        }
        $out .= '</div>';
    }
    return $out;
};
?>
<div class="page-header"><div>
  <h1><?= icon('layers', 18) ?> <?= htmlspecialchars($pg['judul']) ?></h1>
  <div class="page-header-sub"><span class="badge badge-info"><?= htmlspecialchars($tipe) ?></span> · /<?= htmlspecialchars($pg['slug']) ?></div>
</div>
<div class="page-actions">
  <a href="<?= admin_url('?page=services') ?>" class="btn btn-secondary btn-sm"><?= icon('arrow-left', 15) ?> Semua Halaman</a>
  <a href="<?= url($pg['slug']) ?>" target="_blank" class="btn btn-secondary btn-sm">Lihat di situs</a>
</div></div>

<!-- ===== Page fields ===== -->
<form method="POST" action="<?= admin_url('?page=services&action=edit&id=' . $id) ?>" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= $csrf ?>">
<input type="hidden" name="action" value="save_page">
<input type="hidden" name="page_id" value="<?= $id ?>">
<div class="card" style="margin-bottom:16px">
  <div class="card-header"><div class="card-title">Konten Halaman</div></div>
  <div class="card-body">
    <div class="form-row">
      <div class="form-group"><label>Judul</label><input type="text" name="p[judul]" value="<?= htmlspecialchars($pg['judul']) ?>"></div>
      <div class="form-group"><label>Eyebrow</label><input type="text" name="p[eyebrow]" value="<?= htmlspecialchars($pg['eyebrow'] ?? '') ?>"></div>
    </div>
    <div class="form-group"><label>Headline (hero)</label><input type="text" name="p[headline]" value="<?= htmlspecialchars($pg['headline'] ?? '') ?>"></div>
    <div class="form-group"><label>Tagline</label><input type="text" name="p[tagline]" value="<?= htmlspecialchars($pg['tagline'] ?? '') ?>"></div>
    <div class="form-group"><label>Body / intro</label><textarea name="p[body]" rows="3" class="no-wysiwyg"><?= htmlspecialchars($pg['body'] ?? '') ?></textarea></div>
    <div class="form-row">
      <div class="form-group"><label>CTA — label</label><input type="text" name="p[cta_label]" value="<?= htmlspecialchars($pg['cta_label'] ?? '') ?>"></div>
      <div class="form-group"><label>CTA — target (slug/URL)</label><input type="text" name="p[cta_target]" value="<?= htmlspecialchars($pg['cta_target'] ?? '') ?>"></div>
    </div>
    <?php if ($tipe === 'landing'): ?>
      <div class="form-group"><label>Supporting statement (extra1)</label><textarea name="p[extra1]" rows="2" class="no-wysiwyg"><?= htmlspecialchars($pg['extra1'] ?? '') ?></textarea></div>
      <div class="form-row">
        <div class="form-group"><label>Lifecycle (pisah dengan →)</label><input type="text" name="p[extra2]" value="<?= htmlspecialchars($pg['extra2'] ?? '') ?>"></div>
        <div class="form-group"><label>Closing message (extra3)</label><input type="text" name="p[extra3]" value="<?= htmlspecialchars($pg['extra3'] ?? '') ?>"></div>
      </div>
    <?php else: ?>
      <input type="hidden" name="p[extra1]" value="<?= htmlspecialchars($pg['extra1'] ?? '') ?>">
      <div class="form-group"><label>Service flow / lifecycle (opsional, pisah →)</label><input type="text" name="p[extra2]" value="<?= htmlspecialchars($pg['extra2'] ?? '') ?>"></div>
      <input type="hidden" name="p[extra3]" value="<?= htmlspecialchars($pg['extra3'] ?? '') ?>">
    <?php endif; ?>
    <?php if ($tipe === 'capability'): ?>
      <div class="form-group"><label>Data &amp; AI — judul</label><input type="text" name="p[data_ai_title]" value="<?= htmlspecialchars($pg['data_ai_title'] ?? '') ?>"></div>
      <div class="form-group"><label>Data &amp; AI — poin (satu per baris)</label><textarea name="p[data_ai_body]" rows="4" class="no-wysiwyg"><?= htmlspecialchars($pg['data_ai_body'] ?? '') ?></textarea></div>
    <?php else: ?>
      <input type="hidden" name="p[data_ai_title]" value="<?= htmlspecialchars($pg['data_ai_title'] ?? '') ?>">
      <input type="hidden" name="p[data_ai_body]" value="<?= htmlspecialchars($pg['data_ai_body'] ?? '') ?>">
    <?php endif; ?>
    <div class="form-group"><label>Catatan visual (internal)</label><input type="text" name="p[visual_note]" value="<?= htmlspecialchars($pg['visual_note'] ?? '') ?>"></div>
    <div class="form-group"><label style="display:flex;align-items:center;gap:8px;font-weight:normal"><input type="checkbox" name="p[is_active]" value="1" style="width:auto" <?= (int)$pg['is_active'] === 1 ? 'checked' : '' ?>> Halaman aktif</label></div>
    <?php if (function_exists('i18n_fields_editor')):
      echo i18n_fields_editor('service_pages', $id, array_filter([
        'headline' => 'Headline', 'tagline' => 'Tagline', 'body' => ['label'=>'Body','type'=>'textarea'], 'cta_label' => 'CTA label',
        'extra1' => $tipe==='landing' ? 'Supporting statement' : null,
        'data_ai_title' => $tipe==='capability' ? 'Data & AI judul' : null,
        'data_ai_body' => $tipe==='capability' ? ['label'=>'Data & AI poin','type'=>'textarea'] : null,
      ]));
    endif; ?>
    <div style="margin-top:12px"><button class="btn btn-primary"><?= icon('save', 16) ?> Simpan Halaman</button></div>
  </div>
</div>
</form>

<?php
/* ---- section renderer for pillar/tier/matrix ---- */
function svc_section($db, $csrf, $backEdit, $id, $enRow, string $ent, string $title, array $cols) {
    $tbl = ['pillar'=>'service_pillars','tier'=>'service_tiers','matrix'=>'service_matrix'][$ent];
    $rows = $db->fetchAll("SELECT * FROM $tbl WHERE page_id=? ORDER BY urutan, id", [$id]);
    ?>
    <form method="POST" action="<?= admin_url('?page=services&action=edit&id=' . $id) ?>">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>"><input type="hidden" name="page_id" value="<?= $id ?>">
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><div class="card-title"><?= htmlspecialchars($title) ?></div><div class="card-subtitle"><?= count($rows) ?> item</div></div>
      <div class="card-body">
        <?php foreach ($rows as $r): $rid = (int)$r['id']; ?>
          <div style="border:1px solid var(--border);border-radius:10px;padding:12px 14px;margin-bottom:10px">
            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
              <div style="width:64px"><label style="font-size:11px;color:var(--text-muted)">Urut</label><input type="number" name="rows[<?= $rid ?>][urutan]" value="<?= (int)$r['urutan'] ?>"></div>
              <?php foreach ($cols as $c => $meta): $w = $meta['w'] ?? 'flex:1;min-width:150px'; ?>
                <?php if (($meta['type'] ?? 'text') === 'select'): ?>
                  <div style="<?= $w ?>"><label style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars($meta['label']) ?></label>
                    <select name="rows[<?= $rid ?>][<?= $c ?>]" class="form-control"><?php foreach ($meta['options'] as $ov): ?><option value="<?= $ov ?>" <?= ($r[$c] ?? '')===$ov?'selected':'' ?>><?= $ov ?></option><?php endforeach; ?></select></div>
                <?php elseif (($meta['type'] ?? 'text') === 'textarea'): ?>
                  <div style="<?= $w ?>"><label style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars($meta['label']) ?></label>
                    <textarea name="rows[<?= $rid ?>][<?= $c ?>]" rows="2"><?= htmlspecialchars($r[$c] ?? '') ?></textarea></div>
                <?php else: ?>
                  <div style="<?= $w ?>"><label style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars($meta['label']) ?></label>
                    <input type="text" name="rows[<?= $rid ?>][<?= $c ?>]" value="<?= htmlspecialchars($r[$c] ?? '') ?>"></div>
                <?php endif; ?>
              <?php endforeach; ?>
              <button type="submit" name="action" value="del_<?= $ent ?>" formnovalidate class="btn btn-danger btn-sm" style="margin-bottom:2px"
                onclick="var i=document.createElement('input');i.type='hidden';i.name='rid';i.value='<?= $rid ?>';this.form.appendChild(i);return confirm('Hapus item ini?')"><?= icon('trash', 14) ?></button>
            </div>
            <?= $enRow($tbl, $rid, $ent==='matrix' ? ['baris'=>'Baris (EN)'] : ($ent==='tier' ? ['judul'=>'Judul (EN)','deskripsi'=>'Deskripsi (EN)'] : ['judul'=>'Judul (EN)','deskripsi'=>'Deskripsi (EN)'])) ?>
          </div>
        <?php endforeach; ?>
        <div style="display:flex;gap:10px;margin-top:6px">
          <button type="submit" name="action" value="save_<?= $ent ?>" class="btn btn-primary"><?= icon('save', 15) ?> Simpan</button>
          <button type="submit" name="action" value="add_<?= $ent ?>" formnovalidate class="btn btn-secondary"><?= icon('plus', 15) ?> Tambah</button>
        </div>
      </div>
    </div>
    </form>
    <?php
}

if ($tipe === 'landing') {
    svc_section($db, $csrf, $backEdit, $id, $enRow, 'pillar', 'Pilar & Supporting Capability', [
        'kategori' => ['label'=>'Kategori','type'=>'select','options'=>['pillar','supporting'],'w'=>'width:130px'],
        'badge' => ['label'=>'Badge','w'=>'width:150px'],
        'judul' => ['label'=>'Judul'],
        'deskripsi' => ['label'=>'Deskripsi','type'=>'textarea','w'=>'flex:1;min-width:220px'],
        'tags' => ['label'=>'Tags (pisah koma)','w'=>'flex:1;min-width:160px'],
        'link_slug' => ['label'=>'Link slug','w'=>'flex:1;min-width:160px'],
    ]);
}
if ($tipe === 'package') {
    svc_section($db, $csrf, $backEdit, $id, $enRow, 'tier', 'Tier (Gold / Platinum / Diamond)', [
        'nama' => ['label'=>'Nama','w'=>'width:120px'],
        'judul' => ['label'=>'Judul'],
        'deskripsi' => ['label'=>'Deskripsi','type'=>'textarea','w'=>'flex:1;min-width:240px'],
    ]);
    svc_section($db, $csrf, $backEdit, $id, $enRow, 'matrix', 'Matriks Perbandingan', [
        'grup' => ['label'=>'Grup','w'=>'width:180px'],
        'baris' => ['label'=>'Baris','w'=>'flex:1;min-width:180px'],
        'v_gold' => ['label'=>'Gold','w'=>'width:120px'],
        'v_platinum' => ['label'=>'Platinum','w'=>'width:120px'],
        'v_diamond' => ['label'=>'Diamond','w'=>'width:120px'],
    ]);
}
if ($tipe === 'capability') {
    // Areas + items (items as newline textarea) + per-area image upload.
    $areas = $db->fetchAll("SELECT * FROM service_areas WHERE page_id=? ORDER BY urutan, id", [$id]);
    ?>
    <form method="POST" action="<?= admin_url('?page=services&action=edit&id=' . $id) ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>"><input type="hidden" name="page_id" value="<?= $id ?>">
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><div class="card-title">Area Layanan</div><div class="card-subtitle"><?= count($areas) ?> area</div></div>
      <div class="card-body">
        <?php foreach ($areas as $a): $rid = (int)$a['id'];
          $items = $db->fetchAll("SELECT teks FROM service_area_items WHERE area_id=? ORDER BY urutan, id", [$rid]);
          $itemsText = implode("\n", array_map(fn($x) => $x['teks'], $items));
          $img = !empty($a['gambar']) ? uploads_url($a['gambar']) : ''; ?>
          <div style="border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:12px">
            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
              <div style="width:64px"><label style="font-size:11px;color:var(--text-muted)">Urut</label><input type="number" name="rows[<?= $rid ?>][urutan]" value="<?= (int)$a['urutan'] ?>"></div>
              <div style="width:80px"><label style="font-size:11px;color:var(--text-muted)">Kode</label><input type="text" name="rows[<?= $rid ?>][kode]" value="<?= htmlspecialchars($a['kode'] ?? '') ?>"></div>
              <div style="width:130px"><label style="font-size:11px;color:var(--text-muted)">Nama (CONSULT)</label><input type="text" name="rows[<?= $rid ?>][nama]" value="<?= htmlspecialchars($a['nama'] ?? '') ?>"></div>
              <div style="flex:1;min-width:200px"><label style="font-size:11px;color:var(--text-muted)">Judul</label><input type="text" name="rows[<?= $rid ?>][judul]" value="<?= htmlspecialchars($a['judul'] ?? '') ?>"></div>
              <button type="submit" name="action" value="del_area" formnovalidate class="btn btn-danger btn-sm" style="margin-bottom:2px"
                onclick="var i=document.createElement('input');i.type='hidden';i.name='rid';i.value='<?= $rid ?>';this.form.appendChild(i);return confirm('Hapus area ini beserta itemnya?')"><?= icon('trash', 14) ?></button>
            </div>
            <div class="form-group" style="margin-top:8px"><label style="font-size:11px;color:var(--text-muted)">Deskripsi (opsional)</label><input type="text" name="rows[<?= $rid ?>][deskripsi]" value="<?= htmlspecialchars($a['deskripsi'] ?? '') ?>"></div>
            <div class="form-group"><label style="font-size:11px;color:var(--text-muted)">Item kapabilitas (satu per baris)</label><textarea name="rows[<?= $rid ?>][items]" rows="6"><?= htmlspecialchars($itemsText) ?></textarea></div>
            <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap">
              <div style="width:110px;height:70px;border:1px solid var(--border);border-radius:8px;overflow:hidden;background:#f3f6fb;display:grid;place-items:center;flex-shrink:0">
                <?php if ($img): ?><img src="<?= htmlspecialchars($img) ?>" style="width:100%;height:100%;object-fit:cover"><?php else: ?><span style="font-size:10px;color:var(--text-muted)">no image</span><?php endif; ?>
              </div>
              <div><label style="font-size:11px;color:var(--text-muted)">Foto area (<?= htmlspecialchars($a['visual_note'] ?? 'sesuai visual direction') ?>)</label>
                <input type="file" name="area_img[<?= $rid ?>]" accept="image/png,image/jpeg,image/webp"></div>
            </div>
            <?= $enRow('service_areas', $rid, ['judul'=>'Judul (EN)','deskripsi'=>'Deskripsi (EN)']) ?>
          </div>
        <?php endforeach; ?>
        <div style="display:flex;gap:10px;margin-top:6px">
          <button type="submit" name="action" value="save_area" class="btn btn-primary"><?= icon('save', 15) ?> Simpan Area</button>
          <button type="submit" name="action" value="add_area" formnovalidate class="btn btn-secondary"><?= icon('plus', 15) ?> Tambah Area</button>
        </div>
      </div>
    </div>
    </form>
    <?php
}
