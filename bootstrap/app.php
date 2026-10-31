<?php

declare(strict_types=1);

require_once __DIR__ . '/container.php';

use App\Routing\Router;

$container = buildContainer();
$handlers = routeHandlers($container);

$routes = require __DIR__ . '/../src/Routing/routes.php';
$router = new Router($routes);

return [
    'handlers' => $handlers,
    'router' => $router,
];
