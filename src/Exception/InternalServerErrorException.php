<?php

declare(strict_types=1);

namespace App\Exception;

final class InternalServerErrorException extends HttpException
{
    public function __construct(string $message = 'Internal server error')
    {
        parent::__construct($message);
    }

    public function statusCode(): int
    {
        return 500;
    }

    public function errorCode(): string
    {
        return 'INTERNAL_ERROR';
    }
}
