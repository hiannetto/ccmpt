<?php

namespace Core;

use PDO;
use PDOException;

/**
 * Conexão única (singleton) com o MariaDB.
 */
class Database {
    private static $conn = null;

    public static function connection() {
        if (self::$conn === null) {
            $db = Config::get('db');
            try {
                self::$conn = new PDO(
                    "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4",
                    $db['user'],
                    $db['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                Response::error('Falha na conexão com o banco de dados.', 500);
            }
        }
        return self::$conn;
    }

    /** Mantido por compatibilidade com o código anterior. */
    public function getConnection() {
        return self::connection();
    }
}
