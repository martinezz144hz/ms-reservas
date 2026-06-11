<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../modelos/Mesa.php';

class MesaControlador {

    //enlistar las mesas
    public function listar(Request $request, Response $response): Response {
        $mesas = Mesa::all();

        return $this->respuesta($response, $mesas->toArray(), 200);
    }

    // crear mesa
    public function crear(Request $request, Response $response): Response {
        $datos = $request->getParsedBody();

        $numero    = $datos['numero']    ?? '';
        $capacidad = $datos['capacidad'] ?? '';
        $estado    = $datos['estado']    ?? 'disponible';

        if (empty($numero) || empty($capacidad)) {
            return $this->respuesta($response, [
                'message' => 'Número y capacidad son requeridos.'
            ], 400);
        }

        // Verificar que el número de mesa no exista
        $existe = Mesa::where('numero', $numero)->first();
        if ($existe) {
            return $this->respuesta($response, [
                'message' => 'Ya existe una mesa con ese número.'
            ], 400);
        }

        $mesa = Mesa::create([
            'numero'    => $numero,
            'capacidad' => $capacidad,
            'estado'    => $estado,
        ]);

        return $this->respuesta($response, [
            'message' => 'Mesa creada correctamente.',
            'mesa'    => $mesa->toArray()
        ], 201);
    }

    //editar mesa
    public function editar(Request $request, Response $response, array $args): Response {
        $id    = $args['id'];
        $datos = $request->getParsedBody();

        $mesa = Mesa::find($id);

        if (!$mesa) {
            return $this->respuesta($response, [
                'message' => 'Mesa no encontrada.'
            ], 404);
        }

        $mesa->numero    = $datos['numero']    ?? $mesa->numero;
        $mesa->capacidad = $datos['capacidad'] ?? $mesa->capacidad;
        $mesa->estado    = $datos['estado']    ?? $mesa->estado;
        $mesa->save();

        return $this->respuesta($response, [
            'message' => 'Mesa actualizada correctamente.',
            'mesa'    => $mesa->toArray()
        ], 200);
    }

    //eliminar mesa
    public function eliminar(Request $request, Response $response, array $args): Response {
        $id   = $args['id'];
        $mesa = Mesa::find($id);

        if (!$mesa) {
            return $this->respuesta($response, [
                'message' => 'Mesa no encontrada.'
            ], 404);
        }

        $mesa->delete();

        return $this->respuesta($response, [
            'message' => 'Mesa eliminada correctamente.'
        ], 200);
    }

    // obtener una mesa
    public function obtener(Request $request, Response $response, array $args): Response {
        $id   = $args['id'];
        $mesa = Mesa::find($id);

        if (!$mesa) {
            return $this->respuesta($response, [
                'message' => 'Mesa no encontrada.'
            ], 404);
        }

        return $this->respuesta($response, $mesa->toArray(), 200);
    }

    // cambiar estado de mesa
    public function cambiarEstado(Request $request, Response $response, array $args): Response {
        $id     = $args['id'];
        $datos  = $request->getParsedBody();
        $estado = $datos['estado'] ?? '';

        if (empty($estado)) {
            return $this->respuesta($response, [
                'message' => 'El estado es requerido.'
            ], 400);
        }

        $mesa = Mesa::find($id);

        if (!$mesa) {
            return $this->respuesta($response, [
                'message' => 'Mesa no encontrada.'
            ], 404);
        }

        $mesa->estado = $estado;
        $mesa->save();

        return $this->respuesta($response, [
            'message' => 'Estado actualizado correctamente.',
            'mesa'    => $mesa->toArray()
        ], 200);
    }

    //respuesta de json
    private function respuesta(Response $response, array $datos, int $codigo): Response {
        $response->getBody()->write(json_encode($datos));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($codigo);
    }
}

