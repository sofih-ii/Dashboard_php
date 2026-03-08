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

<div class="row mb-4">
    <div class="col-md-3"><div class="card text-white bg-success mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-check-circle"></i> Pagadas</h5><p class="card-text display-6">186</p><small class="text-white-50">$42,300 total</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-warning mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-clock"></i> Pendientes</h5><p class="card-text display-6">34</p><small class="text-white-50">$8,900 pendiente</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-danger mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-times-circle"></i> Vencidas</h5><p class="card-text display-6">12</p><small class="text-white-50">$3,200 en mora</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-info mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-file-alt"></i> Total Emitidas</h5><p class="card-text display-6">232</p><small class="text-white-50">Este mes</small></div></div></div>
</div>

<div class="row mb-4">
    <div class="col-md-8"><div class="card"><div class="card-header"><h5>Ingresos por Facturación Mensual</h5></div><div class="card-body"><canvas id="facturacionChart" height="180"></canvas></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-header"><h5>Estado de Facturas</h5></div><div class="card-body"><canvas id="estadoFacturasChart" height="180"></canvas></div></div></div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Listado de Facturas</h5>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Buscar factura...">
                    <select class="form-select form-select-sm w-auto"><option>Todos</option><option>Pagadas</option><option>Pendientes</option><option>Vencidas</option></select>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead><tr><th>N° Factura</th><th>Cliente</th><th>Concepto</th><th>Monto</th><th>Emisión</th><th>Vencimiento</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <tr><td><strong>FAC-2024-001</strong></td><td>Juan Pérez</td><td>Laptop Pro x1</td><td>$1,500.00</td><td>2024-06-01</td><td>2024-06-15</td><td><span class="badge bg-success">Pagada</span></td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> <button class="btn btn-sm btn-secondary"><i class="fas fa-download"></i></button></td></tr>
                        <tr><td><strong>FAC-2024-002</strong></td><td>María García</td><td>Smartphone X x2</td><td>$1,780.00</td><td>2024-06-02</td><td>2024-06-16</td><td><span class="badge bg-warning">Pendiente</span></td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> <button class="btn btn-sm btn-secondary"><i class="fas fa-download"></i></button></td></tr>
                        <tr><td><strong>FAC-2024-003</strong></td><td>Carlos López</td><td>Tablet Plus x3</td><td>$1,350.00</td><td>2024-05-20</td><td>2024-06-03</td><td><span class="badge bg-danger">Vencida</span></td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> <button class="btn btn-sm btn-secondary"><i class="fas fa-download"></i></button></td></tr>
                        <tr><td><strong>FAC-2024-004</strong></td><td>Ana Martínez</td><td>Auriculares x5</td><td>$600.00</td><td>2024-06-05</td><td>2024-06-19</td><td><span class="badge bg-success">Pagada</span></td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> <button class="btn btn-sm btn-secondary"><i class="fas fa-download"></i></button></td></tr>
                        <tr><td><strong>FAC-2024-005</strong></td><td>Pedro Sánchez</td><td>Cámara Digital x1</td><td>$680.00</td><td>2024-06-06</td><td>2024-06-20</td><td><span class="badge bg-warning">Pendiente</span></td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> <button class="btn btn-sm btn-secondary"><i class="fas fa-download"></i></button></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
new Chart(document.getElementById('facturacionChart').getContext('2d'), {
    type: 'bar',
    data: { labels: ['Ene','Feb','Mar','Abr','May','Jun'], datasets: [{ label: 'Pagadas ($)', data: [6000,8500,7200,9800,11000,12500], backgroundColor: 'rgba(40,167,69,0.7)' }, { label: 'Pendientes ($)', data: [1200,900,1500,800,2000,1800], backgroundColor: 'rgba(255,193,7,0.7)' }] },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
});
new Chart(document.getElementById('estadoFacturasChart').getContext('2d'), {
    type: 'doughnut',
    data: { labels: ['Pagadas','Pendientes','Vencidas'], datasets: [{ data: [186,34,12], backgroundColor: ['rgba(40,167,69,0.8)','rgba(255,193,7,0.8)','rgba(220,53,69,0.8)'] }] },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endsection