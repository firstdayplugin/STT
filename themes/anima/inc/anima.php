<?php
/**
 * Anima theme helpers.
 * hc($key) — read editable Home content via the CMS (get_content('home', $key)),
 * falling back to the default in home.registry.php. Text is escaped; fields whose
 * registry type is 'html' are returned raw. Works both under the CMS and the preview harness.
 */
if (!isset($GLOBALS['ANIMA_HOME_REG'])) {
    $__reg = @include __DIR__ . '/../home.registry.php';
    $GLOBALS['ANIMA_HOME_REG'] = is_array($__reg) ? $__reg : [];
}

if (!function_exists('hc')) {
    function hc(string $key, bool $raw = false): string {
        $reg     = $GLOBALS['ANIMA_HOME_REG'][$key] ?? null;
        $default = is_array($reg) ? (string)($reg['default'] ?? '') : '';
        $isHtml  = is_array($reg) && (($reg['type'] ?? 'text') === 'html');
        $val = function_exists('get_content') ? get_content('home', $key, $default) : $default;
        if ($val === null || $val === '') $val = $default;
        return ($raw || $isHtml) ? (string)$val : htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
    }
}
