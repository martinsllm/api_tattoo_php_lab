<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/env.php';
require __DIR__ . '/../bootstrap/http.php';
require __DIR__ . '/../bootstrap/uploads.php';
require __DIR__ . '/../bootstrap/auth.php';
require __DIR__ . '/../bootstrap/handlers.php';

loadEnv(__DIR__ . '/../.env');

$request = createServerRequest();

$method = $request->getMethod();
$path = requestPath($request);

$app = require __DIR__ . '/../bootstrap/app.php';

$route = $app['router']->match($method, $path);

if ($route === null) {
    emitResponse(jsonErrorResponse(404, 'Not Found', 'ROUTE_NOT_FOUND'));
}

$handler = $route['handler'];

if (!isset($app['handlers'][$handler])) {
    emitResponse(jsonErrorResponse(404, 'Not Found', 'HANDLER_NOT_FOUND'));
}

$response = $app['handlers'][$handler]($request, $route['params']);
emitResponse($response);

