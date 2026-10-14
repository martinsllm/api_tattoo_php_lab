<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');
$path = $path === '' ? '/' : $path;

function sendJsonResponse(int $status, array $data): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_THROW_ON_ERROR);
    exit;
}

if ($method === 'GET' && $path === '/health') {
    sendJsonResponse(200, ['ok' => true]);
} else {
    sendJsonResponse(404, ['error' => 'Not Found']);
}