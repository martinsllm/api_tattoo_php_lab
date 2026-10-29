<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/env.php';
require __DIR__ . '/../bootstrap/http.php';
require __DIR__ . '/../bootstrap/auth.php';
require __DIR__ . '/../bootstrap/handlers.php';

loadEnv(__DIR__ . '/../.env');

$method = $_SERVER['REQUEST_METHOD'];
$path = requestPath();

$app = require __DIR__ . '/../bootstrap/app.php';

$route = $app['router']->match($method, $path);

if ($route === null) {
    jsonError(404, 'Not Found', 'ROUTE_NOT_FOUND');
}

$handler = $route['handler'];

if (!isset($app['handlers'][$handler])) {
    jsonError(404, 'Not Found', 'HANDLER_NOT_FOUND');
}

$app['handlers'][$handler]($route['params']);
