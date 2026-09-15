<?php
// robots.txt generator
header('Content-Type: text/plain');
header('Cache-Control: public, max-age=86400');

$custom = get_setting('robots_custom', '');

if (!empty(trim($custom))) {
    echo $custom;
} else {
    echo "User-agent: *\n";
    echo "Allow: /\n\n";
    echo "Disallow: /admin/\n";
    echo "Disallow: /core/\n\n";
    echo "Sitemap: " . url('/sitemap.xml') . "\n";
}
