<?php
/**
 * Anima — Services LANDING (route: /services). Positioning page per the client handoff:
 * two pillars (Infrastructure / Cybersecurity) + supporting capabilities (Data & AI) + lifecycle.
 * Detail lives in the capability/package sub-pages. Data comes from service_pages/service_pillars.
 * $service_page is passed by index.php. Language-aware via tr_field.
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$p  = $service_page ?? [];
$pid = (int)($p['id'] ?? 0);
$T = fn(string $f) => tr_field('service_pages', $pid, $f, $p[$f] ?? '');

$pillars    = [];
$supporting = [];
try {
    foreach ($db->fetchAll("SELECT * FROM service_pillars WHERE page_id=? ORDER BY urutan, id", [$pid]) as $r) {
        if (($r['kategori'] ?? 'pillar') === 'supporting') $supporting[] = $r; else $pillars[] = $r;
    }
} catch (\Throwable $e) {}
$TP = fn(array $r, string $f) => tr_field('service_pillars', (int)$r['id'], $f, $r[$f] ?? '');
$lifecycle = array_values(array_filter(array_map('trim', explode('→', (string)$T('extra2')))));

$seo = ['title' => ($p['judul'] ?? 'Services') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($T('body')), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body"><div class="page-shell sv">

  <section class="sv-hero">
    <div class="eyebrow"><?= htmlspecialchars($T('eyebrow') ?: 'Services') ?></div>
    <h1><?= htmlspecialchars($T('headline') ?: ($p['judul'] ?? 'Services')) ?></h1>
    <p class="lead"><?= htmlspecialchars($T('body')) ?></p>
    <?php if (trim((string)$T('extra1')) !== ''): ?><p class="sv-support"><?= htmlspecialchars($T('extra1')) ?></p><?php endif; ?>
    <?php if (!empty($p['cta_label'])): ?>
      <a class="btn btn-primary sv-cta" href="<?= htmlspecialchars(url($p['cta_target'] ?: 'contact-us')) ?>"><?= htmlspecialchars($T('cta_label')) ?>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
    <?php endif; ?>
  </section>

  <?php if ($pillars): ?>
  <section class="sv-sec">
    <div class="sv-pillars">
      <?php foreach ($pillars as $pl): $tags = array_values(array_filter(array_map('trim', explode(',', (string)($pl['tags'] ?? ''))))); ?>
      <a class="sv-pillar<?= empty($pl['link_slug']) ? ' nolink' : '' ?>"<?= !empty($pl['link_slug']) ? ' href="' . htmlspecialchars(url($pl['link_slug'])) . '"' : '' ?>>
        <?php if (!empty($pl['badge'])): ?><span class="sv-badge"><?= htmlspecialchars($pl['badge']) ?></span><?php endif; ?>
        <h2><?= htmlspecialchars($TP($pl, 'judul')) ?></h2>
        <p><?= htmlspecialchars($TP($pl, 'deskripsi')) ?></p>
        <?php if ($tags): ?><div class="sv-chips"><?php foreach ($tags as $t): ?><span><?= htmlspecialchars($t) ?></span><?php endforeach; ?></div><?php endif; ?>
        <?php if (!empty($pl['link_slug'])): ?><span class="sv-more">Selengkapnya <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span><?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($supporting): ?>
  <section class="sv-sec">
    <div class="sv-head"><h2>Supporting Capability</h2></div>
    <div class="sv-support-grid">
      <?php foreach ($supporting as $s): ?>
      <div class="sv-supcard">
        <?php if (!empty($s['badge'])): ?><span class="sv-suptag"><?= htmlspecialchars($s['badge']) ?></span><?php endif; ?>
        <h3><?= htmlspecialchars($TP($s, 'judul')) ?></h3>
        <p><?= htmlspecialchars($TP($s, 'deskripsi')) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($lifecycle): ?>
  <section class="sv-sec">
    <div class="sv-lifecycle">
      <?php foreach ($lifecycle as $i => $step): ?>
        <?php if ($i > 0): ?><span class="sv-arrow" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span><?php endif; ?>
        <span class="sv-step"><?= htmlspecialchars($step) ?></span>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if (trim((string)$T('extra3')) !== ''): ?>
  <section class="sv-closing"><p><?= htmlspecialchars($T('extra3')) ?></p></section>
  <?php endif; ?>

</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
