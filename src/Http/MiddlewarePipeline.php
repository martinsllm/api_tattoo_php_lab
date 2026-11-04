<?php

declare(strict_types=1);

namespace App\Http;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class MiddlewarePipeline implements RequestHandlerInterface
{
    public function __construct(
        private RequestHandlerInterface $handler,
        private array $middleware,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if(empty($this->middleware)) 
        {
            return $this->handler->handle($request);
        }

        $middleware = $this->middleware[0];
        $next = array_slice($this->middleware, 1);

        return $middleware->process($request, new self($this->handler, $next));
    }
}