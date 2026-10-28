<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Services\JwtService;

class AuthController
{
    public function __construct(private UserRepository $userRepository, private JwtService $jwtService)
    {
    }

    public function register(array $params)
    {
        $data = requestJsonBody();
        $errors = [];

        if (!array_key_exists('email', $data) || $data['email'] === '' || $data['email'] === null) {
            $errors['email'][] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Invalid email address.';
        }

        if(!array_key_exists('name', $data) || $data['name'] === '' || $data['name'] === null) {
            $errors['name'][] = 'Name is required.';
        }

        if (!array_key_exists('password', $data) || $data['password'] === '' || $data['password'] === null) {
            $errors['password'][] = 'Password is required.';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'][] = 'Password must be at least 8 characters long.';
        }

        if (!array_key_exists('role', $data) || $data['role'] === '' || $data['role'] === null) {
            $errors['role'][] = 'Role is required.';
        } elseif ($data['role'] !== 'client' && $data['role'] !== 'artist') {
            $errors['role'][] = 'Invalid role.';
        }

        if ($errors !== []) {
            jsonError(422, 'Validation failed', 'VALIDATION_ERROR', $errors);
        }

        if($this->userRepository->findByEmail($data['email'])) {
            jsonError(409, 'Email already exists.', 'EMAIL_ALREADY_EXISTS');
        }

        $user = $this->userRepository->create([
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'name' => $data['name'],
            'role' => $data['role'] ?? 'client',
        ]);

        jsonSuccess(201, $user);
    }

    public function login(array $params)
    {
        $data = requestJsonBody();
        $errors = [];

        if (!array_key_exists('email', $data) || $data['email'] === '' || $data['email'] === null) {
            $errors['email'][] = 'Email is required.';
        }

        if (!array_key_exists('password', $data) || $data['password'] === '' || $data['password'] === null) {
            $errors['password'][] = 'Password is required.';
        }

        if ($errors !== []) {
            jsonError(422, 'Validation failed', 'VALIDATION_ERROR', $errors);
        }
        
        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user) {
            jsonError(401, 'Invalid credentials.', 'INVALID_CREDENTIALS');
        }

        if (!password_verify($data['password'], $user['password_hash'])) {
            jsonError(401, 'Invalid credentials.', 'INVALID_CREDENTIALS');
        }

        unset($user['password_hash']);
        $token = $this->jwtService->issue($user['id'], $user['role']);
        jsonSuccess(200, [
            'user' => $user,
            'token' => $token,
        ]);
        
    }
}