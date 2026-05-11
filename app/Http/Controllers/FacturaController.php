<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\Cliente;

class FacturaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'        => 'required|exists:clientes,id',
            'concepto'          => 'required|string|max:255',
            'monto'             => 'required|numeric|min:0',
            'fecha_emision'     => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            'estado'            => 'required|in:pagada,pendiente,vencida',
        ], [
            'cliente_id.required'        => 'Selecciona un cliente.',
            'concepto.required'          => 'El concepto es obligatorio.',
            'monto.required'             => 'El monto es obligatorio.',
            'fecha_emision.required'     => 'La fecha de emisión es obligatoria.',
            'fecha_vencimiento.required' => 'La fecha de vencimiento es obligatoria.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la emisión.',
        ]);

        Factura::create([
            'numero_factura'    => 'FAC-' . strtoupper(substr(uniqid(), -6)),
            'cliente_id'        => $request->cliente_id,
            'concepto'          => $request->concepto,
            'monto'             => $request->monto,
            'fecha_emision'     => $request->fecha_emision,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'estado'            => $request->estado,
        ]);

        return redirect()->route('facturas')->with('success', 'Factura creada correctamente.');
    }

    public function updateEstado(Request $request, $id)
    {
        $factura = Factura::findOrFail($id);
        $request->validate(['estado' => 'required|in:pagada,pendiente,vencida']);
        $factura->update(['estado' => $request->estado]);
        return redirect()->route('facturas')->with('success', 'Estado de factura actualizado.');
    }

    public function export()
    {
        $facturas = Factura::with('cliente')->get();
        $csv  = "\xEF\xBB\xBF";
        $csv .= "N° Factura,Cliente,Concepto,Monto,Emisión,Vencimiento,Estado\n";

        foreach ($facturas as $f) {
            $csv .= implode(',', [
                $f->numero_factura,
                '"' . trim(($f->cliente->nombre ?? '') . ' ' . ($f->cliente->apellido ?? '')) . '"',
                '"' . str_replace('"', '""', $f->concepto) . '"',
                $f->monto,
                $f->fecha_emision->format('Y-m-d'),
                $f->fecha_vencimiento->format('Y-m-d'),
                ucfirst($f->estado),
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="facturas_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
