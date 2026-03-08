@extends('layouts.app')

@section('title', 'Análisis - Dashboard')
@section('side_analisis', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-chart-line text-primary"></i> Análisis</h1>
    <div class="btn-group me-2">
        <button class="btn btn-sm btn-outline-secondary">Exportar</button>
        <button class="btn btn-sm btn-outline-secondary">Filtrar</button>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3"><div class="card text-white bg-primary mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-eye"></i> Visitas</h5><p class="card-text display-6">24,530</p><small class="text-white-50">+8% esta semana</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-success mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-mouse-pointer"></i> Conversiones</h5><p class="card-text display-6">3.2%</p><small class="text-white-50">+0.5% este mes</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-warning mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-clock"></i> Tiempo Promedio</h5><p class="card-text display-6">4:32</p><small class="text-white-50">min por sesión</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-info mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-undo"></i> Rebote</h5><p class="card-text display-6">42%</p><small class="text-white-50">-3% vs anterior</small></div></div></div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Tráfico Semanal</h5></div>
            <div class="card-body"><canvas id="traficoChart" height="180"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Fuentes de Tráfico</h5></div>
            <div class="card-body"><canvas id="fuentesChart" height="180"></canvas></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h5>Páginas Más Visitadas</h5></div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead><tr><th>Página</th><th>Visitas</th><th>Tiempo Promedio</th><th>Tasa de Rebote</th><th>Conversiones</th></tr></thead>
                    <tbody>
                        <tr><td><i class="fas fa-home text-primary"></i> /inicio</td><td>8,432</td><td>2:15</td><td>38%</td><td><span class="badge bg-success">4.2%</span></td></tr>
                        <tr><td><i class="fas fa-shopping-cart text-success"></i> /productos</td><td>6,210</td><td>5:40</td><td>22%</td><td><span class="badge bg-success">7.8%</span></td></tr>
                        <tr><td><i class="fas fa-info-circle text-warning"></i> /nosotros</td><td>3,105</td><td>3:20</td><td>55%</td><td><span class="badge bg-warning">1.2%</span></td></tr>
                        <tr><td><i class="fas fa-envelope text-info"></i> /contacto</td><td>2,890</td><td>4:10</td><td>30%</td><td><span class="badge bg-success">5.5%</span></td></tr>
                        <tr><td><i class="fas fa-blog text-danger"></i> /blog</td><td>1,980</td><td>6:45</td><td>65%</td><td><span class="badge bg-danger">0.8%</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
new Chart(document.getElementById('traficoChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
        datasets: [{ label: 'Visitas', data: [1200,1900,1500,2100,2800,3200,1800], borderColor: 'rgb(54,162,235)', backgroundColor: 'rgba(54,162,235,0.1)', tension: 0.3, fill: true }]
    },
    options: { responsive: true }
});
new Chart(document.getElementById('fuentesChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Orgánico','Directo','Social','Referido'],
        datasets: [{ data: [40,30,20,10], backgroundColor: ['rgba(75,192,192,0.8)','rgba(54,162,235,0.8)','rgba(255,206,86,0.8)','rgba(255,99,132,0.8)'] }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endsection