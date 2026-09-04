<?php
$page_title = 'Manajemen Pengguna';

// Only superadmin can manage users
if ($user['role'] !== 'superadmin') {
    echo '<div class="alert alert-error">⛔ Hanya superadmin yang dapat mengakses halaman ini.</div>';
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error','Token invalid.'); redirect(admin_url('?page=users')); }
    $a = $_POST['_action'] ?? '';

    if ($a === 'delete') {
        $del_id = (int)($_POST['del_id'] ?? 0);
        if ($del_id && $del_id !== (int)$user['id']) {
            $db->execute("DELETE FROM users WHERE id=?",[$del_id]);
            log_activity('delete','Hapus user #'.$del_id,$user['id']);
            set_flash('success','Pengguna dihapus.');
        } else {
            set_flash('error','Tidak bisa menghapus akun sendiri.');
        }
        redirect(admin_url('?page=users'));
    }

    if ($a === 'toggle_active') {
        $tid = (int)($_POST['tid'] ?? 0);
        if ($tid && $tid !== (int)$user['id']) {
            $curr = $db->fetchOne("SELECT is_active FROM users WHERE id=?",[$tid]);
            if ($curr) {
                $new = $curr['is_active'] ? 0 : 1;
                $db->execute("UPDATE users SET is_active=? WHERE id=?",[$new,$tid]);
                set_flash('success','Status pengguna diperbarui.');
            }
        }
        redirect(admin_url('?page=users'));
    }

    if ($a === 'save') {
        $uid     = (int)($_POST['id'] ?? 0);
        $nama    = trim($_POST['nama'] ?? '');
        $username= trim($_POST['username'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $role    = in_array($_POST['role']??'',['superadmin','admin','penulis','admin_produk','tim_ads'])?$_POST['role']:'penulis';
        $pass    = $_POST['password'] ?? '';
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if (!$nama || !$username || !$email) { set_flash('error','Nama, username, dan email wajib diisi.'); redirect(admin_url('?page=users&action='.($uid?'edit&id='.$uid:'create'))); }

        $exist_user = $db->fetchOne("SELECT id FROM users WHERE username=? AND id!=?",[$username,$uid]);
        $exist_email = $db->fetchOne("SELECT id FROM users WHERE email=? AND id!=?",[$email,$uid]);
        if ($exist_user) { set_flash('error','Username sudah digunakan.'); redirect(admin_url('?page=users&action='.($uid?'edit&id='.$uid:'create'))); }
        if ($exist_email) { set_flash('error','Email sudah digunakan.'); redirect(admin_url('?page=users&action='.($uid?'edit&id='.$uid:'create'))); }

        $data = compact('nama','username','email','role','is_active');
        $data['updated_at'] = date('Y-m-d H:i:s');
        if ($pass) $data['password'] = password_hash($pass, PASSWORD_BCRYPT);

        // Avatar upload
        if (!empty($_FILES['avatar']['name'])) {
            $av = upload_image($_FILES['avatar'],'avatars');
            if ($av) $data['avatar'] = $av;
        }

        if ($uid) {
            $set=implode(',',array_map(fn($k)=>"$k=?",array_keys($data)));
            $db->execute("UPDATE users SET $set WHERE id=?", [...array_values($data),$uid]);
            log_activity('update','Update user: '.$username,$user['id']);
        } else {
            if (!$pass) { set_flash('error','Password wajib diisi untuk pengguna baru.'); redirect(admin_url('?page=users&action=create')); }
            $data['created_at'] = date('Y-m-d H:i:s');
            $db->insert('users',$data);
            log_activity('create','Tambah user: '.$username,$user['id']);
        }
        set_flash('success','Pengguna berhasil disimpan!');
        redirect(admin_url('?page=users'));
    }
}

$edit_usr = null;
if ($action === 'edit' && $id) {
    $edit_usr = $db->fetchOne("SELECT * FROM users WHERE id=?",[$id]);
    if (!$edit_usr) { set_flash('error','Pengguna tidak ditemukan.'); redirect(admin_url('?page=users')); }
}

$all_users = $db->fetchAll("SELECT * FROM users ORDER BY role, nama");
$csrf = generate_csrf();

$roles_info = [
    'superadmin'   => ['label'=>'Super Admin',     'color'=>'badge-danger',  'desc'=>'Akses penuh ke semua fitur'],
    'admin'        => ['label'=>'Admin',            'color'=>'badge-warning', 'desc'=>'Akses ke semua konten dan pengaturan'],
    'penulis'      => ['label'=>'Penulis',          'color'=>'badge-info',    'desc'=>'Kelola blog dan artikel'],
    'admin_produk' => ['label'=>'Admin Produk',     'color'=>'badge-success', 'desc'=>'Kelola produk dan galeri'],
    'tim_ads'      => ['label'=>'Tim Ads',          'color'=>'badge-accent',  'desc'=>'Kelola iklan dan SEO'],
];

if ($action === 'create' || $action === 'edit'):
$page_title = $action === 'edit' ? 'Edit Pengguna' : 'Tambah Pengguna';
$breadcrumbs = [['label'=>'Pengguna','url'=>admin_url('?page=users')]];
?>

<div class="page-header">
    <div class="page-title"><?= $page_title ?></div>
    <a href="<?= admin_url('?page=users') ?>" class="btn btn-secondary">← Kembali</a>
</div>

<div style="max-width:600px">
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="_action" value="save">
    <input type="hidden" name="id" value="<?= $edit_usr['id'] ?? '' ?>">

    <div class="card mb-24">
        <div class="card-header"><div class="card-title">Informasi Pengguna</div></div>
        <div class="card-body">
            <div class="form-group">
                <label>Nama Lengkap <span class="required">*</span></label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($edit_usr['nama'] ?? '') ?>" required>
            </div>
            <div class="form-grid">
                <div class="form-group mb-0">
                    <label>Username <span class="required">*</span></label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($edit_usr['username'] ?? '') ?>" required autocomplete="off">
                </div>
                <div class="form-group mb-0">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($edit_usr['email'] ?? '') ?>" required>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-24">
        <div class="card-header"><div class="card-title">Akses & Password</div></div>
        <div class="card-body">
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control">
                    <?php foreach ($roles_info as $rk => $rv): ?>
                    <option value="<?= $rk ?>" <?= ($edit_usr['role'] ?? 'penulis') === $rk ? 'selected' : '' ?>>
                        <?= $rv['label'] ?> — <?= $rv['desc'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Password <?= $action === 'edit' ? '(kosongkan jika tidak diganti)' : '<span class="required">*</span>' ?></label>
                <input type="password" name="password" class="form-control"
                       <?= $action === 'create' ? 'required' : '' ?>
                       autocomplete="new-password"
                       placeholder="<?= $action === 'edit' ? 'Isi untuk ganti password' : 'Min 8 karakter' ?>">
            </div>
            <div class="form-group mb-0">
                <div class="toggle-switch">
                    <label class="switch">
                        <input type="checkbox" name="is_active" value="1" <?= ($edit_usr['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                    <span>Akun Aktif</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-24">
        <div class="card-header"><div class="card-title">Foto Profil</div></div>
        <div class="card-body">
            <?php if (!empty($edit_usr['avatar'])): ?>
            <img src="<?= uploads_url($edit_usr['avatar']) ?>" style="width:60px;height:60px;border-radius:50%;object-fit:cover;margin-bottom:12px;border:2px solid var(--border)">
            <?php endif; ?>
            <input type="file" name="avatar" class="form-control" accept="image/*">
        </div>
    </div>

    <div style="text-align:right">
        <a href="<?= admin_url('?page=users') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">💾 Simpan Pengguna</button>
    </div>
</form>
</div>

<?php else: ?>

<div class="page-header">
    <div class="page-title">👥 Pengguna
        <small><?= count($all_users) ?> pengguna terdaftar</small>
    </div>
    <a href="<?= admin_url('?page=users&action=create') ?>" class="btn btn-primary">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Tambah Pengguna
    </a>
</div>

<!-- Role legend -->
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px">
<?php foreach ($roles_info as $rk => $rv): ?>
<div style="display:flex;align-items:center;gap:8px;padding:8px 14px;background:var(--surface);border:1px solid var(--border);border-radius:10px">
    <span class="badge <?= $rv['color'] ?>"><?= $rv['label'] ?></span>
    <span class="text-xs text-muted"><?= $rv['desc'] ?></span>
</div>
<?php endforeach; ?>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead><tr><th>Avatar</th><th>Nama</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Login Terakhir</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($all_users as $u): ?>
            <tr>
                <td>
                    <?php if ($u['avatar']): ?>
                    <img src="<?= uploads_url($u['avatar']) ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid var(--border)">
                    <?php else: ?>
                    <div style="width:36px;height:36px;border-radius:50%;background:var(--surface3);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--accent)">
                        <?= strtoupper(substr($u['nama'],0,1)) ?>
                    </div>
                    <?php endif; ?>
                </td>
                <td style="font-weight:500"><?= htmlspecialchars($u['nama']) ?>
                    <?php if ($u['id'] == $user['id']): ?><span class="badge badge-accent" style="margin-left:6px">Saya</span><?php endif; ?>
                </td>
                <td class="text-muted">@<?= htmlspecialchars($u['username']) ?></td>
                <td class="text-muted text-sm"><?= htmlspecialchars($u['email']) ?></td>
                <td><span class="badge <?= $roles_info[$u['role']]['color'] ?? 'badge-muted' ?>"><?= $roles_info[$u['role']]['label'] ?? $u['role'] ?></span></td>
                <td>
                    <?php if ($u['is_active']): ?><span class="badge badge-success">Aktif</span>
                    <?php else: ?><span class="badge badge-danger">Nonaktif</span><?php endif; ?>
                </td>
                <td class="text-xs text-muted"><?= $u['last_login'] ? time_ago($u['last_login']) : 'Belum pernah' ?></td>
                <td>
                    <div style="display:flex;gap:6px">
                        <a href="<?= admin_url('?page=users&action=edit&id='.$u['id']) ?>" class="btn btn-xs btn-secondary">✏️</a>
                        <?php if ($u['id'] != $user['id']): ?>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                            <input type="hidden" name="_action" value="toggle_active">
                            <input type="hidden" name="tid" value="<?= $u['id'] ?>">
                            <button type="submit" class="btn btn-xs <?= $u['is_active']?'btn-warning':'btn-success' ?>"
                                    title="<?= $u['is_active']?'Nonaktifkan':'Aktifkan' ?>">
                                <?= $u['is_active'] ? '🔒' : '🔓' ?>
                            </button>
                        </form>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                            <input type="hidden" name="_action" value="delete">
                            <input type="hidden" name="del_id" value="<?= $u['id'] ?>">
                            <button type="submit" class="btn btn-xs btn-danger"
                                    data-confirm="Hapus pengguna '<?= htmlspecialchars(addslashes($u['nama'])) ?>'?">🗑️</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
