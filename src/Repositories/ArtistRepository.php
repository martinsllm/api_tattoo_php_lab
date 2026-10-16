<?php

namespace App\Repositories;

use App\Database\Connection;
use PDO;

class ArtistRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function index()
    {
        $pdo = $this->connection->pdo();
        $stmt = $pdo->query('SELECT * FROM artists');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function show(int $id)
    {
        $pdo = $this->connection->pdo();
        $stmt = $pdo->prepare('SELECT * FROM artists WHERE id = :id');
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

}
