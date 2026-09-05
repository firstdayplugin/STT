<?php
/**
 * Anima theme helpers — page-scoped editable content.
 * ac($page,$key)  -> get_content($page,$key) with the default from registry/<page>.php.
 * hc($key)        -> alias for ac('home',$key) (kept for the Home template).
 * Text is escaped; fields whose registry 'type' is 'html' are returned raw.
 * Works under both the CMS and the preview harness.
 */
if (!function_exists('anima_registry')) {
    function anima_registry(string $page): array {
        static $cache = [];
        if (!array_key_exists($page, $cache)) {
            $safe = preg_replace('/[^a-z0-9_-]/', '', $page);
            $f = __DIR__ . '/../registry/' . $safe . '.php';
            $r = is_file($f) ? include $f : [];
            $cache[$page] = is_array($r) ? $r : [];
        }
        return $cache[$page];
    }
}

if (!function_exists('ac')) {
    function ac(string $page, string $key, bool $raw = false): string {
        $reg     = anima_registry($page)[$key] ?? null;
        $default = is_array($reg) ? (string)($reg['default'] ?? '') : '';
        $isHtml  = is_array($reg) && (($reg['type'] ?? 'text') === 'html');
        $val = function_exists('get_content') ? get_content($page, $key, $default) : $default;
        if ($val === null || $val === '') $val = $default;
        return ($raw || $isHtml) ? (string)$val : htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('hc')) {
    function hc(string $key, bool $raw = false): string { return ac('home', $key, $raw); }
}
