<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exception\ValidationFailedException;

class LoginValidator extends Validator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (!array_key_exists('email', $data) || $data['email'] === '' || $data['email'] === null) {
            $errors['email'][] = 'Email is required.';
        }

        if (!array_key_exists('password', $data) || $data['password'] === '' || $data['password'] === null) {
            $errors['password'][] = 'Password is required.';
        }

        if ($errors !== []) {
            throw new ValidationFailedException($errors);
        }

        return [
            'email' => (string) $data['email'],
            'password' => (string) $data['password'],
        ];
    }
}
