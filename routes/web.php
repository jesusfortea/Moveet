<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMisionController;
use App\Http\Controllers\AdminEventoController;
use App\Http\Controllers\AdminPaseDePaseoController;
use App\Http\Controllers\AdminLugarController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventoController;

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
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/misiones/{mision}/completar', [HomeController::class, 'completarMision']);

    Route::get('/eventos', [EventoController::class, 'index'])->name('eventos');

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

    Route::get('/tienda-puntos', fn() => view('tienda_puntos'))->name('tienda.puntos');
    Route::get('/pase-paseo', [\App\Http\Controllers\PaseDePaseoController::class, 'index'])->name('pase.paseo');
    Route::post('/pase-paseo/reclamar/{recompensa}', [\App\Http\Controllers\PaseDePaseoController::class, 'reclamar'])->name('pase.reclamar');
});

// ── Rutas de Admin (protegidas) ────────────────────────────────────
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
    
    // Rutas de pase de paseo
    Route::get('/pase-paseo', [AdminPaseDePaseoController::class, 'index'])->name('admin.pase_paseo');
    Route::get('/pase-paseo/crear', [AdminPaseDePaseoController::class, 'crear'])->name('admin.pase_paseo.crear');
    Route::post('/pase-paseo', [AdminPaseDePaseoController::class, 'guardar'])->name('admin.pase_paseo.guardar');
    Route::get('/pase-paseo/{pasedepaseo}/editar', [AdminPaseDePaseoController::class, 'editar'])->name('admin.pase_paseo.editar');
    Route::put('/pase-paseo/{pasedepaseo}', [AdminPaseDePaseoController::class, 'actualizar'])->name('admin.pase_paseo.actualizar');
    Route::get('/pase-paseo/{pasedepaseo}/eliminar', [AdminPaseDePaseoController::class, 'eliminar'])->name('admin.pase_paseo.eliminar');
    Route::delete('/pase-paseo/{pasedepaseo}', [AdminPaseDePaseoController::class, 'confirmarEliminar'])->name('admin.pase_paseo.confirmar-eliminar');
    
    // Rutas de lugares
    Route::get('/lugares', [AdminLugarController::class, 'index'])->name('admin.lugares');
    Route::get('/lugares/crear', [AdminLugarController::class, 'crear'])->name('admin.lugares.crear');
    Route::post('/lugares', [AdminLugarController::class, 'guardar'])->name('admin.lugares.guardar');
    Route::get('/lugares/{lugar}/editar', [AdminLugarController::class, 'editar'])->name('admin.lugares.editar');
    Route::put('/lugares/{lugar}', [AdminLugarController::class, 'actualizar'])->name('admin.lugares.actualizar');
    Route::get('/lugares/{lugar}/eliminar', [AdminLugarController::class, 'eliminar'])->name('admin.lugares.eliminar');
    Route::delete('/lugares/{lugar}', [AdminLugarController::class, 'confirmarEliminar'])->name('admin.lugares.confirmar-eliminar');
});
