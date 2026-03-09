@extends('layouts.app')

@section('title', 'Clientes - Dashboard')
@section('nav_clientes', 'active')
@section('side_clientes', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-users text-primary"></i> Clientes</h1>
    <div class="btn-group me-2">
        <button class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Nuevo Cliente</button>
        <button class="btn btn-sm btn-outline-secondary">Exportar</button>
    </div>
</div>

{{-- KPIs con datos reales --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-users"></i> Total Clientes</h5>
                <p class="card-text display-6">{{ $totalClientes }}</p>
                <small class="text-white-50">Registrados</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user-check"></i> Activos</h5>
                <p class="card-text display-6">{{ $activos }}</p>
                <small class="text-white-50">{{ $totalClientes > 0 ? round($activos/$totalClientes*100) : 0 }}% del total</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user-plus"></i> Nuevos</h5>
                <p class="card-text display-6">{{ $nuevos }}</p>
                <small class="text-white-50">Este mes</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user-times"></i> Inactivos</h5>
                <p class="card-text display-6">{{ $inactivos }}</p>
                <small class="text-white-50">{{ $totalClientes > 0 ? round($inactivos/$totalClientes*100) : 0 }}% del total</small>
            </div>
        </div>
    </div>
</div>

{{-- GRÁFICAS: barra + doughnut --}}
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Nuevos Clientes por Mes</h5></div>
            <div class="card-body"><canvas id="clientesMesChart" height="200"></canvas></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Segmentación de Clientes</h5></div>
            <div class="card-body"><canvas id="segmentacionChart" height="200"></canvas></div>
        </div>
    </div>
</div>

{{-- Tabla con datos reales --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Clientes</h5>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Buscar cliente..." id="buscarCliente">
                    <select class="form-select form-select-sm w-auto" id="filtroEstado">
                        <option value="">Todos</option>
                        <option value="activo">Activos</option>
                        <option value="inactivo">Inactivos</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover" id="tablaClientes">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Segmento</th>
                            <th>Compras</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                        <tr>
                            <td>#{{ str_pad($cliente->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td><i class="fas fa-user-circle text-primary"></i> {{ $cliente->nombre }} {{ $cliente->apellido }}</td>
                            <td>{{ $cliente->email }}</td>
                            <td>{{ $cliente->telefono ?? '—' }}</td>
                            <td>
                                @php
                                    $segBadge = match($cliente->segmento) {
                                        'premium'   => 'warning',
                                        'regular'   => 'success',
                                        'ocasional' => 'info',
                                        default     => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $segBadge }}">{{ ucfirst($cliente->segmento ?? '—') }}</span>
                            </td>
                            <td>{{ $cliente->total_compras ?? 0 }}</td>
                            <td>
                                <span class="badge bg-{{ $cliente->estado === 'activo' ? 'success' : 'danger' }}">
                                    {{ ucfirst($cliente->estado) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted">No hay clientes registrados.</td></tr>
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
    // Gráfica de barras - Nuevos clientes por mes (estática decorativa)
    new Chart(document.getElementById('clientesMesChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [{
                label: 'Nuevos Clientes',
                data: [80, 95, 110, 88, 120, 124],
                backgroundColor: 'rgba(54,162,235,0.7)',
                borderColor: 'rgb(54,162,235)',
                borderWidth: 1
            }]
        },
        options: { responsive: true }
    });

    // Gráfica doughnut - Segmentación desde BD
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

    // Búsqueda y filtro
    function filtrarTabla() {
        const q      = document.getElementById('buscarCliente').value.toLowerCase();
        const estado = document.getElementById('filtroEstado').value.toLowerCase();
        document.querySelectorAll('#tablaClientes tbody tr').forEach(row => {
            const texto = row.innerText.toLowerCase();
            row.style.display = (texto.includes(q) && (estado === '' || texto.includes(estado))) ? '' : 'none';
        });
    }
    document.getElementById('buscarCliente').addEventListener('keyup', filtrarTabla);
    document.getElementById('filtroEstado').addEventListener('change', filtrarTabla);
</script>
@endsection