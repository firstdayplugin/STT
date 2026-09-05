<?php
/**
 * Preview harness bootstrap (mock data) — lets Anima templates render via `php -S` WITHOUT MySQL.
 * Defines the CMS helper functions the theme calls, with sample/default values.
 * This is DEV tooling only; production uses the real core/helpers.php.
 */
if (!function_exists('theme_url'))  { function theme_url($p=''){ return '/themes/anima/' . ltrim($p,'/'); } }
if (!function_exists('asset'))      { function asset($p=''){ return '/themes/anima/assets/' . ltrim($p,'/'); } }
if (!function_exists('uploads_url')){ function uploads_url($p=''){ return '/uploads/' . ltrim($p,'/'); } }
if (!function_exists('get_setting')){ function get_setting($k,$d=''){ $m=$GLOBALS['__mock_settings']??[]; return $m[$k]??$d; } }
if (!function_exists('get_content')){ function get_content($p,$b,$d=''){ return $d; } }
if (!function_exists('url'))        { function url($p=''){ return '/' . ltrim($p,'/'); } }
if (!function_exists('e'))          { function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); } }

$GLOBALS['__mock_settings'] = [
  'site_title'       => 'Sapta Tunas Teknologi — Enterprise Solution Provider',
  'site_description' => 'Sapta Tunas Teknologi — established 2015. Business Technology Solutions & Services di Indonesia: IT & Cloud Infrastructure, Cybersecurity, Data & AI.',
];
