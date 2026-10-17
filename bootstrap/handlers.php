<?php

declare(strict_types=1);

use App\Repositories\ArtistRepository;

function routeHandlers(ArtistRepository $artistRepository): array
{
    return [
        'health' => function (array $params): void {
            sendJsonResponse(200, ['ok' => true]);
        },
        'artist.index' => function (array $params) use ($artistRepository): void {
            sendJsonResponse(200, ['artists' => $artistRepository->index()]);
        },
        'artist.show' => function (array $params) use ($artistRepository): void {
            $id = (int) $params['id'];
            $artist = $artistRepository->show($id);

            if ($artist !== false) {
                sendJsonResponse(200, ['artist' => $artist]);
            }

            sendJsonResponse(404, ['error' => 'Artist not found']);
        },
    ];
}