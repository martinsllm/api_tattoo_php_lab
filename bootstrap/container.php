<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use App\Database\Connection;
use App\Repositories\UserRepository;
use App\Repositories\ArtistRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\ArtistImageRepository;
use App\Services\JwtService;
use App\Services\UploadService;
use App\Controllers\HealthController;
use App\Controllers\ArtistController;
use App\Controllers\AuthController;
use App\Controllers\ReviewController;
use App\Controllers\ArtistImageController;
use App\Http\AuthMiddleware;
use App\Http\ExceptionMiddleware;
use App\Validation\LoginValidator;
use App\Validation\RegisterValidator;

function buildContainer(): ContainerInterface
{
    $builder = new ContainerBuilder();

    $builder->addDefinitions([
        Connection::class => \DI\factory(function (): Connection {
            static $connection;

            return $connection ??= new Connection();
        }),

        UserRepository::class => \DI\create(UserRepository::class)
            ->constructor(\DI\get(Connection::class)),

        ArtistRepository::class => \DI\create(ArtistRepository::class)
            ->constructor(\DI\get(Connection::class)),

        ReviewRepository::class => \DI\create(ReviewRepository::class)
            ->constructor(\DI\get(Connection::class)),

        ArtistImageRepository::class => \DI\create(ArtistImageRepository::class)
            ->constructor(\DI\get(Connection::class)),

        JwtService::class => \DI\create(JwtService::class)->constructor(
            env('JWT_SECRET'),
            (int) env('JWT_TTL', '3600'),
        ),

        UploadService::class => \DI\create(UploadService::class)->constructor(
            dirname(__DIR__) . '/storage/uploads',
        ),

        HealthController::class => \DI\create(HealthController::class),


        ArtistController::class => \DI\create(ArtistController::class)
            ->constructor(\DI\get(ArtistRepository::class)),

        AuthController::class => \DI\create(AuthController::class)
            ->constructor(\DI\get(UserRepository::class), \DI\get(JwtService::class), \DI\get(LoginValidator::class), \DI\get(RegisterValidator::class)),

        ReviewController::class => \DI\create(ReviewController::class)
            ->constructor(\DI\get(ReviewRepository::class), \DI\get(ArtistRepository::class), \DI\get(JwtService::class)),

        ArtistImageController::class => \DI\create(ArtistImageController::class)
            ->constructor(\DI\get(ArtistImageRepository::class), \DI\get(ArtistRepository::class), \DI\get(UploadService::class), \DI\get(JwtService::class)),


        AuthMiddleware::class => \DI\create(AuthMiddleware::class)
            ->constructor(\DI\get(JwtService::class)),

        ExceptionMiddleware::class => \DI\create(ExceptionMiddleware::class),

        LoginValidator::class => \DI\create(LoginValidator::class),
    ]);

    return $builder->build();
}
