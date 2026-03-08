<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'contenido',
        'tipo',
        'leido',
    ];

    protected $casts = [
        'leido' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}