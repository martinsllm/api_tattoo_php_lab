<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;
use RuntimeException;

final class Connection
{
    private PDO $pdo;

    public function __construct()
    {
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3307');
        $db = env('DB_NAME');
        $user = env('DB_USER');
        $pass = env('DB_PASSWORD');

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Falha ao conectar no banco: ' . $e->getMessage(), 0, $e);
        }
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}
