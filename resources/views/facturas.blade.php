@extends('layouts.app')

@section('title', 'Facturas - Dashboard')
@section('side_facturas', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-file-invoice-dollar text-warning"></i> Facturas</h1>
    <div class="btn-group me-2">
        <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalNuevaFactura">
            <i class="fas fa-plus"></i> Nueva Factura
        </button>
        <a href="{{ route('facturas.export') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-download"></i> Exportar CSV
        </a>
        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir
        </button>
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

{{-- Gráficas --}}
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Facturación Mensual</h5></div>
            <div class="card-body"><canvas id="facturacionChart" height="180"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Estado de Facturas</h5></div>
            <div class="card-body">
                @if($estadoFacturas->isNotEmpty())
                    <canvas id="estadoFacturasChart" height="180"></canvas>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Listado de Facturas</h5>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm"
                           placeholder="Buscar factura..." id="buscarFactura">
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
                            <td>{{ Str::limit($factura->concepto, 40) }}</td>
                            <td>${{ number_format($factura->monto, 2) }}</td>
                            <td>{{ $factura->fecha_emision->format('d/m/Y') }}</td>
                            <td>
                                <span class="{{ $factura->estado === 'vencida' ? 'text-danger fw-semibold' : '' }}">
                                    {{ $factura->fecha_vencimiento->format('d/m/Y') }}
                                </span>
                            </td>
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
                                {{-- Ver --}}
                                <button class="btn btn-sm btn-info text-white"
                                    title="Ver detalle"
                                    data-bs-toggle="modal" data-bs-target="#modalVerFactura"
                                    data-numero="{{ $factura->numero_factura }}"
                                    data-cliente="{{ $factura->cliente->nombre ?? '—' }} {{ $factura->cliente->apellido ?? '' }}"
                                    data-concepto="{{ $factura->concepto }}"
                                    data-monto="${{ number_format($factura->monto, 2) }}"
                                    data-emision="{{ $factura->fecha_emision->format('d/m/Y') }}"
                                    data-vencimiento="{{ $factura->fecha_vencimiento->format('d/m/Y') }}"
                                    data-estado="{{ ucfirst($factura->estado) }}"
                                    onclick="verFactura(this)">
                                    <i class="fas fa-eye"></i>
                                </button>

                                {{-- Cambiar estado --}}
                                <button class="btn btn-sm btn-secondary"
                                    title="Cambiar estado"
                                    data-bs-toggle="modal" data-bs-target="#modalEstadoFactura"
                                    data-id="{{ $factura->id }}"
                                    data-numero="{{ $factura->numero_factura }}"
                                    data-estado="{{ $factura->estado }}"
                                    onclick="cambiarEstadoFactura(this)">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-file-invoice fa-2x mb-2 d-block"></i>
                                No hay facturas registradas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Nueva Factura --}}
<div class="modal fade" id="modalNuevaFactura" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-1"></i> Nueva Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('facturas.store') }}">
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
                                <option value="pagada">Pagada</option>
                                <option value="vencida">Vencida</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Concepto <span class="text-danger">*</span></label>
                        <input type="text" name="concepto" class="form-control"
                               placeholder="Descripción del servicio o producto" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Monto ($) <span class="text-danger">*</span></label>
                            <input type="number" name="monto" class="form-control"
                                   placeholder="0.00" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha Emisión <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_emision" class="form-control"
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha Vencimiento <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_vencimiento" class="form-control"
                                   value="{{ now()->addDays(30)->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-warning text-white">
                            <i class="fas fa-save"></i> Crear Factura
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Ver Factura --}}
<div class="modal fade" id="modalVerFactura" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice me-1"></i> Detalle de Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-hashtag me-2 text-muted"></i>N° Factura</span>
                        <strong id="fv-numero"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-user me-2 text-muted"></i>Cliente</span>
                        <strong id="fv-cliente"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-align-left me-2 text-muted"></i>Concepto</span>
                        <span id="fv-concepto" class="text-end" style="max-width:60%"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-dollar-sign me-2 text-muted"></i>Monto</span>
                        <strong id="fv-monto" class="text-success"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-calendar me-2 text-muted"></i>Emisión</span>
                        <strong id="fv-emision"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-calendar-times me-2 text-muted"></i>Vencimiento</span>
                        <strong id="fv-vencimiento"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-circle me-2 text-muted"></i>Estado</span>
                        <span id="fv-estado" class="badge bg-secondary"></span>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Cambiar Estado Factura --}}
<div class="modal fade" id="modalEstadoFactura" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-sync-alt me-1"></i> Cambiar Estado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="formEstadoFactura">
                    @csrf
                    @method('PUT')
                    <p class="text-muted small mb-3">Factura: <strong id="ef-numero"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nuevo Estado</label>
                        <select name="estado" id="ef-estado" class="form-select" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="pagada">Pagada</option>
                            <option value="vencida">Vencida</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-secondary text-white">
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
<script>
    function verFactura(btn) {
        document.getElementById('fv-numero').textContent      = btn.dataset.numero;
        document.getElementById('fv-cliente').textContent     = btn.dataset.cliente;
        document.getElementById('fv-concepto').textContent    = btn.dataset.concepto;
        document.getElementById('fv-monto').textContent       = btn.dataset.monto;
        document.getElementById('fv-emision').textContent     = btn.dataset.emision;
        document.getElementById('fv-vencimiento').textContent = btn.dataset.vencimiento;
        document.getElementById('fv-estado').textContent      = btn.dataset.estado;
    }

    function cambiarEstadoFactura(btn) {
        document.getElementById('ef-numero').textContent = btn.dataset.numero;
        document.getElementById('ef-estado').value       = btn.dataset.estado;
        document.getElementById('formEstadoFactura').action = '/facturas/' + btn.dataset.id;
    }

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

    // Gráfica barras - Facturación mensual (decorativa)
    new Chart(document.getElementById('facturacionChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [
                {
                    label: 'Pagadas ($)',
                    data: [6000, 8500, 7200, 9800, 11000, 12500],
                    backgroundColor: 'rgba(40,167,69,0.7)',
                    borderRadius: 4
                },
                {
                    label: 'Pendientes ($)',
                    data: [1200, 900, 1500, 800, 2000, 1800],
                    backgroundColor: 'rgba(255,193,7,0.7)',
                    borderRadius: 4
                }
            ]
        },
        options: { responsive: true, plugins: { legend: { position: 'top' } } }
    });

    // Gráfica doughnut - Estado de facturas desde BD
    @if($estadoFacturas->isNotEmpty())
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
    @endif
</script>
@endsection
