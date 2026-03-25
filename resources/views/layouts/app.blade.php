<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @yield('styles')
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dashboard">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link @yield('nav_dashboard')" href="/dashboard">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @yield('nav_estadisticas')" href="/estadisticas">
                            <i class="fas fa-chart-bar"></i> Estadísticas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @yield('nav_clientes')" href="/clientes">
                            <i class="fas fa-users"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @yield('nav_nosotros')" href="/nosotros">
                            <i class="fas fa-info-circle"></i> Nosotros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @yield('nav_configuracion')" href="/configuracion">
                            <i class="fas fa-cog"></i> Configuración
                        </a>
                    </li>
                    <li class="nav-item d-flex align-items-center ms-2">
                        <span class="nav-link text-white pe-2">
                            <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                        </span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-white">
                                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">

            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link @yield('side_dashboard')" href="/dashboard">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @yield('side_estadisticas')" href="/estadisticas">
                                <i class="fas fa-chart-bar"></i> Estadísticas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @yield('side_analisis')" href="/analisis">
                                <i class="fas fa-chart-line"></i> Análisis
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @yield('side_ventas')" href="/ventas">
                                <i class="fas fa-shopping-cart"></i> Ventas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @yield('side_clientes')" href="/clientes">
                                <i class="fas fa-users"></i> Clientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @yield('side_facturas')" href="/facturas">
                                <i class="fas fa-file-invoice-dollar"></i> Facturas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @yield('side_mensajes')" href="/mensajes">
                                <i class="fas fa-envelope"></i> Mensajes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @yield('side_nosotros')" href="/nosotros">
                                <i class="fas fa-info-circle"></i> Nosotros
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                @yield('content')
            </main>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('scripts')

</body>
</html>