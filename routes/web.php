<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventoController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/eventos', [EventoController::class, 'index'])->name('eventos');
Route::post('/misiones/{mision}/completar', [HomeController::class, 'completarMision']);
