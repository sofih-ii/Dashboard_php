<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => 'required|email|unique:clientes,email',
            'telefono' => 'nullable|string|max:20',
            'estado'   => 'required|in:activo,inactivo',
            'segmento' => 'required|in:premium,regular,ocasional',
        ], [
            'nombre.required'   => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'email.required'    => 'El correo es obligatorio.',
            'email.unique'      => 'Este correo ya está registrado.',
        ]);

        Cliente::create([
            'nombre'        => $request->nombre,
            'apellido'      => $request->apellido,
            'email'         => $request->email,
            'telefono'      => $request->telefono,
            'estado'        => $request->estado,
            'segmento'      => $request->segmento,
            'total_compras' => 0,
        ]);

        return redirect()->route('clientes')->with('success', 'Cliente creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => 'required|email|unique:clientes,email,' . $id,
            'telefono' => 'nullable|string|max:20',
            'estado'   => 'required|in:activo,inactivo',
            'segmento' => 'required|in:premium,regular,ocasional',
        ]);

        $cliente->update([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'telefono' => $request->telefono,
            'estado'   => $request->estado,
            'segmento' => $request->segmento,
        ]);

        return redirect()->route('clientes')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy($id)
    {
        Cliente::findOrFail($id)->delete();
        return redirect()->route('clientes')->with('success', 'Cliente eliminado correctamente.');
    }

    public function export()
    {
        $clientes = Cliente::all();
        $csv  = "\xEF\xBB\xBF";
        $csv .= "ID,Nombre,Apellido,Email,Teléfono,Segmento,Compras,Estado,Registrado\n";

        foreach ($clientes as $c) {
            $csv .= implode(',', [
                $c->id,
                '"' . str_replace('"', '""', $c->nombre ?? '') . '"',
                '"' . str_replace('"', '""', $c->apellido ?? '') . '"',
                $c->email,
                $c->telefono ?? '',
                ucfirst($c->segmento ?? ''),
                $c->total_compras ?? 0,
                ucfirst($c->estado),
                $c->created_at->format('Y-m-d'),
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="clientes_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
