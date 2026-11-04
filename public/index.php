<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/env.php';
require __DIR__ . '/../bootstrap/http.php';
require __DIR__ . '/../bootstrap/uploads.php';
require __DIR__ . '/../bootstrap/auth.php';
require __DIR__ . '/../bootstrap/handlers.php';

use App\Http\CallableRequestHandler;
use App\Http\MiddlewarePipeline;
use App\Http\AuthMiddleware;

loadEnv(__DIR__ . '/../.env');

$request = createServerRequest();

$method = $request->getMethod();
$path = requestPath($request);

$app = require __DIR__ . '/../bootstrap/app.php';

$route = $app['router']->match($method, $path);

if ($route === null) {
    emitResponse(jsonErrorResponse(404, 'Not Found', 'ROUTE_NOT_FOUND'));
}

$handlerKey = $route['handler'];

if (!isset($app['handlers'][$handlerKey])) {
    emitResponse(jsonErrorResponse(404, 'Not Found', 'HANDLER_NOT_FOUND'));
}

$callable = $app['handlers'][$handlerKey];

$protected = [
    'review.index', 'review.show', 'review.store',
    'artist-image.index', 'artist-image.show', 'artist-image.store',
];

$handler = new CallableRequestHandler($callable, $route['params']);

$middlewares = in_array($handlerKey, $protected) 
    ? [$app['container']->get(AuthMiddleware::class)] 
    : [];

$pipeline = new MiddlewarePipeline($handler, $middlewares);
$response = $pipeline->handle($request);

emitResponse($response);

