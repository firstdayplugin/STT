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

// --- Request Proposal submit (PRG): honeypot + Turnstile, then save to `pesan`. ---
$rp_err = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['_form'] ?? '') === 'proposal') {
    $nama     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $company  = trim($_POST['company'] ?? '');
    $jobrole  = trim($_POST['job_role'] ?? '');
    $industry = trim($_POST['industry'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $solution = trim($_POST['solution'] ?? '');
    $msg      = trim($_POST['message'] ?? '');
    $honey    = trim($_POST['website'] ?? '');
    if ($honey !== '') {
        redirect(url('') . '?sent=proposal#contact');            // silent drop for bots
    } elseif ($nama === '') {
        $rp_err = 'Nama wajib diisi.';
    } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $rp_err = 'Masukkan alamat email yang valid.';
    } elseif (!turnstile_verify($_POST['cf-turnstile-response'] ?? null)) {
        $rp_err = 'Verifikasi anti-spam gagal. Silakan coba lagi.';
    } else {
        // Fold the extra fields into a structured message so nothing is lost
        // (pesan table stores company in `perusahaan`; the rest go in the body).
        $detail = [];
        if ($jobrole  !== '') $detail[] = 'Job Role: '   . $jobrole;
        if ($industry !== '') $detail[] = 'Industries: ' . $industry;
        if ($location !== '') $detail[] = 'Locations: '  . $location;
        if ($solution !== '') $detail[] = 'Solutions: '  . $solution;
        $pesan_full = $msg;
        if ($detail) $pesan_full = ($msg !== '' ? $msg . "\n\n" : '') . "— Detail —\n" . implode("\n", $detail);
        $lead = ['nama' => $nama, 'email' => $email, 'telepon' => $phone, 'perusahaan' => $company,
                 'subjek' => 'Request Proposal' . ($solution !== '' ? ' — ' . $solution : ''),
                 'pesan' => $pesan_full, 'halaman' => 'home'];
        save_lead('proposal', $lead);
        notify_lead('proposal', $lead);
        redirect(url('') . '?sent=proposal#contact');
    }
}
$rp_sent = (($_GET['sent'] ?? '') === 'proposal');

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
        <div class="tk-copy">
          <div class="tk-eyebrow" id="tkEye"<?= trim((string)($h0['eye'] ?? '')) === '' ? ' hidden' : '' ?>><?= htmlspecialchars((string)($h0['eye'] ?? '')) ?></div>
          <h1 class="tk-h1" id="tkH1"<?= trim((string)($h0['h'] ?? '')) === '' ? ' hidden' : '' ?>><?= htmlspecialchars((string)($h0['h'] ?? '')) ?></h1>
          <p class="tk-desc" id="tkDesc"<?= trim((string)($h0['desc'] ?? '')) === '' ? ' hidden' : '' ?>><?= htmlspecialchars((string)($h0['desc'] ?? '')) ?></p>
          <div class="tk-foot">
            <a class="tk-btn" href="#contact">Get in Touch <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
            <div class="tk-dots" id="tkDots"></div>
          </div>
        </div>
      </div>
      <div class="tk-next" id="tkNext">
        <div class="tk-bg" id="tkNextBg"></div>
        <div class="tk-ov2"></div>
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
        <span class="ex">EXPLORE →</span><span class="lbl"><?= htmlspecialchars($c['label']) ?></span></a>
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

<!-- ===== WHY US ===== -->
<section class="why2" id="why">
  <div class="why2-head">
    <div class="why2-eye"><?= hc('why_eyebrow') ?></div>
    <h2><?= hc('why_title') ?></h2>
    <p><?= hc('why_intro') ?></p>
  </div>
  <div class="whyloop">
    <div class="whyloop-track" id="whyTrack">
        <?php
          $why_items = [];
          for ($i = 1; $i <= 4; $i++) {
            $why_items[] = ['n'=>$i, 't'=>hc("why{$i}_title"), 's'=>hc("why{$i}_sub"), 'img'=>aimg('home', "why{$i}_img", '')];
          }
          // Rendered twice for the seamless marquee loop.
          foreach (array_merge($why_items, $why_items) as $w): ?>
        <div class="wl-card">
          <div class="wl-img"><?php if ($w['img'] !== ''): ?><img src="<?= htmlspecialchars($w['img']) ?>" data-fallback="remove" alt="" loading="lazy" decoding="async"><?php endif; ?></div>
          <div class="wl-body"><span class="wl-num"><?= (int)$w['n'] ?></span><div class="wl-t"><?= $w['t'] ?></div><div class="wl-s"><?= $w['s'] ?></div></div>
        </div>
        <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===== WHAT THEY SAY (OpenAI-style) ===== -->
<section class="testi" id="testimonials">
  <div class="wrap">
    <div class="testi-head">
      <div class="testi-eye"><?= hc('testi_eyebrow') ?></div>
      <h2><?= hc('testi_title') ?></h2>
      <p><?= hc('testi_intro') ?></p>
    </div>
    <div class="tst-grid">
      <?php if (!empty($home_testi)): foreach ($home_testi as $t):
        $tv = (($t['tipe'] ?? 'text') === 'video');
        $tav = !empty($t['foto']) ? uploads_url($t['foto']) : '';
        $trole = trim(($t['jabatan'] ?? '') . (!empty($t['perusahaan']) ? ', ' . $t['perusahaan'] : ''));
        $thref = url('testimonial/' . (!empty($t['slug']) ? $t['slug'] : $t['id'])); ?>
      <a class="tcard" href="<?= htmlspecialchars($thref) ?>">
        <span class="tbadge <?= $tv ? 'video' : 'text' ?>"><?php if ($tv): ?><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z" fill="currentColor"/></svg>Video<?php else: ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 8h10M7 12h10M7 16h6"/></svg>Text<?php endif; ?></span>
        <p class="quote">&ldquo;<?= htmlspecialchars($t['isi']) ?>&rdquo;</p>
        <div class="person">
          <div class="tperson-av"><?php if ($tav): ?><img src="<?= htmlspecialchars($tav) ?>" data-fallback="remove" alt=""><?php endif; ?><?php if ($tv): ?><span class="play"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z" fill="#fff"/></svg></span><?php endif; ?></div>
          <div><div class="pname"><?= htmlspecialchars($t['nama']) ?></div><div class="prole"><?= htmlspecialchars($trole) ?></div></div>
        </div>
        <span class="watch"><?= $tv ? 'Watch story' : 'Read story' ?> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </a>
      <?php endforeach; else: ?>
      <div class="tst-empty"><?= hc('testi_empty') ?></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="contact-sec" id="contactSection">
  <div class="wrap">
    <div class="form" id="contact">
      <div class="form-eyebrow"><?= hc('contact_eyebrow') ?></div>
      <h3><?= hc('contact_title') ?></h3>
      <p><?= hc('contact_intro') ?></p>
      <?php if ($rp_sent): ?>
        <div class="form-alert ok">Terima kasih! Permintaan Anda sudah kami terima. Tim kami akan segera menghubungi Anda.</div>
      <?php elseif ($rp_err !== ''): ?>
        <div class="form-alert err"><?= htmlspecialchars($rp_err) ?></div>
      <?php endif; ?>
      <form method="post" action="<?= htmlspecialchars(url('') . '#contact') ?>" novalidate>
        <input type="hidden" name="_form" value="proposal">
        <div class="form-hp" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
        <?php
          $rp_solutions = ['Modernize Infrastructure', 'Cybersecurity', 'Data Management',
                           'Artificial Intelligence (AI)', 'AI Platform & Applications', 'Other'];
        ?>
        <div class="field full"><label for="nm">Full Name</label>
          <div class="field-wrap"><svg class="fic" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <input id="nm" name="name" type="text" placeholder="Your full name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"></div></div>
        <div class="form-grid">
          <div class="field"><label for="em">Email</label>
            <div class="field-wrap"><svg class="fic" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
            <input id="em" name="email" type="email" placeholder="you@company.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></div></div>
          <div class="field"><label for="ph">Mobile Phone</label>
            <div class="field-wrap"><svg class="fic" viewBox="0 0 24 24"><path d="M5 4h4l2 5-3 2a11 11 0 005 5l2-3 5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z"/></svg>
            <input id="ph" name="phone" type="tel" placeholder="+62 8xx-xxxx-xxxx" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"></div></div>
          <div class="field"><label for="co">Company / Organization</label>
            <div class="field-wrap"><svg class="fic" viewBox="0 0 24 24"><path d="M3 21h18M5 21V7l7-4 7 4v14"/><path d="M9 9h.01M15 9h.01M9 13h.01M15 13h.01M9 17h.01M15 17h.01"/></svg>
            <input id="co" name="company" type="text" placeholder="Your company / organization" value="<?= htmlspecialchars($_POST['company'] ?? '') ?>"></div></div>
          <div class="field"><label for="jr">Job Role</label>
            <div class="field-wrap"><svg class="fic" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
            <input id="jr" name="job_role" type="text" placeholder="e.g. IT Manager" value="<?= htmlspecialchars($_POST['job_role'] ?? '') ?>"></div></div>
          <div class="field"><label for="ind">Industries</label>
            <div class="field-wrap"><svg class="fic" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            <input id="ind" name="industry" type="text" placeholder="e.g. Financial Services" value="<?= htmlspecialchars($_POST['industry'] ?? '') ?>"></div></div>
          <div class="field"><label for="loc">Locations</label>
            <div class="field-wrap"><svg class="fic" viewBox="0 0 24 24"><path d="M12 21s-7-5.2-7-11a7 7 0 0114 0c0 5.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
            <input id="loc" name="location" type="text" placeholder="e.g. Jakarta, Indonesia" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>"></div></div>
        </div>
        <div class="field full"><label for="sol">Select Solutions</label>
          <div class="field-wrap"><svg class="fic" viewBox="0 0 24 24"><path d="M12 3l9 5-9 5-9-5 9-5zM3 12l9 5 9-5M3 16l9 5 9-5"/></svg>
          <select id="sol" name="solution" class="field-select">
            <option value="" <?= empty($_POST['solution']) ? 'selected' : '' ?>>Select Solutions</option>
            <?php foreach ($rp_solutions as $__sol): ?>
              <option value="<?= htmlspecialchars($__sol) ?>"<?= (($_POST['solution'] ?? '') === $__sol) ? ' selected' : '' ?>><?= htmlspecialchars($__sol) ?></option>
            <?php endforeach; ?>
          </select></div></div>
        <div class="field full"><label for="ms">Message</label>
          <textarea id="ms" name="message" placeholder="Please type your request solution / product here!"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea></div>
        <?php if (turnstile_enabled()): ?><div class="field full"><?= turnstile_widget() ?></div><?php endif; ?>
        <button class="form-submit" type="submit">Submit <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
      </form>
    </div>
  </div>
</section>



<?= turnstile_script() ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
