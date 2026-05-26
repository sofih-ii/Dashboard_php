<?php

// ============================================================
// ARCHIVO: app/Models/User.php
// QUÉ ES: El modelo del usuario autenticado. Representa la
//         tabla 'users' en la base de datos.
// ESPECIAL: Extiende de Authenticatable (no de Model) porque
//           Laravel necesita métodos especiales para el login.
// ============================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Permite crear usuarios de prueba con factories
use Illuminate\Foundation\Auth\User as Authenticatable; // Clase base de Laravel para usuarios autenticables
use Illuminate\Notifications\Notifiable;               // Permite enviar notificaciones al usuario

class User extends Authenticatable // Hereda de Authenticatable (no de Model como los demás modelos)
{
    use HasFactory, Notifiable;
    // HasFactory  → permite usar User::factory() para crear datos de prueba
    // Notifiable  → permite enviar notificaciones (emails, push, etc.) al usuario

    // ─────────────────────────────────────────────────────────
    // $fillable - CAMPOS PERMITIDOS PARA ASIGNACIÓN MASIVA
    // Solo los campos listados aquí pueden usarse con create() o update()
    // Es una medida de seguridad: evita que alguien inyecte campos
    // no deseados (ej: is_admin=true) a través de un formulario
    // ─────────────────────────────────────────────────────────
    protected $fillable = [
        'name',                    // Nombre completo del usuario
        'email',                   // Correo electrónico (único)
        'password',                // Contraseña (se guarda encriptada con bcrypt)
        'phone',                   // Teléfono (opcional)
        'avatar',                  // Ruta de la imagen de perfil en storage/
        'timezone',                // Zona horaria (ej: 'America/Bogota')
        'language',                // Idioma preferido (ej: 'es', 'en')
        'notification_settings',   // Configuración de notificaciones en JSON
        'theme',                   // Tema visual preferido (ej: 'light', 'dark')
        'per_page',                // Registros por página en las listas
        'two_factor_enabled',      // Boolean: si el 2FA está activado o no
        'two_factor_code',         // El código de 6 dígitos del 2FA (temporal)
        'two_factor_expires_at',   // Fecha/hora de expiración del código 2FA
    ];

    // ─────────────────────────────────────────────────────────
    // $hidden - CAMPOS OCULTOS EN RESPUESTAS JSON
    // Estos campos NO aparecerán cuando el modelo se convierta
    // a JSON (ej: en APIs) o a array. Protege datos sensibles.
    // ─────────────────────────────────────────────────────────
    protected $hidden = [
        'password',       // Nunca exponer la contraseña encriptada
        'remember_token', // Token interno de Laravel para "recordarme"
        'two_factor_code', // El código 2FA no debe ser visible en respuestas
    ];

    // ─────────────────────────────────────────────────────────
    // casts() - CONVERSIÓN AUTOMÁTICA DE TIPOS
    // Laravel convierte automáticamente el valor del campo
    // al tipo especificado al leerlo de la base de datos.
    // ─────────────────────────────────────────────────────────
    protected function casts(): array
    {
        return [
            'email_verified_at'     => 'datetime', // Convierte el string de la BD a objeto Carbon (fechas)
            'password'              => 'hashed',   // Al guardar, encripta automáticamente con bcrypt
            'two_factor_enabled'    => 'boolean',  // Convierte 1/0 (entero en BD) a true/false (PHP)
            'two_factor_expires_at' => 'datetime', // Convierte a objeto Carbon para comparar con now()
            'notification_settings' => 'array',    // Convierte JSON de la BD a array PHP automáticamente
        ];
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: generateTwoFactorCode()
    // QUÉ HACE: Genera un código de 6 dígitos aleatorio para
    //           el 2FA y lo guarda con su fecha de expiración.
    // SE LLAMA DESDE: AuthController::login() y TwoFactorController::resend()
    // ─────────────────────────────────────────────────────────
    public function generateTwoFactorCode(): void // void = no retorna ningún valor
    {
        $this->update([ // $this = el objeto User actual (el usuario que tiene el 2FA)
            'two_factor_code' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            // random_int(0, 999999) → genera un número aleatorio criptográficamente seguro (ej: 7823)
            // str_pad(..., 6, '0', STR_PAD_LEFT) → rellena con ceros a la izquierda hasta 6 dígitos
            // Resultado: 7823 → "007823"  |  0 → "000000"  |  999999 → "999999"

            'two_factor_expires_at' => now()->addMinutes(10),
            // now() → fecha y hora actual (objeto Carbon)
            // ->addMinutes(10) → suma 10 minutos → el código expira en 10 minutos
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // MÉTODO: clearTwoFactorCode()
    // QUÉ HACE: Limpia el código 2FA y su expiración después
    //           de que el usuario lo verificó correctamente.
    //           Evita que el código pueda reutilizarse.
    // SE LLAMA DESDE: TwoFactorController::verify()
    // ─────────────────────────────────────────────────────────
    public function clearTwoFactorCode(): void
    {
        $this->update([
            'two_factor_code'       => null, // Borrar el código de la BD
            'two_factor_expires_at' => null, // Borrar la fecha de expiración también
        ]);
    }
}
