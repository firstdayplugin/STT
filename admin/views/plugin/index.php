<?php
$page_title = 'Manajemen Plugin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error','Token invalid.'); redirect(admin_url('?page=plugin')); }
    $a = $_POST['_action'] ?? '';

    if ($a === 'toggle') {
        $plugin_slug = sanitize($_POST['plugin_slug'] ?? '');
        if ($plugin_slug) {
            $plugin = $db->fetchOne("SELECT * FROM plugins WHERE slug=?",[$plugin_slug]);
            if ($plugin) {
                $new = $plugin['is_active'] ? 0 : 1;
                $db->execute("UPDATE plugins SET is_active=? WHERE slug=?",[$new,$plugin_slug]);
                log_activity('update','Toggle plugin: '.$plugin_slug,$user['id']);
                set_flash('success', 'Plugin ' . ($new?'diaktifkan':'dinonaktifkan') . '!');
            }
        }
        redirect(admin_url('?page=plugin'));
    }
}

$all_plugins = $db->fetchAll("SELECT * FROM plugins ORDER BY nama");
$csrf = generate_csrf();

// Built-in plugin definitions
$plugin_defs = [
    'marketplace' => [
        'icon'    => '',
        'version' => '1.0.0',
        'author'  => 'Reklamepedia',
        'desc'    => 'Tambahkan tombol beli ke marketplace (Tokopedia, Shopee, Lazada, dll) di halaman produk.',
        'features'=> ['Tombol multi-marketplace di detail produk','Badge platform dengan logo','Link tracking'],
    ],
];
?>

<div class="page-header">
    <div class="page-title"><?= icon('puzzle', 16) ?> Plugin Manager
        <small>Aktifkan / nonaktifkan fitur tambahan</small>
    </div>
</div>

<div class="alert alert-info mb-24">
    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Plugin mengaktifkan fitur tambahan tanpa mengubah kode inti. Menonaktifkan plugin tidak menghapus datanya.
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px">
<?php foreach ($all_plugins as $plugin):
    $def = $plugin_defs[$plugin['slug']] ?? [];
?>
<div class="card" style="<?= $plugin['is_active'] ? 'border-color:rgba(232,160,32,0.3)' : '' ?>">
    <div class="card-header">
        <div style="display:flex;align-items:center;gap:12px">
            <div style="font-size:28px"><?= $def['icon'] ?? '' ?></div>
            <div>
                <div class="card-title"><?= htmlspecialchars($plugin['nama']) ?></div>
                <div class="text-xs text-muted">v<?= $def['version'] ?? '1.0' ?> by <?= $def['author'] ?? 'Unknown' ?></div>
            </div>
        </div>
        <span class="badge <?= $plugin['is_active'] ? 'badge-success' : 'badge-muted' ?>">
            <?= $plugin['is_active'] ? 'Aktif' : 'Nonaktif' ?>
        </span>
    </div>
    <div class="card-body">
        <p style="font-size:14px;color:var(--text-muted);margin-bottom:14px">
            <?= htmlspecialchars($plugin['deskripsi'] ?: ($def['desc'] ?? '')) ?>
        </p>
        <?php if (!empty($def['features'])): ?>
        <ul style="list-style:none;display:flex;flex-direction:column;gap:6px;margin-bottom:16px">
            <?php foreach ($def['features'] as $feat): ?>
            <li style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-muted)">
                <span style="color:var(--success)"><?= icon('check', 16) ?></span> <?= $feat ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="_action" value="toggle">
            <input type="hidden" name="plugin_slug" value="<?= $plugin['slug'] ?>">
            <button type="submit" class="btn w-full <?= $plugin['is_active'] ? 'btn-danger' : 'btn-primary' ?>"
                    style="justify-content:center"
                    <?= $user['role'] !== 'superadmin' && $user['role'] !== 'admin' ? 'disabled' : '' ?>>
                <?= $plugin['is_active'] ? 'Nonaktifkan Plugin' : 'Aktifkan Plugin' ?>
            </button>
        </form>
    </div>
</div>
<?php endforeach; ?>

<!-- Coming Soon -->
<div class="card" style="border-style:dashed;opacity:0.5">
    <div class="card-body" style="text-align:center;padding:40px">
        <div style="font-size:36px;margin-bottom:12px"><?= icon('arrow-right', 16) ?></div>
        <div style="font-weight:600;margin-bottom:8px">Plugin Lebih Banyak</div>
        <div class="text-muted text-sm">Plugin tambahan akan tersedia dalam update berikutnya</div>
    </div>
</div>
</div>
