<?php
/**
 * Anima theme — About Us (route: /tentang-kami). Built from the Figma "About Us" design.
 * Singular copy is editable via ac('about', key). Repeaters (mission, values, milestones, awards,
 * quality, certs) are defaults here for now — TODO: bind to CMS repeater/gallery modules.
 * Shares layouts/header.php (nav solid) + layouts/footer.php.
 */
if (!isset($db) && class_exists('Database')) { $db = Database::getInstance(); }
$seo = [
  'title'       => get_setting('site_title_about', 'About Us — ' . get_setting('site_name', 'Sapta Tunas Teknologi')),
  'description' => get_setting('site_description_about', 'Sapta Tunas Teknologi — Enterprise Solution Provider sejak 2015: infrastruktur, cloud, cybersecurity, data & AI.'),
];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');

// Repeaters are editable from the "Tentang Kami" admin module (about_items table).
// Read grouped by section; language-aware via tr_field (falls back to base text).
$about_q = function (string $seksi) use ($db) {
    try { return $db ? $db->fetchAll("SELECT * FROM about_items WHERE seksi=? AND is_active=1 ORDER BY urutan, id", [$seksi]) : []; }
    catch (\Throwable $e) { return []; }
};
$mission    = $about_q('mission');
$values     = $about_q('value');
$milestones = $about_q('milestone');
$awards     = $about_q('award');
$quality    = $about_q('quality');
$certs      = $about_q('cert');
$atr = fn($row, $field) => tr_field('about_items', (int)($row['id'] ?? 0), $field, $row[$field] ?? '');

$trophy = '<svg viewBox="0 0 24 24"><path d="M6 4h12v3a6 6 0 01-12 0V4z"/><path d="M6 5H3v2a3 3 0 003 3M18 5h3v2a3 3 0 01-3 3M9 20h6M12 13v7"/></svg>';
$check  = '<svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>';
?>
<main class="page-body">
  <div class="page-shell ab">

    <!-- Intro -->
    <section class="ab-intro">
      <div class="eyebrow"><?= ac('about', 'intro_eyebrow') ?></div>
      <h1><?= ac('about', 'intro_title') ?></h1>
      <p class="lead"><?= ac('about', 'intro_body', true) ?></p>
    </section>

    <!-- Vision + Mission -->
    <section class="ab-sec ab-vm">
      <div class="ab-vcard">
        <h2><?= ac('about', 'vision_title') ?></h2>
        <p><?= ac('about', 'vision_body') ?></p>
      </div>
      <div class="ab-mcard">
        <h2><?= ac('about', 'mission_title') ?></h2>
        <ul class="ab-mlist">
          <?php foreach ($mission as $m): ?>
            <li><span class="chk"><?= $check ?></span><span><?= htmlspecialchars($atr($m, 'teks')) ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>

    <!-- Values (ICARE) -->
    <section class="ab-sec">
      <div class="ab-head">
        <h2><?= ac('about', 'values_eyebrow') ?> <span class="blue"><?= ac('about', 'values_title', true) ?></span></h2>
      </div>
      <div class="ab-values-grid">
        <?php foreach ($values as $v): ?>
          <div class="ab-val">
            <div class="badge"><?= htmlspecialchars($v['kode'] ?? '') ?></div>
            <h3><?= htmlspecialchars($atr($v, 'judul')) ?></h3>
            <p><?= htmlspecialchars($atr($v, 'teks')) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Milestone -->
    <section class="ab-sec">
      <div class="ab-mile">
        <div class="ab-mile-body">
          <h2><?= ac('about', 'milestone_title', true) ?></h2>
          <p><?= ac('about', 'milestone_body', true) ?></p>
        </div>
        <div class="ab-mile-media" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6"/></svg>
        </div>
      </div>
      <div class="ab-timeline">
        <?php foreach ($milestones as $ms): $now = (($ms['kode'] ?? '') === 'now'); ?>
          <div class="ab-tnode<?= $now ? ' now' : '' ?>"><span class="dot"></span><div class="yr"><?= htmlspecialchars($ms['tahun'] ?? '') ?></div></div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Awards -->
    <section class="ab-sec">
      <div class="ab-head">
        <h2><?= ac('about', 'awards_title') ?></h2>
        <p><?= ac('about', 'awards_intro') ?></p>
      </div>
      <div class="ab-row">
        <?php foreach ($awards as $a): ?>
          <div class="ab-award">
            <div class="ph"><?= $trophy ?></div>
            <div class="org"><?= htmlspecialchars($atr($a, 'judul')) ?></div>
            <div class="ttl"><?= htmlspecialchars($atr($a, 'teks')) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Quality Standards -->
    <section class="ab-sec">
      <div class="ab-head">
        <h2><?= ac('about', 'quality_title', true) ?></h2>
        <p><?= ac('about', 'quality_intro') ?></p>
      </div>
      <div class="ab-row">
        <?php foreach ($quality as $iso): ?>
          <div class="ab-iso"><span class="b"><?= htmlspecialchars($iso['judul'] ?? '') ?></span></div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Certifications -->
    <section class="ab-sec">
      <div class="ab-head">
        <h2><?= ac('about', 'certs_title', true) ?></h2>
        <p><?= ac('about', 'certs_intro') ?></p>
      </div>
      <div class="ab-row">
        <?php foreach ($certs as $c): ?>
          <div class="ab-iso"><span class="b"><?= htmlspecialchars($c['judul'] ?? '') ?></span></div>
        <?php endforeach; ?>
      </div>
    </section>

  </div>
</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
