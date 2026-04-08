<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nosotros extends Model
{
    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'tipo',
        'asunto',
        'mensaje',
        'estado'
    ];

    protected $table = 'pqrs';
}