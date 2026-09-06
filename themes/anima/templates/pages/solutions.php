<?php
/**
 * Anima — Solutions landing (route: /solutions). Four solution pillars from `solusi_pilar`.
 * The same pillars reappear as tabs on each Industry detail page (§B).
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$Q  = function (string $sql, array $p = []) use ($db) { try { return $db ? $db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };
$pillars = $Q("SELECT * FROM solusi_pilar WHERE is_active=1 ORDER BY urutan, id");

$pill_icon = function ($p) {
    $ic = trim((string)($p['icon'] ?? ''));
    if ($ic === '') return icon('layers', 26);
    if (str_contains($ic, '/') || str_contains($ic, '.')) {
        return '<img src="' . htmlspecialchars(uploads_url($ic)) . '" alt="" data-fallback="remove" style="width:28px;height:28px;object-fit:contain">';
    }
    return icon($ic, 26);
};

$seo = ['title' => get_content('solutions', 'seo_title', 'Solutions') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => get_content('solutions', 'seo_desc', 'Empat pilar solusi teknologi enterprise dari Sapta Tunas Teknologi.')];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body"><div class="page-shell">

  <div class="page-hero">
    <div class="eyebrow"><?= htmlspecialchars(get_content('solutions', 'eyebrow', 'What We Do')) ?></div>
    <h1><?= htmlspecialchars(get_content('solutions', 'title', 'Our Solutions')) ?></h1>
    <p><?= htmlspecialchars(get_content('solutions', 'lead', 'Empat pilar solusi yang dirancang untuk memodernisasi, mengamankan, dan mengakselerasi bisnis Anda.')) ?></p>
  </div>

  <?php if ($pillars): ?>
  <div class="sol-grid">
    <?php foreach ($pillars as $p): $href = trim((string)($p['url'] ?? '')); ?>
    <a class="sol-card" href="<?= htmlspecialchars($href !== '' ? (preg_match('#^https?:#', $href) ? $href : url(ltrim($href, '/'))) : '#') ?>">
      <div class="sol-ic"><?= $pill_icon($p) ?></div>
      <h3><?= htmlspecialchars($p['nama']) ?></h3>
      <p><?= htmlspecialchars($p['deskripsi'] ?? '') ?></p>
      <span class="sol-more"><?= htmlspecialchars(get_content('solutions', 'card_cta', 'Selengkapnya')) ?>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
    </a>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <p class="bl-empty">Belum ada pilar solusi.</p>
  <?php endif; ?>

  <div class="sol-cta">
    <h2><?= htmlspecialchars(get_content('solutions', 'cta_title', 'Siap memodernisasi bisnis Anda?')) ?></h2>
    <p><?= htmlspecialchars(get_content('solutions', 'cta_lead', 'Diskusikan kebutuhan teknologi Anda dengan tim ahli kami.')) ?></p>
    <a class="btn btn-primary" href="<?= url('hubungi-kami') ?>"><?= htmlspecialchars(get_content('solutions', 'cta_btn', 'Hubungi Kami')) ?>
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
  </div>

</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
