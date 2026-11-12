<?php
// ============================================
// REKLAMEPEDIA CMS - Database
// Supports both static (Database::method) and instance ($db->method) calls
// ============================================

class Database {
    private static ?Database $instance = null;
    private ?PDO $pdo = null;

    private function __construct() {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
            ]);
        } catch (PDOException $e) {
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                die('Koneksi database gagal: ' . $e->getMessage());
            }
            die('Sistem sedang mengalami gangguan. Silakan coba beberapa saat lagi.');
        }
    }

    // ----------------------------------------
    // Singleton
    // ----------------------------------------
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Expose PDO for advanced use
    public function getPdo(): PDO {
        return $this->pdo;
    }

    // ----------------------------------------
    // Instance methods (used in admin: $db->...)
    // ----------------------------------------
    public function fetchOne(string $sql, array $params = []): ?array {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function fetchAll(string $sql, array $params = []): array {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): bool {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Insert a row using an associative array.
     * insert('table', ['col1'=>'val1', 'col2'=>'val2'])
     * Returns new row ID or false.
     */
    public function insert(string $table, array $data) {
        $cols = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO `$table` ($cols) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute(array_values($data))) {
            return (int) $this->pdo->lastInsertId();
        }
        return false;
    }

    public function lastInsertId(): int {
        return (int) $this->pdo->lastInsertId();
    }
    
    /**
     * Get list of column names that actually exist in a table.
     * Used to filter $data arrays before INSERT/UPDATE so missing columns don't crash.
     */
    public function getColumns(string $table): array {
        static $cache = [];
        if (isset($cache[$table])) return $cache[$table];
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM `$table`");
            $cols = [];
            foreach ($stmt->fetchAll() as $row) {
                $cols[] = $row['Field'];
            }
            $cache[$table] = $cols;
            return $cols;
        } catch (Throwable $e) {
            return [];
        }
    }
    
    /**
     * Filter associative array to only include keys that exist as columns.
     */
    public function filterColumns(string $table, array $data): array {
        $existing = $this->getColumns($table);
        if (empty($existing)) return $data; // Fallback if SHOW COLUMNS fails
        return array_intersect_key($data, array_flip($existing));
    }

    public function count(string $sql, array $params = []): int {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    // ----------------------------------------
    // Static proxy methods (for legacy code: Database::fetchAll(...))
    // ----------------------------------------
    public static function __callStatic(string $name, array $args) {
        return self::getInstance()->$name(...$args);
    }
}

// ============================================
// Settings helpers (uses Database singleton)
// ============================================
function get_setting(string $key, string $default = ''): string {
    if (isset($GLOBALS['__settings_cache'][$key])) {
        return $GLOBALS['__settings_cache'][$key];
    }
    try {
        $row = Database::getInstance()->fetchOne(
            'SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1',
            [$key]
        );
        $val = $row ? (string)($row['setting_value'] ?? '') : $default;
    } catch (Throwable $e) {
        $val = $default;
    }
    $GLOBALS['__settings_cache'][$key] = $val;
    return $val;
}

function update_setting(string $key, string $value): bool {
    unset($GLOBALS['__settings_cache'][$key]);
    try {
        return Database::getInstance()->execute(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)',
            [$key, $value]
        );
    } catch (Throwable $e) {
        return false;
    }
}
