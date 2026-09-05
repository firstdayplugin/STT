<?php
// ============================================
// mbstring polyfill (in case extension missing)
// ============================================
if (!function_exists('mb_substr')) {
    function mb_substr($str, $start, $length = null, $encoding = null) {
        return $length === null ? substr($str, $start) : substr($str, $start, $length);
    }
}
if (!function_exists('mb_strlen')) {
    function mb_strlen($str, $encoding = null) { return strlen($str); }
}
if (!function_exists('mb_strtoupper')) {
    function mb_strtoupper($str, $encoding = null) { return strtoupper($str); }
}
if (!function_exists('mb_strtolower')) {
    function mb_strtolower($str, $encoding = null) { return strtolower($str); }
}

// ============================================
// REKLAMEPEDIA CMS - Helper Functions
// ============================================

// Lucide inline-SVG icon library — project rule: NO EMOJI, real icons only.
require_once __DIR__ . '/icons.php';

// ============================================
// STRING HELPERS
// ============================================

function slug(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function make_slug(string $text): string { return slug($text); }

function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}


/**
 * Sanitize HTML content from WYSIWYG editors.
 * Allows safe formatting tags but strips dangerous ones (script, iframe except whitelisted, on* attrs).
 * Less strict than htmlspecialchars (allows formatting); more strict than raw output (strips XSS).
 */
function safe_html(?string $html): string {
    if ($html === null || $html === '') return '';
    
    // Strip <script>, <style>, <iframe> (unless YouTube/Vimeo), <object>, <embed>, <form>
    $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html);
    $html = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $html);
    // Strip event handlers like onclick, onerror, onload, etc.
    $html = preg_replace('/\s+on[a-z]+\s*=\s*"[^"]*"/i', '', $html);
    $html = preg_replace("/\s+on[a-z]+\s*=\s*'[^']*'/i", '', $html);
    // Strip javascript: URIs
    $html = preg_replace('/javascript\s*:/i', '', $html);
    // Allow iframes ONLY from trusted video providers
    $html = preg_replace_callback('#<iframe\b([^>]*)>(.*?)</iframe>#is', function($m) {
        $attrs = $m[1];
        if (preg_match('#src\s*=\s*["\']https?://(www\.)?(youtube\.com|youtube-nocookie\.com|player\.vimeo\.com|youtu\.be)/[^"\']*#i', $attrs)) {
            return $m[0]; // keep as-is
        }
        return ''; // strip foreign iframes
    }, $html);
    return $html;
}


function sanitize_html(string $input): string {
    // Allow basic HTML tags (untuk konten editor)
    $allowed = '<p><br><strong><em><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><a><img><table><thead><tbody><tr><td><th><span><div>';
    return strip_tags($input, $allowed);
}

function excerpt(string $text, int $length = 150): string {
    $text = strip_tags($text);
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, strrpos(substr($text, 0, $length), ' ')) . '...';
}

function format_angka(int|float $number): string {
    if ($number >= 1000) {
        return number_format($number / 1000, 0) . 'k';
    }
    return (string) $number;
}

function format_rupiah(int|float $harga): string {
    return 'Rp ' . number_format($harga, 0, ',', '.');
}

function parse_rupiah($input): int {
    // Accepts any of: 750000, "750000", "750.000", "750,000", "Rp 750.000", "750000.00", "750000,00"
    // Returns clean integer rupiah. Always idempotent - never multiplies repeated input.
    if ($input === null || $input === '') return 0;
    $s = (string) $input;
    // Strip everything except digits and any single decimal mark we can detect
    // Approach: keep only digits. If last "separator" in the string is followed by exactly 1-2 trailing chars,
    // treat that as a decimal portion and drop it (Indonesian Rupiah is integer).
    // Examples:
    //   "750000" -> 750000
    //   "750.000" -> 750000  (dot = thousand separator IDN)
    //   "750.000,00" -> 750000  (drop ",00")
    //   "750,000.00" -> 750000  (drop ".00")
    //   "Rp 1.500.000" -> 1500000
    //   "75000000,00" -> 75000000
    //   "1500000" -> 1500000
    
    // Remove currency symbol and spaces
    $s = preg_replace('/[^\d.,\-]/', '', $s);
    
    // If the string contains BOTH dot and comma, the LAST one is the decimal mark
    // If only one occurs, decide by position: if it has exactly 2 digits after it, treat as decimal
    $hasDot = strpos($s, '.') !== false;
    $hasComma = strpos($s, ',') !== false;
    
    if ($hasDot && $hasComma) {
        // Last symbol = decimal mark; drop decimal portion entirely (we want integer)
        $lastDot = strrpos($s, '.');
        $lastComma = strrpos($s, ',');
        $decimalPos = max($lastDot, $lastComma);
        $s = substr($s, 0, $decimalPos);
        $s = str_replace(['.', ','], '', $s);
    } elseif ($hasDot || $hasComma) {
        // Single separator type. If exactly 1-2 chars after the LAST separator, it might be decimal.
        $sep = $hasDot ? '.' : ',';
        $lastPos = strrpos($s, $sep);
        $tail = substr($s, $lastPos + 1);
        // Decimal if 1-2 digits AND there are digits before the separator
        if (preg_match('/^\d{1,2}$/', $tail) && $lastPos > 0 && substr_count($s, $sep) === 1) {
            $s = substr($s, 0, $lastPos);
        } else {
            // Thousand separator: just strip
            $s = str_replace($sep, '', $s);
        }
    }
    
    // Final clean: only digits
    $s = preg_replace('/[^\d]/', '', $s);
    return $s === '' ? 0 : (int) $s;
}


// ============================================
// URL HELPERS
// ============================================

function url(string $path = ''): string {
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Resolve a menu/CTA URL stored in DB into a full link.
 *  - Absolute URL (http://, https://, mailto:, tel:)  → return as-is
 *  - Anchor (#foo)                                    → return as-is
 *  - Protocol-relative (//cdn...)                     → return as-is
 *  - Path (/about, /produk/foo)                       → prefix with BASE_URL
 *  - Empty / null                                     → "#" placeholder
 * This is critical for subfolder installs where /about must become /demo/kawa/about.
 */
function menu_url(?string $raw): string {
    if ($raw === null || $raw === '') return '#';
    $raw = trim($raw);
    if ($raw === '' || $raw === '#') return '#';
    // Absolute or special schemes
    if (preg_match('#^(https?:|mailto:|tel:|//)#i', $raw)) return $raw;
    // Anchor
    if ($raw[0] === '#') return $raw;
    // Path (with or without leading slash)
    return url('/' . ltrim($raw, '/'));
}


function admin_url(string $path = ''): string {
    return ADMIN_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string {
    return BASE_URL . '/themes/' . get_active_theme() . '/assets/' . ltrim($path, '/');
}

function uploads_url(string $path = ''): string {
    return UPLOADS_URL . '/' . ltrim($path, '/');
}

function redirect(string $url): void {
    // Triple-fallback redirect that NEVER leaves a blank page:
    // 1. HTTP header (fastest, works when no output yet)
    // 2. JS redirect (if headers already sent / output started)
    // 3. <meta refresh> + manual link (if JS disabled)
    if (!headers_sent()) {
        header('Location: ' . $url);
    }
    $html_url = htmlspecialchars($url, ENT_QUOTES);       // for href / meta attributes
    $js_url   = json_encode($url, JSON_UNESCAPED_SLASHES); // safe JS string literal (handles & properly)
    echo '<!DOCTYPE html><html><head><meta charset="utf-8">';
    echo '<meta http-equiv="refresh" content="0;url=' . $html_url . '">';
    echo '<script>window.location.replace(' . $js_url . ');</script>';
    echo '<style>body{font-family:system-ui,sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#F5F7FA}</style>';
    echo '</head><body><div style="text-align:center;color:#475569">';
    echo '<div style="font-size:32px;margin-bottom:12px">&#10003;</div>';
    echo '<div style="font-size:15px;margin-bottom:16px">Tersimpan! Mengalihkan&hellip;</div>';
    echo '<a href="' . $html_url . '" style="color:#2563EB;text-decoration:none;font-size:13px">Klik di sini jika tidak otomatis dialihkan</a>';
    echo '</div></body></html>';
    exit;
}

function current_url(): string {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

// ============================================
// THEME HELPERS
// ============================================

function get_active_theme(): string {
    return get_setting('active_theme', 'default');
}

function theme_path(string $file = ''): string {
    return THEMES_PATH . '/' . get_active_theme() . '/' . ltrim($file, '/');
}

function theme_url(string $file = ''): string {
    return THEMES_URL . '/' . get_active_theme() . '/' . ltrim($file, '/');
}

// ============================================
// IMAGE HELPERS
// ============================================

function get_image(string $path, string $default = ''): string {
    if (empty($path)) {
        return $default ?: (defined('THEMES_URL') ? THEMES_URL . '/default/assets/images/placeholder.svg' : '');
    }
    if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
    return UPLOADS_URL . '/' . ltrim($path, '/');
}

function img_tag(string $src, string $alt = '', string $class = '', string $loading = 'lazy'): string {
    $src = get_image($src);
    $alt = sanitize($alt);
    return "<img src=\"{$src}\" alt=\"{$alt}\" class=\"{$class}\" loading=\"{$loading}\">";
}

// Upload gambar
function upload_image(array $file, string $folder = 'general') {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if ($file['size'] > MAX_UPLOAD_SIZE) return false;
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, ALLOWED_IMAGE_TYPES)) return false;
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_IMAGE_EXTENSIONS)) return false;
    
    $dir = UPLOADS_PATH . '/' . $folder;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $target = $dir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $folder . '/' . $filename;
    }

    return false;
}

// Upload short video (for cube/prism panels). Returns "folder/file" or false.
// Allows mp4/webm; larger cap than images. Used by the Solutions module.
function upload_video(array $file, string $folder = 'solutions', int $maxBytes = 15728640) {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) return false;
    if ($file['size'] > $maxBytes) return false;

    $allowed_mimes = ['video/mp4', 'video/webm', 'video/ogg'];
    $allowed_exts  = ['mp4', 'webm', 'ogv', 'ogg'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowed_mimes, true)) return false;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts, true)) return false;

    $dir = UPLOADS_PATH . '/' . $folder;
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $filename = uniqid() . '_' . time() . '.' . $ext;
    $target = $dir . '/' . $filename;
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $folder . '/' . $filename;
    }
    return false;
}

// ============================================
// AUTH HELPERS
// ============================================

function is_logged_in(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function current_user(): ?array {
    if (!is_logged_in()) return null;
    static $user = null;
    if ($user === null) {
        $user = Database::getInstance()->fetchOne('SELECT * FROM users WHERE id = ?', [$_SESSION['user_id']]);
    }
    return $user;
}

function has_role(string ...$roles): bool {
    $user = current_user();
    if (!$user) return false;
    return in_array($user['role'], $roles);
}

function require_login(): void {
    if (!is_logged_in()) {
        redirect(ADMIN_URL . '/login');
    }
}

function require_role(string ...$roles): void {
    require_login();
    if (!has_role(...$roles)) {
        redirect(ADMIN_URL . '/dashboard?error=akses_ditolak');
    }
}

// ============================================
// CSRF HELPERS
// ============================================

function generate_csrf(): string {
    // Reuse existing token within the same session (still valid + recent enough)
    // This prevents multiple generate_csrf() calls on the same page from invalidating
    // forms that were rendered with an earlier-generated token.
    if (!empty($_SESSION['csrf_token']) && isset($_SESSION['csrf_time']) && (time() - $_SESSION['csrf_time']) < 7200) {
        return $_SESSION['csrf_token'];
    }
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_time'] = time();
    return $token;
}

function verify_csrf(string $token): bool {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_time'])) return false;
    if (time() - $_SESSION['csrf_time'] > 7200) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_field(): string {
    $token = $_SESSION['csrf_token'] ?? generate_csrf();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

// ============================================
// FLASH MESSAGE HELPERS
// ============================================

function set_flash(string $type, string $message): void {
    $_SESSION['flash'][$type] = $message;
}

function get_flash(string $type): ?string {
    if (isset($_SESSION['flash'][$type])) {
        $msg = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $msg;
    }
    return null;
}

function flash_html(): string {
    $msgs = [];
    foreach (['success', 'error', 'warning', 'info'] as $type) {
        $msg = get_flash($type);
        if ($msg) {
            $icon = match($type) {
                'success' => '✓',
                'error'   => '✕',
                'warning' => '⚠',
                default   => 'ℹ'
            };
            $msgs[] = [
                'type' => $type,
                'icon' => $icon,
                'msg'  => $msg,
            ];
        }
    }
    if (empty($msgs)) return '';
    
    $html = '<div class="toast-container" id="toast-container">';
    foreach ($msgs as $m) {
        $html .= '<div class="toast toast-' . $m['type'] . '">';
        $html .= '<span class="toast-icon">' . $m['icon'] . '</span>';
        $html .= '<span class="toast-msg">' . $m['msg'] . '</span>';
        $html .= '<button class="toast-close" onclick="this.parentElement.remove()">×</button>';
        $html .= '</div>';
    }
    $html .= '</div>';
    $html .= '<script>setTimeout(function(){var t=document.getElementById("toast-container");if(t){t.style.opacity="0";setTimeout(function(){t.remove();},400);}}, 4500);</script>';
    return $html;
}

// ============================================
// DATE HELPERS
// ============================================

function format_date(string $date, string $format = 'd F Y'): string {
    $months_id = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    $ts = strtotime($date);
    if ($format === 'd F Y') {
        return date('d', $ts) . ' ' . $months_id[(int)date('n', $ts)] . ' ' . date('Y', $ts);
    }
    return date($format, $ts);
}

function time_ago(string $datetime): string {
    $ts = strtotime($datetime);
    $diff = time() - $ts;
    
    if ($diff < 60) return 'Baru saja';
    if ($diff < 3600) return floor($diff / 60) . ' menit lalu';
    if ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
    if ($diff < 2592000) return floor($diff / 86400) . ' hari lalu';
    return format_date($datetime);
}

// ============================================
// SEO HELPERS
// ============================================

function seo_title(string $title = ''): string {
    $site = get_setting('site_name', 'Reklamepedia');
    if (empty($title)) return get_setting('meta_title_default', $site);
    return $title . ' - ' . $site;
}

function seo_meta(array $data = []): string {
    $title = $data['title'] ?? seo_title();
    $desc = $data['description'] ?? get_setting('meta_desc_default');
    $keywords = $data['keywords'] ?? get_setting('meta_keywords_default');
    $og_image = $data['og_image'] ?? BASE_URL . '/themes/default/assets/images/og-default.jpg';
    $url = $data['url'] ?? current_url();
    
    $html = "<title>" . sanitize($title) . "</title>\n";
    $html .= "<meta name=\"description\" content=\"" . sanitize($desc) . "\">\n";
    $html .= "<meta name=\"keywords\" content=\"" . sanitize($keywords) . "\">\n";
    $html .= "<meta property=\"og:title\" content=\"" . sanitize($title) . "\">\n";
    $html .= "<meta property=\"og:description\" content=\"" . sanitize($desc) . "\">\n";
    $html .= "<meta property=\"og:image\" content=\"{$og_image}\">\n";
    $html .= "<meta property=\"og:url\" content=\"{$url}\">\n";
    $html .= "<meta property=\"og:type\" content=\"website\">\n";
    $html .= "<link rel=\"canonical\" href=\"{$url}\">\n";
    return $html;
}

// ============================================
// ANALYTICS HELPERS
// ============================================

function track_visitor(string $page = '/'): void {
    try {
        $today = date('Y-m-d');
        Database::getInstance()->execute(
            'INSERT INTO statistik_visitor (tanggal, page, views) 
             VALUES (?, ?, 1)
             ON DUPLICATE KEY UPDATE views = views + 1',
            [$today, $page]
        );
    } catch (Exception $e) {
        // Silent fail - jangan ganggu frontend
    }
}

// ============================================
// ACTIVITY LOG
// ============================================

function log_activity(string $aksi, string $keterangan = '', ?int $user_id = null): void {
    if (!$user_id) $user_id = $_SESSION['admin_user']['id'] ?? null;
    try {
        Database::getInstance()->execute(
            'INSERT INTO activity_log (user_id, aksi, keterangan) VALUES (?, ?, ?)',
            [$user_id, $aksi, $keterangan]
        );
    } catch (\Exception $e) {
        // Silently fail - don't break the app for logging errors
    }
}


// ============================================

/**
 * Build a tree from a flat list of categories.
 * Each item must have 'id' and 'parent_id' keys.
 * Returns nested array where each node gets a 'children' key.
 */
function build_category_tree(array $items, $parentId = null): array {
    $tree = [];
    foreach ($items as $item) {
        $itemParent = $item['parent_id'] ?? null;
        // Normalize: 0 / "0" / "" / null are all root
        $itemParent = ($itemParent === null || $itemParent === 0 || $itemParent === '0' || $itemParent === '') ? null : (int)$itemParent;
        $compareParent = ($parentId === null || $parentId === 0 || $parentId === '0' || $parentId === '') ? null : (int)$parentId;
        if ($itemParent === $compareParent) {
            $item['children'] = build_category_tree($items, $item['id']);
            $tree[] = $item;
        }
    }
    return $tree;
}

/**
 * Get all descendant IDs of a category (recursive).
 * Used to filter products/posts under a parent category including all sub-categories.
 */
function get_descendant_ids(array $items, $parentId): array {
    $ids = [];
    foreach ($items as $item) {
        if ((int)($item['parent_id'] ?? 0) === (int)$parentId) {
            $ids[] = (int)$item['id'];
            $ids = array_merge($ids, get_descendant_ids($items, $item['id']));
        }
    }
    return $ids;
}

// SOCIAL ICONS (real SVG, brand colors)
// ============================================
function social_icon_svg(string $type): string {
    $icons = [
        'whatsapp' => '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
        'instagram' => '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
        'facebook' => '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        'youtube' => '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
        'tiktok' => '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
        'twitter' => '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        'linkedin' => '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
        'email' => '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M0 3v18h24V3H0zm21.518 2L12 12.713 2.482 5h19.036zM2 19V7.183l10 8.104 10-8.104V19H2z"/></svg>',
    ];
    return $icons[$type] ?? '';
}

// Brand colors for social platforms
function social_brand_color(string $type): string {
    $colors = [
        'whatsapp'  => '#25D366',
        'instagram' => 'radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%)',
        'facebook'  => '#1877F2',
        'youtube'   => '#FF0000',
        'tiktok'    => '#000000',
        'twitter'   => '#000000',
        'linkedin'  => '#0A66C2',
        'email'     => '#EA4335',
    ];
    return $colors[$type] ?? '#666';
}

// ============================================
// PAGINATION HELPER
// ============================================

function paginate(int $total, int $per_page, int $current_page, string $url_pattern): array {
    $total_pages = (int) ceil($total / $per_page);
    $offset = ($current_page - 1) * $per_page;
    
    return [
        'total'       => $total,
        'per_page'    => $per_page,
        'current'     => $current_page,
        'total_pages' => $total_pages,
        'offset'      => $offset,
        'has_prev'    => $current_page > 1,
        'has_next'    => $current_page < $total_pages,
        'prev_url'    => str_replace('{page}', $current_page - 1, $url_pattern),
        'next_url'    => str_replace('{page}', $current_page + 1, $url_pattern),
        'url_pattern' => $url_pattern
    ];
}

// ============================================
// MENU HELPERS
// ============================================

/**
 * Resolve a single menu item's URL through menu_url() so subfolder paths get BASE_URL prepended.
 * Stores ORIGINAL raw URL in `url_raw` (for compare/active-detection) and overwrites `url` with full URL.
 * This makes raw `$menu["url"]` output in ANY template — old or new — work correctly in subfolder installs.
 */
function _resolve_menu_url(array $item): array {
    $raw = $item["url"] ?? "#";
    $item["url_raw"] = $raw;
    $item["url"]     = menu_url($raw);
    return $item;
}

/**
 * Returns flat list of TOP-LEVEL active menus only (parent_id IS NULL).
 * URLs are pre-resolved with BASE_URL so old templates rendering raw $menu["url"] work in subfolders.
 */
function get_menus(): array {
    $db = Database::getInstance();
    $has_parent = in_array("parent_id", $db->getColumns("menus"));
    $rows = $has_parent
        ? $db->fetchAll("SELECT * FROM menus WHERE is_active = 1 AND parent_id IS NULL ORDER BY urutan ASC, id ASC")
        : $db->fetchAll("SELECT * FROM menus WHERE is_active = 1 ORDER BY urutan ASC, id ASC");
    return array_map("_resolve_menu_url", $rows);
}

/**
 * Returns ALL active menu items, flat (including child items). URLs pre-resolved.
 */
function get_all_menus_flat(): array {
    $rows = Database::getInstance()->fetchAll(
        "SELECT * FROM menus WHERE is_active = 1 ORDER BY COALESCE(parent_id, 0) ASC, urutan ASC, id ASC"
    );
    return array_map("_resolve_menu_url", $rows);
}

/**
 * Returns nested menu tree (each item has "children" key, recursive).
 * URLs are pre-resolved with BASE_URL.
 */
function get_menu_tree(): array {
    $db = Database::getInstance();
    $has_parent = in_array("parent_id", $db->getColumns("menus"));
    if (!$has_parent) {
        $rows = $db->fetchAll("SELECT * FROM menus WHERE is_active = 1 ORDER BY urutan ASC, id ASC");
        $rows = array_map("_resolve_menu_url", $rows);
        return array_map(fn($i) => $i + ["children" => []], $rows);
    }
    return build_category_tree(get_all_menus_flat());
}

// ============================================
// WHATSAPP HELPERS
// ============================================

/**
 * Normalize an Indonesian phone/WhatsApp number into canonical 62xxxxxxxx format.
 * Idempotent — calling twice produces the same result.
 * Accepts: 628139555445, 08139555445, +628139555445, 8139555445, +62 813 9555 445, 62-813-9555-445, etc.
 * Returns: 628139555445 (digits only, single leading 62).
 */
function normalize_phone(string $nomor): string {
    // Strip everything except digits
    $n = preg_replace('/[^0-9]/', '', $nomor);
    if ($n === '') return '';
    // Case 1: already starts with 62 (e.g. 628139555445) — keep as is
    if (str_starts_with($n, '62')) {
        // Strip duplicated 62 prefix like 6262... (defensive against past corruption)
        while (str_starts_with($n, '6262') && strlen($n) > 13) {
            $n = substr($n, 2);
        }
        return $n;
    }
    // Case 2: starts with 0 (e.g. 08139555445) — replace 0 with 62
    if (str_starts_with($n, '0')) {
        return '62' . substr($n, 1);
    }
    // Case 3: starts with 8 (Indonesian mobile without prefix, e.g. 8139555445) — prepend 62
    if (str_starts_with($n, '8')) {
        return '62' . $n;
    }
    // Case 4: any other (e.g. landline 21xxxxxx or another country) — prepend 62 only if it looks Indonesian
    return '62' . $n;
}

function wa_url(string $nomor, string $pesan = ''): string {
    $nomor = normalize_phone($nomor);
    $pesan = urlencode($pesan);
    return "https://wa.me/{$nomor}?text={$pesan}";
}

// ============================================
// JSON RESPONSE
// ============================================

function json_response(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// ============================================
// CONTENT BLOCKS HELPERS (editable text/image per page)
// ============================================

function get_content(string $page_key, string $block_key, string $default = ''): string {
    $cache_key = $page_key . '|' . $block_key;
    if (isset($GLOBALS['__content_cache'][$cache_key])) {
        return $GLOBALS['__content_cache'][$cache_key];
    }
    try {
        $db = Database::getInstance();
        $row = $db->fetchOne(
            "SELECT konten FROM content_blocks WHERE page_key = ? AND block_key = ? AND is_active = 1",
            [$page_key, $block_key]
        );
        $value = ($row && $row['konten'] !== null && $row['konten'] !== '') 
                 ? $row['konten'] : $default;
        $GLOBALS['__content_cache'][$cache_key] = $value;
        return $value;
    } catch (Throwable $e) {
        return $default;
    }
}

function update_content(string $page_key, string $block_key, string $value, string $label = '', string $type = 'text'): bool {
    unset($GLOBALS['__content_cache'][$page_key . '|' . $block_key]);
    try {
        $db = Database::getInstance();
        $existing = $db->fetchOne(
            "SELECT id FROM content_blocks WHERE page_key = ? AND block_key = ?",
            [$page_key, $block_key]
        );
        if ($existing) {
            $db->execute(
                "UPDATE content_blocks SET konten = ?, updated_at = NOW() WHERE id = ?",
                [$value, $existing['id']]
            );
        } else {
            $db->execute(
                "INSERT INTO content_blocks (page_key, block_key, block_label, block_type, konten) VALUES (?, ?, ?, ?, ?)",
                [$page_key, $block_key, $label, $type, $value]
            );
        }
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

function get_page_seo(string $page_key): array {
    static $cache = [];
    if (isset($cache[$page_key])) return $cache[$page_key];
    try {
        $db = Database::getInstance();
        $row = $db->fetchOne("SELECT * FROM page_seo WHERE page_key = ?", [$page_key]);
        $cache[$page_key] = $row ?: [];
        return $cache[$page_key];
    } catch (Throwable $e) {
        return [];
    }
}

