<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use App\Controllers\HealthController;
use App\Controllers\ArtistController;
use App\Controllers\ReviewController;
use App\Controllers\ArtistImageController;
use App\Controllers\AuthController;

function routeHandlers(ContainerInterface $container): array
{
    return [
        'health' => [$container->get(HealthController::class), 'index'],
        'artist.index' => [$container->get(ArtistController::class), 'index'],
        'artist.show' => [$container->get(ArtistController::class), 'show'],
        'review.store' => [$container->get(ReviewController::class), 'store'],
        'review.index' => [$container->get(ReviewController::class), 'index'],
        'review.show' => [$container->get(ReviewController::class), 'show'],
        'artist-image.store' => [$container->get(ArtistImageController::class), 'store'],
        'artist-image.show' => [$container->get(ArtistImageController::class), 'show'],
        'artist-image.index' => [$container->get(ArtistImageController::class), 'index'],
        'auth.register' => [$container->get(AuthController::class), 'register'],
        'auth.login' => [$container->get(AuthController::class), 'login']
    ];
}
