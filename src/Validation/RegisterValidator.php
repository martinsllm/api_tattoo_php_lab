<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exception\ValidationFailedException;

class RegisterValidator extends Validator
{
    public function validate(array $data): array
    {
        $errors = [];
        $roles = ['client', 'artist'];

        if (!array_key_exists('email', $data) || $data['email'] === '' || $data['email'] === null) {
            $errors['email'][] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Invalid email address.';
        }

        if (!array_key_exists('name', $data) || $data['name'] === '' || $data['name'] === null) {
            $errors['name'][] = 'Name is required.';
        }

        if (!array_key_exists('password', $data) || $data['password'] === '' || $data['password'] === null) {
            $errors['password'][] = 'Password is required.';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'][] = 'Password must be at least 8 characters long.';
        }

        if (!array_key_exists('role', $data) || $data['role'] === '' || $data['role'] === null) {
            $errors['role'][] = 'Role is required.';
        } elseif (!in_array($data['role'], $roles, true)) {
            $errors['role'][] = 'Invalid role.';
        }

        if ($errors !== []) {
            throw new ValidationFailedException($errors);
        }
        return [
            'email' => (string) $data['email'],
            'name' => (string) $data['name'],
            'password' => (string) $data['password'],
            'role' => (string) $data['role'],
        ];
    }
}