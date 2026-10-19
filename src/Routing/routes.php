<?php

declare(strict_types=1);

return [
    'GET' => [
        '/health' => ['handler' => 'health'],
        '/artists' => ['handler' => 'artist.index'],
        '/artists/{id}' => ['handler' => 'artist.show'],
        '/reviews' => ['handler' => 'review.index'],
        '/reviews/{id}' => ['handler' => 'review.show'],
    ],
    'POST' => [
        '/reviews' => ['handler' => 'review.store'],
    ],
];