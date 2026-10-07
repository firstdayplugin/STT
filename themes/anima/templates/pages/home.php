<?php
/**
 * Anima theme — Home page.
 * PROJECT RULE #1: Home uses design/master/home.html (NOT the Figma Home).
 * Static text is editable via hc('key') -> get_content('home', key) with defaults in home.registry.php
 * (§14 content registry). Header/footer live in layouts/.
 * TODO(next passes): hero slider / orbit / prism content is JS-driven (anima.js) -> inject config
 * PHP->JS (CSP-safe, §14.2); news cards -> blog module; testimonial cards -> testimonial module;
 * media (hero/portfolio bg, card images) -> uploads/ (replaces Pexels placeholders).
 */
// Home is the hero page: nav stays transparent over the hero (no 'page-inner' body class).
$seo = ['title' => get_setting('site_title', 'Sapta Tunas Teknologi — Enterprise Solution Provider')];
$anima_load_home_js = true;

// Request Proposal form moved to the Contact Us page (themes/anima/templates/pages/contact.php).

// Live data for the News & Testimonials sections (safe if no DB / preview harness).
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$Q  = function (string $sql, array $p = []) use ($db) { try { return $db ? $db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };
$home_news = $Q("SELECT b.*, (SELECT bk.nama FROM blog_kategori_rel r JOIN blog_kategori bk ON bk.id=r.kategori_id WHERE r.blog_id=b.id LIMIT 1) AS kategori FROM blog b WHERE b.status='published' ORDER BY b.created_at DESC LIMIT 6");
$home_testi = $Q("SELECT * FROM testimonial WHERE is_active=1 ORDER BY urutan, id LIMIT 8");
$hero_rows  = $Q("SELECT * FROM hero_slides WHERE is_active=1 ORDER BY urutan, id");
$hero_json  = [];
foreach ($hero_rows as $hs) {
    $vid = trim((string)($hs['video_url'] ?? ''));
    $hero_json[] = [
        'bg'    => !empty($hs['gambar']) ? uploads_url($hs['gambar']) : '',
        'video' => $vid !== '' ? (preg_match('#^https?:#', $vid) ? $vid : uploads_url($vid)) : '',
        'eye'   => (string)($hs['eyebrow'] ?? ''),
        'h'     => (string)($hs['judul'] ?? ''),
        'desc'  => (string)($hs['deskripsi'] ?? ''),
        'sub'   => (string)($hs['subtitle'] ?? ''),
    ];
}
$h0 = $hero_json[0] ?? ['bg' => '', 'eye' => '', 'h' => 'Growing The Global', 'desc' => '', 'sub' => 'Technology Industry'];

// §14.2 — data-driven animations. Cube (Solutions prism) & orbit (Our Industries)
// cards are editable + media-capable; config is injected CSP-safely via data-* attrs
// (anima.js falls back to its built-in arrays when an attribute is absent/empty).
$orbit_rows = $Q("SELECT label, judul, subtitle, gambar, warna1, warna2, url FROM industri WHERE is_active=1 ORDER BY urutan, id");
$orbit_json = [];
foreach ($orbit_rows as $r) {
    $orbit_json[] = [
        'label' => (string)($r['label'] ?? ''),
        'title' => (string)($r['judul'] ?? ''),
        'sub'   => (string)($r['subtitle'] ?? ''),
        'img'   => !empty($r['gambar']) ? uploads_url($r['gambar']) : '',
        'c1'    => (string)($r['warna1'] ?? '#0f2a54'),
        'c2'    => (string)($r['warna2'] ?? '#357be0'),
        'url'   => (string)($r['url'] ?? ''),
    ];
}
$slide_rows = $Q("SELECT eyebrow, judul, deskripsi, label, gambar, video_url, warna_dark, warna_mid, warna_accent, logos, url FROM solution_slides WHERE is_active=1 ORDER BY urutan, id");
// Connect the prism's partner logos to the SAME per-category logos shown on /solutions
// (solution_logos), keyed by each section's anchor slug. Keeps Home ↔ Solutions in sync.
$prism_logos_by_anchor = [];
foreach ($Q("SELECT id, judul FROM solutions_section WHERE is_active=1") as $sc) {
    $anc = make_slug(strip_tags((string)($sc['judul'] ?? '')));
    if ($anc === '') continue;
    $arr = [];
    foreach ($Q("SELECT gambar FROM solution_logos WHERE solution_id=? AND is_active=1 ORDER BY urutan, id", [(int)$sc['id']]) as $one) {
        $g = trim((string)($one['gambar'] ?? ''));
        if ($g === '') continue;
        $arr[] = preg_match('#^(https?:|/|data:)#', $g) ? $g : uploads_url($g);
    }
    if ($arr) $prism_logos_by_anchor[$anc] = $arr;
}
$slides_json = [];
foreach ($slide_rows as $r) {
    $logos = [];
    if (!empty($r['logos'])) {
        $dec = json_decode($r['logos'], true);
        if (is_array($dec)) {
            foreach ($dec as $lg) {
                $lg = (string) $lg;
                // A path/URL (contains a slash or file extension) is used as-is (resolving
                // uploads-relative paths); a bare token is a built-in logo key for anima.js.
                if ($lg !== '' && !preg_match('#^(https?:|/|data:)#', $lg) && str_contains($lg, '.')) { $lg = uploads_url($lg); }
                elseif ($lg !== '' && !preg_match('#^(https?:|/|data:)#', $lg) && str_contains($lg, '/')) { $lg = uploads_url($lg); }
                $logos[] = $lg;
            }
        }
    }
    // Prefer the /solutions per-category logos (matched by the slide's url anchor).
    $__anc = '';
    if (preg_match('/#(.+)$/', (string)($r['url'] ?? ''), $__m)) $__anc = $__m[1];
    if ($__anc !== '' && !empty($prism_logos_by_anchor[$__anc])) {
        $logos = $prism_logos_by_anchor[$__anc];
    }
    $slides_json[] = [
        'eyebrow' => (string)($r['eyebrow'] ?? ''),
        'h'       => (string)($r['judul'] ?? ''),
        'p'       => (string)($r['deskripsi'] ?? ''),
        'label'   => (string)($r['label'] ?? ''),
        'img'     => !empty($r['gambar']) ? uploads_url($r['gambar']) : '',
        'video'   => !empty($r['video_url']) ? (preg_match('#^https?:#', $r['video_url']) ? $r['video_url'] : uploads_url($r['video_url'])) : '',
        'dark'    => (string)($r['warna_dark'] ?? '#0a1430'),
        'mid'     => (string)($r['warna_mid'] ?? '#123a6a'),
        'accent'  => (string)($r['warna_accent'] ?? '#42a0ff'),
        'logos'   => $logos,
        'url'     => (function ($u) { $u = trim((string)$u); if ($u === '' || $u === '#') return ''; return preg_match('#^https?:#', $u) ? $u : url(ltrim($u, '/')); })($r['url'] ?? ''),
    ];
}

include theme_path('templates/layouts/header.php');
?>

<!-- ===== HERO (dark cinematic WebGL) ===== -->
<section class="v3hero" id="hero">
  <div class="v3hero-in">
    <div class="tk-stage"<?= $hero_json ? ' data-hero="' . htmlspecialchars(json_encode($hero_json), ENT_QUOTES) . '"' : '' ?>>
      <div class="tk-cur" id="tkCur">
        <div class="tk-bg" id="tkCurBg"></div>
        <div class="tk-ov"></div>
        <canvas class="tk-fx" id="tkFx" aria-hidden="true"></canvas>
        <div class="tk-copy">
          <div class="tk-eyebrow notranslate" translate="no" id="tkEye"<?= trim((string)($h0['eye'] ?? '')) === '' ? ' hidden' : '' ?>><?= htmlspecialchars((string)($h0['eye'] ?? '')) ?></div>
          <h1 class="tk-h1" id="tkH1"<?= trim((string)($h0['h'] ?? '')) === '' ? ' hidden' : '' ?>><?= htmlspecialchars((string)($h0['h'] ?? '')) ?></h1>
          <p class="tk-desc" id="tkDesc"<?= trim((string)($h0['desc'] ?? '')) === '' ? ' hidden' : '' ?>><?= htmlspecialchars((string)($h0['desc'] ?? '')) ?></p>
          <div class="tk-foot">
            <a class="tk-btn" href="<?= htmlspecialchars(url('contact-us')) ?>">Get in Touch <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
            <div class="tk-dots" id="tkDots"></div>
          </div>
        </div>
      </div>
      <div class="tk-next" id="tkNext">
        <div class="tk-bg" id="tkNextBg"></div>
        <div class="tk-ov2"></div>
        <canvas class="tk-fx" id="tkFx2" aria-hidden="true"></canvas>
      </div>
      <!-- Telkom corner masks sit OUTSIDE the image clipping containers. This is important: the SVG must be able to overlap the image edge cleanly. -->
      <div class="tk-tab" id="tkTab"></div>
      <div class="tk-navpod">
        <button class="np-btn" id="tkPrev" aria-label="Previous"><svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg></button>
        <button class="np-btn np-active" id="tkNextBtn" aria-label="Next"><svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg></button>
      </div>
    </div>
    <div class="tk-below">
      <div class="tk-below-l">
        <h2 class="tk-sub"><span class="blue" id="tkSub"><?= htmlspecialchars($h0['sub']) ?></span></h2>
        <p><?= hc('hero_subcopy') ?></p>
      </div>
      <div class="tk-discover">
        <div class="tk-disc-h"><?= hc('discover_heading') ?></div>
        <div class="tk-disc-grid">
          <a href="#portfolio"><?= hc('discover_portfolio') ?> <i>↗</i></a>
          <a href="#news"><?= hc('discover_news') ?> <i>↗</i></a>
          <a href="#solutions"><?= hc('discover_solution') ?> <i>↗</i></a>
          <a href="#why"><?= hc('discover_why') ?> <i>↗</i></a>
          <a href="#industries"><?= hc('discover_industries') ?> <i>↗</i></a>
          <a href="#testimonials"><?= hc('discover_testi') ?> <i>↗</i></a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== PORTFOLIO ===== -->
<!-- ===== OUR PORTFOLIO (scroll-driven) ===== -->
<section class="tfi" id="portfolio">
  <div class="tfi-pin">
    <div class="tfi-stage" id="tfiStage">
      <div class="tfi-media" id="tfiMedia">
        <?php for ($i = 1; $i <= 4; $i++): $pfi = aimg('home', "pf_img{$i}", ''); ?>
        <div class="tfi-img<?= $i === 1 ? ' on' : '' ?>" data-i="<?= $i - 1 ?>"<?= $pfi !== '' ? ' style="background-image:url(' . htmlspecialchars($pfi) . ')"' : '' ?>></div>
        <?php endfor; ?>
        <div class="tfi-media-ov"></div>
      </div>
      <div class="tfi-panel" id="tfiPanel">
        <div class="tfi-eyebrow"><?= hc('portfolio_eyebrow') ?></div>
        <h2 class="tfi-title"><?= hc('portfolio_title') ?></h2>
        <p class="tfi-lead"><?= hc('portfolio_lead') ?></p>
        <div class="tfi-list" id="tfiList">
          <div class="tfi-prog"><span class="tfi-prog-fill" id="tfiFill"></span></div>
          <div class="tfi-item act" data-i="0"><h3><b><?= hc('pf1_num') ?></b> <?= hc('pf1_label') ?></h3><div class="tfi-d"><?= hc('pf1_desc') ?></div></div>
          <div class="tfi-item dim" data-i="1"><h3><b><?= hc('pf2_num') ?></b> <?= hc('pf2_label') ?></h3><div class="tfi-d"><?= hc('pf2_desc') ?></div></div>
          <div class="tfi-item dim" data-i="2"><h3><b><?= hc('pf3_num') ?></b> <?= hc('pf3_label') ?></h3><div class="tfi-d"><?= hc('pf3_desc') ?></div></div>
          <div class="tfi-item dim" data-i="3"><h3><b><?= hc('pf4_num') ?></b> <?= hc('pf4_label') ?></h3><div class="tfi-d"><?= hc('pf4_desc') ?></div></div>
          </div>
      </div>
    </div>
  </div>
</section>



<!-- ===== PRISM (WebGL scroll story) ===== -->
<section class="prism" id="solutions"<?= $slides_json ? ' data-slides="' . htmlspecialchars(json_encode($slides_json), ENT_QUOTES) . '"' : '' ?>>
  <div class="stage">
    <canvas id="prismCanvas"></canvas>
    <div class="prism-hint" id="prismHint">Scroll</div>
    <div class="prism-overlay">
      <div class="prism-caps" id="prismCaps"></div>
      <div class="prism-dots" id="prismDots"></div>
      <div class="prism-logos" id="prismLogos"></div>
    </div>
  </div>
</section>

<!-- ===== STATEMENT (scroll word-reveal, pemisah Solutions & Industries) ===== -->
<section class="statement" id="statement">
  <div class="stmt-sticky">
    <div class="wrap">
      <p class="stmt" id="stmtText"><?= hc('statement_text') ?></p>
    </div>
  </div>
</section>

<!-- ===== ORBIT (Our Industries) ===== -->
<?php
// Editable orbit cards from the `industri` module (§14.2). Each card carries its own
// image + gradient via data-* attributes; anima.js positions them and paints the media
// CSP-safely (no inline styles). Falls back to hc('ind1..8') labels if the table is empty.
$orbit_cards = $orbit_json;
if (!$orbit_cards) { for ($i = 1; $i <= 8; $i++) { $orbit_cards[] = ['label' => hc('ind' . $i, true), 'title' => '', 'sub' => '', 'img' => '', 'c1' => '', 'c2' => '', 'url' => '']; } }
?>
<section class="ind2" id="industries"<?= $orbit_json ? ' data-orbit="' . htmlspecialchars(json_encode($orbit_json), ENT_QUOTES) . '"' : '' ?>>
  <div class="ind2-cards" id="ind2cards">
    <?php foreach ($orbit_cards as $c): ?>
      <a class="ind2-card" href="<?= htmlspecialchars($c['url'] !== '' ? url(ltrim($c['url'], '/')) : '#') ?>"
         <?php if ($c['img'] !== ''): ?>data-img="<?= htmlspecialchars($c['img']) ?>"<?php endif; ?>
         <?php if ($c['c1'] !== ''): ?>data-c1="<?= htmlspecialchars($c['c1']) ?>" data-c2="<?= htmlspecialchars($c['c2']) ?>"<?php endif; ?>>
        <span class="ex">EXPLORE →</span><span class="lbl notranslate" translate="no"><?= htmlspecialchars($c['label']) ?></span></a>
    <?php endforeach; ?>
  </div>
  <div class="ind2-center">
    <div class="ind2-eye"><?= hc('industries_eyebrow') ?></div>
    <h2 class="ind2-word"><?= hc('industries_title') ?></h2>
    <div class="ind2-sub"><?= hc('industries_sub') ?></div>
  </div>
</section>



<!-- ===== NEWS ===== -->
<section class="news" id="news">
  <div class="news-aurora"><i class="n1"></i><i class="n2"></i></div>
  <div class="wrap news-in">
    <div class="news-intro">
      <h2><?= hc('news_heading') ?></h2>
      <div class="news-line"></div>
      <p><?= hc('news_intro') ?></p>
      <div class="news-nav">
        <button class="nprev" aria-label="Previous"><svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg></button>
        <button class="nnext" aria-label="Next"><svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg></button>
      </div>
    </div>
    <div class="news-viewport">
      <div class="news-track" id="newsTrack">
        <?php if (!empty($home_news)): foreach ($home_news as $np): $nimg = !empty($np['gambar_utama']) ? uploads_url($np['gambar_utama']) : ''; ?>
        <article class="ncard">
          <div class="ncard-img">
            <?php if ($nimg): ?><img src="<?= htmlspecialchars($nimg) ?>" data-fallback="bg" alt="" loading="lazy" decoding="async"><?php endif; ?>
            <div class="ncard-ribbon"><span class="date"><?= htmlspecialchars(date('F j, Y', strtotime($np['created_at']))) ?></span><?php if (!empty($np['kategori'])): ?><span class="cat"><?= htmlspecialchars($np['kategori']) ?></span><?php endif; ?></div>
          </div>
          <div class="ncard-body">
            <h3><a href="<?= url('blog/' . $np['slug']) ?>"><?= htmlspecialchars($np['judul']) ?></a></h3>
            <p><?= htmlspecialchars($np['excerpt'] ?? '') ?></p>
            <a class="read" href="<?= url('blog/' . $np['slug']) ?>">Read More <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
          </div>
        </article>
        <?php endforeach; else: ?>
        <div class="news-empty"><?= hc('news_empty') ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- ===== WHAT SETS US APART (scale.com/enterprise-style coverflow) ===== -->
<section class="wsa" id="why">
  <div class="wsa-head">
    <div class="wsa-eye"><?= hc('why_eyebrow') ?></div>
    <h2><?= hc('why_title') ?></h2>
    <p><?= hc('why_intro') ?></p>
  </div>
  <div class="wsa-stage">
    <button type="button" class="wsa-arw prev" id="wsaPrev" aria-label="<?= htmlspecialchars(t('prev', 'Sebelumnya')) ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
    </button>
    <div class="wsa-viewport">
      <div class="wsa-track" id="wsaTrack">
        <?php
          $why_base = [];
          for ($i = 1; $i <= 4; $i++) {
            $why_base[] = ['t'=>hc("why{$i}_title"), 's'=>hc("why{$i}_sub"), 'img'=>aimg('home', "why{$i}_img", ''), 'cat'=>hc("why{$i}_cat")];
          }
          // Duplicated so the coverflow always shows 5 symmetric cards (2 + centre + 2)
          // and loops seamlessly, matching scale.com/enterprise.
          $why_items = array_merge($why_base, $why_base, $why_base);
          foreach ($why_items as $k => $w): ?>
        <article class="wsa-card<?= $k === 0 ? ' is-active' : '' ?>" data-i="<?= $k ?>">
          <div class="wsa-media"><?php if ($w['img'] !== ''): ?><img src="<?= htmlspecialchars($w['img']) ?>" alt="" loading="lazy" decoding="async" data-fallback="bg"><?php endif; ?></div>
          <?php if (trim(strip_tags((string)$w['cat'])) !== ''): ?><span class="wsa-cat"><?= $w['cat'] ?></span><?php endif; ?>
          <div class="wsa-cap">
            <h3 class="wsa-t"><?= $w['t'] ?></h3>
            <?php if (trim(strip_tags((string)$w['s'])) !== ''): ?><p class="wsa-s"><?= $w['s'] ?></p><?php endif; ?>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
    <button type="button" class="wsa-arw next" id="wsaNext" aria-label="<?= htmlspecialchars(t('next', 'Berikutnya')) ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </button>
  </div>
</section>

<!-- ===== WHAT THEY SAY (OpenAI-style) ===== -->
<?php
/* Resolve a testimonial video URL into a playable shape: mp4 file, or YouTube/Vimeo embed. */
if (!function_exists('tsc_video')) {
  function tsc_video(string $u): array {
    $u = trim($u);
    if ($u === '') return ['kind'=>'', 'embed'=>'', 'mp4'=>''];
    if (preg_match('~(?:youtube\.com/.*[?&]v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{6,})~', $u, $m))
      return ['kind'=>'embed', 'embed'=>'https://www.youtube.com/embed/'.$m[1].'?rel=0', 'mp4'=>''];
    if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $u, $m))
      return ['kind'=>'embed', 'embed'=>'https://player.vimeo.com/video/'.$m[1], 'mp4'=>''];
    $abs = preg_match('#^(https?:|/)#', $u) ? $u : uploads_url($u);
    if (preg_match('~\.(mp4|webm|ogg|mov)(\?|$)~i', $u)) return ['kind'=>'mp4', 'embed'=>'', 'mp4'=>$abs];
    return ['kind'=>'embed', 'embed'=>$abs, 'mp4'=>''];
  }
}
?>
<section class="tsc" id="testimonials">
  <div class="tsc-head">
    <div class="tsc-head-l">
      <div class="tsc-eye"><?= hc('testi_eyebrow') ?></div>
      <h2 class="tsc-title"><?= hc('testi_title') ?></h2>
    </div>
    <div class="tsc-nav">
      <button type="button" class="tsc-arw" data-tsc-prev aria-label="<?= htmlspecialchars(t('prev','Sebelumnya')) ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
      <button type="button" class="tsc-arw" data-tsc-next aria-label="<?= htmlspecialchars(t('next','Berikutnya')) ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg></button>
    </div>
  </div>

  <?php if (!empty($home_testi)): ?>
  <div class="tsc-viewport">
    <div class="tsc-track" id="tscTrack">
      <?php foreach ($home_testi as $k => $t):
        $tv    = (($t['tipe'] ?? 'text') === 'video');
        $vinfo = tsc_video((string)($t['video_url'] ?? ''));
        $hasV  = $tv && $vinfo['kind'] !== '';
        $poster= !empty($t['video_poster']) ? uploads_url($t['video_poster']) : '';
        $bg    = !empty($t['bg_image']) ? uploads_url($t['bg_image']) : '';
        $av    = !empty($t['foto']) ? uploads_url($t['foto']) : '';
        $role  = trim(($t['jabatan'] ?? '') . (!empty($t['perusahaan']) ? ', ' . $t['perusahaan'] : ''));
        $stat  = trim((string)($t['stat_value'] ?? '')); $statcap = trim((string)($t['stat_label'] ?? ''));
        $meta  = array_filter([
          'Industry'     => (string)($t['m_industry'] ?? ''),
          'Company size' => (string)($t['m_size'] ?? ''),
          'Product'      => (string)($t['m_product'] ?? ''),
          'Location'     => (string)($t['m_location'] ?? ''),
        ], fn($v) => trim($v) !== '');
      ?>
      <article class="tsc-card <?= $hasV ? 'is-video' : 'is-text' ?>" data-i="<?= $k ?>"
        <?= $hasV && $vinfo['kind']==='mp4'   ? 'data-vmp4="'.htmlspecialchars($vinfo['mp4']).'"' : '' ?>
        <?= $hasV && $vinfo['kind']==='embed' ? 'data-vembed="'.htmlspecialchars($vinfo['embed']).'"' : '' ?>>
        <div class="tsc-body">
          <p class="tsc-q">&ldquo;<?= htmlspecialchars($t['isi']) ?>&rdquo;</p>
          <?php if ($stat !== ''): ?><div class="tsc-stat"><?= htmlspecialchars($stat) ?></div><?php if ($statcap !== ''): ?><div class="tsc-statcap"><?= htmlspecialchars($statcap) ?></div><?php endif; endif; ?>
          <?php if ($meta): ?><div class="tsc-meta"><?php foreach ($meta as $mk => $mv): ?><b><?= htmlspecialchars($mk) ?>:</b><span><?= htmlspecialchars($mv) ?></span><?php endforeach; ?></div><?php endif; ?>
          <div class="tsc-person">
            <span class="tsc-av"><?php if ($av): ?><img src="<?= htmlspecialchars($av) ?>" data-fallback="remove" alt=""><?php endif; ?></span>
            <span><span class="tsc-nm notranslate" translate="no"><?= htmlspecialchars($t['nama']) ?></span><span class="tsc-rl"><?= htmlspecialchars($role) ?></span></span>
          </div>
        </div>
        <div class="tsc-media">
          <?php if ($hasV && $vinfo['kind']==='mp4'): ?>
            <video class="tsc-vid" src="<?= htmlspecialchars($vinfo['mp4']) ?>" muted loop playsinline preload="metadata"<?= $poster ? ' poster="'.htmlspecialchars($poster).'"' : '' ?>></video>
          <?php elseif ($hasV): ?>
            <div class="tsc-poster"<?= $poster ? ' style="background-image:url('.htmlspecialchars($poster).')"' : '' ?>></div>
          <?php else: ?>
            <div class="tsc-abs"<?= $bg ? ' style="background-image:url('.htmlspecialchars($bg).')"' : '' ?>></div>
          <?php endif; ?>
          <?php if ($hasV): ?><button type="button" class="tsc-play" data-tsc-play aria-label="Play video"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></button><?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="tsc-dots" id="tscDots"></div>
  <?php else: ?>
    <div class="wrap"><div class="tst-empty"><?= hc('testi_empty') ?></div></div>
  <?php endif; ?>
</section>

<?php include theme_path('templates/layouts/footer.php'); ?>
