<?php

declare(strict_types=1);

use App\Repositories\ArtistRepository;
use App\Controllers\ArtistController;
use App\Controllers\HealthController;

function routeHandlers(ArtistRepository $artistRepository): array
{
    $artistController = new ArtistController($artistRepository);
    $healthController = new HealthController();

    return [
        'health' => [$healthController, 'index'],
        'artist.index' => [$artistController, 'index'],
        'artist.show' => [$artistController, 'show'],
    ];
}