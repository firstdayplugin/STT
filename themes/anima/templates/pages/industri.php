<?php
/**
 * Anima — Industries landing (route: /industri). Figma "Our Industries".
 * Header + shared Coming Soon banner + a grid of industry icon-cards from `industri`.
 * Icons/labels/order are CMS-editable (Admin → Industries). A lone last card spans full width.
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$Q  = function (string $sql, array $p = []) use ($db) { try { return $db ? $db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };
$rows = $Q("SELECT * FROM industri WHERE is_active=1 ORDER BY urutan, id");

$c   = fn(string $k, string $d = '') => get_content('industri', $k, $d);
$imgu = function (string $path): string {
    $path = trim($path);
    if ($path === '') return '';
    return preg_match('#^(https?:|/|data:)#', $path) ? $path : uploads_url($path);
};
$card_icon = function ($ic) {
    $ic = trim((string)$ic);
    if ($ic === '') return icon('layers', 64);
    if (str_contains($ic, '/') || str_contains($ic, '.')) {
        return '<img src="' . htmlspecialchars(uploads_url($ic)) . '" alt="" data-fallback="remove">';
    }
    return icon($ic, 64);
};

$seo = ['title' => $c('title', 'Our Industries') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => strip_tags($c('lead', 'Solusi teknologi lintas industri dari Sapta Tunas Teknologi.'))];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');

$banner_img = $imgu($c('banner_img', 'solutions/banner-promo.jpg'));
$banner_url = trim($c('banner_url', '#')); if ($banner_url === '') $banner_url = '#';
// Industries hero photo: CMS-editable (Konten Halaman → Industri → "Foto Hero"), default = towers image.
$ind_hero = $imgu($c('hero_img', 'design3/towers-up.jpg'));
$n = count($rows);

// Promo spotlight — SAME multi-banner component as /solutions (shared solutions_promo table).
$promos = $Q("SELECT * FROM solutions_promo WHERE is_active=1 ORDER BY urutan, id");
// Title accent: escape, then turn [word] into a gradient-highlighted span.
$promo_title = function (string $raw): string {
    $safe = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
    return preg_replace('/\[(.+?)\]/u', '<span class="g">$1</span>', $safe);
};
$arrow  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
$arrowL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>';

/* Per-sector accent, clean stroke line-icon and capability tags, keyed by slug.
   Label / subtitle / link stay CMS-driven; this only styles the rail card. */
$IND_META = [
  'financial'      => ['#3f80e2', ['Infrastructure','Cybersecurity','Data & AI'], '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>'],
  'manufacture'    => ['#f0912a', ['Edge','Computer Vision','IT/OT Security'], '<path d="M2 21h20M4 21V10l5 3V10l5 3V6l6 4v11"/><path d="M8 21v-4M13 21v-4"/>'],
  'healthcare'     => ['#19b8a6', ['Clinical AI','Data Infra','Device Security'], '<path d="M3 12h4l2-5 3 10 2-6 1 1h6"/>'],
  'law-enforcement'=> ['#6366f1', ['Encryption','Secure Cloud','Forensics'], '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>'],
  'energy'         => ['#22c197', ['IoT','Monitoring','Resilience'], '<path d="M13 2L4 14h7l-1 8 9-12h-7l1-8z"/>'],
  'telecom'        => ['#22b8e6', ['Cloud','5G','Network'], '<circle cx="12" cy="12" r="2"/><path d="M8.5 8.5a5 5 0 000 7M15.5 8.5a5 5 0 010 7M6 6a9 9 0 000 12M18 6a9 9 0 010 12"/>'],
  'cross-industry' => ['#a855f7', ['Tailored','Consulting','End-to-end'], '<path d="M12 2l9 5-9 5-9-5 9-5z"/><path d="M3 12l9 5 9-5M3 17l9 5 9-5"/>'],
];
$IND_FALLBACK = ['#2f7be0', [], '<path d="M12 2l9 5-9 5-9-5 9-5z"/><path d="M3 12l9 5 9-5M3 17l9 5 9-5"/>'];
?>
<main class="page-body ind2-page">

  <!-- Dark immersive hero -->
  <section class="ind2-hero">
    <?php if ($ind_hero !== ''): ?><div class="ind2-hero-photo" style="background-image:url(<?= htmlspecialchars($ind_hero) ?>)" aria-hidden="true"></div><?php endif; ?>
    <canvas class="ind2-net" id="ind2Net" aria-hidden="true"></canvas>
    <span class="ind2-au ind2-au1" aria-hidden="true"></span><span class="ind2-au ind2-au2" aria-hidden="true"></span>
    <div class="ind2-hero-in">
      <div class="ind2-eye"><?= htmlspecialchars($c('title', 'Our Industries')) ?></div>
      <h1><?= htmlspecialchars($c('hero_h1', 'Built for every')) ?> <span class="g"><?= htmlspecialchars($c('hero_h2', 'sector you serve.')) ?></span></h1>
      <?php if (trim(strip_tags((string)$c('lead', ''))) !== ''): ?><p><?= $c('lead', '') ?></p><?php endif; ?>
    </div>
  </section>

  <!-- (News & Promotions spotlight removed per client request — the dark hero now
       flows straight into the "Industries we power" intro, bridged by .ind2-lead::before.) -->

  <?php if ($rows): ?>
  <!-- Intro — light band rising over the dark hero (keeps the dark/light/dark/light rhythm the promo gave). -->
  <div class="ind2-intro">
  <section class="ind2-lead reveal">
    <div class="k"><?= htmlspecialchars($c('rail_kicker', 'Industries we power')) ?></div>
    <h2><?= htmlspecialchars($c('rail_title', 'Pick your')) ?> <span class="g"><?= htmlspecialchars($c('rail_title2', 'sector')) ?></span> <?= htmlspecialchars($c('rail_title3', '— scroll to explore')) ?></h2>
    <p><?= htmlspecialchars($c('rail_sub', 'One partner across every sector. Scroll through to see how we tailor technology to each.')) ?></p>
    <?php if ($n > 1): ?>
    <!-- Sectors at a glance — quick jump into the rail below (fills the space the promo left). -->
    <div class="ind2-chips" aria-label="<?= htmlspecialchars(t('sectors', 'Sectors')) ?>">
      <?php foreach ($rows as $r):
        $cslug  = trim((string)($r['slug'] ?? ''));
        $clabel = tr_field('industri', (int)$r['id'], 'label', $r['label']);
        $cac    = ($IND_META[$cslug] ?? $IND_FALLBACK)[0];
        $chref  = $cslug !== '' ? url('industri/' . $cslug) : '#';
      ?>
      <a class="ind2-chip" href="<?= htmlspecialchars($chref) ?>" style="--ac:<?= htmlspecialchars($cac) ?>"><span class="dot" aria-hidden="true"></span><span class="notranslate" translate="no"><?= htmlspecialchars($clabel) ?></span></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>
  </div>

  <!-- Horizontal pinned sector rail (vertical stack on mobile / reduced-motion) -->
  <div class="ind2-rail" id="ind2Rail" data-count="<?= (int)$n ?>">
    <div class="ind2-pin" id="ind2Pin">
      <canvas class="ind2-railnet" id="ind2RailNet" aria-hidden="true"></canvas>
      <div class="ind2-rail-head">
        <div class="rk"><?= htmlspecialchars(t('sectors', 'Sectors')) ?></div>
        <div class="rc"><b id="ind2Num">01</b> / <?= str_pad((string)$n, 2, '0', STR_PAD_LEFT) ?> &nbsp;·&nbsp; <span id="ind2Name"></span></div>
      </div>
      <div class="ind2-track" id="ind2Track">
        <?php foreach ($rows as $i => $r):
          $slug  = trim((string)($r['slug'] ?? ''));
          $href  = $slug !== '' ? url('industri/' . $slug) : '#';
          $label = tr_field('industri', (int)$r['id'], 'label', $r['label']);
          $isub  = tr_field('industri', (int)$r['id'], 'subtitle', $r['subtitle'] ?? '');
          $meta  = $IND_META[$slug] ?? $IND_FALLBACK;
          [$ac, $tags, $icon] = $meta;
        ?>
        <a class="ind2-card" href="<?= htmlspecialchars($href) ?>" style="--ac:<?= htmlspecialchars($ac) ?>" data-name="<?= htmlspecialchars($label) ?>">
          <span class="num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
          <span class="ic"><svg viewBox="0 0 24 24"><?= $icon ?></svg></span>
          <h3 class="notranslate" translate="no"><?= htmlspecialchars($label) ?></h3>
          <?php if (trim((string)$isub) !== ''): ?><div class="sub"><?= htmlspecialchars($isub) ?></div><?php endif; ?>
          <?php if ($tags): ?><div class="tags"><?php foreach ($tags as $tg): ?><span><?= htmlspecialchars($tg) ?></span><?php endforeach; ?></div><?php endif; ?>
          <span class="go"><?= htmlspecialchars(t('explore', 'Explore')) ?> <span class="arw"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></span>
        </a>
        <?php endforeach; ?>
      </div>
      <div class="ind2-rail-foot">
        <div class="rhint"><?= htmlspecialchars(t('scroll', 'Scroll')) ?> ↓</div>
        <div class="rbar"><i id="ind2Bar"></i></div>
        <div class="rdots" id="ind2Dots"></div>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="ind2-empty-wrap"><p class="bl-empty">Belum ada industri.</p></div>
  <?php endif; ?>

  <!-- Pre-footer CTA "Finale" (light) — shared component with Solutions & Services.
       Animated on scroll; white bottom meets the footer seamlessly. CMS-editable. -->
  <?php
  $cta_b1l = trim((string)$c('cta_btn1_label', 'Talk to our team'));
  $cta_b1u = trim((string)$c('cta_btn1_url', 'contact-us'));
  $cta_b2l = trim((string)$c('cta_btn2_label', 'Explore Solutions'));
  $cta_b2u = trim((string)$c('cta_btn2_url', 'solutions'));
  $cta_href = function ($u) { $u = trim($u); if ($u === '' || $u === '#' || strpos($u, '#') === 0) return ($u === '' ? '#' : $u); return preg_match('#^https?:#', $u) ? $u : url(ltrim($u, '/')); };
  ?>
  <section class="cf" aria-label="Contact">
    <canvas class="cf-bg" id="cfBg" aria-hidden="true"></canvas>
    <span class="au au1" aria-hidden="true"></span><span class="au au2" aria-hidden="true"></span><span class="au au3" aria-hidden="true"></span>
    <span class="ring" aria-hidden="true"></span>
    <div class="cf-in">
      <?php if (trim((string)$c('cta_eyebrow', '')) !== ''): ?><div class="cf-eye"><?= htmlspecialchars($c('cta_eyebrow', '')) ?></div><?php endif; ?>
      <h2 class="cf-h" data-text="<?= htmlspecialchars($c('cta_title', "Don't see your [industry?]"), ENT_QUOTES, 'UTF-8') ?>"></h2>
      <p class="cf-p"><?= htmlspecialchars($c('cta_text', "Our cross-industry team builds custom IT solutions for any sector — let's talk about yours.")) ?></p>
      <div class="cf-btns">
        <?php if ($cta_b1l !== ''): ?><a class="btn btn-p" href="<?= htmlspecialchars($cta_href($cta_b1u)) ?>"><?= htmlspecialchars($cta_b1l) ?> <?= $arrow ?></a><?php endif; ?>
        <?php if ($cta_b2l !== ''): ?><a class="btn btn-g" href="<?= htmlspecialchars($cta_href($cta_b2u)) ?>"><?= htmlspecialchars($cta_b2l) ?></a><?php endif; ?>
      </div>
    </div>
  </section>
  <script src="<?= theme_asset('assets/js/ctafinale.js') ?>" defer></script>
</main>
<script src="<?= theme_asset('assets/js/industrail.js') ?>" defer></script>
<?php include theme_path('templates/layouts/footer.php'); ?>
