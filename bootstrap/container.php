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
        
    ]);

    return $builder->build();
}