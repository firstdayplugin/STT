<?php
/**
 * Anima theme — What's New / Blog listing (route: /blog). Wired to the CMS `blog` module.
 * Built from the Figma "What's New" design: featured article, search, category tabs, article grid,
 * sidebar (New Information recent + Publishing Year archive). Shares header/footer.
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$Q = function (string $sql, array $p = []) use ($db) { try { return $db ? $db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };

$kat = isset($_GET['kat']) ? trim((string) $_GET['kat']) : '';
$q   = isset($_GET['q'])   ? trim((string) $_GET['q'])   : '';

$catExpr = "(SELECT bk.nama FROM blog_kategori_rel r JOIN blog_kategori bk ON bk.id=r.kategori_id WHERE r.blog_id=b.id LIMIT 1)";
$where = "b.status='published'"; $args = [];
if ($kat !== '') { $where .= " AND EXISTS (SELECT 1 FROM blog_kategori_rel r JOIN blog_kategori bk ON bk.id=r.kategori_id WHERE r.blog_id=b.id AND bk.slug=?)"; $args[] = $kat; }
if ($q !== '')   { $where .= " AND (b.judul LIKE ? OR b.excerpt LIKE ?)"; $args[] = "%$q%"; $args[] = "%$q%"; }

$posts    = $Q("SELECT b.*, $catExpr AS kategori FROM blog b WHERE $where ORDER BY b.created_at DESC", $args);
$featured = $Q("SELECT b.*, $catExpr AS kategori FROM blog b WHERE b.status='published' ORDER BY b.created_at DESC LIMIT 1");
$featured = $featured[0] ?? null;
$cats     = $Q("SELECT nama, slug FROM blog_kategori ORDER BY urutan, nama");
$recent   = $Q("SELECT judul, slug, created_at FROM blog WHERE status='published' ORDER BY created_at DESC LIMIT 5");
$years    = $Q("SELECT YEAR(created_at) y, COUNT(*) c FROM blog WHERE status='published' GROUP BY y ORDER BY y DESC");

$fmt = fn($d) => $d ? date('F j, Y', strtotime($d)) : '';
$seo = ['title' => "What's New — " . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => 'Berita, artikel, dan update terbaru dari Sapta Tunas Teknologi.'];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body"><div class="page-shell">

  <div class="page-hero">
    <div class="eyebrow">What's New</div>
    <h1>What's New</h1>
    <p>Your main source to stay up-to-date with the latest trends, innovations and updates in various fields.</p>
  </div>

  <?php if ($featured): ?>
  <a class="bl-featured" href="<?= url('blog/' . $featured['slug']) ?>">
    <div class="bl-featured-body">
      <span class="bl-date"><?= htmlspecialchars($fmt($featured['created_at'])) ?></span>
      <h2><?= htmlspecialchars($featured['judul']) ?></h2>
      <p><?= htmlspecialchars($featured['excerpt'] ?? '') ?></p>
      <span class="btn btn-primary">Read More
        <svg class="ic" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
    </div>
  </a>
  <?php endif; ?>

  <form class="bl-search" method="get" action="<?= url('blog') ?>">
    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search by keywords">
  </form>

  <div class="bl-tabs">
    <a class="bl-tab<?= $kat === '' ? ' on' : '' ?>" href="<?= url('blog') ?>">All</a>
    <?php foreach ($cats as $c): ?>
      <a class="bl-tab<?= $kat === $c['slug'] ? ' on' : '' ?>" href="<?= url('blog?kat=' . urlencode($c['slug'])) ?>"><?= htmlspecialchars($c['nama']) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="bl-layout">
    <div class="bl-grid">
      <?php if (!$posts): ?>
        <p class="bl-empty">Belum ada artikel.</p>
      <?php endif; ?>
      <?php foreach ($posts as $p): ?>
      <article class="bl-card">
        <a class="bl-card-img" href="<?= url('blog/' . $p['slug']) ?>">
          <?php if (!empty($p['gambar_utama'])): ?>
            <img src="<?= htmlspecialchars(uploads_url($p['gambar_utama'])) ?>" data-fallback="bg" alt="" loading="lazy">
          <?php endif; ?>
          <div class="bl-ribbon"><span class="d"><?= htmlspecialchars($fmt($p['created_at'])) ?></span>
            <?php if (!empty($p['kategori'])): ?><span class="c"><?= htmlspecialchars($p['kategori']) ?></span><?php endif; ?></div>
        </a>
        <div class="bl-card-body">
          <h3><a href="<?= url('blog/' . $p['slug']) ?>"><?= htmlspecialchars($p['judul']) ?></a></h3>
          <p><?= htmlspecialchars($p['excerpt'] ?? '') ?></p>
          <a class="bl-read" href="<?= url('blog/' . $p['slug']) ?>">Read More
            <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <aside class="bl-side">
      <div class="bl-side-box">
        <h4>New Information</h4>
        <ul class="bl-recent">
          <?php foreach ($recent as $r): ?>
          <li><a href="<?= url('blog/' . $r['slug']) ?>"><?= htmlspecialchars($r['judul']) ?></a>
            <span><?= htmlspecialchars($fmt($r['created_at'])) ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="bl-side-box">
        <h4>Publishing Year</h4>
        <ul class="bl-years">
          <?php foreach ($years as $y): ?>
          <li><a href="<?= url('blog') ?>"><span><?= (int) $y['y'] ?></span> <span class="n">(<?= (int) $y['c'] ?>)</span></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </aside>
  </div>

</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
