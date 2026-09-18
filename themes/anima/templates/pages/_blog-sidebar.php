<?php
/**
 * Anima — shared What's New sidebar (used by /blog listing and /blog/<slug> detail).
 * Self-contained: queries its own recent/years/months. Figma: (optional) category pills,
 * "New Information" recent list, and "Publishing Year" archive with month counts.
 * Set $blog_sidebar_cats = true before include to show the category pills (detail page).
 */
$__db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$__Q  = function (string $sql, array $p = []) use ($__db) { try { return $__db ? $__db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };

$s_kat  = isset($_GET['kat'])  ? trim((string) $_GET['kat'])  : '';
$s_year = isset($_GET['year']) ? (int) $_GET['year']          : 0;
$s_mon  = isset($_GET['mon'])  ? (int) $_GET['mon']           : 0;
$s_fmt  = fn($d) => $d ? date('F j, Y', strtotime($d)) : '';

$s_cats   = $__Q("SELECT nama, slug FROM blog_kategori ORDER BY urutan, nama");
$s_recent = $__Q("SELECT judul, slug, created_at FROM blog WHERE status='published' ORDER BY created_at DESC LIMIT 5");
$s_years  = $__Q("SELECT YEAR(created_at) y, COUNT(*) c FROM blog WHERE status='published' GROUP BY y ORDER BY y DESC");
$s_active = $s_year > 0 ? $s_year : (int)($s_years[0]['y'] ?? (int)date('Y'));
$s_months = $__Q("SELECT MONTH(created_at) m, COUNT(*) c FROM blog WHERE status='published' AND YEAR(created_at)=? GROUP BY m ORDER BY m", [$s_active]);
$s_MONTHS = ['', 'January','February','March','April','May','June','July','August','September','October','November','December'];
$s_showcats = !empty($blog_sidebar_cats);
?>
<aside class="bl-side">

  <?php if ($s_showcats): ?>
  <div class="bl-side-cats">
    <a class="bl-tab<?= $s_kat === '' ? ' on' : '' ?>" href="<?= url('blog') ?>"><?= ac('blog','all_label') ?></a>
    <?php foreach ($s_cats as $c): ?>
      <a class="bl-tab<?= $s_kat === $c['slug'] ? ' on' : '' ?>" href="<?= url('blog?kat=' . urlencode($c['slug'])) ?>"><?= htmlspecialchars($c['nama']) ?></a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="bl-side-box">
    <h4><?= ac('blog','recent_title') ?></h4>
    <ul class="bl-recent">
      <?php foreach ($s_recent as $r): ?>
      <li><a href="<?= url('blog/' . $r['slug']) ?>"><?= htmlspecialchars($r['judul']) ?></a>
        <span><?= htmlspecialchars($s_fmt($r['created_at'])) ?></span></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="bl-side-box bl-pub">
    <h4><?= ac('blog','years_title') ?></h4>
    <div class="bl-yearsel">
      <select data-nav aria-label="Publishing Year">
        <option value="<?= htmlspecialchars(url('blog')) ?>"<?= $s_year === 0 ? ' selected' : '' ?>>Any</option>
        <?php foreach ($s_years as $y): $yy = (int)$y['y']; ?>
          <option value="<?= htmlspecialchars(url('blog?year=' . $yy)) ?>"<?= $s_year === $yy ? ' selected' : '' ?>><?= $yy ?> (<?= (int)$y['c'] ?>)</option>
        <?php endforeach; ?>
      </select>
      <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
    </div>
    <div class="bl-arch">
      <?php foreach ($s_years as $y): $yy = (int)$y['y']; ?>
        <a class="bl-arch-y<?= $s_year === $yy ? ' on' : '' ?>" href="<?= url('blog?year=' . $yy) ?>"><?= $yy ?> <span>(<?= (int)$y['c'] ?>)</span></a>
        <?php if ($yy === $s_active && $s_months): ?>
          <div class="bl-arch-m">
            <?php foreach ($s_months as $m): $mm = (int)$m['m']; ?>
              <a class="<?= $s_mon === $mm && $s_year === $yy ? 'on' : '' ?>" href="<?= url('blog?year=' . $yy . '&mon=' . $mm) ?>"><?= $s_MONTHS[$mm] ?? '' ?> <span>(<?= (int)$m['c'] ?>)</span></a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>

</aside>
