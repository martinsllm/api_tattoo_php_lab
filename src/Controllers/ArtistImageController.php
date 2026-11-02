<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ArtistImageRepository;
use App\Repositories\ArtistRepository;
use App\Services\JwtService;
use App\Services\UploadService;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ServerRequestInterface;

class ArtistImageController
{
    public function __construct(
        private ArtistImageRepository $artistImageRepository,
        private ArtistRepository $artistRepository,
        private UploadService $uploadService,
        private JwtService $jwtService,
    ) {
    }

    public function index(ServerRequestInterface $request, array $params): ResponseInterface
    {
        \requireAuth($this->jwtService);
        
        $id = (int) $params['id'];

        if($this->artistRepository->show($id) === false) {
            return jsonErrorResponse(404, 'Artist not found', 'ARTIST_NOT_FOUND');
        }

        $images = $this->artistImageRepository->index($id);

        return jsonResponse(200, $images);
    }

    public function show(ServerRequestInterface $request, array $params): ResponseInterface
    {
        \requireAuth($this->jwtService);
        
        $id = (int) $params['id'];
        $artistImage = $this->artistImageRepository->show($id);

        if ($artistImage === false) {
            return jsonErrorResponse(404, 'Artist image not found', 'ARTIST_IMAGE_NOT_FOUND');
        }

        $root = rtrim($this->uploadService->uploadsRoot(), '/');
        $path = $root . '/' . $artistImage['stored_path'];

        if (!is_file($path)) {
            return jsonErrorResponse(404, 'Artist image not found', 'ARTIST_IMAGE_NOT_FOUND');
        }

        $stream = Stream::create(fopen($path, 'rb'));
        return new Response(200, [
            'Content-Type' => $artistImage['mime_type'],
            'Content-Length' => (string) filesize($path),
        ], $stream);
    }

    public function store(ServerRequestInterface $request, array $params): ResponseInterface
    {
        $auth = \requireAuth($this->jwtService);

        $artistId = (int) $params['id'];
        $artist = $this->artistRepository->show($artistId);

        if ($artist === false) {
            return jsonErrorResponse(404, 'Artist not found', 'ARTIST_NOT_FOUND');
        }

        if ($auth['role'] !== 'artist' || $auth['user_id'] !== (int) $artist['user_id']) {
            return jsonErrorResponse(403, 'Forbidden', 'FORBIDDEN');
        }

        $items = $this->uploadService->normalizeFiles('images', $_FILES);
        if ($items === []) {
            return jsonErrorResponse(422, 'Validation failed', 'VALIDATION_ERROR', [
                'images' => ['At least one image is required.'],
            ]);
        }

        $created = [];
        foreach ($items as $file) {
            try {
                $stored = $this->uploadService->uploadFile($artistId, $file);
            } catch (\InvalidArgumentException $e) {
                return jsonErrorResponse(422, 'Validation failed', 'VALIDATION_ERROR', [
                    'images' => [$e->getMessage()],
                ]);
            } catch (\RuntimeException) {
                return jsonErrorResponse(500, 'Could not save upload', 'UPLOAD_FAILED');
            }

            $created[] = $this->artistImageRepository->create([
                'artist_id' => $artistId,
                'stored_path' => $stored['relative_path'],
                'image_filename' => $stored['image_filename'],
                'mime_type' => $stored['mime_type'],
                'size_bytes' => $stored['size_bytes'],
            ]);
        }

        return jsonResponse(201, $created);
    }
}