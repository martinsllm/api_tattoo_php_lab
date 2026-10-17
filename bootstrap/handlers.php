<?php

declare(strict_types=1);

use App\AppContext;
use App\Controllers\ArtistController;
use App\Controllers\HealthController;

function routeHandlers(AppContext $app): array
{
    $artistController = new ArtistController($app->artistRepository);
    $healthController = new HealthController();

    return [
        'health' => [$healthController, 'index'],
        'artist.index' => [$artistController, 'index'],
        'artist.show' => [$artistController, 'show'],
    ];
}