<?php

namespace App\Http\Controllers;

use App\Models\Nosotros;
use Illuminate\Http\Request;

class NosotrosController extends Controller
{
    public function index()
    {
        $pqrs = Nosotros::orderBy('created_at', 'desc')->get();


        return view('nosotros', compact('pqrs'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'nombre'  => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'tipo'    => 'required|in:peticion,queja,reclamo,sugerencia',
            'mensaje' => 'required|string|min:10',
        ], [
            'nombre.required'  => 'El nombre es obligatorio.',
            'email.required'   => 'El correo electrónico es obligatorio.',
            'email.email'      => 'Ingresa un correo electrónico válido.',
            'tipo.required'    => 'Debes seleccionar un tipo de solicitud.',
            'tipo.in'          => 'El tipo de solicitud no es válido.',
            'mensaje.required' => 'El mensaje es obligatorio.',
            'mensaje.min'      => 'El mensaje debe tener al menos 10 caracteres.',
        ]);

        Nosotros::create([
            'nombre'  => $request->nombre,
            'email'   => $request->email,
            'tipo'    => $request->tipo,
            'mensaje' => $request->mensaje,
        ]);


        return redirect()->route('nosotros')
                         ->with('success', '¡Tu solicitud fue enviada correctamente!');
    }
}