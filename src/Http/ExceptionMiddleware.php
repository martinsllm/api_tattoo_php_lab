<?php

declare(strict_types=1);

namespace App\Http;

use App\Exception\HttpException;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ExceptionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (HttpException $e) {
            return jsonErrorResponse(
                $e->statusCode(),
                $e->getMessage(),
                $e->errorCode(),
                $e->fieldErrors()
            );
        } catch (\Throwable) {
            return jsonErrorResponse(
                500,
                'Internal Server Error',
                'INTERNAL_ERROR'
            );
        }
    }
}
