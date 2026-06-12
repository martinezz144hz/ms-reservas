<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../modelos/Reserva.php';
require_once __DIR__ . '/../modelos/Mesa.php';

class ReservaControlador {

    //enlistar las reservas

    public function listar(Request $request, Response $response): Response {
        $reservas = Reserva::all();

        return $this->respuesta($response, $reservas->toArray(), 200);
    }

// crear una reserva

    public function crear(Request $request, Response $response): Response {
        $datos = $request->getParsedBody();

        $mesa_id          = $datos['mesa_id']          ?? '';
        $cliente_nombre   = $datos['cliente_nombre']   ?? '';
        $cliente_telefono = $datos['cliente_telefono'] ?? '';
        $fecha            = $datos['fecha']            ?? '';
        $hora             = $datos['hora']             ?? '';
        $personas         = $datos['personas']         ?? '';

        // Validar campos requeridos
        if (empty($mesa_id) || empty($cliente_nombre) || empty($fecha) || empty($hora) || empty($personas)) {
            return $this->respuesta($response, [
                'message' => 'Mesa, cliente, fecha, hora y personas son requeridos.'
            ], 400);
        }

        //  mesa existe
        $mesa = Mesa::find($mesa_id);
        if (!$mesa) {
            return $this->respuesta($response, [
                'message' => 'La mesa seleccionada no existe.'
            ], 404);
        }

        //  mesa esté disponible
        if ($mesa->estado !== 'disponible') {
            return $this->respuesta($response, [
                'message' => 'La mesa no está disponible.'
            ], 400);
        }

        // Crear la reserva
        $reserva = Reserva::create([
            'mesa_id'          => $mesa_id,
            'cliente_nombre'   => $cliente_nombre,
            'cliente_telefono' => $cliente_telefono,
            'fecha'            => $fecha,
            'hora'             => $hora,
            'personas'         => $personas,
            'estado'           => 'pendiente',
        ]);

        // Cambiar estado de la mesa a reservada
        $mesa->estado = 'reservada';
        $mesa->save();

        return $this->respuesta($response, [
            'message' => 'Reserva creada correctamente.',
            'reserva' => $reserva->toArray()
        ], 201);
    }

    // editar la reserva

    public function editar(Request $request, Response $response, array $args): Response {
        $id    = $args['id'];
        $datos = $request->getParsedBody();

        $reserva = Reserva::find($id);

        if (!$reserva) {
            return $this->respuesta($response, [
                'message' => 'Reserva no encontrada.'
            ], 404);
        }

        $reserva->cliente_nombre   = $datos['cliente_nombre']   ?? $reserva->cliente_nombre;
        $reserva->cliente_telefono = $datos['cliente_telefono'] ?? $reserva->cliente_telefono;
        $reserva->fecha            = $datos['fecha']            ?? $reserva->fecha;
        $reserva->hora             = $datos['hora']             ?? $reserva->hora;
        $reserva->personas         = $datos['personas']         ?? $reserva->personas;
        $reserva->estado           = $datos['estado']           ?? $reserva->estado;
        $reserva->save();

        return $this->respuesta($response, [
            'message' => 'Reserva actualizada correctamente.',
            'reserva' => $reserva->toArray()
        ], 200);
    }

    // cancelar la reserva

    public function cancelar(Request $request, Response $response, array $args): Response {
        $id      = $args['id'];
        $reserva = Reserva::find($id);

        if (!$reserva) {
            return $this->respuesta($response, [
                'message' => 'Reserva no encontrada.'
            ], 404);
        }

        // Liberar la mesa
        $mesa = Mesa::find($reserva->mesa_id);
        if ($mesa) {
            $mesa->estado = 'disponible';
            $mesa->save();
        }

        // Cancelar la reserva
        $reserva->estado = 'cancelada';
        $reserva->save();

        return $this->respuesta($response, [
            'message' => 'Reserva cancelada correctamente.'
        ], 200);
    }

   // respuesta de json
   
    private function respuesta(Response $response, array $datos, int $codigo): Response {
        $response->getBody()->write(json_encode($datos));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($codigo);
    }
}

