<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nosotros extends Model
{
    protected $fillable = [
        'nombre',
        'email',
        'tipo',
        'mensaje',
    ];

    protected $table = 'nosotros';
}