@extends('layouts.app')

@section('title', 'Nosotros - Dashboard')
@section('nav_nosotros', 'active')
@section('side_nosotros', 'active')

@section('styles')
<style>
    .hero-nosotros { background:#1a1a1a; border-radius:20px; padding:3rem; margin-bottom:2rem; position:relative; overflow:hidden; display:flex; align-items:center; justify-content:space-between; gap:2rem; }
    .hero-nosotros::before { content:''; position:absolute; top:-60px; right:-60px; width:260px; height:260px; background:var(--card-yellow); border-radius:50%; opacity:0.08; }
    .hero-nosotros::after  { content:''; position:absolute; bottom:-40px; left:30%; width:180px; height:180px; background:var(--card-pink); border-radius:50%; opacity:0.1; }
    .hero-text { position:relative; z-index:1; }
    .hero-text h1 { font-family:'DM Serif Display',serif; font-size:2.4rem; color:#fff; margin-bottom:0.5rem; }
    .hero-text h1 span { color:var(--card-yellow); }
    .hero-text p { font-size:0.9rem; color:rgba(255,255,255,0.55); max-width:500px; margin:0; }
    .hero-badges { display:flex; gap:0.6rem; margin-top:1.2rem; flex-wrap:wrap; }
    .hero-badge { background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.12); color:rgba(255,255,255,0.7); font-family:'DM Sans',sans-serif; font-size:0.72rem; font-weight:500; padding:0.35rem 0.85rem; border-radius:20px; }
    .video-wrapper { position:relative; border-radius:16px; overflow:hidden; box-shadow:0 8px 32px rgba(0,0,0,0.12); }
    .video-wrapper iframe { display:block; width:100%; aspect-ratio:16/9; border:none; }
    .video-label { position:absolute; top:12px; left:12px; background:rgba(232,212,77,0.95); color:#1a1a1a; font-family:'DM Sans',sans-serif; font-size:0.68rem; font-weight:700; padding:0.25rem 0.7rem; border-radius:20px; z-index:2; }
    .article-card { background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.07); transition:all 0.25s ease; height:100%; }
    .article-card:hover { transform:translateY(-4px); box-shadow:0 10px 32px rgba(0,0,0,0.12); }
    .article-card img { width:100%; height:160px; object-fit:cover; }
    .article-card-body { padding:1.1rem 1.2rem 1.3rem; }
    .article-tag { font-family:'DM Sans',sans-serif; font-size:0.65rem; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; padding:0.25rem 0.7rem; border-radius:20px; display:inline-block; margin-bottom:0.6rem; }
    .tag-yellow { background:rgba(232,212,77,0.2); color:#6a5800; }
    .tag-pink   { background:rgba(242,167,195,0.2); color:#8a0040; }
    .tag-green  { background:rgba(143,187,110,0.2); color:#2a6a0a; }
    .tag-blue   { background:rgba(168,200,232,0.2); color:#0a3060; }
    .article-card-body h6 { font-family:'DM Serif Display',serif; font-size:1rem; color:#1a1a1a; margin-bottom:0.4rem; line-height:1.35; }
    .article-card-body p { font-size:0.78rem; color:#888; margin:0; line-height:1.5; }
    .article-meta { display:flex; align-items:center; justify-content:space-between; margin-top:0.9rem; padding-top:0.9rem; border-top:1px solid rgba(0,0,0,0.06); }
    .article-meta-left { font-size:0.72rem; color:#aaa; font-family:'DM Sans',sans-serif; }
    .team-card { background:#fff; border-radius:20px; padding:2rem 1.5rem; text-align:center; box-shadow:0 4px 20px rgba(0,0,0,0.07); transition:all 0.25s ease; position:relative; overflow:hidden; }
    .team-card::before { content:''; position:absolute; top:0; left:0; right:0; height:80px; border-radius:20px 20px 0 0; }
    .team-card.tc-yellow::before { background:var(--card-yellow); }
    .team-card.tc-pink::before   { background:var(--card-pink); }
    .team-card:hover { transform:translateY(-5px); box-shadow:0 12px 36px rgba(0,0,0,0.12); }
    .team-avatar { width:80px; height:80px; border-radius:50%; border:4px solid #fff; box-shadow:0 4px 16px rgba(0,0,0,0.12); position:relative; z-index:1; margin:0 auto 1rem; display:flex; align-items:center; justify-content:center; font-size:2rem; font-family:'DM Serif Display',serif; }
    .team-card.tc-yellow .team-avatar { background:var(--card-yellow); color:#6a5800; }
    .team-card.tc-pink   .team-avatar { background:var(--card-pink);   color:#8a0040; }
    .team-name { font-family:'DM Serif Display',serif; font-size:1.1rem; color:#1a1a1a; margin-bottom:0.2rem; }
    .team-role { font-family:'DM Sans',sans-serif; font-size:0.75rem; color:#aaa; font-weight:500; margin-bottom:0.8rem; }
    .team-desc { font-size:0.78rem; color:#888; line-height:1.5; }
    .stat-pill { background:#fff; border-radius:14px; padding:1rem 1.3rem; display:flex; align-items:center; gap:12px; box-shadow:0 2px 12px rgba(0,0,0,0.06); }
    .stat-pill-icon { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
    .stat-pill-num { font-family:'DM Serif Display',serif; font-size:1.5rem; color:#1a1a1a; line-height:1; }
    .stat-pill-label { font-family:'DM Sans',sans-serif; font-size:0.72rem; color:#aaa; }
    .section-title { font-family:'DM Serif Display',serif; font-size:1.5rem; color:#1a1a1a; margin-bottom:0.2rem; }
    .section-sub { font-size:0.8rem; color:#aaa; font-family:'DM Sans',sans-serif; margin-bottom:1.5rem; }
    .pqrs-wrapper { background:#1a1a1a; border-radius:20px; padding:2.5rem; position:relative; overflow:hidden; }
    .pqrs-wrapper::before { content:''; position:absolute; bottom:-60px; right:-60px; width:200px; height:200px; background:var(--card-yellow); border-radius:50%; opacity:0.07; }
    .pqrs-title { font-family:'DM Serif Display',serif; font-size:1.8rem; color:#fff; margin-bottom:0.4rem; }
    .pqrs-sub { font-size:0.82rem; color:rgba(255,255,255,0.45); margin-bottom:2rem; }
    .pqrs-type-btn { background:rgba(255,255,255,0.07); border:1.5px solid rgba(255,255,255,0.1); color:rgba(255,255,255,0.6); font-family:'DM Sans',sans-serif; font-size:0.78rem; font-weight:500; padding:0.6rem 1.2rem; border-radius:10px; cursor:pointer; transition:all 0.2s; text-align:center; }
    .pqrs-type-btn:hover, .pqrs-type-btn.active { border-color:var(--card-yellow); background:rgba(232,212,77,0.12); color:var(--card-yellow); }
    .pqrs-type-btn i { display:block; font-size:1.2rem; margin-bottom:0.3rem; }
    .pqrs-input { background:rgba(255,255,255,0.06) !important; border:1.5px solid rgba(255,255,255,0.1) !important; color:#fff !important; border-radius:12px !important; font-family:'DM Sans',sans-serif !important; font-size:0.84rem !important; }
    .pqrs-input:focus { background:rgba(255,255,255,0.09) !important; border-color:var(--card-yellow) !important; box-shadow:0 0 0 3px rgba(232,212,77,0.15) !important; color:#fff !important; }
    .pqrs-input::placeholder { color:rgba(255,255,255,0.3) !important; }
    .pqrs-label { font-family:'DM Sans',sans-serif; font-size:0.72rem; font-weight:600; color:rgba(255,255,255,0.5); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.4rem; }
    /* ── Tabla PQRS ── */
    .pqrs-table-wrapper { background:#fff; border-radius:16px; padding:1.8rem; box-shadow:0 4px 20px rgba(0,0,0,0.07); margin-top:2rem; }
    .pqrs-table-title { font-family:'DM Serif Display',serif; font-size:1.3rem; color:#1a1a1a; margin-bottom:0.2rem; }
    .pqrs-table-sub { font-size:0.78rem; color:#aaa; font-family:'DM Sans',sans-serif; margin-bottom:1.2rem; }
    .badge-peticion   { background:rgba(168,200,232,0.2); color:#0a3060; }
    .badge-queja      { background:rgba(242,167,195,0.2); color:#8a0040; }
    .badge-reclamo    { background:rgba(255,100,100,0.15); color:#8b0000; }
    .badge-sugerencia { background:rgba(143,187,110,0.2); color:#2a6a0a; }
</style>
@endsection

@section('content')


@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert" style="border-radius:12px;border:none;background:rgba(143,187,110,0.15);color:#1a6b3a;">
    <i class="fas fa-check-circle"></i>
    <strong>{{ session('success') }}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert" style="border-radius:12px;border:none;background:rgba(220,53,69,0.1);color:#8b1a1a;">
    <i class="fas fa-exclamation-circle me-2"></i>
    <strong>Por favor corrige los siguientes errores:</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@section('content')


<div class="hero-nosotros">
    <div class="hero-text">
        <h1>Conoce el <span>proyecto</span></h1>
        <p>Dashboard académico desarrollado con Laravel, Bootstrap 5 y diseño Soft UI. Panel de control completo con métricas, ventas, clientes y análisis.</p>
        <div class="hero-badges">
            <span class="hero-badge"><i class="fas fa-code me-1"></i> Laravel 11</span>
            <span class="hero-badge"><i class="fas fa-database me-1"></i> PostgreSQL</span>
            <span class="hero-badge"><i class="fas fa-layer-group me-1"></i> Bootstrap 5</span>
            <span class="hero-badge"><i class="fas fa-graduation-cap me-1"></i> Proyecto Académico</span>
        </div>
    </div>
    <div style="flex-shrink:0;text-align:right;position:relative;z-index:1;">
        <div style="font-family:'DM Serif Display',serif;font-size:4rem;color:rgba(255,255,255,0.08);line-height:1;">&lt;/&gt;</div>
        <div style="font-family:'DM Sans',sans-serif;font-size:0.72rem;color:rgba(255,255,255,0.3);letter-spacing:2px;text-transform:uppercase;">Programación Avanzada</div>
    </div>
</div>

+
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="stat-pill"><div class="stat-pill-icon" style="background:rgba(232,212,77,0.15);"><i class="fas fa-route" style="color:var(--card-yellow)"></i></div><div><div class="stat-pill-num">11</div><div class="stat-pill-label">Rutas activas</div></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-pill"><div class="stat-pill-icon" style="background:rgba(242,167,195,0.15);"><i class="fas fa-file-code" style="color:var(--card-pink)"></i></div><div><div class="stat-pill-num">11</div><div class="stat-pill-label">Vistas Blade</div></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-pill"><div class="stat-pill-icon" style="background:rgba(143,187,110,0.15);"><i class="fas fa-database" style="color:var(--card-green)"></i></div><div><div class="stat-pill-num">4</div><div class="stat-pill-label">Modelos BD</div></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-pill"><div class="stat-pill-icon" style="background:rgba(168,200,232,0.15);"><i class="fas fa-users" style="color:var(--card-blue)"></i></div><div><div class="stat-pill-num">2</div><div class="stat-pill-label">Integrantes</div></div></div></div>
</div>


<div class="row g-4 mb-4">
    <div class="col-md-7">
        <p class="section-title">Tutorial del proyecto</p>
        <p class="section-sub">Introducción a Laravel — Rutas, Vistas y Blade</p>
        <div class="video-wrapper">
            <span class="video-label"><i class="fas fa-play me-1"></i> Video</span>
            <iframe src="https://www.youtube.com/embed/MYyJ4PuL4pY" title="Laravel Tutorial" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
    <div class="col-md-5 d-flex flex-column gap-3">
        <p class="section-title mb-0">Tecnologías usadas</p>
        <p class="section-sub mb-0">Stack completo del proyecto</p>
        <div class="article-card" style="flex:1;"><div class="article-card-body"><span class="article-tag tag-yellow"><i class="fas fa-server me-1"></i> Backend</span><h6>Laravel 11 + PHP 8</h6><p>Framework PHP con arquitectura MVC, rutas con nombre, Blade templating y Eloquent ORM para PostgreSQL.</p><div class="article-meta"><span class="article-meta-left"><i class="fas fa-clock"></i> Semestre 2025</span><span class="badge" style="background:rgba(232,212,77,0.2);color:#6a5800;border-radius:20px;font-size:0.65rem;">MVC</span></div></div></div>
        <div class="article-card" style="flex:1;"><div class="article-card-body"><span class="article-tag tag-blue"><i class="fas fa-paint-brush me-1"></i> Frontend</span><h6>Bootstrap 5 + Soft UI</h6><p>Sistema responsive con paleta pastel, tipografía DM Serif Display y componentes Card interactivos.</p><div class="article-meta"><span class="article-meta-left"><i class="fas fa-clock"></i> CDN</span><span class="badge" style="background:rgba(168,200,232,0.2);color:#0a3060;border-radius:20px;font-size:0.65rem;">Responsive</span></div></div></div>
    </div>
</div>


<div class="row g-3 mb-4">
    <div class="col-12"><p class="section-title">Galería del proyecto</p><p class="section-sub">Capturas y recursos visuales del dashboard</p></div>
    <div class="col-md-4"><div class="article-card"><img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&q=80" alt="Dashboard"><div class="article-card-body"><span class="article-tag tag-yellow">Dashboard</span><h6>Panel de Analíticas</h6><p>Vista principal con métricas de usuarios, ventas y crecimiento en tiempo real.</p></div></div></div>
    <div class="col-md-4"><div class="article-card"><img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&q=80" alt="Ventas"><div class="article-card-body"><span class="article-tag tag-green">Ventas</span><h6>Gestión de Ventas</h6><p>Módulo de seguimiento de órdenes y gráficos de facturación mensual.</p></div></div></div>
    <div class="col-md-4"><div class="article-card"><img src="https://images.unsplash.com/photo-1553877522-43269d4ea984?w=600&q=80" alt="Clientes"><div class="article-card-body"><span class="article-tag tag-pink">Clientes</span><h6>Gestión de Clientes</h6><p>Administración con segmentación y sistema de búsqueda avanzada.</p></div></div></div>
</div>


<div class="row g-3 mb-4">
    <div class="col-12"><p class="section-title">Equipo de desarrollo</p><p class="section-sub">Integrantes del grupo — Programación Avanzada 2025</p></div>
    <div class="col-md-6">
        <div class="team-card tc-yellow">
            <div class="team-avatar">A</div>
            <div class="team-name">Nombre Integrante 1</div>
            <div class="team-role">Desarrollador Frontend · Diseño UI/UX</div>
            <p class="team-desc">Responsable del diseño visual, estructura de vistas Blade, integración de Bootstrap y estilización Soft UI.</p>
            <div class="d-flex justify-content-center gap-2 mt-3"><span class="article-tag tag-yellow">Blade</span><span class="article-tag tag-yellow">CSS</span><span class="article-tag tag-yellow">Bootstrap</span></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="team-card tc-pink">
            <div class="team-avatar">B</div>
            <div class="team-name">Nombre Integrante 2</div>
            <div class="team-role">Desarrollador Backend · Base de Datos</div>
            <p class="team-desc">Responsable de rutas Laravel, controladores, modelos Eloquent y conexión con PostgreSQL.</p>
            <div class="d-flex justify-content-center gap-2 mt-3"><span class="article-tag tag-pink">Laravel</span><span class="article-tag tag-pink">PHP</span><span class="article-tag tag-pink">PostgreSQL</span></div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="pqrs-wrapper">
            <div class="row align-items-start">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h2 class="pqrs-title">¿Tienes algo que decirnos?</h2>
                    <p class="pqrs-sub">Envíanos tu Petición, Queja, Reclamo o Sugerencia. Te responderemos en menos de 24 horas.</p>
                    <div class="d-flex flex-column gap-3 mt-3">
                        <div style="display:flex;align-items:center;gap:10px;"><div style="width:36px;height:36px;border-radius:10px;background:rgba(232,212,77,0.15);display:flex;align-items:center;justify-content:center;"><i class="fas fa-clock" style="color:var(--card-yellow);font-size:0.85rem;"></i></div><div><div style="font-family:'DM Sans',sans-serif;font-size:0.75rem;color:rgba(255,255,255,0.7);font-weight:500;">Respuesta rápida</div><div style="font-size:0.68rem;color:rgba(255,255,255,0.35);">Menos de 24 horas</div></div></div>
                        <div style="display:flex;align-items:center;gap:10px;"><div style="width:36px;height:36px;border-radius:10px;background:rgba(242,167,195,0.15);display:flex;align-items:center;justify-content:center;"><i class="fas fa-shield-alt" style="color:var(--card-pink);font-size:0.85rem;"></i></div><div><div style="font-family:'DM Sans',sans-serif;font-size:0.75rem;color:rgba(255,255,255,0.7);font-weight:500;">100% Confidencial</div><div style="font-size:0.68rem;color:rgba(255,255,255,0.35);">Tus datos están protegidos</div></div></div>
                        <div style="display:flex;align-items:center;gap:10px;"><div style="width:36px;height:36px;border-radius:10px;background:rgba(143,187,110,0.15);display:flex;align-items:center;justify-content:center;"><i class="fas fa-check-circle" style="color:var(--card-green);font-size:0.85rem;"></i></div><div><div style="font-family:'DM Sans',sans-serif;font-size:0.75rem;color:rgba(255,255,255,0.7);font-weight:500;">Seguimiento garantizado</div><div style="font-size:0.68rem;color:rgba(255,255,255,0.35);">Número de radicado asignado</div></div></div>
                    </div>
                </div>
                <div class="col-md-8">
                    <label class="pqrs-label mb-2">Tipo de solicitud</label>
                    <div class="row g-2 mb-4">
                        <div class="col-6 col-md-3"><div class="pqrs-type-btn" onclick="selectTipo(this,'peticion')"><i class="fas fa-hand-paper"></i>Petición</div></div>
                        <div class="col-6 col-md-3"><div class="pqrs-type-btn" onclick="selectTipo(this,'queja')"><i class="fas fa-exclamation-triangle"></i>Queja</div></div>
                        <div class="col-6 col-md-3"><div class="pqrs-type-btn" onclick="selectTipo(this,'reclamo')"><i class="fas fa-times-circle"></i>Reclamo</div></div>
                        <div class="col-6 col-md-3"><div class="pqrs-type-btn active" onclick="selectTipo(this,'sugerencia')"><i class="fas fa-lightbulb"></i>Sugerencia</div></div>
                    </div>


                    <form method="POST" action="{{ route('pqrs.store') }}">
                        @csrf

                        <input type="hidden" name="tipo" id="tipo_hidden" value="sugerencia">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="pqrs-label">Nombre completo</label>
                                <input type="text" name="nombre" class="form-control pqrs-input"
                                    placeholder="Tu nombre" value="{{ old('nombre') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="pqrs-label">Correo electrónico</label>
                                <input type="email" name="email" class="form-control pqrs-input"
                                    placeholder="correo@ejemplo.com" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="pqrs-label">Mensaje / Descripción detallada</label>
                                <textarea name="mensaje" class="form-control pqrs-input" rows="4"
                                    placeholder="Describe tu solicitud con el mayor detalle posible..." required>{{ old('mensaje') }}</textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-4 py-2"
                                    style="font-size:0.88rem;font-weight:600;">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar solicitud
                                </button>
                                <span style="font-size:0.72rem;color:rgba(255,255,255,0.3);margin-left:1rem;">
                                    <i class="fas fa-lock me-1"></i>Información protegida
                                </span>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="pqrs-table-wrapper">
            <p class="pqrs-table-title">
                <i class="fas fa-table me-2" style="color:var(--card-yellow);"></i>
                Solicitudes Registradas
            </p>
            <p class="pqrs-table-sub">
                Todos los PQRS enviados — {{ $pqrs->count() }} registro(s) en total
            </p>

            @if($pqrs->isEmpty())
                {{-- Mensaje cuando no hay registros --}}
                <div class="text-center py-5" style="color:#bbb;">
                    <i class="fas fa-inbox" style="font-size:2.5rem;margin-bottom:1rem;display:block;"></i>
                    <p style="font-family:'DM Sans',sans-serif;font-size:0.88rem;">
                        Aún no hay solicitudes registradas. ¡Sé el primero en enviar una!
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="font-family:'DM Sans',sans-serif;font-size:0.85rem;">
                        <thead>
                            <tr style="border-bottom:2px solid #f0f0f0;">
                                <th style="color:#aaa;font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.5px;">#</th>
                                <th style="color:#aaa;font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.5px;">Nombre</th>
                                <th style="color:#aaa;font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.5px;">Correo</th>
                                <th style="color:#aaa;font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.5px;">Tipo</th>
                                <th style="color:#aaa;font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.5px;">Mensaje</th>
                                <th style="color:#aaa;font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.5px;">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pqrs as $item)
                            <tr style="border-bottom:1px solid #f8f8f8;">
                                <td style="color:#bbb;font-size:0.75rem;">{{ $loop->iteration }}</td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div style="width:34px;height:34px;border-radius:50%;background:var(--card-yellow);display:flex;align-items:center;justify-content:center;font-family:'DM Serif Display',serif;font-size:0.9rem;color:#6a5800;flex-shrink:0;">
                                            {{ strtoupper(substr($item->nombre, 0, 1)) }}
                                        </div>
                                        <span style="font-weight:600;color:#1a1a1a;">{{ $item->nombre }}</span>
                                    </div>
                                </td>
                                <td style="color:#888;">{{ $item->email }}</td>
                                <td>
                                    <span class="badge rounded-pill badge-{{ $item->tipo }}" style="font-size:0.72rem;padding:0.35rem 0.8rem;font-weight:600;">
                                        @if($item->tipo === 'peticion')   <i class="fas fa-hand-paper me-1"></i>Petición
                                        @elseif($item->tipo === 'queja')  <i class="fas fa-exclamation-triangle me-1"></i>Queja
                                        @elseif($item->tipo === 'reclamo')<i class="fas fa-times-circle me-1"></i>Reclamo
                                        @else                             <i class="fas fa-lightbulb me-1"></i>Sugerencia
                                        @endif
                                    </span>
                                </td>
                                <td style="color:#888;max-width:280px;">
                                    <span title="{{ $item->mensaje }}">
                                        {{ Str::limit($item->mensaje, 60) }}
                                    </span>
                                </td>
                                <td style="color:#bbb;font-size:0.78rem;white-space:nowrap;">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $item->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>

function selectTipo(el, valor) {
    document.querySelectorAll('.pqrs-type-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('tipo_hidden').value = valor;
}
</script>
@endsection