@extends('layouts.app')

@section('title', 'Dashboard - Panel de Control')
@section('nav_dashboard', 'active')
@section('side_dashboard', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Panel de Control</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button class="btn btn-sm btn-outline-secondary">Compartir</button>
            <button class="btn btn-sm btn-outline-secondary">Exportar</button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3"><div class="card text-white bg-primary mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-users"></i> Usuarios</h5><p class="card-text display-6">1,234</p><small class="text-white-50">+12% este mes</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-success mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-shopping-cart"></i> Ventas</h5><p class="card-text display-6">$12,345</p><small class="text-white-50">+25% este mes</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-warning mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-chart-line"></i> Crecimiento</h5><p class="card-text display-6">+15%</p><small class="text-white-50">vs mes anterior</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-danger mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-exclamation-triangle"></i> Alertas</h5><p class="card-text display-6">3</p><small class="text-white-50">Requieren atención</small></div></div></div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Estadísticas de Ventas</h5></div>
            <div class="card-body"><canvas id="salesChart" height="200"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Actividad Reciente</h5></div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><i class="fas fa-user-plus text-success me-2"></i> Nuevo usuario registrado</li>
                    <li class="list-group-item"><i class="fas fa-shopping-cart text-primary me-2"></i> Venta completada</li>
                    <li class="list-group-item"><i class="fas fa-file-invoice text-warning me-2"></i> Factura generada</li>
                    <li class="list-group-item"><i class="fas fa-envelope text-info me-2"></i> Mensaje recibido</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h5>Datos Recientes</h5></div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Fecha</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <tr><td>1</td><td>Juan Pérez</td><td>juan@example.com</td><td>2023-10-01</td><td><button class="btn btn-sm btn-primary">Editar</button> <button class="btn btn-sm btn-danger">Eliminar</button></td></tr>
                        <tr><td>2</td><td>María García</td><td>maria@example.com</td><td>2023-10-02</td><td><button class="btn btn-sm btn-primary">Editar</button> <button class="btn btn-sm btn-danger">Eliminar</button></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
new Chart(document.getElementById('salesChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: ['Ene','Feb','Mar','Abr','May','Jun'],
        datasets: [{ label: 'Ventas', data: [12,19,3,5,2,3], borderColor: 'rgb(75,192,192)', backgroundColor: 'rgba(75,192,192,0.1)', tension: 0.3, fill: true }]
    },
    options: { responsive: true }
});
</script>
@endsection