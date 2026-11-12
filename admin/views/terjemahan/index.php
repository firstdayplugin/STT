<?php
/**
 * Admin — Translations (module: terjemahan). Manage UI-string translations
 * (the `translations` table, grp='ui') for every non-default language, so the
 * client can localize the interface without touching SQL.
 */
$db = Database::getInstance();
$def = default_lang();
$langs = array_values(array_filter(available_langs(), fn($l) => $l !== $def));
$active = $_GET['lang'] ?? ($langs[0] ?? '');
if ($active !== '' && !in_array($active, $langs, true)) $active = $langs[0] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error', 'Token tidak valid.'); redirect(admin_url('?page=terjemahan')); }
    $lang = $_POST['lang'] ?? '';
    if (in_array($lang, $langs, true)) {
        $vals = $_POST['v'] ?? [];
        $known = ui_string_defaults();
        foreach ($vals as $k => $v) {
            $k = (string) $k; $v = trim((string) $v);
            if (!isset($known[$k])) continue; // only registry keys
            $existing = $db->fetchOne("SELECT id FROM translations WHERE lang=? AND grp='ui' AND k=?", [$lang, $k]);
            if ($v === '') {
                if ($existing) $db->execute("DELETE FROM translations WHERE id=?", [$existing['id']]);
            } elseif ($existing) {
                $db->execute("UPDATE translations SET v=? WHERE id=?", [$v, $existing['id']]);
            } else {
                $db->execute("INSERT INTO translations (lang,grp,k,v) VALUES (?,?,?,?)", [$lang, 'ui', $k, $v]);
            }
        }
        // Menu labels (content_i18n on the `menus` table).
        foreach (($_POST['menu'] ?? []) as $mid => $mv) {
            set_tr_field('menus', (int) $mid, 'nama', $lang, (string) $mv);
        }
        log_activity('update', 'Update terjemahan UI + menu (' . $lang . ')');
        set_flash('success', 'Terjemahan disimpan.');
    }
    redirect(admin_url('?page=terjemahan&lang=' . urlencode($lang)));
}

// current values for the active language
$current = [];
if ($active !== '') {
    foreach ($db->fetchAll("SELECT k, v FROM translations WHERE lang=? AND grp='ui'", [$active]) as $r) { $current[$r['k']] = $r['v']; }
}
$menu_items = $db->fetchAll("SELECT id, nama, parent_id FROM menus WHERE is_active=1 ORDER BY COALESCE(parent_id,0), urutan, id");
$csrf = generate_csrf();
$groups = ui_strings();
?>

<div class="page-header">
  <div>
    <h1><?= icon('globe', 18) ?> Terjemahan</h1>
    <div class="page-header-sub">Terjemahkan teks antarmuka per bahasa. Bahasa default (<?= htmlspecialchars(strtoupper($def)) ?>) memakai teks asli; kosongkan untuk memakai default.</div>
  </div>
</div>

<?php if (empty($langs)): ?>
  <div class="card"><div class="card-body">
    Situs sedang satu bahasa. Aktifkan bahasa lain di <a href="<?= admin_url('?page=pengaturan') ?>">Pengaturan</a>
    (setting <code>languages</code>, mis. <code>id,en</code>).
  </div></div>
<?php else: ?>

  <?php if (count($langs) > 1): ?>
  <div class="card" style="margin-bottom:16px"><div class="card-body" style="display:flex;gap:8px;align-items:center">
    <span style="font-size:12px;color:var(--text-muted);font-weight:600">BAHASA</span>
    <?php foreach ($langs as $l): ?>
      <a href="<?= admin_url('?page=terjemahan&lang=' . urlencode($l)) ?>" class="btn btn-sm <?= $active === $l ? 'btn-primary' : 'btn-secondary' ?>"><?= htmlspecialchars(strtoupper($l)) ?> — <?= htmlspecialchars(lang_label($l)) ?></a>
    <?php endforeach; ?>
  </div></div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="lang" value="<?= htmlspecialchars($active) ?>">
    <?php foreach ($groups as $gname => $keys): ?>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><div class="card-title"><?= htmlspecialchars($gname) ?></div></div>
      <div class="card-body">
        <?php foreach ($keys as $k => $iddef): ?>
        <div class="form-row" style="align-items:center">
          <div class="form-group" style="margin-bottom:10px">
            <label style="font-size:12px;color:var(--text-muted)"><?= htmlspecialchars(strtoupper($def)) ?> · <code style="font-size:11px"><?= htmlspecialchars($k) ?></code></label>
            <div style="padding:9px 12px;background:var(--bg-soft,#f6f9fd);border:1px solid var(--border);border-radius:8px;font-size:13px;color:var(--text-muted)"><?= htmlspecialchars($iddef) ?></div>
          </div>
          <div class="form-group" style="margin-bottom:10px">
            <label style="font-size:12px"><?= htmlspecialchars(strtoupper($active)) ?></label>
            <input type="text" name="v[<?= htmlspecialchars($k) ?>]" value="<?= htmlspecialchars($current[$k] ?? '') ?>" placeholder="<?= htmlspecialchars($iddef) ?>">
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <?php if ($menu_items): ?>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><div class="card-title"><?= icon('menu', 15) ?> Menu Navigasi</div>
        <div class="card-subtitle">Label menu header/footer per bahasa.</div></div>
      <div class="card-body">
        <?php foreach ($menu_items as $mi): $mv = tr_field('menus', (int)$mi['id'], 'nama', '', $active); ?>
        <div class="form-row" style="align-items:center">
          <div class="form-group" style="margin-bottom:10px">
            <label style="font-size:12px;color:var(--text-muted)"><?= htmlspecialchars(strtoupper($def)) ?><?= $mi['parent_id'] ? ' · sub' : '' ?></label>
            <div style="padding:9px 12px;background:var(--bg-soft,#f6f9fd);border:1px solid var(--border);border-radius:8px;font-size:13px;color:var(--text-muted)"><?= $mi['parent_id'] ? '— ' : '' ?><?= htmlspecialchars($mi['nama']) ?></div>
          </div>
          <div class="form-group" style="margin-bottom:10px">
            <label style="font-size:12px"><?= htmlspecialchars(strtoupper($active)) ?></label>
            <input type="text" name="menu[<?= (int)$mi['id'] ?>]" value="<?= htmlspecialchars($mv) ?>" placeholder="<?= htmlspecialchars($mi['nama']) ?>">
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div style="display:flex;justify-content:flex-end;position:sticky;bottom:0;padding:12px 0;background:linear-gradient(transparent,var(--bg) 40%)">
      <button type="submit" class="btn btn-primary btn-lg"><?= icon('save', 16) ?> Simpan Terjemahan <?= htmlspecialchars(strtoupper($active)) ?></button>
    </div>
  </form>
<?php endif; ?>
