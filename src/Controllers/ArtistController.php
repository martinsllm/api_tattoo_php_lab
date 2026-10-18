<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ArtistRepository;

class ArtistController
{
    
    public function __construct(private ArtistRepository $artistRepository)
    {}

    public function index(array $params): void
    {
        jsonSuccess(200, $this->artistRepository->index());
    }

    public function show(array $params): void
    {
        $id = (int) $params['id'];
        $artist = $this->artistRepository->show($id);

        if ($artist !== false) {
            jsonSuccess(200, $artist);
        }

        jsonError(404, 'Artist not found', 'ARTIST_NOT_FOUND');
    }
}