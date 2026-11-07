<?php
/** Preview harness bootstrap (mock data) — renders Anima templates via `php -S` WITHOUT MySQL. */
if (!function_exists('theme_url'))  { function theme_url($p=''){ return '/themes/anima/' . ltrim($p,'/'); } }
if (!function_exists('theme_path')) { function theme_path($f=''){ return dirname(__DIR__) . '/themes/anima/' . ltrim($f,'/'); } }
if (!function_exists('asset'))      { function asset($p=''){ return '/themes/anima/assets/' . ltrim($p,'/'); } }
if (!function_exists('uploads_url')){ function uploads_url($p=''){ return '/uploads/' . ltrim($p,'/'); } }
if (!function_exists('get_content')){ function get_content($p,$k,$d=''){ return $d; } }
if (!function_exists('get_setting')){ function get_setting($k,$d=''){ $m=$GLOBALS['__mock_settings']??[]; return array_key_exists($k,$m)?$m[$k]:$d; } }
if (!function_exists('url'))        { function url($p=''){ return '/' . ltrim($p,'/'); } }

$GLOBALS['__mock_settings'] = [
  'site_title'       => 'Sapta Tunas Teknologi — Enterprise Solution Provider',
  'site_description' => 'Sapta Tunas Teknologi — established 2015. Business Technology Solutions & Services di Indonesia: IT & Cloud Infrastructure, Cybersecurity, Data & AI.',
  // Contact (real Sapta values from the Figma design)
  'site_address'           => 'Komplek Perkantoran Agung Sedayu Blok H No.28-30, Jl. Arteri Mangga Dua Raya, Jakarta Pusat, DKI Jakarta, Indonesia 10730',
  'site_phone'             => '+62 21-5028 1717',
  'site_phone_prosupport'  => '021-2410 1568',
  'wa_number'              => '6282110001087',
  'wa_display'             => '+62 821-1000-1087',
  'site_email'             => 'marketing@saptatunas.com',
  'site_email_prosupport'  => 'prosupport@saptatunas.com',
  'site_maps_embed'        => '',
  'linkedin_url'           => '#',
  'instagram_url'          => '#',
  'facebook_url'           => '#',
  'youtube_url'            => '#',
];
