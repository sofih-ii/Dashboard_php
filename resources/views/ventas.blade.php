@extends('layouts.app')

@section('title', 'Ventas - Dashboard')
@section('side_ventas', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-shopping-cart text-success"></i> Ventas</h1>
    <div class="btn-group me-2">
        <button class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Nueva Venta</button>
        <button class="btn btn-sm btn-outline-secondary">Exportar</button>
    </div>
</div>

{{-- KPIs con datos reales --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-dollar-sign"></i> Total Ventas</h5>
                <p class="card-text display-6">${{ number_format($totalVentas, 0, ',', '.') }}</p>
                <small class="text-white-50">Acumulado</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-receipt"></i> Pedidos</h5>
                <p class="card-text display-6">{{ $totalPedidos }}</p>
                <small class="text-white-50">Total registrados</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-hourglass-half"></i> Pendientes</h5>
                <p class="card-text display-6">{{ $pendientes }}</p>
                <small class="text-white-50">Por procesar</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-undo-alt"></i> Devoluciones</h5>
                <p class="card-text display-6">{{ $devoluciones }}</p>
                <small class="text-white-50">Este período</small>
            </div>
        </div>
    </div>
</div>

{{-- GRÁFICAS: barra + pie --}}
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Ventas por Mes</h5></div>
            <div class="card-body"><canvas id="ventasMesChart" height="180"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Estado de Pedidos</h5></div>
            <div class="card-body"><canvas id="estadoPedidosChart" height="180"></canvas></div>
        </div>
    </div>
</div>

{{-- Tabla con datos reales --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Últimas Ventas</h5>
                <input type="text" class="form-control form-control-sm w-25" placeholder="Buscar..." id="buscarVenta">
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover" id="tablaVentas">
                    <thead>
                        <tr>
                            <th>#Orden</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                        <tr>
                            <td>{{ $venta->numero_orden }}</td>
                            <td>{{ $venta->cliente->nombre ?? '—' }} {{ $venta->cliente->apellido ?? '' }}</td>
                            <td>{{ $venta->producto }}</td>
                            <td>${{ number_format($venta->total, 2) }}</td>
                            <td>
                                @php
                                    $badge = match($venta->estado) {
                                        'completado' => 'success',
                                        'pendiente'  => 'warning',
                                        'en_camino'  => 'info',
                                        'devuelto'   => 'danger',
                                        default      => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucfirst(str_replace('_', ' ', $venta->estado)) }}</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary">Ver</button>
                                @if($venta->estado !== 'devuelto')
                                    <button class="btn btn-sm btn-danger">Cancelar</button>
                                @else
                                    <button class="btn btn-sm btn-secondary" disabled>Cancelar</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted">No hay ventas registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Gráfica de barras - Ventas por Mes (estática decorativa)
    new Chart(document.getElementById('ventasMesChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [{
                label: 'Ventas ($)',
                data: [8000, 12000, 9500, 14000, 16000, 18500],
                backgroundColor: 'rgba(40,167,69,0.7)',
                borderColor: 'rgb(40,167,69)',
                borderWidth: 1
            }]
        },
        options: { responsive: true }
    });

    // Gráfica de pie - Estado de pedidos desde BD
    new Chart(document.getElementById('estadoPedidosChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: @json($estadoPedidos->keys()->map(fn($k) => ucfirst(str_replace('_', ' ', $k)))),
            datasets: [{
                data: @json($estadoPedidos->values()),
                backgroundColor: [
                    'rgba(40,167,69,0.8)',
                    'rgba(255,193,7,0.8)',
                    'rgba(23,162,184,0.8)',
                    'rgba(220,53,69,0.8)'
                ]
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Búsqueda en tabla
    document.getElementById('buscarVenta').addEventListener('keyup', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#tablaVentas tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endsection