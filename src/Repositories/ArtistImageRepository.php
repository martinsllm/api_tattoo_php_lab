<?php

namespace App\Repositories;

use App\Database\Connection;
use PDO;

class ArtistImageRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function index(int $id): array
    {
        $pdo = $this->connection->pdo();
        $stmt = $pdo->prepare('SELECT * FROM artist_images WHERE artist_id = :id ORDER BY created_at DESC');
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function show(int $id): array|false
    {
        $pdo = $this->connection->pdo();
        $stmt = $pdo->prepare('SELECT * FROM artist_images WHERE id = :id');
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create(array $data)
    {
        $pdo = $this->connection->pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO artist_images (artist_id, image_url, stored_path, image_filename, mime_type, size_bytes)
             VALUES (:artist_id, :image_url, :stored_path, :image_filename, :mime_type, :size_bytes)'
        );
        $stmt->bindValue('artist_id', $data['artist_id'], PDO::PARAM_INT);
        $stmt->bindValue('image_url', '', PDO::PARAM_STR);
        $stmt->bindValue('stored_path', $data['stored_path'], PDO::PARAM_STR);
        $stmt->bindValue('image_filename', $data['image_filename'], PDO::PARAM_STR);
        $stmt->bindValue('mime_type', $data['mime_type'], PDO::PARAM_STR);
        $stmt->bindValue('size_bytes', $data['size_bytes'], PDO::PARAM_INT);
        $stmt->execute();

        $id = (int) $pdo->lastInsertId();
        $imageUrl = '/artist-images/' . $id;

        $stmt = $pdo->prepare('UPDATE artist_images SET image_url = :image_url WHERE id = :id');
        $stmt->bindValue('image_url', $imageUrl, PDO::PARAM_STR);
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $pdo->prepare('SELECT * FROM artist_images WHERE id = :id');
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
