<?php

namespace App\Repositories;

use App\Database\Connection;
use PDO;

class UserRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function create(array $data)
    {
        $pdo = $this->connection->pdo();
        $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, name, role) VALUES (:email, :password_hash, :name, :role)');
        $stmt->bindValue('email', $data['email'], PDO::PARAM_STR);
        $stmt->bindValue('password_hash', $data['password_hash'], PDO::PARAM_STR);
        $stmt->bindValue('name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue('role', $data['role'], PDO::PARAM_STR);
        $stmt->execute();
        
        $id = (int) $pdo->lastInsertId();
        return $this->findById($id);
    }

    public function findByEmail(string $email): array|false
    {
        $pdo = $this->connection->pdo();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->bindValue('email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findById(int $id): array|false
    {
        $pdo = $this->connection->pdo();
        $stmt = $pdo->prepare('SELECT id, email, name, role, created_at FROM users WHERE id = :id');
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}