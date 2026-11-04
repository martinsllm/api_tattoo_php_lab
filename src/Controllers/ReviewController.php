<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ArtistRepository;
use App\Repositories\ReviewRepository;
use App\Services\JwtService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReviewController
{
    public function __construct(
        private ReviewRepository $reviewRepository,
        private ArtistRepository $artistRepository
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

        if ($review !== false) {
            return jsonResponse(200, $review);
        } 

        return jsonErrorResponse(404, 'Review not found', 'REVIEW_NOT_FOUND');
    }

    public function store(ServerRequestInterface $request, array $params): ResponseInterface
    {
        if ($request->getAttribute('role') !== 'client') {
            return jsonErrorResponse(403, 'Forbidden', 'FORBIDDEN');
        }

        $userId = (int) $request->getAttribute('user_id');

        $data = requestJsonBody($request);
        $errors = [];

        $artistId = null;
        $rating = null;
        $comment = null;

        if (!array_key_exists('artist_id', $data) || $data['artist_id'] === '' || $data['artist_id'] === null) {
            $errors['artist_id'][] = 'Artist ID is required.';
        } else {
            $artistIdFiltered = filter_var($data['artist_id'], FILTER_VALIDATE_INT);
            if ($artistIdFiltered === false || $artistIdFiltered < 1) {
                $errors['artist_id'][] = 'Artist ID must be a positive integer.';
            } else {
                $artistId = $artistIdFiltered;
                if ($this->artistRepository->show($artistId) === false) {
                    $errors['artist_id'][] = 'Artist not found.';
                }
            }
        }

        if (!array_key_exists('rating', $data)) {
            $errors['rating'][] = 'Rating is required.';
        } else {
            $ratingFiltered = filter_var($data['rating'], FILTER_VALIDATE_INT);
            if ($ratingFiltered === false || $ratingFiltered < 1 || $ratingFiltered > 5) {
                $errors['rating'][] = 'Rating must be between 1 and 5.';
            } else {
                $rating = $ratingFiltered;
            }
        }

        if (array_key_exists('comment', $data) && $data['comment'] !== null) {
            if (!is_string($data['comment'])) {
                $errors['comment'][] = 'Comment must be a string.';
            } else {
                $comment = $data['comment'];
            }
        }

        if ($errors !== []) {
            return jsonErrorResponse(422, 'Validation failed', 'VALIDATION_ERROR', $errors);
        }

        $review = $this->reviewRepository->create([
            'artist_id' => $artistId,
            'rating' => $rating,
            'comment' => $comment,
        ]);

        return jsonResponse(201, $review);
    }
}
