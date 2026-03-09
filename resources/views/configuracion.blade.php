@extends('layouts.app')

@section('title', 'Configuración - Dashboard')
@section('nav_configuracion', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-cog text-secondary"></i> Configuración</h1>
</div>


<ul class="nav nav-tabs mb-4" id="configTabs">
    <li class="nav-item">
        <a class="nav-link {{ (!session('tab') && !$errors->has('current_password') && !$errors->has('password')) ? 'active' : '' }}"
            data-bs-toggle="tab" href="#perfil">
            <i class="fas fa-user"></i> Perfil
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (session('tab') === 'seguridad' || $errors->has('current_password') || $errors->has('password')) ? 'active' : '' }}"
            data-bs-toggle="tab" href="#seguridad">
            <i class="fas fa-lock"></i> Seguridad
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#notificaciones">
            <i class="fas fa-bell"></i> Notificaciones
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#sistema">
            <i class="fas fa-server"></i> Sistema
        </a>
    </li>
</ul>

<div class="tab-content">


    <div class="tab-pane fade {{ (!session('tab') && !$errors->has('current_password') && !$errors->has('password')) ? 'show active' : '' }}" id="perfil">
        <div class="row">


            <div class="col-md-4">
                <div class="card text-center mb-4">
                    <div class="card-body">
                        <i class="fas fa-user-circle fa-6x text-secondary mb-3"></i>
                        <h5 class="card-title">{{ Auth::user()->name }}</h5>
                        <p class="text-muted">{{ Auth::user()->email }}</p>
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-camera"></i> Cambiar Foto
                        </button>
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
                            <span>Miembro desde</span><span class="text-muted">Ene 2024</span>
                        </li>
                    </ul>
                </div>
            </div> 


            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h5>Datos Personales</h5></div>
                    <div class="card-body">

                        @if(session('success'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('perfil.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', Auth::user()->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" class="form-control" placeholder="Opcional">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', Auth::user()->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" placeholder="+57 300 000 0000">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Zona Horaria</label>
                                <select class="form-select">
                                    <option selected>America/Bogota (UTC-5)</option>
                                    <option>America/New_York (UTC-5)</option>
                                    <option>Europe/Madrid (UTC+1)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Idioma</label>
                                <select class="form-select">
                                    <option selected>Español</option>
                                    <option>English</option>
                                    <option>Português</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Perfil
                            </button>

                        </form>

                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 


    <div class="tab-pane fade {{ (session('tab') === 'seguridad' || $errors->has('current_password') || $errors->has('password')) ? 'show active' : '' }}" id="seguridad">
        <div class="row">

            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-key"></i> Cambiar Contraseña</h5>
                    </div>
                    <div class="card-body">

                        @if(session('success_password'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> {{ session('success_password') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Contraseña Actual</label>
                                <div class="input-group">
                                    <input type="password" id="current_password" name="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePass('current_password', 'eye1')">
                                        <i class="fas fa-eye" id="eye1"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="text-danger mt-1" style="font-size:0.82rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="new_password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Mínimo 6 caracteres">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePass('new_password', 'eye2')">
                                        <i class="fas fa-eye" id="eye2"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="text-danger mt-1" style="font-size:0.82rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Confirmar Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="confirm_password" name="password_confirmation"
                                        class="form-control"
                                        placeholder="Repite la nueva contraseña">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePass('confirm_password', 'eye3')">
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

                    {{-- Mensaje éxito toggle --}}
                    @if(session('success_2fa'))
                        <div class="alert alert-success" style="font-size:0.85rem;">
                            <i class="fas fa-check-circle"></i> {{ session('success_2fa') }}
                        </div>
                    @endif

                    {{-- Estado actual --}}
                    <div class="mb-3">
                        @if(Auth::user()->two_factor_enabled)
                            <span class="badge bg-success mb-2">
                                <i class="fas fa-shield-alt"></i> 2FA Activado
                            </span>
                        @else
                            <span class="badge bg-secondary mb-2">
                                <i class="fas fa-shield-alt"></i> 2FA Desactivado
                            </span>
                        @endif
                    </div>

                    {{-- Formulario toggle --}}
                    <form method="POST" action="{{ route('2fa.toggle') }}">
                        @csrf
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="2fa"
                                {{ Auth::user()->two_factor_enabled ? 'checked' : '' }}
                                onchange="this.form.submit()">
                            <label class="form-check-label" for="2fa">
                                {{ Auth::user()->two_factor_enabled ? 'Desactivar 2FA' : 'Activar 2FA' }}
                            </label>
                        </div>
                    </form>

                    {{-- Info --}}
                    <p class="text-muted" style="font-size:0.82rem;">
                        <i class="fas fa-info-circle me-1"></i>
                        Al activarlo, se enviará un código de 6 dígitos a
                        <strong>{{ Auth::user()->email }}</strong> cada vez que inicies sesión.
                    </p>
                </div>
                </div>
            </div> 

        </div> 
    </div> 


    <div class="tab-pane fade" id="notificaciones">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-bell"></i> Preferencias de Notificaciones</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-muted mb-3">Notificaciones por Email</h6>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="n1" checked>
                        <label class="form-check-label" for="n1">Nueva venta realizada</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="n2" checked>
                        <label class="form-check-label" for="n2">Nuevo cliente registrado</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="n3">
                        <label class="form-check-label" for="n3">Factura vencida</label>
                    </div>
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="n4" checked>
                        <label class="form-check-label" for="n4">Mensaje nuevo recibido</label>
                    </div>
                    <hr>
                    <h6 class="text-muted mb-3">Notificaciones del Sistema</h6>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="n5" checked>
                        <label class="form-check-label" for="n5">Alertas de seguridad</label>
                    </div>
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="n6">
                        <label class="form-check-label" for="n6">Reportes semanales</label>
                    </div>
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Preferencias
                    </button>
                </div>
            </div>
        </div>
    </div> 


    <div class="tab-pane fade" id="sistema">
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
                            <span>Laravel</span><span class="text-muted">v11.x</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>PHP</span><span class="text-muted">8.2</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Base de Datos</span><span class="badge bg-success">Conectada</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Último backup</span><span class="text-muted">Hoy 03:00 AM</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-palette"></i> Apariencia</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Tema</label>
                            <select class="form-select">
                                <option selected>Claro (Light)</option>
                                <option>Oscuro (Dark)</option>
                                <option>Automático</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Elementos por página</label>
                            <select class="form-select">
                                <option>10</option>
                                <option selected>25</option>
                                <option>50</option>
                            </select>
                        </div>
                        <button class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Restablecer valores
                        </button>
                    </div>
                </div>
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5><i class="fas fa-exclamation-triangle"></i> Zona de Peligro</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Estas acciones son irreversibles.</p>
                        <button class="btn btn-outline-danger btn-sm me-2">
                            <i class="fas fa-trash"></i> Limpiar caché
                        </button>
                        <button class="btn btn-danger btn-sm">
                            <i class="fas fa-times-circle"></i> Eliminar cuenta
                        </button>
                    </div>
                </div>
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
</script>
@endsection