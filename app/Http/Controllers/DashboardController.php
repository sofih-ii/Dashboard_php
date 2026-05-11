<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\Factura;
use App\Models\Mensaje;
use App\Models\User;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $totalUsuarios = User::count();
        $totalVentas   = Venta::sum('total');
        $totalAlertas  = Venta::where('estado', 'pendiente')->count()
                       + Factura::where('estado', 'vencida')->count();
        $crecimiento   = '+15%';

        $ultimoUsuario  = User::latest()->first();
        $ultimaVenta    = Venta::with('cliente')->where('estado', 'completado')->latest()->first();
        $ultimaFactura  = Factura::with('cliente')->latest()->first();
        $ultimoMensaje  = Mensaje::with('cliente')->where('tipo', 'recibido')->latest()->first();
        $datosRecientes = Cliente::latest()->take(5)->get();

        return view('welcome', compact(
            'totalUsuarios', 'totalVentas', 'totalAlertas', 'crecimiento',
            'ultimoUsuario', 'ultimaVenta', 'ultimaFactura', 'ultimoMensaje',
            'datosRecientes'
        ));
    }

    public function ventas()
    {
        $ventas        = Venta::with('cliente')->latest()->get();
        $totalVentas   = Venta::sum('total');
        $totalPedidos  = Venta::count();
        $pendientes    = Venta::where('estado', 'pendiente')->count();
        $devoluciones  = Venta::where('estado', 'devuelto')->count();
        $estadoPedidos = Venta::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');
        $clientes      = Cliente::orderBy('nombre')->get();

        return view('ventas', compact(
            'ventas', 'totalVentas', 'totalPedidos', 'pendientes',
            'devoluciones', 'estadoPedidos', 'clientes'
        ));
    }

    public function clientes()
    {
        $clientes      = Cliente::latest()->get();
        $totalClientes = Cliente::count();
        $activos       = Cliente::where('estado', 'activo')->count();
        $inactivos     = Cliente::where('estado', 'inactivo')->count();
        $nuevos        = Cliente::whereMonth('created_at', now()->month)->count();
        $segmentacion  = Cliente::selectRaw('segmento, COUNT(*) as total')
            ->groupBy('segmento')
            ->pluck('total', 'segmento');

        return view('clientes', compact(
            'clientes', 'totalClientes', 'activos', 'inactivos', 'nuevos', 'segmentacion'
        ));
    }

    public function facturas()
    {
        $facturas       = Factura::with('cliente')->latest()->get();
        $pagadas        = Factura::where('estado', 'pagada')->count();
        $pendientes     = Factura::where('estado', 'pendiente')->count();
        $vencidas       = Factura::where('estado', 'vencida')->count();
        $totalEmitidas  = Factura::count();
        $totalPagado    = Factura::where('estado', 'pagada')->sum('monto');
        $totalPendiente = Factura::where('estado', 'pendiente')->sum('monto');
        $totalMora      = Factura::where('estado', 'vencida')->sum('monto');
        $estadoFacturas = Factura::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');
        $clientes       = Cliente::orderBy('nombre')->get();

        return view('facturas', compact(
            'facturas', 'pagadas', 'pendientes', 'vencidas',
            'totalEmitidas', 'totalPagado', 'totalPendiente', 'totalMora',
            'estadoFacturas', 'clientes'
        ));
    }

    public function mensajes($id = null)
    {
        $conversaciones = Cliente::whereHas('mensajes')->with(['mensajes' => function ($q) {
            $q->latest()->limit(1);
        }])->get();

        $recibidos  = Mensaje::where('tipo', 'recibido')->count();
        $enviados   = Mensaje::where('tipo', 'enviado')->count();
        $archivados = 0;

        $clienteActivo = $id
            ? Cliente::find($id)
            : $conversaciones->first();

        if ($clienteActivo) {
            Mensaje::where('cliente_id', $clienteActivo->id)
                ->where('tipo', 'recibido')
                ->where('leido', false)
                ->update(['leido' => true]);
        }

        $sinLeer = Mensaje::where('leido', false)->where('tipo', 'recibido')->count();

        $mensajesActivos = $clienteActivo
            ? Mensaje::where('cliente_id', $clienteActivo->id)->orderBy('created_at')->get()
            : collect();

        $todosClientes = Cliente::orderBy('nombre')->get();

        return view('mensajes', compact(
            'conversaciones', 'sinLeer', 'recibidos', 'enviados',
            'archivados', 'clienteActivo', 'mensajesActivos', 'todosClientes'
        ));
    }

    public function estadisticas()
    {
        $totalUsuarios = User::count();
        $totalVentas   = Venta::sum('total');
        $sinLeer       = Mensaje::where('leido', false)->where('tipo', 'recibido')->count();

        $mesActual = now();
        $mesPasado = now()->subMonth();
        $ventasMesActual = Venta::whereYear('created_at', $mesActual->year)
            ->whereMonth('created_at', $mesActual->month)->sum('total');
        $ventasMesPasado = Venta::whereYear('created_at', $mesPasado->year)
            ->whereMonth('created_at', $mesPasado->month)->sum('total');
        $crecimiento = $ventasMesPasado > 0
            ? (($ventasMesActual - $ventasMesPasado) >= 0 ? '+' : '')
              . round((($ventasMesActual - $ventasMesPasado) / $ventasMesPasado) * 100) . '%'
            : ($ventasMesActual > 0 ? '+100%' : '0%');

        $ventasMensuales = [];
        $labelsMeses     = [];
        $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        for ($i = 5; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $labelsMeses[]     = $meses[$fecha->month - 1];
            $ventasMensuales[] = (float) Venta::whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->sum('total');
        }

        $productos = Venta::selectRaw('producto, COUNT(*) as vendidas, SUM(total) as ingresos')
            ->groupBy('producto')
            ->orderByDesc('vendidas')
            ->limit(5)
            ->get();

        $segmentacion = Cliente::selectRaw('segmento, COUNT(*) as total')
            ->groupBy('segmento')
            ->pluck('total', 'segmento');

        $estadosVentas = Venta::selectRaw('estado, SUM(total) as total_monto')
            ->groupBy('estado')
            ->pluck('total_monto', 'estado');

        return view('estadisticas', compact(
            'totalUsuarios', 'totalVentas', 'crecimiento', 'sinLeer',
            'ventasMensuales', 'labelsMeses', 'productos', 'segmentacion', 'estadosVentas'
        ));
    }

    public function analisis()
    {
        $totalTransacciones = Venta::count() + Mensaje::count();
        $totalVentas        = Venta::count();
        $ventasCompletadas  = Venta::where('estado', 'completado')->count();
        $tasa_conversion    = $totalVentas > 0
            ? number_format(($ventasCompletadas / $totalVentas) * 100, 1)
            : 0;
        $clientesNuevosMes  = Cliente::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)->count();
        $mensajesSinLeer    = Mensaje::where('leido', false)->count();

        $ventasSemana = [];
        $labelsDias   = [];
        $diasCortos   = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        for ($i = 6; $i >= 0; $i--) {
            $fecha          = now()->subDays($i);
            $labelsDias[]   = $diasCortos[$fecha->dayOfWeek];
            $ventasSemana[] = Venta::whereDate('created_at', $fecha->format('Y-m-d'))->count();
        }

        $segmentacion = Cliente::selectRaw('segmento, COUNT(*) as total')
            ->groupBy('segmento')
            ->pluck('total', 'segmento');

        $actividadModulos = [
            ['modulo' => 'Ventas',   'total' => Venta::count(),   'completados' => Venta::where('estado', 'completado')->count(),  'icono' => 'fas fa-shopping-cart text-success'],
            ['modulo' => 'Facturas', 'total' => Factura::count(), 'completados' => Factura::where('estado', 'pagada')->count(),    'icono' => 'fas fa-file-invoice-dollar text-warning'],
            ['modulo' => 'Mensajes', 'total' => Mensaje::count(), 'completados' => Mensaje::where('leido', true)->count(),         'icono' => 'fas fa-envelope text-info'],
            ['modulo' => 'Clientes', 'total' => Cliente::count(), 'completados' => Cliente::where('estado', 'activo')->count(),   'icono' => 'fas fa-users text-primary'],
        ];

        return view('analisis', compact(
            'totalTransacciones', 'tasa_conversion', 'clientesNuevosMes', 'mensajesSinLeer',
            'ventasSemana', 'labelsDias', 'segmentacion', 'actividadModulos'
        ));
    }
}
