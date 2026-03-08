<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_orden',
        'cliente_id',
        'producto',
        'total',
        'estado',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }
}