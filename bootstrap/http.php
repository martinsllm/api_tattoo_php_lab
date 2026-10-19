<?php

declare(strict_types=1);

function requestPath(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = rtrim($path, '/');
    
    return $path === '' ? '/' : $path;
}

function sendJsonResponse(int $status, array $data): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_THROW_ON_ERROR);
    exit;
}

function jsonSuccess(int $status, mixed $data): void
{
    sendJsonResponse($status, ['data' => $data]);
}

function jsonError(int $status, string $message, string $code, ?array $fiedldErrors = null): void
{
    $error = [
        'message' => $message,
        'code' => $code,
    ];

    if ($fiedldErrors !== null) {
        $error['errors'] = $fiedldErrors;
    }

    sendJsonResponse($status, ['error' => $error]);
}

function requestJsonBody(): array
{
    $raw = file_get_contents('php://input');

    if ($raw === false || trim($raw) === '') {
        return [];
    }

    try {
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        jsonError(400, 'Invalid JSON body', 'INVALID_JSON');
    }

    if (!is_array($decoded)) {
        jsonError(400, 'JSON body must be an object', 'INVALID_JSON');
    }

    return $decoded;
}