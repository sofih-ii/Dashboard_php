@extends('layouts.app')

@section('title', 'Facturas - Dashboard')
@section('side_facturas', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-file-invoice-dollar text-warning"></i> Facturas</h1>
    <div class="btn-group me-2">
        <button class="btn btn-sm btn-warning"><i class="fas fa-plus"></i> Nueva Factura</button>
        <button class="btn btn-sm btn-outline-secondary">Exportar PDF</button>
    </div>
</div>

{{-- KPIs con datos reales --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-check-circle"></i> Pagadas</h5>
                <p class="card-text display-6">{{ $pagadas }}</p>
                <small class="text-white-50">${{ number_format($totalPagado, 0, ',', '.') }} total</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-clock"></i> Pendientes</h5>
                <p class="card-text display-6">{{ $pendientes }}</p>
                <small class="text-white-50">${{ number_format($totalPendiente, 0, ',', '.') }} pendiente</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-times-circle"></i> Vencidas</h5>
                <p class="card-text display-6">{{ $vencidas }}</p>
                <small class="text-white-50">${{ number_format($totalMora, 0, ',', '.') }} en mora</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-file-alt"></i> Total Emitidas</h5>
                <p class="card-text display-6">{{ $totalEmitidas }}</p>
                <small class="text-white-50">Registradas</small>
            </div>
        </div>
    </div>
</div>

{{-- GRÁFICAS: barra + doughnut --}}
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Ingresos por Facturación Mensual</h5></div>
            <div class="card-body"><canvas id="facturacionChart" height="180"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Estado de Facturas</h5></div>
            <div class="card-body"><canvas id="estadoFacturasChart" height="180"></canvas></div>
        </div>
    </div>
</div>

{{-- Tabla con datos reales --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Listado de Facturas</h5>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Buscar factura..." id="buscarFactura">
                    <select class="form-select form-select-sm w-auto" id="filtroEstadoFactura">
                        <option value="">Todos</option>
                        <option value="pagada">Pagadas</option>
                        <option value="pendiente">Pendientes</option>
                        <option value="vencida">Vencidas</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover" id="tablaFacturas">
                    <thead>
                        <tr>
                            <th>N° Factura</th>
                            <th>Cliente</th>
                            <th>Concepto</th>
                            <th>Monto</th>
                            <th>Emisión</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facturas as $factura)
                        <tr>
                            <td><strong>{{ $factura->numero_factura }}</strong></td>
                            <td>{{ $factura->cliente->nombre ?? '—' }} {{ $factura->cliente->apellido ?? '' }}</td>
                            <td>{{ $factura->concepto }}</td>
                            <td>${{ number_format($factura->monto, 2) }}</td>
                            <td>{{ $factura->fecha_emision->format('Y-m-d') }}</td>
                            <td>{{ $factura->fecha_vencimiento->format('Y-m-d') }}</td>
                            <td>
                                @php
                                    $badge = match($factura->estado) {
                                        'pagada'    => 'success',
                                        'pendiente' => 'warning',
                                        'vencida'   => 'danger',
                                        default     => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucfirst($factura->estado) }}</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-secondary"><i class="fas fa-download"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted">No hay facturas registradas.</td></tr>
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
    // Gráfica de barras - Facturación mensual (estática decorativa)
    new Chart(document.getElementById('facturacionChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [
                {
                    label: 'Pagadas ($)',
                    data: [6000, 8500, 7200, 9800, 11000, 12500],
                    backgroundColor: 'rgba(40,167,69,0.7)'
                },
                {
                    label: 'Pendientes ($)',
                    data: [1200, 900, 1500, 800, 2000, 1800],
                    backgroundColor: 'rgba(255,193,7,0.7)'
                }
            ]
        },
        options: { responsive: true, plugins: { legend: { position: 'top' } } }
    });

    // Gráfica doughnut - Estado de facturas desde BD
    new Chart(document.getElementById('estadoFacturasChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: @json($estadoFacturas->keys()->map(fn($k) => ucfirst($k))),
            datasets: [{
                data: @json($estadoFacturas->values()),
                backgroundColor: [
                    'rgba(40,167,69,0.8)',
                    'rgba(255,193,7,0.8)',
                    'rgba(220,53,69,0.8)'
                ]
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Búsqueda y filtro
    function filtrarFacturas() {
        const q      = document.getElementById('buscarFactura').value.toLowerCase();
        const estado = document.getElementById('filtroEstadoFactura').value.toLowerCase();
        document.querySelectorAll('#tablaFacturas tbody tr').forEach(row => {
            const texto = row.innerText.toLowerCase();
            row.style.display = (texto.includes(q) && (estado === '' || texto.includes(estado))) ? '' : 'none';
        });
    }
    document.getElementById('buscarFactura').addEventListener('keyup', filtrarFacturas);
    document.getElementById('filtroEstadoFactura').addEventListener('change', filtrarFacturas);
</script>
@endsection