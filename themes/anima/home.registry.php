<?php
/**
 * Anima — Home content registry.
 * Every editable Home text field: key => [label, type(text|html|image), group, default].
 * Used by hc() for defaults, and (later) by the admin content editor to auto-generate the
 * per-section edit form (§14 "content registry per template"). Repeaters that map to CMS
 * entities (news→blog, testimonials→testimonial module, hero/orbit/prism→JS data injection)
 * are intentionally NOT here yet — they bind to their modules in a later pass.
 */
return [
  // ---- Hero (static sub-copy below the rotating slider) ----
  'hero_subcopy'        => ['label'=>'Hero sub-teks',        'type'=>'html', 'group'=>'Hero', 'default'=>'Sapta Tunas Teknologi is established in 2015 with high passion and commitment for <b>providing Business Technology Solutions and Services</b> in Indonesia.'],

  // ---- Discover Our Company ----
  'discover_heading'    => ['label'=>'Discover — judul',     'type'=>'text', 'group'=>'Discover', 'default'=>'Discover Our Company'],
  'discover_portfolio'  => ['label'=>'Discover — link 1',    'type'=>'text', 'group'=>'Discover', 'default'=>'Our Portfolio'],
  'discover_news'       => ['label'=>'Discover — link 2',    'type'=>'text', 'group'=>'Discover', 'default'=>'News'],
  'discover_solution'   => ['label'=>'Discover — link 3',    'type'=>'text', 'group'=>'Discover', 'default'=>'Our Solution'],
  'discover_why'        => ['label'=>'Discover — link 4',    'type'=>'text', 'group'=>'Discover', 'default'=>'Why Us'],
  'discover_industries' => ['label'=>'Discover — link 5',    'type'=>'text', 'group'=>'Discover', 'default'=>'Our Industries'],
  'discover_testi'      => ['label'=>'Discover — link 6',    'type'=>'text', 'group'=>'Discover', 'default'=>'What They Say About Us?'],

  // ---- Portfolio ----
  'portfolio_eyebrow'   => ['label'=>'Portfolio — eyebrow',  'type'=>'text', 'group'=>'Portfolio', 'default'=>'Our Track Record'],
  'portfolio_title'     => ['label'=>'Portfolio — judul',    'type'=>'html', 'group'=>'Portfolio', 'default'=>'Our <span class="hl">Portfolio</span>'],
  'portfolio_lead'      => ['label'=>'Portfolio — lead',     'type'=>'text', 'group'=>'Portfolio', 'default'=>'The numbers behind our journey delivering business technology solutions across Indonesia since 2015.'],
  'pf1_num'  => ['label'=>'Stat 1 — angka', 'type'=>'text','group'=>'Portfolio','default'=>'425+'],
  'pf1_label'=> ['label'=>'Stat 1 — label', 'type'=>'text','group'=>'Portfolio','default'=>'Project'],
  'pf1_desc' => ['label'=>'Stat 1 — deskripsi','type'=>'text','group'=>'Portfolio','default'=>'Delivered projects across enterprise and government sectors.'],
  'pf2_num'  => ['label'=>'Stat 2 — angka', 'type'=>'text','group'=>'Portfolio','default'=>'200+'],
  'pf2_label'=> ['label'=>'Stat 2 — label', 'type'=>'text','group'=>'Portfolio','default'=>'Clients'],
  'pf2_desc' => ['label'=>'Stat 2 — deskripsi','type'=>'text','group'=>'Portfolio','default'=>'Clients trusting Sapta Tunas for their technology transformation.'],
  'pf3_num'  => ['label'=>'Stat 3 — angka', 'type'=>'text','group'=>'Portfolio','default'=>'100%'],
  'pf3_label'=> ['label'=>'Stat 3 — label', 'type'=>'text','group'=>'Portfolio','default'=>'Satisfaction'],
  'pf3_desc' => ['label'=>'Stat 3 — deskripsi','type'=>'text','group'=>'Portfolio','default'=>'Customer satisfaction driven by our success-first approach.'],
  'pf4_num'  => ['label'=>'Stat 4 — angka', 'type'=>'text','group'=>'Portfolio','default'=>'26'],
  'pf4_label'=> ['label'=>'Stat 4 — label', 'type'=>'text','group'=>'Portfolio','default'=>'Awards'],
  'pf4_desc' => ['label'=>'Stat 4 — deskripsi','type'=>'text','group'=>'Portfolio','default'=>'Industry awards recognising our delivery and expertise.'],

  // ---- Statement ----
  'statement_text'      => ['label'=>'Statement',            'type'=>'text', 'group'=>'Statement', 'default'=>'Sapta Tunas Teknologi is established in 2015 with high passion and commitment for providing Business Technology Solutions and Services in Indonesia.'],

  // ---- Industries (orbit center + card labels) ----
  'industries_eyebrow'  => ['label'=>'Industries — eyebrow', 'type'=>'text', 'group'=>'Industries', 'default'=>'Industries We Serve'],
  'industries_title'    => ['label'=>'Industries — judul',   'type'=>'html', 'group'=>'Industries', 'default'=>'Our <b>Industries</b>'],
  'industries_sub'      => ['label'=>'Industries — sub',     'type'=>'text', 'group'=>'Industries', 'default'=>'Solusi teknologi untuk beragam industri.'],
  'ind1' => ['label'=>'Industri 1','type'=>'text','group'=>'Industries','default'=>'Financial'],
  'ind2' => ['label'=>'Industri 2','type'=>'text','group'=>'Industries','default'=>'Education'],
  'ind3' => ['label'=>'Industri 3','type'=>'text','group'=>'Industries','default'=>'Healthcare'],
  'ind4' => ['label'=>'Industri 4','type'=>'text','group'=>'Industries','default'=>'Law Enforce'],
  'ind5' => ['label'=>'Industri 5','type'=>'text','group'=>'Industries','default'=>'Manufacture'],
  'ind6' => ['label'=>'Industri 6','type'=>'text','group'=>'Industries','default'=>'Telecom'],
  'ind7' => ['label'=>'Industri 7','type'=>'text','group'=>'Industries','default'=>'Energy'],
  'ind8' => ['label'=>'Industri 8','type'=>'text','group'=>'Industries','default'=>'Cross Industry'],

  // ---- News (section chrome; cards bind to blog later) ----
  'news_heading'        => ['label'=>'News — judul',         'type'=>'text', 'group'=>'News', 'default'=>'News'],
  'news_intro'          => ['label'=>'News — intro',         'type'=>'text', 'group'=>'News', 'default'=>'Explore the latest news, updates, and innovations driving the future of enterprise tech.'],

  // ---- Why Us ----
  'why_eyebrow'         => ['label'=>'Why — eyebrow',        'type'=>'text', 'group'=>'Why Us', 'default'=>'Why Sapta Tunas'],
  'why_title'           => ['label'=>'Why — judul',          'type'=>'html', 'group'=>'Why Us', 'default'=>'What Sets Us <span class="b">Apart</span>'],
  'why_intro'           => ['label'=>'Why — intro',          'type'=>'text', 'group'=>'Why Us', 'default'=>'Alasan enterprise mempercayakan transformasi teknologinya pada STT.'],
  'why1_title'=> ['label'=>'Why 1 — judul','type'=>'text','group'=>'Why Us','default'=>'Tailored Solutions'],
  'why1_sub'  => ['label'=>'Why 1 — sub',  'type'=>'text','group'=>'Why Us','default'=>'10+ tahun pengalaman, solusi dirancang khusus untukmu'],
  'why2_title'=> ['label'=>'Why 2 — judul','type'=>'text','group'=>'Why Us','default'=>'Broad Range of Solution & Service'],
  'why2_sub'  => ['label'=>'Why 2 — sub',  'type'=>'text','group'=>'Why Us','default'=>'Dari infrastruktur, cloud, hingga AI dalam satu atap'],
  'why3_title'=> ['label'=>'Why 3 — judul','type'=>'text','group'=>'Why Us','default'=>'AI-Powered Assistance'],
  'why3_sub'  => ['label'=>'Why 3 — sub',  'type'=>'text','group'=>'Why Us','default'=>'SatuAI siap membantu kapan pun kamu butuh'],
  'why4_title'=> ['label'=>'Why 4 — judul','type'=>'text','group'=>'Why Us','default'=>'Customer Satisfaction'],
  'why4_sub'  => ['label'=>'Why 4 — sub',  'type'=>'text','group'=>'Why Us','default'=>'Success-first, komitmen penuh di tiap proyek'],

  // ---- Testimonials (section chrome; cards bind to testimonial module later) ----
  'testi_eyebrow'       => ['label'=>'Testimoni — eyebrow',  'type'=>'text', 'group'=>'Testimonials', 'default'=>'Customer Testimonials'],
  'testi_title'         => ['label'=>'Testimoni — judul',    'type'=>'html', 'group'=>'Testimonials', 'default'=>'What They Say <span class="b">About Us?</span>'],
  'testi_intro'         => ['label'=>'Testimoni — intro',    'type'=>'text', 'group'=>'Testimonials', 'default'=>'Cerita nyata dari klien lintas industri. Klik untuk melihat testimoni lengkap — video maupun tulisan.'],

  // ---- Contact (Request Proposal) ----
  'contact_eyebrow'     => ['label'=>'Kontak — eyebrow',     'type'=>'text', 'group'=>'Contact', 'default'=>"Let's Collaborate"],
  'contact_title'       => ['label'=>'Kontak — judul',       'type'=>'text', 'group'=>'Contact', 'default'=>'Request Proposal'],
  'contact_intro'       => ['label'=>'Kontak — intro',       'type'=>'text', 'group'=>'Contact', 'default'=>"Unlock your business potential — get the best IT solutions tailored just for you. Let's collaborate!"],
];
