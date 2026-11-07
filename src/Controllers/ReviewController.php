<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ArtistRepository;
use App\Repositories\ReviewRepository;
use App\Exception\NotFoundException;
use App\Exception\ForbiddenException;
use App\Validation\ReviewStoreValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReviewController
{
    public function __construct(
        private ReviewRepository $reviewRepository,
        private ArtistRepository $artistRepository,
        private ReviewStoreValidator $reviewStoreValidator
    ) {
    }

    public function index(ServerRequestInterface $request, array $params): ResponseInterface
    {
        return jsonResponse(200, $this->reviewRepository->index());
    }

    public function show(ServerRequestInterface $request, array $params): ResponseInterface
    {
        $id = (int) $params['id'];
        $review = $this->reviewRepository->show($id);

        if ($review === false) {
            throw new NotFoundException('Review not found', 'REVIEW_NOT_FOUND');
        }

        return jsonResponse(200, $review);
    }

    public function store(ServerRequestInterface $request, array $params): ResponseInterface
    {
        if ($request->getAttribute('role') !== 'client') {
            throw new ForbiddenException('Forbidden', 'FORBIDDEN');
        }

        $userId = (int) $request->getAttribute('user_id');

        $data = requestJsonBody($request);
        $data = $this->reviewStoreValidator->validate($data);

        $review = $this->reviewRepository->create([
            'artist_id' => $data['artist_id'],
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);

        return jsonResponse(201, $review);
    }
}
