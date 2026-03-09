<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;

class TwoFactorController extends Controller
{
    // ─────────────────────────────────────────────
    //  ACTIVAR / DESACTIVAR 2FA desde configuración
    // ─────────────────────────────────────────────
    public function toggle(Request $request)
    {
        $user = Auth::user();
        $user->update([
            'two_factor_enabled' => !$user->two_factor_enabled,
        ]);

        $estado = $user->two_factor_enabled ? 'activado' : 'desactivado';

        return back()
            ->with('success_2fa', "2FA $estado correctamente.")
            ->with('tab', 'seguridad');
    }

    // ─────────────────────────────────────────────
    //  MOSTRAR FORMULARIO DE VERIFICACIÓN
    // ─────────────────────────────────────────────
    public function showVerify()
    {
        // Si no hay usuario pendiente de 2FA en sesión, redirigir
        if (!session('2fa_user_id')) {
            return redirect()->route('home');
        }

        return view('auth.two-factor');
    }

    // ─────────────────────────────────────────────
    //  VERIFICAR CÓDIGO INGRESADO
    // ─────────────────────────────────────────────
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ], [
            'code.required' => 'El código es obligatorio.',
            'code.digits'   => 'El código debe tener exactamente 6 dígitos.',
        ]);

        $userId = session('2fa_user_id');

        if (!$userId) {
            return redirect()->route('home');
        }

        $user = \App\Models\User::find($userId);

        // Verificar que el código no haya expirado
        if (now()->isAfter($user->two_factor_expires_at)) {
            return back()->withErrors(['code' => 'El código ha expirado. Vuelve a iniciar sesión.']);
        }

        // Verificar que el código sea correcto
        if ($request->code !== $user->two_factor_code) {
            return back()->withErrors(['code' => 'El código ingresado es incorrecto.']);
        }

        // Código correcto: autenticar y limpiar
        $user->clearTwoFactorCode();
        Auth::login($user);
        $request->session()->forget('2fa_user_id');
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    // ─────────────────────────────────────────────
    //  REENVIAR CÓDIGO
    // ─────────────────────────────────────────────
    public function resend(Request $request)
    {
        $userId = session('2fa_user_id');

        if (!$userId) {
            return redirect()->route('home');
        }

        $user = \App\Models\User::find($userId);
        $user->generateTwoFactorCode();
        Mail::to($user->email)->send(new TwoFactorCodeMail($user));

        return back()->with('resent', 'Se envió un nuevo código a tu correo.');
    }
}