<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\Factura;
use App\Models\Mensaje;

class DashboardController extends Controller
{
    public function ventas()
    {
        $ventas       = Venta::with('cliente')->latest()->get();
        $totalVentas  = Venta::sum('total');
        $totalPedidos = Venta::count();
        $pendientes   = Venta::where('estado', 'Pendiente')->count();
        $devoluciones = Venta::where('estado', 'Devuelto')->count();

        $estadoPedidos = Venta::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return view('ventas', compact(
            'ventas', 'totalVentas', 'totalPedidos', 'pendientes', 'devoluciones', 'estadoPedidos'
        ));
    }

    public function clientes()
    {
        $clientes      = Cliente::latest()->get();
        $totalClientes = Cliente::count();
        $activos       = Cliente::where('estado', 'Activo')->count();
        $inactivos     = Cliente::where('estado', 'Inactivo')->count();
        $nuevos        = Cliente::whereMonth('created_at', now()->month)->count();

        $segmentacion = Cliente::selectRaw('segmento, COUNT(*) as total')
            ->groupBy('segmento')
            ->pluck('total', 'segmento');

        return view('clientes', compact(
            'clientes', 'totalClientes', 'activos', 'inactivos', 'nuevos', 'segmentacion'
        ));
    }

    public function facturas()
    {
        $facturas       = Factura::with('cliente')->latest()->get();
        $pagadas        = Factura::where('estado', 'Pagada')->count();
        $pendientes     = Factura::where('estado', 'Pendiente')->count();
        $vencidas       = Factura::where('estado', 'Vencida')->count();
        $totalEmitidas  = Factura::count();
        $totalPagado    = Factura::where('estado', 'Pagada')->sum('monto');
        $totalPendiente = Factura::where('estado', 'Pendiente')->sum('monto');
        $totalMora      = Factura::where('estado', 'Vencida')->sum('monto');

        $estadoFacturas = Factura::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return view('facturas', compact(
            'facturas', 'pagadas', 'pendientes', 'vencidas',
            'totalEmitidas', 'totalPagado', 'totalPendiente', 'totalMora', 'estadoFacturas'
        ));
    }

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