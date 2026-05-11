<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mensaje;
use App\Models\Cliente;

class MensajeController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'contenido'  => 'required|string|max:2000',
        ], [
            'contenido.required' => 'Escribe un mensaje antes de enviar.',
        ]);

        Mensaje::create([
            'cliente_id' => $request->cliente_id,
            'contenido'  => $request->contenido,
            'tipo'       => 'enviado',
            'leido'      => true,
        ]);

        return redirect()->route('mensajes.ver', $request->cliente_id);
    }

    public function nuevoMensaje(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'contenido'  => 'required|string|max:2000',
        ], [
            'cliente_id.required' => 'Selecciona un destinatario.',
            'contenido.required'  => 'Escribe un mensaje.',
        ]);

        Mensaje::create([
            'cliente_id' => $request->cliente_id,
            'contenido'  => $request->contenido,
            'tipo'       => 'enviado',
            'leido'      => true,
        ]);

        return redirect()->route('mensajes.ver', $request->cliente_id)
            ->with('success', 'Mensaje enviado correctamente.');
    }

    public function destroy($clienteId)
    {
        Cliente::findOrFail($clienteId);
        Mensaje::where('cliente_id', $clienteId)->delete();
        return redirect()->route('mensajes')->with('success', 'Conversación eliminada.');
    }
}
