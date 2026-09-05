<?php
/** Local dev front controller: serve the REAL CMS (index.php) via `php -S`, static files pass through. */
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$root = $_SERVER['DOCUMENT_ROOT'];
$full = $root . $uri;
if ($uri !== '/' && $uri !== '' && file_exists($full) && !is_dir($full)) { return false; }
require $root . '/index.php';
