<?php

declare(strict_types=1);

namespace App\Controllers;

class HealthController
{
    public function index(): void
    {
        jsonSuccess(200, ['ok' => true]);
    }
}