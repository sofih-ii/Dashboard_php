<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA — Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body { background: #f5f0e8; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); padding: 2.5rem; width: 100%; max-width: 420px; }
        .auth-title { font-family: 'DM Serif Display', serif; font-size: 1.8rem; color: #1a1a1a; }
        .code-inputs { display: flex; gap: 0.5rem; justify-content: center; margin: 1.5rem 0; }
        .code-inputs input { width: 48px; height: 56px; text-align: center; font-size: 1.5rem; font-weight: 700; border: 2px solid #e0e0e0; border-radius: 10px; outline: none; transition: border-color 0.2s; }
        .code-inputs input:focus { border-color: #1a1a1a; }
        .btn-verify { background: #1a1a1a; border: none; border-radius: 10px; padding: 0.75rem; font-weight: 600; width: 100%; color: #fff; font-size: 1rem; }
        .btn-verify:hover { background: #333; }
    </style>
</head>
<body>
<div class="auth-card">

    <div class="text-center mb-4">
        <div class="mb-2" style="font-size:2rem;">🔐</div>
        <h1 class="auth-title">Verificación</h1>
        <p class="text-muted" style="font-size:0.88rem;">
            Enviamos un código de 6 dígitos a tu correo electrónico.<br>
            Ingresa el código para continuar.
        </p>
    </div>

    {{-- Mensajes --}}
    @if(session('resent'))
        <div class="alert alert-success" style="font-size:0.85rem;">
            <i class="fas fa-check-circle me-1"></i> {{ session('resent') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" style="font-size:0.85rem;">
            <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
        </div>
    @endif

    {{-- Formulario --}}
    <form method="POST" action="{{ route('2fa.verify') }}" id="verifyForm">
        @csrf

        {{-- Inputs individuales por dígito (UX) --}}
        <div class="code-inputs">
            <input type="text" maxlength="1" class="digit" id="d1" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="digit" id="d2" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="digit" id="d3" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="digit" id="d4" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="digit" id="d5" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="digit" id="d6" inputmode="numeric" pattern="[0-9]">
        </div>

        {{-- Campo oculto con el código completo --}}
        <input type="hidden" name="code" id="codeHidden">

        <button type="submit" class="btn-verify" onclick="joinCode()">
            <i class="fas fa-check-circle me-2"></i>Verificar código
        </button>
    </form>

    <div class="text-center mt-3">
        <form method="POST" action="{{ route('2fa.resend') }}">
            @csrf
            <button type="submit" class="btn btn-link text-muted" style="font-size:0.85rem;">
                <i class="fas fa-redo me-1"></i> Reenviar código
            </button>
        </form>
        <a href="{{ route('home') }}" class="d-block text-muted mt-1" style="font-size:0.82rem;">
            <i class="fas fa-arrow-left me-1"></i> Volver al login
        </a>
    </div>

</div>

<script>
    // Auto-avanzar entre inputs
    const digits = document.querySelectorAll('.digit');
    digits.forEach((input, i) => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/[^0-9]/g, '');
            if (input.value && i < digits.length - 1) {
                digits[i + 1].focus();
            }
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && i > 0) {
                digits[i - 1].focus();
            }
        });
    });

    // Pegar código completo
    digits[0].addEventListener('paste', (e) => {
        const pasted = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
        pasted.split('').forEach((ch, i) => { if (digits[i]) digits[i].value = ch; });
        e.preventDefault();
    });

    // Unir los 6 dígitos antes de enviar
    function joinCode() {
        const code = Array.from(digits).map(d => d.value).join('');
        document.getElementById('codeHidden').value = code;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>