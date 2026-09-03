<?php
// db.php

class Database {
    private static $storeDb = null;
    private static $serverDb = null;

    public static function getStoreDb() {
        if (self::$storeDb === null) {
            // Pastikan folder db/ ada dan bisa ditulis
            $dbDir = dirname(DB_STORE_PATH);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }
            
            self::$storeDb = new SQLite3(DB_STORE_PATH, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
            self::$storeDb->enableExceptions(true);
            
            // Buat tabel jika belum ada
            self::initializeStoreTables();
        }
        return self::$storeDb;
    }

    private static function initializeStoreTables() {
        $db = self::$storeDb;
        $sql = "
            CREATE TABLE IF NOT EXISTS store_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                item_id INTEGER UNIQUE NOT NULL,
                name TEXT NOT NULL,
                price_rp INTEGER NOT NULL,
                description TEXT,
                image_url TEXT
            );
            
            CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL,
                item_id INTEGER NOT NULL,
                count INTEGER NOT NULL,
                status TEXT DEFAULT 'pending',
                order_id TEXT UNIQUE,
                payment_token TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME
            );
            
            CREATE INDEX IF NOT EXISTS idx_orders_username ON orders(username);
            CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);
            CREATE INDEX IF NOT EXISTS idx_orders_order_id ON orders(order_id);
        ";
        $db->exec($sql);
    }

    public static function getServerDb() {
        if (self::$serverDb === null) {
            if (!file_exists(DB_SERVER_PATH)) {
                throw new Exception("Database server tidak ditemukan: " . DB_SERVER_PATH);
            }
            self::$serverDb = new SQLite3(DB_SERVER_PATH, SQLITE3_OPEN_READONLY);
            self::$serverDb->enableExceptions(true);
        }
        return self::$serverDb;
    }

    // Helper functions
    public static function fetchAll($db, $sql, $params = []) {
        $stmt = $db->prepare($sql);
        if ($params) {
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
        }
        $result = $stmt->execute();
        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public static function fetchOne($db, $sql, $params = []) {
        $rows = self::fetchAll($db, $sql, $params);
        return $rows[0] ?? null;
    }

    public static function execute($db, $sql, $params = []) {
        $stmt = $db->prepare($sql);
        if ($params) {
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
        }
        return $stmt->execute();
    }

    public static function lastInsertId($db) {
        return $db->lastInsertRowID();
    }
}
?>
