<?php

declare(strict_types=1);

namespace App\Exception;

class ConflictException extends HttpException
{
    public function __construct(string $message, private readonly string $errorCode)
    {
        parent::__construct($message);
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }
}