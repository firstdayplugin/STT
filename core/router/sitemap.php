<?php
// Sitemap Generator
header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=3600');

$db   = Database::getInstance();
$base = rtrim(BASE_URL, '/');
$today = date('Y-m-d');

$urls = [
    ['loc' => $base . '/',              'priority' => '1.0', 'changefreq' => 'weekly'],
    ['loc' => $base . '/tentang-kami',  'priority' => '0.8', 'changefreq' => 'monthly'],
    ['loc' => $base . '/layanan',       'priority' => '0.9', 'changefreq' => 'weekly'],
    ['loc' => $base . '/gallery',       'priority' => '0.7', 'changefreq' => 'weekly'],
    ['loc' => $base . '/blog',          'priority' => '0.8', 'changefreq' => 'daily'],
    ['loc' => $base . '/produk',        'priority' => '0.8', 'changefreq' => 'weekly'],
    ['loc' => $base . '/hubungi-kami',  'priority' => '0.7', 'changefreq' => 'monthly'],
];

// Layanan
foreach ($db->fetchAll("SELECT slug, updated_at FROM layanan WHERE is_active = 1") as $l) {
    $urls[] = ['loc' => $base.'/layanan/'.$l['slug'], 'priority' => '0.8', 'lastmod' => date('Y-m-d', strtotime($l['updated_at'] ?: 'now')), 'changefreq' => 'monthly'];
}

// Blog
foreach ($db->fetchAll("SELECT slug, updated_at FROM blog WHERE status = 'published' ORDER BY updated_at DESC LIMIT 200") as $b) {
    $urls[] = ['loc' => $base.'/blog/'.$b['slug'], 'priority' => '0.6', 'lastmod' => date('Y-m-d', strtotime($b['updated_at'] ?: 'now')), 'changefreq' => 'monthly'];
}

// Produk
foreach ($db->fetchAll("SELECT slug, updated_at FROM produk WHERE status = 'aktif' ORDER BY updated_at DESC LIMIT 200") as $p) {
    $urls[] = ['loc' => $base.'/produk/'.$p['slug'], 'priority' => '0.6', 'lastmod' => date('Y-m-d', strtotime($p['updated_at'] ?: 'now')), 'changefreq' => 'monthly'];
}

// Custom pages
foreach ($db->fetchAll("SELECT slug, updated_at FROM pages WHERE status = 'published' AND is_active = 1") as $pg) {
    $urls[] = ['loc' => $base.'/'.$pg['slug'], 'priority' => '0.5', 'lastmod' => date('Y-m-d', strtotime($pg['updated_at'] ?: 'now')), 'changefreq' => 'monthly'];
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $url): ?>
  <url>
    <loc><?= htmlspecialchars($url['loc']) ?></loc>
    <lastmod><?= $url['lastmod'] ?? $today ?></lastmod>
    <changefreq><?= $url['changefreq'] ?></changefreq>
    <priority><?= $url['priority'] ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
