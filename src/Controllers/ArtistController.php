<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ArtistRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ArtistController
{
    public function __construct(private ArtistRepository $artistRepository)
    {
    }

    public function index(ServerRequestInterface $request, array $params): ResponseInterface
    {
        return jsonResponse(200, $this->artistRepository->index());
    }

    public function show(ServerRequestInterface $request, array $params): ResponseInterface
    {
        $id = (int) $params['id'];
        $artist = $this->artistRepository->show($id);

        if ($artist !== false) {
            return jsonResponse(200, $artist);
        }

        return jsonErrorResponse(404, 'Artist not found', 'ARTIST_NOT_FOUND');
    }
}
