<?php
/**
 * Anima — Industries landing content registry (editable copy + Coming Soon banner).
 * The industry cards (icon, name, order) are managed under Admin → Industries (Orbit).
 * The per-industry detail content (heading, text, features) is managed there too.
 */
return [
  'title'        => ['label'=>'Judul halaman',        'type'=>'text', 'group'=>'Header','default'=>'Our Industries'],
  'lead'         => ['label'=>'Paragraf intro',       'type'=>'html', 'group'=>'Header','default'=>'We understand that every industry has its own unique challenges and needs.'],
  'banner_img'   => ['label'=>'Banner Coming Soon (gambar)','type'=>'image','group'=>'Coming Soon','default'=>'solutions/coming-soon-banner.png'],
  'banner_url'   => ['label'=>'Banner — link tujuan', 'type'=>'text', 'group'=>'Coming Soon','default'=>'#'],
  'detail_intro' => ['label'=>'Intro halaman detail (di bawah judul industri)','type'=>'html','group'=>'Halaman Detail','default'=>'We understand that every industry has its own unique challenges and needs.'],
];
