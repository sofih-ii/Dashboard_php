<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NosotrosController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\FacturaController;

Route::get('/',        [AuthController::class, 'showLogin'])->name('home');
Route::post('/login',  [AuthController::class, 'login'])->name('login');
Route::get('/signup',  [AuthController::class, 'showRegister'])->name('signup');
Route::post('/signup', [AuthController::class, 'register'])->name('register');

Route::get('/2fa/verify',  [TwoFactorController::class, 'showVerify'])->name('2fa.verify');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify.post');
Route::post('/2fa/resend', [TwoFactorController::class, 'resend'])->name('2fa.resend');

Route::middleware('auth')->group(function () {

    Route::post('/logout',     [AuthController::class, 'logout'])->name('logout');
    Route::put('/perfil',      [AuthController::class, 'updatePerfil'])->name('perfil.update');
    Route::put('/password',    [AuthController::class, 'updatePassword'])->name('password.update');
    Route::post('/2fa/toggle', [TwoFactorController::class, 'toggle'])->name('2fa.toggle');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // Estadísticas y Análisis (con datos reales desde controlador)
    Route::get('/estadisticas', [DashboardController::class, 'estadisticas'])->name('estadisticas');
    Route::get('/analisis',     [DashboardController::class, 'analisis'])->name('analisis');

    // Configuración
    Route::get('/configuracion',                    fn() => view('configuracion'))->name('configuracion');
    Route::post('/configuracion/notificaciones',    [ConfiguracionController::class, 'updateNotifications'])->name('config.notificaciones');
    Route::post('/configuracion/sistema',           [ConfiguracionController::class, 'updateSystem'])->name('config.sistema');
    Route::post('/configuracion/cache',             [ConfiguracionController::class, 'clearCache'])->name('config.cache');
    Route::delete('/configuracion/cuenta',          [ConfiguracionController::class, 'deleteAccount'])->name('config.delete');

    // Nosotros / PQRS
    Route::get('/nosotros', function () {
        $pqrs = \App\Models\Pqrs::latest()->get();
        return view('nosotros', compact('pqrs'));
    })->name('nosotros');
    Route::post('/pqrs', [NosotrosController::class, 'store'])->name('pqrs.store');

    // Ventas (exportar debe ir ANTES de la ruta raíz para evitar conflictos)
    Route::get('/ventas/exportar', [VentaController::class, 'export'])->name('ventas.export');
    Route::get('/ventas',          [DashboardController::class, 'ventas'])->name('ventas');
    Route::post('/ventas',         [VentaController::class, 'store'])->name('ventas.store');
    Route::put('/ventas/{id}',     [VentaController::class, 'updateEstado'])->name('ventas.estado');
    Route::delete('/ventas/{id}',  [VentaController::class, 'destroy'])->name('ventas.destroy');

    // Clientes
    Route::get('/clientes/exportar',  [ClienteController::class, 'export'])->name('clientes.export');
    Route::get('/clientes',           [DashboardController::class, 'clientes'])->name('clientes');
    Route::post('/clientes',          [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{id}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{id}',      [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{id}',   [ClienteController::class, 'destroy'])->name('clientes.destroy');

    // Facturas
    Route::get('/facturas/exportar', [FacturaController::class, 'export'])->name('facturas.export');
    Route::get('/facturas',          [DashboardController::class, 'facturas'])->name('facturas');
    Route::post('/facturas',         [FacturaController::class, 'store'])->name('facturas.store');
    Route::put('/facturas/{id}',     [FacturaController::class, 'updateEstado'])->name('facturas.estado');

    // Mensajes
    Route::post('/mensajes/enviar',  [MensajeController::class, 'send'])->name('mensajes.send');
    Route::post('/mensajes/nuevo',   [MensajeController::class, 'nuevoMensaje'])->name('mensajes.nuevo');
    Route::get('/mensajes',          [DashboardController::class, 'mensajes'])->name('mensajes');
    Route::get('/mensajes/{id}',     [DashboardController::class, 'mensajes'])->name('mensajes.ver');
    Route::delete('/mensajes/{clienteId}/conversacion', [MensajeController::class, 'destroy'])->name('mensajes.conversacion.destroy');
});
