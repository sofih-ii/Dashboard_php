<?php

// ============================================================
// ARCHIVO: DashboardController.php
// QUÉ HACE: Es el controlador más complejo del proyecto.
//   Tiene un método por cada sección principal del panel:
//   dashboard, ventas, clientes, facturas, mensajes,
//   estadísticas y análisis.
//   Su trabajo es consultar la BD, calcular totales y
//   enviar los datos a las vistas correspondientes.
// ============================================================

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Clase para manejar peticiones HTTP
use App\Models\Cliente;      // Modelo de la tabla 'clientes'
use App\Models\Venta;        // Modelo de la tabla 'ventas'
use App\Models\Factura;      // Modelo de la tabla 'facturas'
use App\Models\Mensaje;      // Modelo de la tabla 'mensajes'
use App\Models\User;         // Modelo de la tabla 'users'

class DashboardController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // MÉTODO: dashboard()
    // RUTA: GET /dashboard
    // QUÉ HACE: Prepara todos los datos del panel principal:
    //           tarjetas de resumen y actividad reciente.
    // ─────────────────────────────────────────────────────────
    public function dashboard()
    {
        $totalUsuarios = User::count();  // Cuenta cuántos usuarios hay en la tabla 'users'

        $totalVentas = Venta::sum('total'); // Suma el campo 'total' de TODAS las ventas → monto total acumulado

        $totalAlertas = Venta::where('estado', 'pendiente')->count()   // Cuenta ventas con estado 'pendiente'
                      + Factura::where('estado', 'vencida')->count();  // Más las facturas vencidas → total de alertas

        $crecimiento = '+15%'; // Valor estático de ejemplo para mostrar en la tarjeta de crecimiento

        $ultimoUsuario  = User::latest()->first();
        // latest() ordena por created_at DESC (más nuevo primero), first() trae solo el primero
        // → el usuario registrado más recientemente

        $ultimaVenta = Venta::with('cliente')->where('estado', 'completado')->latest()->first();
        // with('cliente') = eager loading: carga los datos del cliente relacionado en UNA sola consulta extra
        // where('estado', 'completado') = solo ventas completadas
        // latest()->first() = la más reciente

        $ultimaFactura = Factura::with('cliente')->latest()->first();
        // La factura más reciente con los datos de su cliente

        $ultimoMensaje = Mensaje::with('cliente')->where('tipo', 'recibido')->latest()->first();
        // El último mensaje recibido (tipo='recibido') con los datos del cliente que lo envió

        $datosRecientes = Cliente::latest()->take(5)->get();
        // take(5) limita el resultado a 5 registros → los 5 clientes más nuevos

        // compact() crea un array asociativo: ['totalUsuarios' => $totalUsuarios, 'totalVentas' => $totalVentas, ...]
        // Estas variables estarán disponibles en welcome.blade.php como $totalUsuarios, $totalVentas, etc.
        return view('welcome', compact(
            'totalUsuarios', 'totalVentas', 'totalAlertas', 'crecimiento',
            'ultimoUsuario', 'ultimaVenta', 'ultimaFactura', 'ultimoMensaje',
            'datosRecientes'
        ));
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: ventas()
    // RUTA: GET /ventas
    // QUÉ HACE: Prepara todos los datos de la sección de ventas:
    //           lista completa, totales, contadores y datos para gráficas.
    // ─────────────────────────────────────────────────────────
    public function ventas()
    {
        $ventas = Venta::with('cliente')->latest()->get();
        // Trae TODAS las ventas con los datos de su cliente, ordenadas de la más nueva a la más antigua

        $totalVentas  = Venta::sum('total');    // Suma monetaria total de todas las ventas
        $totalPedidos = Venta::count();          // Número total de registros de ventas (sin importar el estado)
        $pendientes   = Venta::where('estado', 'pendiente')->count();   // Cuántas ventas están pendientes
        $devoluciones = Venta::where('estado', 'devuelto')->count();    // Cuántas ventas fueron devueltas

        $estadoPedidos = Venta::selectRaw('estado, COUNT(*) as total') // SQL: seleccionar estado y contar cuántas hay de cada uno
            ->groupBy('estado')  // Agrupar por el campo 'estado' (completado, pendiente, en_camino, devuelto)
            ->pluck('total', 'estado'); // pluck('valor', 'clave') convierte el resultado en un array: ['completado' => 15, 'pendiente' => 3, ...]
        // Este array se usa para alimentar las gráficas Chart.js en la vista

        $clientes = Cliente::orderBy('nombre')->get();
        // Lista de todos los clientes ordenados alfabéticamente por nombre
        // Se usa para el select del formulario "Registrar nueva venta"

        return view('ventas', compact(
            'ventas', 'totalVentas', 'totalPedidos', 'pendientes',
            'devoluciones', 'estadoPedidos', 'clientes'
        ));
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: clientes()
    // RUTA: GET /clientes
    // QUÉ HACE: Prepara los datos de la sección de clientes:
    //           lista completa, contadores por estado y segmentación.
    // ─────────────────────────────────────────────────────────
    public function clientes()
    {
        $clientes      = Cliente::latest()->get();   // Todos los clientes, de más nuevo a más antiguo
        $totalClientes = Cliente::count();            // Total de clientes registrados
        $activos       = Cliente::where('estado', 'activo')->count();    // Cuántos están activos
        $inactivos     = Cliente::where('estado', 'inactivo')->count();  // Cuántos están inactivos

        $nuevos = Cliente::whereMonth('created_at', now()->month)->count();
        // Clientes registrados en el mes actual
        // whereMonth() filtra por el número de mes (1=enero, ..., 12=diciembre)
        // now()->month devuelve el número del mes actual

        $segmentacion = Cliente::selectRaw('segmento, COUNT(*) as total')
            ->groupBy('segmento')     // Agrupar por segmento: premium, regular, ocasional
            ->pluck('total', 'segmento'); // Resultado: ['premium' => 10, 'regular' => 25, 'ocasional' => 8]
        // Usado para las tarjetas de segmentación en la vista

        return view('clientes', compact(
            'clientes', 'totalClientes', 'activos', 'inactivos', 'nuevos', 'segmentacion'
        ));
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: facturas()
    // RUTA: GET /facturas
    // QUÉ HACE: Prepara los datos de la sección de facturas:
    //           lista completa, contadores, totales monetarios y gráficas.
    // ─────────────────────────────────────────────────────────
    public function facturas()
    {
        $facturas = Factura::with('cliente')->latest()->get();
        // Todas las facturas con los datos de su cliente, ordenadas de más nueva a más antigua

        $pagadas    = Factura::where('estado', 'pagada')->count();    // Cuántas facturas están pagadas
        $pendientes = Factura::where('estado', 'pendiente')->count(); // Cuántas están pendientes de pago
        $vencidas   = Factura::where('estado', 'vencida')->count();   // Cuántas han vencido sin pagarse

        $totalEmitidas  = Factura::count(); // Total de facturas emitidas (sin importar estado)

        $totalPagado    = Factura::where('estado', 'pagada')->sum('monto');    // Suma de montos de facturas pagadas
        $totalPendiente = Factura::where('estado', 'pendiente')->sum('monto'); // Suma de montos pendientes de cobro
        $totalMora      = Factura::where('estado', 'vencida')->sum('monto');   // Suma de montos en mora (vencidas)

        $estadoFacturas = Factura::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')        // Agrupar: pagada, pendiente, vencida
            ->pluck('total', 'estado'); // Array para la gráfica de Chart.js

        $clientes = Cliente::orderBy('nombre')->get();
        // Lista de clientes para el select del formulario "Nueva factura"

        return view('facturas', compact(
            'facturas', 'pagadas', 'pendientes', 'vencidas',
            'totalEmitidas', 'totalPagado', 'totalPendiente', 'totalMora',
            'estadoFacturas', 'clientes'
        ));
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: mensajes()
    // RUTA: GET /mensajes y GET /mensajes/{id}
    // QUÉ HACE: Prepara la bandeja de mensajes tipo chat.
    //           Si se pasa un {id}, abre esa conversación.
    // ─────────────────────────────────────────────────────────
    public function mensajes($id = null) // $id = null → es opcional; si no se pasa, vale null
    {
        // Obtener solo los clientes que tienen al menos un mensaje (whereHas filtra por relación existente)
        // with(['mensajes' => fn]) carga solo el último mensaje de cada conversación (para la preview)
        $conversaciones = Cliente::whereHas('mensajes')->with(['mensajes' => function ($q) {
            $q->latest()->limit(1); // De los mensajes de cada cliente, traer solo el más reciente
        }])->get();

        $recibidos  = Mensaje::where('tipo', 'recibido')->count(); // Total de mensajes recibidos de clientes
        $enviados   = Mensaje::where('tipo', 'enviado')->count();  // Total de mensajes enviados al cliente
        $archivados = 0; // Placeholder estático (funcionalidad futura)

        // Si se pasó un $id en la URL, buscar ese cliente; si no, usar el primero de la lista
        $clienteActivo = $id
            ? Cliente::find($id)       // Buscar cliente por ID (devuelve null si no existe)
            : $conversaciones->first(); // El primer cliente de la lista de conversaciones

        if ($clienteActivo) {
            // Marcar como leídos todos los mensajes recibidos no leídos de este cliente
            // Esto evita que el contador de "sin leer" siga incrementando al abrir la conversación
            Mensaje::where('cliente_id', $clienteActivo->id)
                ->where('tipo', 'recibido')  // Solo los mensajes del cliente hacia el sistema
                ->where('leido', false)       // Solo los que aún no han sido leídos
                ->update(['leido' => true]);  // Marcarlos todos como leídos de una sola vez
        }

        $sinLeer = Mensaje::where('leido', false)->where('tipo', 'recibido')->count();
        // Contar el total de mensajes sin leer de todos los clientes (para el badge del menú)

        // Obtener todos los mensajes de la conversación activa, ordenados cronológicamente
        $mensajesActivos = $clienteActivo
            ? Mensaje::where('cliente_id', $clienteActivo->id)->orderBy('created_at')->get()
            : collect(); // collect() crea una colección vacía si no hay cliente activo

        $todosClientes = Cliente::orderBy('nombre')->get();
        // Lista de todos los clientes para el modal "Nuevo mensaje"

        return view('mensajes', compact(
            'conversaciones', 'sinLeer', 'recibidos', 'enviados',
            'archivados', 'clienteActivo', 'mensajesActivos', 'todosClientes'
        ));
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: estadisticas()
    // RUTA: GET /estadisticas
    // QUÉ HACE: Calcula datos para las gráficas: ventas de los
    //           últimos 6 meses, productos más vendidos, segmentación
    //           de clientes y estados de ventas.
    // ─────────────────────────────────────────────────────────
    public function estadisticas()
    {
        $totalUsuarios = User::count();       // Total de usuarios registrados
        $totalVentas   = Venta::sum('total'); // Monto total acumulado de ventas
        $sinLeer       = Mensaje::where('leido', false)->where('tipo', 'recibido')->count();
        // Mensajes sin leer (para el badge del menú lateral)

        // ── CÁLCULO DEL CRECIMIENTO MENSUAL ────────────────────
        $mesActual = now();                  // Fecha y hora actual (objeto Carbon)
        $mesPasado = now()->subMonth();      // Fecha de exactamente un mes atrás

        $ventasMesActual = Venta::whereYear('created_at', $mesActual->year)   // Filtrar por año actual
            ->whereMonth('created_at', $mesActual->month)->sum('total');       // Y mes actual → suma total
        $ventasMesPasado = Venta::whereYear('created_at', $mesPasado->year)   // Filtrar por año del mes pasado
            ->whereMonth('created_at', $mesPasado->month)->sum('total');       // Y mes pasado → suma total

        // Calcular el porcentaje de crecimiento con la fórmula: ((nuevo - viejo) / viejo) * 100
        $crecimiento = $ventasMesPasado > 0 // Si hubo ventas el mes pasado (evita dividir entre 0)
            ? (($ventasMesActual - $ventasMesPasado) >= 0 ? '+' : '') // Agregar '+' si es crecimiento positivo
              . round((($ventasMesActual - $ventasMesPasado) / $ventasMesPasado) * 100) . '%' // Calcular porcentaje
            : ($ventasMesActual > 0 ? '+100%' : '0%'); // Si no hubo ventas el mes pasado: +100% o 0%

        // ── VENTAS DE LOS ÚLTIMOS 6 MESES (para la gráfica de línea) ──
        $ventasMensuales = []; // Array con los montos de cada mes
        $labelsMeses     = []; // Array con los nombres de los meses (para el eje X de la gráfica)
        $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic']; // Nombres cortos de meses

        for ($i = 5; $i >= 0; $i--) { // Iterar desde 5 meses atrás hasta el mes actual (6 iteraciones)
            $fecha = now()->subMonths($i);           // Fecha del mes correspondiente
            $labelsMeses[]     = $meses[$fecha->month - 1]; // $fecha->month es 1-12, el array es 0-11
            $ventasMensuales[] = (float) Venta::whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->sum('total'); // Suma de ventas de ese mes específico, convertido a float
        }

        // ── TOP 5 PRODUCTOS MÁS VENDIDOS ───────────────────────
        $productos = Venta::selectRaw('producto, COUNT(*) as vendidas, SUM(total) as ingresos')
            // Agrupar por nombre de producto, contar cuántas veces se vendió y sumar los ingresos
            ->groupBy('producto')         // Agrupar: un registro por producto diferente
            ->orderByDesc('vendidas')     // Ordenar de más vendido a menos
            ->limit(5)                    // Solo los top 5
            ->get();                      // Ejecutar la consulta y obtener los resultados

        // ── SEGMENTACIÓN DE CLIENTES (para la gráfica de donut) ──
        $segmentacion = Cliente::selectRaw('segmento, COUNT(*) as total')
            ->groupBy('segmento')
            ->pluck('total', 'segmento'); // Array: ['premium' => 10, 'regular' => 25, 'ocasional' => 8]

        // ── ESTADOS DE VENTAS POR MONTO ────────────────────────
        $estadosVentas = Venta::selectRaw('estado, SUM(total) as total_monto')
            ->groupBy('estado')
            ->pluck('total_monto', 'estado'); // Monto total por estado: ['completado' => 5000, 'pendiente' => 300, ...]

        return view('estadisticas', compact(
            'totalUsuarios', 'totalVentas', 'crecimiento', 'sinLeer',
            'ventasMensuales', 'labelsMeses', 'productos', 'segmentacion', 'estadosVentas'
        ));
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: analisis()
    // RUTA: GET /analisis
    // QUÉ HACE: Calcula datos de actividad: ventas de la última
    //           semana, tasa de conversión y actividad por módulo.
    // ─────────────────────────────────────────────────────────
    public function analisis()
    {
        $totalTransacciones = Venta::count() + Mensaje::count();
        // Total de transacciones del sistema (ventas + mensajes combinados)

        $totalVentas       = Venta::count();   // Número total de ventas registradas
        $ventasCompletadas = Venta::where('estado', 'completado')->count(); // Solo las completadas

        // Tasa de conversión: porcentaje de ventas que se completaron exitosamente
        $tasa_conversion = $totalVentas > 0
            ? number_format(($ventasCompletadas / $totalVentas) * 100, 1) // number_format(..., 1) = 1 decimal
            : 0; // Si no hay ventas, tasa = 0%

        $clientesNuevosMes = Cliente::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)->count();
        // Clientes nuevos registrados en el mes actual

        $mensajesSinLeer = Mensaje::where('leido', false)->count();
        // Total de mensajes no leídos (de cualquier tipo)

        // ── VENTAS DE LOS ÚLTIMOS 7 DÍAS (gráfica de barras) ──
        $ventasSemana = []; // Cantidad de ventas por día
        $labelsDias   = []; // Nombre del día de la semana
        $diasCortos   = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb']; // dayOfWeek: 0=Dom ... 6=Sáb

        for ($i = 6; $i >= 0; $i--) {  // Iterar desde 6 días atrás hasta hoy
            $fecha        = now()->subDays($i);                    // Fecha del día correspondiente
            $labelsDias[] = $diasCortos[$fecha->dayOfWeek];        // Nombre del día (Dom, Lun, etc.)
            $ventasSemana[] = Venta::whereDate('created_at', $fecha->format('Y-m-d'))->count();
            // Contar cuántas ventas hubo en ese día exacto
            // whereDate() compara solo la fecha (sin la hora)
        }

        // ── SEGMENTACIÓN DE CLIENTES ───────────────────────────
        $segmentacion = Cliente::selectRaw('segmento, COUNT(*) as total')
            ->groupBy('segmento')
            ->pluck('total', 'segmento'); // Para la gráfica de segmentos

        // ── ACTIVIDAD POR MÓDULO ───────────────────────────────
        // Array manual con estadísticas de cada módulo del sistema
        $actividadModulos = [
            ['modulo' => 'Ventas',   'total' => Venta::count(),   'completados' => Venta::where('estado', 'completado')->count(),  'icono' => 'fas fa-shopping-cart text-success'],
            ['modulo' => 'Facturas', 'total' => Factura::count(), 'completados' => Factura::where('estado', 'pagada')->count(),    'icono' => 'fas fa-file-invoice-dollar text-warning'],
            ['modulo' => 'Mensajes', 'total' => Mensaje::count(), 'completados' => Mensaje::where('leido', true)->count(),         'icono' => 'fas fa-envelope text-info'],
            ['modulo' => 'Clientes', 'total' => Cliente::count(), 'completados' => Cliente::where('estado', 'activo')->count(),   'icono' => 'fas fa-users text-primary'],
            // Cada elemento tiene: nombre, total de registros, cuántos están "completados/activos", icono Font Awesome
        ];

        return view('analisis', compact(
            'totalTransacciones', 'tasa_conversion', 'clientesNuevosMes', 'mensajesSinLeer',
            'ventasSemana', 'labelsDias', 'segmentacion', 'actividadModulos'
        ));
    }
}
