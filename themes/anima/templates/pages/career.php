<?php
/**
 * Anima — Career listing (route: /career). Jobs from `career` with role/location filters + search.
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$Q  = function (string $sql, array $p = []) use ($db) { try { return $db ? $db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };

$role = isset($_GET['role']) ? trim((string) $_GET['role']) : '';
$loc  = isset($_GET['lokasi']) ? trim((string) $_GET['lokasi']) : '';
$q    = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

$where = "is_active=1 AND (deadline IS NULL OR deadline >= CURDATE())"; $args = [];
if ($role !== '') { $where .= " AND role = ?"; $args[] = $role; }
if ($loc !== '')  { $where .= " AND lokasi = ?"; $args[] = $loc; }
if ($q !== '')    { $where .= " AND (judul LIKE ? OR deskripsi LIKE ? OR role LIKE ?)"; $args[] = "%$q%"; $args[] = "%$q%"; $args[] = "%$q%"; }

$jobs  = $Q("SELECT * FROM career WHERE $where ORDER BY urutan, created_at DESC", $args);
$roles = $Q("SELECT role, COUNT(*) c FROM career WHERE is_active=1 AND role IS NOT NULL AND role<>'' GROUP BY role ORDER BY role");
$locs  = $Q("SELECT lokasi, COUNT(*) c FROM career WHERE is_active=1 AND lokasi IS NOT NULL AND lokasi<>'' GROUP BY lokasi ORDER BY lokasi");

$fmt = fn($d) => $d ? date('M j, Y', strtotime($d)) : '';
$seo = ['title' => get_content('career', 'seo_title', 'Career') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => get_content('career', 'seo_desc', 'Bergabunglah dengan tim Sapta Tunas Teknologi.')];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body"><div class="page-shell">

  <div class="page-hero">
    <div class="eyebrow"><?= htmlspecialchars(get_content('career', 'eyebrow', 'Join Our Team')) ?></div>
    <h1><?= htmlspecialchars(get_content('career', 'title', 'Build the Future with Us')) ?></h1>
    <p><?= htmlspecialchars(get_content('career', 'lead', 'Kami mencari orang-orang hebat untuk tumbuh bersama. Temukan peran yang cocok untuk Anda.')) ?></p>
  </div>

  <form class="cr-search" method="get" action="<?= url('career') ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="<?= htmlspecialchars(t('search_jobs', 'Cari posisi...')) ?>">
    <?php if ($role !== ''): ?><input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>"><?php endif; ?>
    <?php if ($loc !== ''): ?><input type="hidden" name="lokasi" value="<?= htmlspecialchars($loc) ?>"><?php endif; ?>
    <button type="submit" class="btn btn-primary"><?= htmlspecialchars(t('search', 'Cari')) ?></button>
  </form>

  <div class="cr-layout">
    <aside class="cr-side">
      <div class="cr-filter">
        <h4><?= htmlspecialchars(t('job_role', 'Job Role')) ?></h4>
        <ul>
          <li><a href="<?= url('career' . ($q !== '' ? '?q=' . urlencode($q) : '')) ?>" class="<?= $role === '' ? 'on' : '' ?>"><?= htmlspecialchars(t('all', 'Semua')) ?></a></li>
          <?php foreach ($roles as $r): $qs = http_build_query(array_filter(['role' => $r['role'], 'q' => $q])); ?>
          <li><a href="<?= url('career?' . $qs) ?>" class="<?= $role === $r['role'] ? 'on' : '' ?>"><?= htmlspecialchars($r['role']) ?> <span>(<?= (int)$r['c'] ?>)</span></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="cr-filter">
        <h4><?= htmlspecialchars(t('location', 'Location')) ?></h4>
        <ul>
          <li><a href="<?= url('career' . ($q !== '' ? '?q=' . urlencode($q) : '')) ?>" class="<?= $loc === '' ? 'on' : '' ?>"><?= htmlspecialchars(t('all', 'Semua')) ?></a></li>
          <?php foreach ($locs as $l): $qs = http_build_query(array_filter(['lokasi' => $l['lokasi'], 'q' => $q])); ?>
          <li><a href="<?= url('career?' . $qs) ?>" class="<?= $loc === $l['lokasi'] ? 'on' : '' ?>"><?= htmlspecialchars($l['lokasi']) ?> <span>(<?= (int)$l['c'] ?>)</span></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </aside>

    <div class="cr-list">
      <?php if (!$jobs): ?>
        <p class="bl-empty">Belum ada lowongan yang cocok. Coba ubah filter.</p>
      <?php endif; ?>
      <?php foreach ($jobs as $j): ?>
      <a class="cr-card" href="<?= url('career/' . $j['slug']) ?>">
        <div class="cr-card-head">
          <h3><?= htmlspecialchars($j['judul']) ?></h3>
          <?php if (!empty($j['role'])): ?><span class="cr-chip"><?= htmlspecialchars($j['role']) ?></span><?php endif; ?>
        </div>
        <?php if (!empty($j['deskripsi'])): ?><p class="cr-desc"><?= htmlspecialchars($j['deskripsi']) ?></p><?php endif; ?>
        <div class="cr-meta">
          <?php if (!empty($j['lokasi'])): ?><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0116 0z"/><circle cx="12" cy="10" r="3"/></svg> <?= htmlspecialchars($j['lokasi']) ?></span><?php endif; ?>
          <?php if (!empty($j['jenjang'])): ?><span><?= icon('star', 15) ?> <?= htmlspecialchars($j['jenjang']) ?></span><?php endif; ?>
          <?php if (!empty($j['pengalaman'])): ?><span><?= icon('briefcase', 15) ?> <?= htmlspecialchars($j['pengalaman']) ?></span><?php endif; ?>
          <?php if (!empty($j['tipe'])): ?><span><?= icon('clock', 15) ?> <?= htmlspecialchars($j['tipe']) ?></span><?php endif; ?>
        </div>
        <div class="cr-card-foot">
          <?php if (!empty($j['deadline'])): ?><span class="cr-deadline">Sampai <?= htmlspecialchars($fmt($j['deadline'])) ?></span><?php endif; ?>
          <span class="cr-more"><?= htmlspecialchars(t('view_detail', 'Lihat detail')) ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
