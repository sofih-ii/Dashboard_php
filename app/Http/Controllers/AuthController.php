<?php

// ============================================================
// ARCHIVO: AuthController.php
// QUÉ HACE: Maneja TODO lo relacionado con identidad del usuario:
//   - Mostrar y procesar el formulario de login
//   - Mostrar y procesar el formulario de registro
//   - Cerrar sesión (logout)
//   - Actualizar el perfil (nombre, email, avatar, etc.)
//   - Cambiar la contraseña
// ============================================================

namespace App\Http\Controllers; // Declara que esta clase pertenece al espacio de nombres Controllers

use Illuminate\Http\Request;            // Clase que representa la petición HTTP (datos del formulario, etc.)
use Illuminate\Support\Facades\Auth;   // Fachada para manejar autenticación: login, logout, usuario actual
use Illuminate\Support\Facades\Hash;   // Fachada para encriptar y verificar contraseñas (bcrypt)
use Illuminate\Support\Facades\Mail;   // Fachada para enviar correos electrónicos
use App\Models\User;                    // Modelo de la tabla 'users' en la base de datos
use App\Mail\TwoFactorCodeMail;         // Clase de correo que contiene la plantilla del código 2FA

class AuthController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // MÉTODO: showLogin()
    // RUTA: GET /
    // QUÉ HACE: Muestra la pantalla de login. Si el usuario ya
    //           tiene sesión activa, lo manda directo al dashboard.
    // ─────────────────────────────────────────────────────────
    public function showLogin()
    {
        if (Auth::check()) return redirect('/dashboard'); // Si ya hay sesión, no tiene sentido mostrar el login → redirigir al panel
        return view('home'); // Si no hay sesión, mostrar la vista resources/views/home.blade.php
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: login()
    // RUTA: POST /login
    // QUÉ HACE: Procesa el formulario de inicio de sesión.
    //           Si el usuario tiene 2FA activo, intercepta el
    //           login y envía un código al correo.
    // ─────────────────────────────────────────────────────────
    public function login(Request $request)
    {
        // Validar que los campos del formulario cumplan las reglas antes de continuar
        // Si algo falla, Laravel regresa automáticamente con mensajes de error
        $request->validate([
            'email'    => 'required|email',   // El email es obligatorio y debe tener formato de correo
            'password' => 'required|min:6',   // La contraseña es obligatoria y mínimo 6 caracteres
        ], [
            // Mensajes de error personalizados en español para cada regla
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'Ingresa un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'Mínimo 6 caracteres.',
        ]);

        // Auth::attempt() intenta autenticar al usuario comparando email+contraseña con la BD
        // $request->only() extrae solo esos dos campos del formulario
        // $request->has('remember') devuelve true si el checkbox "Recordarme" está marcado
        if (Auth::attempt($request->only('email', 'password'), $request->has('remember'))) {
            $request->session()->regenerate(); // Regenerar el ID de sesión para prevenir ataques de fijación de sesión

            $user = Auth::user(); // Obtener el objeto User del usuario que acaba de autenticarse

            // ── VERIFICAR SI TIENE 2FA ACTIVO ──────────────────────
            if ($user->two_factor_enabled) {                         // Si two_factor_enabled = true en la BD
                session(['2fa_user_id' => $user->id]);               // Guardar el ID del usuario en la sesión temporalmente
                Auth::logout();                                       // Cerrar la sesión hasta que verifique el código 2FA

                $user->generateTwoFactorCode();                      // Generar el código de 6 dígitos y guardarlo en la BD con expiración de 10 min
                Mail::to($user->email)->send(new TwoFactorCodeMail($user)); // Enviar el correo con el código al email del usuario

                return redirect()->route('2fa.verify');              // Redirigir a la pantalla de verificación /2fa/verify
            }

            return redirect('/dashboard'); // Si no tiene 2FA, ir directo al dashboard
        }

        // Si las credenciales son incorrectas, regresar al login con mensaje de error
        // withErrors() inyecta el error en la sesión para mostrarlo en la vista
        // withInput() preserva los datos del formulario (para no tener que escribirlos de nuevo)
        return back()->withErrors(['email' => 'Credenciales incorrectas.'])->withInput();
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: showRegister()
    // RUTA: GET /signup
    // QUÉ HACE: Muestra el formulario de registro. Si ya tiene
    //           sesión activa, redirige al dashboard.
    // ─────────────────────────────────────────────────────────
    public function showRegister()
    {
        if (Auth::check()) return redirect('/dashboard'); // Si ya está autenticado, no necesita registrarse
        return view('home', ['showRegister' => true]); // Mostrar home.blade.php pero pasando showRegister=true para que abra el panel de registro
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: register()
    // RUTA: POST /signup
    // QUÉ HACE: Crea una cuenta nueva en la base de datos y
    //           autentica al usuario automáticamente.
    // ─────────────────────────────────────────────────────────
    public function register(Request $request)
    {
        // Validar los campos del formulario de registro
        $request->validate([
            'name'     => 'required|string|max:255',      // Nombre obligatorio, máximo 255 caracteres
            'email'    => 'required|email|unique:users,email', // Email obligatorio, formato correcto, y que NO exista ya en la tabla users
            'password' => 'required|min:6|confirmed',     // Contraseña obligatoria, mínimo 6 caracteres, y debe coincidir con password_confirmation
        ], [
            'name.required'      => 'El nombre es obligatorio.',
            'email.required'     => 'El correo es obligatorio.',
            'email.unique'       => 'Este correo ya está registrado.',
            'password.min'       => 'Mínimo 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // Crear el usuario en la base de datos
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Hash::make encripta la contraseña con bcrypt antes de guardarla
        ]);

        Auth::login($user);         // Autenticar automáticamente al usuario recién creado (crear sesión)
        return redirect('/dashboard'); // Redirigir al panel principal
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: logout()
    // RUTA: POST /logout
    // QUÉ HACE: Cierra la sesión del usuario de forma segura.
    // ─────────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();                          // Eliminar la autenticación del usuario actual
        $request->session()->invalidate();       // Invalidar todos los datos de la sesión actual
        $request->session()->regenerateToken();  // Generar un nuevo token CSRF para proteger futuros formularios
        return redirect('/');                    // Redirigir al login (ruta raíz)
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: updatePerfil()
    // RUTA: PUT /perfil
    // QUÉ HACE: Actualiza los datos del perfil del usuario
    //           autenticado (nombre, email, teléfono, avatar…).
    // ─────────────────────────────────────────────────────────
    public function updatePerfil(Request $request)
    {
        // Validar los campos del formulario de perfil
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . Auth::id(),
            // unique:users,email,{id} ignora el email del propio usuario al verificar unicidad
            // (sin esto, al guardar con el mismo email daría error de "ya existe")
            'phone'    => 'nullable|string|max:20',  // nullable = no es obligatorio (puede estar vacío)
            'timezone' => 'nullable|string|max:60',
            'language' => 'nullable|string|max:10',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            // image = debe ser una imagen, mimes = tipos aceptados, max:2048 = máximo 2 MB
        ], [
            'name.required'  => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique'   => 'Este correo ya está en uso.',
            'avatar.image'   => 'El archivo debe ser una imagen.',
            'avatar.max'     => 'La imagen no puede superar los 2 MB.',
        ]);

        // Extraer solo los campos de texto del formulario (excluye el archivo avatar)
        $data = $request->only(['name', 'email', 'phone', 'timezone', 'language']);

        if ($request->hasFile('avatar')) {
            // Si el usuario subió una imagen, guardarla en storage/app/public/avatars/
            $path = $request->file('avatar')->store('avatars', 'public'); // store() guarda el archivo y retorna la ruta relativa
            $data['avatar'] = $path; // Agregar la ruta del avatar al array de datos a guardar
        }

        Auth::user()->update($data); // Actualizar los campos del usuario autenticado en la BD

        return back()->with('success', 'Perfil actualizado correctamente.'); // Regresar a la misma página con mensaje de éxito
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: updatePassword()
    // RUTA: PUT /password
    // QUÉ HACE: Cambia la contraseña del usuario después de
    //           verificar que la contraseña actual sea correcta.
    // ─────────────────────────────────────────────────────────
    public function updatePassword(Request $request)
    {
        // Validar los campos del formulario de cambio de contraseña
        $request->validate([
            'current_password'      => 'required',           // La contraseña actual es obligatoria
            'password'              => 'required|min:6|confirmed', // Nueva contraseña: mínimo 6 chars y debe coincidir con password_confirmation
            'password_confirmation' => 'required',           // El campo de confirmación también es obligatorio
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'password.min'              => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
        ]);

        // Verificar que la contraseña actual ingresada coincida con la guardada en la BD
        // Hash::check() compara un texto plano con un hash encriptado de forma segura
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()
                ->withErrors(['current_password' => 'La contraseña actual es incorrecta.']) // Agregar error al campo
                ->with('tab', 'seguridad'); // Mantener abierta la pestaña "Seguridad" en la página de configuración
        }

        // Guardar la nueva contraseña encriptada en la base de datos
        Auth::user()->update([
            'password' => Hash::make($request->password), // Hash::make() encripta la nueva contraseña con bcrypt
        ]);

        return back()
            ->with('success_password', 'Contraseña actualizada correctamente.') // Mensaje de éxito específico para contraseña
            ->with('tab', 'seguridad'); // Mantener abierta la pestaña de seguridad
    }
}
