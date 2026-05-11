@extends('layouts.app')

@section('title', 'Análisis - Dashboard')
@section('side_analisis', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-chart-line text-primary"></i> Análisis</h1>
    <div class="btn-group me-2">
        <a href="{{ route('clientes.export') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-download"></i> Exportar Clientes
        </a>
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
                <h5 class="card-title"><i class="fas fa-exchange-alt"></i> Transacciones</h5>
                <p class="card-text display-6">{{ $totalTransacciones }}</p>
                <small class="text-white-50">Ventas + Mensajes totales</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-check-double"></i> Tasa de Éxito</h5>
                <p class="card-text display-6">{{ $tasa_conversion }}%</p>
                <small class="text-white-50">Ventas completadas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user-plus"></i> Nuevos Clientes</h5>
                <p class="card-text display-6">{{ $clientesNuevosMes }}</p>
                <small class="text-white-50">Este mes</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-envelope-open"></i> Sin Leer</h5>
                <p class="card-text display-6">{{ $mensajesSinLeer }}</p>
                <small class="text-white-50">Mensajes pendientes</small>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Ventas por Día (Última Semana)</h5></div>
            <div class="card-body"><canvas id="traficoChart" height="180"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Segmentos de Clientes</h5></div>
            <div class="card-body">
                @if($segmentacion->isNotEmpty())
                    <canvas id="segmentosChart" height="180"></canvas>
                @else
                    <p class="text-muted text-center py-5">No hay clientes registrados.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h5><i class="fas fa-tachometer-alt me-1"></i> Actividad por Módulo</h5></div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Módulo</th>
                            <th>Total Registros</th>
                            <th>Completados / Activos</th>
                            <th>Progreso</th>
                            <th>Tasa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($actividadModulos as $mod)
                        @php
                            $pct = $mod['total'] > 0 ? round(($mod['completados'] / $mod['total']) * 100) : 0;
                            $cls = $pct >= 70 ? 'success' : ($pct >= 40 ? 'warning' : 'danger');
                        @endphp
                        <tr>
                            <td>
                                <i class="{{ $mod['icono'] }} me-2"></i>
                                <strong>{{ $mod['modulo'] }}</strong>
                            </td>
                            <td>{{ $mod['total'] }}</td>
                            <td>{{ $mod['completados'] }}</td>
                            <td>
                                <div class="progress" style="height:10px;min-width:100px;">
                                    <div class="progress-bar bg-{{ $cls }}" style="width:{{ $pct }}%"></div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $cls }}">{{ $pct }}%</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Ventas por día (última semana)
new Chart(document.getElementById('traficoChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($labelsDias),
        datasets: [{
            label: 'Ventas del día',
            data: @json($ventasSemana),
            backgroundColor: 'rgba(54,162,235,0.7)',
            borderColor: 'rgb(54,162,235)',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Segmentos de clientes
@if($segmentacion->isNotEmpty())
new Chart(document.getElementById('segmentosChart').getContext('2d'), {
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
@endif
</script>
@endsection
