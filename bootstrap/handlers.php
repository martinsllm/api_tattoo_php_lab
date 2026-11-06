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
        'health' => fn ($request, $params) => $container->get(HealthController::class)->index($request, $params),
        'artist.index' => fn ($request, $params) => $container->get(ArtistController::class)->index($request, $params),
        'artist.show' => fn ($request, $params) => $container->get(ArtistController::class)->show($request, $params),
        'review.store' => fn ($request, $params) => $container->get(ReviewController::class)->store($request, $params),
        'review.index' => fn ($request, $params) => $container->get(ReviewController::class)->index($request, $params),
        'review.show' => fn ($request, $params) => $container->get(ReviewController::class)->show($request, $params),
        'artist-image.store' => fn ($request, $params) => $container->get(ArtistImageController::class)->store($request, $params),
        'artist-image.show' => fn ($request, $params) => $container->get(ArtistImageController::class)->show($request, $params),
        'artist-image.index' => fn ($request, $params) => $container->get(ArtistImageController::class)->index($request, $params),
        'auth.register' => fn ($request, $params) => $container->get(AuthController::class)->register($request, $params),
        'auth.login' => fn ($request, $params) => $container->get(AuthController::class)->login($request, $params)
    ];
}
