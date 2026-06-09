<?php

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model {

    // Nombre de la tabla en la BD
    protected $table = 'mesas';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'numero',
        'capacidad',
        'estado',
    ];
}