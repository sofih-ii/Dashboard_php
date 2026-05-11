@extends('layouts.app')

@section('title', 'Configuración - Dashboard')
@section('nav_configuracion', 'active')

@section('content')

@php
    $tabActivo = session('tab', 'perfil');
    if ($errors->has('current_password') || $errors->has('password')) $tabActivo = 'seguridad';
    if ($errors->has('password_confirm')) $tabActivo = 'sistema';
    $ns = Auth::user()->notification_settings ?? [];
@endphp

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-cog text-secondary"></i> Configuración</h1>
</div>

<ul class="nav nav-tabs mb-4" id="configTabs">
    <li class="nav-item">
        <a class="nav-link {{ $tabActivo === 'perfil' ? 'active' : '' }}"
            data-bs-toggle="tab" href="#perfil">
            <i class="fas fa-user"></i> Perfil
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tabActivo === 'seguridad' ? 'active' : '' }}"
            data-bs-toggle="tab" href="#seguridad">
            <i class="fas fa-lock"></i> Seguridad
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tabActivo === 'notificaciones' ? 'active' : '' }}"
            data-bs-toggle="tab" href="#notificaciones">
            <i class="fas fa-bell"></i> Notificaciones
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tabActivo === 'sistema' ? 'active' : '' }}"
            data-bs-toggle="tab" href="#sistema">
            <i class="fas fa-server"></i> Sistema
        </a>
    </li>
</ul>

<div class="tab-content">

    {{-- ═══════════════════ PERFIL ═══════════════════ --}}
    <div class="tab-pane fade {{ $tabActivo === 'perfil' ? 'show active' : '' }}" id="perfil">
        <div class="row">

            {{-- Tarjeta avatar --}}
            <div class="col-md-4">
                <div class="card text-center mb-4">
                    <div class="card-body">
                        @if(Auth::user()->avatar)
                            <img id="avatarPreview"
                                 src="{{ asset('storage/' . Auth::user()->avatar) }}"
                                 class="rounded-circle mb-3"
                                 style="width:100px;height:100px;object-fit:cover;">
                        @else
                            <i class="fas fa-user-circle fa-6x text-secondary mb-3" id="avatarIcon"></i>
                            <img id="avatarPreview" src="" class="rounded-circle mb-3 d-none"
                                 style="width:100px;height:100px;object-fit:cover;">
                        @endif
                        <h5 class="card-title">{{ Auth::user()->name }}</h5>
                        <p class="text-muted mb-2">{{ Auth::user()->email }}</p>
                        <label for="avatarInputTrigger" class="btn btn-outline-primary btn-sm mb-0" style="cursor:pointer;">
                            <i class="fas fa-camera"></i> Cambiar Foto
                        </label>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h6>Información Rápida</h6></div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Rol</span><span class="badge bg-primary">Admin</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Estado</span><span class="badge bg-success">Activo</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>2FA</span>
                            <span class="badge bg-{{ Auth::user()->two_factor_enabled ? 'success' : 'secondary' }}">
                                {{ Auth::user()->two_factor_enabled ? 'Activo' : 'Inactivo' }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Miembro desde</span>
                            <span class="text-muted">{{ Auth::user()->created_at->format('M Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Formulario datos personales --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h5>Datos Personales</h5></div>
                    <div class="card-body">

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('perfil.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Input avatar oculto --}}
                            <input type="file" name="avatar" id="avatarInputTrigger"
                                   class="d-none" accept="image/*">

                            @error('avatar')
                                <div class="alert alert-danger py-1 small">{{ $message }}</div>
                            @enderror

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', Auth::user()->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Teléfono</label>
                                    <input type="text" name="phone"
                                        class="form-control"
                                        placeholder="+57 300 000 0000"
                                        value="{{ old('phone', Auth::user()->phone ?? '') }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', Auth::user()->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Zona Horaria</label>
                                    <select name="timezone" class="form-select">
                                        @foreach([
                                            'America/Bogota'    => 'América/Bogotá (UTC-5)',
                                            'America/New_York'  => 'América/Nueva York (UTC-5)',
                                            'America/Mexico_City' => 'América/Ciudad de México (UTC-6)',
                                            'America/Lima'      => 'América/Lima (UTC-5)',
                                            'America/Santiago'  => 'América/Santiago (UTC-4)',
                                            'Europe/Madrid'     => 'Europa/Madrid (UTC+1)',
                                            'UTC'               => 'UTC',
                                        ] as $tz => $label)
                                            <option value="{{ $tz }}" {{ (Auth::user()->timezone ?? 'America/Bogota') === $tz ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Idioma</label>
                                    <select name="language" class="form-select">
                                        <option value="es" {{ (Auth::user()->language ?? 'es') === 'es' ? 'selected' : '' }}>Español</option>
                                        <option value="en" {{ (Auth::user()->language ?? 'es') === 'en' ? 'selected' : '' }}>English</option>
                                        <option value="pt" {{ (Auth::user()->language ?? 'es') === 'pt' ? 'selected' : '' }}>Português</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Perfil
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i> Cancelar
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════ SEGURIDAD ═══════════════════ --}}
    <div class="tab-pane fade {{ $tabActivo === 'seguridad' ? 'show active' : '' }}" id="seguridad">
        <div class="row">

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-key"></i> Cambiar Contraseña</h5>
                    </div>
                    <div class="card-body">

                        @if(session('success_password'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle"></i> {{ session('success_password') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Contraseña Actual</label>
                                <div class="input-group">
                                    <input type="password" id="current_password" name="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePass('current_password','eye1')">
                                        <i class="fas fa-eye" id="eye1"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="new_password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Mínimo 6 caracteres">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePass('new_password','eye2')">
                                        <i class="fas fa-eye" id="eye2"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Confirmar Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="confirm_password" name="password_confirmation"
                                        class="form-control" placeholder="Repite la nueva contraseña">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePass('confirm_password','eye3')">
                                        <i class="fas fa-eye" id="eye3"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-lock"></i> Actualizar Contraseña
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-shield-alt"></i> Autenticación de Dos Factores</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Agrega una capa extra de seguridad a tu cuenta.</p>

                        @if(session('success_2fa'))
                            <div class="alert alert-success alert-dismissible fade show" style="font-size:.85rem;">
                                <i class="fas fa-check-circle"></i> {{ session('success_2fa') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="mb-3">
                            @if(Auth::user()->two_factor_enabled)
                                <span class="badge bg-success mb-2 fs-6">
                                    <i class="fas fa-shield-alt"></i> 2FA Activado
                                </span>
                                <p class="text-muted small">Tu cuenta está protegida con doble factor.</p>
                            @else
                                <span class="badge bg-secondary mb-2 fs-6">
                                    <i class="fas fa-shield-alt"></i> 2FA Desactivado
                                </span>
                                <p class="text-muted small">Actívalo para mayor seguridad.</p>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('2fa.toggle') }}">
                            @csrf
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="2fa_switch"
                                    {{ Auth::user()->two_factor_enabled ? 'checked' : '' }}
                                    onchange="this.form.submit()">
                                <label class="form-check-label" for="2fa_switch">
                                    {{ Auth::user()->two_factor_enabled ? 'Desactivar 2FA' : 'Activar 2FA' }}
                                </label>
                            </div>
                        </form>

                        <p class="text-muted" style="font-size:.82rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            Se enviará un código de 6 dígitos a
                            <strong>{{ Auth::user()->email }}</strong> en cada inicio de sesión.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Sesiones Activas</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <i class="fas fa-desktop fa-2x text-primary"></i>
                            <div>
                                <div class="fw-semibold">Esta sesión</div>
                                <div class="text-muted small">Sesión actual — {{ now()->format('d/m/Y H:i') }}</div>
                            </div>
                            <span class="badge bg-success ms-auto">Activa</span>
                        </div>
                        <hr>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="fas fa-sign-out-alt"></i> Cerrar todas las sesiones
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════════════ NOTIFICACIONES ═══════════════════ --}}
    <div class="tab-pane fade {{ $tabActivo === 'notificaciones' ? 'show active' : '' }}" id="notificaciones">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-bell"></i> Preferencias de Notificaciones</h5>
                </div>
                <div class="card-body">

                    @if(session('success_notif'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle"></i> {{ session('success_notif') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('config.notificaciones') }}">
                        @csrf

                        <h6 class="text-muted mb-3 fw-semibold">
                            <i class="fas fa-envelope me-1"></i> Notificaciones por Email
                        </h6>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="nueva_venta"
                                   id="n_venta" {{ ($ns['nueva_venta'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="n_venta">
                                <i class="fas fa-shopping-cart text-success me-1"></i>
                                Nueva venta realizada
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="nuevo_cliente"
                                   id="n_cliente" {{ ($ns['nuevo_cliente'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="n_cliente">
                                <i class="fas fa-user-plus text-primary me-1"></i>
                                Nuevo cliente registrado
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="factura_vencida"
                                   id="n_factura" {{ ($ns['factura_vencida'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="n_factura">
                                <i class="fas fa-file-invoice text-danger me-1"></i>
                                Factura vencida
                            </label>
                        </div>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="mensaje_nuevo"
                                   id="n_mensaje" {{ ($ns['mensaje_nuevo'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="n_mensaje">
                                <i class="fas fa-envelope text-info me-1"></i>
                                Mensaje nuevo recibido
                            </label>
                        </div>

                        <hr>

                        <h6 class="text-muted mb-3 fw-semibold">
                            <i class="fas fa-bell me-1"></i> Notificaciones del Sistema
                        </h6>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="alerta_seguridad"
                                   id="n_seguridad" {{ ($ns['alerta_seguridad'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="n_seguridad">
                                <i class="fas fa-shield-alt text-warning me-1"></i>
                                Alertas de seguridad
                            </label>
                        </div>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="reporte_semanal"
                                   id="n_reporte" {{ ($ns['reporte_semanal'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="n_reporte">
                                <i class="fas fa-chart-bar text-secondary me-1"></i>
                                Reportes semanales
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Preferencias
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════ SISTEMA ═══════════════════ --}}
    <div class="tab-pane fade {{ $tabActivo === 'sistema' ? 'show active' : '' }}" id="sistema">
        <div class="row">

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> Información del Sistema</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Versión App</span><span class="badge bg-primary">v1.0.0</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Laravel</span><span class="text-muted">{{ app()->version() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>PHP</span><span class="text-muted">{{ PHP_VERSION }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Base de Datos</span><span class="badge bg-success">Conectada</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Entorno</span>
                            <span class="badge bg-{{ config('app.debug') ? 'warning' : 'success' }}">
                                {{ config('app.env') }}
                            </span>
                        </li>
                    </ul>
                </div>

                {{-- Cache --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-broom"></i> Mantenimiento</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success_system'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle"></i> {{ session('success_system') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        <p class="text-muted small mb-3">Limpia la caché de vistas, configuración y datos almacenados temporalmente.</p>
                        <form method="POST" action="{{ route('config.cache') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-broom"></i> Limpiar Caché del Sistema
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                {{-- Apariencia --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-palette"></i> Apariencia</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('config.sistema') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tema</label>
                                <select name="theme" class="form-select" id="temaSelect">
                                    <option value="light" {{ (Auth::user()->theme ?? 'light') === 'light' ? 'selected' : '' }}>
                                        ☀️ Claro (Light)
                                    </option>
                                    <option value="dark" {{ (Auth::user()->theme ?? 'light') === 'dark' ? 'selected' : '' }}>
                                        🌙 Oscuro (Dark)
                                    </option>
                                </select>
                                <div class="form-text">El cambio se aplica al guardar y recargar la página.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Elementos por página</label>
                                <select name="per_page" class="form-select">
                                    <option value="10"  {{ (Auth::user()->per_page ?? 25) == 10  ? 'selected' : '' }}>10</option>
                                    <option value="25"  {{ (Auth::user()->per_page ?? 25) == 25  ? 'selected' : '' }}>25</option>
                                    <option value="50"  {{ (Auth::user()->per_page ?? 25) == 50  ? 'selected' : '' }}>50</option>
                                </select>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="resetearSistema()">
                                    <i class="fas fa-redo"></i> Restablecer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Zona de peligro --}}
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Zona de Peligro</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Estas acciones son permanentes e irreversibles.</p>

                        @if($errors->has('password_confirm'))
                            <div class="alert alert-danger py-2 small">
                                <i class="fas fa-times-circle"></i> {{ $errors->first('password_confirm') }}
                            </div>
                        @endif

                        <button type="button" class="btn btn-danger w-100"
                            data-bs-toggle="modal" data-bs-target="#modalEliminarCuenta">
                            <i class="fas fa-user-times"></i> Eliminar Mi Cuenta
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Modal Eliminar Cuenta --}}
<div class="modal fade" id="modalEliminarCuenta" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Eliminar Cuenta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-danger fw-semibold">⚠️ Esta acción eliminará permanentemente tu cuenta y todos tus datos.</p>
                <p class="text-muted small">Ingresa tu contraseña para confirmar:</p>
                <form method="POST" action="{{ route('config.delete') }}" id="formEliminarCuenta">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña</label>
                        <input type="password" name="password_confirm" class="form-control"
                               placeholder="Tu contraseña actual" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger flex-fill">
                            <i class="fas fa-trash"></i> Sí, eliminar mi cuenta
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    function resetearSistema() {
        if (confirm('¿Restaurar los valores por defecto del sistema?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("config.sistema") }}';
            form.innerHTML = `
                @csrf
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="theme" value="light">
                <input type="hidden" name="per_page" value="25">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Preview avatar antes de subir
    document.getElementById('avatarInputTrigger').addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.getElementById('avatarPreview');
                const icon    = document.getElementById('avatarIcon');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                if (icon) icon.style.display = 'none';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Abrir modal eliminar cuenta si hay error de contraseña
    @if($errors->has('password_confirm'))
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('modalEliminarCuenta')).show();
        });
    @endif
</script>
@endsection
