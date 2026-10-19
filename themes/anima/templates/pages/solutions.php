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

$banner_img = $img($c('banner_img', 'solutions/banner-promo.jpg'));
$banner_url = trim($c('banner_url', '#')); if ($banner_url === '') $banner_url = '#';
// Solutions hero photo: admin-editable (Konten Halaman → Solutions → "Foto Hero"),
// defaulting to the towers image the client picked.
$sol_hero = $img($c('hero_img', 'design3/towers-up.jpg'));

// Promo spotlight (multi-banner) — Admin → "Solutions Promo" (solutions_promo table).
// Falls back to the legacy single Coming Soon banner when the table is empty/missing.
$promos = $Q("SELECT * FROM solutions_promo WHERE is_active=1 ORDER BY urutan, id");

// Hero "constellation" nodes are derived from the solution sections themselves,
// so each node links to its section and the copy stays CMS-driven. Icons are
// keyword-matched to a small built-in set; positions are computed in solhero.js.
$node_icon = function (string $name): string {
    $n = mb_strtolower($name);
    if (strpos($n, 'platform') !== false || strpos($n, 'application') !== false || strpos($n, 'apps') !== false)
        return '<rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/>';
    if (strpos($n, 'cyber') !== false || strpos($n, 'security') !== false)
        return '<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/>';
    if (strpos($n, 'data') !== false)
        return '<ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v6c0 1.7 3.1 3 7 3s7-1.3 7-3V6"/><path d="M5 12v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"/>';
    if (strpos($n, 'ai') !== false || strpos($n, 'intelligence') !== false)
        return '<rect x="6" y="6" width="12" height="12" rx="3"/><path d="M9 2v4M15 2v4M9 18v4M15 18v4M2 9h4M2 15h4M18 9h4M18 15h4"/><circle cx="12" cy="12" r="2.4"/>';
    if (strpos($n, 'infra') !== false || strpos($n, 'cloud') !== false || strpos($n, 'modern') !== false)
        return '<rect x="3" y="4" width="18" height="5" rx="1"/><rect x="3" y="11" width="18" height="5" rx="1"/><rect x="3" y="18" width="18" height="3" rx="1"/><circle cx="7" cy="6.5" r=".6"/><circle cx="7" cy="13.5" r=".6"/>';
    return '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/>';
};
$sol_nodes = [];
foreach ($sections as $s) {
    $sid  = (int)$s['id'];
    $nm   = trim(strip_tags(tr_field('solutions_section', $sid, 'judul', $s['judul'])));
    if ($nm === '') continue;
    $slug = make_slug(strip_tags((string)($s['judul'] ?? '')));
    $sub  = trim(strip_tags(tr_field('solutions_section', $sid, 'solusi', $s['solusi'] ?? '')));
    if (($cp = mb_strpos($sub, ',')) !== false) $sub = mb_substr($sub, 0, $cp);
    if (mb_strlen($sub) > 40) $sub = rtrim(mb_substr($sub, 0, 38)) . '…';
    $sol_nodes[] = ['nm' => $nm, 'sub' => $sub, 'slug' => $slug, 'ic' => $node_icon($nm)];
}

// Title accent helper: escape, then turn [word] into a gradient-highlighted span.
$promo_title = function (string $raw): string {
    $safe = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
    return preg_replace('/\[(.+?)\]/u', '<span class="g">$1</span>', $safe);
};
$mark_glow = theme_asset('assets/img/stt-mark-glow.png');
// Hero title: honour [word] accent if present, else gradient the last word.
$hero_title_raw = (string)$c('title', 'Our Solutions');
if (strpos($hero_title_raw, '[') !== false) {
    $hero_title = $promo_title($hero_title_raw);
} else {
    $hero_title = preg_replace('/(\S+)(\s*)$/u', '<span class="b">$1</span>$2', htmlspecialchars($hero_title_raw, ENT_QUOTES, 'UTF-8'));
}
?>
<main class="page-body sol-page">

  <!-- Hero "Solutions Constellation" + Promo spotlight share a sticky stack so the
       promo rises up over the pinned hero (premium "cover" scroll, like About Us).
       Hero: solhero.js (cursor parallax, data lines, clickable nodes → sections).
       Promo: solpromo.js (auto-advancing featured spotlight). Mobile: both static. -->
  <div class="scn-stack">
    <section class="scn" id="solHero" aria-label="<?= htmlspecialchars(strip_tags($c('title', 'Our Solutions'))) ?>">
      <canvas class="scn-net" id="solNet" aria-hidden="true"></canvas>
      <div class="scn-copy" id="solCopy">
        <?php if (trim((string)$c('hero_eyebrow', '')) !== ''): ?><div class="scn-eyebrow"><?= htmlspecialchars($c('hero_eyebrow', 'Enterprise Solution Provider')) ?></div><?php endif; ?>
        <h1 class="scn-title"><?= $hero_title ?></h1>
      </div>
      <div class="scn-arena" id="solArena">
        <div class="scn-core" id="solCore"><span class="orb"></span><span class="ring"></span><span class="ring"></span><span class="ring"></span><div class="lbl"><img class="mk" src="<?= htmlspecialchars($mark_glow) ?>" alt="STT"></div></div>
        <?php foreach ($sol_nodes as $nd): ?>
          <a class="scn-node" href="#<?= htmlspecialchars($nd['slug']) ?>">
            <span class="ic"><svg viewBox="0 0 24 24"><?= $nd['ic'] ?></svg></span>
            <span class="nm"><?= htmlspecialchars($nd['nm']) ?><?php if ($nd['sub'] !== ''): ?><small><?= htmlspecialchars($nd['sub']) ?></small><?php endif; ?></span>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="scn-scroll" aria-hidden="true"><i></i>scroll</div>
    </section>

    <?php if ($promos): ?>
    <section class="pm-sec" id="pmSec" aria-label="News &amp; Promotions">
      <div class="pm">
        <div class="pm-head">
          <div><div class="pm-eyebrow"><?= htmlspecialchars($c('promo_eyebrow', "What's happening at STT")) ?></div><h2>News &amp; <span class="b">Promotions</span></h2></div>
          <div class="pm-nav"><button type="button" id="pmPrev" aria-label="Sebelumnya"><?= $arrowL ?? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>' ?></button><button type="button" id="pmNext" aria-label="Berikutnya"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg></button></div>
        </div>
        <div class="pm-feat" id="pmFeat">
          <?php foreach ($promos as $k => $pr): $pid = (int)$pr['id'];
            $p_img  = $img((string)($pr['gambar'] ?? ''));
            $p_ttl  = trim((string)tr_field('solutions_promo', $pid, 'judul', $pr['judul'] ?? ''));
            $p_date = trim((string)tr_field('solutions_promo', $pid, 'tanggal', $pr['tanggal'] ?? ''));
            $p_blb  = trim((string)tr_field('solutions_promo', $pid, 'blurb', $pr['blurb'] ?? ''));
            $p_cta  = trim((string)tr_field('solutions_promo', $pid, 'cta_text', $pr['cta_text'] ?? ''));
            $p_url  = trim((string)($pr['url'] ?? '')); if ($p_url === '') $p_url = '#';
            $p_href = preg_match('#^https?:#', $p_url) ? $p_url : ($p_url === '#' ? '#' : url(ltrim($p_url, '/')));
            $p_ext  = preg_match('#^https?:#', $p_url) ? ' target="_blank" rel="noopener"' : '';
          ?>
          <div class="pm-slide<?= $k === 0 ? ' on' : '' ?>" data-i="<?= $k ?>">
            <div class="ph"<?= $p_img ? ' style="background-image:url(' . htmlspecialchars($p_img) . ')"' : '' ?>></div>
            <div class="tint"></div><div class="au au1"></div><div class="au au2"></div><div class="sheen"></div>
            <span class="cn tl"></span><span class="cn br"></span>
            <div class="pm-ct">
              <?php if ($p_date !== ''): ?><span class="dt"><?= htmlspecialchars($p_date) ?></span><?php endif; ?>
              <?php if ($p_ttl !== ''): ?><h3><?= $promo_title($p_ttl) ?></h3><?php endif; ?>
              <?php if ($p_blb !== ''): ?><p><?= htmlspecialchars($p_blb) ?></p><?php endif; ?>
              <?php if ($p_cta !== ''): ?><a class="cta" href="<?= htmlspecialchars($p_href) ?>"<?= $p_ext ?>><?= htmlspecialchars($p_cta) ?> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="pm-queue" id="pmQueue" style="--n:<?= max(1, count($promos)) ?>">
          <?php foreach ($promos as $k => $pr): $pid = (int)$pr['id'];
            $p_img = $img((string)($pr['gambar'] ?? ''));
            $p_ttl = trim(strip_tags((string)tr_field('solutions_promo', $pid, 'judul', $pr['judul'] ?? '')));
            $p_ttl = preg_replace('/[\[\]]/', '', $p_ttl);
          ?>
          <button type="button" class="pm-thumb<?= $k === 0 ? ' on' : '' ?>" data-i="<?= $k ?>" aria-label="<?= htmlspecialchars($p_ttl) ?>">
            <span class="ph"<?= $p_img ? ' style="background-image:url(' . htmlspecialchars($p_img) . ')"' : '' ?>></span>
            <span class="cap"><?= htmlspecialchars($p_ttl) ?></span><span class="bar"></span>
          </button>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <script src="<?= theme_asset('assets/js/solpromo.js') ?>" defer></script>
    <?php endif; ?>
  </div><!-- /.scn-stack -->
  <script src="<?= theme_asset('assets/js/solhero.js') ?>" defer></script>

  <div class="sol-wrap">

    <?php if (!$promos && $banner_img): $banner_has_link = ($banner_url !== '' && $banner_url !== '#'); ?>
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

    <?php if (empty($sections)): ?>
      <p class="bl-empty">Belum ada section solusi.</p>
    <?php endif; ?>

  </div><!-- /.sol-wrap -->

  <?php
  // Precompute per-section data (used by the deck panels and the modals).
  $sol_rows = [];
  foreach ($sections as $i => $s) {
      $sid = (int)$s['id'];
      $href = trim((string)($s['url'] ?? '')); if ($href === '') $href = '#';
      $detail = tr_field('solutions_section', $sid, 'detail', $s['detail'] ?? '');
      $sol_rows[] = [
          'sid'        => $sid,
          'judul'      => tr_field('solutions_section', $sid, 'judul', $s['judul']),
          'solusi'     => tr_field('solutions_section', $sid, 'solusi', $s['solusi'] ?? ''),
          'detail'     => $detail,
          'has_detail' => (trim(strip_tags($detail)) !== ''),
          'illus'      => $img((string)($s['gambar'] ?? '')),
          'anchor'     => make_slug(strip_tags((string)($s['judul'] ?? ''))),
          'href_r'     => preg_match('#^https?:#', $href) ? $href : ($href === '#' ? '#' : url(ltrim($href, '/'))),
          'logos'      => $Q("SELECT gambar, nama FROM solution_logos WHERE solution_id=? AND is_active=1 ORDER BY urutan, id", [$sid]),
      ];
  }
  ?>

  <?php if ($sections): $sol_n = count($sol_rows); ?>
  <!-- Solutions "Deck" — dark, pinned, scroll-scrubbed. Vertical scroll walks
       through the 5 solutions (heavy lerp); each: big index, title, description,
       a uniform partner-logo wall (whitened → colour on hover, cascades in) and a
       See More button. Rail nav on the left. Motion in soldeck.js; static stack on
       mobile. CMS: solutions_section + solution_logos. -->
  <section class="sd-wrap" id="sdWrap" aria-label="<?= htmlspecialchars(strip_tags($c('title', 'Our Solutions'))) ?>" style="height:<?= max(320, $sol_n * 112) ?>vh">
    <div class="sd-pin">
      <canvas class="sd-bg" id="sdBg" aria-hidden="true"></canvas>
      <div class="sd-eye"><?= htmlspecialchars($c('deck_eyebrow', 'What we deliver')) ?></div>
      <div class="sd-rail" id="sdRail">
        <?php foreach ($sol_rows as $i => $r): ?>
          <button type="button" data-i="<?= $i ?>" class="<?= $i === 0 ? 'on' : '' ?>" aria-label="<?= htmlspecialchars(strip_tags($r['judul'])) ?>"><span class="d"></span><span class="idx"><?= sprintf('%02d', $i + 1) ?></span></button>
        <?php endforeach; ?>
      </div>
      <div class="sd-stage" id="sdStage">
        <?php foreach ($sol_rows as $i => $r): ?>
          <div class="sd-panel<?= $i === 0 ? ' on' : '' ?>" data-i="<?= $i ?>"<?= $r['anchor'] !== '' ? ' id="' . htmlspecialchars($r['anchor']) . '"' : '' ?>>
            <div class="sd-txt">
              <div class="sd-num"><?= sprintf('%02d', $i + 1) ?></div>
              <h2 class="sd-title notranslate" translate="no"><?= $r['judul'] ?></h2>
              <div class="sd-lbl"><?= htmlspecialchars($lbl_solution) ?></div>
              <?php if (trim($r['solusi']) !== ''): ?><p class="sd-desc"><?= nl2br(htmlspecialchars($r['solusi'])) ?></p><?php endif; ?>
              <?php if ($r['logos']): ?>
                <div class="sd-logos">
                  <?php foreach ($r['logos'] as $lg): $lsrc = $img((string)($lg['gambar'] ?? '')); if ($lsrc === '') continue; ?>
                    <div class="cell"><img src="<?= htmlspecialchars($lsrc) ?>" alt="<?= htmlspecialchars((string)($lg['nama'] ?? 'Partner')) ?>" loading="lazy" data-fallback="remove"></div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              <?php if ($r['has_detail']): ?>
                <button type="button" class="sd-cta" data-sol-open="<?= $r['sid'] ?>" aria-haspopup="dialog"><?= htmlspecialchars($lbl_cta) ?> <?= $arrow ?></button>
              <?php else: ?>
                <a class="sd-cta" href="<?= htmlspecialchars($r['href_r']) ?>"><?= htmlspecialchars($lbl_cta) ?> <?= $arrow ?></a>
              <?php endif; ?>
            </div>
            <div class="sd-media"><span class="glow" aria-hidden="true"></span><?php if ($r['illus']): ?><img src="<?= htmlspecialchars($r['illus']) ?>" alt="<?= htmlspecialchars(strip_tags($r['judul'])) ?>" data-fallback="bg"><?php endif; ?></div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="sd-hint" aria-hidden="true">scroll to explore our solutions &darr;</div>
    </div>
  </section>
  <script src="<?= theme_asset('assets/js/soldeck.js') ?>" defer></script>

  <!-- Solution detail modals (futuristic). Opened by the See More buttons via
       anima-ui.js; body renders the CMS "Detail" (WYSIWYG) HTML. -->
  <?php foreach ($sol_rows as $r): if (!$r['has_detail']) continue; $sid = $r['sid']; ?>
  <div class="sol-modal" id="solm-<?= $sid ?>" aria-hidden="true">
    <div class="sol-modal-scrim" data-sol-close></div>
    <div class="sol-modal-panel" role="dialog" aria-modal="true" aria-labelledby="solm-<?= $sid ?>-t">
      <div class="sol-modal-fx" aria-hidden="true"><span class="grid"></span><span class="au au1"></span><span class="au au2"></span></div>
      <span class="sol-modal-cn tl" aria-hidden="true"></span><span class="sol-modal-cn tr" aria-hidden="true"></span><span class="sol-modal-cn bl" aria-hidden="true"></span><span class="sol-modal-cn br" aria-hidden="true"></span>
      <button type="button" class="sol-modal-x" data-sol-close aria-label="<?= htmlspecialchars(t('close', 'Tutup')) ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
      <div class="sol-modal-body">
        <div class="sol-modal-eye"><?= htmlspecialchars(strip_tags($c('title', 'Our Solutions'))) ?></div>
        <h2 class="sol-modal-t notranslate" translate="no" id="solm-<?= $sid ?>-t"><?= $r['judul'] ?></h2>
        <div class="sol-modal-detail"><?= $r['detail'] ?></div>
      </div>
      <div class="sol-modal-foot">
        <span class="sol-modal-note"><?= htmlspecialchars($c('modal_note', "Let's design the right solution for your business.")) ?></span>
        <a class="sol-modal-cta" href="<?= htmlspecialchars(url('contact-us')) ?>"><?= htmlspecialchars(t('contact_us', 'Contact Us')) ?> <?= $arrow ?></a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>

  <!-- Pre-footer CTA band — dark immersive bridge into the footer. CMS-editable. -->
  <?php
  $cta_b1l = trim((string)$c('cta_btn1_label', 'Contact Us'));
  $cta_b1u = trim((string)$c('cta_btn1_url', 'contact-us'));
  $cta_b2l = trim((string)$c('cta_btn2_label', 'Explore Solutions'));
  $cta_b2u = trim((string)$c('cta_btn2_url', '#sdWrap'));
  $cta_href = function ($u) { $u = trim($u); if ($u === '' || $u === '#' || strpos($u, '#') === 0) return ($u === '' ? '#' : $u); return preg_match('#^https?:#', $u) ? $u : url(ltrim($u, '/')); };
  ?>
  <section class="sol-cta" aria-label="Contact">
    <canvas class="sol-cta-bg" id="solCtaBg" aria-hidden="true"></canvas>
    <span class="au au1" aria-hidden="true"></span><span class="au au2" aria-hidden="true"></span><span class="core" aria-hidden="true"></span>
    <div class="sol-cta-in">
      <div class="eye"><?= htmlspecialchars($c('cta_eyebrow', 'Start your transformation')) ?></div>
      <h2><?= $promo_title($c('cta_title', 'Ready to modernize your [enterprise?]')) ?></h2>
      <?php if (trim(strip_tags((string)$c('cta_text', ''))) !== '' || true): ?><p><?= htmlspecialchars($c('cta_text', "Let's design the right foundation, security, data, and AI strategy for your business — backed by a partner trusted by global technology leaders across the region.")) ?></p><?php endif; ?>
      <div class="btns">
        <?php if ($cta_b1l !== ''): ?><a class="btn btn-p" href="<?= htmlspecialchars($cta_href($cta_b1u)) ?>"><?= htmlspecialchars($cta_b1l) ?> <?= $arrow ?></a><?php endif; ?>
        <?php if ($cta_b2l !== ''): ?><a class="btn btn-g" href="<?= htmlspecialchars($cta_href($cta_b2u)) ?>"><?= htmlspecialchars($cta_b2l) ?></a><?php endif; ?>
      </div>
    </div>
  </section>
  <script src="<?= theme_asset('assets/js/solcta.js') ?>" defer></script>
</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
