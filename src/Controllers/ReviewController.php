<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ArtistRepository;
use App\Repositories\ReviewRepository;
use App\Services\JwtService;

class ReviewController
{
    public function __construct(
        private ReviewRepository $reviewRepository,
        private ArtistRepository $artistRepository,
        private JwtService $jwtService,
    ) {
    }

    public function index(array $params): void
    {
        \requireAuth($this->jwtService);
        jsonSuccess(200, $this->reviewRepository->index());
    }

    public function show(array $params): void
    {
        \requireAuth($this->jwtService);

        $id = (int) $params['id'];
        $review = $this->reviewRepository->show($id);

        if ($review !== false) {
            jsonSuccess(200, $review);
        } 

        jsonError(404, 'Review not found', 'REVIEW_NOT_FOUND');
    }

    public function store(array $params): void
    {
        $auth = \requireAuth($this->jwtService);
        
        if ($auth['role'] !== 'client') {
            jsonError(403, 'Forbidden', 'FORBIDDEN');
        }

        $userId = (int) $auth['user_id'];

        $data = requestJsonBody();
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
            jsonError(422, 'Validation failed', 'VALIDATION_ERROR', $errors);
        }

        $review = $this->reviewRepository->create([
            'artist_id' => $artistId,
            'rating' => $rating,
            'comment' => $comment,
        ]);

        jsonSuccess(201, $review);
    }
}
