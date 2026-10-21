<?php
/**
 * Anima — Industry detail (route: /industri/[slug]). ONE shared template for every
 * industry; all copy is CMS-driven (industri + solusi_pilar + industri_pilar).
 * Redesign "Command Deck": cinematic hero → pinned, scroll-scrubbed pillar deck
 * where each pillar's capabilities assemble into a glowing constellation → light
 * CTA finale. Motion in inddeck.js; static stacked layout on mobile/reduced-motion.
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$Q  = function (string $sql, array $p = []) use ($db) { try { return $db ? $db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };
$ind = $industri_data ?? [];
$iid = (int)($ind['id'] ?? 0);

$pillars = $Q("SELECT * FROM solusi_pilar WHERE is_active=1 ORDER BY urutan, id");
$cells = [];
foreach ($Q("SELECT * FROM industri_pilar WHERE industri_id = ?", [$iid]) as $cc) { $cells[(int)$cc['pilar_id']] = $cc; }

$feat_icon = function ($ic) {
    $ic = trim((string)$ic);
    if ($ic === '') return icon('circle', 26);
    if (str_contains($ic, '/') || str_contains($ic, '.')) return '<img src="' . htmlspecialchars(uploads_url($ic)) . '" alt="" data-fallback="remove">';
    return icon($ic, 26);
};
// Gradient the trailing word(s) of a plain-text heading (honours [word] markers too).
$accent = function (string $raw): string {
    $raw = trim($raw);
    if ($raw === '') return '';
    if (strpos($raw, '[') !== false) return preg_replace('/\[(.+?)\]/u', '<span class="g">$1</span>', htmlspecialchars($raw, ENT_QUOTES, 'UTF-8'));
    $safe = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
    return preg_replace('/((?:\S+\s+)?\S+)(\s*)$/u', '<span class="g">$1</span>$2', $safe);
};

$ind_label = tr_field('industri', $iid, 'label', $ind['label'] ?? '');
$intro = tr_field('industri', $iid, 'intro', $ind['intro'] ?? '');
if (trim($intro) === '') $intro = get_content('industri', 'detail_intro', '');
$hero_img = !empty($ind['hero_image']) ? uploads_url($ind['hero_image']) : (!empty($ind['gambar']) ? uploads_url($ind['gambar']) : '');
$n = count($pillars);
$deck_h = max(560, 120 + $n * 160);   // pinned scrub travel (heavy): ~160vh / pillar

$seo = ['title' => ($ind_label ?: 'Industry') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($intro), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body idc-page">

  <!-- Cinematic hero: industry photo + data-network + scan sweep + cursor parallax. -->
  <section class="idc-hero<?= $hero_img === '' ? ' idc-hero--plain' : '' ?>"<?= $hero_img !== '' ? ' style="--idc-photo:url(' . htmlspecialchars($hero_img) . ')"' : '' ?>>
    <div class="idc-photo" aria-hidden="true"></div>
    <div class="idc-hero-ov" aria-hidden="true"></div>
    <canvas class="idc-net" id="idcNet" aria-hidden="true"></canvas>
    <div class="idc-scan" aria-hidden="true"></div>
    <div class="idc-hero-in">
      <div class="idc-eye"><?= htmlspecialchars(get_content('industri', 'detail_eyebrow', 'Industries')) ?></div>
      <h1 class="idc-h1 notranslate" translate="no"><?= $accent($ind_label) ?></h1>
      <?php if (trim($intro) !== ''): ?><p class="idc-intro"><?= $intro ?></p><?php endif; ?>
    </div>
    <?php if ($pillars): ?><div class="idc-cue" aria-hidden="true"><?= htmlspecialchars(t('scroll', 'Scroll')) ?></div><?php endif; ?>
  </section>

  <?php if ($pillars): ?>
  <!-- Pinned pillar deck. Each .idc-panel dwells sharp, then crossfades; its
       capability constellation assembles from the `fitur` icons. -->
  <section class="idc-deck" id="idcDeck" style="height:<?= (int)$deck_h ?>vh" aria-label="<?= htmlspecialchars($ind_label) ?> — pilar solusi">
    <div class="idc-pin">
      <canvas class="idc-dust" id="idcDust" aria-hidden="true"></canvas>
      <div class="idc-deck-eye"><?= htmlspecialchars(get_content('industri', 'detail_pillars_eyebrow', 'Solusi per Pilar')) ?></div>
      <div class="idc-rail" id="idcRail" aria-hidden="true">
        <?php foreach ($pillars as $i => $pl): ?>
          <button type="button" class="<?= $i === 0 ? 'on' : '' ?>"><span class="idx"><?= sprintf('%02d', $i + 1) ?></span><span class="d"></span></button>
        <?php endforeach; ?>
      </div>
      <div class="idc-stage">
        <?php foreach ($pillars as $i => $pl):
          $cell = $cells[(int)$pl['id']] ?? null;
          $pilar_nama = tr_field('solusi_pilar', (int)$pl['id'], 'nama', $pl['nama']);
          $pilar_desk = tr_field('solusi_pilar', (int)$pl['id'], 'deskripsi', $pl['deskripsi'] ?? '');
          $heading = ($cell && $cell['heading'] !== null && $cell['heading'] !== '')
                   ? tr_field('industri_pilar', (int)$cell['id'], 'heading', $cell['heading']) : $pilar_nama;
          $konten  = ($cell && $cell['konten'] !== null && $cell['konten'] !== '')
                   ? tr_field('industri_pilar', (int)$cell['id'], 'konten', $cell['konten']) : ('<p>' . htmlspecialchars($pilar_desk) . '</p>');
          $fitur = [];
          if (!empty($cell['fitur'])) { $d = json_decode($cell['fitur'], true); if (is_array($d)) $fitur = $d; }
        ?>
        <article class="idc-panel<?= $i === 0 ? ' show' : '' ?>"<?= $i === 0 ? '' : ' style="opacity:0;visibility:hidden"' ?>>
          <div class="idc-txt">
            <div class="idc-num idc-rv d1"><?= sprintf('%02d', $i + 1) ?></div>
            <div class="idc-peye idc-rv d2"><?= htmlspecialchars($pilar_nama) ?></div>
            <h2 class="idc-ph idc-rv d2 notranslate" translate="no"><?= $accent($heading) ?></h2>
            <div class="idc-prose idc-rv d3"><?= $konten ?></div>
          </div>
          <div class="idc-con">
            <canvas aria-hidden="true"></canvas>
            <?php if ($fitur): foreach ($fitur as $k => $f): ?>
              <div class="idc-node<?= $k === 0 ? ' lead' : '' ?>">
                <span class="idc-dot"><?= $feat_icon($f['icon'] ?? '') ?></span>
                <span class="idc-lb"><?= htmlspecialchars($f['judul'] ?? '') ?></span>
              </div>
            <?php endforeach; endif; ?>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <script src="<?= theme_asset('assets/js/inddeck.js') ?>" defer></script>
  <?php endif; ?>

  <?php
  // Light CTA finale (shared .cf; reveal + particles in ctafinale.js).
  $cta_title = get_content('industri', 'detail_cta_title', 'Siap memperkuat bisnis [' . ($ind_label ?: 'Anda') . '?]');
  $cta_text  = get_content('industri', 'detail_cta_text', "Diskusikan kebutuhan infrastruktur, keamanan, data, dan AI dengan partner yang dipercaya pemimpin teknologi global.");
  $cta_label = get_content('industri', 'detail_cta_label', t('contact_us', 'Konsultasikan Kebutuhan Anda'));
  $cta_arrow = '<svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
  ?>
  <section class="cf" aria-label="Contact">
    <canvas class="cf-bg" id="cfBg" aria-hidden="true"></canvas>
    <span class="au au1" aria-hidden="true"></span><span class="au au2" aria-hidden="true"></span><span class="au au3" aria-hidden="true"></span>
    <span class="ring" aria-hidden="true"></span>
    <div class="cf-in">
      <?php $cta_eye = get_content('industri', 'detail_cta_eyebrow', 'Mulai transformasi Anda'); if (trim($cta_eye) !== ''): ?><div class="cf-eye"><?= htmlspecialchars($cta_eye) ?></div><?php endif; ?>
      <h2 class="cf-h notranslate" translate="no" data-text="<?= htmlspecialchars($cta_title, ENT_QUOTES, 'UTF-8') ?>"></h2>
      <p class="cf-p"><?= htmlspecialchars($cta_text) ?></p>
      <div class="cf-btns">
        <a class="btn btn-p" href="<?= htmlspecialchars(url('contact-us')) ?>"><?= htmlspecialchars($cta_label) ?> <?= $cta_arrow ?></a>
      </div>
    </div>
  </section>
  <script src="<?= theme_asset('assets/js/ctafinale.js') ?>" defer></script>
</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
