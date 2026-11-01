<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;

class HealthController
{
    public function index(array $params): ResponseInterface
    {
        return jsonResponse(200, ['ok' => true]);
    }
}