<?php

declare(strict_types=1);

namespace App\Exception;

class UnauthorizedException extends HttpException
{
    public function __construct(string $message, private readonly string $errorCode)
    {
        parent::__construct($message);
    }

    public function statusCode(): int
    {
        return 401;
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }
}