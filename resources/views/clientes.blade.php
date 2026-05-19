@extends('layouts.app')

@section('title', 'Clientes - Dashboard')
@section('nav_clientes', 'active')
@section('side_clientes', 'active')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-users text-primary"></i> Clientes</h1>
    <div class="btn-group me-2">
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
            <i class="fas fa-plus"></i> Nuevo Cliente
        </button>
        <a href="{{ route('clientes.export') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-download"></i> Exportar CSV
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- KPIs --}}
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

{{-- Gráficas --}}
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
            <div class="card-body">
                @if($segmentacion->isNotEmpty())
                    <canvas id="segmentacionChart" height="200"></canvas>
                @else
                    <p class="text-muted text-center py-5">No hay datos de segmentación.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Tabla --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users me-1"></i> Lista de Clientes</h5>
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
                            <td><i class="fas fa-user-circle text-primary me-1"></i> {{ $cliente->nombre }} {{ $cliente->apellido }}</td>
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
                                <button class="btn btn-sm btn-info text-white"
                                    title="Ver detalle"
                                    data-bs-toggle="modal" data-bs-target="#modalVerCliente"
                                    data-id="{{ $cliente->id }}"
                                    data-nombre="{{ $cliente->nombre }} {{ $cliente->apellido }}"
                                    data-email="{{ $cliente->email }}"
                                    data-telefono="{{ $cliente->telefono ?? '—' }}"
                                    data-segmento="{{ ucfirst($cliente->segmento ?? '—') }}"
                                    data-estado="{{ ucfirst($cliente->estado) }}"
                                    data-compras="{{ $cliente->total_compras ?? 0 }}"
                                    data-desde="{{ $cliente->created_at->format('d/m/Y') }}"
                                    onclick="verCliente(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-primary"
                                    title="Editar"
                                    data-bs-toggle="modal" data-bs-target="#modalEditarCliente"
                                    data-id="{{ $cliente->id }}"
                                    data-nombre="{{ $cliente->nombre }}"
                                    data-apellido="{{ $cliente->apellido }}"
                                    data-email="{{ $cliente->email }}"
                                    data-telefono="{{ $cliente->telefono ?? '' }}"
                                    data-estado="{{ $cliente->estado }}"
                                    data-segmento="{{ $cliente->segmento ?? 'regular' }}"
                                    onclick="editarCliente(this)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST"
                                      action="{{ route('clientes.destroy', $cliente->id) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('¿Eliminar a {{ $cliente->nombre }}? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                No hay clientes registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Nuevo Cliente --}}
<div class="modal fade" id="modalNuevoCliente" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-1"></i> Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('clientes.store') }}">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="apellido" class="form-control" placeholder="Apellido" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" placeholder="+57 300 000 0000">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                            <select name="estado" class="form-select" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Segmento <span class="text-danger">*</span></label>
                            <select name="segmento" class="form-select" required>
                                <option value="regular">Regular</option>
                                <option value="premium">Premium</option>
                                <option value="ocasional">Ocasional</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cliente
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Ver Cliente --}}
<div class="modal fade" id="modalVerCliente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user me-1"></i> Detalle del Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-user-circle fa-5x text-primary mb-2"></i>
                    <h4 id="ver-nombre"></h4>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-envelope me-2 text-muted"></i>Email</span>
                        <strong id="ver-email"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-phone me-2 text-muted"></i>Teléfono</span>
                        <strong id="ver-telefono"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-tag me-2 text-muted"></i>Segmento</span>
                        <span id="ver-segmento" class="badge bg-warning"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-circle me-2 text-muted"></i>Estado</span>
                        <span id="ver-estado" class="badge bg-success"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-shopping-cart me-2 text-muted"></i>Total Compras</span>
                        <strong id="ver-compras"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-calendar me-2 text-muted"></i>Cliente desde</span>
                        <strong id="ver-desde"></strong>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Editar Cliente --}}
<div class="modal fade" id="modalEditarCliente" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-1"></i> Editar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="formEditarCliente">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="edit-nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="apellido" id="edit-apellido" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit-email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" name="telefono" id="edit-telefono" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="estado" id="edit-estado" class="form-select" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Segmento</label>
                            <select name="segmento" id="edit-segmento" class="form-select" required>
                                <option value="regular">Regular</option>
                                <option value="premium">Premium</option>
                                <option value="ocasional">Ocasional</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
    // ── Auto-abrir modal de edición si se viene desde /clientes/{id}/edit ──────
    @if(session('editarId'))
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.querySelector(
            '[data-bs-target="#modalEditarCliente"][data-id="{{ session('editarId') }}"]'
        );
        if (btn) { btn.click(); }
    });
    @endif

    // ── DataTables ─────────────────────────────────────────────────────────────
    $(document).ready(function () {
        $('#tablaClientes').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
            },
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            responsive: true,
            columnDefs: [
                { orderable: false, targets: -1 }   // columna Acciones no ordenable
            ],
            order: [[0, 'asc']]
        });
    });

    // Ver cliente
    function verCliente(btn) {
        document.getElementById('ver-nombre').textContent   = btn.dataset.nombre;
        document.getElementById('ver-email').textContent    = btn.dataset.email;
        document.getElementById('ver-telefono').textContent = btn.dataset.telefono;
        document.getElementById('ver-segmento').textContent = btn.dataset.segmento;
        document.getElementById('ver-estado').textContent   = btn.dataset.estado;
        document.getElementById('ver-compras').textContent  = btn.dataset.compras;
        document.getElementById('ver-desde').textContent    = btn.dataset.desde;
    }

    // Editar cliente
    function editarCliente(btn) {
        const form = document.getElementById('formEditarCliente');
        form.action = '/clientes/' + btn.dataset.id;
        document.getElementById('edit-nombre').value    = btn.dataset.nombre;
        document.getElementById('edit-apellido').value  = btn.dataset.apellido;
        document.getElementById('edit-email').value     = btn.dataset.email;
        document.getElementById('edit-telefono').value  = btn.dataset.telefono;
        document.getElementById('edit-estado').value    = btn.dataset.estado;
        document.getElementById('edit-segmento').value  = btn.dataset.segmento;
    }

    // Gráfica barras - Clientes por mes (estética)
    new Chart(document.getElementById('clientesMesChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [{
                label: 'Nuevos Clientes',
                data: [80, 95, 110, 88, 120, 124],
                backgroundColor: 'rgba(54,162,235,0.7)',
                borderColor: 'rgb(54,162,235)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // Gráfica segmentación
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
    @endif
</script>
@endsection
