@extends('layouts.app')

@section('title', 'Dashboard - Panel de Control')
@section('nav_dashboard', 'active')
@section('side_dashboard', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Panel de Control</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button class="btn btn-sm btn-outline-secondary" onclick="compartirDashboard()">
                <i class="fas fa-share-alt"></i> Compartir
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="exportarDashboard()">
                <i class="fas fa-download"></i> Exportar
            </button>
        </div>
    </div>
</div>

{{-- Toast notificación --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="toastMsg" class="toast align-items-center text-white border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastTexto"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

{{-- Modal Compartir --}}
<div class="modal fade" id="modalCompartir" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-share-alt"></i> Compartir Dashboard</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Copia el enlace para compartir este panel:</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="urlCompartir" readonly>
                    <button class="btn btn-primary" onclick="copiarUrl()">
                        <i class="fas fa-copy"></i> Copiar
                    </button>
                </div>
                <hr>
                <p class="text-muted mb-2">Compartir por:</p>
                <div class="d-flex gap-2">
                    <button class="btn btn-success flex-fill" onclick="compartirWhatsapp()">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </button>
                    <button class="btn btn-info text-white flex-fill" onclick="compartirEmail()">
                        <i class="fas fa-envelope"></i> Email
                    </button>
                    <button class="btn btn-secondary flex-fill" onclick="copiarUrl()">
                        <i class="fas fa-link"></i> Copiar link
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Exportar --}}
<div class="modal fade" id="modalExportar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-download"></i> Exportar Dashboard</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Selecciona el formato de exportación:</p>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-danger" onclick="exportarPDF()">
                        <i class="fas fa-file-pdf me-2"></i> Exportar como PDF
                    </button>
                    <button class="btn btn-outline-success" onclick="exportarCSV()">
                        <i class="fas fa-file-csv me-2"></i> Exportar tabla como CSV
                    </button>
                    <button class="btn btn-outline-primary" onclick="imprimirPagina()">
                        <i class="fas fa-print me-2"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- KPIs con datos reales --}}
<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-users"></i> Usuarios</h5>
                <p class="card-text display-6">{{ number_format($totalUsuarios) }}</p>
                <small class="text-white-50">Registrados</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-shopping-cart"></i> Ventas</h5>
                <p class="card-text display-6">${{ number_format($totalVentas, 0, ',', '.') }}</p>
                <small class="text-white-50">Total acumulado</small>
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
                <h5 class="card-title"><i class="fas fa-exclamation-triangle"></i> Alertas</h5>
                <p class="card-text display-6">{{ $totalAlertas }}</p>
                <small class="text-white-50">Requieren atención</small>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Estadísticas de Ventas</h5></div>
            <div class="card-body"><canvas id="salesChart" height="200"></canvas></div>
        </div>
    </div>

    {{-- Actividad Reciente con datos reales --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Actividad Reciente</h5>
                <span class="badge bg-primary">En vivo</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">

                    {{-- Nuevo usuario --}}
                    <li class="list-group-item d-flex align-items-start gap-2 py-3">
                        <div class="mt-1">
                            <i class="fas fa-user-plus text-success fa-lg"></i>
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex justify-content-between">
                                <strong class="small">Nuevo usuario registrado</strong>
                                <small class="text-muted">{{ $ultimoUsuario?->created_at->diffForHumans() ?? '—' }}</small>
                            </div>
                            <p class="mb-0 small text-muted">
                                {{ $ultimoUsuario?->name ?? 'Sin datos' }}
                                — {{ $ultimoUsuario?->email ?? '' }}
                            </p>
                        </div>
                    </li>

                    {{-- Venta completada --}}
                    <li class="list-group-item d-flex align-items-start gap-2 py-3">
                        <div class="mt-1">
                            <i class="fas fa-shopping-cart text-primary fa-lg"></i>
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex justify-content-between">
                                <strong class="small">Venta completada</strong>
                                <small class="text-muted">{{ $ultimaVenta?->created_at->diffForHumans() ?? '—' }}</small>
                            </div>
                            <p class="mb-0 small text-muted">
                                @if($ultimaVenta)
                                    {{ $ultimaVenta->numero_orden }} — {{ $ultimaVenta->producto }}
                                    — <strong>${{ number_format($ultimaVenta->total, 2) }}</strong>
                                @else
                                    Sin ventas completadas
                                @endif
                            </p>
                        </div>
                    </li>

                    {{-- Factura generada --}}
                    <li class="list-group-item d-flex align-items-start gap-2 py-3">
                        <div class="mt-1">
                            <i class="fas fa-file-invoice text-warning fa-lg"></i>
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex justify-content-between">
                                <strong class="small">Factura generada</strong>
                                <small class="text-muted">{{ $ultimaFactura?->created_at->diffForHumans() ?? '—' }}</small>
                            </div>
                            <p class="mb-0 small text-muted">
                                @if($ultimaFactura)
                                    {{ $ultimaFactura->numero_factura }} — {{ $ultimaFactura->concepto }}
                                    — <strong>${{ number_format($ultimaFactura->monto, 2) }}</strong>
                                @else
                                    Sin facturas registradas
                                @endif
                            </p>
                        </div>
                    </li>

                    {{-- Mensaje recibido --}}
                    <li class="list-group-item d-flex align-items-start gap-2 py-3">
                        <div class="mt-1">
                            <i class="fas fa-envelope text-info fa-lg"></i>
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex justify-content-between">
                                <strong class="small">Mensaje recibido</strong>
                                <small class="text-muted">{{ $ultimoMensaje?->created_at->diffForHumans() ?? '—' }}</small>
                            </div>
                            <p class="mb-0 small text-muted">
                                @if($ultimoMensaje)
                                    <strong>{{ $ultimoMensaje->cliente->nombre ?? '' }}:</strong>
                                    {{ Str::limit($ultimoMensaje->contenido, 45) }}
                                @else
                                    Sin mensajes recibidos
                                @endif
                            </p>
                        </div>
                    </li>

                </ul>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('mensajes') }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="fas fa-inbox"></i> Ver todos los mensajes
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Tabla clientes recientes desde BD --}}
<div class="row mt-2">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h5>Clientes Recientes</h5></div>
            <div class="card-body">
                <table class="table table-striped table-hover" id="tablaDatos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Segmento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datosRecientes as $cliente)
                        <tr>
                            <td>#{{ str_pad($cliente->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $cliente->nombre }} {{ $cliente->apellido }}</td>
                            <td>{{ $cliente->email }}</td>
                            <td>
                                <span class="badge bg-warning">{{ ucfirst($cliente->segmento ?? '—') }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $cliente->estado === 'activo' ? 'success' : 'danger' }}">
                                    {{ ucfirst($cliente->estado) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('clientes') }}" class="btn btn-sm btn-primary">Ver</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted">No hay clientes registrados.</td></tr>
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
// ── GRÁFICA ──────────────────────────────────────
new Chart(document.getElementById('salesChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: ['Ene','Feb','Mar','Abr','May','Jun'],
        datasets: [{ label: 'Ventas', data: [12,19,3,5,2,3], borderColor: 'rgb(75,192,192)', backgroundColor: 'rgba(75,192,192,0.1)', tension: 0.3, fill: true }]
    },
    options: { responsive: true }
});

// ── TOAST ──────────────────────────────────────
function mostrarToast(mensaje, tipo = 'success') {
    const toast = document.getElementById('toastMsg');
    const texto = document.getElementById('toastTexto');
    texto.textContent = mensaje;
    toast.className = `toast align-items-center text-white border-0 bg-${tipo}`;
    new bootstrap.Toast(toast, { delay: 3000 }).show();
}

// ── COMPARTIR ──────────────────────────────────────
function compartirDashboard() {
    document.getElementById('urlCompartir').value = window.location.href;
    new bootstrap.Modal(document.getElementById('modalCompartir')).show();
}

function copiarUrl() {
    const url = document.getElementById('urlCompartir').value;
    navigator.clipboard.writeText(url).then(() => {
        bootstrap.Modal.getInstance(document.getElementById('modalCompartir'))?.hide();
        mostrarToast('✅ Enlace copiado al portapapeles');
    });
}

function compartirWhatsapp() {
    const url = encodeURIComponent(window.location.href);
    const texto = encodeURIComponent('Mira este Dashboard: ');
    window.open(`https://wa.me/?text=${texto}${url}`, '_blank');
}

function compartirEmail() {
    const url = window.location.href;
    const asunto = encodeURIComponent('Dashboard - Panel de Control');
    const cuerpo = encodeURIComponent(`Te comparto el enlace al dashboard:\n${url}`);
    window.location.href = `mailto:?subject=${asunto}&body=${cuerpo}`;
}

// ── EXPORTAR ──────────────────────────────────────
function exportarDashboard() {
    new bootstrap.Modal(document.getElementById('modalExportar')).show();
}

function exportarPDF() {
    bootstrap.Modal.getInstance(document.getElementById('modalExportar'))?.hide();
    setTimeout(() => { window.print(); }, 400);
}

function imprimirPagina() {
    bootstrap.Modal.getInstance(document.getElementById('modalExportar'))?.hide();
    setTimeout(() => { window.print(); }, 400);
}

function exportarCSV() {
    const tabla = document.getElementById('tablaDatos');
    const filas = tabla.querySelectorAll('tr');
    let csv = '';
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('th, td');
        const fila_csv = Array.from(celdas)
            .slice(0, -1)
            .map(celda => `"${celda.innerText.trim()}"`)
            .join(',');
        csv += fila_csv + '\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'clientes_recientes.csv';
    link.click();
    bootstrap.Modal.getInstance(document.getElementById('modalExportar'))?.hide();
    mostrarToast('✅ CSV exportado correctamente');
}
</script>
@endsection