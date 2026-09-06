<?php
// ============================================
// REKLAMEPEDIA CMS - Main Router
// ============================================

require_once __DIR__ . '/core/config/config.php';
require_once __DIR__ . '/core/database/Database.php';
require_once __DIR__ . '/core/helpers/helpers.php';

$db = Database::getInstance();

// Track visitor
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$clean_uri   = parse_url($request_uri, PHP_URL_PATH);

// SUBFOLDER SUPPORT: strip the install base path from URI before routing.
// BASE_URL was computed in config.php from SCRIPT_FILENAME relative to DOCUMENT_ROOT,
// so its path component is the actual install dir (e.g. /demo/client-a). Strip it.
$base_path = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
if ($base_path !== '' && $base_path !== '/' && str_starts_with($clean_uri, $base_path)) {
    $clean_uri = substr($clean_uri, strlen($base_path));
    if ($clean_uri === '' || $clean_uri === false) $clean_uri = '/';
}
track_visitor($clean_uri);

// ============================================
// ROUTER
// ============================================
$uri      = trim($clean_uri, '/');
$segments = $uri === '' ? [] : explode('/', $uri);

// i18n (§D): strip a leading non-default language prefix (/en/...) and set the
// active language, so the rest of the router is language-agnostic.
if (!empty($segments) && function_exists('available_langs')
    && in_array($segments[0], available_langs(), true) && $segments[0] !== default_lang()) {
    set_current_lang($segments[0]);
    array_shift($segments);
}
if (function_exists('set_current_path')) set_current_path(implode('/', $segments));

$page     = $segments[0] ?? '';
$slug     = $segments[1] ?? '';

switch ($page) {
    case '':
    case 'home':
        require_once theme_path('templates/pages/home.php');
        break;

    case 'tentang-kami':
        require_once theme_path('templates/pages/about.php');
        break;

    case 'layanan':
        if (!empty($slug)) {
            $layanan_data = $db->fetchOne('SELECT * FROM layanan WHERE slug = ? AND is_active = 1', [$slug]);
            if ($layanan_data) {
                $subs = $db->fetchAll('SELECT * FROM layanan_sub WHERE layanan_id = ? ORDER BY urutan', [$layanan_data['id']]);
                require_once theme_path('templates/pages/layanan-detail.php');
            } else {
                http_response_code(404);
                require_once theme_path('templates/pages/404.php');
            }
        } else {
            require_once theme_path('templates/pages/layanan.php');
        }
        break;

    case 'gallery':
        require_once theme_path('templates/pages/gallery.php');
        break;

    case 'blog':
        if (!empty($slug)) {
            $blog_data = $db->fetchOne('SELECT b.*, u.nama as penulis_nama FROM blog b LEFT JOIN users u ON b.user_id = u.id WHERE b.slug = ? AND b.status = "published"', [$slug]);
            if ($blog_data) {
                $db->execute('UPDATE blog SET views = views + 1 WHERE id = ?', [$blog_data['id']]);
                $blog_tags     = $db->fetchAll('SELECT bt.nama FROM blog_tags bt JOIN blog_tags_rel btr ON bt.id = btr.tag_id WHERE btr.blog_id = ?', [$blog_data['id']]);
                $blog_kategori = $db->fetchAll('SELECT bk.nama FROM blog_kategori bk JOIN blog_kategori_rel bkr ON bk.id = bkr.kategori_id WHERE bkr.blog_id = ?', [$blog_data['id']]);
                require_once theme_path('templates/pages/blog-detail.php');
            } else {
                http_response_code(404);
                require_once theme_path('templates/pages/404.php');
            }
        } else {
            require_once theme_path('templates/pages/blog.php');
        }
        break;

    case 'produk':
        if (!empty($slug)) {
            $produk = $db->fetchOne('SELECT * FROM produk WHERE slug = ? AND status = "aktif"', [$slug]);
            if ($produk) {
                require_once theme_path('templates/pages/produk-detail.php');
            } else {
                http_response_code(404);
                require_once theme_path('templates/pages/404.php');
            }
        } else {
            require_once theme_path('templates/pages/produk.php');
        }
        break;

    case 'solutions':
        require_once theme_path('templates/pages/solutions.php');
        break;

    case 'industri':
        if (!empty($slug)) {
            $industri_data = $db->fetchOne('SELECT * FROM industri WHERE slug = ? AND is_active = 1', [$slug]);
            if ($industri_data) {
                require_once theme_path('templates/pages/industri-detail.php');
            } else {
                http_response_code(404);
                require_once theme_path('templates/pages/404.php');
            }
        } else {
            require_once theme_path('templates/pages/industri.php');
        }
        break;

    case 'career':
        if (!empty($slug)) {
            $career_data = $db->fetchOne('SELECT * FROM career WHERE slug = ? AND is_active = 1', [$slug]);
            if ($career_data) {
                require_once theme_path('templates/pages/career-detail.php');
            } else {
                http_response_code(404);
                require_once theme_path('templates/pages/404.php');
            }
        } else {
            require_once theme_path('templates/pages/career.php');
        }
        break;

    case 'hubungi-kami':
        require_once theme_path('templates/pages/contact.php');
        break;

    case 'sitemap.xml':
        require_once __DIR__ . '/core/router/sitemap.php';
        break;

    case 'robots.txt':
        require_once __DIR__ . '/core/router/robots.php';
        break;

    case 'api':
        header('Content-Type: application/json');
        if ($slug === 'wa-click') {
            $contact_id = (int)($_POST['contact_id'] ?? 0);
            if ($contact_id > 0) {
                $db->execute(
                    'INSERT INTO wa_clicks (contact_id, clicks, created_at) VALUES (?, 1, CURDATE())
                     ON DUPLICATE KEY UPDATE clicks = clicks + 1',
                    [$contact_id]
                );
            }
            json_response(['status' => 'ok']);
        }
        json_response(['status' => 'not found'], 404);
        break;

    default:
        // Custom pages
        $page_data = $db->fetchOne('SELECT * FROM pages WHERE slug = ? AND status = "published" AND is_active = 1', [$page]);
        if ($page_data) {
            $page = $page_data; // Pass to template
            require_once theme_path('templates/pages/custom.php');
        } else {
            http_response_code(404);
            require_once theme_path('templates/pages/404.php');
        }
        break;
}
