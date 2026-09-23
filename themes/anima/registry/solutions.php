<?php
/**
 * Anima — Solutions landing content registry (editable copy + Coming Soon banner).
 * The solution blocks themselves (illustration, partner logos, text) are managed under
 * Admin → "Solutions (Page)" (solutions_section table). This registry covers the page
 * header, the Coming Soon banner, and the shared UI labels.
 */
return [
  'title'          => ['label'=>'Judul halaman',       'type'=>'text', 'group'=>'Header','default'=>'Our Solutions'],
  'lead'           => ['label'=>'Paragraf intro',      'type'=>'html', 'group'=>'Header','default'=>'We understand that every industry has its own unique challenges and needs. That’s why STT provides solutions that are not only diverse but also tailored to client needs.'],
  'banner_img'     => ['label'=>'Banner Coming Soon (gambar)', 'type'=>'image','group'=>'Coming Soon','default'=>'solutions/coming-soon-banner.png'],
  'banner_url'     => ['label'=>'Banner — link tujuan','type'=>'text', 'group'=>'Coming Soon','default'=>'#'],
  'label_solution' => ['label'=>'Label "Solution:"',   'type'=>'text', 'group'=>'Label','default'=>'Solution:'],
  'label_partner'  => ['label'=>'Label "Partner:"',    'type'=>'text', 'group'=>'Label','default'=>'Partner:'],
  'card_cta'       => ['label'=>'Teks tombol kartu',   'type'=>'text', 'group'=>'Label','default'=>'See More'],
];
