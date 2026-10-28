<?php

declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\JWT;

class JwtService
{
    public function __construct(
        private string $secret,
        private int $ttlSeconds,
    ) 
    {}

    public function issue(int $userId, string $role): string
    {
        $now = time();
        $payload = [
            'sub' => $userId,
            'role' => $role,
            'iat' => $now,
            'exp' => $now + $this->ttlSeconds,
        ];
        
        return JWT::encode($payload, $this->secret, 'HS256');
    }
}