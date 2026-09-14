<?php
/**
 * Anima — Career listing (route: /career). Figma "Career". Jobs from `career` with
 * role/location checkbox filters + keyword search. Hero copy + team photo are CMS-editable.
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$Q  = function (string $sql, array $p = []) use ($db) { try { return $db ? $db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };

$role = isset($_GET['role'])   ? trim((string) $_GET['role']) : '';
$loc  = isset($_GET['lokasi']) ? trim((string) $_GET['lokasi']) : '';
$q    = isset($_GET['q'])      ? trim((string) $_GET['q']) : '';

$where = "is_active=1 AND (deadline IS NULL OR deadline >= CURDATE())"; $args = [];
if ($role !== '') { $where .= " AND role = ?"; $args[] = $role; }
if ($loc !== '')  { $where .= " AND lokasi = ?"; $args[] = $loc; }
if ($q !== '')    { $where .= " AND (judul LIKE ? OR deskripsi LIKE ? OR role LIKE ?)"; $args[] = "%$q%"; $args[] = "%$q%"; $args[] = "%$q%"; }

$jobs  = $Q("SELECT * FROM career WHERE $where ORDER BY urutan, created_at DESC", $args);
$roles = $Q("SELECT role, COUNT(*) c FROM career WHERE is_active=1 AND role IS NOT NULL AND role<>'' GROUP BY role ORDER BY role");
$locs  = $Q("SELECT lokasi, COUNT(*) c FROM career WHERE is_active=1 AND lokasi IS NOT NULL AND lokasi<>'' GROUP BY lokasi ORDER BY lokasi");

$c   = fn(string $k, string $d = '') => get_content('career', $k, $d);
$imgu = fn($p) => $p ? (preg_match('#^(https?:|/|data:)#', $p) ? $p : uploads_url($p)) : '';
$fmt = fn($d) => $d ? date('M j, Y', strtotime($d)) : '';
$lead_default = "Be part of a team that delivers innovative technology solutions to the challenges of modern businesses. At Sapta Tunas Teknologi, you'll work in a collaborative, learning-driven environment with real opportunities for personal and professional growth. We encourage every team member to continuously enhance their skills, explore new ideas, and take an active role in creating innovations that make a meaningful impact. Together, we build the future through technology, creativity, and a shared passion for continuous growth.";
$seo = ['title' => 'Career — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($c('lead', $lead_default)), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
$team = $imgu($c('team_image', 'career/team.jpg'));
?>
<main class="page-body"><div class="cr-wrap">

  <header class="cr-head">
    <h1><?= htmlspecialchars($c('title', 'Build the future with Us')) ?><br><span class="blue"><?= htmlspecialchars($c('title2', 'Grow your career at Sapta Tunas Teknologi')) ?></span></h1>
    <p><?= $c('lead', $lead_default) ?></p>
  </header>

  <?php if ($team): ?><div class="cr-team"><img src="<?= htmlspecialchars($team) ?>" alt="" data-fallback="remove"></div><?php endif; ?>

  <form class="cr-search" method="get" action="<?= url('career') ?>">
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="<?= htmlspecialchars(t('search_jobs', 'Search by keywords')) ?>">
    <?php if ($role !== ''): ?><input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>"><?php endif; ?>
    <?php if ($loc !== ''): ?><input type="hidden" name="lokasi" value="<?= htmlspecialchars($loc) ?>"><?php endif; ?>
    <button type="submit" class="cr-search-btn" aria-label="Search">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg></button>
  </form>

  <div class="cr-layout">
    <?php $career_active_role = $role; $career_active_loc = $loc; $career_q = $q;
          include theme_path('templates/pages/_career-filters.php'); ?>

    <div class="cr-list">
      <?php if (!$jobs): ?>
        <p class="bl-empty"><?= htmlspecialchars(t('no_jobs', 'Belum ada lowongan yang cocok. Coba ubah filter.')) ?></p>
      <?php endif; ?>
      <?php foreach ($jobs as $j):
        $sub = array_filter([tr_field('career', (int)$j['id'], 'jenjang', $j['jenjang'] ?? ''), $j['pengalaman'] ?? '']);
      ?>
      <a class="crj-card" href="<?= url('career/' . $j['slug']) ?>">
        <div class="crj-main">
          <h3><?= htmlspecialchars(tr_field('career', (int)$j['id'], 'judul', $j['judul'])) ?></h3>
          <?php if ($sub): ?><div class="crj-sub"><?= htmlspecialchars(implode('  |  ', $sub)) ?></div><?php endif; ?>
        </div>
        <div class="crj-side">
          <?php if (!empty($j['deadline'])): ?><span class="crj-until"><?= htmlspecialchars(t('until', 'until') . ' ' . $fmt($j['deadline'])) ?></span><?php endif; ?>
          <span class="btn btn-primary crj-more"><?= htmlspecialchars(t('see_more', 'See more')) ?></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
