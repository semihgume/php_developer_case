<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

class Database {
    private const HOST = '127.0.0.1';
    private const PORT = '5432';
    private const DB_NAME = 'apidb';
    private const USERNAME = 'postgres';
    private const PASSWORD = 'root';

    public function getConnection(): PDO {
        try {
            $dsn = "pgsql:host=" . self::HOST . ";port=" . self::PORT . ";dbname=" . self::DB_NAME;
            $pdo = new PDO($dsn, self::USERNAME, self::PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
        }
        return $pdo;
    }
}