<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/controladores/MesaControlador.php';
require __DIR__ . '/controladores/ReservaControlador.php';
require __DIR__ . '/middleware/AuthMiddleware.php';

$mesaControlador    = new MesaControlador();
$reservaControlador = new ReservaControlador();
$authMiddleware     = new AuthMiddleware();

// rutas de las mesas

$app->get('/mesas', function (Request $request, Response $response) use ($mesaControlador) {
    return $mesaControlador->listar($request, $response);
})->add($authMiddleware);


$app->post('/mesas', function (Request $request, Response $response) use ($mesaControlador) {
    return $mesaControlador->crear($request, $response);
})->add($authMiddleware);


$app->put('/mesas/{id}', function (Request $request, Response $response, array $args) use ($mesaControlador) {
    return $mesaControlador->editar($request, $response, $args);
})->add($authMiddleware);


$app->delete('/mesas/{id}', function (Request $request, Response $response, array $args) use ($mesaControlador) {
    return $mesaControlador->eliminar($request, $response, $args);
})->add($authMiddleware);

// rutas de las reservas


$app->get('/reservas', function (Request $request, Response $response) use ($reservaControlador) {
    return $reservaControlador->listar($request, $response);
})->add($authMiddleware);


$app->post('/reservas', function (Request $request, Response $response) use ($reservaControlador) {
    return $reservaControlador->crear($request, $response);
})->add($authMiddleware);


$app->put('/reservas/{id}', function (Request $request, Response $response, array $args) use ($reservaControlador) {
    return $reservaControlador->editar($request, $response, $args);
})->add($authMiddleware);


$app->delete('/reservas/{id}', function (Request $request, Response $response, array $args) use ($reservaControlador) {
    return $reservaControlador->cancelar($request, $response, $args);
})->add($authMiddleware);

$app->get('/mesas/{id}', function (Request $request, Response $response, array $args) use ($mesaControlador) {
    return $mesaControlador->obtener($request, $response, $args);
})->add($authMiddleware);


$app->put('/mesas/{id}/estado', function (Request $request, Response $response, array $args) use ($mesaControlador) {
    return $mesaControlador->cambiarEstado($request, $response, $args);
})->add($authMiddleware);

//  endpoint verifica que el servicio este activo
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        'servicio' => 'ms-reservas',
        'estado'   => 'activo',
        'puerto'   => 3020
    ]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});