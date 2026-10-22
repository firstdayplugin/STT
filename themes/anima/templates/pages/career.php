<?php
/**
 * Anima — Career listing (route: /career). "Careers Hub": an energetic dark hero
 * (badge + headline + stat strip + CTAs) over an animated network, a "Why join us"
 * values row, then a modern job board. Search + Role + Location filters run
 * CLIENT-SIDE (no reload, no jump). All copy is CMS-editable (get_content('career',…)).
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$Q  = function (string $sql, array $p = []) use ($db) { try { return $db ? $db->fetchAll($sql, $p) : []; } catch (\Throwable $e) { return []; } };

$jobs  = $Q("SELECT * FROM career WHERE is_active=1 AND (deadline IS NULL OR deadline >= CURDATE()) ORDER BY urutan, created_at DESC");
$roles = $Q("SELECT role, COUNT(*) c FROM career WHERE is_active=1 AND (deadline IS NULL OR deadline >= CURDATE()) AND role IS NOT NULL AND role<>'' GROUP BY role ORDER BY role");
$locs  = $Q("SELECT lokasi, COUNT(*) c FROM career WHERE is_active=1 AND (deadline IS NULL OR deadline >= CURDATE()) AND lokasi IS NOT NULL AND lokasi<>'' GROUP BY lokasi ORDER BY lokasi");

$c    = fn(string $k, string $d = '') => get_content('career', $k, $d);
$imgu = fn($p) => $p ? (preg_match('#^(https?:|/|data:)#', $p) ? $p : uploads_url($p)) : '';
$fmt  = fn($d) => $d ? date('M j, Y', strtotime($d)) : '';
$txt  = fn($s) => htmlspecialchars(trim(html_entity_decode(strip_tags((string)$s), ENT_QUOTES | ENT_HTML5, 'UTF-8')), ENT_QUOTES, 'UTF-8');

$lead_default = "Be part of a team that delivers innovative technology solutions to the challenges of modern businesses. At Sapta Tunas Teknologi, you'll work in a collaborative, learning-driven environment with real opportunities for personal and professional growth.";
// Deep-link filters (optional) — JS keeps them in sync without reloading.
$irole = isset($_GET['role'])   ? trim((string)$_GET['role'])   : '';
$iloc  = isset($_GET['lokasi']) ? trim((string)$_GET['lokasi']) : '';
$iq    = isset($_GET['q'])      ? trim((string)$_GET['q'])      : '';

$n_roles = count($roles);
$n_cities = count($locs);

$seo = ['title' => 'Career — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($c('lead', $lead_default)), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
$cr_hero = $imgu($c('team_image', 'career/team.jpg'));

// Values ("Why join us") — editable via content, sensible defaults. Icons are fixed.
$VAL_IC = [
  '<path d="M3 17l6-6 4 4 7-7"/><path d="M14 7h7v7"/>',
  '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r="1"/>',
  '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/>',
  '<path d="M12 3v18"/><path d="M5 7h14M7 7l-2.5 6a3 3 0 0 0 5 0zM17 7l-2.5 6a3 3 0 0 0 5 0z"/>',
];
$VALS = [
  [$c('val1_title', 'Growth & Learning'),   $c('val1_text', 'Mentorship, certifications, and a real learning budget. We help you level up fast.')],
  [$c('val2_title', 'Real Impact'),         $c('val2_text', 'Ship work that matters for enterprise clients across Indonesia from day one.')],
  [$c('val3_title', 'Collaborative Team'),  $c('val3_text', 'Supportive, no-ego engineers and experts who love solving hard problems together.')],
  [$c('val4_title', 'Flexibility & Balance'), $c('val4_text', 'A culture that respects your time, wellbeing, and ways of working.')],
];
$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
$icCap  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10L12 5 2 10l10 5 10-5z"/><path d="M6 12v5c0 1 2.7 2.5 6 2.5s6-1.5 6-2.5v-5"/></svg>';
$icBrief = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>';
$icCal   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>';
?>
<main class="page-body cx-page" data-irole="<?= htmlspecialchars($irole) ?>" data-iloc="<?= htmlspecialchars($iloc) ?>" data-iq="<?= htmlspecialchars($iq) ?>">

  <!-- Energetic dark hero -->
  <section class="cx-hero">
    <?php if ($cr_hero !== ''): ?><div class="cx-hero-photo" style="background-image:url(<?= htmlspecialchars($cr_hero) ?>)" aria-hidden="true"></div><?php endif; ?>
    <canvas class="cx-net" id="cxNet" aria-hidden="true"></canvas>
    <span class="cx-au cx-au1" aria-hidden="true"></span><span class="cx-au cx-au2" aria-hidden="true"></span>
    <div class="cx-hero-in">
      <span class="cx-badge"><span class="dot"></span> <?= $c('badge', "We&rsquo;re hiring") ?></span>
      <h1><?= $txt($c('title', 'Build the future with us.')) ?><br><span class="g"><?= $txt($c('title2', 'Grow your career at Sapta Tunas Teknologi')) ?></span></h1>
      <?php $lead = trim(strip_tags((string)$c('lead', $lead_default))); if ($lead !== ''): ?><p><?= $txt($c('lead', $lead_default)) ?></p><?php endif; ?>
      <div class="cx-cta">
        <a class="cx-btn cx-btn-p" href="#cx-jobs"><?= htmlspecialchars(t('view_open_roles', 'View open roles')) ?> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg></a>
        <a class="cx-btn cx-btn-g" href="<?= htmlspecialchars(url('contact-us')) ?>"><?= htmlspecialchars(t('talk_to_team', 'Talk to our team')) ?></a>
      </div>
      <div class="cx-stats">
        <div class="cx-stat"><b data-c="<?= (int)count($jobs) ?>">0</b><span><?= htmlspecialchars(t('open_roles', 'Open roles')) ?></span></div>
        <div class="cx-stat"><b data-c="<?= (int)$n_cities ?>">0</b><span><?= htmlspecialchars(t('cities', 'Cities')) ?></span></div>
        <div class="cx-stat"><b data-c="<?= (int)$c('stat3_num', '100') ?>" data-suf="<?= htmlspecialchars($c('stat3_suffix', '%')) ?>">0</b><span><?= $txt($c('stat3_label', 'Learning-driven')) ?></span></div>
      </div>
    </div>
  </section>

  <div class="cx-body">
    <!-- Why join us -->
    <section class="cx-wrap">
      <div class="reveal"><div class="cx-sec-k"><?= $txt($c('why_kicker', 'Why Sapta Tunas')) ?></div>
        <h2 class="cx-sec-h"><?php $wt = trim((string)$c('why_title', '')); echo $wt !== '' ? $wt : 'A place to <span class="g">grow</span>, not just work'; ?></h2>
        <?php $ws = trim(strip_tags((string)$c('why_sub', 'We invest in people. Here is what you can expect when you build your career with us.'))); if ($ws !== ''): ?><p class="cx-sec-sub"><?= $txt($c('why_sub', 'We invest in people. Here is what you can expect when you build your career with us.')) ?></p><?php endif; ?></div>
      <div class="cx-vals">
        <?php foreach ($VALS as $i => $v): ?>
        <div class="cx-val reveal">
          <div class="cx-val-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $VAL_IC[$i] ?></svg></div>
          <h3><?= $txt($v[0]) ?></h3><p><?= $txt($v[1]) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Open roles -->
    <section class="cx-wrap cx-jobs" id="cx-jobs">
      <div class="reveal"><div class="cx-sec-k"><?= htmlspecialchars(t('open_positions', 'Open Positions')) ?></div>
        <h2 class="cx-sec-h"><?php $jt = trim((string)$c('jobs_title', '')); echo $jt !== '' ? $jt : 'Find your <span class="g">next role</span>'; ?></h2></div>

      <div class="cx-bar reveal">
        <label class="cx-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
          <input type="search" id="cxSearch" value="<?= htmlspecialchars($iq) ?>" placeholder="<?= htmlspecialchars(t('search_jobs', 'Search roles, skills, keywords')) ?>" autocomplete="off"></label>
        <div class="cx-dd-wrap">
          <label class="cx-dd"><select id="cxRole" aria-label="<?= htmlspecialchars(t('role', 'Role')) ?>">
            <option value=""><?= htmlspecialchars(t('all_roles', 'All roles')) ?></option>
            <?php foreach ($roles as $r): ?><option value="<?= htmlspecialchars($r['role']) ?>"<?= $irole === $r['role'] ? ' selected' : '' ?>><?= htmlspecialchars($r['role']) ?> (<?= (int)$r['c'] ?>)</option><?php endforeach; ?>
          </select><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></label>
          <label class="cx-dd"><select id="cxLoc" aria-label="<?= htmlspecialchars(t('location', 'Location')) ?>">
            <option value=""><?= htmlspecialchars(t('all_locations', 'All locations')) ?></option>
            <?php foreach ($locs as $l): ?><option value="<?= htmlspecialchars($l['lokasi']) ?>"<?= $iloc === $l['lokasi'] ? ' selected' : '' ?>><?= htmlspecialchars($l['lokasi']) ?> (<?= (int)$l['c'] ?>)</option><?php endforeach; ?>
          </select><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></label>
        </div>
      </div>

      <div class="cx-jlist" id="cxList">
        <?php foreach ($jobs as $j):
          $title = tr_field('career', (int)$j['id'], 'judul', $j['judul']);
          $jenj  = tr_field('career', (int)$j['id'], 'jenjang', $j['jenjang'] ?? '');
          $s = strtolower(trim(html_entity_decode(strip_tags($title . ' ' . ($j['role'] ?? '') . ' ' . ($j['lokasi'] ?? '') . ' ' . $jenj . ' ' . ($j['pengalaman'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        ?>
        <a class="cx-job reveal" href="<?= url('career/' . $j['slug']) ?>" data-role="<?= htmlspecialchars((string)($j['role'] ?? '')) ?>" data-loc="<?= htmlspecialchars((string)($j['lokasi'] ?? '')) ?>" data-s="<?= htmlspecialchars($s, ENT_QUOTES) ?>">
          <div class="cx-job-main">
            <div class="cx-job-chips">
              <?php if (!empty($j['role'])): ?><span class="cx-jchip role"><?= $txt($j['role']) ?></span><?php endif; ?>
              <?php if (!empty($j['lokasi'])): ?><span class="cx-jchip loc"><?= $txt($j['lokasi']) ?></span><?php endif; ?>
            </div>
            <h3><?= $txt($title) ?></h3>
            <div class="cx-job-meta">
              <?php if (trim((string)$jenj) !== ''): ?><span><?= $icCap ?><?= $txt($jenj) ?></span><?php endif; ?>
              <?php if (trim((string)($j['pengalaman'] ?? '')) !== ''): ?><span><?= $icBrief ?><?= $txt($j['pengalaman']) ?></span><?php endif; ?>
              <?php if (!empty($j['deadline'])): ?><span><?= $icCal ?><?= htmlspecialchars(t('until', 'Until') . ' ' . $fmt($j['deadline'])) ?></span><?php endif; ?>
            </div>
          </div>
          <div class="cx-job-side"><span class="cx-apply"><?= htmlspecialchars(t('apply_now', 'Apply now')) ?> <?= $arrow ?></span></div>
        </a>
        <?php endforeach; ?>
      </div>
      <p class="cx-empty" id="cxEmpty"<?= $jobs ? ' hidden' : '' ?>><?= htmlspecialchars(t('no_jobs', 'No roles match your filters. Try clearing them.')) ?></p>
    </section>

    <!-- CTA finale -->
    <?php
    $cta_title = $c('cta_title', 'Don&rsquo;t see your [perfect role?]');
    $cta_text  = $c('cta_text', "We're always looking for great people. Send us your CV and we'll reach out when something fits.");
    $cta_label = $c('cta_label', 'Send your CV');
    ?>
    <section class="cf" aria-label="Apply">
      <canvas class="cf-bg" id="cfBg" aria-hidden="true"></canvas>
      <span class="au au1" aria-hidden="true"></span><span class="au au2" aria-hidden="true"></span><span class="au au3" aria-hidden="true"></span>
      <span class="ring" aria-hidden="true"></span>
      <div class="cf-in">
        <h2 class="cf-h" data-text="<?= htmlspecialchars(html_entity_decode($cta_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>"></h2>
        <p class="cf-p"><?= $txt($cta_text) ?></p>
        <div class="cf-btns"><a class="btn btn-p" href="<?= htmlspecialchars(url('contact-us')) ?>"><?= $txt($cta_label) ?> <?= $arrow ?></a></div>
      </div>
    </section>
  </div>

</main>
<script src="<?= theme_asset('assets/js/careerhub.js') ?>" defer></script>
<script src="<?= theme_asset('assets/js/ctafinale.js') ?>" defer></script>
<?php include theme_path('templates/layouts/footer.php'); ?>
