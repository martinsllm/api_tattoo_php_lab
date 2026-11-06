<?php

declare(strict_types=1);

namespace App\Exception;

final class InvalidJsonException extends HttpException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function statusCode(): int
    {
        return 400;
    }

    public function errorCode(): string
    {
        return 'INVALID_JSON';
    }
}