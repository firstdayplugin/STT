<?php
require_once dirname(__DIR__) . '/core/config/config.php';
require_once dirname(__DIR__) . '/core/database/Database.php';
require_once dirname(__DIR__) . '/core/helpers/helpers.php';

// Auth check
if (!isset($_SESSION['admin_user'])) {
    $current = urlencode($_SERVER['REQUEST_URI']);
    redirect(admin_url('login.php') . '?redirect=' . $current);
}

$user = $_SESSION['admin_user'];
$db   = Database::getInstance();

// ============================================
// AJAX ENDPOINTS (return JSON, no layout)
// ============================================
if (($_GET['ajax'] ?? '') !== '') {
    header('Content-Type: application/json');
    $ajax = $_GET['ajax'];
    
    try {
        // TinyMCE WYSIWYG image upload endpoint. TinyMCE POSTs the file as 'file'.
        // Returns JSON {location: "/path/to/image.jpg"} as required by TinyMCE.
        // ============================================
        // Menu save_structure (must run before layout to return clean JSON)
        // Triggered by ?ajax=save_menu_structure
        // ============================================
        if ($ajax === 'save_menu_structure' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                echo json_encode(['ok'=>false,'error'=>'Token tidak valid. Refresh halaman dan coba lagi.']);
                exit;
            }
            try {
                $struct_json = $_POST['structure'] ?? '[]';
                $struct = json_decode($struct_json, true);
                if (!is_array($struct)) throw new Exception('Format struktur tidak valid');
                
                $existing = $db->fetchAll("SELECT id FROM menus");
                $existing_ids = array_map(fn($r)=>(int)$r['id'], $existing);
                $kept_ids = [];
                
                $save_recursive = function($nodes, $parent_id = null) use (&$save_recursive, $db, &$kept_ids) {
                    foreach ($nodes as $i => $node) {
                        $data = [
                            'label'     => trim($node['label'] ?? '') ?: 'Untitled',
                            'url'       => trim($node['url'] ?? '#'),
                            'target'    => in_array($node['target'] ?? '_self', ['_self','_blank']) ? $node['target'] : '_self',
                            'parent_id' => $parent_id,
                            'urutan'    => $i,
                            'css_class' => trim($node['css_class'] ?? '') ?: null,
                            'icon'      => trim($node['icon'] ?? '') ?: null,
                            'is_active' => 1,
                        ];
                        $id = (int)($node['id'] ?? 0);
                        if ($id > 0) {
                            $set = implode(',', array_map(fn($k)=>"$k=?", array_keys($data)));
                            $db->execute("UPDATE menus SET $set WHERE id=?", [...array_values($data), $id]);
                            $kept_ids[] = $id;
                        } else {
                            $db->insert('menus', $data);
                            $kept_ids[] = $db->lastInsertId();
                        }
                        if (!empty($node['children']) && is_array($node['children'])) {
                            $save_recursive($node['children'], end($kept_ids));
                        }
                    }
                };
                $save_recursive($struct);
                
                $to_delete = array_diff($existing_ids, $kept_ids);
                foreach ($to_delete as $del_id) {
                    $db->execute("DELETE FROM menus WHERE id = ?", [$del_id]);
                }
                
                log_activity('update', 'Update struktur menu', $user['id']);
                echo json_encode(['ok'=>true, 'kept'=>count($kept_ids), 'deleted'=>count($to_delete)]);
                exit;
            } catch (Throwable $e) {
                echo json_encode(['ok'=>false, 'error'=>$e->getMessage()]);
                exit;
            }
        }
        
        if ($ajax === 'upload_editor_image' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF: TinyMCE's custom upload handler (see layout.php) sends the session token.
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                http_response_code(403);
                echo json_encode(['error' => ['message' => 'Token keamanan tidak valid. Refresh halaman.']]);
                exit;
            }
            if (!isset($_FILES['file'])) {
                http_response_code(400);
                echo json_encode(['error' => ['message' => 'No file uploaded']]);
                exit;
            }
            $saved = upload_image($_FILES['file'], 'editor');
            if (!$saved) {
                http_response_code(400);
                echo json_encode(['error' => ['message' => 'Upload failed (size/type/permission)']]);
                exit;
            }
            // TinyMCE expects {location: "url"}
            echo json_encode(['location' => uploads_url($saved)]);
            exit;
        }
        
        // Create product category (WooCommerce-style inline) - auto-adapts if parent_id column missing
        if ($ajax === 'create_kategori' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                echo json_encode(['ok' => false, 'error' => 'Token tidak valid']);
                exit;
            }
            $nama = trim($_POST['nama'] ?? '');
            $parent_id = (int)($_POST['parent_id'] ?? 0) ?: null;
            if ($nama === '') {
                echo json_encode(['ok' => false, 'error' => 'Nama kategori wajib diisi']);
                exit;
            }
            $kat_slug = slug($nama);
            $base = $kat_slug; $n = 1;
            while ($db->fetchOne("SELECT id FROM produk_kategori WHERE slug = ?", [$kat_slug])) {
                $kat_slug = $base . '-' . (++$n);
            }
            // Auto-detect if parent_id column exists (works with/without migration)
            $has_parent = in_array('parent_id', $db->getColumns('produk_kategori'));
            if ($has_parent) {
                $db->execute("INSERT INTO produk_kategori (nama, slug, parent_id) VALUES (?,?,?)", [$nama, $kat_slug, $parent_id]);
            } else {
                $db->execute("INSERT INTO produk_kategori (nama, slug) VALUES (?,?)", [$nama, $kat_slug]);
                $parent_id = null;
            }
            $new_id = $db->lastInsertId();
            log_activity('create', 'Kategori produk: ' . $nama);
            echo json_encode([
                'ok' => true,
                'kategori' => ['id' => $new_id, 'nama' => $nama, 'slug' => $kat_slug, 'parent_id' => $parent_id]
            ]);
            exit;
        }
        
        // List categories
        if ($ajax === 'list_kategori') {
            $has_parent = in_array('parent_id', $db->getColumns('produk_kategori'));
            $order = $has_parent ? "ORDER BY COALESCE(parent_id,id), parent_id IS NOT NULL, nama" : "ORDER BY nama";
            $cats = $db->fetchAll("SELECT * FROM produk_kategori $order");
            echo json_encode(['ok' => true, 'kategori' => $cats]);
            exit;
        }
        
        // Delete category
        if ($ajax === 'delete_kategori' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                echo json_encode(['ok' => false, 'error' => 'Token tidak valid']); exit;
            }
            $kid = (int)($_POST['id'] ?? 0);
            $has_parent = in_array('parent_id', $db->getColumns('produk_kategori'));
            if ($has_parent) {
                $db->execute("UPDATE produk_kategori SET parent_id = NULL WHERE parent_id = ?", [$kid]);
            }
            $db->execute("DELETE FROM produk_kategori_rel WHERE kategori_id = ?", [$kid]);
            $db->execute("DELETE FROM produk_kategori WHERE id = ?", [$kid]);
            echo json_encode(['ok' => true]);
            exit;
        }
        
        // Delete product gallery image
        if ($ajax === 'delete_gallery' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                echo json_encode(['ok' => false, 'error' => 'Token tidak valid']); exit;
            }
            $gallery_id = (int)($_POST['gallery_id'] ?? 0);
            $db->execute("DELETE FROM produk_gallery WHERE id = ?", [$gallery_id]);
            echo json_encode(['ok' => true]);
            exit;
        }
        
        // Blog category create (AJAX)
        if ($ajax === 'create_blog_kategori' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                echo json_encode(['ok' => false, 'error' => 'Token tidak valid']); exit;
            }
            $nama = trim($_POST['nama'] ?? '');
            if ($nama === '') { echo json_encode(['ok'=>false,'error'=>'Nama wajib diisi']); exit; }
            $kat_slug = slug($nama);
            $base = $kat_slug; $n = 1;
            while ($db->fetchOne("SELECT id FROM blog_kategori WHERE slug = ?", [$kat_slug])) {
                $kat_slug = $base . '-' . (++$n);
            }
            $parent_id = (int)($_POST['parent_id'] ?? 0) ?: null;
            $b_cols = $db->getColumns('blog_kategori');
            $b_has_parent = in_array('parent_id', $b_cols);
            if ($b_has_parent) {
                $db->execute("INSERT INTO blog_kategori (nama, slug, parent_id) VALUES (?,?,?)", [$nama, $kat_slug, $parent_id]);
            } else {
                $db->execute("INSERT INTO blog_kategori (nama, slug) VALUES (?,?)", [$nama, $kat_slug]);
            }
            $new_id = $db->lastInsertId();
            log_activity('create', 'Kategori artikel: ' . $nama);
            echo json_encode(['ok'=>true,'kategori'=>['id'=>$new_id,'nama'=>$nama,'slug'=>$kat_slug,'parent_id'=>$parent_id]]);
            exit;
        }
        
        // Blog category delete (AJAX)
        if ($ajax === 'delete_blog_kategori' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                echo json_encode(['ok' => false, 'error' => 'Token tidak valid']); exit;
            }
            $kid = (int)($_POST['id'] ?? 0);
            $db->execute("DELETE FROM blog_kategori_rel WHERE kategori_id = ?", [$kid]);
            $db->execute("DELETE FROM blog_kategori WHERE id = ?", [$kid]);
            echo json_encode(['ok'=>true]);
            exit;
        }
        
        echo json_encode(['ok' => false, 'error' => 'Unknown action']);
    } catch (Throwable $e) {
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Parse route
$request = $_GET['page'] ?? 'dashboard';
$action  = $_GET['action'] ?? 'index';
$id      = (int)($_GET['id'] ?? 0);

// Role-based access
$role_access = [
    'superadmin'    => ['*'],
    'admin'         => ['dashboard','blog','produk','layanan','gallery','pages','menu','pengaturan','seo','ads','plugin','users','content','testimonial','faq','klien-logo','wizard','template','flex-blocks','grid-icon','solusi','industri','solusi-pilar','career','career-lamaran'],
    'penulis'       => ['dashboard','blog'],
    'admin_produk'  => ['dashboard','produk','gallery'],
    'tim_ads'       => ['dashboard','ads','seo'],
];

function can_access($role, $page) {
    global $role_access;
    $allowed = $role_access[$role] ?? [];
    return in_array('*', $allowed) || in_array($page, $allowed);
}

if (!can_access($user['role'], $request)) {
    $request = 'dashboard';
}

// Logout
if ($request === 'logout') {
    log_activity('logout', 'User logout: ' . $user['username'], $user['id']);
    session_destroy();
    redirect(admin_url('login.php'));
}

// Valid pages
$valid_pages = ['dashboard','blog','produk','layanan','gallery','pages','menu','pengaturan','plugin','ads','seo','users','wizard','content','testimonial','faq','klien-logo','template','flex-blocks','grid-icon','blog-kategori','produk-kategori','solusi','industri','solusi-pilar','career','career-lamaran'];
if (!in_array($request, $valid_pages)) {
    $request = 'dashboard';
}

// Resolve view file
$view_dir = __DIR__ . '/views/' . $request;
$view_file = $view_dir . '/index.php';
if (!file_exists($view_file)) {
    // fallback to dashboard
    $view_file = __DIR__ . '/views/dashboard/index.php';
    $request = 'dashboard';
}

// Set page metadata
$page_titles = [
    'dashboard'    => 'Dashboard',
    'content'      => 'Konten Halaman',
    'blog'         => 'Blog / Artikel',
    'produk'       => 'Produk',
    'layanan'      => 'Layanan',
    'gallery'      => 'Galeri',
    'testimonial'  => 'Testimoni',
    'solusi'       => 'Solutions (Cube)',
    'solusi-pilar' => 'Pilar Solusi',
    'industri'     => 'Industries (Orbit)',
    'career'       => 'Career / Lowongan',
    'career-lamaran' => 'Lamaran Masuk',
    'faq'          => 'FAQ',
    'klien-logo'   => 'Logo Klien',
    'pages'        => 'Halaman Custom',
    'menu'         => 'Menu Navigasi',
    'seo'          => 'SEO per Halaman',
    'ads'          => 'Iklan & Pixel',
    'pengaturan'   => 'Pengaturan Website',
    'plugin'       => 'Plugin',
    'users'        => 'Pengguna',
    'wizard'       => 'Setup Wizard',
    'template'     => 'Template Manager',
    'flex-blocks'  => 'Flexible Content Block',
    'grid-icon'    => 'Grid Icon Box',
    'blog-kategori'=> 'Kategori Blog',
    'produk-kategori'=> 'Kategori Produk',
];
$page_title = $page_titles[$request] ?? ucfirst($request);

// Include layout
require __DIR__ . '/views/layout.php';
