<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/misiones/{mision}/completar', [HomeController::class, 'completarMision']);

Route::get('/usuario', [UserController::class, 'index'])->name('usuario.index');
Route::post('/usuario', [UserController::class, 'updateProfile'])->name('usuario.update');
Route::get('/usuario/tarjeta/nueva', [UserController::class, 'createCard'])->name('usuario.tarjeta.create');
Route::post('/usuario/tarjeta', [UserController::class, 'storeCard'])->name('usuario.tarjeta.store');
Route::delete('/usuario/tarjeta', [UserController::class, 'destroyCard'])->name('usuario.tarjeta.destroy');
Route::get('/usuario/inventario', [UserController::class, 'inventario'])->name('usuario.inventario');

Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat/contactos', [ChatController::class, 'storeContact'])->name('chat.contactos.store');
Route::delete('/chat/contactos/{contacto}', [ChatController::class, 'deleteContact'])->name('chat.contactos.destroy');
Route::post('/chat/contactos/{contacto}/bloquear', [ChatController::class, 'blockContact'])->name('chat.contactos.block');
Route::post('/chat/contactos/{contacto}/desbloquear', [ChatController::class, 'unblockContact'])->name('chat.contactos.unblock');
Route::post('/chat/solicitudes/{solicitud}/aceptar', [ChatController::class, 'acceptRequest'])->name('chat.solicitudes.accept');
Route::post('/chat/solicitudes/{solicitud}/rechazar', [ChatController::class, 'rejectRequest'])->name('chat.solicitudes.reject');
Route::get('/chat/contactos/{contacto}/mensajes', [ChatController::class, 'messages'])->name('chat.messages.index');
Route::post('/chat/contactos/{contacto}/mensajes', [ChatController::class, 'sendMessage'])->name('chat.messages.store');
use App\Http\Controllers\EventoController;
use App\Http\Controllers\AuthController;

// ── Raíz: login (si ya está autenticado, redirige a /home) ────────
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('home')
        : redirect()->route('login');
});

// ── Autenticación (guests) ────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'login'])->name('login');
    Route::post('/login',   [AuthController::class, 'store'])->name('login.store');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register',[AuthController::class, 'storeRegister'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Rutas protegidas (requieren sesión iniciada) ───────────────────
Route::middleware('auth')->group(function () {
    Route::get('/home',    [HomeController::class, 'index'])->name('home');
    Route::get('/eventos', [EventoController::class, 'index'])->name('eventos');

    Route::post('/misiones/{mision}/completar', [HomeController::class, 'completarMision']);

    Route::get('/usuario',              [UserController::class, 'index'])->name('usuario.index');
    Route::get('/usuario/tarjeta/nueva',[UserController::class, 'createCard'])->name('usuario.tarjeta.create');
    Route::post('/usuario/tarjeta',     [UserController::class, 'storeCard'])->name('usuario.tarjeta.store');

    Route::get('/tienda-puntos', fn() => view('tienda_puntos'))->name('tienda.puntos');
});
