<?php

declare(strict_types=1);

namespace App\Exception;

class UploadFailedException extends HttpException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function statusCode(): int
    {
        return 500;
    }

    public function errorCode(): string
    {
        return 'UPLOAD_FAILED';
    }
}