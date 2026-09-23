<?php
/**
 * Anima — Services LANDING (route: /services). Positioning page: two lead pillars
 * (Infrastructure / Cybersecurity) + supporting capabilities + delivery lifecycle + CTA.
 * All content is CMS-driven (service_pages / service_pillars), language-aware via tr_field.
 * Redesigned to match the site's design language (centered hero, premium cards, animated).
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$p  = $service_page ?? [];
$pid = (int)($p['id'] ?? 0);
$T = fn(string $f) => tr_field('service_pages', $pid, $f, $p[$f] ?? '');

$pillars = []; $supporting = [];
try {
    foreach ($db->fetchAll("SELECT * FROM service_pillars WHERE page_id=? ORDER BY urutan, id", [$pid]) as $r) {
        if (($r['kategori'] ?? 'pillar') === 'supporting') $supporting[] = $r; else $pillars[] = $r;
    }
} catch (\Throwable $e) {}
$TP = fn(array $r, string $f) => tr_field('service_pillars', (int)$r['id'], $f, $r[$f] ?? '');
$lifecycle = array_values(array_filter(array_map('trim', explode('→', (string)$T('extra2')))));

$pillar_icons  = ['layers', 'lock', 'settings', 'sparkles'];
$support_icons = ['sparkles', 'box', 'grid', 'layers'];
$step_icons    = ['search', 'box', 'settings', 'lock', 'rocket'];
$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';

$seo = ['title' => ($p['judul'] ?? 'Services') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($T('body')), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body sv2"><div class="sv2-wrap">

  <div class="sv2-hero">
    <?php if (trim((string)$T('eyebrow')) !== ''): ?><div class="sv2-eyebrow"><?= htmlspecialchars($T('eyebrow')) ?></div><?php endif; ?>
    <h1><?= htmlspecialchars($T('headline') ?: ($p['judul'] ?? 'Services')) ?></h1>
    <?php if (trim((string)$T('body')) !== ''): ?><p class="sv2-lead"><?= htmlspecialchars($T('body')) ?></p><?php endif; ?>
    <?php if (trim((string)$T('extra1')) !== ''): ?><p class="sv2-quote"><?= htmlspecialchars($T('extra1')) ?></p><?php endif; ?>
    <?php if (!empty($p['cta_label'])): ?>
      <a class="btn btn-primary sv2-cta" href="<?= htmlspecialchars(url($p['cta_target'] ?: 'contact-us')) ?>"><?= htmlspecialchars($T('cta_label')) ?> <?= $arrow ?></a>
    <?php endif; ?>
  </div>

  <?php if ($pillars): ?>
  <section class="sv2-pillars reveal">
    <?php foreach ($pillars as $i => $pl): $tags = array_values(array_filter(array_map('trim', explode(',', (string)($pl['tags'] ?? ''))))); $has = !empty($pl['link_slug']); ?>
    <?= $has ? '<a class="sv2-pcard" href="' . htmlspecialchars(url($pl['link_slug'])) . '">' : '<div class="sv2-pcard nolink">' ?>
      <span class="sv2-picon"><?= icon($pillar_icons[$i] ?? 'layers', 30) ?></span>
      <?php if (!empty($pl['badge'])): ?><span class="sv2-badge"><?= htmlspecialchars($pl['badge']) ?></span><?php endif; ?>
      <h2 translate="no" class="notranslate"><?= htmlspecialchars($TP($pl, 'judul')) ?></h2>
      <p><?= htmlspecialchars($TP($pl, 'deskripsi')) ?></p>
      <?php if ($tags): ?><div class="sv2-chips" translate="no"><?php foreach ($tags as $t): ?><span><?= htmlspecialchars($t) ?></span><?php endforeach; ?></div><?php endif; ?>
      <?php if ($has): ?><span class="sv2-more"><?= htmlspecialchars(t('learn_more', 'Selengkapnya')) ?> <?= $arrow ?></span><?php endif; ?>
    <?= $has ? '</a>' : '</div>' ?>
    <?php endforeach; ?>
  </section>
  <?php endif; ?>

  <?php if ($supporting): ?>
  <section class="sv2-sec">
    <div class="sv2-head"><h2><?= htmlspecialchars(t('supporting_capability', 'Supporting Capability')) ?></h2></div>
    <div class="sv2-support reveal">
      <?php foreach ($supporting as $i => $s): ?>
      <div class="sv2-scard">
        <span class="sv2-sicon"><?= icon($support_icons[$i] ?? 'box', 24) ?></span>
        <?php if (!empty($s['badge'])): ?><span class="sv2-stag"><?= htmlspecialchars($s['badge']) ?></span><?php endif; ?>
        <h3 translate="no" class="notranslate"><?= htmlspecialchars($TP($s, 'judul')) ?></h3>
        <p><?= htmlspecialchars($TP($s, 'deskripsi')) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($lifecycle): ?>
  <section class="sv2-sec">
    <div class="sv2-head"><h2><?= htmlspecialchars(t('delivery_lifecycle', 'End-to-End Lifecycle')) ?></h2></div>
    <div class="sv2-flow reveal">
      <?php foreach ($lifecycle as $i => $step): ?>
        <div class="sv2-step">
          <span class="sv2-step-ic"><?= icon($step_icons[$i] ?? 'circle', 24) ?></span>
          <span class="sv2-step-n"><?= $i + 1 ?></span>
          <span class="sv2-step-l"><?= htmlspecialchars($step) ?></span>
        </div>
        <?php if ($i < count($lifecycle) - 1): ?><span class="sv2-flow-line" aria-hidden="true"></span><?php endif; ?>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="sv2-closing">
    <h2><?= htmlspecialchars(trim((string)$T('extra3')) ?: 'One Partner. End-to-End Capability.') ?></h2>
    <a class="btn sv2-closing-btn" href="<?= htmlspecialchars(url($p['cta_target'] ?: 'contact-us')) ?>"><?= htmlspecialchars($T('cta_label') ?: t('contact_us', 'Contact Us')) ?> <?= $arrow ?></a>
  </section>

</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
