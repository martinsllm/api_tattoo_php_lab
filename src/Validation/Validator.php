<?php

declare(strict_types=1);

namespace App\Validation;

abstract class Validator
{
    abstract public function validate(array $data): array;

}
