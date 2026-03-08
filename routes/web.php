<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/',        [AuthController::class, 'showLogin'])->name('home');
Route::post('/login',  [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/signup',  [AuthController::class, 'showRegister'])->name('signup');
Route::post('/signup', [AuthController::class, 'register'])->name('register');

Route::get('/dashboard',     fn() => view('welcome'))->name('dashboard');
Route::get('/estadisticas',  fn() => view('estadisticas'))->name('estadisticas');
Route::get('/analisis',      fn() => view('analisis'))->name('analisis');
Route::get('/ventas',        fn() => view('ventas'))->name('ventas');
Route::get('/clientes',      fn() => view('clientes'))->name('clientes');
Route::get('/facturas',      fn() => view('facturas'))->name('facturas');
Route::get('/mensajes',      fn() => view('mensajes'))->name('mensajes');
Route::get('/configuracion', fn() => view('configuracion'))->name('configuracion');
Route::get('/nosotros',      fn() => view('nosotros'))->name('nosotros');