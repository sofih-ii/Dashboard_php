<?php

// ============================================================
// ARCHIVO: app/Models/Venta.php
// QUÉ ES: El modelo que representa la tabla 'ventas' en la BD.
//         Define los campos permitidos y las relaciones con
//         Cliente (a quién pertenece) y Factura (que puede tener).
// ============================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Permite usar Venta::factory() en seeders/tests
use Illuminate\Database\Eloquent\Model;               // Clase base de todos los modelos Eloquent

class Venta extends Model
{
    use HasFactory; // Habilita la creación de ventas de prueba con factories

    // ─────────────────────────────────────────────────────────
    // $fillable - CAMPOS PERMITIDOS PARA ASIGNACIÓN MASIVA
    // Solo estos campos se pueden guardar con Venta::create([...])
    // ─────────────────────────────────────────────────────────
    protected $fillable = [
        'numero_orden', // Código único autogenerado (ej: "ORD-A3F9B2")
        'cliente_id',   // ID del cliente al que pertenece esta venta (clave foránea → tabla 'clientes')
        'producto',     // Nombre del producto o servicio vendido
        'total',        // Monto total de la venta (número decimal)
        'estado',       // Estado actual: 'completado', 'pendiente', 'en_camino' o 'devuelto'
    ];

    // ─────────────────────────────────────────────────────────
    // RELACIÓN: cliente()
    // TIPO: belongsTo (esta venta PERTENECE A un cliente)
    // QUÉ HACE: Permite acceder a los datos del cliente de la
    //           venta con $venta->cliente
    // INVERSA DE: Cliente::hasMany(Venta::class)
    // ─────────────────────────────────────────────────────────
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
        // belongsTo = "esta venta pertenece a un cliente"
        // Eloquent busca el cliente cuyo 'id' coincide con el 'cliente_id' de esta venta
        // Uso: $venta->cliente->nombre → nombre del cliente de la venta
    }

    // ─────────────────────────────────────────────────────────
    // RELACIÓN: factura()
    // TIPO: hasOne (una venta PUEDE TENER UNA sola factura)
    // QUÉ HACE: Permite acceder a la factura asociada con $venta->factura
    // ─────────────────────────────────────────────────────────
    public function factura()
    {
        return $this->hasOne(Factura::class);
        // hasOne = "esta venta puede tener una factura"
        // Eloquent busca en la tabla 'facturas' donde venta_id = $venta->id
        // A diferencia de hasMany, aquí solo puede haber UN registro relacionado
    }
}
