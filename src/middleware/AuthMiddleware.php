<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware {

    // validar token con ms auth
    public function __invoke(Request $request, RequestHandler $handler): Response {
        $token = $request->getHeaderLine('Authorization');

        if (empty($token)) {
            return $this->respuesta(401, ['message' => 'Token no proporcionado.']);
        }

        // Consultar ms auth para validar el token
        $url = 'http://127.0.0.1:3010/validate';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $token
        ]);

        $respuesta  = curl_exec($curl);
        $httpCode   = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200) {
            return $this->respuesta(401, ['message' => 'Token inválido o expirado.']);
        }

        // se valido el token continuar con la petición
        return $handler->handle($request);
    }

    // respuesta json

    private function respuesta(int $codigo, array $datos): Response {
        $factory  = new Slim\Psr7\Factory\ResponseFactory();
        $response = $factory->createResponse($codigo);
        $response->getBody()->write(json_encode($datos));
        return $response->withHeader('Content-Type', 'application/json');
    }
}