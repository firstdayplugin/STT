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

// Constellation icon: clean stroke line-icon (matches the sketch look) chosen by
// keyword from the capability title — NOT the uploaded raster icon, which renders
// as a boxed thumbnail and clashes with the dark HUD style.
$IDC_ICONS = [
  'server' => '<path d="M3 4h18v6H3zM3 14h18v6H3z"/><path d="M7 7h.01M7 17h.01"/>',
  'cloud'  => '<path d="M17 18H7a4 4 0 0 1 0-8 5 5 0 0 1 9.6-1.3A3.5 3.5 0 0 1 17 18z"/>',
  'scale'  => '<path d="M4 20v-6M12 20V7M20 20v-10"/><path d="M2 14h4M10 7h4M18 10h4"/>',
  'shield' => '<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/>',
  'bot'    => '<rect x="4" y="8" width="16" height="11" rx="2"/><path d="M12 8V4M8 13h.01M16 13h.01"/>',
  'cpu'    => '<rect x="6" y="6" width="12" height="12" rx="2"/><path d="M9 1v3M15 1v3M9 20v3M15 20v3M1 9h3M1 15h3M20 9h3M20 15h3"/>',
  'eye'    => '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/>',
  'alert'  => '<path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>',
  'chart'  => '<path d="M3 3v18h18"/><path d="M7 14l3-3 3 3 5-5"/>',
  'gear'   => '<circle cx="12" cy="12" r="3"/><path d="M19 12a7 7 0 0 0-.1-1.2l2-1.6-2-3.4-2.4 1a7 7 0 0 0-2-1.2L14 2h-4l-.5 2.6a7 7 0 0 0-2 1.2l-2.4-1-2 3.4 2 1.6A7 7 0 0 0 5 12c0 .4 0 .8.1 1.2l-2 1.6 2 3.4 2.4-1a7 7 0 0 0 2 1.2L10 22h4l.5-2.6a7 7 0 0 0 2-1.2l2.4 1 2-3.4-2-1.6z"/>',
  'net'    => '<circle cx="5" cy="6" r="2"/><circle cx="19" cy="6" r="2"/><circle cx="12" cy="18" r="2"/><path d="M6.7 7.3 10.6 16M17.3 7.3 13.4 16M7 6h10"/>',
  'lock'   => '<rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
  'file'   => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6"/>',
  'clock'  => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
  'wrench' => '<path d="M14.7 6.3a4 4 0 0 1-5 5L4 17v3h3l5.7-5.7a4 4 0 0 1 5-5z"/>',
  'target' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.5"/>',
];
$cap_svg = function (string $title) use ($IDC_ICONS) {
    $t = function_exists('mb_strtolower') ? mb_strtolower(trim($title)) : strtolower(trim($title));
    // ordered keyword → icon (specific before generic)
    $map = [
      'data center'=>'server','datacenter'=>'server','infrastruk'=>'server',
      'hybrid'=>'cloud','cloud'=>'cloud',
      'scalable'=>'scale','resilient'=>'scale','scale'=>'scale',
      'compliance'=>'shield','governance'=>'shield','regulat'=>'shield',
      'generative'=>'bot','avatar'=>'bot','chatbot'=>'bot',
      'llm'=>'cpu','model'=>'cpu','compute'=>'cpu','gpu'=>'cpu',
      'surveillance'=>'eye','camera'=>'eye','cctv'=>'eye','vision'=>'eye',
      'report'=>'chart','dashboard'=>'chart','insight'=>'chart','analytic'=>'chart',
      'fraud'=>'alert','anomaly'=>'alert',
      'automation'=>'gear','operation'=>'gear',' ops'=>'gear','orchestr'=>'gear','soar'=>'gear',
      'cx'=>'net','agent'=>'net','customer'=>'net','experience'=>'net','chat'=>'net',
      'edr'=>'shield','endpoint'=>'shield',
      'ndr'=>'net','network'=>'net',
      'xdr'=>'target',
      'firewall'=>'lock','waf'=>'shield','web application'=>'shield',
      'siem'=>'eye','monitoring'=>'eye','log'=>'eye',
      'threat'=>'alert','intelligence'=>'alert','intel'=>'alert',
      'vapt'=>'target','penetration'=>'target','pentest'=>'target','vulnerab'=>'target',
      'forensic'=>'file','audit'=>'file',
      'drill'=>'shield','table top'=>'net','exercise'=>'net','simulat'=>'net',
      '24/7'=>'clock','24x7'=>'clock','365'=>'clock','always'=>'clock','uptime'=>'clock',
      'patch'=>'wrench','maintenance'=>'wrench','update'=>'wrench',
      'security'=>'shield','secure'=>'shield','proteksi'=>'shield','data'=>'chart','ai'=>'cpu',
    ];
    $key = 'net';
    foreach ($map as $kw => $ic) { if ($t !== '' && strpos($t, $kw) !== false) { $key = $ic; break; } }
    return '<svg viewBox="0 0 24 24">' . ($IDC_ICONS[$key] ?? $IDC_ICONS['net']) . '</svg>';
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
            <?php if ($fitur): foreach ($fitur as $k => $f):
              $f_title = trim((string)($f['judul'] ?? ''));
              $f_teks  = trim((string)($f['teks'] ?? ''));
              $has = $f_teks !== '';
            ?>
              <?php if ($has): ?>
              <button type="button" class="idc-node has-detail<?= $k === 0 ? ' lead' : '' ?>" aria-haspopup="dialog">
                <span class="idc-dot"><?= $cap_svg($f_title) ?></span>
                <span class="idc-lb"><?= htmlspecialchars($f_title) ?></span>
                <div class="idc-nd" hidden><?= $f_teks ?></div>
              </button>
              <?php else: ?>
              <div class="idc-node<?= $k === 0 ? ' lead' : '' ?>">
                <span class="idc-dot"><?= $cap_svg($f_title) ?></span>
                <span class="idc-lb"><?= htmlspecialchars($f_title) ?></span>
              </div>
              <?php endif; ?>
            <?php endforeach; endif; ?>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
    <!-- capability detail popover (fixed overlay; filled from the clicked node) -->
    <div class="idc-modal" id="idcModal" hidden>
      <div class="idc-modal-scrim" data-idc-close></div>
      <div class="idc-modal-card" role="dialog" aria-modal="true" aria-labelledby="idcModalT">
        <button type="button" class="idc-modal-x" data-idc-close aria-label="<?= htmlspecialchars(t('close', 'Close')) ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
        <div class="idc-modal-head"><span class="idc-modal-ic" id="idcModalIc" aria-hidden="true"></span><h3 class="idc-modal-t" id="idcModalT"></h3></div>
        <div class="idc-modal-body" id="idcModalBody"></div>
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
