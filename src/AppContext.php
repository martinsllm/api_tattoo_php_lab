<?php

declare(strict_types=1);

namespace App;

use App\Repositories\ArtistRepository;

final readonly class AppContext
{
    public function __construct(
        public ArtistRepository $artistRepository
    ) {}
}