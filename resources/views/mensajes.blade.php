@extends('layouts.app')

@section('title', 'Mensajes - Dashboard')
@section('side_mensajes', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-envelope text-info"></i> Mensajes
        @if($sinLeer > 0)
            <span class="badge bg-danger fs-6">{{ $sinLeer }} nuevos</span>
        @endif
    </h1>
    <button class="btn btn-sm btn-info text-white"><i class="fas fa-plus"></i> Nuevo Mensaje</button>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-inbox"></i> Recibidos</h5>
                <p class="card-text display-6">{{ $recibidos }}</p>
                <small class="text-white-50">Total bandeja</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-envelope-open"></i> Sin Leer</h5>
                <p class="card-text display-6">{{ $sinLeer }}</p>
                <small class="text-white-50">Requieren atención</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-paper-plane"></i> Enviados</h5>
                <p class="card-text display-6">{{ $enviados }}</p>
                <small class="text-white-50">Por el admin</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-secondary mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-trash"></i> Archivados</h5>
                <p class="card-text display-6">{{ $archivados }}</p>
                <small class="text-white-50">Total archivo</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Lista de conversaciones --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Bandeja de Entrada</h5>
            </div>
            <div class="list-group list-group-flush" style="max-height:420px;overflow-y:auto;">
                @forelse($conversaciones as $conv)
                @php
                    $ultimo  = $conv->mensajes->first();
                    $noLeido = $conv->mensajes->where('leido', false)->where('tipo', 'recibido')->count();
                @endphp
                <a href="{{ route('mensajes.ver', $conv->id) }}"
                   class="list-group-item list-group-item-action p-3 {{ $clienteActivo && $clienteActivo->id === $conv->id ? 'active' : '' }}">
                    <div class="d-flex justify-content-between">
                        <strong>
                            <i class="fas fa-user-circle"></i>
                            {{ $conv->nombre }} {{ $conv->apellido }}
                        </strong>
                        <small>{{ $ultimo?->created_at->diffForHumans() ?? '' }}</small>
                    </div>
                    <p class="mb-1 small">{{ Str::limit($ultimo?->contenido ?? '—', 50) }}</p>
                    @if($noLeido > 0)
                        <span class="badge bg-danger">{{ $noLeido }} sin leer</span>
                    @endif
                </a>
                @empty
                <div class="p-3 text-muted text-center">No hay conversaciones.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Chat activo --}}
    <div class="col-md-7">
        <div class="card">
            @if($clienteActivo)
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-user-circle fa-2x text-primary"></i>
                <div>
                    <strong>{{ $clienteActivo->nombre }} {{ $clienteActivo->apellido }}</strong>
                    <div><small class="text-muted">{{ $clienteActivo->email }}</small></div>
                </div>
                <div class="ms-auto">
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="chat-box mb-3" style="max-height:320px;overflow-y:auto;">
                    @foreach($mensajesActivos as $msg)
                        @if($msg->tipo === 'recibido')
                            <div class="burbuja-recibido mb-1">{{ $msg->contenido }}</div>
                            <div class="text-muted small mb-2">{{ $msg->created_at->format('h:i A') }}</div>
                        @else
                            <div class="burbuja-enviado mb-1">{{ $msg->contenido }}</div>
                            <div class="text-muted small mb-2 text-end">{{ $msg->created_at->format('h:i A') }}</div>
                        @endif
                    @endforeach
                </div>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Escribir respuesta...">
                    <button class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar</button>
                </div>
            </div>
            @else
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-comments fa-3x mb-3"></i>
                <p>Selecciona una conversación</p>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection