<?php
/**
 * Anima theme — What's New / Blog listing (route: /blog). Wired to the CMS `blog` module.
 * Figma "What's New": featured article (image bg), search, category tabs, article grid,
 * sidebar (New Information recent + Publishing Year archive with month counts). Shares header/footer.
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$Q = function (string $sql, array $p = []) use ($db) { try { return $db ? $db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };

$kat  = isset($_GET['kat'])  ? trim((string) $_GET['kat'])  : '';
$q    = isset($_GET['q'])    ? trim((string) $_GET['q'])    : '';
$year = isset($_GET['year']) ? (int) $_GET['year']          : 0;
$mon  = isset($_GET['mon'])  ? (int) $_GET['mon']           : 0;

// Build a query string that preserves the other active filters (so links combine).
$qs = function (array $override = []) use ($kat, $q, $year, $mon) {
    $p = ['kat' => $kat, 'q' => $q, 'year' => $year ?: '', 'mon' => $mon ?: ''];
    $p = array_merge($p, $override);
    $p = array_filter($p, fn($v) => $v !== '' && $v !== null && $v !== 0);
    return $p ? 'blog?' . http_build_query($p) : 'blog';
};

$catExpr = "(SELECT bk.nama FROM blog_kategori_rel r JOIN blog_kategori bk ON bk.id=r.kategori_id WHERE r.blog_id=b.id LIMIT 1)";
$where = "b.status='published'"; $args = [];
if ($kat !== '') { $where .= " AND EXISTS (SELECT 1 FROM blog_kategori_rel r JOIN blog_kategori bk ON bk.id=r.kategori_id WHERE r.blog_id=b.id AND bk.slug=?)"; $args[] = $kat; }
if ($q !== '')   { $where .= " AND (b.judul LIKE ? OR b.excerpt LIKE ? OR b.konten LIKE ?)"; $args[] = "%$q%"; $args[] = "%$q%"; $args[] = "%$q%"; }
if ($year > 0)   { $where .= " AND YEAR(b.created_at) = ?"; $args[] = $year; }
if ($mon > 0)    { $where .= " AND MONTH(b.created_at) = ?"; $args[] = $mon; }

$featured = $Q("SELECT b.*, $catExpr AS kategori FROM blog b WHERE b.status='published' ORDER BY b.created_at DESC LIMIT 1");
$featured = $featured[0] ?? null;
$fid = (int)($featured['id'] ?? 0);

// Grid excludes the featured article (it headlines above), unless a filter is active.
$filtering = ($kat !== '' || $q !== '' || $year > 0 || $mon > 0);
$gridWhere = $where . ($filtering ? '' : ($fid ? " AND b.id <> " . $fid : ''));
$posts = $Q("SELECT b.*, $catExpr AS kategori FROM blog b WHERE $gridWhere ORDER BY b.created_at DESC", $args);

$cats   = $Q("SELECT nama, slug FROM blog_kategori ORDER BY urutan, nama");
$recent = $Q("SELECT judul, slug, created_at FROM blog WHERE status='published' ORDER BY created_at DESC LIMIT 5");
$years  = $Q("SELECT YEAR(created_at) y, COUNT(*) c FROM blog WHERE status='published' GROUP BY y ORDER BY y DESC");
$active_year = $year > 0 ? $year : (int)($years[0]['y'] ?? (int)date('Y'));
$months = $Q("SELECT MONTH(created_at) m, COUNT(*) c FROM blog WHERE status='published' AND YEAR(created_at)=? GROUP BY m ORDER BY m", [$active_year]);
$MONTHS = ['', 'January','February','March','April','May','June','July','August','September','October','November','December'];

$fmt = fn($d) => $d ? date('F j, Y', strtotime($d)) : '';
$img = fn($p) => $p ? (preg_match('#^(https?:|/|data:)#', $p) ? $p : uploads_url($p)) : '';
$seo = ['title' => "What's New — " . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => 'Berita, artikel, dan update terbaru dari Sapta Tunas Teknologi.'];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body"><div class="page-shell">

  <div class="page-hero">
    <div class="eyebrow"><?= ac('blog', 'hero_eyebrow') ?></div>
    <h1><?= ac('blog', 'hero_title') ?></h1>
    <p><?= ac('blog', 'hero_sub') ?></p>
  </div>

  <?php if ($featured): ?>
  <a class="bl-featured" href="<?= url('blog/' . $featured['slug']) ?>">
    <?php if (!empty($featured['gambar_utama'])): ?>
      <img class="bl-featured-img" src="<?= htmlspecialchars($img($featured['gambar_utama'])) ?>" alt="" data-fallback="remove">
    <?php else: ?>
      <span class="bl-featured-img phb" aria-hidden="true"></span>
    <?php endif; ?>
    <div class="bl-featured-body">
      <span class="bl-date"><?= htmlspecialchars($fmt($featured['created_at'])) ?></span>
      <h2><?= htmlspecialchars($featured['judul']) ?></h2>
      <p><?= htmlspecialchars(strip_tags($featured['excerpt'] ?? '')) ?></p>
      <span class="btn btn-primary bl-featured-btn">Read More
        <svg class="ic" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
    </div>
  </a>
  <?php endif; ?>

  <form class="bl-search" method="get" action="<?= url('blog') ?>">
    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="<?= ac('blog','search_ph') ?>">
    <?php if ($kat !== ''): ?><input type="hidden" name="kat" value="<?= htmlspecialchars($kat) ?>"><?php endif; ?>
    <button type="submit" class="bl-search-btn" aria-label="Search">
      <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg></button>
  </form>

  <div class="bl-tabs">
    <a class="bl-tab<?= $kat === '' ? ' on' : '' ?>" href="<?= url($qs(['kat' => null])) ?>"><?= ac('blog','all_label') ?></a>
    <?php foreach ($cats as $c): ?>
      <a class="bl-tab<?= $kat === $c['slug'] ? ' on' : '' ?>" href="<?= url($qs(['kat' => $c['slug']])) ?>"><?= htmlspecialchars($c['nama']) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="bl-layout">
    <div class="bl-grid">
      <?php if (!$posts): ?>
        <p class="bl-empty"><?= ac('blog','empty_text') ?></p>
      <?php endif; ?>
      <?php foreach ($posts as $p): ?>
      <article class="bl-card">
        <a class="bl-card-img phb" href="<?= url('blog/' . $p['slug']) ?>">
          <?php if (!empty($p['gambar_utama'])): ?>
            <img src="<?= htmlspecialchars($img($p['gambar_utama'])) ?>" data-fallback="bg" alt="" loading="lazy">
          <?php endif; ?>
          <div class="bl-ribbon"><span class="d"><?= htmlspecialchars($fmt($p['created_at'])) ?></span>
            <?php if (!empty($p['kategori'])): ?><span class="c"><?= htmlspecialchars($p['kategori']) ?></span><?php endif; ?></div>
        </a>
        <div class="bl-card-body">
          <h3><a href="<?= url('blog/' . $p['slug']) ?>"><?= htmlspecialchars($p['judul']) ?></a></h3>
          <p><?= htmlspecialchars(strip_tags($p['excerpt'] ?? '')) ?></p>
          <a class="bl-read" href="<?= url('blog/' . $p['slug']) ?>">Read More
            <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <?php include theme_path('templates/pages/_blog-sidebar.php'); ?>
  </div>

</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
