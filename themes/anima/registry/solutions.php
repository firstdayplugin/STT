<?php
/**
 * Anima — Solutions landing content registry (editable copy + Coming Soon banner).
 * The solution blocks themselves (illustration, partner logos, text) are managed under
 * Admin → "Solutions (Page)" (solutions_section table). This registry covers the page
 * header, the Coming Soon banner, and the shared UI labels.
 */
return [
  'hero_img'       => ['label'=>'Foto Hero (background)','type'=>'image','group'=>'Header','default'=>'design3/towers-up.jpg'],
  'hero_eyebrow'   => ['label'=>'Hero — eyebrow (label kecil)','type'=>'text','group'=>'Header','default'=>'Enterprise Solution Provider'],
  'title'          => ['label'=>'Judul halaman',       'type'=>'text', 'group'=>'Header','default'=>'Our Solutions'],
  'lead'           => ['label'=>'Paragraf intro',      'type'=>'html', 'group'=>'Header','default'=>'We understand that every industry has its own unique challenges and needs. That’s why STT provides solutions that are not only diverse but also tailored to client needs.'],
  'banner_img'     => ['label'=>'Banner Coming Soon (gambar)', 'type'=>'image','group'=>'Coming Soon','default'=>'solutions/banner-promo.jpg'],
  'banner_url'     => ['label'=>'Banner — link tujuan','type'=>'text', 'group'=>'Coming Soon','default'=>'#'],
  'label_solution' => ['label'=>'Label "Solution:"',   'type'=>'text', 'group'=>'Label','default'=>'Solution:'],
  'label_partner'  => ['label'=>'Label "Partner:"',    'type'=>'text', 'group'=>'Label','default'=>'Partner:'],
  'card_cta'       => ['label'=>'Teks tombol kartu',   'type'=>'text', 'group'=>'Label','default'=>'See More'],
  'deck_eyebrow'   => ['label'=>'Deck — eyebrow',      'type'=>'text', 'group'=>'Solution Deck','default'=>'What we deliver'],
  'modal_note'     => ['label'=>'Popup — catatan kaki','type'=>'text', 'group'=>'Solution Deck','default'=>"Let's design the right solution for your business."],
  'cta_eyebrow'    => ['label'=>'CTA — eyebrow',       'type'=>'text', 'group'=>'CTA (pre-footer)','default'=>'Start your transformation'],
  'cta_title'      => ['label'=>'CTA — judul (pakai [..] utk aksen)','type'=>'text','group'=>'CTA (pre-footer)','default'=>'Ready to modernize your [enterprise?]'],
  'cta_text'       => ['label'=>'CTA — paragraf',      'type'=>'text', 'group'=>'CTA (pre-footer)','default'=>"Let's design the right foundation, security, data, and AI strategy for your business — backed by a partner trusted by global technology leaders across the region."],
  'cta_btn1_label' => ['label'=>'CTA — tombol 1 teks', 'type'=>'text', 'group'=>'CTA (pre-footer)','default'=>'Contact Us'],
  'cta_btn1_url'   => ['label'=>'CTA — tombol 1 link', 'type'=>'text', 'group'=>'CTA (pre-footer)','default'=>'contact-us'],
  'cta_btn2_label' => ['label'=>'CTA — tombol 2 teks (opsional)','type'=>'text','group'=>'CTA (pre-footer)','default'=>'Explore Solutions'],
  'cta_btn2_url'   => ['label'=>'CTA — tombol 2 link', 'type'=>'text', 'group'=>'CTA (pre-footer)','default'=>'#sdWrap'],
];
