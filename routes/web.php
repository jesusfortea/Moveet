<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
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
});
