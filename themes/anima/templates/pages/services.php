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
<?php
// Services hero photo: per-page CMS field (service_pages.hero_image), default = the towers image.
$sv_hero = !empty($p['hero_image']) ? uploads_url($p['hero_image']) : uploads_url('design3/towers-up.jpg');
?>
<main class="page-body sv2 svc-page">
<?php
// Lifecycle stages → hero loop nodes (CMS: service_pages.extra2, "A → B → C").
if (empty($lifecycle)) $lifecycle = ['Rencanakan', 'Bangun', 'Kelola', 'Lindungi', 'Optimalkan'];
$lc_icons = [
  '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',                         // plan / search
  '<rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/>', // build
  '<circle cx="12" cy="12" r="3"/><path d="M19 12a7 7 0 0 0-.1-1.2l2-1.6-2-3.4-2.4 1a7 7 0 0 0-2-1.2L14 2h-4l-.5 2.6a7 7 0 0 0-2 1.2l-2.4-1-2 3.4 2 1.6A7 7 0 0 0 5 12c0 .4 0 .8.1 1.2l-2 1.6 2 3.4 2.4-1a7 7 0 0 0 2 1.2L10 22h4l.5-2.6a7 7 0 0 0 2-1.2l2.4 1 2-3.4-2-1.6c.1-.4.1-.8.1-1.2z"/>', // manage / gear
  '<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/>',   // protect / shield
  '<path d="M4 20l5-5 3 3 8-8"/><path d="M15 10h5v5"/>',                                 // optimize / trend
];
// Headline accent: honour [word], else gradient the last word(s).
$svc_acc = function (string $raw): string {
    $raw = trim($raw);
    if ($raw === '') return '';
    if (strpos($raw, '[') !== false) return preg_replace('/\[(.+?)\]/u', '<span class="b">$1</span>', htmlspecialchars($raw, ENT_QUOTES, 'UTF-8'));
    $safe = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
    return preg_replace('/((?:\S+\s+)?\S+)(\s*)$/u', '<span class="b">$1</span>$2', $safe);
};
$svc_headline = $svc_acc($T('headline') ?: ($p['judul'] ?? 'Services'));
?>

  <!-- Hero: "Lifecycle Loop" — delivery stages on a glowing loop with a travelling
       comet (continuous end-to-end cycle). Motion in svchero.js; static list on mobile. -->
  <section class="svc-hero" id="svcHero" aria-label="<?= htmlspecialchars(strip_tags($T('headline') ?: ($p['judul'] ?? 'Services'))) ?>">
    <canvas class="svc-net" id="svcNet" aria-hidden="true"></canvas>
    <div class="svc-copy" id="svcCopy">
      <?php if (trim((string)$T('eyebrow')) !== ''): ?><div class="svc-eye"><?= htmlspecialchars($T('eyebrow')) ?></div><?php endif; ?>
      <h1 class="svc-title"><?= $svc_headline ?></h1>
    </div>
    <div class="svc-stage" id="svcStage">
      <div class="svc-hub" id="svcHub" aria-hidden="true"><span class="ring2"></span><div class="lbl"><b>END&ndash;TO&ndash;END</b><span>Lifecycle</span></div></div>
      <?php foreach ($lifecycle as $i => $step): ?>
        <div class="svc-node">
          <span class="step"><?= $i + 1 ?></span>
          <span class="ic"><svg viewBox="0 0 24 24"><?= $lc_icons[$i % count($lc_icons)] ?></svg></span>
          <span class="n"><?= htmlspecialchars($step) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <script src="<?= theme_asset('assets/js/svchero.js') ?>" defer></script>

  <?php
  // Capabilities "Deck" — the 2 lead pillars + supporting capabilities as a single
  // dark, pinned, scroll-scrubbed deck (same language as the Solutions deck). The
  // right side is a glowing icon emblem instead of a photo. Reuses .sd-* + soldeck.js.
  $caps = [];
  foreach ($pillars as $i => $pl)   $caps[] = ['row' => $pl, 'icon' => $pillar_icons[$i] ?? 'layers'];
  foreach ($supporting as $i => $s) $caps[] = ['row' => $s,  'icon' => $support_icons[$i] ?? 'box'];
  $cap_n = count($caps);
  ?>
  <?php if ($caps): ?>
  <section class="sd-wrap svc-deck" id="sdWrap" aria-label="<?= htmlspecialchars(t('supporting_capability', 'What we deliver')) ?>" style="height:<?= max(320, $cap_n * 112) ?>vh">
    <div class="sd-pin">
      <canvas class="sd-bg" id="sdBg" aria-hidden="true"></canvas>
      <div class="sd-eye"><?= htmlspecialchars(t('what_we_deliver', 'What we deliver')) ?></div>
      <div class="sd-rail" id="sdRail">
        <?php foreach ($caps as $i => $c): ?>
          <button type="button" data-i="<?= $i ?>" class="<?= $i === 0 ? 'on' : '' ?>" aria-label="<?= htmlspecialchars(strip_tags($TP($c['row'], 'judul'))) ?>"><span class="d"></span><span class="idx"><?= sprintf('%02d', $i + 1) ?></span></button>
        <?php endforeach; ?>
      </div>
      <div class="sd-stage" id="sdStage">
        <?php foreach ($caps as $i => $c): $cr = $c['row'];
          $tags = array_values(array_filter(array_map('trim', explode(',', (string)($cr['tags'] ?? '')))));
          $has = !empty($cr['link_slug']);
        ?>
          <div class="sd-panel<?= $i === 0 ? ' on' : '' ?>" data-i="<?= $i ?>">
            <div class="sd-txt">
              <div class="sd-num"><?= sprintf('%02d', $i + 1) ?></div>
              <?php if (!empty($cr['badge'])): ?><span class="sd-badge"><?= htmlspecialchars($cr['badge']) ?></span><?php endif; ?>
              <h2 class="sd-title notranslate" translate="no"><?= $svc_acc($TP($c['row'], 'judul')) ?></h2>
              <?php if (trim((string)$TP($c['row'], 'deskripsi')) !== ''): ?><p class="sd-desc"><?= htmlspecialchars($TP($c['row'], 'deskripsi')) ?></p><?php endif; ?>
              <?php if ($tags): ?><div class="sd-chips" translate="no"><?php foreach ($tags as $tg): ?><span><?= htmlspecialchars($tg) ?></span><?php endforeach; ?></div><?php endif; ?>
              <?php if ($has): ?><a class="sd-cta" href="<?= htmlspecialchars(url($cr['link_slug'])) ?>"><?= htmlspecialchars(t('learn_more', 'Selengkapnya')) ?> <?= $arrow ?></a><?php endif; ?>
            </div>
            <div class="sd-media sd-emblem" aria-hidden="true">
              <span class="glow"></span><span class="ring r1"></span><span class="ring r2"></span>
              <span class="ic"><?= icon($c['icon'], 60) ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <script src="<?= theme_asset('assets/js/soldeck.js') ?>" defer></script>
  <?php endif; ?>

  <!-- Closing CTA "Finale" (light; reveal + particles in ctafinale.js). Its
       white bottom meets the footer's white band, so the transition is smooth. -->
  <section class="cf" aria-label="Contact">
    <canvas class="cf-bg" id="cfBg" aria-hidden="true"></canvas>
    <span class="au au1" aria-hidden="true"></span><span class="au au2" aria-hidden="true"></span><span class="au au3" aria-hidden="true"></span>
    <span class="ring" aria-hidden="true"></span>
    <div class="cf-in">
      <?php if (trim((string)$T('eyebrow')) !== ''): ?><div class="cf-eye"><?= htmlspecialchars($T('eyebrow')) ?></div><?php endif; ?>
      <h2 class="cf-h" data-text="<?= htmlspecialchars(trim((string)$T('extra3')) ?: 'One Partner. End-to-End [Capability.]', ENT_QUOTES, 'UTF-8') ?>"></h2>
      <?php if (trim((string)$T('extra1')) !== ''): ?><p class="cf-p"><?= htmlspecialchars($T('extra1')) ?></p><?php endif; ?>
      <div class="cf-btns">
        <a class="btn btn-p" href="<?= htmlspecialchars(url($p['cta_target'] ?: 'contact-us')) ?>"><?= htmlspecialchars($T('cta_label') ?: t('contact_us', 'Contact Us')) ?> <?= $arrow ?></a>
      </div>
    </div>
  </section>
  <script src="<?= theme_asset('assets/js/ctafinale.js') ?>" defer></script>
</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
