<?php
/**
 * Anima theme — Service detail (route: /layanan/[slug]). Built from the Figma "Managed SOC" design.
 * Renders a tier comparison matrix (Diamond/Platinum/Gold). The tier data is a reusable structure:
 * in the CMS it will come from a service-tier data model ($layanan_data['tiers']/['groups']); the
 * defaults below (Managed SOC) render when no DB data is supplied (e.g. preview).
 * TODO(CMS): schema for service tiers + admin editor (§ service tier tables). Shares header/footer.
 */
if (!isset($db) && class_exists('Database')) { $db = Database::getInstance(); }

$svc  = $layanan_data ?? [];
$name  = $svc['nama']      ?? 'Managed SOC as a Service';
$intro = $svc['deskripsi'] ?? 'A managed Security Operations Center (SOC) service providing continuous proactive monitoring, analysis, and incident response to protect your company’s digital infrastructure.';
$hero  = $svc['gambar']    ?? '';

$seo = ['title' => $name . ' — ' . get_setting('site_name', 'Sapta Tunas Teknologi'),
        'description' => mb_substr(strip_tags($intro), 0, 160)];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');

$tiers = $svc['tiers'] ?? [
  ['name'=>'Diamond',  'cls'=>'diamond',  'hl'=>true],
  ['name'=>'Platinum', 'cls'=>'platinum', 'hl'=>false],
  ['name'=>'Gold',     'cls'=>'gold',     'hl'=>false],
];
$groups = $svc['groups'] ?? [
  ['title'=>'Security Monitoring & Incident Response','rows'=>[
    ['Security Monitoring SLA','Continuous monitoring services for cybersecurity threats.',['24x7','24x7','8x5']],
    ['Log Monitoring Coverage','The maximum number of assets or log sources monitored by the SOC system.',['Up to 300 Assets/Log Sources','Up to 150 Assets/Log Sources','Up to 75 Assets/Log Sources']],
    ['Response Time (MTTA)','Guaranteed initial response time limit (Mean Time to Acknowledge) to identify and respond to threats.',['15 Min','30 Min','1 Hour']],
    ['Incident Handling','The level of technical handling and mitigation provided during a cyber incident.',['Fully Managed','Co-Managed','No']],
    ['Log Retention','The storage duration for historical security log data for audit and investigative purposes.',['90 Days','90 Days','90 Days']],
    ['Security Reporting','The frequency of periodic cybersecurity analysis reports provided to company management.',['Monthly & Quarterly','Monthly','Quarterly']],
    ['Threat Hunting','Proactive searching for hidden cyber threats without waiting for system alerts.',['Yes','No','No']],
    ['Infosec Advisory','Strategic consulting and recommendations on information security from cyber experts.',['Yes','Yes','No']],
  ]],
  ['title'=>'Advanced Services','rows'=>[
    ['Vulnerability Assessment','Periodic testing to detect security vulnerabilities in systems and networks.',['Yes','Yes','No']],
    ['Digital Forensic Capabilities','Digital forensic analysis and investigation capabilities to trace cyber incident footprints.',['Yes','Yes','No']],
    ['Penetration Testing','Controlled simulations of real-world cyberattacks to evaluate system defense reliability.',['Yes','No','No']],
    ['Cyber Drill / Tabletop Exercise','Simulated training and cyber crisis exercises to test the internal team readiness in responding to incidents.',['Yes','No','No']],
  ]],
];

$medals = [
  'diamond'  => '<svg viewBox="0 0 24 24"><path d="M6 3h12l3 6-9 12L3 9z"/><path d="M3 9h18M9 3l-3 6 6 12 6-12-3-6"/></svg>',
  'platinum' => '<svg viewBox="0 0 24 24"><path d="M12 3l2.9 5.9 6.1.9-4.5 4.4 1 6.1-5.5-2.9-5.5 2.9 1-6.1L3 9.8l6.1-.9z"/></svg>',
  'gold'     => '<svg viewBox="0 0 24 24"><path d="M4 8l4 3 4-6 4 6 4-3-2 10H6z"/></svg>',
];
$rowIcon = '<svg viewBox="0 0 24 24"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/></svg>';
$check   = '<svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>';

/** Render one tier value cell. */
function tier_cell($val, $hl) {
  if ($val === 'Yes') return '<span class="tval-yes"><svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg></span>';
  if ($val === 'No')  return '<span class="tval-no">&mdash;</span>';
  return '<span class="' . ($hl ? 'tval-hl' : '') . '">' . htmlspecialchars($val) . '</span>';
}
?>
<main class="page-body">
  <div class="page-shell">

    <section class="svc-hero">
      <h1><?= htmlspecialchars($name) ?></h1>
      <p><?= htmlspecialchars($intro) ?></p>
    </section>

    <div class="svc-media">
      <?php if ($hero): ?>
        <img src="<?= htmlspecialchars($hero) ?>" alt="<?= htmlspecialchars($name) ?>" data-fallback="remove">
      <?php else: ?>
        <div class="ph"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M8 20h8M12 16v4"/></svg></div>
      <?php endif; ?>
    </div>

    <?php foreach ($groups as $gi => $g): ?>
    <div class="tier-block">
      <div class="tier-scroll">
        <table class="tier">
          <thead>
            <tr>
              <th class="feat-h"><?= htmlspecialchars($g['title']) ?></th>
              <?php foreach ($tiers as $t): ?>
                <th class="<?= !empty($t['hl']) ? 'hl' : '' ?>">
                  <span class="tier-badge">
                    <span class="medal <?= htmlspecialchars($t['cls']) ?>"><?= $medals[$t['cls']] ?? '' ?></span>
                    <?= htmlspecialchars($t['name']) ?>
                  </span>
                </th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($g['rows'] as $row): [$fname, $fdesc, $vals] = $row; ?>
            <tr>
              <td class="feat">
                <div class="fi">
                  <span class="fic"><?= $rowIcon ?></span>
                  <span><b><?= htmlspecialchars($fname) ?></b><span><?= htmlspecialchars($fdesc) ?></span></span>
                </div>
              </td>
              <?php foreach ($vals as $ci => $val): ?>
                <td class="<?= !empty($tiers[$ci]['hl']) ? 'hl' : '' ?>"><?= tier_cell($val, !empty($tiers[$ci]['hl'])) ?></td>
              <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
