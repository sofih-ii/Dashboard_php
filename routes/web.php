<?php

// ============================================================
// ARCHIVO: routes/web.php
// QUÉ ES: El mapa completo de la aplicación. Aquí se definen
// TODAS las URLs que existen y qué controlador las maneja.
// Laravel lee este archivo en cada petición para saber a dónde
// enviar al usuario.
// ============================================================

use Illuminate\Support\Facades\Route;          // Clase que permite registrar rutas (get, post, put, delete)
use App\Http\Controllers\AuthController;       // Controlador de login, registro y perfil
use App\Http\Controllers\TwoFactorController;  // Controlador de verificación en 2 pasos (2FA)
use App\Http\Controllers\DashboardController;  // Controlador del panel principal y todas las secciones
use App\Http\Controllers\NosotrosController;   // Controlador de la sección "Nosotros" y PQRS
use App\Http\Controllers\ConfiguracionController; // Controlador de ajustes de cuenta
use App\Http\Controllers\ClienteController;    // Controlador CRUD de clientes
use App\Http\Controllers\VentaController;      // Controlador CRUD de ventas
use App\Http\Controllers\MensajeController;    // Controlador del sistema de mensajes
use App\Http\Controllers\FacturaController;    // Controlador CRUD de facturas

// ── RUTAS PÚBLICAS ────────────────────────────────────────────
// Cualquier visitante puede acceder sin haber iniciado sesión

Route::get('/',        [AuthController::class, 'showLogin'])->name('home');
// GET /  →  Muestra la pantalla de login (página de inicio de la app)
// ->name('home') le asigna un nombre a la ruta para referenciarla con route('home')

Route::post('/login',  [AuthController::class, 'login'])->name('login');
// POST /login  →  Procesa el formulario de login (verifica email y contraseña)

Route::get('/signup',  [AuthController::class, 'showRegister'])->name('signup');
// GET /signup  →  Muestra el formulario de registro de cuenta nueva

Route::post('/signup', [AuthController::class, 'register'])->name('register');
// POST /signup  →  Procesa el formulario de registro (crea el usuario en la BD)

// ── RUTAS DE VERIFICACIÓN EN DOS PASOS (2FA) ──────────────────
// Se usan entre el login y el dashboard cuando 2FA está activo

Route::get('/2fa/verify',  [TwoFactorController::class, 'showVerify'])->name('2fa.verify');
// GET /2fa/verify  →  Muestra la pantalla donde el usuario ingresa el código de 6 dígitos

Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify.post');
// POST /2fa/verify  →  Verifica que el código sea correcto y no haya expirado (10 min)

Route::post('/2fa/resend', [TwoFactorController::class, 'resend'])->name('2fa.resend');
// POST /2fa/resend  →  Genera un código nuevo y lo reenvía al correo del usuario

// ── RUTAS PROTEGIDAS ──────────────────────────────────────────
// Route::middleware('auth') agrupa rutas que SOLO son accesibles con sesión activa.
// Si el usuario no está autenticado, Laravel lo redirige automáticamente al login.
Route::middleware('auth')->group(function () {

    Route::post('/logout',     [AuthController::class, 'logout'])->name('logout');
    // POST /logout  →  Cierra la sesión activa y redirige al login

    Route::put('/perfil',      [AuthController::class, 'updatePerfil'])->name('perfil.update');
    // PUT /perfil  →  Guarda los cambios del perfil (nombre, email, teléfono, avatar, etc.)

    Route::put('/password',    [AuthController::class, 'updatePassword'])->name('password.update');
    // PUT /password  →  Cambia la contraseña verificando primero la contraseña actual

    Route::post('/2fa/toggle', [TwoFactorController::class, 'toggle'])->name('2fa.toggle');
    // POST /2fa/toggle  →  Activa o desactiva el 2FA del usuario (alterna true↔false)

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    // GET /dashboard  →  Panel principal con tarjetas de resumen y actividad reciente

    // Estadísticas y Análisis (con datos reales desde controlador)
    Route::get('/estadisticas', [DashboardController::class, 'estadisticas'])->name('estadisticas');
    // GET /estadisticas  →  Gráficas de ventas mensuales, productos top y segmentación de clientes

    Route::get('/analisis',     [DashboardController::class, 'analisis'])->name('analisis');
    // GET /analisis  →  Análisis de actividad semanal y resumen de módulos del sistema

    // Configuración
    Route::get('/configuracion',                    fn() => view('configuracion'))->name('configuracion');
    // GET /configuracion  →  Muestra la página de ajustes
    // fn() => view('configuracion') es una función corta: no necesita controlador porque no pasa datos

    Route::post('/configuracion/notificaciones',    [ConfiguracionController::class, 'updateNotifications'])->name('config.notificaciones');
    // POST /configuracion/notificaciones  →  Guarda preferencias de notificaciones del usuario

    Route::post('/configuracion/sistema',           [ConfiguracionController::class, 'updateSystem'])->name('config.sistema');
    // POST /configuracion/sistema  →  Guarda ajustes del sistema (tema, idioma, registros por página)

    Route::post('/configuracion/cache',             [ConfiguracionController::class, 'clearCache'])->name('config.cache');
    // POST /configuracion/cache  →  Limpia el caché de la aplicación (útil tras hacer cambios)

    Route::delete('/configuracion/cuenta',          [ConfiguracionController::class, 'deleteAccount'])->name('config.delete');
    // DELETE /configuracion/cuenta  →  Elimina permanentemente la cuenta del usuario

    // Nosotros / PQRS
    Route::get('/nosotros', function () {
        $pqrs = \App\Models\Pqrs::latest()->get();  // Trae todas las PQRS de la BD, de la más nueva a la más antigua
        return view('nosotros', compact('pqrs'));   // Envía la variable $pqrs a la vista nosotros.blade.php
    })->name('nosotros');
    // GET /nosotros  →  Muestra la sección "Nosotros" y el listado de PQRS

    Route::post('/pqrs', [NosotrosController::class, 'store'])->name('pqrs.store');
    // POST /pqrs  →  Guarda una nueva PQRS (Petición, Queja, Reclamo o Sugerencia)

    // Ventas
    // IMPORTANTE: /ventas/exportar debe ir ANTES de /ventas/{id} para que Laravel
    // no confunda la palabra "exportar" con un parámetro dinámico {id}
    Route::get('/ventas/exportar', [VentaController::class, 'export'])->name('ventas.export');
    // GET /ventas/exportar  →  Descarga todas las ventas como archivo CSV

    Route::get('/ventas',          [DashboardController::class, 'ventas'])->name('ventas');
    // GET /ventas  →  Lista de ventas con estadísticas, gráficas y DataTables

    Route::post('/ventas',         [VentaController::class, 'store'])->name('ventas.store');
    // POST /ventas  →  Registra una nueva venta en la base de datos

    Route::put('/ventas/{id}',     [VentaController::class, 'updateEstado'])->name('ventas.estado');
    // PUT /ventas/{id}  →  Actualiza el estado de una venta (completado, pendiente, devuelto…)
    // {id} es dinámico: si la URL es /ventas/5 entonces $id = 5 en el controlador

    Route::delete('/ventas/{id}',  [VentaController::class, 'destroy'])->name('ventas.destroy');
    // DELETE /ventas/{id}  →  Elimina permanentemente una venta de la base de datos

    // Clientes
    Route::get('/clientes/exportar',  [ClienteController::class, 'export'])->name('clientes.export');
    // GET /clientes/exportar  →  Descarga todos los clientes como CSV (con BOM para Excel)

    Route::get('/clientes',           [DashboardController::class, 'clientes'])->name('clientes');
    // GET /clientes  →  Lista de clientes con estadísticas y DataTables interactivo

    Route::post('/clientes',          [ClienteController::class, 'store'])->name('clientes.store');
    // POST /clientes  →  Crea un nuevo cliente en la base de datos

    Route::get('/clientes/{id}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    // GET /clientes/{id}/edit  →  Redirige a la lista de clientes con un dato flash para abrir el modal de edición

    Route::put('/clientes/{id}',      [ClienteController::class, 'update'])->name('clientes.update');
    // PUT /clientes/{id}  →  Guarda los cambios editados de un cliente existente

    Route::delete('/clientes/{id}',   [ClienteController::class, 'destroy'])->name('clientes.destroy');
    // DELETE /clientes/{id}  →  Elimina permanentemente un cliente de la base de datos

    // Facturas
    Route::get('/facturas/exportar', [FacturaController::class, 'export'])->name('facturas.export');
    // GET /facturas/exportar  →  Descarga todas las facturas como CSV

    Route::get('/facturas',          [DashboardController::class, 'facturas'])->name('facturas');
    // GET /facturas  →  Lista de facturas con totales y DataTables

    Route::post('/facturas',         [FacturaController::class, 'store'])->name('facturas.store');
    // POST /facturas  →  Crea una nueva factura en la base de datos

    Route::put('/facturas/{id}',     [FacturaController::class, 'updateEstado'])->name('facturas.estado');
    // PUT /facturas/{id}  →  Actualiza el estado de una factura (pagada, pendiente, vencida)

    // Mensajes
    Route::post('/mensajes/enviar',  [MensajeController::class, 'send'])->name('mensajes.send');
    // POST /mensajes/enviar  →  Envía un mensaje dentro de una conversación ya abierta

    Route::post('/mensajes/nuevo',   [MensajeController::class, 'nuevoMensaje'])->name('mensajes.nuevo');
    // POST /mensajes/nuevo  →  Inicia una conversación nueva con un cliente elegido

    Route::get('/mensajes',          [DashboardController::class, 'mensajes'])->name('mensajes');
    // GET /mensajes  →  Bandeja de mensajes sin conversación activa seleccionada

    Route::get('/mensajes/{id}',     [DashboardController::class, 'mensajes'])->name('mensajes.ver');
    // GET /mensajes/{id}  →  Bandeja de mensajes con la conversación del cliente {id} abierta

    Route::delete('/mensajes/{clienteId}/conversacion', [MensajeController::class, 'destroy'])->name('mensajes.conversacion.destroy');
    // DELETE /mensajes/{clienteId}/conversacion  →  Borra todos los mensajes de esa conversación
});
