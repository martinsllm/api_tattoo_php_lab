<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ArtistImageRepository;
use App\Repositories\ArtistRepository;
use App\Services\JwtService;
use App\Services\UploadService;

class ArtistImageController
{
    public function __construct(
        private ArtistImageRepository $artistImageRepository,
        private ArtistRepository $artistRepository,
        private UploadService $uploadService,
        private JwtService $jwtService,
    ) {
    }

    public function index(array $params): void
    {
        \requireAuth($this->jwtService);
        
        $id = (int) $params['id'];

        if($this->artistRepository->show($id) === false) {
            jsonError(404, 'Artist not found', 'ARTIST_NOT_FOUND');
        }

        $images = $this->artistImageRepository->index($id);

        jsonSuccess(200, $images);
    }

    public function show(array $params): void
    {
        \requireAuth($this->jwtService);
        
        $id = (int) $params['id'];
        $artistImage = $this->artistImageRepository->show($id);

        if ($artistImage === false) {
            jsonError(404, 'Artist image not found', 'ARTIST_IMAGE_NOT_FOUND');
        }

        $root = rtrim($this->uploadService->uploadsRoot(), '/');
        $path = $root . '/' . $artistImage['stored_path'];

        if (!is_file($path)) {
            jsonError(404, 'Artist image not found', 'ARTIST_IMAGE_NOT_FOUND');
        }

        http_response_code(200);
        header('Content-Type: ' . $artistImage['mime_type']);
        header('Content-Length: ' . (string) filesize($path));
        readfile($path);
        exit;
    }

    public function store(array $params): void
    {
        \requireAuth($this->jwtService);
        
        $artistId = (int) $params['id'];
        if ($this->artistRepository->show($artistId) === false) {
            jsonError(404, 'Artist not found', 'ARTIST_NOT_FOUND');
        }

        $items = $this->uploadService->normalizeFiles('images', $_FILES);
        if ($items === []) {
            jsonError(422, 'Validation failed', 'VALIDATION_ERROR', [
                'images' => ['At least one image is required.'],
            ]);
        }

        $created = [];
        foreach ($items as $file) {
            try {
                $stored = $this->uploadService->uploadFile($artistId, $file);
            } catch (\InvalidArgumentException $e) {
                jsonError(422, 'Validation failed', 'VALIDATION_ERROR', [
                    'images' => [$e->getMessage()],
                ]);
            } catch (\RuntimeException) {
                jsonError(500, 'Could not save upload', 'UPLOAD_FAILED');
            }

            $created[] = $this->artistImageRepository->create([
                'artist_id' => $artistId,
                'stored_path' => $stored['relative_path'],
                'image_filename' => $stored['image_filename'],
                'mime_type' => $stored['mime_type'],
                'size_bytes' => $stored['size_bytes'],
            ]);
        }

        jsonSuccess(201, $created);
    }
}