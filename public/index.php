<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/env.php';

loadEnv(__DIR__ . '/../.env');

use App\Database\Connection;
use App\Repositories\ArtistRepository;

$connection = new Connection();
$artistRepository = new ArtistRepository($connection);

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');
$path = $path === '' ? '/' : $path;
$parts = explode('/', trim($path, '/'));

function sendJsonResponse(int $status, array $data): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_THROW_ON_ERROR);
    exit;
}

if ($method === 'GET' && $path === '/health') {
    sendJsonResponse(200, ['ok' => true]);
} else if ($method === 'GET' && $path === '/artists') {
    sendJsonResponse(200, ['artists' => $artistRepository->index()]);
}  elseif (
    $method === 'GET'
    && count($parts) === 2
    && $parts[0] === 'artists'
    && ctype_digit($parts[1])
) {
    $id = (int) $parts[1];
    $artist = $artistRepository->show($id);
    if ($artist !== false) {
        sendJsonResponse(200, ['artist' => $artist]);
    } else {
        sendJsonResponse(404, ['error' => 'Artist not found']);
    }
} else {
    sendJsonResponse(404, ['error' => 'Not Found']);
}