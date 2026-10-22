<?php
// ============================================
// REKLAMEPEDIA CMS - Core Configuration
// ============================================

define('CMS_VERSION', '1.0.0');
define('CMS_NAME', 'Reklamepedia CMS');

// ============================================
// DATABASE CONFIG - Edit sesuai hosting Anda
// ============================================
// ⚠️ GANTI sesuai kredensial hosting Anda!
// Di cPanel biasanya format: namauser_namadatabase
// Values may be overridden by environment variables (CMS_DB_*) — useful for local dev,
// staging, and keeping secrets out of source. When the env var is unset, the hosting
// default below is used, so existing deployments are unaffected.
define('DB_HOST', getenv('CMS_DB_HOST') !== false ? getenv('CMS_DB_HOST') : 'localhost');
define('DB_NAME', getenv('CMS_DB_NAME') !== false ? getenv('CMS_DB_NAME') : 'u780175149_reklamenesia');   // Ganti dengan nama database Anda
define('DB_USER', getenv('CMS_DB_USER') !== false ? getenv('CMS_DB_USER') : 'u780175149_reklamenesia');   // Ganti dengan username database Anda
define('DB_PASS', getenv('CMS_DB_PASS') !== false ? getenv('CMS_DB_PASS') : 'pLBs059>y9!');                    // Isi password database Anda
define('DB_CHARSET', 'utf8mb4');

// ============================================
// PATH CONFIG
// ============================================
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
define('CORE_PATH', BASE_PATH . '/core');
define('THEMES_PATH', BASE_PATH . '/themes');
define('PLUGINS_PATH', BASE_PATH . '/plugins');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('ADMIN_PATH', BASE_PATH . '/admin');

// ============================================
// BASE URL - auto detect for ANY install location:
//   • domain.com                       (root)
//   • sub.domain.com                   (subdomain)
//   • domain.com/cms                   (single subfolder)
//   • domain.com/demo/client-a         (multi-level subfolder)
//   • sub.domain.com/compro/kawa       (subdomain + multi-folder)
// Strategy: determine install root from SCRIPT_FILENAME relative to DOCUMENT_ROOT.
// This works regardless of URL rewriting because SCRIPT_FILENAME points to the actual file executed.
// ============================================
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
if (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') $protocol = 'https'; // behind reverse proxy
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Allow manual override via env or define before this file (highest priority)
if (!defined('BASE_URL')) {
    $manual = getenv('CMS_BASE_URL');
    if ($manual) {
        define('BASE_URL', rtrim($manual, '/'));
    } else {
        // Compute install dir from SCRIPT_FILENAME (true path on disk) vs DOCUMENT_ROOT
        $script_file = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
        $doc_root    = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/'));
        $install_dir = '';
        if ($script_file && $doc_root && str_starts_with($script_file, $doc_root)) {
            // script_file: /home/u/public_html/demo/client-a/admin/index.php
            // doc_root:    /home/u/public_html
            // relative:    /demo/client-a/admin/index.php
            $rel = substr($script_file, strlen($doc_root));
            $rel_dir = rtrim(str_replace('\\', '/', dirname($rel)), '/');
            // Strip trailing /admin or /admin/foo and trailing install.php dir
            $rel_dir = preg_replace('#/admin(/.*)?$#', '', $rel_dir);
            $rel_dir = preg_replace('#/install\.php.*$#', '', $rel_dir);
            $install_dir = $rel_dir;
        } else {
            // Fallback: derive from SCRIPT_NAME (handles symlinks where filename != docroot path)
            $script_name = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
            $sn = rtrim(str_replace('\\', '/', dirname($script_name)), '/');
            $sn = preg_replace('#/admin(/.*)?$#', '', $sn);
            $sn = preg_replace('#/install\.php.*$#', '', $sn);
            $install_dir = $sn;
        }
        $base_url = rtrim($protocol . '://' . $host . $install_dir, '/');
        define('BASE_URL', $base_url);
    }
}
define('UPLOADS_URL', BASE_URL . '/uploads');
define('THEMES_URL',  BASE_URL . '/themes');
define('ADMIN_URL',   BASE_URL . '/admin');
define('SITE_URL',    BASE_URL); // alias for compat

// ============================================
// SECURITY CONFIG
// ============================================
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_NAME', 'reklamepedia_cms');
define('SESSION_LIFETIME', 7200); // 2 jam
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 menit

// ============================================
// UPLOAD CONFIG
// ============================================
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);

// ============================================
// SEO CONFIG
// ============================================
define('DEFAULT_META_TITLE', 'Reklamepedia - Solusi Reklame Profesional');
define('DEFAULT_META_DESC', 'Reklamepedia menyediakan solusi reklame profesional.');
define('SITEMAP_URL', BASE_URL . '/sitemap.xml');

// ============================================
// TIMEZONE
// ============================================
date_default_timezone_set('Asia/Jakarta');

// ============================================
// ERROR HANDLING (production: false, dev: true)
// ============================================
define('DEBUG_MODE', false);
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// ============================================
// SESSION START
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'secure' => ($protocol === 'https'),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// ============================================
// GLOBAL ERROR HANDLER (prevent blank pages)
// ============================================
set_exception_handler(function ($e) {
    $log_file = BASE_PATH . '/error.log';
    $msg = '[' . date('Y-m-d H:i:s') . '] ' . get_class($e) . ': ' . $e->getMessage()
         . ' in ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
    @file_put_contents($log_file, $msg, FILE_APPEND);
    
    // If output already started or headers sent, can't show full error page
    if (headers_sent()) {
        echo '<div style="padding:20px;margin:20px;background:#fee;color:#900;border-radius:8px;font-family:system-ui">';
        echo '<strong>⚠️ Error:</strong> ' . htmlspecialchars($e->getMessage());
        echo '</div>';
        return;
    }
    
    // Show friendly error page instead of blank
    http_response_code(500);
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        echo '<pre style="padding:20px;background:#fee;color:#900;font-family:monospace">';
        echo htmlspecialchars($msg . PHP_EOL . $e->getTraceAsString());
        echo '</pre>';
    } else {
        echo '<div style="max-width:600px;margin:80px auto;padding:32px;font-family:system-ui;text-align:center">';
        echo '<h1 style="font-size:48px;margin:0 0 16px">⚠️</h1>';
        echo '<h2 style="color:#1a1a1a;margin:0 0 12px">Terjadi Kesalahan</h2>';
        echo '<p style="color:#666;margin:0 0 24px">Sistem sedang mengalami gangguan. Silakan refresh atau kembali ke beranda.</p>';
        echo '<a href="javascript:history.back()" style="display:inline-block;padding:10px 22px;background:#2563EB;color:white;text-decoration:none;border-radius:8px;margin-right:8px">← Kembali</a>';
        echo '<a href="/" style="display:inline-block;padding:10px 22px;background:#f3f4f6;color:#1a1a1a;text-decoration:none;border-radius:8px">Beranda</a>';
        if (function_exists('is_admin') || strpos($_SERVER['REQUEST_URI'] ?? '', '/admin') !== false) {
            echo '<details style="margin-top:32px;text-align:left;padding:16px;background:#f9fafb;border-radius:8px;font-family:monospace;font-size:12px;color:#666"><summary style="cursor:pointer">Detail (admin only)</summary>';
            echo '<pre>' . htmlspecialchars($msg) . '</pre></details>';
        }
        echo '</div>';
    }
    exit;
});

// (set_error_handler removed - was converting warnings to fatal errors)

