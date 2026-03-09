<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\TwoFactorCodeMail;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect('/dashboard');
        return view('home');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'Ingresa un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'Mínimo 6 caracteres.',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->has('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // ── Si tiene 2FA activo, interceptar y enviar código ──
            if ($user->two_factor_enabled) {
                session(['2fa_user_id' => $user->id]);
                Auth::logout(); // desautenticar hasta que verifique el código

                $user->generateTwoFactorCode();
                Mail::to($user->email)->send(new TwoFactorCodeMail($user));

                return redirect()->route('2fa.verify');
            }

            return redirect('/dashboard');
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.'])->withInput();
    }

    public function showRegister()
    {
        if (Auth::check()) return redirect('/dashboard');
        return view('home', ['showRegister' => true]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required'      => 'El nombre es obligatorio.',
            'email.required'     => 'El correo es obligatorio.',
            'email.unique'       => 'Este correo ya está registrado.',
            'password.min'       => 'Mínimo 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function updatePerfil(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ], [
            'name.required'  => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique'   => 'Este correo ya está en uso.',
        ]);

        Auth::user()->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'password.min'              => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()
                ->withErrors(['current_password' => 'La contraseña actual es incorrecta.'])
                ->with('tab', 'seguridad');
        }

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()
            ->with('success_password', 'Contraseña actualizada correctamente.')
            ->with('tab', 'seguridad');
    }
}