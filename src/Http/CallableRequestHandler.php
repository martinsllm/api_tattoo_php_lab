<?php

declare(strict_types=1);

namespace App\Http;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class CallableRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private mixed $callable,
        private array $params
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->callable)($request, $this->params);
    }
}
