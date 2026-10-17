<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ArtistRepository;

class ArtistController
{
    
    public function __construct(private ArtistRepository $artistRepository)
    {
        $this->artistRepository = $artistRepository;
    }

    public function index(array $params): void
    {
        sendJsonResponse(200, ['artists' => $this->artistRepository->index()]);
    }

    public function show(array $params): void
    {
        $id = (int) $params['id'];
        $artist = $this->artistRepository->show($id);

        if ($artist !== false) {
            sendJsonResponse(200, ['artist' => $artist]);
        }

        sendJsonResponse(404, ['error' => 'Artist not found']);
    }
}