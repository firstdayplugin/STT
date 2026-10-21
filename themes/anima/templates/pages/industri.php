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

  <!-- Promo banner (CMS image), floating over the hero like Solutions -->
  <?php if ($banner_img): $banner_has_link = ($banner_url !== '' && $banner_url !== '#'); ?>
  <div class="ind2-promo">
    <?php if ($banner_has_link): ?>
    <a class="sol-banner" href="<?= htmlspecialchars($banner_url) ?>"<?= preg_match('#^https?:#', $banner_url) ? ' target="_blank" rel="noopener"' : '' ?>>
      <img src="<?= htmlspecialchars($banner_img) ?>" alt="" data-fallback="remove">
    </a>
    <?php else: ?>
    <div class="sol-banner"><img src="<?= htmlspecialchars($banner_img) ?>" alt="" data-fallback="remove"></div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if ($rows): ?>
  <!-- Intro -->
  <section class="ind2-lead reveal">
    <div class="k"><?= htmlspecialchars($c('rail_kicker', 'Industries we power')) ?></div>
    <h2><?= htmlspecialchars($c('rail_title', 'Pick your')) ?> <span class="g"><?= htmlspecialchars($c('rail_title2', 'sector')) ?></span> <?= htmlspecialchars($c('rail_title3', '— scroll to explore')) ?></h2>
    <p><?= htmlspecialchars($c('rail_sub', 'One partner across every sector. Scroll through to see how we tailor technology to each.')) ?></p>
  </section>

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

  <!-- Closing CTA -->
  <section class="ind2-cta">
    <h2><?= htmlspecialchars($c('cta_h1', "Don't see your")) ?> <span class="g"><?= htmlspecialchars($c('cta_h2', 'industry?')) ?></span></h2>
    <p><?= htmlspecialchars($c('cta_sub', "Our cross-industry team builds custom IT solutions for any sector. Let's talk about yours.")) ?></p>
    <a href="<?= htmlspecialchars(url('contact-us')) ?>"><?= htmlspecialchars(t('talk_to_team', 'Talk to our team')) ?> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
  </section>
</main>
<script src="<?= theme_asset('assets/js/industrail.js') ?>" defer></script>
<?php include theme_path('templates/layouts/footer.php'); ?>
