<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventoController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/eventos', [EventoController::class, 'index'])->name('eventos');
Route::post('/misiones/{mision}/completar', [HomeController::class, 'completarMision']);

Route::get('/usuario', [UserController::class, 'index'])->name('usuario.index');
Route::get('/usuario/tarjeta/nueva', [UserController::class, 'createCard'])->name('usuario.tarjeta.create');
Route::post('/usuario/tarjeta', [UserController::class, 'storeCard'])->name('usuario.tarjeta.store');
use App\Http\Controllers\AuthController;


// Rutas de autenticación
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'storeRegister'])->name('register.store');
