<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Services\JwtService;
use App\Validation\LoginValidator;
use App\Validation\RegisterValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController
{
    public function __construct(private UserRepository $userRepository, private JwtService $jwtService, private LoginValidator $loginValidator, private RegisterValidator $registerValidator)
    {
    }

    public function register(ServerRequestInterface $request, array $params): ResponseInterface
    {
        $data = requestJsonBody($request);
        $data = $this->registerValidator->validate($data);

        if ($this->userRepository->findByEmail($data['email'])) {
            return jsonErrorResponse(409, 'Email already exists.', 'EMAIL_ALREADY_EXISTS');
        }

        $user = $this->userRepository->create([
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'name' => $data['name'],
            'role' => $data['role'],
        ]);

        return jsonResponse(201, $user);
    }

    public function login(ServerRequestInterface $request, array $params): ResponseInterface
    {
        $data = requestJsonBody($request);
        $data = $this->loginValidator->validate($data);

        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user) {
            return jsonErrorResponse(401, 'Invalid credentials.', 'INVALID_CREDENTIALS');
        }

        if (!password_verify($data['password'], $user['password_hash'])) {
            return jsonErrorResponse(401, 'Invalid credentials.', 'INVALID_CREDENTIALS');
        }

        unset($user['password_hash']);
        $token = $this->jwtService->issue($user['id'], $user['role']);
        return jsonResponse(200, [
            'user' => $user,
            'token' => $token,
        ]);

    }
}
