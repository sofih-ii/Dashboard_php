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

<div class="row mb-4">
    <div class="col-md-3"><div class="card text-white bg-success mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-dollar-sign"></i> Total Ventas</h5><p class="card-text display-6">$78,450</p><small class="text-white-50">Este mes</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-primary mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-receipt"></i> Pedidos</h5><p class="card-text display-6">342</p><small class="text-white-50">+22% vs anterior</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-warning mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-hourglass-half"></i> Pendientes</h5><p class="card-text display-6">18</p><small class="text-white-50">Por procesar</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-info mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-undo-alt"></i> Devoluciones</h5><p class="card-text display-6">5</p><small class="text-white-50">Este mes</small></div></div></div>
</div>

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

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Últimas Ventas</h5>
                <input type="text" class="form-control form-control-sm w-25" placeholder="Buscar...">
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead><tr><th>#Orden</th><th>Cliente</th><th>Producto</th><th>Total</th><th>Fecha</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <tr><td>#1045</td><td>Juan Pérez</td><td>Laptop Pro</td><td>$1,500</td><td>2024-06-01</td><td><span class="badge bg-success">Completado</span></td><td><button class="btn btn-sm btn-primary">Ver</button> <button class="btn btn-sm btn-danger">Cancelar</button></td></tr>
                        <tr><td>#1044</td><td>María García</td><td>Smartphone X</td><td>$890</td><td>2024-06-01</td><td><span class="badge bg-warning">Pendiente</span></td><td><button class="btn btn-sm btn-primary">Ver</button> <button class="btn btn-sm btn-danger">Cancelar</button></td></tr>
                        <tr><td>#1043</td><td>Carlos López</td><td>Tablet Plus</td><td>$450</td><td>2024-05-31</td><td><span class="badge bg-success">Completado</span></td><td><button class="btn btn-sm btn-primary">Ver</button> <button class="btn btn-sm btn-danger">Cancelar</button></td></tr>
                        <tr><td>#1042</td><td>Ana Martínez</td><td>Auriculares</td><td>$120</td><td>2024-05-31</td><td><span class="badge bg-danger">Devuelto</span></td><td><button class="btn btn-sm btn-primary">Ver</button> <button class="btn btn-sm btn-secondary" disabled>Cancelar</button></td></tr>
                        <tr><td>#1041</td><td>Pedro Sánchez</td><td>Cámara Digital</td><td>$680</td><td>2024-05-30</td><td><span class="badge bg-info">En camino</span></td><td><button class="btn btn-sm btn-primary">Ver</button> <button class="btn btn-sm btn-danger">Cancelar</button></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
new Chart(document.getElementById('ventasMesChart').getContext('2d'), {
    type: 'bar',
    data: { labels: ['Ene','Feb','Mar','Abr','May','Jun'], datasets: [{ label: 'Ventas ($)', data: [8000,12000,9500,14000,16000,18500], backgroundColor: 'rgba(40,167,69,0.7)', borderColor: 'rgb(40,167,69)', borderWidth: 1 }] },
    options: { responsive: true }
});
new Chart(document.getElementById('estadoPedidosChart').getContext('2d'), {
    type: 'pie',
    data: { labels: ['Completados','Pendientes','En camino','Devueltos'], datasets: [{ data: [280,35,22,5], backgroundColor: ['rgba(40,167,69,0.8)','rgba(255,193,7,0.8)','rgba(23,162,184,0.8)','rgba(220,53,69,0.8)'] }] },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endsection