<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/env.php';
require __DIR__ . '/../bootstrap/http.php';
require __DIR__ . '/../bootstrap/handlers.php';

loadEnv(__DIR__ . '/../.env');

use App\Database\Connection;
use App\Repositories\ArtistRepository;
use App\Routing\Router;

$connection = new Connection();
$artistRepository = new ArtistRepository($connection);

$method = $_SERVER['REQUEST_METHOD'];
$path = requestPath();

$handlers = routeHandlers($artistRepository);

$routes = require __DIR__ . '/../src/Routing/routes.php';
$router = new Router($routes);
$route = $router->match($method, $path);

$handler = $route['handler'];

if (!isset($handlers[$handler])) {
    sendJsonResponse(404, ['error' => 'Not Found']);
}

$handlers[$handler]($route['params']);
