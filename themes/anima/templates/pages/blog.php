<?php
/**
 * Anima theme — What's New / Blog listing (route: /blog). Wired to the CMS `blog` module.
 * "Editorial Spotlight": a cinematic cover (the latest article) over a dark gradient, then a
 * light editorial list — a wide lead card + a 3-up grid with scroll reveal. Category tabs,
 * search and the year filter all run CLIENT-SIDE (no reload, no jump to top). Shares header/footer.
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$Q = function (string $sql, array $p = []) use ($db) { try { return $db ? $db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };

$catName = "(SELECT bk.nama FROM blog_kategori_rel r JOIN blog_kategori bk ON bk.id=r.kategori_id WHERE r.blog_id=b.id ORDER BY bk.urutan, bk.nama LIMIT 1)";
$catSlug = "(SELECT bk.slug FROM blog_kategori_rel r JOIN blog_kategori bk ON bk.id=r.kategori_id WHERE r.blog_id=b.id ORDER BY bk.urutan, bk.nama LIMIT 1)";

$featured = $Q("SELECT b.*, $catName AS kategori, $catSlug AS kat_slug FROM blog b WHERE b.status='published' ORDER BY b.created_at DESC LIMIT 1");
$featured = $featured[0] ?? null;
$fid = (int)($featured['id'] ?? 0);

// All published articles except the featured one — filtered client-side (tabs/search/year).
$posts = $Q("SELECT b.*, $catName AS kategori, $catSlug AS kat_slug FROM blog b WHERE b.status='published'"
  . ($fid ? " AND b.id <> $fid" : "") . " ORDER BY b.created_at DESC");

$cats  = $Q("SELECT nama, slug FROM blog_kategori ORDER BY urutan, nama");
$years = $Q("SELECT YEAR(created_at) y, COUNT(*) c FROM blog WHERE status='published' GROUP BY y ORDER BY y DESC");

$fmt = fn($d) => $d ? date('F j, Y', strtotime($d)) : '';
$img = fn($p) => $p ? (preg_match('#^(https?:|/|data:)#', $p) ? $p : uploads_url($p)) : '';
$rt  = fn($k) => max(1, (int)ceil(str_word_count(strip_tags((string)$k)) / 200)); // ~200 wpm
// Deterministic gradient fallback for posts without an image.
$grad = function ($id) {
    $g = [['#12325f', '#0a1b38', '73,200,210'], ['#17405f', '#0a2030', '90,160,255'], ['#143a52', '#0b2436', '73,214,199'],
          ['#1b2d55', '#0a1733', '120,150,255'], ['#123a4a', '#0a2230', '73,200,210'], ['#1e3a6b', '#0c1733', '90,160,255']];
    $x = $g[$id % count($g)];
    return "linear-gradient(120deg,$x[0],$x[1]),radial-gradient(circle at 70% 25%,rgba($x[2],.3),transparent 55%)";
};
// Initial filter from the URL (deep-link); JS keeps everything in sync without reloading.
$ikat = isset($_GET['kat']) ? trim((string)$_GET['kat']) : '';
$iq   = isset($_GET['q'])   ? trim((string)$_GET['q'])   : '';
$iyr  = isset($_GET['year'])? (int)$_GET['year']          : 0;

$seo = ['title' => "What's New — " . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => strip_tags($featured['excerpt'] ?? 'Latest news, articles and updates from Sapta Tunas Teknologi.')];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');

// Render one article's media (image or gradient fallback) + category chip.
$media = function ($p) use ($img, $grad) {
    $src = !empty($p['gambar_utama']) ? $img($p['gambar_utama']) : '';
    if ($src !== '') echo '<img src="' . htmlspecialchars($src) . '" alt="' . htmlspecialchars($p['judul'] ?? '') . '" loading="lazy" data-fallback="bg">';
    else echo '<span class="wn-ph" style="background-image:' . htmlspecialchars($grad((int)($p['id'] ?? 0))) . '"></span>';
    if (!empty($p['kategori'])) echo '<span class="wn-chip">' . htmlspecialchars($p['kategori']) . '</span>';
};
// data-* attributes used by the client-side filter.
$dataAttr = function ($p) {
    $s = strtolower(trim(($p['judul'] ?? '') . ' ' . strip_tags($p['excerpt'] ?? '') . ' ' . ($p['kategori'] ?? '')));
    return ' data-cat="' . htmlspecialchars((string)($p['kat_slug'] ?? '')) . '"'
         . ' data-y="' . (int)date('Y', strtotime($p['created_at'] ?? 'now')) . '"'
         . ' data-s="' . htmlspecialchars($s, ENT_QUOTES) . '"';
};
$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
$clock = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>';
?>
<main class="page-body wn-page"
      data-ikat="<?= htmlspecialchars($ikat) ?>" data-iq="<?= htmlspecialchars($iq) ?>" data-iyear="<?= $iyr ?: '' ?>">

  <?php if ($featured): ?>
  <!-- Cinematic cover = latest article -->
  <section class="wn-cover">
    <div class="wn-cover-img"<?= !empty($featured['gambar_utama']) ? ' style="background-image:url(' . htmlspecialchars($img($featured['gambar_utama'])) . ')"' : ' style="background-image:' . htmlspecialchars($grad($fid)) . '"' ?> aria-hidden="true"></div>
    <div class="wn-cover-eye"><?= ac('blog', 'hero_eyebrow') ?: 'What&rsquo;s New' ?></div>
    <div class="wn-cover-in">
      <div class="wn-cover-meta">
        <?php if (!empty($featured['kategori'])): ?><span class="wn-chip wn-chip-grad"><?= htmlspecialchars($featured['kategori']) ?></span><?php endif; ?>
        <span class="wn-cover-date"><?= htmlspecialchars($fmt($featured['created_at'])) ?></span>
      </div>
      <h1><a href="<?= url('blog/' . $featured['slug']) ?>"><?= htmlspecialchars($featured['judul']) ?></a></h1>
      <?php if (trim((string)($featured['excerpt'] ?? '')) !== ''): ?><p><?= htmlspecialchars(strip_tags($featured['excerpt'])) ?></p><?php endif; ?>
      <a class="wn-cover-btn" href="<?= url('blog/' . $featured['slug']) ?>"><?= ac('blog', 'read_more') ?: 'Read Story' ?> <?= $arrow ?></a>
    </div>
    <div class="wn-cue" aria-hidden="true"><?= htmlspecialchars(t('scroll', 'Scroll')) ?></div>
  </section>
  <?php endif; ?>

  <!-- Light editorial list -->
  <section class="wn-list">
    <div class="wn-wrap">
      <div class="wn-head">
        <div class="wn-head-t">
          <div class="k"><?= ac('blog', 'list_kicker') ?: 'Latest' ?></div>
          <h2><?= ac('blog', 'list_title', true) ?: 'Stories &amp; <span class="g">Insights</span>' ?></h2>
        </div>
        <label class="wn-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
          <input type="search" id="wnSearch" placeholder="<?= ac('blog','search_ph') ?: 'Search by keywords' ?>" value="<?= htmlspecialchars($iq) ?>" autocomplete="off">
        </label>
      </div>

      <div class="wn-bar">
        <div class="wn-tabs" id="wnTabs" role="tablist">
          <button type="button" class="wn-tab<?= $ikat === '' ? ' on' : '' ?>" data-cat=""><?= ac('blog','all_label') ?: 'All' ?></button>
          <?php foreach ($cats as $c): ?>
            <button type="button" class="wn-tab<?= $ikat === $c['slug'] ? ' on' : '' ?>" data-cat="<?= htmlspecialchars($c['slug']) ?>"><?= htmlspecialchars($c['nama']) ?></button>
          <?php endforeach; ?>
        </div>
        <?php if ($years): ?>
        <label class="wn-year">
          <select id="wnYear" aria-label="Publishing Year">
            <option value=""><?= ac('blog','years_all') ?: 'All years' ?></option>
            <?php foreach ($years as $y): $yy = (int)$y['y']; ?>
              <option value="<?= $yy ?>"<?= $iyr === $yy ? ' selected' : '' ?>><?= $yy ?> (<?= (int)$y['c'] ?>)</option>
            <?php endforeach; ?>
          </select>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
        </label>
        <?php endif; ?>
      </div>

      <?php if ($posts): $lead = $posts[0]; ?>
      <a class="wn-lead wn-item reveal" href="<?= url('blog/' . $lead['slug']) ?>"<?= $dataAttr($lead) ?>>
        <span class="wn-lead-img"><?php $media($lead); ?></span>
        <span class="wn-lead-body">
          <span class="d"><?= htmlspecialchars($fmt($lead['created_at'])) ?></span>
          <span class="wn-lead-h"><?= htmlspecialchars($lead['judul']) ?></span>
          <?php if (trim((string)($lead['excerpt'] ?? '')) !== ''): ?><span class="wn-ex"><?= htmlspecialchars(strip_tags($lead['excerpt'])) ?></span><?php endif; ?>
          <span class="wn-foot"><span class="wn-rt"><?= $clock ?><?= $rt($lead['konten'] ?? '') ?> min read</span><span class="wn-read"><?= ac('blog','read_more') ?: 'Read More' ?> <?= $arrow ?></span></span>
        </span>
      </a>
      <?php endif; ?>

      <div class="wn-grid" id="wnGrid">
        <?php foreach (array_slice($posts, 1) as $p): ?>
        <article class="wn-card wn-item reveal"<?= $dataAttr($p) ?>>
          <a class="wn-card-img" href="<?= url('blog/' . $p['slug']) ?>"><?php $media($p); ?></a>
          <div class="wn-card-body">
            <span class="d"><?= htmlspecialchars($fmt($p['created_at'])) ?></span>
            <h3><a href="<?= url('blog/' . $p['slug']) ?>"><?= htmlspecialchars($p['judul']) ?></a></h3>
            <?php if (trim((string)($p['excerpt'] ?? '')) !== ''): ?><p><?= htmlspecialchars(strip_tags($p['excerpt'])) ?></p><?php endif; ?>
            <div class="wn-foot"><span class="wn-rt"><?= $clock ?><?= $rt($p['konten'] ?? '') ?> min read</span>
              <a class="wn-read" href="<?= url('blog/' . $p['slug']) ?>"><?= ac('blog','read_more') ?: 'Read' ?> <?= $arrow ?></a></div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <p class="wn-empty" id="wnEmpty" hidden><?= ac('blog','empty_text') ?: 'No articles found.' ?></p>
    </div>
  </section>

</main>
<script src="<?= theme_asset('assets/js/blognew.js') ?>" defer></script>
<?php include theme_path('templates/layouts/footer.php'); ?>
