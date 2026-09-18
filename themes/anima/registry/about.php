<?php
/**
 * Anima — About Us content registry (editable singular copy).
 * Repeaters (mission bullets, ICARE values, milestones, awards, quality/cert items) are rendered from
 * arrays in about.php for now, with a TODO to bind them to CMS repeater/gallery modules later.
 */
return [
  'intro_eyebrow'  => ['label'=>'Eyebrow',        'type'=>'text','group'=>'Intro','default'=>'About Us'],
  'intro_title'    => ['label'=>'Judul',          'type'=>'text','group'=>'Intro','default'=>'Enterprise Solution Provider'],
  'intro_body'     => ['label'=>'Paragraf intro', 'type'=>'html','group'=>'Intro','default'=>'Welcome to Sapta Tunas Teknologi. Established in 2015, we are a leading Enterprise Solution Provider dedicated to advancing Business Technology Solutions and Services in Indonesia. We empower organizations across all industries to achieve their goals through efficient IT costs and optimized system performance. Today, we focus on equipping your business to be future-ready by delivering:'],
  'intro_deliver'  => ['label'=>'Intro — daftar pilar','type'=>'html','group'=>'Intro','default'=>'<ul class="ab-deliver"><li><strong>Modernize Infrastructure</strong> — Building scalable and agile foundations</li><li><strong>Cybersecurity</strong> — Protecting your most valuable digital assets</li><li><strong>Data Management</strong> — Organizing and leveraging your data effectively</li><li><strong>Artificial Intelligence (AI)</strong> — Driving smarter, future-ready business outcomes</li><li><strong>AI Platform &amp; Applications</strong> — Transforming AI capabilities into secure, scalable, and business-ready applications</li></ul>'],

  'vision_img1'    => ['label'=>'Gambar kiri — atas', 'type'=>'image','group'=>'Vision & Mission','default'=>''],
  'vision_img2'    => ['label'=>'Gambar kiri — bawah','type'=>'image','group'=>'Vision & Mission','default'=>''],
  'vision_title'   => ['label'=>'Vision — judul', 'type'=>'text','group'=>'Vision & Mission','default'=>'Our Vision'],
  'vision_body'    => ['label'=>'Vision — isi',   'type'=>'text','group'=>'Vision & Mission','default'=>'To become a profound and innovative leading Enterprise Solution Provider with excellent and professional support to meet clients\' needs.'],
  'mission_title'  => ['label'=>'Mission — judul','type'=>'text','group'=>'Vision & Mission','default'=>'Our Mission'],

  'values_eyebrow' => ['label'=>'Value — eyebrow','type'=>'text','group'=>'Values','default'=>'Our'],
  'values_title'   => ['label'=>'Value — judul',  'type'=>'html','group'=>'Values','default'=>'Value'],

  'milestone_title'=> ['label'=>'Milestone — judul','type'=>'html','group'=>'Milestone','default'=>'Our <span class="blue">Milestone</span>'],
  'milestone_body' => ['label'=>'Milestone — isi', 'type'=>'html','group'=>'Milestone','default'=>'Since 2015, Sapta Tunas Teknologi has consistently built a robust technology ecosystem across Indonesia. As a top-tier partner with global leaders, we specialize in Modernize Infrastructure, Data Management, Cybersecurity, and AI Solutions.<br><br>Having successfully empowered hundreds of enterprise clients across various industries, our team of certified engineers is dedicated to guiding your company\'s digital transformation journey every step of the way to achieve sustainable business growth.'],

  'awards_title'   => ['label'=>'Awards — judul',   'type'=>'text','group'=>'Awards','default'=>'Awards'],
  'awards_intro'   => ['label'=>'Awards — intro',   'type'=>'text','group'=>'Awards','default'=>'We showcase the revolutionary creations, latest developments, and technology solutions that are changing the way we live, work, and interact with the world around us.'],

  'quality_title'  => ['label'=>'Quality — judul',  'type'=>'html','group'=>'Quality','default'=>'Our Quality <span class="blue">Standards</span>'],
  'quality_intro'  => ['label'=>'Quality — intro',  'type'=>'text','group'=>'Quality','default'=>'Our commitment to quality and excellence is reflected in the international standards we uphold.'],

  'certs_title'    => ['label'=>'Cert — judul',     'type'=>'html','group'=>'Certifications','default'=>'List of <span class="blue">Certification</span>'],
  'certs_intro'    => ['label'=>'Cert — intro',     'type'=>'text','group'=>'Certifications','default'=>'We take pride in our remarkable achievements, including surpassing sales targets and consistently delivering exceptional customer satisfaction.'],
];
