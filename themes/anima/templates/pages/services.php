<?php
/**
 * Anima — Services LANDING (route: /services).
 * Mockup-aligned content rendered in the SITE's own design language (Space Grotesk/
 * Inter, site tokens — not the mockup font). ADDITIVE on the original design: the
 * animated hero lifecycle ring + the scroll-scrubbed "What we deliver" deck (01/02)
 * + the light closing finale are PRESERVED; stats, trusted-by, two-areas and bundle
 * are added. New-section copy uses get_content('services', …) so it is CMS-editable
 * and EN/ID ready. All new styles are scoped (.sx-* / .svc-* under .svc-page).
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$p  = $service_page ?? [];
$pid = (int)($p['id'] ?? 0);
$T  = fn(string $f) => tr_field('service_pages', $pid, $f, $p[$f] ?? '');
$gc = fn(string $k, string $d = '') => get_content('services', $k, $d);

$pillars = []; $supporting = [];
try {
    foreach ($db->fetchAll("SELECT * FROM service_pillars WHERE page_id=? ORDER BY urutan, id", [$pid]) as $r) {
        if (($r['kategori'] ?? 'pillar') === 'supporting') $supporting[] = $r; else $pillars[] = $r;
    }
} catch (\Throwable $e) {}
$TP = fn(array $r, string $f) => tr_field('service_pillars', (int)$r['id'], $f, $r[$f] ?? '');
$lifecycle = array_values(array_filter(array_map('trim', explode('→', (string)$T('extra2')))));
if (count($lifecycle) < 2) $lifecycle = ['Assess', 'Design', 'Build', 'Secure', 'Run', 'Optimize'];

$pillar_icons  = ['layers', 'lock', 'settings', 'sparkles'];
$support_icons = ['sparkles', 'box', 'grid', 'layers'];
$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
$check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';

// Headline accent: [word] markers, else gradient trailing word(s).
$svc_acc = function (string $raw): string {
    $raw = trim($raw);
    if ($raw === '') return '';
    if (strpos($raw, '[') !== false) return preg_replace('/\[(.+?)\]/u', '<span class="b">$1</span>', htmlspecialchars($raw, ENT_QUOTES, 'UTF-8'));
    $safe = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
    return preg_replace('/((?:\S+\s+)?\S+)(\s*)$/u', '<span class="b">$1</span>$2', $safe);
};
$svc_headline = $svc_acc($T('headline') ?: ($p['judul'] ?? 'Services'));

// ---- Hero lifecycle ring (animated): icon + position + cycling caption per node ----
$ring_icons = [
  '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',
  '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
  '<rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/>',
  '<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/>',
  '<path d="M3 3v18h18"/><path d="M7 15l3-3 3 3 5-6"/>',
  '<path d="M4 20l5-5 3 3 8-8"/><path d="M15 10h5v5"/>',
];
$ring_pos = [[50,3],[93,29],[93,71],[50,97],[7,71],[7,29]];
$ring_caps = [ // keyed by node label → [blurb, link text, link href]
  'Assess'   => [$gc('cap_assess', 'We map your environment, risks, and goals before recommending anything.'), '→ Infrastructure · Cybersecurity', url('services/infrastructure-services')],
  'Design'   => [$gc('cap_design', 'We architect the right solution for your workloads and constraints.'), '→ Infrastructure Services', url('services/infrastructure-services')],
  'Build'    => [$gc('cap_build', 'We deploy and integrate with minimal business disruption.'), '→ Infrastructure Services', url('services/infrastructure-services')],
  'Secure'   => [$gc('cap_secure', 'We protect your systems and help you meet compliance requirements.'), '→ Cybersecurity Services', url('services/cybersecurity-services')],
  'Run'      => [$gc('cap_run', 'We manage and monitor 24×7 so your business keeps moving.'), '→ Managed Infrastructure', url('services/managed-infrastructure-packages')],
  'Optimize' => [$gc('cap_optimize', 'We continuously tune performance, cost, and resilience.'), '→ Infrastructure · Cybersecurity', url('services')],
];

// Trusted-by logos (monochrome white, no boxes; full-width). uploads/services/logos/.
$trust_logos = [['erha.png','Erha',20],['cinema-xxi.png','Cinema XXI',22],['dell.png','Dell Technologies',16],['cyble.png','Cyble',24],['iso-27001.png','ISO/IEC 27001',38]];

// Benefit cards for "Two Areas".
$benes = [
  ['<path d="M3 12a9 9 0 0 1 15-6.7L21 8M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/>', $gc('b1_t','Stronger Business Continuity'), $gc('b1_d','Improve resilience and recovery readiness when disruptions occur.'), false],
  ['<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/>', $gc('b2_t','Reduced Business Risk'), $gc('b2_d','Identify vulnerabilities and strengthen protection across your IT environment.'), true],
  ['<rect x="3" y="4" width="18" height="5" rx="1.5"/><rect x="3" y="12" width="18" height="5" rx="1.5"/>', $gc('b3_t','Simplified IT Management'), $gc('b3_d','One partner to coordinate your infrastructure and security needs.'), false],
  ['<path d="M3 12h4l3 8 4-16 3 8h4"/>', $gc('b4_t','Reliable IT Operations'), $gc('b4_d','Keep critical systems performing with coordinated support and monitoring.'), false],
];

$seo = ['title' => ($p['judul'] ?? 'Services') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($T('body')), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');
?>
<main class="page-body sv2 svc-page">

  <!-- HERO — animated lifecycle ring (rings rotate, nodes activate in sequence, caption cycles) -->
  <?php $svc_lead = $gc('hero_lead', 'We assess, design, build, secure, run, and optimize your enterprise IT infrastructure and cybersecurity operations — delivering end-to-end solutions and managed services that keep your business secure, resilient, and ready for growth.'); ?>
  <section class="svc-hero svc-hero-split" id="svcHero" aria-label="<?= htmlspecialchars(strip_tags($T('headline') ?: ($p['judul'] ?? 'Services'))) ?>">
    <canvas class="svc-net" id="svcNet" aria-hidden="true"></canvas>
    <div class="svc-split">
      <div class="svc-copy" id="svcCopy">
        <?php if (trim((string)$T('eyebrow')) !== ''): ?><div class="svc-eye"><?= htmlspecialchars($T('eyebrow')) ?></div><?php endif; ?>
        <h1 class="svc-title"><?= $svc_headline ?></h1>
        <?php if (trim(strip_tags((string)$svc_lead)) !== ''): ?><p class="svc-lead"><?= pt($svc_lead) ?></p><?php endif; ?>
        <div class="svc-cta">
          <a class="svc-btn svc-btn-p" href="#sdWrap"><?= htmlspecialchars(t('explore_services', 'Explore Services')) ?> <?= $arrow ?></a>
          <a class="svc-btn svc-btn-g" href="<?= htmlspecialchars(url('contact-us')) ?>"><?= htmlspecialchars(t('talk_to_expert', 'Talk to an Expert')) ?></a>
        </div>
      </div>
      <div class="svl-right" aria-hidden="true">
        <div class="svl-stage">
          <div class="svl-orings"><span class="r r1"><i class="d"></i></span><span class="r r2"><i class="d"></i></span><span class="r r3"></span></div>
          <div class="svl-hub"><b>END&ndash;TO&ndash;END</b><span class="lc"><?= htmlspecialchars($gc('hero_core_label', 'Lifecycle')) ?></span><span class="sub"><?= htmlspecialchars($gc('hero_core_sub', '10+ years · 200+ clients')) ?></span></div>
          <?php foreach ($lifecycle as $i => $lab): if ($i > 5) break; [$lx,$ty] = $ring_pos[$i]; ?>
            <div class="svl-node" style="--i:<?= $i ?>;left:<?= $lx ?>%;top:<?= $ty ?>%"><div class="ic"><svg viewBox="0 0 24 24"><?= $ring_icons[$i % count($ring_icons)] ?></svg></div><div class="n"><?= htmlspecialchars($lab) ?></div></div>
          <?php endforeach; ?>
        </div>
        <div class="svl-caps">
          <?php foreach ($lifecycle as $i => $lab): if ($i > 5) break; $cap = $ring_caps[$lab] ?? null; if (!$cap) continue; ?>
            <div class="svl-cap" style="--i:<?= $i ?>"><b><?= htmlspecialchars($lab) ?></b><p><?= htmlspecialchars($cap[0]) ?></p><a href="<?= htmlspecialchars($cap[2]) ?>"><?= htmlspecialchars($cap[1]) ?></a></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- STATS + TRUSTED BY (full-width) -->
  <section class="sx-band sx-intro">
    <div class="sx-wrap">
      <div class="sx-stats">
        <div class="sx-stat"><b><?= htmlspecialchars($gc('stat1_n','10+')) ?></b><span><?= htmlspecialchars($gc('stat1_l','Years of experience')) ?></span></div>
        <div class="sx-stat"><b><?= htmlspecialchars($gc('stat2_n','200+')) ?></b><span><?= htmlspecialchars($gc('stat2_l','Enterprise clients')) ?></span></div>
        <div class="sx-stat"><b><?= htmlspecialchars($gc('stat3_n','425+')) ?></b><span><?= htmlspecialchars($gc('stat3_l','Projects delivered')) ?></span></div>
        <div class="sx-stat"><b><?= htmlspecialchars($gc('stat4_n','Professional')) ?></b><span><?= htmlspecialchars($gc('stat4_l','Certified expert')) ?></span></div>
      </div>
      <div class="sx-trust">
        <span class="lab"><?= htmlspecialchars(t('trusted_by','Trusted by')) ?></span>
        <div class="logos">
          <?php foreach ($trust_logos as [$file,$alt,$h]): ?>
            <img class="sx-logo" style="height:<?= (int)$h ?>px" src="<?= htmlspecialchars(uploads_url('services/logos/'.$file)) ?>" alt="<?= htmlspecialchars($alt) ?>" loading="lazy" decoding="async">
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <?php
  // Capabilities "Deck" — PRESERVED original scroll-scrubbed 01/02 slider (reuses .sd-* + soldeck.js).
  $caps = [];
  foreach ($pillars as $i => $pl)   $caps[] = ['row' => $pl, 'icon' => $pillar_icons[$i] ?? 'layers'];
  foreach ($supporting as $i => $s) $caps[] = ['row' => $s,  'icon' => $support_icons[$i] ?? 'box'];
  // Show only services 1 & 2 for now (data kept in DB). Delete this line to restore all.
  $caps = array_slice($caps, 0, 2);
  $cap_n = count($caps);
  ?>
  <?php if ($caps): ?>
  <section class="sd-wrap svc-deck" id="sdWrap" aria-label="<?= htmlspecialchars(t('supporting_capability', 'What we deliver')) ?>" style="height:<?= max(320, $cap_n * 112) ?>vh">
    <div class="sd-pin">
      <canvas class="sd-bg" id="sdBg" aria-hidden="true"></canvas>
      <div class="sd-eye"><?= htmlspecialchars(t('what_we_deliver', 'What we deliver')) ?></div>
      <div class="sd-rail" id="sdRail">
        <?php foreach ($caps as $i => $c): ?>
          <button type="button" data-i="<?= $i ?>" class="<?= $i === 0 ? 'on' : '' ?>" aria-label="<?= htmlspecialchars(strip_tags($TP($c['row'], 'judul'))) ?>"><span class="d"></span><span class="idx"><?= sprintf('%02d', $i + 1) ?></span></button>
        <?php endforeach; ?>
      </div>
      <div class="sd-stage" id="sdStage">
        <?php foreach ($caps as $i => $c): $cr = $c['row'];
          $tags = array_values(array_filter(array_map('trim', explode(',', (string)($cr['tags'] ?? '')))));
          $has = !empty($cr['link_slug']);
        ?>
          <div class="sd-panel<?= $i === 0 ? ' on' : '' ?>" data-i="<?= $i ?>">
            <div class="sd-txt">
              <div class="sd-num"><?= sprintf('%02d', $i + 1) ?></div>
              <?php if (!empty($cr['badge'])): ?><span class="sd-badge"><?= htmlspecialchars($cr['badge']) ?></span><?php endif; ?>
              <h2 class="sd-title notranslate" translate="no"><?= $svc_acc($TP($c['row'], 'judul')) ?></h2>
              <?php if (trim((string)$TP($c['row'], 'deskripsi')) !== ''): ?><p class="sd-desc"><?= pt($TP($c['row'], 'deskripsi')) ?></p><?php endif; ?>
              <?php if ($tags): ?><div class="sd-chips" translate="no"><?php foreach ($tags as $tg): ?><span><?= htmlspecialchars($tg) ?></span><?php endforeach; ?></div><?php endif; ?>
              <?php if ($has): ?><a class="sd-cta" href="<?= htmlspecialchars(url($cr['link_slug'])) ?>"><?= htmlspecialchars(t('learn_more', 'Learn more')) ?> <?= $arrow ?></a><?php endif; ?>
            </div>
            <div class="sd-media sd-emblem" aria-hidden="true">
              <span class="glow"></span><span class="ring r1"></span><span class="ring r2"></span>
              <span class="ic"><?= icon($c['icon'], 60) ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <script src="<?= theme_asset('assets/js/soldeck.js') ?>" defer></script>
  <?php endif; ?>

  <!-- TWO AREAS (animated connectors) + BUNDLE -->
  <section class="sx-band">
    <div class="sx-wrap">
      <div class="sx-two">
        <div class="sx-eye"><?= htmlspecialchars(t('better_together','Better together')) ?></div>
        <h2 class="sx-h2"><?= $svc_acc($gc('two_h','Two Areas of Expertise. One Trusted Partner.')) ?></h2>
        <p class="sx-sub"><?= htmlspecialchars($gc('two_sub','Simplify technology management, reduce security risks, and strengthen business continuity with infrastructure and cybersecurity expertise from one trusted partner.')) ?></p>
        <div class="sx-diagram">
          <span class="sx-conn l"><i class="d"></i></span><span class="sx-conn r"><i class="d"></i></span>
          <div class="sx-acard"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M7 20h10M12 16v4"/></svg></div><h4><?= htmlspecialchars($gc('two_a1_t','Infrastructure Services')) ?></h4><p><?= htmlspecialchars($gc('two_a1_d','Resilient enterprise foundations, built and managed end to end.')) ?></p><div class="meta"><?= htmlspecialchars($gc('two_a1_m','Consulting · Deployment · Managed Infrastructure')) ?></div></div>
          <div class="sx-hub2"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4.5v9L12 20l-8-4.5v-9z"/><circle cx="12" cy="11" r="2.4"/><path d="M12 8.6V6M9.9 12.2l-2 1.1M14.1 12.2l2 1.1"/></svg></div><b><?= htmlspecialchars($gc('two_hub_t','Unified Expertise')) ?></b><span><?= htmlspecialchars($gc('two_hub_s','Sapta Tunas Teknologi')) ?></span></div>
          <div class="sx-acard cy"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/></svg></div><h4><?= htmlspecialchars($gc('two_a2_t','Cybersecurity Services')) ?></h4><p><?= htmlspecialchars($gc('two_a2_d','Assess, govern and protect against evolving cyber threats.')) ?></p><div class="meta"><?= htmlspecialchars($gc('two_a2_m','Assess · Govern · Protect')) ?></div></div>
        </div>
        <div class="sx-benes">
          <?php foreach ($benes as $bn): ?>
            <div class="sx-bene<?= $bn[3] ? ' on' : '' ?>"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><?= $bn[0] ?></svg></div><h5><?= htmlspecialchars($bn[1]) ?></h5><p><?= htmlspecialchars($bn[2]) ?></p></div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="sx-bundle">
        <div class="sx-bgrid">
          <div>
            <span class="sx-flag"><?= htmlspecialchars(t('proposed_bundle','Proposed Bundle')) ?></span>
            <h3><?= htmlspecialchars($gc('bundle_h','Managed Infrastructure + SOC as a Service')) ?></h3>
            <p class="lead"><?= htmlspecialchars($gc('bundle_lead','One partner from your data center to your security operations. Fewer vendors to manage, fewer gaps to cover.')) ?></p>
            <div class="sx-bbtns">
              <a class="svc-btn svc-btn-p" href="<?= htmlspecialchars(url('contact-us')) ?>"><?= htmlspecialchars(t('request_combined_proposal','Request a Combined Proposal')) ?> <?= $arrow ?></a>
              <a class="sx-blink" href="<?= htmlspecialchars(url('services/managed-infrastructure-packages')) ?>"><?= htmlspecialchars(t('infrastructure_plans','Infrastructure plans')) ?> <?= $arrow ?></a>
              <a class="sx-blink" href="<?= htmlspecialchars(url('services/soc-as-a-service')) ?>"><?= htmlspecialchars(t('soc_plans','SOC plans')) ?> <?= $arrow ?></a>
            </div>
          </div>
          <ul class="sx-points">
            <?php foreach ([$gc('bundle_p1','One contract and one SLA across both services'),$gc('bundle_p2','A single escalation path when something goes wrong'),$gc('bundle_p3','Infrastructure monitoring and SOC working from the same picture'),$gc('bundle_p4','Choose Gold, Platinum, or Diamond on each side')] as $pit): if (trim((string)$pit)==='') continue; ?><li><?= $check ?><span><?= htmlspecialchars($pit) ?></span></li><?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- Closing CTA "Finale" (light; meets the footer's white band smoothly) — PRESERVED. -->
  <section class="cf" aria-label="Contact">
    <canvas class="cf-bg" id="cfBg" aria-hidden="true"></canvas>
    <span class="au au1" aria-hidden="true"></span><span class="au au2" aria-hidden="true"></span><span class="au au3" aria-hidden="true"></span>
    <span class="ring" aria-hidden="true"></span>
    <div class="cf-in">
      <?php if (trim((string)$T('eyebrow')) !== ''): ?><div class="cf-eye"><?= htmlspecialchars($T('eyebrow')) ?></div><?php endif; ?>
      <h2 class="cf-h" data-text="<?= htmlspecialchars(trim((string)$T('extra3')) ?: 'One Partner. End-to-End [Capability.]', ENT_QUOTES, 'UTF-8') ?>"></h2>
      <?php if (trim((string)$T('extra1')) !== ''): ?><p class="cf-p"><?= htmlspecialchars($T('extra1')) ?></p><?php endif; ?>
      <div class="cf-btns">
        <a class="btn btn-p" href="<?= htmlspecialchars(url($p['cta_target'] ?: 'contact-us')) ?>"><?= htmlspecialchars($T('cta_label') ?: t('contact_us', 'Contact Us')) ?> <?= $arrow ?></a>
      </div>
    </div>
  </section>
  <script src="<?= theme_asset('assets/js/ctafinale.js') ?>" defer></script>
</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
