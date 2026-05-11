@extends('layouts.app')

@section('title', 'Mensajes - Dashboard')
@section('side_mensajes', 'active')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-envelope text-info"></i> Mensajes
        @if($sinLeer > 0)
            <span class="badge bg-danger">{{ $sinLeer }} sin leer</span>
        @endif
    </h1>
    <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalNuevoMensaje">
        <i class="fas fa-plus"></i> Nuevo Mensaje
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

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
                <h5 class="card-title"><i class="fas fa-archive"></i> Archivados</h5>
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
                <span class="badge bg-secondary">{{ $conversaciones->count() }}</span>
            </div>
            <div class="list-group list-group-flush" style="max-height:450px;overflow-y:auto;">
                @forelse($conversaciones as $conv)
                @php
                    $ultimo  = $conv->mensajes->first();
                    $noLeido = $conv->mensajes->where('leido', false)->where('tipo', 'recibido')->count();
                    $esActivo = $clienteActivo && $clienteActivo->id === $conv->id;
                @endphp
                <a href="{{ route('mensajes.ver', $conv->id) }}"
                   class="list-group-item list-group-item-action p-3 {{ $esActivo ? 'active' : '' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <strong>
                            <i class="fas fa-user-circle me-1"></i>
                            {{ $conv->nombre }} {{ $conv->apellido }}
                        </strong>
                        <small class="{{ $esActivo ? 'text-white-50' : 'text-muted' }}">
                            {{ $ultimo?->created_at->diffForHumans() ?? '' }}
                        </small>
                    </div>
                    <p class="mb-1 small">{{ Str::limit($ultimo?->contenido ?? '—', 50) }}</p>
                    @if($noLeido > 0)
                        <span class="badge bg-danger">{{ $noLeido }} sin leer</span>
                    @endif
                </a>
                @empty
                <div class="p-4 text-muted text-center">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                    No hay conversaciones.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Chat activo --}}
    <div class="col-md-7">
        <div class="card" style="min-height:500px;">
            @if($clienteActivo)
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-user-circle fa-2x text-primary"></i>
                <div class="flex-fill">
                    <strong>{{ $clienteActivo->nombre }} {{ $clienteActivo->apellido }}</strong>
                    <div><small class="text-muted">{{ $clienteActivo->email }}</small></div>
                </div>
                <div class="d-flex gap-1">
                    <a href="{{ route('clientes') }}" class="btn btn-sm btn-outline-primary" title="Ver perfil">
                        <i class="fas fa-user"></i>
                    </a>
                    <form method="POST"
                          action="{{ route('mensajes.conversacion.destroy', $clienteActivo->id) }}"
                          onsubmit="return confirm('¿Eliminar toda la conversación?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar conversación">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body d-flex flex-column">
                {{-- Burbujas de chat --}}
                <div class="chat-box flex-fill mb-3" id="chatBox"
                     style="max-height:340px;overflow-y:auto;padding:8px;">
                    @forelse($mensajesActivos as $msg)
                        @if($msg->tipo === 'recibido')
                            <div class="d-flex mb-2">
                                <div class="burbuja-recibido p-2 px-3 rounded-3 me-auto"
                                     style="max-width:75%;background:#e9ecef;">
                                    {{ $msg->contenido }}
                                </div>
                            </div>
                            <div class="text-muted small mb-3" style="padding-left:4px;">
                                {{ $msg->created_at->format('h:i A') }}
                            </div>
                        @else
                            <div class="d-flex mb-2 justify-content-end">
                                <div class="burbuja-enviado p-2 px-3 rounded-3 ms-auto text-white"
                                     style="max-width:75%;background:#0d6efd;">
                                    {{ $msg->contenido }}
                                </div>
                            </div>
                            <div class="text-muted small mb-3 text-end" style="padding-right:4px;">
                                {{ $msg->created_at->format('h:i A') }}
                            </div>
                        @endif
                    @empty
                        <div class="text-muted text-center py-4">
                            <i class="fas fa-comment-dots fa-2x mb-2 d-block"></i>
                            Sin mensajes aún. ¡Envía el primero!
                        </div>
                    @endforelse
                </div>

                {{-- Formulario de respuesta --}}
                <form method="POST" action="{{ route('mensajes.send') }}" class="mt-auto">
                    @csrf
                    <input type="hidden" name="cliente_id" value="{{ $clienteActivo->id }}">
                    <div class="input-group">
                        <input type="text" name="contenido" class="form-control"
                               placeholder="Escribe una respuesta..."
                               required maxlength="2000" autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Enviar
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="card-body text-center text-muted d-flex flex-column align-items-center justify-content-center" style="min-height:400px;">
                <i class="fas fa-comments fa-4x mb-3 text-secondary"></i>
                <h5>Selecciona una conversación</h5>
                <p class="small">O crea un nuevo mensaje con el botón de arriba.</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Nuevo Mensaje --}}
<div class="modal fade" id="modalNuevoMensaje" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-1"></i> Nuevo Mensaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('mensajes.nuevo') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Destinatario <span class="text-danger">*</span></label>
                        <select name="cliente_id" class="form-select" required>
                            <option value="">— Selecciona un cliente —</option>
                            @foreach($todosClientes as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }} {{ $c->apellido }} ({{ $c->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mensaje <span class="text-danger">*</span></label>
                        <textarea name="contenido" class="form-control" rows="4"
                                  placeholder="Escribe tu mensaje..." required maxlength="2000"></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-paper-plane"></i> Enviar Mensaje
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
    // Scroll al fondo del chat al cargar
    const chatBox = document.getElementById('chatBox');
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>
@endsection
