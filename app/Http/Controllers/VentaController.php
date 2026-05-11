<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Cliente;

class VentaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'producto'   => 'required|string|max:255',
            'total'      => 'required|numeric|min:0',
            'estado'     => 'required|in:completado,pendiente,en_camino,devuelto',
        ], [
            'cliente_id.required' => 'Selecciona un cliente.',
            'cliente_id.exists'   => 'Cliente no válido.',
            'producto.required'   => 'El producto es obligatorio.',
            'total.required'      => 'El total es obligatorio.',
            'total.numeric'       => 'El total debe ser un número.',
        ]);

        Venta::create([
            'numero_orden' => 'ORD-' . strtoupper(substr(uniqid(), -6)),
            'cliente_id'   => $request->cliente_id,
            'producto'     => $request->producto,
            'total'        => $request->total,
            'estado'       => $request->estado,
        ]);

        Cliente::where('id', $request->cliente_id)->increment('total_compras');

        return redirect()->route('ventas')->with('success', 'Venta registrada correctamente.');
    }

    public function updateEstado(Request $request, $id)
    {
        $venta = Venta::findOrFail($id);
        $request->validate(['estado' => 'required|in:completado,pendiente,en_camino,devuelto']);
        $venta->update(['estado' => $request->estado]);
        return redirect()->route('ventas')->with('success', 'Estado de venta actualizado.');
    }

    public function destroy($id)
    {
        Venta::findOrFail($id)->delete();
        return redirect()->route('ventas')->with('success', 'Venta eliminada correctamente.');
    }

    public function export()
    {
        $ventas = Venta::with('cliente')->get();
        $csv  = "\xEF\xBB\xBF";
        $csv .= "Orden,Cliente,Producto,Total,Estado,Fecha\n";

        foreach ($ventas as $v) {
            $csv .= implode(',', [
                $v->numero_orden,
                '"' . trim(($v->cliente->nombre ?? '') . ' ' . ($v->cliente->apellido ?? '')) . '"',
                '"' . str_replace('"', '""', $v->producto) . '"',
                $v->total,
                ucfirst(str_replace('_', ' ', $v->estado)),
                $v->created_at->format('Y-m-d'),
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="ventas_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
