@extends('layouts.auth')

@section('title', 'Iniciar Sesión - Dashboard')

@section('styles')
<style>
    body { background: #f5f0e8; }
    .auth-page { min-height: 100vh; display: grid; grid-template-columns: 1fr 1fr; }
    .auth-left { background: #1a1a1a; display: flex; flex-direction: column; justify-content: center; align-items: flex-start; padding: 3rem; position: relative; overflow: hidden; }
    .auth-left::before { content: ''; position: absolute; top: -80px; left: -80px; width: 320px; height: 320px; background: var(--card-yellow); border-radius: 50%; opacity: 0.12; }
    .auth-left::after  { content: ''; position: absolute; bottom: -60px; right: -60px; width: 240px; height: 240px; background: var(--card-pink); border-radius: 50%; opacity: 0.15; }
    .auth-brand { font-family: 'DM Serif Display', serif; font-size: 2.8rem; color: #fff; margin-bottom: 0.5rem; position: relative; z-index: 1; }
    .auth-brand span { color: var(--card-yellow); }
    .auth-tagline { font-family: 'DM Sans', sans-serif; font-size: 0.9rem; color: rgba(255,255,255,0.5); margin-bottom: 3rem; position: relative; z-index: 1; }
    .deco-cards { position: relative; z-index: 1; width: 100%; }
    .deco-card { background: rgba(255,255,255,0.07); border-radius: 16px; padding: 1rem 1.2rem; margin-bottom: 0.8rem; display: flex; align-items: center; gap: 12px; border: 1px solid rgba(255,255,255,0.08); }
    .deco-card-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0; }
    .deco-card-title { color: #fff; font-family: 'DM Sans', sans-serif; font-size: 0.78rem; font-weight: 500; }
    .deco-card-sub   { color: rgba(255,255,255,0.4); font-size: 0.7rem; font-family: 'DM Sans', sans-serif; }
    .auth-right { display: flex; align-items: center; justify-content: center; padding: 2rem; }
    .auth-form-wrapper { width: 100%; max-width: 400px; }
    .auth-greeting { font-family: 'DM Serif Display', serif; font-size: 2rem; color: #1a1a1a; margin-bottom: 0.3rem; }
    .auth-sub { font-family: 'DM Sans', sans-serif; font-size: 0.84rem; color: #888; margin-bottom: 2rem; }
    @media (max-width: 768px) { .auth-page { grid-template-columns: 1fr; } .auth-left { display: none; } }
</style>
@endsection

@section('content')
<div class="auth-page">

    <div class="auth-left">
        <div class="auth-brand">
            <i class="fas fa-chart-pie" style="font-size:2rem;margin-bottom:0.5rem;display:block;"></i>
            dash<span>board</span>
        </div>
        <p class="auth-tagline">Tu panel de control completo.<br>Métricas, ventas y clientes en un solo lugar.</p>
        <div class="deco-cards">
            <div class="deco-card">
                <div class="deco-card-icon" style="background:rgba(232,212,77,0.2);"><i class="fas fa-chart-line" style="color:var(--card-yellow)"></i></div>
                <div><div class="deco-card-title">Ventas este mes</div><div class="deco-card-sub">$78,450 — +22% vs anterior</div></div>
            </div>
            <div class="deco-card">
                <div class="deco-card-icon" style="background:rgba(242,167,195,0.2);"><i class="fas fa-users" style="color:var(--card-pink)"></i></div>
                <div><div class="deco-card-title">Clientes activos</div><div class="deco-card-sub">2,340 usuarios registrados</div></div>
            </div>
            <div class="deco-card">
                <div class="deco-card-icon" style="background:rgba(143,187,110,0.2);"><i class="fas fa-file-invoice-dollar" style="color:var(--card-green)"></i></div>
                <div><div class="deco-card-title">Facturas pagadas</div><div class="deco-card-sub">186 este mes — $42,300</div></div>
            </div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-form-wrapper">
            <h1 class="auth-greeting">Buenos días 👋</h1>
            <p class="auth-sub">Ingresa tus credenciales para acceder al panel.</p>

            <ul class="nav nav-tabs mb-4" id="authTab">
                <li class="nav-item"><a class="nav-link {{ !($showRegister ?? false) ? 'active' : '' }}" data-bs-toggle="tab" href="#signin">Iniciar sesión</a></li>
                <li class="nav-item"><a class="nav-link {{ ($showRegister ?? false) ? 'active' : '' }}" data-bs-toggle="tab" href="#signup">Crear cuenta</a></li>
            </ul>

            <div class="tab-content">

                {{-- LOGIN --}}
                <div class="tab-pane fade {{ !($showRegister ?? false) ? 'show active' : '' }}" id="signin">
                    @if ($errors->any())
                        <div class="alert mb-3" style="background:rgba(242,167,195,0.2);border:1.5px solid var(--card-pink);color:#8a0040;border-radius:12px;font-size:0.82rem;padding:0.75rem 1rem;">
                            <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert mb-3" style="background:rgba(143,187,110,0.2);border:1.5px solid var(--card-green);color:#2a6a0a;border-radius:12px;font-size:0.82rem;padding:0.75rem 1rem;">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="email" class="form-control" placeholder="usuario@ejemplo.com" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember" style="font-size:0.8rem;color:#888;text-transform:none;font-weight:400;letter-spacing:0;">Recordar mi sesión</label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2" style="font-size:0.88rem;font-weight:600;">
                                Entrar al panel <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade {{ ($showRegister ?? false) ? 'show active' : '' }}" id="signup">                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" name="name" class="form-control" placeholder="Tu nombre" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="email" class="form-control" placeholder="usuario@ejemplo.com" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Confirmar contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repite la contraseña" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success py-2" style="font-size:0.88rem;font-weight:600;">
                                Crear cuenta <i class="fas fa-user-plus ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection