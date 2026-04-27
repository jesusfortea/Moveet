<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminEventoController;
use App\Http\Controllers\AdminRecompensaController;
use App\Http\Controllers\AdminMisionController;
use App\Http\Controllers\AdminTiendaController;
use App\Http\Controllers\AdminPaseDePaseoController;
use App\Http\Controllers\AdminLugarController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PaseDePaseoController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\SuscripcionController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Root: si hay sesión, home; si no, landing page.
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('home')
        : view('landing');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegister'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas públicas para preguntas
Route::get('/preguntas', [PreguntaController::class, 'index'])->name('preguntas.index');
Route::get('/preguntas/{pregunta}', [PreguntaController::class, 'show'])->name('preguntas.show');

Route::middleware('auth')->group(function () {
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
    Route::get('/chat/contactos', function () {
        return redirect()->route('chat.index');
    })->name('chat.contactos.fallback');
    Route::post('/chat/contactos', [ChatController::class, 'storeContact'])->name('chat.contactos.store');
    Route::post('/chat/contactos/qr/scan', [ChatController::class, 'scanQr'])->name('chat.qr.scan');
    Route::get('/chat/accept-invitation/{code}', [ChatController::class, 'acceptInvitation'])->name('chat.accept-invitation');
    Route::get('/chat/qr/{code}', [ChatController::class, 'acceptInvitation'])->name('chat.qr.accept');
    Route::delete('/chat/contactos/{contacto}', [ChatController::class, 'deleteContact'])->name('chat.contactos.destroy');
    Route::post('/chat/contactos/{contacto}/bloquear', [ChatController::class, 'blockContact'])->name('chat.contactos.block');
    Route::post('/chat/contactos/{contacto}/desbloquear', [ChatController::class, 'unblockContact'])->name('chat.contactos.unblock');
    Route::post('/chat/solicitudes/{solicitud}/aceptar', [ChatController::class, 'acceptRequest'])->name('chat.solicitudes.accept');
    Route::post('/chat/solicitudes/{solicitud}/rechazar', [ChatController::class, 'rejectRequest'])->name('chat.solicitudes.reject');
    Route::get('/chat/contactos/{contacto}/mensajes', [ChatController::class, 'messages'])->name('chat.messages.index');
    Route::post('/chat/contactos/{contacto}/mensajes', [ChatController::class, 'sendMessage'])->name('chat.messages.store');

    Route::get('/tienda', [TiendaController::class, 'index'])->name('tienda.index');
    Route::get('/tienda/articulos/{recompensa}', [TiendaController::class, 'articulo'])->name('tienda.articulo');
    Route::get('/tienda/confirmacion/{recompensa}', [TiendaController::class, 'confirmacion'])->name('tienda.confirmacion');
    Route::post('/tienda/comprar/{recompensa}', [TiendaController::class, 'comprar'])->name('tienda.comprar');
    Route::get('/tienda/compra/{recompensa}', [TiendaController::class, 'compra'])->name('tienda.compra');
    Route::get('/tienda/puntos', [TiendaController::class, 'puntos'])->name('tienda.puntos');
    Route::get('/tienda/puntos/confirmacion/{packPuntos}', [TiendaController::class, 'confirmacionPuntos'])->name('tienda.puntos.confirmacion');
    Route::post('/tienda/puntos/comprar/{packPuntos}', [TiendaController::class, 'comprarPuntos'])->name('tienda.puntos.comprar');
    Route::get('/tienda/puntos/compra/{packPuntos}', [TiendaController::class, 'compraPuntos'])->name('tienda.puntos.compra');

    Route::get('/pase-paseo', [PaseDePaseoController::class, 'index'])->name('pase.paseo');
    Route::post('/pase-paseo/reclamar/{recompensa}', [PaseDePaseoController::class, 'reclamar'])->name('pase.reclamar');

    Route::get('/suscripcion', [SuscripcionController::class, 'index'])->name('suscripcion');
    Route::post('/suscripcion/tarjeta', [SuscripcionController::class, 'storeCard'])->name('suscripcion.tarjeta.store');
    Route::post('/suscripcion/comprar', [SuscripcionController::class, 'subscribe'])->name('suscripcion.comprar');

    // Rutas autenticadas para preguntas
    Route::get('/preguntas/crear', [PreguntaController::class, 'create'])->name('preguntas.create');
    Route::post('/preguntas', [PreguntaController::class, 'store'])->name('preguntas.store');
    Route::get('/preguntas/{pregunta}/editar', [PreguntaController::class, 'edit'])->name('preguntas.edit');
    Route::put('/preguntas/{pregunta}', [PreguntaController::class, 'update'])->name('preguntas.update');
    Route::delete('/preguntas/{pregunta}', [PreguntaController::class, 'destroy'])->name('preguntas.destroy');
    Route::post('/preguntas/{pregunta}/responder', [PreguntaController::class, 'responder'])->name('preguntas.responder');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('admin.usuarios');
    Route::get('/usuarios/crear', [AdminController::class, 'crearUsuario'])->name('admin.usuarios.crear');
    Route::post('/usuarios', [AdminController::class, 'guardarUsuario'])->name('admin.usuarios.guardar');
    Route::get('/usuarios/{user}/editar', [AdminController::class, 'editarUsuario'])->name('admin.usuarios.editar');
    Route::put('/usuarios/{user}', [AdminController::class, 'actualizarUsuario'])->name('admin.usuarios.actualizar');
    Route::get('/usuarios/{user}/eliminar', [AdminController::class, 'eliminarUsuario'])->name('admin.usuarios.eliminar');
    Route::delete('/usuarios/{user}', [AdminController::class, 'confirmarEliminar'])->name('admin.usuarios.confirmar-eliminar');

    Route::get('/misiones', [AdminMisionController::class, 'index'])->name('admin.misiones');
    Route::get('/misiones/crear', [AdminMisionController::class, 'crear'])->name('admin.misiones.crear');
    Route::post('/misiones', [AdminMisionController::class, 'guardar'])->name('admin.misiones.guardar');
    Route::get('/misiones/{mision}/editar', [AdminMisionController::class, 'editar'])->name('admin.misiones.editar');
    Route::put('/misiones/{mision}', [AdminMisionController::class, 'actualizar'])->name('admin.misiones.actualizar');
    Route::get('/misiones/{mision}/eliminar', [AdminMisionController::class, 'eliminar'])->name('admin.misiones.eliminar');
    Route::delete('/misiones/{mision}', [AdminMisionController::class, 'confirmarEliminar'])->name('admin.misiones.confirmar-eliminar');

    Route::get('/eventos', [AdminEventoController::class, 'index'])->name('admin.eventos');
    Route::get('/eventos/crear', [AdminEventoController::class, 'crear'])->name('admin.eventos.crear');
    Route::post('/eventos', [AdminEventoController::class, 'guardar'])->name('admin.eventos.guardar');
    Route::get('/eventos/{evento}/editar', [AdminEventoController::class, 'editar'])->name('admin.eventos.editar');
    Route::put('/eventos/{evento}', [AdminEventoController::class, 'actualizar'])->name('admin.eventos.actualizar');
    Route::get('/eventos/{evento}/eliminar', [AdminEventoController::class, 'eliminar'])->name('admin.eventos.eliminar');
    Route::delete('/eventos/{evento}', [AdminEventoController::class, 'confirmarEliminar'])->name('admin.eventos.confirmar-eliminar');

    Route::get('/recompensas', [AdminRecompensaController::class, 'index'])->name('admin.recompensas');
    Route::get('/recompensas/crear', [AdminRecompensaController::class, 'crear'])->name('admin.recompensas.crear');
    Route::post('/recompensas', [AdminRecompensaController::class, 'guardar'])->name('admin.recompensas.guardar');
    Route::get('/recompensas/{recompensa}/editar', [AdminRecompensaController::class, 'editar'])->name('admin.recompensas.editar');
    Route::put('/recompensas/{recompensa}', [AdminRecompensaController::class, 'actualizar'])->name('admin.recompensas.actualizar');
    Route::get('/recompensas/{recompensa}/eliminar', [AdminRecompensaController::class, 'eliminar'])->name('admin.recompensas.eliminar');
    Route::delete('/recompensas/{recompensa}', [AdminRecompensaController::class, 'confirmarEliminar'])->name('admin.recompensas.confirmar-eliminar');

    Route::get('/tienda', [AdminTiendaController::class, 'index'])->name('admin.tienda');
    Route::patch('/tienda', [AdminTiendaController::class, 'actualizar'])->name('admin.tienda.actualizar');

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

    // Rutas de preguntas (admin)
    Route::get('/preguntas', [PreguntaController::class, 'adminPanel'])->name('admin.preguntas');
});
