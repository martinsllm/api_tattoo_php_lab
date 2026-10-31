<?php

declare(strict_types=1);

require_once __DIR__ . '/container.php';

use App\AppContext;
use App\Database\Connection;
use App\Repositories\ArtistRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\ArtistImageRepository;
use App\Repositories\UserRepository;
use App\Routing\Router;
use App\Services\UploadService;
use App\Services\JwtService;

$connection = new Connection();
$uploadsPath = dirname(__DIR__) . '/storage/uploads';

$context = new AppContext(
    new ArtistRepository($connection),
    new ReviewRepository($connection),
    new ArtistImageRepository($connection),
    new UserRepository($connection),
    new UploadService($uploadsPath),
    new JwtService(env('JWT_SECRET'), (int) env('JWT_TTL', '3600')),
);

$container = buildContainer();
$handlers = routeHandlers($context, $container);

$routes = require __DIR__ . '/../src/Routing/routes.php';
$router = new Router($routes);

return [
    'context' => $context,
    'handlers' => $handlers,
    'router' => $router,
];
