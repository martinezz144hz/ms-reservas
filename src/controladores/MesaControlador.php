<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../modelos/Mesa.php';

class MesaControlador {

    // ============================================
    // LISTAR MESAS
    // ============================================
    public function listar(Request $request, Response $response): Response {
        $mesas = Mesa::all();

        return $this->respuesta($response, $mesas->toArray(), 200);
    }

    // ============================================
    // CREAR MESA
    // ============================================
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

    // ============================================
    // EDITAR MESA
    // ============================================
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

    // ============================================
    // ELIMINAR MESA
    // ============================================
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

    // ============================================
    // HELPER — respuesta JSON
    // ============================================
    private function respuesta(Response $response, array $datos, int $codigo): Response {
        $response->getBody()->write(json_encode($datos));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($codigo);
    }
}

//brrrrr