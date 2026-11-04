<?php

declare(strict_types=1);

use Nyholm\Psr7\Response;
use Nyholm\Psr7\Stream;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

function requestPath(ServerRequestInterface $request): string
{
    $path = rtrim($request->getUri()->getPath(), '/');
    return $path === '' ? '/' : $path;
}

function jsonResponse(int $status, array $data): ResponseInterface
{
    $stream = Stream::create(json_encode(['data' => $data], JSON_THROW_ON_ERROR));
    return new Response($status, [
        'Content-Type' => 'application/json; charset=utf-8',
    ], $stream);
}

function jsonErrorResponse(int $status, string $message, string $code, ?array $fiedldErrors = null): ResponseInterface
{
    $error = [
        'message' => $message,
        'code' => $code,
    ];

    if ($fiedldErrors !== null) {
        $error['errors'] = $fiedldErrors;
    }

    $payload = ['error' => $error];

    $stream = Stream::create(json_encode($payload, JSON_THROW_ON_ERROR));
    return new Response($status, [
        'Content-Type' => 'application/json; charset=utf-8',
    ], $stream);
}

function emitResponse(ResponseInterface $response): void
{
    http_response_code($response->getStatusCode());
    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header("{$name}: {$value}", replace: false);
        }
    }
    echo $response->getBody()->getContents();
    exit;
}

function requestJsonBody(ServerRequestInterface $request): array
{
    $raw = (string) $request->getBody();

    if (trim($raw) === '') {
        return [];
    }

    try {
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        emitResponse(jsonErrorResponse(400, 'Invalid JSON body', 'INVALID_JSON'));
    }

    if (!is_array($decoded)) {
        emitResponse(jsonErrorResponse(400, 'JSON body must be an object', 'INVALID_JSON'));
    }

    return $decoded;
}

function createServerRequest(): ServerRequestInterface
{
    $factory = new Psr17Factory();
    $creator = new ServerRequestCreator($factory, $factory, $factory, $factory);
    return $creator->fromGlobals();
}
