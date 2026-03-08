@extends('layouts.auth')

@section('title', 'Registro - Dashboard')

@section('styles')
<style>
    body { background: #f5f0e8; }
    .card-auth { max-width: 440px; margin: 4rem auto; border-radius: 20px !important; box-shadow: 0 8px 32px rgba(0,0,0,0.09) !important; }
    .card-auth .card-body { padding: 2.5rem; }
    .signup-header { text-align: center; margin-bottom: 2rem; }
    .signup-header i { font-size: 2.5rem; color: var(--card-yellow); margin-bottom: 0.5rem; display: block; }
    .signup-header h5 { font-family: 'DM Serif Display', serif; font-size: 1.6rem; color: #1a1a1a; margin-bottom: 0.3rem; }
    .signup-header p { font-family: 'DM Sans', sans-serif; font-size: 0.82rem; color: #aaa; margin: 0; }
</style>
@endsection

@section('content')

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/"><i class="fas fa-chart-pie"></i> Dashboard</a>
        <a class="btn btn-sm btn-outline-light ms-auto" href="/"><i class="fas fa-arrow-left me-1"></i> Volver al login</a>
    </div>
</nav>

<div class="container">
    <div class="card card-auth">
        <div class="card-body">

            <div class="signup-header">
                <i class="fas fa-user-plus"></i>
                <h5>Crear cuenta nueva</h5>
                <p>Completa el formulario para registrarte</p>
            </div>

            @if ($errors->any())
                <div class="alert mb-3" style="background:rgba(242,167,195,0.2);border:1.5px solid var(--card-pink);color:#8a0040;border-radius:12px;font-size:0.82rem;padding:0.75rem 1rem;">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nombre completo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" class="form-control" placeholder="Tu nombre completo" value="{{ old('name') }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="usuario@ejemplo.com" value="{{ old('email') }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Confirmar contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repite la contraseña" required>
                    </div>
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-success py-2" style="font-size:0.88rem;font-weight:600;">
                        <i class="fas fa-user-plus me-2"></i>Crear cuenta
                    </button>
                </div>
            </form>

            <p class="text-center mb-0" style="font-size:0.82rem;color:#aaa;">
                ¿Ya tienes una cuenta?
                <a href="{{ route('home') }}" style="color:var(--card-yellow);font-weight:600;text-decoration:none;">Iniciar sesión</a>
            </p>

        </div>
    </div>
</div>

@endsection