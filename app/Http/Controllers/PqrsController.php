<?php

namespace App\Http\Controllers;

use App\Models\Pqrs;
use Illuminate\Http\Request;

class PqrsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'  => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'telefono'=> 'nullable|string|max:20',
            'modulo'  => 'nullable|string|max:100',
            'tipo'    => 'required|in:peticion,queja,reclamo,sugerencia',
            'asunto'  => 'required|string|max:200',
            'mensaje' => 'required|string|min:10|max:1000',
        ], [
            'nombre.required'  => 'El nombre es obligatorio.',
            'email.required'   => 'El correo es obligatorio.',
            'email.email'      => 'Ingresa un correo válido.',
            'tipo.required'    => 'Selecciona el tipo de solicitud.',
            'asunto.required'  => 'El asunto es obligatorio.',
            'mensaje.required' => 'La descripción es obligatoria.',
            'mensaje.min'      => 'La descripción debe tener al menos 10 caracteres.',
        ]);

        Pqrs::create($validated);

        return redirect()->route('nosotros')
            ->with('pqrs_success', '¡Tu solicitud fue enviada! Te responderemos en menos de 24 horas.');
    }
}