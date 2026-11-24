<?php
/**
 * Anima — Testimonial DETAIL (route: /testimonial/[slug|id]).
 * $testimonial_data passed from index.php. Two layouts by `tipe`:
 *   video -> embedded player (YouTube / Vimeo / mp4) + story
 *   text  -> large pull-quote + story
 * All content is CMS-driven (admin "Testimoni Klien"); quote/story language-aware via tr_field.
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$t  = $testimonial_data ?? [];
$tid = (int)($t['id'] ?? 0);
$TT = fn(string $f) => tr_field('testimonial', $tid, $f, $t[$f] ?? '');

$imgu = fn($p) => $p ? (preg_match('#^(https?:|/|data:)#', $p) ? $p : uploads_url($p)) : '';
$foto   = $imgu($t['foto'] ?? '');
$poster = $imgu($t['video_poster'] ?? '') ?: $foto;
$name   = $t['nama'] ?? '';
$role   = trim(($t['jabatan'] ?? '') . (!empty($t['perusahaan']) ? ', ' . $t['perusahaan'] : ''));
$rating = max(0, min(5, (int)($t['rating'] ?? 5)));
$quote  = trim(strip_tags((string)$TT('isi')));
$story  = trim((string)$TT('detail'));
$vurl   = trim((string)($t['video_url'] ?? ''));
$is_video = (($t['tipe'] ?? 'text') === 'video') && $vurl !== '';

// Build a safe embed for the video URL (YouTube / Vimeo / direct file).
$embed = null; $file = null;
if ($vurl !== '') {
    if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~', $vurl, $m)) {
        $embed = 'https://www.youtube.com/embed/' . $m[1] . '?rel=0';
    } elseif (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $vurl, $m)) {
        $embed = 'https://player.vimeo.com/video/' . $m[1];
    } elseif (preg_match('~\.(mp4|webm|ogg)(\?|$)~i', $vurl)) {
        $file = $vurl;
    } else {
        $embed = $vurl; // assume a paste-ready embed URL
    }
}

$others = [];
try {
    $others = $db->fetchAll("SELECT * FROM testimonial WHERE is_active=1 AND id<>? ORDER BY urutan, id LIMIT 3", [$tid]);
} catch (\Throwable $e) {}

$seo = ['title' => ($name ?: 'Testimonial') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr($quote, 0, 160)];
$anima_body_class = 'page-inner';
$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
$star  = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.9 6.1 6.6.9-4.8 4.6 1.2 6.5L12 17.8 6.1 20.6l1.2-6.5L2.5 9l6.6-.9z"/></svg>';
$play  = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body sv2"><div class="sv2-wrap tsd">

  <a class="svd-back" href="<?= htmlspecialchars(url('') . '#testimonials') ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
    <?= htmlspecialchars(t('all_stories', 'What They Say')) ?></a>

  <div class="tsd-eyebrow"><?= htmlspecialchars(t('client_story', 'Client Story')) ?></div>

  <?php if ($is_video): ?>
    <!-- ===== VIDEO variant ===== -->
    <section class="tsd-video reveal">
      <?php if ($embed): ?>
        <div class="tsd-frame">
          <iframe src="<?= htmlspecialchars($embed) ?>" title="<?= htmlspecialchars($name) ?>" loading="lazy"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; picture-in-picture" allowfullscreen></iframe>
        </div>
      <?php elseif ($file): ?>
        <div class="tsd-frame">
          <video controls preload="metadata"<?= $poster ? ' poster="' . htmlspecialchars($poster) . '"' : '' ?>>
            <source src="<?= htmlspecialchars($file) ?>"></video>
        </div>
      <?php endif; ?>
    </section>
  <?php else: ?>
    <!-- ===== TEXT variant ===== -->
    <section class="tsd-pull reveal">
      <span class="tsd-mark" aria-hidden="true">&ldquo;</span>
      <blockquote><?= htmlspecialchars($quote) ?></blockquote>
    </section>
  <?php endif; ?>

  <!-- Person + rating -->
  <div class="tsd-person reveal">
    <div class="tsd-av"><?php if ($foto): ?><img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($name) ?>" data-fallback="remove"><?php else: ?><span><?= htmlspecialchars(mb_strtoupper(mb_substr($name, 0, 1))) ?></span><?php endif; ?></div>
    <div class="tsd-meta">
      <div class="tsd-name"><?= htmlspecialchars($name) ?></div>
      <?php if ($role !== ''): ?><div class="tsd-role"><?= htmlspecialchars($role) ?></div><?php endif; ?>
      <?php if ($rating > 0): ?><div class="tsd-stars" aria-label="<?= $rating ?>/5"><?= str_repeat($star, $rating) ?></div><?php endif; ?>
    </div>
  </div>

  <!-- Story -->
  <?php if ($is_video && $quote !== ''): ?>
    <p class="tsd-lead">&ldquo;<?= htmlspecialchars($quote) ?>&rdquo;</p>
  <?php endif; ?>
  <?php if ($story !== ''): ?>
    <article class="tsd-story reveal"><?= $story ?></article>
  <?php endif; ?>

  <!-- Other stories -->
  <?php if ($others): ?>
  <section class="tsd-more">
    <div class="sv2-head"><h2><?= htmlspecialchars(t('more_stories', 'More Stories')) ?></h2></div>
    <div class="tsd-grid">
      <?php foreach ($others as $o): $ov = (($o['tipe'] ?? 'text') === 'video');
        $orole = trim(($o['jabatan'] ?? '') . (!empty($o['perusahaan']) ? ', ' . $o['perusahaan'] : ''));
        $ohref = url('testimonial/' . ($o['slug'] ?: $o['id'])); ?>
      <a class="tsd-card" href="<?= htmlspecialchars($ohref) ?>">
        <span class="tsd-badge <?= $ov ? 'video' : 'text' ?>"><?= $ov ? $play : '' ?><?= htmlspecialchars($ov ? t('video', 'Video') : t('text', 'Text')) ?></span>
        <p><?= htmlspecialchars(mb_strimwidth(strip_tags((string)$o['isi']), 0, 140, '…')) ?></p>
        <div class="tsd-card-p"><span class="tsd-card-n"><?= htmlspecialchars($o['nama']) ?></span><?php if ($orole !== ''): ?><span class="tsd-card-r"><?= htmlspecialchars($orole) ?></span><?php endif; ?></div>
      </a>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="sv2-closing">
    <h2><?= htmlspecialchars(t('testi_cta_title', 'Ready to build your success story?')) ?></h2>
    <a class="btn sv2-closing-btn" href="<?= htmlspecialchars(url('contact-us')) ?>"><?= htmlspecialchars(t('contact_us', 'Contact Us')) ?> <?= $arrow ?></a>
  </section>

</div></main>
<?php include theme_path('templates/layouts/footer.php'); ?>
