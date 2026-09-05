<?php
/** Preview router for `php -S 127.0.0.1:PORT -t <repo-root> preview/router.php`. */
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$root = $_SERVER['DOCUMENT_ROOT'] ?: (__DIR__ . '/..');
$full = $root . $uri;
if ($uri !== '/' && $uri !== '' && file_exists($full) && !is_dir($full)) { return false; }

require __DIR__ . '/bootstrap.php';
$T = $root . '/themes/anima/templates';
require $T . '/layouts/header.php';
if (!empty($_GET['reveal'])) { echo '<link rel="stylesheet" href="/preview/preview.css">'; }
require $T . '/pages/home.php';
require $T . '/layouts/footer.php';
