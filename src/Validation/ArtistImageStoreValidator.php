<?php

declare(strict_types=1);

namespace App\Validation;

use App\Services\UploadService;
use App\Exception\ValidationFailedException;

class ArtistImageStoreValidator extends Validator
{
    public function __construct(private UploadService $uploadService)
    {
        
    }

    public function validate(array $data): array
    {
        $errors = [];

        $items = $data['items'] ?? [];
        
        if ($items === []) {
            $errors['images'][] = 'At least one image is required.';
        }

        if ($errors !== []) {
            throw new ValidationFailedException($errors);
        }

        return $items;
    }
}