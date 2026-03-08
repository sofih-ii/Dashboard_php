<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_factura',
        'cliente_id',
        'venta_id',
        'concepto',
        'monto',
        'fecha_emision',
        'fecha_vencimiento',
        'estado',
    ];

    protected $casts = [
        'fecha_emision'     => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}