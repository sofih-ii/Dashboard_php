<?php

// ============================================================
// ARCHIVO: TwoFactorController.php
// QUÉ HACE: Maneja el sistema de doble verificación (2FA).
//   - Activar/desactivar el 2FA desde configuración
//   - Mostrar la pantalla de ingreso del código
//   - Verificar si el código ingresado es correcto
//   - Reenviar un código nuevo al correo
// FLUJO: Login → [si 2FA activo] → showVerify → verify → dashboard
// ============================================================

namespace App\Http\Controllers;

use Illuminate\Http\Request;            // Clase que representa la petición HTTP entrante
use Illuminate\Support\Facades\Auth;   // Fachada para manejar la autenticación del usuario
use Illuminate\Support\Facades\Mail;   // Fachada para enviar correos electrónicos
use App\Mail\TwoFactorCodeMail;         // Clase de correo con la plantilla del código 2FA

class TwoFactorController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // MÉTODO: toggle()
    // RUTA: POST /2fa/toggle
    // QUÉ HACE: Activa o desactiva el 2FA del usuario.
    //           Si estaba true lo pone false, y viceversa.
    // ─────────────────────────────────────────────────────────
    public function toggle(Request $request)
    {
        $user = Auth::user(); // Obtener el usuario que está autenticado actualmente

        $user->update([
            'two_factor_enabled' => !$user->two_factor_enabled,
            // ! invierte el valor booleano: si era true → false, si era false → true
        ]);

        // Preparar el mensaje según el nuevo estado
        $estado = $user->two_factor_enabled ? 'activado' : 'desactivado'; // Operador ternario: condicion ? valor_si_true : valor_si_false

        return back()
            ->with('success_2fa', "2FA $estado correctamente.") // Mensaje de éxito con el nuevo estado
            ->with('tab', 'seguridad'); // Mantener abierta la pestaña "Seguridad" en configuración
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: showVerify()
    // RUTA: GET /2fa/verify
    // QUÉ HACE: Muestra la pantalla donde el usuario debe
    //           ingresar el código de 6 dígitos recibido por email.
    // ─────────────────────────────────────────────────────────
    public function showVerify()
    {
        // Verificar que exista un usuario pendiente de verificar en la sesión
        // (esto se guarda en AuthController al detectar que el usuario tiene 2FA)
        if (!session('2fa_user_id')) {
            return redirect()->route('home'); // Si alguien llega aquí sin haber pasado por el login, redirigir al inicio
        }

        return view('auth.two-factor'); // Mostrar la vista resources/views/auth/two-factor.blade.php
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: verify()
    // RUTA: POST /2fa/verify
    // QUÉ HACE: Verifica el código de 6 dígitos ingresado.
    //           Si es correcto y no expiró, inicia sesión definitivamente.
    // ─────────────────────────────────────────────────────────
    public function verify(Request $request)
    {
        // Validar que el campo 'code' sea exactamente 6 dígitos numéricos
        $request->validate([
            'code' => 'required|digits:6', // digits:6 = exactamente 6 dígitos (no letras, no espacios)
        ], [
            'code.required' => 'El código es obligatorio.',
            'code.digits'   => 'El código debe tener exactamente 6 dígitos.',
        ]);

        $userId = session('2fa_user_id'); // Recuperar el ID del usuario guardado durante el login

        if (!$userId) {
            return redirect()->route('home'); // Seguridad adicional: si no hay ID en sesión, redirigir al inicio
        }

        $user = \App\Models\User::find($userId); // Buscar al usuario en la BD por su ID (sin fallar si no existe)

        // ── VERIFICAR QUE EL CÓDIGO NO HAYA EXPIRADO ──────────
        // now() es la fecha/hora actual. isAfter() comprueba si ya pasó la hora de expiración
        if (now()->isAfter($user->two_factor_expires_at)) {
            return back()->withErrors(['code' => 'El código ha expirado. Vuelve a iniciar sesión.']);
            // El código dura 10 minutos desde que se generó (definido en User::generateTwoFactorCode)
        }

        // ── VERIFICAR QUE EL CÓDIGO SEA CORRECTO ──────────────
        // Comparar el código ingresado con el que está guardado en la BD
        if ($request->code !== $user->two_factor_code) {
            return back()->withErrors(['code' => 'El código ingresado es incorrecto.']);
        }

        // ── CÓDIGO CORRECTO: AUTENTICAR AL USUARIO ─────────────
        $user->clearTwoFactorCode();         // Limpiar el código de la BD (nullear two_factor_code y two_factor_expires_at)
        Auth::login($user);                  // Crear la sesión definitiva del usuario
        $request->session()->forget('2fa_user_id'); // Eliminar el ID temporal de la sesión (ya no se necesita)
        $request->session()->regenerate();   // Regenerar el ID de sesión por seguridad

        return redirect()->route('dashboard'); // Ir al panel principal
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: resend()
    // RUTA: POST /2fa/resend
    // QUÉ HACE: Genera un código nuevo y lo reenvía al correo.
    //           Útil si el usuario no recibió el primer código.
    // ─────────────────────────────────────────────────────────
    public function resend(Request $request)
    {
        $userId = session('2fa_user_id'); // Recuperar el ID del usuario de la sesión

        if (!$userId) {
            return redirect()->route('home'); // Si no hay ID en sesión, no tiene sentido reenviar
        }

        $user = \App\Models\User::find($userId); // Buscar al usuario en la BD
        $user->generateTwoFactorCode();           // Generar un nuevo código de 6 dígitos con nueva expiración (10 min)
        Mail::to($user->email)->send(new TwoFactorCodeMail($user)); // Enviar el nuevo código al correo del usuario

        return back()->with('resent', 'Se envió un nuevo código a tu correo.'); // Regresar con mensaje de confirmación
    }
}
