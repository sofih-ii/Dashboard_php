@extends('layouts.app')

@section('title', 'Mensajes - Dashboard')
@section('side_mensajes', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-envelope text-info"></i> Mensajes <span class="badge bg-danger fs-6">5 nuevos</span></h1>
    <button class="btn btn-sm btn-info text-white"><i class="fas fa-plus"></i> Nuevo Mensaje</button>
</div>

<div class="row mb-4">
    <div class="col-md-3"><div class="card text-white bg-info mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-inbox"></i> Recibidos</h5><p class="card-text display-6">234</p><small class="text-white-50">Total bandeja</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-danger mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-envelope-open"></i> Sin Leer</h5><p class="card-text display-6">5</p><small class="text-white-50">Requieren atención</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-success mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-paper-plane"></i> Enviados</h5><p class="card-text display-6">89</p><small class="text-white-50">Este mes</small></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-secondary mb-3"><div class="card-body"><h5 class="card-title"><i class="fas fa-trash"></i> Archivados</h5><p class="card-text display-6">412</p><small class="text-white-50">Total archivo</small></div></div></div>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Bandeja de Entrada</h5>
                <input type="text" class="form-control form-control-sm w-50" placeholder="Buscar...">
            </div>
            <div class="list-group list-group-flush" style="max-height:420px;overflow-y:auto;">
                <a href="#" class="list-group-item list-group-item-action mensaje-item no-leido p-3">
                    <div class="d-flex justify-content-between"><strong><i class="fas fa-user-circle text-primary"></i> Juan Pérez</strong><small class="text-muted">10:32 AM</small></div>
                    <p class="mb-1 small">Necesito información sobre mi pedido #1045</p>
                    <span class="badge bg-danger">Sin leer</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action mensaje-item no-leido p-3">
                    <div class="d-flex justify-content-between"><strong><i class="fas fa-user-circle text-success"></i> María García</strong><small class="text-muted">09:15 AM</small></div>
                    <p class="mb-1 small">¿Cuándo llega mi envío?</p>
                    <span class="badge bg-danger">Sin leer</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action mensaje-item p-3">
                    <div class="d-flex justify-content-between"><strong><i class="fas fa-user-circle text-warning"></i> Carlos López</strong><small class="text-muted">Ayer</small></div>
                    <p class="mb-1 small text-muted">Gracias por la atención rápida.</p>
                </a>
                <a href="#" class="list-group-item list-group-item-action mensaje-item p-3">
                    <div class="d-flex justify-content-between"><strong><i class="fas fa-user-circle text-info"></i> Ana Martínez</strong><small class="text-muted">Ayer</small></div>
                    <p class="mb-1 small text-muted">Quiero cambiar mi dirección de envío.</p>
                </a>
                <a href="#" class="list-group-item list-group-item-action mensaje-item no-leido p-3">
                    <div class="d-flex justify-content-between"><strong><i class="fas fa-user-circle text-danger"></i> Pedro Sánchez</strong><small class="text-muted">Lun</small></div>
                    <p class="mb-1 small">Factura incorrecta, por favor revisar.</p>
                    <span class="badge bg-danger">Sin leer</span>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-user-circle fa-2x text-primary"></i>
                <div><strong>Juan Pérez</strong><div><small class="text-success"><i class="fas fa-circle" style="font-size:8px"></i> En línea</small></div></div>
                <div class="ms-auto">
                    <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-archive"></i></button>
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="chat-box mb-3">
                    <div class="burbuja-recibido">Hola, necesito información sobre mi pedido #1045.</div>
                    <div class="text-muted small mb-2">10:30 AM</div>
                    <div class="burbuja-enviado">Hola Juan, con mucho gusto te ayudo. ¿Cuál es el inconveniente?</div>
                    <div class="text-muted small mb-2 text-end">10:31 AM</div>
                    <div class="burbuja-recibido">El seguimiento dice que está detenido desde hace 2 días.</div>
                    <div class="text-muted small mb-2">10:32 AM</div>
                </div>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Escribir respuesta...">
                    <button class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection