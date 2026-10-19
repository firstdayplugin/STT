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
  <div class="sh-stack">
    <section class="sh" id="solHero" aria-label="<?= htmlspecialchars(strip_tags($c('title', 'Our Solutions'))) ?>">
      <canvas class="sh-net" id="solNet" aria-hidden="true"></canvas>
      <div class="sh-copy" id="solCopy">
        <?php if (trim((string)$c('hero_eyebrow', '')) !== ''): ?><div class="sh-eyebrow"><?= htmlspecialchars($c('hero_eyebrow', 'Enterprise Solution Provider')) ?></div><?php endif; ?>
        <h1 class="sh-title"><?= $hero_title ?></h1>
      </div>
      <div class="sh-arena" id="solArena">
        <div class="sh-core" id="solCore"><span class="orb"></span><span class="ring"></span><span class="ring"></span><span class="ring"></span><div class="lbl"><img class="mk" src="<?= htmlspecialchars($mark_glow) ?>" alt="STT"></div></div>
        <?php foreach ($sol_nodes as $nd): ?>
          <a class="sh-node" href="#<?= htmlspecialchars($nd['slug']) ?>">
            <span class="ic"><svg viewBox="0 0 24 24"><?= $nd['ic'] ?></svg></span>
            <span class="nm"><?= htmlspecialchars($nd['nm']) ?><?php if ($nd['sub'] !== ''): ?><small><?= htmlspecialchars($nd['sub']) ?></small><?php endif; ?></span>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="sh-scroll" aria-hidden="true"><i></i>scroll</div>
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
  </div><!-- /.sh-stack -->
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
        <h2 translate="no" class="notranslate"><?= $judul ?></h2>
        <?php if ($solusi !== ''): ?>
          <div class="sol-label"><?= htmlspecialchars($lbl_solution) ?></div>
          <p class="sol-desc<?= $red ? ' red' : '' ?>"><?= nl2br(htmlspecialchars($solusi)) ?></p>
        <?php endif; ?>
        <?php $logos = $Q("SELECT gambar, nama FROM solution_logos WHERE solution_id=? AND is_active=1 ORDER BY urutan, id", [$sid]); ?>
        <div class="sol-label"><?= htmlspecialchars($lbl_partner) ?></div>
        <?php if ($logos): ?>
          <div class="sol-partners sol-partners-grid">
            <?php foreach ($logos as $lg): $lsrc = $img((string)($lg['gambar'] ?? '')); if ($lsrc === '') continue; ?>
              <span class="sol-plogo sol-plogo-img"><img src="<?= htmlspecialchars($lsrc) ?>" alt="<?= htmlspecialchars((string)($lg['nama'] ?? 'Partner')) ?>" loading="lazy" data-fallback="remove"></span>
            <?php endforeach; ?>
          </div>
        <?php elseif ($ptn): ?>
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
