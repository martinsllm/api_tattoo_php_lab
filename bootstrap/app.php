<?php

declare(strict_types=1);

use App\AppContext;
use App\Database\Connection;
use App\Repositories\ArtistRepository;
use App\Repositories\ReviewRepository;
use App\Routing\Router;

$connection = new Connection();

$context = new AppContext(
    new ArtistRepository($connection),
    new ReviewRepository($connection),
);

$handlers = routeHandlers($context);

$routes = require __DIR__ . '/../src/Routing/routes.php';
$router = new Router($routes);

return [
    'context' => $context,
    'handlers' => $handlers,
    'router' => $router,
];
