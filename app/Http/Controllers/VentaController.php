<?php

// ============================================================
// ARCHIVO: VentaController.php
// QUÉ HACE: Maneja las operaciones CRUD de ventas.
//   - store()        → Registrar una nueva venta
//   - updateEstado() → Cambiar el estado de una venta
//   - destroy()      → Eliminar una venta
//   - export()       → Descargar ventas como CSV
// ============================================================

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Clase para acceder a los datos enviados en el formulario
use App\Models\Venta;         // Modelo que representa la tabla 'ventas'
use App\Models\Cliente;       // Modelo de clientes (para actualizar el contador de compras)

class VentaController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // MÉTODO: store()
    // RUTA: POST /ventas
    // QUÉ HACE: Valida el formulario, crea la venta y actualiza
    //           el contador de compras del cliente.
    // ─────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // Validar los datos del formulario antes de guardar
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            // exists:clientes,id → verifica que el cliente_id enviado exista realmente en la tabla 'clientes'
            // Previene guardar ventas con un cliente_id inventado o inválido
            'producto'   => 'required|string|max:255', // El nombre del producto es obligatorio
            'total'      => 'required|numeric|min:0',  // El total debe ser un número mayor o igual a 0
            'estado'     => 'required|in:completado,pendiente,en_camino,devuelto',
            // Solo acepta exactamente esos cuatro estados posibles
        ], [
            'cliente_id.required' => 'Selecciona un cliente.',
            'cliente_id.exists'   => 'Cliente no válido.',
            'producto.required'   => 'El producto es obligatorio.',
            'total.required'      => 'El total es obligatorio.',
            'total.numeric'       => 'El total debe ser un número.',
        ]);

        // Crear el registro de la venta en la base de datos
        Venta::create([
            'numero_orden' => 'ORD-' . strtoupper(substr(uniqid(), -6)),
            // uniqid() genera un ID único basado en el tiempo con microsegundos (ejemplo: "64f3a9c12b8e7")
            // substr(..., -6) toma los últimos 6 caracteres: "2b8e7f" (más únicos que los del inicio)
            // strtoupper() convierte a mayúsculas: "2B8E7F"
            // Resultado final: "ORD-2B8E7F" → número de orden único y legible
            'cliente_id'   => $request->cliente_id, // ID del cliente seleccionado en el formulario
            'producto'     => $request->producto,    // Nombre del producto vendido
            'total'        => $request->total,       // Monto total de la venta
            'estado'       => $request->estado,      // Estado inicial de la venta
        ]);

        Cliente::where('id', $request->cliente_id)->increment('total_compras');
        // increment() suma 1 al campo 'total_compras' del cliente de forma atómica
        // Es más seguro que: $cliente->total_compras = $cliente->total_compras + 1
        // porque evita condiciones de carrera (race conditions) si hay peticiones simultáneas

        return redirect()->route('ventas')->with('success', 'Venta registrada correctamente.');
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: updateEstado()
    // RUTA: PUT /ventas/{id}
    // QUÉ HACE: Actualiza únicamente el estado de una venta
    //           (completado, pendiente, en_camino, devuelto).
    // ─────────────────────────────────────────────────────────
    public function updateEstado(Request $request, $id)
    {
        $venta = Venta::findOrFail($id); // Buscar la venta por ID; si no existe → error 404 automático

        $request->validate(['estado' => 'required|in:completado,pendiente,en_camino,devuelto']);
        // Validación simple: solo se necesita verificar el campo 'estado'

        $venta->update(['estado' => $request->estado]); // Actualizar solo el campo 'estado'

        return redirect()->route('ventas')->with('success', 'Estado de venta actualizado.');
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: destroy()
    // RUTA: DELETE /ventas/{id}
    // QUÉ HACE: Elimina permanentemente una venta de la BD.
    // ─────────────────────────────────────────────────────────
    public function destroy($id)
    {
        Venta::findOrFail($id)->delete();
        // findOrFail($id) → busca la venta (error 404 si no existe)
        // ->delete()      → la elimina permanentemente de la base de datos

        return redirect()->route('ventas')->with('success', 'Venta eliminada correctamente.');
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: export()
    // RUTA: GET /ventas/exportar
    // QUÉ HACE: Genera y descarga todas las ventas como CSV.
    // ─────────────────────────────────────────────────────────
    public function export()
    {
        $ventas = Venta::with('cliente')->get();
        // Traer TODAS las ventas con los datos de su cliente (eager loading: evita N+1 consultas)

        $csv  = "\xEF\xBB\xBF"; // BOM para que Excel muestre las tildes correctamente
        $csv .= "Orden,Cliente,Producto,Total,Estado,Fecha\n"; // Encabezados del CSV

        foreach ($ventas as $v) { // Iterar sobre cada venta
            $csv .= implode(',', [ // Unir los campos con coma → formato CSV estándar
                $v->numero_orden,
                '"' . trim(($v->cliente->nombre ?? '') . ' ' . ($v->cliente->apellido ?? '')) . '"',
                // Concatenar nombre y apellido del cliente, trim() elimina espacios al inicio/fin
                // ?? '' → si el cliente fue eliminado y nombre/apellido son null, usar vacío
                '"' . str_replace('"', '""', $v->producto) . '"',
                // Escapar comillas en el nombre del producto (por si el producto tiene comillas)
                $v->total,
                ucfirst(str_replace('_', ' ', $v->estado)),
                // str_replace('_', ' ', ...) convierte: en_camino → en camino
                // ucfirst() capitaliza la primera letra: en camino → En camino
                $v->created_at->format('Y-m-d'), // Fecha de la venta: 2024-01-15
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="ventas_' . now()->format('Y-m-d') . '.csv"',
            // El navegador descarga el archivo con el nombre: ventas_2024-01-15.csv
        ]);
    }
}
