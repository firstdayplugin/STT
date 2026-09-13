<?php
/**
 * Anima — Solutions landing (route: /solutions). Figma "Our Solutions".
 * Header + Coming Soon banner: content_blocks (page_key 'solutions').
 * Blocks: solutions_section table (illustration, partner strip, solution text, CTA),
 * rendered as alternating image/text rows — all CMS-editable, EN via content_i18n.
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$Q  = function (string $sql, array $p = []) use ($db) { try { return $db ? $db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };
$sections = $Q("SELECT * FROM solutions_section WHERE is_active=1 ORDER BY urutan, id");

$c   = fn(string $k, string $d = '') => get_content('solutions', $k, $d);
$img = function (string $path): string {
    $path = trim($path);
    if ($path === '') return '';
    if (preg_match('#^(https?:|/|data:)#', $path)) return $path;
    return uploads_url($path);
};
$lbl_solution = $c('label_solution', 'Solution:');
$lbl_partner  = $c('label_partner', 'Partner:');
$lbl_cta      = $c('card_cta', 'See More');
$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';

$seo = ['title' => $c('title', 'Our Solutions') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => strip_tags($c('lead', 'Solusi teknologi enterprise dari Sapta Tunas Teknologi.'))];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');

$banner_img = $img($c('banner_img', 'solutions/coming-soon-banner.png'));
$banner_url = trim($c('banner_url', '#')); if ($banner_url === '') $banner_url = '#';
?>
<main class="page-body sol-page">
  <div class="sol-wrap">

    <header class="sol-head">
      <h1><?= htmlspecialchars($c('title', 'Our Solutions')) ?></h1>
      <p><?= $c('lead', '') ?></p>
    </header>

    <?php if ($banner_img): ?>
    <a class="sol-banner" href="<?= htmlspecialchars($banner_url) ?>"<?= preg_match('#^https?:#', $banner_url) ? ' target="_blank" rel="noopener"' : '' ?>>
      <img src="<?= htmlspecialchars($banner_img) ?>" alt="<?= htmlspecialchars(strip_tags($c('title', 'Our Solutions'))) ?>" data-fallback="remove">
    </a>
    <?php endif; ?>

    <?php foreach ($sections as $i => $s):
        $sid    = (int)$s['id'];
        $judul  = tr_field('solutions_section', $sid, 'judul', $s['judul']);
        $solusi = tr_field('solutions_section', $sid, 'solusi', $s['solusi'] ?? '');
        $illus  = $img((string)($s['gambar'] ?? ''));
        $ptn    = $img((string)($s['partner_img'] ?? ''));
        $href   = trim((string)($s['url'] ?? '')); if ($href === '') $href = '#';
        $href_r = preg_match('#^https?:#', $href) ? $href : ($href === '#' ? '#' : url(ltrim($href, '/')));
        $red    = (($s['teks_warna'] ?? '') === 'red');
        $cta_l  = tr_field('solutions_section', $sid, 'cta_label', $s['cta_label'] ?? '');
        $cta_u  = trim((string)($s['cta_url'] ?? '')); if ($cta_u === '') $cta_u = '#';
        $img_left = ($i % 2 === 0); // 1st, 3rd, 5th → illustration on the left (matches Figma)
    ?>
    <?php if ($i > 0): ?><div class="sol-div"></div><?php endif; ?>
    <section class="sol-row<?= $img_left ? '' : ' rev' ?>">

      <div class="sol-media">
        <?php if ($illus): ?><img class="sol-illus-img" src="<?= htmlspecialchars($illus) ?>" alt="<?= htmlspecialchars(strip_tags($judul)) ?>" data-fallback="bg"><?php endif; ?>
        <a class="sol-more" href="<?= htmlspecialchars($href_r) ?>"><?= htmlspecialchars($lbl_cta) ?> <?= $arrow ?></a>
      </div>

      <div class="sol-text">
        <h2><?= $judul ?></h2>
        <?php if ($solusi !== ''): ?>
          <div class="sol-label"><?= htmlspecialchars($lbl_solution) ?></div>
          <p class="sol-desc<?= $red ? ' red' : '' ?>"><?= nl2br(htmlspecialchars($solusi)) ?></p>
        <?php endif; ?>
        <?php if ($ptn): ?>
          <div class="sol-label"><?= htmlspecialchars($lbl_partner) ?></div>
          <div class="sol-partners"><img src="<?= htmlspecialchars($ptn) ?>" alt="Partners" data-fallback="remove"></div>
        <?php endif; ?>
        <?php if (trim($cta_l) !== ''): ?>
          <a class="sol-cta-btn" href="<?= htmlspecialchars(preg_match('#^https?:#', $cta_u) ? $cta_u : ($cta_u === '#' ? '#' : url(ltrim($cta_u, '/')))) ?>">
            <span><?= htmlspecialchars($cta_l) ?></span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7M8 7h9v9"/></svg>
          </a>
        <?php endif; ?>
      </div>

    </section>
    <?php endforeach; ?>

    <?php if (empty($sections)): ?>
      <p class="bl-empty">Belum ada section solusi.</p>
    <?php endif; ?>

  </div>
</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
