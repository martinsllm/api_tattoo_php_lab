<?php

declare(strict_types=1);

namespace App\Exception;

abstract class HttpException extends \Exception
{
    abstract public function statusCode(): int;

    abstract public function errorCode(): string;

    public function fieldErrors(): ?array
    {
        return null;
    }
}
