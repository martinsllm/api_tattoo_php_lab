<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HealthController
{
    public function index(ServerRequestInterface $request, array $params): ResponseInterface
    {
        return jsonResponse(200, ['ok' => true]);
    }
}
