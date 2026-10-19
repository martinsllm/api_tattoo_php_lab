<?php

namespace App\Repositories;

use App\Database\Connection;
use PDO;

class ReviewRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function create(array $data)
    {
        $pdo = $this->connection->pdo();
        $stmt = $pdo->prepare('INSERT INTO reviews (artist_id, rating, comment) VALUES (:artist_id, :rating, :comment)');
        $stmt->bindValue('artist_id', $data['artist_id'], PDO::PARAM_INT);
        $stmt->bindValue('rating', $data['rating'], PDO::PARAM_INT);
        $stmt->bindValue('comment', $data['comment'], PDO::PARAM_STR);
        $stmt->execute();

        $id = (int) $pdo->lastInsertId();
        $stmt = $pdo->prepare('SELECT * FROM reviews WHERE id = :id');
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }
}