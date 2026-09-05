<?php
/**
 * Anima theme — About Us (route: /tentang-kami). Built from the Figma "About Us" design.
 * Singular copy is editable via ac('about', key). Repeaters (mission, values, milestones, awards,
 * quality, certs) are defaults here for now — TODO: bind to CMS repeater/gallery modules.
 * Shares layouts/header.php (nav solid) + layouts/footer.php.
 */
if (!isset($db) && class_exists('Database')) { $db = Database::getInstance(); }
$seo = [
  'title'       => get_setting('site_title_about', 'About Us — ' . get_setting('site_name', 'Sapta Tunas Teknologi')),
  'description' => get_setting('site_description_about', 'Sapta Tunas Teknologi — Enterprise Solution Provider sejak 2015: infrastruktur, cloud, cybersecurity, data & AI.'),
];
$anima_body_class = 'page-inner';
include theme_path('templates/layouts/header.php');

$mission = [
  'Build a wide and constructive relationship with client for the mutual long-term business achievement.',
  'Endless learning to ensure the high quality of people performance through time.',
  'Nurture the high commitment of honesty, integrity, and professional ethics to achieve the highest value to stakeholder.',
  'Ensure excellent and reliable support to clients.',
  'Always making innovations to provide our clients with the best latest technologies.',
  'Responsible and maintain our core values to ensure customer success.',
];
$values = [
  ['I', 'INTEGRITY',      'Employ high ethical standards, demonstrating honesty and fairness.'],
  ['C', 'COLLABORATE',    'Coming together is a beginning, keeping together is progress, working together is success.'],
  ['A', 'ACCOUNTABILITY', 'Responsibility for our decision and actions.'],
  ['R', 'RESPONSIVE',     'Swift attitude to ensure the best service response and service level to our business partner.'],
  ['E', 'EXCELLENCE',     'Striving for the best in every aspect of the business solution.'],
];
$milestones = ['2015', '2017', '2023', '2025', 'Present'];
$awards = [
  ['Dana Indonesia', 'Best Performing Vendor 2022'],
  ['PT Bintang Toedjoe', 'Best Platinum Vendor Award 2023'],
  ['PT Saka Farma Laboratories', 'Excellent Vendor Award 2024'],
  ['PT Bintang Toedjoe', 'Best Platinum Vendor Award 2024'],
  ['PT Pratha Widyahusada Tbk', 'Vendor Excellence Award 2024'],
  ['PT Kalbe Morinaga Indonesia', 'Excellent Vendor Performance Award 2025'],
];
$quality = [ ['ISO 9001', 'ISO 14001', 'ISO 45001'], ['ISO 37001'], ['ISO 27001'] ];
$certs = ['Dell Technologies', 'VMware', 'Microsoft', 'Nutanix', 'Red Hat', 'Veeam'];

$trophy = '<svg viewBox="0 0 24 24"><path d="M6 4h12v3a6 6 0 01-12 0V4z"/><path d="M6 5H3v2a3 3 0 003 3M18 5h3v2a3 3 0 01-3 3M9 20h6M12 13v7"/></svg>';
$check  = '<svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>';
?>
<main class="page-body">
  <div class="page-shell ab">

    <!-- Intro -->
    <section class="ab-intro">
      <div class="eyebrow"><?= ac('about', 'intro_eyebrow') ?></div>
      <h1><?= ac('about', 'intro_title') ?></h1>
      <p class="lead"><?= ac('about', 'intro_body', true) ?></p>
    </section>

    <!-- Vision + Mission -->
    <section class="ab-sec ab-vm">
      <div class="ab-vcard">
        <h2><?= ac('about', 'vision_title') ?></h2>
        <p><?= ac('about', 'vision_body') ?></p>
      </div>
      <div class="ab-mcard">
        <h2><?= ac('about', 'mission_title') ?></h2>
        <ul class="ab-mlist">
          <?php foreach ($mission as $m): ?>
            <li><span class="chk"><?= $check ?></span><span><?= htmlspecialchars($m) ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>

    <!-- Values (ICARE) -->
    <section class="ab-sec">
      <div class="ab-head">
        <h2><?= ac('about', 'values_eyebrow') ?> <span class="blue"><?= ac('about', 'values_title', true) ?></span></h2>
      </div>
      <div class="ab-values-grid">
        <?php foreach ($values as $v): ?>
          <div class="ab-val">
            <div class="badge"><?= htmlspecialchars($v[0]) ?></div>
            <h3><?= htmlspecialchars($v[1]) ?></h3>
            <p><?= htmlspecialchars($v[2]) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Milestone -->
    <section class="ab-sec">
      <div class="ab-mile">
        <div class="ab-mile-body">
          <h2><?= ac('about', 'milestone_title', true) ?></h2>
          <p><?= ac('about', 'milestone_body', true) ?></p>
        </div>
        <div class="ab-mile-media" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6"/></svg>
        </div>
      </div>
      <div class="ab-timeline">
        <?php foreach ($milestones as $yr): $now = ($yr === 'Present'); ?>
          <div class="ab-tnode<?= $now ? ' now' : '' ?>"><span class="dot"></span><div class="yr"><?= htmlspecialchars($yr) ?></div></div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Awards -->
    <section class="ab-sec">
      <div class="ab-head">
        <h2><?= ac('about', 'awards_title') ?></h2>
        <p><?= ac('about', 'awards_intro') ?></p>
      </div>
      <div class="ab-row">
        <?php foreach ($awards as $a): ?>
          <div class="ab-award">
            <div class="ph"><?= $trophy ?></div>
            <div class="org"><?= htmlspecialchars($a[0]) ?></div>
            <div class="ttl"><?= htmlspecialchars($a[1]) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Quality Standards -->
    <section class="ab-sec">
      <div class="ab-head">
        <h2><?= ac('about', 'quality_title', true) ?></h2>
        <p><?= ac('about', 'quality_intro') ?></p>
      </div>
      <div class="ab-row">
        <?php foreach ($quality as $group): ?>
          <div class="ab-iso">
            <?php foreach ($group as $iso): ?><span class="b"><?= htmlspecialchars($iso) ?></span><?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Certifications -->
    <section class="ab-sec">
      <div class="ab-head">
        <h2><?= ac('about', 'certs_title', true) ?></h2>
        <p><?= ac('about', 'certs_intro') ?></p>
      </div>
      <div class="ab-row">
        <?php foreach ($certs as $c): ?>
          <div class="ab-iso"><span class="b"><?= htmlspecialchars($c) ?></span></div>
        <?php endforeach; ?>
      </div>
    </section>

  </div>
</main>
<?php include theme_path('templates/layouts/footer.php'); ?>
