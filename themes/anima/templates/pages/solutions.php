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

    <div class="sol-head">
      <h1><?= htmlspecialchars($c('title', 'Our Solutions')) ?></h1>
      <p><?= $c('lead', '') ?></p>
    </div>

    <?php if ($banner_img): $banner_has_link = ($banner_url !== '' && $banner_url !== '#'); ?>
    <?php if ($banner_has_link): ?>
    <a class="sol-banner" href="<?= htmlspecialchars($banner_url) ?>"<?= preg_match('#^https?:#', $banner_url) ? ' target="_blank" rel="noopener"' : '' ?>>
      <img src="<?= htmlspecialchars($banner_img) ?>" alt="<?= htmlspecialchars(strip_tags($c('title', 'Our Solutions'))) ?>" data-fallback="remove">
    </a>
    <?php else: ?>
    <div class="sol-banner">
      <img src="<?= htmlspecialchars($banner_img) ?>" alt="<?= htmlspecialchars(strip_tags($c('title', 'Our Solutions'))) ?>" data-fallback="remove">
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <?php foreach ($sections as $i => $s):
        $sid    = (int)$s['id'];
        $judul  = tr_field('solutions_section', $sid, 'judul', $s['judul']);
        $solusi = tr_field('solutions_section', $sid, 'solusi', $s['solusi'] ?? '');
        $detail = tr_field('solutions_section', $sid, 'detail', $s['detail'] ?? '');
        $has_detail = trim(strip_tags($detail)) !== '';
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
    <?php $anchor = make_slug(strip_tags((string)($s['judul'] ?? ''))); ?>
    <section class="sol-row<?= $img_left ? '' : ' rev' ?>"<?= $anchor !== '' ? ' id="' . htmlspecialchars($anchor) . '"' : '' ?>>

      <div class="sol-media">
        <?php if ($illus): ?><img class="sol-illus-img" src="<?= htmlspecialchars($illus) ?>" alt="<?= htmlspecialchars(strip_tags($judul)) ?>" data-fallback="bg"><?php endif; ?>
        <?php if ($has_detail): ?>
          <button type="button" class="sol-more" data-sol-open="<?= $sid ?>" aria-haspopup="dialog"><?= htmlspecialchars($lbl_cta) ?> <?= $arrow ?></button>
        <?php else: ?>
          <a class="sol-more" href="<?= htmlspecialchars($href_r) ?>"><?= htmlspecialchars($lbl_cta) ?> <?= $arrow ?></a>
        <?php endif; ?>
      </div>

      <div class="sol-text">
        <h2><?= $judul ?></h2>
        <?php if ($solusi !== ''): ?>
          <div class="sol-label"><?= htmlspecialchars($lbl_solution) ?></div>
          <p class="sol-desc<?= $red ? ' red' : '' ?>"><?= nl2br(htmlspecialchars($solusi)) ?></p>
        <?php endif; ?>
        <div class="sol-label"><?= htmlspecialchars($lbl_partner) ?></div>
        <?php if ($ptn): ?>
          <div class="sol-partners"><img src="<?= htmlspecialchars($ptn) ?>" alt="Partners" data-fallback="remove"></div>
        <?php else: ?>
          <div class="sol-partners sol-partners-ph" role="img" aria-label="Partner logos placeholder"><?php for ($k = 0; $k < 6; $k++): ?><span class="sol-plogo"></span><?php endfor; ?></div>
        <?php endif; ?>
        <?php if (trim($cta_l) !== '' && $cta_u !== '#'): ?>
          <a class="sol-cta-btn" href="<?= htmlspecialchars(preg_match('#^https?:#', $cta_u) ? $cta_u : url(ltrim($cta_u, '/'))) ?>">
            <span><?= htmlspecialchars($cta_l) ?></span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7M8 7h9v9"/></svg>
          </a>
        <?php endif; ?>
      </div>

    </section>

    <?php if ($has_detail): ?>
    <div class="sol-modal" id="solm-<?= $sid ?>" aria-hidden="true">
      <div class="sol-modal-scrim" data-sol-close></div>
      <div class="sol-modal-panel" role="dialog" aria-modal="true" aria-labelledby="solm-<?= $sid ?>-t">
        <button type="button" class="sol-modal-x" data-sol-close aria-label="<?= htmlspecialchars(t('close', 'Tutup')) ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
        <div class="sol-modal-head">
          <div class="sol-modal-eye"><?= htmlspecialchars(strip_tags($c('title', 'Our Solutions'))) ?></div>
          <h2 id="solm-<?= $sid ?>-t"><?= $judul ?></h2>
        </div>
        <div class="sol-modal-body"><?= $detail ?></div>
        <div class="sol-modal-foot">
          <a class="btn btn-primary sol-modal-cta" href="<?= htmlspecialchars(url('contact-us')) ?>"><?= htmlspecialchars(t('contact_us', 'Contact Us')) ?> <?= $arrow ?></a>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>

    <?php if (empty($sections)): ?>
      <p class="bl-empty">Belum ada section solusi.</p>
    <?php endif; ?>

  </div>
</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
