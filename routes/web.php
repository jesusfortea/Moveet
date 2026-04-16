<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMisionController;
use App\Http\Controllers\AdminEventoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/misiones/{mision}/completar', [HomeController::class, 'completarMision']);

// Rutas de usuario
Route::get('/usuario', [UserController::class, 'index'])->name('usuario.index');
Route::get('/usuario/tarjeta/nueva', [UserController::class, 'createCard'])->name('usuario.tarjeta.create');
Route::post('/usuario/tarjeta', [UserController::class, 'storeCard'])->name('usuario.tarjeta.store');

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'storeRegister'])->name('register.store');

// Rutas de Admin (protegidas)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Rutas de usuarios
    Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('admin.usuarios');
    Route::get('/usuarios/crear', [AdminController::class, 'crearUsuario'])->name('admin.usuarios.crear');
    Route::post('/usuarios', [AdminController::class, 'guardarUsuario'])->name('admin.usuarios.guardar');
    Route::get('/usuarios/{user}/editar', [AdminController::class, 'editarUsuario'])->name('admin.usuarios.editar');
    Route::put('/usuarios/{user}', [AdminController::class, 'actualizarUsuario'])->name('admin.usuarios.actualizar');
    Route::get('/usuarios/{user}/eliminar', [AdminController::class, 'eliminarUsuario'])->name('admin.usuarios.eliminar');
    Route::delete('/usuarios/{user}', [AdminController::class, 'confirmarEliminar'])->name('admin.usuarios.confirmar-eliminar');
    
    // Rutas de misiones
    Route::get('/misiones', [AdminMisionController::class, 'index'])->name('admin.misiones');
    Route::get('/misiones/crear', [AdminMisionController::class, 'crear'])->name('admin.misiones.crear');
    Route::post('/misiones', [AdminMisionController::class, 'guardar'])->name('admin.misiones.guardar');
    Route::get('/misiones/{mision}/editar', [AdminMisionController::class, 'editar'])->name('admin.misiones.editar');
    Route::put('/misiones/{mision}', [AdminMisionController::class, 'actualizar'])->name('admin.misiones.actualizar');
    Route::get('/misiones/{mision}/eliminar', [AdminMisionController::class, 'eliminar'])->name('admin.misiones.eliminar');
    Route::delete('/misiones/{mision}', [AdminMisionController::class, 'confirmarEliminar'])->name('admin.misiones.confirmar-eliminar');
    
    // Rutas de eventos
    Route::get('/eventos', [AdminEventoController::class, 'index'])->name('admin.eventos');
    Route::get('/eventos/crear', [AdminEventoController::class, 'crear'])->name('admin.eventos.crear');
    Route::post('/eventos', [AdminEventoController::class, 'guardar'])->name('admin.eventos.guardar');
    Route::get('/eventos/{evento}/editar', [AdminEventoController::class, 'editar'])->name('admin.eventos.editar');
    Route::put('/eventos/{evento}', [AdminEventoController::class, 'actualizar'])->name('admin.eventos.actualizar');
    Route::get('/eventos/{evento}/eliminar', [AdminEventoController::class, 'eliminar'])->name('admin.eventos.eliminar');
    Route::delete('/eventos/{evento}', [AdminEventoController::class, 'confirmarEliminar'])->name('admin.eventos.confirmar-eliminar');
});
