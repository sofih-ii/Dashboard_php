<?php

// ============================================================
// ARCHIVO: MensajeController.php
// QUÉ HACE: Maneja el sistema de mensajería entre el sistema
//           y los clientes.
//   - send()          → Enviar mensaje en conversación existente
//   - nuevoMensaje()  → Iniciar una conversación nueva
//   - destroy()       → Eliminar toda una conversación
// ============================================================

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Clase para acceder a los datos del formulario
use App\Models\Mensaje;       // Modelo que representa la tabla 'mensajes'
use App\Models\Cliente;       // Modelo de clientes (para verificar que existe antes de eliminar)

class MensajeController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // MÉTODO: send()
    // RUTA: POST /mensajes/enviar
    // QUÉ HACE: Envía un mensaje dentro de una conversación
    //           que ya está abierta. El tipo siempre es 'enviado'
    //           porque es el sistema quien escribe al cliente.
    // ─────────────────────────────────────────────────────────
    public function send(Request $request)
    {
        // Validar que se haya seleccionado un cliente y que el mensaje tenga contenido
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            // exists:clientes,id → verifica que el cliente exista en la BD
            'contenido'  => 'required|string|max:2000', // El mensaje no puede estar vacío, máximo 2000 chars
        ], [
            'contenido.required' => 'Escribe un mensaje antes de enviar.',
        ]);

        // Crear el registro del mensaje en la base de datos
        Mensaje::create([
            'cliente_id' => $request->cliente_id, // ID del cliente con quien se habla
            'contenido'  => $request->contenido,  // Texto del mensaje
            'tipo'       => 'enviado',             // 'enviado' = el sistema escribió al cliente
            // (Si el cliente escribiera al sistema, el tipo sería 'recibido')
            'leido'      => true, // Los mensajes enviados por el sistema ya están "leídos" desde el inicio
        ]);

        return redirect()->route('mensajes.ver', $request->cliente_id);
        // Redirigir a la conversación con ese cliente: /mensajes/{cliente_id}
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: nuevoMensaje()
    // RUTA: POST /mensajes/nuevo
    // QUÉ HACE: Inicia una conversación nueva con un cliente
    //           seleccionado desde el modal "Nuevo Mensaje".
    //           Funciona igual que send() pero viene de un modal diferente.
    // ─────────────────────────────────────────────────────────
    public function nuevoMensaje(Request $request)
    {
        // Validar que se eligió un destinatario y se escribió un mensaje
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'contenido'  => 'required|string|max:2000',
        ], [
            'cliente_id.required' => 'Selecciona un destinatario.',
            'contenido.required'  => 'Escribe un mensaje.',
        ]);

        // Crear el mensaje en la BD (mismo proceso que send())
        Mensaje::create([
            'cliente_id' => $request->cliente_id,
            'contenido'  => $request->contenido,
            'tipo'       => 'enviado', // El sistema envía el mensaje
            'leido'      => true,      // Ya leído porque lo acaba de escribir el usuario del sistema
        ]);

        return redirect()->route('mensajes.ver', $request->cliente_id)
            ->with('success', 'Mensaje enviado correctamente.');
        // Redirigir a la conversación del cliente y mostrar mensaje de confirmación
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: destroy()
    // RUTA: DELETE /mensajes/{clienteId}/conversacion
    // QUÉ HACE: Elimina TODOS los mensajes de la conversación
    //           con un cliente específico (no solo uno).
    // ─────────────────────────────────────────────────────────
    public function destroy($clienteId)
    {
        Cliente::findOrFail($clienteId);
        // Verificar que el cliente existe; si no → error 404 automático
        // Esto previene intentar eliminar mensajes de un cliente que no existe

        Mensaje::where('cliente_id', $clienteId)->delete();
        // where('cliente_id', $clienteId) → filtrar solo los mensajes de ese cliente
        // ->delete() → eliminar TODOS esos registros de una sola vez (borrado masivo)
        // Es más eficiente que hacer un foreach y eliminar uno por uno

        return redirect()->route('mensajes')->with('success', 'Conversación eliminada.');
        // Redirigir a la bandeja de mensajes (sin conversación activa) con mensaje de confirmación
    }
}
