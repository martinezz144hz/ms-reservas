<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware {

    public function __invoke(Request $request, RequestHandler $handler): Response {
        $token = $request->getHeaderLine('Authorization');

        if (empty($token)) {
            return $this->respuesta(401, ['message' => 'Token no proporcionado.']);
        }

        $url     = 'http://127.0.0.1:3010/validate';
        $context = stream_context_create([
            'http' => [
                'method'        => 'GET',
                'header'        => 'Authorization: ' . $token . "\r\n",
                'timeout'       => 5,
                'ignore_errors' => true,
            ]
        ]);

        $resultado = file_get_contents($url, false, $context);
        $httpCode  = 401;

        if (isset($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = (int)$matches[1];
                }
            }
        }

        if ($httpCode !== 200) {
            return $this->respuesta(401, ['message' => 'Token inválido o expirado.']);
        }

        return $handler->handle($request);
    }

    private function respuesta(int $codigo, array $datos): Response {
        $factory  = new Slim\Psr7\Factory\ResponseFactory();
        $response = $factory->createResponse($codigo);
        $response->getBody()->write(json_encode($datos));
        return $response->withHeader('Content-Type', 'application/json');
    }
}

//brrrrr