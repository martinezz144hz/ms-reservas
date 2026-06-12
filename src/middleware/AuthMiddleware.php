\<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware {

    public function __invoke(Request $request, RequestHandler $handler): Response {
        // Temporalmente sin validación
        return $handler->handle($request);
    }
}