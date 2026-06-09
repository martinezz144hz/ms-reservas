<?php

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model {

    // Nombre de la tabla en la BD
    protected $table = 'reservas';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'mesa_id',
        'cliente_nombre',
        'cliente_telefono',
        'fecha',
        'hora',
        'personas',
        'estado',
    ];
}
