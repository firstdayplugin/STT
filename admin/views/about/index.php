<?php
/**
 * Admin — Tentang Kami (module: about). Edits the About Us repeaters stored in `about_items`:
 * Mission bullets, ICARE values, Milestone timeline, Awards, Quality standards, Certifications.
 * Singular About copy (intro/vision/mission text) lives in Konten Halaman → Tentang Kami.
 */
$db = Database::getInstance();

$SECTIONS = [
    'mission'   => ['label' => 'Misi (poin per baris)',    'icon' => 'check',     'fields' => ['teks']],
    'value'     => ['label' => 'Nilai — ICARE',            'icon' => 'star',      'fields' => ['kode', 'judul', 'teks']],
    'milestone' => ['label' => 'Milestone (timeline)',     'icon' => 'compass',   'fields' => ['tahun', 'now']],
    'award'     => ['label' => 'Penghargaan / Awards',     'icon' => 'briefcase', 'fields' => ['judul', 'teks']],
    'quality'   => ['label' => 'Quality Standards (ISO)',  'icon' => 'layers',    'fields' => ['judul']],
    'cert'      => ['label' => 'Sertifikasi Vendor',       'icon' => 'box',       'fields' => ['judul']],
];
$FIELD_LABEL = ['kode' => 'Kode (I/C/A/R/E)', 'judul' => 'Judul', 'teks' => 'Teks', 'tahun' => 'Tahun'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error', 'Token tidak valid.'); redirect(admin_url('?page=about')); }
    $act = $_POST['action'] ?? '';

    if ($act === 'add') {
        $seksi = $_POST['seksi'] ?? '';
        if (isset($SECTIONS[$seksi])) {
            $maxo = (int)($db->fetchOne("SELECT COALESCE(MAX(urutan),0) m FROM about_items WHERE seksi=?", [$seksi])['m'] ?? 0);
            $db->execute("INSERT INTO about_items (seksi,kode,judul,teks,tahun,urutan,is_active) VALUES (?,?,?,?,?,?,1)",
                [$seksi, null, null, null, null, $maxo + 1]);
            set_flash('success', 'Item baru ditambahkan. Isi lalu Simpan.');
        }
        redirect(admin_url('?page=about#s-' . urlencode($seksi)));
    }

    if ($act === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) { $db->execute("DELETE FROM about_items WHERE id=?", [$id]); set_flash('success', 'Item dihapus.'); }
        redirect(admin_url('?page=about'));
    }

    if ($act === 'save_all') {
        $items = $_POST['items'] ?? [];
        $n = 0;
        foreach ($items as $id => $row) {
            $id = (int)$id;
            if ($id <= 0) continue;
            $now = !empty($row['now']);
            $db->execute("UPDATE about_items SET kode=?, judul=?, teks=?, tahun=?, urutan=? WHERE id=?", [
                $now ? 'now' : (trim((string)($row['kode'] ?? '')) ?: null),
                trim((string)($row['judul'] ?? '')) ?: null,
                trim((string)($row['teks'] ?? '')) ?: null,
                trim((string)($row['tahun'] ?? '')) ?: null,
                (int)($row['urutan'] ?? 0),
                $id,
            ]);
            // Optional per-language translations (i18n[<lang>][<field>] under items[<id>]).
            if (function_exists('save_i18n_fields') && !empty($row['i18n'])) {
                save_i18n_fields('about_items', $id, ['i18n' => $row['i18n']]);
            }
            $n++;
        }
        set_flash('success', "Tersimpan. $n item diperbarui.");
        redirect(admin_url('?page=about'));
    }
}

$csrf = generate_csrf();
$multilang = function_exists('is_multilang') && is_multilang();
$others    = $multilang ? array_values(array_filter(available_langs(), fn($l) => $l !== default_lang())) : [];
$all = [];
foreach (array_keys($SECTIONS) as $s) {
    $all[$s] = $db->fetchAll("SELECT * FROM about_items WHERE seksi=? ORDER BY urutan, id", [$s]);
}
?>

<div class="page-header"><div>
  <h1><?= icon('users', 18) ?> Tentang Kami — Konten</h1>
  <div class="page-header-sub">Misi, Nilai (ICARE), Milestone, Awards, Quality, &amp; Sertifikasi. Teks judul/visi/misi ada di <a href="<?= admin_url('?page=content&p=about') ?>">Konten Halaman → Tentang Kami</a>.</div>
</div></div>

<form method="POST" action="<?= admin_url('?page=about') ?>">
<input type="hidden" name="csrf_token" value="<?= $csrf ?>">
<input type="hidden" name="action" value="save_all">

<?php foreach ($SECTIONS as $seksi => $meta): $rows = $all[$seksi]; ?>
<div class="card" id="s-<?= $seksi ?>" style="margin-bottom:16px">
  <div class="card-header"><div class="card-title"><?= icon($meta['icon'], 16) ?> <?= htmlspecialchars($meta['label']) ?></div>
    <div class="card-subtitle"><?= count($rows) ?> item</div></div>
  <div class="card-body">
    <?php if (empty($rows)): ?>
      <div style="color:var(--text-muted);font-size:13px;margin-bottom:12px">Belum ada item.</div>
    <?php endif; ?>
    <?php foreach ($rows as $r): $id = (int)$r['id']; ?>
      <div style="border:1px solid var(--border);border-radius:10px;padding:12px 14px;margin-bottom:10px">
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
          <div style="width:70px"><label style="font-size:11px;color:var(--text-muted)">Urut</label>
            <input type="number" name="items[<?= $id ?>][urutan]" value="<?= (int)$r['urutan'] ?>"></div>

          <?php foreach ($meta['fields'] as $f): ?>
            <?php if ($f === 'now'): ?>
              <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-muted);padding-bottom:8px">
                <input type="checkbox" name="items[<?= $id ?>][now]" value="1" style="width:auto" <?= ($r['kode'] ?? '') === 'now' ? 'checked' : '' ?>> Tandai "Sekarang"
              </label>
            <?php elseif ($f === 'teks'): ?>
              <div style="flex:1;min-width:260px"><label style="font-size:11px;color:var(--text-muted)"><?= $FIELD_LABEL[$f] ?></label>
                <textarea name="items[<?= $id ?>][teks]" rows="2"><?= htmlspecialchars($r['teks'] ?? '') ?></textarea></div>
            <?php else: ?>
              <div style="<?= $f === 'kode' ? 'width:110px' : 'flex:1;min-width:160px' ?>"><label style="font-size:11px;color:var(--text-muted)"><?= $FIELD_LABEL[$f] ?></label>
                <input type="text" name="items[<?= $id ?>][<?= $f ?>]" value="<?= htmlspecialchars($r[$f] ?? '') ?>"></div>
            <?php endif; ?>
          <?php endforeach; ?>

          <button type="submit" name="action" value="delete" formnovalidate
                  onclick="this.form.querySelector('[name=id]')?.remove();var i=document.createElement('input');i.type='hidden';i.name='id';i.value='<?= $id ?>';this.form.appendChild(i);return confirm('Hapus item ini?')"
                  class="btn btn-danger btn-sm" style="margin-bottom:2px"><?= icon('trash', 14) ?></button>
        </div>

        <?php // Per-language translations (optional), reuse the shared editor's field spec.
        if ($others):
          foreach ($others as $L):
            $tfields = array_values(array_filter($meta['fields'], fn($x) => in_array($x, ['judul', 'teks'], true)));
            if (!$tfields) continue; ?>
          <div style="margin-top:8px;padding-top:8px;border-top:1px dashed var(--border);display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
            <span style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;padding-bottom:8px"><?= htmlspecialchars(strtoupper($L)) ?></span>
            <?php foreach ($tfields as $f): $tv = tr_field('about_items', $id, $f, '', $L); ?>
              <div style="flex:1;min-width:200px"><label style="font-size:11px;color:var(--text-muted)"><?= $FIELD_LABEL[$f] ?> (<?= strtoupper($L) ?>)</label>
                <input type="text" name="items[<?= $id ?>][i18n][<?= htmlspecialchars($L) ?>][<?= $f ?>]" value="<?= htmlspecialchars($tv) ?>"></div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; endif; ?>
      </div>
    <?php endforeach; ?>

    <button type="submit" name="action" value="add" formnovalidate class="btn btn-secondary btn-sm"
            onclick="var i=document.createElement('input');i.type='hidden';i.name='seksi';i.value='<?= $seksi ?>';this.form.appendChild(i)">
      <?= icon('plus', 14) ?> Tambah item</button>
  </div>
</div>
<?php endforeach; ?>

<div style="position:sticky;bottom:0;background:white;padding:16px;border-top:2px solid var(--border);margin:16px -16px -16px;display:flex;justify-content:flex-end;z-index:10;box-shadow:0 -4px 12px rgba(0,0,0,0.04)">
  <button type="submit" name="action" value="save_all" class="btn btn-primary btn-lg"><?= icon('save', 16) ?> Simpan Semua</button>
</div>
</form>
