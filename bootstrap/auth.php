<?php

declare(strict_types=1);

use App\Services\JwtService;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

function bearerToken(): ?string
{
    $header = $_SERVER['HTTP_AUTHORIZATION']
        ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
        ?? '';

    if ($header === '' && function_exists('getallheaders')) {
        $headers = getallheaders();
        if (is_array($headers)) {
            foreach ($headers as $name => $value) {
                if (strcasecmp((string) $name, 'Authorization') === 0) {
                    $header = (string) $value;
                    break;
                }
            }
        }
    }

    if ($header === '') {
        return null;
    }

    if (! preg_match('/^Bearer\s+(\S+)$/i', $header, $matches)) {
        return null;
    }

    return $matches[1];
}

function requireAuth(JwtService $jwt): array
{
    $token = bearerToken();
    if ($token === null) {
        jsonError(401, 'Unauthorized', 'UNAUTHORIZED');
    }

    try {
        $decoded = $jwt->decode($token);
    } catch (ExpiredException|SignatureInvalidException|\UnexpectedValueException) {
        jsonError(401, 'Unauthorized', 'UNAUTHORIZED');
    }

    return [
        'user_id' => (int) $decoded->sub,
        'role' => (string) $decoded->role,
    ];
}
