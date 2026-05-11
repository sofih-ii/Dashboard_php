@extends('layouts.app')

@section('title', 'Estadísticas - Dashboard')
@section('nav_estadisticas', 'active')
@section('side_estadisticas', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-chart-bar text-primary"></i> Estadísticas</h1>
    <div class="btn-group me-2">
        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <a href="{{ route('ventas.export') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-download"></i> Exportar Ventas
        </a>
    </div>
</div>

{{-- KPIs con datos reales --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-users"></i> Usuarios Totales</h5>
                <p class="card-text display-6">{{ $totalUsuarios }}</p>
                <small class="text-white-50">Registrados en el sistema</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-shopping-cart"></i> Ventas Totales</h5>
                <p class="card-text display-6">${{ number_format($totalVentas, 0, ',', '.') }}</p>
                <small class="text-white-50">Ingresos acumulados</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-chart-line"></i> Crecimiento</h5>
                <p class="card-text display-6">{{ $crecimiento }}</p>
                <small class="text-white-50">vs mes anterior</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-envelope-open"></i> Sin Leer</h5>
                <p class="card-text display-6">{{ $sinLeer }}</p>
                <small class="text-white-50">Mensajes pendientes</small>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Ventas Mensuales (Últimos 6 meses)</h5></div>
            <div class="card-body"><canvas id="ventasChart" height="200"></canvas></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Segmentación de Clientes</h5></div>
            <div class="card-body"><canvas id="segmentacionChart" height="200"></canvas></div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Productos Más Vendidos</h5></div>
            <div class="card-body">
                @if($productos->isEmpty())
                    <p class="text-muted text-center py-4">No hay datos de ventas aún.</p>
                @else
                    <canvas id="productosChart" height="200"></canvas>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Distribución por Estado de Ventas</h5></div>
            <div class="card-body">
                @if($estadosVentas->isEmpty())
                    <p class="text-muted text-center py-4">No hay datos de ventas aún.</p>
                @else
                    <canvas id="estadosChart" height="200"></canvas>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h5>Productos Más Vendidos</h5></div>
            <div class="card-body">
                @if($productos->isEmpty())
                    <p class="text-muted text-center py-4">
                        <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                        No hay ventas registradas aún.
                    </p>
                @else
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th>Unidades Vendidas</th>
                                <th>Ingresos</th>
                                <th>Rendimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $i => $p)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><i class="fas fa-cube text-primary me-1"></i> {{ $p->producto }}</td>
                                <td>{{ $p->vendidas }}</td>
                                <td>${{ number_format($p->ingresos, 2) }}</td>
                                <td>
                                    @php
                                        $max = $productos->max('vendidas');
                                        $pct = $max > 0 ? round(($p->vendidas / $max) * 100) : 0;
                                        $cls = $pct >= 70 ? 'success' : ($pct >= 40 ? 'warning' : 'danger');
                                    @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-fill" style="height:8px;">
                                            <div class="progress-bar bg-{{ $cls }}" style="width:{{ $pct }}%"></div>
                                        </div>
                                        <span class="badge bg-{{ $cls }} small">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Ventas mensuales
new Chart(document.getElementById('ventasChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: @json($labelsMeses),
        datasets: [{
            label: 'Ventas ($)',
            data: @json($ventasMensuales),
            borderColor: 'rgb(75,192,192)',
            backgroundColor: 'rgba(75,192,192,0.1)',
            tension: 0.3,
            fill: true,
            pointBackgroundColor: 'rgb(75,192,192)',
            pointRadius: 5
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
});

// Segmentación
@if($segmentacion->isNotEmpty())
new Chart(document.getElementById('segmentacionChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: @json($segmentacion->keys()->map(fn($k) => ucfirst($k))),
        datasets: [{
            data: @json($segmentacion->values()),
            backgroundColor: [
                'rgba(255,193,7,0.8)',
                'rgba(40,167,69,0.8)',
                'rgba(23,162,184,0.8)',
                'rgba(220,53,69,0.8)'
            ]
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
@else
document.getElementById('segmentacionChart').parentElement.innerHTML =
    '<p class="text-muted text-center py-4">No hay clientes registrados aún.</p>';
@endif

// Productos más vendidos
@if($productos->isNotEmpty())
new Chart(document.getElementById('productosChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($productos->pluck('producto')),
        datasets: [{
            label: 'Unidades Vendidas',
            data: @json($productos->pluck('vendidas')),
            backgroundColor: [
                'rgba(75,192,192,0.8)','rgba(54,162,235,0.8)',
                'rgba(255,206,86,0.8)','rgba(75,192,192,0.8)','rgba(255,99,132,0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        indexAxis: 'y',
        plugins: { legend: { display: false } }
    }
});
@endif

// Estados de ventas
@if($estadosVentas->isNotEmpty())
new Chart(document.getElementById('estadosChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: @json($estadosVentas->keys()->map(fn($k) => ucfirst(str_replace('_',' ',$k)))),
        datasets: [{
            data: @json($estadosVentas->values()),
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
@endif
</script>
@endsection
