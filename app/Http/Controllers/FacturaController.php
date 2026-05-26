<?php

// ============================================================
// ARCHIVO: FacturaController.php
// QUÉ HACE: Maneja las operaciones de facturas.
//   - store()        → Crear una nueva factura
//   - updateEstado() → Cambiar el estado de una factura
//   - export()       → Descargar facturas como CSV
// ============================================================

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Clase para acceder a los datos enviados en el formulario
use App\Models\Factura;       // Modelo que representa la tabla 'facturas'
use App\Models\Cliente;       // Modelo de clientes (usado para el select del formulario)

class FacturaController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // MÉTODO: store()
    // RUTA: POST /facturas
    // QUÉ HACE: Valida el formulario y crea una nueva factura
    //           en la base de datos con número único autogenerado.
    // ─────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // Validar todos los campos del formulario de nueva factura
        $request->validate([
            'cliente_id'        => 'required|exists:clientes,id',
            // exists:clientes,id → verifica que el cliente exista en la tabla 'clientes'
            'concepto'          => 'required|string|max:255', // Descripción de la factura, obligatoria
            'monto'             => 'required|numeric|min:0',  // Monto monetario, obligatorio y positivo
            'fecha_emision'     => 'required|date',           // Fecha válida en formato Y-m-d
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            // after_or_equal:fecha_emision → la fecha de vencimiento NO puede ser anterior a la de emisión
            // Laravel hace esta validación automáticamente comparando las dos fechas
            'estado'            => 'required|in:pagada,pendiente,vencida',
            // Solo acepta exactamente esos tres estados
        ], [
            // Mensajes de error personalizados
            'cliente_id.required'        => 'Selecciona un cliente.',
            'concepto.required'          => 'El concepto es obligatorio.',
            'monto.required'             => 'El monto es obligatorio.',
            'fecha_emision.required'     => 'La fecha de emisión es obligatoria.',
            'fecha_vencimiento.required' => 'La fecha de vencimiento es obligatoria.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la emisión.',
        ]);

        // Crear el registro de la factura en la base de datos
        Factura::create([
            'numero_factura'    => 'FAC-' . strtoupper(substr(uniqid(), -6)),
            // Mismo patrón que VentaController para generar un código único:
            // uniqid() → ID único por tiempo, substr(..., -6) → últimos 6 chars, strtoupper() → mayúsculas
            // Resultado: "FAC-A3F9B2" → número de factura único y legible
            'cliente_id'        => $request->cliente_id,
            'concepto'          => $request->concepto,          // Descripción o motivo de la factura
            'monto'             => $request->monto,             // Monto a cobrar
            'fecha_emision'     => $request->fecha_emision,     // Fecha en que se emitió la factura
            'fecha_vencimiento' => $request->fecha_vencimiento, // Fecha límite de pago
            'estado'            => $request->estado,            // Estado inicial de la factura
        ]);

        return redirect()->route('facturas')->with('success', 'Factura creada correctamente.');
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: updateEstado()
    // RUTA: PUT /facturas/{id}
    // QUÉ HACE: Actualiza únicamente el estado de una factura
    //           (pagada, pendiente o vencida).
    // ─────────────────────────────────────────────────────────
    public function updateEstado(Request $request, $id)
    {
        $factura = Factura::findOrFail($id); // Buscar la factura; si no existe → error 404 automático

        $request->validate(['estado' => 'required|in:pagada,pendiente,vencida']);
        // Solo validar el campo 'estado' ya que es lo único que se puede cambiar aquí

        $factura->update(['estado' => $request->estado]); // Actualizar solo el campo 'estado'

        return redirect()->route('facturas')->with('success', 'Estado de factura actualizado.');
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: export()
    // RUTA: GET /facturas/exportar
    // QUÉ HACE: Genera y descarga todas las facturas como CSV.
    // ─────────────────────────────────────────────────────────
    public function export()
    {
        $facturas = Factura::with('cliente')->get();
        // Traer TODAS las facturas junto con los datos de su cliente (eager loading)

        $csv  = "\xEF\xBB\xBF"; // BOM para que Excel muestre tildes y caracteres especiales correctamente
        $csv .= "N° Factura,Cliente,Concepto,Monto,Emisión,Vencimiento,Estado\n"; // Encabezados del CSV

        foreach ($facturas as $f) { // Iterar sobre cada factura
            $csv .= implode(',', [ // Unir campos con coma → formato CSV
                $f->numero_factura,
                '"' . trim(($f->cliente->nombre ?? '') . ' ' . ($f->cliente->apellido ?? '')) . '"',
                // Nombre completo del cliente (nombre + apellido) envuelto en comillas
                '"' . str_replace('"', '""', $f->concepto) . '"',
                // Concepto escapado: si contiene comillas, se duplican (estándar CSV)
                $f->monto,                              // Monto como número
                $f->fecha_emision->format('Y-m-d'),     // Fecha de emisión formateada: 2024-01-15
                // NOTA: fecha_emision es objeto Carbon gracias al $casts del modelo Factura
                // Carbon tiene el método format() para formatear fechas fácilmente
                $f->fecha_vencimiento->format('Y-m-d'), // Fecha de vencimiento formateada
                ucfirst($f->estado),                    // vencida → Vencida
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="facturas_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
