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
        if ($default === '') { $default = ui_string_defaults()[$key] ?? ''; }
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

    /**
     * Registry of translatable UI strings: [group => [key => default-language text]].
     * The Translations admin lists these; t('key') falls back here when no default is passed.
     * Add a row here whenever you wrap a new string with t().
     */
    function ui_strings(): array {
        return [
            'Umum' => [
                'all'                => 'Semua',
                'search'             => 'Cari',
                'view_detail'        => 'Lihat detail',
                'read_more'          => 'Selengkapnya',
                'learn_more'         => 'Pelajari lebih lanjut',
                'contact_us'         => 'Hubungi Kami',
                'explore'            => 'Explore',
            ],
            'Career' => [
                'search_jobs'        => 'Cari posisi...',
                'job_role'           => 'Job Role',
                'location'           => 'Location',
                'back_to_jobs'       => 'Semua Lowongan',
                'apply_here'         => 'Lamar Posisi Ini',
                'submit_application' => 'Kirim Lamaran',
                'responsibilities'   => 'Responsibilities',
                'requirements'       => 'Requirements',
                'application_sent'   => 'Terima kasih! Lamaran Anda sudah kami terima. Tim kami akan menghubungi jika cocok.',
            ],
        ];
    }

    /** Flat map of every registry key => default text (used by t() fallback + admin). */
    function ui_string_defaults(): array {
        static $flat = null;
        if ($flat === null) { $flat = []; foreach (ui_strings() as $grp) { $flat += $grp; } }
        return $flat;
    }

    /**
     * Reusable admin editor for per-row field translations (content_i18n).
     * $spec: ['field' => 'Label']  or  ['field' => ['label'=>..,'type'=>'text|textarea|wysiwyg']].
     * Renders one section per non-default language with inputs named i18n[<lang>][<field>],
     * prefilled from stored translations. Drop it inside the module's edit <form>, then call
     * save_i18n_fields($table,$id,$_POST) in that form's POST handler.
     */
    function i18n_fields_editor(string $table, int $id, array $spec): string {
        if ($id <= 0 || !is_multilang()) return '';
        $def = default_lang();
        $others = array_values(array_filter(available_langs(), fn($l) => $l !== $def));
        if (!$others) return '';
        $out = '<div class="card" style="margin-top:16px"><div class="card-header"><div class="card-title">'
             . icon('globe', 15) . ' Terjemahan (opsional)</div><div class="card-subtitle">Kosongkan untuk memakai teks '
             . htmlspecialchars(strtoupper($def)) . '.</div></div><div class="card-body">';
        foreach ($others as $l) {
            $out .= '<div style="font-weight:700;font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em;margin:4px 0 10px">'
                  . htmlspecialchars(strtoupper($l)) . ' — ' . htmlspecialchars(lang_label($l)) . '</div>';
            foreach ($spec as $field => $conf) {
                $label = is_array($conf) ? ($conf['label'] ?? $field) : $conf;
                $type  = is_array($conf) ? ($conf['type'] ?? 'text') : 'text';
                $val   = tr_field($table, $id, (string) $field, '', $l);
                $name  = 'i18n[' . htmlspecialchars($l) . '][' . htmlspecialchars((string) $field) . ']';
                $out  .= '<div class="form-group"><label style="font-size:12px">' . htmlspecialchars($label) . '</label>';
                if ($type === 'textarea') {
                    $out .= '<textarea name="' . $name . '" class="no-wysiwyg" rows="2">' . htmlspecialchars($val) . '</textarea>';
                } elseif ($type === 'wysiwyg') {
                    $out .= '<textarea name="' . $name . '" class="wysiwyg" rows="4">' . htmlspecialchars($val) . '</textarea>';
                } else {
                    $out .= '<input type="text" name="' . $name . '" value="' . htmlspecialchars($val) . '">';
                }
                $out .= '</div>';
            }
        }
        return $out . '</div></div>';
    }

    /** Persist the i18n[<lang>][<field>] inputs rendered by i18n_fields_editor(). */
    function save_i18n_fields(string $table, int $id, array $post): void {
        if ($id <= 0) return;
        $i18n = $post['i18n'] ?? [];
        if (!is_array($i18n)) return;
        foreach ($i18n as $lang => $fields) {
            if (!in_array($lang, available_langs(), true) || $lang === default_lang() || !is_array($fields)) continue;
            foreach ($fields as $field => $value) {
                set_tr_field($table, $id, (string) $field, (string) $lang, (string) $value);
            }
        }
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
