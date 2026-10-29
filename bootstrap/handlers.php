<?php

declare(strict_types=1);

use App\AppContext;
use App\Controllers\ArtistController;
use App\Controllers\HealthController;
use App\Controllers\ReviewController;
use App\Controllers\ArtistImageController;
use App\Controllers\AuthController;

function routeHandlers(AppContext $app): array
{
    $artistController = new ArtistController($app->artistRepository);
    $healthController = new HealthController();
    $reviewController = new ReviewController($app->reviewRepository, $app->artistRepository, $app->jwtService);
    $artistImageController = new ArtistImageController($app->artistImageRepository, $app->artistRepository, $app->uploadService, $app->jwtService);
    $authController = new AuthController($app->userRepository, $app->jwtService);

    return [
        'health' => [$healthController, 'index'],
        'artist.index' => [$artistController, 'index'],
        'artist.show' => [$artistController, 'show'],
        'review.store' => [$reviewController, 'store'],
        'review.index' => [$reviewController, 'index'],
        'review.show' => [$reviewController, 'show'],
        'artist-image.store' => [$artistImageController, 'store'],
        'artist-image.show' => [$artistImageController, 'show'],
        'artist-image.index' => [$artistImageController, 'index'],
        'auth.register' => [$authController, 'register'],
        'auth.login' => [$authController, 'login']
    ];
}