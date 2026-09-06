<?php
/**
 * i18n engine (§D). N-language, path-prefix URLs (/en/...), default language has no prefix.
 * Storage is row-based:
 *   - `translations`  : UI strings  (lang, grp, k, v)
 *   - `content_i18n`  : per-row content field translations (tabel, row_id, field, lang, nilai)
 *   - `content_blocks.lang` : page text per language
 * The current language is resolved from the URL by the router (set_current_lang), default otherwise.
 */

if (!function_exists('default_lang')) {

    /** Site default language (no URL prefix). From setting `default_lang`. */
    function default_lang(): string {
        static $d = null;
        if ($d === null) { $d = get_setting('default_lang', 'id') ?: 'id'; }
        return $d;
    }

    /** Enabled languages, e.g. ['id','en']. From setting `languages` (comma list). */
    function available_langs(): array {
        static $l = null;
        if ($l === null) {
            $raw = get_setting('languages', 'id');
            $l = array_values(array_unique(array_filter(array_map('trim', explode(',', (string)$raw)))));
            if (!$l) $l = ['id'];
            // default language always first / present
            if (!in_array(default_lang(), $l, true)) array_unshift($l, default_lang());
        }
        return $l;
    }

    /** True when more than one language is enabled (drives the switcher etc.). */
    function is_multilang(): bool { return count(available_langs()) > 1; }

    /** Human label for a language code. */
    function lang_label(string $code): string {
        $map = ['id' => 'Indonesia', 'en' => 'English', 'ar' => 'العربية', 'zh' => '中文', 'ja' => '日本語', 'ko' => '한국어', 'th' => 'ไทย', 'vi' => 'Tiếng Việt', 'ms' => 'Melayu'];
        return $map[$code] ?? strtoupper($code);
    }

    /** The active language for this request. */
    function current_lang(): string {
        return $GLOBALS['__lang'] ?? default_lang();
    }

    /** Set the active language (router calls this after reading the URL prefix). */
    function set_current_lang(string $lang): void {
        if (in_array($lang, available_langs(), true)) $GLOBALS['__lang'] = $lang;
    }

    /** URL path prefix for a language ('' for default, '/en' otherwise). */
    function lang_prefix(?string $lang = null): string {
        $lang = $lang ?? current_lang();
        return ($lang === default_lang()) ? '' : '/' . $lang;
    }

    /** Remember the routed path (without lang prefix), for the language switcher. */
    function set_current_path(string $path): void { $GLOBALS['__path'] = trim($path, '/'); }

    /** Same page in another language (keeps the current path, drops query for simplicity). */
    function switch_lang_url(string $lang): string {
        $path = $GLOBALS['__path'] ?? '';
        return url($path, $lang);
    }

    /**
     * Translate a UI string. Looks up `translations` (grp='ui') for the current/desired
     * language, falling back to the provided default (which is the default-language copy).
     */
    function t(string $key, string $default = '', ?string $lang = null): string {
        $lang = $lang ?? current_lang();
        if ($lang === default_lang()) return $default !== '' ? $default : $key;
        static $cache = [];
        if (!isset($cache[$lang])) {
            $cache[$lang] = [];
            try {
                $db = Database::getInstance();
                foreach ($db->fetchAll("SELECT k, v FROM translations WHERE lang = ? AND grp = 'ui'", [$lang]) as $r) {
                    $cache[$lang][$r['k']] = $r['v'];
                }
            } catch (\Throwable $e) { /* table may not exist yet */ }
        }
        $v = $cache[$lang][$key] ?? '';
        return $v !== '' ? $v : ($default !== '' ? $default : $key);
    }

    /**
     * Translate a DB row field. Returns the `content_i18n` value for the current/desired
     * language, falling back to $base (the default-language value stored on the row).
     */
    function tr_field(string $table, int $id, string $field, $base = '', ?string $lang = null): string {
        $lang = $lang ?? current_lang();
        if ($lang === default_lang() || $id <= 0) return (string) $base;
        static $cache = [];
        $ck = $table . '|' . $id . '|' . $lang;
        if (!isset($cache[$ck])) {
            $cache[$ck] = [];
            try {
                $db = Database::getInstance();
                foreach ($db->fetchAll("SELECT field, nilai FROM content_i18n WHERE tabel = ? AND row_id = ? AND lang = ?", [$table, $id, $lang]) as $r) {
                    $cache[$ck][$r['field']] = $r['nilai'];
                }
            } catch (\Throwable $e) { /* table may not exist yet */ }
        }
        $v = $cache[$ck][$field] ?? '';
        return $v !== '' ? $v : (string) $base;
    }

    /** Save a per-row field translation (admin). Empty value removes the override. */
    function set_tr_field(string $table, int $id, string $field, string $lang, string $value): void {
        try {
            $db = Database::getInstance();
            $existing = $db->fetchOne("SELECT id FROM content_i18n WHERE tabel=? AND row_id=? AND field=? AND lang=?", [$table, $id, $field, $lang]);
            if (trim($value) === '') {
                if ($existing) $db->execute("DELETE FROM content_i18n WHERE id=?", [$existing['id']]);
                return;
            }
            if ($existing) $db->execute("UPDATE content_i18n SET nilai=? WHERE id=?", [$value, $existing['id']]);
            else $db->execute("INSERT INTO content_i18n (tabel,row_id,field,lang,nilai) VALUES (?,?,?,?,?)", [$table, $id, $field, $lang, $value]);
        } catch (\Throwable $e) { /* ignore */ }
    }

    /** Render the language switcher (empty when only one language). */
    function language_switcher(string $class = 'lang-switch'): string {
        if (!is_multilang()) return '';
        $cur = current_lang();
        $out = '<div class="' . htmlspecialchars($class) . '">';
        foreach (available_langs() as $l) {
            $out .= '<a href="' . htmlspecialchars(switch_lang_url($l)) . '" class="' . ($l === $cur ? 'on' : '') . '" hreflang="' . htmlspecialchars($l) . '">' . htmlspecialchars(strtoupper($l)) . '</a>';
        }
        return $out . '</div>';
    }
}
