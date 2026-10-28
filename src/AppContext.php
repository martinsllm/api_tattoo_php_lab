<?php

declare(strict_types=1);

namespace App;

use App\Repositories\ArtistRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\ArtistImageRepository;
use App\Repositories\UserRepository;
use App\Services\UploadService;
use App\Services\JwtService;

final readonly class AppContext
{
    public function __construct(
        public ArtistRepository $artistRepository,
        public ReviewRepository $reviewRepository,
        public ArtistImageRepository $artistImageRepository,
        public UserRepository $userRepository,
        public UploadService $uploadService,
        public JwtService $jwtService,
    ) {}
}