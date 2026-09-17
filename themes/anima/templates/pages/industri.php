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

$banner_img = $imgu($c('banner_img', 'solutions/coming-soon-banner.png'));
$banner_url = trim($c('banner_url', '#')); if ($banner_url === '') $banner_url = '#';
$n = count($rows);
?>
<main class="page-body indpg-page">
  <div class="indpg-wrap">

    <div class="indpg-head">
      <h1><?= htmlspecialchars($c('title', 'Our Industries')) ?></h1>
      <p><?= $c('lead', '') ?></p>
    </div>

    <?php if ($banner_img): $banner_has_link = ($banner_url !== '' && $banner_url !== '#'); ?>
    <?php if ($banner_has_link): ?>
    <a class="sol-banner" href="<?= htmlspecialchars($banner_url) ?>"<?= preg_match('#^https?:#', $banner_url) ? ' target="_blank" rel="noopener"' : '' ?>>
      <img src="<?= htmlspecialchars($banner_img) ?>" alt="" data-fallback="remove">
    </a>
    <?php else: ?>
    <div class="sol-banner"><img src="<?= htmlspecialchars($banner_img) ?>" alt="" data-fallback="remove"></div>
    <?php endif; ?>
    <?php endif; ?>

    <?php if ($rows): ?>
    <div class="indl-grid">
      <?php foreach ($rows as $i => $r):
        $slug = trim((string)($r['slug'] ?? ''));
        $href = $slug !== '' ? url('industri/' . $slug) : '#';
        // A lone card on the final row spans the full width (Figma: Cross Industry).
        $wide = ($i === $n - 1 && ($n % 3) === 1) ? ' wide' : '';
      ?>
      <a class="indl-card<?= $wide ?>" href="<?= htmlspecialchars($href) ?>">
        <span class="indl-ic"><?= $card_icon($r['icon'] ?? '') ?></span>
        <span class="indl-name"><?= htmlspecialchars(tr_field('industri', (int)$r['id'], 'label', $r['label'])) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
      <p class="bl-empty">Belum ada industri.</p>
    <?php endif; ?>

  </div>
</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
