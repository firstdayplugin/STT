<?php
/** Preview router: `php -S 127.0.0.1:PORT -t <repo-root> preview/router.php`. Mirrors the CMS routing. */
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$root = $_SERVER['DOCUMENT_ROOT'] ?: dirname(__DIR__);
$full = $root . $uri;
if ($uri !== '/' && $uri !== '' && file_exists($full) && !is_dir($full)) { return false; }

require __DIR__ . '/bootstrap.php';

$seg  = trim($uri, '/');
$map  = [
  ''             => 'home',
  'home'         => 'home',
  'hubungi-kami' => 'contact',
  'contact'      => 'contact',
  'tentang-kami' => 'about',
  'about'        => 'about',
];
$page = $map[$seg] ?? null;
if ($page === null) { http_response_code(404); $page = 'home'; }

$tpl = theme_path('templates/pages/' . $page . '.php');
if (!empty($_GET['reveal'])) { $GLOBALS['__preview_reveal'] = true; }
require $tpl;
