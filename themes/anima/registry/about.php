<?php
/**
 * Anima — About Us content registry (editable singular copy).
 * Repeaters (mission bullets, ICARE values, milestones, awards, quality/cert items) are rendered from
 * arrays in about.php for now, with a TODO to bind them to CMS repeater/gallery modules later.
 */
return [
  'intro_eyebrow'  => ['label'=>'Eyebrow',        'type'=>'text','group'=>'Intro','default'=>'About Us'],
  'intro_title'    => ['label'=>'Judul',          'type'=>'text','group'=>'Intro','default'=>'Enterprise Solution Provider'],
  'intro_body'     => ['label'=>'Paragraf intro', 'type'=>'html','group'=>'Intro','default'=>'Welcome to Sapta Tunas Teknologi which is established in 2015 with high passion and commitment for providing Business Technology Solutions and services in Indonesia. Sapta Tunas Teknologi is Enterprise Solution Provider, we focus solely on embracing our client\'s hardware, networking, application and technology needs. Helping organizations across all industry to achieve their business goals and objectives by lowering the cost and maximizing the performance of their information technology systems.'],

  'vision_title'   => ['label'=>'Vision — judul', 'type'=>'text','group'=>'Vision & Mission','default'=>'Our Vision'],
  'vision_body'    => ['label'=>'Vision — isi',   'type'=>'text','group'=>'Vision & Mission','default'=>'To become a profound and innovative leading IT Solution Provider with excellent and professional support to meet clients\' needs.'],
  'mission_title'  => ['label'=>'Mission — judul','type'=>'text','group'=>'Vision & Mission','default'=>'Our Mission'],

  'values_eyebrow' => ['label'=>'Value — eyebrow','type'=>'text','group'=>'Values','default'=>'Our'],
  'values_title'   => ['label'=>'Value — judul',  'type'=>'html','group'=>'Values','default'=>'Value'],

  'milestone_title'=> ['label'=>'Milestone — judul','type'=>'html','group'=>'Milestone','default'=>'Our <span class="blue">Milestone</span>'],
  'milestone_body' => ['label'=>'Milestone — isi', 'type'=>'html','group'=>'Milestone','default'=>'Since its establishment in 2015, Sapta Tunas Teknologi has consistently built a robust and reliable technology ecosystem across Indonesia. Backed by an extensive network of strategic partnerships with global leaders such as Dell Technologies Titanium Partner, we specialize in Enterprise Infrastructure, Cloud, Cybersecurity, Data Management, and AI solutions.<br><br>Having successfully empowered hundreds of enterprise clients across various industries, our team of certified engineers is dedicated to guiding your company\'s digital transformation journey every step of the way to achieve sustainable business growth.'],

  'awards_title'   => ['label'=>'Awards — judul',   'type'=>'text','group'=>'Awards','default'=>'Awards'],
  'awards_intro'   => ['label'=>'Awards — intro',   'type'=>'text','group'=>'Awards','default'=>'We showcase the revolutionary creations, latest developments, and technology solutions that are changing the way we live, work, and interact with the world around us.'],

  'quality_title'  => ['label'=>'Quality — judul',  'type'=>'html','group'=>'Quality','default'=>'Our Quality <span class="blue">Standards</span>'],
  'quality_intro'  => ['label'=>'Quality — intro',  'type'=>'text','group'=>'Quality','default'=>'We take pride in our remarkable achievements, including surpassing sales targets and consistently delivering exceptional customer satisfaction.'],

  'certs_title'    => ['label'=>'Cert — judul',     'type'=>'html','group'=>'Certifications','default'=>'List of <span class="blue">Certification</span>'],
  'certs_intro'    => ['label'=>'Cert — intro',     'type'=>'text','group'=>'Certifications','default'=>'Our team holds top-tier vendor certifications, ensuring every solution is delivered with proven expertise.'],
];
