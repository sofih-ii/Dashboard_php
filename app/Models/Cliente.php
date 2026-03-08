<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'estado',
        'segmento',
        'total_compras',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class);
    }
}