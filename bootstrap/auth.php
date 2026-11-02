<?php

declare(strict_types=1);

use App\Services\JwtService;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Psr\Http\Message\ServerRequestInterface;

function bearerToken(ServerRequestInterface $request): ?string
{
    $header = $request->getHeaderLine('Authorization');

    if ($header === '') {
        return null;
    }

    if (! preg_match('/^Bearer\s+(\S+)$/i', $header, $matches)) {
        return null;
    }

    return $matches[1];
}

function requireAuth(ServerRequestInterface $request, JwtService $jwt): array
{
    $token = bearerToken($request);
    if ($token === null) {
        emitResponse(jsonErrorResponse(401, 'Unauthorized', 'UNAUTHORIZED'));
    }

    try {
        $decoded = $jwt->decode($token);
    } catch (ExpiredException|SignatureInvalidException|\UnexpectedValueException) {
        emitResponse(jsonErrorResponse(401, 'Unauthorized', 'UNAUTHORIZED'));
    }

    return [
        'user_id' => (int) $decoded->sub,
        'role' => (string) $decoded->role,
    ];
}
