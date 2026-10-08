<?php
/**
 * Admin — Job applications (module: career-lamaran). Submissions from the /career apply form.
 */
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$job = (int)($_GET['job'] ?? 0);
$fstatus = trim((string)($_GET['status'] ?? ''));
$STATUSES = ['baru' => 'Baru', 'review' => 'Review', 'interview' => 'Interview', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error', 'Token tidak valid.'); redirect(admin_url('?page=career-lamaran')); }
    $act = $_POST['action'] ?? '';
    if ($act === 'set_status' && $id > 0) {
        $st = $_POST['status'] ?? 'baru';
        if (isset($STATUSES[$st])) { $db->execute("UPDATE job_applications SET status=? WHERE id=?", [$st, $id]); set_flash('success', 'Status diperbarui.'); }
        redirect(admin_url('?page=career-lamaran&action=view&id=' . $id));
    }
    if ($act === 'delete' && $id > 0) {
        $row = $db->fetchOne("SELECT cv_file FROM job_applications WHERE id=?", [$id]);
        if ($row && !empty($row['cv_file'])) { $p = UPLOADS_PATH . '/' . $row['cv_file']; if (is_file($p)) @unlink($p); }
        $db->execute("DELETE FROM job_applications WHERE id=?", [$id]);
        set_flash('success', 'Lamaran dihapus.');
        redirect(admin_url('?page=career-lamaran'));
    }
}

$fmt = fn($d) => $d ? date('d M Y H:i', strtotime($d)) : '';
$badge = fn($s) => '<span class="badge badge-' . ($s === 'diterima' ? 'success' : ($s === 'ditolak' ? 'danger' : ($s === 'baru' ? 'info' : 'gray'))) . '">' . htmlspecialchars($STATUSES[$s] ?? $s) . '</span>';
$csrf = generate_csrf();
$jobs = $db->fetchAll("SELECT id, judul FROM career ORDER BY urutan, judul");
?>

<?php if ($action === 'view' && $id > 0): $a = $db->fetchOne("SELECT * FROM job_applications WHERE id=?", [$id]); ?>
  <?php if (!$a): ?>
    <div class="card"><div class="card-body">Lamaran tidak ditemukan. <a href="<?= admin_url('?page=career-lamaran') ?>">Kembali</a>.</div></div>
  <?php else: ?>
  <div class="page-header"><div>
    <h1><?= icon('users', 18) ?> Detail Lamaran</h1>
    <div class="page-header-sub"><?= htmlspecialchars($a['posisi'] ?? '') ?></div>
  </div>
  <div class="page-actions"><a href="<?= admin_url('?page=career-lamaran') ?>" class="btn btn-secondary btn-sm"><?= icon('arrow-left', 15) ?> Semua Lamaran</a></div></div>

  <div class="card"><div class="card-body">
    <div class="form-row">
      <div class="form-group"><label>Nama</label><div><?= htmlspecialchars($a['nama']) ?></div></div>
      <div class="form-group"><label>Status</label><div><?= $badge($a['status']) ?></div></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Email</label><div><a href="mailto:<?= htmlspecialchars($a['email']) ?>"><?= htmlspecialchars($a['email']) ?></a></div></div>
      <div class="form-group"><label>Telepon</label><div><?= htmlspecialchars($a['telepon'] ?: '—') ?></div></div>
    </div>
    <div class="form-group"><label>Subject</label><div><?= htmlspecialchars($a['subject'] ?: '—') ?></div></div>
    <div class="form-group"><label>Cover Letter</label><div style="white-space:pre-wrap;color:var(--text-muted)"><?= htmlspecialchars($a['cover_letter'] ?: '—') ?></div></div>
    <div class="form-group"><label>CV</label><div>
      <?php if (!empty($a['cv_file'])): ?>
        <a href="<?= htmlspecialchars(uploads_url($a['cv_file'])) ?>" target="_blank" class="btn btn-secondary btn-sm"><?= icon('download', 15) ?> Unduh CV</a>
      <?php else: ?>—<?php endif; ?>
    </div></div>
    <div class="form-group"><label>Dikirim</label><div style="color:var(--text-muted)"><?= htmlspecialchars($fmt($a['created_at'])) ?> · IP <?= htmlspecialchars($a['ip'] ?: '-') ?></div></div>

    <form method="POST" action="<?= admin_url('?page=career-lamaran&action=set_status&id=' . $a['id']) ?>" style="display:flex;gap:10px;align-items:flex-end;border-top:1px solid var(--border);padding-top:16px;margin-top:8px">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>"><input type="hidden" name="action" value="set_status">
      <div class="form-group" style="margin:0"><label>Ubah Status</label>
        <select name="status" class="form-control">
          <?php foreach ($STATUSES as $k => $v): ?><option value="<?= $k ?>" <?= $a['status'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?>
        </select></div>
      <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Simpan</button>
      <form method="POST" action="<?= admin_url('?page=career-lamaran&action=delete&id=' . $a['id']) ?>" onsubmit="return confirm('Hapus lamaran ini beserta CV-nya?')" style="margin-left:auto">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>"><input type="hidden" name="action" value="delete">
        <button type="submit" class="btn btn-danger"><?= icon('trash', 15) ?> Hapus</button>
      </form>
    </form>
  </div></div>
  <?php endif; ?>

<?php else:
  $where = "1=1"; $args = [];
  if ($job > 0)      { $where .= " AND a.career_id = ?"; $args[] = $job; }
  if ($fstatus !== '' && isset($STATUSES[$fstatus])) { $where .= " AND a.status = ?"; $args[] = $fstatus; }
  $rows = $db->fetchAll("SELECT a.* FROM job_applications a WHERE $where ORDER BY a.created_at DESC", $args);
?>
  <div class="page-header"><div>
    <h1><?= icon('users', 18) ?> Lamaran Masuk</h1>
    <div class="page-header-sub"><?= count($rows) ?> lamaran<?= $job > 0 ? ' untuk posisi terpilih' : '' ?>.</div>
  </div>
  <div class="page-actions"><a href="<?= admin_url('?page=career') ?>" class="btn btn-secondary btn-sm"><?= icon('briefcase', 15) ?> Kelola Lowongan</a></div></div>

  <div class="card" style="margin-bottom:16px"><div class="card-body" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center">
    <span style="font-size:12px;color:var(--text-muted);font-weight:600">FILTER</span>
    <a href="<?= admin_url('?page=career-lamaran') ?>" class="btn btn-sm <?= $job === 0 && $fstatus === '' ? 'btn-primary' : 'btn-secondary' ?>">Semua</a>
    <?php foreach ($STATUSES as $k => $v): ?>
      <a href="<?= admin_url('?page=career-lamaran&status=' . $k . ($job ? '&job=' . $job : '')) ?>" class="btn btn-sm <?= $fstatus === $k ? 'btn-primary' : 'btn-secondary' ?>"><?= $v ?></a>
    <?php endforeach; ?>
    <select class="form-control" style="max-width:260px;margin-left:auto" onchange="if(this.value)location.href=this.value">
      <option value="<?= admin_url('?page=career-lamaran' . ($fstatus ? '&status=' . $fstatus : '')) ?>">Semua posisi</option>
      <?php foreach ($jobs as $j): ?>
        <option value="<?= admin_url('?page=career-lamaran&job=' . $j['id'] . ($fstatus ? '&status=' . $fstatus : '')) ?>" <?= $job === (int)$j['id'] ? 'selected' : '' ?>><?= htmlspecialchars($j['judul']) ?></option>
      <?php endforeach; ?>
    </select>
  </div></div>

  <?php if (empty($rows)): ?>
    <div class="card"><div class="empty-state"><div class="empty-state-icon"><?= icon('users', 40) ?></div><div>Belum ada lamaran.</div></div></div>
  <?php else: ?>
  <div class="table-wrap"><table>
    <thead><tr><th>Pelamar</th><th>Posisi</th><th style="width:160px">Dikirim</th><th style="width:90px">CV</th><th style="width:90px">Status</th><th style="width:90px">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $a): ?>
      <tr>
        <td><div style="font-weight:600"><?= htmlspecialchars($a['nama']) ?></div>
          <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars($a['email']) ?><?= !empty($a['telepon']) ? ' • ' . htmlspecialchars($a['telepon']) : '' ?></div></td>
        <td style="font-size:12px"><?= htmlspecialchars($a['posisi'] ?? '') ?></td>
        <td style="font-size:12px"><?= htmlspecialchars($fmt($a['created_at'])) ?></td>
        <td><?php if (!empty($a['cv_file'])): ?><a href="<?= htmlspecialchars(uploads_url($a['cv_file'])) ?>" target="_blank" class="btn btn-secondary btn-sm" title="Unduh CV"><?= icon('download', 15) ?></a><?php else: ?>—<?php endif; ?></td>
        <td><?= $badge($a['status']) ?></td>
        <td><a href="<?= admin_url('?page=career-lamaran&action=view&id=' . $a['id']) ?>" class="btn btn-secondary btn-sm">Detail</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
<?php endif; ?>
