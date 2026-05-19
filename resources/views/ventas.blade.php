@extends('layouts.app')

@section('title', 'Ventas - Dashboard')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endsection
@section('side_ventas', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-shopping-cart text-success"></i> Ventas</h1>
    <div class="btn-group me-2">
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevaVenta">
            <i class="fas fa-plus"></i> Nueva Venta
        </button>
        <a href="{{ route('ventas.export') }}" class="btn btn-sm btn-outline-secondary">
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

{{-- Gráficas --}}
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
            <div class="card-body">
                @if($estadoPedidos->isNotEmpty())
                    <canvas id="estadoPedidosChart" height="180"></canvas>
                @else
                    <p class="text-muted text-center py-5">Sin datos.</p>
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
                <h5 class="mb-0"><i class="fas fa-shopping-cart me-1"></i> Listado de Ventas</h5>
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
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                        <tr>
                            <td><code>{{ $venta->numero_orden }}</code></td>
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
                                <span class="badge bg-{{ $badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $venta->estado)) }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ $venta->created_at->format('d/m/Y') }}</small></td>
                            <td>
                                {{-- Ver detalle --}}
                                <button class="btn btn-sm btn-info text-white"
                                    title="Ver detalle"
                                    data-bs-toggle="modal" data-bs-target="#modalVerVenta"
                                    data-orden="{{ $venta->numero_orden }}"
                                    data-cliente="{{ $venta->cliente->nombre ?? '—' }} {{ $venta->cliente->apellido ?? '' }}"
                                    data-producto="{{ $venta->producto }}"
                                    data-total="${{ number_format($venta->total, 2) }}"
                                    data-estado="{{ ucfirst(str_replace('_', ' ', $venta->estado)) }}"
                                    data-fecha="{{ $venta->created_at->format('d/m/Y H:i') }}"
                                    onclick="verVenta(this)">
                                    <i class="fas fa-eye"></i>
                                </button>

                                {{-- Cambiar estado --}}
                                <button class="btn btn-sm btn-warning text-white"
                                    title="Cambiar estado"
                                    data-bs-toggle="modal" data-bs-target="#modalEstadoVenta"
                                    data-id="{{ $venta->id }}"
                                    data-orden="{{ $venta->numero_orden }}"
                                    data-estado="{{ $venta->estado }}"
                                    onclick="cambiarEstadoVenta(this)">
                                    <i class="fas fa-sync-alt"></i>
                                </button>

                                {{-- Eliminar --}}
                                <form method="POST"
                                      action="{{ route('ventas.destroy', $venta->id) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('¿Eliminar la venta {{ $venta->numero_orden }}?')">
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
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-shopping-cart fa-2x mb-2 d-block"></i>
                                No hay ventas registradas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Nueva Venta --}}
<div class="modal fade" id="modalNuevaVenta" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-1"></i> Nueva Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('ventas.store') }}">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cliente <span class="text-danger">*</span></label>
                            <select name="cliente_id" class="form-select" required>
                                <option value="">— Selecciona un cliente —</option>
                                @foreach($clientes as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre }} {{ $c->apellido }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                            <select name="estado" class="form-select" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="completado">Completado</option>
                                <option value="en_camino">En Camino</option>
                                <option value="devuelto">Devuelto</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Producto <span class="text-danger">*</span></label>
                            <input type="text" name="producto" class="form-control"
                                   placeholder="Nombre del producto o servicio" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Total ($) <span class="text-danger">*</span></label>
                            <input type="number" name="total" class="form-control"
                                   placeholder="0.00" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Registrar Venta
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Ver Venta --}}
<div class="modal fade" id="modalVerVenta" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-alt me-1"></i> Detalle de Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-hashtag me-2 text-muted"></i>N° Orden</span>
                        <strong id="vv-orden"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-user me-2 text-muted"></i>Cliente</span>
                        <strong id="vv-cliente"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-box me-2 text-muted"></i>Producto</span>
                        <strong id="vv-producto"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-dollar-sign me-2 text-muted"></i>Total</span>
                        <strong id="vv-total" class="text-success"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-circle me-2 text-muted"></i>Estado</span>
                        <span id="vv-estado" class="badge bg-secondary"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-calendar me-2 text-muted"></i>Fecha</span>
                        <strong id="vv-fecha"></strong>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Cambiar Estado --}}
<div class="modal fade" id="modalEstadoVenta" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-sync-alt me-1"></i> Cambiar Estado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="formEstadoVenta">
                    @csrf
                    @method('PUT')
                    <p class="text-muted small mb-3">Venta: <strong id="ev-orden"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nuevo Estado</label>
                        <select name="estado" id="ev-estado" class="form-select" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="completado">Completado</option>
                            <option value="en_camino">En Camino</option>
                            <option value="devuelto">Devuelto</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-warning text-white">
                            <i class="fas fa-save"></i> Actualizar Estado
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
    // ── DataTables ─────────────────────────────────────────────────────────────
    $(document).ready(function () {
        $('#tablaVentas').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
            },
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            responsive: true,
            columnDefs: [
                { orderable: false, targets: -1 }
            ],
            order: [[0, 'desc']]
        });
    });

    // Ver venta
    function verVenta(btn) {
        document.getElementById('vv-orden').textContent    = btn.dataset.orden;
        document.getElementById('vv-cliente').textContent  = btn.dataset.cliente;
        document.getElementById('vv-producto').textContent = btn.dataset.producto;
        document.getElementById('vv-total').textContent    = btn.dataset.total;
        document.getElementById('vv-estado').textContent   = btn.dataset.estado;
        document.getElementById('vv-fecha').textContent    = btn.dataset.fecha;
    }

    // Cambiar estado
    function cambiarEstadoVenta(btn) {
        document.getElementById('ev-orden').textContent = btn.dataset.orden;
        document.getElementById('ev-estado').value      = btn.dataset.estado;
        document.getElementById('formEstadoVenta').action = '/ventas/' + btn.dataset.id;
    }

    // Gráfica barras - Ventas por mes (decorativa)
    new Chart(document.getElementById('ventasMesChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [{
                label: 'Ventas ($)',
                data: [8000, 12000, 9500, 14000, 16000, 18500],
                backgroundColor: 'rgba(40,167,69,0.7)',
                borderColor: 'rgb(40,167,69)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // Gráfica pie - Estado de pedidos desde BD
    @if($estadoPedidos->isNotEmpty())
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
    @endif
</script>
@endsection
