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
    // ── DASHBOARD PRINCIPAL ──────────────────────────────────────
    public function dashboard()
    {
        // KPIs
        $totalUsuarios = User::count();
        $totalVentas   = Venta::sum('total');
        $totalAlertas  = Venta::where('estado', 'pendiente')->count()
                       + Factura::where('estado', 'vencida')->count();
        $crecimiento   = '+15%'; // estático decorativo

        // Actividad reciente — últimos registros de cada tipo
        $ultimoUsuario  = User::latest()->first();
        $ultimaVenta    = Venta::with('cliente')->where('estado', 'completado')->latest()->first();
        $ultimaFactura  = Factura::with('cliente')->latest()->first();
        $ultimoMensaje  = Mensaje::with('cliente')->where('tipo', 'recibido')->latest()->first();

        // Datos recientes — últimos 5 clientes
        $datosRecientes = Cliente::latest()->take(5)->get();

        return view('welcome', compact(
            'totalUsuarios', 'totalVentas', 'totalAlertas', 'crecimiento',
            'ultimoUsuario', 'ultimaVenta', 'ultimaFactura', 'ultimoMensaje',
            'datosRecientes'
        ));
    }

    // ── VENTAS ──────────────────────────────────────
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

        return view('ventas', compact(
            'ventas', 'totalVentas', 'totalPedidos', 'pendientes', 'devoluciones', 'estadoPedidos'
        ));
    }

    // ── CLIENTES ──────────────────────────────────────
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

    // ── FACTURAS ──────────────────────────────────────
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

        return view('facturas', compact(
            'facturas', 'pagadas', 'pendientes', 'vencidas',
            'totalEmitidas', 'totalPagado', 'totalPendiente', 'totalMora', 'estadoFacturas'
        ));
    }

    // ── MENSAJES ──────────────────────────────────────
    public function mensajes()
    {
        $conversaciones = Cliente::whereHas('mensajes')->with(['mensajes' => function ($q) {
            $q->latest()->limit(1);
        }])->get();

        $sinLeer    = Mensaje::where('leido', false)->where('tipo', 'recibido')->count();
        $recibidos  = Mensaje::where('tipo', 'recibido')->count();
        $enviados   = Mensaje::where('tipo', 'enviado')->count();
        $archivados = 0;

        $clienteActivo   = $conversaciones->first();
        $mensajesActivos = $clienteActivo
            ? Mensaje::where('cliente_id', $clienteActivo->id)->orderBy('created_at')->get()
            : collect();

        return view('mensajes', compact(
            'conversaciones', 'sinLeer', 'recibidos', 'enviados',
            'archivados', 'clienteActivo', 'mensajesActivos'
        ));
    }
}