<?php
/**
 * Anima — Services SUB-PAGE (route: /services/[slug]).
 * Layout mengikuti mockup klien, dirender dalam design language situs (font
 * Space Grotesk + Inter, token warna existing). Dua archetype by tipe:
 *   capability -> hero split + AT A GLANCE, Service Areas (3 kartu), Plan
 *                 Positioning (teaser paket), cross-sell, final CTA.
 *   package    -> hero split + AT A GLANCE, Compare plans (matrix), What happens
 *                 next (3 langkah), cross-sell, final CTA.
 * Hero constellation (#csdNet) & reveal animasi via svcdetail.js (element-guarded).
 * Data: service_areas/items, service_tiers, service_matrix (tr_field aware).
 * Konten extra mockup (tombol/glance/steps/cross-sell/plan-teaser) default di
 * template, dapat dioverride via content_blocks(slug,key) lewat get_content().
 */
$db  = $db ?? (class_exists('Database') ? Database::getInstance() : null);
$p   = $service_page ?? [];
$pid = (int)($p['id'] ?? 0);
$slug = (string)($p['slug'] ?? '');
$tipe = $p['tipe'] ?? 'capability';
$T  = fn(string $f) => tr_field('service_pages', $pid, $f, $p[$f] ?? '');
$gc = fn(string $k, string $d = '') => function_exists('get_content') ? get_content($slug, $k, $d) : $d;

$areas = $tiers = $matrix = [];
try {
    if ($tipe === 'capability') {
        $areas = $db->fetchAll("SELECT * FROM service_areas WHERE page_id=? ORDER BY urutan, id", [$pid]);
        foreach ($areas as &$a) {
            $a['items'] = $db->fetchAll("SELECT * FROM service_area_items WHERE area_id=? ORDER BY urutan, id", [(int)$a['id']]);
        }
        unset($a);
    } else {
        $tiers  = $db->fetchAll("SELECT * FROM service_tiers WHERE page_id=? ORDER BY urutan, id", [$pid]);
        $matrix = $db->fetchAll("SELECT * FROM service_matrix WHERE page_id=? ORDER BY urutan, id", [$pid]);
    }
} catch (\Throwable $e) {}
$mgroups = [];
foreach ($matrix as $row) { $mgroups[(string)($row['grup'] ?? '')][] = $row; }

/* -------- per-page mockup config (EN defaults; override via content_blocks) ---- */
$CFG_ALL = [
  'services/infrastructure-services' => [
    'crumb'      => [['Services','services']],
    'crumb_last' => 'Infrastructure Services',
    'btn1' => ['Compare Service Packages','services/managed-infrastructure-packages'],
    'btn2' => ['Talk to an Expert','contact-us'],
    'glance' => [['23','Capabilities across 3 service areas'],['24×7','Helpdesk service SLA'],['3','Service plans: Gold, Platinum, Diamond'],['ISO 27001','Certified information security']],
    'areas_head' => ['Service Areas','Three areas, one accountable team','Start anywhere in the lifecycle. Every area connects to the next.'],
    'plan' => [
      'eye'=>'Package Positioning','h'=>'Choose Your Plan',
      'sub'=>'Managed Infrastructure Services matched to your environment\'s criticality, support needs, maintenance frequency, and onsite requirements.',
      'tiers'=>[
        ['GOLD','Essential Infrastructure Support',false,['Preventive maintenance 2× / year','6 corrective maintenance tickets','2 move, add & change tickets']],
        ['PLATINUM','Enhanced Operational Support',true,['Preventive maintenance 4× / year','12 corrective maintenance tickets','6 move, add & change tickets','Yearly maintenance review']],
        ['DIAMOND','Comprehensive Managed Infrastructure',false,['Monthly preventive maintenance','Unlimited corrective and MAC tickets','1 onsite standby engineer (office hours)','Maintenance review 2× / year']],
      ],
      'note'=>'All plans include a 24×7 helpdesk, max. 1 hour response time, and a 1-year minimum period.',
      'cta'=>['Compare all plans','services/managed-infrastructure-packages'],
    ],
    'final' => ['Not sure which plan fits?','Tell us about your environment. We will recommend a plan and send a tailored proposal.',['Discuss Your SLA Needs','contact-us']],
  ],
  'services/cybersecurity-services' => [
    'alert' => ['Under attack or suspect a breach?','Talk to our team now','contact-us'],
    'crumb'      => [['Services','services']],
    'crumb_last' => 'Cybersecurity Services',
    'btn1' => ['Explore SOC Plans','services/soc-as-a-service'],
    'btn2' => ['Talk to a Security Expert','contact-us'],
    'glance' => [['15 min','SOC Acknowledgement SLA Target'],['GRC','Regulatory & Privacy Compliance Consulting'],['SOC','SOC as a Service with Managed Threat Detection & Response'],['ISO 27001','Certified information security']],
    'areas_head' => ['Service Areas','Three areas, one accountable team','Start with an assessment, build governance, then protect continuously.'],
    'plan' => [
      'eye'=>'Package Positioning','h'=>'Choose Your Plan',
      'sub'=>'SOC as a Service plans matched to your monitoring hours, log coverage, response time, and incident-handling needs.',
      'tiers'=>[
        ['GOLD','Essential SOC Monitoring',false,['Security monitoring 8 × 5','Up to 25 assets','Log ingest 5 GB / day','Detection & recommendation','Monthly security reporting']],
        ['PLATINUM','Managed Detection & Investigation',true,['Security monitoring 24 × 7','Up to 50 assets','Log ingest 10 GB / day','Co-managed incident handling with SOAR','VA: 4 credits / year','Monthly & annual security reporting']],
        ['DIAMOND','Comprehensive Cyber Defense',false,['Security monitoring 24 × 7','Up to 100 assets / source logs','Log ingest 30 GB / day','Fully managed incident handling with SOAR','Threat hunting & pentest credits','Monthly, half & annual reporting']],
      ],
      'note'=>'All plans include 30 days of log retention.',
      'cta'=>['Compare all SOC plans','services/soc-as-a-service'],
    ],
    'final' => ['Not sure where to start?','Tell us about your environment and regulatory needs. We will map the right services and send a tailored proposal.',['Discuss Your SOC Needs','contact-us']],
  ],
  'services/managed-infrastructure-packages' => [
    'crumb'      => [['Services','services'],['Infrastructure Services','services/infrastructure-services']],
    'crumb_last' => 'Managed Infrastructure Packages',
    'btn1' => ['Discuss Your SLA Needs','contact-us'],
    'btn2' => ['Back to Infrastructure Services','services/infrastructure-services'],
    'glance' => [['3','Plans: Gold, Platinum, Diamond'],['24×7','Helpdesk service SLA'],['1 hour','Maximum response time'],['1 year','Minimum period']],
    'plan_head' => ['Choose Your Plan','Compare plans in detail','Full coverage for every plan, side by side. Platinum is our recommended plan.'],
    'steps' => ['What happens next','From first call to signed plan',[
      ['Share your environment','Tell us what you run and how critical it is.'],
      ['We recommend a plan','A written recommendation with scope and SLA.'],
      ['Proposal & onboarding','Agree on the proposal, then we take over operations.'],
    ]],
    'cross' => ['Protecting what you run?','Pair managed infrastructure with security monitoring from our SOC.',['Explore SOC as a Service','services/soc-as-a-service']],
    'final' => ['Not sure which plan fits?','Tell us about your environment. We will recommend a plan and send a tailored proposal.',['Discuss Your SLA Needs','contact-us']],
  ],
  'services/soc-as-a-service' => [
    'alert' => ['Under attack or suspect a breach?','Talk to our team now','contact-us'],
    'crumb'      => [['Services','services'],['Cybersecurity Services','services/cybersecurity-services']],
    'crumb_last' => 'SOC as a Service',
    'btn1' => ['Discuss Your SOC Needs','contact-us'],
    'btn2' => ['Back to Cybersecurity Services','services/cybersecurity-services'],
    'glance' => [['3','Plans: Gold, Platinum, Diamond'],['24×7','Monitoring on Platinum and Diamond'],['15 min','Fastest response time'],['30 days','Log retention on all plans']],
    'plan_head' => ['Choose Your Plan','Compare plans in detail','Full coverage for every plan, side by side. Platinum is our recommended plan.'],
    'steps' => ['What happens next','From first call to live monitoring',[
      ['Share your scope','Tell us your assets, log sources, and regulatory needs.'],
      ['We recommend a plan','A written recommendation with coverage and response times.'],
      ['Proposal & onboarding','Agree on the proposal, then we connect your log sources.'],
    ]],
    'cross' => ['Want the full picture?','See how assessment, governance, and managed protection fit together.',['Back to Cybersecurity Services','services/cybersecurity-services']],
    'final' => ['Not sure which plan fits?','Tell us about your environment and regulatory needs. We will map the right services and send a tailored proposal.',['Discuss Your SOC Needs','contact-us']],
  ],
];
$C = $CFG_ALL[$slug] ?? [];

$seo = ['title' => ($p['judul'] ?? 'Services') . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($T('body')), 0, 160)];
$anima_body_class = 'page-inner';
$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
$check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
$cell = function ($v) use ($check) {
    $v = trim((string)$v);
    if ($v === '' || $v === '-' || strcasecmp($v, 'No') === 0) return '<span class="svd-dash">—</span>';
    if (strcasecmp($v, 'Yes') === 0) return '<span class="svd-yes">' . $check . '</span>';
    return '<span class="svd-val">' . htmlspecialchars($v) . '</span>';
};

// Shared render helpers --------------------------------------------------------
$render_hero = function () use ($C, $T, $p, $arrow, $slug) {
    $btn1 = $C['btn1'] ?? [($T('cta_label') ?: 'Contact Us'), ($p['cta_target'] ?: 'contact-us')];
    $btn2 = $C['btn2'] ?? null;
    ?>
    <?php if (!empty($C['alert'])): ?>
      <a class="csd-alert" href="<?= htmlspecialchars(url($C['alert'][2])) ?>">
        <span class="al-dot" aria-hidden="true"></span>
        <strong><?= htmlspecialchars($C['alert'][0]) ?></strong>
        <span class="al-go"><?= htmlspecialchars($C['alert'][1]) ?> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </a>
    <?php endif; ?>
    <section class="csd-hero sxd" id="csdHero">
      <canvas class="csd-net" id="csdNet" aria-hidden="true"></canvas>
      <div class="sxd-inner">
        <div class="sxd-copy">
          <?php if (!empty($C['crumb'])): ?>
          <nav class="svc-crumb" aria-label="Breadcrumb">
            <?php foreach ($C['crumb'] as $cr): ?><a href="<?= htmlspecialchars(url($cr[1])) ?>"><?= htmlspecialchars($cr[0]) ?></a><span class="sep">/</span><?php endforeach; ?>
            <span class="cur"><?= htmlspecialchars($C['crumb_last'] ?? ($p['judul'] ?? '')) ?></span>
          </nav>
          <?php endif; ?>
          <?php if (trim((string)$T('tagline')) !== ''): ?><div class="csd-eye notranslate"><?= htmlspecialchars($T('tagline')) ?></div><?php endif; ?>
          <h1 class="csd-h1"><?= htmlspecialchars($T('headline') ?: ($p['judul'] ?? '')) ?></h1>
          <?php if (trim((string)$T('body')) !== ''): ?><p class="csd-lead"><?= htmlspecialchars($T('body')) ?></p><?php endif; ?>
          <div class="sxd-btns">
            <a class="csd-cta" href="<?= htmlspecialchars(url($btn1[1])) ?>"><?= htmlspecialchars($btn1[0]) ?> <?= $arrow ?></a>
            <?php if ($btn2): ?><a class="csd-cta2" href="<?= htmlspecialchars(url($btn2[1])) ?>"><?= htmlspecialchars($btn2[0]) ?></a><?php endif; ?>
          </div>
        </div>
        <?php if (!empty($C['glance'])): ?>
        <aside class="svc-glance">
          <div class="gl-eye"><?= htmlspecialchars(t('at_a_glance','At a Glance')) ?></div>
          <div class="gl-grid">
            <?php foreach ($C['glance'] as $g): ?>
              <div class="gl-cell"><div class="gl-n notranslate"><?= htmlspecialchars($g[0]) ?></div><div class="gl-c"><?= htmlspecialchars($g[1]) ?></div></div>
            <?php endforeach; ?>
          </div>
        </aside>
        <?php endif; ?>
      </div>
    </section>
    <?php
};

$render_cross = function () use ($C, $arrow) {
    if (empty($C['cross'])) return; $x = $C['cross']; ?>
    <section class="svc-cross">
      <div class="xc-inner">
        <div class="xc-txt"><h3><?= htmlspecialchars($x[0]) ?></h3><p><?= htmlspecialchars($x[1]) ?></p></div>
        <a class="xc-btn" href="<?= htmlspecialchars(url($x[2][1])) ?>"><?= htmlspecialchars($x[2][0]) ?> <?= $arrow ?></a>
      </div>
    </section>
    <?php
};

$render_final = function () use ($C, $T, $p, $arrow) {
    $f = $C['final'] ?? ['One Partner. End-to-End Capability.','', [($T('cta_label') ?: 'Contact Us'), ($p['cta_target'] ?: 'contact-us')]];
    ?>
    <section class="csd-finale">
      <h2><?= htmlspecialchars($f[0]) ?></h2>
      <?php if (!empty($f[1])): ?><p class="fin-sub"><?= htmlspecialchars($f[1]) ?></p><?php endif; ?>
      <a class="csd-fbtn" href="<?= htmlspecialchars(url($f[2][1])) ?>"><?= htmlspecialchars($f[2][0]) ?> <?= $arrow ?></a>
    </section>
    <?php
};

include theme_path('templates/layouts/header.php');
?>
<?php if ($tipe === 'capability'): /* ===== capability: areas + plan teaser ===== */ ?>
<main class="page-body csd csd2<?= !empty($C['alert']) ? ' has-alert' : '' ?>">

  <?php $render_hero(); ?>

  <?php if ($areas): $ah = $C['areas_head'] ?? ['Service Areas','Three areas, one accountable team','']; ?>
  <section class="svc-areas">
    <div class="svc-sec-head">
      <div class="eye"><?= htmlspecialchars($ah[0]) ?></div>
      <h2><?= htmlspecialchars($ah[1]) ?></h2>
      <?php if (!empty($ah[2])): ?><p><?= htmlspecialchars($ah[2]) ?></p><?php endif; ?>
    </div>
    <div class="svc-areagrid">
      <?php foreach ($areas as $i => $a): $aid = (int)$a['id'];
        $TA = fn($f) => tr_field('service_areas', $aid, $f, $a[$f] ?? '');
        $items = $a['items'] ?? []; $tot = count($items); $rest = max(0, $tot - 4); ?>
        <article class="svc-areacard pkg-reveal">
          <div class="ac-kick"><span class="num"><?= htmlspecialchars($a['kode'] ?: sprintf('%02d',$i+1)) ?></span><span class="sep">·</span><span class="code notranslate"><?= htmlspecialchars($a['nama'] ?? '') ?></span></div>
          <h3><?= htmlspecialchars($TA('judul')) ?></h3>
          <?php if (trim((string)$TA('deskripsi')) !== ''): ?><p class="ac-desc"><?= htmlspecialchars($TA('deskripsi')) ?></p><?php endif; ?>
          <?php if ($items): ?>
          <ul class="ac-caps">
            <?php foreach ($items as $k => $it): ?><li<?= $k >= 4 ? ' class="extra"' : '' ?>><span class="chk"><?= $check ?></span><span><?= htmlspecialchars(tr_field('service_area_items',(int)$it['id'],'teks',$it['teks'])) ?></span></li><?php endforeach; ?>
          </ul>
          <?php endif; ?>
          <?php if ($rest > 0): ?>
            <button type="button" class="ac-more" aria-expanded="false">
              <span class="ac-lbl"><span class="more">+ <?= (int)$rest ?> more</span><span class="less"><?= htmlspecialchars(t('show_less','Show less')) ?></span> · <?= (int)$tot ?> <?= htmlspecialchars(t('capabilities','capabilities')) ?> in total</span>
              <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            </button>
          <?php elseif ($tot): ?><div class="ac-more static"><?= (int)$tot ?> <?= htmlspecialchars(t('capabilities','capabilities')) ?> in total</div><?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($C['plan'])): $pl = $C['plan']; ?>
  <section class="svc-plan">
    <div class="svc-sec-head">
      <div class="eye"><?= htmlspecialchars($pl['eye']) ?></div>
      <h2><?= htmlspecialchars($pl['h']) ?></h2>
      <?php if (!empty($pl['sub'])): ?><p><?= htmlspecialchars($pl['sub']) ?></p><?php endif; ?>
    </div>
    <div class="pkg-tiers">
      <?php foreach ($pl['tiers'] as $tr): $feat = !empty($tr[2]); ?>
        <div class="pkg-tier pkg-reveal<?= $feat ? ' feat' : '' ?>">
          <?php if ($feat): ?><span class="flag"><?= htmlspecialchars(t('recommended','Recommended')) ?></span><?php endif; ?>
          <div class="nm notranslate"><?= htmlspecialchars($tr[0]) ?></div>
          <h3><?= htmlspecialchars($tr[1]) ?></h3>
          <ul class="pt-bullets">
            <?php foreach ($tr[3] as $b): ?><li><span class="chk"><?= $check ?></span><span><?= htmlspecialchars($b) ?></span></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if (!empty($pl['note'])): ?><div class="svc-plan-note"><?= htmlspecialchars($pl['note']) ?></div><?php endif; ?>
    <?php if (!empty($pl['cta'])): ?><div class="svc-plan-cta"><a class="csd-cta2 wide" href="<?= htmlspecialchars(url($pl['cta'][1])) ?>"><?= htmlspecialchars($pl['cta'][0]) ?> <?= $arrow ?></a></div><?php endif; ?>
  </section>
  <?php endif; ?>

  <?php $render_cross(); ?>
  <?php $render_final(); ?>

</main>
<script src="<?= theme_asset('assets/js/svcdetail.js') ?>" defer></script>

<?php else: /* ===== package: compare matrix + steps ===== */ ?>
<main class="page-body csd csd2<?= !empty($C['alert']) ? ' has-alert' : '' ?>">

  <?php $render_hero(); ?>

  <?php if ($matrix):
    $tier_labels = [];
    foreach ($tiers as $tr) { $tier_labels[] = $tr; }
    $ph = $C['plan_head'] ?? ['Choose Your Plan','Compare plans in detail',''];
    $feat_idx = 1; // Platinum
  ?>
  <section class="pkg-cmp-sec">
    <div class="svc-sec-head">
      <div class="eye"><?= htmlspecialchars($ph[0]) ?></div>
      <h2><?= htmlspecialchars($ph[1]) ?></h2>
      <?php if (!empty($ph[2])): ?><p><?= htmlspecialchars($ph[2]) ?></p><?php endif; ?>
    </div>
    <div class="pkg-cmp pkg-cmp-rich">
      <div class="crow head">
        <div class="cmpintro"><?= htmlspecialchars(t('whats_different',"What's Different")) ?></div>
        <?php foreach ($tiers as $ti => $tr): $tid=(int)$tr['id']; $isf = ($ti===$feat_idx); ?>
          <div class="tcol<?= $isf ? ' feat' : '' ?>">
            <?php if ($isf): ?><span class="tflag"><?= htmlspecialchars(t('recommended','Recommended')) ?></span><?php endif; ?>
            <span class="tn notranslate"><?= htmlspecialchars($tr['nama']) ?></span>
            <span class="tt"><?= htmlspecialchars(tr_field('service_tiers',$tid,'judul',$tr['judul'] ?? '')) ?></span>
            <?php $td = tr_field('service_tiers',$tid,'deskripsi',$tr['deskripsi'] ?? ''); if (trim((string)$td)!==''): ?><span class="td"><?= htmlspecialchars($td) ?></span><?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <?php foreach ($mgroups as $gname => $rows): ?>
        <?php if ($gname !== ''): ?><div class="cgroup"><?= htmlspecialchars($gname) ?></div><?php endif; ?>
        <?php foreach ($rows as $r): ?>
          <div class="crow item pkg-reveal">
            <div><?= htmlspecialchars(tr_field('service_matrix',(int)$r['id'],'baris',$r['baris'])) ?></div>
            <div><?= $cell($r['v_gold']) ?></div><div class="feat"><?= $cell($r['v_platinum']) ?></div><div><?= $cell($r['v_diamond']) ?></div>
          </div>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($C['steps'])): $st = $C['steps']; ?>
  <section class="svc-steps">
    <div class="svc-sec-head">
      <div class="eye"><?= htmlspecialchars($st[0]) ?></div>
      <h2><?= htmlspecialchars($st[1]) ?></h2>
    </div>
    <div class="svc-steprow">
      <?php foreach ($st[2] as $k => $s): ?>
        <div class="svc-step pkg-reveal"><span class="sn"><?= sprintf('%02d',$k+1) ?></span><h4><?= htmlspecialchars($s[0]) ?></h4><p><?= htmlspecialchars($s[1]) ?></p></div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php $render_cross(); ?>
  <?php $render_final(); ?>

</main>
<script src="<?= theme_asset('assets/js/svcdetail.js') ?>" defer></script>
<?php endif; ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
