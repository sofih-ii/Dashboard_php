<?php

// ============================================================
// ARCHIVO: app/Models/Factura.php
// QUÉ ES: El modelo que representa la tabla 'facturas' en la BD.
//         Tiene conversión automática de fechas gracias a $casts,
//         y relaciones con Cliente y Venta.
// ============================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Permite usar Factura::factory() en pruebas
use Illuminate\Database\Eloquent\Model;               // Clase base de todos los modelos Eloquent

class Factura extends Model
{
    use HasFactory; // Habilita la creación de facturas de prueba con factories

    // ─────────────────────────────────────────────────────────
    // $fillable - CAMPOS PERMITIDOS PARA ASIGNACIÓN MASIVA
    // ─────────────────────────────────────────────────────────
    protected $fillable = [
        'numero_factura',    // Código único autogenerado (ej: "FAC-B7C4D1")
        'cliente_id',        // ID del cliente al que pertenece (clave foránea → tabla 'clientes')
        'venta_id',          // ID de la venta relacionada (opcional, puede ser null)
        'concepto',          // Descripción del servicio o producto facturado
        'monto',             // Monto total a cobrar (número decimal)
        'fecha_emision',     // Fecha en que se emitió la factura (guardada como DATE en la BD)
        'fecha_vencimiento', // Fecha límite de pago (guardada como DATE en la BD)
        'estado',            // Estado: 'pagada', 'pendiente' o 'vencida'
    ];

    // ─────────────────────────────────────────────────────────
    // $casts - CONVERSIÓN AUTOMÁTICA DE TIPOS
    // Cuando Eloquent lee los valores de la BD, los convierte
    // automáticamente al tipo especificado.
    // ─────────────────────────────────────────────────────────
    protected $casts = [
        'fecha_emision'     => 'date', // Convierte el string "2024-01-15" a objeto Carbon de solo fecha
        'fecha_vencimiento' => 'date', // Igual para la fecha de vencimiento
        // Con este cast, podemos usar: $factura->fecha_emision->format('d/m/Y')
        // Carbon tiene métodos como format(), diffInDays(), isBefore(), etc.
    ];

    // ─────────────────────────────────────────────────────────
    // RELACIÓN: cliente()
    // TIPO: belongsTo (esta factura PERTENECE A un cliente)
    // QUÉ HACE: Permite acceder a los datos del cliente con $factura->cliente
    // ─────────────────────────────────────────────────────────
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
        // Eloquent busca en la tabla 'clientes' el registro cuyo 'id' = 'cliente_id' de esta factura
        // Uso: $factura->cliente->nombre → nombre del cliente de la factura
    }

    // ─────────────────────────────────────────────────────────
    // RELACIÓN: venta()
    // TIPO: belongsTo (esta factura PERTENECE A una venta)
    // QUÉ HACE: Permite acceder a la venta relacionada con $factura->venta
    // NOTA: venta_id puede ser null (no toda factura tiene venta asociada)
    // ─────────────────────────────────────────────────────────
    public function venta()
    {
        return $this->belongsTo(Venta::class);
        // Eloquent busca en la tabla 'ventas' el registro cuyo 'id' = 'venta_id' de esta factura
    }
}
