@extends('layouts.app')

@section('title', 'Estadísticas - Dashboard')
@section('nav_estadisticas', 'active')
@section('side_analisis', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Estadísticas</h1>
    <button class="btn btn-sm btn-outline-secondary">Exportar</button>
</div>

<div class="row mb-4">
    <div class="col-md-3"><div class="card text-white bg-primary mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-users"></i> Usuarios Totales</h5><p class="card-text display-6">5,432</p><small class="text-white-50">+12% este mes</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-success mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-shopping-cart"></i> Ventas Totales</h5><p class="card-text display-6">$45,890</p><small class="text-white-50">+25% este mes</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-warning mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-chart-line"></i> Crecimiento</h5><p class="card-text display-6">+18%</p><small class="text-white-50">vs mes anterior</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-info mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-comment-dots"></i> Mensajes</h5><p class="card-text display-6">234</p><small class="text-white-50">Sin leer</small></div></div></div>
</div>

<div class="row mb-4">
    <div class="col-md-6"><div class="card"><div class="card-header"><h5>Ventas Mensuales</h5></div><div class="card-body"><canvas id="ventasChart" height="200"></canvas></div></div></div>
    <div class="col-md-6"><div class="card"><div class="card-header"><h5>Género de Usuarios</h5></div><div class="card-body"><canvas id="generoChart" height="200"></canvas></div></div></div>
</div>

<div class="row mb-4">
    <div class="col-md-6"><div class="card"><div class="card-header"><h5>Productos Más Vendidos</h5></div><div class="card-body"><canvas id="productosChart" height="200"></canvas></div></div></div>
    <div class="col-md-6"><div class="card"><div class="card-header"><h5>Distribución de Ingresos</h5></div><div class="card-body"><canvas id="ingresosChart" height="200"></canvas></div></div></div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h5>Estadísticas de Productos</h5></div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead><tr><th>Producto</th><th>Vendidas</th><th>Ingresos</th><th>Margen</th><th>Estado</th></tr></thead>
                    <tbody>
                        <tr><td><i class="fas fa-cube"></i> Laptop Pro</td><td>156</td><td>$23,400</td><td>35%</td><td><span class="badge bg-success">Alto</span></td></tr>
                        <tr><td><i class="fas fa-mobile-alt"></i> Smartphone X</td><td>342</td><td>$15,890</td><td>28%</td><td><span class="badge bg-success">Alto</span></td></tr>
                        <tr><td><i class="fas fa-headphones"></i> Auriculares</td><td>89</td><td>$2,670</td><td>42%</td><td><span class="badge bg-warning">Medio</span></td></tr>
                        <tr><td><i class="fas fa-tablet-alt"></i> Tablet Plus</td><td>67</td><td>$3,350</td><td>25%</td><td><span class="badge bg-warning">Medio</span></td></tr>
                        <tr><td><i class="fas fa-camera"></i> Cámara Digital</td><td>45</td><td>$2,250</td><td>18%</td><td><span class="badge bg-danger">Bajo</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
new Chart(document.getElementById('ventasChart').getContext('2d'), {
    type: 'line',
    data: { labels: ['Enero','Febrero','Marzo','Abril','Mayo','Junio'], datasets: [{ label: 'Ventas ($)', data: [5000,7500,6800,9200,11500,14200], borderColor: 'rgb(75,192,192)', backgroundColor: 'rgba(75,192,192,0.1)', tension: 0.3, fill: true }] },
    options: { responsive: true }
});
new Chart(document.getElementById('generoChart').getContext('2d'), {
    type: 'doughnut',
    data: { labels: ['Hombres','Mujeres','Otros'], datasets: [{ data: [55,40,5], backgroundColor: ['rgba(54,162,235,0.8)','rgba(255,99,132,0.8)','rgba(255,206,86,0.8)'] }] },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
new Chart(document.getElementById('productosChart').getContext('2d'), {
    type: 'bar',
    data: { labels: ['Laptop','Smartphone','Auriculares','Tablet','Cámara'], datasets: [{ label: 'Unidades Vendidas', data: [156,342,89,67,45], backgroundColor: ['rgba(75,192,192,0.8)','rgba(54,162,235,0.8)','rgba(255,206,86,0.8)','rgba(75,192,192,0.8)','rgba(255,99,132,0.8)'] }] },
    options: { responsive: true, indexAxis: 'y', plugins: { legend: { display: false } } }
});
new Chart(document.getElementById('ingresosChart').getContext('2d'), {
    type: 'pie',
    data: { labels: ['Electrónica','Accesorios','Servicios','Otros'], datasets: [{ data: [45,25,20,10], backgroundColor: ['rgba(75,192,192,0.8)','rgba(54,162,235,0.8)','rgba(255,206,86,0.8)','rgba(255,99,132,0.8)'] }] },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endsection