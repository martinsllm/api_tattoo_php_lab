<?php

declare(strict_types=1);

use App\AppContext;
use App\Controllers\ArtistController;
use App\Controllers\HealthController;
use App\Controllers\ReviewController;

function routeHandlers(AppContext $app): array
{
    $artistController = new ArtistController($app->artistRepository);
    $healthController = new HealthController();
    $reviewController = new ReviewController($app->reviewRepository, $app->artistRepository);
    
    return [
        'health' => [$healthController, 'index'],
        'artist.index' => [$artistController, 'index'],
        'artist.show' => [$artistController, 'show'],
        'review.store' => [$reviewController, 'store'],
    ];
}