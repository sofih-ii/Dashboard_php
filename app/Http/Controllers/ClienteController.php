<?php

// ============================================================
// ARCHIVO: ClienteController.php
// QUÉ HACE: Maneja el CRUD completo de clientes.
//   - store()   → Crear un cliente nuevo
//   - edit()    → Preparar la edición (redirigir con dato flash)
//   - update()  → Guardar los cambios de un cliente editado
//   - destroy() → Eliminar un cliente
//   - export()  → Descargar todos los clientes como CSV
// ============================================================

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Clase para acceder a los datos del formulario (POST, PUT, etc.)
use App\Models\Cliente;       // Modelo que representa la tabla 'clientes' en la base de datos

class ClienteController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // MÉTODO: store()
    // RUTA: POST /clientes
    // QUÉ HACE: Valida el formulario y crea un nuevo cliente
    //           en la base de datos.
    // ─────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // Validar los datos antes de guardar. Si algo falla, Laravel regresa
        // automáticamente al formulario con los mensajes de error.
        $request->validate([
            'nombre'   => 'required|string|max:100', // El nombre es obligatorio, debe ser texto, máximo 100 caracteres
            'apellido' => 'required|string|max:100', // El apellido también es obligatorio
            'email'    => 'required|email|unique:clientes,email',
            // unique:clientes,email → verifica que no exista OTRO cliente con ese email en la tabla 'clientes'
            'telefono' => 'nullable|string|max:20',  // nullable = es opcional (puede estar vacío o null)
            'estado'   => 'required|in:activo,inactivo',
            // in:activo,inactivo → solo acepta exactamente esas dos palabras; nada más
            'segmento' => 'required|in:premium,regular,ocasional',
            // Solo acepta uno de esos tres valores de segmento
        ], [
            // Mensajes de error personalizados en español para cada regla
            'nombre.required'   => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'email.required'    => 'El correo es obligatorio.',
            'email.unique'      => 'Este correo ya está registrado.',
        ]);

        // Insertar el nuevo cliente en la base de datos
        // Solo se pueden guardar campos que están en el $fillable del modelo Cliente
        Cliente::create([
            'nombre'        => $request->nombre,   // Tomar el valor del campo 'nombre' del formulario
            'apellido'      => $request->apellido,
            'email'         => $request->email,
            'telefono'      => $request->telefono, // Puede ser null si el usuario no lo ingresó
            'estado'        => $request->estado,
            'segmento'      => $request->segmento,
            'total_compras' => 0, // Todo cliente nuevo empieza con 0 compras
        ]);

        // Redirigir a la lista de clientes con mensaje de éxito
        // with('success', ...) guarda el mensaje en la sesión para mostrarlo una sola vez en la vista
        return redirect()->route('clientes')->with('success', 'Cliente creado correctamente.');
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: edit()
    // RUTA: GET /clientes/{id}/edit
    // QUÉ HACE: Verifica que el cliente existe y redirige a la
    //           lista con un dato flash para abrir el modal de edición.
    // ─────────────────────────────────────────────────────────
    public function edit($id) // $id viene de la URL: /clientes/5/edit → $id = 5
    {
        Cliente::findOrFail($id);
        // findOrFail busca el cliente por ID. Si NO existe → error 404 automático.
        // Si existe, continuamos (no guardamos el resultado, solo verificamos que existe)

        return redirect()->route('clientes')->with('editarId', $id);
        // Redirigir a la lista de clientes (/clientes)
        // with('editarId', $id) guarda el ID en sesión como dato flash (dura solo una petición)
        // La vista clientes.blade.php lee 'editarId' y abre el modal de edición automáticamente
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: update()
    // RUTA: PUT /clientes/{id}
    // QUÉ HACE: Actualiza los datos de un cliente existente.
    // ─────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id); // Buscar el cliente; si no existe → 404 automático

        $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => 'required|email|unique:clientes,email,' . $id,
            // unique:clientes,email,{id} → verifica unicidad IGNORANDO el registro con ID=$id
            // Sin esto, al guardar el mismo email daría error "ya está registrado"
            'telefono' => 'nullable|string|max:20',
            'estado'   => 'required|in:activo,inactivo',
            'segmento' => 'required|in:premium,regular,ocasional',
        ]);

        // Actualizar solo los campos del cliente en la base de datos
        $cliente->update([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'telefono' => $request->telefono,
            'estado'   => $request->estado,
            'segmento' => $request->segmento,
            // NOTA: total_compras NO se toca aquí (se incrementa en VentaController al crear una venta)
        ]);

        return redirect()->route('clientes')->with('success', 'Cliente actualizado correctamente.');
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: destroy()
    // RUTA: DELETE /clientes/{id}
    // QUÉ HACE: Elimina permanentemente un cliente de la BD.
    // ─────────────────────────────────────────────────────────
    public function destroy($id)
    {
        Cliente::findOrFail($id)->delete();
        // findOrFail($id) → busca el cliente (404 si no existe)
        // ->delete()      → encadena el borrado directamente sobre el objeto encontrado
        // Equivale a: $c = Cliente::findOrFail($id);  $c->delete();

        return redirect()->route('clientes')->with('success', 'Cliente eliminado correctamente.');
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: export()
    // RUTA: GET /clientes/exportar
    // QUÉ HACE: Genera un archivo CSV con todos los clientes
    //           y lo envía al navegador para descargar.
    // ─────────────────────────────────────────────────────────
    public function export()
    {
        $clientes = Cliente::all(); // Traer TODOS los clientes de la base de datos

        $csv  = "\xEF\xBB\xBF"; // BOM (Byte Order Mark): bytes especiales al inicio del CSV
        // El BOM le indica a Excel que el archivo usa UTF-8, para que muestre tildes correctamente

        $csv .= "ID,Nombre,Apellido,Email,Teléfono,Segmento,Compras,Estado,Registrado\n";
        // Primera línea del CSV: encabezados de columna. \n = salto de línea (nueva fila)

        foreach ($clientes as $c) { // Iterar sobre cada cliente
            $csv .= implode(',', [  // implode(',', [...]) une el array con comas → formato CSV
                $c->id,
                '"' . str_replace('"', '""', $c->nombre ?? '') . '"',
                // str_replace('"', '""', ...) escapa comillas internas duplicándolas (estándar CSV)
                // ?? '' → si nombre es null, usar string vacío (operador null coalescing)
                // Se envuelve entre comillas dobles para proteger valores que puedan tener comas
                '"' . str_replace('"', '""', $c->apellido ?? '') . '"',
                $c->email,
                $c->telefono ?? '',             // Si no tiene teléfono, celda vacía
                ucfirst($c->segmento ?? ''),    // ucfirst() capitaliza: premium → Premium
                $c->total_compras ?? 0,
                ucfirst($c->estado),            // activo → Activo
                $c->created_at->format('Y-m-d'), // Fecha formateada: 2024-01-15
            ]) . "\n"; // Salto de línea al final de cada fila del CSV
        }

        // Devolver el CSV como archivo de descarga
        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=utf-8', // Tipo MIME: CSV codificado en UTF-8
            'Content-Disposition' => 'attachment; filename="clientes_' . now()->format('Y-m-d') . '.csv"',
            // attachment; → el navegador descarga en vez de mostrar el contenido
            // filename → nombre del archivo con la fecha de hoy: clientes_2024-01-15.csv
        ]);
    }
}
