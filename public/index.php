<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/env.php';

loadEnv(__DIR__ . '/../.env');

use App\Database\Connection;
use App\Repositories\ArtistRepository;
use App\Routing\Router;

$connection = new Connection();
$artistRepository = new ArtistRepository($connection);

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

$routes = require __DIR__ . '/../src/Routing/routes.php';
$router = new Router($routes);
$route = $router->match($method, $path);


if ($route === null) {
    sendJsonResponse(404, ['error' => 'Not Found']);
}

$handler = $route['handler'];

if ($handler === 'health') {
    sendJsonResponse(200, ['ok' => true]);
} 

if ($handler === 'artist.index') {
    sendJsonResponse(200, ['artists' => $artistRepository->index()]);
}

if ($handler === 'artist.show') {
    $id = (int) $route['params']['id'];
    $artist = $artistRepository->show($id);

    if ($artist !== false) {
        sendJsonResponse(200, ['artist' => $artist]);
    }
    
    sendJsonResponse(404, ['error' => 'Artist not found']);
}

