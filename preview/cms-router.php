<?php
/** Local dev front controller: serve the REAL CMS via `php -S`. Static files pass through;
 *  /admin/* is handled by admin/index.php, everything else by the root index.php. */
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$root = $_SERVER['DOCUMENT_ROOT'];
$full = $root . $uri;
if ($uri !== '/' && $uri !== '' && is_file($full)) { return false; }           // real static file
if (preg_match('#^/admin(/|$)#', $uri)) { require $root . '/admin/index.php'; return; }
require $root . '/index.php';
