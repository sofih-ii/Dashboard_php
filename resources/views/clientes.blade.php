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

<div class="row mb-4">
    <div class="col-md-3"><div class="card text-white bg-primary mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-users"></i> Total Clientes</h5><p class="card-text display-6">2,847</p><small class="text-white-50">+15% este mes</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-success mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-user-check"></i> Activos</h5><p class="card-text display-6">2,340</p><small class="text-white-50">82% del total</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-warning mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-user-plus"></i> Nuevos</h5><p class="card-text display-6">124</p><small class="text-white-50">Este mes</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-danger mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-user-times"></i> Inactivos</h5><p class="card-text display-6">507</p><small class="text-white-50">18% del total</small></div></div></div>
</div>

<div class="row mb-4">
    <div class="col-md-6"><div class="card"><div class="card-header"><h5>Nuevos Clientes por Mes</h5></div><div class="card-body"><canvas id="clientesMesChart" height="200"></canvas></div></div></div>
    <div class="col-md-6"><div class="card"><div class="card-header"><h5>Segmentación de Clientes</h5></div><div class="card-body"><canvas id="segmentacionChart" height="200"></canvas></div></div></div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Clientes</h5>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Buscar cliente...">
                    <select class="form-select form-select-sm w-auto"><option>Todos</option><option>Activos</option><option>Inactivos</option></select>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Compras</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <tr><td>#001</td><td><i class="fas fa-user-circle text-primary"></i> Juan Pérez</td><td>juan@email.com</td><td>+57 300 123 4567</td><td>12</td><td><span class="badge bg-success">Activo</span></td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td></tr>
                        <tr><td>#002</td><td><i class="fas fa-user-circle text-success"></i> María García</td><td>maria@email.com</td><td>+57 310 987 6543</td><td>8</td><td><span class="badge bg-success">Activo</span></td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td></tr>
                        <tr><td>#003</td><td><i class="fas fa-user-circle text-warning"></i> Carlos López</td><td>carlos@email.com</td><td>+57 320 456 7890</td><td>3</td><td><span class="badge bg-warning">Inactivo</span></td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td></tr>
                        <tr><td>#004</td><td><i class="fas fa-user-circle text-info"></i> Ana Martínez</td><td>ana@email.com</td><td>+57 315 234 5678</td><td>21</td><td><span class="badge bg-success">Activo</span></td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td></tr>
                        <tr><td>#005</td><td><i class="fas fa-user-circle text-danger"></i> Pedro Sánchez</td><td>pedro@email.com</td><td>+57 312 789 0123</td><td>0</td><td><span class="badge bg-danger">Inactivo</span></td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
new Chart(document.getElementById('clientesMesChart').getContext('2d'), {
    type: 'bar',
    data: { labels: ['Ene','Feb','Mar','Abr','May','Jun'], datasets: [{ label: 'Nuevos Clientes', data: [80,95,110,88,120,124], backgroundColor: 'rgba(54,162,235,0.7)', borderColor: 'rgb(54,162,235)', borderWidth: 1 }] },
    options: { responsive: true }
});
new Chart(document.getElementById('segmentacionChart').getContext('2d'), {
    type: 'doughnut',
    data: { labels: ['Premium','Regular','Ocasional','Inactivo'], datasets: [{ data: [15,45,22,18], backgroundColor: ['rgba(255,193,7,0.8)','rgba(40,167,69,0.8)','rgba(23,162,184,0.8)','rgba(220,53,69,0.8)'] }] },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endsection