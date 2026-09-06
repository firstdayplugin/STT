<?php
/**
 * Anima — Industries landing (route: /industri). Grid of industries from `industri`.
 * Each card links to /industri/[slug] (the Industry × Pillar detail).
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$Q  = function (string $sql, array $p = []) use ($db) { try { return $db ? $db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };
$rows = $Q("SELECT * FROM industri WHERE is_active=1 ORDER BY urutan, id");

$seo = ['title' => get_content('industri', 'seo_title', 'Industries') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => get_content('industri', 'seo_desc', 'Solusi teknologi lintas industri dari Sapta Tunas Teknologi.')];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body"><div class="page-shell">

  <div class="page-hero">
    <div class="eyebrow"><?= htmlspecialchars(get_content('industri', 'eyebrow', 'Industries We Serve')) ?></div>
    <h1><?= htmlspecialchars(get_content('industri', 'title', 'Our Industries')) ?></h1>
    <p><?= htmlspecialchars(get_content('industri', 'lead', 'Kami memahami tantangan unik tiap industri dan menghadirkan solusi teknologi yang tepat sasaran.')) ?></p>
  </div>

  <?php if ($rows): ?>
  <div class="ind-grid">
    <?php foreach ($rows as $r):
      $slug = trim((string)($r['slug'] ?? ''));
      $href = $slug !== '' ? url('industri/' . $slug) : '#';
      $img  = !empty($r['gambar']) ? uploads_url($r['gambar']) : '';
    ?>
    <a class="ind-card" href="<?= htmlspecialchars($href) ?>"<?= $img === '' ? ' style="background:linear-gradient(135deg,' . htmlspecialchars($r['warna1']) . ',' . htmlspecialchars($r['warna2']) . ')"' : '' ?>>
      <?php if ($img !== ''): ?><img class="ind-card-bg" src="<?= htmlspecialchars($img) ?>" alt="" data-fallback="remove" loading="lazy"><?php endif; ?>
      <span class="ind-card-ov"></span>
      <span class="ind-card-body">
        <span class="ind-card-label"><?= htmlspecialchars($r['label']) ?></span>
        <?php if (!empty($r['subtitle'])): ?><span class="ind-card-sub"><?= htmlspecialchars($r['subtitle']) ?></span><?php endif; ?>
        <span class="ind-card-more">Explore
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </span>
    </a>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <p class="bl-empty">Belum ada industri.</p>
  <?php endif; ?>

</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
