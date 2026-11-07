<?php

declare(strict_types=1);

namespace App\Exception;

final class ValidationFailedException extends HttpException
{
    public function __construct(private readonly array $fieldErrors, string $message = 'Validation failed')
    {
        parent::__construct($message);
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function errorCode(): string
    {
        return 'VALIDATION_ERROR';
    }

    public function fieldErrors(): ?array
    {
        return $this->fieldErrors;
    }
}