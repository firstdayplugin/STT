<?php
/**
 * Anima — Services LANDING (route: /services). Mockup-aligned layout (hero lifecycle
 * ring + stats + trusted-by + "what we deliver" + two-areas + bundle + CTA), rendered
 * in the site's own design language (Space Grotesk / Inter, site tokens — NOT the mockup
 * font). Hero copy is CMS-driven via service_pages (tr_field); the newer sections use
 * get_content('services', …) so every string is CMS-overridable and EN/ID ready.
 * All new section styles are scoped under .svc-page (see pages.css .sx-*) so no other
 * page/template is affected.
 */
$db = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$p  = $service_page ?? [];
$pid = (int)($p['id'] ?? 0);
$T  = fn(string $f) => tr_field('service_pages', $pid, $f, $p[$f] ?? '');
$gc = fn(string $k, string $d = '') => get_content('services', $k, $d);
$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
$check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';

// Headline accent: honour [word] markers, else gradient the trailing word(s).
$svc_acc = function (string $raw): string {
    $raw = trim($raw);
    if ($raw === '') return '';
    if (strpos($raw, '[') !== false) return preg_replace('/\[(.+?)\]/u', '<span class="b">$1</span>', htmlspecialchars($raw, ENT_QUOTES, 'UTF-8'));
    $safe = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
    return preg_replace('/((?:\S+\s+)?\S+)(\s*)$/u', '<span class="b">$1</span>$2', $safe);
};

// Hero lifecycle ring nodes (label + icon). Labels are CMS-overridable; icons fixed.
$ring_icons = [
  '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',                                                   // Assess
  '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',                                    // Design
  '<rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/>', // Build
  '<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/>',                             // Secure
  '<path d="M3 3v18h18"/><path d="M7 15l3-3 3 3 5-6"/>',                                                           // Run
  '<path d="M4 20l5-5 3 3 8-8"/><path d="M15 10h5v5"/>',                                                           // Optimize
];
$ring_labels = array_values(array_filter(array_map('trim', explode('→', (string)($T('extra2') ?: 'Assess → Design → Build → Secure → Run → Optimize')))));
if (count($ring_labels) < 2) $ring_labels = ['Assess', 'Design', 'Build', 'Secure', 'Run', 'Optimize'];
$ring_pos = [[50,6],[88,30],[88,70],[50,94],[12,70],[12,30]]; // left%,top% around the stage

$seo = ['title' => ($p['judul'] ?? 'Services') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($T('body') ?: $gc('deliver_h', '')), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');

// Trusted-by logos (monochrome white, no boxes). Files under uploads/services/logos/.
$trust_logos = [
  ['erha.png', 'Erha', 19], ['cinema-xxi.png', 'Cinema XXI', 21], ['dell.png', 'Dell Technologies', 15],
  ['cyble.png', 'Cyble', 23], ['iso-27001.png', 'ISO/IEC 27001', 36],
];

// "What we deliver" — two capability blocks (copy CMS-overridable via get_content).
$deliver = [
  [
    'num' => '01', 'badge' => $gc('d1_badge', 'Build & Run'),
    'title' => $gc('d1_title', 'Infrastructure Services'),
    'lead'  => $gc('d1_lead', 'Build an enterprise foundation that keeps critical workloads running: designed around your business, deployed with minimal disruption, and managed 24×7 by one accountable team.'),
    'checks' => [
      $gc('d1_c1', "Capacity sized for today's workloads and tomorrow's growth, including AI"),
      $gc('d1_c2', 'Seamless infrastructure deployment, integration, migration, and relocation — with minimal business disruption'),
      $gc('d1_c3', 'Proactive 24/7 infrastructure management to ensure reliability, performance, and business continuity'),
    ],
    'tags' => [$gc('d1_t1', 'Consulting'), $gc('d1_t2', 'Deployment'), $gc('d1_t3', 'Managed Infrastructure')],
    'url' => url('services/infrastructure-services'),
    'how_ic' => '<path d="M3 3v18h18"/><path d="M7 15l3-3 3 3 5-6"/>',
    'how' => [
      [$gc('d1_h1t', 'Consulting'), $gc('d1_h1d', 'Know exactly where you stand, and the shortest path to where you need to be.')],
      [$gc('d1_h2t', 'Deployment'), $gc('d1_h2d', 'Go live with infrastructure that is integrated, documented, and tested.')],
      [$gc('d1_h3t', 'Managed Infrastructure'), $gc('d1_h3d', '24×7 helpdesk and a maximum 1-hour response, backed by your SLA.')],
    ],
    'mini' => [],
  ],
  [
    'num' => '02', 'badge' => $gc('d2_badge', 'Protect & Respond'),
    'title' => $gc('d2_title', 'Cybersecurity Services'),
    'lead'  => $gc('d2_lead', 'Strengthen your security posture, meet regulatory requirements, and stay ahead of evolving cyber threats — with expert-led cybersecurity services and continuous protection by 24×7 SOC monitoring.'),
    'checks' => [
      $gc('d2_c1', 'Identify and prioritize security risks based on business impact.'),
      $gc('d2_c2', 'Strengthen compliance readiness for OJK, BI, UU PDP, and ISO 27001.'),
      $gc('d2_c3', 'Rapid security response with SLA targets as short as 15 minutes.'),
    ],
    'tags' => [$gc('d2_t1', 'Assess'), $gc('d2_t2', 'Govern'), $gc('d2_t3', 'Protect')],
    'url' => url('services/cybersecurity-services'),
    'how_ic' => '<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/>',
    'how' => [
      [$gc('d2_h1t', 'Assess'), $gc('d2_h1d', 'Uncover vulnerabilities, assess exposure, and prioritize risks before they impact your business.')],
      [$gc('d2_h2t', 'Govern'), $gc('d2_h2d', 'Strengthen security governance and compliance with practical, audit-ready controls.')],
      [$gc('d2_h3t', 'Protect'), $gc('d2_h3d', 'Protect your business with 24/7 SOC monitoring, proactive threat detection, and rapid incident response.')],
    ],
    'mini' => [
      [$gc('d2_m1t', 'VAPT'), $gc('d2_m1d', 'Security Assessment & Penetration Testing')],
      [$gc('d2_m2t', 'Compliance'), $gc('d2_m2d', 'Regulatory Compliance Consulting & Audit Readiness')],
      [$gc('d2_m3t', 'SOC'), $gc('d2_m3d', 'SOC as a Service with Managed Threat Detection & Response')],
    ],
  ],
];

// Benefit cards for "Two Areas".
$benes = [
  ['<path d="M3 12a9 9 0 0 1 15-6.7L21 8M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/>', $gc('b1_t', 'Stronger Business Continuity'), $gc('b1_d', 'Improve resilience and recovery readiness when disruptions occur.')],
  ['<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/>', $gc('b2_t', 'Reduced Business Risk'), $gc('b2_d', 'Identify vulnerabilities and strengthen protection across your IT environment.')],
  ['<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>', $gc('b3_t', 'Simplified IT Management'), $gc('b3_d', 'One partner to coordinate your infrastructure and security needs.')],
  ['<path d="M3 12h4l3 8 4-16 3 8h4"/>', $gc('b4_t', 'Reliable IT Operations'), $gc('b4_d', 'Keep critical systems performing with coordinated support and monitoring.')],
];
?>
<main class="page-body sv2 svc-page">

  <!-- HERO — lifecycle ring (static labelled nodes) -->
  <section class="svc-hero svc-hero-split" id="svcHero" aria-label="<?= htmlspecialchars(strip_tags($T('headline') ?: ($p['judul'] ?? 'Services'))) ?>">
    <canvas class="svc-net" id="svcNet" aria-hidden="true"></canvas>
    <div class="svc-split">
      <div class="svc-copy">
        <?php if (trim((string)$T('eyebrow')) !== ''): ?><div class="svc-eye"><?= htmlspecialchars($T('eyebrow')) ?></div><?php endif; ?>
        <h1 class="svc-title"><?= $svc_acc($T('headline') ?: ($p['judul'] ?? 'Services')) ?></h1>
        <?php $lead = $gc('hero_lead', 'We assess, design, build, secure, run, and optimize your enterprise IT infrastructure and cybersecurity operations — delivering end-to-end solutions and managed services that keep your business secure, resilient, and ready for growth.');
          if (trim(strip_tags((string)$lead)) !== ''): ?><p class="svc-lead"><?= pt($lead) ?></p><?php endif; ?>
        <div class="svc-cta">
          <a class="svc-btn svc-btn-p" href="#sxDeliver"><?= htmlspecialchars(t('explore_services', 'Explore Services')) ?> <?= $arrow ?></a>
          <a class="svc-btn svc-btn-g" href="<?= htmlspecialchars(url('contact-us')) ?>"><?= htmlspecialchars(t('talk_to_expert', 'Talk to an Expert')) ?></a>
        </div>
      </div>
      <div class="svc-stage" aria-hidden="true">
        <div class="svc-hub"><span class="ring2"></span><span class="lbl"><b>END&ndash;TO&ndash;END</b><span>Lifecycle</span><span class="sub"><?= htmlspecialchars($gc('hero_core_sub', '10+ years · 200+ clients')) ?></span></span></div>
        <?php foreach ($ring_labels as $i => $lab): if ($i > 5) break; [$lx, $ty] = $ring_pos[$i]; ?>
          <div class="svc-node" style="left:<?= $lx ?>%;top:<?= $ty ?>%"><div class="ic"><svg viewBox="0 0 24 24"><?= $ring_icons[$i % count($ring_icons)] ?></svg></div><div class="n"><?= htmlspecialchars($lab) ?></div></div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="sx-band" style="padding:clamp(30px,5vh,56px) 0 clamp(56px,9vh,104px)">
    <div class="sx-wrap">

      <!-- STATS -->
      <div class="sx-stats">
        <div class="sx-stat"><b><?= htmlspecialchars($gc('stat1_n', '10+')) ?></b><span><?= htmlspecialchars($gc('stat1_l', 'Years of experience')) ?></span></div>
        <div class="sx-stat"><b><?= htmlspecialchars($gc('stat2_n', '200+')) ?></b><span><?= htmlspecialchars($gc('stat2_l', 'Enterprise clients')) ?></span></div>
        <div class="sx-stat"><b><?= htmlspecialchars($gc('stat3_n', '425+')) ?></b><span><?= htmlspecialchars($gc('stat3_l', 'Projects delivered')) ?></span></div>
        <div class="sx-stat"><b><?= htmlspecialchars($gc('stat4_n', 'Professional')) ?></b><span><?= htmlspecialchars($gc('stat4_l', 'Certified expert')) ?></span></div>
      </div>

      <!-- TRUSTED BY -->
      <div class="sx-trust">
        <span class="lab"><?= htmlspecialchars(t('trusted_by', 'Trusted by')) ?></span>
        <div class="logos">
          <?php foreach ($trust_logos as [$file, $alt, $h]): $src = uploads_url('services/logos/' . $file); ?>
            <img class="sx-logo" style="height:<?= (int)$h ?>px" src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($alt) ?>" loading="lazy" decoding="async">
          <?php endforeach; ?>
        </div>
      </div>

      <!-- WHAT WE DELIVER -->
      <div class="sx-deliver" id="sxDeliver">
        <div class="sx-dhead"><div class="sx-eye"><?= htmlspecialchars(t('what_we_deliver', 'What we deliver')) ?></div>
          <h2 class="sx-h2"><?= $svc_acc($gc('deliver_h', 'End-to-End Infrastructure & Cybersecurity Services.')) ?></h2></div>
        <?php foreach ($deliver as $d): ?>
        <div class="sx-dblock">
          <div>
            <div class="sx-dnum"><?= htmlspecialchars($d['num']) ?></div>
            <?php if ($d['badge'] !== ''): ?><span class="sx-badge"><?= htmlspecialchars($d['badge']) ?></span><?php endif; ?>
            <h3 class="sx-dtitle notranslate" translate="no"><?= htmlspecialchars($d['title']) ?></h3>
            <?php if ($d['lead'] !== ''): ?><p class="sx-dlead"><?= htmlspecialchars($d['lead']) ?></p><?php endif; ?>
            <ul class="sx-checks"><?php foreach ($d['checks'] as $c): if (trim((string)$c) === '') continue; ?><li><?= $check ?><span><?= htmlspecialchars($c) ?></span></li><?php endforeach; ?></ul>
            <?php if (array_filter($d['tags'])): ?><div class="sx-tags" translate="no"><?php foreach ($d['tags'] as $tg): if (trim((string)$tg) === '') continue; ?><span><?= htmlspecialchars($tg) ?></span><?php endforeach; ?></div><?php endif; ?>
            <a class="sx-learn" href="<?= htmlspecialchars($d['url']) ?>"><?= htmlspecialchars(t('learn_more', 'Learn more')) ?> <?= $arrow ?></a>
          </div>
          <div class="sx-how">
            <div class="cap"><?= htmlspecialchars(t('how_we_deliver', 'How we deliver')) ?> <span class="em"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?= $d['how_ic'] ?></svg></span></div>
            <?php foreach ($d['how'] as $k => $row): ?>
              <div class="sx-row"><span class="n"><?= $k + 1 ?></span><div><b><?= htmlspecialchars($row[0]) ?></b><p><?= htmlspecialchars($row[1]) ?></p></div></div>
            <?php endforeach; ?>
            <?php if (!empty($d['mini'])): ?><div class="mini"><?php foreach ($d['mini'] as $m): ?><div><b><?= htmlspecialchars($m[0]) ?></b><span><?= htmlspecialchars($m[1]) ?></span></div><?php endforeach; ?></div><?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- TWO AREAS -->
      <div class="sx-two">
        <div class="sx-eye"><?= htmlspecialchars(t('better_together', 'Better together')) ?></div>
        <h2 class="sx-h2"><?= $svc_acc($gc('two_h', 'Two Areas of Expertise. One Trusted Partner.')) ?></h2>
        <p class="sx-sub"><?= htmlspecialchars($gc('two_sub', 'Simplify technology management, reduce security risks, and strengthen business continuity with infrastructure and cybersecurity expertise from one trusted partner.')) ?></p>
        <div class="sx-diagram">
          <div class="sx-acard"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M7 20h10M12 16v4"/></svg></div><h4><?= htmlspecialchars($gc('two_a1_t', 'Infrastructure Services')) ?></h4><p><?= htmlspecialchars($gc('two_a1_d', 'Resilient enterprise foundations, built and managed end to end.')) ?></p><div class="meta"><?= htmlspecialchars($gc('two_a1_m', 'Consulting · Deployment · Managed Infrastructure')) ?></div></div>
          <div class="sx-hub2"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/></svg></div><b><?= htmlspecialchars($gc('two_hub_t', 'Unified Expertise')) ?></b><span><?= htmlspecialchars($gc('two_hub_s', 'Sapta Tunas Teknologi')) ?></span></div>
          <div class="sx-acard"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/></svg></div><h4><?= htmlspecialchars($gc('two_a2_t', 'Cybersecurity Services')) ?></h4><p><?= htmlspecialchars($gc('two_a2_d', 'Assess, govern and protect against evolving cyber threats.')) ?></p><div class="meta"><?= htmlspecialchars($gc('two_a2_m', 'Assess · Govern · Protect')) ?></div></div>
        </div>
        <div class="sx-benes">
          <?php foreach ($benes as $bn): ?>
            <div class="sx-bene"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><?= $bn[0] ?></svg></div><h5><?= htmlspecialchars($bn[1]) ?></h5><p><?= htmlspecialchars($bn[2]) ?></p></div>
          <?php endforeach; ?>
        </div>
        <div class="sx-twocta">
          <a class="svc-btn svc-btn-p" href="#sxDeliver"><?= htmlspecialchars(t('explore_our_services', 'Explore Our Services')) ?> <?= $arrow ?></a>
          <a class="svc-btn svc-btn-g" href="<?= htmlspecialchars(url('contact-us')) ?>"><?= htmlspecialchars(t('talk_to_experts', 'Talk to Our Experts')) ?></a>
        </div>
      </div>

      <!-- BUNDLE -->
      <div class="sx-bundle">
        <div class="sx-bgrid">
          <div>
            <span class="sx-flag"><?= htmlspecialchars(t('proposed_bundle', 'Proposed Bundle')) ?></span>
            <h3><?= htmlspecialchars($gc('bundle_h', 'Managed Infrastructure + SOC as a Service')) ?></h3>
            <p class="lead"><?= htmlspecialchars($gc('bundle_lead', 'One partner from your data center to your security operations. Fewer vendors to manage, fewer gaps to cover.')) ?></p>
            <div class="sx-bbtns">
              <a class="svc-btn svc-btn-p" href="<?= htmlspecialchars(url('contact-us')) ?>"><?= htmlspecialchars(t('request_combined_proposal', 'Request a Combined Proposal')) ?> <?= $arrow ?></a>
              <a class="sx-blink" href="<?= htmlspecialchars(url('services/managed-infrastructure-packages')) ?>"><?= htmlspecialchars(t('infrastructure_plans', 'Infrastructure plans')) ?> <?= $arrow ?></a>
              <a class="sx-blink" href="<?= htmlspecialchars(url('services/soc-as-a-service')) ?>"><?= htmlspecialchars(t('soc_plans', 'SOC plans')) ?> <?= $arrow ?></a>
            </div>
          </div>
          <ul class="sx-points">
            <?php foreach ([
              $gc('bundle_p1', 'One contract and one SLA across both services'),
              $gc('bundle_p2', 'A single escalation path when something goes wrong'),
              $gc('bundle_p3', 'Infrastructure monitoring and SOC working from the same picture'),
              $gc('bundle_p4', 'Choose Gold, Platinum, or Diamond on each side'),
            ] as $pt): if (trim((string)$pt) === '') continue; ?><li><?= $check ?><span><?= htmlspecialchars($pt) ?></span></li><?php endforeach; ?>
          </ul>
        </div>
      </div>

      <!-- FINAL CTA -->
      <div class="sx-final">
        <div><h3><?= htmlspecialchars($gc('final_h', 'Not sure where to start?')) ?></h3><p><?= htmlspecialchars($gc('final_p', 'Tell us about your environment. Our team will map the right services and send a tailored proposal.')) ?></p></div>
        <a class="svc-btn svc-btn-p" href="<?= htmlspecialchars(url($p['cta_target'] ?: 'contact-us')) ?>"><?= htmlspecialchars($T('cta_label') ?: t('request_proposal', 'Request Proposal')) ?> <?= $arrow ?></a>
      </div>

    </div>
  </section>
</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
