<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/controladores/MesaControlador.php';
require __DIR__ . '/controladores/ReservaControlador.php';

$mesaControlador   = new MesaControlador();
$reservaControlador = new ReservaControlador();

// ============================================
// RUTAS DE MESAS
// ============================================

// GET /mesas — listar todas las mesas
$app->get('/mesas', function (Request $request, Response $response) use ($mesaControlador) {
    return $mesaControlador->listar($request, $response);
});

// POST /mesas — crear mesa
$app->post('/mesas', function (Request $request, Response $response) use ($mesaControlador) {
    return $mesaControlador->crear($request, $response);
});

// PUT /mesas/{id} — editar mesa
$app->put('/mesas/{id}', function (Request $request, Response $response, array $args) use ($mesaControlador) {
    return $mesaControlador->editar($request, $response, $args);
});

// DELETE /mesas/{id} — eliminar mesa
$app->delete('/mesas/{id}', function (Request $request, Response $response, array $args) use ($mesaControlador) {
    return $mesaControlador->eliminar($request, $response, $args);
});

// ============================================
// RUTAS DE RESERVAS
// ============================================

// GET /reservas — listar todas las reservas
$app->get('/reservas', function (Request $request, Response $response) use ($reservaControlador) {
    return $reservaControlador->listar($request, $response);
});

// POST /reservas — crear reserva
$app->post('/reservas', function (Request $request, Response $response) use ($reservaControlador) {
    return $reservaControlador->crear($request, $response);
});

// PUT /reservas/{id} — editar reserva
$app->put('/reservas/{id}', function (Request $request, Response $response, array $args) use ($reservaControlador) {
    return $reservaControlador->editar($request, $response, $args);
});

// DELETE /reservas/{id} — cancelar reserva
$app->delete('/reservas/{id}', function (Request $request, Response $response, array $args) use ($reservaControlador) {
    return $reservaControlador->cancelar($request, $response, $args);
});