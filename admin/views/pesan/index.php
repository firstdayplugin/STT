<?php
/**
 * Admin — Inbound messages (module: pesan). Submissions from the contact form,
 * the home "Request Proposal" form, and the footer newsletter subscribe.
 */
$db = Database::getInstance();
$action  = $_GET['action'] ?? 'list';
$id      = (int)($_GET['id'] ?? 0);
$ftipe   = trim((string)($_GET['tipe'] ?? ''));
$TYPES   = ['kontak' => 'Kontak', 'proposal' => 'Request Proposal', 'newsletter' => 'Newsletter'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error', 'Token tidak valid.'); redirect(admin_url('?page=pesan')); }
    $act = $_POST['action'] ?? '';
    if ($act === 'delete' && $id > 0) {
        $db->execute("DELETE FROM pesan WHERE id=?", [$id]);
        set_flash('success', 'Pesan dihapus.');
        redirect(admin_url('?page=pesan' . ($ftipe ? '&tipe=' . $ftipe : '')));
    }
    if ($act === 'toggle_read' && $id > 0) {
        $db->execute("UPDATE pesan SET is_read = 1 - is_read WHERE id=?", [$id]);
        redirect(admin_url('?page=pesan&action=view&id=' . $id));
    }
}

$fmt   = fn($d) => $d ? date('d M Y H:i', strtotime($d)) : '';
$badge = fn($t) => '<span class="badge badge-' . ($t === 'proposal' ? 'success' : ($t === 'newsletter' ? 'gray' : 'info')) . '">' . htmlspecialchars($TYPES[$t] ?? $t) . '</span>';
$csrf  = generate_csrf();
?>

<?php if ($action === 'view' && $id > 0):
  $m = $db->fetchOne("SELECT * FROM pesan WHERE id=?", [$id]);
  if ($m && (int)$m['is_read'] === 0) { $db->execute("UPDATE pesan SET is_read=1 WHERE id=?", [$id]); $m['is_read'] = 1; }
?>
  <?php if (!$m): ?>
    <div class="card"><div class="card-body">Pesan tidak ditemukan. <a href="<?= admin_url('?page=pesan') ?>">Kembali</a>.</div></div>
  <?php else: ?>
  <div class="page-header"><div>
    <h1><?= icon('mail', 18) ?> Detail Pesan</h1>
    <div class="page-header-sub"><?= $badge($m['tipe']) ?> · <?= htmlspecialchars($fmt($m['created_at'])) ?></div>
  </div>
  <div class="page-actions"><a href="<?= admin_url('?page=pesan') ?>" class="btn btn-secondary btn-sm"><?= icon('arrow-left', 15) ?> Semua Pesan</a></div></div>

  <div class="card"><div class="card-body">
    <div class="form-row">
      <div class="form-group"><label>Nama</label><div><?= htmlspecialchars($m['nama'] ?: '—') ?></div></div>
      <div class="form-group"><label>Email</label><div><?php if (!empty($m['email'])): ?><a href="mailto:<?= htmlspecialchars($m['email']) ?>"><?= htmlspecialchars($m['email']) ?></a><?php else: ?>—<?php endif; ?></div></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Telepon</label><div><?php if (!empty($m['telepon'])): ?><a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/','',$m['telepon'])) ?>"><?= htmlspecialchars($m['telepon']) ?></a><?php else: ?>—<?php endif; ?></div></div>
      <div class="form-group"><label>Perusahaan</label><div><?= htmlspecialchars($m['perusahaan'] ?: '—') ?></div></div>
    </div>
    <?php if (!empty($m['subjek'])): ?><div class="form-group"><label>Subjek</label><div><?= htmlspecialchars($m['subjek']) ?></div></div><?php endif; ?>
    <?php if (!empty($m['pesan'])): ?><div class="form-group"><label>Pesan</label><div style="white-space:pre-wrap;color:var(--text-muted)"><?= htmlspecialchars($m['pesan']) ?></div></div><?php endif; ?>
    <div class="form-group"><label>Diterima</label><div style="color:var(--text-muted)"><?= htmlspecialchars($fmt($m['created_at'])) ?> · Halaman <?= htmlspecialchars($m['halaman'] ?: '-') ?> · IP <?= htmlspecialchars($m['ip'] ?: '-') ?></div></div>

    <div style="display:flex;gap:10px;border-top:1px solid var(--border);padding-top:16px;margin-top:8px">
      <form method="POST" action="<?= admin_url('?page=pesan&action=toggle_read&id=' . $m['id']) ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>"><input type="hidden" name="action" value="toggle_read">
        <button type="submit" class="btn btn-secondary"><?= icon('check', 16) ?> Tandai <?= (int)$m['is_read'] === 1 ? 'belum dibaca' : 'sudah dibaca' ?></button>
      </form>
      <form method="POST" action="<?= admin_url('?page=pesan&action=delete&id=' . $m['id']) ?>" onsubmit="return confirm('Hapus pesan ini?')" style="margin-left:auto">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>"><input type="hidden" name="action" value="delete">
        <button type="submit" class="btn btn-danger"><?= icon('trash', 15) ?> Hapus</button>
      </form>
    </div>
  </div></div>
  <?php endif; ?>

<?php else:
  $where = "1=1"; $args = [];
  if ($ftipe !== '' && isset($TYPES[$ftipe])) { $where .= " AND tipe = ?"; $args[] = $ftipe; }
  $rows   = $db->fetchAll("SELECT * FROM pesan WHERE $where ORDER BY created_at DESC", $args);
  $unread = (int)($db->fetchOne("SELECT COUNT(*) c FROM pesan WHERE is_read=0")['c'] ?? 0);
?>
  <div class="page-header"><div>
    <h1><?= icon('mail', 18) ?> Pesan Masuk</h1>
    <div class="page-header-sub"><?= count($rows) ?> pesan<?= $unread > 0 ? ' · ' . $unread . ' belum dibaca' : '' ?>.</div>
  </div></div>

  <div class="card" style="margin-bottom:16px"><div class="card-body" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center">
    <span style="font-size:12px;color:var(--text-muted);font-weight:600">FILTER</span>
    <a href="<?= admin_url('?page=pesan') ?>" class="btn btn-sm <?= $ftipe === '' ? 'btn-primary' : 'btn-secondary' ?>">Semua</a>
    <?php foreach ($TYPES as $k => $v): ?>
      <a href="<?= admin_url('?page=pesan&tipe=' . $k) ?>" class="btn btn-sm <?= $ftipe === $k ? 'btn-primary' : 'btn-secondary' ?>"><?= $v ?></a>
    <?php endforeach; ?>
  </div></div>

  <?php if (empty($rows)): ?>
    <div class="card"><div class="empty-state"><div class="empty-state-icon"><?= icon('mail', 40) ?></div><div>Belum ada pesan masuk.</div></div></div>
  <?php else: ?>
  <div class="table-wrap"><table>
    <thead><tr><th style="width:44px"></th><th>Pengirim</th><th>Isi</th><th style="width:130px">Tipe</th><th style="width:150px">Diterima</th><th style="width:90px">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $m): $new = (int)$m['is_read'] === 0; ?>
      <tr<?= $new ? ' style="font-weight:600"' : '' ?>>
        <td><?php if ($new): ?><span class="badge badge-info" title="Belum dibaca">Baru</span><?php endif; ?></td>
        <td><div><?= htmlspecialchars($m['nama'] ?: ($m['email'] ?: '—')) ?></div>
          <div style="font-size:11px;color:var(--text-muted);font-weight:400"><?= htmlspecialchars($m['email'] ?: '') ?><?= !empty($m['telepon']) ? ' • ' . htmlspecialchars($m['telepon']) : '' ?></div></td>
        <td style="font-size:12px;color:var(--text-muted);font-weight:400"><?= htmlspecialchars(mb_strimwidth((string)($m['subjek'] ?: $m['pesan'] ?: ''), 0, 60, '…')) ?: '—' ?></td>
        <td><?= $badge($m['tipe']) ?></td>
        <td style="font-size:12px;font-weight:400"><?= htmlspecialchars($fmt($m['created_at'])) ?></td>
        <td><a href="<?= admin_url('?page=pesan&action=view&id=' . $m['id']) ?>" class="btn btn-secondary btn-sm">Detail</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
<?php endif; ?>
