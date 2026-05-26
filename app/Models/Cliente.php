<?php

// ============================================================
// ARCHIVO: app/Models/Cliente.php
// QUÉ ES: El modelo que representa la tabla 'clientes' en la BD.
//         Define qué campos se pueden guardar y las relaciones
//         con otras tablas (ventas, facturas, mensajes).
// ============================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Permite crear clientes de prueba con factories
use Illuminate\Database\Eloquent\Model;               // Clase base de Eloquent ORM para todos los modelos

class Cliente extends Model
{
    use HasFactory; // Habilita Cliente::factory() para generar datos de prueba en seeders/tests

    // ─────────────────────────────────────────────────────────
    // $fillable - CAMPOS PERMITIDOS PARA ASIGNACIÓN MASIVA
    // Solo estos campos pueden pasarse a Cliente::create([...])
    // o $cliente->update([...]) de forma masiva.
    // Si un campo no está aquí, Eloquent lo ignorará al guardar.
    // ─────────────────────────────────────────────────────────
    protected $fillable = [
        'nombre',        // Primer nombre del cliente
        'apellido',      // Apellido del cliente
        'email',         // Correo electrónico (único en la tabla)
        'telefono',      // Número de teléfono (puede ser null)
        'estado',        // 'activo' o 'inactivo'
        'segmento',      // 'premium', 'regular' u 'ocasional'
        'total_compras', // Contador de cuántas ventas ha tenido (se incrementa en VentaController)
    ];

    // ─────────────────────────────────────────────────────────
    // RELACIÓN: ventas()
    // TIPO: hasMany (un cliente TIENE MUCHAS ventas)
    // QUÉ HACE: Permite obtener todas las ventas de un cliente
    //           con $cliente->ventas
    // CÓMO FUNCIONA: Eloquent busca en la tabla 'ventas' todos
    //   los registros donde cliente_id = $cliente->id
    // ─────────────────────────────────────────────────────────
    public function ventas()
    {
        return $this->hasMany(Venta::class);
        // hasMany(Venta::class) = "este cliente tiene muchas ventas"
        // Laravel asume automáticamente que la clave foránea se llama 'cliente_id'
    }

    // ─────────────────────────────────────────────────────────
    // RELACIÓN: facturas()
    // TIPO: hasMany (un cliente TIENE MUCHAS facturas)
    // ─────────────────────────────────────────────────────────
    public function facturas()
    {
        return $this->hasMany(Factura::class);
        // Busca en la tabla 'facturas' donde cliente_id = $cliente->id
    }

    // ─────────────────────────────────────────────────────────
    // RELACIÓN: mensajes()
    // TIPO: hasMany (un cliente TIENE MUCHOS mensajes)
    // ─────────────────────────────────────────────────────────
    public function mensajes()
    {
        return $this->hasMany(Mensaje::class);
        // Busca en la tabla 'mensajes' donde cliente_id = $cliente->id
        // Permite usar: $cliente->mensajes para obtener toda la conversación
    }
}
