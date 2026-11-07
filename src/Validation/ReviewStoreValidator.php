<?php

declare(strict_types=1);

namespace App\Validation;

use App\Repositories\ArtistRepository;
use App\Exception\ValidationFailedException;

final class ReviewStoreValidator extends Validator
{
    public function __construct(private ArtistRepository $artistRepository)
    {
    }

    public function validate(array $data): array
    {
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
            throw new ValidationFailedException($errors);
        }

        return [
            'artist_id' => $artistId,
            'rating' => $rating,
            'comment' => $comment,
        ];
    }
}