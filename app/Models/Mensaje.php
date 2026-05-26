<?php

// ============================================================
// ARCHIVO: app/Models/Mensaje.php
// QUÉ ES: El modelo que representa la tabla 'mensajes' en la BD.
//         Almacena la conversación entre el sistema y los clientes.
//         Cada mensaje tiene un 'tipo' (enviado/recibido) y
//         un estado de lectura 'leido' (true/false).
// ============================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Permite crear mensajes de prueba con factories
use Illuminate\Database\Eloquent\Model;               // Clase base de todos los modelos Eloquent

class Mensaje extends Model
{
    use HasFactory; // Habilita Mensaje::factory() para generar datos de prueba

    // ─────────────────────────────────────────────────────────
    // $fillable - CAMPOS PERMITIDOS PARA ASIGNACIÓN MASIVA
    // ─────────────────────────────────────────────────────────
    protected $fillable = [
        'cliente_id', // ID del cliente de la conversación (clave foránea → tabla 'clientes')
        'contenido',  // Texto del mensaje
        'tipo',       // 'enviado' = el sistema escribió al cliente | 'recibido' = el cliente escribió
        'leido',      // true = el mensaje ya fue visto | false = no leído (aparece en el contador)
    ];

    // ─────────────────────────────────────────────────────────
    // $casts - CONVERSIÓN AUTOMÁTICA DE TIPOS
    // ─────────────────────────────────────────────────────────
    protected $casts = [
        'leido' => 'boolean',
        // Convierte 1/0 (entero en la BD) a true/false (booleano en PHP)
        // Sin este cast, tendríamos que comparar con === 1 en vez de === true
        // Con este cast: if ($mensaje->leido) funciona directamente como booleano
    ];

    // ─────────────────────────────────────────────────────────
    // RELACIÓN: cliente()
    // TIPO: belongsTo (este mensaje PERTENECE A un cliente)
    // QUÉ HACE: Permite acceder a los datos del cliente con $mensaje->cliente
    // ─────────────────────────────────────────────────────────
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
        // Eloquent busca en la tabla 'clientes' el registro cuyo 'id' = 'cliente_id' del mensaje
        // Uso: $mensaje->cliente->nombre → nombre del cliente que envió/recibió el mensaje
    }
}
