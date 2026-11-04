<?php

declare(strict_types=1);

namespace App\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use App\Services\JwtService;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private JwtService $jwt,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = bearerToken($request);

        if ($token === null) {
            return jsonErrorResponse(401, 'Unauthorized', 'UNAUTHORIZED');
        }

        try {
            $decoded = $this->jwt->decode($token);
        } catch (ExpiredException|SignatureInvalidException|\UnexpectedValueException) {
            return jsonErrorResponse(401, 'Unauthorized', 'UNAUTHORIZED');
        }

        $request = $request
            ->withAttribute('user_id', (int) $decoded->sub)
            ->withAttribute('role', (string) $decoded->role);

        return $handler->handle($request);
    }
}
