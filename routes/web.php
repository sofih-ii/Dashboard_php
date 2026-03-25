<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\DashboardController;

// RUTAS PÚBLICAS
Route::get('/',        [AuthController::class, 'showLogin'])->name('home');
Route::post('/login',  [AuthController::class, 'login'])->name('login');

Route::get('/signup',  [AuthController::class, 'showRegister'])->name('signup');
Route::post('/signup', [AuthController::class, 'register'])->name('register');

// 2FA
Route::get('/2fa/verify',  [TwoFactorController::class, 'showVerify'])->name('2fa.verify');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify.post');
Route::post('/2fa/resend', [TwoFactorController::class, 'resend'])->name('2fa.resend');

// RUTAS PROTEGIDAS
Route::middleware('auth')->group(function () {

    Route::post('/logout',     [AuthController::class, 'logout'])->name('logout');
    Route::put('/perfil',      [AuthController::class, 'updatePerfil'])->name('perfil.update');
    Route::put('/password',    [AuthController::class, 'updatePassword'])->name('password.update');
    Route::post('/2fa/toggle', [TwoFactorController::class, 'toggle'])->name('2fa.toggle');

    // Dashboard con datos reales
    Route::get('/dashboard',     [DashboardController::class, 'dashboard'])->name('dashboard');

    // Vistas estáticas
    Route::get('/estadisticas',  fn() => view('estadisticas'))->name('estadisticas');
    Route::get('/analisis',      fn() => view('analisis'))->name('analisis');
    Route::get('/configuracion', fn() => view('configuracion'))->name('configuracion');
    Route::get('/nosotros',      fn() => view('nosotros'))->name('nosotros');

    // Vistas con datos reales desde BD
    Route::get('/ventas',        [DashboardController::class, 'ventas'])->name('ventas');
    Route::get('/clientes',      [DashboardController::class, 'clientes'])->name('clientes');
    Route::get('/facturas',      [DashboardController::class, 'facturas'])->name('facturas');
    Route::get('/mensajes',      [DashboardController::class, 'mensajes'])->name('mensajes');
    Route::get('/mensajes/{id}', [DashboardController::class, 'mensajes'])->name('mensajes.ver');
});